<x-app-layout>
    <x-slot name="header">Institut: Dastlabki baholash</x-slot>

    <x-page-header
        title="Dastlabki baholash (Institut)"
        subtitle="Kelib tushgan arizalarni 15 kun ichida dastlabki baholashdan o‘tkazing va Vazirlik davlat ekspertizasiga uzating."
        :crumbs="[
            ['label' => 'Bosh sahifa', 'url' => route('dashboard')],
            ['label' => 'Institut'],
        ]"
    />

    {{-- Yangi arizalar --}}
    <div class="att-card att-card-accent-gold" style="margin-bottom:18px;">
        <div class="att-card-header">
            <span class="att-card-title">Kutilayotgan arizalar</span>
            @if (! $applications->isEmpty())
                <span class="nav-badge" style="font-size:13px;padding:4px 10px;">{{ $applications->count() }}</span>
            @endif
        </div>
        <div class="att-card-body" style="padding:0;">
            @if ($applications->isEmpty())
                <x-empty-state
                    icon="✅"
                    title="Yangi ariza yo‘q"
                    description="Hozircha dastlabki baholashga kelgan arizalar mavjud emas."
                />
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
                        @foreach ($applications as $app)
                            <tr>
                                <td style="font-weight:600;color:var(--muted);">#{{ $app->id }}</td>
                                <td style="font-weight:600;color:var(--ink);">{{ optional($app->organization)->name }}</td>
                                <td>{{ optional($app->laboratory)->name ?? '—' }}</td>
                                <td>{{ $app->created_at->format('d.m.Y H:i') }}</td>
                                <td style="text-align:center;">
                                    <x-att-button :href="route('institute.expertise.show', $app)" variant="primary" size="sm">Ko‘rib chiqish</x-att-button>
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
            <span class="att-card-title">Ko‘rib chiqilgan arizalar (tarix)</span>
        </div>
        <div class="att-card-body" style="padding:0;">
            @if ($history->isEmpty())
                <x-empty-state
                    icon="📁"
                    title="Tarix bo‘sh"
                    description="Sizning institut tomonidan ko‘rib chiqilgan arizalar bu yerda ko‘rsatiladi."
                />
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
                        @foreach ($history as $app)
                            <tr>
                                <td style="font-weight:600;color:var(--muted);">#{{ $app->id }}</td>
                                <td style="font-weight:600;color:var(--ink);">{{ optional($app->organization)->name }}</td>
                                <td style="text-align:center;">
                                    @if ($app->institute_status === 'approved')
                                        <span class="status-badge sb-finalized">Ma’qullangan</span>
                                    @else
                                        <span class="status-badge sb-rejected">Qaytarilgan</span>
                                    @endif
                                </td>
                                <td>{{ optional($app->institute_reviewed_at)->format('d.m.Y') ?? '—' }}</td>
                                <td style="text-align:center;">
                                    <x-att-button :href="route('institute.expertise.show', $app)" variant="secondary" size="sm">Batafsil</x-att-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>
