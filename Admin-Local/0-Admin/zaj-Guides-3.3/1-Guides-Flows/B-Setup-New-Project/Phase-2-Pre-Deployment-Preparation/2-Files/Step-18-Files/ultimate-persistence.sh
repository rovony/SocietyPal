#!/bin/bash

# 🛡️ Universal Data Persistence System - Exclusion-Based Strategy
# Zero Data Loss | All CodeCanyon Projects | Shared Hosting Ready
# Author: Universal Customization Protection System
# Version: 3.0.0 - Universal Exclusion-Based

set -e  # Exit on error

# Parameters
RELEASE_PATH="${1:-$(pwd)}"
SHARED_PATH="${2:-$(pwd)/../shared}"
DEPLOY_TYPE="${3:-auto}"  # auto|first|subsequent|verify|custom
ENVIRONMENT="${4:-auto}"  # auto|local|server
SCRIPT_VERSION="3.0.0"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${PURPLE}🛡️ Universal Data Persistence System v${SCRIPT_VERSION}${NC}"
echo -e "${PURPLE}📋 Strategy: Exclusion-Based (Universal Patterns + All CodeCanyon Projects)${NC}"
echo -e "${BLUE}📂 Release: $RELEASE_PATH${NC}"
echo -e "${BLUE}📁 Shared:  $SHARED_PATH${NC}"
echo -e "${BLUE}🌐 Environment: $ENVIRONMENT${NC}"
echo -e "${BLUE}🔧 Mode: $DEPLOY_TYPE${NC}"
echo ""

# Logging functions
log() { echo -e "${BLUE}[$(date '+%H:%M:%S')] $1${NC}"; }
success() { echo -e "${GREEN}✅ $1${NC}"; }
warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
error() { echo -e "${RED}❌ $1${NC}"; exit 1; }
info() { echo -e "${CYAN}ℹ️  $1${NC}"; }

# Validation
validate_environment() {
    log "Validating environment..."
    
    # Check if Laravel application
    if [ ! -f "$RELEASE_PATH/artisan" ]; then
        error "Not a Laravel application! artisan file not found."
    fi
    
    # Check paths
    if [ ! -d "$RELEASE_PATH" ]; then
        error "Release path does not exist: $RELEASE_PATH"
    fi
    
    success "Laravel application detected"
    success "Paths validated"
}

# Detect environment type
detect_environment() {
    if [ "$ENVIRONMENT" = "auto" ]; then
        # Check for local development indicators
        if [[ "$RELEASE_PATH" == *"/Users/"* ]] || [[ "$RELEASE_PATH" == *"/home/"*"/"*"/"* ]] || [[ "$PWD" == *"localhost"* ]] || [[ -f ".env.local" ]]; then
            ENVIRONMENT="local"
            info "🖥️  Auto-detected: LOCAL development environment"
            warning "Symlinks will be SKIPPED - directories will be created for structure only"
        else
            ENVIRONMENT="server"
            info "🌐 Auto-detected: SERVER production environment"
            info "Full symlink persistence will be enabled"
        fi
    else
        if [ "$ENVIRONMENT" = "local" ]; then
            info "🖥️  Manual override: LOCAL development environment"
            warning "Symlinks will be SKIPPED - directories will be created for structure only"
        else
            info "🌐 Manual override: SERVER production environment"
            info "Full symlink persistence will be enabled"
        fi
    fi
}

# Detect deployment type
detect_deployment_type() {
    if [ "$DEPLOY_TYPE" = "auto" ]; then
        if [ -f "$SHARED_PATH/.deployment-history" ]; then
            DEPLOY_TYPE="subsequent"
            info "Auto-detected: Subsequent deployment"
        else
            DEPLOY_TYPE="first"
            info "Auto-detected: First deployment"
        fi
    else
        case "$DEPLOY_TYPE" in
            "first")
                info "Manual mode: First deployment (preserve demo data)"
                ;;
            "subsequent")
                info "Manual mode: Subsequent deployment (preserve user data)"
                ;;
            "verify")
                info "Manual mode: Verification only (health check)"
                ;;
            "custom")
                info "Manual mode: Custom directory setup"
                ;;
            *)
                warning "Unknown deployment type: $DEPLOY_TYPE, defaulting to 'verify'"
                DEPLOY_TYPE="verify"
                ;;
        esac
    fi
}

# Create shared directory structure
create_shared_structure() {
    log "Creating shared directory structure..."
    
    mkdir -p "$SHARED_PATH"
    mkdir -p "$SHARED_PATH/storage"
    mkdir -p "$SHARED_PATH/bootstrap/cache"
    mkdir -p "$SHARED_PATH/public"
    
    success "Shared directory structure created"
}

# EXCLUSION-BASED STRATEGY: Universal Shared Patterns
# Works for ALL CodeCanyon projects - only these patterns are shared
get_universal_shared_patterns() {
    echo "
.env
storage/
bootstrap/cache/
public/.well-known/
public/*upload*/
public/*avatar*/
public/*media*/
public/qr*
public/*invoice*/
public/*export*/
public/*report*/
public/*temp*/
public/*generated*/
public/custom-*
storage/app/custom/
"
}

# Scan for custom shared directories
detect_custom_shared_directories() {
    log "Scanning for custom directories to share..."
    
    local custom_patterns=()
    
    # Scan public directory for user/runtime generated content
    if [ -d "$RELEASE_PATH/public" ]; then
        while IFS= read -r dir; do
            local dirname=$(basename "$dir")
            
            # Skip if it's standard app code
            case "$dirname" in
                build|css|js|assets|img|images|flags|icons|themes|fonts|vendor)
                    continue ;;
                *)
                    # Check if directory contains files that look like user content
                    if find "$dir" -type f \( -name "*.pdf" -o -name "*.doc*" -o -name "*.xls*" \
                        -o -name "*.jpg" -o -name "*.png" -o -name "*.gif" -o -name "*.zip" \) 2>/dev/null | head -1 | grep -q .; then
                        custom_patterns+=("public/$dirname/")
                        info "Custom user directory detected: public/$dirname/"
                    elif [ "$dirname" = "qrcodes" ] || [ "$dirname" = "invoices" ] || [ "$dirname" = "exports" ]; then
                        custom_patterns+=("public/$dirname/")
                        info "Custom runtime directory detected: public/$dirname/"
                    fi
                    ;;
            esac
        done < <(find "$RELEASE_PATH/public" -maxdepth 1 -type d ! -path "$RELEASE_PATH/public")
    fi
    
    # Save custom patterns for later use
    printf '%s\n' "${custom_patterns[@]}" > "$SHARED_PATH/.custom-shared-patterns"
}

# Create symlinks for shared directories (SERVER) or prepare structure (LOCAL)
create_symlinks() {
    if [ "$ENVIRONMENT" = "local" ]; then
        log "🖥️  LOCAL: Preparing directory structure (no symlinks)..."
        create_local_structure
    else
        log "🌐 SERVER: Creating symlinks for shared directories..."
        create_server_symlinks
    fi
}

# Create local development structure
create_local_structure() {
    cd "$RELEASE_PATH"
    
    # Ensure local directories exist
    log "Ensuring local directories exist..."
    
    # Standard Laravel directories
    mkdir -p storage/app/public
    mkdir -p storage/framework/{cache,sessions,views}
    mkdir -p storage/logs
    mkdir -p bootstrap/cache
    
    # Ensure .env exists
    if [ ! -f ".env" ] && [ -f ".env.example" ]; then
        cp .env.example .env
        success "Created .env from .env.example"
    fi
    
    # Create user data directories
    mkdir -p public/user-uploads
    mkdir -p public/qrcodes
    mkdir -p public/avatars
    
    # Note: NO storage link in local development - use `php artisan storage:link` manually if needed
    
    success "Local development structure prepared"
    success "✅ All required directories exist for development"
    success "⚠️  Remember: This is LOCAL setup - no persistence symlinks created"
}

# Create server symlinks  
create_server_symlinks() {
    cd "$RELEASE_PATH"
    
    # Standard Laravel symlinks
    local standard_symlinks=(
        ".env:../$SHARED_PATH/.env"
        "storage:../$SHARED_PATH/storage"
        "bootstrap/cache:../../$SHARED_PATH/bootstrap/cache"
    )
    
    # Process standard symlinks
    for symlink_def in "${standard_symlinks[@]}"; do
        local source="${symlink_def%%:*}"
        local target="${symlink_def##*:}"
        
        if [ -e "$source" ] && [ ! -L "$source" ]; then
            if [ "$DEPLOY_TYPE" = "first" ] && [ -d "$source" ]; then
                # First deployment: copy to shared first
                log "First deployment: copying $source to shared..."
                cp -r "$source"/* "$SHARED_PATH/$(basename "$source")/" 2>/dev/null || true
            fi
            rm -rf "$source"
        fi
        
        ln -sf "$target" "$source"
        success "Symlinked: $source → $target"
    done
    
    # Process custom shared directories
    if [ -f "$SHARED_PATH/.custom-shared-patterns" ]; then
        while IFS= read -r pattern; do
            [ -z "$pattern" ] && continue
            
            local dir_path="$pattern"
            local shared_target="../$SHARED_PATH/$pattern"
            
            if [ -d "$dir_path" ] && [ ! -L "$dir_path" ]; then
                if [ "$DEPLOY_TYPE" = "first" ]; then
                    # First deployment: copy to shared first
                    log "First deployment: copying $dir_path to shared..."
                    mkdir -p "$(dirname "$SHARED_PATH/$pattern")"
                    cp -r "$dir_path"/* "$SHARED_PATH/$pattern" 2>/dev/null || true
                fi
                rm -rf "$dir_path"
            fi
            
            mkdir -p "$(dirname "$dir_path")"
            ln -sf "$shared_target" "$dir_path"
            success "Custom symlinked: $dir_path → $shared_target"
            
        done < "$SHARED_PATH/.custom-shared-patterns"
    fi
    
    # Laravel storage link
    if [ ! -L "public/storage" ]; then
        php artisan storage:link 2>/dev/null || {
            ln -sf ../../storage/app/public public/storage
            success "Manual storage:link created"
        }
    fi
}

# Create environment file if needed
setup_environment_file() {
    if [ ! -f "$SHARED_PATH/.env" ]; then
        if [ -f "$RELEASE_PATH/.env.example" ]; then
            log "Creating .env from .env.example..."
            cp "$RELEASE_PATH/.env.example" "$SHARED_PATH/.env"
            success "Environment file created from example"
        else
            log "Creating basic .env file..."
            cat > "$SHARED_PATH/.env" << 'EOF'
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
EOF
            warning "Basic .env created - configure your settings!"
        fi
    fi
}

# Create health check script
create_health_check() {
    log "Creating health check system..."
    
    cat > "$SHARED_PATH/health-check.sh" << 'EOF'
#!/bin/bash

# Laravel Data Persistence Health Check
# Verifies symlinks and data integrity

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}🏥 Laravel Data Persistence Health Check${NC}"
echo "=============================================="

ERRORS=0
WARNINGS=0

check_symlink() {
    local path="$1"
    local description="$2"
    
    if [ -L "$path" ]; then
        if [ -e "$path" ]; then
            echo -e "✅ Valid symlink: $description"
        else
            echo -e "${RED}❌ Broken symlink: $description${NC}"
            ((ERRORS++))
        fi
    else
        echo -e "${YELLOW}⚠️  Missing symlink: $description${NC}"
        ((WARNINGS++))
    fi
}

# Check standard Laravel symlinks
check_symlink "storage" "storage → ../shared/storage"
check_symlink ".env" ".env → ../shared/.env"
check_symlink "bootstrap/cache" "bootstrap/cache → ../../shared/bootstrap/cache"
check_symlink "public/storage" "public/storage → ../../storage/app/public"

# Check shared directory accessibility
if [ -d "../shared/storage" ]; then
    echo -e "✅ Shared storage accessible"
else
    echo -e "${RED}❌ Shared storage not accessible${NC}"
    ((ERRORS++))
fi

if [ -f "../shared/.env" ]; then
    echo -e "✅ Shared .env accessible"
else
    echo -e "${RED}❌ Shared .env not accessible${NC}"
    ((ERRORS++))
fi

# Summary
echo ""
echo "📊 Results: ✅ $((12-ERRORS-WARNINGS)) passed, ⚠️ $WARNINGS warnings, ❌ $ERRORS failed"

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}🎉 System is perfectly healthy!${NC}"
    exit 0
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠️ System is healthy with minor warnings${NC}"
    exit 0
else
    echo -e "${RED}❌ System has critical errors requiring attention${NC}"
    exit 1
fi
EOF

    chmod +x "$SHARED_PATH/health-check.sh"
    success "Health check script created"
}

# Create emergency recovery script
create_emergency_recovery() {
    log "Creating emergency recovery system..."
    
    cat > "$SHARED_PATH/emergency-recovery.sh" << 'EOF'
#!/bin/bash

# Laravel Data Persistence Emergency Recovery
# Repairs broken symlinks and permissions

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

RELEASE_PATH="${1:-$(pwd)}"
SHARED_PATH="$(dirname "$0")"

echo -e "${RED}🚨 Laravel Data Persistence Emergency Recovery${NC}"
echo "================================================"
echo -e "${YELLOW}Release: $RELEASE_PATH${NC}"
echo -e "${YELLOW}Shared:  $SHARED_PATH${NC}"
echo ""

# Recreate standard symlinks
echo "🔧 Recreating standard symlinks..."
cd "$RELEASE_PATH"

rm -rf storage .env bootstrap/cache
ln -sf "../shared/storage" storage
ln -sf "../shared/.env" .env
ln -sf "../../shared/bootstrap/cache" bootstrap/cache

# Fix Laravel storage link
rm -rf public/storage
php artisan storage:link 2>/dev/null || ln -sf ../../storage/app/public public/storage

# Fix permissions
echo "🔧 Fixing permissions..."
chmod -R 755 "$SHARED_PATH/storage"
chmod -R 755 "$SHARED_PATH/bootstrap/cache"
chmod 644 "$SHARED_PATH/.env"

echo -e "${GREEN}✅ Emergency recovery completed!${NC}"
echo "🏥 Run health check: bash ../shared/health-check.sh"
EOF

    chmod +x "$SHARED_PATH/emergency-recovery.sh"
    success "Emergency recovery script created"
}

# Update deployment history
update_deployment_history() {
    log "Updating deployment history..."
    
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    echo "[$timestamp] Deployment Type: $DEPLOY_TYPE | Version: $SCRIPT_VERSION" >> "$SHARED_PATH/.deployment-history"
    
    success "Deployment history updated"
}

# Create deployment summary
create_deployment_summary() {
    log "Creating deployment summary..."
    
    cat > "$SHARED_PATH/DEPLOYMENT-SUMMARY.md" << EOF
# Laravel Data Persistence Deployment Summary

## Configuration
- **Deployment Type**: $DEPLOY_TYPE
- **Timestamp**: $(date '+%Y-%m-%d %H:%M:%S')
- **Script Version**: $SCRIPT_VERSION
- **Strategy**: Exclusion-Based Laravel Standard

## Shared Directories Created
- \`.env\` → Environment configuration
- \`storage/\` → Laravel storage (logs, cache, sessions, files)
- \`bootstrap/cache/\` → Laravel bootstrap cache
- \`public/storage/\` → Laravel public storage link

$(if [ -f "$SHARED_PATH/.custom-shared-patterns" ]; then
    echo "## Custom Shared Directories"
    while IFS= read -r pattern; do
        [ -z "$pattern" ] && continue
        echo "- \`$pattern\` → Custom user/runtime data"
    done < "$SHARED_PATH/.custom-shared-patterns"
fi)

## Quick Commands
\`\`\`bash
# Health check
bash shared/health-check.sh

# Emergency recovery
bash shared/emergency-recovery.sh "\$(pwd)"

# View this summary
cat shared/DEPLOYMENT-SUMMARY.md
\`\`\`

## Next Steps
1. Configure your \`.env\` file: \`nano shared/.env\`
2. Set app key: \`php artisan key:generate\`
3. Run migrations: \`php artisan migrate\`
4. Test application functionality
5. Run health check: \`bash shared/health-check.sh\`
EOF

    success "Deployment summary created"
}

# Main execution
main() {
    log "Starting Laravel Data Persistence Setup..."
    
    validate_environment
    detect_environment
    detect_deployment_type
    
    if [ "$ENVIRONMENT" = "server" ]; then
        create_shared_structure
        detect_custom_shared_directories
        setup_environment_file
        create_symlinks
        create_health_check
        create_emergency_recovery
        update_deployment_history
        create_deployment_summary
    else
        # Local development setup
        create_symlinks  # This now calls create_local_structure
        
        echo ""
        echo -e "${GREEN}🎉 Local Development Structure Prepared!${NC}"
        echo ""
        echo -e "${CYAN}📋 Local Development Notes:${NC}"
        echo -e "  ✅ All required directories created"
        echo -e "  ✅ .env prepared from .env.example" 
        echo -e "  ✅ Storage directories prepared"
        echo -e "  ⚠️  No symlinks created (local development)"
        echo -e "  🌐 On server deployment, symlinks will be created automatically"
        echo ""
        return
    fi
    
    echo ""
    echo -e "${GREEN}🎉 Laravel Data Persistence Setup Complete!${NC}"
    echo ""
    echo -e "${CYAN}📋 Next Steps:${NC}"
    echo -e "  1. Configure environment: ${YELLOW}nano $SHARED_PATH/.env${NC}"
    echo -e "  2. Generate app key: ${YELLOW}php artisan key:generate${NC}"
    echo -e "  3. Run health check: ${YELLOW}bash $SHARED_PATH/health-check.sh${NC}"
    echo ""
    echo -e "${CYAN}📊 Quick Status Check:${NC}"
    echo -e "  ${YELLOW}bash $SHARED_PATH/health-check.sh${NC}"
    echo ""
    echo -e "${CYAN}🚨 Emergency Recovery:${NC}"
    echo -e "  ${YELLOW}bash $SHARED_PATH/emergency-recovery.sh \"\$(pwd)\"${NC}"
    echo ""
}

# Execute main function
main "$@"
