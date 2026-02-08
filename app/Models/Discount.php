<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class Discount extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'discounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'discount_name',
        'type',
        'value',
        'eligibility_rules',
        'description',
        'applicable_grades',
        'is_active',
        'is_automatic',
        'priority',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'decimal:2',
        'eligibility_rules' => 'array',
        'applicable_grades' => 'array',
        'is_active' => 'boolean',
        'is_automatic' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Get the fee assignments that include this discount.
     */
    public function feeAssignments(): BelongsToMany
    {
        return $this->belongsToMany(FeeAssignment::class, 'fee_assignment_discounts', 'discount_id', 'fee_assignment_id')
            ->withPivot('applied_amount')
            ->withTimestamps();
    }

    /**
     * Scope to get active discounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get automatic discounts.
     */
    public function scopeAutomatic($query)
    {
        return $query->where('is_automatic', true);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by applicable grades.
     */
    public function scopeApplicableToGrade($query, $gradeLevel)
    {
        return $query->where(function ($q) use ($gradeLevel) {
            $q->whereNull('applicable_grades')
                ->orWhereJsonContains('applicable_grades', $gradeLevel);
        });
    }

    /**
     * Check if this discount applies to a specific grade level.
     */
    public function appliesToGrade($gradeLevel): bool
    {
        if (! $this->applicable_grades) {
            return true; // No grade restriction
        }

        return in_array($gradeLevel, $this->applicable_grades);
    }

    /**
     * Calculate the discount amount for a given base amount.
     */
    public function calculateDiscountAmount($baseAmount): float
    {
        if ($this->type === 'percentage') {
            $rules = $this->eligibility_rules ?? [];
            $maxPercent = (float) (data_get($rules, 'max_percent', null));
            $value = (float) $this->value;
            if ($maxPercent) {
                $value = min($value, $maxPercent);
            }

            return ($baseAmount * $value) / 100;
        }

        return min($this->value, $baseAmount); // Don't exceed base amount for fixed discounts
    }

    /**
     * Get the formatted value.
     */
    public function getFormattedValueAttribute(): string
    {
        if ($this->type === 'percentage') {
            return $this->value.'%';
        }

        return '₱'.number_format($this->value, 2);
    }

    /**
     * Get the discount type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return ucfirst($this->type);
    }

    public function isEligibleForStudent($student): bool
    {
        $name = trim((string) $this->discount_name);
        if (strcasecmp($name, 'Senior High School (SHS) Voucher') === 0) {
            if (! $student || ! in_array($student->level, ['Grade 11', 'Grade 12'], true)) {
                return false;
            }
            if (! property_exists($student, 'is_shs_voucher') && ! isset($student->is_shs_voucher)) {
                return false;
            }
            if (! $student->is_shs_voucher) {
                return false;
            }
        }

        $rules = $this->eligibility_rules ?? [];

        $isSiblingDiscount = strcasecmp(trim((string) $this->discount_name), 'Sibling Discount') === 0
            || array_key_exists('min_siblings', $rules);

        if ($isSiblingDiscount) {
            $validGrades = [
                'Grade 7',
                'Grade 8',
                'Grade 9',
                'Grade 10',
                'Grade 11',
                'Grade 12',
            ];

            if (! $student || ! in_array($student->level, $validGrades, true)) {
                return false;
            }

            if (in_array($student->level, ['Grade 11', 'Grade 12'], true) && $student->is_shs_voucher) {
                return false;
            }

            if ($this->countStudentSiblings($student) < 2) {
                return false;
            }
        }

        if (! $this->eligibility_rules) {
            return true;
        }

        return $this->checkEligibilityRules($student);
    }

    public function getApplyScope(): string
    {
        $scope = data_get($this->eligibility_rules, 'apply_scope', 'total');
        if (! in_array($scope, ['total', 'tuition_only', 'charges_only', 'specific_charges'])) {
            return 'total';
        }

        return $scope;
    }

    public function getTargetChargeIds(): array
    {
        return data_get($this->eligibility_rules, 'target_charge_ids', []);
    }

    public function isStackable(): bool
    {
        return (bool) data_get($this->eligibility_rules, 'is_stackable', true);
    }

    public function isCurrentlyValid(): bool
    {
        $from = data_get($this->eligibility_rules, 'valid_from');
        $to = data_get($this->eligibility_rules, 'valid_to');
        $now = Carbon::now()->startOfDay();
        if ($from) {
            try {
                $fromDate = Carbon::parse($from)->startOfDay();
                if ($now->lt($fromDate)) {
                    return false;
                }
            } catch (\Throwable $e) {
            }
        }
        if ($to) {
            try {
                $toDate = Carbon::parse($to)->endOfDay();
                if ($now->gt($toDate)) {
                    return false;
                }
            } catch (\Throwable $e) {
            }
        }

        return true;
    }

    /**
     * Check eligibility rules against student data.
     */
    private function checkEligibilityRules($student): bool
    {
        foreach ($this->eligibility_rules as $rule) {
            if (! $this->evaluateRule($rule, $student)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluate a single eligibility rule.
     */
    private function evaluateRule($rule, $student): bool
    {
        // Implement rule evaluation logic
        // Examples:
        // - min_siblings: student must have at least X siblings
        // - min_gpa: student must have GPA of at least X
        // - early_payment: student paid within X days of billing

        switch ($rule['field'] ?? '') {
            case 'min_siblings':
                return $this->countStudentSiblings($student) >= ($rule['value'] ?? 0);
            case 'max_siblings':
                return $this->countStudentSiblings($student) <= ($rule['value'] ?? PHP_INT_MAX);
            case 'sibling_rank':
                // Check if student's rank matches the rule value (e.g., 2 for 2nd child)
                return $this->getSiblingRank($student) === (int) ($rule['value'] ?? 0);
            case 'min_gpa':
                return isset($student->gpa) && $student->gpa >= ($rule['value'] ?? 0);
            case 'no_overdue_balance':
                return (float) ($student->current_balance ?? 0) <= 0.0;
            default:
                return true;
        }
    }

    /**
     * Get the student's rank among siblings based on enrollment date.
     * Returns 0 if not linked to a parent.
     * 1 = Eldest/First Enrolled, 2 = Second, etc.
     */
    public function getSiblingRank($student): int
    {
        // 1. Get Primary Parent
        $parent = $student->parents->where('pivot.is_primary', true)->first() ?? $student->parents->first();

        if (! $parent) {
            return 0;
        }

        $siblings = $parent->students()
            ->where(function ($q) use ($student) {
                $q->where('enrollment_status', 'Active')
                    ->orWhere('students.student_id', $student->student_id);
            })
            ->where('school_year', $student->school_year)
            ->select('students.student_id', 'students.date_of_birth')
            ->withPivot('created_at')
            ->get()
            ->sortBy(function ($child) {
                return $child->pivot->created_at
                    ? $child->pivot->created_at->timestamp
                    : 0;
            })
            ->values();

        // 3. Find rank
        $index = $siblings->search(function ($child) use ($student) {
            return $child->student_id === $student->student_id;
        });

        if ($index === false) {
            // If the student themselves isn't "active"/enrolled yet, they might not be in the list
            // if we filtered strict. But usually this is called during enrollment/fee assessment.
            // If we are assessing a student, they should count themselves.
            // If filtering removed them, return 0 (ineligible).
            return 0;
        }

        return $index + 1; // 0-based index to 1-based rank
    }

    /**
     * Count total siblings for a student.
     */
    public function countStudentSiblings($student): int
    {
        $parent = $student->parents->where('pivot.is_primary', true)->first() ?? $student->parents->first();

        if (! $parent) {
            return 0;
        }

        return $parent->students()
            ->where('enrollment_status', 'Active')
            ->where('school_year', $student->school_year)
            ->count();
    }

    /**
     * Ensure default sibling discounts exist (5% cap, automatic, active).
     */
    public static function ensureSiblingDefaults(): void
    {
        if (! Schema::hasTable('discounts')) {
            return;
        }

        $existing = static::where('discount_name', 'Sibling Discount')->first();
        if (! $existing) {
            static::create([
                'discount_name' => 'Sibling Discount',
                'type' => 'percentage',
                'value' => 5.00,
                'description' => 'Automatic 5% discount for siblings linked to the same parent.',
                'is_automatic' => true,
                'is_active' => true,
                'priority' => 100,
                'eligibility_rules' => [
                    'min_siblings' => 2,
                    'apply_scope' => 'tuition_only',
                    'is_stackable' => false,
                    'max_percent' => 5,
                ],
                'applicable_grades' => null,
            ]);
        }

        static::whereIn('discount_name', [
            'Sibling Discount - 2nd Child',
            'Sibling Discount - 3rd Child',
        ])->update(['is_active' => false]);
    }
}
