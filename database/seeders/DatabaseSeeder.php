<?php

namespace Database\Seeders;

use App\Models\AttestationCampaign;
use App\Models\Laboratory;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Faqat mahalliy sinov / birinchi o‘rnatish uchun.
     * Internetdagi serverda parollarni o‘zgartiring yoki seedni o‘chirib tashlang.
     */
    public function run(): void
    {
        // ──────────────────────────────────────────────
        //  1. ADMIN
        // ──────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@attestatsiya.uz'],
            [
                'name' => 'Tizim Administratori',
                'password' => Hash::make('Admin@2024!'),
                'role' => 'admin',
            ]
        );

        // ──────────────────────────────────────────────
        //  2. LABORATORY + User
        // ──────────────────────────────────────────────
        $laboratory = Laboratory::updateOrCreate(
            ['stir_inn' => '987654321'],
            [
                'name' => 'O\'zbek Mehnat Muhofazasi Laboratoriyasi',
                'accreditation_certificate_number' => 'AKKREDIT-2024-001',
                'accreditation_expiry_date' => '2026-12-31',
                'accreditation_scope' => 'Kimyoviy, biologik, fizik va mikroiqlim omillarini o\'lchash',
                'is_active' => true,
            ]
        );

        $labUser = User::updateOrCreate(
            ['email' => 'lab@attestatsiya.uz'],
            [
                'name' => 'Laboratoriya Rahbari',
                'password' => Hash::make('Lab@2024!'),
                'role' => 'laboratory',
                'laboratory_id' => $laboratory->id,
            ]
        );

        // ──────────────────────────────────────────────
        //  3. INSTITUTE EXPERT
        // ──────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'institut@attestatsiya.uz'],
            [
                'name' => 'Institut Eksperti Kamolov',
                'password' => Hash::make('Institut@2024!'),
                'role' => 'institute_expert',
            ]
        );

        // ──────────────────────────────────────────────
        //  4. MINISTRY EXPERT (Vazirlik)
        // ──────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'vazirlik@attestatsiya.uz'],
            [
                'name' => 'Vazirlik Bosh Eksperti Toshmatov',
                'password' => Hash::make('Vazirlik@2024!'),
                'role' => 'expert',
            ]
        );

        // ──────────────────────────────────────────────
        //  5. EMPLOYER DEMO + Organization
        // ──────────────────────────────────────────────
        $org = Organization::updateOrCreate(
            ['stir_inn' => '123456789'],
            [
                'name' => '"Almazbor" Qurilish MChJ',
                'ifut_code' => '41.20',
                'mhobt_code' => 'XK',
                'activity_type' => 'Qurilish',
                'legal_address' => 'Toshkent sh., Yunusobod tumani, 14-kvartal, 5-uy',
                'total_employees' => 120,
                'women_employees' => 38,
                'disabled_employees' => 2,
            ]
        );

        User::updateOrCreate(
            ['email' => 'employer@attestatsiya.uz'],
            [
                'name' => 'Ish Beruvchi (Demo)',
                'password' => Hash::make('Employer@2024!'),
                'role' => 'employer',
                'organization_id' => $org->id,
            ]
        );

        // ──────────────────────────────────────────────
        //  6. COMMISSION
        // ──────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'komissiya@attestatsiya.uz'],
            [
                'name' => 'Attestatsiya Komissiyasi',
                'password' => Hash::make('Komiss@2024!'),
                'role' => 'commission',
            ]
        );

        // ──────────────────────────────────────────────
        //  7. HR (arizalarni ko'rib chiqish)
        // ──────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'hr@attestatsiya.uz'],
            [
                'name' => 'HR Mutaxassisi (Demo)',
                'password' => Hash::make('Hr@2024!'),
                'role' => 'hr',
            ]
        );

        // ──────────────────────────────────────────────
        //  8. OPEN ATTESTATION CAMPAIGN
        // ──────────────────────────────────────────────
        AttestationCampaign::updateOrCreate(
            ['title' => '2024-yil Ish o\'rinlari Attestatsiyasi'],
            [
                'description' => 'SanQvaM 0069-24 asosida 18 omilli mehnat sharoitlari baholash kampaniyasi.',
                'start_date' => now()->subMonth()->format('Y-m-d'),
                'end_date' => now()->addMonths(5)->format('Y-m-d'),
                'status' => 'open',
            ]
        );

        $this->command->info('✅ Seeder muvaffaqiyatli bajarildi!');
        $this->command->table(
            ['Rol', 'Email', 'Parol'],
            [
                ['Admin',           'admin@attestatsiya.uz',    'Admin@2024!'],
                ['Ish beruvchi',    'employer@attestatsiya.uz', 'Employer@2024!'],
                ['Laboratoriya',    'lab@attestatsiya.uz',      'Lab@2024!'],
                ['Institut Ekspеrti', 'institut@attestatsiya.uz', 'Institut@2024!'],
                ['Vazirlik Ekspеrti', 'vazirlik@attestatsiya.uz', 'Vazirlik@2024!'],
                ['HR',              'hr@attestatsiya.uz',       'Hr@2024!'],
            ]
        );
    }
}
