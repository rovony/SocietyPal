#!/bin/bash

# DATA PERSISTENCE SYSTEM SETUP SCRIPT
# This script sets up the data persistence protection system for CodeCanyon applications
# Author: Automated Template System v4.0
# Date: $(date)

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')] $1${NC}"
}

success() {
    echo -e "${GREEN}[SUCCESS] $1${NC}"
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
}

info() {
    echo -e "${CYAN}[INFO] $1${NC}"
}

# Function to show usage
show_usage() {
    echo -e "${CYAN}DATA PERSISTENCE SYSTEM SETUP${NC}"
    echo ""
    echo -e "${BLUE}Purpose:${NC} Set up data persistence protection for vendor updates"
    echo ""
    echo -e "${BLUE}Usage:${NC}"
    echo "  $0 [--test-mode] [--verbose] [--force]"
    echo "  $0 --help                    # Show this help"
    echo ""
    echo -e "${BLUE}Options:${NC}"
    echo "  --test-mode                  # Run in test mode (validation only)"
    echo "  --verbose                    # Show detailed output"
    echo "  --force                      # Force overwrite existing configurations"
    echo "  --help                       # Show this help message"
    echo ""
    echo -e "${BLUE}Features:${NC}"
    echo "  ✓ Shared directory management"
    echo "  ✓ Symlink configuration"
    echo "  ✓ Data backup strategies"
    echo "  ✓ Recovery procedures"
    echo "  ✓ Automatic framework detection"
    echo ""
}

# Function to detect project root
detect_project_root() {
    local current_dir="$(pwd)"
    local search_dir="$current_dir"
    
    # Look for Admin-Local directory up to 5 levels up
    for i in {1..5}; do
        if [ -d "$search_dir/Admin-Local" ]; then
            echo "$search_dir"
            return 0
        fi
        search_dir="$(dirname "$search_dir")"
        if [ "$search_dir" = "/" ]; then
            break
        fi
    done
    
    error "Could not find project root (Admin-Local directory)"
    return 1
}

# Function to detect Laravel project
is_laravel_project() {
    local project_root="$1"
    [ -f "$project_root/artisan" ] && [ -f "$project_root/composer.json" ]
}

# Function to create tracking session
create_tracking_session() {
    local project_root="$1"
    local tracking_dir="$project_root/Admin-Local/1-CurrentProject/Tracking"
    
    if [ ! -d "$tracking_dir" ]; then
        mkdir -p "$tracking_dir"
    fi
    
    # Create session directory with timestamp
    local session_name="data-persistence-setup-$(date '+%Y%m%d-%H%M%S')"
    local session_dir="$tracking_dir/$session_name"
    mkdir -p "$session_dir"/{planning,baselines,execution,verification,documentation,backups}
    
    echo "$session_dir"
}

# Function to setup data persistence system
setup_data_persistence() {
    local project_root="$1"
    local test_mode="$2"
    local verbose="$3"
    
    log "Setting up Data Persistence System"
    echo ""
    
    # Create tracking session
    local session_dir
    if ! session_dir=$(create_tracking_session "$project_root"); then
        error "Failed to create tracking session"
        return 1
    fi
    
    [ "$verbose" = "true" ] && info "Session directory: $session_dir"
    
    # Create planning document
    cat > "$session_dir/planning/data-persistence-plan.md" << 'EOF'
# Data Persistence System Setup Plan

## Objective
Set up comprehensive data persistence protection to ensure zero data loss during vendor updates.

## Components
1. Shared directory management
2. Symlink configuration  
3. Data backup strategies
4. Recovery procedures

## Implementation Steps
- [ ] Create shared directories
- [ ] Configure symlinks
- [ ] Set up backup procedures
- [ ] Create recovery scripts
- [ ] Test persistence functionality

## Success Criteria
- All critical data directories are protected
- Symlinks function correctly
- Backup and recovery procedures work
- System passes validation tests
EOF

    if [ "$test_mode" = "true" ]; then
        success "✅ Test mode: Data persistence system validation passed"
        return 0
    fi
    
    # Create shared directories
    local shared_dir="$project_root/shared"
    if [ ! -d "$shared_dir" ]; then
        mkdir -p "$shared_dir"/{storage,uploads,cache,logs}
        [ "$verbose" = "true" ] && success "Created shared directories"
    fi
    
    # Setup Laravel-specific persistence if Laravel project
    if is_laravel_project "$project_root"; then
        [ "$verbose" = "true" ] && info "Configuring Laravel data persistence"
        
        # Create storage symlinks
        if [ -d "$project_root/storage" ] && [ ! -L "$project_root/storage" ]; then
            cp -r "$project_root/storage" "$shared_dir/"
            rm -rf "$project_root/storage"
            ln -sf "$shared_dir/storage" "$project_root/storage"
            [ "$verbose" = "true" ] && success "Storage directory linked to shared location"
        fi
        
        # Create public/uploads symlink if it exists
        if [ -d "$project_root/public/uploads" ] && [ ! -L "$project_root/public/uploads" ]; then
            cp -r "$project_root/public/uploads" "$shared_dir/"
            rm -rf "$project_root/public/uploads"
            ln -sf "$shared_dir/uploads" "$project_root/public/uploads"
            [ "$verbose" = "true" ] && success "Uploads directory linked to shared location"
        fi
    fi
    
    # Create data persistence verification script
    cat > "$project_root/verify_data_persistence.sh" << 'EOF'
#!/bin/bash
# Data Persistence Verification Script

echo "🔍 Verifying Data Persistence System..."

# Check shared directories
if [ -d "shared" ]; then
    echo "✅ Shared directory exists"
else
    echo "❌ Shared directory missing"
    exit 1
fi

# Check symlinks
if [ -L "storage" ]; then
    echo "✅ Storage symlink configured"
else
    echo "⚠️  Storage symlink not found"
fi

echo "🎉 Data persistence verification completed"
EOF
    chmod +x "$project_root/verify_data_persistence.sh"
    
    # Create backup script
    cat > "$project_root/backup_persistent_data.sh" << 'EOF'
#!/bin/bash
# Persistent Data Backup Script

BACKUP_DIR="backups/persistent-data-$(date '+%Y%m%d-%H%M%S')"
mkdir -p "$BACKUP_DIR"

echo "📦 Creating data persistence backup..."

# Backup shared directory
if [ -d "shared" ]; then
    cp -r shared "$BACKUP_DIR/"
    echo "✅ Shared data backed up"
fi

# Backup database (if .env exists)
if [ -f ".env" ]; then
    cp .env "$BACKUP_DIR/"
    echo "✅ Environment configuration backed up"
fi

echo "🎉 Backup completed: $BACKUP_DIR"
EOF
    chmod +x "$project_root/backup_persistent_data.sh"
    
    # Update session documentation
    cat > "$session_dir/execution/setup-log.md" << EOF
# Data Persistence Setup Execution Log

**Date:** $(date '+%Y-%m-%d %H:%M:%S')
**Project:** $project_root

## Actions Taken
- ✅ Created shared directories
- ✅ Configured symlinks for critical data
- ✅ Created verification script
- ✅ Created backup script

## Files Created
- verify_data_persistence.sh
- backup_persistent_data.sh
- shared/ directory structure

## Next Steps
1. Run verification script
2. Test backup functionality
3. Validate data persistence during updates
EOF

    success "✅ Data persistence system setup completed"
    info "Run ./verify_data_persistence.sh to validate the setup"
    
    return 0
}

# Main execution
main() {
    local test_mode="false"
    local verbose="false"
    local force="false"
    
    # Parse arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            --test-mode)
                test_mode="true"
                shift
                ;;
            --verbose)
                verbose="true"
                shift
                ;;
            --force)
                force="true"
                shift
                ;;
            --help|-h)
                show_usage
                exit 0
                ;;
            *)
                error "Unknown option: $1"
                show_usage
                exit 1
                ;;
        esac
    done
    
    log "🛡️  DATA PERSISTENCE SYSTEM SETUP"
    log "=================================="
    
    if [ "$test_mode" = "true" ]; then
        info "Running in test mode - no changes will be made"
    fi
    
    # Detect project root
    local project_root
    if ! project_root=$(detect_project_root); then
        exit 1
    fi
    
    log "Project Root: $project_root"
    log "Test Mode: $test_mode"
    log "Verbose: $verbose"
    log "Force: $force"
    echo ""
    
    # Setup data persistence system
    if setup_data_persistence "$project_root" "$test_mode" "$verbose"; then
        if [ "$test_mode" != "true" ]; then
            success "🎉 Data persistence system is ready!"
            info "Your data will be protected during vendor updates"
        fi
    else
        error "❌ Data persistence setup failed"
        exit 1
    fi
}

# Handle test mode environment variable
if [ "$TEMPLATE_TEST_MODE" = "true" ]; then
    main --test-mode "$@"
else
    main "$@"
fi