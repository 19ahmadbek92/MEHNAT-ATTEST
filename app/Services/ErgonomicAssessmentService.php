<?php

namespace App\Services;

/**
 * Yarim tayyor mahsulot va xomashyoni qo'lda solish-ortish (yuklash-tushirish)
 * ishlarida band ishchilarning mehnat xavfsizligini baholaydi:
 *  - jismoniy (ergonomik) yuklanish — NIOSH (1994) ko'tarish tenglamasi + R2.2.2006-05
 *    uslubiyatiga asoslangan og'irlik darajasi ko'rsatkichlari;
 *  - psixofiziologik zo'riqish ko'rsatkichlari;
 *  - inson omili (tajriba, o'qitilganlik, charchoq, sog'liq holati) xavfi;
 *  - alyuminiy profil ishlab chiqarishga xos ish muhiti omillari (issiqlik, metall changi, shovqin).
 *
 * Natija: SanQvaM 0069-24 uslubidagi xavf klasslari (1 / 2 / 3.1-3.4 / 4) va
 * 0-100 oralig'idagi integral xavfsizlik ko'rsatkichi.
 */
class ErgonomicAssessmentService
{
    /** NIOSH load constant (kg) */
    private const LC = 23.0;

    /** Ayollar uchun O'zbekiston Mehnat kodeksi bo'yicha qo'lda ko'tarish chegarasi (kg, doimiy). */
    private const WOMEN_CONTINUOUS_LIMIT_KG = 10.0;

    /** Ayollar uchun boshqa ish bilan navbatlashtirib ko'tarish chegarasi (kg). */
    private const WOMEN_ALTERNATING_LIMIT_KG = 15.0;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function evaluate(array $data): array
    {
        $niosh = $this->calculateNiosh($data);
        $severity = $this->classifySeverity($data, $niosh['lifting_index']);
        $strain = $this->classifyStrain($data);
        $humanFactor = $this->scoreHumanFactor($data);
        $envPenalty = $this->environmentalPenalty($data);

        $integral = $this->integralSafetyIndex($severity['score'], $strain['score'], $humanFactor['score'], $envPenalty);
        $riskCategory = $this->riskCategoryLabel($integral);

        $recommendations = $this->buildRecommendations($data, $niosh, $severity, $strain, $humanFactor);

        return [
            'rwl_kg' => $niosh['rwl'],
            'lifting_index' => $niosh['lifting_index'],
            'severity_class' => $severity['class'],
            'strain_class' => $strain['class'],
            'human_factor_risk_score' => $humanFactor['score'],
            'integral_safety_index' => $integral,
            'risk_category' => $riskCategory,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * NIOSH (1994) ko'tarish tenglamasi: RWL = LC x HM x VM x DM x AM x FM x CM.
     *
     * @param  array<string, mixed>  $data
     * @return array{rwl: float, lifting_index: float}
     */
    private function calculateNiosh(array $data): array
    {
        $h = max((float) $data['horizontal_distance_cm'], 25.0);
        $v = min(max((float) $data['vertical_location_cm'], 0.0), 175.0);
        $d = max((float) $data['vertical_travel_cm'], 25.0);
        $a = (float) ($data['asymmetry_angle_deg'] ?? 0);

        $hm = $h > 63 ? 0.0 : 25 / $h;
        $vm = 1 - (0.003 * abs($v - 75));
        $dm = $d > 175 ? 0.0 : 0.82 + (4.5 / $d);
        $am = $a > 135 ? 0.0 : 1 - (0.0032 * $a);
        $fm = $this->frequencyMultiplier((float) ($data['lifts_per_minute'] ?? 0), (string) ($data['duration_category'] ?? 'long'), $v);
        $cm = $this->couplingMultiplier((string) ($data['coupling_quality'] ?? 'fair'), $v);

        $rwl = self::LC * $hm * $vm * $dm * $am * $fm * $cm;
        $rwl = round(max($rwl, 0), 2);

        $mass = (float) $data['load_mass_kg'];
        $li = $rwl > 0 ? round($mass / $rwl, 2) : 99.99;

        return ['rwl' => $rwl, 'lifting_index' => $li];
    }

    private function frequencyMultiplier(float $f, string $duration, float $v): float
    {
        $column = $v < 75 ? 'lt75' : 'ge75';

        // NIOSH (1994) chastota multiplikatori jadvali (Table 5), soddalashtirilgan.
        // PHP float array-kalitlarni int'ga kesib tashlaydi, shu sabab [freq, values]
        // juftliklari ro'yxati sifatida saqlanadi (0.2 va 0.5 kalitlar to'qnashmasligi uchun).
        $table = [
            'short' => [ // <= 1 soat
                [0.2, ['lt75' => 1.00, 'ge75' => 1.00]],
                [0.5, ['lt75' => 0.97, 'ge75' => 0.97]],
                [1, ['lt75' => 0.94, 'ge75' => 0.94]],
                [2, ['lt75' => 0.91, 'ge75' => 0.91]],
                [3, ['lt75' => 0.88, 'ge75' => 0.88]],
                [4, ['lt75' => 0.84, 'ge75' => 0.84]],
                [5, ['lt75' => 0.80, 'ge75' => 0.80]],
                [6, ['lt75' => 0.75, 'ge75' => 0.75]],
                [7, ['lt75' => 0.70, 'ge75' => 0.70]],
                [8, ['lt75' => 0.60, 'ge75' => 0.60]],
                [9, ['lt75' => 0.52, 'ge75' => 0.52]],
                [10, ['lt75' => 0.45, 'ge75' => 0.45]],
                [11, ['lt75' => 0.41, 'ge75' => 0.41]],
                [12, ['lt75' => 0.37, 'ge75' => 0.37]],
                [13, ['lt75' => 0.00, 'ge75' => 0.34]],
                [14, ['lt75' => 0.00, 'ge75' => 0.31]],
                [15, ['lt75' => 0.00, 'ge75' => 0.28]],
            ],
            'moderate' => [ // <= 2 soat
                [0.2, ['lt75' => 0.95, 'ge75' => 0.95]],
                [0.5, ['lt75' => 0.92, 'ge75' => 0.92]],
                [1, ['lt75' => 0.88, 'ge75' => 0.88]],
                [2, ['lt75' => 0.84, 'ge75' => 0.84]],
                [3, ['lt75' => 0.79, 'ge75' => 0.79]],
                [4, ['lt75' => 0.72, 'ge75' => 0.72]],
                [5, ['lt75' => 0.60, 'ge75' => 0.60]],
                [6, ['lt75' => 0.50, 'ge75' => 0.50]],
                [7, ['lt75' => 0.42, 'ge75' => 0.42]],
                [8, ['lt75' => 0.35, 'ge75' => 0.35]],
                [9, ['lt75' => 0.30, 'ge75' => 0.30]],
                [10, ['lt75' => 0.26, 'ge75' => 0.26]],
                [11, ['lt75' => 0.00, 'ge75' => 0.23]],
                [12, ['lt75' => 0.00, 'ge75' => 0.21]],
            ],
            'long' => [ // <= 8 soat
                [0.2, ['lt75' => 0.85, 'ge75' => 0.85]],
                [0.5, ['lt75' => 0.81, 'ge75' => 0.81]],
                [1, ['lt75' => 0.75, 'ge75' => 0.75]],
                [2, ['lt75' => 0.65, 'ge75' => 0.65]],
                [3, ['lt75' => 0.55, 'ge75' => 0.55]],
                [4, ['lt75' => 0.45, 'ge75' => 0.45]],
                [5, ['lt75' => 0.35, 'ge75' => 0.35]],
                [6, ['lt75' => 0.27, 'ge75' => 0.27]],
                [7, ['lt75' => 0.22, 'ge75' => 0.22]],
                [8, ['lt75' => 0.18, 'ge75' => 0.18]],
                [9, ['lt75' => 0.15, 'ge75' => 0.15]],
                [10, ['lt75' => 0.13, 'ge75' => 0.13]],
            ],
        ];

        $rows = $table[$duration] ?? $table['long'];

        foreach ($rows as [$freq, $values]) {
            if ($f <= $freq) {
                return $values[$column];
            }
        }

        return 0.0;
    }

    private function couplingMultiplier(string $quality, float $v): float
    {
        return match ($quality) {
            'good' => 1.00,
            'poor' => 0.90,
            default => $v < 75 ? 0.95 : 1.00, // fair
        };
    }

    /**
     * Og'irlik darajasi (jismoniy yuklanish) klassini eng noqulay ko'rsatkich bo'yicha aniqlaydi.
     *
     * @param  array<string, mixed>  $data
     * @return array{class: string, score: int}
     */
    private function classifySeverity(array $data, float $liftingIndex): array
    {
        $scores = [];

        $scores[] = $this->liftingIndexScore($liftingIndex);

        if (! empty($data['static_load_kgs'])) {
            $scores[] = $this->bracketScore((float) $data['static_load_kgs'], [43000, 97000, 208000, 245000]);
        }

        $scores[] = match ($data['posture_type'] ?? 'erkin') {
            'erkin' => 0,
            'epizodik_noqulay' => 1,
            'davriy_noqulay' => 2,
            'majburiy' => 3,
            'qattiq_majburiy' => 4,
            default => 1,
        };

        $scores[] = $this->bracketScore((float) ($data['body_inclinations_per_shift'] ?? 0), [50, 100, 300, 300]);
        $scores[] = $this->bracketScore((float) ($data['walking_distance_km'] ?? 0), [4, 10, 15, 15]);

        // Ayol ishchi uchun qonuniy og'irlik chegarasi buzilsa — avtomatik "xavfli" (4).
        if (($data['worker_gender'] ?? 'erkak') === 'ayol') {
            $mass = (float) $data['load_mass_kg'];
            if ($mass > self::WOMEN_ALTERNATING_LIMIT_KG) {
                $scores[] = 5;
            } elseif ($mass > self::WOMEN_CONTINUOUS_LIMIT_KG) {
                $scores[] = 3;
            }
        }

        $worst = max($scores);

        return ['class' => $this->scoreToClassLabel($worst), 'score' => $worst];
    }

    private function liftingIndexScore(float $li): int
    {
        return match (true) {
            $li <= 0.5 => 0,
            $li <= 1.0 => 1,
            $li <= 1.5 => 2,
            $li <= 2.0 => 3,
            $li <= 3.0 => 4,
            $li <= 4.0 => 4,
            default => 5,
        };
    }

    /**
     * @param  array<int, float>  $limits  [class1_max, class2_max, class3.1_max, class3.2_max]
     */
    private function bracketScore(float $value, array $limits): int
    {
        return match (true) {
            $value <= $limits[0] => 0,
            $value <= $limits[1] => 1,
            $value <= $limits[2] => 2,
            $value <= $limits[3] => 3,
            default => 4,
        };
    }

    private function scoreToClassLabel(int $score): string
    {
        return match (true) {
            $score <= 0 => '1',
            $score === 1 => '2',
            $score === 2 => '3.1',
            $score === 3 => '3.2',
            $score === 4 => '3.3',
            default => '4',
        };
    }

    /**
     * Zo'riqish (psixofiziologik yuklanish) klassi.
     *
     * @param  array<string, mixed>  $data
     * @return array{class: string, score: int}
     */
    private function classifyStrain(array $data): array
    {
        $scores = [];

        $scores[] = $this->bracketScore((float) ($data['attention_concentration_percent'] ?? 0), [25, 50, 75, 75]);
        $scores[] = $this->bracketScore((float) ($data['signals_per_hour'] ?? 0), [75, 175, 300, 300]);
        $scores[] = $this->bracketScore((float) ($data['monotony_operations_count'] ?? 0), [10, 6, 4, 4]);

        $scores[] = match ($data['responsibility_level'] ?? 'ozi_uchun') {
            'ozi_uchun' => 0,
            'jamoa_uchun' => 2,
            'xavfsizlik_uchun' => 3,
            default => 1,
        };

        $shift = (float) ($data['shift_duration_hours'] ?? 8);
        if ($shift > 12) {
            $scores[] = 4;
        } elseif ($shift > 8) {
            $scores[] = 2;
        }

        if (! empty($data['night_shift'])) {
            $scores[] = 2;
        }

        $worst = max($scores);

        return ['class' => $this->scoreToClassLabel($worst), 'score' => $worst];
    }

    /**
     * Inson omili xavf balli (0-100, kattaroq = xavfliroq).
     *
     * @param  array<string, mixed>  $data
     * @return array{score: int}
     */
    private function scoreHumanFactor(array $data): array
    {
        $score = 0;

        $experience = (float) ($data['experience_years'] ?? 0);
        $score += match (true) {
            $experience < 1 => 25,
            $experience < 3 => 15,
            $experience < 5 => 5,
            default => 0,
        };

        if (empty($data['training_completed'])) {
            $score += 20;
        } elseif (! empty($data['last_training_at']) && now()->diffInMonths($data['last_training_at']) > 12) {
            $score += 10;
        }

        $fatigue = (int) ($data['fatigue_self_score'] ?? 1);
        $score += max(0, $fatigue - 1) * 5;

        $age = (int) ($data['employee_age'] ?? 30);
        if ($age > 0 && ($age < 18 || $age > 55)) {
            $score += 10;
        }

        if (in_array($data['health_group'] ?? 'soglom', ['cheklangan', 'nogironligi_bor'], true)) {
            $score += 15;
        }

        $incidents = (int) ($data['prior_incidents_count'] ?? 0);
        $score += min($incidents * 10, 30);

        return ['score' => min((int) $score, 100)];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function environmentalPenalty(array $data): int
    {
        $penalty = 0;

        $temp = $data['temperature_c'] ?? null;
        if ($temp !== null && ($temp < 10 || $temp > 32)) {
            $penalty += 8;
        }

        $dust = $data['metal_dust_mg_m3'] ?? null;
        if ($dust !== null && $dust > 6) {
            $penalty += 8;
        }

        $noise = $data['noise_level_db'] ?? null;
        if ($noise !== null && $noise > 80) {
            $penalty += 6;
        }

        if (empty($data['uses_ppe'])) {
            $penalty += 10;
        }

        return $penalty;
    }

    private function integralSafetyIndex(int $severityScore, int $strainScore, int $humanFactorScore, int $envPenalty): int
    {
        $penalty = ($severityScore * 8) + ($strainScore * 6) + ($humanFactorScore * 0.3) + $envPenalty;

        return (int) max(0, min(100, round(100 - $penalty)));
    }

    private function riskCategoryLabel(int $integral): string
    {
        return match (true) {
            $integral >= 85 => 'Optimal',
            $integral >= 70 => 'Ruxsat etiladigan',
            $integral >= 55 => 'Zararli — past daraja (3.1)',
            $integral >= 40 => 'Zararli — o\'rta daraja (3.2)',
            $integral >= 25 => 'Zararli — yuqori daraja (3.3-3.4)',
            default => 'Xavfli (4)',
        };
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array{rwl: float, lifting_index: float}  $niosh
     * @param  array{class: string, score: int}  $severity
     * @param  array{class: string, score: int}  $strain
     * @param  array{score: int}  $humanFactor
     * @return array<int, string>
     */
    private function buildRecommendations(array $data, array $niosh, array $severity, array $strain, array $humanFactor): array
    {
        $recommendations = [];

        if ($niosh['lifting_index'] > 1.0) {
            $recommendations[] = sprintf(
                "Ko'tarish indeksi (LI=%.2f) me'yordan yuqori — yukni mexanizatsiyalash (ko'targich, konveyer), yuk massasini yoki ko'tarish chastotasini kamaytirish tavsiya etiladi.",
                $niosh['lifting_index']
            );
        }

        if (($data['worker_gender'] ?? 'erkak') === 'ayol' && (float) $data['load_mass_kg'] > self::WOMEN_CONTINUOUS_LIMIT_KG) {
            $recommendations[] = "Ayol ishchilar uchun qo'lda ko'tarish og'irligi qonuniy chegaradan (doimiy — 10 kg, navbatlashtirib — 15 kg) oshib ketgan — operatsiyani qayta tashkil eting.";
        }

        if (in_array($severity['class'], ['3.3', '3.4', '4'], true)) {
            $recommendations[] = "Ish holati va statik yuk ko'rsatkichlari zararli darajada — ishchi holatini (poza) yaxshilash, tanaffuslar sonini oshirish zarur.";
        }

        if (in_array($strain['class'], ['3.1', '3.2', '3.3', '3.4'], true)) {
            $recommendations[] = "Psixofiziologik zo'riqish yuqori — monoton operatsiyalar sonini kamaytirish, tungi smenalarni cheklash va qo'shimcha tanaffuslar joriy etish tavsiya etiladi.";
        }

        if ($humanFactor['score'] >= 40) {
            $recommendations[] = "Inson omili xavfi yuqori — ishchini mehnat xavfsizligi bo'yicha qayta o'qitish va tibbiy ko'rikdan o'tkazish tavsiya etiladi.";
        }

        if (! empty($data['temperature_c']) && ($data['temperature_c'] < 10 || $data['temperature_c'] > 32)) {
            $recommendations[] = "Ish muhiti harorati me'yordan chetga chiqqan (alyuminiy pressi/eritish uchastkasi yaqinida) — issiqlikdan/sovuqdan himoya vositalari va tanaffuslar tashkil etilsin.";
        }

        if (! empty($data['metal_dust_mg_m3']) && $data['metal_dust_mg_m3'] > 6) {
            $recommendations[] = "Metall changi konsentratsiyasi me'yordan yuqori — mahalliy shamollatish va nafas olish organlarini himoya vositalaridan foydalanish shart.";
        }

        if (empty($data['uses_ppe'])) {
            $recommendations[] = "Shaxsiy himoya vositalaridan (qo'lqop, poyabzal, kamar) muntazam foydalanilmayapti — ta'minot va nazoratni yo'lga qo'ying.";
        }

        if (empty($recommendations)) {
            $recommendations[] = "Asosiy ko'rsatkichlar me'yor doirasida — mavjud xavfsizlik tartib-qoidalarini saqlab qolish tavsiya etiladi.";
        }

        return $recommendations;
    }
}
