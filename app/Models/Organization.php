<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'stir_inn',
        'ifut_code',
        'mhobt_code',
        'parent_organization',
        'activity_type',
        'legal_address',
        'total_employees',
        'disabled_employees',
        'women_employees',
        'risk_profile_score',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function tenders()
    {
        return $this->hasMany(AttestationTender::class);
    }

    public function workplaces()
    {
        return $this->hasMany(Workplace::class);
    }
}
