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
        if (Schema::hasTable('additional_charges')) {
            Schema::table('additional_charges', function (Blueprint $table) {
                $table->string('charge_type', 20)->default('one_time')->after('description');
                $table->string('school_year', 20)->nullable()->after('charge_type');
                $table->string('applies_to', 10)->default('all')->after('school_year');
                $table->boolean('allow_installment')->default(false)->after('applies_to');
                $table->boolean('include_in_total')->default(true)->after('allow_installment');
                $table->date('due_date')->nullable()->after('include_in_total');
                $table->text('notes')->nullable()->after('due_date');
                $table->string('status', 20)->default('active')->after('notes');
                $table->string('track', 100)->nullable()->after('status');
                $table->string('strand', 100)->nullable()->after('track');
                $table->string('required_or_optional', 20)->default('required')->after('is_mandatory');

                $table->index('charge_type');
                $table->index('school_year');
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('additional_charges')) {
            Schema::table('additional_charges', function (Blueprint $table) {
                $table->dropIndex(['charge_type']);
                $table->dropIndex(['school_year']);
                $table->dropIndex(['status']);
                $table->dropColumn([
                    'charge_type',
                    'school_year',
                    'applies_to',
                    'allow_installment',
                    'include_in_total',
                    'due_date',
                    'notes',
                    'status',
                    'track',
                    'strand',
                    'required_or_optional',
                ]);
            });
        }
    }
};
