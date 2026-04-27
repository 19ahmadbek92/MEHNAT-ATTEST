<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'E-Attestatsiya') }} — Ish o‘rinlarini elektron attestatsiya qilish tizimi</title>
    <meta name="description" content="O‘zbekiston Respublikasi Mehnat va aholini ish bilan ta‘minlash vazirligining ish o‘rinlarini sanitariya-gigiena va xavfsizlik shartlariga ko‘ra elektron attestatsiya qilish yagona davlat axborot tizimi.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|dm-serif-display:400|figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --hero-blur: 90px;
        }
        body.landing {
            background: var(--paper);
            font-family: 'Figtree', 'DM Sans', system-ui, sans-serif;
            color: var(--ink);
        }
        .ld-display {
            font-family: 'DM Serif Display', 'Georgia', serif;
            letter-spacing: -0.02em;
            line-height: 1.05;
        }
        .ld-shell {
            position: relative;
            isolation: isolate;
            overflow-x: hidden;
        }
        .ld-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(var(--hero-blur));
            opacity: 0.55;
            mix-blend-mode: multiply;
            pointer-events: none;
            z-index: 0;
        }
        .ld-blob.b1 { background: #4ecdc4; width: 26rem; height: 26rem; top: -6rem; left: -8rem; }
        .ld-blob.b2 { background: #f0b93a; width: 22rem; height: 22rem; top: 10rem; right: -6rem; opacity: 0.35; }
        .ld-blob.b3 { background: #0d6e6e; width: 20rem; height: 20rem; bottom: -6rem; left: 30%; opacity: 0.25; }

        .ld-nav {
            position: sticky; top: 0; z-index: 50;
            background: rgba(244, 242, 238, 0.85);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border-soft);
        }
        .ld-nav-link {
            font-size: 13.5px; font-weight: 600; color: var(--muted);
            text-decoration: none; padding: 6px 4px;
            border-bottom: 2px solid transparent;
            transition: color .18s, border-color .18s;
        }
        .ld-nav-link:hover { color: var(--ink); border-bottom-color: var(--teal-mid); }

        .ld-lang {
            display: inline-flex; align-items: center;
            background: var(--white); border: 1px solid var(--border);
            border-radius: 999px; padding: 4px;
            box-shadow: var(--shadow-sm);
        }
        .ld-lang a {
            font-size: 11.5px; font-weight: 700; letter-spacing: 0.6px;
            padding: 5px 11px; border-radius: 999px;
            color: var(--muted); text-decoration: none;
            transition: all .18s;
        }
        .ld-lang a.active { background: var(--ink); color: white; }
        .ld-lang a:not(.active):hover { color: var(--ink); background: var(--cream); }

        .ld-cta {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 20px; border-radius: 999px;
            font-weight: 700; font-size: 14px;
            text-decoration: none;
            transition: transform .15s, box-shadow .2s, background .18s;
        }
        .ld-cta-primary {
            background: linear-gradient(135deg, #0d6e6e 0%, #0a8a8a 100%);
            color: white;
            box-shadow: 0 6px 22px rgba(13,110,110,.28);
        }
        .ld-cta-primary:hover { transform: translateY(-1px); color: white;
            box-shadow: 0 10px 28px rgba(13,110,110,.36); }
        .ld-cta-ghost {
            background: var(--white); color: var(--ink);
            border: 1.5px solid var(--border);
        }
        .ld-cta-ghost:hover { transform: translateY(-1px); border-color: var(--ink); color: var(--ink); }

        .ld-pill {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 6px 14px; border-radius: 999px;
            background: rgba(13,110,110,.08);
            color: var(--teal);
            font-size: 11.5px; font-weight: 700;
            letter-spacing: 1.2px; text-transform: uppercase;
            border: 1px solid rgba(13,110,110,.18);
        }
        .ld-pill .dot { width: 7px; height: 7px; border-radius: 50%;
            background: var(--teal-mid); box-shadow: 0 0 0 3px rgba(10,138,138,.18); }

        .ld-hero h1 {
            font-size: clamp(2.4rem, 5.4vw, 4.4rem);
            color: var(--ink);
        }
        .ld-hero h1 .accent {
            background: linear-gradient(135deg, #0d6e6e 0%, #0a8a8a 60%, #c9952a 100%);
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .ld-hero p.lead {
            font-size: 17px; line-height: 1.65; color: #524e46;
            max-width: 36rem;
        }

        .ld-mock {
            position: relative;
            background: var(--white);
            border: 1px solid var(--border-soft);
            border-radius: 24px;
            box-shadow: var(--shadow-xl);
            padding: 22px;
            transform: rotate(2deg);
            transition: transform .6s cubic-bezier(.22,.61,.36,1);
        }
        .ld-mock:hover { transform: rotate(0); }
        .ld-mock-bar {
            display: flex; align-items: center; gap: 6px;
            padding-bottom: 14px; border-bottom: 1px solid var(--border-soft);
            margin-bottom: 14px;
        }
        .ld-mock-bar i {
            width: 10px; height: 10px; border-radius: 50%;
            display: inline-block;
        }
        .ld-mock-bar i:nth-child(1) { background: #ef6062; }
        .ld-mock-bar i:nth-child(2) { background: #f4ba49; }
        .ld-mock-bar i:nth-child(3) { background: #5dc66c; }
        .ld-mock-row {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 14px; border-radius: 12px;
            background: #faf8f3;
            margin-bottom: 9px;
            border: 1px solid var(--border-soft);
        }
        .ld-mock-row .ic {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 17px;
        }
        .ld-mock-row .meta { flex: 1; }
        .ld-mock-row .meta .t { font-size: 13px; font-weight: 700; color: var(--ink); }
        .ld-mock-row .meta .s { font-size: 11px; color: var(--muted); margin-top: 2px; }
        .ld-mock-row .badge {
            font-size: 10.5px; font-weight: 700; padding: 3px 9px;
            border-radius: 999px; letter-spacing: .3px;
        }

        .ld-stat-floating {
            position: absolute; left: -28px; bottom: 28px;
            background: var(--white);
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            padding: 16px 20px;
            box-shadow: var(--shadow-lg);
            display: flex; align-items: center; gap: 14px;
            transform: rotate(-3deg);
            transition: transform .5s;
        }
        .ld-stat-floating:hover { transform: rotate(0) translateY(-3px); }
        .ld-stat-floating .num {
            font-family: 'DM Serif Display', serif;
            font-size: 30px; color: var(--ink); line-height: 1;
        }
        .ld-stat-floating .lbl {
            font-size: 11px; font-weight: 700; color: var(--teal);
            letter-spacing: 1px; text-transform: uppercase; margin-top: 4px;
        }
        .ld-stat-floating .icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, #176b3a, #2dcc70);
            color: white; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 6px 18px rgba(23,107,58,.35);
        }

        .ld-section { position: relative; z-index: 1; }
        .ld-section-eyebrow {
            display: inline-block;
            font-size: 12px; font-weight: 700; letter-spacing: 2px;
            color: var(--teal); text-transform: uppercase;
            margin-bottom: 12px;
        }
        .ld-section h2 {
            font-size: clamp(2rem, 3.6vw, 2.6rem);
            color: var(--ink);
            margin-bottom: 14px;
        }
        .ld-section p.intro {
            font-size: 16px; color: #524e46;
            max-width: 40rem;
        }

        .feat-card {
            background: var(--white);
            border: 1px solid var(--border-soft);
            border-radius: 22px;
            padding: 28px 26px;
            transition: transform .25s, box-shadow .25s, border-color .25s;
            position: relative; overflow: hidden;
        }
        .feat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--teal-light);
        }
        .feat-card .ic {
            width: 56px; height: 56px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 18px;
        }
        .feat-card.dark {
            background: linear-gradient(160deg, #0e1117 0%, #1a2a3a 100%);
            color: white; border-color: rgba(255,255,255,.06);
        }
        .feat-card.dark h4 { color: white; }
        .feat-card.dark p { color: rgba(255,255,255,.66); }
        .feat-card.dark .ic { background: rgba(78,205,196,.18); color: #4ecdc4;
            border: 1px solid rgba(78,205,196,.28); }
        .feat-card h4 {
            font-size: 19px; font-weight: 700; color: var(--ink);
            margin-bottom: 10px;
        }
        .feat-card p { font-size: 14.5px; color: #524e46; line-height: 1.65; }

        .step {
            text-align: center; position: relative;
            padding: 0 12px;
        }
        .step .num {
            width: 64px; height: 64px; margin: 0 auto 18px;
            border-radius: 18px;
            background: var(--white);
            border: 1px solid var(--border-soft);
            display: flex; align-items: center; justify-content: center;
            font-family: 'DM Serif Display', serif;
            font-size: 26px; color: var(--teal);
            box-shadow: var(--shadow);
            transition: transform .3s;
        }
        .step:hover .num { transform: translateY(-4px) scale(1.04); border-color: var(--teal-light); }
        .step .num.gold { color: var(--gold); }
        .step .num.green { color: var(--green-bright); }
        .step .num.purple { color: #6e3a9f; }
        .step h4 { font-size: 16px; font-weight: 700; color: var(--ink); margin-bottom: 6px; }
        .step p { font-size: 13.5px; color: var(--muted); line-height: 1.6; padding: 0 6px; }

        .ld-footer {
            background: linear-gradient(180deg, #0e1117 0%, #060912 100%);
            color: rgba(255,255,255,.7);
            position: relative; overflow: hidden;
            padding: 72px 0 28px;
        }
        .ld-footer::before {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(800px 380px at 12% 0%, rgba(13,110,110,.25), transparent 60%),
                        radial-gradient(700px 360px at 92% 100%, rgba(201,149,42,.18), transparent 60%);
            pointer-events: none;
        }
        .ld-footer h4 { color: white; font-size: 14px; font-weight: 700; letter-spacing: 1.2px;
            text-transform: uppercase; margin-bottom: 18px; }
        .ld-footer a { color: rgba(255,255,255,.62); text-decoration: none; transition: color .18s, transform .18s; display: inline-block; }
        .ld-footer a:hover { color: white; transform: translateX(2px); }
        .ld-footer .brand-mark {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, #0d6e6e, #4ecdc4);
            display: flex; align-items: center; justify-content: center;
            font-family: 'DM Serif Display', serif;
            color: white; font-size: 22px;
            box-shadow: 0 8px 22px rgba(78,205,196,.28);
        }
        .ld-footer-sm { font-size: 12.5px; color: rgba(255,255,255,.42); }

        @keyframes ld-fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .ld-anim { animation: ld-fadeUp .8s cubic-bezier(.22,.61,.36,1) both; }
        .ld-anim.delay-1 { animation-delay: .12s; }
        .ld-anim.delay-2 { animation-delay: .24s; }

        @media (max-width: 900px) {
            .ld-hero-grid { grid-template-columns: 1fr !important; }
            .ld-hero-art  { display: none; }
            .ld-stat-floating { left: 10px; bottom: 10px; }
        }
    </style>
</head>
<body class="landing antialiased min-h-screen ld-shell">

    <div class="ld-blob b1"></div>
    <div class="ld-blob b2"></div>
    <div class="ld-blob b3"></div>

    {{-- ─────── NAV ─────── --}}
    <nav class="ld-nav">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">
            <div class="flex items-center justify-between" style="height: 70px;">
                <a href="{{ url('/') }}" class="flex items-center gap-3 no-underline">
                    <span class="brand-mark" style="width:40px;height:40px;border-radius:11px;background:linear-gradient(135deg,#0d6e6e,#4ecdc4);display:flex;align-items:center;justify-content:center;color:white;font-family:'DM Serif Display',serif;font-size:20px;box-shadow:0 6px 18px rgba(78,205,196,.32);">E</span>
                    <span class="flex flex-col leading-tight">
                        <span class="ld-display" style="font-size:18px;color:var(--ink);">E-Attestatsiya</span>
                        <span style="font-size:10px;letter-spacing:1.6px;text-transform:uppercase;color:var(--muted);font-weight:600;">Davlat Portali</span>
                    </span>
                </a>

                <div class="hidden md:flex items-center" style="gap:32px;">
                    <a href="#about" class="ld-nav-link">Tizim haqida</a>
                    <a href="#features" class="ld-nav-link">Afzalliklar</a>
                    <a href="#workflow" class="ld-nav-link">Qanday ishlaydi</a>
                    <a href="#contact" class="ld-nav-link">Bog‘lanish</a>
                </div>

                <div class="flex items-center gap-3">
                    <div class="ld-lang hidden sm:inline-flex">
                        <a href="{{ route('lang.switch', 'uz') }}" class="{{ app()->getLocale() === 'uz' ? 'active' : '' }}">UZ</a>
                        <a href="{{ route('lang.switch', 'ru') }}" class="{{ app()->getLocale() === 'ru' ? 'active' : '' }}">RU</a>
                        <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                    </div>

                    @auth
                        <a href="{{ url('/dashboard') }}" class="ld-cta ld-cta-primary">
                            Kabinetga o‘tish
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <a href="{{ route('auth.select-type') }}" class="ld-cta ld-cta-primary">
                            Tizimga kirish
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ─────── HERO ─────── --}}
    <section id="about" class="ld-section ld-hero" style="padding-top:64px;padding-bottom:96px;">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">
            <div class="ld-hero-grid grid items-center gap-12" style="grid-template-columns: 1.05fr 1fr;">
                <div class="ld-anim">
                    <span class="ld-pill"><span class="dot"></span>O‘zR Mehnat vazirligi raqamli xizmati</span>

                    <h1 class="ld-display mt-6">
                        Ish o‘rinlarini <span class="accent">elektron attestatsiya</span> qilishning yagona davlat tizimi
                    </h1>

                    <p class="lead mt-6">
                        Tashkilotlar ish o‘rinlarini sanitariya-gigiena va xavfsizlik shartlariga ko‘ra onlayn ariza topshiradi;
                        attestatsiya o‘tkazuvchi tashkilotlar laboratoriya o‘lchovlarini va protokollarni elektron tarzda yuritadi;
                        davlat ekspertizasi yakuniy xulosani imzolaydi — barchasi bitta xavfsiz portalda.
                    </p>

                    <div class="flex flex-wrap items-center gap-3 mt-9">
                        <a href="{{ route('auth.select-type') }}" class="ld-cta ld-cta-primary" style="padding:14px 26px;font-size:15px;">
                            Tizimga kirish
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                        </a>
                        <a href="#workflow" class="ld-cta ld-cta-ghost" style="padding:14px 22px;font-size:15px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--teal);"><circle cx="12" cy="12" r="10"/><path d="M10 8l6 4-6 4z" fill="currentColor"/></svg>
                            Jarayon bilan tanishish
                        </a>
                    </div>

                    <div class="grid grid-cols-3 mt-12 gap-6 max-w-xl">
                        <div>
                            <div class="ld-display" style="font-size:30px;color:var(--ink);">7+</div>
                            <div style="font-size:12px;color:var(--muted);font-weight:600;letter-spacing:.4px;margin-top:4px;">Kirish rollari</div>
                        </div>
                        <div>
                            <div class="ld-display" style="font-size:30px;color:var(--ink);">100%</div>
                            <div style="font-size:12px;color:var(--muted);font-weight:600;letter-spacing:.4px;margin-top:4px;">Onlayn jarayon</div>
                        </div>
                        <div>
                            <div class="ld-display" style="font-size:30px;color:var(--ink);">OneID</div>
                            <div style="font-size:12px;color:var(--muted);font-weight:600;letter-spacing:.4px;margin-top:4px;">E-IMZO orqali kirish</div>
                        </div>
                    </div>
                </div>

                <div class="ld-hero-art ld-anim delay-1 relative">
                    <div class="ld-mock">
                        <div class="ld-mock-bar"><i></i><i></i><i></i>
                            <span style="margin-left:auto;font-size:11.5px;color:var(--muted);font-weight:600;">attestatsiya.uz/dashboard</span>
                        </div>

                        <div style="font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);font-weight:700;margin-bottom:10px;">Oxirgi arizalar</div>

                        <div class="ld-mock-row">
                            <div class="ic" style="background:var(--teal-light);color:var(--teal);">🏭</div>
                            <div class="meta">
                                <div class="t">"Toshkent Mexanika" MChJ</div>
                                <div class="s">Texnologik ish o‘rni №14 · 12 xodim</div>
                            </div>
                            <span class="badge sb-finalized" style="background:var(--green-light);color:var(--green);">Xulosa berildi</span>
                        </div>

                        <div class="ld-mock-row">
                            <div class="ic" style="background:var(--gold-light);color:var(--gold);">🧪</div>
                            <div class="meta">
                                <div class="t">"Sanoat Lab" laboratoriya</div>
                                <div class="s">Shovqin va yorug‘lik o‘lchovi · Protokol №48</div>
                            </div>
                            <span class="badge sb-approved" style="background:var(--gold-light);color:var(--gold);">Tekshiruvda</span>
                        </div>

                        <div class="ld-mock-row" style="margin-bottom:0;">
                            <div class="ic" style="background:var(--blue-light);color:var(--blue);">📋</div>
                            <div class="meta">
                                <div class="t">Davlat ekspertizasi</div>
                                <div class="s">7 yangi ariza ko‘rib chiqilmoqda</div>
                            </div>
                            <span class="badge sb-submitted" style="background:var(--blue-light);color:var(--blue);">Yangi</span>
                        </div>
                    </div>

                    <div class="ld-stat-floating">
                        <div class="icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z"/></svg>
                        </div>
                        <div>
                            <div class="num">PKCS-7</div>
                            <div class="lbl">E-IMZO imzo qo‘llab-quvvatlanadi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─────── FEATURES ─────── --}}
    <section id="features" class="ld-section" style="padding:96px 0;background:var(--white);border-top:1px solid var(--border-soft);border-bottom:1px solid var(--border-soft);">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">
            <div class="text-center max-w-3xl mx-auto" style="margin-bottom:56px;">
                <span class="ld-section-eyebrow">Tizim afzalliklari</span>
                <h2 class="ld-display">Hujjat-bozliksiz, tezkor va shaffof attestatsiya</h2>
                <p class="intro mx-auto" style="margin-top:14px;">
                    Bir nechta tashkilot va davlat organi o‘rtasidagi murakkab jarayon
                    yagona portalda boshqariladi: ariza topshirishdan to elektron xulosa berishgacha.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="feat-card">
                    <div class="ic" style="background:var(--teal-light);color:var(--teal);">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h7l-1 8 11-13h-7l0-7Z"/></svg>
                    </div>
                    <h4>Tezkor onlayn ariza</h4>
                    <p>OneID/E-IMZO orqali kirib, tashkilot va ish o‘rnini bir necha daqiqada ro‘yxatdan o‘tkazing — qog‘ozsiz va navbatsiz.</p>
                </div>

                <div class="feat-card dark">
                    <div class="ic">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4M20.62 7A10 10 0 1 1 4 17"/></svg>
                    </div>
                    <h4>To‘liq shaffoflik</h4>
                    <p>Har bir bosqich (HR, laboratoriya, institut, vazirlik) audit jurnali bilan qayd qilinadi. Tashkilot arizasining holatini real vaqt rejimida ko‘radi.</p>
                </div>

                <div class="feat-card">
                    <div class="ic" style="background:var(--gold-light);color:var(--gold);">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13h6M9 17h4"/></svg>
                    </div>
                    <h4>Elektron xulosa</h4>
                    <p>Davlat ekspertizasi PKCS-7 formatda elektron raqamli imzo qo‘yib xulosa beradi. PDF/CSV eksport, audit log va tarix saqlanadi.</p>
                </div>

                <div class="feat-card">
                    <div class="ic" style="background:#e8efff;color:var(--blue);">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    </div>
                    <h4>Markazlashtirilgan reyestr</h4>
                    <p>Barcha attestatsiya qilingan ish o‘rinlari yagona reyestrda — qidirish, filtrlash va kampaniya bo‘yicha hisobot olish bir bosishda.</p>
                </div>

                <div class="feat-card">
                    <div class="ic" style="background:var(--green-light);color:var(--green);">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h4>Xavfsizlik va GDPR</h4>
                    <p>Sessiyalar shifrlangan, parollar bcrypt, audit jurnal o‘zgartirilmas. HTTPS/TLS, ishonchli proxy va rate-limit tayyor.</p>
                </div>

                <div class="feat-card">
                    <div class="ic" style="background:#f4eaff;color:#6e3a9f;">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 14l4-4 4 4 5-5"/></svg>
                    </div>
                    <h4>Hisobot va analitika</h4>
                    <p>Kampaniya bo‘yicha statistika, holat dinamikasi va CSV eksport — vazirlik va admin uchun bevosita tayyor.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ─────── WORKFLOW ─────── --}}
    <section id="workflow" class="ld-section" style="padding:104px 0;">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">
            <div class="text-center max-w-2xl mx-auto" style="margin-bottom:64px;">
                <span class="ld-section-eyebrow">Jarayon</span>
                <h2 class="ld-display">Arizadan elektron xulosagacha — 5 qadam</h2>
                <p class="intro mx-auto" style="margin-top:14px;">
                    Har bir bosqich roli aniq belgilangan foydalanuvchi tomonidan bajariladi.
                    Ariza bir bosqichdan keyingisiga o‘tganda barcha ishtirokchilar avtomatik ogohlantiriladi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-8 relative">
                <div class="step">
                    <div class="num">1</div>
                    <h4>Identifikatsiya</h4>
                    <p>Tashkilot vakili OneID yoki E-IMZO (PKCS-7) orqali tizimga kiradi.</p>
                </div>
                <div class="step">
                    <div class="num gold">2</div>
                    <h4>Tashkilot va ish o‘rni</h4>
                    <p>Korxona ma‘lumotlari, ish o‘rinlari va xodimlar soni kiritiladi.</p>
                </div>
                <div class="step">
                    <div class="num purple">3</div>
                    <h4>Laboratoriya o‘lchovi</h4>
                    <p>Akkreditatsiyalangan laboratoriya o‘lchovlarni o‘tkazadi va protokol yaratadi.</p>
                </div>
                <div class="step">
                    <div class="num">4</div>
                    <h4>Institut tahlili</h4>
                    <p>Mehnat ilmiy-tadqiqot instituti dastlabki ekspertiza xulosasini yozadi.</p>
                </div>
                <div class="step">
                    <div class="num green">5</div>
                    <h4>Vazirlik xulosasi</h4>
                    <p>Davlat ekspertizasi elektron imzo bilan yakuniy attestatsiya xulosasini beradi.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ─────── ROLES ─────── --}}
    <section class="ld-section" style="padding:96px 0;background:var(--paper);border-top:1px solid var(--border-soft);">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">
            <div class="grid lg:grid-cols-2 gap-14 items-start">
                <div>
                    <span class="ld-section-eyebrow">Foydalanuvchi rollari</span>
                    <h2 class="ld-display">Bitta portal — yetti xil foydalanuvchi roli</h2>
                    <p class="intro mt-3">
                        Har bir rol o‘ziga tegishli ekran va vakolatlarni ko‘radi. Kontrol va xavfsizlik tizim darajasida ta’minlangan.
                    </p>
                    <a href="{{ route('auth.select-type') }}" class="ld-cta ld-cta-primary mt-7" style="padding:13px 24px;">
                        Roli bo‘yicha kirish
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @php
                        $roles = [
                            ['Ish beruvchi (employer)',          'Tashkilot va ish o‘rinlarini boshqaradi, tender va ariza yaratadi.', '🏭', 'teal'],
                            ['Laboratoriya',                     'Shovqin, yorug‘lik, havo va boshqa o‘lchovlarni o‘tkazadi.',          '🧪', 'gold'],
                            ['HR mas‘ul',                        'Tashkilotning kadrlar arizalarini ko‘rib chiqadi.',                   '🧑‍💼','blue'],
                            ['Komissiya',                        'Ish o‘rni baholash mezonlari bo‘yicha xulosa beradi.',                '⚖️',  'green'],
                            ['Institut eksperti',                 'Dastlabki ekspertiza xulosasini tayyorlaydi.',                        '🔬', 'gold'],
                            ['Vazirlik eksperti',                 'Davlat ekspertizasi yakuniy xulosasini imzolaydi.',                   '🏛️', 'teal'],
                            ['Administrator',                     'Tizim sozlamalari, kampaniyalar va hisobotlarni yuritadi.',           '🛡️', 'red'],
                        ];
                        $roleColor = [
                            'teal'  => ['bg'=>'var(--teal-light)',  'fg'=>'var(--teal)'],
                            'gold'  => ['bg'=>'var(--gold-light)',  'fg'=>'var(--gold)'],
                            'green' => ['bg'=>'var(--green-light)', 'fg'=>'var(--green)'],
                            'red'   => ['bg'=>'var(--red-light)',   'fg'=>'var(--red)'],
                            'blue'  => ['bg'=>'var(--blue-light)',  'fg'=>'var(--blue)'],
                        ];
                    @endphp
                    @foreach ($roles as [$title, $desc, $icon, $color])
                        <div style="background:var(--white);border:1px solid var(--border-soft);border-radius:14px;padding:16px 18px;display:flex;gap:12px;align-items:flex-start;transition:box-shadow .2s, transform .2s;">
                            <div style="width:40px;height:40px;border-radius:11px;background:{{ $roleColor[$color]['bg'] }};color:{{ $roleColor[$color]['fg'] }};display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">{{ $icon }}</div>
                            <div>
                                <div style="font-size:14px;font-weight:700;color:var(--ink);">{{ $title }}</div>
                                <div style="font-size:12.5px;color:var(--muted);margin-top:3px;line-height:1.5;">{{ $desc }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ─────── CTA banner ─────── --}}
    <section class="ld-section" style="padding:80px 0;background:var(--white);">
        <div class="max-w-5xl mx-auto px-6 lg:px-10">
            <div style="background:linear-gradient(135deg,#0e1117 0%,#0d2d3d 60%,#0a8a8a 130%);border-radius:28px;padding:56px 48px;color:white;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:24px;box-shadow:var(--shadow-xl);position:relative;overflow:hidden;">
                <div style="position:absolute;top:-60px;right:-40px;width:240px;height:240px;border-radius:50%;background:radial-gradient(circle,rgba(78,205,196,.35),transparent 70%);"></div>
                <div style="max-width:36rem;position:relative;">
                    <div class="ld-section-eyebrow" style="color:#4ecdc4;">Boshlash uchun tayyormisiz?</div>
                    <h3 class="ld-display" style="font-size:30px;color:white;margin-top:10px;">Birinchi attestatsiya arizasini bugun topshiring</h3>
                    <p style="margin-top:10px;color:rgba(255,255,255,.72);line-height:1.6;font-size:15px;">
                        OneID yoki E-IMZO bilan kirib, tashkilotingiz uchun yagona elektron attestatsiya tizimini ishga tushiring.
                    </p>
                </div>
                <div style="position:relative;display:flex;flex-direction:column;gap:10px;">
                    <a href="{{ route('auth.select-type') }}" class="ld-cta" style="background:white;color:var(--ink);padding:14px 28px;font-size:15px;">
                        Tizimga kirish
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </a>
                    <a href="#contact" style="text-align:center;color:rgba(255,255,255,.7);font-size:13px;font-weight:600;text-decoration:none;">yoki vazirlikka murojaat</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ─────── FOOTER ─────── --}}
    <footer id="contact" class="ld-footer">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 relative" style="z-index:1;">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10" style="padding-bottom:48px;border-bottom:1px solid rgba(255,255,255,.08);">
                <div class="md:col-span-5">
                    <div class="flex items-center gap-3" style="margin-bottom:18px;">
                        <div class="brand-mark">E</div>
                        <div>
                            <div class="ld-display" style="color:white;font-size:22px;">E-Attestatsiya</div>
                            <div style="font-size:11px;color:rgba(255,255,255,.45);letter-spacing:1.6px;text-transform:uppercase;font-weight:700;">Davlat Portali</div>
                        </div>
                    </div>
                    <p style="color:rgba(255,255,255,.62);font-size:14px;line-height:1.7;max-width:30rem;">
                        O‘zbekiston Respublikasi Mehnat va aholini ish bilan ta‘minlash vazirligining ish o‘rinlarini sanitariya-gigiena va xavfsizlik shartlariga ko‘ra elektron attestatsiya qilish yagona axborot tizimi.
                    </p>
                </div>

                <div class="md:col-span-3">
                    <h4>Tizim</h4>
                    <ul style="display:flex;flex-direction:column;gap:11px;font-size:14px;list-style:none;padding:0;margin:0;">
                        <li><a href="#about">Tizim haqida</a></li>
                        <li><a href="#features">Afzalliklar</a></li>
                        <li><a href="#workflow">Jarayon</a></li>
                        <li><a href="{{ route('auth.select-type') }}">Tizimga kirish</a></li>
                    </ul>
                </div>

                <div class="md:col-span-4">
                    <h4>Bog‘lanish</h4>
                    <ul style="display:flex;flex-direction:column;gap:14px;font-size:14px;color:rgba(255,255,255,.7);list-style:none;padding:0;margin:0;">
                        <li style="display:flex;gap:12px;align-items:flex-start;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ecdc4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>Toshkent shahri, Mustaqillik shoh ko‘chasi, 100011</span>
                        </li>
                        <li style="display:flex;gap:12px;align-items:center;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ecdc4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>+998 (71) 200-00-00</span>
                        </li>
                        <li style="display:flex;gap:12px;align-items:center;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ecdc4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg>
                            <span>info@mehnat.uz</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex flex-wrap justify-between items-center" style="padding-top:24px;gap:14px;">
                <p class="ld-footer-sm">© {{ date('Y') }} E-Attestatsiya · O‘zR Mehnat va aholini ish bilan ta’minlash vazirligi</p>
                <div class="flex items-center" style="gap:24px;font-size:13px;font-weight:600;">
                    <a href="#">Maxfiylik siyosati</a>
                    <a href="#">Foydalanish shartlari</a>
                    <a href="{{ url('/healthz') }}">Tizim holati</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
