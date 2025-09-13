<?php
/**
 * Google OAuth Configuration Test Script
 * 
 * Run this script to test your Google OAuth configuration
 * Usage: php test-google-oauth.php
 */

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "🔍 Google OAuth Configuration Test\n";
echo "==================================\n\n";

// Check required environment variables
$required_vars = [
    'GOOGLE_CLIENT_ID',
    'GOOGLE_CLIENT_SECRET', 
    'APP_URL'
];

// GOOGLE_REDIRECT_URI is optional - it's auto-generated from APP_URL

echo "📋 Checking Environment Variables:\n";
$all_configured = true;

foreach ($required_vars as $var) {
    $value = $_ENV[$var] ?? null;
    if ($value && $value !== "your_google_client_id_here" && $value !== "your_google_client_secret_here") {
        echo "✅ $var: " . (strlen($value) > 20 ? substr($value, 0, 20) . "..." : $value) . "\n";
    } else {
        echo "❌ $var: Not configured or using placeholder\n";
        $all_configured = false;
    }
}

echo "\n";

if (!$all_configured) {
    echo "⚠️  Configuration Issues Found:\n";
    echo "1. Update your .env file with real Google OAuth credentials\n";
    echo "2. Follow the instructions in GOOGLE_OAUTH_SETUP_INSTRUCTIONS.md\n";
    echo "3. Make sure APP_URL matches your local development URL\n\n";
} else {
    echo "✅ All environment variables are configured!\n\n";
}

// Test URL generation
echo "🔗 Testing URL Generation:\n";
$app_url = $_ENV['APP_URL'] ?? 'Not set';
$redirect_uri = $_ENV['GOOGLE_REDIRECT_URI'] ?? null;

echo "APP_URL: $app_url\n";

if ($app_url !== 'Not set') {
    $expected_callback = rtrim($app_url, '/') . '/auth/google/callback';
    
    if ($redirect_uri) {
        echo "GOOGLE_REDIRECT_URI: $redirect_uri (manual)\n";
        if ($redirect_uri === $expected_callback) {
            echo "✅ Manual redirect URI matches expected callback URL\n";
        } else {
            echo "⚠️  Manual redirect URI doesn't match expected callback URL\n";
            echo "   Expected: $expected_callback\n";
            echo "   Current:  $redirect_uri\n";
        }
    } else {
        echo "GOOGLE_REDIRECT_URI: Auto-generated from APP_URL\n";
        echo "✅ Expected callback URL: $expected_callback\n";
    }
}

echo "\n";

// Test Google OAuth URL
if ($all_configured) {
    echo "🌐 Google OAuth URLs:\n";
    
    $callback_url = $_ENV['GOOGLE_REDIRECT_URI'] ?? (rtrim($_ENV['APP_URL'], '/') . '/auth/google/callback');
    
    $google_auth_url = "https://accounts.google.com/o/oauth2/auth?" . http_build_query([
        'client_id' => $_ENV['GOOGLE_CLIENT_ID'],
        'redirect_uri' => $callback_url,
        'scope' => 'openid profile email',
        'response_type' => 'code',
        'state' => 'test_state'
    ]);
    
    echo "Login URL: " . rtrim($_ENV['APP_URL'], '/') . "/auth/google\n";
    echo "Callback URL: " . $callback_url . "\n";
    echo "Google Auth URL: " . substr($google_auth_url, 0, 100) . "...\n";
}

echo "\n📝 Next Steps:\n";
echo "1. If configuration is complete, test at: " . ($_ENV['APP_URL'] ?? 'http://localhost:8000') . "/login\n";
echo "2. Click 'Sign in with Google' button\n";
echo "3. Check browser console for any JavaScript errors\n";
echo "4. Check Laravel logs: storage/logs/laravel.log\n\n";

echo "🔧 Troubleshooting:\n";
echo "- Make sure Google Cloud Console has the correct redirect URI\n";
echo "- Check that Google+ API is enabled in Google Cloud Console\n";
echo "- Verify OAuth consent screen is configured\n";
echo "- Check Laravel logs for detailed error messages\n";
