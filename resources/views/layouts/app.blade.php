<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'E-Attestatsiya') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
    /* ── DESIGN TOKENS ── */
    :root {
        --ink:          #0e1117;
        --paper:        #f4f2ee;
        --cream:        #eae7df;
        --teal:         #0d6e6e;
        --teal-mid:     #0a8080;
        --teal-light:   #d6eeee;
        --teal-glow:    rgba(13,110,110,0.15);
        --gold:         #b8841e;
        --gold-bright:  #c9952a;
        --gold-light:   #fdf2d5;
        --red:          #b83232;
        --red-light:    #fce8e8;
        --green:        #166534;
        --green-bright: #1a7a40;
        --green-light:  #dcf5e7;
        --blue:         #1e40af;
        --blue-light:   #dbeafe;
        --muted:        #78756e;
        --border:       #dddad2;
        --border-soft:  #eae7e0;
        --white:        #ffffff;
        --shadow-sm: 0 1px 4px rgba(14,17,23,0.06);
        --shadow:    0 2px 12px rgba(14,17,23,0.09);
        --shadow-lg: 0 8px 32px rgba(14,17,23,0.14);
        --r:  10px;
        --rl: 14px;
    }

    *, *::before, *::after { box-sizing: border-box; }

    /* ── SIDEBAR ── */
    .sidebar {
        position: fixed; left: 0; top: 0; bottom: 0; width: 256px;
        background: linear-gradient(180deg, #0e1117 0%, #111927 100%);
        display: flex; flex-direction: column;
        z-index: 100;
        transition: transform 0.3s ease;
        box-shadow: 2px 0 24px rgba(0,0,0,0.2);
    }
    .sidebar-logo {
        padding: 24px 20px 18px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
    }
    .sidebar-logo .logo-icon {
        width: 38px; height: 38px; border-radius: 10px;
        background: linear-gradient(135deg, #0d6e6e, #4ecdc4);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; margin-bottom: 10px;
        box-shadow: 0 3px 12px rgba(78,205,196,0.3);
    }
    .sidebar-logo .logo-mark {
        font-family: 'DM Serif Display', serif;
        font-size: 20px; color: white; line-height: 1;
    }
    .sidebar-logo .logo-mark span { color: #4ecdc4; }
    .sidebar-logo .logo-sub {
        font-size: 10px; color: rgba(255,255,255,0.28);
        letter-spacing: 1.8px; text-transform: uppercase; margin-top: 3px;
    }
    .nav-section {
        padding: 14px 10px; flex: 1; overflow-y: auto;
    }
    .nav-section::-webkit-scrollbar { width: 3px; }
    .nav-section::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
    .nav-label {
        font-size: 9.5px; letter-spacing: 1.8px; text-transform: uppercase;
        color: rgba(255,255,255,0.25); padding: 10px 10px 5px;
    }
    .nav-item {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 10px; border-radius: 8px;
        color: rgba(255,255,255,0.52); font-size: 13px; font-weight: 500;
        transition: all 0.16s; text-decoration: none; cursor: pointer;
        position: relative;
    }
    .nav-item:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.88); text-decoration: none; }
    .nav-item.active {
        background: linear-gradient(135deg, rgba(78,205,196,0.16), rgba(13,110,110,0.08));
        color: #4ecdc4;
        box-shadow: inset 0 0 0 1px rgba(78,205,196,0.18);
    }
    .nav-item.active::before {
        content: ''; position: absolute; left: -10px; top: 25%; bottom: 25%;
        width: 3px; border-radius: 0 3px 3px 0; background: #4ecdc4;
    }
    .nav-item .nav-icon { font-size: 15px; width: 20px; text-align: center; }
    .nav-badge {
        margin-left: auto;
        background: linear-gradient(135deg, #b8841e, #d4a030);
        color: white; font-size: 10px; font-weight: 700;
        padding: 2px 8px; border-radius: 10px;
    }
    .sidebar-footer {
        padding: 12px 18px;
        border-top: 1px solid rgba(255,255,255,0.06);
        background: rgba(0,0,0,0.12);
    }
    .sidebar-user { display: flex; align-items: center; justify-content: space-between; }
    .sidebar-user .name { color: rgba(255,255,255,0.72); font-size: 12.5px; font-weight: 600; }
    .sidebar-user .role-tag {
        display: inline-block; margin-top: 2px;
        font-size: 9px; color: #4ecdc4;
        text-transform: uppercase; letter-spacing: 1px; font-weight: 700;
    }

    /* ── MAIN ── */
    .main-content {
        margin-left: 256px; min-height: 100vh;
        display: flex; flex-direction: column;
        background: var(--paper);
    }
    .topbar {
        background: rgba(255,255,255,0.94);
        backdrop-filter: blur(16px);
        border-bottom: 1px solid var(--border-soft);
        padding: 0 32px; height: 58px;
        display: flex; align-items: center; justify-content: space-between;
        position: sticky; top: 0; z-index: 50;
        box-shadow: 0 1px 4px rgba(14,17,23,0.06);
    }
    .topbar-title { font-family: 'DM Serif Display', serif; font-size: 17px; color: var(--ink); }
    .topbar-actions { display: flex; align-items: center; gap: 10px; }
    .page-content { padding: 28px 32px; flex: 1; }

    /* ── FLASH ── */
    .flash-success {
        margin: 14px 32px 0; padding: 11px 16px;
        background: var(--green-light); color: var(--green);
        border-radius: var(--r); font-size: 13.5px; font-weight: 500;
        display: flex; align-items: center; gap: 10px;
        border: 1px solid rgba(22,101,52,0.18);
        animation: slideDown 0.3s ease;
    }
    .flash-error {
        margin: 14px 32px 0; padding: 11px 16px;
        background: var(--red-light); color: var(--red);
        border-radius: var(--r); font-size: 13.5px; font-weight: 500;
        display: flex; align-items: center; gap: 10px;
        border: 1px solid rgba(184,50,50,0.18);
    }
    @keyframes slideDown { from{opacity:0;transform:translateY(-8px);}to{opacity:1;transform:translateY(0);} }
    @keyframes fadeUp    { from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);} }
    .page-animate { animation: fadeUp 0.28s ease; }

    /* ── STAT CARDS ── */
    .stat-card {
        background: var(--white); border-radius: var(--rl);
        padding: 22px; border: 1px solid var(--border-soft);
        position: relative; overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
    .stat-card::before { content:''; position:absolute; top:0;left:0;right:0; height:3px; }
    .stat-card-teal::before  { background: linear-gradient(90deg,#0d6e6e,#4ecdc4); }
    .stat-card-gold::before  { background: linear-gradient(90deg,#b8841e,#e0ab30); }
    .stat-card-green::before { background: linear-gradient(90deg,#166534,#22c55e); }
    .stat-card-red::before   { background: linear-gradient(90deg,#b83232,#ef4444); }
    .stat-icon {
        font-size: 22px; margin-bottom: 12px;
        width: 44px; height: 44px; border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
    }
    .stat-card-teal .stat-icon  { background: var(--teal-light); }
    .stat-card-gold .stat-icon  { background: var(--gold-light); }
    .stat-card-green .stat-icon { background: var(--green-light); }
    .stat-card-red .stat-icon   { background: var(--red-light); }
    .stat-value { font-family: 'DM Serif Display', serif; font-size: 36px; color: var(--ink); line-height: 1; }
    .stat-label { font-size: 12px; color: var(--muted); margin-top: 4px; font-weight: 500; }

    /* ── CARDS ── */
    .att-card {
        background: var(--white); border-radius: var(--rl);
        border: 1px solid var(--border-soft); overflow: hidden;
    }
    .att-card-header {
        padding: 16px 22px 14px;
        border-bottom: 1px solid var(--border-soft);
        display: flex; align-items: center; justify-content: space-between;
        background: linear-gradient(135deg, #faf8f4, #fff);
    }
    .att-card-title { font-size: 13.5px; font-weight: 700; color: var(--ink); display: flex; align-items:center; gap:8px; }
    .att-card-body { padding: 20px 22px; }

    /* ── TABLE ── */
    .att-table { width: 100%; border-collapse: collapse; }
    .att-table thead { background: linear-gradient(135deg, #0e1117, #1a2233); color: white; }
    .att-table th { font-size: 10.5px; letter-spacing: 1.2px; text-transform: uppercase; padding: 11px 14px; text-align: left; font-weight: 600; white-space: nowrap; }
    .att-table td { padding: 13px 14px; font-size: 13px; border-bottom: 1px solid var(--border-soft); vertical-align: middle; }
    .att-table tr:last-child td { border-bottom: none; }
    .att-table tbody tr:hover td { background: #f7f4ef; }

    /* ── STATUS BADGES ── */
    .status-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px; font-size:10.5px; font-weight:700; white-space:nowrap; }
    .sb-submitted { background: var(--blue-light);  color: var(--blue); }
    .sb-approved  { background: var(--gold-light);  color: var(--gold); }
    .sb-rejected  { background: var(--red-light);   color: var(--red); }
    .sb-finalized { background: var(--green-light); color: var(--green); }
    .sb-pending   { background: #f0ece4; color: var(--muted); }

    /* ── BUTTONS ── */
    .btn-att {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 16px; border-radius: var(--r);
        font-size: 13px; font-weight: 600;
        cursor: pointer; border: none;
        font-family: 'DM Sans', sans-serif;
        transition: all 0.16s; text-decoration: none; white-space: nowrap;
    }
    .btn-att-primary { background: linear-gradient(135deg,#0d6e6e,#0a8080); color:white; box-shadow:0 2px 8px var(--teal-glow); }
    .btn-att-primary:hover { background: linear-gradient(135deg,#095c5c,#0d6e6e); color:white; box-shadow:0 4px 16px rgba(13,110,110,0.28); transform:translateY(-1px); text-decoration:none; }
    .btn-att-secondary { background: var(--white); color: var(--ink); border: 1.5px solid var(--border); }
    .btn-att-secondary:hover { background: var(--cream); color:var(--ink); text-decoration:none; }
    .btn-att-ghost { background: transparent; color: var(--muted); padding: 9px 11px; }
    .btn-att-ghost:hover { background: var(--cream); color: var(--ink); text-decoration:none; }
    .btn-att-danger { background: linear-gradient(135deg,#b83232,#c0392b); color:white; }
    .btn-att-danger:hover { color:white; transform:translateY(-1px); text-decoration:none; }
    .btn-att-sm { padding: 5px 11px; font-size: 11.5px; }

    /* ── FORM FIELDS ── */
    .att-field { margin-bottom: 0; }
    .att-field label { font-size: 11px; font-weight: 600; color: var(--muted); letter-spacing: 0.5px; text-transform: uppercase; display: block; margin-bottom: 6px; }
    .att-field input, .att-field select, .att-field textarea {
        padding: 9px 12px; border: 1.5px solid var(--border); border-radius: var(--r);
        font-size: 13.5px; font-family: 'DM Sans', sans-serif;
        background: var(--white); color: var(--ink);
        transition: border-color 0.16s, box-shadow 0.16s;
        outline: none; width: 100%;
    }
    .att-field input:focus, .att-field select:focus, .att-field textarea:focus {
        border-color: var(--teal-mid); box-shadow: 0 0 0 3px rgba(13,110,110,0.1);
    }
    .att-field textarea { resize: vertical; min-height: 76px; }

    /* ── WELCOME CARD ── */
    .welcome-card {
        background: linear-gradient(135deg, #0e1117 0%, #1a2a3a 55%, #0d2a3a 100%);
        border-radius: var(--rl); padding: 26px 30px; color: white;
        position: relative; overflow: hidden;
        border: 1px solid rgba(255,255,255,0.05);
    }
    .welcome-card::before {
        content:''; position:absolute; top:-50px; right:-50px;
        width:200px; height:200px; border-radius:50%;
        background: radial-gradient(circle, rgba(78,205,196,0.12), transparent 70%);
    }
    .welcome-card::after {
        content:''; position:absolute; bottom:-40px; left:80px;
        width:160px; height:160px; border-radius:50%;
        background: radial-gradient(circle, rgba(201,149,42,0.08), transparent 70%);
    }

    /* ── ACTION CARDS (dashboard menu) ── */
    .action-card {
        background: var(--white); border-radius: var(--rl);
        border: 1px solid var(--border-soft);
        text-decoration: none; color: inherit; display: block;
        transition: all 0.2s ease; position: relative; overflow: hidden;
    }
    .action-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); border-color: var(--teal-light); color: inherit; text-decoration: none; }
    .action-card-body { padding: 20px 22px 36px; }
    .action-card-icon { width: 48px; height: 48px; border-radius: 12px; display:flex; align-items:center; justify-content:center; font-size: 22px; margin-bottom: 12px; }
    .icon-teal  { background: var(--teal-light); }
    .icon-gold  { background: var(--gold-light); }
    .icon-green { background: var(--green-light); }
    .icon-red   { background: var(--red-light); }
    .icon-blue  { background: var(--blue-light); }
    .action-card-title { font-size: 14px; font-weight: 700; color: var(--ink); margin-bottom: 4px; }
    .action-card-desc  { font-size: 12px; color: var(--muted); line-height: 1.5; }
    .action-card-arrow {
        position: absolute; bottom: 18px; right: 18px;
        width: 26px; height: 26px; border-radius: 7px;
        background: var(--cream); color: var(--muted);
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; transition: all 0.18s;
    }
    .action-card:hover .action-card-arrow { background: var(--teal-light); color: var(--teal); transform: translateX(2px); }

    /* ── TOASTS ── */
    .notif-stack { position: fixed; bottom: 22px; right: 22px; display: flex; flex-direction: column; gap: 8px; z-index: 500; }
    .notif-toast {
        background: var(--ink); color: white; padding: 12px 16px; border-radius: var(--r);
        font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 9px;
        box-shadow: var(--shadow-lg); min-width: 240px; max-width: 380px;
        border-left: 3px solid rgba(255,255,255,0.15);
        animation: toastIn 0.3s ease;
    }
    .notif-toast.success { background: var(--green); border-left-color: #22c55e; }
    .notif-toast.error   { background: var(--red);   border-left-color: #f87171; }
    .notif-toast.warn    { background: var(--gold-bright); color: var(--ink); border-left-color: #f0b93a; }
    @keyframes toastIn { from{opacity:0;transform:translateX(16px);}to{opacity:1;transform:translateX(0);} }

    /* ── MOBILE ── */
    .mobile-header { display: none; background: var(--ink); color: white; padding: 11px 16px; position: sticky; top:0; z-index:110; align-items:center; justify-content:space-between; }
    .mobile-menu-btn { background: none; border: none; color: white; font-size: 22px; cursor: pointer; }
    .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(14,17,23,0.55); z-index: 99; }
    .mobile-card-list { display: none; }
    .mobile-app-card { background:var(--white); border:1px solid var(--border-soft); border-radius:var(--r); padding:16px; margin-bottom:10px; border-left:4px solid var(--teal); }
    .mobile-app-card.border-gold  { border-left-color: var(--gold-bright); }
    .mobile-app-card.border-green { border-left-color: var(--green-bright); }
    .mobile-app-card.border-red   { border-left-color: var(--red); }

    @media (max-width: 768px) {
        .sidebar { transform: translateX(-100%); }
        .sidebar.open { transform: translateX(0); }
        .sidebar-overlay.open { display: block; }
        .mobile-header { display: flex; }
        .main-content { margin-left: 0; }
        .topbar { display: none; }
        .page-content { padding: 14px; }
        .desktop-table { display: none !important; }
        .mobile-card-list { display: block !important; }
    }
    @media (min-width: 769px) {
        .mobile-header { display: none; }
        .mobile-card-list { display: none !important; }
    }
    </style>
</head>
<body style="font-family:'DM Sans',sans-serif; background:var(--paper); color:var(--ink); margin:0;">

    <!-- Mobile Header -->
    <div class="mobile-header">
        <button class="mobile-menu-btn" onclick="document.querySelector('.sidebar').classList.add('open'); document.querySelector('.sidebar-overlay').classList.add('open');">☰</button>
        <span style="font-family:'DM Serif Display',serif;font-size:18px;">Mehnat<span style="color:#4ecdc4;">Attest</span></span>
        <div style="width:32px;"></div>
    </div>
    <div class="sidebar-overlay" onclick="document.querySelector('.sidebar').classList.remove('open');this.classList.remove('open');"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">⚖️</div>
            <div class="logo-mark">Mehnat<span>Attest</span></div>
            <div class="logo-sub">Attestatsiya platformasi</div>
        </div>
        <nav class="nav-section">
            <div class="nav-label">Asosiy</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span> Boshqaruv paneli
            </a>

            @if(Auth::user()->role === 'admin')
                <div class="nav-label">Administrator</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">⚙️</span> Admin paneli
                </a>
                <a href="{{ route('admin.campaigns.index') }}" class="nav-item {{ request()->routeIs('admin.campaigns.*') ? 'active' : '' }}">
                    <span class="nav-icon">📋</span> Kampaniyalar
                </a>
            @endif

            @if(Auth::user()->role === 'employer')
                <div class="nav-label">Ish beruvchi</div>
                <a href="{{ route('employer.organization.index') }}" class="nav-item {{ request()->routeIs('employer.organization.*') ? 'active' : '' }}">
                    <span class="nav-icon">🏢</span> Korxona profili
                </a>
                <a href="{{ route('employer.tenders.index') }}" class="nav-item {{ request()->routeIs('employer.tenders.*') ? 'active' : '' }}">
                    <span class="nav-icon">🤝</span> Tenderlar
                </a>
                <a href="{{ route('employee.applications.index') }}" class="nav-item {{ request()->routeIs('employee.applications.*') ? 'active' : '' }}">
                    <span class="nav-icon">🏭</span> Arizalar
                </a>
                <a href="{{ route('employer.expertise.index') }}" class="nav-item {{ request()->routeIs('employer.expertise.*') ? 'active' : '' }}">
                    <span class="nav-icon">📑</span> Davlat ekspertizasi
                </a>
            @endif

            @if(Auth::user()->role === 'laboratory')
                <div class="nav-label">Laboratoriya</div>
                <a href="{{ route('laboratory.profile.index') }}" class="nav-item {{ request()->routeIs('laboratory.profile.*') ? 'active' : '' }}">
                    <span class="nav-icon">🧪</span> Profilim
                </a>
                <a href="{{ route('laboratory.protocols.index') }}" class="nav-item {{ request()->routeIs('laboratory.protocols.*') ? 'active' : '' }}">
                    <span class="nav-icon">📋</span> O'lchov protokollari
                </a>
            @endif

            @if(Auth::user()->role === 'commission')
                <div class="nav-label">Komissiya</div>
                <a href="{{ route('commission.evaluations.index') }}" class="nav-item {{ request()->routeIs('commission.evaluations.*') ? 'active' : '' }}">
                    <span class="nav-icon">🔍</span> Tekshirish
                    @php $pendingCount = \App\Models\AttestationApplication::where('status', 'hr_approved')->count(); @endphp
                    @if($pendingCount > 0)<span class="nav-badge">{{ $pendingCount }}</span>@endif
                </a>
            @endif

            @if(Auth::user()->role === 'institute_expert')
                <div class="nav-label">Institut (Dastlabki baholash)</div>
                <a href="{{ route('institute.expertise.index') }}" class="nav-item {{ request()->routeIs('institute.expertise.*') ? 'active' : '' }}">
                    <span class="nav-icon">🏫</span> Arizalar
                    @php $instCount = \App\Models\StateExpertiseApplication::where('institute_status','pending')->count(); @endphp
                    @if($instCount > 0)<span class="nav-badge">{{ $instCount }}</span>@endif
                </a>
            @endif

            @if(Auth::user()->role === 'expert')
                <div class="nav-label">Davlat ekspertizasi (Vazirlik)</div>
                <a href="{{ route('ministry.expertise.index') }}" class="nav-item {{ request()->routeIs('ministry.expertise.*') ? 'active' : '' }}">
                    <span class="nav-icon">🏛</span> Yakuniy xulosalar
                    @php $minCount = \App\Models\StateExpertiseApplication::where('institute_status','approved')->where('ministry_status','pending')->count(); @endphp
                    @if($minCount > 0)<span class="nav-badge">{{ $minCount }}</span>@endif
                </a>
            @endif

            @if(in_array(Auth::user()->role, ['admin','expert']))
                <div class="nav-label">Hisobot</div>
                <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span> Hisobotlar
                </a>
            @endif
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div>
                    <div class="name">{{ Auth::user()->name }}</div>
                    <div class="role-tag">
                        @switch(Auth::user()->role)
                            @case('admin') Administrator @break
                            @case('employer') Ish beruvchi @break
                            @case('commission') Komissiya @break
                            @case('expert') Vazirlik Ekspеrti @break
                            @case('institute_expert') Institut Ekspеrti @break
                            @case('laboratory') Laboratoriya @break
                            @default {{ Auth::user()->role }}
                        @endswitch
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        style="background:rgba(255,255,255,0.07);border:none;color:rgba(255,255,255,0.4);padding:6px 10px;border-radius:7px;cursor:pointer;font-size:11px;font-family:'DM Sans',sans-serif;transition:all 0.2s;"
                        onmouseenter="this.style.color='#f87171';this.style.background='rgba(184,50,50,0.2)';"
                        onmouseleave="this.style.color='rgba(255,255,255,0.4)';this.style.background='rgba(255,255,255,0.07)';">
                        Chiqish
                    </button>
                </form>
            </div>
            <div style="margin-top:8px;padding-top:8px;border-top:1px solid rgba(255,255,255,0.05);font-size:9.5px;color:rgba(255,255,255,0.18);">
                VM Qarori №263 · 15.09.2014
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-title">
                @isset($header){{ $header }}@endisset
            </div>
            <div class="topbar-actions">
                <a href="{{ route('profile.edit') }}" class="btn-att btn-att-ghost btn-att-sm" style="font-size:12px;">
                    👤 {{ Auth::user()->name }}
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="flash-success">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash-error">⚠️ {{ session('error') }}</div>
        @endif
        @if(session('status'))
            <div class="flash-success" style="background:var(--blue-light);color:var(--blue);border-color:rgba(30,64,175,0.18);">ℹ️ {{ session('status') }}</div>
        @endif

        <div class="page-content page-animate">
            {{ $slot }}
        </div>
    </div>

    <div class="notif-stack" id="notif-stack"></div>
    <script>
    function showNotif(msg, type) {
        const s = document.getElementById('notif-stack');
        const n = document.createElement('div');
        n.className = 'notif-toast ' + (type||'');
        n.innerHTML = '<span>' + ({success:'✅',error:'❌',warn:'⚠️'}[type]||'ℹ️') + '</span> ' + msg;
        s.appendChild(n);
        setTimeout(()=>{ n.style.opacity='0'; n.style.transform='translateX(16px)'; n.style.transition='all 0.3s'; setTimeout(()=>n.remove(),300); }, 3500);
    }
    document.querySelectorAll('.sidebar .nav-item').forEach(i=>{
        i.addEventListener('click',()=>{ if(window.innerWidth<=768){ document.querySelector('.sidebar').classList.remove('open'); document.querySelector('.sidebar-overlay').classList.remove('open'); } });
    });
    </script>
</body>
</html>
