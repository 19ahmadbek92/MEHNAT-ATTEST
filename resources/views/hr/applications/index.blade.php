<x-app-layout>
    <x-slot name="header">Davlat ekspertizasi: Arizalar</x-slot>

    <x-page-header
        title="Davlat ekspertizasi"
        subtitle="Tashkilotlardan kelgan ish o‘rni attestatsiyasi arizalarini ko‘rib chiqing va yakunlang."
        :crumbs="[
            ['label' => 'Bosh sahifa', 'url' => route('dashboard')],
            ['label' => 'Davlat ekspertizasi'],
        ]"
    />

    {{-- Filter chips --}}
    @php
        $tabs = [
            'submitted'   => ['label' => 'Yangi',         'icon' => '🆕'],
            'hr_approved' => ['label' => 'Tasdiqlangan',  'icon' => '⏳'],
            'finalized'   => ['label' => 'Yakunlangan',   'icon' => '✅'],
            'hr_rejected' => ['label' => 'Rad etilgan',   'icon' => '🚫'],
        ];
    @endphp

    <div class="filter-tabs">
        @foreach ($tabs as $key => $tab)
            <a href="{{ route('hr.applications.index', ['status' => $key]) }}"
               class="chip {{ $status === $key ? 'is-active' : '' }}">
                <span aria-hidden="true">{{ $tab['icon'] }}</span>
                {{ $tab['label'] }}
                <span class="count">{{ $counts[$key] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    {{-- Search bar --}}
    <form method="GET" action="{{ route('hr.applications.index') }}" class="att-toolbar">
        <input type="hidden" name="status" value="{{ $status }}" />
        <x-search-input name="q" placeholder="Ariza, tashkilot yoki ish o‘rni bo‘yicha qidirish…" />
        <x-att-button type="submit" variant="primary" size="sm">Qidirish</x-att-button>
        @if (request('q'))
            <x-att-button :href="route('hr.applications.index', ['status' => $status])" variant="secondary" size="sm">Tozalash</x-att-button>
        @endif
    </form>

    @if ($applications->isEmpty())
        <x-empty-state
            icon="📭"
            title="Bu bosqichda arizalar yo‘q"
            description="Hozircha ushbu holatdagi arizalar mavjud emas. Yangi arizalar paydo bo‘lganda ular avtomatik ravishda shu yerda ko‘rinadi."
        />
    @else
        {{-- Desktop table --}}
        <div class="att-card desktop-table" style="padding:0;">
            <table class="att-table">
                <thead>
                    <tr>
                        <th>Tashkilot va ish o‘rni</th>
                        <th>Kampaniya</th>
                        <th>Ball / klass</th>
                        <th style="text-align:center;">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($applications as $app)
                        @php
                            $accent = match ($app->status) {
                                'submitted'   => 'var(--gold)',
                                'finalized'   => 'var(--green)',
                                'hr_rejected' => 'var(--red)',
                                default       => 'var(--teal)',
                            };
                        @endphp
                        <tr style="border-left:4px solid {{ $accent }};">
                            <td>
                                <div style="font-weight:600;color:var(--ink);">{{ $app->user?->name }}</div>
                                <div style="color:var(--teal);font-weight:500;">{{ $app->workplace_name ?? $app->position }}</div>
                                <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;">{{ $app->department ?? '—' }}</div>
                            </td>
                            <td style="color:#555;">{{ $app->campaign?->title ?? '—' }}</td>
                            <td>
                                @if ($app->status === 'finalized')
                                    <div style="font-weight:700;color:var(--teal);text-transform:uppercase;letter-spacing:.3px;">{{ $app->getWorkplaceClassLabel() }}</div>
                                @else
                                    <div style="font-size:13px;color:var(--muted);font-style:italic;">O‘rtacha ball: {{ $app->final_score ?? '0.00' }}</div>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <x-att-button :href="route('hr.applications.show', $app)" variant="primary" size="sm">Ko‘rish</x-att-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile list --}}
        <div class="mobile-card-list">
            @foreach ($applications as $app)
                <a href="{{ route('hr.applications.show', $app) }}" class="mobile-app-card" style="display:block;text-decoration:none;color:inherit;">
                    <div style="font-weight:600;color:var(--ink);margin-bottom:4px;">{{ $app->user?->name }}</div>
                    <div style="color:var(--teal);font-size:14px;font-weight:500;">{{ $app->workplace_name ?? $app->position }}</div>
                    <div style="font-size:12px;color:var(--muted);margin-top:6px;">{{ $app->campaign?->title }}</div>
                    @if ($app->status === 'finalized')
                        <div style="margin-top:8px;font-weight:700;color:var(--teal);font-size:13px;">{{ $app->getWorkplaceClassLabel() }}</div>
                    @endif
                </a>
            @endforeach
        </div>

        {{ $applications->links() }}
    @endif
</x-app-layout>
