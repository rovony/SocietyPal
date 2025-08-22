# 🔐 GitHub Secrets & Variables Management Guide

**Managing SocietyPal Deployment Secrets via CLI in VS Code**

## 📋 Quick Reference

### Main Takeaway

**Yes, you can add and manage GitHub Actions environment secrets and variables directly from VS Code terminal using GitHub CLI (`gh`).** This eliminates the need for web interface management and integrates perfectly with your development workflow.

### Prerequisites Checklist

-   [ ] GitHub CLI (`gh`) installed
-   [ ] Authenticated with `gh auth login`
-   [ ] Repository access permissions
-   [ ] VS Code with integrated terminal

---

## 🚀 Installation & Setup

### Step 1: Install GitHub CLI

**On macOS (your setup):**

```bash
# Install via Homebrew (recommended)
brew install gh

# Verify installation
gh --version
```

**Alternative installation methods:**

```bash
# Via MacPorts
sudo port install gh

# Download from releases
# Visit: https://github.com/cli/cli/releases
```

### Step 2: Authenticate with GitHub

```bash
# Authenticate with GitHub
gh auth login

# Choose:
# 1. GitHub.com
# 2. HTTPS
# 3. Yes (authenticate Git with GitHub credentials)
# 4. Login with a web browser (recommended)
```

### Step 3: Repository Identification & Setup

#### Navigate to Your Project Directory

```bash
# Navigate to your SocietyPal project
cd /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root
```

#### Identify & Verify Repository Connection

```bash
# Check current git remote (should show GitHub repository)
git remote -v

# Expected output:
# origin  https://github.com/rovony/SocietyPal.git (fetch)
# origin  https://github.com/rovony/SocietyPal.git (push)

# Verify repository connection with GitHub CLI
gh repo view

# Expected output should show:
# rovony/SocietyPal
# Repository details...
```

#### Check Current Repository Status

```bash
# Check current branch
git branch --show-current

# Check repository status
git status

# List current secrets (to verify access)
gh secret list

# List current variables
gh variable list
```

#### Verify Environments Exist

Before adding secrets, ensure your environments are created:

```bash
# Check if environments exist (via GitHub API)
gh api repos/:owner/:repo/environments

# If environments don't exist, create them via web UI:
# 1. Go to: https://github.com/rovony/SocietyPal/settings/environments
# 2. Click "New environment"
# 3. Create "staging" environment
# 4. Create "production" environment
```

---

## 🎯 Step-by-Step Implementation Guide

### Phase 1: Environment Setup Verification

#### Step 1.1: Verify Prerequisites

```bash
# Check if GitHub CLI is installed
gh --version
# Expected: gh version 2.x.x (or newer)

# Check authentication status
gh auth status
# Expected: Logged in to github.com as [your-username]

# Verify you're in the correct directory
pwd
# Expected: /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root

# Check repository connection
gh repo view
# Expected: rovony/SocietyPal repository details
```

#### Step 1.2: Create GitHub Environments (Web UI Required)

**Important:** Environments must be created via GitHub web interface first:

1. **Open Repository Settings:**

    ```bash
    # Open repository in browser
    gh repo view --web
    ```

2. **Navigate to Environments:**

    - Click "Settings" tab
    - Click "Environments" in left sidebar
    - Click "New environment"

3. **Create Staging Environment:**

    - Name: `staging`
    - Click "Configure environment"
    - Set protection rules if needed
    - Click "Save protection rules"

4. **Create Production Environment:**
    - Name: `production`
    - Click "Configure environment"
    - Set protection rules (recommended for production)
    - Click "Save protection rules"

#### Step 1.3: Verify Environments Created

```bash
# Verify environments exist
gh api repos/rovony/SocietyPal/environments

# Expected output should show both environments:
# [
#   {
#     "id": ...,
#     "name": "staging",
#     ...
#   },
#   {
#     "id": ...,
#     "name": "production",
#     ...
#   }
# ]
```

### Phase 2: Secrets Implementation

#### Step 2.1: Test Secret Creation (Dry Run)

```bash
# Test with a simple secret first
echo "test_value" | gh secret set TEST_SECRET --env staging

# Verify it was created
gh secret list

# Clean up test secret
gh secret delete TEST_SECRET --env staging
```

#### Step 2.2: Implement Staging Environment Secrets

```bash
echo "🔧 Setting up Staging Environment Secrets..."

# Server connection secrets
echo "31.97.195.108" | gh secret set SERVER_HOST --env staging
echo "u227177893" | gh secret set SERVER_USER --env staging
echo "65002" | gh secret set SERVER_PORT --env staging

# Database secrets
echo "127.0.0.1" | gh secret set DB_HOST --env staging
echo "3306" | gh secret set DB_PORT --env staging
echo "u227177893_s_zaj_socpal_d" | gh secret set DB_DATABASE --env staging
echo "u227177893_s_zaj_socpal_u" | gh secret set DB_USERNAME --env staging
echo "V0Z^G=I2:=r^f2" | gh secret set DB_PASSWORD --env staging

echo "✅ Staging secrets configured"
```

#### Step 2.3: Implement Production Environment Secrets

```bash
echo "🔧 Setting up Production Environment Secrets..."

# Server connection secrets
echo "31.97.195.108" | gh secret set SERVER_HOST --env production
echo "u227177893" | gh secret set SERVER_USER --env production
echo "65002" | gh secret set SERVER_PORT --env production

# Database secrets
echo "127.0.0.1" | gh secret set DB_HOST --env production
echo "3306" | gh secret set DB_PORT --env production
echo "u227177893_p_zaj_socpal_d" | gh secret set DB_DATABASE --env production
echo "u227177893_p_zaj_socpal_u" | gh secret set DB_USERNAME --env production
echo "t5TmP9\$[iG7hu2eYRWUIWH@IRF2" | gh secret set DB_PASSWORD --env production

echo "✅ Production secrets configured"
```

#### Step 2.4: Add SSH Keys

```bash
echo "🔑 Setting up SSH Keys..."

# Check if SSH key exists
if [ -f ~/.ssh/id_ed25519 ]; then
    echo "SSH key found, adding to environments..."

    # Add to staging
    gh secret set SERVER_SSH_KEY --env staging < ~/.ssh/id_ed25519

    # Add to production
    gh secret set SERVER_SSH_KEY --env production < ~/.ssh/id_ed25519

    echo "✅ SSH keys configured"
else
    echo "⚠️  SSH key not found at ~/.ssh/id_ed25519"
    echo "Please generate SSH key first:"
    echo "ssh-keygen -t ed25519 -C 'github-actions-societypal'"
fi
```

### Phase 3: Verification & Testing

#### Step 3.1: Verify All Secrets

```bash
echo "🔍 Verifying secrets configuration..."

# List all repository secrets
echo "Repository secrets:"
gh secret list

# Note: Environment-specific secrets require API calls to verify
echo ""
echo "To verify environment secrets, check GitHub web UI:"
echo "https://github.com/rovony/SocietyPal/settings/environments"
```

#### Step 3.2: Test Repository Variables

```bash
echo "📊 Setting up repository variables..."

# Set deployment configuration
gh variable set DEPLOYMENT_TIMEOUT --value "300"
gh variable set HEALTH_CHECK_RETRIES --value "3"
gh variable set KEEP_RELEASES --value "3"

# Laravel environment
gh variable set LARAVEL_ENV_STAGING --value "staging"
gh variable set LARAVEL_ENV_PRODUCTION --value "production"

echo "✅ Repository variables configured"

# Verify variables
echo "Repository variables:"
gh variable list
```

### Phase 4: Create Automated Setup Script

#### Step 4.1: Create Scripts Directory

```bash
# Create scripts directory if it doesn't exist
mkdir -p .github/scripts

# Make sure we're in the right location
cd /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root
```

#### Step 4.2: Create Complete Setup Script

```bash
# Create the comprehensive setup script
cat > .github/scripts/setup-societypal-secrets.sh << 'EOF'
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
EOF

# Make script executable
chmod +x .github/scripts/setup-societypal-secrets.sh

echo "✅ Setup script created at: .github/scripts/setup-societypal-secrets.sh"
```

---

## 🚀 Let's Do It! - Complete Implementation

### Quick Start (Recommended)

```bash
# 1. Navigate to your project
cd /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root

# 2. Create the setup script (if not already done)
mkdir -p .github/scripts

# 3. Run the complete setup
./.github/scripts/setup-societypal-secrets.sh both
```

### Step-by-Step Manual Implementation

#### Option A: Use the Automated Script (Recommended)

```bash
# Step 1: Verify everything is ready
./.github/scripts/setup-societypal-secrets.sh verify

# Step 2: Run complete setup
./.github/scripts/setup-societypal-secrets.sh both

# Step 3: Verify results
gh secret list
gh variable list
```

#### Option B: Manual Step-by-Step

```bash
# Phase 1: Prerequisites
gh --version              # Check GitHub CLI
gh auth status           # Check authentication
gh repo view             # Verify repository access

# Phase 2: Environment Secrets - Staging
echo "31.97.195.108" | gh secret set SERVER_HOST --env staging
echo "u227177893" | gh secret set SERVER_USER --env staging
echo "65002" | gh secret set SERVER_PORT --env staging
echo "127.0.0.1" | gh secret set DB_HOST --env staging
echo "3306" | gh secret set DB_PORT --env staging
echo "u227177893_s_zaj_socpal_d" | gh secret set DB_DATABASE --env staging
echo "u227177893_s_zaj_socpal_u" | gh secret set DB_USERNAME --env staging
echo "V0Z^G=I2:=r^f2" | gh secret set DB_PASSWORD --env staging

# Phase 3: Environment Secrets - Production
echo "31.97.195.108" | gh secret set SERVER_HOST --env production
echo "u227177893" | gh secret set SERVER_USER --env production
echo "65002" | gh secret set SERVER_PORT --env production
echo "127.0.0.1" | gh secret set DB_HOST --env production
echo "3306" | gh secret set DB_PORT --env production
echo "u227177893_p_zaj_socpal_d" | gh secret set DB_DATABASE --env production
echo "u227177893_p_zaj_socpal_u" | gh secret set DB_USERNAME --env production
echo "t5TmP9\$[iG7hu2eYRWUIWH@IRF2" | gh secret set DB_PASSWORD --env production

# Phase 4: SSH Keys
gh secret set SERVER_SSH_KEY --env staging < ~/.ssh/id_ed25519
gh secret set SERVER_SSH_KEY --env production < ~/.ssh/id_ed25519

# Phase 5: Repository Variables
gh variable set DEPLOYMENT_TIMEOUT --value "300"
gh variable set HEALTH_CHECK_RETRIES --value "3"
gh variable set KEEP_RELEASES --value "3"
gh variable set LARAVEL_ENV_STAGING --value "staging"
gh variable set LARAVEL_ENV_PRODUCTION --value "production"

# Phase 6: Verification
gh secret list
gh variable list
```

### Pre-Implementation Checklist

Before running the setup, ensure:

-   [ ] **GitHub CLI installed**: `brew install gh`
-   [ ] **Authenticated**: `gh auth login`
-   [ ] **In correct directory**: SocietyPal project root
-   [ ] **Repository access**: `gh repo view` works
-   [ ] **Environments created**: via GitHub web UI
-   [ ] **SSH key exists**: `~/.ssh/id_ed25519` file present

### Troubleshooting Quick Fixes

#### If Authentication Fails

```bash
gh auth logout
gh auth login
# Choose: GitHub.com, HTTPS, Yes, Login with browser
```

#### If Repository Not Found

```bash
# Check remote URL
git remote -v

# Should show: https://github.com/rovony/SocietyPal.git
# If not, fix with:
git remote set-url origin https://github.com/rovony/SocietyPal.git
```

#### If Environments Don't Exist

```bash
# Open repository settings
gh repo view --web
# Navigate to Settings > Environments
# Create "staging" and "production" environments
```

#### If SSH Key Missing

```bash
# Generate new SSH key
ssh-keygen -t ed25519 -C "github-actions-societypal"

# Add to ssh-agent
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_ed25519

# Then re-run the setup script
```

---

## 🎯 Ready to Implement? Let's Do It Now!

### Immediate Action Plan

**Step 1: Check Prerequisites (30 seconds)**

```bash
# Check GitHub CLI
gh --version

# Check authentication
gh auth status

# Check repository access
gh repo view
```

**Step 2: Create Environments (2 minutes)**

```bash
# Open repository settings in browser
gh repo view --web

# Navigate to Settings > Environments
# Create "staging" and "production" environments
```

**Step 3: Run Complete Setup (1 minute)**

```bash
# Navigate to project root
cd /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root

# Run the automated setup
./.github/scripts/setup-societypal-secrets.sh both
```

**Step 4: Verify Success (30 seconds)**

```bash
# Check secrets and variables
gh secret list
gh variable list

# Open GitHub environments to verify
open "https://github.com/rovony/SocietyPal/settings/environments"
```

### Expected Output

When successful, you should see:

```
🔐 SocietyPal GitHub Secrets & Variables Setup
================================================

📁 Repository: rovony/SocietyPal
🎯 Target: both
📅 Date: [current date]

🔍 Checking prerequisites...
✅ Prerequisites check passed

🏗️  Verifying GitHub environments...
✅ Both staging and production environments found

🔧 Setting up staging environment secrets...
✅ SSH key added to staging
✅ staging environment secrets configured

🔧 Setting up production environment secrets...
✅ SSH key added to production
✅ production environment secrets configured

📊 Setting up repository variables...
✅ Repository variables configured

🔍 Verifying setup...
[Lists of secrets and variables]

🎉 SocietyPal Secrets Setup Complete!
```

### Quick Test Commands

After setup, test with:

```bash
# List all configured secrets
gh secret list

# List all configured variables
gh variable list

# Test workflow trigger (if workflows exist)
gh workflow list

# Monitor recent runs
gh run list --limit 3
```

---

## 📞 Support & Troubleshooting

### Common Issues Resolution

| Issue                | Command                 | Solution                           |
| -------------------- | ----------------------- | ---------------------------------- |
| Not authenticated    | `gh auth login`         | Follow browser authentication      |
| Missing environments | `gh repo view --web`    | Create via Settings > Environments |
| SSH key missing      | `ssh-keygen -t ed25519` | Generate new key                   |
| Permission denied    | `gh auth refresh`       | Refresh GitHub token               |
| Repository not found | `git remote -v`         | Verify repository URL              |

### Emergency Reset

If something goes wrong:

```bash
# Reset authentication
gh auth logout
gh auth login

# Verify repository connection
gh repo view rovony/SocietyPal

# Re-run setup
./.github/scripts/setup-societypal-secrets.sh both
```

### Get Help

```bash
# GitHub CLI help
gh help secret
gh help variable

# Script help
./.github/scripts/setup-societypal-secrets.sh
```

---

## 🏁 Final Summary

**You now have a complete, ready-to-use system for managing GitHub secrets and variables for SocietyPal deployments:**

✅ **Complete Guide**: Step-by-step instructions for first-time setup  
✅ **Automated Script**: One-command setup for all secrets and variables  
✅ **Repository Identification**: Clear steps to verify GitHub connection  
✅ **Environment Management**: Staging and production configurations  
✅ **Error Handling**: Troubleshooting for common issues  
✅ **Verification**: Commands to confirm successful setup

**Next Actions:**

1. **Now**: Run the setup script to configure your secrets
2. **Today**: Test deployment workflows
3. **Ongoing**: Use CLI commands for maintenance and updates

**The setup script handles everything automatically - just run:**

```bash
./.github/scripts/setup-societypal-secrets.sh both
```

---

_Last updated: August 18, 2025_  
_SocietyPal Project - GitHub Actions Integration_  
_Complete Implementation Guide with AI Assistant_

## 🔑 Environment Secrets Management

### Quick Commands Reference

```bash
# Set environment secret
gh secret set SECRET_NAME --env ENVIRONMENT_NAME

# Set repository secret
gh secret set SECRET_NAME

# List all secrets
gh secret list

# Delete secret
gh secret delete SECRET_NAME --env ENVIRONMENT_NAME
```

### SocietyPal-Specific Environment Setup

#### For Staging Environment

```bash
# Server connection secrets
echo "31.97.195.108" | gh secret set SERVER_HOST --env staging
echo "u227177893" | gh secret set SERVER_USER --env staging
echo "65002" | gh secret set SERVER_PORT --env staging

# Database secrets
echo "127.0.0.1" | gh secret set DB_HOST --env staging
echo "3306" | gh secret set DB_PORT --env staging
echo "u227177893_s_zaj_socpal_d" | gh secret set DB_DATABASE --env staging
echo "u227177893_s_zaj_socpal_u" | gh secret set DB_USERNAME --env staging
echo "V0Z^G=I2:=r^f2" | gh secret set DB_PASSWORD --env staging
```

#### For Production Environment

```bash
# Server connection secrets
echo "31.97.195.108" | gh secret set SERVER_HOST --env production
echo "u227177893" | gh secret set SERVER_USER --env production
echo "65002" | gh secret set SERVER_PORT --env production

# Database secrets
echo "127.0.0.1" | gh secret set DB_HOST --env production
echo "3306" | gh secret set DB_PORT --env production
echo "u227177893_p_zaj_socpal_d" | gh secret set DB_DATABASE --env production
echo "u227177893_p_zaj_socpal_u" | gh secret set DB_USERNAME --env production
echo "t5TmP9\$[iG7hu2eYRWUIWH@IRF2" | gh secret set DB_PASSWORD --env production
```

#### SSH Key Setup

```bash
# Add SSH private key (multiline)
gh secret set SERVER_SSH_KEY --env staging < ~/.ssh/id_ed25519
gh secret set SERVER_SSH_KEY --env production < ~/.ssh/id_ed25519

# Or interactively (will prompt for input)
gh secret set SERVER_SSH_KEY --env staging
# Paste your private key when prompted
```

#### CodeCanyon License (if applicable)

```bash
# Set license content
echo "your-codecanyon-license-here" | gh secret set LICENSE_CONTENT --env staging
echo "your-codecanyon-license-here" | gh secret set LICENSE_CONTENT --env production
```

---

## 📊 Variables Management

### Repository Variables (Non-Secret)

```bash
# Set deployment configuration
gh variable set DEPLOYMENT_TIMEOUT --value "300"
gh variable set HEALTH_CHECK_RETRIES --value "3"
gh variable set KEEP_RELEASES --value "3"

# Laravel environment
gh variable set LARAVEL_ENV_STAGING --value "staging"
gh variable set LARAVEL_ENV_PRODUCTION --value "production"
```

### ⚠️ Environment Variables Limitation

**Current GitHub CLI Limitation (Aug 2025):**

-   ✅ Environment **secrets**: Supported via CLI
-   ❌ Environment **variables**: Web UI only

**For environment-specific variables, use the web interface:**

1. Go to `https://github.com/[USERNAME]/[REPO]/settings/environments`
2. Select environment → Variables section
3. Add variables manually

---

## 🛠️ Practical VS Code Workflow

### Create a Secrets Setup Script

Create a helper script for easy setup:

```bash
# Create setup script
touch .github/scripts/setup-secrets.sh
chmod +x .github/scripts/setup-secrets.sh
```

**Script Content (`setup-secrets.sh`):**

```bash
#!/bin/bash

# SocietyPal GitHub Secrets Setup Script
# Usage: ./setup-secrets.sh [staging|production|both]

set -e

ENVIRONMENT=${1:-both}
REPO=$(gh repo view --json nameWithOwner -q .nameWithOwner)

echo "🔐 Setting up GitHub secrets for SocietyPal"
echo "📁 Repository: $REPO"
echo "🎯 Environment: $ENVIRONMENT"
echo ""

setup_environment() {
    local env=$1
    echo "Setting up $env environment..."

    # Server connection
    echo "31.97.195.108" | gh secret set SERVER_HOST --env $env
    echo "u227177893" | gh secret set SERVER_USER --env $env
    echo "65002" | gh secret set SERVER_PORT --env $env

    # Database (environment-specific)
    echo "127.0.0.1" | gh secret set DB_HOST --env $env
    echo "3306" | gh secret set DB_PORT --env $env

    if [ "$env" = "staging" ]; then
        echo "u227177893_s_zaj_socpal_d" | gh secret set DB_DATABASE --env $env
        echo "u227177893_s_zaj_socpal_u" | gh secret set DB_USERNAME --env $env
        echo "V0Z^G=I2:=r^f2" | gh secret set DB_PASSWORD --env $env
    elif [ "$env" = "production" ]; then
        echo "u227177893_p_zaj_socpal_d" | gh secret set DB_DATABASE --env $env
        echo "u227177893_p_zaj_socpal_u" | gh secret set DB_USERNAME --env $env
        echo "t5TmP9\$[iG7hu2eYRWUIWH@IRF2" | gh secret set DB_PASSWORD --env $env
    fi

    echo "✅ $env environment secrets configured"
}

# Setup SSH key (interactive)
setup_ssh_key() {
    echo ""
    echo "🔑 SSH Key Setup Required"
    echo "Please run manually for each environment:"
    echo "  gh secret set SERVER_SSH_KEY --env staging < ~/.ssh/id_ed25519"
    echo "  gh secret set SERVER_SSH_KEY --env production < ~/.ssh/id_ed25519"
    echo ""
}

# Main execution
case $ENVIRONMENT in
    staging)
        setup_environment "staging"
        ;;
    production)
        setup_environment "production"
        ;;
    both)
        setup_environment "staging"
        setup_environment "production"
        ;;
    *)
        echo "❌ Invalid environment. Use: staging, production, or both"
        exit 1
        ;;
esac

setup_ssh_key

echo "🎉 Secret setup complete!"
echo "📝 Don't forget to:"
echo "   1. Set up SSH keys manually (shown above)"
echo "   2. Configure environment variables via web UI if needed"
echo "   3. Test deployment workflows"
```

### Usage in VS Code

```bash
# Navigate to project directory
cd /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root

# Run setup script
./.github/scripts/setup-secrets.sh both

# Or for specific environment
./.github/scripts/setup-secrets.sh staging
./.github/scripts/setup-secrets.sh production
```

---

## 🔍 Verification & Management

### List All Secrets

```bash
# List repository secrets
gh secret list

# List environment secrets (requires web UI or API)
gh api repos/:owner/:repo/environments/staging/secrets
gh api repos/:owner/:repo/environments/production/secrets
```

### Update Existing Secrets

```bash
# Update secret (same command as create)
echo "new_value" | gh secret set SECRET_NAME --env staging

# Update from file
gh secret set SERVER_SSH_KEY --env production < ~/.ssh/id_ed25519
```

### Delete Secrets

```bash
# Delete environment secret
gh secret delete SECRET_NAME --env staging

# Delete repository secret
gh secret delete SECRET_NAME
```

### Backup Secrets List

```bash
# Create backup of secret names (not values)
gh secret list > .github/secrets-backup.txt
```

---

## 🚨 Common Issues & Solutions

### Issue: Authentication Failed

```bash
# Re-authenticate
gh auth logout
gh auth login

# Verify authentication
gh auth status
```

### Issue: Permission Denied

```bash
# Check repository access
gh repo view

# Verify you're in correct repository
git remote -v
```

### Issue: Environment Not Found

```bash
# List environments (requires web UI or API)
gh api repos/:owner/:repo/environments

# Create environment via web UI first:
# https://github.com/[USERNAME]/[REPO]/settings/environments
```

### Issue: Special Characters in Secrets

```bash
# Use single quotes to preserve special characters
echo 't5TmP9$[iG7hu2eYRWUIWH@IRF2' | gh secret set DB_PASSWORD --env production

# Or escape special characters
echo "t5TmP9\$[iG7hu2eYRWUIWH@IRF2" | gh secret set DB_PASSWORD --env production
```

---

## 📋 Quick Command Cheatsheet

### Essential Commands

```bash
# Authentication
gh auth login                              # Login to GitHub
gh auth status                             # Check auth status

# Secrets
gh secret set NAME --env ENV               # Set environment secret
gh secret set NAME                         # Set repository secret
gh secret list                             # List secrets
gh secret delete NAME --env ENV            # Delete environment secret

# Variables
gh variable set NAME --value VALUE         # Set repository variable
gh variable list                           # List variables
gh variable delete NAME                    # Delete variable

# Repository
gh repo view                               # View repository info
gh workflow list                           # List workflows
gh run list                                # List workflow runs
```

### SocietyPal-Specific Quick Setup

```bash
# Complete setup in one go
./.github/scripts/setup-secrets.sh both

# Manual SSH key setup
gh secret set SERVER_SSH_KEY --env staging < ~/.ssh/id_ed25519
gh secret set SERVER_SSH_KEY --env production < ~/.ssh/id_ed25519

# Verify setup
gh secret list
gh workflow run "SocietyPal Automated Deployment" --ref main
```

---

## 🎯 Integration with SocietyPal Workflows

### Workflow Trigger Testing

```bash
# Test staging deployment
gh workflow run "SocietyPal Automated Deployment" --ref main

# Test production deployment
gh workflow run "SocietyPal Automated Deployment" --ref production

# Manual deployment
gh workflow run "Emergency Manual Deployment"
```

### Monitor Deployment

```bash
# Watch latest run
gh run watch

# List recent runs
gh run list --limit 5

# View specific run
gh run view [RUN_ID]
```

---

## 🔄 Ongoing Maintenance

### Regular Tasks

1. **Rotate SSH Keys:**

    ```bash
    # Generate new key
    ssh-keygen -t ed25519 -C "github-actions-societypal-$(date +%Y%m%d)"

    # Update secret
    gh secret set SERVER_SSH_KEY --env staging < ~/.ssh/new_key
    gh secret set SERVER_SSH_KEY --env production < ~/.ssh/new_key
    ```

2. **Update Database Credentials:**

    ```bash
    echo "new_password" | gh secret set DB_PASSWORD --env production
    ```

3. **Backup Configuration:**
    ```bash
    # Document current setup
    gh secret list > .github/docs/secrets-$(date +%Y%m%d).txt
    gh variable list >> .github/docs/secrets-$(date +%Y%m%d).txt
    ```

### Emergency Procedures

```bash
# Quick rollback
gh workflow run "Emergency Manual Deployment" --field environment=production --field action=rollback

# Disable automatic deployments
gh variable set AUTO_DEPLOY_ENABLED --value "false"

# Re-enable after fixing issues
gh variable set AUTO_DEPLOY_ENABLED --value "true"
```

---

## 🔧 Advanced Usage

### Bulk Secret Management

```bash
# Set multiple secrets from file
while IFS='=' read -r key value; do
    echo "$value" | gh secret set "$key" --env staging
done < secrets.env
```

### Environment-Specific Configurations

```bash
# Create environment-specific setup files
cat > staging-secrets.env << EOF
APP_ENV=staging
APP_DEBUG=true
LOG_LEVEL=debug
EOF

cat > production-secrets.env << EOF
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error
EOF
```

### API Integration

```bash
# Use GitHub API directly for advanced operations
gh api --method POST /repos/:owner/:repo/environments/staging/secrets/SECRET_NAME \
    --field encrypted_value="$(echo 'secret_value' | base64)" \
    --field key_id="$(gh api /repos/:owner/:repo/actions/secrets/public-key --jq .key_id)"
```

---

## 📊 CLI Capabilities Summary

| Type     | Scope       | CLI Support | Command Example                                  |
| -------- | ----------- | ----------- | ------------------------------------------------ |
| Secret   | Environment | ✅ Yes      | `gh secret set NAME --env ENV --repo OWNER/REPO` |
| Secret   | Repository  | ✅ Yes      | `gh secret set NAME --repo OWNER/REPO`           |
| Variable | Repository  | ✅ Yes      | `gh variable set NAME --repo OWNER/REPO`         |
| Variable | Environment | ❌ Web UI   | Use GitHub web interface                         |

---

## 🎓 Learning Resources

### GitHub CLI Documentation

-   [GitHub CLI Manual](https://cli.github.com/manual/)
-   [gh secret commands](https://cli.github.com/manual/gh_secret)
-   [gh variable commands](https://cli.github.com/manual/gh_variable)

### GitHub Actions Documentation

-   [Encrypted secrets](https://docs.github.com/en/actions/security-guides/encrypted-secrets)
-   [Variables](https://docs.github.com/en/actions/learn-github-actions/variables)
-   [Environment protection rules](https://docs.github.com/en/actions/deployment/targeting-different-environments/using-environments-for-deployment)

### SocietyPal-Specific

-   [Deployment Implementation Plan](./.github/SocietyPal-GitHub-Actions-Implementation-Plan.md)
-   [Customization Protection System](./copilot-instructions.md)

---

## 🏁 Summary

**This guide provides a complete CLI-based approach to managing GitHub secrets and variables for SocietyPal deployments. Key benefits:**

-   ✅ **Efficiency**: Manage secrets directly from VS Code terminal
-   ✅ **Version Control**: Script-based setup for reproducibility
-   ✅ **Security**: No sensitive data in web browsers or logs
-   ✅ **Automation**: Integrate with deployment workflows
-   ✅ **Team Collaboration**: Standardized setup procedures

**Remember**: Always use environment secrets for sensitive data and repository variables for non-sensitive configuration. This approach protects your deployment credentials while maintaining efficient DevOps workflows.

---

_Last updated: August 18, 2025_
_SocietyPal Project - GitHub Actions Integration_
