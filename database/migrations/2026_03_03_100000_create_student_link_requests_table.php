<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_link_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->string('student_id');
            $table->enum('type', ['link', 'unlink'])->default('link');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('relationship')->default('Parent');
            $table->text('reason')->nullable();           // Parent's reason / note
            $table->text('admin_remarks')->nullable();     // Admin's reason for rejection
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('user_id')->on('users')->onDelete('set null');

            $table->index(['status', 'created_at']);
            $table->index(['parent_id', 'student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_link_requests');
    }
};
