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
        if (! Schema::hasTable('fee_periods')) {
            Schema::create('fee_periods', function (Blueprint $table) {
                $table->id();
                $table->string('type'); // 'school_year', 'semester', 'quarter', 'month'
                $table->string('label'); // '2024-2025', '1st Semester', '1st Quarter', 'January'
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0); // For ordering (e.g., month number, quarter number)
                $table->timestamps();

                $table->index(['type', 'is_active']);
                $table->index('start_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_periods');
    }
};
