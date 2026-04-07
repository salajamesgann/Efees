<?php

namespace App\Enums;

class PaymentStatus
{
    // Payment status constants
    const PENDING = 'pending';         // Awaiting admin approval
    const APPROVED = 'approved';       // Approved by admin (cash payment)
    const CONFIRMED = 'confirmed';     // Confirmed by payment gateway (online)
    const PAID = 'paid';               // Marked as paid (legacy/fallback)
    const SUCCESS = 'success';         // Successful (legacy/fallback)
    
    // Statuses that represent successful/collected payments
    const SUCCESSFUL_STATUSES = ['confirmed', 'approved', 'paid', 'success'];
    
    // Statuses that represent pending approval
    const PENDING_APPROVAL_STATUSES = ['pending'];
    
    /**
     * Get all valid payment statuses.
     */
    public static function all(): array
    {
        return [
            self::PENDING,
            self::APPROVED,
            self::CONFIRMED,
            self::PAID,
            self::SUCCESS,
        ];
    }
    
    /**
     * Get successful payment statuses.
     */
    public static function successful(): array
    {
        return self::SUCCESSFUL_STATUSES;
    }
}
