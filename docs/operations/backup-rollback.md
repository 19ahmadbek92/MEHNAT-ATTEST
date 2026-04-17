# Backup va Rollback Playbook

## 1. Database backup (kunlik)

- MySQL:
  - `mysqldump -h <host> -u <user> -p<password> <db_name> > backup_$(date +%F).sql`
- Fayl nomi format:
  - `attestation_prod_YYYY-MM-DD.sql`
- Saqlash:
  - Kamida 14 kun retention (`D14`)
  - Haftalik full backup (`W8`)

## 2. Restore (avariya holati)

1. Platformani maintenance holatga o'tkazing:
   - `php artisan down --retry=60`
2. Oxirgi sog'lom backupni tiklang:
   - `mysql -h <host> -u <user> -p<password> <db_name> < attestation_prod_YYYY-MM-DD.sql`
3. Migratsiya holatini tekshiring:
   - `php artisan migrate:status`
4. Keshlarni yangilang:
   - `php artisan optimize:clear`
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`
5. Platformani ishga tushiring:
   - `php artisan up`

## 3. Deploy rollback

1. Oldingi image tag'ga qayting:
   - `docker compose pull app worker scheduler`
   - `docker compose up -d app worker scheduler`
2. Agar release migratsiya bilan kelgan bo'lsa va backward-compatible bo'lmasa:
   - DB restore + oldingi app image.
3. Health check:
   - `GET /up`
   - `GET /healthz`

## 4. Tekshiruv ro'yxati

- Login ishlayaptimi
- Ariza oqimi ochilyaptimi
- Queue ishlayaptimi (`queue:work` loglari)
- Scheduler tasklar yuguryaptimi
