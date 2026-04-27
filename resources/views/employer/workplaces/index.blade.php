<x-app-layout>
    <x-slot name="header">{{ __('messages.workplaces') }}</x-slot>

    <x-page-header
        title="Ro‘yxatga olingan ish o‘rinlari"
        subtitle="Tashkilotingiz tarkibidagi attestatsiya qilinadigan ish o‘rinlari va ularning hozirgi holati."
        :crumbs="[
            ['label' => 'Bosh sahifa', 'url' => route('dashboard')],
            ['label' => 'Ish o‘rinlari'],
        ]"
    >
        <x-slot name="actions">
            <x-att-button :href="route('employer.workplaces.create')" variant="primary">+ Yangi qo‘shish</x-att-button>
        </x-slot>
    </x-page-header>

    @if ($workplaces->isEmpty())
        <x-empty-state
            icon="🏭"
            title="Hozircha ish o‘rinlari qo‘shilmagan"
            description="Tashkilotingiz tarkibidagi attestatsiyaga muhtoj barcha ish o‘rinlarini bu yerga qo‘shing."
        >
            <x-slot name="action">
                <x-att-button :href="route('employer.workplaces.create')" variant="primary">Birinchi ish o‘rnini qo‘shish</x-att-button>
            </x-slot>
        </x-empty-state>
    @else
        {{-- Desktop --}}
        <div class="att-card desktop-table" style="padding:0;">
            <table class="att-table">
                <thead>
                    <tr>
                        <th>Nomi / Kasb</th>
                        <th>Bo‘lim (sex)</th>
                        <th>OKZ kodi</th>
                        <th>Holati</th>
                        <th style="text-align:center;">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($workplaces as $wp)
                        <tr>
                            <td style="font-weight:600;color:var(--ink);">{{ $wp->name }}</td>
                            <td style="color:#555;">{{ $wp->department ?? '—' }}</td>
                            <td style="color:#555;font-family:ui-monospace,monospace;font-size:12.5px;">{{ $wp->code ?? '—' }}</td>
                            <td><x-att-badge :status="$wp->status" /></td>
                            <td style="text-align:center;">
                                <x-att-button :href="route('employer.workplaces.show', $wp)" variant="primary" size="sm">Batafsil</x-att-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="mobile-card-list">
            @foreach ($workplaces as $wp)
                <a href="{{ route('employer.workplaces.show', $wp) }}" class="mobile-app-card" style="display:block;text-decoration:none;color:inherit;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
                        <div style="font-weight:600;color:var(--ink);">{{ $wp->name }}</div>
                        <x-att-badge :status="$wp->status" />
                    </div>
                    <div style="font-size:12px;color:var(--muted);margin-top:6px;">{{ $wp->department ?? '—' }}</div>
                    @if ($wp->code)
                        <div style="font-family:ui-monospace,monospace;font-size:11.5px;color:var(--muted);margin-top:4px;">OKZ: {{ $wp->code }}</div>
                    @endif
                </a>
            @endforeach
        </div>

        {{ $workplaces->links() }}
    @endif
</x-app-layout>
