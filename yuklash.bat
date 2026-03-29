@echo off
color 0f
echo ==============================================
echo GITHUBGA JONATISH DASTURI ISHGA TUSHTI
echo ==============================================
cd c:\Users\acer\Desktop\e-attestatsiya

echo.
echo 1/3: Fayllarni tayyorlash...
git add .

echo.
echo 2/3: O'zgarishlarni xotiraga olish...
git commit -m "Fix Dockerfile for Render NEW"

echo.
echo 3/3: Asosiy tarmoqni belgilash...
git branch -M main

echo.
echo 4/4: Internetga YUKLASH (Majburiy)...
echo DIQQAT BILAN KUZATING, XATO CHIQMAYDIMI?
git push -f origin main

echo.
echo ==============================================
echo AGAR YUQORIDA 100% VA "master -> main" YOKI "O.K" SO'ZLARI CHIQGAN BO'LSA
echo Barchasi muvaffaqiyatli ketdi. 
echo Rasmda tushgani kabi Render ga o'tib yana "Manual Deploy" qiling!
echo ==============================================
pause
