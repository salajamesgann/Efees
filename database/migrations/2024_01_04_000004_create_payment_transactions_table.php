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
        if (! Schema::hasTable('payment_transactions')) {
            Schema::create('payment_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('student_id');
                $table->decimal('amount', 10, 2);
                $table->string('type');
                $table->text('note')->nullable();
                $table->unsignedBigInteger('staff_user_id')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
                $table->foreign('staff_user_id')->references('user_id')->on('users')->onDelete('set null');
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
