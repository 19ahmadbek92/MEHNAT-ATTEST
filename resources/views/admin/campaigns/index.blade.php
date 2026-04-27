<x-app-layout>
    <x-slot name="header">Attestatsiya kampaniyalari</x-slot>

    <x-page-header
        title="Attestatsiya kampaniyalari"
        subtitle="Tashkil etilgan attestatsiya kampaniyalari, ularning muddati va holati."
        :crumbs="[
            ['label' => 'Bosh sahifa', 'url' => route('dashboard')],
            ['label' => 'Admin paneli', 'url' => route('admin.dashboard')],
            ['label' => 'Kampaniyalar'],
        ]"
    >
        <x-slot name="actions">
            <x-att-button :href="route('admin.campaigns.create')" variant="primary">+ Yangi kampaniya</x-att-button>
        </x-slot>
    </x-page-header>

    @if ($campaigns->isEmpty())
        <x-empty-state
            icon="📋"
            title="Hozircha kampaniyalar yo‘q"
            description="Birinchi attestatsiya kampaniyasini yarating va tashkilotlarga arizalar topshirishlari uchun ochiq qiling."
        >
            <x-slot name="action">
                <x-att-button :href="route('admin.campaigns.create')" variant="primary">Birinchi kampaniya</x-att-button>
            </x-slot>
        </x-empty-state>
    @else
        {{-- Desktop --}}
        <div class="att-card desktop-table" style="padding:0;">
            <table class="att-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nomi</th>
                        <th>Muddat</th>
                        <th>Holati</th>
                        <th style="text-align:right;">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($campaigns as $campaign)
                        <tr>
                            <td style="font-weight:600;color:var(--muted);">{{ $campaign->id }}</td>
                            <td style="font-weight:600;color:var(--ink);">{{ $campaign->title }}</td>
                            <td style="color:#555;font-size:13px;">{{ $campaign->start_date }} – {{ $campaign->end_date }}</td>
                            <td><x-att-badge :status="$campaign->status" /></td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex;gap:6px;">
                                    <x-att-button :href="route('admin.campaigns.edit', $campaign)" variant="secondary" size="sm">Tahrirlash</x-att-button>
                                    <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <x-att-button type="submit" variant="danger" size="sm" onclick="return confirm('Haqiqatan ham o‘chirilsinmi?')">O‘chirish</x-att-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="mobile-card-list">
            @foreach ($campaigns as $campaign)
                <div class="mobile-app-card">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:10px;">
                        <div style="font-weight:600;color:var(--ink);">{{ $campaign->title }}</div>
                        <x-att-badge :status="$campaign->status" />
                    </div>
                    <div style="font-size:12px;color:var(--muted);margin-bottom:12px;">{{ $campaign->start_date }} – {{ $campaign->end_date }}</div>
                    <div style="display:flex;gap:8px;">
                        <x-att-button :href="route('admin.campaigns.edit', $campaign)" variant="secondary" size="sm" class="flex-1">Tahrirlash</x-att-button>
                        <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <x-att-button type="submit" variant="danger" size="sm" onclick="return confirm('O‘chirilsinmi?')">O‘chirish</x-att-button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $campaigns->links() }}
    @endif
</x-app-layout>
