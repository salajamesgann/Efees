<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeRecord extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fee_records';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
<<<<<<< HEAD
    public $timestamps = true;
=======
    public $timestamps = false;
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141

    /**
     * The attributes that are mass assignable.
     *
<<<<<<< HEAD
     * @var array<string>
     */
    protected $fillable = [
        'student_id',
        'record_type',
        'amount',
        'balance',
        'status',
        'payment_method',
        'reference_number',
        'notes',
        'payment_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'payment_date' => 'datetime',
=======
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'fee_id',
        'balance',
        'status',
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
    ];

    /**
     * Get the student that owns the fee record.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
<<<<<<< HEAD

    /**
     * Get the fee assignment associated with this record.
     */
    public function feeAssignment()
    {
        return $this->belongsTo(FeeAssignment::class, 'student_id', 'student_id');
    }

    /**
     * Scope for payment records.
     */
    public function scopePayments($query)
    {
        return $query->where('record_type', 'payment');
    }

    /**
     * Scope for adjustments.
     */
    public function scopeAdjustments($query)
    {
        return $query->where('record_type', 'adjustment');
    }

    /**
     * Scope for refunds.
     */
    public function scopeRefunds($query)
    {
        return $query->where('record_type', 'refund');
    }

    /**
     * Scope for active records.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'paid', 'overdue']);
    }

    /**
     * Scope for completed payments.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for overdue payments.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₱'.number_format($this->amount, 2);
    }

    /**
     * Get formatted balance.
     */
    public function getFormattedBalanceAttribute(): string
    {
        return '₱'.number_format($this->balance, 2);
    }

    /**
     * Check if the record is fully paid.
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid' || $this->balance <= 0;
    }

    /**
     * Check if the record is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'overdue';
    }
=======
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
}
