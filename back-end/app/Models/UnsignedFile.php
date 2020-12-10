<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UnsignedFile
 *
 * @property int $id
 * @property string $name
 * @property string $mime_type
 * @property string $storage_path
 * @property int $signature_container_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class UnsignedFile extends Model
{
    use HasFactory;
}
