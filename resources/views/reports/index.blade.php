<x-app-layout>
    <x-slot name="header">Hisobotlar</x-slot>

    <x-page-header
        title="Hisobotlar"
        subtitle="Attestatsiya kampaniyalari bo‘yicha natijalar, ish o‘rinlari klassifikatsiyasi va eksport."
        :crumbs="[
            ['label' => 'Bosh sahifa', 'url' => route('dashboard')],
            ['label' => 'Hisobotlar'],
        ]"
    />

    @if ($campaigns->isEmpty())
        <x-empty-state
            icon="📊"
            title="Hozircha kampaniyalar yo‘q"
            description="Kampaniyalar yaratilgach, ularning natijalari va eksport havolalari shu yerda paydo bo‘ladi."
        />
    @else
        <div class="metric-grid" style="grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:18px;">
            @foreach ($campaigns as $campaign)
                @php
                    $total      = $campaign->applications->count();
                    $finalized  = $campaign->applications->where('status', 'finalized')->count();
                    $pct        = $total > 0 ? (int) round($finalized / $total * 100) : 0;
                    $accent     = $pct === 100 ? 'green' : ($pct > 50 ? 'gold' : 'teal');
                @endphp

                <div class="att-card att-card-accent-{{ $accent }}" style="padding:22px;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:14px;">
                        <div>
                            <h3 style="margin:0 0 4px;font-weight:700;font-size:16px;color:var(--ink);">{{ $campaign->title }}</h3>
                            <p style="margin:0;font-size:12px;color:var(--muted);">{{ $campaign->start_date }} – {{ $campaign->end_date }}</p>
                        </div>
                        <x-att-badge :status="$campaign->status" />
                    </div>

                    <div style="margin-bottom:14px;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--muted);margin-bottom:6px;">
                            <span>Yakunlangan: {{ $finalized }} / {{ $total }}</span>
                            <span style="font-weight:700;color:var(--ink);">{{ $pct }}%</span>
                        </div>
                        <div style="height:6px;background:var(--cream);border-radius:4px;overflow:hidden;">
                            <div style="height:100%;width:{{ $pct }}%;background:{{ $pct === 100 ? 'var(--green)' : 'var(--teal)' }};transition:width .5s;"></div>
                        </div>
                    </div>

                    <div style="display:flex;gap:8px;">
                        <x-att-button :href="route('reports.campaign', $campaign)" variant="primary" size="sm" class="flex-1">Batafsil</x-att-button>
                        <x-att-button :href="route('reports.campaign.csv', $campaign)" variant="secondary" size="sm">CSV</x-att-button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
