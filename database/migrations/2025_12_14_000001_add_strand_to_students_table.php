<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'strand')) {
                $table->string('strand', 50)->nullable();
            }
        });

        try {
            if (Schema::hasColumn('students', 'department')) {
                DB::table('students')
                    ->whereIn('department', ['STEM', 'ABM', 'HUMSS', 'GAS', 'TVL'])
                    ->update([
                        'strand' => DB::raw('department'),
                    ]);
            }
        } catch (\Throwable $e) {
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'strand')) {
                $table->dropColumn('strand');
            }
        });
    }
};
