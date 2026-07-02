<x-app-layout>
    <x-slot name="header">Ergonomik baholash: {{ $workplace->name }}</x-slot>

    <x-page-header
        title="{{ $workplace->name }} — o'tkazilgan ergonomik baholashlar"
        :subtitle="($workplace->organization->name ?? '').' korxonasidagi solish-ortish ishchilari uchun tarix.'"
        :crumbs="[
            ['label' => 'Bosh sahifa', 'url' => route('dashboard')],
            ['label' => 'Ish o‘rinlari o‘lchovi', 'url' => route('laboratory.workplaces.index')],
            ['label' => 'Ergonomik baholash'],
        ]"
    >
        <x-slot name="actions">
            <x-att-button :href="route('laboratory.ergonomic.create', $workplace)" variant="primary">
                + Yangi baholash
            </x-att-button>
        </x-slot>
    </x-page-header>

    @if ($assessments->isEmpty())
        <x-empty-state
            icon="🏗"
            title="Hali ergonomik baholash o'tkazilmagan"
            description="Yuklash-tushirish ishchilari uchun yangi baholashni boshlash uchun yuqoridagi tugmani bosing."
        />
    @else
        <div class="att-card desktop-table" style="padding:0;">
            <table class="att-table">
                <thead>
                    <tr>
                        <th>Sana</th>
                        <th>Xodim</th>
                        <th>Yuk / operatsiya</th>
                        <th>Ko'tarish indeksi (LI)</th>
                        <th>Og'irlik klassi</th>
                        <th>Integral xavfsizlik</th>
                        <th style="text-align:center;">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assessments as $a)
                        <tr>
                            <td>{{ optional($a->assessed_at)->format('d.m.Y') }}</td>
                            <td style="font-weight:600;color:var(--ink);">{{ $a->employee->full_name ?? '—' }}</td>
                            <td style="color:#555;">{{ $a->load_description ?? '—' }} ({{ $a->load_mass_kg }} kg)</td>
                            <td>{{ $a->lifting_index }}</td>
                            <td><span class="status-badge {{ $a->isHazardous() ? 'sb-rejected' : 'sb-approved' }}">{{ $a->severity_class }}</span></td>
                            <td>{{ $a->integral_safety_index }}/100 — {{ $a->risk_category }}</td>
                            <td style="text-align:center;">
                                <x-att-button :href="route('laboratory.ergonomic.show', [$workplace, $a])" variant="secondary" size="sm">Ko'rish</x-att-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mobile-card-list">
            @foreach ($assessments as $a)
                <div class="mobile-app-card {{ $a->isHazardous() ? 'border-red' : 'border-green' }}">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
                        <div>
                            <div style="font-weight:600;color:var(--ink);">{{ $a->employee->full_name ?? '—' }}</div>
                            <div style="font-size:13px;color:var(--teal);font-weight:500;">{{ $a->load_description ?? '—' }}</div>
                            <div style="font-size:12px;color:var(--muted);">{{ optional($a->assessed_at)->format('d.m.Y') }} · LI={{ $a->lifting_index }}</div>
                        </div>
                        <span class="status-badge {{ $a->isHazardous() ? 'sb-rejected' : 'sb-approved' }}">{{ $a->severity_class }}</span>
                    </div>
                    <div style="margin-top:10px;">
                        <x-att-button :href="route('laboratory.ergonomic.show', [$workplace, $a])" variant="secondary" size="sm" class="w-full">Ko'rish</x-att-button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
