<?php

namespace App\Http\Resources;

use App\Models\SignatureContainer;
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
        $data = parent::toArray($request);
        unset($data['users']);
        $data['access_level'] = $this->users->first()->pivot->access_level;

        $files = [];

        foreach ($this->files as $file) {
            $files[] = [
                'id'   => $file->id,
                'name' => $file->name,
                'size' => $file->size,
            ];
        }

        $data['files'] = $files;

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
