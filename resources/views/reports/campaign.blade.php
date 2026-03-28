<x-app-layout>
    <x-slot name="header">📊 Kampaniya hisoboti: {{ $campaign->title }}</x-slot>

    {{-- Kampaniya info --}}
    <div class="att-card" style="margin-bottom: 24px;">
        <div style="background: var(--ink); padding: 20px 24px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <h3 style="font-family: 'DM Serif Display', serif; font-size: 20px; color: white; margin: 0 0 4px;">{{ $campaign->title }}</h3>
                <p style="color: rgba(255,255,255,0.4); font-size: 13px; margin: 0;">{{ $campaign->start_date }} – {{ $campaign->end_date }}</p>
            </div>
            <a href="{{ route('reports.campaign.csv', $campaign) }}" class="btn-att btn-att-primary btn-att-sm" style="background: #4ecdc4; color: var(--ink);">📥 CSV Eksport</a>
        </div>

        @php
            $total = $applications->total();
            $finalized = $campaign->applications()->where('status', 'finalized')->count();
        @endphp

        <div class="att-card-body">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px;">
                <div style="text-align: center; background: var(--cream); border-radius: 10px; padding: 16px;">
                    <div style="font-family: 'DM Serif Display', serif; font-size: 28px; color: var(--teal);">{{ $total }}</div>
                    <div style="font-size: 11px; color: var(--muted); text-transform: uppercase;">Jami arizalar</div>
                </div>
                <div style="text-align: center; background: var(--cream); border-radius: 10px; padding: 16px;">
                    <div style="font-family: 'DM Serif Display', serif; font-size: 28px; color: var(--green);">{{ $finalized }}</div>
                    <div style="font-size: 11px; color: var(--muted); text-transform: uppercase;">Yakunlangan</div>
                </div>
                <div style="text-align: center; background: var(--cream); border-radius: 10px; padding: 16px;">
                    <div style="font-family: 'DM Serif Display', serif; font-size: 28px; color: var(--gold);">{{ $total - $finalized }}</div>
                    <div style="font-size: 11px; color: var(--muted); text-transform: uppercase;">Jarayonda</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Desktop Table --}}
    <div class="att-card desktop-table">
        <table class="att-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tashkilot</th>
                    <th>Ish o'rni</th>
                    <th>Bo'lim</th>
                    <th>Status</th>
                    <th>Ball</th>
                    <th>Klass</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr style="border-left: 4px solid {{ $app->status === 'finalized' ? 'var(--green)' : ($app->status === 'hr_rejected' ? 'var(--red)' : 'var(--gold)') }};">
                        <td style="font-weight: 600; color: var(--muted);">{{ $app->id }}</td>
                        <td style="font-weight: 600; color: var(--ink);">{{ $app->user?->name }}</td>
                        <td style="font-weight: 500; color: var(--teal);">{{ $app->workplace_name ?? $app->position }}</td>
                        <td style="color: #555;">{{ $app->department ?? '—' }}</td>
                        <td>
                            <span class="status-badge
                                @switch($app->status)
                                    @case('submitted') sb-submitted @break
                                    @case('hr_approved') sb-approved @break
                                    @case('hr_rejected') sb-rejected @break
                                    @case('finalized') sb-finalized @break
                                @endswitch">
                                @switch($app->status)
                                    @case('submitted') 🆕 Yangi @break
                                    @case('hr_approved') ⏳ Tekshiruvda @break
                                    @case('hr_rejected') ❌ Rad @break
                                    @case('finalized') ✅ Yakunlangan @break
                                @endswitch
                            </span>
                        </td>
                        <td style="font-weight: 700; color: var(--ink);">{{ $app->final_score ?? '—' }}</td>
                        <td style="font-weight: 700; color: var(--teal); text-transform: uppercase;">{{ $app->status === 'finalized' ? $app->getWorkplaceClassLabel() : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 48px; color: var(--muted); font-style: italic;">Ma'lumot yo'q.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View --}}
    <div class="mobile-card-list">
        @forelse($applications as $app)
            <div class="mobile-app-card {{ $app->status === 'finalized' ? 'border-green' : ($app->status === 'hr_rejected' ? 'border-red' : 'border-gold') }}">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <div>
                        <div style="font-weight: 600; color: var(--ink);">{{ $app->user?->name }}</div>
                        <div style="font-size: 13px; color: var(--teal);">{{ $app->workplace_name ?? $app->position }}</div>
                    </div>
                    <span class="status-badge
                        @switch($app->status)
                            @case('submitted') sb-submitted @break
                            @case('hr_approved') sb-approved @break
                            @case('hr_rejected') sb-rejected @break
                            @case('finalized') sb-finalized @break
                        @endswitch">
                        {{ ucfirst($app->status) }}
                    </span>
                </div>
                @if($app->status === 'finalized')
                    <div style="font-size: 13px; font-weight: 700; color: var(--teal);">{{ $app->getWorkplaceClassLabel() }} · Ball: {{ $app->final_score ?? '—' }}</div>
                @endif
            </div>
        @empty
            <div style="text-align: center; padding: 48px 20px; color: var(--muted);">Ma'lumot yo'q.</div>
        @endforelse
    </div>

    @if($applications->hasPages())
        <div style="margin-top: 16px;">{{ $applications->links() }}</div>
    @endif

    <div style="margin-top: 20px;">
        <a href="{{ route('reports.index') }}" class="btn-att btn-att-secondary">← Hisobotlarga qaytish</a>
    </div>
</x-app-layout>
