<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('parents', 'preferred_contact_method')) {
            Schema::table('parents', function (Blueprint $table) {
                $table->dropColumn('preferred_contact_method');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('parents', 'preferred_contact_method')) {
            Schema::table('parents', function (Blueprint $table) {
                $table->enum('preferred_contact_method', ['sms', 'email', 'both'])->default('sms');
            });
        }
    }
};
