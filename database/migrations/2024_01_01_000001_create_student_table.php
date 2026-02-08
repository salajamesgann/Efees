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
        Schema::create('students', function (Blueprint $table) {
            $table->string('student_id')->primary();
            $table->string('first_name')->nullable();
            $table->string('middle_initial', 1)->nullable();
            $table->string('last_name')->nullable();
<<<<<<< HEAD
            $table->string('sex')->nullable();
            $table->string('level')->nullable();
            $table->string('section')->nullable();
=======
            $table->bigInteger('contact_number')->nullable();
            $table->string('sex')->nullable();
            $table->string('level')->nullable();
            $table->string('section')->nullable();
            $table->string('department')->nullable();
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
