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
        // Add additional_charges column if it doesn't exist
        if (! Schema::hasColumn('tuition_fees', 'additional_charges')) {
            Schema::table('tuition_fees', function (Blueprint $table) {
                $table->json('additional_charges')->nullable()->after('payment_schedule');
            });
        }

        // Add subject_fees column if it doesn't exist
        if (! Schema::hasColumn('tuition_fees', 'subject_fees')) {
            Schema::table('tuition_fees', function (Blueprint $table) {
                $table->json('subject_fees')->nullable()->after('additional_charges');
            });
        }

        // Add notes column if it doesn't exist
        if (! Schema::hasColumn('tuition_fees', 'notes')) {
            Schema::table('tuition_fees', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('subject_fees');
            });
        }

        // Add discount_type column if it doesn't exist
        if (! Schema::hasColumn('tuition_fees', 'discount_type')) {
            Schema::table('tuition_fees', function (Blueprint $table) {
                $table->string('discount_type')->nullable()->after('notes');
            });
        }

        // Add discount_value column if it doesn't exist
        if (! Schema::hasColumn('tuition_fees', 'discount_value')) {
            Schema::table('tuition_fees', function (Blueprint $table) {
                $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            });
        }

        // Add discount_description column if it doesn't exist
        if (! Schema::hasColumn('tuition_fees', 'discount_description')) {
            Schema::table('tuition_fees', function (Blueprint $table) {
                $table->string('discount_description')->nullable()->after('discount_value');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tuition_fees', function (Blueprint $table) {
            $columns = ['additional_charges', 'subject_fees', 'notes', 'discount_type', 'discount_value', 'discount_description'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('tuition_fees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
