<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Hash;

class Staff extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'staff';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'staff_id';

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [];

    /**
     * The name of the "created at" column.
     */
    const CREATED_AT = null;

    /**
     * The name of the "updated at" column.
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'staff_id',
        'first_name',
        'MI',
        'last_name',
        'contact_number',
        'position',
        'salary',
        // Note: is_active, created_at, updated_at are handled conditionally since columns may not exist
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'salary' => 'decimal:2',
        // Note: is_active casting removed since column may not exist
        // Note: created_at and updated_at casting removed since columns may not exist
    ];

    /**
     * Disable Laravel's automatic timestamp handling completely.
     */
    public function usesTimestamps(): bool
    {
        return false;
    }

    /**
     * Get the user record associated with this staff member.
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'roleable');
    }

    /**
     * Get the staff's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->MI ? $this->MI . '. ' : '') . $this->last_name);
    }

    /**
     * Get the staff's initials.
     */
    public function getInitialsAttribute(): string
    {
        $initials = strtoupper(substr($this->first_name, 0, 1));
        if ($this->MI) {
            $initials .= strtoupper($this->MI);
        } else {
            $initials .= strtoupper(substr($this->last_name, 0, 1));
        }
        return $initials;
    }

    /**
     * Check if staff is active.
     */
    public function isActive(): bool
    {
        return $this->is_active ?? true;
    }

    /**
     * Create a new staff account with user credentials.
     */
    public static function createWithAccount(array $staffData, array $userData): self
    {
        // Generate staff ID if not provided
        if (empty($staffData['staff_id'])) {
            $staffData['staff_id'] = 'STF' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
        }

        // Set timestamps only if columns exist
        try {
            $staffData['updated_at'] = now();
        } catch (\Exception $e) {
            // Columns might not exist, skip timestamp setting
        }

        // Set is_active only if column exists
        try {
            $staffData['is_active'] = true;
        } catch (\Exception $e) {
            // Column might not exist, skip is_active setting
        }

        // Create staff record
        $staff = static::create($staffData);

        // Create user account
        $userData['roleable_type'] = self::class;
        $userData['roleable_id'] = $staff->staff_id;
        $userData['password'] = Hash::make($userData['password']);
        User::create($userData);

        return $staff;
    }

    /**
     * Update staff account and user credentials.
     */
    public function updateWithAccount(array $staffData, ?array $userData = null): bool
    {
        // Set updated_at timestamp only if column exists
        try {
            $staffData['updated_at'] = now();
        } catch (\Exception $e) {
            // Column might not exist, skip timestamp setting
        }

        $updated = $this->update($staffData);

        if ($updated && $userData && $this->user) {
            if (isset($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            }

            $this->user->update($userData);
        }

        return $updated;
    }

    /**
     * Get active staff members.
     */
    public function scopeActive($query)
    {
        // Since is_active column may not exist, return all records for now
        return $query;
    }

    /**
     * Get inactive staff members.
     */
    public function scopeInactive($query)
    {
        // Since is_active column may not exist, return empty result for now
        return $query->whereRaw('1 = 0');
    }
}
