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
        Schema::create('measurement_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workplace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('laboratory_id')->nullable()->constrained()->nullOnDelete();

            // Xavf omillari (SanQvaM 0069-24 ga asosan: mikroiqlim, shovqin, vibratsiya, chang, zaharli moddalar va hkz.)
            $table->string('factor_name');

            $table->string('measured_value')->nullable(); // O'lchangan ko'rsatkich
            $table->string('norm_value')->nullable(); // Me'yor miqdori

            // Xavf klassi. '1'(Optimal), '2'(Ruxsat etilgan), '3.1', '3.2', '3.3', '3.4'(Zararli), '4'(Xavfli)
            $table->string('danger_class', 10)->nullable();

            $table->string('protocol_number')->nullable();
            $table->date('measured_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurement_results');
    }
};
