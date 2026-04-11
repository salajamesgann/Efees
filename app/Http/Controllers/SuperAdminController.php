<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\User;
use App\Models\Student;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\FeeRecord;
use App\Models\SystemSetting;
use App\Models\TuitionFee;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SuperAdminController extends Controller
{
    public function dashboard(): View
    {
        $hasPaymentStatus = Schema::hasColumn('payments', 'status');
        $activeSchoolYear = SystemSetting::getActiveSchoolYear();
        $hasActiveSyTuitionConfigured = ! $activeSchoolYear || TuitionFee::query()
            ->active()
            ->forSchoolYear($activeSchoolYear)
            ->exists();

        $studentsInActiveYear = Student::query()
            ->when($activeSchoolYear, fn ($query) => $query->where('school_year', $activeSchoolYear));

        $gradeLevels = collect(range(1, 12))
            ->map(fn ($grade) => 'Grade ' . $grade);

        $countsByLevel = (clone $studentsInActiveYear)
            ->select('level', DB::raw('count(*) as total'))
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
            'active_school_year' => $activeSchoolYear,
            'financial_data_ready' => $hasActiveSyTuitionConfigured,
            'total_students' => (clone $studentsInActiveYear)->count(),
            'total_users' => User::count(),
            'recent_activity' => AuditLog::with('user')->latest()->limit(5)->get(),
            'role_distribution' => User::select('role_id', DB::raw('count(*) as total'))
                ->with('role')
                ->groupBy('role_id')
                ->get(),
            
            // Financial Stats
            // 'total_collected' should include all successful payment statuses
            'total_collected' => $hasActiveSyTuitionConfigured
                ? Payment::query()
                    ->when($hasPaymentStatus, fn ($q) => $q->whereIn('status', PaymentStatus::successful()))
                    ->when($activeSchoolYear, function ($query) use ($activeSchoolYear) {
                        $query->whereHas('student', function ($studentQuery) use ($activeSchoolYear) {
                            $studentQuery->where('school_year', $activeSchoolYear)
                                ->whereNotIn('enrollment_status', ['Withdrawn', 'Archived']);
                        });
                    })
                    ->sum('amount_paid')
                : 0.0,
            
            // Shared outstanding-debt policy used across dashboards
            'total_outstanding' => $hasActiveSyTuitionConfigured
                ? FeeRecord::outstandingDebt()
                    ->when($activeSchoolYear, function ($query) use ($activeSchoolYear) {
                        $query->whereHas('student', function ($studentQuery) use ($activeSchoolYear) {
                            $studentQuery->where('school_year', $activeSchoolYear)
                                ->whereNotIn('enrollment_status', ['Withdrawn', 'Archived']);
                        });
                    })
                    ->sum('balance')
                : 0.0,
            
            // Enrollment Trends
            'enrollment_by_level' => $enrollmentByLevel,
            'enrollment_by_strand' => (clone $studentsInActiveYear)
                ->select('strand', DB::raw('count(*) as total'))
                ->whereNotNull('strand')
                ->groupBy('strand')
                ->get(),
        ];

        return view('super_admin.dashboard', compact('stats'));
    }
}
