#!/bin/sh

# Wait for MySQL to be ready
until nc -z db 3306; do
  echo "Waiting for MySQL..."
  sleep 2
done

# Run database migrations
php artisan migrate --force 

# Wait for all processes to complete
wait
