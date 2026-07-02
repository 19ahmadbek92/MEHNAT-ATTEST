<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErgonomicAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'workplace_id',
        'employee_id',
        'laboratory_id',
        'assessed_by',
        'operation_type',
        'load_description',
        'load_mass_kg',
        'lifts_per_shift',
        'lifts_per_minute',
        'shift_duration_hours',
        'worker_gender',
        'horizontal_distance_cm',
        'vertical_location_cm',
        'vertical_travel_cm',
        'asymmetry_angle_deg',
        'coupling_quality',
        'duration_category',
        'static_load_kgs',
        'posture_type',
        'body_inclinations_per_shift',
        'walking_distance_km',
        'attention_concentration_percent',
        'signals_per_hour',
        'responsibility_level',
        'monotony_operations_count',
        'night_shift',
        'temperature_c',
        'metal_dust_mg_m3',
        'noise_level_db',
        'uses_ppe',
        'employee_age',
        'experience_years',
        'training_completed',
        'last_training_at',
        'fatigue_self_score',
        'health_group',
        'prior_incidents_count',
        'rwl_kg',
        'lifting_index',
        'severity_class',
        'strain_class',
        'human_factor_risk_score',
        'integral_safety_index',
        'risk_category',
        'recommendations',
        'notes',
        'assessed_at',
    ];

    protected $casts = [
        'load_mass_kg' => 'decimal:2',
        'lifts_per_minute' => 'decimal:2',
        'shift_duration_hours' => 'decimal:2',
        'horizontal_distance_cm' => 'decimal:1',
        'vertical_location_cm' => 'decimal:1',
        'vertical_travel_cm' => 'decimal:1',
        'asymmetry_angle_deg' => 'decimal:1',
        'walking_distance_km' => 'decimal:2',
        'temperature_c' => 'decimal:1',
        'metal_dust_mg_m3' => 'decimal:2',
        'noise_level_db' => 'decimal:1',
        'experience_years' => 'decimal:1',
        'night_shift' => 'boolean',
        'uses_ppe' => 'boolean',
        'training_completed' => 'boolean',
        'last_training_at' => 'date',
        'assessed_at' => 'date',
        'rwl_kg' => 'decimal:2',
        'lifting_index' => 'decimal:2',
        'recommendations' => 'array',
    ];

    public function workplace()
    {
        return $this->belongsTo(Workplace::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function isHazardous(): bool
    {
        return in_array($this->severity_class, ['3.3', '3.4', '4'], true)
            || in_array($this->strain_class, ['3.3', '3.4', '4'], true);
    }
}
