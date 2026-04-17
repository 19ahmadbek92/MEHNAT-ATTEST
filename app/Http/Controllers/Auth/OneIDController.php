<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Identity\IdentityProviderManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class OneIDController extends Controller
{
    public function __construct(private readonly IdentityProviderManager $identityManager)
    {
    }

    /**
     * OneID xizmatiga yo'naltirish (demo varianti).
     *
     * Real integratsiyada bu yerda OneID SSO URL'iga redirect qilasiz.
     */
    public function redirect(Request $request): RedirectResponse
    {
        try {
            return $this->identityManager->oneId()->redirect($request);
        } catch (RuntimeException $e) {
            return redirect()->route('login')
                ->withErrors(['oneid' => $e->getMessage()]);
        }
    }

    /**
     * OneID callback (demo).
     *
     * Real holatda bu metod OneID'dan kelgan ma'lumotlarni qabul qiladi,
     * PINFL bo'yicha foydalanuvchini topadi yoki yaratadi va tizimga kiritadi.
     */
    public function callback(Request $request): RedirectResponse
    {
        try {
            $user = $this->identityManager->oneId()->resolveUser($request);
            Auth::login($user);

            return redirect()->route('dashboard');
        } catch (RuntimeException $e) {
            return redirect()->route('login')
                ->withErrors(['oneid' => $e->getMessage()]);
        }
    }
}