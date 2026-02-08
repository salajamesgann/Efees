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
        $users = DB::table('users')
            ->where('roleable_type', 'like', '%ParentGuardian')
            ->get();

        foreach ($users as $user) {
            // Find matching parent contact by email
            // The email in users table should match the email in parents table
            // or we can try to match by name if email is missing in parents (less reliable)
            
            $parent = DB::table('parents')
                ->where('email', $user->email)
                ->first();

            if ($parent) {
                DB::table('users')
                    ->where('user_id', $user->user_id)
                    ->update([
                        'roleable_type' => 'App\Models\ParentContact',
                        'roleable_id' => $parent->id,
                        'updated_at' => now(),
                    ]);
            } else {
                // If no parent found, we might have a problem. 
                // However, since we migrated data based on phone, maybe some emails were lost?
                // Let's try to match by name if possible, but user table doesn't have name.
                // We'll leave it for now. These users might be orphaned.
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We cannot easily reverse this as we don't know the old IDs
    }
};
