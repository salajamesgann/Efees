<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\User;
use App\Models\Student;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\FeeRecord;
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
            'total_users' => User::whereHas('role', function ($query) {
                $query->whereRaw('LOWER(role_name) != ?', ['super_admin']);
            })->count(),
            'recent_activity' => AuditLog::with('user')->latest()->limit(5)->get(),
            'role_distribution' => User::select('role_id', DB::raw('count(*) as total'))
                ->whereHas('role', function ($query) {
                    $query->whereRaw('LOWER(role_name) != ?', ['super_admin']);
                })
                ->with('role')
                ->groupBy('role_id')
                ->get(),
            
            // Financial Stats
            // 'total_collected' should include all successful payment statuses:
            // - 'confirmed': PayMongo confirmed
            // - 'approved': Staff cash payment approved by admin
            // - 'paid' / 'success': Fallback successful statuses
            'total_collected' => Payment::whereIn('status', ['confirmed', 'approved', 'paid', 'success'])->sum('amount_paid'),
            
            // 'total_outstanding' should exclude:
            // - 'tuition_total' (to avoid double counting with individual 'tuition' records)
            // - 'payment' (payments are credited, not debited)
            // - 'adjustment' (we'll sum adjustments separately or include if signed balance is correct)
            // Actually, adjustment records carry the net effect of discounts/charges.
            'total_outstanding' => FeeRecord::where('status', '!=', 'paid')
                ->whereNotIn('record_type', ['tuition_total', 'payment'])
                ->sum('balance'),
            
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
