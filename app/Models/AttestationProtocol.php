<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttestationProtocol extends Model
{
    use HasFactory;

    protected $fillable = [
        // ── Asosiy ──
        'application_id',
        'laboratory_id',

        // ── SanQvaM 0069-24: 18 omil ──
        'chemical_factors',        // 1a-ilova — Kimyoviy omillar
        'fibrogenic_aerosols',     // 1b-ilova — Fibrogenli aerozollar
        'biological_factors',      // 2-ilova  — Biologik omillar
        'noise_vibration_factors', // 3-ilova  — Shovqin, infratovush, tebranish
        'emf_factors',             // 4-ilova  — Ionlanmagan elektromagnit maydon
        'optical_radiation',       // 5-ilova  — Optik nurlanish
        'microclimate_factors',    // 6–12-ilova — Mikroiqlim (SanQvaM 0204-06)
        'lighting_factors',        // 13-ilova — Yorug'lik (KMK 2.01.05-98)
        'ionizing_radiation',      // 14-ilova — Ionlangan nurlanish (SanQvaM 0194-06)
        'atmospheric_pressure',    // 15-ilova — Atmosfera bosimi
        'work_severity_class',     // 16-ilova — Mehnat og'irligi sinfi
        'work_intensity_class',    // 17–18-ilova — Mehnat zichligi sinfi

        // ── Umumiy baho (Nizom 29-band) ──
        'overall_class',           // 1, 2, 3.1, 3.2, 3.3, 3.4, 4

        // ── Jarohatlanish xavfi sinfi (Nizom 38-band) ──
        'injury_hazard_class',     // 'low', 'medium', 'high', 'critical'

        // ── YaTHV — Shaxsiy himoya vositalari (MK 477-band) ──
        'ppe_assessment',
        // {provided: bool, certified: bool, types: [], condition: 'satisfactory|unsatisfactory'}

        // ── Kafolatlar va kompensatsiyalar (MK 183-184-427-429 bandlar) ──
        'additional_leave_days',    // MK 183-band: Qo'shimcha ta'til
        'reduced_work_hours',       // MK 184-band: Qisqartirilgan ish vaqti
        'has_medical_food',         // SanQvaM 0184-05: Sut/tenglashtirilgan mahsulot
        'has_therapeutic_nutrition',// Davolash-profilaktik ovqatlanish
        'requires_benefits',        // Imtiyozlar kerakmi (umumiy)

        // ── XALIKK-2024 Kasblar klassifikatori ──
        'profession_code',
        'profession_name',

        // ── O'xshash ish o'rinlari — 20% qoidasi ──
        'similar_workplaces_count',
        'is_representative_sample',

        // ── Ta'sir davomiyligi — Ilova 1 ──
        'exposure_duration',
    ];

    protected $casts = [
        'chemical_factors'        => 'array',
        'fibrogenic_aerosols'     => 'array',
        'biological_factors'      => 'array',
        'noise_vibration_factors' => 'array',
        'emf_factors'             => 'array',
        'optical_radiation'       => 'array',
        'microclimate_factors'    => 'array',
        'lighting_factors'        => 'array',
        'ionizing_radiation'      => 'array',
        'atmospheric_pressure'    => 'array',
        'ppe_assessment'          => 'array',
        'exposure_duration'       => 'array',
        'requires_benefits'       => 'boolean',
        'has_medical_food'        => 'boolean',
        'has_therapeutic_nutrition' => 'boolean',
        'is_representative_sample' => 'boolean',
        'reduced_work_hours'      => 'decimal:1',
    ];

    /* ── Munosabatlar ── */
    public function application()
    {
        return $this->belongsTo(AttestationApplication::class);
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    /* ── Sinf yorliqlari (Nizom 29-band) ── */
    public static function classLabel(string $cls): array
    {
        return match ($cls) {
            '1'   => ['Optimal (1-sinf)', 'green'],
            '2'   => ['Ruxsat etilgan (2-sinf)', 'blue'],
            '3.1' => ['Zararli — 1-daraja (3.1)', 'orange'],
            '3.2' => ['Zararli — 2-daraja (3.2)', 'orange'],
            '3.3' => ['Zararli — 3-daraja (3.3)', 'red'],
            '3.4' => ['Zararli — 4-daraja (3.4)', 'red'],
            '4'   => ['Xavfli (4-sinf)', 'red'],
            default => ['Aniqlanmagan', 'gray'],
        };
    }

    /* ── Kompensatsiyalar avtomatik hisoblash ── */
    public function calculateCompensations(): array
    {
        $cls = $this->overall_class;
        $leave = 0;
        $hours = null;
        $milk  = false;

        if (in_array($cls, ['3.1', '3.2', '3.3', '3.4', '4'])) {
            // MK 183-band: Qo'shimcha ta'til
            $leave = match ($cls) {
                '3.1' => 6,
                '3.2' => 6,
                '3.3' => 12,
                '3.4' => 12,
                '4'   => 12,
                default => 0,
            };
            // MK 184-band: Qisqartirilgan ish vaqti
            $hours = in_array($cls, ['3.3', '3.4', '4']) ? 36.0 : 40.0;
            // SanQvaM 0184-05: Sut
            $milk = true;
        }

        return [
            'leave_days'     => $leave,
            'work_hours'     => $hours,
            'milk_benefit'   => $milk,
            'requires_ppq'   => in_array($cls, ['3.3', '3.4', '4']),
        ];
    }

    /* ── Jarohatlanish xavfi belgisi ── */
    public function injuryHazardLabel(): string
    {
        return match ($this->injury_hazard_class) {
            'low'      => '✅ Past xavf',
            'medium'   => '⚠️ O\'rta xavf',
            'high'     => '🔶 Yuqori xavf',
            'critical' => '🔴 Kritik xavf',
            default    => '—',
        };
    }
}
