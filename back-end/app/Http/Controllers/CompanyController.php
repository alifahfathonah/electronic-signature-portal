<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\CreateNewCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\User;
use App\Services\CompanyService;
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

    public function update(UpdateCompanyRequest $request)
    {
        $companyService = app(CompanyService::class);

        $company = Company::findOrFail($request->route('company_id'));
        $company->update($request->all());

        $companies = $companyService->getUserCompanies(Auth::id());

        return response([
            'companies' => CompanyResource::collection($companies),
        ]);
    }
}
