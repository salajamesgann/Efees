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
            // Remove JSON columns that should be separate tables
            if (Schema::hasColumn('tuition_fees', 'additional_charges')) {
                $table->dropColumn('additional_charges');
            }
            if (Schema::hasColumn('tuition_fees', 'discount_type')) {
                $table->dropColumn('discount_type');
            }
            if (Schema::hasColumn('tuition_fees', 'discount_value')) {
                $table->dropColumn('discount_value');
            }
            if (Schema::hasColumn('tuition_fees', 'discount_description')) {
                $table->dropColumn('discount_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tuition_fees', function (Blueprint $table) {
            $table->json('additional_charges')->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->string('discount_description')->nullable();
        });
    }
};
