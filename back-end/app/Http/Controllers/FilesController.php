<?php

namespace App\Http\Controllers;

use App\Http\Requests\Container\CreateContainerRequest;
use App\Models\Company;
use App\Models\SignatureContainer;
use App\Models\UnsignedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilesController extends Controller
{
    public function createSignatureContainer(CreateContainerRequest $request)
    {
        DB::beginTransaction();

        $container                 = new SignatureContainer();
        $container->container_type = "asice";
        $container->container_path = "container.asice";
        $container->public_id      = Str::random(20);
        $container->company_id     = Company::where('url_slug', $request->route('url_slug'))->firstOrFail()->id;
        $container->save();

        $user = $request->user();
        $container->users()->attach($user->id, ['access_level' => SignatureContainer::LEVEL_OWNER]);

        $this->storeFiles($request, $container);

        DB::commit();

        return response()->json([
            'id'    => $container->id,
            'files' => $container->files()->select(['id', 'name'])->get()
        ]);
    }

    public function getFiles(Request $request)
    {

        // TODO check permissions
        $signatureContainer = SignatureContainer::find($request->fileid);

        // TODO ge actual signature container files
        $files   = [];
        $files[] = [
            'fileName'    => 'test.pdf',
            'fileContent' => Storage::get('/company1/1/test.pdf'),
        ];
        $files[] = [
            'fileName'    => 'test.txt',
            'fileContent' => Storage::get('/company1/1/test.txt')
        ];

        return response()->json($files);
    }

    private function storeFiles(Request $request, SignatureContainer $container)
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
            Storage::put("/containers/$container->id/$name", $fileContent);

            // Add file to the container.
            $unsignedFile->signature_container_id = $container->id;
            $unsignedFile->name                   = $name;
            $unsignedFile->storage_path           = $name;
            $unsignedFile->mime_type              = $fileData['mime'];
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

        Storage::put("/containers/$container->id/container-" . now()->timestamp . ".asice", file_get_contents($zipPath));
    }
}
