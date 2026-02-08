<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type');
            $table->json('parameters')->nullable();
            $table->string('frequency'); // 'daily', 'weekly', 'monthly'
            $table->timestamp('next_run_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->string('status')->default('active'); // 'active', 'inactive'
            $table->timestamps();

            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->index(['status', 'next_run_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_reports');
    }
};
