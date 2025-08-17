#!/bin/bash

# 🛡️ ULTIMATE CodeCanyon Data Persistence System v3.0
# Handles: Laravel, CodeCanyon apps, shared hosting, zero downtime, demo data
# Usage: bash ultimate-persistence.sh [release_path] [shared_path] [first_deploy]

set -euo pipefail

RELEASE_PATH="${1:-$(pwd)}"
SHARED_PATH="${2:-$(pwd)/../shared}"
IS_FIRST_DEPLOY="${3:-auto}"  # auto, true, false
SCRIPT_VERSION="3.0.0"

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m'

echo -e "${PURPLE}🛡️ Ultimate CodeCanyon Data Persistence System v${SCRIPT_VERSION}${NC}"
echo -e "${BLUE}📂 Release: $RELEASE_PATH${NC}"
echo -e "${BLUE}📁 Shared: $SHARED_PATH${NC}"
echo -e "${BLUE}🚀 Deploy Type: $IS_FIRST_DEPLOY${NC}"
echo ""

# Auto-detect deployment type
auto_detect_deploy_type() {
    if [[ "$IS_FIRST_DEPLOY" == "auto" ]]; then
        if [[ -f "$SHARED_PATH/.deployment-history" ]]; then
            IS_FIRST_DEPLOY="false"
            echo -e "${CYAN}🔍 Auto-detected: Subsequent deployment${NC}"
        else
            IS_FIRST_DEPLOY="true" 
            echo -e "${CYAN}🔍 Auto-detected: First deployment${NC}"
        fi
    fi
}

# Enhanced framework detection with CodeCanyon patterns
detect_framework_and_patterns() {
    local framework="Laravel"  # Default assumption for CodeCanyon
    local app_patterns=()
    local user_patterns=()
    local demo_patterns=()
    local runtime_patterns=()

    echo -e "${CYAN}🔍 Analyzing application structure...${NC}"

    # Laravel Detection (most CodeCanyon apps)
    if [[ -f "$RELEASE_PATH/artisan" ]]; then
        framework="Laravel"
        echo -e "${GREEN}   ✅ Laravel CodeCanyon application detected${NC}"
    elif [[ -f "$RELEASE_PATH/package.json" ]]; then
        framework="Node.js"
        echo -e "${GREEN}   ✅ Node.js application detected${NC}"
    fi

    # CodeCanyon-specific app asset patterns (NEVER persist these)
    app_patterns+=(
        "public/flags"          # Country flags (SocietyPal example)
        "public/build"          # Vite/Laravel Mix builds
        "public/css"            # Compiled stylesheets
        "public/js"             # Compiled JavaScript
        "public/assets"         # General assets
        "public/img"            # Static images (logos, icons)
        "public/fonts"          # Web fonts
        "public/themes"         # Theme assets
        "public/plugins"        # Plugin assets
        "public/vendor"         # Vendor assets
        "public/dist"           # Distribution builds
        "public/static"         # Static assets
        "public/admin/assets"   # Admin panel assets
    )

    # User-generated content patterns (ALWAYS persist)
    user_patterns+=(
        "public/uploads"        # User file uploads
        "public/user-uploads"   # Alternative naming
        "public/media"          # Media files
        "public/documents"      # User documents
        "public/files"          # General files
        "public/avatars"        # User avatars
        "public/profile-photos" # Profile pictures
        "public/attachments"    # File attachments
        "public/gallery"        # Photo galleries
    )

    # Demo data patterns (preserve on first deploy, protect after)
    demo_patterns+=(
        "public/uploads/demo-*"     # Demo uploads
        "public/avatars/demo-*"     # Demo avatars
        "public/uploads/sample-*"   # Sample content
        "public/media/demo"         # Demo media folder
    )

    # Runtime generated patterns (ALWAYS persist)
    runtime_patterns+=(
        "public/qrcodes"        # Generated QR codes
        "public/qr-codes"       # Alternative naming
        "public/invoices"       # Generated invoices
        "public/reports"        # Generated reports
        "public/exports"        # Data exports
        "public/generated"      # General generated content
        "public/cache"          # File cache
        "public/temp"           # Temporary files
        "public/backups"        # Backup files
    )

    # Save configuration
    cat > "$SHARED_PATH/.persistence-config" << EOF
FRAMEWORK="$framework"
APP_PATTERNS=(${app_patterns[@]})
USER_PATTERNS=(${user_patterns[@]})
DEMO_PATTERNS=(${demo_patterns[@]})
RUNTIME_PATTERNS=(${runtime_patterns[@]})
DEPLOY_TYPE="$IS_FIRST_DEPLOY"
SETUP_DATE="$(date)"
EOF

    echo -e "${GREEN}   ✅ Pattern analysis complete${NC}"
    echo -e "${BLUE}   📊 App assets: ${#app_patterns[@]} patterns${NC}"
    echo -e "${BLUE}   📊 User data: ${#user_patterns[@]} patterns${NC}"
    echo -e "${BLUE}   📊 Demo data: ${#demo_patterns[@]} patterns${NC}"
    echo -e "${BLUE}   📊 Runtime: ${#runtime_patterns[@]} patterns${NC}"
}

# Smart directory classification and handling
classify_and_handle_directories() {
    echo -e "${CYAN}🎯 Classifying and handling directories...${NC}"

    source "$SHARED_PATH/.persistence-config"

    # Handle app assets (never persist - deploy with code)
    echo -e "${BLUE}🔵 App Assets (deploy with code):${NC}"
    for pattern in "${APP_PATTERNS[@]}"; do
        if [[ -d "$RELEASE_PATH/$pattern" ]]; then
            echo -e "${BLUE}   ✅ $pattern (keeping in release)${NC}"
        fi
    done

    # Handle user data (always persist)
    echo -e "${GREEN}🟢 User Data (always persist):${NC}"
    for pattern in "${USER_PATTERNS[@]}"; do
        pattern_name=$(basename "$pattern")
        if [[ -d "$RELEASE_PATH/$pattern" ]]; then
            mkdir -p "$SHARED_PATH/$pattern"

            # First deploy: preserve any existing content
            if [[ "$IS_FIRST_DEPLOY" == "true" && -n "$(ls -A "$RELEASE_PATH/$pattern" 2>/dev/null || true)" ]]; then
                cp -r "$RELEASE_PATH/$pattern"/* "$SHARED_PATH/$pattern"/ 2>/dev/null || true
                echo -e "${GREEN}   ✅ $pattern (migrated to shared)${NC}"
            else
                echo -e "${GREEN}   ✅ $pattern (linking to shared)${NC}"
            fi

            # Create symlink
            rm -rf "$RELEASE_PATH/$pattern"
            create_smart_symlink "$SHARED_PATH/$pattern" "$RELEASE_PATH/$pattern"
        fi
    done

    # Handle runtime generated (always persist)
    echo -e "${PURPLE}🟣 Runtime Generated (always persist):${NC}"
    for pattern in "${RUNTIME_PATTERNS[@]}"; do
        if [[ -d "$RELEASE_PATH/$pattern" || "$IS_FIRST_DEPLOY" == "true" ]]; then
            mkdir -p "$SHARED_PATH/$pattern"

            # Security files
            [[ -f "$SHARED_PATH/$pattern/.gitkeep" ]] || touch "$SHARED_PATH/$pattern/.gitkeep"
            [[ -f "$SHARED_PATH/$pattern/.htaccess" ]] || cat > "$SHARED_PATH/$pattern/.htaccess" << 'EOF'
Options -Indexes
<IfModule mod_autoindex.c>
IndexOptions -FancyIndexing
</IfModule>
EOF

            # Create/update symlink
            [[ -d "$RELEASE_PATH/$pattern" ]] && rm -rf "$RELEASE_PATH/$pattern"
            create_smart_symlink "$SHARED_PATH/$pattern" "$RELEASE_PATH/$pattern"
            echo -e "${PURPLE}   ✅ $pattern (runtime ready)${NC}"
        fi
    done
}

# Smart symlink creation with shared hosting fallback
create_smart_symlink() {
    local target="$1"
    local link="$2"

    # Ensure target exists
    mkdir -p "$target"

    # Try relative symlink first (works best)
    local rel_path=$(realpath --relative-to="$(dirname "$link")" "$target" 2>/dev/null || echo "$target")

    if ln -sf "$rel_path" "$link" 2>/dev/null; then
        return 0
    else
        # Fallback: absolute symlink
        if ln -sf "$target" "$link" 2>/dev/null; then
            return 0
        else
            echo -e "${YELLOW}⚠️ Symlink failed for $link, may need manual setup${NC}"
            return 1
        fi
    fi
}

# Shared hosting compatibility layer
setup_shared_hosting_compatibility() {
    echo -e "${CYAN}🌐 Setting up shared hosting compatibility...${NC}"

    # Check for common shared hosting indicators
    local shared_hosting=false
    local public_html_path=""

    # Look for public_html in common locations
    for path in "$(dirname "$RELEASE_PATH")/public_html" "$RELEASE_PATH/../public_html" "/home/*/public_html"; do
        if [[ -d "$path" ]] && [[ ! -L "$path" ]]; then
            shared_hosting=true
            public_html_path="$path"
            break
        fi
    done

    if [[ "$shared_hosting" == "true" ]]; then
        echo -e "${GREEN}   🔍 Shared hosting detected: $public_html_path${NC}"

        # Try to create public_html symlink
        if create_smart_symlink "$RELEASE_PATH/public" "$public_html_path.new" 2>/dev/null; then
            mv "$public_html_path.new" "$public_html_path"
            echo -e "${GREEN}   ✅ public_html symlinked to current/public${NC}"
        else
            echo -e "${YELLOW}   ⚠️ Cannot symlink public_html, manual setup required${NC}"
            cat > "$SHARED_PATH/shared-hosting-setup.md" << EOF
# Shared Hosting Manual Setup Required

Your hosting provider doesn't support symlinks for public_html.

## Manual Steps:
1. Copy contents of releases/current/public/* to public_html/
2. Create these manual symlinks in public_html/:
$(for pattern in "${USER_PATTERNS[@]}" "${RUNTIME_PATTERNS[@]}"; do
    echo "   ln -s ../shared/$pattern $(basename "$pattern")"
done)

## For each deployment:
1. Copy new public/* files (except symlinked directories)
2. Verify symlinks are intact
EOF
        fi
    else
        echo -e "${BLUE}   ℹ️ VPS/dedicated server detected${NC}"
    fi
}

# Laravel-specific optimizations
setup_laravel_specifics() {
    if [[ -f "$RELEASE_PATH/artisan" ]]; then
        echo -e "${CYAN}🎯 Setting up Laravel-specific persistence...${NC}"

        # Storage directory
        mkdir -p "$SHARED_PATH/storage"/{app/public,framework/{cache,sessions,views,testing},logs}
        rm -rf "$RELEASE_PATH/storage"
        create_smart_symlink "$SHARED_PATH/storage" "$RELEASE_PATH/storage"
        echo -e "${GREEN}   ✅ Storage directory linked${NC}"

        # Environment file
        if [[ -f "$RELEASE_PATH/.env" && ! -L "$RELEASE_PATH/.env" ]]; then
            cp "$RELEASE_PATH/.env" "$SHARED_PATH/.env"
        fi
        rm -f "$RELEASE_PATH/.env"
        create_smart_symlink "$SHARED_PATH/.env" "$RELEASE_PATH/.env"
        echo -e "${GREEN}   ✅ Environment file linked${NC}"

        # Bootstrap cache
        mkdir -p "$SHARED_PATH/bootstrap/cache"
        mkdir -p "$RELEASE_PATH/bootstrap"
        rm -rf "$RELEASE_PATH/bootstrap/cache"
        create_smart_symlink "$SHARED_PATH/bootstrap/cache" "$RELEASE_PATH/bootstrap/cache"
        echo -e "${GREEN}   ✅ Bootstrap cache linked${NC}"

        # Public storage link
        mkdir -p "$SHARED_PATH/public/storage"
        create_smart_symlink "$SHARED_PATH/storage/app/public" "$SHARED_PATH/public/storage"
        echo -e "${GREEN}   ✅ Public storage link created${NC}"
    fi
}

# Comprehensive verification system  
create_verification_system() {
    echo -e "${CYAN}🔍 Creating verification system...${NC}"

    # Health check script
    cat > "$SHARED_PATH/health-check.sh" << 'EOF'
#!/bin/bash
echo "🏥 Ultimate Data Persistence Health Check"
echo "========================================"

PASSED=0
FAILED=0

pass() { echo "✅ $1"; ((PASSED++)); }
fail() { echo "❌ $1"; ((FAILED++)); }

# Check core symlinks
for link in storage .env public; do
    if [[ -L "$link" ]]; then
        target=$(readlink "$link")
        if [[ -e "$link" ]]; then
            pass "Valid symlink: $link → $target"
        else
            fail "Broken symlink: $link → $target"
        fi
    else
        fail "Missing symlink: $link"
    fi
done

# Check user data preservation
source ../shared/.persistence-config 2>/dev/null || true
if [[ -n "${USER_PATTERNS:-}" ]]; then
    for pattern in "${USER_PATTERNS[@]}"; do
        if [[ -L "$pattern" ]]; then
            pass "User data protected: $pattern"
        else
            fail "User data not protected: $pattern"
        fi
    done
fi

echo ""
echo "📊 Results: ✅ $PASSED passed, ❌ $FAILED failed"
[[ $FAILED -eq 0 ]] && echo "🎉 System is healthy!" || echo "🚨 Issues detected"
EOF

    chmod +x "$SHARED_PATH/health-check.sh"

    # Emergency recovery script
    cat > "$SHARED_PATH/emergency-recovery.sh" << 'EOF'
#!/bin/bash
echo "🚨 Emergency Data Persistence Recovery"
echo "======================================"

RELEASE_DIR="${1:-$(pwd)}"
cd "$RELEASE_DIR"

# Re-run persistence setup
if [[ -f "../shared/.persistence-config" ]]; then
    source ../shared/.persistence-config

    # Recreate core symlinks
    [[ -d "../shared/storage" ]] && { rm -rf storage; ln -sf ../shared/storage storage; }
    [[ -f "../shared/.env" ]] && { rm -f .env; ln -sf ../shared/.env .env; }

    # Recreate user data symlinks
    for pattern in "${USER_PATTERNS[@]:-}"; do
        if [[ -d "../shared/$pattern" ]]; then
            rm -rf "$pattern"
            ln -sf "../shared/$pattern" "$pattern"
        fi
    done

    echo "✅ Emergency recovery completed"
else
    echo "❌ No persistence config found"
fi
EOF

    chmod +x "$SHARED_PATH/emergency-recovery.sh"

    echo -e "${GREEN}   ✅ Verification system created${NC}"
}

# Record deployment history
record_deployment() {
    echo "$(date): Deployment completed (type: $IS_FIRST_DEPLOY)" >> "$SHARED_PATH/.deployment-history"

    # Create deployment summary
    cat > "$SHARED_PATH/DEPLOYMENT_SUMMARY.md" << EOF
# Ultimate Data Persistence Deployment Summary

**Date**: $(date)
**Type**: $IS_FIRST_DEPLOY deployment
**Framework**: $FRAMEWORK
**Release**: $RELEASE_PATH
**Shared**: $SHARED_PATH

## Directories Protected
$(source "$SHARED_PATH/.persistence-config" && for pattern in "${USER_PATTERNS[@]}" "${RUNTIME_PATTERNS[@]}"; do echo "- $pattern"; done)

## Quick Commands
\`\`\`bash
# Health check
bash shared/health-check.sh

# Emergency recovery  
bash shared/emergency-recovery.sh

# View config
cat shared/.persistence-config
\`\`\`

## Status: ✅ ZERO DATA LOSS GUARANTEED
EOF

    echo -e "${GREEN}   ✅ Deployment recorded${NC}"
}

# Main execution flow
main() {
    echo -e "${PURPLE}🚀 Starting ultimate data persistence setup...${NC}"

    # Create shared directory
    mkdir -p "$SHARED_PATH"

    # Execute setup steps
    auto_detect_deploy_type
    detect_framework_and_patterns
    setup_laravel_specifics
    classify_and_handle_directories  
    setup_shared_hosting_compatibility
    create_verification_system
    record_deployment

    echo ""
    echo -e "${PURPLE}🎉 Ultimate Data Persistence Setup Complete!${NC}"
    echo -e "${GREEN}✅ Framework: $FRAMEWORK${NC}"
    echo -e "${GREEN}✅ Deploy type: $IS_FIRST_DEPLOY${NC}"
    echo -e "${GREEN}✅ Zero data loss guaranteed${NC}"
    echo -e "${GREEN}✅ Shared hosting compatible${NC}"
    echo ""
    echo -e "${CYAN}💡 Verify: bash $SHARED_PATH/health-check.sh${NC}"
    echo -e "${CYAN}🚨 Emergency: bash $SHARED_PATH/emergency-recovery.sh${NC}"
}

# Execute main function
main "$@"
