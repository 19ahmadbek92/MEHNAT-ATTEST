<x-app-layout>
    <x-slot name="header">Laboratoriya: O‘lchovlar</x-slot>

    <x-page-header
        title="O‘lchov o‘tkazilishi kerak bo‘lgan ish o‘rinlari"
        subtitle="Sizning laboratoriyangizga rasmiylashtirilgan yoki umumiy bazadagi kutilayotgan ish o‘rinlari."
        :crumbs="[
            ['label' => 'Bosh sahifa', 'url' => route('dashboard')],
            ['label' => 'Laboratoriya'],
        ]"
    />

    @if ($workplaces->isEmpty())
        <x-empty-state
            icon="🧪"
            title="Kutilayotgan o‘lchov yo‘q"
            description="Hozircha laboratoriya nomiga rasmiylashtirilgan yoki kutilayotgan ish o‘rinlari mavjud emas."
        />
    @else
        {{-- Desktop --}}
        <div class="att-card desktop-table" style="padding:0;">
            <table class="att-table">
                <thead>
                    <tr>
                        <th>Korxona</th>
                        <th>Kasb / lavozim</th>
                        <th>Sex moduli</th>
                        <th>Holat</th>
                        <th style="text-align:center;">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($workplaces as $wp)
                        <tr>
                            <td style="font-weight:600;color:var(--ink);">{{ $wp->organization->name ?? 'Nomaʼlum' }}</td>
                            <td style="font-weight:500;color:#555;">{{ $wp->name }}</td>
                            <td style="color:#555;">{{ $wp->department ?? '—' }}</td>
                            <td><x-att-badge :status="$wp->status" /></td>
                            <td style="text-align:center;">
                                @if ($wp->status === 'pending')
                                    <x-att-button :href="route('laboratory.measurements.create', $wp)" variant="primary" size="sm">O‘lchov kiritish</x-att-button>
                                @else
                                    <span style="color:var(--muted);font-size:12px;font-style:italic;">Yakunlangan</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="mobile-card-list">
            @foreach ($workplaces as $wp)
                <div class="mobile-app-card">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
                        <div>
                            <div style="font-weight:600;color:var(--ink);">{{ $wp->organization->name ?? 'Nomaʼlum' }}</div>
                            <div style="font-size:13px;color:var(--teal);font-weight:500;">{{ $wp->name }}</div>
                            <div style="font-size:12px;color:var(--muted);">{{ $wp->department ?? '—' }}</div>
                        </div>
                        <x-att-badge :status="$wp->status" />
                    </div>
                    @if ($wp->status === 'pending')
                        <div style="margin-top:10px;">
                            <x-att-button :href="route('laboratory.measurements.create', $wp)" variant="primary" size="sm" class="w-full">O‘lchov kiritish</x-att-button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{ $workplaces->links() }}
    @endif
</x-app-layout>
