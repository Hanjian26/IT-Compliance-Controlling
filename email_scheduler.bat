@REM @echo off
@REM cd C:\xampp\htdocs\PRODEPT\prodept-app
@REM php artisan schedule:run >> C:\xampp\htdocs\PRODEPT\prodept-app\storage\logs\scheduler.log 2>&1
@REM exit


@echo off
cd /d C:\xampp\htdocs\PRODEPT\prodept-app
C:\xampp\php\php.exe artisan schedule:run >> storage\logs\scheduler.log 2>&1
exit
