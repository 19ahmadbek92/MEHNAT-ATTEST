<x-app-layout>
    <x-slot name="header">🔍 Ekspertiza #{{ $expertise->id }} — Dastlabki Baholash</x-slot>

    <div style="display: flex; gap: 12px; margin-bottom: 24px;">
        <a href="{{ route('institute.expertise.index') }}" class="btn-att btn-att-secondary btn-att-sm">← Ortga</a>
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
                            <td style="padding:10px 0; font-weight:600; border-bottom:1px solid var(--cream);">
                                {{ optional($expertise->organization)->name }}
                                <div style="font-size:12px; color:var(--muted); font-weight:400;">STIR: {{ optional($expertise->organization)->stir_inn }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:10px 0; color:var(--muted); font-size:13px; border-bottom:1px solid var(--cream);">Laboratoriya</td>
                            <td style="padding:10px 0; font-weight:600; border-bottom:1px solid var(--cream);">
                                {{ optional($expertise->laboratory)->name }}
                                <div style="font-size:12px; color:var(--muted); font-weight:400;">Akkreditatsiya: {{ optional($expertise->laboratory)->accreditation_certificate_number }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:10px 0; color:var(--muted); font-size:13px;">Yuborilgan sana</td>
                            <td style="padding:10px 0;">{{ $expertise->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="att-card">
                <div class="att-card-header">
                    <span class="att-card-title">📋 Ilova qilingan ish o'rinlari</span>
                    @php
                        $appsArray = is_array($expertise->application_ids) ? $expertise->application_ids : json_decode($expertise->application_ids, true);
                        $applications = \App\Models\AttestationApplication::whereIn('id', $appsArray)->get();
                    @endphp
                    <span style="font-size: 12px; color: var(--muted);">{{ $applications->count() }} ta</span>
                </div>
                <div class="att-card-body" style="padding: 0;">
                    <table class="att-table">
                        <thead>
                            <tr>
                                <th>Ish o'rni nomi</th>
                                <th style="text-align:center;">Og'irlik</th>
                                <th style="text-align:center;">Tig'izlik</th>
                                <th style="text-align:center;">Umumiy Sinf</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $app)
                                <tr>
                                    <td style="font-weight:600;">{{ $app->workplace_name }}</td>
                                    <td style="text-align:center;"><span class="status-badge" style="background:var(--cream); color:var(--ink);">{{ optional($app->protocol)->work_severity_class ?? '—' }}</span></td>
                                    <td style="text-align:center;"><span class="status-badge" style="background:var(--cream); color:var(--ink);">{{ optional($app->protocol)->work_intensity_class ?? '—' }}</span></td>
                                    <td style="text-align:center;"><span class="status-badge" style="background:var(--teal-light); color:var(--teal); font-weight:700;">Klass {{ optional($app->protocol)->overall_class ?? '—' }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            <div class="att-card" style="border-top: 3px solid var(--gold);">
                <div class="att-card-header">
                    <span class="att-card-title">⚖️ Qaror qabul qilish</span>
                </div>
                <div class="att-card-body">
                    @if($expertise->institute_status == 'pending')
                        <form action="{{ route('institute.expertise.process', $expertise) }}" method="POST">
                            @csrf
                            <div class="att-field" style="margin-bottom: 16px;">
                                <label>Amalni tanlang *</label>
                                <select name="action" id="action-select" required onchange="document.getElementById('comment-box').style.display = this.value === 'return' ? 'block' : 'none'; document.querySelector('[name=comment]').required = this.value === 'return';">
                                    <option value="">— Tanlang —</option>
                                    <option value="approve">✅ Ma'qullash (Davlat ekspertizasiga uzatish)</option>
                                    <option value="return">↩️ Kamchiliklar bilan qaytarish</option>
                                </select>
                            </div>

                            <div id="comment-box" style="display:none;" class="att-field" style="margin-bottom: 16px;">
                                <label>Qaytarish asosi va izohi</label>
                                <textarea name="comment" rows="4" placeholder="Qaysi bandlarda kamchilik aniqlanganligini kiriting..."></textarea>
                            </div>

                            <button type="submit" class="btn-att btn-att-primary" style="width: 100%;">Tasdiqlash</button>
                        </form>
                    @else
                        <div style="text-align: center; padding: 16px;">
                            <div style="font-size: 48px; margin-bottom: 10px;">
                                {{ $expertise->institute_status == 'approved' ? '✅' : '↩️' }}
                            </div>
                            <div style="font-weight: 700; font-size: 16px; color: {{ $expertise->institute_status == 'approved' ? 'var(--green)' : 'var(--red)' }}; margin-bottom: 6px;">
                                {{ $expertise->institute_status == 'approved' ? 'Ma\'qullangan' : 'Qaytarilgan' }}
                            </div>
                            <div style="font-size: 12px; color: var(--muted);">{{ optional($expertise->institute_reviewed_at)->format('d.m.Y H:i') }}</div>
                            @if($expertise->institute_comment)
                                <div style="margin-top: 12px; padding: 12px; background: var(--cream); border-radius: 8px; font-size: 13px; text-align: left; color: var(--ink);">
                                    {{ $expertise->institute_comment }}
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
