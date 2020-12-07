<?php

namespace App\Http\Controllers;

use App\Models\SignatureContainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function getFiles(Request $request) {

        // TODO check permissions
        $signatureContainer = SignatureContainer::find($request->fileid);

        // TODO ge actual signature container files
        $files   = [];
        $files[] = [
            'fileName'    => 'test.pdf',
            'fileContent' => Storage::get('/company1/1/test.pdf')
        ];
        $files[] = [
            'fileName'    => 'test.txt',
            'fileContent' => Storage::get('/company1/1/test.txt')
        ];

        return response()->json($files);
    }
}
