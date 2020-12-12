<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


/**
 * App\Models\ContainerSigner
 *
 * @property int $id
 * @property string $public_id
 * @property int $signature_container_id
 * @property string $identifier
 * @property string $identifier_type
 * @property string|null $country
 * @property array|null $visual_coordinates
 * @property AuditTrail[]|Collection|null $audit_trail
 * @property \Illuminate\Support\Carbon|null $signed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ContainerSigner extends Model
{
    use HasFactory;

    protected $casts = [
        'visual_coordinates' => 'array',
        'activity_log'       => 'array',
    ];

    public function auditTrail()
    {
        return $this->hasMany(AuditTrail::class)->orderBy('created_at');
    }
}
