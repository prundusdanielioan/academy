<?php

require_once 'vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "🔧 Google OAuth Debug Test\n";
echo "========================\n\n";

// Check environment variables
echo "1. Environment Variables:\n";
echo "   APP_URL: " . ($_ENV['APP_URL'] ?? 'Not set') . "\n";
echo "   GOOGLE_CLIENT_ID: " . (isset($_ENV['GOOGLE_CLIENT_ID']) ? 'Set (' . strlen($_ENV['GOOGLE_CLIENT_ID']) . ' chars)' : 'Not set') . "\n";
echo "   GOOGLE_CLIENT_SECRET: " . (isset($_ENV['GOOGLE_CLIENT_SECRET']) ? 'Set (' . strlen($_ENV['GOOGLE_CLIENT_SECRET']) . ' chars)' : 'Not set') . "\n";
echo "   GOOGLE_REDIRECT_URI: " . ($_ENV['GOOGLE_REDIRECT_URI'] ?? 'Auto-generated') . "\n\n";

// Test config
echo "2. Laravel Config:\n";
$config = require 'config/services.php';
echo "   Config redirect: " . $config['google']['redirect'] . "\n\n";

// Test URLs
echo "3. Test URLs:\n";
echo "   Login page: http://localhost:8000/login\n";
echo "   Google OAuth: http://localhost:8000/auth/google\n";
echo "   Callback: http://localhost:8000/auth/google/callback\n\n";

// Check if Google Cloud Console is configured correctly
echo "4. Google Cloud Console Checklist:\n";
echo "   ✅ Authorized JavaScript origins should include: http://localhost:8000\n";
echo "   ✅ Authorized redirect URIs should include: http://localhost:8000/auth/google/callback\n";
echo "   ✅ OAuth consent screen should be configured\n";
echo "   ✅ Your email should be in test users (if in testing mode)\n\n";

// Common issues
echo "5. Common Issues & Solutions:\n";
echo "   ❌ 'malformed request' usually means:\n";
echo "      - Wrong redirect URI in Google Cloud Console\n";
echo "      - Missing OAuth consent screen configuration\n";
echo "      - App not in testing mode or user not in test users\n";
echo "      - Wrong Client ID or Client Secret\n\n";

echo "6. Next Steps:\n";
echo "   1. Go to https://console.cloud.google.com/\n";
echo "   2. Select your project\n";
echo "   3. APIs & Services > Credentials\n";
echo "   4. Edit your OAuth 2.0 Client ID\n";
echo "   5. Verify the URLs match exactly:\n";
echo "      - Authorized JavaScript origins: http://localhost:8000\n";
echo "      - Authorized redirect URIs: http://localhost:8000/auth/google/callback\n";
echo "   6. APIs & Services > OAuth consent screen\n";
echo "   7. Make sure it's configured and add your email to test users\n\n";

echo "🚀 Ready to test at: http://localhost:8000/login\n";
