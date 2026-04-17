<?php

namespace Tests\Feature;

use App\Models\AttestationApplication;
use App\Models\AttestationCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowStateMachineTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_rejects_invalid_transition(): void
    {
        $application = $this->makeApplication(AttestationApplication::STATUS_SUBMITTED);

        $updated = $application->transitionTo(AttestationApplication::STATUS_FINALIZED);

        $this->assertFalse($updated);
        $this->assertSame(AttestationApplication::STATUS_SUBMITTED, $application->fresh()->status);
    }

    public function test_hr_can_approve_and_finalize_via_valid_flow(): void
    {
        $hr = User::factory()->create(['role' => 'hr']);
        $application = $this->makeApplication(AttestationApplication::STATUS_SUBMITTED);

        $this->actingAs($hr)
            ->post(route('hr.applications.approve', $application), ['hr_comment' => 'ok'])
            ->assertRedirect();

        $this->assertSame(AttestationApplication::STATUS_HR_APPROVED, $application->fresh()->status);

        $this->actingAs($hr)
            ->post(route('hr.applications.finalize', $application), ['workplace_class' => 'optimal'])
            ->assertRedirect();

        $this->assertSame(AttestationApplication::STATUS_FINALIZED, $application->fresh()->status);
    }

    public function test_employer_cannot_access_hr_endpoints(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);
        $application = $this->makeApplication(AttestationApplication::STATUS_SUBMITTED);

        $this->actingAs($employer)
            ->post(route('hr.applications.approve', $application), ['hr_comment' => 'deny'])
            ->assertForbidden();
    }

    private function makeApplication(string $status): AttestationApplication
    {
        $campaign = AttestationCampaign::create([
            'title' => 'Test campaign',
            'description' => 'Test campaign description',
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'status' => 'open',
        ]);

        $owner = User::factory()->create(['role' => 'employer']);

        return AttestationApplication::create([
            'user_id' => $owner->id,
            'campaign_id' => $campaign->id,
            'workplace_name' => 'Press line',
            'status' => $status,
        ]);
    }
}
