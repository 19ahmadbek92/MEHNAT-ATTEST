<?php

namespace App\Services\Identity\Providers;

use App\Models\User;
use App\Services\Identity\Contracts\OneIdProviderContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class RealOneIdProvider implements OneIdProviderContract
{
    public function redirect(Request $request): RedirectResponse
    {
        $baseUrl = (string) config('services.oneid.base_url');
        $clientId = (string) config('services.oneid.client_id');
        $redirectUri = (string) config('services.oneid.redirect_uri');

        if ($baseUrl === '' || $clientId === '' || $redirectUri === '') {
            throw new RuntimeException('OneID configuration is incomplete.');
        }

        $state = hash('sha256', $request->ip().'|'.microtime(true));
        $request->session()->put('oneid_state', $state);

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => 'openid profile',
            'state' => $state,
        ]);

        return redirect()->away(rtrim($baseUrl, '/').'?'.$query);
    }

    public function resolveUser(Request $request): User
    {
        throw new RuntimeException('Real OneID callback exchange is not implemented yet.');
    }
}
