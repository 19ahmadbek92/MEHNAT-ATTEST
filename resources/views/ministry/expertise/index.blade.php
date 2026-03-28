<x-app-layout>
    <x-slot name="header">🏛 Davlat Ekspertizasi Yakuniy Xulosasi (Vazirlik)</x-slot>

    @if(session('success'))
        <div style="margin-bottom: 20px; padding: 14px 18px; background: var(--green-light); color: var(--green); border-radius: 10px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; border: 1px solid rgba(26,107,60,0.2);">
            ✅ {{ session('success') }}
        </div>
    @endif

    <p style="color: var(--muted); font-size: 14px; margin: 0 0 24px; font-style: italic;">
        Institut tomonidan ma'qullangan arizalarga 10 kun ichida yakuniy xulosa bering.
    </p>

    <div class="att-card" style="margin-bottom: 20px; border-left: 4px solid var(--gold);">
        <div class="att-card-header">
            <span class="att-card-title">⏳ Institut ma'qullagan — Kutilayotgan</span>
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
                            <th>Institut tomonidan</th>
                            <th style="text-align:center;">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $app)
                            <tr>
                                <td style="font-weight:600; color:var(--muted);">#{{ $app->id }}</td>
                                <td style="font-weight:600; color:var(--ink);">{{ optional($app->organization)->name }}</td>
                                <td>{{ optional($app->laboratory)->name }}</td>
                                <td>{{ optional($app->institute_reviewed_at)->format('d.m.Y') }}</td>
                                <td style="text-align:center;">
                                    <a href="{{ route('ministry.expertise.show', $app) }}" class="btn-att btn-att-primary btn-att-sm">
                                        📝 Xulosa berish
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="att-card">
        <div class="att-card-header">
            <span class="att-card-title">📁 Yakunlangan arizalar (Tarix)</span>
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
                            <th>Xulosa raqami</th>
                            <th>Sana</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $app)
                            <tr>
                                <td style="font-weight:600; color:var(--muted);">#{{ $app->id }}</td>
                                <td style="font-weight:600; color:var(--ink);">{{ optional($app->organization)->name }}</td>
                                <td style="text-align:center;">
                                    @if($app->ministry_status == 'approved')
                                        <span class="status-badge sb-finalized">✓ Tasdiqlangan</span>
                                    @else
                                        <span class="status-badge sb-rejected">Qaytarilgan</span>
                                    @endif
                                </td>
                                <td>
                                    @if($app->conclusion_number)
                                        <span style="font-family: monospace; font-size: 13px; font-weight: 700; color: var(--green);">{{ $app->conclusion_number }}</span>
                                    @else
                                        <span style="color: var(--muted);">—</span>
                                    @endif
                                </td>
                                <td>{{ optional($app->ministry_reviewed_at)->format('d.m.Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>
