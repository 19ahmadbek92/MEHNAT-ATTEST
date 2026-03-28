<x-app-layout>
    <x-slot name="header">📋 Ekspertiza #{{ $expertise->id }} — Yakuniy Xulosa</x-slot>

    <div style="display: flex; gap: 12px; margin-bottom: 24px;">
        <a href="{{ route('ministry.expertise.index') }}" class="btn-att btn-att-secondary btn-att-sm">← Ortga</a>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;" class="detail-grid-responsive">

        <div>
            <div class="att-card" style="margin-bottom: 20px;">
                <div class="att-card-header">
                    <span class="att-card-title">🏭 Buyurtmachi va Laboratoriya</span>
                </div>
                <div class="att-card-body">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="padding:10px 0; color:var(--muted); font-size:13px; width:40%; border-bottom:1px solid var(--cream);">Korxona</td>
                            <td style="padding:10px 0; font-weight:600; border-bottom:1px solid var(--cream);">{{ optional($expertise->organization)->name }}</td>
                        </tr>
                        <tr>
                            <td style="padding:10px 0; color:var(--muted); font-size:13px; border-bottom:1px solid var(--cream);">Laboratoriya</td>
                            <td style="padding:10px 0; font-weight:600; border-bottom:1px solid var(--cream);">{{ optional($expertise->laboratory)->name }}</td>
                        </tr>
                        <tr>
                            <td style="padding:10px 0; color:var(--muted); font-size:13px; border-bottom:1px solid var(--cream);">Institut xulosasi</td>
                            <td style="padding:10px 0; border-bottom:1px solid var(--cream);">
                                <span class="status-badge sb-finalized">✓ Ma'qullangan</span>
                                <span style="margin-left:8px; font-size:12px; color:var(--muted);">{{ optional($expertise->institute_reviewed_at)->format('d.m.Y H:i') }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="att-card">
                <div class="att-card-header">
                    <span class="att-card-title">📊 Ish o'rinlari natijalari</span>
                    @php
                        $appsArray = is_array($expertise->application_ids) ? $expertise->application_ids : json_decode($expertise->application_ids, true);
                        $applications = \App\Models\AttestationApplication::whereIn('id', $appsArray)->get();
                    @endphp
                </div>
                <div class="att-card-body" style="padding: 0;">
                    <table class="att-table">
                        <thead>
                            <tr>
                                <th>Ish o'rni</th>
                                <th style="text-align:center;">Umumiy Sinf</th>
                                <th style="text-align:center;">Imtiyoz</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $app)
                                <tr>
                                    <td style="font-weight:600;">{{ $app->workplace_name }}</td>
                                    <td style="text-align:center;"><span class="status-badge" style="background:var(--teal-light); color:var(--teal); font-weight:700;">Klass {{ optional($app->protocol)->overall_class ?? '—' }}</span></td>
                                    <td style="text-align:center;">
                                        @if(optional($app->protocol)->requires_benefits)
                                            <span class="status-badge sb-approved">Belgilangan</span>
                                        @else
                                            <span style="color:var(--muted); font-size:13px;">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            <div class="att-card" style="border-top: 3px solid var(--red);">
                <div class="att-card-header">
                    <span class="att-card-title" style="color:var(--red);">⚖️ Yakuniy Xulosa</span>
                </div>
                <div class="att-card-body">
                    @if($expertise->ministry_status == 'pending')
                        <form action="{{ route('ministry.expertise.process', $expertise) }}" method="POST">
                            @csrf
                            <div class="att-field" style="margin-bottom: 16px;">
                                <label>Amalni tanlang *</label>
                                <select name="action" id="action-select" required onchange="document.getElementById('comment-box').style.display = this.value === 'return' ? 'block' : 'none'; document.querySelector('[name=comment]').required = this.value === 'return';">
                                    <option value="">— Tanlang —</option>
                                    <option value="approve">✅ Tasdiqlash (QR-Kodli Xulosa Berish)</option>
                                    <option value="return">↩️ Rad etish / Qaytarish</option>
                                </select>
                            </div>

                            <div id="comment-box" style="display:none;" class="att-field" style="margin-bottom: 16px;">
                                <label>Rad etish asosi</label>
                                <textarea name="comment" rows="4" placeholder="Qaysi me'yoriy talablarni bajarmaganligini kiriting..."></textarea>
                            </div>

                            <button type="submit" class="btn-att btn-att-primary" style="width: 100%;">🏛 Tasdiqlash</button>
                        </form>
                    @else
                        <div style="text-align: center;">
                            <div style="font-size: 48px; margin-bottom: 12px;">
                                {{ $expertise->ministry_status == 'approved' ? '✅' : '↩️' }}
                            </div>
                            <div style="font-weight: 700; font-size: 16px; color: {{ $expertise->ministry_status == 'approved' ? 'var(--green)' : 'var(--red)' }}; margin-bottom: 6px;">
                                {{ $expertise->ministry_status == 'approved' ? 'Tasdiqlangan' : 'Rad etilgan' }}
                            </div>
                            <div style="font-size: 12px; color: var(--muted);">{{ optional($expertise->ministry_reviewed_at)->format('d.m.Y H:i') }}</div>

                            @if($expertise->conclusion_number)
                                <div style="margin-top: 20px; padding: 16px; background: var(--green-light); border-radius: 12px; border: 1px solid rgba(26,107,60,0.2);">
                                    <div style="font-size: 11px; color: var(--green); text-transform: uppercase; font-weight: 600; letter-spacing: 1px; margin-bottom: 6px;">Elektron Xulosa Raqami</div>
                                    <div style="font-family: monospace; font-size: 18px; font-weight: 700; color: var(--green);">{{ $expertise->conclusion_number }}</div>
                                    <div style="margin-top: 8px; font-size: 11px; color: var(--muted);">QR-kod orqali tekshirish mumkin</div>
                                </div>
                            @endif

                            @if($expertise->ministry_comment)
                                <div style="margin-top: 12px; padding: 12px; background: var(--red-light); border-radius: 8px; font-size: 13px; text-align: left;">
                                    {{ $expertise->ministry_comment }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) { .detail-grid-responsive { grid-template-columns: 1fr !important; } }
    </style>
</x-app-layout>
