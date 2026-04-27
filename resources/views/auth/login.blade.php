<x-guest-layout>

    <div class="g-eyebrow">
        <div class="g-eyebrow-dot"></div>
        <span class="g-eyebrow-txt">Xavfsiz kirish</span>
    </div>
    <div class="g-card-title">Kabinetga kiring</div>
    <div class="g-card-sub">Login va parol orqali tizimga kiring</div>

    @if (session('status'))
        <div class="g-alert-error" style="background: rgba(13,110,110,0.1); border-color: rgba(13,110,110,0.25); color: #4ecdc4;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="g-alert-error">
            ⚠️ {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="g-field">
            <label class="g-label" for="email">Email manzil</label>
            <input id="email" class="g-input" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="email@example.com">
            @error('email')<div class="g-input-error">{{ $message }}</div>@enderror
        </div>

        <div class="g-field">
            <label class="g-label" for="password">Parol</label>
            <input id="password" class="g-input" type="password" name="password" required placeholder="••••••••">
            @error('password')<div class="g-input-error">{{ $message }}</div>@enderror
        </div>

        <div class="g-row-between">
            <label class="g-check-row" style="margin-bottom:0;">
                <input type="checkbox" name="remember">
                <span>Meni eslaysizmi</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="g-link">Parolni unutdingizmi?</a>
            @endif
        </div>

        <button type="submit" class="g-btn g-btn-primary">
            🔑 Tizimga kirish
        </button>
    </form>

    <div class="g-divider">
        <div class="g-divider-line"></div>
        <span class="g-divider-txt">yoki davlat tizimi orqali</span>
        <div class="g-divider-line"></div>
    </div>

    <div class="g-sso">
        <a href="{{ route('auth.oneid.redirect') }}" class="g-sso-btn g-sso-1">
            <div class="g-sso-icon">OneID</div>
            <div class="g-sso-body">
                <div class="g-sso-title">OneID orqali kirish</div>
                <div class="g-sso-sub">Davlat identifikatsiya xizmati</div>
            </div>
            <div class="g-sso-arrow">→</div>
        </a>

        <a href="{{ route('auth.eri.login') }}" class="g-sso-btn g-sso-2">
            <div class="g-sso-icon">🔏</div>
            <div class="g-sso-body">
                <div class="g-sso-title">ERI (E-IMZO) orqali</div>
                <div class="g-sso-sub">Elektron raqamli imzo</div>
            </div>
            <div class="g-sso-arrow">→</div>
        </a>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('home') }}#paneller" class="g-link">
            ← Kabinet tanlashga qaytish
        </a>
    </div>

</x-guest-layout>
