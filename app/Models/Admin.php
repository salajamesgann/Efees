<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Admin extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'admins';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'admin_id';

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
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'admin_id',
        'first_name',
        'MI',
        'last_name',
        'contact_number',
        'department',
        'position',
    ];

    /**
     * Get the user record associated with this admin.
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'roleable');
    }

    /**
     * Get the admin's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.($this->MI ? $this->MI.'. ' : '').$this->last_name);
    }

    /**
     * Get the admin's initials.
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
}
