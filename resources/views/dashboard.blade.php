<x-app-layout>
    <x-slot name="header">Boshqaruv paneli</x-slot>

    @php
        $roleLabels = [
            'admin'            => 'Administrator',
            'employer'         => 'Ish beruvchi',
            'commission'       => 'Komissiya a\'zosi',
            'expert'           => 'Vazirlik eksperti',
            'hr'               => 'HR ekspertizasi',
            'institute_expert' => 'Institut eksperti',
            'laboratory'       => 'Laboratoriya',
        ];
        $roleLabel = $roleLabels[$role] ?? $role;
    @endphp

    {{-- ─── HERO / WELCOME ─── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <div class="welcome-card lg:col-span-2">
            <span class="wc-pill">
                <span style="width:6px;height:6px;border-radius:50%;background:#4ecdc4;display:inline-block;box-shadow:0 0 6px #4ecdc4;"></span>
                Onlayn · {{ now()->translatedFormat('d F Y, H:i') }}
            </span>
            <h2 class="wc-title" style="margin-top:14px;">
                Xush kelibsiz, {{ $user->name }}
            </h2>
            <p class="wc-sub">
                Siz tizimga <strong style="color:#4ecdc4;">{{ $roleLabel }}</strong> sifatida kirdingiz.
                Quyida hozirgi holatdagi asosiy ko‘rsatkichlar va tezkor amallar keltirilgan.
            </p>

            <div style="display:flex;gap:10px;margin-top:20px;flex-wrap:wrap;">
                @if($role === 'employer')
                    <x-att-button :href="route('employee.applications.create')" variant="primary" size="sm">
                        + Yangi ariza
                    </x-att-button>
                    <x-att-button :href="route('employer.workplaces.index')" variant="secondary" size="sm" style="background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,255,255,.15);">
                        Ish o‘rinlari
                    </x-att-button>
                @elseif($role === 'admin')
                    <x-att-button :href="route('admin.campaigns.create')" variant="primary" size="sm">
                        + Yangi kampaniya
                    </x-att-button>
                    <x-att-button :href="route('reports.index')" variant="secondary" size="sm" style="background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,255,255,.15);">
                        Hisobotlar
                    </x-att-button>
                @elseif($role === 'hr')
                    <x-att-button :href="route('hr.applications.index')" variant="primary" size="sm">
                        Arizalarni ko‘rib chiqish
                    </x-att-button>
                @elseif($role === 'commission')
                    <x-att-button :href="route('commission.evaluations.index')" variant="primary" size="sm">
                        Tekshirishga o‘tish
                    </x-att-button>
                @elseif($role === 'laboratory')
                    <x-att-button :href="route('laboratory.protocols.index')" variant="primary" size="sm">
                        Protokollar
                    </x-att-button>
                @elseif($role === 'institute_expert')
                    <x-att-button :href="route('institute.expertise.index')" variant="primary" size="sm">
                        Arizalar
                    </x-att-button>
                @elseif($role === 'expert')
                    <x-att-button :href="route('ministry.expertise.index')" variant="primary" size="sm">
                        Yakuniy xulosalar
                    </x-att-button>
                @endif
            </div>
        </div>

        @if($chart)
            <div class="att-card">
                <div class="att-card-header">
                    <div class="att-card-title">{{ $chart['title'] }}</div>
                </div>
                <div class="att-card-body" style="display:flex;align-items:center;justify-content:center;height:230px;">
                    <canvas id="dashboardChart" style="max-height:200px;"></canvas>
                </div>
            </div>
        @endif
    </div>

    {{-- ─── STAT CARDS ─── --}}
    @if(count($stats) > 0)
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin-bottom:28px;">
            @foreach($stats as $s)
                <x-stat-card
                    :icon="$s['icon']"
                    :value="$s['value']"
                    :label="$s['label']"
                    :color="$s['color']"
                />
            @endforeach
        </div>
    @endif

    {{-- ─── ACTIONS + RECENT ACTIVITY (split layout) ─── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Action grid (2 columns) --}}
        <div class="lg:col-span-2">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <h3 style="font-family:var(--font-display);font-size:18px;color:var(--ink);margin:0;">Tezkor amallar</h3>
                <span style="font-size:11.5px;color:var(--muted);font-weight:600;letter-spacing:.4px;">
                    Sizning rolingizga mos sahifalar
                </span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px;">

                @if($role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="action-card">
                        <div class="action-card-body">
                            <div class="action-card-icon icon-teal">⚙</div>
                            <div class="action-card-title">Admin paneli</div>
                            <div class="action-card-desc">Foydalanuvchilar va tizim sozlamalari.</div>
                            <div class="action-card-arrow">→</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.campaigns.index') }}" class="action-card">
                        <div class="action-card-body">
                            <div class="action-card-icon icon-gold">▤</div>
                            <div class="action-card-title">Kampaniyalar</div>
                            <div class="action-card-desc">Attestatsiya kampaniyalarini yaratish va boshqarish.</div>
                            <div class="action-card-arrow">→</div>
                        </div>
                    </a>
                @endif

                @if($role === 'employer')
                    <a href="{{ route('employer.organization.index') }}" class="action-card">
                        <div class="action-card-body">
                            <div class="action-card-icon icon-teal">⌂</div>
                            <div class="action-card-title">Korxona profili</div>
                            <div class="action-card-desc">Tashkilot rekvizitlari va xodimlar tarkibi.</div>
                            <div class="action-card-arrow">→</div>
                        </div>
                    </a>
                    <a href="{{ route('employer.workplaces.index') }}" class="action-card">
                        <div class="action-card-body">
                            <div class="action-card-icon icon-blue">▥</div>
                            <div class="action-card-title">Ish o‘rinlari</div>
                            <div class="action-card-desc">Korxona tarkibidagi ish o‘rinlari.</div>
                            <div class="action-card-arrow">→</div>
                        </div>
                    </a>
                    <a href="{{ route('employer.tenders.index') }}" class="action-card">
                        <div class="action-card-body">
                            <div class="action-card-icon icon-gold">⇆</div>
                            <div class="action-card-title">Tender va shartnomalar</div>
                            <div class="action-card-desc">Laboratoriya tanlash va shartnomalar.</div>
                            <div class="action-card-arrow">→</div>
                        </div>
                    </a>
                    <a href="{{ route('employee.applications.create') }}" class="action-card">
                        <div class="action-card-body">
                            <div class="action-card-icon icon-green">+</div>
                            <div class="action-card-title">Yangi ariza</div>
                            <div class="action-card-desc">Ish o‘rnini attestatsiyaga taqdim etish.</div>
                            <div class="action-card-arrow">→</div>
                        </div>
                    </a>
                    <a href="{{ route('employer.expertise.index') }}" class="action-card">
                        <div class="action-card-body">
                            <div class="action-card-icon icon-red">◈</div>
                            <div class="action-card-title">Davlat ekspertizasi</div>
                            <div class="action-card-desc">Arizalar yig‘indisini yuborish va holati.</div>
                            <div class="action-card-arrow">→</div>
                        </div>
                    </a>
                @endif

                @if($role === 'commission')
                    <a href="{{ route('commission.evaluations.index') }}" class="action-card">
                        <div class="action-card-body">
                            <div class="action-card-icon icon-gold">⌕</div>
                            <div class="action-card-title">Tekshirish</div>
                            <div class="action-card-desc">Ish o‘rinlarini joyida tekshirish va o‘lchov natijalari.</div>
                            <div class="action-card-arrow">→</div>
                        </div>
                    </a>
                @endif

                @if($role === 'hr')
                    <a href="{{ route('hr.applications.index') }}" class="action-card">
                        <div class="action-card-body">
                            <div class="action-card-icon icon-blue">▤</div>
                            <div class="action-card-title">Arizalarni ko‘rib chiqish</div>
                            <div class="action-card-desc">Yangi arizalarni tasdiqlash, rad etish, yakunlash.</div>
                            <div class="action-card-arrow">→</div>
                        </div>
                    </a>
                @endif

                @if($role === 'laboratory')
                    <a href="{{ route('laboratory.profile.index') }}" class="action-card">
                        <div class="action-card-body">
                            <div class="action-card-icon icon-teal">◉</div>
                            <div class="action-card-title">Laboratoriya profili</div>
                            <div class="action-card-desc">Akkreditatsiya va o‘lchov sohalari.</div>
                            <div class="action-card-arrow">→</div>
                        </div>
                    </a>
                    <a href="{{ route('laboratory.protocols.index') }}" class="action-card">
                        <div class="action-card-body">
                            <div class="action-card-icon icon-gold">▤</div>
                            <div class="action-card-title">O‘lchov protokollari</div>
                            <div class="action-card-desc">18 ta omil bo‘yicha o‘lchov xaritalari.</div>
                            <div class="action-card-arrow">→</div>
                        </div>
                    </a>
                    <a href="{{ route('laboratory.workplaces.index') }}" class="action-card">
                        <div class="action-card-body">
                            <div class="action-card-icon icon-blue">⌬</div>
                            <div class="action-card-title">Ish o‘rinlari o‘lchovi</div>
                            <div class="action-card-desc">Faol ish o‘rinlarini topib, o‘lchov yaratish.</div>
                            <div class="action-card-arrow">→</div>
                        </div>
                    </a>
                @endif

                @if($role === 'institute_expert')
                    <a href="{{ route('institute.expertise.index') }}" class="action-card">
                        <div class="action-card-body">
                            <div class="action-card-icon icon-teal">⌬</div>
                            <div class="action-card-title">Dastlabki baholash</div>
                            <div class="action-card-desc">Laboratoriya o‘lchovlarini ko‘rib chiqish (15 kun).</div>
                            <div class="action-card-arrow">→</div>
                        </div>
                    </a>
                @endif

                @if($role === 'expert')
                    <a href="{{ route('ministry.expertise.index') }}" class="action-card">
                        <div class="action-card-body">
                            <div class="action-card-icon icon-red">⚖</div>
                            <div class="action-card-title">Davlat ekspertizasi</div>
                            <div class="action-card-desc">Yakuniy xulosani elektron tasdiqlash (10 kun).</div>
                            <div class="action-card-arrow">→</div>
                        </div>
                    </a>
                @endif

                @if(in_array($role, ['admin', 'expert']))
                    <a href="{{ route('reports.index') }}" class="action-card" style="background:linear-gradient(135deg,#0e1117,#1a2233);border-color:rgba(255,255,255,.08);">
                        <div class="action-card-body">
                            <div class="action-card-icon" style="background:rgba(78,205,196,.12);color:#4ecdc4;">▦</div>
                            <div class="action-card-title" style="color:white;">Hisobotlar</div>
                            <div class="action-card-desc" style="color:rgba(255,255,255,.5);">Kampaniya bo‘yicha natijalar va eksport.</div>
                            <div class="action-card-arrow" style="background:rgba(78,205,196,.12);color:#4ecdc4;">→</div>
                        </div>
                    </a>
                @endif

                <a href="{{ route('profile.edit') }}" class="action-card">
                    <div class="action-card-body">
                        <div class="action-card-icon icon-purple">◉</div>
                        <div class="action-card-title">Profil sozlamalari</div>
                        <div class="action-card-desc">Ism, email va parolni yangilash.</div>
                        <div class="action-card-arrow">→</div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Recent activity (1 column) --}}
        <div>
            <div class="att-card">
                <div class="att-card-header">
                    <div class="att-card-title">
                        <span style="color:var(--teal);">◷</span> So‘nggi faollik
                    </div>
                </div>
                <div class="att-card-body">
                    @if(count($recent) > 0)
                        <div class="activity-list">
                            @foreach($recent as $r)
                                <div class="activity-item">
                                    <div class="activity-icon icon-{{ $r['color'] }}">{!! $r['icon'] !!}</div>
                                    <div class="activity-meta">
                                        <div class="activity-title">{{ $r['title'] }}</div>
                                        <div class="activity-sub">{{ $r['sub'] }}</div>
                                    </div>
                                    <div class="activity-time">{{ $r['when'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <x-empty-state
                            icon="◷"
                            title="Faollik yo‘q"
                            description="Tizim aktivligi shu yerda ko‘rinadi. Birinchi amalingizni bajarganingizdan so‘ng yozuvlar paydo bo‘ladi."
                        />
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @if($chart)
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const ctx = document.getElementById('dashboardChart');
                    if (!ctx || !window.Chart) return;
                    new window.Chart(ctx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: @json($chart['labels']),
                            datasets: [{
                                data: @json($chart['data']),
                                backgroundColor: @json($chart['colors']),
                                borderWidth: 0,
                                hoverOffset: 6,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '68%',
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: { boxWidth: 10, padding: 14, font: { size: 11.5 } }
                                },
                                tooltip: {
                                    backgroundColor: '#0e1117',
                                    padding: 10,
                                    cornerRadius: 8,
                                    titleFont: { weight: '700' },
                                    bodyFont: { size: 12 },
                                }
                            }
                        }
                    });
                });
            </script>
        @endif
    @endpush
</x-app-layout>
