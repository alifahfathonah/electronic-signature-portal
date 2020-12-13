<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\ContainerSigner;
use App\Models\SignatureContainer;
use App\Models\UnsignedFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

class SignatureController extends Controller
{
    public function applyVisualSignature(Request $request, SignatureContainer $container, ContainerSigner $signer)
    {
        $request->validate([
            'visual_signature' => 'required',
            'identifier'       => 'required',
            'identifier_type'  => 'required:in:email',
        ]);

        if ($signer->signed_at) {
            info("$signer->identifier already signed container $container->public_id");
            abort(403, 'Already signed');
        }

        // Get container and file
        /** @var UnsignedFile $unsignedFile */
        $unsignedFile = $container->files->first();
        $lastModified = Storage::lastModified($unsignedFile->storagePath());

        $pdf       = new Fpdi();
        $pageCount = $pdf->setSourceFile(StreamReader::createByString(Storage::get($unsignedFile->storagePath())));

        // Get signer
        $identifier = $request->input('identifier');
        $userType   = $request->input('identifier_type');

        $signerQuery = ContainerSigner::where('signature_container_id', $container->id)->where('identifier_type', $userType)->where('identifier', $identifier);

        $country = $request->input('country');
        if ($country) {
            $signerQuery = $signerQuery->where('country', $country);
        }

        $user = $signerQuery->first();
        info("User on $user");

        foreach ($container->signers as $containerUser) {
            info('Container user', $containerUser->toArray());
            if ($containerUser->id === $user->id) {
                $visualCoordinates = $containerUser->visual_coordinates;
                break;
            }
        }

        // Read whole PDF apply signature to correct page
        for ($i = 0; $i <= $pageCount; $i++) {
            $pdf->AddPage();
            $imported = $pdf->importPage(1);
            $pdf->useImportedPage($imported, 0, 0);

            $tempPath = tempnam(sys_get_temp_dir(), 'eid_');
            file_put_contents($tempPath, base64_decode($request->input('visual_signature')));

            if ($i === $visualCoordinates['page']) {
                $pdf->Image($tempPath,
                    $visualCoordinates['x'],
                    $visualCoordinates['y'],
                    $visualCoordinates['width'],
                    $visualCoordinates['height'],
                    'PNG'
                );
            }
        }

        // Overwrite existing PDF if not changed
        $pdfContents = $pdf->Output('S');

        // Avoid race conditions
        if (Storage::lastModified($unsignedFile->storagePath()) != $lastModified) {
            return $this->applyVisualSignature($request, $container, $signer);
        }

        $signer->signed_at = now();
        $signer->save();

        $trail                      = new AuditTrail();
        $trail->container_signer_id = $signer->id;
        $trail->ip                  = $request->ip();
        $trail->action_type         = AuditTrail::ACTION_SIGNED;
        $trail->save();

        Storage::put($unsignedFile->storagePath(), $pdfContents);

        $allSigned = true;
        foreach ($container->signers as $containerSigner) {
            if (!$containerSigner->signed_at) {
                $allSigned = false;
                break;
            }
        }

        if ($allSigned) {
            $container->addConfirmationPage();
        }

        return Storage::download($unsignedFile->storagePath());
    }

    public function getIdcardToken()
    {
        // TODO error handling.
        $response = Http::post(env('EID_API_URL') . "/api/signatures/integration/id-card/get-token", [
            'client_id' => env('EID_CLIENT_ID'),
            'secret'    => env('EID_SECRET'),
            'method'    => 'id-signature',
        ]);

        return response()->json(['token' => $response->json()['token']]);
    }

    public function getSignatureDigest(Request $request, SignatureContainer $container)
    {
        // TODO check permissions!!

        $files = [];
        foreach ($container->files as $file) {
            $files[] = [
                'fileName'    => $file->name,
                'fileContent' => base64_encode(hash('sha256', Storage::get($file->storagePath()), true)),
                'mimeType'    => $file->mime_type,
            ];
        }

        $prepareResponse = Http::post(env('EID_API_URL') . "/api/signatures/prepare-files-for-signing", [
            'client_id'      => env('EID_CLIENT_ID'),
            'secret'         => env('EID_SECRET'),
            'container_type' => 'xades',
            'baseline'       => 'B', //TODO remove to default to LT
            'files'          => $files
        ]);

        $docId = $prepareResponse->json()['doc_id'];

        $startSigningResponse = Http::withHeaders(['accept' => 'application/json'])->post(env('EID_API_URL') . "/api/signatures/start-signing", [
            'client_id'   => env('EID_CLIENT_ID'),
            'doc_id'      => $docId,
            'sign_type'   => 'id-card',
            'certificate' => $request->input('certificate'),
        ]);

        if ($startSigningResponse->failed()) {
            Log::error("Start signing failed", $startSigningResponse->json());
            return $startSigningResponse->body();
        }

        return response()->json([
            'hexDigest' => $startSigningResponse->json()['hexDigest'],
            'doc_id'    => $docId,
        ]);
    }

    public function finishSignature(Request $request, SignatureContainer $container)
    {
        // TODO check permissions.

        $rootSignature               = new \DOMDocument('1.0', 'utf-8');
        $rootSignature->formatOutput = true;
        $rootSignature->loadXML('<asic:XAdESSignatures xmlns:asic="http://uri.etsi.org/02918/v1.2.1#" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:xades="http://uri.etsi.org/01903/v1.3.2#"></asic:XAdESSignatures>');

        $completeResponse = Http::post(env('EID_API_URL') . "/api/signatures/id-card/complete", [
            'client_id'       => env('EID_CLIENT_ID'),
            'secret'          => env('EID_SECRET'),
            'doc_id'          => $request->doc_id,
            'signature_value' => $request->signature_value,
        ]);

        if ($completeResponse->failed()) {
            Log::error("Signature complete failed", $completeResponse->json());
            return $completeResponse->body();
        }

        $downloadResponse = Http::post(env('EID_API_URL') . "/api/signatures/download-signed-file", [
            'client_id' => env('EID_CLIENT_ID'),
            'secret'    => env('EID_SECRET'),
            'doc_id'    => $request->doc_id,
        ]);

        // TODO error handling
        $xadesSignature = base64_decode($downloadResponse->json()['signed_file_contents']);

        $signature = new \DOMDocument();
        $signature->loadXML($xadesSignature);
        $node = $signature->firstChild;

        $node = $rootSignature->importNode($node, true);

        $rootSignature->documentElement->appendChild($node);

        $containerFile = Storage::get($container->container_path);

        $tempZipFile = tempnam(sys_get_temp_dir(), 'signature');
        file_put_contents($tempZipFile, $containerFile);

        $zip = new \ZipArchive();

        $zip->open($tempZipFile);

        $numSignatures = 0;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat         = $zip->statIndex($i);
            $zipEntryName = basename($stat['name']);;
            if (Str::contains($zipEntryName, 'META-INF/Signature')) {
                $numSignatures++;
            }
        }

        $zip->addFromString("META-INF/signatures$numSignatures.xml", $rootSignature->saveXML());

        $zip->close();

        // TODO temporary test naming with random
        Storage::put($container->container_path . Str::random(4) . ".asice", file_get_contents($tempZipFile));

//        return $downloadResponse->json()['signed_file_contents'];
        return "Signature done";
    }
}
