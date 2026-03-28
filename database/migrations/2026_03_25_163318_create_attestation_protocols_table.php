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
        Schema::create('attestation_protocols', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('attestation_applications')->cascadeOnDelete();
            $table->foreignId('laboratory_id')->constrained()->cascadeOnDelete();
            $table->json('chemical_factors')->nullable();
            $table->json('biological_factors')->nullable();
            $table->json('noise_vibration_factors')->nullable();
            $table->json('microclimate_factors')->nullable();
            $table->json('lighting_factors')->nullable();
            $table->json('radiation_factors')->nullable();
            $table->string('work_severity_class')->nullable(); // Mehnat og'irligi
            $table->string('work_intensity_class')->nullable(); // Mehnat tig'izligi
            $table->string('overall_class')->nullable(); // 1, 2, 3.1, 3.2, 3.3, 3.4, 4
            $table->boolean('requires_benefits')->default(false); // Imtiyozlar kerakmi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attestation_protocols');
    }
};
