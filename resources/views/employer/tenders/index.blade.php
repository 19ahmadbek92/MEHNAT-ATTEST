<x-app-layout>
    <x-slot name="header">🤝 Tender va Shartnomalar</x-slot>

    @if(session('success'))
        <div style="margin-bottom: 20px; padding: 14px 18px; background: var(--green-light); color: var(--green); border-radius: 10px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; border: 1px solid rgba(26,107,60,0.2);">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <p style="color: var(--muted); font-size: 14px; margin: 0; font-style: italic;">
            Attestatsiya o'tkazish uchun akkreditatsiyalangan laboratoriyanı tanlang va tender e'lon qiling.
        </p>
        <a href="{{ route('employer.tenders.create') }}" class="btn-att btn-att-primary">
            ＋ Yangi tender
        </a>
    </div>

    {{-- Desktop Table --}}
    <div class="att-card desktop-table">
        <table class="att-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Laboratoriya</th>
                    <th>Boshlanish</th>
                    <th>Tugash</th>
                    <th>Holati</th>
                    <th style="text-align: center;">Amallar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenders as $tender)
                    <tr>
                        <td style="font-weight: 600; color: var(--muted);">#{{ $tender->id }}</td>
                        <td style="font-weight: 600; color: var(--ink);">
                            {{ $tender->laboratory ? $tender->laboratory->name : '—' }}
                            @if($tender->laboratory)
                                <div style="font-size: 11px; color: var(--muted);">Akkr: {{ $tender->laboratory->accreditation_certificate_number }}</div>
                            @endif
                        </td>
                        <td>{{ $tender->start_date->format('d.m.Y') }}</td>
                        <td>{{ $tender->end_date->format('d.m.Y') }}</td>
                        <td>
                            @if($tender->status == 'open')
                                <span class="status-badge sb-submitted">Ochiq</span>
                            @elseif($tender->status == 'awarded')
                                <span class="status-badge sb-finalized">Kelishilgan</span>
                            @else
                                <span class="status-badge" style="background: var(--cream); color: var(--muted);">Tugallangan</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('employer.tenders.show', $tender) }}" class="btn-att btn-att-secondary btn-att-sm">
                                🔍 Batafsil
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 48px; color: var(--muted); font-style: italic;">
                            <div style="font-size: 36px; margin-bottom: 12px;">🤝</div>
                            Hali tenderlar mavjud emas. Yuqoridagi tugma orqali birinchi tenderni yarating.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View --}}
    <div class="mobile-card-list">
        @forelse($tenders as $tender)
            <div class="mobile-app-card border-gold">
                <div style="margin-bottom: 10px;">
                    <div style="font-weight: 600; font-size: 15px; color: var(--ink);">
                        {{ $tender->laboratory ? $tender->laboratory->name : 'Laboratoriya biriktirilmagan' }}
                    </div>
                    <div style="font-size: 12px; color: var(--muted);">
                        {{ $tender->start_date->format('d.m.Y') }} — {{ $tender->end_date->format('d.m.Y') }}
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    @if($tender->status == 'open')
                        <span class="status-badge sb-submitted">Ochiq</span>
                    @elseif($tender->status == 'awarded')
                        <span class="status-badge sb-finalized">Kelishilgan</span>
                    @else
                        <span class="status-badge" style="background: var(--cream); color: var(--muted);">Tugallangan</span>
                    @endif
                    <a href="{{ route('employer.tenders.show', $tender) }}" class="btn-att btn-att-secondary btn-att-sm">🔍 Batafsil</a>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 48px 20px; color: var(--muted);">
                <div style="font-size: 36px; margin-bottom: 12px;">🤝</div>
                Tenderlar mavjud emas.
            </div>
        @endforelse
    </div>
</x-app-layout>
