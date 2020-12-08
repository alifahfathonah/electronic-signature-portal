<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Collection;

class CompanyService
{
    public function getUserCompanies(int $userId): Collection
    {
        return Company::whereHas('users', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        })->get();
    }
}
