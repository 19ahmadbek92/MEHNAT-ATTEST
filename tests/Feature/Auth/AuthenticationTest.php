<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_redirects_to_panel_chooser(): void
    {
        $response = $this->get('/login');

        // /login no longer renders a form; users pick their panel from the
        // landing page where every role has its own door.
        $response->assertRedirect(route('home', absolute: false));
    }

    public function test_role_login_screen_can_be_rendered(): void
    {
        $this->get('/login/employer')->assertOk();
        $this->get('/login/admin')->assertOk();
        $this->get('/login/laboratory')->assertOk();
    }

    public function test_unknown_panel_falls_back_to_home(): void
    {
        $this->get('/login/ghost')->assertNotFound();
    }

    public function test_user_can_authenticate_through_their_own_panel(): void
    {
        $user = User::factory()->create(['role' => 'employer']);

        $response = $this->post('/login/employer', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_admin_authentication_lands_on_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->post('/login/admin', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_user_cannot_authenticate_through_a_foreign_panel(): void
    {
        $hr = User::factory()->create(['role' => 'hr']);

        $response = $this->post('/login/admin', [
            'email' => $hr->email,
            'password' => 'password',
        ]);

        // Even though the credentials are valid, the panel is wrong → user
        // is logged straight back out and an explanatory error is flashed.
        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create(['role' => 'employer']);

        $this->post('/login/employer', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
