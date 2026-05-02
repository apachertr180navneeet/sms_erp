#!/bin/bash
# Deploy backend (Laravel API) to production server

set -e

echo "🚀 Deploying Backend..."

# Configuration
DEPLOY_DIR="/var/www/sms_erp/backend"
BRANCH="main"
REPO="git@github.com:your-org/sms_erp.git"

# Pull latest code
cd $DEPLOY_DIR
git pull origin $BRANCH

# Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache

# Clear caches
php artisan config:clear
php artisan cache:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Restart queue workers
php artisan queue:restart

echo "✅ Backend deployed successfully!"
