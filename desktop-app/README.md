# Ergonomik va inson omili xavfsizlik baholash dasturi

Yarim tayyor mahsulot va xomashyoni solish-ortish (yuklash-tushirish) ishlarida band
ishchilarning mehnat xavfsizligini **inson omili** va **ergonomik yuklanishlarni**
kompleks baholovchi mustaqil desktop dastur (alyuminiy profillarini ishlab
chiqaruvchilari misolida).

Bu dastur MEHNAT-ATTEST veb-platformasidan **mustaqil** ishlaydi: internet, login,
server yoki ma'lumotlar bazasi sozlash shart emas — barcha hisob-kitoblar
kompyuteringizda, offline holda bajariladi. Natijalar mahalliy `ergonomic_assessments.db`
(SQLite) fayliga saqlanadi.

## Talablar

- Python 3.9+ (Windows/macOS/Linux). `tkinter` va `sqlite3` Python bilan birga keladi —
  qo'shimcha paket o'rnatish shart emas.
  - Ba'zi Linux distributivlarida (Debian/Ubuntu) `tkinter` alohida paket sifatida
    kerak bo'lishi mumkin: `sudo apt install python3-tk`.

## Ishga tushirish

```bash
cd desktop-app
python3 app.py
```

## Metodika

- **NIOSH (1994) ko'tarish tenglamasi** — yuk ko'tarish/tashish uchun tavsiya etilgan
  og'irlik chegarasi (RWL) va ko'tarish indeksi (LI) hisoblanadi.
- **R2.2.2006-05 / SanQvaM 0069-24 uslubi** — og'irlik darajasi (poza, statik yuk, tana
  egilishi, yurish masofasi) va psixofiziologik zo'riqish darajasi 1 / 2 / 3.1–3.4 / 4
  klasslariga ajratiladi.
- **Inson omili xavf balli** — tajriba, o'qitilganlik, charchoq, sog'liq holati,
  oldingi baxtsiz hodisalar asosida 0–100 ballik xavf ko'rsatkichi.
- **Ish muhiti omillari** — alyuminiy profil ishlab chiqarishga xos harorat, metall
  changi va shovqin ko'rsatkichlari.
- Barchasi **0–100 integral xavfsizlik ko'rsatkichi** va avtomatik tavsiyalarga
  birlashtiriladi.

Hisoblash mexanizmi (`engine.py`) MEHNAT-ATTEST veb-platformasidagi
`App\Services\ErgonomicAssessmentService` (PHP) bilan bir xil formulalarga
asoslangan — ikkala dastur bir xil kirish ma'lumotlari uchun bir xil natija beradi.

## Fayllar

| Fayl | Vazifasi |
|------|----------|
| `app.py` | Tkinter interfeysi — kirish formasi, natija oynasi, tarix ro'yxati |
| `engine.py` | Hisoblash mexanizmi (GUI'dan mustaqil, sinovdan o'tkazilishi mumkin) |
| `storage.py` | Mahalliy SQLite orqali baholashlar tarixini saqlash |
| `tests/test_engine.py` | `engine.py` uchun birlik testlari |
| `ergonomic_assessments.db` | Dastur birinchi marta ishga tushganda avtomatik yaratiladi |

## Testlarni ishga tushirish

```bash
cd desktop-app
python3 -m unittest discover -s tests -v
```

## Foydalanish

1. **"Yangi baholash"** bo'limida formani to'ldiring, yoki **"Namuna bilan
   to'ldirish"** tugmasi orqali alyuminiy profil bog'lamini yuklash-tushirish
   misolini avtomatik yuklang.
2. **"Hisoblash va saqlash"** tugmasini bosing — natija oynasida integral xavfsizlik
   ko'rsatkichi, NIOSH ko'tarish indeksi, xavf klasslari va tavsiyalar ko'rsatiladi.
3. **"Tarix"** bo'limida barcha oldingi baholashlar ro'yxatini ko'rish, qayta ochish
   yoki o'chirish mumkin.
