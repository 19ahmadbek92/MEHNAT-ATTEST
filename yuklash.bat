@echo off
chcp 65001 >nul 2>&1
color 0f
echo ==============================================
echo   E-ATTESTATSIYA DEPLOY SKRIPTI
echo ==============================================
cd /d c:\Users\acer\Desktop\e-attestatsiya

echo.
echo [1/4] Fayllarni tayyorlash...
git add .
if %ERRORLEVEL% NEQ 0 (
    echo XATO: Git add bajarilmadi!
    pause
    exit /b 1
)

echo.
echo [2/4] O'zgarishlarni saqlash...
set MSG=Deploy: %date% %time:~0,5%
git commit -m "%MSG%"
if %ERRORLEVEL% NEQ 0 (
    echo MA'LUMOT: Yangi o'zgarishlar yo'q yoki commit xatosi.
)

echo.
echo [3/4] Asosiy tarmoqni belgilash...
git branch -M main

echo.
echo [4/4] GitHub'ga yuklash...
git push -f origin main
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo XATO: GitHub'ga yuklashda muammo chiqdi!
    echo Internet ulanishini va git sozlamalarini tekshiring.
    pause
    exit /b 1
)

echo.
echo ==============================================
echo   MUVAFFAQIYATLI YUKLANDI!
echo   Endi https://dashboard.render.com ga o'ting
echo   va "Manual Deploy" tugmasini bosing.
echo ==============================================
pause
