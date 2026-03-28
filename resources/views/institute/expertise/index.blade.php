<x-app-layout>
    <x-slot name="header">🏫 Dastlabki Baholash (Institut)</x-slot>

    @if(session('success'))
        <div style="margin-bottom: 20px; padding: 14px 18px; background: var(--green-light); color: var(--green); border-radius: 10px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; border: 1px solid rgba(26,107,60,0.2);">
            ✅ {{ session('success') }}
        </div>
    @endif

    <p style="color: var(--muted); font-size: 14px; margin: 0 0 24px; font-style: italic;">
        Kelib tushgan arizalarni 15 kun ichida dastlabki baholashdan o'tkazing va Vazirlik davlat ekspertizasiga uzating.
    </p>

    {{-- Yangi arizalar --}}
    <div class="att-card" style="margin-bottom: 20px; border-left: 4px solid var(--gold);">
        <div class="att-card-header">
            <span class="att-card-title">⏳ Kutilayotgan arizalar</span>
            @if(!$applications->isEmpty())
                <span class="nav-badge" style="font-size: 13px; padding: 4px 10px;">{{ $applications->count() }}</span>
            @endif
        </div>
        <div class="att-card-body" style="padding: 0;">
            @if($applications->isEmpty())
                <div style="text-align: center; padding: 48px; color: var(--muted);">
                    <div style="font-size: 36px; margin-bottom: 12px;">✅</div>
                    Kutilayotgan arizalar mavjud emas.
                </div>
            @else
                <table class="att-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Buyurtmachi korxona</th>
                            <th>Laboratoriya</th>
                            <th>Yuborilgan sana</th>
                            <th style="text-align:center;">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $app)
                            <tr>
                                <td style="font-weight:600; color:var(--muted);">#{{ $app->id }}</td>
                                <td style="font-weight:600; color:var(--ink);">{{ optional($app->organization)->name }}</td>
                                <td>{{ optional($app->laboratory)->name }}</td>
                                <td>{{ $app->created_at->format('d.m.Y H:i') }}</td>
                                <td style="text-align:center;">
                                    <a href="{{ route('institute.expertise.show', $app) }}" class="btn-att btn-att-primary btn-att-sm">
                                        🔍 Ko'rib chiqish
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Tarix --}}
    <div class="att-card">
        <div class="att-card-header">
            <span class="att-card-title">📁 Ko'rib chiqilgan arizalar (Tarix)</span>
        </div>
        <div class="att-card-body" style="padding: 0;">
            @if($history->isEmpty())
                <div style="text-align: center; padding: 32px; color: var(--muted); font-style: italic;">Tarix bo'sh.</div>
            @else
                <table class="att-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Korxona</th>
                            <th style="text-align:center;">Holat</th>
                            <th>Baholash sanasi</th>
                            <th style="text-align:center;">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $app)
                            <tr>
                                <td style="font-weight:600; color:var(--muted);">#{{ $app->id }}</td>
                                <td style="font-weight:600; color:var(--ink);">{{ optional($app->organization)->name }}</td>
                                <td style="text-align:center;">
                                    @if($app->institute_status == 'approved')
                                        <span class="status-badge sb-finalized">✓ Ma'qullangan</span>
                                    @else
                                        <span class="status-badge sb-rejected">Qaytarilgan</span>
                                    @endif
                                </td>
                                <td>{{ optional($app->institute_reviewed_at)->format('d.m.Y') }}</td>
                                <td style="text-align:center;">
                                    <a href="{{ route('institute.expertise.show', $app) }}" class="btn-att btn-att-secondary btn-att-sm">Batafsil</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>
