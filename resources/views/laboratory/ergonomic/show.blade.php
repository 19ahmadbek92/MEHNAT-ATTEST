@php
    $riskColor = match (true) {
        $assessment->integral_safety_index >= 70 => 'var(--green)',
        $assessment->integral_safety_index >= 40 => 'var(--gold)',
        default => 'var(--red)',
    };
@endphp
<x-app-layout>
    <x-slot name="header">Ergonomik baholash natijasi</x-slot>

    <x-page-header
        title="{{ $workplace->name }} — {{ $assessment->employee->full_name ?? 'Baholash natijasi' }}"
        :subtitle="'Baholangan sana: '.optional($assessment->assessed_at)->format('d.m.Y')"
        :crumbs="[
            ['label' => 'Bosh sahifa', 'url' => route('dashboard')],
            ['label' => 'Ish o‘rinlari o‘lchovi', 'url' => route('laboratory.workplaces.index')],
            ['label' => 'Ergonomik baholash', 'url' => route('laboratory.ergonomic.index', $workplace)],
            ['label' => 'Natija'],
        ]"
    />

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:16px;">
        <div class="att-card" style="padding:20px;text-align:center;">
            <div style="font-size:11px;letter-spacing:.6px;color:var(--muted);text-transform:uppercase;margin-bottom:8px;">Integral xavfsizlik ko'rsatkichi</div>
            <div style="font-size:38px;font-weight:800;color:{{ $riskColor }};">{{ $assessment->integral_safety_index }}<span style="font-size:16px;color:var(--muted);">/100</span></div>
            <div style="font-size:13px;font-weight:600;margin-top:4px;color:{{ $riskColor }};">{{ $assessment->risk_category }}</div>
        </div>
        <div class="att-card" style="padding:20px;text-align:center;">
            <div style="font-size:11px;letter-spacing:.6px;color:var(--muted);text-transform:uppercase;margin-bottom:8px;">Ko'tarish indeksi (NIOSH LI)</div>
            <div style="font-size:38px;font-weight:800;color:var(--ink);">{{ $assessment->lifting_index }}</div>
            <div style="font-size:13px;color:var(--muted);margin-top:4px;">RWL = {{ $assessment->rwl_kg }} kg · Yuk = {{ $assessment->load_mass_kg }} kg</div>
        </div>
        <div class="att-card" style="padding:20px;text-align:center;">
            <div style="font-size:11px;letter-spacing:.6px;color:var(--muted);text-transform:uppercase;margin-bottom:8px;">Og'irlik / Zo'riqish klassi</div>
            <div style="font-size:24px;font-weight:800;color:var(--ink);">{{ $assessment->severity_class }} / {{ $assessment->strain_class }}</div>
            <div style="font-size:12px;color:var(--muted);margin-top:4px;">SanQvaM 0069-24 uslubida (1 / 2 / 3.1–3.4 / 4)</div>
        </div>
        <div class="att-card" style="padding:20px;text-align:center;">
            <div style="font-size:11px;letter-spacing:.6px;color:var(--muted);text-transform:uppercase;margin-bottom:8px;">Inson omili xavfi</div>
            <div style="font-size:38px;font-weight:800;color:var(--ink);">{{ $assessment->human_factor_risk_score }}<span style="font-size:16px;color:var(--muted);">/100</span></div>
        </div>
    </div>

    <div class="att-card" style="margin-bottom:16px;">
        <div class="att-card-header"><div class="att-card-title">Tavsiyalar</div></div>
        <div class="att-card-body">
            <ul style="margin:0;padding-left:18px;line-height:1.8;color:#333;">
                @foreach ($assessment->recommendations ?? [] as $rec)
                    <li>{{ $rec }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:16px;">
        <div class="att-card">
            <div class="att-card-header"><div class="att-card-title">Yuk ko'tarish parametrlari</div></div>
            <div class="att-card-body">
                <div class="info-row"><span class="info-label">Yuk tavsifi</span><span>{{ $assessment->load_description ?? '—' }}</span></div>
                <div class="info-row"><span class="info-label">Mexanizatsiya</span><span>{{ $assessment->operation_type }}</span></div>
                <div class="info-row"><span class="info-label">Massa</span><span>{{ $assessment->load_mass_kg }} kg</span></div>
                <div class="info-row"><span class="info-label">Chastota</span><span>{{ $assessment->lifts_per_minute }} /daq · {{ $assessment->lifts_per_shift }} /smena</span></div>
                <div class="info-row"><span class="info-label">H / V / D</span><span>{{ $assessment->horizontal_distance_cm }} / {{ $assessment->vertical_location_cm }} / {{ $assessment->vertical_travel_cm }} sm</span></div>
                <div class="info-row"><span class="info-label">Burilish burchagi</span><span>{{ $assessment->asymmetry_angle_deg }}°</span></div>
                <div class="info-row"><span class="info-label">Ushlash sifati</span><span>{{ $assessment->coupling_quality }}</span></div>
            </div>
        </div>

        <div class="att-card">
            <div class="att-card-header"><div class="att-card-title">Ish holati va zo'riqish</div></div>
            <div class="att-card-body">
                <div class="info-row"><span class="info-label">Ish holati (poza)</span><span>{{ $assessment->posture_type }}</span></div>
                <div class="info-row"><span class="info-label">Tana egilishi</span><span>{{ $assessment->body_inclinations_per_shift }} marta/smena</span></div>
                <div class="info-row"><span class="info-label">Yurish masofasi</span><span>{{ $assessment->walking_distance_km }} km</span></div>
                <div class="info-row"><span class="info-label">Diqqat konsentratsiyasi</span><span>{{ $assessment->attention_concentration_percent }}%</span></div>
                <div class="info-row"><span class="info-label">Mas'uliyat</span><span>{{ $assessment->responsibility_level }}</span></div>
                <div class="info-row"><span class="info-label">Tungi smena</span><span>{{ $assessment->night_shift ? 'Ha' : "Yo'q" }}</span></div>
            </div>
        </div>

        <div class="att-card">
            <div class="att-card-header"><div class="att-card-title">Ish muhiti</div></div>
            <div class="att-card-body">
                <div class="info-row"><span class="info-label">Harorat</span><span>{{ $assessment->temperature_c ?? '—' }} °C</span></div>
                <div class="info-row"><span class="info-label">Metall changi</span><span>{{ $assessment->metal_dust_mg_m3 ?? '—' }} mg/m³</span></div>
                <div class="info-row"><span class="info-label">Shovqin</span><span>{{ $assessment->noise_level_db ?? '—' }} dB</span></div>
                <div class="info-row"><span class="info-label">SHV foydalanish</span><span>{{ $assessment->uses_ppe ? 'Ha' : "Yo'q" }}</span></div>
            </div>
        </div>

        <div class="att-card">
            <div class="att-card-header"><div class="att-card-title">Inson omili</div></div>
            <div class="att-card-body">
                <div class="info-row"><span class="info-label">Yoshi / staji</span><span>{{ $assessment->employee_age ?? '—' }} / {{ $assessment->experience_years ?? '—' }} yil</span></div>
                <div class="info-row"><span class="info-label">O'qitilganlik</span><span>{{ $assessment->training_completed ? 'Ha' : "Yo'q" }}</span></div>
                <div class="info-row"><span class="info-label">Charchoq bahosi</span><span>{{ $assessment->fatigue_self_score }}/5</span></div>
                <div class="info-row"><span class="info-label">Sog'liq guruhi</span><span>{{ $assessment->health_group }}</span></div>
                <div class="info-row"><span class="info-label">Oldingi hodisalar</span><span>{{ $assessment->prior_incidents_count }}</span></div>
            </div>
        </div>
    </div>

    @if ($assessment->notes)
        <div class="att-card" style="margin-top:16px;">
            <div class="att-card-header"><div class="att-card-title">Izoh</div></div>
            <div class="att-card-body">{{ $assessment->notes }}</div>
        </div>
    @endif

    <div style="margin-top:16px;">
        <x-att-button :href="route('laboratory.ergonomic.index', $workplace)" variant="ghost">← Ro'yxatga qaytish</x-att-button>
    </div>
</x-app-layout>
