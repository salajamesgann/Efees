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
        if (! Schema::hasTable('staff') && ! Schema::hasTable('STAFF')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->string('staff_id', 10)->primary();
                $table->string('first_name', 100);
                $table->string('MI', 1)->nullable();
                $table->string('last_name', 100);
                $table->string('contact_number', 15);
                $table->string('department', 100)->nullable();
                $table->string('position', 100)->nullable();
                $table->decimal('salary', 10, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');

                $table->index(['is_active', 'created_at']);
                $table->index(['department', 'position']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
