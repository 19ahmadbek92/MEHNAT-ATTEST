<x-app-layout>
    <x-slot name="header">Komissiya: Tekshiriladiganlar</x-slot>

    <x-page-header
        title="Tekshirilishi kutilayotgan ish o‘rinlari"
        subtitle="Davlat ekspertizasi tomonidan tasdiqlangan va joyida tekshirilishi kerak bo‘lgan ish o‘rinlari ro‘yxati."
        :crumbs="[
            ['label' => 'Bosh sahifa', 'url' => route('dashboard')],
            ['label' => 'Komissiya'],
        ]"
    />

    @if ($applications->isEmpty())
        <x-empty-state
            icon="✅"
            title="Hammasi tekshirilgan"
            description="Hozircha komissiya tomonidan tekshirilishi kerak bo‘lgan ish o‘rinlari mavjud emas."
        />
    @else
        {{-- Desktop --}}
        <div class="att-card desktop-table" style="padding:0;">
            <table class="att-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tashkilot</th>
                        <th>Ish o‘rni</th>
                        <th>Bo‘lim</th>
                        <th>Kampaniya</th>
                        <th style="text-align:center;">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($applications as $app)
                        <tr style="border-left:4px solid var(--gold);">
                            <td style="font-weight:600;color:var(--muted);">#{{ $app->id }}</td>
                            <td style="font-weight:600;color:var(--ink);">{{ $app->user?->name }}</td>
                            <td>
                                <div style="font-weight:600;color:var(--ink);">{{ $app->workplace_name ?? $app->position }}</div>
                            </td>
                            <td style="color:#555;">{{ $app->department ?? '—' }}</td>
                            <td style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;">{{ $app->campaign?->title }}</td>
                            <td style="text-align:center;">
                                <x-att-button :href="route('commission.evaluations.form', $app)" variant="primary" size="sm">Tekshirish</x-att-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="mobile-card-list">
            @foreach ($applications as $app)
                <div class="mobile-app-card border-gold">
                    <div style="margin-bottom:8px;">
                        <div style="font-weight:600;color:var(--ink);">{{ $app->workplace_name ?? $app->position }}</div>
                        <div style="font-size:12px;color:var(--muted);">{{ $app->department ?? '—' }}</div>
                    </div>
                    <div style="font-size:13px;color:var(--teal);font-weight:500;margin-bottom:10px;">{{ $app->user?->name }}</div>
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
                        <span style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;">{{ $app->campaign?->title }}</span>
                        <x-att-button :href="route('commission.evaluations.form', $app)" variant="primary" size="sm">Tekshirish</x-att-button>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $applications->links() }}
    @endif
</x-app-layout>
