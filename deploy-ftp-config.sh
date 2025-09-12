#!/bin/bash

echo "🚀 FTP Deployment Script with Config File"
echo "=========================================="
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Not in Laravel project directory"
    exit 1
fi

echo "✅ Laravel project detected"
echo ""

# Check if config file exists
CONFIG_FILE="ftp-config.txt"
if [ ! -f "$CONFIG_FILE" ]; then
    echo "📝 Creating FTP configuration file..."
    echo "Please edit $CONFIG_FILE with your FTP credentials:"
    echo ""
    cat > $CONFIG_FILE << 'EOF'
# FTP Configuration
FTP_SERVER=s040.host-age.ro
FTP_USERNAME=your_username_here
FTP_PASSWORD=your_password_here
REMOTE_DIR=/public_html/self-learn.ro/
EOF
    echo "✅ Created $CONFIG_FILE"
    echo ""
    echo "📋 Please edit $CONFIG_FILE with your actual credentials:"
    echo "   - FTP_SERVER: s040.host-age.ro"
    echo "   - FTP_USERNAME: your FTP username"
    echo "   - FTP_PASSWORD: your FTP password"
    echo "   - REMOTE_DIR: /public_html/self-learn.ro/"
    echo ""
    echo "Then run this script again."
    exit 0
fi

# Load configuration
echo "📖 Loading FTP configuration from $CONFIG_FILE..."
source $CONFIG_FILE

if [ -z "$FTP_SERVER" ] || [ -z "$FTP_USERNAME" ] || [ -z "$FTP_PASSWORD" ]; then
    echo "❌ Please configure all FTP credentials in $CONFIG_FILE"
    exit 1
fi

echo "🔧 Configuration loaded:"
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
    echo "Please check your credentials in $CONFIG_FILE"
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

echo "Uploading files (this may take a few minutes)..."
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
echo "You'll need to access cPanel Terminal or File Manager to run:"
echo "- php artisan migrate --force"
echo "- php artisan config:cache"
echo "- php artisan route:cache"
echo "- php artisan view:cache"
echo ""
echo "✅ Deployment script completed!"
