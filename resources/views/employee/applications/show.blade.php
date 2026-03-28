<x-app-layout>
    <x-slot name="header">Ariza #{{ $application->id }}</x-slot>

    <div style="max-width: 860px;">
        {{-- Holat paneli --}}
        @php
            $steps = [
                'submitted'           => ['Yuborildi',          '📝', 'Ish beruvchi ariza yubordi'],
                'commission_reviewed' => ['Protokol tayyor',    '🔬', 'Laboratoriya o\'lchovlarni kiritdi'],
                'hr_approved'         => ['Ekspertizaga tayyor','📦', 'Davlat ekspertizasiga yuborildi'],
                'finalized'           => ['Yakunlandi',         '✅', 'Davlat xulosasi berildi'],
            ];
            $statusOrder = array_keys($steps);
            $currentIdx  = array_search($application->status, $statusOrder);
        @endphp

        <div class="att-card" style="margin-bottom: 20px;">
            <div class="att-card-header">
                <span class="att-card-title">⚡ Ariza holati</span>
                <span class="status-badge {{ match($application->status) {
                    'submitted'           => 'sb-submitted',
                    'commission_reviewed' => 'sb-approved',
                    'hr_approved'         => 'sb-approved',
                    'finalized'           => 'sb-finalized',
                    default               => 'sb-pending'
                } }}">
                    {{ $steps[$application->status][0] ?? $application->status }}
                </span>
            </div>
            <div class="att-card-body">
                <div style="display: flex; gap: 0; overflow-x: auto;">
                    @foreach($steps as $key => [$label, $icon, $desc])
                        @php
                            $idx   = array_search($key, $statusOrder);
                            $done  = $currentIdx !== false && $idx <= $currentIdx;
                            $active= $key === $application->status;
                        @endphp
                        <div style="flex: 1; text-align: center; position: relative; min-width: 120px;">
                            @if(!$loop->first)
                                <div style="position: absolute; top: 18px; left: -50%; right: 50%; height: 2px; background: {{ $done ? '#0d6e6e' : '#dddad2' }};"></div>
                            @endif
                            <div style="width: 38px; height: 38px; border-radius: 50%; margin: 0 auto 8px; display: flex; align-items: center; justify-content: center; font-size: 17px; position: relative; z-index: 1;
                                background: {{ $active ? '#0d6e6e' : ($done ? '#d6eeee' : '#f0ece4') }};
                                box-shadow: {{ $active ? '0 0 0 4px rgba(13,110,110,0.18)' : 'none' }};">
                                {{ $icon }}
                            </div>
                            <div style="font-size: 11px; font-weight: 700; color: {{ $done ? '#0e1117' : '#78756e' }};">{{ $label }}</div>
                            <div style="font-size: 10.5px; color: #78756e; margin-top: 2px; line-height: 1.4;">{{ $desc }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            {{-- Asosiy ma'lumotlar --}}
            <div class="att-card">
                <div class="att-card-header"><span class="att-card-title">🏭 Ish o'rni ma'lumotlari</span></div>
                <div class="att-card-body" style="display: flex; flex-direction: column; gap: 12px;">
                    <div>
                        <div style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.6px; color: var(--muted); margin-bottom: 3px;">Ish o'rni nomi</div>
                        <div style="font-weight: 600; color: var(--ink);">{{ $application->workplace_name }}</div>
                    </div>
                    @if($application->department)
                    <div>
                        <div style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.6px; color: var(--muted); margin-bottom: 3px;">Bo'lim</div>
                        <div style="font-weight: 500;">{{ $application->department }}</div>
                    </div>
                    @endif
                    <div>
                        <div style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.6px; color: var(--muted); margin-bottom: 3px;">Xodimlar soni</div>
                        <div style="font-weight: 500;">{{ $application->employee_count ?? '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.6px; color: var(--muted); margin-bottom: 3px;">Kampaniya</div>
                        <div style="font-weight: 500;">{{ $application->campaign->name ?? '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Xavfli omillar --}}
            <div class="att-card">
                <div class="att-card-header"><span class="att-card-title">⚠️ E'lon qilingan xavfli omillar</span></div>
                <div class="att-card-body">
                    @if($application->hazard_factors && count($application->hazard_factors))
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            @foreach($application->hazard_factors as $factor)
                                <span class="status-badge sb-approved">{{ $factor }}</span>
                            @endforeach
                        </div>
                    @else
                        <p style="color: var(--muted); font-size: 13px;">—</p>
                    @endif

                    @if($application->workplace_description)
                        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border-soft);">
                            <div style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.6px; color: var(--muted); margin-bottom: 4px;">Tavsif</div>
                            <p style="font-size: 13px; color: var(--ink); line-height: 1.6; margin: 0;">{{ $application->workplace_description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Protocol natijasi --}}
        @if($application->protocol)
        <div class="att-card" style="margin-bottom: 16px;">
            <div class="att-card-header">
                <span class="att-card-title">🔬 Laboratoriya o'lchov protokoli</span>
                <span class="status-badge sb-finalized">Tayyor ✓</span>
            </div>
            <div class="att-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                    @php
                        $classes = ['1'=>['Optimal', 'green'], '2'=>['Ruxsat etilgan', 'gold'], '3.1'=>['Zararli (3.1)', 'gold'], '3.2'=>['Zararli (3.2)', 'red'], '3.3'=>['Zararli (3.3)', 'red'], '3.4'=>['Zararli (3.4)', 'red'], '4'=>['Xavfli', 'red']];
                    @endphp
                    @foreach([
                        ['Mehnat og\'irligi', $application->protocol->work_severity_class],
                        ['Mehnat zichligi', $application->protocol->work_intensity_class],
                        ['Umumiy sinf', $application->protocol->overall_class],
                    ] as [$label, $cls])
                        @php $info = $classes[$cls] ?? ['???', 'muted']; @endphp
                        <div style="text-align: center; padding: 14px; border-radius: 10px; background: var(--paper);">
                            <div style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.6px; color: var(--muted); margin-bottom: 6px;">{{ $label }}</div>
                            <div style="font-size: 22px; font-family: 'DM Serif Display', serif; color: var(--ink);">{{ $cls }}</div>
                            <div style="font-size: 11px; font-weight: 700; color: var(--{{ $info[1] }});">{{ $info[0] }}</div>
                        </div>
                    @endforeach
                </div>
                @if($application->protocol->requires_benefits)
                    <div style="margin-top: 14px; padding: 10px 14px; background: var(--gold-light); border-radius: 8px; font-size: 13px; color: var(--gold); font-weight: 600; border: 1px solid rgba(201,149,42,0.2);">
                        ⚠️ Imtiyoz va kompensatsiyalar talab etiladi
                    </div>
                @endif
            </div>
        </div>
        @else
        <div style="background: var(--blue-light); border-radius: 10px; padding: 14px 18px; font-size: 13px; color: var(--blue); margin-bottom: 16px;">
            🔬 Laboratoriya hali o'lchov protokolini kiritgani yo'q.
        </div>
        @endif

        {{-- Qaytish --}}
        <a href="{{ route('employee.applications.index') }}" class="btn-att btn-att-secondary">← Arizalar ro'yxatiga</a>
    </div>
</x-app-layout>
