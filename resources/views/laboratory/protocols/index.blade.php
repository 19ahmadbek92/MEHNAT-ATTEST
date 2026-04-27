<x-app-layout>
    <x-slot name="header">Laboratoriya: O‘lchov protokollari</x-slot>

    <x-page-header
        title="O‘lchov protokollari"
        subtitle="Tender g‘olibi sifatida biriktirilgan korxonalarning ish o‘rinlarini o‘lchab, protokollarni to‘ldiring."
        :crumbs="[
            ['label' => 'Bosh sahifa', 'url' => route('dashboard')],
            ['label' => 'Laboratoriya'],
            ['label' => 'Protokollar'],
        ]"
    />

    @php $myLabId = auth()->user()->laboratory_id; @endphp

    @if ($applications->isEmpty())
        <x-empty-state
            icon="🧪"
            title="Biriktirilgan ish o‘rni yo‘q"
            description="Hozircha sizning laboratoriyangizga biriktirilgan kutilayotgan ish o‘rinlari mavjud emas."
        />
    @else
        {{-- Desktop --}}
        <div class="att-card desktop-table" style="padding:0;">
            <table class="att-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Buyurtmachi korxona</th>
                        <th>Ish o‘rni</th>
                        <th style="text-align:center;">Protokol holati</th>
                        <th style="text-align:center;">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($applications as $app)
                        @php $hasProtocol = $app->protocols && $app->protocols->where('laboratory_id', $myLabId)->count() > 0; @endphp
                        <tr style="border-left:4px solid {{ $hasProtocol ? 'var(--green)' : 'var(--gold)' }};">
                            <td style="font-weight:600;color:var(--muted);">#{{ $app->id }}</td>
                            <td style="font-weight:600;color:var(--ink);">{{ $app->user->organization->name ?? '—' }}</td>
                            <td>
                                <div style="font-weight:700;color:var(--ink);">{{ $app->workplace_name }}</div>
                                <div style="font-size:12px;color:var(--muted);">{{ $app->department ?? '' }}</div>
                            </td>
                            <td style="text-align:center;">
                                @if ($hasProtocol)
                                    <span class="status-badge sb-finalized">Kiritilgan</span>
                                @else
                                    <span class="status-badge sb-approved">Kutilmoqda</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <x-att-button :href="route('laboratory.protocols.create', $app)" variant="primary" size="sm">
                                    {{ $hasProtocol ? 'Tahrirlash' : 'Protokol to‘ldirish' }}
                                </x-att-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="mobile-card-list">
            @foreach ($applications as $app)
                @php $hasProtocol = $app->protocols && $app->protocols->where('laboratory_id', $myLabId)->count() > 0; @endphp
                <div class="mobile-app-card {{ $hasProtocol ? 'border-green' : 'border-gold' }}">
                    <div style="margin-bottom:10px;">
                        <div style="font-weight:700;color:var(--ink);">{{ $app->workplace_name }}</div>
                        <div style="font-size:12px;color:var(--muted);">{{ $app->user->organization->name ?? '' }}</div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
                        @if ($hasProtocol)
                            <span class="status-badge sb-finalized">Kiritilgan</span>
                        @else
                            <span class="status-badge sb-approved">Kutilmoqda</span>
                        @endif
                        <x-att-button :href="route('laboratory.protocols.create', $app)" variant="primary" size="sm">Protokol</x-att-button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
