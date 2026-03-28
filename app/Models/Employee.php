<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'workplace_id',
        'full_name',
        'pinfl',
        'gender',
        'employment_date',
        'is_active',
    ];

    public function workplace()
    {
        return $this->belongsTo(Workplace::class);
    }
}
