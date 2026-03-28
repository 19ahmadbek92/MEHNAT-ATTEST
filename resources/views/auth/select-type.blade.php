<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Attestatsiya | Kirish</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            background: #0a0f1e;
            overflow: hidden;
        }

        /* ── Animated gradient background ── */
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

        /* ── Main layout ── */
        .page {
            position: relative; z-index: 1;
            min-height: 100vh;
            display: flex;
        }

        /* ── Left panel ── */
        .left-panel {
            width: 48%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
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
            margin-bottom: 28px;
        }

        .hero-title {
            font-family: 'DM Serif Display', serif;
            font-size: 46px; line-height: 1.15;
            color: white;
            margin-bottom: 20px;
        }
        .hero-title .accent { color: #4ecdc4; }

        .hero-desc {
            font-size: 15px; color: rgba(255,255,255,0.5);
            line-height: 1.8; max-width: 400px;
        }

        /* Stats strip */
        .stats-strip {
            display: flex; gap: 32px;
            margin-top: 48px;
            padding-top: 32px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .stat-item {}
        .stat-num {
            font-family: 'DM Serif Display', serif;
            font-size: 28px; color: white;
        }
        .stat-label { font-size: 12px; color: rgba(255,255,255,0.35); margin-top: 2px; }

        .left-footer {
            font-size: 12px; color: rgba(255,255,255,0.2);
            line-height: 1.7;
        }

        /* ── Right panel ── */
        .right-panel {
            flex: 1;
            display: flex; align-items: center; justify-content: center;
            padding: 40px 48px;
        }

        .login-card {
            width: 100%; max-width: 440px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            padding: 44px 40px;
            backdrop-filter: blur(24px);
            box-shadow: 0 24px 80px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.08);
            animation: slideIn 0.6s cubic-bezier(0.22, 0.61, 0.36, 1) both;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-eyebrow {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 32px;
        }
        .eyebrow-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #4ecdc4;
            box-shadow: 0 0 8px #4ecdc4;
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(0.8); }
        }
        .eyebrow-text { font-size: 12px; color: rgba(255,255,255,0.4); font-weight: 500; letter-spacing: 1px; text-transform: uppercase; }

        .card-title {
            font-family: 'DM Serif Display', serif;
            font-size: 28px; color: white; margin-bottom: 6px;
        }
        .card-subtitle { font-size: 14px; color: rgba(255,255,255,0.4); margin-bottom: 32px; }

        /* Login options */
        .login-options { display: flex; flex-direction: column; gap: 14px; margin-bottom: 28px; }

        .login-option {
            display: flex; align-items: center; justify-content: space-between;
            padding: 18px 20px;
            border-radius: 16px;
            border: 1.5px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.03);
            text-decoration: none;
            color: white;
            transition: all 0.25s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .login-option::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.03));
            opacity: 0;
            transition: opacity 0.25s;
        }

        .login-option:hover {
            border-color: rgba(255,255,255,0.2);
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.4);
            background: rgba(255,255,255,0.06);
            color: white;
            text-decoration: none;
        }
        .login-option:hover::before { opacity: 1; }

        .login-option.primary { border-color: rgba(78,205,196,0.3); background: rgba(78,205,196,0.06); }
        .login-option.primary:hover { border-color: #4ecdc4; background: rgba(78,205,196,0.12); box-shadow: 0 12px 40px rgba(78,205,196,0.15); }

        .option-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; flex-shrink: 0;
        }
        .option-icon.teal { background: linear-gradient(135deg, #0d6e6e, #4ecdc4); box-shadow: 0 4px 16px rgba(78,205,196,0.3); }
        .option-icon.gold { background: linear-gradient(135deg, #a47621, #c9952a); box-shadow: 0 4px 16px rgba(201,149,42,0.3); }

        .option-body { padding: 0 14px; flex: 1; }
        .option-title { font-size: 15px; font-weight: 700; color: white; margin-bottom: 2px; }
        .option-sub { font-size: 11px; color: rgba(255,255,255,0.35); font-weight: 400; letter-spacing: 0.5px; text-transform: uppercase; }

        .option-arrow {
            width: 32px; height: 32px; border-radius: 8px;
            background: rgba(255,255,255,0.06);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; color: rgba(255,255,255,0.4);
            transition: all 0.2s;
        }
        .login-option:hover .option-arrow {
            background: rgba(255,255,255,0.12);
            color: white;
            transform: translateX(2px);
        }

        /* Divider */
        .divider-row { display: flex; align-items: center; gap: 12px; margin: 8px 0; }
        .divider-line { flex: 1; height: 1px; background: rgba(255,255,255,0.06); }
        .divider-text { font-size: 11px; color: rgba(255,255,255,0.2); font-weight: 500; white-space: nowrap; }

        /* Footer note */
        .card-footer { text-align: center; }
        .card-footer p { font-size: 12px; color: rgba(255,255,255,0.2); line-height: 1.7; }

        /* Mobile */
        @media (max-width: 900px) {
            body { overflow: auto; }
            .page { flex-direction: column; min-height: auto; }
            .left-panel { width: 100%; padding: 32px 24px 20px; }
            .hero-title { font-size: 30px; }
            .stats-strip { gap: 20px; }
            .right-panel { padding: 16px 20px 32px; }
            .login-card { padding: 28px 24px; }
        }
    </style>
</head>
<body>
    <div class="bg-scene">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <div class="page">
        <!-- Left panel -->
        <div class="left-panel">
            <div class="logo-wrap">
                <div class="logo-icon">⚖️</div>
                <div>
                    <div class="logo-text">Mehnat<span>Attest</span></div>
                    <div class="logo-sub">Attestatsiya platformasi</div>
                </div>
            </div>

            <div class="hero-content">
                <div class="badge-pill">
                    <span style="width:6px; height:6px; border-radius:50%; background:#4ecdc4; display:inline-block; box-shadow: 0 0 6px #4ecdc4;"></span>
                    Davlat platformasi
                </div>

                <h1 class="hero-title">
                    Ish o'rinlari<br>
                    mehnat sharoitlari<br>
                    <span class="accent">attestatsiyasi</span>
                </h1>

                <p class="hero-desc">
                    Mehnat sharoitlari va asbob-uskunalarning
                    jarohatlash xavfliligi yuzasidan yagona
                    raqamli attestatsiya platformasi.
                </p>

                <div class="stats-strip">
                    <div class="stat-item">
                        <div class="stat-num">18</div>
                        <div class="stat-label">Ishlab chiqarish omili</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num">4</div>
                        <div class="stat-label">bosqichli jarayon</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num">100%</div>
                        <div class="stat-label">Raqamli tizim</div>
                    </div>
                </div>
            </div>

            <div class="left-footer">
                VM Qarori №263 · 15.09.2014<br>
                © {{ now()->year }} Kambag'allikni qisqartirish va bandlik vazirligi
            </div>
        </div>

        <!-- Right panel -->
        <div class="right-panel">
            <div class="login-card">
                <div class="card-eyebrow">
                    <div class="eyebrow-dot"></div>
                    <span class="eyebrow-text">Xavfsiz kirish</span>
                </div>

                <div class="card-title">Kabinetga kiring</div>
                <div class="card-subtitle">Kirish turini tanlang</div>

                <div class="login-options">
                    <!-- OneID -->
                    <a href="{{ route('auth.oneid.redirect') }}" class="login-option primary">
                        <div class="option-icon teal">👤</div>
                        <div class="option-body">
                            <div class="option-title">Jismoniy shaxs</div>
                            <div class="option-sub">OneID orqali kirish</div>
                        </div>
                        <div class="option-arrow">→</div>
                    </a>

                    <!-- ERI -->
                    <a href="{{ route('auth.eri.login') }}" class="login-option">
                        <div class="option-icon gold">🏢</div>
                        <div class="option-body">
                            <div class="option-title">Yuridik shaxs</div>
                            <div class="option-sub">ERI (E-imzo) orqali kirish</div>
                        </div>
                        <div class="option-arrow">→</div>
                    </a>
                </div>

                <div class="divider-row">
                    <div class="divider-line"></div>
                    <span class="divider-text">yoki ishlab chiqish uchun</span>
                    <div class="divider-line"></div>
                </div>

                <div style="margin-top: 16px;">
                    <a href="{{ route('login.email') }}" class="login-option" style="justify-content: center; gap: 10px; padding: 14px 20px;">
                        <span style="font-size: 18px;">🔑</span>
                        <div>
                            <div class="option-title" style="font-size: 14px; text-align: center;">Login / Parol orqali kirish</div>
                        </div>
                    </a>
                </div>

                <div class="card-footer" style="margin-top: 28px;">
                    <p>
                        Platformadan foydalanish orqali siz<br>
                        <span style="color: rgba(255,255,255,0.35);">foydalanish shartlari</span>ga rozilik bildirasiz.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
