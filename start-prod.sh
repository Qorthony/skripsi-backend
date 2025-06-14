#!/bin/sh

# Wait for MySQL to be ready
until nc -z db 3306; do
  echo "Waiting for MySQL..."
  sleep 2
done

# Create necessary directories and fix permissions
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

php artisan storage:link

# Run database migrations
php artisan migrate --force 

# Start Octane with proper config for Docker
# Clear any existing PID files
rm -f storage/logs/octane.pid

# Start Octane with proper config for Docker and no daemon mode
php artisan octane:frankenphp --host=0.0.0.0 --port=8000 --admin-port=2019 --max-requests=1000 --workers=auto
# Wait for all processes to complete
wait
