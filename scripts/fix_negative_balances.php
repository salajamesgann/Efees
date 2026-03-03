<?php
// One-time script: clamp existing negative-balance FeeRecord rows (non-adjustment)
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$count = DB::table('fee_records')
    ->where('balance', '<', 0)
    ->where('record_type', '!=', 'adjustment')
    ->count();

echo "Negative non-adjustment rows found: {$count}\n";

if ($count > 0) {
    $updated = DB::table('fee_records')
        ->where('balance', '<', 0)
        ->where('record_type', '!=', 'adjustment')
        ->update(['balance' => 0, 'status' => 'paid']);
    echo "Clamped {$updated} row(s) to balance=0, status='paid'.\n";
} else {
    echo "Nothing to fix — all non-adjustment balances are already >= 0.\n";
}
