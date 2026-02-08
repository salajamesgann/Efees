<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->enum('preferred_contact_method', ['sms', 'email', 'both'])->default('sms');
            $table->enum('account_status', ['Active', 'Archived'])->default('Active');
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->index(['account_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
