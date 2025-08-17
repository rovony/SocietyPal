#!/bin/bash

# TEMPLATE CLEANUP SCRIPT
# This script removes all generated template files from the project
# WARNING: This will remove customizations and generated files - use with caution!
# Author: Automated Template System v4.0
# Date: $(date)

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
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

danger() {
    echo -e "${MAGENTA}[DANGER] $1${NC}"
}

# Function to show usage
show_usage() {
    echo -e "${CYAN}TEMPLATE CLEANUP SCRIPT${NC}"
    echo ""
    echo -e "${BLUE}Usage:${NC}"
    echo "  $0 [template-name] [template-name] ..."
    echo "  $0 --list                    # List cleanup targets"
    echo "  $0 --interactive             # Interactive selection mode"
    echo "  $0 --all --force             # Remove all template files (DANGEROUS)"
    echo "  $0 --help                    # Show this help"
    echo ""
    echo -e "${BLUE}Available Templates:${NC}"
    echo "  tracking                     # 5-Tracking-System files"
    echo "  customization               # 6-Customization-System files"
    echo "  data-persistence            # 7-Data-Persistence-System files"
    echo "  investment-protection       # 8-Investment-Protection-System files"
    echo ""
    echo -e "${RED}⚠️  WARNING: This script permanently removes files!${NC}"
    echo -e "${RED}   Make sure you have backups before proceeding.${NC}"
    echo ""
    echo -e "${BLUE}Examples:${NC}"
    echo "  $0 tracking                 # Remove tracking system files only"
    echo "  $0 customization --force    # Remove customization files without confirmation"
    echo "  $0 --interactive             # Choose from menu with safety prompts"
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

# Function to get cleanup targets for each template
get_cleanup_targets() {
    local template_key="$1"
    local project_root="$2"
    
    case "$template_key" in
        "tracking")
            echo "$project_root/Admin-Local/1-CurrentProject/Tracking"
            ;;
        "customization")
            echo "$project_root/app/Custom:$project_root/app/Providers/CustomizationServiceProvider.php:$project_root/resources/Custom:$project_root/config/custom-app.php:$project_root/config/custom-database.php:$project_root/webpack.custom.cjs"
            ;;
        "data-persistence")
            echo "$project_root/.env.backup*:$project_root/storage/persistence:$project_root/DATA_PERSISTENCE.md"
            ;;
        "investment-protection")
            echo "$project_root/.investment-tracking:$project_root/INVESTMENT_TRACKING.md"
            ;;
        *)
            error "Unknown template: $template_key"
            return 1
            ;;
    esac
}

# Function to remove files/directories safely
safe_remove() {
    local target="$1"
    local template_name="$2"
    local force="$3"
    
    if [ ! -e "$target" ]; then
        info "Target does not exist: $target (skipping)"
        return 0
    fi
    
    if [ "$force" != "true" ]; then
        echo -n "Remove $(basename "$target") from $template_name? (y/N): "
        read -r confirm
        if [[ ! "$confirm" =~ ^[Yy]$ ]]; then
            info "Skipped: $target"
            return 0
        fi
    fi
    
    if [ -d "$target" ]; then
        log "Removing directory: $target"
        rm -rf "$target"
    else
        log "Removing file: $target"
        rm -f "$target"
    fi
    
    success "Removed: $target"
    return 0
}

# Function to cleanup specific template
cleanup_template() {
    local template_key="$1"
    local project_root="$2"
    local force="$3"
    
    log "Cleaning up $template_key template files..."
    
    local targets
    targets=$(get_cleanup_targets "$template_key" "$project_root")
    
    if [ $? -ne 0 ]; then
        return 1
    fi
    
    local success_count=0
    local total_count=0
    
    IFS=':' read -ra TARGET_ARRAY <<< "$targets"
    for target in "${TARGET_ARRAY[@]}"; do
        # Handle glob patterns
        if [[ "$target" == *"*"* ]]; then
            for expanded_target in $target; do
                if [ -e "$expanded_target" ]; then
                    total_count=$((total_count + 1))
                    if safe_remove "$expanded_target" "$template_key" "$force"; then
                        success_count=$((success_count + 1))
                    fi
                fi
            done
        else
            total_count=$((total_count + 1))
            if safe_remove "$target" "$template_key" "$force"; then
                success_count=$((success_count + 1))
            fi
        fi
    done
    
    log "$template_key cleanup: $success_count/$total_count targets processed"
    return 0
}

# Function for interactive mode
interactive_mode() {
    local project_root="$1"
    
    info "🗑️  INTERACTIVE TEMPLATE CLEANUP"
    echo ""
    
    danger "⚠️  WARNING: This will permanently delete files!"
    danger "   Make sure you have backups of any important customizations."
    echo ""
    
    declare -a available_templates=(
        "tracking:5-Tracking-System"
        "customization:6-Customization-System"
        "data-persistence:7-Data-Persistence-System"
        "investment-protection:8-Investment-Protection-System"
    )
    
    declare -a selected_templates=()
    
    echo "Available template systems to clean:"
    for i in "${!available_templates[@]}"; do
        IFS=':' read -r key display_name <<< "${available_templates[$i]}"
        echo "  $((i+1)). $display_name ($key)"
    done
    echo ""
    
    while true; do
        echo -n "Enter template numbers to clean (e.g., 1,3 or 'all' or 'done'): "
        read -r selection
        
        if [[ "$selection" == "done" ]]; then
            break
        elif [[ "$selection" == "all" ]]; then
            danger "⚠️  You selected ALL templates for cleanup!"
            echo -n "Are you absolutely sure? Type 'DELETE ALL' to confirm: "
            read -r confirm_all
            if [[ "$confirm_all" == "DELETE ALL" ]]; then
                for template_entry in "${available_templates[@]}"; do
                    IFS=':' read -r key display_name <<< "$template_entry"
                    selected_templates+=("$key")
                done
                break
            else
                warning "Cancelled - confirmation not exact"
                continue
            fi
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
        warning "No templates selected for cleanup. Exiting."
        return 1
    fi
    
    echo ""
    info "Selected templates for cleanup:"
    for template in "${selected_templates[@]}"; do
        echo "  - $template"
    done
    
    echo ""
    danger "⚠️  FINAL WARNING: This will permanently delete template files!"
    echo -n "Type 'I UNDERSTAND' to proceed: "
    read -r final_confirm
    if [[ "$final_confirm" != "I UNDERSTAND" ]]; then
        info "Operation cancelled - safety check failed"
        return 1
    fi
    
    # Process selected templates
    local total_templates=${#selected_templates[@]}
    local successful_templates=0
    local failed_templates=0
    
    for template_key in "${selected_templates[@]}"; do
        if cleanup_template "$template_key" "$project_root" "false"; then
            successful_templates=$((successful_templates + 1))
        else
            failed_templates=$((failed_templates + 1))
        fi
        echo ""
    done
    
    # Final summary
    log "========================================="
    log "🗑️  CLEANUP SUMMARY"
    log "Total Templates: $total_templates"
    success "Successfully cleaned: $successful_templates"
    if [ $failed_templates -gt 0 ]; then
        error "Failed to clean: $failed_templates"
        return 1
    else
        log "Failed: $failed_templates"
        success "🎉 ALL SELECTED TEMPLATES CLEANED SUCCESSFULLY!"
        return 0
    fi
}

# Main execution
main() {
    local force_mode="false"
    local templates_to_clean=()
    
    # Parse arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            --help|-h)
                show_usage
                exit 0
                ;;
            --list|-l)
                log "Available cleanup targets:"
                log "- tracking: Admin-Local/1-CurrentProject/Tracking/"
                log "- customization: app/Custom/, resources/Custom/, config files, etc."
                log "- data-persistence: .env.backup*, storage/persistence/, etc."
                log "- investment-protection: .investment-tracking/, INVESTMENT_TRACKING.md"
                exit 0
                ;;
            --interactive|-i)
                # Detect paths
                local project_root
                if ! project_root=$(detect_project_root); then
                    exit 1
                fi
                
                interactive_mode "$project_root"
                exit $?
                ;;
            --force)
                force_mode="true"
                ;;
            --all)
                if [ "$force_mode" = "true" ]; then
                    templates_to_clean=("tracking" "customization" "data-persistence" "investment-protection")
                else
                    error "--all requires --force for safety"
                    exit 1
                fi
                ;;
            "")
                show_usage
                exit 0
                ;;
            *)
                templates_to_clean+=("$1")
                ;;
        esac
        shift
    done
    
    if [ ${#templates_to_clean[@]} -eq 0 ]; then
        show_usage
        exit 0
    fi
    
    danger "🗑️  TEMPLATE CLEANUP STARTING"
    danger "⚠️  THIS WILL PERMANENTLY DELETE FILES!"
    log "========================================="
    
    # Detect paths
    local project_root
    if ! project_root=$(detect_project_root); then
        exit 1
    fi
    
    log "Project Root: $project_root"
    log "Force Mode: $force_mode"
    log ""
    
    if [ "$force_mode" != "true" ]; then
        danger "⚠️  You are about to remove template files permanently!"
        echo -n "Are you sure you want to continue? (y/N): "
        read -r confirm
        if [[ ! "$confirm" =~ ^[Yy]$ ]]; then
            info "Operation cancelled by user"
            exit 0
        fi
    fi
    
    # Process templates
    local total_templates=${#templates_to_clean[@]}
    local successful_templates=0
    local failed_templates=0
    
    for template_key in "${templates_to_clean[@]}"; do
        if cleanup_template "$template_key" "$project_root" "$force_mode"; then
            successful_templates=$((successful_templates + 1))
        else
            failed_templates=$((failed_templates + 1))
        fi
        echo ""
    done
    
    # Final summary
    log "========================================="
    log "🗑️  CLEANUP SUMMARY"
    log "Total Templates: $total_templates"
    success "Successfully cleaned: $successful_templates"
    if [ $failed_templates -gt 0 ]; then
        error "Failed to clean: $failed_templates"
        exit 1
    else
        log "Failed: $failed_templates"
        success "🎉 CLEANUP COMPLETED SUCCESSFULLY!"
        info "Project is now clean of selected template files"
    fi
}

# Run main function
main "$@"