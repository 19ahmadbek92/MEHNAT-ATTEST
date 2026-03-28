<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'stir_inn',
        'accreditation_certificate_number',
        'accreditation_expiry_date',
        'accreditation_scope',
        'is_active',
    ];

    protected $casts = [
        'accreditation_expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function tenders()
    {
        return $this->hasMany(AttestationTender::class);
    }

    public function protocols()
    {
        return $this->hasMany(AttestationProtocol::class);
    }

    public function measurementResults()
    {
        return $this->hasMany(MeasurementResult::class);
    }
}
