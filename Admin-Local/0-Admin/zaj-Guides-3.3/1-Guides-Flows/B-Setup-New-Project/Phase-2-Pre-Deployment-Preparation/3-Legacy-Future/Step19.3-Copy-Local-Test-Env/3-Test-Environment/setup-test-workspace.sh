#!/bin/bash

echo "=== Setting up Test Workspace ==="

# Create test workspace directory
mkdir -p test-workspace
cd test-workspace

# Create minimal Laravel-like structure
echo "Creating minimal Laravel structure..."
mkdir -p bootstrap/cache
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p public
mkdir -p resources
mkdir -p app

# Create a minimal composer.json for testing
echo "Creating test composer.json..."
cat > composer.json << 'EOF'
{
    "name": "test/laravel-app",
    "type": "project",
    "description": "Test Laravel application for build pipeline testing",
    "require": {
        "php": "^8.1",
        "laravel/framework": "^10.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist"
    }
}
EOF

# Create a minimal package.json for testing
echo "Creating test package.json..."
cat > package.json << 'EOF'
{
    "name": "laravel-test-app",
    "version": "1.0.0",
    "description": "Test Laravel application for build pipeline testing",
    "scripts": {
        "dev": "echo 'Development build completed'",
        "build": "echo 'Production build completed' && mkdir -p public/build && echo 'Build output created' > public/build/app.js",
        "production": "echo 'Production build completed' && mkdir -p public/dist && echo 'Build output created' > public/dist/app.js",
        "prod": "echo 'Production build completed' && mkdir -p public/assets && echo 'Build output created' > public/assets/app.js"
    },
    "devDependencies": {
        "test-package": "^1.0.0"
    }
EOF

# Create a minimal package-lock.json
echo "Creating test package-lock.json..."
cat > package-lock.json << 'EOF'
{
    "name": "laravel-test-app",
    "version": "1.0.0",
    "lockfileVersion": 2,
    "requires": true,
    "packages": {
        "": {
            "name": "laravel-test-app",
            "version": "1.0.0"
        }
    }
}
EOF

# Create a simple index.php for testing
echo "Creating test index.php..."
cat > public/index.php << 'EOF'
<?php
echo "Laravel Test Application - Build Pipeline Test Environment";
echo "<br>Current time: " . date('Y-m-d H:i:s');
echo "<br>PHP version: " . PHP_VERSION;
EOF

# Create .gitkeep files
find . -type d -empty -exec touch {}/.gitkeep \;

echo "✅ Test workspace created successfully!"
echo "📁 Location: $(pwd)"
echo "🚀 Ready for testing build commands!"
