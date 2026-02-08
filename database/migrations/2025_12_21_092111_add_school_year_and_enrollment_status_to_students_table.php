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
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'school_year')) {
                $table->string('school_year', 20)->nullable()->after('section');
            }
            if (! Schema::hasColumn('students', 'enrollment_status')) {
                $table->string('enrollment_status', 20)->default('Active')->after('school_year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'enrollment_status')) {
                $table->dropColumn('enrollment_status');
            }
            if (Schema::hasColumn('students', 'school_year')) {
                $table->dropColumn('school_year');
            }
        });
    }
};
