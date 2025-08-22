# 🚀 Laravel GitHub Actions CI/CD Implementation Guide

**Framework**: Laravel Applications (Universal Template)  
**Version**: 3.0 (Single Source of Truth)  
**Created**: August 18, 2025 10:35 AM EST  
**Updated**: August 18, 2025 12:18 PM EST  
**Architecture**: Roo + User Collaboration  
**Deployment Strategy**: Fully Automated CI/CD Pipeline  
**Target Environments**: Staging + Production  
**Approach**: ✅ Minimal Secrets + Existing .env Files  
**Usage**: Template for all Laravel applications

---

## 📋 Table of Contents

- [Background, Info and Overview](#background-info-and-overview)
  - [1. Project Analysis](#1-project-analysis)
  - [2. Architecture Overview](#2-architecture-overview)
  - [3. Technical Specifications](#3-technical-specifications)
  - [4. Deployment Strategy](#4-deployment-strategy)
- [1-Time Setup Steps](#1-time-setup-steps)
  - [1. Environment Preparation](#1-environment-preparation)
  - [2. GitHub Repository Configuration](#2-github-repository-configuration)
  - [3. Server Setup and SSH Keys](#3-server-setup-and-ssh-keys)
  - [4. Secrets Configuration (Minimal Approach)](#4-secrets-configuration-minimal-approach)
  - [5. Directory Structure Setup](#5-directory-structure-setup)
- [First Deploy Deployment Steps](#first-deploy-deployment-steps)
  - [1. Initial Workflow Verification](#1-initial-workflow-verification)
  - [2. Environment File Deployment](#2-environment-file-deployment)
  - [3. First Deployment Execution](#3-first-deployment-execution)
  - [4. Verification and Testing](#4-verification-and-testing)
- [Deploying a Vendor Update](#deploying-a-vendor-update)
  - [1. Standard Update Process](#1-standard-update-process)
  - [2. CodeCanyon Update Process](#2-codecanyon-update-process)
  - [3. Emergency Procedures](#3-emergency-procedures)
  - [4. Rollback Procedures](#4-rollback-procedures)

---

# Background, Info and Overview

This comprehensive guide provides a complete GitHub Actions CI/CD implementation template for Laravel applications, consolidating best practices into a single, authoritative source of truth for any Laravel project.

## 1. Project Analysis

### 1.1 Supported Application Stack

```yaml
Framework: Laravel 10.x/11.x/12.x
PHP: 8.1+ (8.2+ recommended)
Frontend: Vite + Any CSS Framework (Tailwind, Bootstrap, etc.)
Authentication: Laravel Breeze/Jetstream/Fortify (any)
Compatible Features:
    - Multi-payment gateways
    - PWA capabilities
    - Excel processing
    - Advanced permissions (Spatie)
    - Multi-language support
    - API development
    - Queue systems
```

### 1.2 Server Infrastructure Requirements

```yaml
Host: Any VPS/Shared hosting (Hostinger, DigitalOcean, AWS, etc.)
IP: [Your server IP]
Port: [Your SSH port - usually 22 or 65002]
User: [Your SSH username]
Web Server: Apache/Nginx/LiteSpeed
PHP: 8.1+ (matching Laravel requirements)
Node.js: Not required on server (GitHub Actions handles builds)
```

### 1.3 Domain Configuration Template

```yaml
Production: https://[your-domain.com]/
Staging: https://staging.[your-domain.com]/
Database Production: [your_db_prod]
Database Staging: [your_db_staging]
```

## 2. Architecture Overview

### 2.1 Deployment Flow Logic

```mermaid
flowchart TD
    A[Code Push] --> B{Branch?}
    B -->|main/staging| C[Staging Env]
    B -->|production| D[Production Env]

    C --> E[Build Assets]
    D --> E

    E --> F[Package Creation]
    F --> G[Upload to Server]
    G --> H[Extract & Link]
    H --> I{Migrations Safe?}

    I -->|Yes| J[Run Migrations]
    I -->|No| K[Halt & Alert]

    J --> L[Atomic Switch]
    L --> M[Health Check]
    M --> N{Health OK?}

    N -->|Yes| O[Success Report]
    N -->|No| P[Auto Rollback]

    K --> Q[Manual Review Required]
    P --> R[Failure Report]
```

### 2.2 Build Process Architecture

```mermaid
graph TB
    subgraph "Code Repository"
        A[Developer Push] --> B{Branch Detection}
        B -->|main/staging| C[Staging Pipeline]
        B -->|production| D[Production Pipeline]
    end

    subgraph "Build Process"
        C --> E1[Install PHP Dependencies]
        D --> F1[Install PHP Dependencies]
        E1 --> E2[Install Node.js Dependencies]
        F1 --> F2[Install Node.js Dependencies]
        E2 --> E3[Vite Build Assets]
        F2 --> F3[Vite Build Assets]
        E3 --> E4[Laravel Optimization]
        F3 --> F4[Laravel Optimization]
        E4 --> E5[Create Deployment Package]
        F4 --> F5[Create Deployment Package]
    end

    subgraph "Target Server"
        E5 --> G[Deploy to Staging Environment]
        F5 --> H[Deploy to Production Environment]

        G --> I1[Extract Package]
        H --> J1[Extract Package]
        I1 --> I2[Database Migrations]
        J1 --> J2[Database Migrations]
        I2 --> I3[Atomic Symlink Switch]
        J2 --> J3[Atomic Symlink Switch]
        I3 --> I4[Health Check]
        J3 --> J4[Health Check]
    end

    subgraph "Verification"
        I4 --> K[Staging Tests]
        J4 --> L[Production Verification]
        K --> M[Deployment Report]
        L --> N[Deployment Report]
    end
```

## 3. Technical Specifications

### 3.1 Server Path Mapping Template

```yaml
Production:
    Domain: [your-domain.com]
    Path: /home/[username]/domains/[your-domain.com]/
    Public: /home/[username]/domains/[your-domain.com]/public_html
    Shared: /home/[username]/domains/[your-domain.com]/deploy/shared
    Releases: /home/[username]/domains/[your-domain.com]/releases/

Staging:
    Domain: staging.[your-domain.com]
    Path: /home/[username]/domains/staging.[your-domain.com]/
    Public: /home/[username]/domains/staging.[your-domain.com]/public_html
    Shared: /home/[username]/domains/staging.[your-domain.com]/deploy/shared
    Releases: /home/[username]/domains/staging.[your-domain.com]/releases/
```

### 3.2 Laravel Optimizations Included

```yaml
Build Optimizations:
    - Composer: --optimize-autoloader --no-dev --prefer-dist
    - Assets: Vite production build with optimization
    - Cache: config:cache, route:cache, view:cache
    - Storage: Proper linking and permissions
    - Migrations: Safety validation with rollback detection
```

### 3.3 Deployment Process

```yaml
Release Management:
    - Format: Timestamped releases (YYYYMMDD-HHMMSS)
    - Symlinks: Atomic switching (current -> releases/timestamp)
    - Shared: .env, storage, uploads linked from shared directory
    - Cleanup: Keep 3 most recent releases, auto-cleanup older
    - Rollback: Instant via symlink switching to previous release
```

## 4. Deployment Strategy

### 4.1 ✅ Better Approach: Minimal Secrets + Existing .env Files

**This implementation uses the optimized approach:**

1. **Only 4 GitHub Secrets Required** (Connection secrets only):
   - `SERVER_HOST`: [Your server IP]
   - `SERVER_USER`: [Your SSH username]  
   - `SERVER_PORT`: [Your SSH port]
   - `SERVER_SSH_KEY`: Private SSH key content

2. **Database Credentials in .env Files** (Already on server):
   - `.env.production` → `/home/[username]/domains/[your-domain.com]/deploy/shared/.env`
   - `.env.staging` → `/home/[username]/domains/staging.[your-domain.com]/deploy/shared/.env`

3. **Benefits**:
   - ✅ Reduced GitHub secrets complexity
   - ✅ Environment files managed on server
   - ✅ Easier credential rotation
   - ✅ Better security isolation

### 4.2 Included Workflow Files Overview

This template includes these fully-implemented workflows:

```yaml
Workflows:
    - first-install-part1.yml: CodeCanyon first-time installation (Stage 1)
    - first-install-part2.yml: CodeCanyon installation completion (Stage 2)  
    - update-install.yml: Standard deployment pipeline
    - manual-deploy.yml: Emergency manual deployment

Scripts:
    - check-migrations.sh: Database migration safety validation
    - rollback.sh: Emergency rollback procedures
    - validate-setup.sh: Pre-deployment validation
    - verify-server-config.sh: Server configuration verification
    - setup-laravel-secrets-simple.sh: Simplified secrets setup
    - setup-laravel-secrets.sh: Complete secrets setup automation
```

---

# 1-Time Setup Steps

These steps are performed once during initial setup and typically don't need to be repeated.

## 1. Environment Preparation

### 1.1 Local Development Environment

**Prerequisites:**
```bash
# Verify required tools
git --version          # Git for version control  
gh --version           # GitHub CLI (optional but recommended)
ssh -V                # SSH client for server access
```

**Project Directory Verification:**
```bash
# Navigate to your Laravel project
cd /path/to/your/laravel-project

# Verify project structure
ls -la .github/        # Should show workflows/ and scripts/ directories
ls -la .env.production # Should exist
ls -la .env.staging    # Should exist
```

### 1.2 Environment Files Validation

**Check Environment Files Content:**
```bash
# Verify production environment file
cat .env.production | grep -E "(DB_|APP_ENV)"

# Expected output should include:
# APP_ENV=production
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=[your_production_db]
# DB_USERNAME=[your_production_user]
# DB_PASSWORD=[production password]

# Verify staging environment file  
cat .env.staging | grep -E "(DB_|APP_ENV)"

# Expected output should include:
# APP_ENV=staging
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=[your_staging_db]
# DB_USERNAME=[your_staging_user]
# DB_PASSWORD=[staging password]
```

## 2. GitHub Repository Configuration

### 2.1 Repository Access Verification

```bash
# Check repository connection
git remote -v
# Expected: origin  https://github.com/[username]/[repository].git (fetch)
#          origin  https://github.com/[username]/[repository].git (push)

# Optional: Verify with GitHub CLI
gh repo view
# Expected: [username]/[repository] repository details
```

### 2.2 GitHub Environments Setup (Recommended)

**Create GitHub Environments for better security:**

1. **Navigate to Repository Settings:**
   - Go to: `https://github.com/[username]/[repository]/settings`
   - Click "Environments" in left sidebar

2. **Create Staging Environment:**
   - Click "New environment"
   - Name: `staging`
   - Configure deployment branches: `main`, `staging`
   - Click "Create environment"

3. **Create Production Environment:**
   - Click "New environment"
   - Name: `production`
   - Configure deployment branches: `production`
   - Enable "Required reviewers" (recommended)
   - Click "Create environment"

### 2.3 Branch Protection Rules (Optional but Recommended)

```bash
# Verify current branches
git branch -a

# Expected branches:
# * main (or master)
# * production (should exist or be created)
# * staging (optional)
```

**Setup Branch Protection via GitHub UI:**
1. Go to `https://github.com/[username]/[repository]/settings/branches`
2. Add protection rule for `production` branch
3. Enable "Require a pull request before merging"
4. Enable "Require status checks to pass before merging"

## 3. Server Setup and SSH Keys

### 3.1 SSH Key Configuration

**Option 1: Use Existing SSH Key (Recommended)**

If you already have SSH access configured from your previous server setup:

```bash
# Verify existing SSH key
ls -la ~/.ssh/id_ed25519*
# Should show: id_ed25519 (private) and id_ed25519.pub (public)

# Test existing connection
ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "echo 'SSH connection successful'"
```

**Option 2: Generate New Deployment Key**

```bash
# Generate dedicated deployment key
ssh-keygen -t ed25519 -C "github-actions-laravel" -f ~/.ssh/laravel_deploy

# Copy public key for server addition
cat ~/.ssh/laravel_deploy.pub
```

### 3.2 Add Public Key to Server

**If using new key, add public key to server:**

```bash
# Connect to server
ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP]

# Add public key to authorized_keys
echo "YOUR_PUBLIC_KEY_CONTENT_HERE" >> ~/.ssh/authorized_keys

# Set proper permissions
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh

# Exit server
exit
```

### 3.3 SSH Key Verification

**Verify SSH key setup:**

```bash
# Test connection with your key
ssh -i ~/.ssh/id_ed25519 -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "echo 'GitHub Actions SSH test successful'"

# OR for dedicated key:
ssh -i ~/.ssh/laravel_deploy -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "echo 'GitHub Actions SSH test successful'"
```

## 4. Secrets Configuration (Minimal Approach)

### 4.1 GitHub CLI Setup (Recommended Method)

**Install and Authenticate GitHub CLI:**

```bash
# Install GitHub CLI (macOS)
brew install gh

# Authenticate
gh auth login
# Choose: GitHub.com, HTTPS, Yes, Login with browser
```

### 4.2 Set Repository Secrets (Only 4 Required)

**Method A: GitHub CLI (Recommended)**

```bash
# Navigate to your project directory
cd /path/to/your/laravel-project

# Set server connection secrets (ONLY THESE 4 NEEDED)
echo "[YOUR_SERVER_IP]" | gh secret set SERVER_HOST
echo "[YOUR_SSH_USER]" | gh secret set SERVER_USER  
echo "[YOUR_SSH_PORT]" | gh secret set SERVER_PORT

# Set SSH private key (use your existing key)
gh secret set SERVER_SSH_KEY < ~/.ssh/id_ed25519
# OR for dedicated key:
# gh secret set SERVER_SSH_KEY < ~/.ssh/laravel_deploy

# Verify all secrets are set
gh secret list
```

**Expected Output:**
```
SERVER_HOST     2025-08-18T14:35:00Z
SERVER_PORT     2025-08-18T14:35:00Z  
SERVER_SSH_KEY  2025-08-18T14:35:00Z
SERVER_USER     2025-08-18T14:35:00Z
```

**Method B: GitHub Web UI (Alternative)**

1. Go to: `https://github.com/[username]/[repository]/settings/secrets/actions`
2. Click "New repository secret" for each:

| Secret Name      | Value                | Description             |
| ---------------- | -------------------- | ----------------------- |
| `SERVER_HOST`    | `[Your server IP]`   | Server IP address       |
| `SERVER_USER`    | `[Your SSH user]`    | SSH username            |
| `SERVER_PORT`    | `[Your SSH port]`    | SSH port                |
| `SERVER_SSH_KEY` | `[PRIVATE_KEY]`      | Complete SSH private key |

**Private Key Format:**
```
-----BEGIN OPENSSH PRIVATE KEY-----
[key content here]
-----END OPENSSH PRIVATE KEY-----
```

### 4.3 Environment-Specific Secrets (Optional Enhanced Security)

**For enhanced security, you can set environment-specific secrets:**

```bash
# Set secrets for staging environment
echo "[YOUR_SERVER_IP]" | gh secret set SERVER_HOST --env staging
echo "[YOUR_SSH_USER]" | gh secret set SERVER_USER --env staging
echo "[YOUR_SSH_PORT]" | gh secret set SERVER_PORT --env staging
gh secret set SERVER_SSH_KEY --env staging < ~/.ssh/id_ed25519

# Set secrets for production environment  
echo "[YOUR_SERVER_IP]" | gh secret set SERVER_HOST --env production
echo "[YOUR_SSH_USER]" | gh secret set SERVER_USER --env production
echo "[YOUR_SSH_PORT]" | gh secret set SERVER_PORT --env production
gh secret set SERVER_SSH_KEY --env production < ~/.ssh/id_ed25519

# Verify environment secrets
gh secret list --env staging
gh secret list --env production
```

### 4.4 Repository Variables (Optional Configuration)

**Set non-sensitive configuration variables:**

```bash
# Deployment configuration
gh variable set DEPLOYMENT_TIMEOUT --value "300"
gh variable set HEALTH_CHECK_RETRIES --value "3"
gh variable set KEEP_RELEASES --value "3"

# Application configuration
gh variable set LARAVEL_ENV_STAGING --value "staging"
gh variable set LARAVEL_ENV_PRODUCTION --value "production"
gh variable set APP_NAME --value "[Your App Name]"

# Verify variables
gh variable list
```

## 5. Directory Structure Setup

### 5.1 Server Directory Creation

**Create required directories on server:**

```bash
# Create directory structure for both environments (REPLACE PLACEHOLDERS)
ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "
    # Production directories
    mkdir -p /home/[YOUR_USER]/domains/[your-domain.com]/releases
    mkdir -p /home/[YOUR_USER]/domains/[your-domain.com]/deploy/shared
    
    # Staging directories  
    mkdir -p /home/[YOUR_USER]/domains/staging.[your-domain.com]/releases
    mkdir -p /home/[YOUR_USER]/domains/staging.[your-domain.com]/deploy/shared
    
    # Set proper permissions
    chmod 755 /home/[YOUR_USER]/domains/[your-domain.com]
    chmod 755 /home/[YOUR_USER]/domains/staging.[your-domain.com]
    
    # Display created structure
    echo '✅ Directory structure created:'
    ls -la /home/[YOUR_USER]/domains/[your-domain.com]/
    ls -la /home/[YOUR_USER]/domains/staging.[your-domain.com]/
"
```

### 5.2 Environment Files Deployment

**Copy environment files to server shared directories:**

```bash
# Copy production environment file (REPLACE PLACEHOLDERS)
scp -P [YOUR_PORT] .env.production [YOUR_USER]@[YOUR_SERVER_IP]:/home/[YOUR_USER]/domains/[your-domain.com]/deploy/shared/.env

# Copy staging environment file
scp -P [YOUR_PORT] .env.staging [YOUR_USER]@[YOUR_SERVER_IP]:/home/[YOUR_USER]/domains/staging.[your-domain.com]/deploy/shared/.env

# Verify files were copied correctly
ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "
    echo '✅ Production .env file:'
    ls -la /home/[YOUR_USER]/domains/[your-domain.com]/deploy/shared/.env
    
    echo '✅ Staging .env file:'
    ls -la /home/[YOUR_USER]/domains/staging.[your-domain.com]/deploy/shared/.env
"
```

### 5.3 Setup Validation

**Run the validation script to verify setup:**

```bash
# Navigate to project directory
cd /path/to/your/laravel-project

# Make validation script executable
chmod +x .github/scripts/validate-setup.sh

# Run validation
./.github/scripts/validate-setup.sh
```

**Expected Output:**
```
🔍 Laravel Deployment Setup Validation
======================================

✅ GitHub workflows found
✅ SSH connection successful
✅ Server directories verified
✅ Environment files confirmed
✅ GitHub secrets configured

🎉 VALIDATION COMPLETE - READY FOR DEPLOYMENT
```

---

# First Deploy Deployment Steps

These steps guide you through your first deployment to verify everything is working correctly.

## 1. Initial Workflow Verification

### 1.1 Verify GitHub Actions Workflows

**Check workflows are visible in GitHub:**

1. Navigate to: `https://github.com/[username]/[repository]/actions`
2. Verify you see these workflows:
   - 🎯 **Laravel Automated Deployment** (`update-install.yml`)
   - 🚨 **Emergency Manual Deployment** (`manual-deploy.yml`) 
   - 🎯 **First Installation - Part 1** (`first-install-part1.yml`)
   - 🔒 **First Installation - Part 2** (`first-install-part2.yml`)

### 1.2 Workflow File Verification

**Verify workflow files are committed and pushed:**

```bash
# Check workflow files exist locally
ls -la .github/workflows/
# Expected: first-install-part1.yml, first-install-part2.yml, update-install.yml, manual-deploy.yml

# Check git status
git status

# If files are not committed yet:
git add .github/
git commit -m "feat: Add GitHub Actions CI/CD deployment workflows"
git push origin main
```

## 2. Environment File Deployment

### 2.1 Verify Environment Files on Server

**Confirm environment files are properly deployed:**

```bash
# Verify production environment file (REPLACE PLACEHOLDERS)
ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "
    echo '🔍 Production environment file:'
    ls -la /home/[YOUR_USER]/domains/[your-domain.com]/deploy/shared/.env
    echo ''
    echo '📋 Database configuration (production):'
    grep -E '^(DB_|APP_ENV)' /home/[YOUR_USER]/domains/[your-domain.com]/deploy/shared/.env
"

# Verify staging environment file  
ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "
    echo '🔍 Staging environment file:'
    ls -la /home/[YOUR_USER]/domains/staging.[your-domain.com]/deploy/shared/.env
    echo ''
    echo '📋 Database configuration (staging):'
    grep -E '^(DB_|APP_ENV)' /home/[YOUR_USER]/domains/staging.[your-domain.com]/deploy/shared/.env
"
```

### 2.2 Database Connection Testing

**Test database connections from server:**

```bash
# Test staging database connection (REPLACE WITH YOUR DB CREDENTIALS)
ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "
    mysql -h 127.0.0.1 -P 3306 -u [STAGING_DB_USER] -p'[STAGING_DB_PASS]' -e 'SELECT \"Staging DB connection successful\" as status;' [STAGING_DB_NAME]
"

# Test production database connection (be careful with production!)
ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "
    mysql -h 127.0.0.1 -P 3306 -u [PROD_DB_USER] -p'[PROD_DB_PASS]' -e 'SELECT \"Production DB connection successful\" as status;' [PROD_DB_NAME]
"
```

## 3. First Deployment Execution

### 3.1 Test Staging Deployment (Recommended First Step)

**Manual staging deployment trigger:**

1. **Navigate to GitHub Actions:**
   - Go to: `https://github.com/[username]/[repository]/actions`
   
2. **Select Laravel Automated Deployment:**
   - Click on "Laravel Automated Deployment" workflow
   
3. **Trigger Manual Deployment:**
   - Click "Run workflow" button
   - Select branch: `main`
   - Select environment: `staging`
   - Click "Run workflow"

4. **Monitor Deployment:**
   - Watch the workflow execution in real-time
   - Monitor each step completion
   - Note any errors or warnings

### 3.2 Deployment Process Monitoring

**Key steps to monitor during deployment:**

1. **Build Phase:**
   - ✅ PHP environment setup
   - ✅ Composer dependency installation
   - ✅ Node.js and npm dependency installation  
   - ✅ Vite asset compilation
   - ✅ Laravel optimization (config/route/view cache)

2. **Deployment Phase:**
   - ✅ Package creation and upload
   - ✅ Server directory preparation
   - ✅ Environment file linking
   - ✅ Database migration execution
   - ✅ Atomic symlink switching

3. **Verification Phase:**
   - ✅ Health check execution
   - ✅ Response time verification
   - ✅ Database connectivity check
   - ✅ Asset accessibility confirmation

### 3.3 Common First Deployment Issues

**If deployment fails, check these common issues:**

1. **SSH Connection Issues:**
   ```bash
   # Test SSH connection manually
   ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "echo 'SSH works'"
   ```

2. **Missing Environment Files:**
   ```bash
   # Verify .env files exist on server
   ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "ls -la /home/[YOUR_USER]/domains/staging.[your-domain.com]/deploy/shared/.env"
   ```

3. **Permission Issues:**
   ```bash
   # Check directory permissions
   ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "ls -la /home/[YOUR_USER]/domains/staging.[your-domain.com]/"
   ```

## 4. Verification and Testing

### 4.1 Post-Deployment Verification

**After successful staging deployment:**

1. **Visit Staging Site:**
   - URL: `https://staging.[your-domain.com]`
   - Verify site loads correctly
   - Test key functionality:
     - User registration/login
     - Database connectivity
     - Asset loading (CSS/JS)

2. **Check Deployment Artifacts:**
   ```bash
   # Verify deployment structure on server
   ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "
       echo '📁 Staging deployment structure:'
       ls -la /home/[YOUR_USER]/domains/staging.[your-domain.com]/
       echo ''
       echo '📁 Current release:'
       ls -la /home/[YOUR_USER]/domains/staging.[your-domain.com]/public_html
       echo ''
       echo '📁 Release history:'
       ls -la /home/[YOUR_USER]/domains/staging.[your-domain.com]/releases/
   "
   ```

### 4.2 Health Check Verification

**Manual health check execution:**

```bash
# Run manual health check
ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "
    cd /home/[YOUR_USER]/domains/staging.[your-domain.com]/public_html
    
    # Check Laravel application status
    php artisan --version
    
    # Verify database connectivity
    php artisan migrate:status
    
    # Check storage permissions
    ls -la storage/
"
```

### 4.3 Production Deployment (After Staging Success)

**Once staging deployment is verified successful:**

1. **Create Production Branch (if not exists):**
   ```bash
   # Create production branch from main
   git checkout main
   git pull origin main
   git checkout -b production
   git push origin production
   ```

2. **Trigger Production Deployment:**
   - Go to: `https://github.com/[username]/[repository]/actions`
   - Click "Laravel Automated Deployment"
   - Click "Run workflow"
   - Select branch: `production`
   - Select environment: `production`
   - Click "Run workflow"

3. **Enhanced Monitoring for Production:**
   - Monitor deployment extra carefully
   - Have rollback plan ready
   - Test production site immediately after deployment

### 4.4 Success Validation Checklist

**Confirm these items after first deployment:**

- [ ] Staging site accessible at `https://staging.[your-domain.com]`
- [ ] Production site accessible at `https://[your-domain.com]`
- [ ] Database connections working in both environments
- [ ] User registration/login functionality works
- [ ] Payment gateway integration functional (if applicable)
- [ ] Asset files (CSS/JS) loading correctly
- [ ] Admin panel accessible (if applicable)
- [ ] No PHP errors in application logs
- [ ] GitHub Actions showing successful deployment runs

**🎉 First Deployment Complete!**

---

# Deploying a Vendor Update

This section covers ongoing deployments for code updates, vendor updates, and maintenance.

## 1. Standard Update Process

### 1.1 Automatic Deployments (Branch-Based)

**Standard deployment workflow triggers automatically on:**

1. **Push to `main` or `staging` branch → Staging deployment**
   ```bash
   # Example: Deploy to staging
   git checkout main
   git add .
   git commit -m "feat: Add new feature"
   git push origin main
   # Automatically triggers staging deployment
   ```

2. **Push to `production` branch → Production deployment**
   ```bash
   # Example: Deploy to production
   git checkout production
   git merge main
   git push origin production
   # Automatically triggers production deployment
   ```

### 1.2 Manual Deployments

**When you need control over deployment timing:**

1. **Navigate to GitHub Actions:**
   - `https://github.com/[username]/[repository]/actions`

2. **Select "Laravel Automated Deployment"**

3. **Click "Run workflow":**
   - Choose target branch
   - Choose environment (staging/production)
   - Add deployment notes (optional)
   - Click "Run workflow"

### 1.3 Pre-Deployment Checklist

**Before any deployment, verify:**

```bash
# 1. Local testing complete
php artisan test
npm run build
php artisan config:clear

# 2. Database migrations safe
php artisan migrate --dry-run

# 3. Dependencies updated
composer install --no-dev
npm install

# 4. Environment files updated if needed
# (Update .env.production/.env.staging if database changes)

# 5. Backup available (production only)
# Ensure recent database backup exists before production deployment
```

## 2. CodeCanyon Update Process

### 2.1 CodeCanyon Application Detection

**The system automatically detects CodeCanyon applications:**

```yaml
Detection Criteria:
    - Presence of /install directory
    - Presence of /installer directory  
    - Installation script files
    - CodeCanyon license files
```

### 2.2 Two-Stage CodeCanyon Process

**For CodeCanyon applications requiring web installer:**

#### Stage 1: Pre-Installation Deployment

1. **Trigger "First Installation - Part 1":**
   - Go to GitHub Actions
   - Select "First Installation - Part 1" 
   - Run workflow for target environment
   - This deploys code with installer directories preserved
   - Sets temporary permissions for installation

2. **Complete Web Installation:**
   - Visit: `https://staging.[your-domain.com]/install` (or production)
   - Follow CodeCanyon installation wizard
   - Complete database setup via web interface
   - Verify installation completion

#### Stage 2: Security Lockdown

3. **Trigger "First Installation - Part 2":**
   - Go to GitHub Actions
   - Select "First Installation - Part 2"
   - Run workflow for same environment
   - This secures installer directories
   - Reverts to production permissions
   - Optimizes Laravel caches

### 2.3 CodeCanyon Update Workflow

**For CodeCanyon application updates:**

1. **Standard Update Process:**
   - CodeCanyon updates typically don't need web installer
   - Use standard "Laravel Automated Deployment" workflow
   - System automatically detects and handles CodeCanyon specifics

2. **If Web Installer Required:**
   - Use two-stage process described above
   - Usually only needed for major version updates

### 2.4 CodeCanyon Permission Strategy

```yaml
Pre-Installation (Stage 1):
    - storage/: 777 (temporary)
    - bootstrap/cache/: 777 (temporary)
    - config/: 777 (temporary)
    - public/: 777 (temporary)
    - .env: 600 (always secure)

Post-Installation (Stage 2):
    - Local/Staging: 775 for writable directories
    - Production: 755 for writable directories
    - Config: 755 directories, 644 files
    - .env: 600 (always secure)
```

## 3. Emergency Procedures

### 3.1 Emergency Manual Deployment

**For urgent fixes or when automated deployment fails:**

1. **Trigger Emergency Deployment:**
   - Go to: `https://github.com/[username]/[repository]/actions`
   - Select "Emergency Manual Deployment"
   - Choose environment
   - Select deployment action:
     - `deploy`: Emergency deployment
     - `rollback`: Immediate rollback
     - `health-check`: Health verification only
   - Run workflow

### 3.2 Emergency Rollback

**Immediate rollback to previous release:**

```bash
# Option 1: Via GitHub Actions
# - Go to "Emergency Manual Deployment"
# - Select action: "rollback"  
# - Choose environment
# - Execute

# Option 2: Direct server rollback (if GitHub Actions unavailable)
ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP]
cd /home/[YOUR_USER]/domains/staging.[your-domain.com]/  # or production

# List available releases
ls -la releases/

# Rollback to previous release (example)
ln -nfs releases/20250818-094500 public_html
echo "Rollback complete to release 20250818-094500"
```

### 3.3 Emergency Database Rollback

**If database migrations need rollback:**

```bash
# Connect to server
ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP]

# Navigate to current release
cd /home/[YOUR_USER]/domains/staging.[your-domain.com]/public_html

# Check migration status
php artisan migrate:status

# Rollback specific migrations if needed
php artisan migrate:rollback --step=1

# OR rollback to specific batch
php artisan migrate:rollback --batch=5
```

### 3.4 Emergency Security Lockdown

**For CodeCanyon applications with security concerns:**

```bash
# Immediate installer directory lockdown
ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP] "
    cd /home/[YOUR_USER]/domains/staging.[your-domain.com]/public_html
    
    # Disable installer directories
    if [ -d 'install' ]; then
        chmod 000 install
        echo 'Installer directory secured'
    fi
    
    if [ -d 'installer' ]; then
        chmod 000 installer
        echo 'Installer directory secured'
    fi
    
    # Set production permissions
    find . -type d -exec chmod 755 {} \;
    find . -type f -exec chmod 644 {} \;
    chmod 600 .env
"
```

## 4. Rollback Procedures

### 4.1 Automated Rollback

**System automatically triggers rollback when:**

1. **Health checks fail after deployment**
2. **Application becomes unresponsive**  
3. **Database connectivity lost**
4. **Critical errors detected**

**Rollback process includes:**
```yaml
Rollback Steps:
    1. Symlink switching to previous release
    2. Database migration rollback (if safe)
    3. Cache clearing and regeneration
    4. Health check verification
    5. Notification of rollback completion
```

### 4.2 Manual Rollback Procedures

#### 4.2.1 Application Rollback

**To rollback application code:**

```bash
# Run rollback script
ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP]
cd /home/[YOUR_USER]/domains/staging.[your-domain.com]/

# Execute rollback script
./rollback.sh --environment=staging --steps=1

# OR for production
cd /home/[YOUR_USER]/domains/[your-domain.com]/
./rollback.sh --environment=production --steps=1
```

#### 4.2.2 Database Rollback

**Careful database migration rollback:**

```bash
# Check current migration status
php artisan migrate:status

# Rollback latest batch only (safest)
php artisan migrate:rollback --batch=LATEST_BATCH_NUMBER

# Rollback specific number of migrations
php artisan migrate:rollback --step=2

# Check status after rollback
php artisan migrate:status
```

#### 4.2.3 Asset Rollback

**If asset compilation issues occur:**

```bash
# Clear compiled assets
rm -rf public/build/*

# Regenerate from previous release
cd releases/PREVIOUS_RELEASE_TIMESTAMP
npm run build
cp -r public/build/* ../../public_html/public/build/
```

### 4.3 Rollback Verification

**After any rollback, verify:**

1. **Site Accessibility:**
   ```bash
   # Test site response
   curl -I https://staging.[your-domain.com]
   # Should return: HTTP/1.1 200 OK
   ```

2. **Database Functionality:**
   ```bash
   # Test database connectivity
   ssh -p [YOUR_PORT] [YOUR_USER]@[YOUR_SERVER_IP]
   cd /home/[YOUR_USER]/domains/staging.[your-domain.com]/public_html
   php artisan migrate:status
   ```

3. **Application Health:**
   ```bash
   # Run health checks
   ./.github/scripts/verify-server-config.sh staging
   ```

### 4.4 Post-Rollback Actions

**After successful rollback:**

1. **Document the Issue:**
   - Create GitHub issue with rollback details
   - Include error logs and failure reasons
   - Add steps to prevent recurrence

2. **Investigate Root Cause:**
   - Review deployment logs
   - Check application logs
   - Identify configuration issues

3. **Plan Fix Deployment:**
   - Address root cause in code
   - Test fix in staging environment
   - Schedule fix deployment when ready

### 4.5 Rollback Prevention

**Best practices to minimize rollback need:**

1. **Always deploy to staging first**
2. **Run comprehensive tests before production**
3. **Use feature flags for major changes**
4. **Maintain database migration safety**
5. **Keep regular database backups**
6. **Monitor application health post-deployment**

---

## 🎉 Implementation Complete

**Status**: ✅ **Version 3.0 - Complete Implementation - Universal Laravel Template**

This comprehensive guide provides everything needed for Laravel GitHub Actions CI/CD implementation on any project:

### ✅ Key Features Implemented:
- **Zero-downtime deployments** with atomic symlink switching
- **Multi-environment support** (staging/production)
- **CodeCanyon applications** with two-stage security pipeline
- **Minimal secrets approach** using existing .env files
- **Comprehensive rollback** and emergency procedures
- **Database migration safety** with validation
- **Asset optimization** and Laravel caching
- **Health monitoring** and automated recovery

### 🔄 Deployment Workflows:
1. **Standard Updates**: Use "Laravel Automated Deployment" workflow
2. **CodeCanyon First-Time**: Use two-stage installation process
3. **Emergencies**: Use "Emergency Manual Deployment" workflow
4. **Rollbacks**: Automatic on failure, manual via emergency workflow

### 📁 File References:
- **Workflows**: `.github/workflows/` (4 workflow files)  
- **Scripts**: `.github/scripts/` (6 helper scripts)
- **Documentation**: This file serves as universal template
- **Environment**: `.env.production` and `.env.staging` deployed to server

### 🚀 Usage Instructions:
1. **Copy this template** to any new Laravel project
2. **Replace all placeholder values** `[YOUR_*]` with actual values
3. **Follow the 1-Time Setup Steps** for each new project
4. **Deploy with confidence** using the established workflows

The system automatically handles both regular Laravel applications and CodeCanyon applications with frontend installers, ensuring security and functionality across all deployment scenarios.

---

*Last updated: August 18, 2025*  
*Universal Laravel Project - GitHub Actions Implementation Guide*  
*Complete V3.0 - Reusable Template for All Laravel Applications*