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
        Schema::table('USER', function (Blueprint $table) {
            // Remove the old role column and student_id
            $table->dropColumn(['role', 'student_id']);
            
            // Add role_id foreign key
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('role_id')->on('ROLE');
            
            // Add polymorphic columns for the role relationship
            $table->string('roleable_type', 50); // 'Student', 'Admin', or 'Staff'
            $table->string('roleable_id', 20);   // The ID from the respective table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('USER', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'roleable_type', 'roleable_id']);
            
            // Restore old columns
            $table->string('role', 50)->default('student');
            $table->string('student_id', 20)->nullable();
        });
    }
};
