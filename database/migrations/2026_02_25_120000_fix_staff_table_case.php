<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure the 'staff' table exists on PostgreSQL, migrating from legacy 'STAFF' if necessary.
     */
    public function up(): void
    {
        if (Schema::hasTable('staff')) {
            return;
        }

        if (Schema::hasTable('STAFF')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->string('staff_id', 20)->primary();
                $table->string('first_name', 100);
                $table->string('MI', 1)->nullable();
                $table->string('last_name', 100);
                $table->string('contact_number', 15);
                $table->string('email')->nullable();
                $table->string('position', 100)->nullable();
                $table->string('department', 100)->nullable();
                $table->decimal('salary', 10, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            $legacy = DB::table('STAFF')->get();
            foreach ($legacy as $row) {
                DB::table('staff')->insert([
                    'staff_id' => $row->staff_id,
                    'first_name' => $row->first_name ?? '',
                    'MI' => $row->MI ?? null,
                    'last_name' => $row->last_name ?? '',
                    'contact_number' => $row->contact_number ?? '',
                    'email' => null,
                    'position' => $row->position ?? null,
                    'department' => $row->department ?? null,
                    'salary' => $row->salary ?? null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Schema::dropIfExists('STAFF');
        } else {
            Schema::create('staff', function (Blueprint $table) {
                $table->string('staff_id', 20)->primary();
                $table->string('first_name', 100);
                $table->string('MI', 1)->nullable();
                $table->string('last_name', 100);
                $table->string('contact_number', 15);
                $table->string('email')->nullable();
                $table->string('position', 100)->nullable();
                $table->string('department', 100)->nullable();
                $table->decimal('salary', 10, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Drop the 'staff' table on rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
