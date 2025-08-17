#!/bin/bash

# MASTER TEMPLATE REGENERATION SCRIPT
# This script regenerates ALL template systems in the correct order
# Author: Automated Template System v4.0
# Date: $(date)

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
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

# Function to detect template directory
detect_template_dir() {
    local project_root="$1"
    local template_dir="$project_root/Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates"
    
    if [ -d "$template_dir" ]; then
        echo "$template_dir"
        return 0
    fi
    
    error "Could not find templates directory: $template_dir"
    return 1
}

# Function to run template regeneration script
run_template_script() {
    local template_name="$1"
    local script_name="$2"
    local template_dir="$3"
    local script_path="$template_dir/$template_name/setup-${script_name}.sh"
    
    log "Regenerating $template_name..."
    
    if [ ! -f "$script_path" ]; then
        warning "Script not found: $script_path - Skipping $template_name"
        return 1
    fi
    
    if [ ! -x "$script_path" ]; then
        log "Making $script_path executable..."
        chmod +x "$script_path"
    fi
    
    cd "$(dirname "$script_path")"
    if bash "$script_path"; then
        success "$template_name regenerated successfully"
        return 0
    else
        error "Failed to regenerate $template_name"
        return 1
    fi
}

# Main execution
main() {
    log "🚀 MASTER TEMPLATE REGENERATION STARTING"
    log "========================================="
    
    # Detect paths
    local project_root
    if ! project_root=$(detect_project_root); then
        exit 1
    fi
    
    local template_dir
    if ! template_dir=$(detect_template_dir "$project_root"); then
        exit 1
    fi
    
    log "Project Root: $project_root"
    log "Template Directory: $template_dir"
    log ""
    
    # Initialize counters
    local total_templates=0
    local successful_templates=0
    local failed_templates=0
    
    # Define regeneration order (dependencies first)
    declare -a template_order=(
        "5-Tracking-System:tracking"
        "6-Customization-System:customization"
        "7-Data-Persistence-System:data-persistence"
        "8-Investment-Protection-System:investment-protection"
    )
    
    # Regenerate each template system
    for template_entry in "${template_order[@]}"; do
        IFS=':' read -r template_name script_name <<< "$template_entry"
        total_templates=$((total_templates + 1))
        
        if run_template_script "$template_name" "$script_name" "$template_dir"; then
            successful_templates=$((successful_templates + 1))
        else
            failed_templates=$((failed_templates + 1))
        fi
        
        log ""
    done
    
    # Final summary
    log "========================================="
    log "📊 REGENERATION SUMMARY"
    log "Total Templates: $total_templates"
    success "Successful: $successful_templates"
    if [ $failed_templates -gt 0 ]; then
        error "Failed: $failed_templates"
    else
        log "Failed: $failed_templates"
    fi
    
    if [ $failed_templates -eq 0 ]; then
        success "🎉 ALL TEMPLATES REGENERATED SUCCESSFULLY!"
        log "Project is now ready with fresh template implementations"
    else
        error "❌ SOME TEMPLATES FAILED TO REGENERATE"
        log "Please check the error messages above and fix any issues"
        exit 1
    fi
}

# Run main function
main "$@"