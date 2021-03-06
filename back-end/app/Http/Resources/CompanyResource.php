<?php

namespace App\Http\Resources;

use App\Models\Company;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $toArray = parent::toArray($request);

        /** @var $this Company */
        if ($this->eid_client_id) {
            $toArray['eid_client_id'] = $this->eid_client_id;
        }
        if ($this->eid_secret) {
            $toArray['eid_secret'] = isset($this->eid_secret);
        }

        $toArray['role'] = $this->users()
            ->where('users.id', Auth::id())
            ->withPivot('role')
            ->select(['users.id'])
            ->first()
            ->pivot['role'];

        return $toArray;
    }
}
