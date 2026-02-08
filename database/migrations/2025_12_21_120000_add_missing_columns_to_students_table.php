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
            if (! Schema::hasColumn('students', 'address')) {
                $table->string('address', 500)->nullable();
            }
            if (! Schema::hasColumn('students', 'profile_picture_url')) {
                $table->string('profile_picture_url')->nullable();
            }
            if (! Schema::hasColumn('students', 'lrn')) {
                $table->string('lrn')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['address', 'profile_picture_url', 'lrn']);
        });
    }
};
