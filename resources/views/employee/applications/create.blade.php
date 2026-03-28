<x-app-layout>
    <x-slot name="header">📝 Ish o'rnini attestatsiyaga taqdim etish</x-slot>

    <div style="max-width: 900px;">
        <div class="att-card">
            <div style="background: var(--ink); padding: 24px; border-radius: 12px 12px 0 0;">
                <h3 style="font-family: 'DM Serif Display', serif; font-size: 22px; color: white; margin: 0 0 4px;">Yangi ariza yuborish</h3>
                <p style="color: rgba(255,255,255,0.4); font-size: 13px; margin: 0;">Vazirlar Mahkamasining 263-sonli qarori talablari asosida</p>
            </div>

            <form method="POST" action="{{ route('employee.applications.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="att-card-body">
                    {{-- 1-qadam: Asosiy ma'lumotlar --}}
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                        <div style="width: 4px; height: 32px; background: var(--teal); border-radius: 4px;"></div>
                        <h4 style="font-size: 16px; font-weight: 700; text-transform: uppercase; margin: 0;">Asosiy ma'lumotlar</h4>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 32px;">
                        <div class="att-field" style="grid-column: span 2;">
                            <label>Attestatsiya kampaniyasi *</label>
                            <select name="campaign_id" required>
                                <option value="">-- Kampaniyani tanlang --</option>
                                @foreach ($openCampaigns as $campaign)
                                    <option value="{{ $campaign->id }}" @selected(old('campaign_id') == $campaign->id)>{{ $campaign->title }}</option>
                                @endforeach
                            </select>
                            @error('campaign_id') <p style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>

                        <div class="att-field">
                            <label>Ish o'rni nomi *</label>
                            <input type="text" name="workplace_name" value="{{ old('workplace_name') }}" required placeholder="Masalan: Payvandchi ish o'rni">
                            @error('workplace_name') <p style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>

                        <div class="att-field">
                            <label>Bo'lim / Sex</label>
                            <input type="text" name="department" value="{{ old('department') }}" placeholder="Masalan: Issiqlik sexi">
                        </div>

                        <div class="att-field">
                            <label>Xodimlar soni</label>
                            <input type="number" name="employee_count" min="1" value="{{ old('employee_count') }}" placeholder="0">
                        </div>

                        <div class="att-field">
                            <label>Ish o'rni tavsifi</label>
                            <textarea name="workplace_description" rows="3" placeholder="Ish jarayoni va sharoitlar haqida qisqacha">{{ old('workplace_description') }}</textarea>
                        </div>
                    </div>

                    {{-- 2-qadam: Xavfli omillar --}}
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                        <div style="width: 4px; height: 32px; background: var(--gold); border-radius: 4px;"></div>
                        <h4 style="font-size: 16px; font-weight: 700; text-transform: uppercase; margin: 0;">Xavfli omillar va uskunalar</h4>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 32px;">
                        <div style="background: var(--cream); padding: 20px; border-radius: 12px;">
                            <div style="font-size: 11px; font-weight: 700; color: var(--teal); letter-spacing: 1px; text-transform: uppercase; margin-bottom: 12px;">Mavjud xavfli va zararli omillar</div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                @foreach(['shovqin' => 'Shovqin', 'tebranish' => 'Tebranish', 'chang' => 'Chang', 'kimyoviy' => 'Kimyoviy moddalar', 'yoritilganlik' => 'Yoritilganlik', 'mikroiqlim' => 'Mikroiqlim', 'nurlanish' => 'Nurlanish', 'biologik' => 'Biologik'] as $key => $label)
                                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer; font-weight: 500;">
                                        <input type="checkbox" name="hazard_factors[]" value="{{ $key }}"
                                               style="accent-color: var(--teal); width: 16px; height: 16px;"
                                               @checked(is_array(old('hazard_factors')) && in_array($key, old('hazard_factors')))>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            <div class="att-field">
                                <label>Asbob-uskunalar ro'yxati</label>
                                <textarea name="equipment_list" rows="2" placeholder="Uskunalar to'liq ro'yxati">{{ old('equipment_list') }}</textarea>
                            </div>
                            <div class="att-field">
                                <label>Shaxsiy himoya vositalari</label>
                                <textarea name="protective_equipment" rows="2" placeholder="Xodimlarga berilgan vositalar">{{ old('protective_equipment') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 3-qadam: Hujjatlar --}}
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                        <div style="width: 4px; height: 32px; background: var(--green); border-radius: 4px;"></div>
                        <h4 style="font-size: 16px; font-weight: 700; text-transform: uppercase; margin: 0;">Hujjatlar va suratlar</h4>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 32px;">
                        <div class="upload-zone" style="position: relative;">
                            <div style="font-size: 28px; margin-bottom: 8px;">📸</div>
                            <div style="font-size: 14px; font-weight: 600; margin-bottom: 4px;">Ish o'rni surati</div>
                            <div style="font-size: 12px; color: var(--muted);">JPG, PNG · maks 10MB</div>
                            <input type="file" name="workplace_photo" accept="image/*" style="position: absolute; inset: 0; opacity: 0; cursor: pointer;">
                        </div>
                        <div class="upload-zone" style="position: relative;">
                            <div style="font-size: 28px; margin-bottom: 8px;">📄</div>
                            <div style="font-size: 14px; font-weight: 600; margin-bottom: 4px;">Hujjatlar</div>
                            <div style="font-size: 12px; color: var(--muted);">PDF, DOC · maks 20MB</div>
                            <input type="file" name="documents" accept=".pdf,.doc,.docx" style="position: absolute; inset: 0; opacity: 0; cursor: pointer;">
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid var(--border);">
                        <a href="{{ route('employee.applications.index') }}" class="btn-att btn-att-secondary">← Bekor qilish</a>
                        <button type="submit" class="btn-att btn-att-primary">✓ Arizani yuborish</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            div[style*="grid-template-columns: 1fr 1fr"] { grid-template-columns: 1fr !important; }
        }
    </style>
</x-app-layout>
