<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attestation_protocols', function (Blueprint $table) {
            // ── SanQvaM 0069-24 Ilova №4: Ionlanmagan elektromagnit maydonlar ──
            $table->json('emf_factors')->nullable()->after('radiation_factors');
            // ── SanQvaM 0069-24 Ilova №5: Optik diapazonda nurlanish ──
            $table->json('optical_radiation')->nullable()->after('emf_factors');
            // ── SanQvaM 0069-24 Ilova №14: Ionlangan nurlanish (SanQvaM 0194-06) ──
            $table->json('ionizing_radiation')->nullable()->after('optical_radiation');
            // ── SanQvaM 0069-24 Ilova №15: Atmosfera bosimi ──
            $table->json('atmospheric_pressure')->nullable()->after('ionizing_radiation');

            // ── Jarohatlanish xavfi sinfi — Nizom, 38-band ──
            $table->string('injury_hazard_class')->nullable()->after('overall_class');
            // Mezon: asbob-uskunalar, moslamalar, yoʻriqnomalar talablari

            // ── YaTHV (Yakka tartibdagi himoya vositalari) baholash ──
            $table->json('ppe_assessment')->nullable()->after('injury_hazard_class');
            // {provided: bool, certified: bool, types: [], condition: 'satisfactory|unsatisfactory'}

            // ── Kafolatlar va kompensatsiyalar (MK 183-184-427-429-363-477 bandlar) ──
            $table->integer('additional_leave_days')->default(0)->after('ppe_assessment');
            $table->decimal('reduced_work_hours', 4, 1)->nullable()->after('additional_leave_days');
            // 40 soatgacha, 36, 24 soat
            $table->boolean('has_medical_food')->default(false)->after('reduced_work_hours');
            // Sut yoki teng oziq-ovqat (SanQvaM 0184-05)
            $table->boolean('has_therapeutic_nutrition')->default(false)->after('has_medical_food');
            // Davolash-profilaktik ovqatlanish

            // ── XALIKK-2024 — Kasblar klassifikatori ──
            $table->string('profession_code')->nullable()->after('has_therapeutic_nutrition');
            $table->string('profession_name')->nullable()->after('profession_code');

            // ── O'xshash ish o'rinlari (20% qoidasi) ──
            $table->integer('similar_workplaces_count')->default(1)->after('profession_name');
            $table->boolean('is_representative_sample')->default(false)->after('similar_workplaces_count');
            // Agar true bo'lsa, bu ish o'rni 20% namunaviy tekshirish

            // ── O'lchash davomiyligi (% yoki soatda) — Ilova 1, 18-band ──
            $table->json('exposure_duration')->nullable()->after('is_representative_sample');
            // {factor: 'chemical', duration_pct: 85, duration_hours: 6.8}
        });
    }

    public function down(): void
    {
        Schema::table('attestation_protocols', function (Blueprint $table) {
            $table->dropColumn([
                'emf_factors', 'optical_radiation', 'ionizing_radiation',
                'atmospheric_pressure', 'injury_hazard_class', 'ppe_assessment',
                'additional_leave_days', 'reduced_work_hours', 'has_medical_food',
                'has_therapeutic_nutrition', 'profession_code', 'profession_name',
                'similar_workplaces_count', 'is_representative_sample', 'exposure_duration',
            ]);
        });
    }
};
