<x-app-layout>
    <x-slot name="header">📋 Kampaniya tafsilotlari</x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('status'))
                <div style="margin-bottom: 20px; padding: 14px 18px; background: var(--green-light, #dcfce7); color: var(--green, #166534); border-radius: 10px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                    ✅ {{ session('status') }}
                </div>
            @endif

            {{-- Kampaniya ma'lumotlari --}}
            <div class="att-card" style="margin-bottom: 24px;">
                <div class="att-card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="att-card-title">{{ $campaign->title }}</span>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        @if($campaign->status === 'open')
                            <span class="status-badge sb-finalized">🟢 Faol</span>
                        @elseif($campaign->status === 'draft')
                            <span class="status-badge sb-submitted">📝 Qoralama</span>
                        @else
                            <span class="status-badge sb-rejected">🔒 Yopilgan</span>
                        @endif
                        <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn-att btn-att-secondary" style="font-size: 13px;">
                            ✏️ Tahrirlash
                        </a>
                    </div>
                </div>
                <div class="att-card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;" class="campaign-grid">
                        <div>
                            <div style="font-size: 12px; color: var(--muted, #94a3b8); text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Boshlanish sanasi</div>
                            <div style="font-size: 16px; font-weight: 700; color: var(--ink, #1e293b);">{{ \Carbon\Carbon::parse($campaign->start_date)->format('d.m.Y') }}</div>
                        </div>
                        <div>
                            <div style="font-size: 12px; color: var(--muted, #94a3b8); text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Tugash sanasi</div>
                            <div style="font-size: 16px; font-weight: 700; color: var(--ink, #1e293b);">{{ \Carbon\Carbon::parse($campaign->end_date)->format('d.m.Y') }}</div>
                        </div>
                        <div>
                            <div style="font-size: 12px; color: var(--muted, #94a3b8); text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Jami arizalar</div>
                            <div style="font-size: 16px; font-weight: 700; color: var(--ink, #1e293b);">{{ $campaign->applications->count() }}</div>
                        </div>
                    </div>
                    @if($campaign->description)
                        <div style="padding: 12px 16px; background: var(--bg-card, #f8fafc); border-radius: 8px; font-size: 14px; color: var(--muted, #64748b); line-height: 1.6;">
                            {{ $campaign->description }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Arizalar ro'yxati --}}
            <div class="att-card">
                <div class="att-card-header">
                    <span class="att-card-title">📋 Kampaniya arizalari</span>
                </div>
                <div class="att-card-body">
                    @if($campaign->applications->isEmpty())
                        <div style="text-align: center; padding: 40px; color: var(--muted, #94a3b8);">
                            <div style="font-size: 40px; margin-bottom: 12px;">📭</div>
                            <div style="font-weight: 600;">Hali ariza topshirilmagan</div>
                            <div style="font-size: 13px; margin-top: 4px;">Ish beruvchilar ariza topshirgach, ular shu yerda paydo bo'ladi.</div>
                        </div>
                    @else
                        <table class="att-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ish o'rni</th>
                                    <th>Bo'lim</th>
                                    <th>Holati</th>
                                    <th>Sana</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($campaign->applications as $i => $app)
                                    <tr>
                                        <td style="font-weight: 600; color: var(--muted, #94a3b8);">{{ $i + 1 }}</td>
                                        <td style="font-weight: 600; color: var(--ink, #1e293b);">{{ $app->workplace_name }}</td>
                                        <td>{{ $app->department ?? '—' }}</td>
                                        <td>
                                            @switch($app->status)
                                                @case('submitted')
                                                    <span class="status-badge sb-submitted">Kutayotgan</span>
                                                    @break
                                                @case('hr_approved')
                                                    <span class="status-badge" style="background: var(--blue-light, #dbeafe); color: var(--blue, #2563eb);">HR tasdiqladi</span>
                                                    @break
                                                @case('commission_reviewed')
                                                    <span class="status-badge" style="background: var(--teal-light, #ccfbf1); color: var(--teal, #0d9488);">Tekshirildi</span>
                                                    @break
                                                @case('finalized')
                                                    <span class="status-badge sb-finalized">Yakunlangan</span>
                                                    @break
                                                @default
                                                    <span class="status-badge">{{ ucfirst($app->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td style="font-size: 13px; color: var(--muted, #94a3b8);">{{ $app->created_at->format('d.m.Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div style="margin-top: 20px;">
                <a href="{{ route('admin.campaigns.index') }}" class="btn-att btn-att-secondary">← Kampaniyalar ro'yxati</a>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 640px) {
            .campaign-grid { grid-template-columns: 1fr !important; }
        }
    </style>
</x-app-layout>
