#!/bin/bash

echo "🚀 Interactive FTP Deployment Script"
echo "===================================="
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Not in Laravel project directory"
    exit 1
fi

echo "✅ Laravel project detected"
echo ""

# Get FTP credentials
echo "📋 Please provide your FTP credentials:"
echo ""

read -p "FTP Server (default: s040.host-age.ro): " FTP_SERVER
FTP_SERVER=${FTP_SERVER:-s040.host-age.ro}

read -p "FTP Username: " FTP_USERNAME
if [ -z "$FTP_USERNAME" ]; then
    echo "❌ FTP Username is required"
    exit 1
fi

read -s -p "FTP Password: " FTP_PASSWORD
echo ""
if [ -z "$FTP_PASSWORD" ]; then
    echo "❌ FTP Password is required"
    exit 1
fi

read -p "Remote directory (default: /public_html/self-learn.ro/): " REMOTE_DIR
REMOTE_DIR=${REMOTE_DIR:-/public_html/self-learn.ro/}

echo ""
echo "🔧 Configuration:"
echo "Server: $FTP_SERVER"
echo "Username: $FTP_USERNAME"
echo "Remote Directory: $REMOTE_DIR"
echo ""

read -p "Continue with deployment? (y/N): " CONFIRM
if [[ ! $CONFIRM =~ ^[Yy]$ ]]; then
    echo "❌ Deployment cancelled"
    exit 1
fi

echo ""
echo "🚀 Starting deployment process..."
echo ""

# Step 1: Prepare files
echo "📦 Step 1: Preparing files for deployment..."

# Create temporary deployment directory
DEPLOY_DIR="deploy-temp"
rm -rf $DEPLOY_DIR
mkdir -p $DEPLOY_DIR

# Copy necessary files
echo "Copying application files..."
cp -r app $DEPLOY_DIR/
cp -r bootstrap $DEPLOY_DIR/
cp -r config $DEPLOY_DIR/
cp -r database $DEPLOY_DIR/
cp -r public $DEPLOY_DIR/
cp -r resources $DEPLOY_DIR/
cp -r routes $DEPLOY_DIR/
cp -r storage $DEPLOY_DIR/
cp -r vendor $DEPLOY_DIR/
cp artisan $DEPLOY_DIR/
cp composer.json $DEPLOY_DIR/
cp composer.lock $DEPLOY_DIR/
cp .env.production $DEPLOY_DIR/.env

echo "✅ Files prepared in $DEPLOY_DIR"
echo ""

# Step 2: Test FTP connection
echo "🔌 Step 2: Testing FTP connection..."
if lftp -c "open $FTP_SERVER; user $FTP_USERNAME $FTP_PASSWORD; ls; quit" 2>/dev/null; then
    echo "✅ FTP connection successful"
else
    echo "❌ FTP connection failed"
    echo "Please check your credentials and try again"
    rm -rf $DEPLOY_DIR
    exit 1
fi

# Step 3: Upload files
echo ""
echo "📤 Step 3: Uploading files to server..."

# Create lftp script for upload
cat > lftp_upload.txt << EOF
open $FTP_SERVER
user $FTP_USERNAME $FTP_PASSWORD
cd $REMOTE_DIR
lcd $DEPLOY_DIR
mirror -R --delete --verbose --exclude-glob .git* --exclude-glob node_modules --exclude-glob tests --exclude-glob *.md --exclude-glob *.sh --exclude-glob *.mp4
quit
EOF

if lftp -f lftp_upload.txt; then
    echo "✅ Files uploaded successfully"
else
    echo "❌ Upload failed"
    rm -rf $DEPLOY_DIR
    rm lftp_upload.txt
    exit 1
fi

# Clean up
rm lftp_upload.txt
rm -rf $DEPLOY_DIR

echo ""
echo "🎉 Deployment completed successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Check your website: https://self-learn.ro"
echo "2. Verify that the new design is working"
echo "3. Test all functionality"
echo ""
echo "🔧 If you need to run Laravel commands on the server:"
echo "You'll need SSH access or cPanel Terminal to run:"
echo "- php artisan migrate --force"
echo "- php artisan config:cache"
echo "- php artisan route:cache"
echo "- php artisan view:cache"
echo ""
echo "✅ Deployment script completed!"
