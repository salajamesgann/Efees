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
        if (!Schema::hasColumn('fee_records', 'created_at')) {
            Schema::table('fee_records', function (Blueprint $table) {
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->after('student_id');
            });
        }
        if (!Schema::hasColumn('fee_records', 'updated_at')) {
            Schema::table('fee_records', function (Blueprint $table) {
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->after('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_records', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }
};
