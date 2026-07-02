<?php

namespace Tests\Unit;

use App\Services\ErgonomicAssessmentService;
use PHPUnit\Framework\TestCase;

class ErgonomicAssessmentServiceTest extends TestCase
{
    private ErgonomicAssessmentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ErgonomicAssessmentService;
    }

    private function baseInput(array $overrides = []): array
    {
        return array_merge([
            'load_mass_kg' => 20,
            'lifts_per_shift' => 60,
            'lifts_per_minute' => 1,
            'shift_duration_hours' => 8,
            'worker_gender' => 'erkak',
            'horizontal_distance_cm' => 25,
            'vertical_location_cm' => 75,
            'vertical_travel_cm' => 25,
            'asymmetry_angle_deg' => 0,
            'coupling_quality' => 'good',
            'duration_category' => 'short',
            'posture_type' => 'erkin',
            'body_inclinations_per_shift' => 10,
            'walking_distance_km' => 1,
            'attention_concentration_percent' => 20,
            'signals_per_hour' => 10,
            'responsibility_level' => 'ozi_uchun',
            'monotony_operations_count' => 20,
            'night_shift' => false,
            'uses_ppe' => true,
            'fatigue_self_score' => 1,
            'health_group' => 'soglom',
            'prior_incidents_count' => 0,
            'experience_years' => 10,
            'training_completed' => true,
            'employee_age' => 30,
        ], $overrides);
    }

    public function test_niosh_rwl_and_lifting_index_for_a_favourable_task(): void
    {
        // H=25 -> HM=1, V=75 -> VM=1, D=25 -> DM=1.0, A=0 -> AM=1,
        // freq=1/min short duration V>=75 -> FM=0.94, coupling good -> CM=1.
        // RWL = 23 * 0.94 = 21.62
        $result = $this->service->evaluate($this->baseInput());

        $this->assertEqualsWithDelta(21.62, $result['rwl_kg'], 0.05);
        $this->assertEqualsWithDelta(20 / 21.62, $result['lifting_index'], 0.02);
    }

    public function test_lifting_index_rises_when_horizontal_distance_and_load_increase(): void
    {
        $favourable = $this->service->evaluate($this->baseInput());
        $strained = $this->service->evaluate($this->baseInput([
            'load_mass_kg' => 35,
            'horizontal_distance_cm' => 55,
            'vertical_travel_cm' => 90,
            'lifts_per_minute' => 8,
            'duration_category' => 'long',
        ]));

        $this->assertGreaterThan($favourable['lifting_index'], $strained['lifting_index']);
        $this->assertGreaterThan($strained['integral_safety_index'], $favourable['integral_safety_index']);
    }

    public function test_women_lifting_above_legal_limit_is_flagged_hazardous(): void
    {
        $result = $this->service->evaluate($this->baseInput([
            'worker_gender' => 'ayol',
            'load_mass_kg' => 20,
        ]));

        $this->assertSame('4', $result['severity_class']);
        $this->assertNotEmpty(array_filter(
            $result['recommendations'],
            fn (string $r) => str_contains($r, 'Ayol ishchilar')
        ));
    }

    public function test_human_factor_risk_increases_with_inexperience_and_no_training(): void
    {
        $experienced = $this->service->evaluate($this->baseInput());
        $novice = $this->service->evaluate($this->baseInput([
            'experience_years' => 0.5,
            'training_completed' => false,
            'fatigue_self_score' => 5,
            'prior_incidents_count' => 2,
        ]));

        $this->assertGreaterThan($experienced['human_factor_risk_score'], $novice['human_factor_risk_score']);
        $this->assertGreaterThan($novice['integral_safety_index'], $experienced['integral_safety_index']);
    }

    public function test_integral_safety_index_stays_within_bounds(): void
    {
        $worstCase = $this->service->evaluate($this->baseInput([
            'load_mass_kg' => 60,
            'horizontal_distance_cm' => 60,
            'vertical_travel_cm' => 150,
            'lifts_per_minute' => 15,
            'duration_category' => 'long',
            'posture_type' => 'qattiq_majburiy',
            'static_load_kgs' => 300000,
            'body_inclinations_per_shift' => 400,
            'walking_distance_km' => 20,
            'attention_concentration_percent' => 90,
            'signals_per_hour' => 400,
            'responsibility_level' => 'xavfsizlik_uchun',
            'monotony_operations_count' => 2,
            'night_shift' => true,
            'shift_duration_hours' => 13,
            'temperature_c' => 45,
            'metal_dust_mg_m3' => 15,
            'noise_level_db' => 95,
            'uses_ppe' => false,
            'experience_years' => 0,
            'training_completed' => false,
            'fatigue_self_score' => 5,
            'health_group' => 'nogironligi_bor',
            'prior_incidents_count' => 5,
        ]));

        $this->assertGreaterThanOrEqual(0, $worstCase['integral_safety_index']);
        $this->assertLessThanOrEqual(100, $worstCase['integral_safety_index']);
        $this->assertSame('Xavfli (4)', $worstCase['risk_category']);
        $this->assertNotEmpty($worstCase['recommendations']);
    }
}
