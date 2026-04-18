<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Identity\IdentityProviderManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class EriController extends Controller
{
    public function __construct(private readonly IdentityProviderManager $identityManager) {}

    public function login(Request $request): RedirectResponse
    {
        try {
            $user = $this->identityManager->eri()->resolveUser($request);
            Auth::login($user);

            return redirect()->route('dashboard');
        } catch (RuntimeException $e) {
            return redirect()->route('login')
                ->withErrors(['eri' => $e->getMessage()]);
        }
    }
}
