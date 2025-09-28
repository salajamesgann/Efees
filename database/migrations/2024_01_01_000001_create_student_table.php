<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('STUDENT', function (Blueprint $table) {
    //         $table->string('student_id')->primary();
    //         $table->string('first_name')->nullable();
    //         $table->string('MI', 1)->nullable();
    //         $table->string('last_name')->nullable();
    //         $table->bigInteger('contact_number')->nullable();
    //         $table->string('level')->nullable();
    //     });
    // }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('STUDENT');
    }
};
