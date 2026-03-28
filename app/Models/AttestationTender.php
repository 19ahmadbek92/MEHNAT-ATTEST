<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttestationTender extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'laboratory_id',
        'start_date',
        'end_date',
        'status',
        'contract_details',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }
}
