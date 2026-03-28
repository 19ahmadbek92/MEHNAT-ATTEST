<x-app-layout>
    <x-slot name="header">🔬 Ish o'rnini tekshirish: #{{ $application->id }}</x-slot>

    <div style="max-width: 900px;">
        <div class="att-card">
            <div style="background: var(--ink); padding: 24px; border-radius: 12px 12px 0 0;">
                <h3 style="font-family: 'DM Serif Display', serif; font-size: 22px; color: white; margin: 0 0 4px;">
                    {{ $application->workplace_name ?? $application->position }}
                </h3>
                <p style="color: rgba(255,255,255,0.4); font-size: 13px; margin: 0;">O'lchov natijalarini va komissiya xulosasini kiritish</p>
            </div>

            <div class="att-card-body">
                {{-- Ariza ma'lumotlari --}}
                <div style="background: var(--cream); border-radius: 12px; padding: 20px; margin-bottom: 28px;">
                    <div style="font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 14px;">Ariza ma'lumotlari</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed var(--border); font-size: 14px;">
                            <span style="color: var(--muted);">Tashkilot:</span>
                            <span style="font-weight: 700;">{{ $application->user?->name }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed var(--border); font-size: 14px;">
                            <span style="color: var(--muted);">Bo'lim:</span>
                            <span style="font-weight: 700;">{{ $application->department ?? '—' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed var(--border); font-size: 14px;">
                            <span style="color: var(--muted);">Xodimlar:</span>
                            <span style="font-weight: 700;">{{ $application->employee_count ?? '—' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed var(--border); font-size: 14px;">
                            <span style="color: var(--muted);">Kampaniya:</span>
                            <span style="font-weight: 700;">{{ $application->campaign?->title }}</span>
                        </div>
                    </div>

                    @if($application->hazard_factors)
                        <div style="margin-top: 14px; padding: 12px; background: var(--gold-light); border-radius: 8px; border: 1px solid rgba(201,149,42,0.2);">
                            <div style="font-size: 10px; font-weight: 700; color: var(--gold); text-transform: uppercase; margin-bottom: 6px;">E'lon qilingan xavfli omillar</div>
                            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                @foreach($application->hazard_factors as $factor)
                                    <span style="font-size: 11px; font-weight: 600; padding: 3px 10px; background: rgba(201,149,42,0.15); border-radius: 4px; color: var(--gold); text-transform: uppercase;">{{ $factor }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Tekshiruv formasi --}}
                <form method="POST" action="{{ route('commission.evaluations.store', $application) }}">
                    @csrf

                    @php $existing = $application->evaluations->firstWhere('evaluator_id', auth()->id()); @endphp

                    {{-- O'lchov ko'rsatkichlari --}}
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                        <div style="width: 4px; height: 32px; background: var(--teal); border-radius: 4px;"></div>
                        <h4 style="font-size: 16px; font-weight: 700; text-transform: uppercase; margin: 0;">O'lchov ko'rsatkichlari</h4>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 28px;">
                        @foreach([['noise_level', 'Shovqin', 'dB'], ['dust_level', 'Chang', 'mg/m³'], ['vibration_level', 'Tebranish', 'm/s²'], ['lighting_level', 'Yoritilganlik', 'lux']] as [$name, $label, $unit])
                        <div style="background: var(--cream); padding: 16px; border-radius: 12px; text-align: center;">
                            <div style="font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 8px;">{{ $label }} ({{ $unit }})</div>
                            <input name="{{ $name }}" type="number" step="0.01" min="0"
                                   value="{{ old($name, $existing?->$name) }}"
                                   placeholder="0.00"
                                   style="width: 100%; background: var(--white); border: 1.5px solid var(--border); border-radius: 8px; padding: 10px; text-align: center; font-size: 18px; font-weight: 700; font-family: 'DM Sans', sans-serif; color: var(--ink); outline: none;"
                                   onfocus="this.style.borderColor='var(--teal)'" onblur="this.style.borderColor='var(--border)'">
                        </div>
                        @endforeach
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; margin-bottom: 28px;">
                        <div class="att-field">
                            <label>Mikroiqlim</label>
                            <input type="text" name="microclimate" value="{{ old('microclimate', $existing?->microclimate) }}" placeholder="28°C / 65%">
                        </div>
                        <div class="att-field">
                            <label>Uskunalar xavfliligi (0-100)</label>
                            <input type="number" name="equipment_hazard_score" min="0" max="100" value="{{ old('equipment_hazard_score', $existing?->equipment_hazard_score) }}">
                        </div>
                        <div class="att-field">
                            <label>Himoya vositalari</label>
                            <select name="protective_equipment_status">
                                <option value="">-- Tanlang --</option>
                                <option value="yetarli" @selected(old('protective_equipment_status', $existing?->protective_equipment_status) === 'yetarli')>✅ Yetarli</option>
                                <option value="qisman" @selected(old('protective_equipment_status', $existing?->protective_equipment_status) === 'qisman')>⚠️ Qisman</option>
                                <option value="yetarli_emas" @selected(old('protective_equipment_status', $existing?->protective_equipment_status) === 'yetarli_emas')>❌ Yetarli emas</option>
                            </select>
                        </div>
                    </div>

                    {{-- Komissiya xulosasi --}}
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                        <div style="width: 4px; height: 32px; background: var(--gold); border-radius: 4px;"></div>
                        <h4 style="font-size: 16px; font-weight: 700; text-transform: uppercase; margin: 0;">Komissiya xulosasi</h4>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 28px;">
                        <div style="background: var(--gold-light); padding: 24px; border-radius: 12px; text-align: center; border: 2px solid rgba(201,149,42,0.2);">
                            <div style="font-size: 11px; font-weight: 700; color: var(--gold); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">Umumiy ball *</div>
                            <input name="score" type="number" min="0" max="100"
                                   value="{{ old('score', $existing?->score) }}" required
                                   style="width: 100%; background: var(--white); border: 2px solid var(--gold); border-radius: 10px; padding: 12px; text-align: center; font-size: 28px; font-weight: 900; font-family: 'DM Serif Display', serif; color: var(--ink); outline: none;">
                            <div style="font-size: 10px; color: var(--gold); margin-top: 6px; font-weight: 600; text-transform: uppercase;">0 dan 100 gacha</div>
                        </div>
                        <div class="att-field">
                            <label>Batafsil izoh va xulosa</label>
                            <textarea name="comment" rows="5" placeholder="O'lchovlar va uskunalar holati bo'yicha yakuniy fikrni yozing..." style="height: 100%;">{{ old('comment', $existing?->comment) }}</textarea>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid var(--border);">
                        <a href="{{ route('commission.evaluations.index') }}" class="btn-att btn-att-secondary">← Bekor qilish</a>
                        <button type="submit" class="btn-att btn-att-primary">✓ Natijalarni saqlash</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            div[style*="grid-template-columns: repeat(4"] { grid-template-columns: 1fr 1fr !important; }
            div[style*="grid-template-columns: 1fr 2fr"] { grid-template-columns: 1fr !important; }
            div[style*="grid-template-columns: 1fr 1fr 1fr"] { grid-template-columns: 1fr !important; }
        }
    </style>
</x-app-layout>
