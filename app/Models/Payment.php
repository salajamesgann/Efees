<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'student_id',
        'fee_record_id',
        'amount_paid',
        'status',
        'method',
        'reference_number',
        'remarks',
        'paid_at',
    ];

    /**
     * The "type" of the primary key ID.
     * Use string to ensure compatibility with polymorphic relations (AuditLog)
     * where model_id is a string, preventing PostgreSQL type mismatch errors.
     *
     * @var string
     */
    protected $keyType = 'string';

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(PaymentReceipt::class, 'payment_id', 'id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'model_id', 'id')
            ->where('model_type', self::class);
    }

    public function submitAuditLog(): HasOne
    {
        return $this->hasOne(AuditLog::class, 'model_id', 'id')
            ->where('model_type', self::class)
            ->where('action', 'Payment Submitted')
            ->latestOfMany();
    }
}
