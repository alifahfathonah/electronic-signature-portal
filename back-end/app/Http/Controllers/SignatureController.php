<?php

namespace App\Http\Controllers;

use App\Models\SignatureContainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    public function getIdcardToken()
    {
        // TODO error handling.
        $response = Http::post(env('EID_EASY_URL') . "/api/signatures/integration/id-card/get-token", [
            'client_id' => env('EID_CLIENT_ID'),
            'secret'    => env('EID_SECRET'),
            'method'    => 'id-signature',
        ]);

        return response()->json(['token' => $response->json()['token']]);
    }

    public function getSignatureDigest(Request $request)
    {
        // TODO check permissions.
        $signatureContainer = SignatureContainer::find($request->fileid);

        // TODO get actual container files to get digest.

//        $tempFile = tempnam(sys_get_temp_dir(), 'signature');
//        file_put_contents($tempFile, $content);
//        $mimeType = mime_content_type($tempFile);

        $files   = [];
        $files[] = [
            'fileName'    => 'test.pdf',
            'fileContent' => base64_encode(hash('sha256', Storage::get('/company1/1/test.pdf'), true)),
            'mimeType'    => 'application/pdf',
        ];
        $files[] = [
            'fileName'    => 'test.txt',
            'fileContent' => base64_encode(hash('sha256', Storage::get('/company1/1/test.txt'), true)),
            'mimeType'    => 'text/plain'
        ];

        $prepareResponse = Http::post(env('EID_EASY_URL') . "/api/signatures/prepare-files-for-signing", [
            'client_id'      => env('EID_CLIENT_ID'),
            'secret'         => env('EID_SECRET'),
            'container_type' => 'xades',
            'baseline'       => 'B', //TODO remove to default to LT
            'files'          => $files
        ]);

        $docId = $prepareResponse->json()['doc_id'];

        $startSigningResponse = Http::withHeaders(['accept' => 'application/json'])->post(env('EID_EASY_URL') . "/api/signatures/start-signing", [
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

    public function finishSignature(Request $request)
    {
        // TODO check permissions.
        $signatureContainer = SignatureContainer::find($request->fileid);

        // TODO get actual container files to get digest.

//        $tempFile = tempnam(sys_get_temp_dir(), 'signature');
//        file_put_contents($tempFile, $content);
//        $mimeType = mime_content_type($tempFile);

        $files   = [];
        $files[] = [
            'fileName'    => 'test.pdf',
            'fileContent' => hash('sha256', Storage::get('/company1/1/test.pdf')),
            'mimeType'    => 'application/pdf'
        ];
        $files[] = [
            'fileName'    => 'test.txt',
            'fileContent' => hash('sha256', Storage::get('/company1/1/test.txt')),
            'mimeType'    => 'text/plain'
        ];

        $completeResponse = Http::post(env('EID_EASY_URL') . "/api/signatures/id-card/complete", [
            'client_id'       => env('EID_CLIENT_ID'),
            'secret'          => env('EID_SECRET'),
            'doc_id'          => $request->doc_id,
            'signature_value' => $request->signature_value,
        ]);

        if ($completeResponse->failed()) {
            Log::error("Signature complete failed", $completeResponse->json());
            return $completeResponse->body();
        }

        $downloadResponse = Http::post(env('EID_EASY_URL') . "/api/signatures/download-signed-file", [
            'client_id' => env('EID_CLIENT_ID'),
            'secret'    => env('EID_SECRET'),
            'doc_id'    => $request->doc_id,
        ]);

        if ($downloadResponse->failed()) {
            return $downloadResponse->body();
        }

        // TODO take the signature XML and compile into the .asice container

        return $downloadResponse->json()['signed_file_contents'];
    }
}
