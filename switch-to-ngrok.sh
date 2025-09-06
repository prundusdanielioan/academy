#!/bin/bash

# Switch to ngrok configuration
echo "Switching to ngrok configuration..."

# Update .env for ngrok
sed -i '' 's|APP_URL=.*|APP_URL=https://e59aa1f4d88b.ngrok-free.app|' .env
sed -i '' 's|ASSET_URL=.*|ASSET_URL=https://e59aa1f4d88b.ngrok-free.app|' .env
sed -i '' 's|FORCE_HTTPS=.*|FORCE_HTTPS=true|' .env

echo "✅ Configuration updated for ngrok:"
echo "   APP_URL=https://e59aa1f4d88b.ngrok-free.app"
echo "   ASSET_URL=https://e59aa1f4d88b.ngrok-free.app"
echo "   FORCE_HTTPS=true"
echo ""
echo "Now run: php artisan config:clear && php artisan cache:clear"
