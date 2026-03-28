<x-app-layout>
    <x-slot name="header">⚙️ Administrator paneli</x-slot>

    {{-- Stats Grid --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 32px;" class="stats-grid-responsive">
        <div class="stat-card stat-card-teal">
            <div class="stat-icon">👥</div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-label">Jami foydalanuvchilar</div>
            <div style="display: flex; flex-direction: column; gap: 4px; margin-top: 12px; font-size: 12px;">
                <div style="display: flex; justify-content: space-between; color: var(--muted);"><span>Admin:</span><span style="font-weight: 700;">{{ $adminCount }}</span></div>
                <div style="display: flex; justify-content: space-between; color: var(--muted);"><span>Ekspert:</span><span style="font-weight: 700;">{{ $expertCount }}</span></div>
                <div style="display: flex; justify-content: space-between; color: var(--muted);"><span>Komissiya:</span><span style="font-weight: 700;">{{ $commissionCount }}</span></div>
                <div style="display: flex; justify-content: space-between; color: var(--muted);"><span>Ish beruvchi:</span><span style="font-weight: 700;">{{ $employerCount }}</span></div>
            </div>
        </div>

        <div class="stat-card stat-card-gold" style="background: var(--ink); color: white;">
            <div class="stat-icon">📄</div>
            <div class="stat-value" style="color: white;">{{ $totalApplications }}</div>
            <div class="stat-label" style="color: rgba(255,255,255,0.5);">Jami arizalar</div>
        </div>

        <div class="stat-card stat-card-green">
            <div class="stat-icon">📋</div>
            <div class="stat-value">{{ $activeCampaigns }}</div>
            <div class="stat-label">Ochiq kampaniyalar</div>
            <a href="{{ route('admin.campaigns.index') }}" style="display: inline-block; margin-top: 12px; font-size: 13px; color: var(--teal); font-weight: 600; text-decoration: none;">
                Boshqarish →
            </a>
        </div>

        <div class="stat-card stat-card-red" style="background: var(--ink); color: white; cursor: pointer;" onclick="window.location='{{ route('reports.index') }}'">
            <div class="stat-icon">📊</div>
            <div class="stat-value" style="color: #4ecdc4;">HISOBOT</div>
            <div class="stat-label" style="color: rgba(255,255,255,0.5);">Natijalarni ko'rish</div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            .stats-grid-responsive { grid-template-columns: 1fr 1fr !important; gap: 12px !important; }
        }
        @media (max-width: 480px) {
            .stats-grid-responsive { grid-template-columns: 1fr !important; }
        }
    </style>

    {{-- Info section --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <div class="att-card">
            <div class="att-card-header">
                <div class="att-card-title">📌 Platforma maqsadi</div>
            </div>
            <div class="att-card-body">
                <p style="color: #555; line-height: 1.7; font-size: 14px; margin: 0;">
                    Ushbu platforma O'zbekiston Respublikasi Vazirlar Mahkamasining 263-sonli qaroriga asosan mehnat sharoitlarini attestatsiyadan o'tkazishni raqamlashtirish uchun mo'ljallangan.
                </p>
            </div>
        </div>
        <div class="att-card">
            <div class="att-card-header">
                <div class="att-card-title">✅ So'nggi yangilanishlar</div>
            </div>
            <div class="att-card-body">
                <div style="display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: #555;">
                    <div style="display: flex; align-items: center; gap: 8px;">✅ Ish o'rni klassifikatsiyasi (1, 2, 3-klasslar)</div>
                    <div style="display: flex; align-items: center; gap: 8px;">✅ Premium dizayn va sidebar navigatsiya</div>
                    <div style="display: flex; align-items: center; gap: 8px;">✅ Mobil qurilmalar uchun optimal dizayn</div>
                    <div style="display: flex; align-items: center; gap: 8px;">✅ O'lchov natijalarini kiritish moduli</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
