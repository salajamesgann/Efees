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
        // Drop the old incorrectly named table if it exists
        Schema::dropIfExists('FEE_RECORD');
        // Drop fee_records if it exists (in case it was created by the fixed legacy migration)
        Schema::dropIfExists('fee_records');

        // Create the properly named table
        Schema::create('fee_records', function (Blueprint $table) {
            $table->id();
            $table->string('student_id'); // Foreign key to students table
            $table->string('record_type'); // 'payment', 'adjustment', 'refund', etc.
            $table->decimal('amount', 10, 2);
            $table->decimal('balance', 10, 2);
            $table->string('status')->default('pending'); // 'pending', 'paid', 'overdue', 'cancelled'
            $table->string('payment_method')->nullable(); // 'cash', 'bank_transfer', 'online', etc.
            $table->string('reference_number')->nullable(); // Receipt number, transaction ID, etc.
            $table->text('notes')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');

            $table->index(['student_id', 'status']);
            $table->index('record_type');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_records');

        // Recreate the old table structure for rollback
        Schema::create('FEE_RECORD', function (Blueprint $table) {
            $table->string('record_id')->primary();
            $table->bigInteger('fee_id');
            $table->string('balance')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('record_id')->references('student_id')->on('STUDENT');
        });
    }
};
