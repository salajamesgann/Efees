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
        Schema::create('strands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Insert default strands
        DB::table('strands')->insert([
            ['name' => 'STEM', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ABM', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HUMSS', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GAS', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'TVL', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strands');
    }
};
