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
        Schema::create('student_sms_preferences', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');
            $table->boolean('sms_due_reminder_enabled')->default(true);
            $table->boolean('sms_payment_confirm_enabled')->default(true);
            $table->boolean('sms_overdue_enabled')->default(true);
            $table->unsignedBigInteger('updated_by')->nullable(); // User ID of who updated it
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');

            $table->unique('student_id'); // Ensure one preference record per student
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_sms_preferences');
    }
};
