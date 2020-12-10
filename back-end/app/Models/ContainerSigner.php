<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ContainerSigner
 *
 * @property int $id
 * @property string $signature_status
 * @property string $country
 * @property string $idcode
 * @property int $signature_container_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ContainerSigner extends Model
{
    use HasFactory;
}
