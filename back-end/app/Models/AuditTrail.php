<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AuditTrail
 *
 * @property int $id
 * @property string $action_type
 * @property string $ip
 * @property string $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class AuditTrail extends Model
{
    public const ACTION_SENT = "sent";
    public const ACTION_OPENED = "opened";
    public const ACTION_SIGNED = "signed";

    use HasFactory;
}
