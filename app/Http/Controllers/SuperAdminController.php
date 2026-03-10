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
        $stats = [
            'total_students' => Student::count(),
            'total_users' => User::count(),
            'recent_activity' => AuditLog::with('user')->latest()->limit(5)->get(),
            'role_distribution' => User::select('role_id', DB::raw('count(*) as total'))
                ->with('role')
                ->groupBy('role_id')
                ->get(),
            
            // Financial Stats
            'total_collected' => Payment::whereIn('status', ['confirmed', 'approved'])->sum('amount_paid'),
            'total_outstanding' => FeeRecord::where('status', '!=', 'paid')
                ->where('record_type', '!=', 'tuition_total')
                ->sum('balance'),
            
            // Enrollment Trends
            'enrollment_by_level' => Student::select('level', DB::raw('count(*) as total'))
                ->groupBy('level')
                ->orderBy('level')
                ->get(),
            'enrollment_by_strand' => Student::select('strand', DB::raw('count(*) as total'))
                ->whereNotNull('strand')
                ->groupBy('strand')
                ->get(),
        ];

        return view('super_admin.dashboard', compact('stats'));
    }
}
