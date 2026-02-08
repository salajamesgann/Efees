<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdditionalCharge extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'additional_charges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'charge_name',
        'description',
        'charge_type',
        'school_year',
        'applies_to',
        'amount',
        'applicable_grades',
        'allow_installment',
        'include_in_total',
        'due_date',
        'notes',
        'status',
        'track',
        'strand',
        'required_or_optional',
        'is_active',
        'is_mandatory',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'applicable_grades' => 'array',
        'is_active' => 'boolean',
        'is_mandatory' => 'boolean',
        'allow_installment' => 'boolean',
        'include_in_total' => 'boolean',
        'due_date' => 'date',
    ];

    /**
     * Get the fee assignments that include this charge.
     */
    public function feeAssignments(): BelongsToMany
    {
        return $this->belongsToMany(FeeAssignment::class, 'fee_assignment_additional_charges', 'additional_charge_id', 'fee_assignment_id');
    }

    /**
     * Scope to get active charges.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get mandatory charges.
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
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
     * Check if this charge applies to a specific grade level.
     */
    public function appliesToGrade($gradeLevel): bool
    {
        return in_array($gradeLevel, $this->applicable_grades ?? []);
    }

    /**
     * Get the formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₱'.number_format($this->amount, 2);
    }

    /**
     * Get the status badge for the charge.
     */
    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_active) {
            return $this->is_mandatory ? 'Mandatory' : 'Optional';
        }

        return 'Inactive';
    }
}
