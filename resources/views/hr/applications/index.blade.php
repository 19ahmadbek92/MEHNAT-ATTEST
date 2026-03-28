<x-app-layout>
    <x-slot name="header">📑 Davlat ekspertizasi: Arizalar</x-slot>

    {{-- Filter tabs --}}
    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 24px;">
        <a href="{{ route('hr.applications.index', ['status' => 'submitted']) }}"
           class="btn-att {{ $status === 'submitted' ? 'btn-att-primary' : 'btn-att-secondary' }} btn-att-sm">
            🆕 Yangi ({{ \App\Models\AttestationApplication::where('status', 'submitted')->count() }})
        </a>
        <a href="{{ route('hr.applications.index', ['status' => 'hr_approved']) }}"
           class="btn-att {{ $status === 'hr_approved' ? 'btn-att-primary' : 'btn-att-secondary' }} btn-att-sm">
            ⏳ Tasdiqlangan ({{ \App\Models\AttestationApplication::where('status', 'hr_approved')->count() }})
        </a>
        <a href="{{ route('hr.applications.index', ['status' => 'finalized']) }}"
           class="btn-att {{ $status === 'finalized' ? 'btn-att-primary' : 'btn-att-secondary' }} btn-att-sm">
            ✅ Yakunlangan ({{ \App\Models\AttestationApplication::where('status', 'finalized')->count() }})
        </a>
    </div>

    {{-- Desktop Table --}}
    <div class="att-card desktop-table">
        <table class="att-table">
            <thead>
                <tr>
                    <th>Tashkilot va Ish o'rni</th>
                    <th>Kampaniya</th>
                    <th>Ball / Klass</th>
                    <th style="text-align: center;">Amallar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr style="border-left: 4px solid {{ $app->status === 'submitted' ? 'var(--gold)' : ($app->status === 'finalized' ? 'var(--green)' : 'var(--teal)') }};">
                        <td>
                            <div style="font-weight: 600; font-size: 15px; color: var(--ink);">{{ $app->user?->name }}</div>
                            <div style="color: var(--teal); font-weight: 500;">{{ $app->workplace_name ?? $app->position }}</div>
                            <div style="font-size: 11px; color: var(--muted); text-transform: uppercase;">{{ $app->department ?? '—' }}</div>
                        </td>
                        <td style="color: #555;">{{ $app->campaign?->title }}</td>
                        <td>
                            @if($app->status === 'finalized')
                                <div style="font-weight: 700; color: var(--teal); text-transform: uppercase;">{{ $app->getWorkplaceClassLabel() }}</div>
                            @else
                                <div style="font-size: 13px; color: var(--muted); font-style: italic;">O'rtacha ball: {{ $app->final_score ?? '0.00' }}</div>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('hr.applications.show', $app) }}" class="btn-att btn-att-primary btn-att-sm">
                                🔍 Ko'rish
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 48px; color: var(--muted); font-style: italic;">
                            <div style="font-size: 36px; margin-bottom: 12px;">📭</div>
                            Arizalar mavjud emas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View --}}
    <div class="mobile-card-list">
        @forelse($applications as $app)
            <a href="{{ route('hr.applications.show', $app) }}" class="mobile-app-card" style="display: block; text-decoration: none; color: inherit; {{ $app->status === 'finalized' ? 'border-left-color: var(--green);' : ($app->status === 'submitted' ? 'border-left-color: var(--gold);' : '') }}">
                <div style="font-weight: 600; color: var(--ink); margin-bottom: 4px;">{{ $app->user?->name }}</div>
                <div style="color: var(--teal); font-size: 14px; font-weight: 500;">{{ $app->workplace_name ?? $app->position }}</div>
                <div style="font-size: 12px; color: var(--muted); margin-top: 6px;">{{ $app->campaign?->title }}</div>
                @if($app->status === 'finalized')
                    <div style="margin-top: 8px; font-weight: 700; color: var(--teal); font-size: 13px;">{{ $app->getWorkplaceClassLabel() }}</div>
                @endif
            </a>
        @empty
            <div style="text-align: center; padding: 48px 20px; color: var(--muted);">Arizalar mavjud emas.</div>
        @endforelse
    </div>

    @if($applications->hasPages())
        <div style="margin-top: 16px;">{{ $applications->links() }}</div>
    @endif
</x-app-layout>
