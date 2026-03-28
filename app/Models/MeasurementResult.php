<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasurementResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'workplace_id',
        'laboratory_id',
        'factor_name',
        'measured_value',
        'norm_value',
        'danger_class',
        'protocol_number',
        'measured_at',
    ];

    protected $casts = [
        'measured_at' => 'date',
    ];

    public function workplace()
    {
        return $this->belongsTo(Workplace::class);
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }
}
