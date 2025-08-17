#!/bin/bash

# Step 17 - PostCSS Config Fix Script
# Fixes PostCSS configuration ESM/CommonJS conflicts
# Author: Auto-generated Step 17 Enhancement
# Usage: bash fix-postcss-config.sh

set -euo pipefail

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

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

# Function to detect module type
detect_module_type() {
    log "Detecting project module type..."
    
    if [ ! -f "package.json" ]; then
        error "package.json not found. Are you in the Laravel project root?"
    fi
    
    # Check if package.json has "type": "module"
    if grep -q '"type": "module"' package.json; then
        echo "module"
    else
        echo "commonjs"
    fi
}

# Function to check current PostCSS config format
check_postcss_format() {
    local file="$1"
    
    if grep -q "module.exports" "$file"; then
        echo "commonjs"
    elif grep -q "export default" "$file"; then
        echo "module"
    else
        echo "unknown"
    fi
}

# Function to convert PostCSS config to ES module
convert_to_esm() {
    local file="$1"
    
    log "Converting $file to ES module format..."
    
    # Create backup
    cp "$file" "${file}.backup"
    
    # Convert module.exports to export default
    sed -i.bak 's/module\.exports = {/export default {/' "$file"
    
    # Remove the .bak file created by sed
    rm -f "${file}.bak"
    
    success "Converted to ES module format"
}

# Function to convert PostCSS config to CommonJS
convert_to_commonjs() {
    local file="$1"
    
    log "Converting $file to CommonJS format..."
    
    # Create backup
    cp "$file" "${file}.backup"
    
    # Convert export default to module.exports
    sed -i.bak 's/export default {/module.exports = {/' "$file"
    
    # Remove the .bak file created by sed
    rm -f "${file}.bak"
    
    success "Converted to CommonJS format"
}

# Function to create PostCSS config if missing
create_postcss_config() {
    local module_type="$1"
    local file="postcss.config.js"
    
    log "Creating $file in $module_type format..."
    
    case "$module_type" in
        "module")
            cat > "$file" << 'EOF'
export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};
EOF
            ;;
        "commonjs")
            cat > "$file" << 'EOF'
module.exports = {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};
EOF
            ;;
        *)
            error "Unknown module type: $module_type"
            ;;
    esac
    
    success "Created $file in $module_type format"
}

# Function to verify PostCSS config
verify_postcss_config() {
    local file="$1"
    local expected_format="$2"
    
    log "Verifying $file format..."
    
    local current_format
    current_format=$(check_postcss_format "$file")
    
    if [ "$current_format" = "$expected_format" ]; then
        success "PostCSS config format is correct ($expected_format)"
        return 0
    else
        warning "PostCSS config format mismatch. Expected: $expected_format, Found: $current_format"
        return 1
    fi
}

# Main execution
main() {
    log "🔧 Starting PostCSS Configuration Fix"
    echo "================================================"
    
    local postcss_file="postcss.config.js"
    
    # Detect project module type
    local module_type
    module_type=$(detect_module_type)
    log "Project module type: $module_type"
    
    # Check if PostCSS config exists
    if [ ! -f "$postcss_file" ]; then
        warning "PostCSS config file not found"
        create_postcss_config "$module_type"
    else
        # Check current format
        local current_format
        current_format=$(check_postcss_format "$postcss_file")
        log "Current PostCSS format: $current_format"
        
        # Fix format if needed
        if [ "$current_format" != "$module_type" ]; then
            warning "PostCSS config format doesn't match project type"
            
            case "$module_type" in
                "module")
                    convert_to_esm "$postcss_file"
                    ;;
                "commonjs")
                    convert_to_commonjs "$postcss_file"
                    ;;
                *)
                    error "Unknown module type: $module_type"
                    ;;
            esac
        else
            success "PostCSS config format already matches project type"
        fi
    fi
    
    # Verify the fix
    if verify_postcss_config "$postcss_file" "$module_type"; then
        echo "================================================"
        success "✨ PostCSS configuration fix completed successfully!"
        echo ""
        log "Next step: Run 'npm run custom:build' to test the build"
    else
        error "PostCSS configuration fix failed verification"
    fi
}

# Execute main function
main "$@"