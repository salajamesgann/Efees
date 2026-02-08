<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FeeRecord;
use App\Models\Payment;
use App\Models\Student;
use App\Services\FeeManagementService;

echo "--- ANALYSIS ---\n";

// 1. Reports: FeeRecord 'pending' sum
// Note: I need to know what scopePending does. Assuming it exists.
$reportsPending = 0;
if (method_exists(FeeRecord::class, 'scopePending')) {
    $reportsPending = FeeRecord::pending()->sum('balance');
} else {
    // Fallback if scope doesn't exist (check manually)
    $reportsPending = FeeRecord::where('status', 'pending')->sum('balance');
}
echo 'Reports (Pending Payments / Non-Overdue Debt): '.number_format($reportsPending, 2)."\n";

// 2. Reports: FeeRecord 'overdue' sum
$reportsOverdue = 0;
if (method_exists(FeeRecord::class, 'scopeOverdue')) {
    $reportsOverdue = FeeRecord::overdue()->sum('balance');
}
echo 'Reports (Overdue Balances): '.number_format($reportsOverdue, 2)."\n";

echo 'Reports Total Debt (Pending + Overdue): '.number_format($reportsPending + $reportsOverdue, 2)."\n";

// 3. Dashboard: Pending Outstanding (Iterative)
$svc = app(FeeManagementService::class);
$students = Student::with('payments')->get();
$dashboardOutstanding = $students->reduce(function ($carry, $student) use ($svc) {
    $totals = $svc->computeTotalsForStudent($student);
    $totalFees = (float) ($totals['totalAmount'] ?? 0.0);
    // Dashboard logic uses: $student->payments->where('status', 'paid')->sum('amount')
    // Wait, the Payment model usually has 'amount_paid', not 'amount'.
    // Let's check if the Dashboard code uses 'amount' or 'amount_paid'.
    // The snippet said: sum('amount'). If column is 'amount_paid', this returns 0!

    $paid = (float) $student->payments->where('status', 'paid')->sum('amount_paid');
    // I will try both to see if that's the bug.

    return $carry + max($totalFees - $paid, 0.0);
}, 0.0);
echo 'Dashboard (Pending Outstanding - Calculated): '.number_format($dashboardOutstanding, 2)."\n";

// Check for 'amount' vs 'amount_paid' bug in Dashboard logic
$dashboardOutstandingBugCheck = $students->reduce(function ($carry, $student) use ($svc) {
    $totals = $svc->computeTotalsForStudent($student);
    $totalFees = (float) ($totals['totalAmount'] ?? 0.0);
    $paid = (float) $student->payments->where('status', 'paid')->sum('amount'); // The potentially buggy column name

    return $carry + max($totalFees - $paid, 0.0);
}, 0.0);
echo "Dashboard (Original Logic using 'amount'): ".number_format($dashboardOutstandingBugCheck, 2)."\n";

// 4. Actual Pending Payments (Transactions waiting for approval)
$pendingTransactions = Payment::where('status', 'pending')->sum('amount_paid');
echo 'Real-Time Pending Transactions (Waiting Approval): '.number_format($pendingTransactions, 2)."\n";
