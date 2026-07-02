<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ergonomic_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workplace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('laboratory_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assessed_by')->nullable()->constrained('users')->nullOnDelete();

            // Operatsiya tavsifi (Yarim tayyor mahsulot / xomashyoni solish-ortish)
            $table->string('operation_type', 40)->default('qolda'); // qolda | yarim_mexanizatsiya | mexanizatsiya
            $table->string('load_description')->nullable();
            $table->decimal('load_mass_kg', 6, 2);
            $table->unsignedSmallInteger('lifts_per_shift')->default(0);
            $table->decimal('lifts_per_minute', 5, 2)->default(0);
            $table->decimal('shift_duration_hours', 4, 2)->default(8);
            $table->string('worker_gender', 10)->default('erkak'); // erkak | ayol

            // NIOSH ko'tarish tenglamasi kirish parametrlari
            $table->decimal('horizontal_distance_cm', 5, 1);
            $table->decimal('vertical_location_cm', 5, 1);
            $table->decimal('vertical_travel_cm', 5, 1);
            $table->decimal('asymmetry_angle_deg', 5, 1)->default(0);
            $table->string('coupling_quality', 10)->default('fair'); // good | fair | poor
            $table->string('duration_category', 10)->default('long'); // short(<=1s) | moderate(<=2s) | long(<=8s)

            // Og'irlik darajasi qo'shimcha ko'rsatkichlari (R2.2.2006-05 uslubiga asosan)
            $table->unsignedInteger('static_load_kgs')->nullable();
            $table->string('posture_type', 20)->default('erkin'); // erkin|epizodik_noqulay|davriy_noqulay|majburiy|qattiq_majburiy
            $table->unsignedInteger('body_inclinations_per_shift')->default(0);
            $table->decimal('walking_distance_km', 5, 2)->default(0);

            // Zo'riqish (psixofiziologik yuklanish, inson omili bilan bog'liq)
            $table->unsignedTinyInteger('attention_concentration_percent')->default(25);
            $table->unsignedInteger('signals_per_hour')->default(0);
            $table->string('responsibility_level', 20)->default('ozi_uchun'); // ozi_uchun|jamoa_uchun|xavfsizlik_uchun
            $table->unsignedInteger('monotony_operations_count')->default(0);
            $table->boolean('night_shift')->default(false);

            // Ish muhiti (alyuminiy profil ishlab chiqarish sanoatiga xos)
            $table->decimal('temperature_c', 4, 1)->nullable();
            $table->decimal('metal_dust_mg_m3', 5, 2)->nullable();
            $table->decimal('noise_level_db', 5, 1)->nullable();
            $table->boolean('uses_ppe')->default(true);

            // Inson omili (ishchi bilan bog'liq)
            $table->unsignedTinyInteger('employee_age')->nullable();
            $table->decimal('experience_years', 4, 1)->nullable();
            $table->boolean('training_completed')->default(false);
            $table->date('last_training_at')->nullable();
            $table->unsignedTinyInteger('fatigue_self_score')->default(1); // 1 (charchamagan) .. 5 (juda charchagan)
            $table->string('health_group', 20)->default('soglom'); // soglom|cheklangan|nogironligi_bor
            $table->unsignedInteger('prior_incidents_count')->default(0);

            // Hisoblangan natijalar (App\Services\ErgonomicAssessmentService)
            $table->decimal('rwl_kg', 6, 2)->nullable();
            $table->decimal('lifting_index', 6, 2)->nullable();
            $table->string('severity_class', 10)->nullable();
            $table->string('strain_class', 10)->nullable();
            $table->unsignedTinyInteger('human_factor_risk_score')->nullable();
            $table->unsignedTinyInteger('integral_safety_index')->nullable();
            $table->string('risk_category', 40)->nullable();
            $table->json('recommendations')->nullable();

            $table->text('notes')->nullable();
            $table->date('assessed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ergonomic_assessments');
    }
};
