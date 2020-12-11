<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\ContainerSigner
 *
 * @property int $id
 * @property string $public_id
 * @property int $signature_container_id
 * @property string $identifier
 * @property string $identifier_type
 * @property string|null $country
 * @property mixed|null $visual_coordinates
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ContainerSigner extends Model
{
    use HasFactory;
}
