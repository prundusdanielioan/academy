#!/bin/bash

echo "🚀 SSH Deployment Script"
echo "========================"
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Not in Laravel project directory"
    exit 1
fi

echo "✅ Laravel project detected"
echo ""

# Get SSH credentials
echo "📋 Please provide your SSH credentials:"
echo ""

read -p "SSH Host (default: s040.host-age.ro): " SSH_HOST
SSH_HOST=${SSH_HOST:-s040.host-age.ro}

read -p "SSH Username: " SSH_USERNAME
if [ -z "$SSH_USERNAME" ]; then
    echo "❌ SSH Username is required"
    exit 1
fi

read -p "Remote directory (default: /home/$SSH_USERNAME/public_html/self-learn.ro/): " REMOTE_DIR
REMOTE_DIR=${REMOTE_DIR:-/home/$SSH_USERNAME/public_html/self-learn.ro/}

echo ""
echo "🔧 Configuration:"
echo "Host: $SSH_HOST"
echo "Username: $SSH_USERNAME"
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

# Step 2: Test SSH connection
echo "🔌 Step 2: Testing SSH connection..."
if ssh -o ConnectTimeout=10 -o BatchMode=yes $SSH_USERNAME@$SSH_HOST "echo 'SSH connection successful'" 2>/dev/null; then
    echo "✅ SSH connection successful"
else
    echo "❌ SSH connection failed"
    echo "Please check your SSH key or credentials"
    rm -rf $DEPLOY_DIR
    exit 1
fi

# Step 3: Upload files
echo ""
echo "📤 Step 3: Uploading files to server..."

if rsync -avz --delete --exclude='.git*' --exclude='node_modules' --exclude='tests' --exclude='*.md' --exclude='*.sh' --exclude='*.mp4' $DEPLOY_DIR/ $SSH_USERNAME@$SSH_HOST:$REMOTE_DIR/; then
    echo "✅ Files uploaded successfully"
else
    echo "❌ Upload failed"
    rm -rf $DEPLOY_DIR
    exit 1
fi

# Step 4: Run Laravel commands on server
echo ""
echo "⚙️  Step 4: Running Laravel commands on server..."

ssh $SSH_USERNAME@$SSH_HOST "cd $REMOTE_DIR && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && chmod -R 755 storage bootstrap/cache"

if [ $? -eq 0 ]; then
    echo "✅ Laravel commands executed successfully"
else
    echo "⚠️  Some Laravel commands may have failed"
fi

# Clean up
rm -rf $DEPLOY_DIR

echo ""
echo "🎉 Deployment completed successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Check your website: https://self-learn.ro"
echo "2. Verify that the new design is working"
echo "3. Test all functionality"
echo ""
echo "✅ Deployment script completed!"
