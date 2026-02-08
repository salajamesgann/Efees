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
        Schema::create('fee_records', function (Blueprint $table) {
            $table->string('record_id')->primary();
            $table->bigInteger('fee_id');
            $table->string('balance')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('record_id')->references('student_id')->on('students');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_records');
    }
};
