<x-app-layout>
    <x-slot name="header">Ariza #{{ $application->id }}</x-slot>

    <div class="page-narrow">

        <x-page-header
            title="Ish o‘rni ma‘lumotlari"
            subtitle="Vazirlar Mahkamasining 263-sonli qaroriga asosan"
            :crumbs="[
                ['label' => 'Boshqaruv paneli', 'url' => route('dashboard')],
                ['label' => 'Arizalar', 'url' => route('hr.applications.index')],
                ['label' => 'Ariza #'.$application->id],
            ]"
        >
            <x-slot name="actions">
                <x-att-button :href="route('hr.applications.index')" variant="secondary" size="sm">
                    Ortga
                </x-att-button>
            </x-slot>
        </x-page-header>

        {{-- ─── Asosiy ma'lumot ─── --}}
        <div class="att-card att-card-accent-teal" style="margin-bottom:18px;">
            <div class="att-card-header">
                <span class="att-card-title">Tashkilot va ish o‘rni</span>
                @php
                    $statusMap = [
                        'submitted'   => 'submitted',
                        'hr_approved' => 'approved',
                        'hr_rejected' => 'rejected',
                        'finalized'   => 'finalized',
                    ];
                @endphp
                <x-att-badge :status="$statusMap[$application->status] ?? 'pending'" />
            </div>
            <div class="att-card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <table class="kv-list">
                            <tr class="kv-row"><td class="kv-key">Tashkilot</td><td class="kv-val">{{ $application->user?->name }}</td></tr>
                            <tr class="kv-row"><td class="kv-key">Ish o‘rni</td><td class="kv-val">{{ $application->workplace_name ?? $application->position }}</td></tr>
                            <tr class="kv-row"><td class="kv-key">Bo‘lim</td><td class="kv-val">{{ $application->department ?? '—' }}</td></tr>
                            <tr class="kv-row"><td class="kv-key">Xodimlar soni</td><td class="kv-val">{{ $application->employee_count ?? '—' }}</td></tr>
                            <tr class="kv-row"><td class="kv-key">Kampaniya</td><td class="kv-val">{{ $application->campaign?->title ?? '—' }}</td></tr>
                            <tr class="kv-row"><td class="kv-key">Yuborilgan</td><td class="kv-val">{{ optional($application->created_at)->format('d.m.Y H:i') }}</td></tr>
                        </table>
                    </div>
                    <div>
                        @if($application->workplace_photo_path)
                            <div style="border-radius:var(--r);overflow:hidden;border:1px solid var(--border);margin-bottom:12px;">
                                <img src="{{ asset('storage/'.$application->workplace_photo_path) }}" alt="Ish o‘rni" style="width:100%;height:200px;object-fit:cover;display:block;">
                            </div>
                        @endif
                        <div style="background:#faf8f4;border:1px solid var(--border-soft);border-radius:var(--r);padding:14px 16px;">
                            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;">Hujjatlar</div>
                            @if($application->documents_path)
                                <a class="file-pill" href="{{ asset('storage/'.$application->documents_path) }}" target="_blank" rel="noopener">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                                    Hujjatlarni ko‘rish
                                </a>
                            @else
                                <span class="text-muted" style="font-size:13px;">Hujjat yuklanmagan</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Komissiya natijalari ─── --}}
        <div class="att-card" style="margin-bottom:18px;">
            <div class="att-card-header">
                <span class="att-card-title">Komissiya tekshiruvi natijalari</span>
            </div>
            <div class="att-card-body">
                @php $ev = $application->evaluations->first(); @endphp

                @if($ev)
                    <div class="metric-grid" style="margin-bottom:18px;">
                        @foreach([['Shovqin', $ev->noise_level, 'dB'], ['Chang', $ev->dust_level, 'mg/m³'], ['Tebranish', $ev->vibration_level, 'm/s²'], ['Yoritilganlik', $ev->lighting_level, 'lux']] as [$label, $val, $unit])
                            <div class="metric-box">
                                <div class="ml">{{ $label }}</div>
                                <div class="mv">{{ $val ?? '—' }}<span class="mu">{{ $unit }}</span></div>
                            </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-section" style="margin:0;">
                            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;">Atrof-muhit</div>
                            <div class="info-row"><span class="info-label">Mikroiqlim</span><span class="info-value">{{ $ev->microclimate ?? '—' }}</span></div>
                            <div class="info-row"><span class="info-label">Uskunalar xavfi</span><span class="info-value">{{ $ev->equipment_hazard_score ?? '—' }} ball</span></div>
                            <div class="info-row"><span class="info-label">Himoya vositalari</span><span class="info-value">{{ $ev->getProtectiveStatusLabel() }}</span></div>
                        </div>
                        <div class="form-section" style="margin:0;">
                            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;">Komissiya xulosasi</div>
                            <p style="font-size:13px;color:#524e46;line-height:1.65;margin:0 0 12px;">{{ $ev->comment ?? 'Xulosa kiritilmagan' }}</p>
                            <div class="info-row" style="border-top:1px solid var(--border-soft);padding-top:12px;border-bottom:none;">
                                <span class="info-label" style="font-size:11px;text-transform:uppercase;font-weight:700;letter-spacing:.6px;">O‘rtacha ball</span>
                                <span style="font-family:var(--font-display);font-size:24px;color:var(--teal);">{{ $application->final_score ?? '0.00' }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <x-empty-state
                        icon="◷"
                        title="Tekshiruv natijalari yo‘q"
                        description="Komissiya hali bu ish o‘rni uchun o‘lchov natijalarini kiritmagan."
                    />
                @endif
            </div>
        </div>

        {{-- ─── Ekspertiza qarori ─── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="att-card">
                <div class="att-card-header">
                    <span class="att-card-title">Ekspertiza qarori</span>
                </div>
                <div class="att-card-body">
                    @if($application->status === 'submitted')
                        <p class="text-muted" style="font-size:13.5px;margin:0 0 16px;">Arizani ko‘rib chiqing va qaror qabul qiling.</p>

                        <form method="POST" action="{{ route('hr.applications.approve', $application) }}" style="margin-bottom:16px;">
                            @csrf
                            <div class="att-field" style="margin-bottom:12px;">
                                <label>Izoh (ixtiyoriy)</label>
                                <textarea name="hr_comment" rows="2" placeholder="Tasdiqlash uchun izoh..."></textarea>
                            </div>
                            <x-att-button type="submit" variant="success" style="width:100%;">Tasdiqlash</x-att-button>
                        </form>

                        <form method="POST" action="{{ route('hr.applications.reject', $application) }}">
                            @csrf
                            <div class="att-field" style="margin-bottom:12px;">
                                <label>Rad etish sababi <span class="req">*</span></label>
                                <textarea name="hr_comment" rows="2" placeholder="Rad etish sababini yozing..." required></textarea>
                            </div>
                            <x-att-button type="submit" variant="danger" style="width:100%;">Rad etish</x-att-button>
                        </form>

                    @elseif($application->status === 'hr_approved')
                        <form method="POST" action="{{ route('hr.applications.finalize', $application) }}">
                            @csrf
                            <div class="att-field" style="margin-bottom:14px;">
                                <label>Yakuniy ish o‘rni klassi <span class="req">*</span></label>
                                <select name="workplace_class" required>
                                    <option value="">— Tanlang —</option>
                                    <option value="optimal">Optimal (1-klass)</option>
                                    <option value="ruxsat_etilgan">Ruxsat etilgan (2-klass)</option>
                                    <option value="zararli_xavfli">Zararli / Xavfli (3-klass)</option>
                                </select>
                            </div>
                            <x-att-button type="submit" variant="primary" style="width:100%;">Qarorni tasdiqlash</x-att-button>
                        </form>

                    @elseif($application->status === 'finalized')
                        <div class="info-row" style="border-bottom:1px solid var(--border-soft);"><span class="info-label">Yakuniy natija</span><span style="font-family:var(--font-display);font-size:18px;color:var(--teal);">{{ $application->getWorkplaceClassLabel() }}</span></div>
                        <p style="font-size:13px;color:#524e46;line-height:1.6;margin:14px 0 8px;"><strong>Ekspert izohi:</strong> {{ $application->hr_comment ?? '—' }}</p>
                        <p class="text-muted" style="font-size:11.5px;margin:0;">Tasdiqlangan: {{ optional($application->finalized_at)->format('d.m.Y H:i') }}</p>

                    @else
                        <div style="padding:18px 14px;background:var(--red-light);border-radius:var(--r);color:var(--red);">
                            <strong style="display:block;margin-bottom:6px;">Ariza rad etilgan</strong>
                            <span style="font-size:13px;">Sabab: {{ $application->hr_comment ?? '—' }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="att-card" style="background:linear-gradient(160deg,#0e1117 0%,#1a2a3a 100%);color:white;border-color:rgba(255,255,255,.06);">
                <div class="att-card-body">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#4ecdc4;margin-bottom:10px;">Ish o‘rni hujjatlari</div>
                    @if($application->status === 'finalized')
                        <p style="font-size:13.5px;color:rgba(255,255,255,.62);margin:0 0 14px;line-height:1.6;">Attestatsiya yakunlandi. Ish o‘rni pasporti va ma‘lumotnomani chop etishingiz mumkin.</p>
                        <button type="button" onclick="window.print()" class="btn-att" style="background:#4ecdc4;color:var(--ink);width:100%;">
                            Pasportni chop etish
                        </button>
                    @else
                        <p style="font-size:13.5px;color:rgba(255,255,255,.5);margin:0;line-height:1.6;">Yakuniy qaror qabul qilingandan so‘ng pasport tayyor bo‘ladi.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
