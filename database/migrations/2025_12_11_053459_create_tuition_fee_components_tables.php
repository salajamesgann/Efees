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
        // Create table for additional charges
        Schema::create('tuition_fee_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tuition_fee_id')->constrained('tuition_fees')->onDelete('cascade');
            $table->string('name');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for discounts
        Schema::create('tuition_fee_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tuition_fee_id')->constrained('tuition_fees')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();
        });

        // Remove old JSON columns from tuition_fees
        Schema::table('tuition_fees', function (Blueprint $table) {
            if (Schema::hasColumn('tuition_fees', 'additional_charges')) {
                $table->dropColumn('additional_charges');
            }
            if (Schema::hasColumn('tuition_fees', 'discounts')) {
                $table->dropColumn('discounts');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuition_fee_discounts');
        Schema::dropIfExists('tuition_fee_charges');

        Schema::table('tuition_fees', function (Blueprint $table) {
            $table->json('additional_charges')->nullable();
            $table->json('discounts')->nullable();
        });
    }
};
