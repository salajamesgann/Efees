<?php

namespace App\Services;

use App\Models\AdditionalCharge;
use App\Models\Discount;
use App\Models\FeeAssignment;
use App\Models\FeeRecord;
use App\Models\Student;
use App\Models\TuitionFee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\AuditService;

class FeeManagementService
{
    private function resolveTuitionFeeForStudent(Student $student): ?TuitionFee
    {
        $query = TuitionFee::active()->forGrade($student->level);

        if (! empty($student->school_year)) {
            $query->where('school_year', $student->school_year);
        }

        $strand = in_array($student->level, ['Grade 11', 'Grade 12'], true) ? ($student->strand ?? null) : null;
        if ($strand) {
            $strandFee = (clone $query)->where('strand', $strand)->first();
            if ($strandFee) {
                return $strandFee;
            }
        }

        return $query->first();
    }

    public function computeTotalsForStudent(Student $student, bool $allowSyncWrites = true): array
    {
        if ($allowSyncWrites) {
            // Use savepoints so a failed write doesn't poison the PG transaction
            try { DB::transaction(fn () => \App\Models\Discount::ensureSiblingDefaults()); } catch (\Throwable $e) {}
            try { DB::transaction(fn () => \App\Models\Discount::ensureShsVoucherDefault()); } catch (\Throwable $e) {}
            try { DB::transaction(fn () => \App\Models\Discount::ensureAcademicScholarExclusive()); } catch (\Throwable $e) {}
        }

        $tuitionFee = $this->resolveTuitionFeeForStudent($student);

        // 1. Check for specific FeeAssignment first
        if ($tuitionFee) {
            $assignment = FeeAssignment::where('student_id', $student->student_id)
                ->where('tuition_fee_id', $tuitionFee->id)
                ->first();

            // Fallback: check by SY/Sem if tuition ID doesn't match but context does
            if (! $assignment) {
                $assignment = FeeAssignment::where('student_id', $student->student_id)
                    ->where('school_year', $tuitionFee->school_year)
                    ->where('semester', $tuitionFee->semester)
                    ->first();
            }

            // If still no assignment exists, create one now to keep totals and ledger consistent
            if (! $assignment && $allowSyncWrites) {
                try {
                    $assignment = FeeAssignment::assignForStudent(
                        $student->student_id,
                        $student->school_year ?? ($tuitionFee->school_year ?: 'N/A'),
                        $tuitionFee->semester ?? 'N/A'
                    );
                } catch (\Throwable $e) {
                    // Fall through to theoretical calculation if creation fails
                    $assignment = null;
                }
            }

            if ($assignment) {
                // Ensure additional charges are attached to assignment (fallback for older tuition records)
                // Wrapped in a savepoint so a failure here doesn't poison the PG transaction
                if ($allowSyncWrites) {
                    try { DB::transaction(function () use ($assignment, $student) {
                    if ($assignment->additionalCharges()->count() === 0) {
                        $chargeIds = [];

                        if ($assignment->tuitionFee) {
                            $tf = $assignment->tuitionFee;
                            if (! empty($tf->default_charge_ids)) {
                                $chargeIds = (array) $tf->default_charge_ids;
                            } else {
                                // Map tuition_fee_charges -> additional_charges by name/amount
                                $tfCharges = $tf->charges()->get(['name','amount']);
                                if ($tfCharges->isNotEmpty()) {
                                    $names = $tfCharges->pluck('name')->filter()->values()->all();
                                    $candidates = \App\Models\AdditionalCharge::active()
                                        ->whereIn('charge_name', $names)
                                        ->get();
                                    foreach ($tfCharges as $c) {
                                        $match = $candidates->first(function ($cand) use ($c) {
                                            return strcasecmp((string) $cand->charge_name, (string) $c->name) === 0
                                                && abs((float) $cand->amount - (float) $c->amount) < 0.01;
                                        });
                                        if ($match) {
                                            $chargeIds[] = $match->id;
                                        }
                                    }
                                }
                            }
                        }

                        // Final fallback: get active mandatory charges applicable to the grade
                        if (empty($chargeIds)) {
                            $chargeIds = \App\Models\AdditionalCharge::active()
                                ->mandatory()
                                ->applicableToGrade($student->level)
                                ->pluck('id')
                                ->all();
                        }

                        if (! empty($chargeIds)) {
                            $assignment->additionalCharges()->syncWithoutDetaching($chargeIds);
                        }
                    }
                    }); } catch (\Throwable $e) {
                    }
                }
                $existingDiscounts = $assignment->discounts()->get();
                $manualDiscountIds = $existingDiscounts->filter(function ($discount) {
                    return ! $discount->is_automatic;
                })->pluck('id')->all();

                $automaticDiscounts = Discount::active()
                    ->automatic()
                    ->applicableToGrade($student->level)
                    ->get();

                $defaultDiscounts = collect();
                if ($assignment->tuitionFee && ! empty($assignment->tuitionFee->default_discount_ids)) {
                    // Only consider defaults that are also automatic; exclude Academic Scholar by name
                    $defaultDiscounts = Discount::active()
                        ->whereIn('id', $assignment->tuitionFee->default_discount_ids)
                        ->get()
                        ->filter(function ($d) {
                            $name = strtolower(trim((string) $d->discount_name));
                            return $d->is_automatic && strpos($name, 'academic scholar') === false;
                        });
                }

                $eligibleDiscounts = $automaticDiscounts->merge($defaultDiscounts)
                    ->unique('id')
                    ->filter(function ($discount) use ($student) {
                        return $discount->isEligibleForStudent($student) && $discount->isCurrentlyValid();
                    })
                    ->reject(function ($d) {
                        // Ensure Academic Scholar variants are never auto-attached
                        $name = strtolower(trim((string) $d->discount_name));
                        return strpos($name, 'academic scholar') !== false;
                    });

                foreach ($eligibleDiscounts as $discount) {
                    if (strcasecmp(trim((string) $discount->discount_name), 'Sibling Discount') === 0) {
                        $details = json_encode([
                            'discount_name' => $discount->discount_name,
                            'grade_level' => $student->level,
                            'discount_rate' => $discount->value,
                        ]);
                        AuditService::logOnce('SIBLING_DISCOUNT_APPLIED', $student, $details, null, null, 3600);
                    }
                }

                $finalDiscountIds = array_values(array_unique(array_merge(
                    $manualDiscountIds,
                    $eligibleDiscounts->pluck('id')->all()
                )));

                if (! empty($finalDiscountIds) && $allowSyncWrites) {
                    try { DB::transaction(function () use ($assignment, $finalDiscountIds) {
                        $assignment->discounts()->sync($finalDiscountIds);
                        $assignment->discount_ids = $finalDiscountIds;
                        $assignment->save();
                    }); } catch (\Throwable $e) {
                        \App\Services\AuditService::log(
                            'DISCOUNT_SYNC_FAILED',
                            $student,
                            'Failed to sync discounts',
                            null,
                            ['error' => $e->getMessage(), 'discount_ids' => $finalDiscountIds]
                        );
                        $adminRole = \App\Models\Role::where('role_name', 'admin')->first();
                        if ($adminRole) {
                            $adminUsers = $adminRole->users()->get();
                            foreach ($adminUsers as $adminUser) {
                                \Illuminate\Support\Facades\DB::table('notifications')->insert([
                                    'user_id' => $adminUser->user_id,
                                    'title' => 'Discount Application Failure',
                                    'body' => 'Failed to apply automatic discounts for student '.$student->student_id,
                                    'created_at' => now(),
                                ]);
                            }
                        }
                    }
                }

                if ($allowSyncWrites) {
                    try { DB::transaction(fn () => $assignment->calculateTotal());
                    } catch (\Throwable $e) {
                        \App\Services\AuditService::log(
                            'DISCOUNT_CALCULATION_FAILED',
                            $student,
                            'Failed to calculate discounts',
                            null,
                            ['error' => $e->getMessage()]
                        );
                        $adminRole = \App\Models\Role::where('role_name', 'admin')->first();
                        if ($adminRole) {
                            $adminUsers = $adminRole->users()->get();
                            foreach ($adminUsers as $adminUser) {
                                \Illuminate\Support\Facades\DB::table('notifications')->insert([
                                    'user_id' => $adminUser->user_id,
                                    'title' => 'Fee Calculation Failure',
                                    'body' => 'Failed to calculate fee totals for student '.$student->student_id,
                                    'created_at' => now(),
                                ]);
                            }
                        }
                    }
                }

                // Ensure ledger 'tuition_total' reflects assignment total; skip if installment records exist
                if ($allowSyncWrites) {
                    try { DB::transaction(function () use ($student, $assignment) {
                    $hasInstallments = \App\Models\FeeRecord::where('student_id', $student->student_id)
                        ->whereIn('record_type', ['tuition_installment', 'tuition_base'])
                        ->exists();
                    if (! $hasInstallments) {
                        $currentTotal = (float) $assignment->total_amount;
                        $paidAmount = (float) \App\Models\Payment::where('student_id', $student->student_id)
                            ->where(function ($q) {
                                $q->whereIn('status', ['approved', 'paid'])
                                    ->orWhereNull('status');
                            })
                            ->sum('amount_paid');
                        $record = \App\Models\FeeRecord::firstOrNew([
                            'student_id' => $student->student_id,
                            'record_type' => 'tuition_total',
                        ]);
                        $expectedBalance = max(0.0, $currentTotal - $paidAmount);
                        if (abs(((float) ($record->amount ?? 0)) - $currentTotal) > 0.009 || abs(((float) ($record->balance ?? 0)) - $expectedBalance) > 0.009) {
                            $record->amount = $currentTotal;
                            $record->balance = $expectedBalance;
                            $record->status = $expectedBalance > 0 ? 'pending' : 'paid';
                            $record->notes = 'System recalculated';
                            if (\Illuminate\Support\Facades\Schema::hasColumn('fee_records', 'payment_date') && ! $record->payment_date) {
                                $record->payment_date = now()->addDays(30);
                            }
                            $record->save();
                        }
                    }
                    }); } catch (\Throwable $e) {
                    }
                }

                $paidAmount = (float) \App\Models\Payment::where('student_id', $student->student_id)
                    ->where(function ($q) {
                        $q->whereIn('status', ['approved', 'paid'])
                            ->orWhereNull('status');
                    })
                    ->sum('amount_paid');

                $penaltiesTotal = (float) \App\Models\FeeRecord::where('student_id', $student->student_id)
                    ->where(function ($q) {
                        $q->where('record_type', 'penalty')
                            ->orWhere('record_type', 'fine');
                    })
                    ->sum('amount');

                $ledgerBalance = (float) \App\Models\FeeRecord::where('student_id', $student->student_id)
                    ->where('record_type', '!=', 'tuition_total')
                    ->sum('balance');

                $expectedBalance = max(0.0, (float) $assignment->total_amount - $paidAmount + $penaltiesTotal);
                $remainingBalance = $expectedBalance;

                return [
                    'baseTuition' => (float) $assignment->base_tuition,
                    'chargesTotal' => (float) $assignment->additional_charges_total,
                    'discountsTotal' => (float) $assignment->discounts_total,
                    'totalAmount' => (float) $assignment->total_amount,
                    'paidAmount' => $paidAmount,
                    'penaltiesTotal' => $penaltiesTotal,
                    'remainingBalance' => $remainingBalance,
                ];
            }
        }

        // 2. Generic Calculation (Fallback)
        // If there is no active tuition fee for this grade, do not show theoretical totals
        if (! $tuitionFee) {
            return [
                'baseTuition' => 0.0,
                'chargesTotal' => 0.0,
                'discountsTotal' => 0.0,
                'totalAmount' => 0.0,
                'paidAmount' => 0.0,
                'penaltiesTotal' => 0.0,
                'remainingBalance' => 0.0,
            ];
        }
        $baseTuition = $tuitionFee ? (float) $tuitionFee->amount : 0.0;

        $additionalCharges = AdditionalCharge::active()->applicableToGrade($student->level)->get();
        $chargesTotal = (float) $additionalCharges->sum('amount');

        $automaticDiscounts = Discount::active()
            ->automatic()
            ->applicableToGrade($student->level)
            ->orderBy('priority', 'desc')
            ->get();
        $defaultDiscounts = collect();
        if ($tuitionFee && ! empty($tuitionFee->default_discount_ids)) {
            $defaultDiscounts = Discount::active()
                ->whereIn('id', $tuitionFee->default_discount_ids)
                ->get();
        }
        $discounts = $automaticDiscounts
            ->merge($defaultDiscounts)
            ->unique('id')
            ->filter(function ($discount) use ($student) {
                return $discount->isEligibleForStudent($student) && $discount->isCurrentlyValid();
            });
        // Ensure Academic Scholar is not automatically applied in fallback path
        $discounts = $discounts->reject(function ($d) {
            $name = strtolower(trim((string) $d->discount_name));
            return strpos($name, 'academic scholar') !== false;
        });
        if ($student->is_shs_voucher) {
            $discounts = $discounts->reject(function ($d) {
                return strcasecmp(trim((string) $d->discount_name), 'Sibling Discount') === 0
                    || collect($d->eligibility_rules)->contains('field', 'sibling_rank');
            });
        }

        $remainingTuition = $baseTuition;

        $chargeBalances = [];
        foreach ($additionalCharges as $charge) {
            $chargeBalances[$charge->id] = (float) $charge->amount;
        }

        $exclusiveApplied = false;
        $calculatedDiscountsTotal = 0.0;

        foreach ($discounts as $discount) {
            if ($exclusiveApplied) {
                break;
            }
            $scope = $discount->getApplyScope();
            $discountAmount = 0;

            if ($scope === 'tuition_only') {
                if ($remainingTuition > 0) {
                    $discountAmount = $discount->calculateDiscountAmount($remainingTuition);
                    $discountAmount = min($discountAmount, $remainingTuition);
                    $remainingTuition -= $discountAmount;
                }
            } elseif ($scope === 'specific_charges') {
                $targetIds = $discount->getTargetChargeIds();
                $eligibleAmount = 0;
                foreach ($targetIds as $id) {
                    $eligibleAmount += $chargeBalances[$id] ?? 0;
                }

                if ($eligibleAmount > 0) {
                    $discountAmount = $discount->calculateDiscountAmount($eligibleAmount);
                    $discountAmount = min($discountAmount, $eligibleAmount);
                    $deductionRatio = $eligibleAmount > 0 ? $discountAmount / $eligibleAmount : 0;
                    foreach ($targetIds as $id) {
                        if (isset($chargeBalances[$id])) {
                            $chargeBalances[$id] -= $chargeBalances[$id] * $deductionRatio;
                        }
                    }
                }
            } elseif ($scope === 'charges_only') {
                $currentChargesTotal = array_sum($chargeBalances);
                if ($currentChargesTotal > 0) {
                    $discountAmount = $discount->calculateDiscountAmount($currentChargesTotal);
                    $discountAmount = min($discountAmount, $currentChargesTotal);
                    $deductionRatio = $currentChargesTotal > 0 ? $discountAmount / $currentChargesTotal : 0;
                    foreach ($chargeBalances as $id => $bal) {
                        $chargeBalances[$id] -= $bal * $deductionRatio;
                    }
                }
            } else {
                $currentChargesTotal = array_sum($chargeBalances);
                $currentTotal = $remainingTuition + $currentChargesTotal;
                if ($currentTotal > 0) {
                    $discountAmount = $discount->calculateDiscountAmount($currentTotal);
                    $discountAmount = min($discountAmount, $currentTotal);
                    if ($remainingTuition >= $discountAmount) {
                        $remainingTuition -= $discountAmount;
                    } else {
                        $leftover = $discountAmount - $remainingTuition;
                        $remainingTuition = 0;
                        $currentChargesTotal = array_sum($chargeBalances);
                        if ($currentChargesTotal > 0) {
                            $deductionRatio = $leftover / $currentChargesTotal;
                            foreach ($chargeBalances as $id => $bal) {
                                $chargeBalances[$id] -= $bal * $deductionRatio;
                            }
                        }
                    }
                }
            }
            $calculatedDiscountsTotal += $discountAmount;
            if (! $discount->isStackable()) {
                $exclusiveApplied = true;
            }
        }
        $discountsTotal = $calculatedDiscountsTotal;

        $totalAmount = max(0.0, $baseTuition + $chargesTotal - $discountsTotal);

        // Calculate Real-time Payments and Balance from Ledger/Payments
        $paidAmount = (float) \App\Models\Payment::where('student_id', $student->student_id)
            ->where(function ($q) {
                $q->whereIn('status', ['approved', 'paid'])
                    ->orWhereNull('status');
            })
            ->sum('amount_paid');

        // Calculate Penalties (assuming record_type 'penalty')
        $penaltiesTotal = (float) \App\Models\FeeRecord::where('student_id', $student->student_id)
            ->where(function ($q) {
                $q->where('record_type', 'penalty')
                    ->orWhere('record_type', 'fine');
            })
            ->sum('amount');

        // Calculate Remaining Balance from Ledger (FeeRecords)
        // Note: totalAmount above is the THEORETICAL total. The actual balance is in FeeRecords.
        // If FeeRecords exist, use them. If not, use theoretical.
        // We must exclude 'tuition_total' (System recalculated) records to avoid double counting
        $ledgerBalance = (float) \App\Models\FeeRecord::where('student_id', $student->student_id)
            ->where('record_type', '!=', 'tuition_total')
            ->sum('balance');

        $expectedBalance = max(0.0, $totalAmount - $paidAmount);
        $remainingBalance = $expectedBalance;

        return [
            'baseTuition' => $baseTuition,
            'chargesTotal' => $chargesTotal,
            'discountsTotal' => $discountsTotal,
            'totalAmount' => $totalAmount, // Theoretical Total
            'paidAmount' => $paidAmount,
            'penaltiesTotal' => $penaltiesTotal,
            'remainingBalance' => $remainingBalance,
        ];
    }

    /**
     * Reconcile tuition installments with the current payment schedule.
     * Only updates if no payments have been made towards installments.
     */
    public function reconcileInstallments(Student $student): void
    {
        $assignment = FeeAssignment::where('student_id', $student->student_id)
            ->where('school_year', $student->school_year)
            ->with('tuitionFee')
            ->first();

        if (! $assignment || ! $assignment->tuitionFee) {
            return;
        }

        // Check if we have any 'tuition_installment' records that are paid or partially paid
        $hasPayments = FeeRecord::where('student_id', $student->student_id)
            ->where('record_type', 'tuition_installment')
            ->where(function ($q) {
                $q->where('status', 'paid')
                    ->orWhere('status', 'partial')
                    ->orWhereColumn('balance', '<', 'amount');
            })
            ->exists();

        if ($hasPayments) {
            // Cannot safely regenerate if payments exist
            return;
        }

        // Check if schedule exists
        $schedule = $assignment->tuitionFee->payment_schedule;
        if (empty($schedule)) {
            return;
        }

        // Check if we should regenerate (if existing installments differ from schedule or don't exist)
        // For simplicity and correctness, if no payments exist, we force regeneration to match current schedule.
        DB::transaction(function () use ($student, $assignment) {
            // Delete old pending installments
            FeeRecord::where('student_id', $student->student_id)
                ->where('record_type', 'tuition_installment')
                ->where('status', 'pending')
                ->delete();

            // Generate new ones
            $assignment->generateInstallments();
        });
    }

    public function recomputeStudentLedger(Student $student): void
    {
        // Ensure FeeAssignment exists for the student's current school year
        // This fixes the flow where fees are not automatically assigned on creation
        $schoolYear = $student->school_year;
        $assignment = FeeAssignment::where('student_id', $student->student_id)
            ->where('school_year', $schoolYear)
            ->first();

        if (! $assignment && $schoolYear) {
            // Auto-assign fees if missing
            $assignment = FeeAssignment::assignForStudent($student->student_id, $schoolYear, 'N/A');
            
            // If no assignment was created (no fees available), don't proceed
            if (! $assignment) {
                return;
            }
        }

        // Attempt to reconcile installments (e.g. if schedule changed)
        $this->reconcileInstallments($student);

        $totals = $this->computeTotalsForStudent($student);

        // Check if we have individual tuition records that replace the summary record
        $hasTuitionRecords = FeeRecord::where('student_id', $student->student_id)
            ->whereIn('record_type', ['tuition_installment', 'tuition_base'])
            ->exists();

        if ($hasTuitionRecords) {
            // If actual tuition installments exist, we should NOT have a summary 'tuition_total' record
            FeeRecord::where('student_id', $student->student_id)
                ->where('record_type', 'tuition_total')
                ->delete();

            $this->broadcastStudentUpdate($student, 'Fees Updated', 'Your fees have been recalculated.');
            $this->syncSupabaseBalance($student);

            return;
        }

        // Summary record logic: If no individual tuition records exist, create/update tuition_total.
        // This will exist alongside adjustments (discounts/charges).
        $paidAmount = $totals['paidAmount'];

        // The tuition_total record should match the assignment total amount,
        // and the balance equals (total - paid). Adjustments remain separate records.
        $theoreticalTotal = (float) $totals['totalAmount'];
        $baseBalance = max(0.0, $theoreticalTotal - $paidAmount);
        $status = $baseBalance > 0 ? 'pending' : 'paid';

        $record = FeeRecord::firstOrNew([
            'student_id' => $student->student_id,
            'record_type' => 'tuition_total',
        ]);

        $record->amount = max(0.0, $theoreticalTotal);
        $record->balance = $baseBalance;
        $record->status = $status;
        $record->notes = 'System recalculated';

        if (\Illuminate\Support\Facades\Schema::hasColumn('fee_records', 'payment_date')) {
            if (! $record->payment_date) {
                $record->payment_date = now()->addDays(30);
            }
        }

        $record->save();

        $this->broadcastStudentUpdate($student, 'Fees Updated', 'Your fees have been recalculated.');
        $this->syncSupabaseBalance($student);
    }

    public function recomputeForGrade(string $gradeLevel): int
    {
        $students = Student::where('level', $gradeLevel)
            ->whereNotIn('enrollment_status', ['Withdrawn', 'Archived'])
            ->get();
        $count = 0;
        DB::transaction(function () use ($students, &$count) {
            foreach ($students as $student) {
                $this->recomputeStudentLedger($student);
                $count++;
            }
        });
        $this->broadcastStaff('Fees Updated', 'Fees have been recalculated for grade '.$gradeLevel.'.');

        return $count;
    }

    private function syncSupabaseBalance(Student $student): void
    {
        $url = env('SUPABASE_URL', '');
        $serviceKey = env('SUPABASE_SERVICE_KEY', env('SUPABASE_KEY', ''));
        if (! $url || ! $serviceKey) {
            return;
        }
        $balanceRecord = FeeRecord::where('student_id', $student->student_id)
            ->where('record_type', 'tuition_total')
            ->first();
        $balance = (float) optional($balanceRecord)->balance ?? 0.0;
        $endpoint = rtrim($url, '/').'/rest/v1/student_balances';
        $headers = [
            'Authorization' => 'Bearer '.$serviceKey,
            'apikey' => $serviceKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Prefer' => 'resolution=merge-duplicates',
        ];
        try {
            $resp = Http::withHeaders($headers)->patch($endpoint.'?student_id=eq.'.urlencode($student->student_id), [
                'balance' => $balance,
                'updated_at' => now()->toISOString(),
            ]);
            if ($resp->status() === 404 || $resp->status() === 406) {
                Http::withHeaders($headers)->post($endpoint, [
                    'student_id' => $student->student_id,
                    'balance' => $balance,
                    'updated_at' => now()->toISOString(),
                ]);
            }
        } catch (\Throwable $e) {
        }
    }

    public function syncToSupabase(string $table, array $data, string $idColumn, string|int $idValue): void
    {
        $url = env('SUPABASE_URL', '');
        // Fallback to SUPABASE_KEY if SUPABASE_SERVICE_KEY is not set (useful for dev/setup)
        $serviceKey = env('SUPABASE_SERVICE_KEY', env('SUPABASE_KEY', ''));
        if (! $url || ! $serviceKey) {
            return;
        }
        $endpoint = rtrim($url, '/').'/rest/v1/'.$table;
        $headers = [
            'Authorization' => 'Bearer '.$serviceKey,
            'apikey' => $serviceKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Prefer' => 'resolution=merge-duplicates',
        ];
        try {
            $query = '?'.rawurlencode($idColumn).'=eq.'.rawurlencode((string) $idValue);
            $resp = Http::withHeaders($headers)->patch($endpoint.$query, $data);
            if ($resp->status() === 404 || $resp->status() === 406) {
                Http::withHeaders($headers)->post($endpoint, array_merge($data, [$idColumn => $idValue]));
            }
        } catch (\Throwable $e) {
        }
    }

    public function syncChargeAssignmentsToSupabase(int|string $chargeId, array $assignmentIds, array $meta = []): void
    {
        $url = env('SUPABASE_URL', '');
        $serviceKey = env('SUPABASE_SERVICE_KEY', env('SUPABASE_KEY', ''));
        if (! $url || ! $serviceKey || empty($assignmentIds)) {
            return;
        }
        $endpoint = rtrim($url, '/').'/rest/v1/fee_assignment_additional_charges?on_conflict=fee_assignment_id,additional_charge_id';
        $headers = [
            'Authorization' => 'Bearer '.$serviceKey,
            'apikey' => $serviceKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Prefer' => 'resolution=merge-duplicates',
        ];
        $now = now()->toISOString();
        $rows = [];
        foreach ($assignmentIds as $aid) {
            $rows[] = array_merge([
                'fee_assignment_id' => $aid,
                'additional_charge_id' => $chargeId,
                'amount' => $meta['amount'] ?? null,
                'date_added' => $meta['date_added'] ?? $now,
                'category' => $meta['category'] ?? null,
                'reference_no' => $meta['reference_no'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ], []);
        }
        try {
            Http::withHeaders($headers)->post($endpoint, $rows);
        } catch (\Throwable $e) {
        }
    }

    public function syncFeeAdditionalChargesToSupabase(int|string $chargeId, array $assignmentIds, array $payload): void
    {
        $url = env('SUPABASE_URL', '');
        $serviceKey = env('SUPABASE_SERVICE_KEY', '');
        if (! $url || ! $serviceKey || empty($assignmentIds)) {
            return;
        }
        $endpoint = rtrim($url, '/').'/rest/v1/fee_additional_charges?on_conflict=fee_assignment_id,additional_charge_id';
        $headers = [
            'Authorization' => 'Bearer '.$serviceKey,
            'apikey' => $serviceKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Prefer' => 'resolution=merge-duplicates',
        ];
        $now = now()->toISOString();
        $rows = [];
        foreach ($assignmentIds as $aid) {
            $rows[] = [
                'fee_assignment_id' => $aid,
                'additional_charge_id' => $chargeId,
                'charge_name' => $payload['charge_name'] ?? null,
                'description' => $payload['description'] ?? null,
                'amount' => $payload['amount'] ?? null,
                'charge_type' => $payload['charge_type'] ?? null,
                'school_year' => $payload['school_year'] ?? null,
                'applies_to' => $payload['applies_to'] ?? null,
                'grade_levels' => $payload['grade_levels'] ?? [],
                'track' => $payload['track'] ?? null,
                'strand' => $payload['strand'] ?? null,
                'required' => ($payload['required_or_optional'] ?? '') === 'required',
                'allow_installment' => (bool) ($payload['allow_installment'] ?? false),
                'include_in_total' => (bool) ($payload['include_in_total'] ?? true),
                'due_date' => $payload['due_date'] ?? null,
                'notes' => $payload['notes'] ?? null,
                'status' => $payload['status'] ?? 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        try {
            Http::withHeaders($headers)->post($endpoint, $rows);
        } catch (\Throwable $e) {
        }
    }

    public function recomputeForGrades(array $grades): int
    {
        if (empty($grades)) {
            $students = Student::whereNotNull('level')
                ->whereNotIn('enrollment_status', ['Withdrawn', 'Archived'])
                ->get();
        } else {
            $students = Student::whereIn('level', $grades)
                ->whereNotIn('enrollment_status', ['Withdrawn', 'Archived'])
                ->get();
        }
        $count = 0;
        DB::transaction(function () use ($students, &$count) {
            foreach ($students as $student) {
                $this->recomputeStudentLedger($student);
                $count++;
            }
        });
        $this->broadcastStaff('Fees Updated', 'Fees have been recalculated for affected students.');

        return $count;
    }

    public function broadcastStudentUpdate(Student $student, string $title, string $body): void
    {
        $user = User::where('roleable_type', 'App\\Models\\Student')
            ->where('roleable_id', $student->student_id)
            ->first();
        if ($user) {
            DB::table('notifications')->insert([
                'user_id' => $user->user_id,
                'title' => $title,
                'body' => $body,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function attachChargeToAssignmentsLocal(int $chargeId, array $assignmentIds): void
    {
        if (empty($assignmentIds)) {
            return;
        }
        $assignments = FeeAssignment::whereIn('id', $assignmentIds)->get();
        foreach ($assignments as $fa) {
            $fa->additionalCharges()->syncWithoutDetaching([$chargeId]);
        }
    }

    public function attachDiscountsToAssignmentsLocal(array $discountIds, array $assignmentIds): void
    {
        if (empty($assignmentIds) || empty($discountIds)) {
            return;
        }
        $assignments = FeeAssignment::whereIn('id', $assignmentIds)->get();
        foreach ($assignments as $fa) {
            foreach ($discountIds as $did) {
                $fa->discounts()->syncWithoutDetaching([$did]);
            }
        }
    }

    public function deleteSupabaseChargeAssignmentRows(int|string $chargeId): void
    {
        $url = env('SUPABASE_URL', '');
        $serviceKey = env('SUPABASE_SERVICE_KEY', '');
        if (! $url || ! $serviceKey) {
            return;
        }
        $headers = [
            'Authorization' => 'Bearer '.$serviceKey,
            'apikey' => $serviceKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        try {
            $pivot = rtrim($url, '/').'/rest/v1/fee_assignment_additional_charges?additional_charge_id=eq.'.rawurlencode((string) $chargeId);
            Http::withHeaders($headers)->delete($pivot);
            $details = rtrim($url, '/').'/rest/v1/fee_additional_charges?additional_charge_id=eq.'.rawurlencode((string) $chargeId);
            Http::withHeaders($headers)->delete($details);
        } catch (\Throwable $e) {
        }
    }

    public function patchSupabaseFeeAdditionalCharges(int|string $chargeId, array $payload): void
    {
        $url = env('SUPABASE_URL', '');
        $serviceKey = env('SUPABASE_SERVICE_KEY', '');
        if (! $url || ! $serviceKey) {
            return;
        }
        $endpoint = rtrim($url, '/').'/rest/v1/fee_additional_charges?additional_charge_id=eq.'.rawurlencode((string) $chargeId);
        $headers = [
            'Authorization' => 'Bearer '.$serviceKey,
            'apikey' => $serviceKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        $fields = [
            'charge_name' => $payload['charge_name'] ?? null,
            'description' => $payload['description'] ?? null,
            'amount' => $payload['amount'] ?? null,
            'charge_type' => $payload['charge_type'] ?? null,
            'required' => ($payload['required_or_optional'] ?? '') === 'required',
            'allow_installment' => (bool) ($payload['allow_installment'] ?? false),
            'include_in_total' => (bool) ($payload['include_in_total'] ?? true),
            'due_date' => $payload['due_date'] ?? null,
            'notes' => $payload['notes'] ?? null,
            'status' => $payload['status'] ?? 'active',
            'updated_at' => now()->toISOString(),
        ];
        try {
            Http::withHeaders($headers)->patch($endpoint, $fields);
        } catch (\Throwable $e) {
        }
    }

    public function syncChargeAssignmentsToSupabaseBatch(array $chargeIds, array $assignmentIds): void
    {
        if (empty($chargeIds) || empty($assignmentIds)) {
            return;
        }
        foreach ($chargeIds as $cid) {
            $this->syncChargeAssignmentsToSupabase($cid, $assignmentIds, [
                'amount' => null,
                'date_added' => now()->toISOString(),
                'category' => null,
                'reference_no' => null,
            ]);
        }
    }

    public function syncDiscountAssignmentsToSupabaseBatch(array $discountIds, array $assignmentIds): void
    {
        $url = env('SUPABASE_URL', '');
        $serviceKey = env('SUPABASE_SERVICE_KEY', '');
        if (! $url || ! $serviceKey || empty($discountIds) || empty($assignmentIds)) {
            return;
        }
        $endpoint = rtrim($url, '/').'/rest/v1/fee_assignment_discounts?on_conflict=fee_assignment_id,discount_id';
        $headers = [
            'Authorization' => 'Bearer '.$serviceKey,
            'apikey' => $serviceKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Prefer' => 'resolution=merge-duplicates',
        ];
        $now = now()->toISOString();
        $rows = [];
        foreach ($discountIds as $did) {
            foreach ($assignmentIds as $aid) {
                $rows[] = [
                    'fee_assignment_id' => $aid,
                    'discount_id' => $did,
                    'applied_amount' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        try {
            Http::withHeaders($headers)->post($endpoint, $rows);
        } catch (\Throwable $e) {
        }
    }

    public function broadcastStaff(string $title, string $body): void
    {
        $staffUsers = User::where('roleable_type', 'App\\Models\\Staff')->get(['user_id']);
        foreach ($staffUsers as $u) {
            DB::table('notifications')->insert([
                'user_id' => $u->user_id,
                'title' => $title,
                'body' => $body,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
