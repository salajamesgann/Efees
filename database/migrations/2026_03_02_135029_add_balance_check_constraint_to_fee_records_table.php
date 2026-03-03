<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a database-level check constraint so that the `balance` column
     * in `fee_records` cannot go below zero — except for rows whose
     * `record_type` is 'adjustment' (which uses a signed balance as a
     * credit/debit marker).
     *
     * This acts as a second line of defence behind the model's saving hook.
     */
    public function up(): void
    {
        if (! Schema::hasTable('fee_records')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL (Supabase) — supports conditional check constraints natively
            DB::statement("
                ALTER TABLE fee_records
                ADD CONSTRAINT chk_fee_records_balance_non_negative
                CHECK (balance >= 0 OR record_type = 'adjustment')
            ");

            // Also guard `amount` — it is always a non-negative magnitude
            DB::statement("
                ALTER TABLE fee_records
                ADD CONSTRAINT chk_fee_records_amount_non_negative
                CHECK (amount >= 0)
            ");
        } elseif ($driver === 'mysql' || $driver === 'mariadb') {
            // MySQL 8.0.16+ / MariaDB 10.2+ support CHECK constraints
            DB::statement("
                ALTER TABLE fee_records
                ADD CONSTRAINT chk_fee_records_balance_non_negative
                CHECK (balance >= 0 OR record_type = 'adjustment')
            ");

            DB::statement("
                ALTER TABLE fee_records
                ADD CONSTRAINT chk_fee_records_amount_non_negative
                CHECK (amount >= 0)
            ");
        }
        // SQLite (used in testing) does not support ALTER TABLE ADD CONSTRAINT CHECK;
        // the model saving hook provides the guard in that case.
    }

    public function down(): void
    {
        if (! Schema::hasTable('fee_records')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE fee_records DROP CONSTRAINT IF EXISTS chk_fee_records_balance_non_negative');
            DB::statement('ALTER TABLE fee_records DROP CONSTRAINT IF EXISTS chk_fee_records_amount_non_negative');
        } elseif ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE fee_records DROP CHECK chk_fee_records_balance_non_negative');
            DB::statement('ALTER TABLE fee_records DROP CHECK chk_fee_records_amount_non_negative');
        }
    }
};

