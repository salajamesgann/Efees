<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->nullable();
            $table->timestamp('schedule_time');
            $table->text('message');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('set null');
            $table->index(['student_id', 'status']);
            $table->index('schedule_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_schedules');
    }
};
