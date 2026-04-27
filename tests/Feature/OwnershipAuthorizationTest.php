<?php

namespace Tests\Feature;

use App\Models\AttestationApplication;
use App\Models\AttestationCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ownership-based authorisation regression suite.
 *
 * Guards against accidental scope leaks when controllers are refactored:
 * a user must never reach another user's application or any role-bound page.
 */
class OwnershipAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_cannot_view_other_users_application(): void
    {
        $owner = User::factory()->create(['role' => 'employer']);
        $stranger = User::factory()->create(['role' => 'employer']);

        $campaign = AttestationCampaign::create([
            'title' => 'C1',
            'description' => 'Test',
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'status' => 'open',
        ]);

        $application = AttestationApplication::create([
            'user_id' => $owner->id,
            'campaign_id' => $campaign->id,
            'workplace_name' => 'Stamping cell',
            'status' => AttestationApplication::STATUS_SUBMITTED,
        ]);

        $this->actingAs($stranger)
            ->get(route('employee.applications.show', $application))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('employee.applications.show', $application))
            ->assertOk();
    }

    public function test_non_admin_roles_are_blocked_from_audit_log(): void
    {
        foreach (['employer', 'hr', 'commission', 'expert', 'institute', 'laboratory'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->get(route('admin.audit.index'))
                ->assertForbidden();
        }
    }

    public function test_admin_can_open_audit_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.audit.index'))
            ->assertOk();
    }

    public function test_security_headers_are_set_on_authenticated_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        $csp = (string) $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString('https://fonts.bunny.net', $csp);
    }

    public function test_recent_notifications_endpoint_is_authentication_protected(): void
    {
        $this->getJson(route('notifications.recent'))->assertUnauthorized();

        $user = User::factory()->create(['role' => 'employer']);
        $this->actingAs($user)
            ->getJson(route('notifications.recent'))
            ->assertOk()
            ->assertJsonStructure(['items']);
    }
}
