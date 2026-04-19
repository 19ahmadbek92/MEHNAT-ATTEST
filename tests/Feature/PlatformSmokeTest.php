<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PlatformSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect();
    }

    public function test_home_and_select_type_render(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->get(route('auth.select-type'))->assertOk();
        $this->get(route('healthz'))
            ->assertOk()
            ->assertJsonStructure(['status', 'checks' => ['database', 'cache', 'storage']]);

        // Render: Renderda health check sifatida /container-live.txt (nginx statik, FPMsiz).
        // PHPUnit nginxsiz ishlaydi — fayl mavjudligini tekshiramiz.
        $this->assertFileExists(public_path('container-live.txt'));
        $this->assertStringContainsString('ok', (string) file_get_contents(public_path('container-live.txt')));
    }

    public function test_critical_named_routes_are_registered(): void
    {
        foreach ([
            'home',
            'dashboard',
            'healthz',
            'auth.select-type',
            'login',
            'admin.dashboard',
            'hr.applications.index',
            'employee.applications.index',
            'commission.evaluations.index',
            'ministry.expertise.index',
            'reports.index',
        ] as $name) {
            $this->assertTrue(Route::has($name), "Marshrut nomi yo'q: {$name}");
        }
    }

    public function test_admin_can_open_dashboard_and_admin_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_hr_can_open_applications_index(): void
    {
        $hr = User::factory()->create(['role' => 'hr']);

        $this->actingAs($hr)->get(route('hr.applications.index'))->assertOk();
    }

    public function test_employer_can_open_applications_index(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);

        $this->actingAs($employer)->get(route('employee.applications.index'))->assertOk();
    }

    public function test_commission_can_open_evaluations_index(): void
    {
        $user = User::factory()->create(['role' => 'commission']);

        $this->actingAs($user)->get(route('commission.evaluations.index'))->assertOk();
    }

    public function test_expert_can_open_ministry_expertise_index(): void
    {
        $expert = User::factory()->create(['role' => 'expert']);

        $this->actingAs($expert)->get(route('ministry.expertise.index'))->assertOk();
    }

    public function test_demo_sso_routes_are_closed_in_testing_environment(): void
    {
        $this->get(route('auth.oneid.redirect'))->assertNotFound();
        $this->get(route('auth.eri.login'))->assertNotFound();
    }
}
