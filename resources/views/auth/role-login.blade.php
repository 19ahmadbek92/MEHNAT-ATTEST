<x-guest-layout>

    <div class="g-eyebrow">
        <span class="g-eyebrow-dot" style="background:{{ $panel['accent'] }};box-shadow:0 0 7px {{ $panel['accent'] }};"></span>
        <span class="g-eyebrow-txt">{{ __('messages.auth.panel_cabinet', ['panel' => $panel['label']]) }}</span>
    </div>

    <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.06);
                    border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;
                    font-size:22px;color:{{ $panel['accent'] }};">
            {{ $panel['icon'] }}
        </div>
        <div>
            <div class="g-card-title" style="margin-bottom:2px;">{{ $panel['label'] }}</div>
            <div class="g-card-sub" style="margin:0;">{{ $panel['subtitle'] }}</div>
        </div>
    </div>

    @if (session('status'))
        <div class="g-alert-error" style="background:rgba(13,110,110,0.1);border-color:rgba(13,110,110,0.25);color:#4ecdc4;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="g-alert-error">⚠️ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.role.store', $role) }}">
        @csrf

        <div class="g-field">
            <label class="g-label" for="email">{{ __('messages.auth.email') }}</label>
            <input id="email" class="g-input" type="email" name="email" value="{{ old('email') }}"
                   required autofocus autocomplete="username" placeholder="email@example.com">
            @error('email')<div class="g-input-error">{{ $message }}</div>@enderror
        </div>

        <div class="g-field">
            <label class="g-label" for="password">{{ __('messages.auth.password') }}</label>
            <input id="password" class="g-input" type="password" name="password"
                   required autocomplete="current-password" placeholder="••••••••">
            @error('password')<div class="g-input-error">{{ $message }}</div>@enderror
        </div>

        <div class="g-row-between">
            <label class="g-check-row" style="margin-bottom:0;">
                <input type="checkbox" name="remember">
                <span>{{ __('messages.auth.remember') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="g-link">{{ __('messages.auth.forgot') }}</a>
            @endif
        </div>

        <button type="submit" class="g-btn g-btn-primary"
                style="background:linear-gradient(135deg, {{ $panel['accent'] }}, {{ $panel['accent'] }}dd);
                       box-shadow:0 4px 20px {{ $panel['accent'] }}40;">
            🔑 {{ __('messages.auth.sign_in_as', ['panel' => $panel['label']]) }}
        </button>
    </form>

    @if ($role === 'employer')
        <div class="g-divider">
            <div class="g-divider-line"></div>
            <span class="g-divider-txt">{{ __('messages.auth.or_state_system') }}</span>
            <div class="g-divider-line"></div>
        </div>

        <div class="g-sso">
            <a href="{{ route('auth.oneid.redirect') }}" class="g-sso-btn g-sso-1">
                <div class="g-sso-icon">OneID</div>
                <div class="g-sso-body">
                    <div class="g-sso-title">{{ __('messages.entry.sso_individual_sub') }}</div>
                    <div class="g-sso-sub">{{ __('messages.entry.sso_individual_title') }}</div>
                </div>
                <div class="g-sso-arrow">→</div>
            </a>

            <a href="{{ route('auth.eri.login') }}" class="g-sso-btn g-sso-2">
                <div class="g-sso-icon">🔏</div>
                <div class="g-sso-body">
                    <div class="g-sso-title">{{ __('messages.entry.sso_legal_sub') }}</div>
                    <div class="g-sso-sub">{{ __('messages.entry.sso_legal_title') }}</div>
                </div>
                <div class="g-sso-arrow">→</div>
            </a>
        </div>
    @endif

    <div style="text-align:center;margin-top:22px;display:flex;justify-content:space-between;align-items:center;gap:8px;">
        <a href="{{ route('home') }}" class="g-link">{{ __('messages.auth.go_home') }}</a>
        <a href="{{ route('home') }}" class="g-link">{{ __('messages.auth.other_cabinet') }}</a>
    </div>

</x-guest-layout>
