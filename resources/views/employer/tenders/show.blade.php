<x-app-layout>
    <x-slot name="header">📋 Tender #{{ $tender->id }}</x-slot>

    <div style="display: flex; gap: 12px; margin-bottom: 24px;">
        <a href="{{ route('employer.tenders.index') }}" class="btn-att btn-att-secondary btn-att-sm">← Ortga</a>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;" class="detail-grid-responsive">

        <div>
            <div class="att-card" style="margin-bottom: 20px;">
                <div class="att-card-header">
                    <span class="att-card-title">🏭 Shartnoma ma'lumotlari</span>
                    @if($tender->status == 'open')
                        <span class="status-badge sb-submitted">Ochiq</span>
                    @elseif($tender->status == 'awarded')
                        <span class="status-badge sb-finalized">Kelishilgan</span>
                    @else
                        <span class="status-badge" style="background: var(--cream); color: var(--muted);">Tugallangan</span>
                    @endif
                </div>
                <div class="att-card-body">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 10px 0; color: var(--muted); font-size: 13px; width: 40%; border-bottom: 1px solid var(--cream);">Buyurtmachi korxona</td>
                            <td style="padding: 10px 0; font-weight: 600; border-bottom: 1px solid var(--cream);">
                                {{ $tender->organization->name }}
                                <div style="font-size: 12px; color: var(--muted); font-weight: 400;">STIR: {{ $tender->organization->stir_inn }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: var(--muted); font-size: 13px; border-bottom: 1px solid var(--cream);">Laboratoriya</td>
                            <td style="padding: 10px 0; font-weight: 600; border-bottom: 1px solid var(--cream);">
                                {{ $tender->laboratory ? $tender->laboratory->name : '—' }}
                                @if($tender->laboratory)
                                    <div style="font-size: 12px; color: var(--muted); font-weight: 400;">Akkreditatsiya: {{ $tender->laboratory->accreditation_certificate_number }}</div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: var(--muted); font-size: 13px; border-bottom: 1px solid var(--cream);">Boshlanish</td>
                            <td style="padding: 10px 0; border-bottom: 1px solid var(--cream);">{{ $tender->start_date->format('d.m.Y') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: var(--muted); font-size: 13px;">Tugash</td>
                            <td style="padding: 10px 0;">{{ $tender->end_date->format('d.m.Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($tender->contract_details)
            <div class="att-card">
                <div class="att-card-header">
                    <span class="att-card-title">📝 Shartnoma detallari</span>
                </div>
                <div class="att-card-body">
                    <p style="color: var(--ink); font-size: 14px; line-height: 1.7; margin: 0;">{{ $tender->contract_details }}</p>
                </div>
            </div>
            @endif
        </div>

        <div>
            <div class="att-card" style="border-top: 3px solid var(--teal);">
                <div class="att-card-header">
                    <span class="att-card-title">📊 Holat</span>
                </div>
                <div class="att-card-body" style="text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 12px;">
                        @if($tender->status == 'open') 🔓
                        @elseif($tender->status == 'awarded') 🤝
                        @else ✅ @endif
                    </div>
                    <div style="font-size: 16px; font-weight: 700; color: var(--ink); margin-bottom: 6px;">
                        @if($tender->status == 'open') Tender ochiq
                        @elseif($tender->status == 'awarded') Laboratoriya bilan kelishilgan
                        @else Tugallangan @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        @media (max-width: 768px) {
            .detail-grid-responsive { grid-template-columns: 1fr !important; }
        }
    </style>
</x-app-layout>
