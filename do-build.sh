#!/bin/bash

# Exit on any error
set -e

# Print each command before executing (helps with debugging)
set -x

# Set Node options to prevent memory issues during build
export NODE_OPTIONS="--max_old_space_size=4096"

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Cache Laravel configuration and routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear any previous build artifacts
rm -rf public/build

# Install Node dependencies using ci for more reliable builds
npm ci

# Build the frontend assets
npm run build

# Ensure proper permissions
chmod -R 755 storage bootstrap/cache