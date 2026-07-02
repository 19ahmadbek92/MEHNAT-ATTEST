# Ergonomik Baholash — Android dasturi

Yarim tayyor mahsulot va xomashyoni solish-ortish (yuklash-tushirish) ishlarida band
ishchilarning mehnat xavfsizligini **inson omili** va **ergonomik yuklanishlarni**
kompleks baholovchi native Android dastur (alyuminiy profillarini ishlab
chiqaruvchilari misolida).

Bu loyiha MEHNAT-ATTEST veb-platformasi va `desktop-app/` (Python) dasturi bilan bir
xil hisoblash mantig'iga asoslangan (`Engine.kt` — NIOSH ko'tarish tenglamasi,
SanQvaM 0069-24 uslubidagi og'irlik/zo'riqish klasslari, inson omili xavf balli).
Telefonda offline ishlaydi, natijalar qurilma ichidagi SQLite bazasida saqlanadi.

## Tayyor APK qanday olinadi

Bu repo'dagi **GitHub Actions** (`.github/workflows/android-build.yml`) har push'da
`android-app/**` o'zgarganda avtomatik ishga tushadi va debug APK'ni yig'ib, workflow
run sahifasidagi **Artifacts** bo'limiga (`ergonomik-baholash-debug-apk`) yuklaydi.
Uni yuklab olib, telefoningizga o'rnatishingiz mumkin (noma'lum manbalardan o'rnatishga
ruxsat berish kerak bo'ladi, chunki Google Play orqali tarqatilmagan).

## Mahalliy build (Android Studio bilan)

1. `android-app/` papkasini Android Studio'da oching (Gradle wrapper avtomatik
   yaratiladi — bu repo'da `gradlew` ataylab saqlanmagan, chunki uni yig'ish uchun
   `services.gradle.org`ga ulanish kerak, bu esa hozirgi CI/build muhitida bloklangan;
   Android Studio buni avtomatik hal qiladi).
2. Gradle sinxronizatsiyasidan so'ng **Run** tugmasini bosing yoki
   **Build > Build Bundle(s) / APK(s) > Build APK(s)**.

Terminal orqali (Android SDK va Gradle o'rnatilgan bo'lsa):

```bash
cd android-app
gradle wrapper --gradle-version 8.7   # bir martalik: gradlew yaratadi
./gradlew assembleDebug
```

APK: `app/build/outputs/apk/debug/app-debug.apk`

## Metodika

`Engine.kt` — MEHNAT-ATTEST'dagi `App\Services\ErgonomicAssessmentService` (PHP) va
`desktop-app/engine.py` (Python) bilan bir xil formulalarga asoslangan:

- **NIOSH (1994) ko'tarish tenglamasi** — RWL (tavsiya etilgan og'irlik chegarasi) va
  ko'tarish indeksi (LI).
- **R2.2.2006-05 / SanQvaM 0069-24 uslubi** — og'irlik va psixofiziologik zo'riqish
  darajalari (1 / 2 / 3.1–3.4 / 4).
- **Inson omili xavf balli** — tajriba, o'qitilganlik, charchoq, sog'liq holati,
  oldingi hodisalar (0–100).
- **Ish muhiti omillari** — alyuminiy profil ishlab chiqarishga xos harorat, metall
  changi, shovqin.
- **0–100 integral xavfsizlik ko'rsatkichi** va avtomatik tavsiyalar.

## Testlar

`Engine.kt` uchun JUnit birlik testlari (`app/src/test/.../EngineTest.kt`) mavjud —
alyuminiy profil misoli veb va desktop versiyalar bilan bir xil natija berishini ham
tekshiradi. CI'da `gradle testDebugUnitTest` orqali ishga tushadi.

## Fayl tuzilishi

| Fayl | Vazifasi |
|------|----------|
| `app/src/main/java/.../Engine.kt` | Hisoblash mexanizmi (UI'dan mustaqil) |
| `app/src/main/java/.../FormSpec.kt` | Kirish formasi maydonlari tavsifi |
| `app/src/main/java/.../MainActivity.kt` | Kirish formasi (dasturiy ravishda quriladi) |
| `app/src/main/java/.../ReportActivity.kt` | Natija hisoboti ekrani |
| `app/src/main/java/.../HistoryActivity.kt` | Saqlangan baholashlar tarixi |
| `app/src/main/java/.../AssessmentDbHelper.kt` | Mahalliy SQLite saqlash qatlami |
| `app/src/test/java/.../EngineTest.kt` | `Engine.kt` uchun JUnit testlari |
