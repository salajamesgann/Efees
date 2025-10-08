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
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role_name', 50)->unique();
            $table->string('description', 255)->nullable();
        });

        // Insert default roles
        DB::table('roles')->insert([
            ['role_name' => 'student', 'description' => 'Student role with access to fee management'],
            ['role_name' => 'admin', 'description' => 'Administrator with full system access'],
            ['role_name' => 'staff', 'description' => 'Staff member with limited administrative access']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
