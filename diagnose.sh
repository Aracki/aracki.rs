#!/bin/bash
# Run this script on your server to diagnose the photo loading issue
# Usage: bash diagnose.sh

echo "=== Nginx Photo Loading Diagnostics ==="
echo ""

DOCROOT="/var/www/site/aracki.rs/public_html"

echo "1. Checking if foto directory exists..."
if [ -d "$DOCROOT/foto" ]; then
    echo "   ✓ $DOCROOT/foto EXISTS"
else
    echo "   ✗ $DOCROOT/foto NOT FOUND - This is the problem!"
    echo "   Run: ls -la $DOCROOT/"
    exit 1
fi

echo ""
echo "2. Checking foto subdirectories..."
for dir in logotipi prospekti kalendari bilbordi 3d knjige ilustracije; do
    if [ -d "$DOCROOT/foto/$dir/thumb" ]; then
        count=$(ls -1 "$DOCROOT/foto/$dir/thumb" 2>/dev/null | wc -l)
        echo "   ✓ foto/$dir/thumb - $count files"
    else
        echo "   ✗ foto/$dir/thumb NOT FOUND"
    fi
done

echo ""
echo "3. Checking permissions..."
echo "   foto/ directory:"
ls -la "$DOCROOT/foto/" | head -5

echo ""
echo "4. Checking Nginx user can read files..."
NGINX_USER=$(ps aux | grep "nginx: worker" | grep -v grep | head -1 | awk '{print $1}')
echo "   Nginx runs as: $NGINX_USER"

echo ""
echo "5. Testing a sample image file..."
SAMPLE=$(find "$DOCROOT/foto" -name "*.jpg" -type f | head -1)
if [ -n "$SAMPLE" ]; then
    echo "   Sample file: $SAMPLE"
    echo "   File permissions: $(ls -la "$SAMPLE" | awk '{print $1, $3, $4}')"
    if [ -r "$SAMPLE" ]; then
        echo "   ✓ File is readable"
    else
        echo "   ✗ File is NOT readable - Fix with: chmod -R 755 $DOCROOT/foto"
    fi
else
    echo "   ✗ No .jpg files found in foto directory!"
fi

echo ""
echo "6. Testing HTTP access with curl..."
curl -I http://localhost/foto/logotipi/thumb/01.jpg 2>/dev/null | head -3

echo ""
echo "=== Done ==="

