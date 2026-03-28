<x-app-layout>
    <x-slot name="header">🏢 Korxona Profili</x-slot>

    @if(session('success'))
        <div style="margin-bottom: 20px; padding: 14px 18px; background: var(--green-light); color: var(--green); border-radius: 10px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; border: 1px solid rgba(26,107,60,0.2);">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="margin-bottom: 20px; padding: 14px 18px; background: var(--red-light); color: var(--red); border-radius: 10px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; border: 1px solid rgba(192,57,43,0.2);">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <p style="color: var(--muted); font-size: 14px; margin: 0 0 24px; font-style: italic;">
        Attestatsiyani boshlash uchun korxona rekvizitlarini to'liq kiriting.
    </p>

    <form action="{{ $organization ? route('employer.organization.update', $organization) : route('employer.organization.store') }}" method="POST">
        @csrf
        @if($organization) @method('PUT') @endif

        {{-- Asosiy ma'lumotlar --}}
        <div class="att-card" style="margin-bottom: 20px;">
            <div class="att-card-header">
                <span class="att-card-title">🏭 Asosiy rekvizitlar</span>
            </div>
            <div class="att-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="form-grid-responsive">
                    <div class="att-field">
                        <label>Korxona to'liq nomi *</label>
                        <input type="text" name="name" value="{{ old('name', $organization->name ?? '') }}" required placeholder="Masalan: Toshkent metallurgiya zavodi MChJ">
                    </div>
                    <div class="att-field">
                        <label>STIR (INN) *</label>
                        <input type="text" name="stir_inn" value="{{ old('stir_inn', $organization->stir_inn ?? '') }}" required placeholder="9 yoki 14 raqamli">
                    </div>
                    <div class="att-field">
                        <label>IFUT kodi</label>
                        <input type="text" name="ifut_code" value="{{ old('ifut_code', $organization->ifut_code ?? '') }}" placeholder="Iqtisodiy faoliyat turi kodi">
                    </div>
                    <div class="att-field">
                        <label>MHOBT kodi</label>
                        <input type="text" name="mhobt_code" value="{{ old('mhobt_code', $organization->mhobt_code ?? '') }}" placeholder="Mulk shakli kodi">
                    </div>
                    <div class="att-field">
                        <label>Faoliyat turi</label>
                        <input type="text" name="activity_type" value="{{ old('activity_type', $organization->activity_type ?? '') }}" placeholder="Masalan: Ishlab chiqarish, Qurilish">
                    </div>
                </div>
                <div class="att-field" style="margin-top: 4px;">
                    <label>Yuridik manzil</label>
                    <textarea name="legal_address" rows="2" placeholder="To'liq yuridik manzil...">{{ old('legal_address', $organization->legal_address ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Xodimlar tarkibi --}}
        <div class="att-card" style="margin-bottom: 20px;">
            <div class="att-card-header">
                <span class="att-card-title">👥 Xodimlar tarkibi</span>
            </div>
            <div class="att-card-body">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;" class="form-grid-responsive">
                    <div class="att-field">
                        <label>Jami xodimlar *</label>
                        <input type="number" min="0" name="total_employees" value="{{ old('total_employees', $organization->total_employees ?? 0) }}" required>
                    </div>
                    <div class="att-field">
                        <label>Shu jumladan ayollar *</label>
                        <input type="number" min="0" name="women_employees" value="{{ old('women_employees', $organization->women_employees ?? 0) }}" required>
                    </div>
                    <div class="att-field">
                        <label>Nogironligi bor xodimlar *</label>
                        <input type="number" min="0" name="disabled_employees" value="{{ old('disabled_employees', $organization->disabled_employees ?? 0) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <button type="submit" class="btn-att btn-att-primary">
                💾 Saqlash
            </button>
        </div>
    </form>

    <style>
        @media (max-width: 768px) {
            .form-grid-responsive { grid-template-columns: 1fr !important; }
        }
    </style>
</x-app-layout>
