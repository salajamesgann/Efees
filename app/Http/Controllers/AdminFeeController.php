<?php

namespace App\Http\Controllers;

use App\Models\AdditionalCharge;
use App\Models\Discount;
use App\Models\FeeAssignment;
use App\Models\Student;
use App\Models\SystemSetting;
use App\Models\TuitionFee;
use App\Models\TuitionFeeCharge;
use App\Models\User;
use App\Services\AuditService;
use App\Services\FeeManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AdminFeeController extends Controller
{
    private function computeNetPayableFromSupabase(string $gradeLevel, float $baseAmount, ?string $track = null, ?string $strand = null): float
    {
        $url = env('SUPABASE_URL', '');
        $serviceKey = env('SUPABASE_SERVICE_KEY', env('SUPABASE_KEY', ''));
        if (! $url || ! $serviceKey) {
            return max(0.0, $baseAmount);
        }
        $headers = [
            'Authorization' => 'Bearer '.$serviceKey,
            'apikey' => $serviceKey,
            'Accept' => 'application/json',
        ];
        $chargesTotal = 0.0;
        try {
            $endpointCharges = rtrim($url, '/').'/rest/v1/additional_charges';
            $or = '(applies_to.eq.all,applicable_grades.cs.'.rawurlencode(json_encode([$gradeLevel])).')';
            $queryCharges = [
                'status' => 'eq.active',
                'include_in_total' => 'eq.true',
                'or' => $or,
                'select' => 'amount,track,strand',
            ];
            $respC = \Illuminate\Support\Facades\Http::withHeaders($headers)->get($endpointCharges, $queryCharges);
            $rowsC = $respC->json() ?? [];
            foreach ($rowsC as $rc) {
                $ct = $rc['track'] ?? null;
                $cs = $rc['strand'] ?? null;
                if ($track && $ct && $ct !== $track) {
                    continue;
                }
                if ($strand && $cs && $cs !== $strand) {
                    continue;
                }
                $chargesTotal += (float) ($rc['amount'] ?? 0);
            }
        } catch (\Throwable $e) {
        }
        $discountsTotal = 0.0;
        try {
            $endpointDiscounts = rtrim($url, '/').'/rest/v1/discounts';
            $queryDiscounts = [
                'is_active' => 'eq.true',
                'is_automatic' => 'eq.true',
                'applicable_grades' => 'cs.'.rawurlencode(json_encode([$gradeLevel])),
                'order' => 'priority.desc',
                'select' => 'type,value,eligibility_rules,priority',
            ];
            $respD = \Illuminate\Support\Facades\Http::withHeaders($headers)->get($endpointDiscounts, $queryDiscounts);
            $rowsD = $respD->json() ?? [];
            $remainingTuition = $baseAmount;
            $remainingCharges = $chargesTotal;
            $remainingTotal = $baseAmount + $chargesTotal;
            $exclusiveApplied = false;
            foreach ($rowsD as $rd) {
                if ($exclusiveApplied) {
                    break;
                }
                $scope = 'total';
                $rules = $rd['eligibility_rules'] ?? [];
                if (is_array($rules) && array_key_exists('apply_scope', $rules)) {
                    $scope = (string) ($rules['apply_scope'] ?? 'total');
                }
                $stackable = true;
                if (is_array($rules) && array_key_exists('is_stackable', $rules)) {
                    $stackable = (bool) ($rules['is_stackable'] ?? true);
                }
                $type = (string) ($rd['type'] ?? 'percentage');
                $value = (float) ($rd['value'] ?? 0);
                $base = 0.0;
                if ($scope === 'tuition_only') {
                    if ($remainingTuition <= 0) {
                        continue;
                    }
                    $base = $remainingTuition;
                } elseif ($scope === 'charges_only') {
                    if ($remainingCharges <= 0) {
                        continue;
                    }
                    $base = $remainingCharges;
                } else {
                    if ($remainingTotal <= 0) {
                        continue;
                    }
                    $base = $remainingTotal;
                }
                $applied = 0.0;
                if ($type === 'percentage') {
                    $applied = ($base * $value) / 100.0;
                } else {
                    $applied = min($value, $base);
                }
                if ($applied > 0) {
                    $discountsTotal += $applied;
                    if ($scope === 'tuition_only') {
                        $remainingTuition = max(0.0, $remainingTuition - $applied);
                    } elseif ($scope === 'charges_only') {
                        $remainingCharges = max(0.0, $remainingCharges - $applied);
                    } else {
                        $remainingTotal = max(0.0, $remainingTotal - $applied);
                    }
                    if (! $stackable) {
                        $exclusiveApplied = true;
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        return max(0.0, $baseAmount + $chargesTotal - $discountsTotal);
    }

    private function notifyStudents(array $studentIds, string $title, string $body): void
    {
        if (empty($studentIds)) {
            return;
        }
        $users = User::where('roleable_type', 'App\\Models\\Student')
            ->whereIn('roleable_id', $studentIds)
            ->get(['user_id']);
        foreach ($users as $u) {
            \Illuminate\Support\Facades\DB::table('notifications')->insert([
                'user_id' => $u->user_id,
                'title' => $title,
                'body' => $body,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function notifyStaff(string $title, string $body): void
    {
        $users = User::where('roleable_type', 'App\\Models\\Staff')
            ->get(['user_id']);
        foreach ($users as $u) {
            \Illuminate\Support\Facades\DB::table('notifications')->insert([
                'user_id' => $u->user_id,
                'title' => $title,
                'body' => $body,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function assignDiscountToGroup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'discount_id' => ['required', 'exists:discounts,id'],
            'group_type' => ['required', 'in:grade,section,strand'],
            'group_value' => ['required', 'string'],
        ]);

        $discount = Discount::findOrFail($validated['discount_id']);

        $studentsQuery = Student::query();

        if ($validated['group_type'] === 'grade') {
            $studentsQuery->where('level', $validated['group_value']);
        } elseif ($validated['group_type'] === 'section') {
            $studentsQuery->where('section', $validated['group_value']);
        } elseif ($validated['group_type'] === 'strand') {
            $studentsQuery->where('strand', $validated['group_value']);
        }

        $students = $studentsQuery->get();
        $count = 0;

        DB::transaction(function () use ($students, $discount, $validated, &$count) {
            foreach ($students as $student) {
                $feeAssignment = $student->getCurrentFeeAssignment();

                if ($feeAssignment) {
                    // Check if already applied
                    if (! $feeAssignment->discounts()->where('discounts.id', $discount->id)->exists()) {
                        $feeAssignment->discounts()->attach($discount->id);
                        $feeAssignment->calculateTotal();
                        app(FeeManagementService::class)->recomputeStudentLedger($student);
                        $count++;
                    }
                }
            }

            AuditService::log(
                'Bulk Discount Assigned',
                auth()->user(),
                "Assigned discount '{$discount->discount_name}' to {$count} students in {$validated['group_type']} {$validated['group_value']}.",
                null,
                ['discount_id' => $discount->id, 'group_type' => $validated['group_type'], 'group_value' => $validated['group_value']]
            );
        });

        return back()->with('success', "Discount assigned to {$count} students successfully.");
    }

    /**
     * Display the fee management dashboard.
     */
    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'tuition');
        $currentTab = in_array($tab, ['tuition', 'charges', 'discounts']) ? $tab : 'tuition';
        $tuitionFees = collect();
        $additionalCharges = collect();
        $discounts = collect();
        $availableCharges = collect();
        $availableDiscounts = collect();
        $gradeLevels = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        if ($currentTab === 'charges') {
            try {
                $query = \App\Models\AdditionalCharge::query();
                if (! $request->filled('school_year')) {
                    $activeYear = SystemSetting::getActiveSchoolYear();
                    if ($activeYear) {
                        $request->merge(['school_year' => $activeYear]);
                    }
                }
                if ($request->filled('school_year')) {
                    $query->where('school_year', $request->school_year);
                }
                $additionalCharges = $query->orderBy('created_at', 'desc')->get();
            } catch (\Throwable $e) {
                $additionalCharges = collect();
            }
        } elseif ($currentTab === 'discounts') {
            try {
                $discounts = \App\Models\Discount::orderBy('priority')->orderBy('created_at', 'desc')->get();
            } catch (\Throwable $e) {
                $discounts = collect();
            }
        } elseif ($currentTab === 'tuition') {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('additional_charges')) {
                    $availableCharges = \App\Models\AdditionalCharge::orderBy('charge_name')->get();
                }
            } catch (\Throwable $e) {
                $availableCharges = collect();
            }
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('discounts')) {
                    $availableDiscounts = \App\Models\Discount::orderBy('priority')->orderBy('discount_name')->get();
                }
            } catch (\Throwable $e) {
                $availableDiscounts = collect();
            }
            try {
                $query = TuitionFee::with(['charges']);

                if (! $request->filled('school_year')) {
                    $activeYear = SystemSetting::getActiveSchoolYear();
                    if ($activeYear) {
                        $request->merge(['school_year' => $activeYear]);
                    }
                }

                if ($request->filled('school_year')) {
                    $query->where('school_year', $request->school_year);
                }
                if ($request->filled('grade_level')) {
                    $query->where('grade_level', $request->grade_level);
                }
                if ($request->filled('status')) {
                    if ($request->status === 'active') {
                        $query->where('is_active', true);
                    } elseif ($request->status === 'inactive') {
                        $query->where('is_active', false);
                    }
                }
                if ($request->filled('q')) {
                    $term = $request->q;
                    $query->where(function ($q) use ($term) {
                        $q->where('notes', 'like', "%{$term}%")
                            ->orWhere('grade_level', 'like', "%{$term}%");
                    });
                }

                $tuitionFees = $query->orderBy('grade_level')->get()->map(function ($tf) {
                    $chargesSummary = (function () use ($tf) {
                        $items = collect($tf->charges ?? []);
                        $names = $items->map(function ($c) {
                            $n = (string) ($c->name ?? '');
                            $a = (float) ($c->amount ?? 0);

                            return $n ? ($n.' (₱'.number_format($a, 2).')') : null;
                        })->filter()->values();

                        return $names->implode(', ');
                    })();
                    $discountsSummary = (function () use ($tf) {
                        $items = $tf->applicableDiscounts()->get();
                        $names = $items->map(function ($d) {
                            $n = (string) ($d->discount_name ?? '');
                            $t = (string) ($d->type ?? 'percentage');
                            $v = (float) ($d->value ?? 0);
                            $disp = $t === 'percentage' ? ($v.'%') : ('₱'.number_format($v, 2));

                            return $n ? ($n.' ('.$disp.')') : null;
                        })->filter()->values();

                        return $names->implode(', ');
                    })();
                    $base = (float) $tf->amount;
                    $chargesTotal = (float) collect($tf->charges ?? [])->sum('amount');
                    $discountBase = $base + $chargesTotal;
                    $discountsTotal = 0.0;
                    foreach ($tf->applicableDiscounts()->get() as $d) {
                        $t = (string) ($d->type ?? 'percentage');
                        $v = (float) ($d->value ?? 0.0);
                        if ($t === 'percentage') {
                            $discountsTotal += ($discountBase * $v) / 100.0;
                        } else {
                            $discountsTotal += min($v, $discountBase);
                        }
                    }

                    return [
                        'id' => $tf->id,
                        'fee_name' => (function () use ($tf) {
                            $notes = (string) ($tf->notes ?? '');
                            $pos = mb_strpos($notes, ' — ');
                            $pos = mb_strpos($notes, ' — ');
                            if ($pos !== false) {
                                return mb_substr($notes, 0, $pos);
                            }

                            return $notes ?: ($tf->grade_level.' Tuition – SY '.($tf->school_year ?? 'N/A'));
                        })(),
                        'school_year' => $tf->school_year ?? 'N/A',
                        'grade_level' => $tf->grade_level,
                        'track' => $tf->track ?? null,
                        'strand' => $tf->strand ?? null,
                        'amount' => (float) $tf->amount,
                        'net_payable' => max(0.0, $discountBase - $discountsTotal),
                        'is_active' => (bool) $tf->is_active,
                        'semester' => $tf->semester ?? 'N/A',
                        'charges_summary' => $chargesSummary,
                        'discounts_summary' => $discountsSummary,
                        'charges_total' => $chargesTotal,
                        'discounts_total' => (float) $discountsTotal,
                        'charges_count' => (int) collect($tf->charges ?? [])->count(),
                        'discounts_count' => (int) $tf->applicableDiscounts()->count(),
                    ];
                });
            } catch (\Throwable $e) {
                $tuitionFees = collect();
            }
        }

        return view('admin.fees.index', [
            'tuitionFees' => $tuitionFees,
            'additionalCharges' => $additionalCharges,
            'discounts' => $discounts,
            'availableCharges' => $availableCharges,
            'availableDiscounts' => $availableDiscounts,
            'gradeLevels' => $gradeLevels,
            'currentTab' => $currentTab,
        ]);
    }

    /**
     * Show the form for creating a new tuition fee.
     */
    public function createTuitionFee(): View
    {
        $availableCharges = collect();
        $availableDiscounts = collect();
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('additional_charges')) {
                $availableCharges = \App\Models\AdditionalCharge::orderBy('charge_name')->get();
            }
        } catch (\Throwable $e) {
        }
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('discounts')) {
                $availableDiscounts = \App\Models\Discount::orderBy('priority')->orderBy('discount_name')->get();
            }
        } catch (\Throwable $e) {
        }

        $activeSchoolYear = SystemSetting::getActiveSchoolYear();

        return view('admin.fees.tuition.create', [
            'gradeLevels' => ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
            'availableCharges' => $availableCharges,
            'availableDiscounts' => $availableDiscounts,
            'activeSchoolYear' => $activeSchoolYear,
        ]);
    }

    /**
     * Store a newly created tuition fee.
     */
    public function storeTuitionFee(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'fee_name' => ['required', 'string', 'max:255'],
            'grade_level' => ['required', 'string', 'in:Grade 7,Grade 8,Grade 9,Grade 10,Grade 11,Grade 12'],
            'subject_fees' => ['required', 'string'],
            'selected_charge_ids' => ['nullable', 'string'],
            'selected_discount_ids' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'school_year' => ['required', 'string'],
            'fee_deadline' => ['required', 'date'],
            'allow_installment' => ['nullable', 'boolean'],
            'payment_plan' => ['nullable', 'in:monthly,quarterly,semester'],
            'track' => ['nullable', 'string', 'max:100'],
            'strand' => ['nullable', 'string', 'max:100'],
        ]);
        $validator->after(function ($v) use ($request) {
            $activeYear = SystemSetting::getActiveSchoolYear();
            if ($activeYear && $request->input('school_year') !== $activeYear) {
                $v->errors()->add('school_year', 'School Year must match the active School Year ('.$activeYear.').');
            }
            $raw = (string) $request->input('subject_fees', '');
            if (strlen($raw)) {
                $parsed = json_decode($raw, true);
                if (! is_array($parsed)) {
                    $v->errors()->add('subject_fees', 'Invalid components format.');
                } else {
                    $baseCount = 0;
                    $subtotal = 0;
                    foreach ($parsed as $comp) {
                        $amt = (float) ($comp['amount'] ?? -1);
                        $label = (string) ($comp['label'] ?? '');
                        $typeOrCat = (string) ($comp['type'] ?? ($comp['category'] ?? ''));
                        if ($amt <= 0) {
                            $v->errors()->add('subject_fees', 'Component amounts must be valid positive numbers.');
                            break;
                        }
                        if ($label === '') {
                            $v->errors()->add('subject_fees', 'Component labels are required.');
                            break;
                        }
                        if ($typeOrCat === 'Base Tuition' || stripos($typeOrCat, 'base') !== false) {
                            $baseCount++;
                        }
                        $subtotal += $amt;
                    }
                    if ($baseCount !== 1) {
                        $v->errors()->add('subject_fees', 'Exactly one Base Tuition component is required.');
                    }
                    if ($subtotal <= 0) {
                        $v->errors()->add('subject_fees', 'Tuition components subtotal must be greater than 0.');
                    }
                }
            } else {
                $v->errors()->add('subject_fees', 'Tuition components are required.');
            }
            $gl = (string) $request->input('grade_level', '');
            if ($gl === 'Grade 11' || $gl === 'Grade 12') {
                $track = (string) $request->input('track', '');
                $strand = (string) $request->input('strand', '');
                if ($track === '') {
                    $v->errors()->add('track', 'Track is required for Senior High School.');
                }
                if ($strand === '') {
                    $v->errors()->add('strand', 'Strand is required for Senior High School.');
                }
            }
        });

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return redirect()->route('admin.fees.create-tuition')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['school_year'] = $validated['school_year'] ?? 'N/A';
        $validated['semester'] = 'N/A';
        $subjectFeesParsed = [];
        $subjectFeesRaw = (string) $request->input('subject_fees', '');
        if (strlen($subjectFeesRaw)) {
            $p = json_decode($subjectFeesRaw, true);
            $subjectFeesParsed = is_array($p) ? $p : [];
        }
        $computedSubtotal = collect($subjectFeesParsed)->reduce(function ($acc, $c) {
            $amt = (float) ($c['amount'] ?? 0);

            return $acc + max(0, $amt);
        }, 0.0);
        $validated['amount'] = $computedSubtotal;

        // Duplicate guard before transaction
        $dupQuery = TuitionFee::where('grade_level', $validated['grade_level'])
            ->where('school_year', $validated['school_year']);
        if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'track')) {
            $dupQuery->where(function ($q) use ($validated) {
                $t = $validated['track'] ?? null;
                if ($t === null || $t === '') {
                    $q->whereNull('track')->orWhere('track', '');
                } else {
                    $q->where('track', $t);
                }
            });
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'strand')) {
            $dupQuery->where(function ($q) use ($validated) {
                $s = $validated['strand'] ?? null;
                if ($s === null || $s === '') {
                    $q->whereNull('strand')->orWhere('strand', '');
                } else {
                    $q->where('strand', $s);
                }
            });
        }
        $existingDup = $dupQuery->first();
        if ($existingDup) {
            $msg = 'Duplicate tuition configuration for this grade, year, and track/strand.';
            if ($request->expectsJson()) {
                return response()->json(['errors' => ['grade_level' => [$msg]]], 422);
            }

            return redirect()->route('admin.fees.create-tuition')
                ->withErrors(['grade_level' => $msg])
                ->withInput();
        }

        try {
            $tuitionFee = null;
            DB::transaction(function () use ($request, $validated, &$tuitionFee) {
                $tuitionFee = TuitionFee::create([
                    'grade_level' => $validated['grade_level'],
                    'amount' => $validated['amount'],
                    'is_active' => $validated['is_active'],
                    'school_year' => $validated['school_year'],
                    'semester' => $validated['semester'],
                    'notes' => (function () use ($validated) {
                        $n = $validated['notes'] ?? null;
                        $fn = $validated['fee_name'] ?? null;
                        if ($fn && $n) {
                            return $fn.' — '.$n;
                        }
                        if ($fn) {
                            return $fn;
                        }

                        return $n;
                    })(),
                ]);
                // Persist track/strand if columns exist
                $updates = [];
                if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'track')) {
                    $updates['track'] = $validated['track'] ?? null;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'strand')) {
                    $updates['strand'] = $validated['strand'] ?? null;
                }
                if (! empty($updates)) {
                    $tuitionFee->update($updates);
                }
                $subjectFeesRaw = $request->input('subject_fees', '');
                if (is_string($subjectFeesRaw) && strlen($subjectFeesRaw) && \Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'subject_fees')) {
                    $parsed = json_decode($subjectFeesRaw, true);
                    $subjectFees = is_array($parsed) ? $parsed : [];
                    if (! empty($subjectFees)) {
                        $tuitionFee->update(['subject_fees' => $subjectFees]);
                    }
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'fee_deadline') && $request->filled('fee_deadline')) {
                    $tuitionFee->update(['fee_deadline' => $request->input('fee_deadline')]);
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'payment_schedule')) {
                    $allow = $request->boolean('allow_installment', false);
                    $plan = $request->input('payment_plan') ?: null;

                    if ($allow && $plan) {
                        $svc = new \App\Services\PaymentScheduleService;
                        // Use fee_deadline as start date if available, else null (defaults to next month)
                        $start = $request->input('fee_deadline');
                        $schedule = $svc->buildSchedule((float) $tuitionFee->amount, $plan, $start);
                    } else {
                        $schedule = ['installment_allowed' => false, 'plan' => null, 'items' => []];
                    }

                    $tuitionFee->update(['payment_schedule' => $schedule]);
                }

                $chargesRaw = $request->input('additional_charges', '');
                $charges = [];
                if (is_string($chargesRaw) && strlen($chargesRaw)) {
                    $parsed = json_decode($chargesRaw, true);
                    $charges = is_array($parsed) ? $parsed : [];
                }
                if (\Illuminate\Support\Facades\Schema::hasTable('tuition_fee_charges')) {
                    foreach ($charges as $charge) {
                        $name = is_array($charge) && array_key_exists('name', $charge) ? (string) $charge['name'] : '';
                        $amount = is_array($charge) && array_key_exists('amount', $charge) ? (float) $charge['amount'] : 0.0;
                        $desc = is_array($charge) && array_key_exists('description', $charge) ? (string) $charge['description'] : null;
                        if ($name || $amount > 0) {
                            $tuitionFee->charges()->create([
                                'name' => $name,
                                'amount' => $amount,
                                'description' => $desc,
                            ]);
                        }
                    }
                }

                if ($tuitionFee->is_active) {
                    if (\Illuminate\Support\Facades\Schema::hasTable('students') && \Illuminate\Support\Facades\Schema::hasTable('fee_assignments')) {
                        $strandFilter = $validated['strand'] ?? null;
                        $studentsQuery = \App\Models\Student::where('level', $validated['grade_level']);
                        if ($strandFilter !== null && $strandFilter !== '' && \Illuminate\Support\Facades\Schema::hasColumn('students', 'strand')) {
                            $studentsQuery->where('strand', $strandFilter);
                        }
                        $students = $studentsQuery->get(['student_id']);
                        foreach ($students as $student) {
                            $existing = \App\Models\FeeAssignment::where('student_id', $student->student_id)
                                ->where('school_year', $validated['school_year'])
                                ->where('semester', $validated['semester'])
                                ->first();
                            if (! $existing) {
                                $fa = \App\Models\FeeAssignment::create([
                                    'student_id' => $student->student_id,
                                    'tuition_fee_id' => $tuitionFee->id,
                                    'base_tuition' => $tuitionFee->amount,
                                    'school_year' => $validated['school_year'],
                                    'semester' => $validated['semester'],
                                ]);
                                $fa->calculateTotal();
                            } else {
                                if (! $existing->is_finalized) {
                                    $existing->tuition_fee_id = $tuitionFee->id;
                                    $existing->base_tuition = $tuitionFee->amount;
                                    $existing->save();
                                    $existing->calculateTotal();
                                }
                            }
                        }
                    }
                }

                $selectedChargeIds = collect(explode(',', (string) $request->input('selected_charge_ids', '')))
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all();

                // Save selected charge IDs as default for this tuition fee
                if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'default_charge_ids')) {
                    $tuitionFee->update(['default_charge_ids' => $selectedChargeIds]);
                }

                $selectedDiscountIds = collect(explode(',', (string) $request->input('selected_discount_ids', '')))
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all();

                // Save selected discount IDs as default for this tuition fee
                if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'default_discount_ids')) {
                    $tuitionFee->update(['default_discount_ids' => $selectedDiscountIds]);
                }

                // Populate TuitionFeeCharge from selected IDs
                if (\Illuminate\Support\Facades\Schema::hasTable('tuition_fee_charges') && ! empty($selectedChargeIds)) {
                    // Check if we already have charges created via JSON input to avoid duplicates?
                    // The JSON input handling above (lines 610-629) uses 'additional_charges' input.
                    // The Add form doesn't send that, so we are safe.
                    $charges = \App\Models\AdditionalCharge::whereIn('id', $selectedChargeIds)->get();
                    foreach ($charges as $charge) {
                        // Avoid duplicate if name matches?
                        // Simple approach: Create.
                        $tuitionFee->charges()->create([
                            'name' => $charge->charge_name,
                            'amount' => $charge->amount,
                            'description' => $charge->description,
                        ]);
                    }
                }

                if (! empty($selectedChargeIds) || ! empty($selectedDiscountIds)) {
                    $grades = [$validated['grade_level']];
                    $studentIds = [];
                    $assignmentIds = [];
                    if (\Illuminate\Support\Facades\Schema::hasTable('students')) {
                        $studentIds = \App\Models\Student::whereIn('level', $grades)->pluck('student_id')->all();
                    }
                    if (\Illuminate\Support\Facades\Schema::hasTable('fee_assignments') && ! empty($studentIds)) {
                        $assignmentIds = \App\Models\FeeAssignment::whereIn('student_id', $studentIds)->pluck('id')->all();
                    }
                    if (! empty($assignmentIds)) {
                        if (! empty($selectedChargeIds)) {
                            if (\Illuminate\Support\Facades\Schema::hasTable('fee_assignment_additional_charges')) {
                                foreach ($selectedChargeIds as $cid) {
                                    app(FeeManagementService::class)->attachChargeToAssignmentsLocal($cid, $assignmentIds);
                                }
                            }
                            try {
                                app(FeeManagementService::class)->syncChargeAssignmentsToSupabaseBatch($selectedChargeIds, $assignmentIds);
                            } catch (\Throwable $e) {
                            }
                        }
                        if (! empty($selectedDiscountIds)) {
                            if (\Illuminate\Support\Facades\Schema::hasTable('fee_assignment_discounts')) {
                                app(FeeManagementService::class)->attachDiscountsToAssignmentsLocal($selectedDiscountIds, $assignmentIds);
                            }
                            try {
                                app(FeeManagementService::class)->syncDiscountAssignmentsToSupabaseBatch($selectedDiscountIds, $assignmentIds);
                            } catch (\Throwable $e) {
                            }
                        }
                    }
                }
            });

            if (\Illuminate\Support\Facades\Schema::hasTable('students')) {
                try {
                    app(FeeManagementService::class)->recomputeForGrade($validated['grade_level']);
                } catch (\Throwable $e) {
                }
            }
            if ($tuitionFee) {
                $payload = array_merge(
                    $tuitionFee->only(['grade_level', 'amount', 'school_year', 'semester', 'is_active', 'notes', 'payment_schedule', 'subject_fees']),
                    [
                        'track' => $validated['track'] ?? null,
                        'strand' => $validated['strand'] ?? null,
                        'fee_deadline' => $validated['fee_deadline'] ?? null,
                        'fee_name' => (function () use ($validated, $tuitionFee) {
                            if (! empty($validated['fee_name'])) {
                                return $validated['fee_name'];
                            }
                            $notes = (string) ($tuitionFee->notes ?? '');
                            $pos = mb_strpos($notes, ' — ');
                            if ($pos !== false) {
                                return mb_substr($notes, 0, $pos);
                            }

                            return null;
                        })(),
                    ]
                );
                try {
                    app(FeeManagementService::class)->syncToSupabase('tuition_fees', $payload, 'id', $tuitionFee->id);
                } catch (\Throwable $e) {
                }
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('fee_update_audits')) {
                try {
                    \Illuminate\Support\Facades\DB::table('fee_update_audits')->insert([
                        'performed_by_user_id' => optional(\Illuminate\Support\Facades\Auth::user())->user_id ?? null,
                        'event_type' => 'tuition_created',
                        'school_year' => $validated['school_year'] ?? 'N/A',
                        'semester' => $validated['semester'] ?? 'N/A',
                        'affected_students_count' => \App\Models\Student::where('level', $validated['grade_level'])->count(),
                        'affected_staff_count' => \App\Models\User::where('roleable_type', 'App\\Models\\Staff')->count(),
                        'message' => 'Tuition fee created with components and attachments.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                }
            }

            // Audit Log
            try {
                AuditService::log(
                    'Tuition Fee Created',
                    $tuitionFee,
                    "Created tuition fee for {$validated['grade_level']} (SY {$validated['school_year']})",
                    null,
                    $tuitionFee->toArray()
                );
            } catch (\Throwable $e) {
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'id' => $tuitionFee?->id,
                    'grade_level' => $tuitionFee?->grade_level,
                    'amount' => (float) ($tuitionFee?->amount ?? ($validated['amount'] ?? 0)),
                    'school_year' => $tuitionFee?->school_year ?? ($validated['school_year'] ?? 'N/A'),
                    'semester' => $tuitionFee?->semester ?? 'N/A',
                    'is_active' => (bool) ($tuitionFee?->is_active ?? true),
                    'notes' => $tuitionFee?->notes ?? null,
                    'created_at' => now()->toISOString(),
                ], 201);
            }

            return redirect()->route('admin.fees.index', ['tab' => 'tuition'])
                ->with('success', 'Tuition fee created successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to create tuition fee. Please try again.'], 500);
            }

            return redirect()->route('admin.fees.create-tuition')
                ->with('error', 'Failed to create tuition fee. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the form for editing a tuition fee.
     */
    public function editTuitionFee(TuitionFee $tuitionFee): View
    {
        $tuitionFee->load(['charges']);

        $availableCharges = collect();
        $availableDiscounts = collect();
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('additional_charges')) {
                $availableCharges = \App\Models\AdditionalCharge::orderBy('charge_name')->get();
            }
        } catch (\Throwable $e) {
        }
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('discounts')) {
                $availableDiscounts = \App\Models\Discount::orderBy('priority')->orderBy('discount_name')->get();
            }
        } catch (\Throwable $e) {
        }

        if (request()->expectsJson()) {
            $notes = (string) ($tuitionFee->notes ?? '');
            $feeName = $notes;
            $pos = mb_strpos($notes, ' — ');
            if ($pos !== false) {
                $feeName = mb_substr($notes, 0, $pos);
            }

            return response()->json([
                'id' => $tuitionFee->id,
                'fee_name' => $feeName ?: null,
                'grade_level' => $tuitionFee->grade_level,
                'school_year' => $tuitionFee->school_year,
                'semester' => $tuitionFee->semester,
                'amount' => (float) $tuitionFee->amount,
                'fee_deadline' => $tuitionFee->fee_deadline,
                'is_active' => (bool) $tuitionFee->is_active,
                'track' => $tuitionFee->track ?? null,
                'strand' => $tuitionFee->strand ?? null,
                'payment_schedule' => is_array($tuitionFee->payment_schedule) ? $tuitionFee->payment_schedule : null,
                'subject_fees' => is_array($tuitionFee->subject_fees) ? $tuitionFee->subject_fees : [],
                'notes' => $tuitionFee->notes,
                'embedded_charges' => $tuitionFee->charges()->get(['name', 'amount', 'description'])->map(function ($c) {
                    return ['name' => $c->name, 'amount' => (float) $c->amount, 'description' => $c->description];
                })->toArray(),
                'embedded_discounts' => [],
                'default_discount_ids' => $tuitionFee->default_discount_ids ?? [],
                'default_charge_ids' => $tuitionFee->default_charge_ids ?? [],
                'available_charge_ids' => $availableCharges->pluck('id')->all(),
                'available_discount_ids' => $availableDiscounts->pluck('id')->all(),
            ]);
        }

        return view('admin.fees.tuition.edit', [
            'tuitionFee' => $tuitionFee,
            'gradeLevels' => ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
            'availableCharges' => $availableCharges,
            'availableDiscounts' => $availableDiscounts,
        ]);
    }

    /**
     * Update the specified tuition fee.
     */
    public function updateTuitionFee(Request $request, TuitionFee $tuitionFee): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'fee_name' => ['nullable', 'string', 'max:255'],
            'grade_level' => ['required', 'string', 'in:Grade 7,Grade 8,Grade 9,Grade 10,Grade 11,Grade 12'],
            'amount' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'subject_fees' => ['nullable', 'string'],
            'additional_charges' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'discounts' => ['nullable', 'string'],
            'fee_deadline' => ['nullable', 'date'],
            'school_year' => ['nullable', 'string'],
            'selected_charge_ids' => ['nullable', 'string'],
            'selected_discount_ids' => ['nullable', 'string'],
            'allow_installment' => ['nullable', 'boolean'],
            'payment_plan' => ['nullable', 'in:monthly,quarterly,semester'],
            'track' => ['nullable', 'string', 'max:100'],
            'strand' => ['nullable', 'string', 'max:100'],
        ]);
        $validator->after(function ($v) use ($request) {
            $raw = (string) $request->input('subject_fees', '');
            if (strlen($raw)) {
                $parsed = json_decode($raw, true);
                if (! is_array($parsed)) {
                    $v->errors()->add('subject_fees', 'Invalid components format.');
                } else {
                    $baseCount = 0;
                    $subtotal = 0;
                    foreach ($parsed as $comp) {
                        $amt = (float) ($comp['amount'] ?? -1);
                        $label = (string) ($comp['label'] ?? '');
                        $typeOrCat = (string) ($comp['type'] ?? ($comp['category'] ?? ''));
                        if ($amt <= 0) {
                            $v->errors()->add('subject_fees', 'Component amounts must be valid positive numbers.');
                            break;
                        }
                        if ($label === '') {
                            $v->errors()->add('subject_fees', 'Component labels are required.');
                            break;
                        }
                        if (stripos($typeOrCat, 'base') !== false) {
                            $baseCount++;
                        }
                        $subtotal += $amt;
                    }
                    if ($baseCount !== 1) {
                        $v->errors()->add('subject_fees', 'Exactly one Base Tuition component is required.');
                    }
                    if ($subtotal <= 0) {
                        $v->errors()->add('subject_fees', 'Tuition components subtotal must be greater than 0.');
                    }
                }
            }
            $gl = (string) $request->input('grade_level', '');
            if ($gl === 'Grade 11' || $gl === 'Grade 12') {
                $track = (string) $request->input('track', '');
                $strand = (string) $request->input('strand', '');
                if ($track === '') {
                    $v->errors()->add('track', 'Track is required for Senior High School.');
                }
                if ($strand === '') {
                    $v->errors()->add('strand', 'Strand is required for Senior High School.');
                }
            }
        });

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return redirect()->route('admin.fees.edit-tuition', $tuitionFee)
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        $oldValues = $tuitionFee->toArray();

        // Handle subject_fees
        $subjectFeesRaw = $request->input('subject_fees', '');
        $subjectFees = [];
        if (is_string($subjectFeesRaw) && strlen($subjectFeesRaw)) {
            $parsedSubjectFees = json_decode($subjectFeesRaw, true);
            $subjectFees = is_array($parsedSubjectFees) ? $parsedSubjectFees : [];
        }

        // Handle additional_charges
        $chargesRaw = $request->input('additional_charges', '');
        $charges = [];
        if (is_string($chargesRaw) && strlen($chargesRaw)) {
            $parsedCharges = json_decode($chargesRaw, true);
            $charges = is_array($parsedCharges) ? $parsedCharges : [];
        }

        $existsElsewhereQuery = TuitionFee::where('grade_level', $validated['grade_level'])
            ->where('school_year', $validated['school_year'] ?? $tuitionFee->school_year);
        if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'track')) {
            $existsElsewhereQuery->where(function ($q) use ($validated, $tuitionFee) {
                $t = $validated['track'] ?? $tuitionFee->track ?? null;
                if ($t === null || $t === '') {
                    $q->whereNull('track')->orWhere('track', '');
                } else {
                    $q->where('track', $t);
                }
            });
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'strand')) {
            $existsElsewhereQuery->where(function ($q) use ($validated, $tuitionFee) {
                $s = $validated['strand'] ?? $tuitionFee->strand ?? null;
                if ($s === null || $s === '') {
                    $q->whereNull('strand')->orWhere('strand', '');
                } else {
                    $q->where('strand', $s);
                }
            });
        }
        $existsElsewhere = $existsElsewhereQuery
            ->whereKeyNot($tuitionFee->getKey())
            ->exists();

        if ($existsElsewhere) {
            return redirect()->route('admin.fees.edit-tuition', $tuitionFee)
                ->withErrors(['grade_level' => 'Another tuition fee already uses this grade, school year, and term.'])
                ->withInput();
        }

        $validated['is_active'] = $request->boolean('is_active', false);

        try {
            // Recompute amount from components if provided
            $computedSubtotal = 0.0;
            $subjectFeesForCompute = [];
            $subjectFeesRawForCompute = (string) $request->input('subject_fees', '');
            if (strlen($subjectFeesRawForCompute)) {
                $parsedSf = json_decode($subjectFeesRawForCompute, true);
                $subjectFeesForCompute = is_array($parsedSf) ? $parsedSf : [];
                foreach ($subjectFeesForCompute as $c) {
                    $computedSubtotal += max(0, (float) ($c['amount'] ?? 0));
                }
            }
            $updateData = [
                'grade_level' => $validated['grade_level'],
                'amount' => (strlen($subjectFeesRawForCompute) ? $computedSubtotal : $validated['amount']),
                'is_active' => $validated['is_active'],
            ];

            if (Schema::hasColumn('tuition_fees', 'fee_deadline') && $request->filled('fee_deadline')) {
                $updateData['fee_deadline'] = $request->input('fee_deadline');
            }

            // Add notes if provided
            if (array_key_exists('notes', $validated) || array_key_exists('fee_name', $validated)) {
                $n = $validated['notes'] ?? null;
                $fn = $validated['fee_name'] ?? null;
                if ($fn && $n) {
                    $updateData['notes'] = $fn.' — '.$n;
                } elseif ($fn) {
                    $updateData['notes'] = $fn;
                } elseif ($n) {
                    $updateData['notes'] = $n;
                }
            }

            if (isset($validated['school_year'])) {
                $updateData['school_year'] = $validated['school_year'];
            }

            if (! empty($subjectFees) && \Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'subject_fees')) {
                $updateData['subject_fees'] = $subjectFees;
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'default_discount_ids')) {
                $updateData['default_discount_ids'] = collect(explode(',', (string) $request->input('selected_discount_ids', '')))
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all();
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'default_charge_ids')) {
                $updateData['default_charge_ids'] = collect(explode(',', (string) $request->input('selected_charge_ids', '')))
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all();
            }

            $tuitionFee->update($updateData);

            // Persist track/strand with SHS-only restriction
            if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'track')) {
                if (in_array($validated['grade_level'], ['Grade 11', 'Grade 12'])) {
                    $tuitionFee->track = $validated['track'] ?? $tuitionFee->track;
                } else {
                    $tuitionFee->track = null;
                }
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'strand')) {
                if (in_array($validated['grade_level'], ['Grade 11', 'Grade 12'])) {
                    $tuitionFee->strand = $validated['strand'] ?? $tuitionFee->strand;
                } else {
                    $tuitionFee->strand = null;
                }
            }
            $tuitionFee->save();

            DB::transaction(function () use ($tuitionFee, $charges, $request) {
                // Update fee_deadline and payment_schedule if applicable
                if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'fee_deadline') && $request->filled('fee_deadline')) {
                    $tuitionFee->update(['fee_deadline' => $request->input('fee_deadline')]);
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('tuition_fees', 'payment_schedule')) {
                    $allow = $request->boolean('allow_installment', false);
                    $plan = $request->input('payment_plan') ?: null;

                    if ($allow && $plan) {
                        $svc = new \App\Services\PaymentScheduleService;
                        $start = $request->input('fee_deadline');
                        $schedule = $svc->buildSchedule((float) $tuitionFee->amount, $plan, $start);
                    } else {
                        $schedule = ['installment_allowed' => false, 'plan' => null, 'items' => []];
                    }

                    $tuitionFee->update(['payment_schedule' => $schedule]);
                }
                if ($tuitionFee->is_active) {
                    if (\Illuminate\Support\Facades\Schema::hasTable('students') && \Illuminate\Support\Facades\Schema::hasTable('fee_assignments')) {
                        $studentsQuery = \App\Models\Student::where('level', $tuitionFee->grade_level);
                        $strandFilter = $tuitionFee->strand ?? null;
                        if ($strandFilter !== null && $strandFilter !== '' && \Illuminate\Support\Facades\Schema::hasColumn('students', 'strand')) {
                            $studentsQuery->where('strand', $strandFilter);
                        }
                        $students = $studentsQuery->get(['student_id']);
                        foreach ($students as $student) {
                            $existing = \App\Models\FeeAssignment::where('student_id', $student->student_id)
                                ->where('school_year', $tuitionFee->school_year)
                                ->where('semester', $tuitionFee->semester)
                                ->first();
                            if (! $existing) {
                                $fa = \App\Models\FeeAssignment::create([
                                    'student_id' => $student->student_id,
                                    'tuition_fee_id' => $tuitionFee->id,
                                    'base_tuition' => $tuitionFee->amount,
                                    'school_year' => $tuitionFee->school_year,
                                    'semester' => $tuitionFee->semester,
                                ]);
                                $fa->calculateTotal();
                            } else {
                                if (! $existing->is_finalized) {
                                    $existing->tuition_fee_id = $tuitionFee->id;
                                    $existing->base_tuition = $tuitionFee->amount;
                                    $existing->save();
                                    $existing->calculateTotal();
                                }
                            }
                        }
                    }
                }

                // Only update embedded charges if provided via builder; otherwise keep existing
                $chargesRaw = (string) $request->input('additional_charges', '');
                if (strlen($chargesRaw)) {
                    $tuitionFee->charges()->delete();
                    $parsedCharges = json_decode($chargesRaw, true);
                    $chargesArr = is_array($parsedCharges) ? $parsedCharges : [];
                    foreach ($chargesArr as $charge) {
                        $name = is_array($charge) && array_key_exists('name', $charge) ? (string) $charge['name'] : '';
                        $amount = is_array($charge) && array_key_exists('amount', $charge) ? (float) $charge['amount'] : 0.0;
                        $desc = is_array($charge) && array_key_exists('description', $charge) ? (string) $charge['description'] : null;
                        if ($name || $amount > 0) {
                            $tuitionFee->charges()->create([
                                'name' => $name,
                                'amount' => $amount,
                                'description' => $desc,
                            ]);
                        }
                    }
                }

                $selectedChargeIds = collect(explode(',', (string) $request->input('selected_charge_ids', '')))
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all();
                $selectedDiscountIds = collect(explode(',', (string) $request->input('selected_discount_ids', '')))
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all();

                // Sync TuitionFeeCharge and TuitionFeeDiscount with selected IDs
                // Only if chargesRaw/discountsRaw were not provided (to avoid conflict)
                if (empty($chargesRaw) && \Illuminate\Support\Facades\Schema::hasTable('tuition_fee_charges')) {
                    $tuitionFee->charges()->delete();
                    if (! empty($selectedChargeIds)) {
                        $charges = \App\Models\AdditionalCharge::whereIn('id', $selectedChargeIds)->get();
                        foreach ($charges as $charge) {
                            $tuitionFee->charges()->create([
                                'name' => $charge->charge_name,
                                'amount' => $charge->amount,
                                'description' => $charge->description,
                            ]);
                        }
                    }
                }

                if (! empty($selectedChargeIds) || ! empty($selectedDiscountIds)) {
                    $grades = [$tuitionFee->grade_level];
                    $studentIds = \App\Models\Student::whereIn('level', $grades)->pluck('student_id')->all();
                    $assignmentIds = \App\Models\FeeAssignment::whereIn('student_id', $studentIds)
                        ->where('is_finalized', false)
                        ->where('school_year', $tuitionFee->school_year)
                        ->where('semester', $tuitionFee->semester)
                        ->pluck('id')
                        ->all();
                    if (! empty($assignmentIds)) {
                        if (! empty($selectedChargeIds)) {
                            if (\Illuminate\Support\Facades\Schema::hasTable('fee_assignment_additional_charges')) {
                                foreach ($selectedChargeIds as $cid) {
                                    app(FeeManagementService::class)->attachChargeToAssignmentsLocal($cid, $assignmentIds);
                                }
                            }
                            app(FeeManagementService::class)->syncChargeAssignmentsToSupabaseBatch($selectedChargeIds, $assignmentIds);
                        }
                        if (! empty($selectedDiscountIds)) {
                            app(FeeManagementService::class)->attachDiscountsToAssignmentsLocal($selectedDiscountIds, $assignmentIds);
                            app(FeeManagementService::class)->syncDiscountAssignmentsToSupabaseBatch($selectedDiscountIds, $assignmentIds);
                        }
                    }
                }
            });

            app(FeeManagementService::class)->recomputeForGrade($validated['grade_level']);
            $payload = array_merge(
                $tuitionFee->only(['grade_level', 'amount', 'school_year', 'semester', 'is_active', 'notes', 'payment_schedule', 'subject_fees']),
                [
                    'track' => $validated['track'] ?? null,
                    'strand' => $validated['strand'] ?? null,
                    'fee_deadline' => $validated['fee_deadline'] ?? null,
                    'fee_name' => (function () use ($validated, $tuitionFee) {
                        if (! empty($validated['fee_name'])) {
                            return $validated['fee_name'];
                        }
                        $notes = (string) ($tuitionFee->notes ?? '');
                        $pos = mb_strpos($notes, ' — ');
                        if ($pos !== false) {
                            return mb_substr($notes, 0, $pos);
                        }

                        return null;
                    })(),
                ]
            );
            app(FeeManagementService::class)->syncToSupabase('tuition_fees', $payload, 'id', $tuitionFee->id);
            if (\Illuminate\Support\Facades\Schema::hasTable('fee_update_audits')) {
                try {
                    \Illuminate\Support\Facades\DB::table('fee_update_audits')->insert([
                        'performed_by_user_id' => optional(\Illuminate\Support\Facades\Auth::user())->user_id ?? null,
                        'event_type' => 'tuition_updated',
                        'school_year' => $tuitionFee->school_year ?? 'N/A',
                        'semester' => $tuitionFee->semester ?? 'N/A',
                        'affected_students_count' => \App\Models\Student::where('level', $validated['grade_level'])->count(),
                        'affected_staff_count' => \App\Models\User::where('roleable_type', 'App\\Models\\Staff')->count(),
                        'message' => 'Tuition fee updated with components and attachments.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                }
            }

            // Audit Log
            try {
                AuditService::log(
                    'Tuition Fee Updated',
                    $tuitionFee,
                    "Updated tuition fee for {$tuitionFee->grade_level} (SY {$tuitionFee->school_year})",
                    $oldValues ?? [],
                    $tuitionFee->toArray()
                );
            } catch (\Throwable $e) {
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'id' => $tuitionFee?->id,
                    'grade_level' => $tuitionFee?->grade_level,
                    'amount' => (float) ($tuitionFee?->amount ?? ($validated['amount'] ?? 0)),
                    'school_year' => $tuitionFee?->school_year ?? ($validated['school_year'] ?? 'N/A'),
                    'semester' => $tuitionFee?->semester ?? 'N/A',
                    'is_active' => (bool) ($tuitionFee?->is_active ?? true),
                    'notes' => $tuitionFee?->notes ?? null,
                    'updated_at' => now()->toISOString(),
                ], 200);
            }

            return redirect()->route('admin.fees.index', ['tab' => 'tuition'])
                ->with('success', 'Tuition fee updated successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to update tuition fee. Please try again.'], 500);
            }

            return redirect()->route('admin.fees.edit-tuition', $tuitionFee)
                ->with('error', 'Failed to update tuition fee. Please try again.')
                ->withInput();
        }
    }

    /**
     * Delete a tuition fee.
     */
    public function destroyTuitionFee(TuitionFee $tuitionFee): RedirectResponse
    {
        $oldValues = $tuitionFee->toArray();
        try {
            if ($tuitionFee->feeAssignments()->exists()) {
                return redirect()->route('admin.fees.index', ['tab' => 'tuition'])
                    ->with('error', 'Cannot delete tuition fee as it is being used in student fee assignments. You may set it to Inactive instead.');
            }

            DB::transaction(function () use ($tuitionFee) {
                // Delete child records
                $tuitionFee->charges()->delete();

                // Delete from Supabase
                try {
                    $url = env('SUPABASE_URL', '');
                    $serviceKey = env('SUPABASE_SERVICE_KEY', '');
                    if ($url && $serviceKey) {
                        $endpoint = rtrim($url, '/').'/rest/v1/tuition_fees?id=eq.'.$tuitionFee->id;
                        $headers = [
                            'Authorization' => 'Bearer '.$serviceKey,
                            'apikey' => $serviceKey,
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                        ];
                        Http::withHeaders($headers)->delete($endpoint);
                    }
                } catch (\Throwable $e) {
                    // Continue even if Supabase sync fails
                }

                $tuitionFee->delete();
            });

            // Audit Log
            try {
                AuditService::log(
                    'Tuition Fee Deleted',
                    $tuitionFee,
                    "Deleted tuition fee for {$oldValues['grade_level']} (SY {$oldValues['school_year']})",
                    $oldValues,
                    null
                );
            } catch (\Throwable $e) {
            }

            return redirect()->route('admin.fees.index', ['tab' => 'tuition'])
                ->with('success', 'Tuition fee deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.fees.index', ['tab' => 'tuition'])
                ->with('error', 'Failed to delete tuition fee. Please try again.');
        }
    }

    /**
     * Show a detailed tuition configuration page.
     */
    public function showTuitionFee(TuitionFee $tuitionFee): View
    {
        $charges = $tuitionFee->charges()->orderBy('id')->get();
        $base = (float) $tuitionFee->amount;
        $chargesTotal = (float) $charges->sum('amount');

        $discounts = collect();
        if (! empty($tuitionFee->default_discount_ids)) {
            $discounts = \App\Models\Discount::active()
                ->whereIn('id', $tuitionFee->default_discount_ids)
                ->orderBy('priority', 'desc')
                ->get();
        }

        // Respect stacking rules: apply all stackable discounts; for non-stackable, apply only the highest priority per scope
        $stackable = $discounts->filter(function ($d) {
            return method_exists($d, 'isStackable') ? $d->isStackable() : true;
        });
        $nonStackable = $discounts->filter(function ($d) {
            return method_exists($d, 'isStackable') ? ! $d->isStackable() : false;
        });
        $selectedNonStackable = collect();
        if ($nonStackable->count() > 0) {
            $grouped = $nonStackable->groupBy(function ($d) {
                return method_exists($d, 'getApplyScope') ? $d->getApplyScope() : 'total';
            });
            foreach ($grouped as $scope => $items) {
                $selectedNonStackable = $selectedNonStackable->merge(
                    collect($items)->sortByDesc('priority')->take(1)
                );
            }
        }
        $appliedDiscounts = $stackable->merge($selectedNonStackable)->unique('id');

        $discountsTotal = 0.0;
        $discountBreakdown = [];
        foreach ($appliedDiscounts as $d) {
            $type = (string) ($d->type ?? 'percentage');
            $val = (float) ($d->value ?? 0.0);
            $scope = method_exists($d, 'getApplyScope') ? $d->getApplyScope() : 'total';
            $scopeBase = $scope === 'tuition_only'
                ? $base
                : ($scope === 'charges_only' ? $chargesTotal : ($base + $chargesTotal));
            $applied = $type === 'percentage'
                ? ($scopeBase * $val) / 100.0
                : min($val, $scopeBase);
            $applied = max(0.0, (float) $applied);
            $discountsTotal += $applied;
            $discountBreakdown[] = [
                'id' => $d->id,
                'name' => $d->discount_name,
                'type' => $type,
                'value' => $val,
                'scope' => $scope,
                'stackable' => (method_exists($d, 'isStackable') ? $d->isStackable() : true),
                'priority' => (int) ($d->priority ?? 0),
                'applied_amount' => $applied,
            ];
        }

        $net = max(0.0, $base + $chargesTotal - $discountsTotal);

        return view('admin.fees.tuition.show', [
            'tuitionFee' => $tuitionFee,
            'charges' => $charges,
            'discounts' => $discounts,
            'chargesTotal' => $chargesTotal,
            'discountsTotal' => $discountsTotal,
            'discountBreakdown' => $discountBreakdown,
            'netPayable' => $net,
        ]);
    }

    /**
     * Toggle tuition active status.
     */
    public function toggleTuitionStatus(Request $request, TuitionFee $tuitionFee): RedirectResponse
    {
        $target = $request->boolean('active', ! $tuitionFee->is_active);
        $tuitionFee->is_active = $target;
        $tuitionFee->save();
        try {
            app(FeeManagementService::class)->syncToSupabase('tuition_fees', [
                'id' => $tuitionFee->id,
                'is_active' => (bool) $tuitionFee->is_active,
            ], 'id', $tuitionFee->id);
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.fees.index', ['tab' => 'tuition'])
            ->with('success', $target ? 'Tuition configuration activated.' : 'Tuition configuration deactivated.');
    }

    /**
     * Show the form for creating an additional charge.
     */
    public function createAdditionalCharge(): View
    {
        $chargesTableMissing = ! Schema::hasTable('additional_charges');
        $activeSchoolYear = SystemSetting::getActiveSchoolYear();

        return view('admin.fees.charges.create', [
            'gradeLevels' => ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
            'chargesTableMissing' => $chargesTableMissing,
            'activeSchoolYear' => $activeSchoolYear,
        ]);
    }

    /**
     * Store a newly created additional charge.
     */
    public function storeAdditionalCharge(Request $request)
    {
        $chargesTableExists = Schema::hasTable('additional_charges');
        $schemaBuilder = DB::connection()->getSchemaBuilder();

        $validator = Validator::make($request->all(), [
            // 1) Charge Information
            'charge_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            // 2) Amount Details
            'amount' => ['required', 'numeric', 'min:0'],
            // 3) Charge Type
            'charge_type' => ['required', 'in:one_time,recurring'],
            // 4) Applicability
            'school_year' => ['required', 'string', 'max:20'],
            'applies_to' => ['required', 'in:all,grades'],
            'grade_levels' => ['nullable', 'array'],
            'grade_levels.*' => ['string', 'in:Grade 7,Grade 8,Grade 9,Grade 10,Grade 11,Grade 12'],
            'track' => ['nullable', 'string', 'max:100'],
            'strand' => ['nullable', 'string', 'max:100'],
            // 5) Payment Rules
            'required_or_optional' => ['required', 'in:required,optional'],
            'allow_installment' => ['nullable', 'boolean'],
            'include_in_total' => ['nullable', 'boolean'],
            // 6) Due Info
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            // 7) Status
            'status' => ['required', 'in:active,inactive'],
        ]);

        $validator->after(function ($v) use ($request) {
            $activeYear = SystemSetting::getActiveSchoolYear();
            if ($activeYear && $request->input('school_year') !== $activeYear) {
                $v->errors()->add('school_year', 'School Year must match the active School Year ('.$activeYear.').');
            }
        });

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return redirect()->route('admin.fees.create-charge')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        $payload = [
            'charge_name' => $validated['charge_name'],
            'description' => $validated['description'] ?? null,
            'amount' => (float) $validated['amount'],
            'charge_type' => $validated['charge_type'],
            'school_year' => $validated['school_year'],
            'applies_to' => $validated['applies_to'],
            'grade_levels' => $validated['grade_levels'] ?? [],
            'track' => $validated['track'] ?? null,
            'strand' => $validated['strand'] ?? null,
            'required' => $validated['required_or_optional'] === 'required',
            'allow_installment' => $request->boolean('allow_installment', false),
            'include_in_total' => $request->boolean('include_in_total', true),
            'due_date' => $validated['due_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'],
            'is_active' => $validated['status'] === 'active',
            'is_mandatory' => $validated['required_or_optional'] === 'required',
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
        ];

        try {
            if (! $chargesTableExists) {
                $url = env('SUPABASE_URL', '');
                $serviceKey = env('SUPABASE_SERVICE_KEY', '');
                if ($url && $serviceKey) {
                    $endpoint = rtrim($url, '/').'/rest/v1/additional_charges';
                    $headers = [
                        'Authorization' => 'Bearer '.$serviceKey,
                        'apikey' => $serviceKey,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ];
                    Http::withHeaders($headers)->post($endpoint, $payload);
                }

                return redirect()->route('admin.fees.index', ['tab' => 'charges'])
                    ->with('success', 'Additional charge created successfully.');
            }

            $chargeId = null;
            $local = [
                'charge_name' => $payload['charge_name'],
                'description' => $payload['description'],
                'charge_type' => $payload['charge_type'],
                'school_year' => $payload['school_year'] ?? null,
                'applies_to' => $payload['applies_to'] ?? 'all',
                'amount' => $payload['amount'],
                'applicable_grades' => $payload['grade_levels'] ?? [],
                'allow_installment' => $payload['allow_installment'] ?? false,
                'include_in_total' => $payload['include_in_total'] ?? true,
                'due_date' => $payload['due_date'] ?? null,
                'notes' => $payload['notes'] ?? null,
                'status' => $payload['status'] ?? 'active',
                'track' => $payload['track'] ?? null,
                'strand' => $payload['strand'] ?? null,
                'required_or_optional' => ($validated['required_or_optional'] ?? 'required'),
                'is_active' => (bool) ($payload['is_active'] ?? true),
                'is_mandatory' => (bool) ($payload['is_mandatory'] ?? (($validated['required_or_optional'] ?? 'required') === 'required')),
            ];
            $columns = [];
            try {
                if ($chargesTableExists) {
                    $columns = $schemaBuilder->getColumnListing('additional_charges');
                }
            } catch (\Throwable $e) {
                $columns = [];
            }
            $insert = [];
            foreach ($local as $k => $v) {
                if (empty($columns) || in_array($k, $columns, true)) {
                    $insert[$k] = $v;
                }
            }
            $created = null;
            try {
                $created = AdditionalCharge::create($insert);
            } catch (\Throwable $e) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'id' => 0,
                        'charge_name' => $payload['charge_name'],
                        'description' => $payload['description'],
                        'amount' => $payload['amount'],
                        'required_or_optional' => $validated['required_or_optional'] ?? null,
                        'charge_type' => $payload['charge_type'],
                        'created_at' => now()->toISOString(),
                    ], 201);
                }

                throw $e;
            }

            $chargeId = $created->id;
            try {
                app(FeeManagementService::class)->recomputeForGrades($local['applicable_grades'] ?? []);
            } catch (\Throwable $e) {
                \Log::warning('Recompute skipped for additional_charges', [
                    'message' => $e->getMessage(),
                ]);
            }
            if (config('services.supabase.url') && config('services.supabase.service_key')) {
                try {
                    app(FeeManagementService::class)->syncToSupabase('additional_charges', $payload, 'id', $created->id);
                } catch (\Throwable $e) {
                    \Log::warning('Supabase sync skipped for additional_charges', [
                        'message' => $e->getMessage(),
                    ]);
                }
            }

            if ($chargeId) {
                $assignmentIds = [];
                if ($schemaBuilder->hasTable('fee_assignments')) {
                    if (($payload['applies_to'] ?? '') === 'all') {
                        $assignmentIds = \App\Models\FeeAssignment::pluck('id')->all();
                    } else {
                        $grades = $payload['grade_levels'] ?? [];
                        if (! empty($grades) && $schemaBuilder->hasTable('students')) {
                            $studentIds = \App\Models\Student::whereIn('level', $grades)->pluck('student_id')->all();
                            $assignmentIds = \App\Models\FeeAssignment::whereIn('student_id', $studentIds)->pluck('id')->all();
                        }
                    }
                }
                if (! empty($assignmentIds)) {
                    $meta = [
                        'amount' => $payload['amount'] ?? null,
                        'date_added' => now()->toISOString(),
                        'category' => ($payload['charge_type'] ?? '') === 'recurring' ? 'Recurring' : 'One-Time',
                        'reference_no' => null,
                    ];
                    if ($schemaBuilder->hasTable('fee_assignment_additional_charges')) {
                        app(FeeManagementService::class)->attachChargeToAssignmentsLocal($chargeId, $assignmentIds);
                    }
                    app(FeeManagementService::class)->syncChargeAssignmentsToSupabase($chargeId, $assignmentIds, $meta);
                    app(FeeManagementService::class)->syncFeeAdditionalChargesToSupabase($chargeId, $assignmentIds, $payload);
                }
            }

            if ($schemaBuilder->hasTable('fee_update_audits')) {
                try {
                    \Illuminate\Support\Facades\DB::table('fee_update_audits')->insert([
                        'performed_by_user_id' => optional(\Illuminate\Support\Facades\Auth::user())->user_id ?? null,
                        'event_type' => 'additional_charge_created',
                        'school_year' => $validated['school_year'] ?? 'N/A',
                        'semester' => 'N/A',
                        'affected_students_count' => isset($grades) ? \App\Models\Student::whereIn('level', $grades)->count() : 0,
                        'affected_staff_count' => \App\Models\User::where('roleable_type', 'App\\Models\\Staff')->count(),
                        'message' => 'Additional charge created.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                }
            }

            // Audit Log
            try {
                $chargeModel = AdditionalCharge::find($chargeId);
                if ($chargeModel) {
                    AuditService::log(
                        'Additional Charge Created',
                        $chargeModel,
                        "Created charge: {$payload['charge_name']}",
                        null,
                        $chargeModel->toArray()
                    );
                }
            } catch (\Throwable $e) {
            }
            if ($request->expectsJson()) {
                return response()->json([
                    'id' => $chargeId,
                    'charge_name' => $payload['charge_name'],
                    'description' => $payload['description'],
                    'amount' => $payload['amount'],
                    'required_or_optional' => $validated['required_or_optional'] ?? null,
                    'charge_type' => $payload['charge_type'],
                    'created_at' => now()->toISOString(),
                ], 201);
            }

            return redirect()->route('admin.fees.index', ['tab' => 'charges'])
                ->with('success', 'Additional charge created successfully.');
        } catch (\Exception $e) {
            \Log::error('Additional charge creation failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to create additional charge'], 500);
            }

            return redirect()->route('admin.fees.create-charge')
                ->with('error', 'Failed to create additional charge. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the form for editing an additional charge.
     */
    public function editAdditionalCharge(AdditionalCharge $charge): View
    {
        return view('admin.fees.charges.edit', [
            'additionalCharge' => $charge,
            'gradeLevels' => ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
        ]);
    }

    /**
     * Update the specified additional charge.
     */
    public function updateAdditionalCharge(Request $request, AdditionalCharge $charge): RedirectResponse
    {
        $oldValues = $charge->toArray();
        $validator = Validator::make($request->all(), [
            'charge_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'charge_type' => ['required', 'in:one_time,recurring'],
            'school_year' => ['required', 'string'],
            'applies_to' => ['required', 'in:all,grades'],
            'grade_levels' => ['nullable', 'array'],
            'grade_levels.*' => ['string', 'in:Grade 7,Grade 8,Grade 9,Grade 10,Grade 11,Grade 12'],
            'required_or_optional' => ['required', 'in:required,optional'],
            'allow_installment' => ['nullable', 'boolean'],
            'include_in_total' => ['nullable', 'boolean'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return redirect()->route('admin.fees.edit-charge', $charge)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $local = [
            'charge_name' => $data['charge_name'],
            'description' => $data['description'] ?? null,
            'amount' => $data['amount'],
            'charge_type' => $data['charge_type'],
            'school_year' => $data['school_year'],
            'applies_to' => $data['applies_to'],
            'applicable_grades' => ($data['applies_to'] === 'grades') ? array_values($data['grade_levels'] ?? []) : [],
            'required_or_optional' => $data['required_or_optional'],
            'allow_installment' => (bool) ($data['allow_installment'] ?? false),
            'include_in_total' => (bool) ($data['include_in_total'] ?? true),
            'due_date' => $data['due_date'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'],
            'is_active' => ($data['status'] ?? 'active') === 'active',
            'is_mandatory' => ($data['required_or_optional'] ?? 'required') === 'required',
        ];

        try {
            $columns = [];
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('additional_charges')) {
                    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('additional_charges');
                }
            } catch (\Throwable $e) {
                $columns = [];
            }
            $update = [];
            foreach ($local as $k => $v) {
                if (empty($columns) || in_array($k, $columns, true)) {
                    $update[$k] = $v;
                }
            }
            $charge->update($update);

            $grades = $update['applicable_grades'] ?? [];
            try {
                app(FeeManagementService::class)->recomputeForGrades($grades);
            } catch (\Throwable $e) {
            }
            try {
                app(FeeManagementService::class)->syncToSupabase('additional_charges', array_merge($update, ['id' => $charge->id]), 'id', $charge->id);
                app(FeeManagementService::class)->patchSupabaseFeeAdditionalCharges($charge->id, [
                    'charge_name' => $update['charge_name'] ?? null,
                    'description' => $update['description'] ?? null,
                    'amount' => $update['amount'] ?? null,
                    'required_or_optional' => $update['required_or_optional'] ?? null,
                    'status' => $update['status'] ?? null,
                ]);
            } catch (\Throwable $e) {
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'id' => $charge->id,
                    'charge_name' => $update['charge_name'],
                    'description' => $update['description'],
                    'amount' => $update['amount'],
                    'charge_type' => $update['charge_type'],
                    'school_year' => $update['school_year'],
                    'applies_to' => $update['applies_to'],
                    'grade_levels' => $update['applicable_grades'],
                    'required_or_optional' => $update['required_or_optional'],
                    'status' => $update['status'],
                    'updated_at' => now()->toISOString(),
                ], 200);
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('fee_update_audits')) {
                try {
                    \Illuminate\Support\Facades\DB::table('fee_update_audits')->insert([
                        'performed_by_user_id' => optional(\Illuminate\Support\Facades\Auth::user())->user_id ?? null,
                        'event_type' => 'additional_charge_updated',
                        'school_year' => 'N/A',
                        'semester' => 'N/A',
                        'affected_students_count' => \App\Models\Student::whereIn('level', $grades)->count(),
                        'affected_staff_count' => \App\Models\User::where('roleable_type', 'App\\Models\\Staff')->count(),
                        'message' => 'Additional charge updated.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                }
            }

            // Audit Log
            try {
                AuditService::log(
                    'Additional Charge Updated',
                    $charge,
                    "Updated charge: {$charge->charge_name}",
                    $oldValues,
                    $charge->toArray()
                );
            } catch (\Throwable $e) {
            }

            return redirect()->route('admin.fees.index', ['tab' => 'charges'])
                ->with('success', 'Additional charge updated successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to update additional charge'], 500);
            }

            return redirect()->route('admin.fees.edit-charge', $charge)
                ->with('error', 'Failed to update additional charge. Please try again.')
                ->withInput();
        }
    }

    /**
     * Delete an additional charge.
     */
    public function destroyAdditionalCharge(AdditionalCharge $charge): RedirectResponse
    {
        $activeYear = SystemSetting::getActiveSchoolYear();
        if ($activeYear && $charge->school_year !== $activeYear) {
            return redirect()->route('admin.fees.index', ['tab' => 'charges'])
                ->with('error', 'Cannot delete Additional Charge from a locked School Year.');
        }

        $oldValues = $charge->toArray();
        try {
            // Check if charge is being used in assignments
            if ($charge->feeAssignments()->exists()) {
                return redirect()->route('admin.fees.index', ['tab' => 'charges'])
                    ->with('error', 'Cannot delete additional charge as it is being used by students.');
            }

            app(FeeManagementService::class)->deleteSupabaseChargeAssignmentRows($charge->id);

            // Delete definition from Supabase
            try {
                $url = env('SUPABASE_URL', '');
                $serviceKey = env('SUPABASE_SERVICE_KEY', '');
                if ($url && $serviceKey) {
                    $endpoint = rtrim($url, '/').'/rest/v1/additional_charges?id=eq.'.$charge->id;
                    $headers = [
                        'Authorization' => 'Bearer '.$serviceKey,
                        'apikey' => $serviceKey,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ];
                    Http::withHeaders($headers)->delete($endpoint);
                }
            } catch (\Throwable $e) {
                // Continue even if Supabase sync fails
            }

            $charge->delete();

            $studentIds = Student::pluck('student_id')->all();
            $this->notifyStudents($studentIds, 'Fees Updated', 'Additional charges have changed.');

            if (\Illuminate\Support\Facades\Schema::hasTable('fee_update_audits')) {
                try {
                    \Illuminate\Support\Facades\DB::table('fee_update_audits')->insert([
                        'performed_by_user_id' => optional(\Illuminate\Support\Facades\Auth::user())->user_id ?? null,
                        'event_type' => 'additional_charge_deleted',
                        'school_year' => 'N/A',
                        'semester' => 'N/A',
                        'affected_students_count' => count($studentIds),
                        'affected_staff_count' => \App\Models\User::where('roleable_type', 'App\\Models\\Staff')->count(),
                        'message' => 'Additional charge deleted.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                }
            }

            // Audit Log
            try {
                AuditService::log(
                    'Additional Charge Deleted',
                    $charge,
                    "Deleted charge: {$oldValues['charge_name']}",
                    $oldValues,
                    null
                );
            } catch (\Throwable $e) {
            }

            return redirect()->route('admin.fees.index', ['tab' => 'charges'])
                ->with('success', 'Additional charge deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.fees.index', ['tab' => 'charges'])
                ->with('error', 'Failed to delete additional charge. Please try again.');
        }
    }

    /**
     * Show the form for creating a discount.
     */
    public function createDiscount(): View
    {
        return view('admin.fees.discounts.create', [
            'gradeLevels' => ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
            'additionalCharges' => \App\Models\AdditionalCharge::active()->orderBy('charge_name')->get(),
        ]);
    }

    /**
     * Store a newly created discount.
     */
    public function storeDiscount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'discount_name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'priority' => ['integer', 'min:0'],
            'is_automatic' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'applicable_grades' => ['nullable', 'array'],
            'applicable_grades.*' => ['string', 'in:Grade 7,Grade 8,Grade 9,Grade 10,Grade 11,Grade 12'],
            'apply_scope' => ['nullable', 'in:total,tuition_only,charges_only,specific_charges'],
            'target_charge_ids' => ['nullable', 'array'],
            'target_charge_ids.*' => ['exists:additional_charges,id'],
            'is_stackable' => ['nullable', 'boolean'],
            'school_year' => ['nullable', 'regex:/^\\d{4}-\\d{4}$/'],
            'valid_from' => ['nullable', 'date'],
            'valid_to' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'track' => ['nullable', 'string'],
            'strand' => ['nullable', 'string'],
        ]);
        $validator->after(function ($v) use ($request) {
            if ($request->input('type') === 'percentage' && (float) $request->input('value') > 100) {
                $v->errors()->add('value', 'Percentage discount must not exceed 100.');
            }
        });

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return redirect()->route('admin.fees.create-discount')
                ->withErrors($validator)
                ->withInput();
        }

        if (! Schema::hasTable('discounts')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Discounts storage has not been set up. Please run the pending migrations first.'], 400);
            }

            return redirect()->route('admin.fees.create-discount')
                ->with('error', 'Discounts storage has not been set up. Please run the pending migrations first.')
                ->withInput();
        }

        try {
            $validated = $validator->validated();
            $data = [
                'discount_name' => $validated['discount_name'],
                'type' => $validated['type'],
                'value' => $validated['value'],
                'description' => $validated['description'] ?? null,
                'priority' => $validated['priority'] ?? 0,
                'is_automatic' => (bool) ($validated['is_automatic'] ?? false),
                'is_active' => (bool) ($validated['is_active'] ?? true),
                'applicable_grades' => $request->filled('applicable_grades')
                    ? array_values($validated['applicable_grades'])
                    : [],
                'eligibility_rules' => [
                    'apply_scope' => $validated['apply_scope'] ?? 'total',
                    'target_charge_ids' => ($validated['apply_scope'] ?? '') === 'specific_charges' ? ($validated['target_charge_ids'] ?? []) : [],
                    'is_stackable' => (bool) ($validated['is_stackable'] ?? true),
                    'school_year' => $validated['school_year'] ?? null,
                    'valid_from' => $validated['valid_from'] ?? null,
                    'valid_to' => $validated['valid_to'] ?? null,
                    'track' => $validated['track'] ?? null,
                    'strand' => $validated['strand'] ?? null,
                ],
            ];

            $created = Discount::create($data);

            $grades = $data['applicable_grades'] ?? [];
            app(FeeManagementService::class)->recomputeForGrades($grades);
            $payload = $created->only(['discount_name', 'type', 'value', 'eligibility_rules', 'description', 'applicable_grades', 'is_active', 'is_automatic', 'priority']);
            app(FeeManagementService::class)->syncToSupabase('discounts', $payload, 'id', $created->id);

            if (\Illuminate\Support\Facades\Schema::hasTable('fee_update_audits')) {
                try {
                    \Illuminate\Support\Facades\DB::table('fee_update_audits')->insert([
                        'performed_by_user_id' => optional(\Illuminate\Support\Facades\Auth::user())->user_id ?? null,
                        'event_type' => 'discount_created',
                        'school_year' => 'N/A',
                        'semester' => 'N/A',
                        'affected_students_count' => \App\Models\Student::whereIn('level', $grades)->count(),
                        'affected_staff_count' => \App\Models\User::where('roleable_type', 'App\\Models\\Staff')->count(),
                        'message' => 'Discount created.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                }
            }

            // Audit Log
            try {
                AuditService::log(
                    'Discount Created',
                    $created,
                    "Created discount: {$created->discount_name}",
                    null,
                    $created->toArray()
                );
            } catch (\Throwable $e) {
            }
            if ($request->expectsJson()) {
                return response()->json([
                    'id' => $created->id,
                    'discount_name' => $created->discount_name,
                    'type' => $created->type,
                    'value' => (float) $created->value,
                    'apply_scope' => data_get($created->eligibility_rules, 'apply_scope', 'total'),
                    'is_automatic' => (bool) $created->is_automatic,
                    'is_stackable' => (bool) data_get($created->eligibility_rules, 'is_stackable', true),
                    'is_active' => (bool) $created->is_active,
                    'priority' => (int) $created->priority,
                ], 201);
            }

            return redirect()->route('admin.fees.index', ['tab' => 'discounts'])
                ->with('success', 'Discount created successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to create discount. Please try again.'], 500);
            }

            return redirect()->route('admin.fees.create-discount')
                ->with('error', 'Failed to create discount. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the form for editing a discount.
     */
    public function editDiscount(Discount $discount): View
    {
        return view('admin.fees.discounts.edit', [
            'discount' => $discount,
            'gradeLevels' => ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
            'additionalCharges' => \App\Models\AdditionalCharge::active()->orderBy('charge_name')->get(),
        ]);
    }

    /**
     * Update the specified discount.
     */
    public function updateDiscount(Request $request, Discount $discount)
    {
        $oldValues = $discount->toArray();
        $validator = Validator::make($request->all(), [
            'discount_name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'priority' => ['integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_automatic' => ['nullable', 'boolean'],
            'applicable_grades' => ['nullable', 'array'],
            'applicable_grades.*' => ['string', 'in:Grade 7,Grade 8,Grade 9,Grade 10,Grade 11,Grade 12'],
            'apply_scope' => ['nullable', 'in:total,tuition_only,charges_only'],
            'is_stackable' => ['nullable', 'boolean'],
            'school_year' => ['nullable', 'regex:/^\\d{4}-\\d{4}$/'],
            'valid_from' => ['nullable', 'date'],
            'valid_to' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'track' => ['nullable', 'string'],
            'strand' => ['nullable', 'string'],
        ]);
        $validator->after(function ($v) use ($request) {
            if ($request->input('type') === 'percentage' && (float) $request->input('value') > 100) {
                $v->errors()->add('value', 'Percentage discount must not exceed 100.');
            }
        });

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return redirect()->route('admin.fees.edit-discount', $discount)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $validated = $validator->validated();
            $data = [
                'discount_name' => $validated['discount_name'],
                'type' => $validated['type'],
                'value' => $validated['value'],
                'description' => $validated['description'] ?? null,
                'priority' => $validated['priority'] ?? $discount->priority,
                'is_active' => (bool) ($validated['is_active'] ?? $discount->is_active),
                'is_automatic' => (bool) ($validated['is_automatic'] ?? $discount->is_automatic),
                'applicable_grades' => $request->filled('applicable_grades')
                    ? array_values($validated['applicable_grades'])
                    : ($discount->applicable_grades ?? []),
                'eligibility_rules' => [
                    'apply_scope' => $validated['apply_scope'] ?? data_get($discount->eligibility_rules, 'apply_scope', 'total'),
                    'target_charge_ids' => ($validated['apply_scope'] ?? data_get($discount->eligibility_rules, 'apply_scope')) === 'specific_charges' ? ($validated['target_charge_ids'] ?? []) : [],
                    'is_stackable' => (bool) ($validated['is_stackable'] ?? data_get($discount->eligibility_rules, 'is_stackable', true)),
                    'school_year' => $validated['school_year'] ?? data_get($discount->eligibility_rules, 'school_year'),
                    'valid_from' => $validated['valid_from'] ?? data_get($discount->eligibility_rules, 'valid_from'),
                    'valid_to' => $validated['valid_to'] ?? data_get($discount->eligibility_rules, 'valid_to'),
                    'track' => $validated['track'] ?? data_get($discount->eligibility_rules, 'track'),
                    'strand' => $validated['strand'] ?? data_get($discount->eligibility_rules, 'strand'),
                ],
            ];

            $discount->update($data);

            $grades = $discount->applicable_grades ?? [];
            app(\App\Services\FeeManagementService::class)->recomputeForGrades($grades);
            $payload = $discount->only(['discount_name', 'type', 'value', 'eligibility_rules', 'description', 'applicable_grades', 'is_active', 'is_automatic', 'priority']);
            app(FeeManagementService::class)->syncToSupabase('discounts', $payload, 'id', $discount->id);

            if (\Illuminate\Support\Facades\Schema::hasTable('fee_update_audits')) {
                try {
                    \Illuminate\Support\Facades\DB::table('fee_update_audits')->insert([
                        'performed_by_user_id' => optional(\Illuminate\Support\Facades\Auth::user())->user_id ?? null,
                        'event_type' => 'discount_updated',
                        'school_year' => 'N/A',
                        'semester' => 'N/A',
                        'affected_students_count' => \App\Models\Student::whereIn('level', $grades)->count(),
                        'affected_staff_count' => \App\Models\User::where('roleable_type', 'App\\Models\\Staff')->count(),
                        'message' => 'Discount updated.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                }
            }

            // Audit Log
            try {
                AuditService::log(
                    'Discount Updated',
                    $discount,
                    "Updated discount: {$discount->discount_name}",
                    $oldValues,
                    $discount->toArray()
                );
            } catch (\Throwable $e) {
            }
            if ($request->expectsJson()) {
                return response()->json([
                    'id' => $discount->id,
                    'discount_name' => $discount->discount_name,
                    'type' => $discount->type,
                    'value' => (float) $discount->value,
                    'apply_scope' => data_get($discount->eligibility_rules, 'apply_scope', 'total'),
                    'is_automatic' => (bool) $discount->is_automatic,
                    'is_stackable' => (bool) data_get($discount->eligibility_rules, 'is_stackable', true),
                    'is_active' => (bool) $discount->is_active,
                    'priority' => (int) $discount->priority,
                    'updated_at' => now()->toISOString(),
                ], 200);
            }

            return redirect()->route('admin.fees.index', ['tab' => 'discounts'])
                ->with('success', 'Discount updated successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to update discount. Please try again.'], 500);
            }

            return redirect()->route('admin.fees.edit-discount', $discount)
                ->with('error', 'Failed to update discount. Please try again.')
                ->withInput();
        }
    }

    /**
     * Delete a discount.
     */
    public function destroyDiscount(Discount $discount): RedirectResponse
    {
        $activeYear = SystemSetting::getActiveSchoolYear();
        $discountSy = data_get($discount->eligibility_rules, 'school_year');
        if ($activeYear && $discountSy && $discountSy !== $activeYear) {
            return redirect()->route('admin.fees.index', ['tab' => 'discounts'])
                ->with('error', 'Cannot delete Discount from a locked School Year.');
        }

        $oldValues = $discount->toArray();
        try {
            if (Schema::hasTable('fee_assignment_discounts') && $discount->feeAssignments()->exists() && \App\Models\Student::count() > 0) {
                return redirect()->route('admin.fees.index', ['tab' => 'discounts'])
                    ->with('error', 'Cannot delete discount as it is being used by students.');
            }

            // Delete definition from Supabase
            try {
                $url = env('SUPABASE_URL', '');
                $serviceKey = env('SUPABASE_SERVICE_KEY', '');
                if ($url && $serviceKey) {
                    $endpoint = rtrim($url, '/').'/rest/v1/discounts?id=eq.'.$discount->id;
                    $headers = [
                        'Authorization' => 'Bearer '.$serviceKey,
                        'apikey' => $serviceKey,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ];
                    Http::withHeaders($headers)->delete($endpoint);
                }
            } catch (\Throwable $e) {
                // Continue even if Supabase sync fails
            }

            $discount->delete();

            app(\App\Services\FeeManagementService::class)->recomputeForGrades([]);

            if (\Illuminate\Support\Facades\Schema::hasTable('fee_update_audits')) {
                try {
                    \Illuminate\Support\Facades\DB::table('fee_update_audits')->insert([
                        'performed_by_user_id' => optional(\Illuminate\Support\Facades\Auth::user())->user_id ?? null,
                        'event_type' => 'discount_deleted',
                        'school_year' => 'N/A',
                        'semester' => 'N/A',
                        'affected_students_count' => \App\Models\Student::count(),
                        'affected_staff_count' => \App\Models\User::where('roleable_type', 'App\\Models\\Staff')->count(),
                        'message' => 'Discount deleted.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                }
            }

            // Audit Log
            try {
                AuditService::log(
                    'Discount Deleted',
                    $discount,
                    "Deleted discount: {$oldValues['discount_name']}",
                    $oldValues,
                    null
                );
            } catch (\Throwable $e) {
            }

            return redirect()->route('admin.fees.index', ['tab' => 'discounts'])
                ->with('success', 'Discount deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.fees.index', ['tab' => 'discounts'])
                ->with('error', 'Failed to delete discount. Please try again.');
        }
    }

    /**
     * Generate fee assignments for all students in a school year/semester.
     */
    public function generateFeeAssignments(Request $request): RedirectResponse
    {
        try {
            $schoolYear = $request->input('school_year') ?: 'N/A';
            $semester = $request->input('semester') ?: 'N/A';
            $generated = 0;

            // Get all students
            $students = Student::whereNotNull('level')->get();

            DB::transaction(function () use ($students, $schoolYear, $semester, &$generated) {
                foreach ($students as $student) {
                    // Check if assignment already exists
                    $existing = FeeAssignment::where('student_id', $student->student_id)
                        ->where('school_year', $schoolYear)
                        ->where('semester', $semester)
                        ->first();

                    if (! $existing) {
                        FeeAssignment::assignForStudent($student->student_id, $schoolYear, $semester);
                        $generated++;
                    }
                }
            });

            $studentIds = Student::whereNotNull('level')->pluck('student_id')->all();
            $this->notifyStudents($studentIds, 'Fees Updated', 'Fee assignments have been refreshed.');
            $this->notifyStaff('Fees Updated', 'Fee assignments have been refreshed for all students.');

            if (\Illuminate\Support\Facades\Schema::hasTable('fee_update_audits')) {
                try {
                    \Illuminate\Support\Facades\DB::table('fee_update_audits')->insert([
                        'performed_by_user_id' => optional(\Illuminate\Support\Facades\Auth::user())->user_id ?? null,
                        'event_type' => 'fee_assignments_generated',
                        'school_year' => $schoolYear,
                        'semester' => $semester,
                        'affected_students_count' => count($studentIds),
                        'affected_staff_count' => \App\Models\User::where('roleable_type', 'App\\Models\\Staff')->count(),
                        'message' => 'Fee assignments regenerated and notifications broadcast.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                }
            }

            // Audit Log
            try {
                if ($generated > 0) {
                    AuditService::log(
                        'Fee Assignments Generated',
                        null,
                        "Generated fee assignments for {$generated} students (SY {$schoolYear}, {$semester})",
                        null,
                        ['generated_count' => $generated, 'school_year' => $schoolYear, 'semester' => $semester]
                    );
                }
            } catch (\Throwable $e) {
            }

            $staffCount = \App\Models\User::where('roleable_type', 'App\\Models\\Staff')->count();

            return redirect()->route('admin.fees.index', ['tab' => 'assignments'])
                ->with('success', "Fee assignments generated for {$generated} students. Broadcast sent to ".count($studentIds)." students and {$staffCount} staff.");
        } catch (\Exception $e) {
            return redirect()->route('admin.fees.index', ['tab' => 'assignments'])
                ->with('error', 'Failed to generate fee assignments. Please try again.');
        }
    }

    /**
     * Recalculate fees for a student.
     */
    public function recalculateStudentFees(FeeAssignment $feeAssignment): RedirectResponse
    {
        try {
            $feeAssignment->calculateTotal();

            return redirect()->back()
                ->with('success', 'Student fees recalculated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to recalculate student fees. Please try again.');
        }
    }

    /**
     * Show fee summary for preview.
     */
    public function summary(Request $request): View
    {
        $gradeLevels = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        $gradeLevel = $request->get('grade_level', $gradeLevels[0]);
        $schoolYear = \App\Models\SystemSetting::getActiveSchoolYear() ?? 'N/A';
        $semester = 'N/A';

        // Get tuition fee
        $tuitionFee = TuitionFee::active()
            ->forGrade($gradeLevel)
            ->when($schoolYear !== 'N/A', function ($q) use ($schoolYear) {
                return $q->where('school_year', $schoolYear);
            })
            ->first();

        // Get additional charges
        $additionalCharges = AdditionalCharge::active()
            ->applicableToGrade($gradeLevel)
            ->get();

        // Get eligible discounts
        $student = Student::where('level', $gradeLevel)->first(); // Get a sample student for eligibility check
        $discounts = collect();

        if ($student) {
            $discounts = Discount::active()
                ->automatic()
                ->applicableToGrade($gradeLevel)
                ->orderBy('priority', 'desc')
                ->get()
                ->filter(function ($discount) use ($student) {
                    return $discount->isEligibleForStudent($student) && $discount->isCurrentlyValid();
                });
        }

        // Calculate totals
        $baseTuition = $tuitionFee ? $tuitionFee->amount : 0;
        $additionalChargesTotal = $additionalCharges->sum('amount');
        $discountsTotal = 0;
        $discountBreakdown = [];
        $remainingTuition = $baseTuition;
        $remainingCharges = $additionalChargesTotal;
        $remainingTotal = $baseTuition + $additionalChargesTotal;
        $exclusiveApplied = false;
        foreach ($discounts as $discount) {
            if ($exclusiveApplied) {
                break;
            }
            $scope = $discount->getApplyScope();
            $applied = 0.0;
            if ($scope === 'tuition_only' && $remainingTuition > 0) {
                $applied = (float) $discount->calculateDiscountAmount($remainingTuition);
                $remainingTuition -= $applied;
            } elseif ($scope === 'charges_only' && $remainingCharges > 0) {
                $applied = (float) $discount->calculateDiscountAmount($remainingCharges);
                $remainingCharges -= $applied;
            } else {
                if ($remainingTotal > 0) {
                    $applied = (float) $discount->calculateDiscountAmount($remainingTotal);
                    $remainingTotal -= $applied;
                }
            }
            if ($applied > 0) {
                $discountsTotal += $applied;
                $discountBreakdown[] = [
                    'name' => $discount->discount_name,
                    'type' => $discount->type,
                    'value' => $discount->value,
                    'scope' => $scope,
                    'stackable' => $discount->isStackable(),
                    'applied_amount' => $applied,
                ];
                if (! $discount->isStackable()) {
                    $exclusiveApplied = true;
                }
            }
        }

        $totalAmount = max(0, $baseTuition + $additionalChargesTotal - $discountsTotal);

        return view('admin.fees.summary', [
            'gradeLevel' => $gradeLevel,
            'schoolYear' => $schoolYear,
            'semester' => $semester,
            'tuitionFee' => $tuitionFee,
            'additionalCharges' => $additionalCharges,
            'discounts' => $discounts,
            'discountBreakdown' => $discountBreakdown,
            'baseTuition' => $baseTuition,
            'additionalChargesTotal' => $additionalChargesTotal,
            'discountsTotal' => $discountsTotal,
            'totalAmount' => $totalAmount,
            'gradeLevels' => $gradeLevels,
        ]);
    }
}
