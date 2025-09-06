#!/bin/bash

# Switch to local development configuration
echo "Switching to local development configuration..."

# Update .env for local development
sed -i '' 's|APP_URL=.*|APP_URL=http://lms.local:8000|' .env
sed -i '' 's|ASSET_URL=.*|ASSET_URL=http://lms.local:8000|' .env
sed -i '' 's|FORCE_HTTPS=.*|FORCE_HTTPS=false|' .env

echo "✅ Configuration updated for local development:"
echo "   APP_URL=http://lms.local:8000"
echo "   ASSET_URL=http://lms.local:8000"
echo "   FORCE_HTTPS=false"
echo ""
echo "Now run: php artisan config:clear && php artisan cache:clear"
