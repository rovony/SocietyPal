#!/bin/bash

# ===================================================================
# 🎯 UNIVERSAL LARAVEL TRACKING SYSTEM SETUP v4.0
# ===================================================================
# Linear ADHD-Friendly Project Tracking System
# Supports: 1-First-Setup, 2-Update-or-Customization, 3-Update-or-Customization, etc.
# Compatible with: B-Setup-New-Project, C-Deploy-Vendor-Updates, E-Customize-App
# ===================================================================

set -euo pipefail

# 🎨 Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# 📋 Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT=""
TRACKING_ROOT=""
DATE_TIME=$(date '+%Y%m%d_%H%M%S')

# 🔍 Functions
log_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; }
log_header() { echo -e "${PURPLE}🎯 $1${NC}"; }

# 🔍 Detect project root
detect_project_root() {
    local current_dir="$PWD"
    
    # Look for Laravel indicators
    while [[ "$current_dir" != "/" ]]; do
        if [[ -f "$current_dir/artisan" && -f "$current_dir/composer.json" ]]; then
            PROJECT_ROOT="$current_dir"
            log_success "Laravel project detected: $PROJECT_ROOT"
            return 0
        fi
        current_dir="$(dirname "$current_dir")"
    done
    
    log_error "Laravel project not found! Please run from within a Laravel project directory."
    exit 1
}

# 🔍 Cross-platform relative path function
get_relative_path() {
    local target="$1"
    local base="$2"
    
    # Convert to relative path using bash parameter expansion (cross-platform)
    if [[ "$target" == "$base/"* ]]; then
        echo "${target#$base/}"
    else
        echo "$target"
    fi
}

# 🏗️ Create directory structure (Cross-platform: Windows/Mac/Linux)
create_directory() {
    local dir="$1"
    if [[ ! -d "$dir" ]]; then
        mkdir -p "$dir"
        log_success "Created: $(get_relative_path "$dir" "$PROJECT_ROOT")"
    else
        log_info "Exists: $(get_relative_path "$dir" "$PROJECT_ROOT")"
    fi
}

# 📝 Create README template
create_readme() {
    local dir="$1"
    local title="$2"
    local description="$3"
    
    if [[ ! -f "$dir/README.md" ]]; then
        cat > "$dir/README.md" << EOF
# $title

$description

## 📅 Created
- **Date**: $(date '+%Y-%m-%d %H:%M:%S')
- **System**: Laravel CodeCanyon Project Management System

## 📋 Contents
- This directory is part of the Linear ADHD-Friendly Tracking System
- Each numbered folder represents a logical step in the process
- Follow the numbering sequence: 1 → 2 → 3 → 4 → 5

## 🎯 Usage
1. Document planning decisions in 1-Planning/
2. Record baseline states in 2-Baselines/
3. Track execution steps in 3-Execution/
4. Verify results in 4-Verification/
5. Generate final documentation in 5-Documentation/

## 🔗 Integration
This tracking structure integrates with:
- **B-Setup-New-Project** (Uses 1-First-Setup/)
- **C-Deploy-Vendor-Updates** (Creates X-Vendor-Update-vX.X.XX/)
- **E-Customize-App** (Creates X-Custom-FeatureName/)

EOF
        log_success "Created README: $(get_relative_path "$dir/README.md" "$PROJECT_ROOT")"
    fi
}

# 🏗️ Create First Setup structure
create_first_setup() {
    log_header "Creating 1-First-Setup Structure"
    
    local base_dir="$TRACKING_ROOT/1-First-Setup"
    create_directory "$base_dir"
    
    # Create numbered subdirectories
    local subdirs=(
        "1-Planning"
        "2-Baselines"
        "3-Execution"
        "4-Verification"
        "5-Documentation"
    )
    
    for subdir in "${subdirs[@]}"; do
        create_directory "$base_dir/$subdir"
    done
    
    create_readme "$base_dir" "1-First-Setup" "Initial project setup tracking for B-Setup-New-Project workflow"
}

# 🔄 Create Update-or-Customization template
create_update_customization_template() {
    local template_num="$1"
    log_header "Creating $template_num-Update-or-Customization Structure"
    
    local base_dir="$TRACKING_ROOT/$template_num-Update-or-Customization"
    create_directory "$base_dir"
    
    # Create 0-Backups with sub-categories
    create_directory "$base_dir/0-Backups"
    local backup_subdirs=(
        "1-Critical-Files"
        "2-Build-Assets"
        "3-Custom-Files"
        "4-Config-Files"
    )
    
    for subdir in "${backup_subdirs[@]}"; do
        create_directory "$base_dir/0-Backups/$subdir"
    done
    
    # Create numbered subdirectories
    local subdirs=(
        "1-Planning"
        "2-Baselines"
        "3-Execution"
        "4-Verification"
        "5-Documentation"
    )
    
    for subdir in "${subdirs[@]}"; do
        create_directory "$base_dir/$subdir"
    done
    
    create_readme "$base_dir" "$template_num-Update-or-Customization" "Template for vendor updates or customizations (C-Deploy-Vendor-Updates or E-Customize-App)"
    
    # Create backup README
    cat > "$base_dir/0-Backups/README.md" << EOF
# 0-Backups

Critical backup storage for update/customization operations.

## 📁 Structure
- **1-Critical-Files**: Laravel core files (.env, composer.json, etc.)
- **2-Build-Assets**: Compiled CSS, JS, images
- **3-Custom-Files**: Our custom code and templates
- **4-Config-Files**: Configuration files (database, cache, etc.)

## 🎯 Usage
Always create backups BEFORE making any changes:
1. Copy critical project files to 1-Critical-Files/
2. Backup compiled assets to 2-Build-Assets/
3. Backup custom code to 3-Custom-Files/
4. Backup config files to 4-Config-Files/

## 🔄 Recovery
Use these backups if something goes wrong during updates or customizations.
EOF
    log_success "Created backup structure in $template_num-Update-or-Customization/"
}

# 📊 Create Master Reports structure
create_master_reports() {
    log_header "Creating 99-Master-Reports Structure"
    
    local base_dir="$TRACKING_ROOT/99-Master-Reports"
    create_directory "$base_dir"
    
    create_readme "$base_dir" "99-Master-Reports" "Cross-operation analysis and master reporting"
    
    # Create master report template
    cat > "$base_dir/TEMPLATE_MASTER_REPORT.md" << EOF
# Master Project Report

## 📋 Project Overview
- **Project**: [Project Name]
- **CodeCanyon Version**: [Version]
- **Laravel Version**: [Version]
- **Report Generated**: $(date '+%Y-%m-%d %H:%M:%S')

## 📈 Operations Summary
| # | Operation | Type | Date | Status |
|---|-----------|------|------|--------|
| 1 | First-Setup | Setup | [Date] | ✅ Complete |
| 2 | [Operation] | Update/Custom | [Date] | [Status] |

## 🎯 Current State
- **Total Operations**: [Number]
- **Last Update**: [Date]
- **Custom Features**: [Count]
- **System Health**: [Status]

## 📊 Statistics
- **Files Modified**: [Number]
- **Lines of Code**: [Number]
- **Investment Value**: \$[Amount]
- **ROI**: [Percentage]

## 🔗 Quick Links
- [1-First-Setup](../1-First-Setup/)
- [Latest Operation](../X-Latest-Operation/)
EOF
    log_success "Created master report template"
}

# 🔧 Setup tracking system
setup_tracking_system() {
    log_header "Setting Up Linear ADHD-Friendly Tracking System"
    
    # Detect project root
    detect_project_root
    
    # Set tracking root
    TRACKING_ROOT="$PROJECT_ROOT/Admin-Local/1-CurrentProject/Tracking"
    
    # Create base tracking directory
    create_directory "$TRACKING_ROOT"
    
    # Create main README
    cat > "$TRACKING_ROOT/README.md" << EOF
# 🎯 Linear ADHD-Friendly Project Tracking System v4.0

## 📋 System Overview
This is the **Universal Laravel CodeCanyon Project Management System** using linear numbering for maximum clarity and ADHD-friendliness.

## 🔢 Numbering System
- **1-First-Setup/**: Initial project setup (B-Setup-New-Project)
- **2-Update-or-Customization/**: First operation (vendor update OR customization)
- **3-Update-or-Customization/**: Second operation (vendor update OR customization)  
- **4-Update-or-Customization/**: Third operation (vendor update OR customization)
- **...**
- **99-Master-Reports/**: Cross-operation analysis

## 🔄 Operation Types
Each numbered slot (2+) can be:
- **Vendor Update**: \`X-Vendor-Update-vX.X.XX/\` (C-Deploy-Vendor-Updates)
- **Custom Feature**: \`X-Custom-FeatureName/\` (E-Customize-App)

## 🎯 Workflow Integration
- **B-Setup-New-Project**: Uses \`1-First-Setup/\`
- **C-Deploy-Vendor-Updates**: Copies template → \`X-Vendor-Update-vX.X.XX/\`
- **E-Customize-App**: Copies template → \`X-Custom-FeatureName/\`

## 📅 Created
- **Date**: $(date '+%Y-%m-%d %H:%M:%S')
- **Setup Script**: 5-Tracking-System/setup-tracking.sh
- **Version**: v4.0 Linear ADHD-Friendly

EOF
    log_success "Created main tracking README"
    
    # Create structures
    create_first_setup
    create_update_customization_template "2"
    create_update_customization_template "3" 
    create_update_customization_template "4"
    create_master_reports
    
    log_success "Tracking system setup completed!"
}

# 📋 Main execution
main() {
    echo
    log_header "🎯 UNIVERSAL LARAVEL TRACKING SYSTEM SETUP v4.0"
    echo
    log_info "This script creates a Linear ADHD-Friendly tracking structure for Laravel CodeCanyon projects"
    echo
    
    setup_tracking_system
    
    echo
    log_header "📋 SETUP COMPLETE!"
    echo
    log_success "Tracking system created at: Admin-Local/1-CurrentProject/Tracking/"
    log_info "Structure: 1-First-Setup, 2-Update-or-Customization, 3-Update-or-Customization, ..."
    log_info "Integration: Works with B-Setup-New-Project, C-Deploy-Vendor-Updates, E-Customize-App"
    echo
    log_warning "Next Steps:"
    echo "  1. Use 1-First-Setup/ for B-Setup-New-Project workflow"
    echo "  2. Copy 2-Update-or-Customization/ template for first operation"
    echo "  3. Follow linear numbering: 2, 3, 4, 5, etc."
    echo
}

# 🚀 Execute
main "$@"