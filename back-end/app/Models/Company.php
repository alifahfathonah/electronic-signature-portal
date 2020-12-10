<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Company
 *
 * @property string $url_slug
 * @property string $eid_client_id
 * @package App\Models
 * @property int $id
 * @property mixed|null $eid_secret
 * @property string|null $logo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 */

class Company extends Model
{
    use HasFactory;

    protected $casts = [
        'active_methods' => 'object',
        'eid_secret'     => 'encrypted',
    ];

    protected $fillable = [
        'url_slug',
        'eid_client_id',
        'eid_secret',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_users')->withPivot(['role']);
    }
}
