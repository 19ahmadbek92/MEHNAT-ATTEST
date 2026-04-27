<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Role-scoped login.
 *
 * Each role has its own dedicated login URL (/login/{role}). A user that
 * authenticates against a panel they do not belong to is logged out at once
 * and redirected back with an explanatory error — the UI is "strict" by
 * product decision.
 */
class RoleLoginController extends Controller
{
    /**
     * Visual metadata for every panel.
     *
     * Labels and subtitles are NOT stored here on purpose — they come from
     * the translation layer at request time so the entry/login pages
     * follow the active locale. Visual properties (icon glyph, accent
     * colour) stay static because they don't change with locale.
     *
     * @var array<string, array<string, string>>
     */
    public const PANELS = [
        'admin' => ['icon' => '⚙',  'accent' => '#c9952a'],
        'employer' => ['icon' => '🏭', 'accent' => '#0d6e6e'],
        'hr' => ['icon' => '📋', 'accent' => '#1a4db0'],
        'commission' => ['icon' => '🔍', 'accent' => '#176b3a'],
        'laboratory' => ['icon' => '🧪', 'accent' => '#4ecdc4'],
        'institute_expert' => ['icon' => '🏫', 'accent' => '#8a5cf6'],
        'expert' => ['icon' => '⚖',  'accent' => '#b83232'],
    ];

    /**
     * Hydrate the static metadata with translated copy for the active locale.
     *
     * @return array{label:string, subtitle:string, icon:string, accent:string}
     */
    private function panel(string $role): array
    {
        return [
            ...self::PANELS[$role],
            'label' => __('messages.roles.'.$role),
            'subtitle' => __('messages.panel_subtitle.'.$role),
        ];
    }

    public function show(string $role): View|RedirectResponse
    {
        if (! array_key_exists($role, self::PANELS)) {
            return redirect()->route('home');
        }

        return view('auth.role-login', [
            'role' => $role,
            'panel' => $this->panel($role),
        ]);
    }

    public function store(LoginRequest $request, string $role): RedirectResponse
    {
        if (! array_key_exists($role, self::PANELS)) {
            return redirect()->route('home');
        }

        $request->authenticate();

        $user = $request->user();

        // Strict role gate: even if the credentials are valid, an account that
        // does not belong to this panel is denied entry — fully matching the
        // product expectation that each panel is an independent door.
        if ($user?->role !== $role) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => __('messages.auth.denied_panel'),
            ])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return redirect()->intended($role === 'admin'
            ? route('admin.dashboard')
            : route('dashboard'));
    }
}
