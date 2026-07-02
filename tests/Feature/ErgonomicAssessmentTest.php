<?php

namespace Tests\Feature;

use App\Models\AttestationTender;
use App\Models\Employee;
use App\Models\ErgonomicAssessment;
use App\Models\Laboratory;
use App\Models\Organization;
use App\Models\User;
use App\Models\Workplace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErgonomicAssessmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_laboratory_with_awarded_tender_can_create_and_view_assessment(): void
    {
        [$labUser, $workplace] = $this->makeAccreditedLabAndWorkplace();

        $this->actingAs($labUser)
            ->get(route('laboratory.ergonomic.create', $workplace))
            ->assertOk();

        $response = $this->actingAs($labUser)
            ->post(route('laboratory.ergonomic.store', $workplace), $this->validPayload());

        $assessment = ErgonomicAssessment::first();
        $response->assertRedirect(route('laboratory.ergonomic.show', [$workplace, $assessment]));

        $this->assertNotNull($assessment);
        $this->assertSame($workplace->id, $assessment->workplace_id);
        $this->assertNotNull($assessment->rwl_kg);
        $this->assertNotNull($assessment->lifting_index);
        $this->assertNotNull($assessment->integral_safety_index);
        $this->assertIsArray($assessment->recommendations);

        $this->actingAs($labUser)
            ->get(route('laboratory.ergonomic.show', [$workplace, $assessment]))
            ->assertOk()
            ->assertSee($assessment->risk_category);
    }

    public function test_laboratory_without_tender_cannot_access_workplace(): void
    {
        $organization = Organization::create([
            'name' => 'Boshqa Alyuminiy MChJ',
            'stir_inn' => '111222333',
        ]);
        $workplace = Workplace::create([
            'organization_id' => $organization->id,
            'name' => 'Ombor yuklovchisi',
            'employees_count' => 5,
        ]);

        $laboratory = Laboratory::create([
            'name' => 'Boshqa Laboratoriya',
            'stir_inn' => '444555666',
            'is_active' => true,
        ]);
        $labUser = User::factory()->create(['role' => 'laboratory', 'laboratory_id' => $laboratory->id]);

        $this->actingAs($labUser)
            ->get(route('laboratory.ergonomic.create', $workplace))
            ->assertForbidden();
    }

    public function test_non_laboratory_role_cannot_access_route(): void
    {
        [, $workplace] = $this->makeAccreditedLabAndWorkplace();
        $employer = User::factory()->create(['role' => 'employer']);

        $this->actingAs($employer)
            ->get(route('laboratory.ergonomic.create', $workplace))
            ->assertForbidden();
    }

    private function makeAccreditedLabAndWorkplace(): array
    {
        $organization = Organization::create([
            'name' => '"AlyuminProfil" MChJ',
            'stir_inn' => '987000111',
        ]);

        $workplace = Workplace::create([
            'organization_id' => $organization->id,
            'name' => 'Ombor solish-ortish ishchisi',
            'employees_count' => 3,
            'status' => 'pending',
        ]);

        Employee::create([
            'workplace_id' => $workplace->id,
            'full_name' => 'Aliyev Anvar',
            'is_active' => true,
        ]);

        $laboratory = Laboratory::create([
            'name' => 'Mehnat Muhofazasi Laboratoriyasi',
            'stir_inn' => '222333444',
            'is_active' => true,
        ]);

        AttestationTender::create([
            'organization_id' => $organization->id,
            'laboratory_id' => $laboratory->id,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'status' => 'awarded',
        ]);

        $labUser = User::factory()->create(['role' => 'laboratory', 'laboratory_id' => $laboratory->id]);

        return [$labUser, $workplace];
    }

    private function validPayload(): array
    {
        return [
            'operation_type' => 'qolda',
            'load_description' => "6 metrli alyuminiy profil bog'lami",
            'load_mass_kg' => 22,
            'lifts_per_shift' => 120,
            'lifts_per_minute' => 3,
            'shift_duration_hours' => 8,
            'worker_gender' => 'erkak',

            'horizontal_distance_cm' => 30,
            'vertical_location_cm' => 75,
            'vertical_travel_cm' => 60,
            'asymmetry_angle_deg' => 20,
            'coupling_quality' => 'fair',
            'duration_category' => 'long',

            'static_load_kgs' => 20000,
            'posture_type' => 'davriy_noqulay',
            'body_inclinations_per_shift' => 120,
            'walking_distance_km' => 5,

            'attention_concentration_percent' => 40,
            'signals_per_hour' => 50,
            'responsibility_level' => 'jamoa_uchun',
            'monotony_operations_count' => 6,
            'night_shift' => '0',

            'temperature_c' => 30,
            'metal_dust_mg_m3' => 4,
            'noise_level_db' => 78,
            'uses_ppe' => '1',

            'employee_age' => 32,
            'experience_years' => 3,
            'training_completed' => '1',
            'fatigue_self_score' => 2,
            'health_group' => 'soglom',
            'prior_incidents_count' => 0,

            'notes' => 'Test orqali yaratilgan baholash.',
        ];
    }
}
