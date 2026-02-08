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
        Schema::create('admins', function (Blueprint $table) {
            $table->string('admin_id', 20)->primary();
            $table->string('first_name', 100);
            $table->string('MI', 1)->nullable();
            $table->string('last_name', 100);
            $table->string('contact_number', 15);
            $table->string('department', 100)->nullable();
            $table->string('position', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
