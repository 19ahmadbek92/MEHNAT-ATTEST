<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workplace extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'department',
        'status',
        'employees_count',
        'is_active',
        'description',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function measurements()
    {
        return $this->hasMany(MeasurementResult::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isAttested()
    {
        return $this->status === 'attested';
    }
}
