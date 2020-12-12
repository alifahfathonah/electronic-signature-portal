<?php

namespace App\Http\Controllers;

use App\Http\Requests\Container\CreateContainerRequest;
use App\Http\Requests\Container\DownloadFileRequest;
use App\Http\Resources\ContainerResource;
use App\Models\Company;
use App\Models\ContainerSigner;
use App\Models\SignatureContainer;
use App\Models\UnsignedFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilesController extends Controller
{
    public function createSignatureContainer(CreateContainerRequest $request, Company $company)
    {
        DB::beginTransaction();

        $container                 = new SignatureContainer();
        $container->container_type = $request->input('signature_type') === 'crypto' ? "asice" : "pdf";
        $container->public_id      = Str::random(20);
        $container->company_id     = $company->id;
        $container->save();

        // TODO implement saving people

        if ($request->has('signers')) {
            foreach ($request->input('signers') as $signer) {
                $containerSigner                         = new ContainerSigner();
                $containerSigner->identifier             = $signer['identifier'];
                $containerSigner->identifier_type        = $signer['identifier_type'];
                $containerSigner->country                = $signer['country'] ?? null;
                $containerSigner->visual_coordinates     = $signer['visual_coordinates'] ?? null;
                $containerSigner->signature_container_id = $container->id;
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

            $storagePath                = $unsignedFile->storagePath();
            $unsignedFile->storage_path = $storagePath;
            $unsignedFile->size         = Storage::size($storagePath);
            $unsignedFile->mime_type    = $fileData['mime'];

            Storage::put($storagePath, $fileContent);

            $unsignedFile->save();
            $zip->addFromString($name, $fileContent);

            // Add file metadata to container manifest.xml.
            $newFileEntry = $manifest->addChild('file-entry');
            $newFileEntry->addAttribute('manifest:full-path', $name, $namespace);
            $xmlMime = str_replace("/", "-", $fileData['mime']);
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
