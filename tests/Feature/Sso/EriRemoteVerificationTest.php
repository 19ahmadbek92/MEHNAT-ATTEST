<?php

namespace Tests\Feature\Sso;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EriRemoteVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_eri_verify_creates_user_when_remote_service_accepts(): void
    {
        config(['identity.sso_routes_enabled' => true]);
        config(['demo.sso' => false]);
        config(['services.eri.verification_url' => 'https://eri.test/api/verify']);
        config(['services.eri.client_id' => 'eri-cid']);
        config(['services.eri.client_secret' => 'eri-sec']);

        Http::fake([
            'https://eri.test/*' => Http::response([
                'valid' => true,
                'message' => 'ok',
            ], 200),
        ]);

        $this->withSession(['eri_challenge' => 'challenge-abc'])
            ->post(route('auth.eri.verify'), [
                'tin' => '987654321',
                'name' => 'MCHJ Namuna',
                'email' => 'legal@example.com',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'tin' => '987654321',
            'email' => 'legal@example.com',
        ]);

        $this->assertAuthenticated();

        Http::assertSent(fn ($request) => str_contains($request->url(), 'eri.test'));
    }
}
