<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeAssignment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fee_assignments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'tuition_fee_id',
        'additional_charge_ids',
        'discount_ids',
        'base_tuition',
        'additional_charges_total',
        'discounts_total',
        'total_amount',
        'school_year',
        'semester',
        'is_finalized',
        'finalized_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'additional_charge_ids' => 'array',
        'discount_ids' => 'array',
        'base_tuition' => 'decimal:2',
        'additional_charges_total' => 'decimal:2',
        'discounts_total' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_finalized' => 'boolean',
        'finalized_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function tuitionFee(): BelongsTo
    {
        return $this->belongsTo(TuitionFee::class);
    }

    /**
     * Get the additional charges for this assignment.
     */
    public function additionalCharges(): BelongsToMany
    {
        return $this->belongsToMany(AdditionalCharge::class, 'fee_assignment_additional_charges', 'fee_assignment_id', 'additional_charge_id');
    }

    /**
     * Get the discounts for this assignment.
     */
    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class, 'fee_assignment_discounts', 'fee_assignment_id', 'discount_id')
            ->withPivot('applied_amount')
            ->withTimestamps();
    }

    /**
     * Get the student fee adjustments for this assignment.
     */
    public function adjustments(): HasMany
    {
        return $this->hasMany(StudentFeeAdjustment::class);
    }

    /**
     * Calculate and update the total amount.
     */
    public function calculateTotal(): void
    {
        $baseTuition = $this->tuitionFee ? $this->tuitionFee->amount : $this->base_tuition;

        $activeCharges = $this->additionalCharges()->where('is_active', true)->get();
        $standardAdditionalCharges = $activeCharges->sum('amount');

        $adjustments = $this->adjustments()->get();
        $adjustmentCharges = $adjustments->where('type', 'charge')->sum('amount');
        $adjustmentDiscounts = $adjustments->where('type', 'discount')->sum('amount');

        $additionalChargesTotal = $standardAdditionalCharges + $adjustmentCharges;

        $discounts = $this->discounts()->where('is_active', true)->get()
            ->sortByDesc(function ($discount) {
                $score = $discount->priority;

                if (stripos($discount->discount_name, 'voucher') !== false) {
                    $score += 2000;
                } elseif (collect($discount->eligibility_rules)->contains('field', 'sibling_rank')) {
                    $score += 1000;
                }

                return $score;
            });

        $calculatedDiscountsTotal = 0;
        $remainingTuition = $baseTuition;

        $chargeBalances = [];
        foreach ($activeCharges as $charge) {
            $chargeBalances[$charge->id] = (float) $charge->amount;
        }
        $remainingAdjustmentCharges = $adjustmentCharges;

        $exclusiveApplied = false;
        $nonVoucherPercentageTotal = 0;

        foreach ($discounts as $discount) {
            if ($exclusiveApplied) {
                break;
            }

            $isVoucher = stripos($discount->discount_name, 'voucher') !== false;
            $isSibling = collect($discount->eligibility_rules)->contains('field', 'sibling_rank');

            if (! $isVoucher && $discount->type === 'percentage') {
                $projected = $nonVoucherPercentageTotal + (float) $discount->value;
                if ($projected > 10.0) {
                    continue;
                }
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
                $currentChargesTotal = array_sum($chargeBalances) + $remainingAdjustmentCharges;

                if ($currentChargesTotal > 0) {
                    $discountAmount = $discount->calculateDiscountAmount($currentChargesTotal);
                    $discountAmount = min($discountAmount, $currentChargesTotal);

                    $deductionRatio = $currentChargesTotal > 0 ? $discountAmount / $currentChargesTotal : 0;
                    foreach ($chargeBalances as $id => $bal) {
                        $chargeBalances[$id] -= $bal * $deductionRatio;
                    }
                    $remainingAdjustmentCharges -= $remainingAdjustmentCharges * $deductionRatio;
                }
            } else {
                $currentChargesTotal = array_sum($chargeBalances) + $remainingAdjustmentCharges;
                $currentTotal = $remainingTuition + $currentChargesTotal;

                if ($currentTotal > 0) {
                    $discountAmount = $discount->calculateDiscountAmount($currentTotal);
                    $discountAmount = min($discountAmount, $currentTotal);

                    if ($remainingTuition >= $discountAmount) {
                        $remainingTuition -= $discountAmount;
                    } else {
                        $leftover = $discountAmount - $remainingTuition;
                        $remainingTuition = 0;
                        $currentChargesTotal = array_sum($chargeBalances) + $remainingAdjustmentCharges;
                        if ($currentChargesTotal > 0) {
                            $deductionRatio = $leftover / $currentChargesTotal;
                            foreach ($chargeBalances as $id => $bal) {
                                $chargeBalances[$id] -= $bal * $deductionRatio;
                            }
                            $remainingAdjustmentCharges -= $remainingAdjustmentCharges * $deductionRatio;
                        }
                    }
                }
            }

            $calculatedDiscountsTotal += $discountAmount;

            if (! $isVoucher && $discount->type === 'percentage') {
                $nonVoucherPercentageTotal += (float) $discount->value;
            }

            // Update the applied amount in the pivot
            $this->discounts()->updateExistingPivot($discount->id, [
                'applied_amount' => $discountAmount,
            ]);

            if (! $discount->isStackable()) {
                $exclusiveApplied = true;
            }
        }

        $discountsTotal = $calculatedDiscountsTotal + $adjustmentDiscounts;

        $this->update([
            'base_tuition' => $baseTuition,
            'additional_charges_total' => $additionalChargesTotal,
            'discounts_total' => $discountsTotal,
            'total_amount' => max(0, $baseTuition + $additionalChargesTotal - $discountsTotal),
        ]);
    }

    /**
     * Calculate total additional charges amount.
     */
    private function calculateAdditionalChargesTotal(): float
    {
        // Get active additional charges through the relationship
        return $this->additionalCharges()->where('is_active', true)->sum('amount');
    }

    /**
     * Calculate total discounts amount.
     */
    private function calculateDiscountsTotal($baseAmount): float
    {
        $totalDiscount = 0;
        $remainingAmount = $baseAmount;

        // Get active discounts through the relationship
        $discounts = $this->discounts()->where('is_active', true)->orderBy('priority', 'desc')->get();

        foreach ($discounts as $discount) {
            if ($remainingAmount <= 0) {
                break;
            }

            $discountAmount = $discount->calculateDiscountAmount($remainingAmount);
            $totalDiscount += $discountAmount;
            $remainingAmount -= $discountAmount;

            // Update the applied amount in the pivot
            $this->discounts()->updateExistingPivot($discount->id, [
                'applied_amount' => $discountAmount,
            ]);
        }

        return $totalDiscount;
    }

    /**
     * Auto-assign fees for a student based on their grade level.
     */
    public static function assignForStudent($studentId, $schoolYear, $semester): ?self
    {
        $student = Student::with('parents')->where('student_id', $studentId)->first();

        if (! $student || ! $student->level) {
            return null;
        }

        $gradeLevel = $student->level;

        // Get tuition fee with period filtering, fallback to active by grade
        $tuitionFee = TuitionFee::forGrade($gradeLevel)
            ->forSchoolYear($schoolYear)
            ->forSemester($semester)
            ->first();
        if (! $tuitionFee) {
            $tuitionFee = TuitionFee::active()
                ->forGrade($gradeLevel)
                ->first();
        }

        // Get applicable additional charges
        $additionalCharges = collect();
        if ($tuitionFee && isset($tuitionFee->default_charge_ids)) {
            // If default_charge_ids is defined (even if empty), use it as the source of truth
            if (! empty($tuitionFee->default_charge_ids)) {
                $additionalCharges = AdditionalCharge::active()
                    ->whereIn('id', $tuitionFee->default_charge_ids)
                    ->get();
            }
        } else {
            // Fallback to legacy behavior: all charges applicable to grade
            $additionalCharges = AdditionalCharge::active()
                ->applicableToGrade($gradeLevel)
                ->get();
        }

        // Get applicable discounts (check eligibility)
        $automaticDiscounts = Discount::active()
            ->automatic()
            ->applicableToGrade($gradeLevel)
            ->get();

        // Log Sibling Discount Skip (Voucher Exclusion)
        if ($student->is_shs_voucher) {
            foreach ($automaticDiscounts as $discount) {
                if (
                    strcasecmp(trim((string) $discount->discount_name), 'Sibling Discount') === 0
                    || collect($discount->eligibility_rules)->contains('field', 'sibling_rank')
                ) {
                    AuditLog::create([
                        'user_id' => auth()->id(),
                        'user_role' => 'system',
                        'action' => 'SIBLING_DISCOUNT_SKIPPED_VOUCHER',
                        'model_type' => 'Student',
                        'model_id' => $student->student_id,
                        'details' => json_encode([
                            'discount_name' => $discount->discount_name,
                            'reason' => 'SHS Voucher Recipient',
                            'parent_id' => $student->parents->sortByDesc('pivot.is_primary')->first()?->id,
                            'grade_level' => $student->level,
                            'child_order' => $discount->getSiblingRank($student),
                            'discount_rate' => $discount->value,
                        ]),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);

                    // Send SMS Notification
                    if ($contactNumber = $student->parents->sortByDesc('pivot.is_primary')->first()?->phone) {
                        try {
                            app(\App\Services\SmsService::class)->send(
                                $contactNumber,
                                "Sibling Discount Update: The sibling discount was not applied for {$student->first_name} as they are an SHS Voucher recipient.",
                                $student->student_id
                            );
                        } catch (\Throwable $e) {
                            // Ignore SMS failure to prevent blocking process
                        }
                    }
                }
            }
        }

        // Get default discounts from tuition fee
        $defaultDiscounts = collect();
        if ($tuitionFee && ! empty($tuitionFee->default_discount_ids)) {
            $defaultDiscounts = Discount::active()
                ->whereIn('id', $tuitionFee->default_discount_ids)
                ->get();
        }

        // Merge and filter by eligibility
        $eligibleDiscounts = $automaticDiscounts->merge($defaultDiscounts)
            ->unique('id')
            ->filter(function ($discount) use ($student) {
                return $discount->isEligibleForStudent($student);
            });

        // Log Sibling Discount Application
        foreach ($eligibleDiscounts as $discount) {
            if (
                strcasecmp(trim((string) $discount->discount_name), 'Sibling Discount') === 0
                || collect($discount->eligibility_rules)->contains('field', 'sibling_rank')
            ) {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'user_role' => 'system',
                    'action' => 'SIBLING_DISCOUNT_APPLIED',
                    'model_type' => 'Student',
                    'model_id' => $student->student_id,
                    'details' => json_encode([
                        'discount_name' => $discount->discount_name,
                        'parent_id' => $student->parents->sortByDesc('pivot.is_primary')->first()?->id,
                        'grade_level' => $student->level,
                        'child_order' => $discount->getSiblingRank($student),
                        'discount_rate' => $discount->value,
                    ]),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

                // Send SMS Notification
                if ($contactNumber = $student->parents->sortByDesc('pivot.is_primary')->first()?->phone) {
                    try {
                        app(\App\Services\SmsService::class)->send(
                            $contactNumber,
                            "Sibling Discount Applied: {$discount->discount_name} has been applied to {$student->first_name}'s account.",
                            $student->student_id
                        );
                    } catch (\Throwable $e) {
                        // Ignore SMS failure
                    }
                }
            }
        }

        // Create fee assignment
        $feeAssignment = self::create([
            'student_id' => $studentId,
            'tuition_fee_id' => $tuitionFee?->id,
            'additional_charge_ids' => $additionalCharges->pluck('id')->toArray(),
            'discount_ids' => $eligibleDiscounts->pluck('id')->toArray(),
            'base_tuition' => $tuitionFee?->amount ?? 0,
            'school_year' => $schoolYear ?? 'N/A',
            'semester' => $semester ?? 'N/A',
        ]);

        // Sync additional charges
        $feeAssignment->additionalCharges()->sync($additionalCharges->pluck('id'));

        // Sync discounts
        $feeAssignment->discounts()->sync($eligibleDiscounts->pluck('id'));

        // Calculate totals
        $feeAssignment->calculateTotal();

        // Create fee records from tuition payment schedule
        $schedule = is_array($tuitionFee?->payment_schedule) ? $tuitionFee->payment_schedule : [];

        // Handle both new format (with 'items') and legacy/manual format (array of items)
        $scheduleItems = [];
        if (isset($schedule['items']) && is_array($schedule['items'])) {
            $scheduleItems = $schedule['items'];
        } elseif (isset($schedule[0]) && is_array($schedule[0])) {
            $scheduleItems = $schedule;
        }

        if (! empty($scheduleItems)) {
            foreach ($scheduleItems as $item) {
                $amount = is_array($item) && array_key_exists('amount', $item) ? (float) $item['amount'] : 0.0;
                $due = is_array($item) && array_key_exists('due_date', $item) ? ($item['due_date'] ?: null) : null;

                // Fallback to 30 days from now if no due date is set
                if (! $due) {
                    $due = now()->addDays(30);
                }

                $label = is_array($item) && array_key_exists('label', $item) ? (string) $item['label'] : '';

                if ($amount <= 0) {
                    continue;
                }

                FeeRecord::create([
                    'student_id' => $studentId,
                    'record_type' => 'tuition_installment',
                    'amount' => $amount,
                    'balance' => $amount,
                    'status' => 'pending',
                    'notes' => trim("{$schoolYear} {$semester} ".($label ?: 'Tuition')),
                    'payment_date' => $due,
                ]);
            }
        }

        return $feeAssignment;
    }

    /**
     * Get the formatted total amount.
     */
    public function getFormattedTotalAttribute(): string
    {
        return '₱'.number_format($this->total_amount, 2);
    }

    /**
     * Get the formatted breakdown.
     */
    public function getFormattedBreakdownAttribute(): array
    {
        return [
            'base_tuition' => '₱'.number_format($this->base_tuition, 2),
            'additional_charges' => '₱'.number_format($this->additional_charges_total, 2),
            'discounts' => '-₱'.number_format($this->discounts_total, 2),
            'total' => '₱'.number_format($this->total_amount, 2),
        ];
    }

    /**
     * Scope for finalized assignments.
     */
    public function scopeFinalized($query)
    {
        return $query->where('is_finalized', true);
    }

    /**
     * Scope for pending assignments.
     */
    public function scopePending($query)
    {
        return $query->where('is_finalized', false);
    }

    /**
     * Scope for current school year.
     */
    public function scopeForSchoolYear($query, $schoolYear)
    {
        return $query->where('school_year', $schoolYear);
    }

    /**
     * Scope for current semester.
     */
    public function scopeForSemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function isLocked(): bool
    {
        return $this->student && $this->student->payments()->where('status', 'paid')->exists();
    }
}
