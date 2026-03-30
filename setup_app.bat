@echo off

echo "generating application key"
php artisan key:generate
pause

echo "running db migration"
php artisan migrate
pause