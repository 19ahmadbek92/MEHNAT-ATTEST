# Secrets boshqaruvi bo'yicha tavsiya

## Minimal qoidalar

- `.env` faylini gitga yuklamang.
- Har muhitda (`staging`, `production`) alohida credential ishlating.
- Har 90 kunda parol/kalitlarni rotatsiya qiling.

## Tavsiya etilgan saqlash joyi

- Render/Fly/Heroku: platforma environment secrets.
- AWS: Secrets Manager yoki SSM Parameter Store.
- GCP: Secret Manager.

## Majburiy sirlar

- `APP_KEY`
- `DB_PASSWORD`
- `MAIL_PASSWORD`
- `ONEID_CLIENT_SECRET` (integratsiya bo'lsa)
- `ERI_CLIENT_SECRET` (integratsiya bo'lsa)

## Incident holatida

1. Oqib ketgan secretni bekor qiling.
2. Yangi secret chiqaring.
3. Deploy orqali yangilang.
4. Audit loglarni tekshiring.
