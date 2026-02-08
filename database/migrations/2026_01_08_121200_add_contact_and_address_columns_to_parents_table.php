<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            if (! Schema::hasColumn('parents', 'phone_secondary')) {
                $table->string('phone_secondary')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('parents', 'address_street')) {
                $table->string('address_street')->nullable()->after('email');
            }
            if (! Schema::hasColumn('parents', 'address_barangay')) {
                $table->string('address_barangay')->nullable()->after('address_street');
            }
            if (! Schema::hasColumn('parents', 'address_city')) {
                $table->string('address_city')->nullable()->after('address_barangay');
            }
            if (! Schema::hasColumn('parents', 'address_province')) {
                $table->string('address_province')->nullable()->after('address_city');
            }
            if (! Schema::hasColumn('parents', 'address_zip')) {
                $table->string('address_zip')->nullable()->after('address_province');
            }
        });
    }

    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            if (Schema::hasColumn('parents', 'phone_secondary')) {
                $table->dropColumn('phone_secondary');
            }
            foreach (['address_street', 'address_barangay', 'address_city', 'address_province', 'address_zip'] as $col) {
                if (Schema::hasColumn('parents', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
