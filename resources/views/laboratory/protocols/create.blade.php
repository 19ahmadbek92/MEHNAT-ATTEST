<x-app-layout>
    <x-slot name="header">🔬 18-omilli O'lchash Protokoli — Ish o'rni #{{ $application->id }}: {{ $application->workplace_name }}</x-slot>

    <form method="POST" action="{{ route('laboratory.protocols.store', $application) }}" style="max-width: 980px;">
        @csrf
        @if($protocol) @method('PUT') @endif

        {{-- Flash xabarlar --}}
        @if(session('success'))
        <div style="background:var(--green-light);border:1px solid var(--green);color:var(--green);padding:12px 18px;border-radius:10px;margin-bottom:20px;">✅ {{ session('success') }}</div>
        @endif
        @if($errors->any())
        <div style="background:var(--red-light);border:1px solid var(--red);color:var(--red);padding:12px 18px;border-radius:10px;margin-bottom:20px;">
            @foreach($errors->all() as $e) <div>⚠️ {{ $e }}</div> @endforeach
        </div>
        @endif

        {{-- ── XALIKK + Asosiy ma'lumotlar ── --}}
        <div class="att-card" style="margin-bottom:18px;">
            <div class="att-card-header"><span class="att-card-title">📋 Ish o'rni identifikatori — Nizom 16-band</span></div>
            <div class="att-card-body" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                <div class="att-field">
                    <label class="att-label">Kasb nomi (XALIKK-2024) *</label>
                    <input type="text" name="profession_name" class="att-input" value="{{ old('profession_name', $protocol?->profession_name) }}" placeholder="Masalan: Payvandchi" required>
                </div>
                <div class="att-field">
                    <label class="att-label">XALIKK-2024 kod *</label>
                    <input type="text" name="profession_code" class="att-input" value="{{ old('profession_code', $protocol?->profession_code) }}" placeholder="7212.2" required>
                </div>
                <div class="att-field">
                    <label class="att-label">O'xshash ish o'rinlari soni</label>
                    <input type="number" name="similar_workplaces_count" class="att-input" value="{{ old('similar_workplaces_count', $protocol?->similar_workplaces_count ?? 1) }}" min="1">
                </div>
            </div>
            <div class="att-card-body" style="padding-top:0;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="att-field">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="is_representative_sample" value="1" {{ old('is_representative_sample', $protocol?->is_representative_sample) ? 'checked' : '' }}>
                        <span class="att-label" style="margin:0;">20% namunaviy tekshirish (o'xshash ish o'rinlari uchun)</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- ══ 18 OMIL BLOKI ══ --}}

        {{-- 1. Kimyoviy omillar (1a-ilova) --}}
        <div class="att-card" style="margin-bottom:14px;">
            <div class="att-card-header"><span class="att-card-title">☣️ 1. Kimyoviy omillar — SanQvaM 0069-24 Ilova №1a</span></div>
            <div class="att-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div class="att-field">
                        <label class="att-label">Zararli modda nomi</label>
                        <input type="text" name="chemical_factors[substance_name]" class="att-input" value="{{ old('chemical_factors.substance_name', $protocol?->chemical_factors['substance_name'] ?? '') }}" placeholder="Masalan: Azotsiz oksid">
                    </div>
                    <div class="att-field">
                        <label class="att-label">Haqiqiy qiymat (mg/m³)</label>
                        <input type="text" name="chemical_factors[actual_value]" class="att-input" value="{{ old('chemical_factors.actual_value', $protocol?->chemical_factors['actual_value'] ?? '') }}" placeholder="0.5">
                    </div>
                    <div class="att-field">
                        <label class="att-label">Gigienik me'yor (PDK)</label>
                        <input type="text" name="chemical_factors[norm_value]" class="att-input" value="{{ old('chemical_factors.norm_value', $protocol?->chemical_factors['norm_value'] ?? '') }}" placeholder="1.0">
                    </div>
                    <div class="att-field">
                        <label class="att-label">Ta'sir davomiyligi (%)</label>
                        <input type="number" name="chemical_factors[duration_pct]" class="att-input" value="{{ old('chemical_factors.duration_pct', $protocol?->chemical_factors['duration_pct'] ?? '') }}" placeholder="85" min="0" max="100">
                    </div>
                    <div class="att-field">
                        <label class="att-label">Sinf</label>
                        <select name="chemical_factors[class]" class="att-input">
                            @foreach(['—','1','2','3.1','3.2','3.3','3.4','4'] as $c)
                            <option value="{{ $c }}" {{ ($protocol?->chemical_factors['class'] ?? '—') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Fibrogenli aerozollar (1b-ilova) --}}
        <div class="att-card" style="margin-bottom:14px;">
            <div class="att-card-header"><span class="att-card-title">🌫️ 2. Fibrogenli aerozollar — SanQvaM 0069-24 Ilova №1b</span></div>
            <div class="att-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div class="att-field"><label class="att-label">Aerozol turi</label>
                        <input type="text" name="fibrogenic_aerosols[type]" class="att-input" value="{{ old('fibrogenic_aerosols.type', $protocol?->fibrogenic_aerosols['type'] ?? '') }}" placeholder="Silitsiy dioksid">
                    </div>
                    <div class="att-field"><label class="att-label">Haqiqiy qiymat (mg/m³)</label>
                        <input type="text" name="fibrogenic_aerosols[actual_value]" class="att-input" value="{{ old('fibrogenic_aerosols.actual_value', $protocol?->fibrogenic_aerosols['actual_value'] ?? '') }}">
                    </div>
                    <div class="att-field"><label class="att-label">Sinf</label>
                        <select name="fibrogenic_aerosols[class]" class="att-input">
                            @foreach(['—','1','2','3.1','3.2','3.3','3.4','4'] as $c)
                            <option value="{{ $c }}" {{ ($protocol?->fibrogenic_aerosols['class'] ?? '—') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Biologik omillar (2-ilova) --}}
        <div class="att-card" style="margin-bottom:14px;">
            <div class="att-card-header"><span class="att-card-title">🦠 3. Biologik omillar — SanQvaM 0069-24 Ilova №2</span></div>
            <div class="att-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div class="att-field"><label class="att-label">Biologik omil</label>
                        <input type="text" name="biological_factors[type]" class="att-input" value="{{ old('biological_factors.type', $protocol?->biological_factors['type'] ?? '') }}" placeholder="Mikroorganizmlar, sporalar...">
                    </div>
                    <div class="att-field"><label class="att-label">Sinf</label>
                        <select name="biological_factors[class]" class="att-input">
                            @foreach(['—','2','3.1','3.2','3.3','3.4','4'] as $c)
                            <option value="{{ $c }}" {{ ($protocol?->biological_factors['class'] ?? '—') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Shovqin, infratovush, tebranish (3-ilova) --}}
        <div class="att-card" style="margin-bottom:14px;">
            <div class="att-card-header"><span class="att-card-title">🔊 4. Shovqin / Infratovush / Tebranish — SanQvaM 0069-24 Ilova №3</span></div>
            <div class="att-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;">
                    <div class="att-field"><label class="att-label">Shovqin darajasi (dB)</label>
                        <input type="text" name="noise_vibration_factors[noise_db]" class="att-input" value="{{ old('noise_vibration_factors.noise_db', $protocol?->noise_vibration_factors['noise_db'] ?? '') }}">
                    </div>
                    <div class="att-field"><label class="att-label">Shovqin — PDU (dB)</label>
                        <input type="text" name="noise_vibration_factors[noise_norm]" class="att-input" value="{{ old('noise_vibration_factors.noise_norm', $protocol?->noise_vibration_factors['noise_norm'] ?? '') }}" placeholder="80">
                    </div>
                    <div class="att-field"><label class="att-label">Tebranish turi</label>
                        <select name="noise_vibration_factors[vibration_type]" class="att-input">
                            <option value="">—</option>
                            <option value="local" {{ ($protocol?->noise_vibration_factors['vibration_type'] ?? '') == 'local' ? 'selected' : '' }}>Mahalliy</option>
                            <option value="general" {{ ($protocol?->noise_vibration_factors['vibration_type'] ?? '') == 'general' ? 'selected' : '' }}>Umumiy</option>
                        </select>
                    </div>
                    <div class="att-field"><label class="att-label">Shovqin sinfi</label>
                        <select name="noise_vibration_factors[class]" class="att-input">
                            @foreach(['—','1','2','3.1','3.2','3.3','3.4','4'] as $c)
                            <option value="{{ $c }}" {{ ($protocol?->noise_vibration_factors['class'] ?? '—') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Ionlanmagan EMM (4-ilova) --}}
        <div class="att-card" style="margin-bottom:14px;">
            <div class="att-card-header"><span class="att-card-title">📡 5. Ionlanmagan Elektromagnit Maydon — SanQvaM 0069-24 Ilova №4</span></div>
            <div class="att-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div class="att-field"><label class="att-label">EMM turi (chastota)</label>
                        <input type="text" name="emf_factors[type]" class="att-input" value="{{ old('emf_factors.type', $protocol?->emf_factors['type'] ?? '') }}" placeholder="50 Hz, 3-300 kHz...">
                    </div>
                    <div class="att-field"><label class="att-label">Haqiqiy qiymat</label>
                        <input type="text" name="emf_factors[actual_value]" class="att-input" value="{{ old('emf_factors.actual_value', $protocol?->emf_factors['actual_value'] ?? '') }}">
                    </div>
                    <div class="att-field"><label class="att-label">Sinf</label>
                        <select name="emf_factors[class]" class="att-input">
                            @foreach(['—','1','2','3.1','3.2','3.3','3.4','4'] as $c)
                            <option value="{{ $c }}" {{ ($protocol?->emf_factors['class'] ?? '—') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. Mikroiqlim (6-12-ilova, SanQvaM 0204-06) --}}
        <div class="att-card" style="margin-bottom:14px;">
            <div class="att-card-header"><span class="att-card-title">🌡️ 6–12. Mikroiqlim — SanQvaM 0069-24 Ilova №6-12, SanQvaM RUz 0204-06</span></div>
            <div class="att-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;">
                    <div class="att-field"><label class="att-label">Havo harorati (°C)</label>
                        <input type="text" name="microclimate_factors[temperature]" class="att-input" value="{{ old('microclimate_factors.temperature', $protocol?->microclimate_factors['temperature'] ?? '') }}">
                    </div>
                    <div class="att-field"><label class="att-label">Nisbiy namlik (%)</label>
                        <input type="text" name="microclimate_factors[humidity]" class="att-input" value="{{ old('microclimate_factors.humidity', $protocol?->microclimate_factors['humidity'] ?? '') }}">
                    </div>
                    <div class="att-field"><label class="att-label">Havo tezligi (m/s)</label>
                        <input type="text" name="microclimate_factors[air_speed]" class="att-input" value="{{ old('microclimate_factors.air_speed', $protocol?->microclimate_factors['air_speed'] ?? '') }}">
                    </div>
                    <div class="att-field"><label class="att-label">Mikroiqlim sinfi</label>
                        <select name="microclimate_factors[class]" class="att-input">
                            @foreach(['—','1','2','3.1','3.2','3.3','3.4','4'] as $c)
                            <option value="{{ $c }}" {{ ($protocol?->microclimate_factors['class'] ?? '—') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- 7. Yorug'lik (13-ilova, KMK 2.01.05-98) --}}
        <div class="att-card" style="margin-bottom:14px;">
            <div class="att-card-header"><span class="att-card-title">💡 7. Yorug'lik muhiti — SanQvaM 0069-24 Ilova №13, KMK 2.01.05-98</span></div>
            <div class="att-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;">
                    <div class="att-field"><label class="att-label">Tabiiy yorug'lik KEO (%)</label>
                        <input type="text" name="lighting_factors[natural_keo]" class="att-input" value="{{ old('lighting_factors.natural_keo', $protocol?->lighting_factors['natural_keo'] ?? '') }}">
                    </div>
                    <div class="att-field"><label class="att-label">Sun'iy yoritilganlik (lk)</label>
                        <input type="text" name="lighting_factors[artificial_lux]" class="att-input" value="{{ old('lighting_factors.artificial_lux', $protocol?->lighting_factors['artificial_lux'] ?? '') }}">
                    </div>
                    <div class="att-field"><label class="att-label">Ko'z qamashishi (koeffitsient)</label>
                        <input type="text" name="lighting_factors[glare]" class="att-input" value="{{ old('lighting_factors.glare', $protocol?->lighting_factors['glare'] ?? '') }}">
                    </div>
                    <div class="att-field"><label class="att-label">Yorug'lik sinfi</label>
                        <select name="lighting_factors[class]" class="att-input">
                            @foreach(['—','1','2','3.1','3.2'] as $c)
                            <option value="{{ $c }}" {{ ($protocol?->lighting_factors['class'] ?? '—') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- 8. Ionlangan nurlanish (14-ilova, SanQvaM 0194-06) --}}
        <div class="att-card" style="margin-bottom:14px;">
            <div class="att-card-header"><span class="att-card-title">☢️ 8. Ionlangan nurlanish — SanQvaM 0069-24 Ilova №14, SanQvaM №0194-06</span></div>
            <div class="att-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div class="att-field"><label class="att-label">Yillik doza (mZv)</label>
                        <input type="text" name="ionizing_radiation[annual_dose_msv]" class="att-input" value="{{ old('ionizing_radiation.annual_dose_msv', $protocol?->ionizing_radiation['annual_dose_msv'] ?? '') }}">
                    </div>
                    <div class="att-field"><label class="att-label">PDU (mZv/yil)</label>
                        <input type="text" name="ionizing_radiation[norm_msv]" class="att-input" value="{{ old('ionizing_radiation.norm_msv', $protocol?->ionizing_radiation['norm_msv'] ?? '') }}" placeholder="20">
                    </div>
                    <div class="att-field"><label class="att-label">Sinf</label>
                        <select name="ionizing_radiation[class]" class="att-input">
                            @foreach(['—','1','2','3.1','3.2','3.3','3.4','4'] as $c)
                            <option value="{{ $c }}" {{ ($protocol?->ionizing_radiation['class'] ?? '—') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- 9. Atmosfera bosimi (15-ilova) --}}
        <div class="att-card" style="margin-bottom:14px;">
            <div class="att-card-header"><span class="att-card-title">🌀 9. Atmosfera bosimi — SanQvaM 0069-24 Ilova №15</span></div>
            <div class="att-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div class="att-field"><label class="att-label">Bosim darajasi (hPa)</label>
                        <input type="text" name="atmospheric_pressure[value_hpa]" class="att-input" value="{{ old('atmospheric_pressure.value_hpa', $protocol?->atmospheric_pressure['value_hpa'] ?? '') }}">
                    </div>
                    <div class="att-field"><label class="att-label">Sharoit turi</label>
                        <select name="atmospheric_pressure[condition_type]" class="att-input">
                            <option value="normal">Normal bosim</option>
                            <option value="elevated" {{ ($protocol?->atmospheric_pressure['condition_type'] ?? '') == 'elevated' ? 'selected' : '' }}>Oshirilgan bosim</option>
                            <option value="reduced" {{ ($protocol?->atmospheric_pressure['condition_type'] ?? '') == 'reduced' ? 'selected' : '' }}>Kamaytrilgan bosim</option>
                        </select>
                    </div>
                    <div class="att-field"><label class="att-label">Sinf</label>
                        <select name="atmospheric_pressure[class]" class="att-input">
                            @foreach(['—','1','2','3.1','3.2','3.3','3.4','4'] as $c)
                            <option value="{{ $c }}" {{ ($protocol?->atmospheric_pressure['class'] ?? '—') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- 10-11. Mehnat og'irligi va zichligi (16-17-18-ilova) --}}
        <div class="att-card" style="margin-bottom:14px;">
            <div class="att-card-header"><span class="att-card-title">💪 10–12. Mehnat og'irligi va zichligi — SanQvaM 0069-24 Ilova №16-17-18</span></div>
            <div class="att-card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="att-field">
                    <label class="att-label">Mehnat og'irligi sinfi *</label>
                    <select name="work_severity_class" class="att-input" required>
                        @foreach(['1','2','3.1','3.2','3.3','3.4','4'] as $c)
                        <option value="{{ $c }}" {{ old('work_severity_class', $protocol?->work_severity_class) == $c ? 'selected' : '' }}>
                            {{ $c }} — {{ ['1'=>'Engil','2'=>'O\'rta','3.1'=>'Og\'ir-1','3.2'=>'Og\'ir-2','3.3'=>'Og\'ir-3','3.4'=>'Og\'ir-4','4'=>'O\'ta og\'ir'][$c] }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="att-field">
                    <label class="att-label">Mehnat zichligi sinfi *</label>
                    <select name="work_intensity_class" class="att-input" required>
                        @foreach(['1','2','3.1','3.2','3.3','3.4','4'] as $c)
                        <option value="{{ $c }}" {{ old('work_intensity_class', $protocol?->work_intensity_class) == $c ? 'selected' : '' }}>
                            {{ $c }} — {{ ['1'=>'Minimal','2'=>'O\'rta','3.1'=>'Og\'ir-1','3.2'=>'Og\'ir-2','3.3'=>'Og\'ir-3','3.4'=>'Og\'ir-4','4'=>'O\'ta og\'ir'][$c] }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ══ UMUMIY BAHO ══ --}}
        <div class="att-card" style="margin-bottom:14px;border-top:3px solid var(--teal);">
            <div class="att-card-header"><span class="att-card-title">🏆 Umumiy baho — Nizom 29-band va 38-band</span></div>
            <div class="att-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div class="att-field">
                        <label class="att-label">Mehnat sharoitlari umumiy sinfi *</label>
                        <select name="overall_class" class="att-input" required>
                            @foreach(['1','2','3.1','3.2','3.3','3.4','4'] as $c)
                            <option value="{{ $c }}" {{ old('overall_class', $protocol?->overall_class) == $c ? 'selected' : '' }}>
                                {{ $c }} — {{ ['1'=>'Optimal (1-sinf)','2'=>'Ruxsat etilgan (2-sinf)','3.1'=>'Zararli 3.1','3.2'=>'Zararli 3.2','3.3'=>'Zararli 3.3','3.4'=>'Zararli 3.4','4'=>'Xavfli (4-sinf)'][$c] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="att-field">
                        <label class="att-label">Jarohatlanish xavfi sinfi — Nizom 38-band *</label>
                        <select name="injury_hazard_class" class="att-input" required>
                            <option value="low"      {{ old('injury_hazard_class', $protocol?->injury_hazard_class) == 'low' ? 'selected' : '' }}>✅ Past xavf (1-sinf)</option>
                            <option value="medium"   {{ old('injury_hazard_class', $protocol?->injury_hazard_class) == 'medium' ? 'selected' : '' }}>⚠️ O'rta xavf (2-sinf)</option>
                            <option value="high"     {{ old('injury_hazard_class', $protocol?->injury_hazard_class) == 'high' ? 'selected' : '' }}>🔶 Yuqori xavf (3-sinf)</option>
                            <option value="critical" {{ old('injury_hazard_class', $protocol?->injury_hazard_class) == 'critical' ? 'selected' : '' }}>🔴 Kritik xavf (4-sinf)</option>
                        </select>
                    </div>
                </div>

                {{-- YaTHV (PPE) baholash --}}
                <div style="border-top:1px solid var(--border-soft);padding-top:16px;margin-top:4px;">
                    <div style="font-weight:700;margin-bottom:10px;color:var(--ink);">🧤 YaTHV — Shaxsiy himoya vositalari baholash (MK 477-band)</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="ppe_provided" value="1" {{ old('ppe_provided', $protocol?->ppe_assessment['provided'] ?? false) ? 'checked' : '' }}>
                            <span class="att-label" style="margin:0;">YaTHV berilgan</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="ppe_certified" value="1" {{ old('ppe_certified', $protocol?->ppe_assessment['certified'] ?? false) ? 'checked' : '' }}>
                            <span class="att-label" style="margin:0;">Sertifikatlanganlar</span>
                        </label>
                        <div class="att-field">
                            <label class="att-label">YaTHV holati</label>
                            <select name="ppe_condition" class="att-input">
                                <option value="satisfactory" {{ ($protocol?->ppe_assessment['condition'] ?? 'satisfactory') == 'satisfactory' ? 'selected' : '' }}>Qoniqarli</option>
                                <option value="unsatisfactory" {{ ($protocol?->ppe_assessment['condition'] ?? '') == 'unsatisfactory' ? 'selected' : '' }}>Qoniqarsiz</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kafolatlar va Kompensatsiyalar (MK 183-184-427-429) --}}
        <div class="att-card" style="margin-bottom:14px;">
            <div class="att-card-header"><span class="att-card-title">🎁 Kafolatlar va Kompensatsiyalar — MK 183-184-427-429-363-477 bandlar</span></div>
            <div class="att-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:14px;">
                    <div class="att-field">
                        <label class="att-label">Qo'shimcha ta'til (kun) — MK 183-band</label>
                        <input type="number" name="additional_leave_days" class="att-input" value="{{ old('additional_leave_days', $protocol?->additional_leave_days ?? 0) }}" min="0" max="30">
                    </div>
                    <div class="att-field">
                        <label class="att-label">Qisqartirilgan ish vaqti (soat) — MK 184-band</label>
                        <input type="number" step="0.5" name="reduced_work_hours" class="att-input" value="{{ old('reduced_work_hours', $protocol?->reduced_work_hours) }}" placeholder="36.0 yoki 24.0" min="24" max="40">
                    </div>
                </div>
                <div style="display:flex;gap:24px;flex-wrap:wrap;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="has_medical_food" value="1" {{ old('has_medical_food', $protocol?->has_medical_food) ? 'checked' : '' }}>
                        <span class="att-label" style="margin:0;">Sut / tenglashtirilgan mahsulot — SanQvaM 0184-05</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="has_therapeutic_nutrition" value="1" {{ old('has_therapeutic_nutrition', $protocol?->has_therapeutic_nutrition) ? 'checked' : '' }}>
                        <span class="att-label" style="margin:0;">Davolash-profilaktik ovqatlanish</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="requires_benefits" value="1" {{ old('requires_benefits', $protocol?->requires_benefits) ? 'checked' : '' }}>
                        <span class="att-label" style="margin:0;">Imtiyozlar va kompensatsiyalar talab etiladi</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Saqlash --}}
        <div style="display:flex;gap:12px;align-items:center;margin-top:4px;">
            <button type="submit" class="btn-att btn-att-primary" style="padding:14px 36px;font-size:15px;">
                💾 Protokolni saqlash
            </button>
            <a href="{{ route('laboratory.protocols.index') }}" class="btn-att btn-att-secondary">← Orqaga</a>
            <span style="font-size:12px;color:var(--muted);margin-left:auto;">
                📌 VM Qarori №263, SanQvaM 0069-24, MK 183-184-427-429-477
            </span>
        </div>
    </form>
</x-app-layout>
