composer -o dumpautoload
composer dump-autoload
php artisan dump-autoload

echo "Dump autoload. \n";
php artisan migrate:refresh
echo "Dump autoload. \n";
php app/cron/ewms_cron_dump.php
echo "Refreshed database. \n";
#php artisan migrate --package="lucadegasperi/oauth2-server-laravel"
#echo "Migrated Oauth tables. \n";
#php artisan db:seed --class=OauthclientSeeder
#php artisan db:seed --class=OauthscopeSeeder
echo "Seeded oauth tables. \n";