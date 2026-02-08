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
<<<<<<< HEAD
        Schema::create('fee_records', function (Blueprint $table) {
=======
        Schema::create('FEE_RECORD', function (Blueprint $table) {
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            $table->string('record_id')->primary();
            $table->bigInteger('fee_id');
            $table->string('balance')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

<<<<<<< HEAD
            $table->foreign('record_id')->references('student_id')->on('students');
=======
            $table->foreign('record_id')->references('student_id')->on('STUDENT');
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<< HEAD
        Schema::dropIfExists('fee_records');
=======
        Schema::dropIfExists('FEE_RECORD');
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
    }
};
