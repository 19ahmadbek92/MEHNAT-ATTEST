<x-guest-layout>
    <div class="g-eyebrow">
        <div class="g-eyebrow-dot"></div>
        <span class="g-eyebrow-txt">Yuridik shaxs</span>
    </div>
    <div class="g-card-title">ERI (E-IMZO) orqali kirish</div>
    <div class="g-card-sub">Ma'lumotlarni kiriting. Imzo E-IMZO dasturi bilan challenge matniga qo‘yiladi (ixtiyoriy, agar <code>ERI_VERIFICATION_URL</code> bo‘lmasa majburiy bo‘lishi mumkin).</div>

    @if ($errors->any())
        <div class="g-alert-error">⚠️ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('auth.eri.verify') }}">
        @csrf

        <div class="g-field">
            <label class="g-label" for="tin">STIR (9 raqam)</label>
            <input id="tin" class="g-input" type="text" name="tin" value="{{ old('tin') }}" required maxlength="20" inputmode="numeric" autocomplete="off">
        </div>

        <div class="g-field">
            <label class="g-label" for="name">Korxona / tashkilot nomi</label>
            <input id="name" class="g-input" type="text" name="name" value="{{ old('name') }}" required maxlength="255">
        </div>

        <div class="g-field">
            <label class="g-label" for="email">Aloqa email</label>
            <input id="email" class="g-input" type="email" name="email" value="{{ old('email') }}" required maxlength="255">
        </div>

        <div class="g-field">
            <label class="g-label" for="challenge">Imzolanadigan challenge (sessiya)</label>
            <textarea id="challenge" class="g-input" rows="2" readonly style="font-family:monospace;font-size:12px;">{{ $challenge }}</textarea>
            <div class="g-card-sub" style="margin-top:6px;">E-IMZO bilan shu matnni imzolang va natijani Base64 qilib pastga joylang (tashqi tekshiruv bo‘lmasa).</div>
        </div>

        <div class="g-field">
            <label class="g-label" for="signed_payload">Imzoli ma'lumot (Base64, ixtiyoriy)</label>
            <textarea id="signed_payload" class="g-input" name="signed_payload" rows="4" placeholder="PKCS7 (detached) Base64">{{ old('signed_payload') }}</textarea>
        </div>

        <button type="submit" class="g-btn g-btn-primary">Tekshirish va kirish</button>
    </form>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('login') }}" class="g-link">← Login sahifasiga</a>
    </div>
</x-guest-layout>
