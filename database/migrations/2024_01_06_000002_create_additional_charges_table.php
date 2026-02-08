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
        Schema::create('additional_charges', function (Blueprint $table) {
            $table->id();
            $table->string('charge_name'); // e.g., "Laboratory Fee", "Library Fee"
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->json('applicable_grades'); // Array of grade levels ["Grade 7", "Grade 8", "Grade 9"]
            $table->boolean('is_active')->default(true);
            $table->boolean('is_mandatory')->default(true); // Whether this charge is mandatory
            $table->timestamps();

            $table->index('charge_name');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_charges');
    }
};
