#!/bin/sh

# Wait for MySQL to be ready
until nc -z db 3306; do
  echo "Waiting for MySQL..."
  sleep 2
done

# Run database migrations
php artisan migrate --force &

# run queue worker
php artisan queue:listen &

# Start Octane with FrankenPHP
php artisan octane:frankenphp --workers=1 --max-requests=1 &

# Start Vite Dev Server
npm run dev &

# Wait for all processes to complete
wait
