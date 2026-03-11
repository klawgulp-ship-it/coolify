<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instance_settings', function (Blueprint $table) {
            $table->boolean('is_registration_enabled_for_oauth')->default(true)->after('is_registration_enabled');
            $table->boolean('is_oauth_only_registration')->default(false)->after('is_registration_enabled_for_oauth');
        });
    }

    public function down(): void
    {
        Schema::table('instance_settings', function (Blueprint $table) {
            $table->dropColumn('is_registration_enabled_for_oauth');
            $table->dropColumn('is_oauth_only_registration');
        });
    }
};
