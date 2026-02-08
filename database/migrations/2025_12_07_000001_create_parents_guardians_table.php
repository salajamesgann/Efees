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
        Schema::create('parents_guardians', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');
            $table->string('parent_guardian_name');
            $table->string('relationship'); // Father, Mother, Guardian, etc.
            $table->string('contact_number');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_primary_contact')->default(false);
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->index(['student_id', 'is_primary_contact']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents_guardians');
    }
};
