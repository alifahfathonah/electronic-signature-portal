<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\CreateNewCompanyRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * Check if a URL slug is already taken.
     * */
    public function checkUrlSlug(CreateNewCompanyRequest $request)
    {
        // The relevant check is performed in the FormRequest.
        return response([
            'is_slug_available' => true,
        ]);
    }

    public function store(CreateNewCompanyRequest $request)
    {
        DB::beginTransaction();

        $company = Company::create([
            'url_slug' => $request->slug,
        ]);

        $company->users()->attach(Auth::id(), ['role' => User::ROLE_ADMIN]);

        DB::commit();

        return response([
            'company' => $company,
        ]);
    }
}
