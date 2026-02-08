<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'is_shs_voucher')) {
                $table->boolean('is_shs_voucher')->default(false)->after('strand');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'is_shs_voucher')) {
                $table->dropColumn('is_shs_voucher');
            }
        });
    }
};
