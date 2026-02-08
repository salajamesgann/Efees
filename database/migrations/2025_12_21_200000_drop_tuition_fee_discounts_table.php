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
        Schema::dropIfExists('tuition_fee_discounts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('tuition_fee_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tuition_fee_id')->constrained('tuition_fees')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }
};
