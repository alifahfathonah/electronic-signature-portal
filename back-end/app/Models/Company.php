<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
