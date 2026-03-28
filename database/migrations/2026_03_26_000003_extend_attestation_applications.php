<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attestation_applications', function (Blueprint $table) {
            // VM Qarori №263, Nizom — Attestatsiya sababi (Nizom 2-band)
            $table->enum('attestation_reason', [
                'benefits',       // Imtiyoz va kompensatsiyalar berilgan ish o'rinlari
                'disabled',       // Nogironligi bo'lgan shaxslar
                'pension_list',   // 1-2-ro'yxat (imtiyozli pensiya)
                'hazardous',      // Xavfli ishlab chiqarish ob'eklari
                'other',          // Boshqa (kollektiv shartnoma bo'yicha)
            ])->default('benefits')->after('organization_id');

            // Attestatsiya davriyligi (Nizom — 5 yilda kamida 1 marta)
            $table->enum('attestation_frequency', [
                'initial',   // Dastlabki
                '5_year',    // Rejalashtirilgan (5 yillik)
                '3_year',    // O'zgartirilgan (3 yillik)
                'unscheduled', // Rejalanmagan (yangi asbobuskunalar holatida)
            ])->default('5_year')->after('attestation_reason');

            // Attestatsiya komissiyasi (Nizom 9-10-11-band)
            $table->string('commission_order_number')->nullable()->after('attestation_frequency');
            $table->date('commission_order_date')->nullable()->after('commission_order_number');
            $table->json('commission_members')->nullable()->after('commission_order_date');
            // [{name, position, role:'chairman|member|lab_rep|union_rep'}]

            // Korxona statistikasi (Nizom 13-band)
            $table->integer('certified_workers_count')->nullable()->after('commission_members');
            $table->integer('women_workers_count')->nullable()->after('certified_workers_count');
            $table->integer('disabled_workers_count')->nullable()->after('women_workers_count');
        });
    }

    public function down(): void
    {
        Schema::table('attestation_applications', function (Blueprint $table) {
            $table->dropColumn([
                'attestation_reason', 'attestation_frequency',
                'commission_order_number', 'commission_order_date', 'commission_members',
                'certified_workers_count', 'women_workers_count', 'disabled_workers_count',
            ]);
        });
    }
};
