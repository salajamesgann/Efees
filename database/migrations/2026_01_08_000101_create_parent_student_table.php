<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->string('student_id');
            $table->string('relationship');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->unique(['parent_id', 'student_id']);
            $table->index(['student_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_student');
    }
};
