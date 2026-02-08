<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_reports', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->text('file_url');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->index(['type', 'created_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_reports');
    }
};
