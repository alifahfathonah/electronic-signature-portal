<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class SignatureContainer
 *
 * @package App\Models
 * @mixin Eloquent
 * @property int $id
 * @property string $container_type
 * @property string|null $container_path
 * @property string $public_id
 * @property string $security
 * @property UnsignedFile[]|Collection $files
 * @property User[]|Collection $users
 * @property string $company_id
 * @property Company $company
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class SignatureContainer extends Model
{
    use HasFactory;

    public const LEVEL_OWNER = "owner";
    public const LEVEL_SIGNER = "signer";
    public const LEVEL_VIEWER = "viewer";

    public const ACCESS_WHITELIST = "whitelist";
    public const ACCESS_PUBLIC = "public";

    public function generatePath(): string
    {
        return "containers/$this->id/container-$this->id.$this->container_type";
    }

    public function files()
    {
        return $this->hasMany(UnsignedFile::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot(['access_level', 'signed_at', 'visual_coordinates']);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
