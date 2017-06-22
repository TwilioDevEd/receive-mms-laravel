set -e;

ngrok http $PORT &
php artisan serve --host 0.0.0.0 --port $PORT
