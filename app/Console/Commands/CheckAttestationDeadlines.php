<?php

namespace App\Console\Commands;

use App\Models\StateExpertiseApplication;
use Illuminate\Console\Command;

class CheckAttestationDeadlines extends Command
{
    protected $signature = 'attestation:check-deadlines';

    protected $description = 'VM Qarori №263 §33: Muddati o\'tgan davlat ekspertizasi arizalarini avtomatik tasdiqlash va ogohlantirish';

    public function handle(): int
    {
        $this->info('Attestatsiya muddat tekshiruvi boshlanmoqda...');

        // ── §33: 25 kun o'tgan, javobi yo'q → avtomatik tasdiqlash ──
        $expired = StateExpertiseApplication::where('ministry_status', 'pending')
            ->where('is_auto_approved', false)
            ->get();

        $autoApproved = 0;
        foreach ($expired as $app) {
            if ($app->autoApproveIfOverdue()) {
                $autoApproved++;
                $this->line("✅ Avtomatik tasdiqlandi: #{$app->id} ({$app->organization?->name}) — {$app->conclusion_number}");
            }
        }

        // ── Ogohlantirish: Institut muddati 2-3 kun qolganlar ──
        $instWarning = StateExpertiseApplication::where('institute_status', 'pending')
            ->whereNotNull('institute_deadline')
            ->whereDate('institute_deadline', '<=', now()->addDays(3))
            ->whereDate('institute_deadline', '>=', now())
            ->get();

        foreach ($instWarning as $app) {
            $days = $app->instituteDaysRemaining();
            $this->warn("⚠️  Institut muddati yaqinlashdi: #{$app->id} — {$days} kun qoldi ({$app->organization?->name})");
        }

        // ── Ogohlantirish: Laboratoriya akkreditatsiya muddati tugash ──
        $labExpiry = \App\Models\Laboratory::whereNotNull('accreditation_expiry_date')
            ->whereDate('accreditation_expiry_date', '<=', now()->addDays(30))
            ->where('is_active', true)
            ->get();

        foreach ($labExpiry as $lab) {
            $this->warn("🔬 Laboratoriya akkreditatsiyasi tugash arafasida: {$lab->name} — {$lab->accreditation_expiry_date}");
        }

        $this->info("Jarayon tugadi. Avtomatik tasdiqlangan: {$autoApproved}");

        return Command::SUCCESS;
    }
}
