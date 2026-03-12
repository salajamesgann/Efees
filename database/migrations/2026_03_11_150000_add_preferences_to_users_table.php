<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'preferences')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('preferences')->nullable()->after('password_expires_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'preferences')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('preferences');
            });
        }
    }
};
