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
        // Main fee assignments table
        if (! Schema::hasTable('fee_assignments')) {
            Schema::create('fee_assignments', function (Blueprint $table) {
                $table->id();
                $table->string('student_id');
                $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
                $table->foreignId('tuition_fee_id')->nullable()->constrained('tuition_fees')->nullOnDelete();
                $table->json('additional_charge_ids')->nullable();
                $table->json('discount_ids')->nullable();
                $table->decimal('base_tuition', 10, 2)->default(0);
                $table->decimal('additional_charges_total', 10, 2)->default(0);
                $table->decimal('discounts_total', 10, 2)->default(0);
                $table->decimal('total_amount', 10, 2)->default(0);
                $table->string('school_year')->nullable();
                $table->string('semester')->nullable();
                $table->boolean('is_finalized')->default(false);
                $table->timestamp('finalized_at')->nullable();
                $table->timestamps();

                $table->index(['student_id', 'school_year', 'semester']);
                $table->index('is_finalized');
            });
        }

        // Pivot: fee_assignment_additional_charges
        if (! Schema::hasTable('fee_assignment_additional_charges')) {
            Schema::create('fee_assignment_additional_charges', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('fee_assignment_id');
                $table->unsignedBigInteger('additional_charge_id');
                $table->timestamps();

                $table->unique(['fee_assignment_id', 'additional_charge_id']);
                $table->index(['additional_charge_id']);
            });

            // Add foreign keys only if referenced tables exist
            if (Schema::hasTable('fee_assignments')) {
                Schema::table('fee_assignment_additional_charges', function (Blueprint $table) {
                    $table->foreign('fee_assignment_id')->references('id')->on('fee_assignments')->onDelete('cascade');
                });
            }
            if (Schema::hasTable('additional_charges')) {
                Schema::table('fee_assignment_additional_charges', function (Blueprint $table) {
                    $table->foreign('additional_charge_id')->references('id')->on('additional_charges')->onDelete('cascade');
                });
            }
        }

        // Pivot: fee_assignment_discounts
        if (! Schema::hasTable('fee_assignment_discounts')) {
            Schema::create('fee_assignment_discounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('fee_assignment_id');
                $table->unsignedBigInteger('discount_id');
                $table->decimal('applied_amount', 10, 2)->default(0);
                $table->timestamps();

                $table->unique(['fee_assignment_id', 'discount_id']);
                $table->index(['discount_id']);
            });

            // Add foreign keys only if referenced tables exist
            if (Schema::hasTable('fee_assignments')) {
                Schema::table('fee_assignment_discounts', function (Blueprint $table) {
                    $table->foreign('fee_assignment_id')->references('id')->on('fee_assignments')->onDelete('cascade');
                });
            }
            if (Schema::hasTable('discounts')) {
                Schema::table('fee_assignment_discounts', function (Blueprint $table) {
                    $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_assignment_discounts');
        Schema::dropIfExists('fee_assignment_additional_charges');
        Schema::dropIfExists('fee_assignments');
    }
};
