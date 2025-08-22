#!/bin/bash

# SocietyPal GitHub Secrets & Variables SIMPLIFIED Setup Script
# Usage: ./setup-societypal-secrets-simple.sh [staging|production|both|setup-server|verify]
# Author: GitHub Copilot AI Assistant  
# Date: August 18, 2025
# Approach: Minimal secrets + Server .env files

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

ENVIRONMENT=${1:-both}
REPO="rovony/SocietyPal"

echo -e "${BLUE}🔐 SocietyPal SIMPLIFIED Secrets Setup${NC}"
echo -e "${BLUE}=====================================${NC}"
echo ""
echo -e "📁 Repository: ${GREEN}$REPO${NC}"
echo -e "🎯 Target: ${GREEN}$ENVIRONMENT${NC}"
echo -e "📅 Date: $(date)"
echo -e "🎯 Approach: ${GREEN}Minimal secrets + server .env files${NC}"
echo ""

# Function to check prerequisites
check_prerequisites() {
    echo -e "${YELLOW}🔍 Checking prerequisites...${NC}"
    
    # Check if gh CLI is installed
    if ! command -v gh &> /dev/null; then
        echo -e "${RED}❌ GitHub CLI (gh) is not installed${NC}"
        echo -e "Install with: ${YELLOW}brew install gh${NC}"
        exit 1
    fi
    
    # Check if authenticated
    if ! gh auth status &> /dev/null; then
        echo -e "${RED}❌ Not authenticated with GitHub${NC}"
        echo -e "Run: ${YELLOW}gh auth login${NC}"
        exit 1
    fi
    
    # Check repository access
    if ! gh repo view $REPO &> /dev/null; then
        echo -e "${RED}❌ Cannot access repository $REPO${NC}"
        exit 1
    fi
    
    # Check if SSH key exists
    if [ ! -f ~/.ssh/id_ed25519 ]; then
        echo -e "${YELLOW}⚠️  SSH key not found at ~/.ssh/id_ed25519${NC}"
        echo -e "Generate with: ${YELLOW}ssh-keygen -t ed25519 -C 'github-actions-societypal'${NC}"
        export NO_SSH_KEY=true
    fi
    
    echo -e "${GREEN}✅ Prerequisites check passed${NC}"
    echo ""
}

# Function to verify environments exist
verify_environments() {
    echo -e "${YELLOW}🏗️  Verifying GitHub environments...${NC}"
    
    local envs_response=$(gh api repos/$REPO/environments 2>/dev/null || echo "[]")
    local staging_exists=$(echo "$envs_response" | jq -r '.environments[]? | select(.name=="staging") | .name' 2>/dev/null || echo "")
    local production_exists=$(echo "$envs_response" | jq -r '.environments[]? | select(.name=="production") | .name' 2>/dev/null || echo "")
    
    if [[ -z "$staging_exists" || -z "$production_exists" ]]; then
        echo -e "${RED}❌ Environments not found. Please create them first:${NC}"
        echo -e "1. Open: ${BLUE}https://github.com/$REPO/settings/environments${NC}"
        echo -e "2. Create 'staging' environment"
        echo -e "3. Create 'production' environment"
        echo ""
        echo -e "${YELLOW}Or run this script with 'verify' to check again${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}✅ Both staging and production environments found${NC}"
    echo ""
}

# Function to setup environment secrets (SIMPLIFIED)
setup_environment() {
    local env=$1
    echo -e "${YELLOW}🔧 Setting up $env environment secrets (SIMPLIFIED)...${NC}"
    
    # Only SSH Key (if available)
    if [ "$NO_SSH_KEY" != "true" ]; then
        gh secret set SERVER_SSH_KEY --env $env --repo $REPO < ~/.ssh/id_ed25519
        echo -e "${GREEN}✅ SSH key added to $env${NC}"
    else
        echo -e "${YELLOW}⚠️  Skipped SSH key for $env (not found)${NC}"
    fi
    
    echo -e "${GREEN}✅ $env environment secrets configured${NC}"
    echo ""
}

# Function to setup repository variables (public info)
setup_repository_variables() {
    echo -e "${YELLOW}📊 Setting up repository variables (public info)...${NC}"
    
    # Server connection info (not secret)
    gh variable set SERVER_HOST --value "31.97.195.108" --repo $REPO
    gh variable set SERVER_USER --value "u227177893" --repo $REPO
    gh variable set SERVER_PORT --value "65002" --repo $REPO
    
    # Deployment configuration
    gh variable set DEPLOYMENT_TIMEOUT --value "300" --repo $REPO
    gh variable set HEALTH_CHECK_RETRIES --value "3" --repo $REPO
    gh variable set KEEP_RELEASES --value "3" --repo $REPO
    
    # SocietyPal specific
    gh variable set APP_NAME --value "SocietyPal" --repo $REPO
    gh variable set DEPLOY_BRANCH_STAGING --value "main" --repo $REPO
    gh variable set DEPLOY_BRANCH_PRODUCTION --value "production" --repo $REPO
    
    echo -e "${GREEN}✅ Repository variables configured${NC}"
    echo ""
}

# Function to setup server .env files
setup_server_env_files() {
    echo -e "${YELLOW}🏠 Setting up server .env files...${NC}"
    
    if [ "$NO_SSH_KEY" = "true" ]; then
        echo -e "${RED}❌ Cannot setup server files without SSH key${NC}"
        return 1
    fi
    
    echo -e "Creating shared .env files on server..."
    
    ssh -p 65002 -i ~/.ssh/id_ed25519 u227177893@31.97.195.108 << 'ENDSSH'
    
    # Create staging .env
    mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared
    cat > /home/u227177893/domains/staging.societypal.com/deploy/shared/.env << 'STAGINGEOF'
APP_NAME="SocietyPal"
APP_ENV=staging
APP_KEY=base64:$(openssl rand -base64 32)
APP_DEBUG=true
APP_URL=https://staging.societypal.com

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u227177893_s_zaj_socpal_d
DB_USERNAME=u227177893_s_zaj_socpal_u
DB_PASSWORD="V0Z^G=I2:=r^f2"

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
STAGINGEOF

    # Create production .env
    mkdir -p /home/u227177893/domains/societypal.com/deploy/shared
    cat > /home/u227177893/domains/societypal.com/deploy/shared/.env << 'PRODEOF'
APP_NAME="SocietyPal"
APP_ENV=production
APP_KEY=base64:$(openssl rand -base64 32)
APP_DEBUG=false
APP_URL=https://societypal.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u227177893_p_zaj_socpal_d
DB_USERNAME=u227177893_p_zaj_socpal_u
DB_PASSWORD="t5TmP9\$[iG7hu2eYRWUIWH@IRF2"

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
PRODEOF

    # Set proper permissions
    chmod 600 /home/u227177893/domains/*/deploy/shared/.env
    
    echo "✅ Server .env files created successfully"
    
ENDSSH
    
    echo -e "${GREEN}✅ Server .env files configured${NC}"
    echo ""
}

# Function to verify setup
verify_setup() {
    echo -e "${YELLOW}🔍 Verifying setup...${NC}"
    
    echo -e "Repository secrets:"
    gh secret list --repo $REPO
    
    echo ""
    echo -e "Repository variables:"
    gh variable list --repo $REPO
    
    echo ""
    echo -e "${BLUE}📝 Manual verification needed for environment secrets:${NC}"
    echo -e "Visit: ${BLUE}https://github.com/$REPO/settings/environments${NC}"
    
    echo ""
    echo -e "${GREEN}✅ Setup verification complete${NC}"
}

# Function to display next steps
show_next_steps() {
    echo ""
    echo -e "${BLUE}🎉 SocietyPal SIMPLIFIED Setup Complete!${NC}"
    echo -e "${BLUE}=======================================${NC}"
    echo ""
    echo -e "${GREEN}✅ What's been configured:${NC}"
    echo -e "   • SSH key for secure deployment"
    echo -e "   • Server connection variables (public info)"
    echo -e "   • Repository deployment variables"
    echo -e "   • Server .env files with database credentials"
    echo ""
    echo -e "${GREEN}✅ Benefits of this approach:${NC}"
    echo -e "   • 🔥 Only 1 secret instead of 16"
    echo -e "   • 🛡️ Better security (no DB passwords in CI/CD)"
    echo -e "   • 🚀 Easier maintenance (change .env on server only)"
    echo -e "   • 📋 Laravel best practices"
    echo ""
    echo -e "${YELLOW}📝 Next Steps:${NC}"
    echo -e "1. Verify environment secrets in GitHub web UI:"
    echo -e "   ${BLUE}https://github.com/$REPO/settings/environments${NC}"
    echo ""
    echo -e "2. Test your GitHub Actions workflows:"
    echo -e "   ${YELLOW}gh workflow run 'SocietyPal Automated Deployment' --ref main${NC}"
    echo ""
    echo -e "3. Monitor deployment runs:"
    echo -e "   ${YELLOW}gh run list --limit 5${NC}"
    echo ""
    if [ "$NO_SSH_KEY" = "true" ]; then
        echo -e "${RED}⚠️  Don't forget to set up SSH key:${NC}"
        echo -e "   ${YELLOW}ssh-keygen -t ed25519 -C 'github-actions-societypal'${NC}"
        echo -e "   ${YELLOW}gh secret set SERVER_SSH_KEY --env staging < ~/.ssh/id_ed25519${NC}"
        echo -e "   ${YELLOW}gh secret set SERVER_SSH_KEY --env production < ~/.ssh/id_ed25519${NC}"
        echo ""
    fi
}

# Main execution logic
main() {
    case $ENVIRONMENT in
        verify)
            check_prerequisites
            verify_environments
            verify_setup
            ;;
        setup-server)
            check_prerequisites
            setup_server_env_files
            ;;
        staging)
            check_prerequisites
            verify_environments
            setup_environment "staging"
            setup_repository_variables
            verify_setup
            show_next_steps
            ;;
        production)
            check_prerequisites
            verify_environments
            setup_environment "production"
            setup_repository_variables
            verify_setup
            show_next_steps
            ;;
        both)
            check_prerequisites
            verify_environments
            setup_environment "staging"
            setup_environment "production"
            setup_repository_variables
            verify_setup
            show_next_steps
            ;;
        *)
            echo -e "${RED}❌ Invalid environment. Use: staging, production, both, setup-server, or verify${NC}"
            echo ""
            echo -e "${YELLOW}Usage examples:${NC}"
            echo -e "  $0 both         # Set up both environments (recommended)"
            echo -e "  $0 staging      # Set up staging only"
            echo -e "  $0 production   # Set up production only"
            echo -e "  $0 setup-server # Create .env files on server"
            echo -e "  $0 verify       # Verify current setup"
            exit 1
            ;;
    esac
}

# Run main function
main "$@"
