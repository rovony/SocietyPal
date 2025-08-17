#!/bin/bash

# SELECTIVE TEMPLATE REGENERATION SCRIPT
# This script allows selective regeneration of specific template systems
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
    echo -e "${CYAN}SELECTIVE TEMPLATE REGENERATION SCRIPT${NC}"
    echo ""
    echo -e "${BLUE}Usage:${NC}"
    echo "  $0 [template-name] [template-name] ..."
    echo "  $0 --list                    # List available templates"
    echo "  $0 --interactive             # Interactive selection mode"
    echo "  $0 --help                    # Show this help"
    echo ""
    echo -e "${BLUE}Available Templates:${NC}"
    echo "  tracking                     # 5-Tracking-System"
    echo "  customization               # 6-Customization-System"
    echo "  data-persistence            # 7-Data-Persistence-System"
    echo "  investment-protection       # 8-Investment-Protection-System"
    echo ""
    echo -e "${BLUE}Examples:${NC}"
    echo "  $0 tracking customization   # Regenerate tracking and customization systems"
    echo "  $0 data-persistence         # Regenerate data persistence system only"
    echo "  $0 --interactive             # Choose from menu"
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

# Function to get template mapping
get_template_mapping() {
    declare -A template_map=(
        ["tracking"]="5-Tracking-System:tracking"
        ["customization"]="6-Customization-System:customization" 
        ["data-persistence"]="7-Data-Persistence-System:data-persistence"
        ["investment-protection"]="8-Investment-Protection-System:investment-protection"
    )
    
    local key="$1"
    echo "${template_map[$key]}"
}

# Function for interactive mode
interactive_mode() {
    local template_dir="$1"
    
    info "🎯 INTERACTIVE TEMPLATE SELECTION"
    echo ""
    
    declare -a available_templates=(
        "tracking:5-Tracking-System"
        "customization:6-Customization-System"
        "data-persistence:7-Data-Persistence-System"
        "investment-protection:8-Investment-Protection-System"
    )
    
    declare -a selected_templates=()
    
    echo "Available template systems:"
    for i in "${!available_templates[@]}"; do
        IFS=':' read -r key display_name <<< "${available_templates[$i]}"
        echo "  $((i+1)). $display_name ($key)"
    done
    echo ""
    
    while true; do
        echo -n "Enter template numbers to regenerate (e.g., 1,3 or 'all' or 'done'): "
        read -r selection
        
        if [[ "$selection" == "done" ]]; then
            break
        elif [[ "$selection" == "all" ]]; then
            for template_entry in "${available_templates[@]}"; do
                IFS=':' read -r key display_name <<< "$template_entry"
                selected_templates+=("$key")
            done
            break
        else
            IFS=',' read -ra numbers <<< "$selection"
            for num in "${numbers[@]}"; do
                num=$(echo "$num" | xargs)  # Trim whitespace
                if [[ "$num" =~ ^[0-9]+$ ]] && [ "$num" -ge 1 ] && [ "$num" -le "${#available_templates[@]}" ]; then
                    IFS=':' read -r key display_name <<< "${available_templates[$((num-1))]}"
                    selected_templates+=("$key")
                else
                    warning "Invalid selection: $num"
                fi
            done
            break
        fi
    done
    
    if [ ${#selected_templates[@]} -eq 0 ]; then
        warning "No templates selected. Exiting."
        return 1
    fi
    
    echo ""
    info "Selected templates for regeneration:"
    for template in "${selected_templates[@]}"; do
        echo "  - $template"
    done
    
    echo ""
    echo -n "Proceed with regeneration? (y/N): "
    read -r confirm
    if [[ ! "$confirm" =~ ^[Yy]$ ]]; then
        info "Operation cancelled by user"
        return 1
    fi
    
    # Process selected templates
    local total_templates=${#selected_templates[@]}
    local successful_templates=0
    local failed_templates=0
    
    for template_key in "${selected_templates[@]}"; do
        local template_mapping
        template_mapping=$(get_template_mapping "$template_key")
        
        if [ -z "$template_mapping" ]; then
            error "Unknown template: $template_key"
            failed_templates=$((failed_templates + 1))
            continue
        fi
        
        IFS=':' read -r template_name script_name <<< "$template_mapping"
        
        if run_template_script "$template_name" "$script_name" "$template_dir"; then
            successful_templates=$((successful_templates + 1))
        else
            failed_templates=$((failed_templates + 1))
        fi
        
        echo ""
    done
    
    # Final summary
    log "========================================="
    log "📊 SELECTIVE REGENERATION SUMMARY"
    log "Total Templates: $total_templates"
    success "Successful: $successful_templates"
    if [ $failed_templates -gt 0 ]; then
        error "Failed: $failed_templates"
        return 1
    else
        log "Failed: $failed_templates"
        success "🎉 ALL SELECTED TEMPLATES REGENERATED SUCCESSFULLY!"
        return 0
    fi
}

# Main execution
main() {
    # Handle help and list options
    case "${1:-}" in
        --help|-h)
            show_usage
            exit 0
            ;;
        --list|-l)
            log "Available template systems:"
            log "- tracking (5-Tracking-System)"
            log "- customization (6-Customization-System)"
            log "- data-persistence (7-Data-Persistence-System)"
            log "- investment-protection (8-Investment-Protection-System)"
            exit 0
            ;;
        --interactive|-i)
            # Detect paths
            local project_root
            if ! project_root=$(detect_project_root); then
                exit 1
            fi
            
            local template_dir
            if ! template_dir=$(detect_template_dir "$project_root"); then
                exit 1
            fi
            
            interactive_mode "$template_dir"
            exit $?
            ;;
        "")
            show_usage
            exit 0
            ;;
    esac
    
    log "🔧 SELECTIVE TEMPLATE REGENERATION STARTING"
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
    
    # Process command line arguments
    local total_templates=0
    local successful_templates=0
    local failed_templates=0
    
    for template_key in "$@"; do
        total_templates=$((total_templates + 1))
        
        local template_mapping
        template_mapping=$(get_template_mapping "$template_key")
        
        if [ -z "$template_mapping" ]; then
            error "Unknown template: $template_key"
            error "Use --list to see available templates"
            failed_templates=$((failed_templates + 1))
            continue
        fi
        
        IFS=':' read -r template_name script_name <<< "$template_mapping"
        
        if run_template_script "$template_name" "$script_name" "$template_dir"; then
            successful_templates=$((successful_templates + 1))
        else
            failed_templates=$((failed_templates + 1))
        fi
        
        echo ""
    done
    
    # Final summary
    log "========================================="
    log "📊 SELECTIVE REGENERATION SUMMARY"
    log "Total Templates: $total_templates"
    success "Successful: $successful_templates"
    if [ $failed_templates -gt 0 ]; then
        error "Failed: $failed_templates"
        exit 1
    else
        log "Failed: $failed_templates"
        success "🎉 ALL SELECTED TEMPLATES REGENERATED SUCCESSFULLY!"
    fi
}

# Run main function
main "$@"