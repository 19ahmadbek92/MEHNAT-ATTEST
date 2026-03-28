<x-app-layout>
    <x-slot name="header">Davlat ekspertizasi: Ariza #{{ $application->id }}</x-slot>

    <div style="max-width: 1000px;">
        {{-- Header --}}
        <div class="att-card" style="margin-bottom: 24px;">
            <div style="background: var(--ink); padding: 24px; color: white; border-radius: 12px 12px 0 0;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <h3 style="font-family: 'DM Serif Display', serif; font-size: 22px; margin: 0 0 4px;">Ish o'rni ma'lumotlari</h3>
                        <p style="color: rgba(255,255,255,0.5); font-size: 13px; margin: 0;">Vazirlar Mahkamasining 263-sonli qaroriga asosan</p>
                    </div>
                    <span class="status-badge
                        @switch($application->status)
                            @case('submitted') sb-submitted @break
                            @case('hr_approved') sb-approved @break
                            @case('hr_rejected') sb-rejected @break
                            @case('finalized') sb-finalized @break
                        @endswitch" style="font-size: 12px; padding: 6px 14px;">
                        {{ strtoupper($application->status) }}
                    </span>
                </div>
            </div>
            <div class="att-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <div>
                        <div style="margin-bottom: 16px; padding-left: 14px; border-left: 3px solid var(--teal);">
                            <div style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 600; letter-spacing: 1px;">Tashkilot</div>
                            <div style="font-size: 18px; font-weight: 700; color: var(--ink);">{{ $application->user?->name }}</div>
                        </div>
                        <div style="margin-bottom: 16px; padding-left: 14px; border-left: 3px solid var(--teal);">
                            <div style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 600; letter-spacing: 1px;">Ish o'rni nomi</div>
                            <div style="font-size: 18px; font-weight: 700; color: var(--ink);">{{ $application->workplace_name ?? $application->position }}</div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <div style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 600;">Bo'lim</div>
                                <div style="font-size: 16px;">{{ $application->department ?? '—' }}</div>
                            </div>
                            <div>
                                <div style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 600;">Xodimlar soni</div>
                                <div style="font-size: 16px;">{{ $application->employee_count ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        @if($application->workplace_photo_path)
                            <div style="border-radius: 10px; overflow: hidden; border: 1px solid var(--border); margin-bottom: 12px;">
                                <img src="{{ asset('storage/'.$application->workplace_photo_path) }}" alt="Ish o'rni" style="width: 100%; height: 180px; object-fit: cover;">
                            </div>
                        @endif
                        <div style="background: var(--cream); border-radius: 10px; padding: 16px;">
                            <h4 style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--muted); letter-spacing: 1px; margin: 0 0 10px;">Taqdim etilgan hujjatlar</h4>
                            @if($application->documents_path)
                                <a href="{{ asset('storage/'.$application->documents_path) }}" target="_blank" style="display: flex; align-items: center; gap: 8px; color: var(--teal); font-weight: 600; text-decoration: none; font-size: 14px;">📄 Hujjatlarni ko'rish</a>
                            @else
                                <span style="color: var(--muted); font-size: 13px;">Hujjat yuklanmagan</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Komissiya natijalari --}}
        <div class="att-card" style="margin-bottom: 24px;">
            <div class="att-card-header">
                <div class="att-card-title">🔬 Komissiya tekshiruvi natijalari</div>
            </div>
            <div class="att-card-body">
                @php $ev = $application->evaluations->first(); @endphp

                @if($ev)
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px;">
                        @foreach([['Shovqin', $ev->noise_level, 'dB'], ['Chang', $ev->dust_level, 'mg/m³'], ['Tebranish', $ev->vibration_level, 'm/s²'], ['Yoritilganlik', $ev->lighting_level, 'lux']] as [$label, $val, $unit])
                        <div style="text-align: center; background: var(--cream); padding: 16px; border-radius: 10px;">
                            <div style="font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase;">{{ $label }}</div>
                            <div style="font-size: 20px; font-weight: 700; color: var(--ink);">{{ $val ?? '—' }} <small style="font-size: 11px;">{{ $unit }}</small></div>
                        </div>
                        @endforeach
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div style="background: var(--cream); padding: 16px; border-radius: 10px;">
                            <div style="font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 10px; letter-spacing: 1px;">Atrof-muhit</div>
                            <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed var(--border); font-size: 13px;"><span style="color: var(--muted);">Mikroiqlim:</span><span style="font-weight: 600;">{{ $ev->microclimate ?? '—' }}</span></div>
                            <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed var(--border); font-size: 13px;"><span style="color: var(--muted);">Uskunalar xavfi:</span><span style="font-weight: 600;">{{ $ev->equipment_hazard_score ?? '—' }} ball</span></div>
                            <div style="display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px;"><span style="color: var(--muted);">Himoya vositalari:</span><span style="font-weight: 600; text-transform: uppercase;">{{ $ev->getProtectiveStatusLabel() }}</span></div>
                        </div>
                        <div style="background: var(--cream); padding: 16px; border-radius: 10px;">
                            <div style="font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 10px; letter-spacing: 1px;">Komissiya xulosasi</div>
                            <p style="font-style: italic; color: #555; line-height: 1.6; font-size: 13px; margin: 0 0 12px;">{{ $ev->comment ?? 'Xulosa kiritilmagan' }}</p>
                            <div style="padding-top: 10px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 600;">O'rtacha ball:</span>
                                <span style="font-size: 20px; font-weight: 900; color: var(--teal);">{{ $application->final_score ?? '0.00' }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 40px; background: var(--cream); border-radius: 10px; color: var(--muted); font-style: italic;">
                        <div style="font-size: 36px; margin-bottom: 8px;">🕒</div>
                        Tekshiruv natijalari hali kiritilmagan.
                    </div>
                @endif
            </div>
        </div>

        {{-- Ekspert qarori --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <div class="att-card">
                <div class="att-card-header">
                    <div class="att-card-title">📋 Ekspertiza qarori</div>
                </div>
                <div class="att-card-body">
                    @if($application->status === 'submitted')
                        {{-- Tasdiqlash va rad etish tugmalari --}}
                        <p style="font-size: 14px; color: var(--muted); margin: 0 0 16px;">Arizani ko'rib chiqing va qaror qabul qiling:</p>

                        <form method="POST" action="{{ route('hr.applications.approve', $application) }}" style="margin-bottom: 16px;">
                            @csrf
                            <div class="att-field" style="margin-bottom: 12px;">
                                <label>Izoh (ixtiyoriy)</label>
                                <textarea name="hr_comment" rows="2" placeholder="Tasdiqlash izohi..."></textarea>
                            </div>
                            <button type="submit" class="btn-att btn-att-primary" style="width: 100%;">✅ Tasdiqlash</button>
                        </form>

                        <form method="POST" action="{{ route('hr.applications.reject', $application) }}">
                            @csrf
                            <div class="att-field" style="margin-bottom: 12px;">
                                <label>Rad etish sababi *</label>
                                <textarea name="hr_comment" rows="2" placeholder="Rad etish sababini yozing..." required></textarea>
                            </div>
                            <button type="submit" class="btn-att btn-att-danger" style="width: 100%;">❌ Rad etish</button>
                        </form>

                    @elseif(in_array($application->status, ['hr_approved', 'finalized']))
                        {{-- Yakuniy klass berish formasi --}}
                        @if($application->status !== 'finalized')
                        <form method="POST" action="{{ route('hr.applications.finalize', $application) }}">
                            @csrf
                            <div class="att-field" style="margin-bottom: 16px;">
                                <label>Yakuniy ish o'rni klassi *</label>
                                <select name="workplace_class" required>
                                    <option value="">-- Tanlang --</option>
                                    <option value="optimal">Optimal (1-klass)</option>
                                    <option value="ruxsat_etilgan">Ruxsat etilgan (2-klass)</option>
                                    <option value="zararli_xavfli">Zararli / Xavfli (3-klass)</option>
                                </select>
                            </div>
                            <button type="submit" class="btn-att btn-att-primary" style="width: 100%;">Qarorni tasdiqlash</button>
                        </form>
                        @else
                        <div style="space-y: 12px;">
                            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed var(--cream);">
                                <span style="color: var(--muted); font-size: 12px; text-transform: uppercase; font-weight: 600;">Yakuniy natija:</span>
                                <span style="font-size: 18px; font-weight: 900; color: var(--teal); text-transform: uppercase;">{{ $application->getWorkplaceClassLabel() }}</span>
                            </div>
                            <p style="font-size: 13px; font-style: italic; margin-top: 10px;"><span style="font-weight: 700;">Ekspert izohi:</span> {{ $application->hr_comment ?? '—' }}</p>
                            <p style="font-size: 11px; color: var(--muted); font-style: italic; margin-top: 4px;">Tasdiqlangan sana: {{ optional($application->finalized_at)->format('d.m.Y H:i') }}</p>
                        </div>
                        @endif

                    @else
                        <div style="color: var(--muted); font-style: italic; padding: 20px; text-align: center;">
                            Bu ariza rad etilgan.
                            <p style="margin-top: 8px; font-weight: 600;">Sabab: {{ $application->hr_comment ?? '—' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="att-card" style="background: var(--ink); color: white;">
                <div class="att-card-body">
                    <h4 style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #4ecdc4; margin: 0 0 12px;">Ish o'rni hujjatlari</h4>
                    @if($application->status === 'finalized')
                        <p style="font-size: 14px; color: rgba(255,255,255,0.6); margin: 0 0 16px; line-height: 1.6;">Attestatsiya yakunlandi. Ish o'rni pasporti va ma'lumotnomani chop etishingiz mumkin.</p>
                        <button onclick="window.print()" class="btn-att btn-att-primary" style="width: 100%; background: #4ecdc4; color: var(--ink);">⎙ Pasportni chop etish</button>
                    @else
                        <p style="color: rgba(255,255,255,0.4); display: flex; align-items: center; gap: 8px; font-style: italic; font-size: 14px; margin: 0;">
                            🕒 Yakuniy qaror qabul qilingandan so'ng pasport tayyor bo'ladi.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <a href="{{ route('hr.applications.index') }}" class="btn-att btn-att-secondary">← Ro'yxatga qaytish</a>
        </div>
    </div>
</x-app-layout>
