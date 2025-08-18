#!/bin/bash

# SocietyPal Emergency Rollback Script
# This script provides emergency rollback capabilities for failed deployments
# Can be run manually or triggered automatically by the CI/CD pipeline

set -e

# Colors for output
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
ENVIRONMENT="${1:-staging}"
SERVER_HOST="${SERVER_HOST:-31.97.195.108}"
SERVER_PORT="${SERVER_PORT:-65002}"
SERVER_USER="${SERVER_USER:-u227177893}"

# Set paths based on environment
if [[ "$ENVIRONMENT" == "production" ]]; then
    DOMAIN_PATH="/home/$SERVER_USER/domains/societypal.com"
    PUBLIC_PATH="/home/$SERVER_USER/domains/societypal.com/public_html"
    RELEASES_PATH="/home/$SERVER_USER/domains/societypal.com/releases"
    DOMAIN_URL="https://societypal.com"
else
    DOMAIN_PATH="/home/$SERVER_USER/domains/staging.societypal.com"
    PUBLIC_PATH="/home/$SERVER_USER/domains/staging.societypal.com/public_html"
    RELEASES_PATH="/home/$SERVER_USER/domains/staging.societypal.com/releases"
    DOMAIN_URL="https://staging.societypal.com"
fi

echo -e "${BLUE}🔄 SocietyPal Emergency Rollback Script${NC}"
echo -e "${BLUE}Environment: $ENVIRONMENT${NC}"
echo -e "${BLUE}Domain: $DOMAIN_URL${NC}"
echo ""

# Function to display usage
show_usage() {
    echo "Usage: $0 [staging|production] [options]"
    echo ""
    echo "Options:"
    echo "  --list              List available releases"
    echo "  --current           Show current active release"
    echo "  --rollback [release] Rollback to specific release (or previous if not specified)"
    echo "  --health-check      Perform health check on current deployment"
    echo "  --help              Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 staging --list"
    echo "  $0 production --rollback 20250118-143020"
    echo "  $0 staging --current"
}

# Function to execute SSH commands
ssh_exec() {
    ssh -p "$SERVER_PORT" -o StrictHostKeyChecking=no "$SERVER_USER@$SERVER_HOST" "$1"
}

# Function to list available releases
list_releases() {
    echo -e "${BLUE}📁 Available releases for $ENVIRONMENT:${NC}"
    echo ""
    
    ssh_exec "
        if [ -d '$RELEASES_PATH' ]; then
            cd '$RELEASES_PATH'
            echo 'Release Directory | Created | Size'
            echo '------------------|---------|-----'
            for release in \$(ls -dt */ 2>/dev/null | head -10); do
                release_name=\${release%/}
                created=\$(stat -c %y \"\$release\" 2>/dev/null | cut -d' ' -f1,2 | cut -d. -f1 || echo 'Unknown')
                size=\$(du -sh \"\$release\" 2>/dev/null | cut -f1 || echo 'Unknown')
                echo \"\$release_name | \$created | \$size\"
            done
        else
            echo '❌ No releases directory found'
            exit 1
        fi
    "
}

# Function to show current release
show_current() {
    echo -e "${BLUE}🔍 Current active release for $ENVIRONMENT:${NC}"
    echo ""
    
    current_release=$(ssh_exec "
        if [ -L '$PUBLIC_PATH' ]; then
            readlink '$PUBLIC_PATH' | xargs basename | xargs dirname | xargs basename
        else
            echo 'No symlink found'
            exit 1
        fi
    ")
    
    if [[ "$current_release" != "No symlink found" ]]; then
        echo -e "${GREEN}✅ Current Release: $current_release${NC}"
        
        # Get release info
        ssh_exec "
            release_path='$RELEASES_PATH/$current_release'
            if [ -d \"\$release_path\" ]; then
                echo \"📅 Created: \$(stat -c %y \"\$release_path\" | cut -d' ' -f1,2 | cut -d. -f1)\"
                echo \"💾 Size: \$(du -sh \"\$release_path\" | cut -f1)\"
            fi
        "
    else
        echo -e "${RED}❌ No current release found${NC}"
        exit 1
    fi
}

# Function to perform health check
health_check() {
    echo -e "${BLUE}🏥 Performing health check for $ENVIRONMENT...${NC}"
    echo ""
    
    # HTTP Response Check
    echo "🌐 Testing HTTP response..."
    if curl -f -s -o /dev/null -w "%{http_code}" "$DOMAIN_URL" | grep -q "200"; then
        echo -e "${GREEN}✅ HTTP Status: OK (200)${NC}"
    else
        echo -e "${RED}❌ HTTP Status: Failed${NC}"
        return 1
    fi
    
    # Response Time Check
    echo "⏱️  Testing response time..."
    response_time=$(curl -o /dev/null -s -w "%{time_total}" "$DOMAIN_URL")
    if (( $(echo "$response_time < 5.0" | bc -l) )); then
        echo -e "${GREEN}✅ Response Time: ${response_time}s (Good)${NC}"
    else
        echo -e "${YELLOW}⚠️  Response Time: ${response_time}s (Slow)${NC}"
    fi
    
    # Application Check
    echo "🔍 Testing application availability..."
    if curl -s "$DOMAIN_URL" | grep -q "SocietyPal\|Laravel"; then
        echo -e "${GREEN}✅ Application: Available${NC}"
    else
        echo -e "${RED}❌ Application: Not responding properly${NC}"
        return 1
    fi
    
    echo -e "${GREEN}🎉 Health check completed successfully!${NC}"
}

# Function to rollback to previous release
rollback() {
    local target_release="$1"
    
    echo -e "${YELLOW}🔄 Starting rollback process for $ENVIRONMENT...${NC}"
    echo ""
    
    # If no specific release provided, get the previous one
    if [[ -z "$target_release" ]]; then
        echo "🔍 Finding previous release..."
        target_release=$(ssh_exec "
            cd '$RELEASES_PATH'
            ls -dt */ | head -n 2 | tail -n 1 | sed 's/\///'
        ")
        
        if [[ -z "$target_release" ]]; then
            echo -e "${RED}❌ No previous release found for rollback${NC}"
            exit 1
        fi
        
        echo -e "${BLUE}📦 Target release: $target_release${NC}"
    fi
    
    # Verify target release exists
    echo "✅ Verifying target release exists..."
    if ! ssh_exec "[ -d '$RELEASES_PATH/$target_release' ]"; then
        echo -e "${RED}❌ Release '$target_release' not found${NC}"
        echo "Available releases:"
        list_releases
        exit 1
    fi
    
    # Get current release for backup
    current_release=$(ssh_exec "
        if [ -L '$PUBLIC_PATH' ]; then
            readlink '$PUBLIC_PATH' | xargs basename | xargs dirname | xargs basename
        else
            echo 'none'
        fi
    ")
    
    echo -e "${BLUE}📋 Rollback Summary:${NC}"
    echo "  From: $current_release"
    echo "  To:   $target_release"
    echo "  Environment: $ENVIRONMENT"
    echo "  Domain: $DOMAIN_URL"
    echo ""
    
    # Confirm rollback
    read -p "❓ Continue with rollback? (y/N): " -r
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "🚫 Rollback cancelled"
        exit 0
    fi
    
    # Perform rollback
    echo "🔄 Performing atomic rollback..."
    ssh_exec "
        # Create backup of current symlink
        if [ -L '$PUBLIC_PATH' ]; then
            cp -P '$PUBLIC_PATH' '$PUBLIC_PATH.backup.\$(date +%s)' 2>/dev/null || true
        fi
        
        # Perform atomic switch
        ln -nfs '$RELEASES_PATH/$target_release/public' '$PUBLIC_PATH'
        
        echo '✅ Symlink updated successfully'
    "
    
    # Clear Laravel caches in the rolled-back release
    echo "🔄 Clearing caches in rolled-back release..."
    ssh_exec "
        cd '$RELEASES_PATH/$target_release'
        
        # Clear caches
        php artisan config:clear 2>/dev/null || true
        php artisan cache:clear 2>/dev/null || true
        php artisan view:clear 2>/dev/null || true
        
        # Rebuild caches
        php artisan config:cache 2>/dev/null || true
        php artisan route:cache 2>/dev/null || true
        php artisan view:cache 2>/dev/null || true
        
        echo '✅ Caches rebuilt successfully'
    "
    
    echo -e "${GREEN}✅ Rollback completed successfully!${NC}"
    echo ""
    
    # Perform post-rollback health check
    echo "🏥 Performing post-rollback health check..."
    if health_check; then
        echo -e "${GREEN}🎉 Rollback successful and application is healthy!${NC}"
    else
        echo -e "${YELLOW}⚠️  Rollback completed but health check has issues${NC}"
        echo "🔍 Please verify the application manually: $DOMAIN_URL"
    fi
    
    # Log rollback event
    ssh_exec "
        echo \"\$(date '+%Y-%m-%d %H:%M:%S') - Rollback from $current_release to $target_release completed\" >> '$DOMAIN_PATH/rollback.log'
    "
}

# Main script logic
case "${2:-help}" in
    --list)
        list_releases
        ;;
    --current)
        show_current
        ;;
    --rollback)
        rollback "$3"
        ;;
    --health-check)
        health_check
        ;;
    --help|help)
        show_usage
        ;;
    *)
        show_usage
        exit 1
        ;;
esac