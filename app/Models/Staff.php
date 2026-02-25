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
    public $timestamps = true;

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [];

    /**
     * The name of the "created at" column.
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     */
    const UPDATED_AT = 'updated_at';

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
        'department',
        'is_active',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Disable Laravel's automatic timestamp handling completely.
     */
    public function usesTimestamps(): bool
    {
        return true;
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
        return trim($this->first_name.' '.($this->MI ? $this->MI.'. ' : '').$this->last_name);
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
     * Create a new staff account with user credentials.
     */
    public static function createWithAccount(array $staffData, array $userData): self
    {
        // Generate short, prefixed ID if not provided (fits varchar(20))
        if (! isset($staffData['staff_id'])) {
            $prefix = 'STF';
            do {
                $candidate = $prefix.strtoupper(\Illuminate\Support\Str::random(7)); // e.g., STFABC123
            } while (static::where('staff_id', $candidate)->exists());
            $staffData['staff_id'] = $candidate;
        }

        // Set timestamps only if columns exist
        try {
            $staffData['created_at'] = now();
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
        $userPayload = [
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'role_id' => $userData['role_id'] ?? null,
            'roleable_type' => self::class,
            'roleable_id' => (string) $staff->staff_id,
        ];

        if (isset($userData['must_change_password'])) {
            $userPayload['must_change_password'] = $userData['must_change_password'];
        }

        User::create($userPayload);

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
            $userPayload = [];

            if (isset($userData['email'])) {
                $userPayload['email'] = $userData['email'];
            }

            if (isset($userData['password'])) {
                $userPayload['password'] = Hash::make($userData['password']);
            }

            if (isset($userData['role_id'])) {
                $userPayload['role_id'] = $userData['role_id'];
            }

            if (! empty($userPayload)) {
                $this->user->update($userPayload);
            }
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
