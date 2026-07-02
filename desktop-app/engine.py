"""
Yarim tayyor mahsulot va xomashyoni qo'lda solish-ortish (yuklash-tushirish)
ishlarida band ishchilarning mehnat xavfsizligini baholovchi hisoblash mexanizmi:
 - jismoniy (ergonomik) yuklanish - NIOSH (1994) ko'tarish tenglamasi + R2.2.2006-05
   uslubiyatiga asoslangan og'irlik darajasi ko'rsatkichlari;
 - psixofiziologik zo'riqish ko'rsatkichlari;
 - inson omili (tajriba, o'qitilganlik, charchoq, sog'liq holati) xavfi;
 - alyuminiy profil ishlab chiqarishga xos ish muhiti omillari (issiqlik, metall changi, shovqin).

Natija: SanQvaM 0069-24 uslubidagi xavf klasslari (1 / 2 / 3.1-3.4 / 4) va
0-100 oralig'idagi integral xavfsizlik ko'rsatkichi.

Bu modul MEHNAT-ATTEST veb-platformasidagi App\\Services\\ErgonomicAssessmentService
(PHP) bilan bir xil metodologiya va formulalarga asoslanadi, shu sabab ikkala
dastur bir xil kirish ma'lumotlari uchun bir xil natija beradi.
"""

from __future__ import annotations

from datetime import date, datetime
from typing import Any

LC = 23.0  # NIOSH load constant (kg)

# Ayollar uchun O'zbekiston Mehnat kodeksi bo'yicha qo'lda ko'tarish chegarasi (kg).
WOMEN_CONTINUOUS_LIMIT_KG = 10.0
WOMEN_ALTERNATING_LIMIT_KG = 15.0

# NIOSH (1994) chastota multiplikatori jadvali (Table 5), soddalashtirilgan:
# {duration_category: [(freq, {'lt75': ..., 'ge75': ...}), ...]}  (o'sish tartibida)
FREQUENCY_TABLE: dict[str, list[tuple[float, dict[str, float]]]] = {
    "short": [  # <= 1 soat
        (0.2, {"lt75": 1.00, "ge75": 1.00}),
        (0.5, {"lt75": 0.97, "ge75": 0.97}),
        (1, {"lt75": 0.94, "ge75": 0.94}),
        (2, {"lt75": 0.91, "ge75": 0.91}),
        (3, {"lt75": 0.88, "ge75": 0.88}),
        (4, {"lt75": 0.84, "ge75": 0.84}),
        (5, {"lt75": 0.80, "ge75": 0.80}),
        (6, {"lt75": 0.75, "ge75": 0.75}),
        (7, {"lt75": 0.70, "ge75": 0.70}),
        (8, {"lt75": 0.60, "ge75": 0.60}),
        (9, {"lt75": 0.52, "ge75": 0.52}),
        (10, {"lt75": 0.45, "ge75": 0.45}),
        (11, {"lt75": 0.41, "ge75": 0.41}),
        (12, {"lt75": 0.37, "ge75": 0.37}),
        (13, {"lt75": 0.00, "ge75": 0.34}),
        (14, {"lt75": 0.00, "ge75": 0.31}),
        (15, {"lt75": 0.00, "ge75": 0.28}),
    ],
    "moderate": [  # <= 2 soat
        (0.2, {"lt75": 0.95, "ge75": 0.95}),
        (0.5, {"lt75": 0.92, "ge75": 0.92}),
        (1, {"lt75": 0.88, "ge75": 0.88}),
        (2, {"lt75": 0.84, "ge75": 0.84}),
        (3, {"lt75": 0.79, "ge75": 0.79}),
        (4, {"lt75": 0.72, "ge75": 0.72}),
        (5, {"lt75": 0.60, "ge75": 0.60}),
        (6, {"lt75": 0.50, "ge75": 0.50}),
        (7, {"lt75": 0.42, "ge75": 0.42}),
        (8, {"lt75": 0.35, "ge75": 0.35}),
        (9, {"lt75": 0.30, "ge75": 0.30}),
        (10, {"lt75": 0.26, "ge75": 0.26}),
        (11, {"lt75": 0.00, "ge75": 0.23}),
        (12, {"lt75": 0.00, "ge75": 0.21}),
    ],
    "long": [  # <= 8 soat
        (0.2, {"lt75": 0.85, "ge75": 0.85}),
        (0.5, {"lt75": 0.81, "ge75": 0.81}),
        (1, {"lt75": 0.75, "ge75": 0.75}),
        (2, {"lt75": 0.65, "ge75": 0.65}),
        (3, {"lt75": 0.55, "ge75": 0.55}),
        (4, {"lt75": 0.45, "ge75": 0.45}),
        (5, {"lt75": 0.35, "ge75": 0.35}),
        (6, {"lt75": 0.27, "ge75": 0.27}),
        (7, {"lt75": 0.22, "ge75": 0.22}),
        (8, {"lt75": 0.18, "ge75": 0.18}),
        (9, {"lt75": 0.15, "ge75": 0.15}),
        (10, {"lt75": 0.13, "ge75": 0.13}),
    ],
}


def evaluate(data: dict[str, Any]) -> dict[str, Any]:
    rwl, lifting_index = _calculate_niosh(data)
    severity_class, severity_score = _classify_severity(data, lifting_index)
    strain_class, strain_score = _classify_strain(data)
    human_factor_score = _score_human_factor(data)
    env_penalty = _environmental_penalty(data)

    integral = _integral_safety_index(severity_score, strain_score, human_factor_score, env_penalty)
    risk_category = _risk_category_label(integral)

    recommendations = _build_recommendations(
        data, rwl, lifting_index, severity_class, strain_class, human_factor_score
    )

    return {
        "rwl_kg": rwl,
        "lifting_index": lifting_index,
        "severity_class": severity_class,
        "strain_class": strain_class,
        "human_factor_risk_score": human_factor_score,
        "integral_safety_index": integral,
        "risk_category": risk_category,
        "recommendations": recommendations,
    }


def _calculate_niosh(data: dict[str, Any]) -> tuple[float, float]:
    """NIOSH (1994) ko'tarish tenglamasi: RWL = LC x HM x VM x DM x AM x FM x CM."""
    h = max(float(data["horizontal_distance_cm"]), 25.0)
    v = min(max(float(data["vertical_location_cm"]), 0.0), 175.0)
    d = max(float(data["vertical_travel_cm"]), 25.0)
    a = float(data.get("asymmetry_angle_deg") or 0)

    hm = 0.0 if h > 63 else 25 / h
    vm = 1 - (0.003 * abs(v - 75))
    dm = 0.0 if d > 175 else 0.82 + (4.5 / d)
    am = 0.0 if a > 135 else 1 - (0.0032 * a)
    fm = _frequency_multiplier(float(data.get("lifts_per_minute") or 0), str(data.get("duration_category") or "long"), v)
    cm = _coupling_multiplier(str(data.get("coupling_quality") or "fair"), v)

    rwl = LC * hm * vm * dm * am * fm * cm
    rwl = round(max(rwl, 0), 2)

    mass = float(data["load_mass_kg"])
    li = round(mass / rwl, 2) if rwl > 0 else 99.99

    return rwl, li


def _frequency_multiplier(f: float, duration: str, v: float) -> float:
    column = "lt75" if v < 75 else "ge75"
    rows = FREQUENCY_TABLE.get(duration, FREQUENCY_TABLE["long"])

    for freq, values in rows:
        if f <= freq:
            return values[column]

    return 0.0


def _coupling_multiplier(quality: str, v: float) -> float:
    if quality == "good":
        return 1.00
    if quality == "poor":
        return 0.90
    return 0.95 if v < 75 else 1.00  # fair


def _classify_severity(data: dict[str, Any], lifting_index: float) -> tuple[str, int]:
    """Og'irlik darajasi (jismoniy yuklanish) klassini eng noqulay ko'rsatkich bo'yicha aniqlaydi."""
    scores = [_lifting_index_score(lifting_index)]

    static_load = data.get("static_load_kgs")
    if static_load:
        scores.append(_bracket_score(float(static_load), [43000, 97000, 208000, 245000]))

    scores.append(
        {
            "erkin": 0,
            "epizodik_noqulay": 1,
            "davriy_noqulay": 2,
            "majburiy": 3,
            "qattiq_majburiy": 4,
        }.get(data.get("posture_type", "erkin"), 1)
    )

    scores.append(_bracket_score(float(data.get("body_inclinations_per_shift") or 0), [50, 100, 300, 300]))
    scores.append(_bracket_score(float(data.get("walking_distance_km") or 0), [4, 10, 15, 15]))

    if data.get("worker_gender", "erkak") == "ayol":
        mass = float(data["load_mass_kg"])
        if mass > WOMEN_ALTERNATING_LIMIT_KG:
            scores.append(5)
        elif mass > WOMEN_CONTINUOUS_LIMIT_KG:
            scores.append(3)

    worst = max(scores)

    return _score_to_class_label(worst), worst


def _lifting_index_score(li: float) -> int:
    if li <= 0.5:
        return 0
    if li <= 1.0:
        return 1
    if li <= 1.5:
        return 2
    if li <= 2.0:
        return 3
    if li <= 4.0:
        return 4
    return 5


def _bracket_score(value: float, limits: list[float]) -> int:
    if value <= limits[0]:
        return 0
    if value <= limits[1]:
        return 1
    if value <= limits[2]:
        return 2
    if value <= limits[3]:
        return 3
    return 4


def _score_to_class_label(score: int) -> str:
    return {
        0: "1",
        1: "2",
        2: "3.1",
        3: "3.2",
        4: "3.3",
    }.get(score, "4")


def _classify_strain(data: dict[str, Any]) -> tuple[str, int]:
    """Zo'riqish (psixofiziologik yuklanish) klassi."""
    scores = [
        _bracket_score(float(data.get("attention_concentration_percent") or 0), [25, 50, 75, 75]),
        _bracket_score(float(data.get("signals_per_hour") or 0), [75, 175, 300, 300]),
        _bracket_score(float(data.get("monotony_operations_count") or 0), [10, 6, 4, 4]),
        {
            "ozi_uchun": 0,
            "jamoa_uchun": 2,
            "xavfsizlik_uchun": 3,
        }.get(data.get("responsibility_level", "ozi_uchun"), 1),
    ]

    shift = float(data.get("shift_duration_hours") or 8)
    if shift > 12:
        scores.append(4)
    elif shift > 8:
        scores.append(2)

    if data.get("night_shift"):
        scores.append(2)

    worst = max(scores)

    return _score_to_class_label(worst), worst


def _score_human_factor(data: dict[str, Any]) -> int:
    """Inson omili xavf balli (0-100, kattaroq = xavfliroq)."""
    score = 0

    experience = float(data.get("experience_years") or 0)
    if experience < 1:
        score += 25
    elif experience < 3:
        score += 15
    elif experience < 5:
        score += 5

    last_training_at = data.get("last_training_at")
    if not data.get("training_completed"):
        score += 20
    elif last_training_at and _months_since(last_training_at) > 12:
        score += 10

    fatigue = int(data.get("fatigue_self_score") or 1)
    score += max(0, fatigue - 1) * 5

    age = int(data.get("employee_age") or 30)
    if age and (age < 18 or age > 55):
        score += 10

    if data.get("health_group", "soglom") in ("cheklangan", "nogironligi_bor"):
        score += 15

    incidents = int(data.get("prior_incidents_count") or 0)
    score += min(incidents * 10, 30)

    return min(int(score), 100)


def _months_since(value: date | str) -> int:
    if isinstance(value, str):
        value = datetime.strptime(value, "%Y-%m-%d").date()
    today = date.today()
    return (today.year - value.year) * 12 + (today.month - value.month)


def _environmental_penalty(data: dict[str, Any]) -> int:
    penalty = 0

    temp = data.get("temperature_c")
    if temp is not None and (float(temp) < 10 or float(temp) > 32):
        penalty += 8

    dust = data.get("metal_dust_mg_m3")
    if dust is not None and float(dust) > 6:
        penalty += 8

    noise = data.get("noise_level_db")
    if noise is not None and float(noise) > 80:
        penalty += 6

    if not data.get("uses_ppe"):
        penalty += 10

    return penalty


def _integral_safety_index(severity_score: int, strain_score: int, human_factor_score: int, env_penalty: int) -> int:
    penalty = (severity_score * 8) + (strain_score * 6) + (human_factor_score * 0.3) + env_penalty
    return int(max(0, min(100, round(100 - penalty))))


def _risk_category_label(integral: int) -> str:
    if integral >= 85:
        return "Optimal"
    if integral >= 70:
        return "Ruxsat etiladigan"
    if integral >= 55:
        return "Zararli — past daraja (3.1)"
    if integral >= 40:
        return "Zararli — o'rta daraja (3.2)"
    if integral >= 25:
        return "Zararli — yuqori daraja (3.3-3.4)"
    return "Xavfli (4)"


def _build_recommendations(
    data: dict[str, Any],
    rwl: float,
    lifting_index: float,
    severity_class: str,
    strain_class: str,
    human_factor_score: int,
) -> list[str]:
    recommendations: list[str] = []

    if lifting_index > 1.0:
        recommendations.append(
            f"Ko'tarish indeksi (LI={lifting_index:.2f}) me'yordan yuqori — yukni mexanizatsiyalash "
            "(ko'targich, konveyer), yuk massasini yoki ko'tarish chastotasini kamaytirish tavsiya etiladi."
        )

    if data.get("worker_gender", "erkak") == "ayol" and float(data["load_mass_kg"]) > WOMEN_CONTINUOUS_LIMIT_KG:
        recommendations.append(
            "Ayol ishchilar uchun qo'lda ko'tarish og'irligi qonuniy chegaradan (doimiy — 10 kg, "
            "navbatlashtirib — 15 kg) oshib ketgan — operatsiyani qayta tashkil eting."
        )

    if severity_class in ("3.3", "3.4", "4"):
        recommendations.append(
            "Ish holati va statik yuk ko'rsatkichlari zararli darajada — ishchi holatini (poza) "
            "yaxshilash, tanaffuslar sonini oshirish zarur."
        )

    if strain_class in ("3.1", "3.2", "3.3", "3.4"):
        recommendations.append(
            "Psixofiziologik zo'riqish yuqori — monoton operatsiyalar sonini kamaytirish, tungi "
            "smenalarni cheklash va qo'shimcha tanaffuslar joriy etish tavsiya etiladi."
        )

    if human_factor_score >= 40:
        recommendations.append(
            "Inson omili xavfi yuqori — ishchini mehnat xavfsizligi bo'yicha qayta o'qitish va "
            "tibbiy ko'rikdan o'tkazish tavsiya etiladi."
        )

    temp = data.get("temperature_c")
    if temp is not None and (float(temp) < 10 or float(temp) > 32):
        recommendations.append(
            "Ish muhiti harorati me'yordan chetga chiqqan (alyuminiy pressi/eritish uchastkasi "
            "yaqinida) — issiqlikdan/sovuqdan himoya vositalari va tanaffuslar tashkil etilsin."
        )

    dust = data.get("metal_dust_mg_m3")
    if dust is not None and float(dust) > 6:
        recommendations.append(
            "Metall changi konsentratsiyasi me'yordan yuqori — mahalliy shamollatish va nafas olish "
            "organlarini himoya vositalaridan foydalanish shart."
        )

    if not data.get("uses_ppe"):
        recommendations.append(
            "Shaxsiy himoya vositalaridan (qo'lqop, poyabzal, kamar) muntazam foydalanilmayapti — "
            "ta'minot va nazoratni yo'lga qo'ying."
        )

    if not recommendations:
        recommendations.append("Asosiy ko'rsatkichlar me'yor doirasida — mavjud xavfsizlik tartib-qoidalarini saqlab qolish tavsiya etiladi.")

    return recommendations
