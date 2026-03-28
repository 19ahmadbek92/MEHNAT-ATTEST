<x-app-layout>
    <x-slot name="header">📋 O'lchov Protokollari</x-slot>

    @if(session('success'))
        <div style="margin-bottom: 20px; padding: 14px 18px; background: var(--green-light); color: var(--green); border-radius: 10px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; border: 1px solid rgba(26,107,60,0.2);">
            ✅ {{ session('success') }}
        </div>
    @endif

    <p style="color: var(--muted); font-size: 14px; margin: 0 0 24px; font-style: italic;">
        Tender g'olibi sifatida biriktirilgan korxonalarning ish o'rinlarini o'lchab, protokollarni to'ldiring.
    </p>

    {{-- Desktop Table --}}
    <div class="att-card desktop-table">
        <table class="att-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Buyurtmachi Korxona</th>
                    <th>Ish o'rni nomi</th>
                    <th style="text-align: center;">Protokol holati</th>
                    <th style="text-align: center;">Amallar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    @php
                        $hasProtocol = $app->protocols && $app->protocols->where('laboratory_id', auth()->user()->laboratory_id)->count() > 0;
                    @endphp
                    <tr style="border-left: 4px solid {{ $hasProtocol ? 'var(--green)' : 'var(--gold)' }};">
                        <td style="font-weight: 600; color: var(--muted);">#{{ $app->id }}</td>
                        <td style="font-weight: 600; color: var(--ink);">{{ $app->user->organization->name ?? '—' }}</td>
                        <td>
                            <div style="font-size: 15px; font-weight: 700; color: var(--ink);">{{ $app->workplace_name }}</div>
                            <div style="font-size: 12px; color: var(--muted);">{{ $app->department ?? '' }}</div>
                        </td>
                        <td style="text-align: center;">
                            @if($hasProtocol)
                                <span class="status-badge sb-finalized">✓ Kiritilgan</span>
                            @else
                                <span class="status-badge sb-approved">⏳ Kutilmoqda</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('laboratory.protocols.create', $app) }}" class="btn-att btn-att-primary btn-att-sm">
                                {{ $hasProtocol ? '✏️ Tahrirlash' : '📝 Protokol to\'ldirish' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 48px; color: var(--muted); font-style: italic;">
                            <div style="font-size: 36px; margin-bottom: 12px;">⏳</div>
                            Biriktirilgan ish o'rinlari mavjud emas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="mobile-card-list">
        @forelse($applications as $app)
        @php $hasProtocol = $app->protocols && $app->protocols->where('laboratory_id', auth()->user()->laboratory_id)->count() > 0; @endphp
            <div class="mobile-app-card {{ $hasProtocol ? 'border-green' : 'border-gold' }}">
                <div style="margin-bottom: 10px;">
                    <div style="font-weight: 700; font-size: 15px; color: var(--ink);">{{ $app->workplace_name }}</div>
                    <div style="font-size: 12px; color: var(--muted);">{{ $app->user->organization->name ?? '' }}</div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    @if($hasProtocol)
                        <span class="status-badge sb-finalized">✓ Kiritilgan</span>
                    @else
                        <span class="status-badge sb-approved">⏳ Kutilmoqda</span>
                    @endif
                    <a href="{{ route('laboratory.protocols.create', $app) }}" class="btn-att btn-att-primary btn-att-sm">📝 Protokol</a>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 48px 20px; color: var(--muted);">
                <div style="font-size: 36px; margin-bottom: 12px;">⏳</div>
                Biriktirilgan ish o'rinlari mavjud emas.
            </div>
        @endforelse
    </div>
</x-app-layout>
