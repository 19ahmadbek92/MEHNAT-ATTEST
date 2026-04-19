<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Identity\IdentityProviderManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

class EriController extends Controller
{
    public function __construct(private readonly IdentityProviderManager $identityManager) {}

    /**
     * Local / demo: darhol ERI provayderi orqali kirish. Production: imzo formasi.
     */
    public function form(Request $request): RedirectResponse|View
    {
        if (config('demo.sso') || app()->isLocal()) {
            try {
                $user = $this->identityManager->eri()->resolveUser($request);
                Auth::login($user);

                return redirect()->intended(route('dashboard'));
            } catch (RuntimeException $e) {
                return redirect()->route('login')
                    ->withErrors(['eri' => $e->getMessage()]);
            }
        }

        $request->session()->put('eri_challenge', bin2hex(random_bytes(16)));

        return view('auth.eri-form', [
            'challenge' => $request->session()->get('eri_challenge'),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'tin' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'signed_payload' => ['nullable', 'string'],
        ]);

        try {
            $user = $this->identityManager->eri()->resolveUser($request);
            Auth::login($user);

            return redirect()->intended(route('dashboard'));
        } catch (RuntimeException $e) {
            return redirect()->route('auth.eri.login')
                ->withInput($request->except('signed_payload'))
                ->withErrors(['eri' => $e->getMessage()]);
        }
    }
}
