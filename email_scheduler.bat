@echo off
cd C:\xampp\htdocs\PRODEPT\prodept-app
php artisan schedule:run >> C:\xampp\htdocs\PRODEPT\prodept-app\storage\logs\scheduler.log 2>&1
exit
