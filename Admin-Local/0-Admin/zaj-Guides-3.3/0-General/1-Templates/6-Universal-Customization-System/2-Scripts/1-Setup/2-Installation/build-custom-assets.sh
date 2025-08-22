#!/bin/bash

# Step 17 - Build Custom Assets Script
# Universal Laravel project custom asset builder
# Author: Auto-generated Step 17 Enhancement
# Usage: bash build-custom-assets.sh [production|development]

set -euo pipefail

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Default build mode
BUILD_MODE="${1:-production}"

# Logging function
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

success() {
    echo -e "${GREEN}✅ $1${NC}"
}

warning() {
    echo -e "${YELLOW}⚠️ $1${NC}"
}

error() {
    echo -e "${RED}❌ $1${NC}"
    exit 1
}

# Function to verify prerequisites
verify_prerequisites() {
    log "Verifying prerequisites..."
    
    # Check if we're in Laravel project root
    if [ ! -f "artisan" ]; then
        error "Not in Laravel project root (artisan file not found)"
    fi
    
    # Check if webpack.custom.cjs exists
    if [ ! -f "webpack.custom.cjs" ]; then
        error "webpack.custom.cjs not found. Run customization setup first."
    fi
    
    # Check if package.json has custom scripts
    if ! grep -q "custom:build" package.json; then
        error "Custom build scripts not found in package.json. Run customization setup first."
    fi
    
    # Check if custom source files exist
    if [ ! -d "resources/Custom" ]; then
        error "Custom resources directory not found. Run customization setup first."
    fi
    
    success "Prerequisites verified"
}

# Function to clean previous builds
clean_custom_assets() {
    log "Cleaning previous custom builds..."
    
    if [ -d "public/Custom/css" ]; then
        rm -f public/Custom/css/*
        log "Cleaned CSS assets"
    fi
    
    if [ -d "public/Custom/js" ]; then
        rm -f public/Custom/js/*
        log "Cleaned JS assets"
    fi
    
    success "Previous builds cleaned"
}

# Function to build assets
build_assets() {
    local mode="$1"
    
    log "Building custom assets in $mode mode..."
    
    case "$mode" in
        "production")
            if npm run custom:build; then
                success "Production build completed"
            else
                error "Production build failed"
            fi
            ;;
        "development")
            if npm run custom:dev; then
                success "Development build completed"
            else
                error "Development build failed"
            fi
            ;;
        *)
            error "Invalid build mode: $mode. Use 'production' or 'development'"
            ;;
    esac
}

# Function to verify build output
verify_build_output() {
    log "Verifying build output..."
    
    local issues=0
    
    # Check CSS output
    if [ ! -f "public/Custom/css/app.css" ]; then
        warning "CSS output file not found: public/Custom/css/app.css"
        ((issues++))
    else
        local css_size=$(stat -f%z "public/Custom/css/app.css" 2>/dev/null || stat -c%s "public/Custom/css/app.css" 2>/dev/null || echo "0")
        if [ "$css_size" -gt 0 ]; then
            success "CSS built successfully (${css_size} bytes)"
        else
            warning "CSS file is empty"
            ((issues++))
        fi
    fi
    
    # Check JS output
    if [ ! -f "public/Custom/js/app.js" ]; then
        warning "JS output file not found: public/Custom/js/app.js"
        ((issues++))
    else
        local js_size=$(stat -f%z "public/Custom/js/app.js" 2>/dev/null || stat -c%s "public/Custom/js/app.js" 2>/dev/null || echo "0")
        if [ "$js_size" -gt 0 ]; then
            success "JS built successfully (${js_size} bytes)"
        else
            warning "JS file is empty"
            ((issues++))
        fi
    fi
    
    if [ $issues -eq 0 ]; then
        success "Build verification passed"
    else
        warning "$issues issue(s) found in build output"
    fi
    
    return $issues
}

# Function to display build summary
display_summary() {
    echo ""
    echo "================================================"
    log "📊 Build Summary"
    echo "================================================"
    
    # Show file sizes
    if [ -f "public/Custom/css/app.css" ]; then
        local css_size=$(stat -f%z "public/Custom/css/app.css" 2>/dev/null || stat -c%s "public/Custom/css/app.css" 2>/dev/null || echo "0")
        echo "CSS: $(basename "public/Custom/css/app.css") (${css_size} bytes)"
    fi
    
    if [ -f "public/Custom/js/app.js" ]; then
        local js_size=$(stat -f%z "public/Custom/js/app.js" 2>/dev/null || stat -c%s "public/Custom/js/app.js" 2>/dev/null || echo "0")
        echo "JS:  $(basename "public/Custom/js/app.js") (${js_size} bytes)"
    fi
    
    echo ""
    echo "Build mode: $BUILD_MODE"
    echo "Build time: $(date)"
}

# Main execution
main() {
    log "🎨 Starting Step 17 Custom Asset Build"
    echo "================================================"
    echo "Build mode: $BUILD_MODE"
    echo "================================================"
    
    # Verify prerequisites
    verify_prerequisites
    
    # Clean previous builds
    clean_custom_assets
    
    # Build assets
    build_assets "$BUILD_MODE"
    
    # Verify build output
    verify_build_output
    
    # Display summary
    display_summary
    
    echo "================================================"
    success "✨ Custom asset build completed successfully!"
    echo ""
    
    # Show next steps
    case "$BUILD_MODE" in
        "production")
            log "Next steps:"
            echo "  1. Test the application: php artisan serve"
            echo "  2. Verify custom assets are loaded in browser"
            echo "  3. Run verification: bash verify-step17-complete.sh"
            ;;
        "development")
            log "Development build completed. For file watching, run: npm run custom:watch"
            ;;
    esac
}

# Execute main function
main "$@"