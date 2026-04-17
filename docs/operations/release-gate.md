# Release Gate va Staging Sign-off

## 1. Avtomat tekshiruvlar

Lokal yoki CI:

```bash
php artisan release:gate
```

Bu quyidagilarni tekshiradi:
- testlar
- migratsiya holati
- migratsiya dry-run
- config/route/view cache

## 2. Qo'shimcha xavfsizlik tekshiruv

```bash
composer audit
```

## 3. Staging sign-off checklist

- [ ] Biznes vakil: asosiy oqimlar qabul qilindi (ariza, HR, komissiya, ekspertiza, hisobot).
- [ ] Texnik vakil: monitoring, loglar, backup/restore tekshirildi.
- [ ] SSO holati tasdiqlandi (`APP_DEMO_SSO=false` productionda).
- [ ] DB backup snapshot olindi.
- [ ] Rollback rejasi tasdiqlandi.
