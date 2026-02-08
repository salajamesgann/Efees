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
        Schema::create('student_fee_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fee_assignment_id')->nullable();
            $table->string('student_id');
            $table->string('type'); // 'discount' or 'charge'
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('fee_assignment_id')->references('id')->on('fee_assignments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fee_adjustments');
    }
};
