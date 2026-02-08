<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tuition_fees', function (Blueprint $table) {
            $table->json('default_charge_ids')->nullable()->after('default_discount_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tuition_fees', function (Blueprint $table) {
            $table->dropColumn('default_charge_ids');
        });
    }
};
