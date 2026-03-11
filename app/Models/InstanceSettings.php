<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    description: 'Instance Settings',
    type: 'object',
    properties: [
        'id' => ['type' => 'integer'],
        'fqdn' => ['type' => 'string', 'nullable' => true],
        'resale_license' => ['type' => 'string', 'nullable' => true],
        'is_registration_enabled' => ['type' => 'boolean'],
        'is_registration_enabled_for_oauth' => ['type' => 'boolean'],
        'is_smtp_enabled' => ['type' => 'boolean'],
        'is_resale_enabled' => ['type' => 'boolean'],
    ],
)]
class InstanceSettings extends Model
{
    protected $table = 'instance_settings';

    protected $guarded = [];

    protected $casts = [
        'is_registration_enabled' => 'boolean',
        'is_registration_enabled_for_oauth' => 'boolean',
        'is_smtp_enabled' => 'boolean',
        'is_resale_enabled' => 'boolean',
        'is_auto_update_enabled' => 'boolean',
        'is_dns_validation_enabled' => 'boolean',
        'is_api_enabled' => 'boolean',
    ];

    public static function get()
    {
        return self::findOrFail(0);
    }
}
