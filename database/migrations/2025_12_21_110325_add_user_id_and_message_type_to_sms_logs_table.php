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
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('student_id');
            $table->string('message_type')->default('reminder')->after('mobile_number');
            // Assuming users table uses id as primary key. If not, adjustment needed.
            // But based on user model, user_id is the custom id, but id is the primary key.
            // Let's check User model or user table migration.
            // user_id in User model seems to be a string or int?
            // In AdminReportsController: Auth::user()->user_id.
            // In create_user_table.php migration I should check.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'message_type']);
        });
    }
};
