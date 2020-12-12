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
        $data = [
            'container_type' => $this->container_type,
            'public_id'      => $this->public_id,
        ];
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

        foreach ($this->signers as $user) {
            $signers[] = [
                'identifier'      => $user->identifier,
                'identifier_type' => $user->identifier_type,
                'signed_at'       => $user->signed_at,
                // TODO only for testing
                'signature_page'  => "http://localhost:3000/signatures/$this->public_id/signer/$user->public_id"
            ];
        }

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
        $resource->load(['files', 'signers']);
        return parent::collection($resource);
    }
}
