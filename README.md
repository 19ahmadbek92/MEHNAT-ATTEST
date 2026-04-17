# E-Attestatsiya

Laravel asosidagi ish o‘rinlari mehnat sharoitlari attestatsiyasi jarayonini boshqarish tizimi: kampaniyalar, arizalar, komissiya baholashi, HR ko‘rib chiqishi, davlat ekspertizasi va hisobotlar.

## Talablar

- PHP **8.2+**
- [Composer](https://getcomposer.org/)
- Node.js **20+** va npm (frontend uchun)

## O‘rnatish (mahalliy)

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # yoki .env da MySQL/PostgreSQL
php artisan migrate
php artisan db:seed
npm install
npm run build
php artisan serve
```

Brauzerda `http://127.0.0.1:8000` oching. Seeder foydalanuvchilari va parollari `DatabaseSeeder` ichida; **productionda** ularni o‘zgartiring yoki seedni ishlatmang.

## Muhim muhit o‘zgaruvchilari

| O‘zgaruvchi | Tavsif |
|-------------|--------|
| `APP_ENV` | Productionda `production`. |
| `APP_DEBUG` | Productionda `false`. |
| `APP_URL` | To‘liq URL, masalan `https://attest.example.uz`. |
| `APP_KEY` | `php artisan key:generate` — bo‘sh qoldirilmasin. |
| `APP_DEMO_SSO` | `true` faqat staging/demo: OneID/ERI sinov kirish. Productionda `false`. |
| `TRUSTED_PROXIES` | Reverse proxy orqasida HTTPS uchun: `*` yoki IP ro‘yxati (vergul bilan). Ishonchsiz ochiq tarmoqda `*` ishlatmang. |
| `DB_*` | Productionda SQLite o‘rniga MySQL/PostgreSQL tavsiya etiladi. |

Batafsil izohlar: `.env.example`.

## Docker

```bash
docker build -t e-attestatsiya .
```

Ishga tushirishda konteynerga `.env` (yoki muhit o‘zgaruvchilari) bering; image ichida `.env` bo‘lmasligi kerak.

`docker-compose.yml` ishlab chiqarish oqimi uchun 3 process modelini beradi:
- `app` (HTTP)
- `worker` (queue)
- `scheduler` (schedule)

```bash
docker compose up -d --build
```

Health endpointlar:
- `GET /up` (Laravel built-in)
- `GET /healthz` (DB + cache check)

## OneID va ERI

Hozircha **demo** rejim: `APP_ENV=local` yoki `APP_DEMO_SSO=true` bo‘lganda yoqiladi. Haqiqiy OneID / E-IMZO integratsiyasi alohida ishlab chiqiladi.

## Testlar

```bash
php artisan test
```

GitHub Actions: `.github/workflows/tests.yml`.

## Operatsion hujjatlar

- Backup va rollback yo'riqnomasi: `docs/operations/backup-rollback.md`
- Secrets boshqaruvi: `docs/operations/secrets-management.md`
- Release gate va sign-off: `docs/operations/release-gate.md`

## Litsenziya

Laravel asosiy skeleti [MIT](https://opensource.org/licenses/MIT) litsenziyasi ostida. Loyiha modifikatsiyalari ham shu asosda tarqatilishi mumkin (kerak bo‘lsa, litsenziyani aniq belgilang).
