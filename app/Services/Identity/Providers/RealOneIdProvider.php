<?php

namespace App\Services\Identity\Providers;

use App\Models\User;
use App\Services\Identity\Contracts\OneIdProviderContract;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class RealOneIdProvider implements OneIdProviderContract
{
    public function redirect(Request $request): RedirectResponse
    {
        $baseUrl = (string) config('services.oneid.base_url');
        $clientId = (string) config('services.oneid.client_id');
        $redirectUri = (string) config('services.oneid.redirect_uri');

        if ($baseUrl === '' || $clientId === '' || $redirectUri === '') {
            throw new RuntimeException('OneID configuration is incomplete (base_url, client_id, redirect_uri).');
        }

        $state = bin2hex(random_bytes(16));
        $request->session()->put('oneid_state', $state);

        $authorizePath = (string) config('services.oneid.authorize_path', '/oauth2/authorize');
        $authorizeUrl = $this->joinUrl($baseUrl, $authorizePath);

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => (string) config('services.oneid.scope', 'openid profile'),
            'state' => $state,
        ]);

        return redirect()->away($authorizeUrl.'?'.$query);
    }

    public function resolveUser(Request $request): User
    {
        if ($request->query('error')) {
            throw new RuntimeException('OneID: '.(string) $request->query('error_description', $request->query('error')));
        }

        $code = (string) $request->query('code', '');
        if ($code === '') {
            throw new RuntimeException('OneID callback missing authorization code.');
        }

        $sessionState = (string) $request->session()->pull('oneid_state', '');
        $queryState = (string) $request->query('state', '');
        if ($sessionState === '' || ! hash_equals($sessionState, $queryState)) {
            throw new RuntimeException('OneID state mismatch. Please try signing in again.');
        }

        $baseUrl = (string) config('services.oneid.base_url');
        $clientId = (string) config('services.oneid.client_id');
        $clientSecret = (string) config('services.oneid.client_secret');
        $redirectUri = (string) config('services.oneid.redirect_uri');
        $tokenPath = (string) config('services.oneid.token_path', '/oauth2/token');
        $userinfoPath = (string) config('services.oneid.userinfo_path', '/oauth2/userinfo');

        $tokenUrl = $this->joinUrl($baseUrl, $tokenPath);
        $userinfoUrl = $this->joinUrl($baseUrl, $userinfoPath);

        $tokenPayload = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri,
        ];

        $http = Http::asForm()->acceptJson()->timeout(30);
        if (config('services.oneid.token_auth') === 'basic') {
            $http = $http->withBasicAuth($clientId, $clientSecret);
        } else {
            $tokenPayload['client_id'] = $clientId;
            $tokenPayload['client_secret'] = $clientSecret;
        }

        try {
            $tokenResponse = $http->post($tokenUrl, $tokenPayload);
        } catch (RequestException $e) {
            throw new RuntimeException('OneID token request failed: '.$e->getMessage(), 0, $e);
        }

        if (! $tokenResponse->successful()) {
            throw new RuntimeException('OneID token endpoint error: '.$tokenResponse->body());
        }

        $accessToken = (string) data_get($tokenResponse->json(), 'access_token', '');
        if ($accessToken === '') {
            throw new RuntimeException('OneID token response missing access_token.');
        }

        try {
            $userinfoResponse = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout(30)
                ->get($userinfoUrl);
        } catch (RequestException $e) {
            throw new RuntimeException('OneID userinfo request failed: '.$e->getMessage(), 0, $e);
        }

        if (! $userinfoResponse->successful()) {
            throw new RuntimeException('OneID userinfo error: '.$userinfoResponse->body());
        }

        /** @var array<string, mixed> $userinfo */
        $userinfo = $userinfoResponse->json() ?? [];

        return $this->syncUserFromUserinfo($userinfo);
    }

    /**
     * @param  array<string, mixed>  $userinfo
     */
    private function syncUserFromUserinfo(array $userinfo): User
    {
        $subKey = (string) config('services.oneid.claim_sub', 'sub');
        $pinflKey = (string) config('services.oneid.claim_pinfl', 'pinfl');
        $nameKey = (string) config('services.oneid.claim_name', 'name');
        $emailKey = (string) config('services.oneid.claim_email', 'email');

        $sub = (string) $this->claim($userinfo, $subKey);
        if ($sub === '') {
            throw new RuntimeException('OneID userinfo missing subject. Check ONEID_CLAIM_SUB / provider response.');
        }

        $pinfl = $this->claim($userinfo, $pinflKey);
        $pinfl = $pinfl !== null && $pinfl !== '' ? (string) $pinfl : null;

        $name = (string) ($this->claim($userinfo, $nameKey) ?: 'OneID foydalanuvchi');
        $email = (string) ($this->claim($userinfo, $emailKey) ?: '');

        if ($email === '') {
            $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'app.local';
            $email = 'oneid.'.Str::lower(Str::slug(substr(hash('sha256', $sub), 0, 24), '-')).'@'.$host;
        }

        $user = User::query()->where('oneid_sub', $sub)->first();
        if ($user) {
            $user->forceFill(array_filter([
                'name' => $name,
                'email' => $email,
                'pinfl' => $pinfl,
                'person_type' => 'jismoniy',
                'is_verified' => true,
            ], fn ($v) => $v !== null))->save();

            return $user->fresh() ?? $user;
        }

        if ($pinfl !== null) {
            $existing = User::query()->where('pinfl', $pinfl)->first();
            if ($existing) {
                $existing->forceFill([
                    'oneid_sub' => $sub,
                    'name' => $name,
                    'email' => $email,
                    'person_type' => 'jismoniy',
                    'is_verified' => true,
                ])->save();

                return $existing->fresh() ?? $existing;
            }
        }

        return User::create([
            'oneid_sub' => $sub,
            'pinfl' => $pinfl,
            'name' => $name,
            'email' => $email,
            'password' => bcrypt(Str::random(48)),
            'role' => 'employer',
            'person_type' => 'jismoniy',
            'is_verified' => true,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function claim(array $data, string $key): mixed
    {
        if (str_contains($key, '.')) {
            return data_get($data, $key);
        }

        return $data[$key] ?? null;
    }

    private function joinUrl(string $base, string $path): string
    {
        return rtrim($base, '/').'/'.ltrim($path, '/');
    }
}
