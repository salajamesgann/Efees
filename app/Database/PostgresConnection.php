<?php

namespace App\Database;

use Illuminate\Database\PostgresConnection as BasePostgresConnection;

class PostgresConnection extends BasePostgresConnection
{
    /**
     * Prepare the query bindings for execution.
     *
     * When ATTR_EMULATE_PREPARES is enabled (required for PgBouncer/Supabase
     * transaction-pooling mode), PDO interpolates bindings into the SQL string
     * directly. PHP's default boolean-to-int cast produces WHERE "col" = 1,
     * which PostgreSQL rejects because boolean != integer.
     *
     * This override converts booleans to the string literals 'true'/'false'
     * that PostgreSQL understands.
     */
    public function prepareBindings(array $bindings)
    {
        $grammar = $this->getQueryGrammar();

        foreach ($bindings as $key => $value) {
            if ($value instanceof \DateTimeInterface) {
                $bindings[$key] = $value->format($grammar->getDateFormat());
            } elseif (is_bool($value)) {
                $bindings[$key] = $value ? 'true' : 'false';
            }
        }

        return $bindings;
    }
}
