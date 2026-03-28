<x-app-layout>
    <x-slot name="header">📋 Attestatsiya kampaniyalari</x-slot>

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <p style="color: var(--muted); font-size: 14px; margin: 0;">Barcha attestatsiya kampaniyalari va ularning holati.</p>
        <a href="{{ route('admin.campaigns.create') }}" class="btn-att btn-att-primary">+ Yangi kampaniya</a>
    </div>

    {{-- Desktop Table --}}
    <div class="att-card desktop-table">
        <table class="att-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nomi</th>
                    <th>Muddat</th>
                    <th>Holati</th>
                    <th style="text-align: right;">Amallar</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($campaigns as $campaign)
                    <tr>
                        <td style="font-weight: 600; color: var(--muted);">{{ $campaign->id }}</td>
                        <td style="font-weight: 600; color: var(--ink);">{{ $campaign->title }}</td>
                        <td style="color: #555; font-size: 13px;">{{ $campaign->start_date }} – {{ $campaign->end_date }}</td>
                        <td>
                            <span class="status-badge {{ $campaign->status === 'open' ? 'sb-approved' : ($campaign->status === 'closed' ? 'sb-finalized' : 'sb-submitted') }}">
                                @switch($campaign->status)
                                    @case('draft') 📝 Draft @break
                                    @case('open') ✅ Ochiq @break
                                    @case('closed') 🔒 Yopilgan @break
                                @endswitch
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn-att btn-att-secondary btn-att-sm">✏️ Tahrirlash</a>
                                <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Haqiqatan ham o\'chirilsinmi?')" class="btn-att btn-att-danger btn-att-sm">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 48px; color: var(--muted); font-style: italic;">
                            <div style="font-size: 36px; margin-bottom: 12px;">📭</div>
                            Hozircha kampaniyalar yo'q.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View --}}
    <div class="mobile-card-list">
        @forelse ($campaigns as $campaign)
            <div class="mobile-app-card {{ $campaign->status === 'open' ? 'border-green' : ($campaign->status === 'closed' ? 'border-red' : '') }}">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <div style="font-weight: 600; font-size: 15px; color: var(--ink);">{{ $campaign->title }}</div>
                    <span class="status-badge {{ $campaign->status === 'open' ? 'sb-approved' : ($campaign->status === 'closed' ? 'sb-finalized' : 'sb-submitted') }}">
                        {{ ucfirst($campaign->status) }}
                    </span>
                </div>
                <div style="font-size: 12px; color: var(--muted); margin-bottom: 12px;">{{ $campaign->start_date }} – {{ $campaign->end_date }}</div>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn-att btn-att-secondary btn-att-sm" style="flex: 1; justify-content: center;">✏️ Tahrirlash</a>
                    <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('O\'chirilsinmi?')" class="btn-att btn-att-danger btn-att-sm">🗑️</button>
                    </form>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 48px 20px; color: var(--muted);">Kampaniyalar yo'q.</div>
        @endforelse
    </div>

    <div style="margin-top: 16px;">{{ $campaigns->links() }}</div>
</x-app-layout>
