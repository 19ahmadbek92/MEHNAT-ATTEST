<x-app-layout>
    <x-slot name="header">Tender va shartnomalar</x-slot>

    <x-page-header
        title="Tender va shartnomalar"
        subtitle="Akkreditatsiyalangan laboratoriyani tanlang va attestatsiya o‘tkazish uchun tender e‘lon qiling."
        :crumbs="[
            ['label' => 'Bosh sahifa', 'url' => route('dashboard')],
            ['label' => 'Tenderlar'],
        ]"
    >
        <x-slot name="actions">
            <x-att-button :href="route('employer.tenders.create')" variant="primary">+ Yangi tender</x-att-button>
        </x-slot>
    </x-page-header>

    @if ($tenders->isEmpty())
        <x-empty-state
            icon="🤝"
            title="Hali tenderlar yo‘q"
            description="Birinchi tenderni yarating va akkreditatsiyalangan laboratoriya bilan shartnoma rasmiylashtiring."
        >
            <x-slot name="action">
                <x-att-button :href="route('employer.tenders.create')" variant="primary">Birinchi tender</x-att-button>
            </x-slot>
        </x-empty-state>
    @else
        @php
            $statusBadge = function (string $status) {
                return match ($status) {
                    'open'      => ['cls' => 'sb-submitted', 'label' => 'Ochiq'],
                    'awarded'   => ['cls' => 'sb-finalized', 'label' => 'Kelishilgan'],
                    'completed' => ['cls' => 'sb-pending',   'label' => 'Tugallangan'],
                    default     => ['cls' => 'sb-pending',   'label' => ucfirst($status)],
                };
            };
        @endphp

        {{-- Desktop --}}
        <div class="att-card desktop-table" style="padding:0;">
            <table class="att-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Laboratoriya</th>
                        <th>Boshlanish</th>
                        <th>Tugash</th>
                        <th>Holati</th>
                        <th style="text-align:center;">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tenders as $tender)
                        @php $b = $statusBadge($tender->status); @endphp
                        <tr>
                            <td style="font-weight:600;color:var(--muted);">#{{ $tender->id }}</td>
                            <td style="font-weight:600;color:var(--ink);">
                                {{ $tender->laboratory->name ?? '—' }}
                                @if ($tender->laboratory)
                                    <div style="font-size:11px;color:var(--muted);">Akkr: {{ $tender->laboratory->accreditation_certificate_number }}</div>
                                @endif
                            </td>
                            <td>{{ $tender->start_date->format('d.m.Y') }}</td>
                            <td>{{ $tender->end_date->format('d.m.Y') }}</td>
                            <td><span class="status-badge {{ $b['cls'] }}">{{ $b['label'] }}</span></td>
                            <td style="text-align:center;">
                                <x-att-button :href="route('employer.tenders.show', $tender)" variant="secondary" size="sm">Batafsil</x-att-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="mobile-card-list">
            @foreach ($tenders as $tender)
                @php $b = $statusBadge($tender->status); @endphp
                <div class="mobile-app-card border-gold">
                    <div style="margin-bottom:10px;">
                        <div style="font-weight:600;color:var(--ink);">{{ $tender->laboratory->name ?? 'Laboratoriya biriktirilmagan' }}</div>
                        <div style="font-size:12px;color:var(--muted);">{{ $tender->start_date->format('d.m.Y') }} – {{ $tender->end_date->format('d.m.Y') }}</div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
                        <span class="status-badge {{ $b['cls'] }}">{{ $b['label'] }}</span>
                        <x-att-button :href="route('employer.tenders.show', $tender)" variant="secondary" size="sm">Batafsil</x-att-button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
