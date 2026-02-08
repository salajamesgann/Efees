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
        // 1. Migrate data from 'parents_guardians' to 'parents' and 'parent_student'
        if (Schema::hasTable('parents_guardians')) {
            $legacyGuardians = DB::table('parents_guardians')->get();

            foreach ($legacyGuardians as $guardian) {
                // Check if parent already exists in 'parents' table (by phone)
                // If phone is missing, we might skip or use name? Phone is usually unique identifier.
                $phone = $guardian->contact_number;
                $name = $guardian->parent_guardian_name;
                
                if (empty($phone)) {
                    continue; // Cannot migrate without phone (key identifier)
                }

                $parent = DB::table('parents')->where('phone', $phone)->first();

                $parentId = null;

                if (!$parent) {
                    // Create new parent
                    $parentId = DB::table('parents')->insertGetId([
                        'full_name' => $name,
                        'phone' => $phone,
                        'email' => $guardian->email,
                        'address_street' => $guardian->address,
                        'account_status' => 'Active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $parentId = $parent->id;
                }

                // Link to student if not already linked
                $exists = DB::table('parent_student')
                    ->where('parent_id', $parentId)
                    ->where('student_id', $guardian->student_id)
                    ->exists();

                if (!$exists) {
                    DB::table('parent_student')->insert([
                        'parent_id' => $parentId,
                        'student_id' => $guardian->student_id,
                        'relationship' => $guardian->relationship ?? 'Guardian',
                        'is_primary' => $guardian->is_primary_contact ?? false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 2. Drop the legacy table
        Schema::dropIfExists('parents_guardians');
        
        // 3. Drop other unused tables
        Schema::dropIfExists('payment_gateways');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the table schema (Data restoration is not feasible without backup)
        Schema::create('parents_guardians', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');
            $table->string('parent_guardian_name')->nullable();
            $table->string('relationship')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_primary_contact')->default(false);
            $table->timestamps();

            // Note: We cannot easily restore the foreign key constraint to students table
            // without knowing the exact previous schema definition, but usually:
            // $table->foreign('student_id')->references('student_id')->on('students');
        });
    }
};
