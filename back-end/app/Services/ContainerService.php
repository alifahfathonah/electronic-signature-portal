<?php

namespace App\Services;

use App\Models\SignatureContainer;
use App\Models\User;
use Illuminate\Support\Collection;

class ContainerService
{
    public function getUserContainers(int $userId): Collection
    {
        // All containers to which the user has explicit access, or to which the user has access due to being admin of container's company.
        return SignatureContainer::whereHas('users', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        })->orWhereHas('company', function ($companyQ) use ($userId) {
            $companyQ->whereHas('users', function ($userQ) use ($userId) {
                $userQ->where('users.id', $userId)
                    ->where('role', User::ROLE_ADMIN);
            });
        })->get();
    }
}
