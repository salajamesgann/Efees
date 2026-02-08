<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');
            $table->decimal('amount_paid', 10, 2);
            $table->string('method');
            $table->string('reference_number')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->index(['student_id', 'paid_at']);
            $table->index('method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
