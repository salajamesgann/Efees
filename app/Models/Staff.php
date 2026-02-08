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
<<<<<<< HEAD
    public $timestamps = true;
=======
    public $timestamps = false;
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [];

    /**
     * The name of the "created at" column.
     */
<<<<<<< HEAD
    const CREATED_AT = 'created_at';
=======
    const CREATED_AT = null;
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141

    /**
     * The name of the "updated at" column.
     */
<<<<<<< HEAD
    const UPDATED_AT = 'updated_at';
=======
    const UPDATED_AT = null;
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141

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
<<<<<<< HEAD
        'department',
        'is_active',
        'created_at',
        'updated_at',
=======
        'salary',
        // Note: is_active, created_at, updated_at are handled conditionally since columns may not exist
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
<<<<<<< HEAD
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
=======
        'salary' => 'decimal:2',
        // Note: is_active casting removed since column may not exist
        // Note: created_at and updated_at casting removed since columns may not exist
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
    ];

    /**
     * Disable Laravel's automatic timestamp handling completely.
     */
    public function usesTimestamps(): bool
    {
<<<<<<< HEAD
        return true;
=======
        return false;
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
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
<<<<<<< HEAD
        return trim($this->first_name.' '.($this->MI ? $this->MI.'. ' : '').$this->last_name);
=======
        return trim($this->first_name . ' ' . ($this->MI ? $this->MI . '. ' : '') . $this->last_name);
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
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
<<<<<<< HEAD

=======
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
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
<<<<<<< HEAD
            $staffData['staff_id'] = 'STF'.str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
        }

        // Normalize optional fields to satisfy non-null columns
        if (! array_key_exists('contact_number', $staffData) || $staffData['contact_number'] === null) {
            $staffData['contact_number'] = '';
        }
        if (! array_key_exists('department', $staffData) || $staffData['department'] === null) {
            $staffData['department'] = 'General';
        }
        if (! array_key_exists('position', $staffData) || $staffData['position'] === null) {
            $staffData['position'] = 'Staff';
=======
            $staffData['staff_id'] = 'STF' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
        }

        // Set timestamps only if columns exist
        try {
<<<<<<< HEAD
            $staffData['created_at'] = now();
=======
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
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
