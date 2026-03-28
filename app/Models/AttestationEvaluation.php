<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttestationEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'evaluator_id',
        'score',
        'comment',
        'noise_level',
        'dust_level',
        'vibration_level',
        'lighting_level',
        'microclimate',
        'chemical_factors',
        'equipment_hazard_score',
        'protective_equipment_status',
    ];

    protected $casts = [
        'noise_level' => 'decimal:2',
        'dust_level' => 'decimal:2',
        'vibration_level' => 'decimal:2',
        'lighting_level' => 'decimal:2',
        'equipment_hazard_score' => 'integer',
    ];

    /**
     * Himoya vositalari holatini o'zbekchada qaytarish
     */
    public function getProtectiveStatusLabel(): string
    {
        return match ($this->protective_equipment_status) {
            'yetarli' => 'Yetarli',
            'qisman' => 'Qisman',
            'yetarli_emas' => 'Yetarli emas',
            default => '—',
        };
    }

    public function application()
    {
        return $this->belongsTo(AttestationApplication::class, 'application_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
