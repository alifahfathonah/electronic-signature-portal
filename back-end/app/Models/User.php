<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 *
 * @package App\Models
 * @property string $email
 * @property string $password
 * @property string $idcode
 * @property string $country
 * @property string $first_name
 * @property string $last_name
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'ADMIN';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['idcode', 'country'];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function signatureContainers()
    {
        return $this->belongsToMany(SignatureContainer::class)->withPivot(['access_level']);
    }
}
