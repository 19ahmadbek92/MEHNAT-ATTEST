<x-app-layout>
    <x-slot name="header">Boshqaruv paneli</x-slot>

    {{-- Statistika - admin, davlat eksperti va HR uchun --}}
    @if(in_array(auth()->user()->role, ['admin', 'expert', 'hr'], true))
        @php
            $totalApps    = \App\Models\AttestationApplication::count();
            $pendingApps  = \App\Models\AttestationApplication::where('status', 'submitted')->count();
            $approvedApps = \App\Models\AttestationApplication::where('status', 'hr_approved')->count();
            $finalApps    = \App\Models\AttestationApplication::where('status', 'finalized')->count();
        @endphp
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 28px;" class="stats-grid">
            <div class="stat-card stat-card-teal">
                <div class="stat-icon">🏭</div>
                <div class="stat-value">{{ $totalApps }}</div>
                <div class="stat-label">Jami arizalar</div>
            </div>
            <div class="stat-card stat-card-gold">
                <div class="stat-icon">⏳</div>
                <div class="stat-value">{{ $pendingApps }}</div>
                <div class="stat-label">Yangi arizalar</div>
            </div>
            <div class="stat-card stat-card-green">
                <div class="stat-icon">✅</div>
                <div class="stat-value">{{ $finalApps }}</div>
                <div class="stat-label">Yakunlangan</div>
            </div>
            <div class="stat-card stat-card-red">
                <div class="stat-icon">🔍</div>
                <div class="stat-value">{{ $approvedApps }}</div>
                <div class="stat-label">Tekshiruvda</div>
            </div>
        </div>
        <style>
            @media (max-width: 900px) { .stats-grid { grid-template-columns: 1fr 1fr !important; } }
            @media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr !important; } }
        </style>
    @endif

    {{-- Xush kelibsiz va Chart.js qismi --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8 mt-4">
        {{-- Xush kelibsiz (Chap qism) --}}
        <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden flex flex-col justify-center">
            <div class="absolute top-0 right-0 p-8 opacity-20 transform translate-x-1/4 -translate-y-1/4">
                <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13h-13L12 6.5z"/></svg>
            </div>
            <div class="relative z-10 flex items-center gap-4 mb-4">
                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-3xl shadow-inner border border-white/30">
                    👋
                </div>
                <div>
                    <h2 class="text-3xl font-black">Xush kelibsiz, {{ auth()->user()->name }}!</h2>
                    <p class="text-indigo-100 mt-1 max-w-sm">Tizimga rasmiy 
                        <span class="bg-white/20 px-2 py-0.5 rounded text-white font-bold ml-1">
                            @switch(auth()->user()->role)
                                @case('admin') Administrator @break
                                @case('employer') Ish beruvchi @break
                                @case('commission') Komissiya a'zosi @break
                                @case('expert') Davlat Ekspеrti @break
                                @case('hr') HR (ekspertiza) @break
                                @case('institute_expert') Institut Ekspеrti @break
                                @case('laboratory') Laboratoriya (Tashkilot) @break
                                @default {{ auth()->user()->role }}
                            @endswitch
                        </span> 
                        sifatida kirdingiz.
                    </p>
                </div>
            </div>
        </div>

        {{-- Analitika Chart (O'ng qism) --}}
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 relative">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Umumiy Ko'rsatkichlar Analitikasi</h3>
            <div class="relative w-full h-48 flex justify-center items-center">
                <canvas id="dashboardChart"></canvas>
            </div>
        </div>
    </div>

    @php
        // Chart ma'lumotlarini tayyorlash
        $chartLabels = [];
        $chartData = [];
        $chartColors = [];

        if (in_array(auth()->user()->role, ['admin', 'expert'], true)) {
            $chartLabels = ['Kutayotgan', 'Ko\'rib chiqilmoqda', 'Yakunlangan (Tasdiqlangan)', 'Rad etilgan'];
            $chartData = [
                \App\Models\StateExpertiseApplication::where('ministry_status', 'pending')->count(),
                \App\Models\StateExpertiseApplication::where('ministry_status', 'returned')->count(),
                \App\Models\StateExpertiseApplication::where('ministry_status', 'approved')->count(),
                0 // example placeholder
            ];
            $chartColors = ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'];
        } elseif (auth()->user()->role === 'hr') {
            $chartLabels = ['Yangi', 'HR tasdiqlangan', 'Rad etilgan', 'Yakunlangan'];
            $chartData = [
                \App\Models\AttestationApplication::where('status', 'submitted')->count(),
                \App\Models\AttestationApplication::where('status', 'hr_approved')->count(),
                \App\Models\AttestationApplication::where('status', 'hr_rejected')->count(),
                \App\Models\AttestationApplication::where('status', 'finalized')->count(),
            ];
            $chartColors = ['#f59e0b', '#3b82f6', '#ef4444', '#10b981'];
        } elseif (auth()->user()->role === 'employer') {
            $orgId = auth()->user()->organization_id;
            $chartLabels = ['Kutayotgan joylar', 'Jarayonda', 'Attestatsiyalangan'];
            $chartData = [
                \App\Models\Workplace::where('organization_id', $orgId)->where('status', 'pending')->count(),
                \App\Models\Workplace::where('organization_id', $orgId)->where('status', 'in_progress')->count(),
                \App\Models\Workplace::where('organization_id', $orgId)->where('status', 'attested')->count(),
            ];
            $chartColors = ['#f59e0b', '#3b82f6', '#10b981'];
        } else {
            $chartLabels = ['Ma\'lumot yo\'q'];
            $chartData = [100];
            $chartColors = ['#e5e7eb'];
        }
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('dashboardChart').getContext('2d');
            var dashboardChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: {!! json_encode($chartColors) !!},
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    },
                    cutout: '70%'
                }
            });
        });
    </script>

    {{-- Menyu kartochkalari --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px;">

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="action-card">
                <div class="action-card-body">
                    <div class="action-card-icon icon-teal">⚙️</div>
                    <div class="action-card-title">Admin paneli</div>
                    <div class="action-card-desc">Foydalanuvchilar va tizim sozlamalari.</div>
                    <div class="action-card-arrow">→</div>
                </div>
            </a>
            <a href="{{ route('admin.campaigns.index') }}" class="action-card">
                <div class="action-card-body">
                    <div class="action-card-icon icon-gold">📋</div>
                    <div class="action-card-title">Kampaniyalar</div>
                    <div class="action-card-desc">Attestatsiya kampaniyalarini yaratish va boshqarish.</div>
                    <div class="action-card-arrow">→</div>
                </div>
            </a>
        @endif

        @if(auth()->user()->role === 'employer')
            <a href="{{ route('employer.organization.index') }}" class="action-card">
                <div class="action-card-body">
                    <div class="action-card-icon icon-teal">🏢</div>
                    <div class="action-card-title">Korxona profili</div>
                    <div class="action-card-desc">Tashkilot rekvizitlari va xodimlar tarkibi.</div>
                    <div class="action-card-arrow">→</div>
                </div>
            </a>
            <a href="{{ route('employer.tenders.index') }}" class="action-card">
                <div class="action-card-body">
                    <div class="action-card-icon icon-gold">🤝</div>
                    <div class="action-card-title">Tender va shartnomalar</div>
                    <div class="action-card-desc">Laboratoriya tanlash va shartnomalar rasmiylashtirish.</div>
                    <div class="action-card-arrow">→</div>
                </div>
            </a>
            <a href="{{ route('employer.workplaces.index') }}" class="action-card">
                <div class="action-card-body">
                    <div class="action-card-icon icon-blue">🏭</div>
                    <div class="action-card-title">Ish o'rinlari</div>
                    <div class="action-card-desc">Korxona tarkibidagi ish o'rinlari.</div>
                    <div class="action-card-arrow">→</div>
                </div>
            </a>
            <a href="{{ route('employee.applications.create') }}" class="action-card">
                <div class="action-card-body">
                    <div class="action-card-icon icon-green">📝</div>
                    <div class="action-card-title">Yangi ariza</div>
                    <div class="action-card-desc">Yangi ish o'rnini attestatsiyaga taqdim etish.</div>
                    <div class="action-card-arrow">→</div>
                </div>
            </a>
            <a href="{{ route('employer.expertise.index') }}" class="action-card">
                <div class="action-card-body">
                    <div class="action-card-icon icon-red">📑</div>
                    <div class="action-card-title">Davlat ekspertizasi</div>
                    <div class="action-card-desc">Barcha arizalar yig'indisini yuborish va holati.</div>
                    <div class="action-card-arrow">→</div>
                </div>
            </a>
        @endif

        @if(auth()->user()->role === 'commission')
            <a href="{{ route('commission.evaluations.index') }}" class="action-card">
                <div class="action-card-body">
                    <div class="action-card-icon icon-gold">🔍</div>
                    <div class="action-card-title">Tekshirish</div>
                    <div class="action-card-desc">Ish o'rinlarini joyida tekshirish va o'lchov natijalarini kiritish.</div>
                    <div class="action-card-arrow">→</div>
                </div>
            </a>
        @endif

        @if(auth()->user()->role === 'hr')
            <a href="{{ route('hr.applications.index') }}" class="action-card">
                <div class="action-card-body">
                    <div class="action-card-icon icon-blue">📋</div>
                    <div class="action-card-title">Arizalarni ko‘rib chiqish</div>
                    <div class="action-card-desc">Yangi arizalarni tasdiqlash yoki rad etish, yakuniy sinf belgilash.</div>
                    <div class="action-card-arrow">→</div>
                </div>
            </a>
        @endif

        @if(auth()->user()->role === 'laboratory')
            <a href="{{ route('laboratory.profile.index') }}" class="action-card">
                <div class="action-card-body">
                    <div class="action-card-icon icon-teal">🧪</div>
                    <div class="action-card-title">Laboratoriya profili</div>
                    <div class="action-card-desc">Akkreditatsiya va ma'qullash sohalari.</div>
                    <div class="action-card-arrow">→</div>
                </div>
            </a>
            <a href="{{ route('laboratory.protocols.index') }}" class="action-card">
                <div class="action-card-body">
                    <div class="action-card-icon icon-gold">📋</div>
                    <div class="action-card-title">O'lchov protokollari</div>
                    <div class="action-card-desc">Ish o'rinlari xaritalarini to'ldirish (18 omil).</div>
                    <div class="action-card-arrow">→</div>
                </div>
            </a>
        @endif

        @if(auth()->user()->role === 'institute_expert')
            <a href="{{ route('institute.expertise.index') }}" class="action-card">
                <div class="action-card-body">
                    <div class="action-card-icon icon-teal">🏫</div>
                    <div class="action-card-title">Dastlabki baholash</div>
                    <div class="action-card-desc">Laboratoriya o'lchovlarini ko'rib chiqish (15 kun).</div>
                    <div class="action-card-arrow">→</div>
                </div>
            </a>
        @endif

        @if(auth()->user()->role === 'expert')
            <a href="{{ route('ministry.expertise.index') }}" class="action-card">
                <div class="action-card-body">
                    <div class="action-card-icon icon-red">🏛</div>
                    <div class="action-card-title">Davlat ekspertizasi</div>
                    <div class="action-card-desc">Yakuniy xulosani elektron tasdiqlash (10 kun).</div>
                    <div class="action-card-arrow">→</div>
                </div>
            </a>
        @endif

        @if(in_array(auth()->user()->role, ['admin', 'expert']))
            <a href="{{ route('reports.index') }}" class="action-card" style="background: linear-gradient(135deg, #0e1117, #1a2233); border-color: rgba(255,255,255,0.08);">
                <div class="action-card-body">
                    <div class="action-card-icon" style="background: rgba(78,205,196,0.1); font-size: 24px;">📊</div>
                    <div class="action-card-title" style="color: white;">Hisobotlar</div>
                    <div class="action-card-desc" style="color: rgba(255,255,255,0.4);">Kampaniya bo'yicha natijalarni ko'rish va eksport qilish.</div>
                    <div class="action-card-arrow" style="background: rgba(78,205,196,0.1); color: #4ecdc4;">→</div>
                </div>
            </a>
        @endif
    </div>
</x-app-layout>
