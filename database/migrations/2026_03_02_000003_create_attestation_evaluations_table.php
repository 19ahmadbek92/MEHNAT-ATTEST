<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attestation_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('attestation_applications')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('noise_level', 5, 2)->nullable();
            $table->decimal('dust_level', 5, 2)->nullable();
            $table->decimal('vibration_level', 5, 2)->nullable();
            $table->decimal('lighting_level', 8, 2)->nullable();
            $table->string('microclimate')->nullable();
            $table->unsignedTinyInteger('equipment_hazard_score')->nullable();
            $table->enum('protective_equipment_status', ['yetarli', 'qisman', 'yetarli_emas'])->nullable();
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attestation_evaluations');
    }
};

