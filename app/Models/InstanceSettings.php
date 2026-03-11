<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    description: 'Instance Settings',
    type: 'object',
    properties: [
        'id' => ['type' => 'integer', 'description' => 'The instance settings identifier in the database.'],
        'fqdn' => ['type' => 'string', 'description' => 'The instance FQDN.'],
        'is_registration_enabled' => ['type' => 'boolean', 'description' => 'Whether registration is enabled.'],
        'is_registration_enabled_for_oauth' => ['type' => 'boolean', 'description' => 'Whether registration via OAuth2 is enabled even when general registration is disabled.'],
        'is_oauth_only_registration' => ['type' => 'boolean', 'description' => 'Whether users are restricted to OAuth2 only (no password login).'],
    ],
)]
class InstanceSettings extends Model
{
    protected $table = 'instance_settings';

    protected $guarded = [];

    protected $casts = [
        'is_registration_enabled' => 'boolean',
        'is_registration_enabled_for_oauth' => 'boolean',
        'is_oauth_only_registration' => 'boolean',
        'is_smtp_enabled' => 'boolean',
        'is_resend_enabled' => 'boolean',
        'smtp_ssl' => 'boolean',
        'smtp_tls' => 'boolean',
        'is_auto_update_enabled' => 'boolean',
        'is_dns_validation_enabled' => 'boolean',
        'do_not_track' => 'boolean',
    ];
}
