<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    public function getFiles($containerId)
    {
        $files   = [];
        $files[] = [
            'fileName'    => 'test.pdf',
            'fileContent' => Storage::get('/company1/test.pdf')
        ];
        $files[] = [
            'fileName'    => 'test.txt',
            'fileContent' => Storage::get('/company1/test.txt')
        ];

        return response()->json($files);
    }
}
