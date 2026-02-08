<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'level', 'strand_id'];

    public function strand()
    {
        return $this->belongsTo(Strand::class);
    }
}
