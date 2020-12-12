<?php

namespace App\Http\Resources;

use App\Models\SignatureContainer;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ContainerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var $this SignatureContainer */
        $data                 = parent::toArray($request);
//        $data['access_level'] = $this->signers->first(function (User $user) {
//                return $user->id === Auth::id();
//            })->pivot->access_level ?? SignatureContainer::LEVEL_SIGNER;

        $files = [];
        foreach ($this->files as $file) {
            $files[] = [
                'id'   => $file->id,
                'name' => $file->name,
                'size' => $file->size,
            ];
        }

        $data['files'] = $files;

        $signers = [];

//        foreach ($this->signers as $user) {
//            $signers[] = [
//                'id'           => $user->id,
//                'country'      => $user->country,
//                'first_name'   => $user->first_name,
//                'last_name'    => $user->last_name,
//                'idcode'       => $user->idcode,
//                'access_level' => $user->pivot->access_level,
//                'signed_at'    => $user->pivot->signed_at,
//            ];
//        }

        $data['signers'] = $signers;

        return $data;
    }

    /**
     * Create new anonymous resource collection.
     *
     * @param mixed $resource
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collection($resource)
    {
        $resource->load(['users' => function ($userQ) {
            $userQ->where('users.id', Auth::id())->selectRaw('1');
        }]);
        $resource->load('files');
        return parent::collection($resource);
    }
}
