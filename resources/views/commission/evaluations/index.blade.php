<x-app-layout>
    <x-slot name="header">🔍 Tekshirilishi kutilayotgan ish o'rinlari</x-slot>

    <p style="color: var(--muted); font-size: 14px; margin: 0 0 24px; font-style: italic;">Davlat ekspertizasi tomonidan tasdiqlangan va joyida tekshirilishi kerak bo'lgan ish o'rinlari.</p>

    {{-- Desktop Table --}}
    <div class="att-card desktop-table">
        <table class="att-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tashkilot</th>
                    <th>Ish o'rni nomi</th>
                    <th>Bo'lim</th>
                    <th>Kampaniya</th>
                    <th style="text-align: center;">Amallar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr style="border-left: 4px solid var(--gold);">
                        <td style="font-weight: 600; color: var(--muted);">#{{ $app->id }}</td>
                        <td style="font-weight: 600; color: var(--ink);">{{ $app->user?->name }}</td>
                        <td>
                            <div style="font-size: 16px; font-weight: 700; color: var(--ink);">{{ $app->workplace_name ?? $app->position }}</div>
                        </td>
                        <td style="color: #555;">{{ $app->department ?? '—' }}</td>
                        <td style="font-size: 12px; color: var(--muted); text-transform: uppercase;">{{ $app->campaign?->title }}</td>
                        <td style="text-align: center;">
                            <a href="{{ route('commission.evaluations.form', $app) }}" class="btn-att btn-att-primary btn-att-sm">
                                ✏️ Tekshirish
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 48px; color: var(--muted); font-style: italic;">
                            <div style="font-size: 36px; margin-bottom: 12px;">✅</div>
                            Hozircha tekshirilishi kerak bo'lgan ish o'rinlari mavjud emas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View --}}
    <div class="mobile-card-list">
        @forelse($applications as $app)
            <div class="mobile-app-card border-gold">
                <div style="margin-bottom: 10px;">
                    <div style="font-weight: 600; font-size: 15px; color: var(--ink);">{{ $app->workplace_name ?? $app->position }}</div>
                    <div style="font-size: 12px; color: var(--muted);">{{ $app->department ?? '—' }}</div>
                </div>
                <div style="font-size: 13px; color: var(--teal); font-weight: 500; margin-bottom: 10px;">{{ $app->user?->name }}</div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 11px; color: var(--muted); text-transform: uppercase;">{{ $app->campaign?->title }}</span>
                    <a href="{{ route('commission.evaluations.form', $app) }}" class="btn-att btn-att-primary btn-att-sm">✏️ Tekshirish</a>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 48px 20px; color: var(--muted);">
                <div style="font-size: 36px; margin-bottom: 12px;">✅</div>
                Tekshirilishi kerak bo'lgan ish o'rinlari mavjud emas.
            </div>
        @endforelse
    </div>

    @if($applications->hasPages())
        <div style="margin-top: 16px;">{{ $applications->links() }}</div>
    @endif
</x-app-layout>
