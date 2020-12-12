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
