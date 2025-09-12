#!/bin/bash

echo "🚀 Testing Local Deployment Process"
echo "==================================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Not in Laravel project directory"
    exit 1
fi

echo "✅ Laravel project detected"

# Step 1: Install dependencies
echo ""
echo "📦 Step 1: Installing dependencies..."
if composer install --no-dev --optimize-autoloader --no-interaction; then
    echo "✅ Dependencies installed successfully"
else
    echo "❌ Failed to install dependencies"
    exit 1
fi

# Step 2: Build assets
echo ""
echo "🎨 Step 2: Building assets..."
if npm ci && npm run build; then
    echo "✅ Assets built successfully"
else
    echo "⚠️  Assets build failed (might not have Vite configured)"
fi

# Step 3: Prepare production environment
echo ""
echo "⚙️  Step 3: Preparing production environment..."
cp .env.example .env.production
sed -i '' 's/APP_ENV=local/APP_ENV=production/' .env.production
sed -i '' 's/APP_DEBUG=true/APP_DEBUG=false/' .env.production
sed -i '' 's|APP_URL=http://lms.local:8000|APP_URL=https://self-learn.ro|' .env.production
sed -i '' 's|ASSET_URL=http://lms.local:8000|ASSET_URL=https://self-learn.ro|' .env.production
sed -i '' 's/FORCE_HTTPS=false/FORCE_HTTPS=true/' .env.production

echo "✅ Production .env created"

# Step 4: Generate application key
echo ""
echo "🔑 Step 4: Generating application key..."
if php artisan key:generate --env=production; then
    echo "✅ Application key generated"
else
    echo "❌ Failed to generate application key"
    exit 1
fi

# Step 5: Test database connection
echo ""
echo "🗄️  Step 5: Testing database connection..."
if php artisan migrate:status --env=production; then
    echo "✅ Database connection successful"
else
    echo "⚠️  Database connection failed (might need configuration)"
fi

# Step 6: Cache configuration
echo ""
echo "💾 Step 6: Caching configuration..."
if php artisan config:cache --env=production; then
    echo "✅ Configuration cached"
else
    echo "❌ Failed to cache configuration"
    exit 1
fi

if php artisan route:cache --env=production; then
    echo "✅ Routes cached"
else
    echo "❌ Failed to cache routes"
fi

if php artisan view:cache --env=production; then
    echo "✅ Views cached"
else
    echo "❌ Failed to cache views"
fi

# Step 7: Check file structure
echo ""
echo "📁 Step 7: Checking deployment structure..."
echo "Files to be deployed:"
ls -la | grep -E "(app|bootstrap|config|database|public|resources|routes|storage|vendor|artisan|composer\.json|composer\.lock|\.env\.production)"

echo ""
echo "🎉 Local deployment test completed!"
echo ""
echo "📋 Next steps:"
echo "1. Get FTP credentials from cPanel"
echo "2. Configure GitHub Secrets"
echo "3. Run deployment workflow"
echo ""
echo "🔧 To test FTP connection:"
echo "lftp s040.host-age.ro"
echo "user [username] [password]"
echo "ls"
echo "quit"

