<x-app-layout>
    <x-slot name="header">🧪 Laboratoriya Profili</x-slot>

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
        Tizimda ro'yxatdan o'tish va tenderlarni qabul qilish uchun akkreditatsiya ma'lumotlarini kiriting.
    </p>

    <form action="{{ $laboratory ? route('laboratory.profile.update', $laboratory) : route('laboratory.profile.store') }}" method="POST">
        @csrf
        @if($laboratory) @method('PUT') @endif

        <div class="att-card" style="margin-bottom: 20px;">
            <div class="att-card-header">
                <span class="att-card-title">🏢 Asosiy ma'lumotlar</span>
                @if($laboratory && $laboratory->is_active)
                    <span class="status-badge sb-finalized">Faol</span>
                @endif
            </div>
            <div class="att-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="form-grid-responsive">
                    <div class="att-field">
                        <label>Laboratoriya nomi *</label>
                        <input type="text" name="name" value="{{ old('name', $laboratory->name ?? '') }}" required placeholder="Masalan: Mehnat muhofazasi markazi MChJ">
                    </div>
                    <div class="att-field">
                        <label>STIR (INN) *</label>
                        <input type="text" name="stir_inn" value="{{ old('stir_inn', $laboratory->stir_inn ?? '') }}" required placeholder="9 yoki 14 raqamli">
                    </div>
                </div>
            </div>
        </div>

        <div class="att-card" style="margin-bottom: 20px;">
            <div class="att-card-header">
                <span class="att-card-title">🎓 Akkreditatsiya</span>
            </div>
            <div class="att-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="form-grid-responsive">
                    <div class="att-field">
                        <label>Akkreditatsiya guvohnomasi raqami *</label>
                        <input type="text" name="accreditation_certificate_number" value="{{ old('accreditation_certificate_number', $laboratory->accreditation_certificate_number ?? '') }}" required placeholder="Masalan: UZ.A.01.00123">
                    </div>
                    <div class="att-field">
                        <label>Akkreditatsiya muddati *</label>
                        <input type="date" name="accreditation_expiry_date"
                            value="{{ old('accreditation_expiry_date', optional($laboratory)->accreditation_expiry_date?->format('Y-m-d') ?? '') }}"
                            required>
                    </div>
                </div>
                <div class="att-field" style="margin-top: 4px;">
                    <label>Akkreditatsiya sohasi (O'lchash mumkin bo'lgan omillar) *</label>
                    <textarea name="accreditation_scope" rows="4" required placeholder="Masalan: Shovqin (dB), Tebranish (m/s²), Kimyoviy omillar (mg/m³), Yoritilganlik (Lux), Mikroiqlim...">{{ old('accreditation_scope', $laboratory->accreditation_scope ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <button type="submit" class="btn-att btn-att-primary">💾 Saqlash</button>
        </div>
    </form>

    <style>
        @media (max-width: 768px) {
            .form-grid-responsive { grid-template-columns: 1fr !important; }
        }
    </style>
</x-app-layout>
