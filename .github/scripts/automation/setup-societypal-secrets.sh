#!/bin/bash

# SocietyPal GitHub Secrets & Variables Complete Setup Script
# Usage: ./setup-societypal-secrets.sh [staging|production|both|verify]
# Author: GitHub Copilot AI Assistant
# Date: August 18, 2025

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

ENVIRONMENT=${1:-both}
REPO="rovony/SocietyPal"

echo -e "${BLUE}🔐 SocietyPal GitHub Secrets & Variables Setup${NC}"
echo -e "${BLUE}================================================${NC}"
echo ""
echo -e "📁 Repository: ${GREEN}$REPO${NC}"
echo -e "🎯 Target: ${GREEN}$ENVIRONMENT${NC}"
echo -e "📅 Date: $(date)"
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

# Function to setup environment secrets
setup_environment() {
    local env=$1
    echo -e "${YELLOW}🔧 Setting up $env environment secrets...${NC}"
    
    # Server connection secrets
    echo "31.97.195.108" | gh secret set SERVER_HOST --env $env --repo $REPO
    echo "u227177893" | gh secret set SERVER_USER --env $env --repo $REPO
    echo "65002" | gh secret set SERVER_PORT --env $env --repo $REPO
    
    # Database secrets (environment-specific)
    echo "127.0.0.1" | gh secret set DB_HOST --env $env --repo $REPO
    echo "3306" | gh secret set DB_PORT --env $env --repo $REPO
    
    if [ "$env" = "staging" ]; then
        echo "u227177893_s_zaj_socpal_d" | gh secret set DB_DATABASE --env $env --repo $REPO
        echo "u227177893_s_zaj_socpal_u" | gh secret set DB_USERNAME --env $env --repo $REPO
        echo "V0Z^G=I2:=r^f2" | gh secret set DB_PASSWORD --env $env --repo $REPO
    elif [ "$env" = "production" ]; then
        echo "u227177893_p_zaj_socpal_d" | gh secret set DB_DATABASE --env $env --repo $REPO
        echo "u227177893_p_zaj_socpal_u" | gh secret set DB_USERNAME --env $env --repo $REPO
        echo "t5TmP9\$[iG7hu2eYRWUIWH@IRF2" | gh secret set DB_PASSWORD --env $env --repo $REPO
    fi
    
    # SSH Key (if available)
    if [ "$NO_SSH_KEY" != "true" ]; then
        gh secret set SERVER_SSH_KEY --env $env --repo $REPO < ~/.ssh/id_ed25519
        echo -e "${GREEN}✅ SSH key added to $env${NC}"
    else
        echo -e "${YELLOW}⚠️  Skipped SSH key for $env (not found)${NC}"
    fi
    
    echo -e "${GREEN}✅ $env environment secrets configured${NC}"
    echo ""
}

# Function to setup repository variables
setup_repository_variables() {
    echo -e "${YELLOW}📊 Setting up repository variables...${NC}"
    
    # Deployment configuration
    gh variable set DEPLOYMENT_TIMEOUT --value "300" --repo $REPO
    gh variable set HEALTH_CHECK_RETRIES --value "3" --repo $REPO
    gh variable set KEEP_RELEASES --value "3" --repo $REPO
    
    # Laravel environments
    gh variable set LARAVEL_ENV_STAGING --value "staging" --repo $REPO
    gh variable set LARAVEL_ENV_PRODUCTION --value "production" --repo $REPO
    
    # SocietyPal specific
    gh variable set APP_NAME --value "SocietyPal" --repo $REPO
    gh variable set DEPLOY_BRANCH_STAGING --value "main" --repo $REPO
    gh variable set DEPLOY_BRANCH_PRODUCTION --value "production" --repo $REPO
    
    echo -e "${GREEN}✅ Repository variables configured${NC}"
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
    echo -e "${BLUE}🎉 SocietyPal Secrets Setup Complete!${NC}"
    echo -e "${BLUE}======================================${NC}"
    echo ""
    echo -e "${GREEN}✅ What's been configured:${NC}"
    echo -e "   • Server connection secrets for both environments"
    echo -e "   • Database credentials for staging and production"
    echo -e "   • SSH keys (if available)"
    echo -e "   • Repository deployment variables"
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
            echo -e "${RED}❌ Invalid environment. Use: staging, production, both, or verify${NC}"
            echo ""
            echo -e "${YELLOW}Usage examples:${NC}"
            echo -e "  $0 both       # Set up both environments (recommended)"
            echo -e "  $0 staging    # Set up staging only"
            echo -e "  $0 production # Set up production only"
            echo -e "  $0 verify     # Verify current setup"
            exit 1
            ;;
    esac
}

# Run main function
main "$@"
