#!/bin/bash

# Script pentru a schimba de la lms.local la localhost pentru Google OAuth

echo "🔄 Switching to localhost for Google OAuth compatibility..."

# Backup current .env
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Update APP_URL to localhost
sed -i '' 's|APP_URL=http://lms.local:8000|APP_URL=http://localhost:8000|g' .env

# Remove GOOGLE_REDIRECT_URI to use auto-generation
sed -i '' '/^GOOGLE_REDIRECT_URI=/d' .env

echo "✅ Updated .env:"
echo "   APP_URL=http://localhost:8000"
echo "   GOOGLE_REDIRECT_URI will be auto-generated"
echo ""
echo "📝 Next steps:"
echo "1. Update Google Cloud Console:"
echo "   - Authorized JavaScript origins: http://localhost:8000"
echo "   - Authorized redirect URIs: http://localhost:8000/auth/google/callback"
echo "2. Test at: http://localhost:8000/login"
echo ""
echo "💾 Backup created: .env.backup.$(date +%Y%m%d_%H%M%S)"
