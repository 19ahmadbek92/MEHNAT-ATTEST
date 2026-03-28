<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laboratories', function (Blueprint $table) {
            // Yagona reyestr — "Mehnatni muhofaza qilish sohasidagi xizmatlar bozori
            // professional ishtirokchilari Yagona reestri"
            $table->string('registry_number')->unique()->nullable()->after('stir_inn');

            // "O'zbekiston akkreditatsiya markazi" guvohnomasi
            $table->string('accreditation_body')->default("O'zbekiston akkreditatsiya markazi")->after('registry_number');

            // Bandlik vazirligi xabarnomasi (Tartibnoma 1-band)
            $table->string('ministry_notification_number')->nullable()->after('accreditation_body');
            $table->date('ministry_notification_date')->nullable()->after('ministry_notification_number');

            // Xodimlarning malakaviy sertifikatlari
            $table->json('specialist_certificates')->nullable()->after('ministry_notification_date');
            // [{name, certificate_no, expiry_date, specialization}]

            // Ma'qullash sohasi — qaysi omillarni o'lchashga vakolatli
            $table->json('authorized_factors')->nullable()->after('specialist_certificates');
            // ['chemical', 'noise', 'lighting', 'emf', ...]

            // Kontakt ma'lumotlari
            $table->string('contact_phone')->nullable()->after('authorized_factors');
            $table->string('contact_email')->nullable()->after('contact_phone');
            $table->text('legal_address')->nullable()->after('contact_email');
        });
    }

    public function down(): void
    {
        Schema::table('laboratories', function (Blueprint $table) {
            $table->dropColumn([
                'registry_number', 'accreditation_body',
                'ministry_notification_number', 'ministry_notification_date',
                'specialist_certificates', 'authorized_factors',
                'contact_phone', 'contact_email', 'legal_address',
            ]);
        });
    }
};
