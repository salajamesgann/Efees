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
        if (! Schema::hasTable('discounts')) {
            Schema::create('discounts', function (Blueprint $table) {
                $table->id();
                $table->string('discount_name');
                $table->string('type'); // 'percentage' or 'fixed'
                $table->decimal('value', 10, 2)->default(0);
                $table->json('eligibility_rules')->nullable();
                $table->text('description')->nullable();
                $table->json('applicable_grades')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_automatic')->default(false);
                $table->integer('priority')->default(0);
                $table->timestamps();

                $table->index('discount_name');
                $table->index('priority');
                $table->index('is_active');
            });
        }

        if (Schema::hasTable('fee_assignment_discounts')) {
            Schema::table('fee_assignment_discounts', function (Blueprint $table) {
                // Add FK to discounts if not already present
                try {
                    $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
                } catch (\Throwable $e) {
                    // Ignore if the foreign key already exists
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('fee_assignment_discounts')) {
            Schema::table('fee_assignment_discounts', function (Blueprint $table) {
                try {
                    $table->dropForeign(['discount_id']);
                } catch (\Throwable $e) {
                }
            });
        }
        Schema::dropIfExists('discounts');
    }
};
