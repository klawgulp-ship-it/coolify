<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('oauth_id')->nullable()->after('password');
            $table->string('oauth_provider')->nullable()->after('oauth_id');
            $table->boolean('is_oauth_only')->default(false)->after('oauth_provider');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['oauth_id', 'oauth_provider', 'is_oauth_only']);
        });
    }
};
