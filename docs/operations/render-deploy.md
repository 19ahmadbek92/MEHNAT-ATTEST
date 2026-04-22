# Render.com da ishlab chiqarishga deploy qilish

Bu loyihani Render da Docker web service sifatida mustahkam ishga tushirish uchun minimal qadamlar.

## 1. PostgreSQL yarating (tavsiya etiladi)

Render panel → **New → PostgreSQL**
- Name: `eattest-db`
- Region: xizmat bilan bir xil
- Plan: Starter (boshlanishi uchun) yoki yuqori

Yaratilgandan keyin **Info** tabida quyidagilarni saqlab qo‘ying:
- `Internal Database URL` — `postgres://user:pass@host:5432/db` — shuni ishlatamiz.
- Host, Port, DB name, User, Password (alohida kerak bo‘lsa).

## 2. Web Service yarating

**New → Web Service → Git repo (GitHub: e-attestatsiya)**

- **Language**: Docker
- **Region**: PG bilan bir xil
- **Branch**: `main`
- **Dockerfile path**: `./Dockerfile` (default)
- **Health Check Path**: `/container-live.txt`
- **Plan**: Starter yoki yuqori

## 3. Environment Variables

Quyidagilarni **Environment** yorlig‘iga qo‘shing:

| Kalit | Qiymat | Izoh |
|---|---|---|
| `APP_ENV` | `production` | |
| `APP_DEBUG` | `false` | |
| `APP_URL` | `https://SERVICE.onrender.com` | o‘z domeningiz |
| `APP_KEY` | `base64:...` | `php artisan key:generate --show` natijasi |
| `LOG_CHANNEL` | `stderr` | Render Logs paneliga |
| `LOG_LEVEL` | `warning` | |
| `TRUSTED_PROXIES` | `*` | HTTPS redirect Render orqasida |
| `DB_CONNECTION` | `pgsql` | |
| `DATABASE_URL` | `(Internal Database URL)` | 1-qadamdan |
| `SESSION_DRIVER` | `database` | |
| `CACHE_STORE` | `database` | |
| `QUEUE_CONNECTION` | `database` | |
| `RUN_MIGRATIONS` | `true` | birinchi deploydan keyin `false` qilish mumkin |
| `APP_DEMO_SSO` | `false` | |
| `APP_SSO_ROUTES_ENABLED` | `true` yoki `false` | real OneID/ERI ulaganmisiz? |

Real OneID/ERI ishlatilsa:
- `ONEID_BASE_URL`, `ONEID_CLIENT_ID`, `ONEID_CLIENT_SECRET`, `ONEID_REDIRECT_URI`
- (ixtiyoriy) `ONEID_AUTHORIZE_PATH`, `ONEID_TOKEN_PATH`, `ONEID_USERINFO_PATH`, `ONEID_SCOPE`
- `ERI_VERIFICATION_URL` yoki lokal PKCS7 mode

## 4. Birinchi deploy

Save → **Deploy**. Build bosqichlari:
1. Composer vendor (alohida stage)
2. Vite assetlar (node stage)
3. Yakuniy `webdevops/php-nginx:8.2` tasvir

Konteyner ishga tushgach, `[laravel-boot]` loglari paydo bo‘ladi:
```
[laravel-boot] role=app env=production ...
[laravel-boot] writable paths: storage, bootstrap/cache, database
[laravel-boot] waiting for pgsql at host:5432 (max 60s)...
[laravel-boot] DB erishildi
[laravel-boot] running migrations (--force)
[laravel-boot] migrations OK
[laravel-boot] warming caches ...
[laravel-boot] Laravel ready ✔
```

Ushbu qatorlar chiqqanida `/`, `/up`, `/healthz` javob beradi.

## 5. Deploydan keyingi qadamlar

- Birinchi muvaffaqiyatli deploydan so‘ng `RUN_MIGRATIONS=false` qo‘yib qayta deploy (tez start).
- Admin foydalanuvchi yaratish: Render Shell → `php artisan tinker` → `\App\Models\User::factory()->admin()->create([...])` yoki seed.
- Agar OneID callback URL o‘zgarsa, `ONEID_REDIRECT_URI` ni yangilab, qayta deploy qiling.
- Monitoring: Render Logs → `stderr` oqimi. Kerak bo‘lsa `LOG_SLACK_WEBHOOK_URL` yoqing.

## 6. Xatoliklarni tuzatish

**`SQLSTATE ... readonly database`** — SQLite ishlatgansiz. Pgsql’ga o‘ting (yuqoridagi jadval).

**`connect() failed (111: Connection refused) ... 127.0.0.1:9000`** — Nginx php-fpm tayyor bo‘lishini kutishi kerak. Loyiha allaqachon `99-wait-php-fpm.sh` bilan kutadi; agar xato davom etsa, log panelini tekshiring.

**`Exited with status 1`** — Loglarda `[laravel-boot][ERROR]` qatori bor. Odatda: DB yetib bo‘lmadi yoki `APP_KEY` yo‘q. Environment qiymatlarini tekshiring.

**`No application encryption key has been specified`** — `APP_KEY` o‘rnatilmagan. Lokal mashinada `php artisan key:generate --show` natijasini Render env panel’ga qo‘ying.

## 7. Worker/Scheduler (ixtiyoriy)

Queue uchun alohida **Background Worker**:
- Dockerfile: bir xil
- Start command: `/usr/local/bin/laravel-role-cmd`
- Env: `APP_PROCESS_ROLE=worker` + boshqa barcha env’lar (DB, APP_KEY …)

Scheduler uchun **Cron Job** yoki shu bilan `APP_PROCESS_ROLE=scheduler`.
