<?php

namespace App\Models;

use BrowscapPHP\Browscap;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AuditTrail
 *
 * @property int $id
 * @property string $action_type
 * @property string $ip
 * @property string $system_info
 * @property string container_signer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class AuditTrail extends Model
{
    public const ACTION_SENT = "sent";
    public const ACTION_OPENED = "opened";
    public const ACTION_SIGNED = "signed";

    protected $casts = ['system_info' => 'array'];

    use HasFactory;

    public static function boot()
    {
        parent::boot();

        self::saving(function (AuditTrail $trail) {
            $cap               = app(Browscap::class);
            $trail->system_info = $cap->getBrowser();
        });
    }
}
