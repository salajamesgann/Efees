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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('payment_type'); // credit_card, debit_card, bank_account, paypal
            $table->string('card_number')->nullable();
            $table->integer('expiry_month')->nullable();
            $table->integer('expiry_year')->nullable();
            $table->string('cvv')->nullable();
            $table->string('account_holder_name');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('routing_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
