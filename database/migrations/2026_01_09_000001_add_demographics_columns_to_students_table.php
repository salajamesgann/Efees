<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('first_name');
            }
            if (! Schema::hasColumn('students', 'suffix')) {
                $table->string('suffix', 20)->nullable()->after('last_name');
            }
            if (! Schema::hasColumn('students', 'nationality')) {
                $table->string('nationality', 100)->nullable()->after('date_of_birth');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('students', 'middle_name') ? 'middle_name' : null,
                Schema::hasColumn('students', 'suffix') ? 'suffix' : null,
                Schema::hasColumn('students', 'nationality') ? 'nationality' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
