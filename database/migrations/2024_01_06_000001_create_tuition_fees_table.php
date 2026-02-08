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
        if (! Schema::hasTable('tuition_fees')) {
            Schema::create('tuition_fees', function (Blueprint $table) {
                $table->id();
                $table->string('grade_level');
                $table->decimal('amount', 10, 2);
                $table->string('school_year');
                $table->string('semester');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['grade_level', 'school_year', 'semester']);
                $table->index('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuition_fees');
    }
};
