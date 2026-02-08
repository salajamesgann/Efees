<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TuitionFeeCharge extends Model
{
    use HasFactory;

    protected $table = 'tuition_fee_charges';

    protected $fillable = [
        'tuition_fee_id',
        'name',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function tuitionFee(): BelongsTo
    {
        return $this->belongsTo(TuitionFee::class);
    }
}
