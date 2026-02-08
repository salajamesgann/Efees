<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsActiveToStaffTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (! Schema::hasColumn('staff', 'is_active')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('position');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('staff', 'is_active')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
}
