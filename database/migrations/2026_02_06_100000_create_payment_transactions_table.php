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
        if (!Schema::hasTable('payment_transactions')) {
            Schema::create('payment_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('student_id')->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('type'); // e.g., 'payment'
                $table->text('description')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                // student_id is string in students table, so we can't easily foreign key it if it's not consistent, 
                // but usually it references students.student_id. 
                // Given loose coupling in code, index is good.
                $table->index('student_id');
                $table->index('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
