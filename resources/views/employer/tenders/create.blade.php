<x-app-layout>
    <x-slot name="header">＋ Yangi Tender Yaratish</x-slot>

    <p style="color: var(--muted); font-size: 14px; margin: 0 0 24px; font-style: italic;">
        Mehnat sharoitlarini o'lchash uchun akkreditatsiyalangan laboratoriyani tanlang.
    </p>

    <form action="{{ route('employer.tenders.store') }}" method="POST">
        @csrf

        <div class="att-card" style="margin-bottom: 20px;">
            <div class="att-card-header">
                <span class="att-card-title">🧪 Laboratoriyani tanlash</span>
            </div>
            <div class="att-card-body">
                <div class="att-field">
                    <label>Akkreditatsiyalangan tashkilot *</label>
                    <select name="laboratory_id" required>
                        <option value="">— Laboratoriyani tanlang —</option>
                        @foreach($laboratories as $lab)
                            <option value="{{ $lab->id }}" {{ old('laboratory_id') == $lab->id ? 'selected' : '' }}>
                                {{ $lab->name }} · Akkreditatsiya: {{ $lab->accreditation_certificate_number }}
                                (yaroqli: {{ $lab->accreditation_expiry_date?->format('d.m.Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="att-card" style="margin-bottom: 20px;">
            <div class="att-card-header">
                <span class="att-card-title">📅 Shartnoma muddatlari</span>
            </div>
            <div class="att-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="form-grid-responsive">
                    <div class="att-field">
                        <label>Boshlanish sanasi *</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required>
                    </div>
                    <div class="att-field">
                        <label>Tugash sanasi *</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" required>
                    </div>
                </div>
                <div class="att-field" style="margin-top: 4px;">
                    <label>Shartnoma detallari va talablar</label>
                    <textarea name="contract_details" rows="3" placeholder="O'lchash kerak bo'lgan omillar, maxsus talablar...">{{ old('contract_details') }}</textarea>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('employer.tenders.index') }}" class="btn-att btn-att-secondary">Bekor qilish</a>
            <button type="submit" class="btn-att btn-att-primary">🤝 Tender e'lon qilish</button>
        </div>
    </form>

    <style>
        @media (max-width: 768px) {
            .form-grid-responsive { grid-template-columns: 1fr !important; }
        }
    </style>
</x-app-layout>
