<x-guest-layout>

    <div class="g-eyebrow">
        <div class="g-eyebrow-dot"></div>
        <span class="g-eyebrow-txt">Parolni tiklash</span>
    </div>
    <div class="g-card-title">Parolni unutdingizmi?</div>
    <div class="g-card-sub" style="margin-bottom: 20px;">
        Email manzilingizni kiriting — parolni tiklash havolasini yuboramiz.
    </div>

    @if (session('status'))
        <div class="g-alert-error" style="background: rgba(13,110,110,0.1); border-color: rgba(13,110,110,0.25); color: #4ecdc4; margin-bottom: 18px;">
            ✅ {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="g-alert-error">⚠️ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="g-field">
            <label class="g-label" for="email">Email manzil</label>
            <input id="email" class="g-input" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="email@example.com">
            @error('email')<div class="g-input-error">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="g-btn g-btn-primary">
            📧 Havola yuborish
        </button>
    </form>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('login') }}" class="g-link">← Kirishga qaytish</a>
    </div>

</x-guest-layout>
