<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getActiveSchoolYear(): ?string
    {
        return self::where('key', 'school_year')->value('value');
    }

    public static function getValue(string $key, $default = null)
    {
        $val = self::where('key', $key)->value('value');

        return $val !== null ? $val : $default;
    }
}
