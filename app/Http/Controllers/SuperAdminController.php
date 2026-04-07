<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\User;
use App\Models\Student;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\FeeRecord;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function dashboard(): View
    {
        $gradeLevels = collect(range(1, 12))
            ->map(fn ($grade) => 'Grade ' . $grade);

        $countsByLevel = Student::select('level', DB::raw('count(*) as total'))
            ->whereIn('level', $gradeLevels->all())
            ->groupBy('level')
            ->pluck('total', 'level');

        $enrollmentByLevel = $gradeLevels
            ->map(function ($level) use ($countsByLevel) {
                return (object) [
                    'level' => $level,
                    'total' => (int) ($countsByLevel[$level] ?? 0),
                ];
            });

        $stats = [
            'total_students' => Student::count(),
            'total_users' => User::count(),
            'recent_activity' => AuditLog::with('user')->latest()->limit(5)->get(),
            'role_distribution' => User::select('role_id', DB::raw('count(*) as total'))
                ->with('role')
                ->groupBy('role_id')
                ->get(),
            
            // Financial Stats
            // 'total_collected' should include all successful payment statuses
            'total_collected' => Payment::whereIn('status', PaymentStatus::successful())->sum('amount_paid'),
            
            // Shared outstanding-debt policy used across dashboards
            'total_outstanding' => FeeRecord::outstandingDebt()->sum('balance'),
            
            // Enrollment Trends
            'enrollment_by_level' => $enrollmentByLevel,
            'enrollment_by_strand' => Student::select('strand', DB::raw('count(*) as total'))
                ->whereNotNull('strand')
                ->groupBy('strand')
                ->get(),
        ];

        return view('super_admin.dashboard', compact('stats'));
    }
}
