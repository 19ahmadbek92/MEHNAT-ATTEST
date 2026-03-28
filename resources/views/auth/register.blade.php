<x-guest-layout>

    <div class="g-eyebrow">
        <div class="g-eyebrow-dot"></div>
        <span class="g-eyebrow-txt">Yangi hisob</span>
    </div>
    <div class="g-card-title">Ro'yxatdan o'ting</div>
    <div class="g-card-sub">Tizimda yangi hisob yarating</div>

    @if ($errors->any())
        <div class="g-alert-error">
            ⚠️ {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="g-field">
            <label class="g-label" for="name">To'liq ism</label>
            <input id="name" class="g-input" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Ism Familiya">
            @error('name')<div class="g-input-error">{{ $message }}</div>@enderror
        </div>

        <div class="g-field">
            <label class="g-label" for="email">Email manzil</label>
            <input id="email" class="g-input" type="email" name="email" value="{{ old('email') }}" required placeholder="email@example.com">
            @error('email')<div class="g-input-error">{{ $message }}</div>@enderror
        </div>

        <div class="g-field">
            <label class="g-label" for="password">Parol</label>
            <input id="password" class="g-input" type="password" name="password" required autocomplete="new-password" placeholder="Kamida 8 ta belgi">
            @error('password')<div class="g-input-error">{{ $message }}</div>@enderror
        </div>

        <div class="g-field">
            <label class="g-label" for="password_confirmation">Parolni tasdiqlang</label>
            <input id="password_confirmation" class="g-input" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Parolni qayta kiriting">
            @error('password_confirmation')<div class="g-input-error">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="g-btn g-btn-primary">
            ✅ Ro'yxatdan o'tish
        </button>
    </form>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('login') }}" class="g-link">Allaqachon hisobingiz bormi? Kiring →</a>
    </div>

</x-guest-layout>
