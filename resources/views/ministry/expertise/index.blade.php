<x-app-layout>
    <x-slot name="header">Davlat ekspertizasi yakuniy xulosasi</x-slot>

    <x-page-header
        title="Yakuniy xulosalar"
        subtitle="Institut tomonidan ma‘qullangan arizalarga 10 kun ichida xulosa bering."
        :crumbs="[
            ['label' => 'Boshqaruv paneli', 'url' => route('dashboard')],
            ['label' => 'Davlat ekspertizasi'],
        ]"
    />

    {{-- ─── Pending list ─── --}}
    <div class="att-card att-card-accent-gold" style="margin-bottom:18px;">
        <div class="att-card-header">
            <span class="att-card-title">Institut ma‘qullagan — kutilayotgan</span>
            @if(! $applications->isEmpty())
                <span class="nav-badge" style="margin-left:auto;font-size:12px;padding:3px 10px;">{{ $applications->count() }}</span>
            @endif
        </div>
        <div class="att-card-body" style="padding:0;">
            @if($applications->isEmpty())
                <div style="padding:32px;">
                    <x-empty-state
                        icon="✓"
                        title="Kutilayotgan ariza yo‘q"
                        description="Hozircha vazirlik ekspertizasiga yuborilgan yangi arizalar yo‘q."
                    />
                </div>
            @else
                <div class="desktop-table">
                    <table class="att-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Korxona</th>
                                <th>Laboratoriya</th>
                                <th>Institut sanasi</th>
                                <th style="text-align:center;">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $app)
                                <tr>
                                    <td class="text-muted" style="font-weight:600;">#{{ $app->id }}</td>
                                    <td style="font-weight:600;color:var(--ink);">{{ optional($app->organization)->name }}</td>
                                    <td>{{ optional($app->laboratory)->name }}</td>
                                    <td class="text-muted">{{ optional($app->institute_reviewed_at)->format('d.m.Y') }}</td>
                                    <td style="text-align:center;">
                                        <x-att-button :href="route('ministry.expertise.show', $app)" variant="primary" size="sm">
                                            Xulosa berish
                                        </x-att-button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mobile-card-list" style="padding:14px;">
                    @foreach($applications as $app)
                        <a href="{{ route('ministry.expertise.show', $app) }}" class="mobile-app-card border-gold" style="display:block;text-decoration:none;color:inherit;">
                            <div style="font-weight:700;color:var(--ink);font-size:14px;">{{ optional($app->organization)->name }}</div>
                            <div class="text-muted" style="font-size:12.5px;margin-top:4px;">#{{ $app->id }} · {{ optional($app->laboratory)->name }}</div>
                            <div class="text-muted" style="font-size:11.5px;margin-top:6px;">{{ optional($app->institute_reviewed_at)->format('d.m.Y') }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ─── History ─── --}}
    <div class="att-card">
        <div class="att-card-header">
            <span class="att-card-title">Yakunlangan arizalar (Tarix)</span>
        </div>
        <div class="att-card-body" style="padding:0;">
            @if($history->isEmpty())
                <div style="padding:32px;">
                    <x-empty-state icon="▤" title="Tarix bo‘sh" description="Hali yakunlangan arizalar mavjud emas." />
                </div>
            @else
                <table class="att-table desktop-table">
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
                                <td class="text-muted" style="font-weight:600;">#{{ $app->id }}</td>
                                <td style="font-weight:600;color:var(--ink);">{{ optional($app->organization)->name }}</td>
                                <td style="text-align:center;">
                                    @if($app->ministry_status === 'approved')
                                        <x-att-badge status="finalized" label="Tasdiqlandi" />
                                    @else
                                        <x-att-badge status="rejected" label="Qaytarildi" />
                                    @endif
                                </td>
                                <td>
                                    @if($app->conclusion_number)
                                        <span style="font-family:ui-monospace,monospace;font-size:13px;font-weight:700;color:var(--green);">{{ $app->conclusion_number }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ optional($app->ministry_reviewed_at)->format('d.m.Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mobile-card-list" style="padding:14px;">
                    @foreach($history as $app)
                        <div class="mobile-app-card {{ $app->ministry_status === 'approved' ? 'border-green' : 'border-red' }}">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
                                <div>
                                    <div style="font-weight:700;color:var(--ink);font-size:14px;">{{ optional($app->organization)->name }}</div>
                                    <div class="text-muted" style="font-size:12px;margin-top:3px;">#{{ $app->id }} · {{ optional($app->ministry_reviewed_at)->format('d.m.Y') }}</div>
                                </div>
                                @if($app->ministry_status === 'approved')
                                    <x-att-badge status="finalized" label="Tasdiqlandi" />
                                @else
                                    <x-att-badge status="rejected" label="Qaytarildi" />
                                @endif
                            </div>
                            @if($app->conclusion_number)
                                <div style="margin-top:8px;font-family:ui-monospace,monospace;font-size:12px;font-weight:700;color:var(--green);">{{ $app->conclusion_number }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
