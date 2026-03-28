<x-app-layout>
    <x-slot name="header">📊 Hisobotlar</x-slot>

    <p style="color: var(--muted); font-size: 14px; margin: 0 0 24px;">Attestatsiya kampaniyalari bo'yicha natijalar va hisobotlar.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px;">
        @forelse($campaigns as $campaign)
            @php
                $total = $campaign->applications->count();
                $finalized = $campaign->applications->where('status', 'finalized')->count();
                $pct = $total > 0 ? round($finalized / $total * 100) : 0;
            @endphp
            <div class="att-card" style="transition: all 0.2s; cursor: pointer;" onmouseenter="this.style.boxShadow='var(--shadow-lg)';this.style.transform='translateY(-2px)'" onmouseleave="this.style.boxShadow='';this.style.transform=''">
                <div style="border-top: 3px solid {{ $pct === 100 ? 'var(--green)' : ($pct > 50 ? 'var(--gold)' : 'var(--teal)') }}; padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                        <div>
                            <h3 style="font-weight: 700; font-size: 16px; color: var(--ink); margin: 0 0 4px;">{{ $campaign->title }}</h3>
                            <p style="font-size: 12px; color: var(--muted); margin: 0;">{{ $campaign->start_date }} – {{ $campaign->end_date }}</p>
                        </div>
                        <span class="status-badge {{ $campaign->status === 'open' ? 'sb-approved' : ($campaign->status === 'closed' ? 'sb-finalized' : 'sb-submitted') }}">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>

                    {{-- Progress bar --}}
                    <div style="margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--muted); margin-bottom: 6px;">
                            <span>Yakunlangan: {{ $finalized }}/{{ $total }}</span>
                            <span style="font-weight: 700;">{{ $pct }}%</span>
                        </div>
                        <div style="height: 6px; background: var(--cream); border-radius: 4px; overflow: hidden;">
                            <div style="height: 100%; border-radius: 4px; width: {{ $pct }}%; background: {{ $pct === 100 ? 'var(--green)' : 'var(--teal)' }}; transition: width 0.5s;"></div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('reports.campaign', $campaign) }}" class="btn-att btn-att-primary btn-att-sm" style="flex: 1; justify-content: center;">📊 Batafsil</a>
                        <a href="{{ route('reports.campaign.csv', $campaign) }}" class="btn-att btn-att-secondary btn-att-sm">📥 CSV</a>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: var(--muted);">
                <div style="font-size: 48px; margin-bottom: 12px;">📊</div>
                <p style="font-size: 15px;">Hozircha kampaniyalar mavjud emas.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
