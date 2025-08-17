#!/bin/bash

# 🛡️ Simple Data Persistence - Laravel Projects
# LOCAL: Directory setup only | SERVER: Full symlink persistence
# Version: 4.0.0 - Simplified

set -e

# Simple parameter handling
PROJECT_PATH="${1:-$(pwd)}"
MODE="${2:-local}"  # local or server
SCRIPT_VERSION="4.0.0"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m'

echo -e "${PURPLE}🛡️ Simple Data Persistence v${SCRIPT_VERSION}${NC}"
echo -e "${BLUE}📂 Project: $PROJECT_PATH${NC}"
echo -e "${BLUE}🔧 Mode: $MODE${NC}"
echo ""

# Validation
if [ ! -f "$PROJECT_PATH/artisan" ]; then
    echo -e "❌ Not a Laravel application! artisan file not found."
    exit 1
fi

cd "$PROJECT_PATH"

if [ "$MODE" = "local" ]; then
    echo -e "${CYAN}🖥️  Setting up LOCAL development structure...${NC}"
    
    # Create required directories
    mkdir -p storage/app/public
    mkdir -p storage/framework/{cache,sessions,views}
    mkdir -p storage/logs
    mkdir -p bootstrap/cache
    mkdir -p public/user-uploads
    mkdir -p public/qrcodes
    mkdir -p public/avatars
    
    # Ensure .env exists
    if [ ! -f ".env" ] && [ -f ".env.example" ]; then
        cp .env.example .env
        echo -e "${GREEN}✅ Created .env from .env.example${NC}"
    fi
    
    echo -e "${GREEN}✅ Local development structure ready${NC}"
    echo -e "${YELLOW}⚠️  No symlinks created (local development)${NC}"
    echo -e "${CYAN}💡 Use 'php artisan storage:link' manually if needed${NC}"
    
elif [ "$MODE" = "server" ]; then
    echo -e "${CYAN}🌐 Setting up SERVER production symlinks...${NC}"
    
    SHARED_PATH="../shared"
    mkdir -p "$SHARED_PATH"/{storage/app/public,storage/framework/{cache,sessions,views},storage/logs,bootstrap/cache}
    mkdir -p "$SHARED_PATH"/public/{user-uploads,qrcodes,avatars}
    
    # Copy existing data on first deployment
    [ -d "storage" ] && [ ! -L "storage" ] && cp -r storage/* "$SHARED_PATH/storage/" 2>/dev/null
    [ -f ".env" ] && [ ! -L ".env" ] && cp .env "$SHARED_PATH/.env" 2>/dev/null
    [ -d "bootstrap/cache" ] && [ ! -L "bootstrap/cache" ] && cp -r bootstrap/cache/* "$SHARED_PATH/bootstrap/cache/" 2>/dev/null
    
    # Create symlinks
    rm -rf storage .env bootstrap/cache
    ln -sf "$SHARED_PATH/storage" storage
    ln -sf "$SHARED_PATH/.env" .env
    ln -sf "../../$SHARED_PATH/bootstrap/cache" bootstrap/cache
    
    # Laravel storage link
    rm -rf public/storage
    php artisan storage:link 2>/dev/null || ln -sf ../../storage/app/public public/storage
    
    echo -e "${GREEN}✅ Server symlinks created${NC}"
    echo -e "${GREEN}✅ Laravel storage linked${NC}"
    
else
    echo -e "❌ Invalid mode: $MODE (use 'local' or 'server')"
    exit 1
fi

echo ""
echo -e "${PURPLE}🎉 Data Persistence Setup Complete!${NC}"
