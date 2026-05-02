#!/bin/bash
# Deploy entire stack using Docker

set -e

echo "🚀 Deploying with Docker..."

# Copy environment files
cp .env.example .env

# Build and start containers
docker-compose up -d --build

# Run migrations
docker-compose exec api php artisan migrate --force

# Seed database (first time only)
# docker-compose exec api php artisan db:seed --class=RolePermissionSeeder
# docker-compose exec api php artisan db:seed --class=PlanSeeder

echo "✅ Docker deployment complete!"
echo "📌 API: http://localhost:8000"
echo "📌 Web: http://localhost"
