<x-app-layout>
    <x-slot name="header">Ergonomik baholash: {{ $workplace->name }}</x-slot>

    <x-page-header
        title="Solish-ortish ishchisini ergonomik va inson omili bo'yicha baholash"
        subtitle="Yarim tayyor mahsulot / xomashyoni qo'lda yoki mexanizatsiyalashgan holda yuklash-tushirish operatsiyalari uchun (alyuminiy profil ishlab chiqarish korxonalari misolida)."
        :crumbs="[
            ['label' => 'Bosh sahifa', 'url' => route('dashboard')],
            ['label' => 'Ish o‘rinlari o‘lchovi', 'url' => route('laboratory.workplaces.index')],
            ['label' => 'Ergonomik baholash'],
        ]"
    />

    <div class="att-card" style="padding:18px 22px;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
        <div style="font-size:13px;color:var(--muted);max-width:640px;">
            Metodika: <strong>NIOSH (1994) ko'tarish tenglamasi</strong> (jismoniy yuklanish/ko'tarish indeksi),
            <strong>R2.2.2006-05 / SanQvaM 0069-24</strong> uslubidagi og'irlik va zo'riqish darajalari, hamda
            ishchining tajribasi, o'qitilganligi va charchog'iga asoslangan <strong>inson omili xavfi</strong> bali.
        </div>
        <button type="button" class="btn-att btn-att-secondary btn-att-sm" onclick="fillAluminumExample()">
            Namuna bilan to'ldirish (Alyuminiy profil bog'lami)
        </button>
    </div>

    <form action="{{ route('laboratory.ergonomic.store', $workplace) }}" method="POST" id="ergo-form">
        @csrf

        <div class="att-card" style="margin-bottom:16px;">
            <div class="att-card-header"><div class="att-card-title">1. Umumiy ma'lumot</div></div>
            <div class="att-card-body att-form-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
                <div class="att-field">
                    <label>Xodim (ixtiyoriy)</label>
                    <select name="employee_id">
                        <option value="">— Tanlanmagan —</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="att-field">
                    <label>Jinsi <span class="req">*</span></label>
                    <select name="worker_gender" required>
                        <option value="erkak">Erkak</option>
                        <option value="ayol">Ayol</option>
                    </select>
                </div>
                <div class="att-field">
                    <label>Yoshi</label>
                    <input type="number" name="employee_age" min="14" max="80">
                </div>
                <div class="att-field">
                    <label>Ish staji (yil)</label>
                    <input type="number" step="0.1" name="experience_years" min="0" max="60">
                </div>
                <div class="att-field">
                    <label>Mexanizatsiya darajasi <span class="req">*</span></label>
                    <select name="operation_type" required>
                        <option value="qolda">Qo'lda (mexanizatsiyalashmagan)</option>
                        <option value="yarim_mexanizatsiya">Yarim mexanizatsiyalashgan</option>
                        <option value="mexanizatsiya">To'liq mexanizatsiyalashgan</option>
                    </select>
                </div>
                <div class="att-field" style="grid-column:1 / -1;">
                    <label>Yuk tavsifi</label>
                    <input type="text" name="load_description" maxlength="255" placeholder="Masalan: 6 metrli alyuminiy profil bog'lami">
                </div>
            </div>
        </div>

        <div class="att-card" style="margin-bottom:16px;">
            <div class="att-card-header"><div class="att-card-title">2. Yuk ko'tarish parametrlari (NIOSH tenglamasi)</div></div>
            <div class="att-card-body att-form-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
                <div class="att-field">
                    <label>Yuk massasi, kg <span class="req">*</span></label>
                    <input type="number" step="0.1" name="load_mass_kg" min="0.1" max="200" required>
                </div>
                <div class="att-field">
                    <label>Smena davomida ko'tarishlar soni <span class="req">*</span></label>
                    <input type="number" name="lifts_per_shift" min="0" required>
                </div>
                <div class="att-field">
                    <label>Chastota, ko'tarish/daqiqa <span class="req">*</span></label>
                    <input type="number" step="0.1" name="lifts_per_minute" min="0" max="30" required>
                </div>
                <div class="att-field">
                    <label>Smena davomiyligi, soat <span class="req">*</span></label>
                    <input type="number" step="0.5" name="shift_duration_hours" min="1" max="24" value="8" required>
                </div>
                <div class="att-field">
                    <label>Davomiylik toifasi <span class="req">*</span></label>
                    <select name="duration_category" required>
                        <option value="short">Qisqa (≤ 1 soat)</option>
                        <option value="moderate">O'rta (≤ 2 soat)</option>
                        <option value="long" selected>Uzoq (≤ 8 soat)</option>
                    </select>
                </div>
                <div class="att-field">
                    <label>Gorizontal masofa (H), sm <span class="req">*</span></label>
                    <input type="number" step="0.1" name="horizontal_distance_cm" min="0" max="100" required>
                </div>
                <div class="att-field">
                    <label>Vertikal balandlik (V), sm <span class="req">*</span></label>
                    <input type="number" step="0.1" name="vertical_location_cm" min="0" max="200" required>
                </div>
                <div class="att-field">
                    <label>Vertikal harakat masofasi (D), sm <span class="req">*</span></label>
                    <input type="number" step="0.1" name="vertical_travel_cm" min="0" max="250" required>
                </div>
                <div class="att-field">
                    <label>Tana burilish burchagi (A), gradus</label>
                    <input type="number" step="1" name="asymmetry_angle_deg" min="0" max="180" value="0">
                </div>
                <div class="att-field">
                    <label>Yukni ushlash sifati <span class="req">*</span></label>
                    <select name="coupling_quality" required>
                        <option value="good">Yaxshi (dastak/tutqich bor)</option>
                        <option value="fair" selected>O'rtacha</option>
                        <option value="poor">Yomon (silliq/qulaysiz yuzalar)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="att-card" style="margin-bottom:16px;">
            <div class="att-card-header"><div class="att-card-title">3. Og'irlik darajasi: ish holati va statik yuk</div></div>
            <div class="att-card-body att-form-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
                <div class="att-field">
                    <label>Ish holati (poza) <span class="req">*</span></label>
                    <select name="posture_type" required>
                        <option value="erkin">Erkin, qulay</option>
                        <option value="epizodik_noqulay">Epizodik noqulay holat</option>
                        <option value="davriy_noqulay">Davriy noqulay/majburiy holat</option>
                        <option value="majburiy">Ko'p vaqt majburiy holat</option>
                        <option value="qattiq_majburiy">Qattiq majburiy, cheklangan harakat</option>
                    </select>
                </div>
                <div class="att-field">
                    <label>Statik yuk, kgf·s (ixtiyoriy)</label>
                    <input type="number" name="static_load_kgs" min="0">
                </div>
                <div class="att-field">
                    <label>Tana egilishi, smenada marta <span class="req">*</span></label>
                    <input type="number" name="body_inclinations_per_shift" min="0" required>
                </div>
                <div class="att-field">
                    <label>Piyoda yurish masofasi, km/smena <span class="req">*</span></label>
                    <input type="number" step="0.1" name="walking_distance_km" min="0" max="30" required>
                </div>
            </div>
        </div>

        <div class="att-card" style="margin-bottom:16px;">
            <div class="att-card-header"><div class="att-card-title">4. Psixofiziologik zo'riqish</div></div>
            <div class="att-card-body att-form-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
                <div class="att-field">
                    <label>Diqqat konsentratsiyasi, smena vaqtidan % <span class="req">*</span></label>
                    <input type="number" name="attention_concentration_percent" min="0" max="100" required>
                </div>
                <div class="att-field">
                    <label>Soatiga signal/axborot soni <span class="req">*</span></label>
                    <input type="number" name="signals_per_hour" min="0" required>
                </div>
                <div class="att-field">
                    <label>Mas'uliyat darajasi <span class="req">*</span></label>
                    <select name="responsibility_level" required>
                        <option value="ozi_uchun">Faqat o'z ishi uchun</option>
                        <option value="jamoa_uchun">Jamoa natijasi uchun</option>
                        <option value="xavfsizlik_uchun">Boshqalar xavfsizligi uchun</option>
                    </select>
                </div>
                <div class="att-field">
                    <label>Bir xil operatsiyalar soni (monotoniya) <span class="req">*</span></label>
                    <input type="number" name="monotony_operations_count" min="0" required>
                </div>
                <div class="att-field" style="display:flex;align-items:center;gap:8px;padding-top:22px;">
                    <input type="checkbox" name="night_shift" id="night_shift" value="1" style="width:auto;">
                    <label for="night_shift" style="margin:0;">Tungi smenada ishlaydi</label>
                </div>
            </div>
        </div>

        <div class="att-card" style="margin-bottom:16px;">
            <div class="att-card-header"><div class="att-card-title">5. Ish muhiti (alyuminiy profil ishlab chiqarishga xos)</div></div>
            <div class="att-card-body att-form-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
                <div class="att-field">
                    <label>Harorat, °C (masalan, ekstruziya pressi yaqinida)</label>
                    <input type="number" step="0.1" name="temperature_c">
                </div>
                <div class="att-field">
                    <label>Metall changi konsentratsiyasi, mg/m³</label>
                    <input type="number" step="0.01" name="metal_dust_mg_m3" min="0">
                </div>
                <div class="att-field">
                    <label>Shovqin darajasi, dB</label>
                    <input type="number" step="0.1" name="noise_level_db" min="0">
                </div>
                <div class="att-field" style="display:flex;align-items:center;gap:8px;padding-top:22px;">
                    <input type="checkbox" name="uses_ppe" id="uses_ppe" value="1" checked style="width:auto;">
                    <label for="uses_ppe" style="margin:0;">Shaxsiy himoya vositalaridan muntazam foydalanadi</label>
                </div>
            </div>
        </div>

        <div class="att-card" style="margin-bottom:16px;">
            <div class="att-card-header"><div class="att-card-title">6. Inson omili</div></div>
            <div class="att-card-body att-form-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
                <div class="att-field" style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="training_completed" id="training_completed" value="1" style="width:auto;">
                    <label for="training_completed" style="margin:0;">Mehnat xavfsizligi bo'yicha o'qitishdan o'tgan</label>
                </div>
                <div class="att-field">
                    <label>Oxirgi o'qitish sanasi</label>
                    <input type="date" name="last_training_at">
                </div>
                <div class="att-field">
                    <label>O'z-o'zini charchoq bahosi (1–5) <span class="req">*</span></label>
                    <select name="fatigue_self_score" required>
                        <option value="1">1 — charchamagan</option>
                        <option value="2">2</option>
                        <option value="3">3 — o'rtacha</option>
                        <option value="4">4</option>
                        <option value="5">5 — juda charchagan</option>
                    </select>
                </div>
                <div class="att-field">
                    <label>Sog'liq holati guruhi <span class="req">*</span></label>
                    <select name="health_group" required>
                        <option value="soglom">Sog'lom</option>
                        <option value="cheklangan">Tibbiy cheklovlar mavjud</option>
                        <option value="nogironligi_bor">Nogironligi bor</option>
                    </select>
                </div>
                <div class="att-field">
                    <label>Oldingi baxtsiz hodisalar / near-miss soni <span class="req">*</span></label>
                    <input type="number" name="prior_incidents_count" min="0" value="0" required>
                </div>
            </div>
        </div>

        <div class="att-card" style="margin-bottom:16px;">
            <div class="att-card-body">
                <div class="att-field">
                    <label>Qo'shimcha izoh</label>
                    <textarea name="notes" rows="3"></textarea>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;">
            <a href="{{ route('laboratory.ergonomic.index', $workplace) }}" class="btn-att btn-att-ghost">Bekor qilish</a>
            <button type="submit" class="btn-att btn-att-primary">Hisoblash va saqlash</button>
        </div>
    </form>

    @push('scripts')
    <script>
        function fillAluminumExample() {
            const values = {
                worker_gender: 'erkak',
                employee_age: 34,
                experience_years: 4,
                operation_type: 'qolda',
                load_description: "6 metrli alyuminiy profil bog'lami (ekstruziya sexidan omborga)",
                load_mass_kg: 22,
                lifts_per_shift: 180,
                lifts_per_minute: 4,
                shift_duration_hours: 8,
                duration_category: 'long',
                horizontal_distance_cm: 30,
                vertical_location_cm: 75,
                vertical_travel_cm: 60,
                asymmetry_angle_deg: 30,
                coupling_quality: 'fair',
                posture_type: 'davriy_noqulay',
                static_load_kgs: 25000,
                body_inclinations_per_shift: 150,
                walking_distance_km: 6,
                attention_concentration_percent: 40,
                signals_per_hour: 60,
                responsibility_level: 'jamoa_uchun',
                monotony_operations_count: 5,
                temperature_c: 34,
                metal_dust_mg_m3: 5,
                noise_level_db: 82,
                fatigue_self_score: 3,
                health_group: 'soglom',
                prior_incidents_count: 0,
            };

            const form = document.getElementById('ergo-form');
            Object.entries(values).forEach(([name, value]) => {
                const field = form.elements[name];
                if (!field) return;
                field.value = value;
            });
            form.elements['night_shift'].checked = false;
            form.elements['uses_ppe'].checked = true;
            form.elements['training_completed'].checked = true;
        }
    </script>
    @endpush
</x-app-layout>
