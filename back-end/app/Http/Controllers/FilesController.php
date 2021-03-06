<?php

namespace App\Http\Controllers;

use App\Http\Requests\Container\CreateContainerRequest;
use App\Http\Requests\Container\DownloadFileRequest;
use App\Http\Resources\ContainerResource;
use App\Mail\SignatureRequested;
use App\Models\AuditTrail;
use App\Models\Company;
use App\Models\ContainerSigner;
use App\Models\SignatureContainer;
use App\Models\UnsignedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilesController extends Controller
{
    public function getContainerAndSigner(Request $request, SignatureContainer $container, ContainerSigner $signer)
    {
        if ($signer->signature_container_id !== $container->id) {
            abort(404, 'Invalid signer for this container');
        }

        // TODO check for file permissions
        $returnFiles = [];
        foreach ($container->files as $file) {
            $returnFiles[] = [
                'id'      => $file->id,
                'name'    => $file->name,
                'mime'    => $file->mime_type,
                'content' => base64_encode(Storage::get($file->storagePath())),
            ];
        }

        $trail                      = new AuditTrail();
        $trail->container_signer_id = $signer->id;
        $trail->ip                  = $request->ip();
        $trail->action_type         = AuditTrail::ACTION_OPENED;
        $trail->save();

        return response()->json([
            'files'  => $returnFiles,
            'signer' => $signer->only(['identifier', 'identifier_type', 'visual_coordinates', 'country']),
        ]);
    }

    public function getContainerFiles(Request $request, SignatureContainer $container)
    {
        // TODO check for file permissions
        $returnFiles = [];
        foreach ($container->files as $file) {
            $returnFiles[] = [
                'id'      => $file->id,
                'name'    => $file->name,
                'mime'    => $file->mime_type,
                'content' => base64_encode(Storage::get($file->storagePath())),
            ];
        }

        return response()->json(['files' => $returnFiles]);
    }

    public function createSignatureContainer(CreateContainerRequest $request, Company $company)
    {
        DB::beginTransaction();

        $container                 = new SignatureContainer();
        $container->container_type = $request->input('signature_type') === 'crypto' ? "asice" : "pdf";
        $container->public_id      = Str::random(20);
        $container->company_id     = $company->id;
        $container->save();

        if ($request->has('signers')) {
            foreach ($request->input('signers') as $signer) {
                $containerSigner                         = new ContainerSigner();
                $containerSigner->public_id              = Str::random(20);
                $containerSigner->identifier             = $signer['identifier'];
                $containerSigner->identifier_type        = $signer['identifier_type'];
                $containerSigner->country                = $signer['country'] ?? null;
                $containerSigner->visual_coordinates     = $signer['visual_coordinates'] ?? null;
                $containerSigner->signature_container_id = $container->id;
                $containerSigner->save();
                if ($containerSigner->identifier_type === "email") {
                    info("Sending notification e-mail to " . $containerSigner->identifier);
                    $url = env('APP_URL') . "/signatures/$container->public_id/signer/$containerSigner->public_id";
                    Mail::to($containerSigner->identifier)->send(new SignatureRequested($url));

                    $trail                      = new AuditTrail();
                    $trail->container_signer_id = $containerSigner->id;
                    $trail->ip                  = $request->ip();
                    $trail->action_type         = AuditTrail::ACTION_SENT;
                    $trail->save();
                }
            }
        }

        if ($request->input('signature_type') === 'crypto') {
            $containerPath = $this->createAsiceContainer($request, $container);
        } else {
            $containerPath = $this->savePdf($request, $container);
        }

        $container->container_path = $containerPath;
        $container->save();

        DB::commit();

        return response()->json([
            'container' => new ContainerResource($container),
        ]);
    }

    public function downloadFile(DownloadFileRequest $request)
    {
        $container = SignatureContainer::where('public_id', $request->route('container_id'))->firstOrFail();
        $path      = $this->getFileStoragePath($container->id, $request->input('file_name'));
        return response()->download(storage_path('app/' . $path));
    }

    public function getContainerInfo(Request $request)
    {
        $container = SignatureContainer::where('public_id', $request->route('container_id'))->firstOrFail();
        return response([
            'container' => new ContainerResource($container),
        ]);
    }

    private function savePdf(Request $request, SignatureContainer $container)
    {
        foreach ($request->input('files') as $fileData) {
            // Store file.
            $unsignedFile = new UnsignedFile();
            $name         = $fileData['name'];
            $fileContent  = base64_decode($fileData['content']);
            $storagePath  = $this->getFileStoragePath($container->id, $name);
            Storage::put($storagePath, $fileContent);

            $unsignedFile->name                   = $name;
            $unsignedFile->mime_type              = $fileData['mime'];
            $unsignedFile->storage_path           = $storagePath;
            $unsignedFile->size                   = strlen($fileContent);
            $unsignedFile->signature_container_id = $container->id;
            $unsignedFile->save();

            return $storagePath; // There can be only one PDF
        }
        Log::error("Saving PDF and file missing");
        return null;
    }

    private function createAsiceContainer(Request $request, SignatureContainer $container): string
    {
        $zip     = new \ZipArchive();
        $zipPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . Str::random();
        $zip->open($zipPath, \ZipArchive::CREATE);
        $zip->addFromString("mimetype", "application/vnd.etsi.asic-e+zip");
        $zip->addEmptyDir('META-INF');

        $manifestTemplate = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0">
  <manifest:file-entry manifest:full-path="/" manifest:media-type="application/vnd.etsi.asic-e+zip"/>
</manifest:manifest>
XML;

        $manifest  = simplexml_load_string($manifestTemplate);
        $namespace = 'urn:oasis:names:tc:opendocument:xmlns:manifest:1.0';

        foreach ($request->input('files') as $fileData) {
            // Store file.
            $unsignedFile = new UnsignedFile();
            $name         = $fileData['name'];
            $fileContent  = base64_decode($fileData['content']);

            // Add file to the container.
            $unsignedFile->signature_container_id = $container->id;
            $unsignedFile->name                   = $name;

            $storagePath = $unsignedFile->storagePath();
            Storage::put($storagePath, $fileContent);

            $unsignedFile->storage_path = $storagePath;
            $unsignedFile->size         = Storage::size($storagePath);
            $unsignedFile->mime_type    = $fileData['mime'];

            $unsignedFile->save();
            $zip->addFromString($name, $fileContent);

            // Add file metadata to container manifest.xml.
            $newFileEntry = $manifest->addChild('file-entry');
            $newFileEntry->addAttribute('manifest:full-path', $name, $namespace);
            $xmlMime = $fileData['mime'];
            $newFileEntry->addAttribute('manifest:media-type', $xmlMime, $namespace);
        }

        $zip->addFromString('META-INF/manifest.xml', $manifest->asXML());
        $zip->close();

        $containerPath = "containers/$container->id/container-" . now()->timestamp . ".asice";
        Storage::put($containerPath, file_get_contents($zipPath));

        return $containerPath;
    }

    private function getFileStoragePath(int $containerId, string $fileName): string
    {
        return "/containers/$containerId/$fileName";
    }
}
