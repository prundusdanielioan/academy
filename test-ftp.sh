#!/bin/bash

echo "🔧 Testing FTP connection to s040.host-age.ro"
echo "=============================================="

# Test basic connectivity
echo "1. Testing server connectivity..."
if curl -s --connect-timeout 10 ftp://s040.host-age.ro/ > /dev/null 2>&1; then
    echo "✅ Server is reachable"
else
    echo "❌ Server is not reachable"
    exit 1
fi

# Test FTP connection (without credentials)
echo "2. Testing FTP service..."
if lftp -c "open s040.host-age.ro; quit" 2>/dev/null; then
    echo "✅ FTP service is running"
else
    echo "❌ FTP service is not responding"
    exit 1
fi

echo ""
echo "📋 Next steps:"
echo "1. Get your FTP credentials from cPanel (s040.host-age.ro:2083)"
echo "2. Go to FTP Accounts in cPanel"
echo "3. Note down:"
echo "   - Server: s040.host-age.ro"
echo "   - Username: [your_ftp_username]"
echo "   - Password: [your_ftp_password]"
echo "4. Test with: lftp s040.host-age.ro"
echo "5. Configure these in GitHub Secrets:"
echo "   - FTP_SERVER = s040.host-age.ro"
echo "   - FTP_USERNAME = [your_ftp_username]"
echo "   - FTP_PASSWORD = [your_ftp_password]"

