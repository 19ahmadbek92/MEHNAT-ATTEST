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
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; min-height: 100vh; background: #0a0f1e; }

        /* Blobs */
        .g-bg { position: fixed; inset: 0; background: linear-gradient(135deg, #0a0f1e 0%, #0d1b2a 40%, #0e2340 70%, #071629 100%); overflow: hidden; pointer-events: none; }
        .g-blob { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.22; animation: gfloat 9s ease-in-out infinite; }
        .g-blob-1 { width: 600px; height: 600px; background: radial-gradient(circle, #0d6e6e, transparent); top: -150px; left: -80px; animation-delay: 0s; }
        .g-blob-2 { width: 500px; height: 500px; background: radial-gradient(circle, #c9952a, transparent); bottom: -120px; right: -80px; animation-delay: 3.5s; }
        .g-blob-3 { width: 350px; height: 350px; background: radial-gradient(circle, #4ecdc4, transparent); top: 40%; right: 20%; animation-delay: 6s; opacity: 0.1; }
        @keyframes gfloat { 0%,100%{transform:translateY(0) scale(1);} 50%{transform:translateY(-24px) scale(1.04);} }

        /* Layout */
        .g-page { position: relative; z-index: 1; display: flex; min-height: 100vh; }

        /* Left branding panel */
        .g-left {
            width: 45%; padding: 48px 52px;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .g-logo { display: flex; align-items: center; gap: 12px; }
        .g-logo-icon { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #0d6e6e, #4ecdc4); display: flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 4px 16px rgba(78,205,196,0.3); }
        .g-logo-text { font-family: 'DM Serif Display', serif; font-size: 24px; color: white; }
        .g-logo-text span { color: #4ecdc4; }
        .g-logo-sub { font-size: 10px; color: rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 2px; margin-top: 2px; }

        .g-hero { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 32px 0; }
        .g-badge { display: inline-flex; align-items: center; gap: 7px; background: rgba(78,205,196,0.08); border: 1px solid rgba(78,205,196,0.25); color: #4ecdc4; padding: 5px 12px; border-radius: 100px; font-size: 10px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 24px; width: fit-content; }
        .g-badge-dot { width: 6px; height: 6px; border-radius: 50%; background: #4ecdc4; box-shadow: 0 0 6px #4ecdc4; animation: gpulse 2s ease-in-out infinite; }
        @keyframes gpulse { 0%,100%{opacity:1;} 50%{opacity:0.5;} }

        .g-title { font-family: 'DM Serif Display', serif; font-size: 40px; line-height: 1.15; color: white; margin-bottom: 16px; }
        .g-title .ac { color: #4ecdc4; }
        .g-desc { font-size: 14px; color: rgba(255,255,255,0.45); line-height: 1.8; max-width: 380px; }

        .g-steps { display: flex; flex-direction: column; gap: 14px; margin-top: 36px; }
        .g-step { display: flex; align-items: center; gap: 14px; }
        .g-step-num { width: 28px; height: 28px; border-radius: 8px; background: rgba(78,205,196,0.1); border: 1px solid rgba(78,205,196,0.2); color: #4ecdc4; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .g-step-txt { font-size: 13px; color: rgba(255,255,255,0.4); }
        .g-step-txt strong { color: rgba(255,255,255,0.75); font-weight: 600; }

        .g-footer { font-size: 11px; color: rgba(255,255,255,0.18); line-height: 1.8; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px; }

        /* Right form panel */
        .g-right { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 48px; }
        .g-card {
            width: 100%; max-width: 440px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 24px;
            padding: 40px 36px;
            backdrop-filter: blur(24px);
            box-shadow: 0 24px 80px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.06);
            animation: gslide 0.5s cubic-bezier(0.22,0.61,0.36,1) both;
        }
        @keyframes gslide { from{opacity:0;transform:translateY(18px);} to{opacity:1;transform:translateY(0);} }

        .g-eyebrow { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; }
        .g-eyebrow-dot { width: 7px; height: 7px; border-radius: 50%; background: #4ecdc4; box-shadow: 0 0 7px #4ecdc4; animation: gpulse 2s ease-in-out infinite; }
        .g-eyebrow-txt { font-size: 11px; color: rgba(255,255,255,0.35); font-weight: 500; letter-spacing: 1px; text-transform: uppercase; }

        .g-card-title { font-family: 'DM Serif Display', serif; font-size: 26px; color: white; margin-bottom: 4px; }
        .g-card-sub { font-size: 13px; color: rgba(255,255,255,0.35); margin-bottom: 28px; }

        /* Dark form fields */
        .g-field { margin-bottom: 18px; }
        .g-label { display: block; font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 7px; }
        .g-input {
            width: 100%; padding: 11px 14px;
            background: rgba(255,255,255,0.05);
            border: 1.5px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            color: white; font-size: 14px; font-family: 'DM Sans', sans-serif;
            outline: none; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .g-input::placeholder { color: rgba(255,255,255,0.2); }
        .g-input:focus { border-color: rgba(78,205,196,0.5); box-shadow: 0 0 0 3px rgba(78,205,196,0.1); }
        .g-input-error { font-size: 12px; color: #ff6b6b; margin-top: 5px; }

        /* Checkbox */
        .g-check-row { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; cursor: pointer; }
        .g-check-row input[type=checkbox] { width: 16px; height: 16px; accent-color: #4ecdc4; border-radius: 4px; }
        .g-check-row span { font-size: 13px; color: rgba(255,255,255,0.4); }

        /* Buttons */
        .g-btn {
            width: 100%; padding: 12px;
            border-radius: 10px;
            font-size: 14px; font-weight: 700; font-family: 'DM Sans', sans-serif;
            cursor: pointer; border: none; transition: all 0.2s; text-decoration: none;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .g-btn-primary { background: linear-gradient(135deg, #0d6e6e, #4ecdc4); color: white; box-shadow: 0 4px 20px rgba(78,205,196,0.25); }
        .g-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(78,205,196,0.35); }

        /* SSO buttons */
        .g-sso { display: flex; flex-direction: column; gap: 10px; }
        .g-sso-btn {
            display: flex; align-items: center; gap: 12px; padding: 12px 16px;
            border-radius: 12px; border: 1.5px solid rgba(255,255,255,0.07);
            background: rgba(255,255,255,0.03); text-decoration: none; color: white;
            transition: all 0.2s;
        }
        .g-sso-btn:hover { border-color: rgba(255,255,255,0.18); background: rgba(255,255,255,0.06); transform: translateY(-1px); color: white; text-decoration: none; }
        .g-sso-icon { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 800; font-family: monospace; flex-shrink: 0; }
        .g-sso-1 .g-sso-icon { background: linear-gradient(135deg, #3730a3, #6366f1); color: white; font-size: 12px; box-shadow: 0 3px 12px rgba(99,102,241,0.3); }
        .g-sso-2 .g-sso-icon { background: linear-gradient(135deg, #0d6e6e, #4ecdc4); font-size: 14px; box-shadow: 0 3px 12px rgba(78,205,196,0.25); }
        .g-sso-body .g-sso-title { font-size: 13px; font-weight: 600; color: white; }
        .g-sso-body .g-sso-sub { font-size: 10px; color: rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.5px; }
        .g-sso-arrow { margin-left: auto; font-size: 14px; color: rgba(255,255,255,0.25); }

        /* Divider */
        .g-divider { display: flex; align-items: center; gap: 10px; margin: 20px 0; }
        .g-divider-line { flex: 1; height: 1px; background: rgba(255,255,255,0.06); }
        .g-divider-txt { font-size: 11px; color: rgba(255,255,255,0.2); white-space: nowrap; font-weight: 500; }

        /* Links */
        .g-link { color: #4ecdc4; text-decoration: none; font-size: 12px; }
        .g-link:hover { color: #6edbd8; }
        .g-row-between { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }

        /* Errors */
        .g-alert-error { padding: 10px 14px; background: rgba(192,57,43,0.1); border: 1px solid rgba(192,57,43,0.2); border-radius: 8px; color: #ff7c7c; font-size: 13px; margin-bottom: 18px; }

        /* Mobile */
        @media (max-width: 900px) {
            body { overflow: auto; }
            .g-page { flex-direction: column; }
            .g-left { width: 100%; padding: 28px 24px 16px; }
            .g-steps { display: none; }
            .g-title { font-size: 26px; }
            .g-right { padding: 12px 20px 32px; }
            .g-card { padding: 28px 22px; }
        }
    </style>
</head>
<body>
    <div class="g-bg">
        <div class="g-blob g-blob-1"></div>
        <div class="g-blob g-blob-2"></div>
        <div class="g-blob g-blob-3"></div>
    </div>

    <div class="g-page">
        {{-- Branding Left --}}
        <div class="g-left">
            <a href="/" style="text-decoration:none;">
                <div class="g-logo">
                    <div class="g-logo-icon">⚖️</div>
                    <div>
                        <div class="g-logo-text">Mehnat<span>Attest</span></div>
                        <div class="g-logo-sub">Attestatsiya platformasi</div>
                    </div>
                </div>
            </a>

            <div class="g-hero">
                <div class="g-badge">
                    <span class="g-badge-dot"></span>
                    Davlat platformasi
                </div>
                <h1 class="g-title">
                    Ish o'rinlari<br>
                    mehnat sharoitlari<br>
                    <span class="ac">attestatsiyasi</span>
                </h1>
                <p class="g-desc">
                    Mehnat sharoitlari va asbob-uskunalarning
                    jarohatlash xavfliligi yuzasidan yagona
                    raqamli attestatsiya platformasi.
                </p>

                <div class="g-steps">
                    <div class="g-step">
                        <div class="g-step-num">1</div>
                        <div class="g-step-txt"><strong>Ish beruvchi</strong> ariza topshiradi</div>
                    </div>
                    <div class="g-step">
                        <div class="g-step-num">2</div>
                        <div class="g-step-txt"><strong>Laboratoriya</strong> 18 omilni o'lchaydi</div>
                    </div>
                    <div class="g-step">
                        <div class="g-step-num">3</div>
                        <div class="g-step-txt"><strong>Institut</strong> dastlabki baholash</div>
                    </div>
                    <div class="g-step">
                        <div class="g-step-num">4</div>
                        <div class="g-step-txt"><strong>Vazirlik</strong> yakuniy xulosa beradi</div>
                    </div>
                </div>
            </div>

            <div class="g-footer">
                VM Qarori №263 · 15.09.2014<br>
                © {{ now()->year }} Kambag'allikni qisqartirish va bandlik vazirligi
            </div>
        </div>

        {{-- Form Right --}}
        <div class="g-right">
            <div class="g-card">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
