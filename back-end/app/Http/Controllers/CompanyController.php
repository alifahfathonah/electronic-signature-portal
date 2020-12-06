<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Check if a URL slug is already taken.
     * */
    public function checkUrlSlug(Request $request)
    {
        $isTaken = Company::where('url_slug', $request->get('url_slug'))->exists();
        return response([
            'is_slug_taken' => $isTaken,
        ]);
    }
}
