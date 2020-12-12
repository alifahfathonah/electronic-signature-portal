<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\CreateNewCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\User;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function getContainerStatuses(Request $request, Company $company)
    {
        // TODO get all containers that currently logger in user has access to.
        // All company containers if is company admin
        $user = $request->user();

        // All personal company related containers if is not admin
    }

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

    public function store(CreateNewCompanyRequest $request, CompanyService $companyService)
    {
        DB::beginTransaction();

        $company = Company::create([
            'url_slug' => $request->input('url_slug'),
        ]);

        $company->users()->attach(Auth::id(), ['role' => User::ROLE_ADMIN]);

        DB::commit();

        $companies = $companyService->getUserCompanies(Auth::id());

        return response([
            'companies' => CompanyResource::collection($companies),
        ]);
    }

    public function update(UpdateCompanyRequest $request, CompanyService $companyService)
    {
        $company = Company::where('url_slug', $request->route('url_slug'))->firstOrFail();
        $company->update($request->all());

        $companies = $companyService->getUserCompanies(Auth::id());

        return response([
            'companies' => CompanyResource::collection($companies),
        ]);
    }
}
