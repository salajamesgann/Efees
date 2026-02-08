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
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('user_role')->nullable()->after('user_id');
            $table->string('model_type')->nullable()->after('action'); // e.g., 'App\Models\Student'
            $table->string('model_id')->nullable()->after('model_type'); // Supports both int IDs and string IDs like LRN
            $table->json('old_values')->nullable()->after('details');
            $table->json('new_values')->nullable()->after('old_values');
            $table->text('user_agent')->nullable()->after('ip_address');

            $table->index(['created_at']);
            $table->index(['user_id']);
            $table->index(['action']);
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn([
                'user_role',
                'model_type',
                'model_id',
                'old_values',
                'new_values',
                'user_agent',
            ]);

            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['action']);
            $table->dropIndex(['model_type', 'model_id']);
        });
    }
};
