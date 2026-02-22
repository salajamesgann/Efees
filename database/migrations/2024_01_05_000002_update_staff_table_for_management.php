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
        // First, let's check if the old STAFF table exists and migrate data
        if (Schema::hasTable('STAFF')) {
            // Create new staff table with Laravel conventions
            Schema::create('staff', function (Blueprint $table) {
                $table->id('staff_id');
                $table->string('first_name', 100);
                $table->string('middle_initial', 1)->nullable();
                $table->string('last_name', 100);
                $table->string('contact_number', 15);
                $table->string('email')->nullable();
                $table->string('position', 100)->nullable();
                $table->string('department', 100)->nullable();
                $table->decimal('salary', 10, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });

            // Migrate data from old STAFF table if it exists
            $oldStaffData = DB::table('STAFF')->get();
            foreach ($oldStaffData as $oldStaff) {
                DB::table('staff')->insert([
                    'staff_id' => $oldStaff->staff_id,
                    'first_name' => $oldStaff->first_name,
                    'middle_initial' => $oldStaff->MI,
                    'last_name' => $oldStaff->last_name,
                    'contact_number' => $oldStaff->contact_number,
                    'email' => null, // We'll need to add this field
                    'position' => $oldStaff->position,
                    'department' => $oldStaff->department,
                    'salary' => $oldStaff->salary,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Drop old table after migration
            Schema::dropIfExists('STAFF');
        } else {
            // Create staff table if it doesn't exist
            Schema::create('staff', function (Blueprint $table) {
                $table->id('staff_id');
                $table->string('first_name', 100);
                $table->string('middle_initial', 1)->nullable();
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
     * Reverse the migrations.
     */
    public function down(): void
    {
        // If we need to rollback, we'll need to recreate the old table structure
        // For now, we'll just drop the new table
        Schema::dropIfExists('staff');
    }
};
