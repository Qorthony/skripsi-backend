#!/bin/bash

echo "🚀 Setting up Production Environment with Queue Worker..."

# Stop existing containers
echo "Stopping existing containers..."
docker-compose -f compose.prod.yaml down

# Build and start services
echo "Building and starting services..."
docker-compose -f compose.prod.yaml up -d --build

# Wait for services to be ready
echo "Waiting for services to be ready..."
sleep 30

# Check if queue worker is running
echo "Checking queue worker status..."
docker-compose -f compose.prod.yaml logs queue

echo "✅ Production setup complete!"
echo ""
echo "📋 Available services:"
echo "  - app:       Main Laravel application (http://localhost)"
echo "  - queue:     Queue worker for background jobs"
echo "  - scheduler: Laravel scheduler for cron jobs"
echo "  - db:        MySQL database"
echo ""
echo "🔧 Useful commands:"
echo "  docker-compose -f compose.prod.yaml logs queue     # Check queue logs"
echo "  docker-compose -f compose.prod.yaml exec app php artisan queue:test  # Test queue"
echo "  docker-compose -f compose.prod.yaml exec app php artisan queue:work  # Manual queue work"
echo "  docker-compose -f compose.prod.yaml restart queue  # Restart queue worker"
