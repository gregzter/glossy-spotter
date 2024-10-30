composer create-project laravel/laravel tmp && mv tmp/* tmp/.* . && rmdir tmp
chown -R www-data:www-data /var/www/storage
chmod -R 775 /var/www/storage
exit
php -m | grep redis
php artisan config:clear
php artisan cache:clear
exit
