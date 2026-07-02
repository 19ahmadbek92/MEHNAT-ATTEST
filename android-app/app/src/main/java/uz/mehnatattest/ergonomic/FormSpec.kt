package uz.mehnatattest.ergonomic

enum class FieldKind { ENTRY, CHECK, COMBO, TEXT }

data class FieldSpec(
    val key: String,
    val label: String,
    val kind: FieldKind,
    val options: List<Pair<String, String>> = emptyList(),
)

/** Kirish formasi bo'limlari — desktop-app/app.py dagi FIELD_SECTIONS bilan bir xil tarkib. */
object FormSpec {

    val GENDER_OPTIONS = listOf("erkak" to "Erkak", "ayol" to "Ayol")
    val OPERATION_OPTIONS = listOf(
        "qolda" to "Qo'lda (mexanizatsiyalashmagan)",
        "yarim_mexanizatsiya" to "Yarim mexanizatsiyalashgan",
        "mexanizatsiya" to "To'liq mexanizatsiyalashgan",
    )
    val DURATION_OPTIONS = listOf(
        "short" to "Qisqa (<= 1 soat)",
        "moderate" to "O'rta (<= 2 soat)",
        "long" to "Uzoq (<= 8 soat)",
    )
    val COUPLING_OPTIONS = listOf(
        "good" to "Yaxshi (dastak/tutqich bor)",
        "fair" to "O'rtacha",
        "poor" to "Yomon (silliq/qulaysiz yuzalar)",
    )
    val POSTURE_OPTIONS = listOf(
        "erkin" to "Erkin, qulay",
        "epizodik_noqulay" to "Epizodik noqulay holat",
        "davriy_noqulay" to "Davriy noqulay/majburiy holat",
        "majburiy" to "Ko'p vaqt majburiy holat",
        "qattiq_majburiy" to "Qattiq majburiy, cheklangan harakat",
    )
    val RESPONSIBILITY_OPTIONS = listOf(
        "ozi_uchun" to "Faqat o'z ishi uchun",
        "jamoa_uchun" to "Jamoa natijasi uchun",
        "xavfsizlik_uchun" to "Boshqalar xavfsizligi uchun",
    )
    val FATIGUE_OPTIONS = listOf(
        "1" to "1 — charchamagan",
        "2" to "2",
        "3" to "3 — o'rtacha",
        "4" to "4",
        "5" to "5 — juda charchagan",
    )
    val HEALTH_OPTIONS = listOf(
        "soglom" to "Sog'lom",
        "cheklangan" to "Tibbiy cheklovlar mavjud",
        "nogironligi_bor" to "Nogironligi bor",
    )

    val SECTIONS: List<Pair<String, List<FieldSpec>>> = listOf(
        "1. Umumiy ma'lumot" to listOf(
            FieldSpec("employee_name", "Xodim F.I.Sh. (ixtiyoriy)", FieldKind.ENTRY),
            FieldSpec("worker_gender", "Jinsi", FieldKind.COMBO, GENDER_OPTIONS),
            FieldSpec("employee_age", "Yoshi", FieldKind.ENTRY),
            FieldSpec("experience_years", "Ish staji (yil)", FieldKind.ENTRY),
            FieldSpec("operation_type", "Mexanizatsiya darajasi", FieldKind.COMBO, OPERATION_OPTIONS),
            FieldSpec("load_description", "Yuk tavsifi", FieldKind.ENTRY),
        ),
        "2. Yuk ko'tarish parametrlari (NIOSH tenglamasi)" to listOf(
            FieldSpec("load_mass_kg", "Yuk massasi, kg", FieldKind.ENTRY),
            FieldSpec("lifts_per_shift", "Smena davomida ko'tarishlar soni", FieldKind.ENTRY),
            FieldSpec("lifts_per_minute", "Chastota, ko'tarish/daqiqa", FieldKind.ENTRY),
            FieldSpec("shift_duration_hours", "Smena davomiyligi, soat", FieldKind.ENTRY),
            FieldSpec("duration_category", "Davomiylik toifasi", FieldKind.COMBO, DURATION_OPTIONS),
            FieldSpec("horizontal_distance_cm", "Gorizontal masofa (H), sm", FieldKind.ENTRY),
            FieldSpec("vertical_location_cm", "Vertikal balandlik (V), sm", FieldKind.ENTRY),
            FieldSpec("vertical_travel_cm", "Vertikal harakat masofasi (D), sm", FieldKind.ENTRY),
            FieldSpec("asymmetry_angle_deg", "Tana burilish burchagi (A), gradus", FieldKind.ENTRY),
            FieldSpec("coupling_quality", "Yukni ushlash sifati", FieldKind.COMBO, COUPLING_OPTIONS),
        ),
        "3. Og'irlik darajasi: ish holati va statik yuk" to listOf(
            FieldSpec("posture_type", "Ish holati (poza)", FieldKind.COMBO, POSTURE_OPTIONS),
            FieldSpec("static_load_kgs", "Statik yuk, kgf·s (ixtiyoriy)", FieldKind.ENTRY),
            FieldSpec("body_inclinations_per_shift", "Tana egilishi, smenada marta", FieldKind.ENTRY),
            FieldSpec("walking_distance_km", "Piyoda yurish masofasi, km/smena", FieldKind.ENTRY),
        ),
        "4. Psixofiziologik zo'riqish" to listOf(
            FieldSpec("attention_concentration_percent", "Diqqat konsentratsiyasi, %", FieldKind.ENTRY),
            FieldSpec("signals_per_hour", "Soatiga signal/axborot soni", FieldKind.ENTRY),
            FieldSpec("responsibility_level", "Mas'uliyat darajasi", FieldKind.COMBO, RESPONSIBILITY_OPTIONS),
            FieldSpec("monotony_operations_count", "Bir xil operatsiyalar soni (monotoniya)", FieldKind.ENTRY),
            FieldSpec("night_shift", "Tungi smenada ishlaydi", FieldKind.CHECK),
        ),
        "5. Ish muhiti (alyuminiy profil ishlab chiqarishga xos)" to listOf(
            FieldSpec("temperature_c", "Harorat, °C", FieldKind.ENTRY),
            FieldSpec("metal_dust_mg_m3", "Metall changi konsentratsiyasi, mg/m³", FieldKind.ENTRY),
            FieldSpec("noise_level_db", "Shovqin darajasi, dB", FieldKind.ENTRY),
            FieldSpec("uses_ppe", "Shaxsiy himoya vositalaridan foydalanadi", FieldKind.CHECK),
        ),
        "6. Inson omili" to listOf(
            FieldSpec("training_completed", "Mehnat xavfsizligi bo'yicha o'qitishdan o'tgan", FieldKind.CHECK),
            FieldSpec("last_training_at", "Oxirgi o'qitish sanasi (yyyy-MM-dd, ixtiyoriy)", FieldKind.ENTRY),
            FieldSpec("fatigue_self_score", "O'z-o'zini charchoq bahosi", FieldKind.COMBO, FATIGUE_OPTIONS),
            FieldSpec("health_group", "Sog'liq holati guruhi", FieldKind.COMBO, HEALTH_OPTIONS),
            FieldSpec("prior_incidents_count", "Oldingi baxtsiz hodisalar soni", FieldKind.ENTRY),
        ),
        "Qo'shimcha" to listOf(
            FieldSpec("notes", "Izoh", FieldKind.TEXT),
        ),
    )

    val DEFAULTS: Map<String, String> = mapOf(
        "worker_gender" to "erkak",
        "operation_type" to "qolda",
        "duration_category" to "long",
        "coupling_quality" to "fair",
        "posture_type" to "erkin",
        "responsibility_level" to "ozi_uchun",
        "fatigue_self_score" to "1",
        "health_group" to "soglom",
        "shift_duration_hours" to "8",
        "prior_incidents_count" to "0",
        "body_inclinations_per_shift" to "0",
        "walking_distance_km" to "0",
        "attention_concentration_percent" to "0",
        "signals_per_hour" to "0",
        "monotony_operations_count" to "0",
    )
    val DEFAULT_CHECKS: Map<String, Boolean> = mapOf(
        "uses_ppe" to true,
        "training_completed" to false,
        "night_shift" to false,
    )

    val ALUMINUM_EXAMPLE: Map<String, String> = mapOf(
        "employee_name" to "Aliyev Anvar",
        "worker_gender" to "erkak",
        "employee_age" to "34",
        "experience_years" to "4",
        "operation_type" to "qolda",
        "load_description" to "6 metrli alyuminiy profil bog'lami (ekstruziya sexidan omborga)",
        "load_mass_kg" to "22",
        "lifts_per_shift" to "180",
        "lifts_per_minute" to "4",
        "shift_duration_hours" to "8",
        "duration_category" to "long",
        "horizontal_distance_cm" to "30",
        "vertical_location_cm" to "75",
        "vertical_travel_cm" to "60",
        "asymmetry_angle_deg" to "30",
        "coupling_quality" to "fair",
        "posture_type" to "davriy_noqulay",
        "static_load_kgs" to "25000",
        "body_inclinations_per_shift" to "150",
        "walking_distance_km" to "6",
        "attention_concentration_percent" to "40",
        "signals_per_hour" to "60",
        "responsibility_level" to "jamoa_uchun",
        "monotony_operations_count" to "5",
        "temperature_c" to "34",
        "metal_dust_mg_m3" to "5",
        "noise_level_db" to "82",
        "fatigue_self_score" to "3",
        "health_group" to "soglom",
        "prior_incidents_count" to "0",
    )
    val ALUMINUM_EXAMPLE_CHECKS: Map<String, Boolean> = mapOf(
        "uses_ppe" to true,
        "training_completed" to true,
        "night_shift" to false,
    )
}
