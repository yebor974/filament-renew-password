<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp(config('filament-renew-password.renew_password_timestamp_column'))->nullable();
            $table->boolean(config('filament-renew-password.renew_password_force_column'))->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(config('filament-renew-password.renew_password_force_column'));
            $table->dropColumn(config('filament-renew-password.renew_password_timestamp_column'));
        });
    }

};