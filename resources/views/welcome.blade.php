<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'E-Attestatsiya') }} — {{ __('messages.entry.card_title') }}</title>
    <meta name="description" content="{{ __('messages.entry.hero_desc') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|dm-serif-display:400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            min-height: 100vh;
            background: #0a0f1e;
            overflow: hidden;
        }

        /* ── Animated gradient background ─────────────────────────── */
        .bg-scene {
            position: fixed; inset: 0;
            background: linear-gradient(135deg, #0a0f1e 0%, #0d1b2a 40%, #0e2340 70%, #071629 100%);
            overflow: hidden;
        }
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.25;
            animation: float 8s ease-in-out infinite;
        }
        .blob-1 { width: 600px; height: 600px; background: radial-gradient(circle, #0d6e6e, transparent); top: -200px; left: -100px; animation-delay: 0s; }
        .blob-2 { width: 500px; height: 500px; background: radial-gradient(circle, #c9952a, transparent); bottom: -150px; right: -100px; animation-delay: 3s; }
        .blob-3 { width: 400px; height: 400px; background: radial-gradient(circle, #4ecdc4, transparent); top: 50%; left: 50%; animation-delay: 5s; opacity: 0.1; }
        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        /* ── Layout ───────────────────────────────────────────────── */
        .page {
            position: relative; z-index: 1;
            min-height: 100vh;
            display: flex;
        }

        /* ── Top-right language switcher ──────────────────────────── */
        .lang-switcher {
            position: absolute;
            top: 22px; right: 28px;
            display: inline-flex; align-items: center;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 999px; padding: 4px;
            backdrop-filter: blur(12px);
            z-index: 10;
        }
        .lang-switcher a {
            font-size: 11.5px; font-weight: 700;
            letter-spacing: 0.6px;
            padding: 5px 12px; border-radius: 999px;
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            transition: all 0.18s;
        }
        .lang-switcher a.active { background: #4ecdc4; color: #061a25; }
        .lang-switcher a:not(.active):hover { color: white; background: rgba(255,255,255,0.06); }

        /* ── Left panel ───────────────────────────────────────────── */
        .left-panel {
            width: 48%;
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 52px 56px;
            position: relative;
        }

        .logo-wrap { display: flex; align-items: center; gap: 14px; }
        .logo-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0d6e6e, #4ecdc4);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 20px rgba(78,205,196,0.3);
        }
        .logo-text { font-family: 'DM Serif Display', serif; font-size: 26px; color: white; }
        .logo-text span { color: #4ecdc4; }
        .logo-sub { font-size: 11px; color: rgba(255,255,255,0.35); letter-spacing: 2px; text-transform: uppercase; margin-top: 2px; }

        .hero-content { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 40px 0; }

        .badge-pill {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(78,205,196,0.1);
            border: 1px solid rgba(78,205,196,0.3);
            color: #4ecdc4;
            padding: 6px 14px; border-radius: 100px;
            font-size: 11px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase;
            margin-bottom: 28px; width: fit-content;
        }

        .hero-title {
            font-family: 'DM Serif Display', serif;
            font-size: 46px; line-height: 1.15;
            color: white; margin-bottom: 20px;
        }
        .hero-title .accent { color: #4ecdc4; }

        .hero-desc {
            font-size: 15px; color: rgba(255,255,255,0.5);
            line-height: 1.8; max-width: 400px;
        }

        .stats-strip {
            display: flex; gap: 32px;
            margin-top: 48px; padding-top: 32px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .stat-num { font-family: 'DM Serif Display', serif; font-size: 28px; color: white; }
        .stat-label { font-size: 12px; color: rgba(255,255,255,0.35); margin-top: 2px; }

        .left-footer { font-size: 12px; color: rgba(255,255,255,0.2); line-height: 1.7; }

        /* ── Right panel ──────────────────────────────────────────── */
        .right-panel { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 48px; }

        .login-card {
            width: 100%; max-width: 460px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            padding: 40px 36px;
            backdrop-filter: blur(24px);
            box-shadow: 0 24px 80px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.08);
            animation: slideIn 0.6s cubic-bezier(0.22, 0.61, 0.36, 1) both;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-eyebrow { display: flex; align-items: center; gap: 8px; margin-bottom: 26px; }
        .eyebrow-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #4ecdc4; box-shadow: 0 0 8px #4ecdc4;
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(0.8); }
        }
        .eyebrow-text {
            font-size: 12px; color: rgba(255,255,255,0.4);
            font-weight: 500; letter-spacing: 1px; text-transform: uppercase;
        }

        .card-title {
            font-family: 'DM Serif Display', serif;
            font-size: 28px; color: white; margin-bottom: 6px;
        }
        .card-subtitle { font-size: 14px; color: rgba(255,255,255,0.4); margin-bottom: 28px; }

        /* ── Login options (SSO) ──────────────────────────────────── */
        .login-options { display: flex; flex-direction: column; gap: 14px; margin-bottom: 22px; }

        .login-option {
            display: flex; align-items: center; justify-content: space-between;
            padding: 18px 20px;
            border-radius: 16px;
            border: 1.5px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.03);
            text-decoration: none; color: white;
            transition: all 0.25s ease;
            cursor: pointer; position: relative; overflow: hidden;
        }
        .login-option:hover {
            border-color: rgba(255,255,255,0.2);
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.4);
            background: rgba(255,255,255,0.06);
            color: white; text-decoration: none;
        }
        .login-option.primary { border-color: rgba(78,205,196,0.3); background: rgba(78,205,196,0.06); }
        .login-option.primary:hover { border-color: #4ecdc4; background: rgba(78,205,196,0.12); box-shadow: 0 12px 40px rgba(78,205,196,0.15); }

        .option-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; flex-shrink: 0;
        }
        .option-icon.teal { background: linear-gradient(135deg, #0d6e6e, #4ecdc4); box-shadow: 0 4px 16px rgba(78,205,196,0.3); }
        .option-icon.gold { background: linear-gradient(135deg, #a47621, #c9952a); box-shadow: 0 4px 16px rgba(201,149,42,0.3); }

        .option-body { padding: 0 14px; flex: 1; min-width: 0; }
        .option-title { font-size: 15px; font-weight: 700; color: white; margin-bottom: 2px; }
        .option-sub { font-size: 11px; color: rgba(255,255,255,0.35); font-weight: 400; letter-spacing: 0.5px; text-transform: uppercase; }

        .option-arrow {
            width: 32px; height: 32px; border-radius: 8px;
            background: rgba(255,255,255,0.06);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; color: rgba(255,255,255,0.4);
            transition: all 0.2s; flex-shrink: 0;
        }
        .login-option:hover .option-arrow {
            background: rgba(255,255,255,0.12); color: white; transform: translateX(2px);
        }

        /* ── Divider ─────────────────────────────────────────────── */
        .divider-row { display: flex; align-items: center; gap: 12px; margin: 14px 0 18px; }
        .divider-line { flex: 1; height: 1px; background: rgba(255,255,255,0.06); }
        .divider-text { font-size: 11px; color: rgba(255,255,255,0.25); font-weight: 600; white-space: nowrap; letter-spacing: 1.4px; text-transform: uppercase; }

        /* ── Trigger for "Login / Parol orqali kirish" ──────────── */
        .panel-trigger {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding: 16px 20px;
            border-radius: 14px;
            border: 1.5px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.04);
            color: white; cursor: pointer;
            font-family: inherit; font-size: 14px; font-weight: 700;
            width: 100%; text-align: left;
            transition: all 0.2s;
        }
        .panel-trigger:hover { border-color: rgba(255,255,255,0.2); background: rgba(255,255,255,0.07); }
        .panel-trigger .pt-icon { font-size: 18px; }
        .panel-trigger .pt-chev { font-size: 12px; transition: transform 0.25s; opacity: 0.5; }
        .panel-trigger[aria-expanded="true"] .pt-chev { transform: rotate(180deg); opacity: 1; }

        /* ── Panel grid (revealed on click) ──────────────────────── */
        .panel-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 8px;
            margin-top: 12px;
        }
        .panel-grid a {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 12px;
            border-radius: 11px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.03);
            color: white; text-decoration: none;
            font-size: 13px; font-weight: 600;
            transition: all 0.18s;
        }
        .panel-grid a:hover { border-color: var(--accent, #4ecdc4); background: rgba(78,205,196,0.08); color: white; transform: translateY(-1px); }
        .panel-grid a .pg-ico { font-size: 16px; flex-shrink: 0; }
        .panel-grid a .pg-name { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* ── Footer ──────────────────────────────────────────────── */
        .card-footer { text-align: center; margin-top: 24px; }
        .card-footer p { font-size: 12px; color: rgba(255,255,255,0.25); line-height: 1.7; }

        /* ── Mobile ──────────────────────────────────────────────── */
        @media (max-width: 900px) {
            body { overflow: auto; }
            .page { flex-direction: column; min-height: auto; }
            .lang-switcher { top: 16px; right: 16px; }
            .left-panel { width: 100%; padding: 60px 24px 20px; }
            .hero-title { font-size: 30px; }
            .stats-strip { gap: 18px; flex-wrap: wrap; }
            .right-panel { padding: 16px 20px 32px; }
            .login-card { padding: 28px 22px; }
            .panel-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="bg-scene">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    {{-- ─────── Language switcher (UZ / RU / EN) ─────── --}}
    <div class="lang-switcher" role="navigation" aria-label="{{ __('messages.language') }}">
        @foreach (['uz' => 'UZ', 'ru' => 'RU', 'en' => 'EN'] as $code => $label)
            <a href="{{ route('lang.switch', $code) }}"
               class="{{ app()->getLocale() === $code ? 'active' : '' }}"
               aria-current="{{ app()->getLocale() === $code ? 'page' : 'false' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="page">
        {{-- ─────── Left: Branding + hero ─────── --}}
        <div class="left-panel">
            <div class="logo-wrap">
                <div class="logo-icon" aria-hidden="true">⚖️</div>
                <div>
                    <div class="logo-text">Mehnat<span>Attest</span></div>
                    <div class="logo-sub">{{ __('messages.entry.brand_subtitle') }}</div>
                </div>
            </div>

            <div class="hero-content">
                <div class="badge-pill">
                    <span style="width:6px;height:6px;border-radius:50%;background:#4ecdc4;display:inline-block;box-shadow:0 0 6px #4ecdc4;"></span>
                    {{ __('messages.entry.platform_tag') }}
                </div>

                <h1 class="hero-title">
                    {{ __('messages.entry.hero_line_1') }}<br>
                    {{ __('messages.entry.hero_line_2') }}<br>
                    <span class="accent">{{ __('messages.entry.hero_accent') }}</span>
                </h1>

                <p class="hero-desc">{{ __('messages.entry.hero_desc') }}</p>

                <div class="stats-strip">
                    <div>
                        <div class="stat-num">18</div>
                        <div class="stat-label">{{ __('messages.entry.stat_factors') }}</div>
                    </div>
                    <div>
                        <div class="stat-num">4</div>
                        <div class="stat-label">{{ __('messages.entry.stat_steps') }}</div>
                    </div>
                    <div>
                        <div class="stat-num">100%</div>
                        <div class="stat-label">{{ __('messages.entry.stat_digital') }}</div>
                    </div>
                </div>
            </div>

            <div class="left-footer">
                {{ __('messages.entry.footer_act') }}<br>
                © {{ now()->year }} {{ __('messages.entry.footer_copyright') }}
            </div>
        </div>

        {{-- ─────── Right: Login card ─────── --}}
        <div class="right-panel">
            <div class="login-card">
                <div class="card-eyebrow">
                    <div class="eyebrow-dot"></div>
                    <span class="eyebrow-text">{{ __('messages.entry.secure_login') }}</span>
                </div>

                @auth
                    <div class="card-title">{{ __('messages.entry.go_to_dashboard') }}</div>
                    <div class="card-subtitle">{{ auth()->user()->name }}</div>
                    <a href="{{ url('/dashboard') }}" class="login-option primary" style="justify-content:center;">
                        <div class="option-body" style="flex:0;padding:0;">
                            <div class="option-title">{{ __('messages.entry.go_to_dashboard') }} →</div>
                        </div>
                    </a>
                @else
                    @php
                        $showSso = app()->isLocal() || config('demo.sso') || config('identity.sso_routes_enabled');
                        $demoSsoLabel = config('demo.sso') || app()->isLocal();
                        $panels = [
                            ['employer',         '🏭', __('messages.roles.employer')],
                            ['laboratory',       '🧪', __('messages.roles.laboratory')],
                            ['hr',               '🧑‍💼', __('messages.roles.hr')],
                            ['commission',       '⚖️', __('messages.roles.commission')],
                            ['institute_expert', '🔬', __('messages.roles.institute_expert')],
                            ['expert',           '🏛️', __('messages.roles.expert')],
                            ['admin',            '🛡️', __('messages.roles.admin')],
                        ];
                    @endphp

                    <div class="card-title">{{ __('messages.entry.card_title') }}</div>
                    <div class="card-subtitle">{{ __('messages.entry.card_subtitle') }}</div>

                    @if ($showSso)
                        <div class="login-options">
                            <a href="{{ route('auth.oneid.redirect') }}" class="login-option primary">
                                <div class="option-icon teal" aria-hidden="true">👤</div>
                                <div class="option-body">
                                    <div class="option-title">{{ __('messages.entry.sso_individual_title') }}</div>
                                    <div class="option-sub">{{ $demoSsoLabel ? __('messages.entry.sso_individual_demo') : __('messages.entry.sso_individual_sub') }}</div>
                                </div>
                                <div class="option-arrow">→</div>
                            </a>

                            <a href="{{ route('auth.eri.login') }}" class="login-option">
                                <div class="option-icon gold" aria-hidden="true">🏢</div>
                                <div class="option-body">
                                    <div class="option-title">{{ __('messages.entry.sso_legal_title') }}</div>
                                    <div class="option-sub">{{ $demoSsoLabel ? __('messages.entry.sso_legal_demo') : __('messages.entry.sso_legal_sub') }}</div>
                                </div>
                                <div class="option-arrow">→</div>
                            </a>
                        </div>

                        <div class="divider-row">
                            <div class="divider-line"></div>
                            <span class="divider-text">{{ __('messages.entry.or') }}</span>
                            <div class="divider-line"></div>
                        </div>
                    @else
                        <p style="font-size:13px;color:rgba(255,255,255,0.45);line-height:1.6;margin-bottom:18px;">
                            {{ __('messages.entry.sso_disabled_note') }}
                        </p>
                    @endif

                    {{-- Panel chooser (collapses → 7 panel buttons) --}}
                    <div x-data="{ open: false }">
                        <button type="button"
                                class="panel-trigger"
                                aria-expanded="false"
                                x-bind:aria-expanded="open.toString()"
                                @click="open = !open">
                            <span style="display:flex;align-items:center;gap:12px;">
                                <span class="pt-icon" aria-hidden="true">🔑</span>
                                <span>
                                    <span style="display:block;">{{ __('messages.entry.login_password') }}</span>
                                    <span style="display:block;font-weight:500;font-size:11px;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:0.6px;margin-top:2px;">
                                        {{ __('messages.entry.login_password_sub') }}
                                    </span>
                                </span>
                            </span>
                            <span class="pt-chev" aria-hidden="true">▾</span>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-cloak
                             style="margin-top:8px;">
                            <div style="font-size:11px;font-weight:600;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:1px;margin:8px 4px 8px;">
                                {{ __('messages.entry.choose_panel') }}
                            </div>
                            <div class="panel-grid">
                                @foreach ($panels as [$key, $icon, $label])
                                    <a href="{{ route('login.role', $key) }}">
                                        <span class="pg-ico" aria-hidden="true">{{ $icon }}</span>
                                        <span class="pg-name">{{ $label }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <p>{{ __('messages.entry.tos') }}</p>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>
