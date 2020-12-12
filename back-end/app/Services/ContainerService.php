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
        // TODO
        return collect();
    }
}
