<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name', 'E-Attestatsiya') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-serif-display:400|dm-sans:300,400,500,600,700|figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body>

    {{-- ─── MOBILE HEADER ─── --}}
    <div class="mobile-header">
        <button type="button" class="mobile-menu-btn" aria-label="Menyu" onclick="document.querySelector('.sidebar').classList.add('open'); document.querySelector('.sidebar-overlay').classList.add('open');">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <span style="font-family:var(--font-display);font-size:18px;">Mehnat<span style="color:#4ecdc4;">Attest</span></span>
        <a href="{{ route('profile.edit') }}" class="mobile-menu-btn" aria-label="Profil">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </a>
    </div>
    <div class="sidebar-overlay" onclick="document.querySelector('.sidebar').classList.remove('open');this.classList.remove('open');"></div>

    {{-- ─── SIDEBAR ─── --}}
    <aside class="sidebar" aria-label="Asosiy navigatsiya">
        <div class="sidebar-logo">
            <div class="logo-icon" aria-hidden="true">⚖</div>
            <div class="logo-mark">Mehnat<span>Attest</span></div>
            <div class="logo-sub">Attestatsiya platformasi</div>
        </div>

        <nav class="nav-section">
            <div class="nav-label">{{ __('messages.nav.main') }}</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">▦</span> {{ __('messages.dashboard') }}
            </a>

            @php $role = Auth::user()->role; @endphp

            @if ($role === 'admin')
                <div class="nav-label">{{ __('messages.nav.admin') }}</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">⚙</span> {{ __('messages.nav.admin_panel') }}
                </a>
                <a href="{{ route('admin.campaigns.index') }}" class="nav-item {{ request()->routeIs('admin.campaigns.*') ? 'active' : '' }}">
                    <span class="nav-icon">▤</span> {{ __('messages.nav.campaigns') }}
                </a>
                <a href="{{ route('admin.audit.index') }}" class="nav-item {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
                    <span class="nav-icon">◷</span> {{ __('messages.nav.audit_log') }}
                </a>
            @endif

            @if ($role === 'employer')
                <div class="nav-label">{{ __('messages.nav.employer') }}</div>
                <a href="{{ route('employer.organization.index') }}" class="nav-item {{ request()->routeIs('employer.organization.*') ? 'active' : '' }}">
                    <span class="nav-icon">⌂</span> {{ __('messages.nav.organization') }}
                </a>
                <a href="{{ route('employer.workplaces.index') }}" class="nav-item {{ request()->routeIs('employer.workplaces.*') ? 'active' : '' }}">
                    <span class="nav-icon">▥</span> {{ __('messages.workplaces') }}
                </a>
                <a href="{{ route('employer.tenders.index') }}" class="nav-item {{ request()->routeIs('employer.tenders.*') ? 'active' : '' }}">
                    <span class="nav-icon">⇆</span> {{ __('messages.nav.tenders') }}
                </a>
                <a href="{{ route('employee.applications.index') }}" class="nav-item {{ request()->routeIs('employee.applications.*') ? 'active' : '' }}">
                    <span class="nav-icon">▤</span> {{ __('messages.nav.applications') }}
                </a>
                <a href="{{ route('employer.expertise.index') }}" class="nav-item {{ request()->routeIs('employer.expertise.*') ? 'active' : '' }}">
                    <span class="nav-icon">◈</span> {{ __('messages.nav.state_expertise') }}
                </a>
            @endif

            @if ($role === 'laboratory')
                <div class="nav-label">{{ __('messages.nav.laboratory') }}</div>
                <a href="{{ route('laboratory.profile.index') }}" class="nav-item {{ request()->routeIs('laboratory.profile.*') ? 'active' : '' }}">
                    <span class="nav-icon">◉</span> {{ __('messages.nav.my_profile') }}
                </a>
                <a href="{{ route('laboratory.protocols.index') }}" class="nav-item {{ request()->routeIs('laboratory.protocols.*') ? 'active' : '' }}">
                    <span class="nav-icon">▤</span> {{ __('messages.nav.protocols') }}
                </a>
                <a href="{{ route('laboratory.workplaces.index') }}" class="nav-item {{ request()->routeIs('laboratory.workplaces.*', 'laboratory.measurements.*') ? 'active' : '' }}">
                    <span class="nav-icon">⌬</span> {{ __('messages.nav.workplace_meas') }}
                </a>
            @endif

            @if ($role === 'commission')
                <div class="nav-label">{{ __('messages.nav.commission') }}</div>
                <a href="{{ route('commission.evaluations.index') }}" class="nav-item {{ request()->routeIs('commission.evaluations.*') ? 'active' : '' }}">
                    <span class="nav-icon">⌕</span> {{ __('messages.nav.review') }}
                    @php $pendingCount = \App\Models\AttestationApplication::where('status', 'hr_approved')->count(); @endphp
                    @if ($pendingCount > 0)<span class="nav-badge">{{ $pendingCount }}</span>@endif
                </a>
            @endif

            @if ($role === 'hr')
                <div class="nav-label">{{ __('messages.nav.hr') }}</div>
                <a href="{{ route('hr.applications.index') }}" class="nav-item {{ request()->routeIs('hr.applications.*') ? 'active' : '' }}">
                    <span class="nav-icon">▤</span> {{ __('messages.nav.review_apps') }}
                    @php $hrNew = \App\Models\AttestationApplication::where('status', 'submitted')->count(); @endphp
                    @if ($hrNew > 0)<span class="nav-badge">{{ $hrNew }}</span>@endif
                </a>
            @endif

            @if ($role === 'institute_expert')
                <div class="nav-label">{{ __('messages.nav.institute') }}</div>
                <a href="{{ route('institute.expertise.index') }}" class="nav-item {{ request()->routeIs('institute.expertise.*') ? 'active' : '' }}">
                    <span class="nav-icon">⌬</span> {{ __('messages.nav.applications') }}
                    @php $instCount = \App\Models\StateExpertiseApplication::where('institute_status', 'pending')->count(); @endphp
                    @if ($instCount > 0)<span class="nav-badge">{{ $instCount }}</span>@endif
                </a>
            @endif

            @if ($role === 'expert')
                <div class="nav-label">{{ __('messages.nav.state_expertise') }}</div>
                <a href="{{ route('ministry.expertise.index') }}" class="nav-item {{ request()->routeIs('ministry.expertise.*') ? 'active' : '' }}">
                    <span class="nav-icon">⚖</span> {{ __('messages.nav.final_conclusions') }}
                    @php $minCount = \App\Models\StateExpertiseApplication::where('institute_status', 'approved')->where('ministry_status', 'pending')->count(); @endphp
                    @if ($minCount > 0)<span class="nav-badge">{{ $minCount }}</span>@endif
                </a>
            @endif

            @if (in_array($role, ['admin', 'expert'], true))
                <div class="nav-label">{{ __('messages.nav.report') }}</div>
                <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <span class="nav-icon">▦</span> {{ __('messages.reports') }}
                </a>
            @endif

            <div class="nav-label">{{ __('messages.nav.settings') }}</div>
            <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <span class="nav-icon">◉</span> {{ __('messages.profile') }}
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div>
                    <div class="name">{{ Auth::user()->name }}</div>
                    <div class="role-tag">
                        @php $roleKey = 'messages.roles.'.$role; @endphp
                        {{ trans()->has($roleKey) ? __($roleKey) : ucfirst($role) }}
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-logout-btn">{{ __('messages.logout') }}</button>
                </form>
            </div>
            <div style="margin-top:10px;padding-top:8px;border-top:1px solid rgba(255,255,255,.05);font-size:9.5px;color:rgba(255,255,255,.22);letter-spacing:.5px;">
                VM Qarori №263 · 15.09.2014
            </div>
        </div>
    </aside>

    {{-- ─── MAIN ─── --}}
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <div class="topbar-title">
                    @isset($header){{ $header }}@else {{ __('messages.dashboard') }} @endisset
                </div>
            </div>

            <div class="topbar-actions">
                {{-- search (admin/expert ko'rishi yetadi, lekin har kim tanlashi mumkin) --}}
                <div class="topbar-search hidden md:block">
                    <span class="si">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    </span>
                    <input type="search" placeholder="{{ __('messages.nav.search_placeholder') }}" aria-label="{{ __('messages.common.search') }}" id="topbar-search-input" autocomplete="off" />
                </div>

                {{-- language --}}
                <div class="hidden lg:inline-flex" style="background:var(--paper);border:1px solid var(--border-soft);border-radius:999px;padding:3px;">
                    @foreach (['uz' => 'UZ', 'ru' => 'RU', 'en' => 'EN'] as $code => $label)
                        <a href="{{ route('lang.switch', $code) }}"
                           style="font-size:11px;font-weight:700;letter-spacing:.4px;padding:4px 10px;border-radius:999px;text-decoration:none;
                                  {{ app()->getLocale() === $code ? 'background:var(--ink);color:#fff;' : 'color:var(--muted);' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                {{-- bell + dropdown --}}
                <div x-data="notifBell()" x-init="init()" style="position:relative;">
                    <button type="button" class="topbar-icon-btn" aria-label="{{ __('messages.nav.notifications') }}" @click="toggle()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        <span class="dot" x-show="items.length > 0" x-cloak></span>
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         style="position:absolute;right:0;top:46px;width:340px;background:white;border:1px solid var(--border-soft);border-radius:var(--r-lg);box-shadow:var(--shadow-xl);overflow:hidden;z-index:200;">
                        <div style="padding:14px 18px;border-bottom:1px solid var(--border-soft);display:flex;align-items:center;justify-content:space-between;background:linear-gradient(135deg,#faf8f4,#fff);">
                            <span style="font-weight:700;font-size:13px;color:var(--ink);">{{ __('messages.nav.notifications') }}</span>
                            <span class="text-muted" style="font-size:11px;">{{ __('messages.nav.last_n') }}</span>
                        </div>
                        <div style="max-height:400px;overflow-y:auto;">
                            <template x-if="loading">
                                <div style="padding:24px;text-align:center;color:var(--muted);font-size:13px;">{{ __('messages.nav.loading') }}</div>
                            </template>
                            <template x-if="!loading && items.length === 0">
                                <div style="padding:32px 24px;text-align:center;">
                                    <div style="font-size:24px;color:var(--muted);margin-bottom:6px;">◌</div>
                                    <div class="text-muted" style="font-size:13px;">{{ __('messages.nav.no_notifications') }}</div>
                                </div>
                            </template>
                            <template x-for="n in items" :key="n.id">
                                <div style="padding:12px 18px;border-bottom:1px solid var(--border-soft);display:flex;gap:12px;">
                                    <div style="width:30px;height:30px;border-radius:9px;background:var(--teal-light);color:var(--teal);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;">◷</div>
                                    <div style="flex:1;min-width:0;">
                                        <div style="font-size:13px;font-weight:600;color:var(--ink);" x-text="n.title"></div>
                                        <div class="text-muted" style="font-size:11.5px;margin-top:2px;" x-text="n.subject || ''"></div>
                                        <div class="text-muted" style="font-size:10.5px;margin-top:3px;letter-spacing:.4px;" x-text="n.when"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- user pill --}}
                <a href="{{ route('profile.edit') }}" class="topbar-user-pill" aria-label="Profil">
                    <span class="av">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                    <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="flash flash-success">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @if(session('status'))
            <div class="flash flash-info">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                {{ session('status') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="flash flash-warn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                {{ session('warning') }}
            </div>
        @endif

        <div class="page-content page-animate">
            {{ $slot }}
        </div>
    </div>

    <div class="notif-stack" id="notif-stack" aria-live="polite"></div>

    @stack('scripts')
</body>
</html>
