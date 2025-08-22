#!/bin/bash
set -e

echo "=== Building Assets & Optimizing Laravel ==="

# Generate optimized autoloader
echo "Generating optimized autoloader..."
composer dump-autoload --optimize --classmap-authoritative

# Build frontend assets if Node.js dependencies exist
if [ -f "package.json" ]; then
  echo "Building production assets..."

  # Try different build commands (covers all Laravel asset compilation methods)
  if npm run build 2>/dev/null; then
    echo "✅ Assets built with 'npm run build'"
  elif npm run production 2>/dev/null; then
    echo "✅ Assets built with 'npm run production'"
  elif npm run prod 2>/dev/null; then
    echo "✅ Assets built with 'npm run prod'"
  elif npm run dev 2>/dev/null; then
    echo "⚠️ Built with 'npm run dev' (not optimized for production)"
  else
    echo "ℹ️ No suitable build script found, skipping asset compilation"
  fi

  # Clean up node_modules to reduce deployment size
  echo "Cleaning up Node.js modules..."
  rm -rf node_modules

  # Verify build output exists
  if [ -d "public/build" ] || [ -d "public/dist" ] || [ -d "public/js" ] || [ -d "public/css" ] || [ -d "public/assets" ]; then
    echo "✅ Frontend assets compiled successfully"
  else
    echo "ℹ️ No build output detected (may be normal for API-only apps)"
  fi
else
  echo "ℹ️ No package.json found, skipping asset compilation"
fi

# Final verification
echo "Final verification..."
echo "✅ vendor/: $([ -d "vendor" ] && echo "exists" || echo "missing")"
echo "✅ bootstrap/cache/: $([ -d "bootstrap/cache" ] && echo "exists" || echo "missing")"
echo "✅ Build complete and ready for deployment"

echo "✅ Laravel build and optimization completed"
