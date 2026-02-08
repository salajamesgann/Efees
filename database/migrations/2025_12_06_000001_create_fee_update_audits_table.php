<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_update_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('performed_by_user_id')->nullable();
            $table->string('event_type');
            $table->string('school_year')->nullable();
            $table->string('semester')->nullable();
            $table->unsignedInteger('affected_students_count')->default(0);
            $table->unsignedInteger('affected_staff_count')->default(0);
            $table->text('message')->nullable();
            $table->timestamps();

            $table->foreign('performed_by_user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->index(['event_type']);
            $table->index(['school_year', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_update_audits');
    }
};
