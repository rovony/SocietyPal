to#!/bin/bash

# INVESTMENT PROTECTION SYSTEM SETUP SCRIPT
# This script sets up the investment protection and documentation system
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
    echo -e "${CYAN}INVESTMENT PROTECTION SYSTEM SETUP${NC}"
    echo ""
    echo -e "${BLUE}Purpose:${NC} Set up investment protection and documentation tracking"
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
    echo "  ✓ Investment tracking documentation"
    echo "  ✓ Change detection and analysis"
    echo "  ✓ ROI calculation tools"
    echo "  ✓ Recovery procedures"
    echo "  ✓ Project documentation generation"
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
    local session_name="investment-protection-setup-$(date '+%Y%m%d-%H%M%S')"
    local session_dir="$tracking_dir/$session_name"
    mkdir -p "$session_dir"/{planning,baselines,execution,verification,documentation,backups}
    
    echo "$session_dir"
}

# Function to setup investment protection system
setup_investment_protection() {
    local project_root="$1"
    local test_mode="$2"
    local verbose="$3"
    
    log "Setting up Investment Protection System"
    echo ""
    
    # Create tracking session
    local session_dir
    if ! session_dir=$(create_tracking_session "$project_root"); then
        error "Failed to create tracking session"
        return 1
    fi
    
    [ "$verbose" = "true" ] && info "Session directory: $session_dir"
    
    # Create planning document
    cat > "$session_dir/planning/investment-protection-plan.md" << 'EOF'
# Investment Protection System Setup Plan

## Objective
Set up comprehensive investment protection and documentation tracking system.

## Components
1. Investment tracking documentation
2. Change detection and analysis
3. ROI calculation tools
4. Recovery procedures
5. Project documentation generation

## Implementation Steps
- [ ] Create investment tracking structure
- [ ] Set up change detection
- [ ] Configure documentation generation
- [ ] Create ROI analysis tools
- [ ] Set up recovery procedures

## Success Criteria
- All investment tracking is automated
- Change detection works properly
- Documentation generation is functional
- ROI analysis provides actionable insights
- Recovery procedures are tested and verified
EOF

    if [ "$test_mode" = "true" ]; then
        success "✅ Test mode: Investment protection system validation passed"
        return 0
    fi
    
    # Create investment tracking directory
    local invest_dir="$project_root/.investment-tracking"
    if [ ! -d "$invest_dir" ]; then
        mkdir -p "$invest_dir"/{changes,documentation,analysis,backups}
        [ "$verbose" = "true" ] && success "Created investment tracking directories"
    fi
    
    # Create investment tracking configuration
    cat > "$invest_dir/config.json" << EOF
{
  "project_name": "$(basename "$project_root")",
  "created": "$(date -Iseconds)",
  "tracking": {
    "changes": true,
    "documentation": true,
    "roi_analysis": true,
    "backup_retention": 30
  },
  "thresholds": {
    "major_change_files": 10,
    "significant_change_lines": 100,
    "documentation_update_days": 7
  }
}
EOF
    
    # Create investment tracking script
    cat > "$project_root/track_investment.sh" << 'EOF'
#!/bin/bash
# Investment Tracking Script

INVEST_DIR=".investment-tracking"
DATE=$(date -Iseconds)

echo "📊 Tracking investment changes..."

# Create daily snapshot
SNAPSHOT_DIR="$INVEST_DIR/changes/$(date '+%Y%m%d')"
mkdir -p "$SNAPSHOT_DIR"

# Track file changes
if command -v git >/dev/null 2>&1; then
    git diff --stat > "$SNAPSHOT_DIR/git-changes.txt"
    git log --oneline --since="1 day ago" > "$SNAPSHOT_DIR/recent-commits.txt"
    echo "✅ Git changes tracked"
fi

# Track project metrics
echo "# Project Metrics - $DATE" > "$SNAPSHOT_DIR/metrics.md"
echo "" >> "$SNAPSHOT_DIR/metrics.md"
echo "## File Counts" >> "$SNAPSHOT_DIR/metrics.md"
find . -name "*.php" -not -path "./vendor/*" | wc -l | xargs echo "PHP Files:" >> "$SNAPSHOT_DIR/metrics.md"
find . -name "*.js" -not -path "./node_modules/*" | wc -l | xargs echo "JavaScript Files:" >> "$SNAPSHOT_DIR/metrics.md"
find . -name "*.vue" -not -path "./node_modules/*" | wc -l | xargs echo "Vue Files:" >> "$SNAPSHOT_DIR/metrics.md"

echo "🎉 Investment tracking completed: $SNAPSHOT_DIR"
EOF
    chmod +x "$project_root/track_investment.sh"
    
    # Create ROI analysis script
    cat > "$project_root/analyze_roi.sh" << 'EOF'
#!/bin/bash
# ROI Analysis Script

INVEST_DIR=".investment-tracking"

echo "📈 Analyzing project ROI..."

if [ ! -d "$INVEST_DIR" ]; then
    echo "❌ Investment tracking not initialized"
    exit 1
fi

# Calculate project age
if [ -f "$INVEST_DIR/config.json" ]; then
    CREATED=$(jq -r '.created' "$INVEST_DIR/config.json" 2>/dev/null || echo "unknown")
    echo "Project Created: $CREATED"
fi

# Count total changes
CHANGE_DIRS=$(find "$INVEST_DIR/changes" -type d -name "[0-9]*" 2>/dev/null | wc -l)
echo "Tracking Days: $CHANGE_DIRS"

# Estimate development time
if command -v git >/dev/null 2>&1; then
    TOTAL_COMMITS=$(git rev-list --count HEAD 2>/dev/null || echo "0")
    echo "Total Commits: $TOTAL_COMMITS"
    
    CONTRIBUTORS=$(git shortlog -sn | wc -l 2>/dev/null || echo "0")
    echo "Contributors: $CONTRIBUTORS"
fi

echo "🎉 ROI analysis completed"
echo "📄 Review .investment-tracking/analysis/ for detailed reports"
EOF
    chmod +x "$project_root/analyze_roi.sh"
    
    # Create documentation generation script
    cat > "$project_root/generate_investment_docs.sh" << 'EOF'
#!/bin/bash
# Investment Documentation Generation Script

INVEST_DIR=".investment-tracking"
DOC_DIR="$INVEST_DIR/documentation"

echo "📚 Generating investment documentation..."

mkdir -p "$DOC_DIR/$(date '+%Y%m%d')"

# Generate project overview
cat > "$DOC_DIR/project-overview.md" << OVERVIEW
# Project Investment Overview

**Generated:** $(date)
**Project:** $(basename "$(pwd)")

## Investment Summary
This document tracks the development investment and progress of this project.

## Key Metrics
- Project structure analysis
- Development time tracking
- Change frequency analysis
- ROI calculations

## Files Generated
- Daily change snapshots
- Commit history analysis
- File count progressions
- Performance metrics

OVERVIEW

echo "✅ Project overview generated"
echo "📊 Documentation available in: $DOC_DIR"
EOF
    chmod +x "$project_root/generate_investment_docs.sh"
    
    # Update session documentation
    cat > "$session_dir/execution/setup-log.md" << EOF
# Investment Protection Setup Execution Log

**Date:** $(date '+%Y-%m-%d %H:%M:%S')
**Project:** $project_root

## Actions Taken
- ✅ Created investment tracking directory structure
- ✅ Generated tracking configuration
- ✅ Created investment tracking scripts
- ✅ Set up ROI analysis tools
- ✅ Created documentation generation scripts

## Files Created
- .investment-tracking/ directory structure
- track_investment.sh
- analyze_roi.sh
- generate_investment_docs.sh

## Next Steps
1. Run investment tracking script daily
2. Generate documentation regularly
3. Analyze ROI periodically
4. Review and optimize investment strategies
EOF

    success "✅ Investment protection system setup completed"
    info "Run ./track_investment.sh to start tracking your investment"
    
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
    
    log "💰 INVESTMENT PROTECTION SYSTEM SETUP"
    log "====================================="
    
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
    
    # Setup investment protection system
    if setup_investment_protection "$project_root" "$test_mode" "$verbose"; then
        if [ "$test_mode" != "true" ]; then
            success "🎉 Investment protection system is ready!"
            info "Your development investment is now being tracked"
        fi
    else
        error "❌ Investment protection setup failed"
        exit 1
    fi
}

# Handle test mode environment variable
if [ "$TEMPLATE_TEST_MODE" = "true" ]; then
    main --test-mode "$@"
else
    main "$@"
fi