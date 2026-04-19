<?php

namespace Tests\Feature\Sso;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OneIdOAuthFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_oneid_callback_exchanges_code_and_logs_user_in(): void
    {
        config(['identity.sso_routes_enabled' => true]);
        config(['demo.sso' => false]);

        config([
            'services.oneid.base_url' => 'https://oauth.test',
            'services.oneid.client_id' => 'client-id',
            'services.oneid.client_secret' => 'client-secret',
            'services.oneid.redirect_uri' => 'https://app.test/callback',
        ]);

        Http::fake([
            'https://oauth.test/oauth2/token' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
            ], 200),
            'https://oauth.test/oauth2/userinfo' => Http::response([
                'sub' => 'oneid-sub-99',
                'pinfl' => '12345678901234',
                'name' => 'Test Foydalanuvchi',
                'email' => 'oneiduser@example.com',
            ], 200),
        ]);

        $state = bin2hex(random_bytes(8));

        $this->withSession(['oneid_state' => $state])
            ->get(route('auth.oneid.callback', [
                'code' => 'authorization-code-xyz',
                'state' => $state,
            ]))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'oneid_sub' => 'oneid-sub-99',
            'pinfl' => '12345678901234',
            'email' => 'oneiduser@example.com',
        ]);

        $this->assertAuthenticated();
    }
}
