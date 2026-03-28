<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('state_expertise_applications', function (Blueprint $table) {
            // ── Ariza sanasi va muddatlar (Tartibnoma 26-30-bandlar) ──
            $table->timestamp('submitted_at')->nullable()->after('application_ids');
            $table->date('institute_deadline')->nullable()->after('submitted_at');
            // 15 kalendar kun: submitted_at + 15d
            $table->date('ministry_deadline')->nullable()->after('institute_deadline');
            // 10 kalendar kun institut ma'qullagandan so'ng: +10d
            $table->date('total_deadline')->nullable()->after('ministry_deadline');
            // Jami 25 kun: submitted_at + 25d

            // ── To'lov (Tartibnoma 25-band: EMHM 25%) ──
            $table->decimal('payment_amount', 12, 2)->nullable()->after('total_deadline');
            $table->string('payment_receipt_number')->nullable()->after('payment_amount');
            $table->boolean('payment_confirmed')->default(false)->after('payment_receipt_number');

            // ── Avtomatik tasdiqlash (Tartibnoma §33) ──
            $table->boolean('is_auto_approved')->default(false)->after('payment_confirmed');
            $table->timestamp('auto_approved_at')->nullable()->after('is_auto_approved');

            // ── Institut qaytarish (Tartibnoma 31-band) ──
            $table->text('institute_return_reason')->nullable()->after('institute_comment');
            $table->string('institute_return_legal_ref')->nullable()->after('institute_return_reason');
            // Aniq norma: masalan "SanQvaM 0069-24 Ilova №3, band 5"
            $table->date('institute_return_deadline')->nullable()->after('institute_return_legal_ref');
            // Qayta topshirish muddati (min 10 ish kuni)

            // ── Vazirlik qaytarish (Tartibnoma 31-band) ──
            $table->text('ministry_return_reason')->nullable()->after('ministry_comment');
            $table->string('ministry_return_legal_ref')->nullable()->after('ministry_return_reason');
            $table->date('ministry_return_deadline')->nullable()->after('ministry_return_legal_ref');

            // ── Qayta topshirishlar soni (Tartibnoma 34-band) ──
            $table->integer('resubmission_count')->default(0)->after('ministry_return_deadline');

            // ── Xulosa blanki (Tartibnoma 38-39-band) ──
            // Format: DX-2024-000001
            $table->string('conclusion_series')->nullable()->after('conclusion_number');
            $table->string('conclusion_blank_no')->unique()->nullable()->after('conclusion_series');
            // Blankning hisob seriyasi va raqami (qat'iy hisobot hujjati)
        });
    }

    public function down(): void
    {
        Schema::table('state_expertise_applications', function (Blueprint $table) {
            $table->dropColumn([
                'submitted_at', 'institute_deadline', 'ministry_deadline', 'total_deadline',
                'payment_amount', 'payment_receipt_number', 'payment_confirmed',
                'is_auto_approved', 'auto_approved_at',
                'institute_return_reason', 'institute_return_legal_ref', 'institute_return_deadline',
                'ministry_return_reason', 'ministry_return_legal_ref', 'ministry_return_deadline',
                'resubmission_count', 'conclusion_series', 'conclusion_blank_no',
            ]);
        });
    }
};
