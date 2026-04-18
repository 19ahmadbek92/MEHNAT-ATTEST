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
        Schema::create('state_expertise_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('laboratory_id')->constrained()->cascadeOnDelete();
            $table->json('application_ids'); // array of attestation_applications ids

            // Institute Review (Dastlabki baholash)
            $table->enum('institute_status', ['pending', 'approved', 'returned'])->default('pending');
            $table->foreignId('institute_expert_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('institute_comment')->nullable();
            $table->timestamp('institute_reviewed_at')->nullable();

            // Ministry Review (Davlat ekspertizasi)
            $table->enum('ministry_status', ['pending', 'approved', 'returned'])->default('pending');
            $table->foreignId('ministry_expert_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('ministry_comment')->nullable();
            $table->timestamp('ministry_reviewed_at')->nullable();

            // Final Conclusion
            $table->string('conclusion_number')->unique()->nullable(); // xulosa seriyasi va raqami
            $table->string('qr_code_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('state_expertise_applications');
    }
};
