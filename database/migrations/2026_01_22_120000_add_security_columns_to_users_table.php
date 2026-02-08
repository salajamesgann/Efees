<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->unsignedInteger('failed_login_attempts')->default(0)->after('password');
            }
            if (! Schema::hasColumn('users', 'lockout_until')) {
                $table->dateTime('lockout_until')->nullable()->after('failed_login_attempts');
            }
            if (! Schema::hasColumn('users', 'password_expires_at')) {
                $table->dateTime('password_expires_at')->nullable()->after('lockout_until');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->dropColumn('failed_login_attempts');
            }
            if (Schema::hasColumn('users', 'lockout_until')) {
                $table->dropColumn('lockout_until');
            }
            if (Schema::hasColumn('users', 'password_expires_at')) {
                $table->dropColumn('password_expires_at');
            }
        });
    }
};
