<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attestation_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained('attestation_campaigns')->cascadeOnDelete();
            $table->string('workplace_name');
            $table->string('department')->nullable();
            $table->integer('employee_count')->nullable();
            $table->text('workplace_description')->nullable();
            $table->json('hazard_factors')->nullable();
            $table->text('equipment_list')->nullable();
            $table->text('protective_equipment')->nullable();
            $table->string('workplace_photo_path')->nullable();
            $table->string('documents_path')->nullable();
            $table->enum('status', ['submitted', 'hr_approved', 'hr_rejected', 'finalized'])->default('submitted')->index();

            $table->foreignId('hr_reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hr_reviewed_at')->nullable();
            $table->text('hr_comment')->nullable();

            $table->decimal('final_score', 5, 2)->nullable();
            $table->enum('final_decision', ['pending', 'fail', 'optimal', 'ruxsat_etilgan', 'zararli_xavfli'])->default('pending')->index();
            $table->enum('workplace_class', ['optimal', 'ruxsat_etilgan', 'zararli_xavfli'])->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalized_at')->nullable();

            $table->index(['campaign_id', 'status']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attestation_applications');
    }
};

