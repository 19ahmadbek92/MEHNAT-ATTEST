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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('stir_inn')->unique();
            $table->string('ifut_code')->nullable();
            $table->string('mhobt_code')->nullable();
            $table->string('parent_organization')->nullable();
            $table->string('activity_type')->nullable();
            $table->text('legal_address')->nullable();
            $table->integer('total_employees')->default(0);
            $table->integer('disabled_employees')->default(0);
            $table->integer('women_employees')->default(0);
            $table->decimal('risk_profile_score', 5, 2)->default(0); // For Xavflarni tahlil qilish
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
