# Step 08: Deploy Updates

**Goal:** Execute the deployment of your updated Laravel application using your chosen deployment method (A: Manual SSH, B: GitHub Actions, C: DeployHQ, D: GitHub + Manual).

**Time Required:** 15-60 minutes (varies by method)
**Prerequisites:** Step 07 completed with success rate ≥ 70%

---

## **Tracking Integration**

### **Initialize Step 08 Tracking**

```bash
# Detect project paths dynamically
if [ -d "Admin-Local" ]; then
    PROJECT_ROOT="$(pwd)"
elif [ -d "../Admin-Local" ]; then
    PROJECT_ROOT="$(cd .. && pwd)"
elif [ -d "../../Admin-Local" ]; then
    PROJECT_ROOT="$(cd ../.. && pwd)"
else
    echo "❌ Could not detect Admin-Local directory"
    exit 1
fi

ADMIN_LOCAL="$PROJECT_ROOT/Admin-Local"
PROJECT_NAME=$(basename "$PROJECT_ROOT")

# Find active session directory
SESSION_DIR=$(find "$ADMIN_LOCAL/1-CurrentProject/Tracking" -name "*-Update-or-Customization" -type d | sort | tail -1)
if [ -z "$SESSION_DIR" ]; then
    echo "❌ No active tracking session found"
    exit 1
fi

echo "📋 Step 08: Deploy Updates - Tracking Initialized"
echo "   Project: $PROJECT_NAME"
echo "   Session: $(basename "$SESSION_DIR")"

# Create step-specific tracking files
STEP_PLAN="$SESSION_DIR/1-Planning/step-08-deployment-plan.md"
STEP_BASELINE="$SESSION_DIR/2-Baselines/step-08-pre-deployment-baseline.txt"
STEP_EXECUTION="$SESSION_DIR/3-Execution/step-08-execution.log"

# Initialize planning document
cat > "$STEP_PLAN" << 'PLAN_EOF'
# Step 08: Deployment Execution Plan

## Tracking Information
- **Step:** 08 - Deploy Updates
- **Started:** $(date)
- **Project:** $(basename "$(pwd)")

## Pre-Deployment Checklist
- [ ] Step 07 completion verified (≥70% success rate)
- [ ] Deployment method determined
- [ ] Deployment assets ready
- [ ] Server connectivity verified
- [ ] Configuration files prepared

## Method-Specific Execution
- [ ] Method A: Manual SSH deployment
- [ ] Method B: GitHub Actions deployment
- [ ] Method C: DeployHQ deployment
- [ ] Method D: GitHub + Manual deployment

## Post-Deployment Tasks
- [ ] Deployment summary created
- [ ] Update logs updated
- [ ] Verification prepared for Step 09

## Completion Status
- [ ] Step 08 completed successfully
PLAN_EOF

# Initialize baseline
cat > "$STEP_BASELINE" << BASELINE_EOF
# Step 08 Pre-Deployment Baseline
Date: $(date)
Project: $PROJECT_NAME

## System Status
Git Status: $(git status --porcelain | wc -l) files changed
Current Branch: $(git branch --show-current 2>/dev/null || echo "Not a git repo")

## Previous Step Results
Step 07 Status: $([ -f "$SESSION_DIR/5-Documentation/step-07-verification.md" ] && echo "Completed" || echo "Pending")

## Pre-Deployment State
Deployment Method: [TBD]
Customization Mode: [TBD]
Build Assets: [TBD]
BASELINE_EOF

echo "✅ Step 08 tracking initialized"
echo "📝 Plan: $STEP_PLAN"
echo "📊 Baseline: $STEP_BASELINE"
echo "📋 Execution Log: $STEP_EXECUTION"
```

---

## **Analysis Source**

Based on **Laravel - Final Guides/V1_vs_V2_Comparison_Report.md** and deployment execution:

-   V1 Step 14: Deploy Application with method-specific strategies
-   V2 Step 13: Execute deployment with verification
-   V3 Scenarios 24A/B/C/D: Production deployment execution by method

---

## **8.1: Pre-Deployment Verification**

### **Final Pre-Deployment Checks:**

1. **Initialize deployment environment:**

    ```bash
    # Navigate to project root and set project-agnostic paths
    cd "$PROJECT_ROOT"

    # Get context from previous steps using project-agnostic paths
    LATEST_STAGING=$(find "$ADMIN_LOCAL/vendor_updates" -name "202*" -type d | sort | tail -1)

    # Try to get deployment method and customization mode from various sources
    if [ -f "$ADMIN_LOCAL/update_logs/update_$(date +%Y%m%d)_*.md" ]; then
        DEPLOY_METHOD=$(grep "DEPLOY_METHOD=" "$ADMIN_LOCAL/update_logs/update_*.md" | tail -1 | cut -d'"' -f2)
        CUSTOMIZATION_MODE=$(grep "CUSTOMIZATION_MODE=" "$ADMIN_LOCAL/update_logs/update_*.md" | tail -1 | cut -d'"' -f2)
    else
        # Fallback: try to detect from session logs
        LATEST_SESSION_LOG=$(find "$SESSION_DIR/3-Execution" -name "step-*-execution.log" | sort | tail -1)
        if [ -f "$LATEST_SESSION_LOG" ]; then
            DEPLOY_METHOD=$(grep "Deploy Method:" "$LATEST_SESSION_LOG" | tail -1 | cut -d':' -f2 | xargs)
            CUSTOMIZATION_MODE=$(grep "Customization Mode:" "$LATEST_SESSION_LOG" | tail -1 | cut -d':' -f2 | xargs)
        fi
    fi

    # Set defaults if not found
    DEPLOY_METHOD=${DEPLOY_METHOD:-"manual_ssh"}
    CUSTOMIZATION_MODE=${CUSTOMIZATION_MODE:-"simple"}

    echo "🚀 Initializing deployment process..." | tee -a "$STEP_EXECUTION"
    echo "   Method: $DEPLOY_METHOD" | tee -a "$STEP_EXECUTION"
    echo "   Customization Mode: $CUSTOMIZATION_MODE" | tee -a "$STEP_EXECUTION"
    echo "   Staging Directory: $LATEST_STAGING" | tee -a "$STEP_EXECUTION"

    # Update tracking plan
    sed -i.bak "s/Deployment Method: \[TBD\]/Deployment Method: $DEPLOY_METHOD/" "$STEP_PLAN"
    sed -i.bak "s/Customization Mode: \[TBD\]/Customization Mode: $CUSTOMIZATION_MODE/" "$STEP_PLAN"

    # Check Step 07 results
    BUILD_TEST_REPORT="$LATEST_STAGING/BUILD_TEST_REPORT.md"
    if [ -f "$BUILD_TEST_REPORT" ]; then
        SUCCESS_RATE=$(grep "Success Rate:" "$BUILD_TEST_REPORT" | cut -d' ' -f3 | cut -d'%' -f1)
        if [ "$SUCCESS_RATE" -ge 70 ]; then
            echo "   ✅ Build tests passed ($SUCCESS_RATE% success rate)" | tee -a "$STEP_EXECUTION"
            echo "Build Test Status: PASSED ($SUCCESS_RATE%)" >> "$STEP_BASELINE"
            sed -i.bak 's/- \[ \] Step 07 completion verified/- [x] Step 07 completion verified/' "$STEP_PLAN"
        else
            echo "   ❌ Build tests failed ($SUCCESS_RATE% success rate)" | tee -a "$STEP_EXECUTION"
            echo "Build Test Status: FAILED ($SUCCESS_RATE%)" >> "$STEP_BASELINE"
            echo "   Please fix issues in Step 07 before deploying"
            exit 1
        fi
    else
        echo "   ⚠️  Build test report not found - proceed with caution" | tee -a "$STEP_EXECUTION"
        echo "Build Test Status: REPORT_NOT_FOUND" >> "$STEP_BASELINE"
    fi

    # Create deployment logs directory
    mkdir -p "$LATEST_STAGING/deployment_logs"
    DEPLOYMENT_LOG="$LATEST_STAGING/deployment_logs/deployment_$(date +%Y%m%d_%H%M%S).log"

    echo "🚀 Starting deployment execution at $(date)" | tee "$DEPLOYMENT_LOG"
    echo "   Method: $DEPLOY_METHOD" | tee -a "$DEPLOYMENT_LOG"
    echo "   Customization: $CUSTOMIZATION_MODE" | tee -a "$DEPLOYMENT_LOG"

    # Update tracking baseline with deployment info
    echo "Deployment Log: $DEPLOYMENT_LOG" >> "$STEP_BASELINE"
    echo "Started: $(date)" >> "$STEP_BASELINE"

    # Check off deployment method determination in plan
    sed -i.bak 's/- \[ \] Deployment method determined/- [x] Deployment method determined/' "$STEP_PLAN"
    sed -i.bak 's/- \[ \] Deployment assets ready/- [x] Deployment assets ready/' "$STEP_PLAN"

    echo "✅ Pre-deployment verification completed" | tee -a "$STEP_EXECUTION"
    ```

---

## **8.2: Method A - Manual SSH Deployment**

### **Manual SSH Upload and Deployment:**

1. **Method A: Manual SSH execution:**

    ```bash
    if [ "$DEPLOY_METHOD" = "manual_ssh" ]; then
        echo "🔨 Executing Method A: Manual SSH Deployment" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "   Strategy: Upload package via SSH and deploy manually" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"

        # Update tracking plan
        sed -i.bak 's/- \[ \] Method A: Manual SSH deployment/- [x] Method A: Manual SSH deployment/' "$STEP_PLAN"

        # Check deployment package exists
        PACKAGE_DIR="$LATEST_STAGING/deployment_package"
        DEPLOYMENT_PACKAGE="$PACKAGE_DIR/deployment.tar.gz"

        if [ ! -f "$DEPLOYMENT_PACKAGE" ]; then
            echo "   ❌ Deployment package not found: $DEPLOYMENT_PACKAGE" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "   Please run Step 07 to create deployment package"
            echo "Method A Status: FAILED - Package not found" >> "$STEP_BASELINE"
            exit 1
        fi

        PACKAGE_SIZE=$(du -sh "$DEPLOYMENT_PACKAGE" | cut -f1)
        echo "   📦 Deployment package ready: $PACKAGE_SIZE" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "Method A Package: $DEPLOYMENT_PACKAGE ($PACKAGE_SIZE)" >> "$STEP_BASELINE"

        # Get server connection details
        echo "   📋 Manual SSH Deployment Instructions:" | tee -a "$DEPLOYMENT_LOG"
        echo "" | tee -a "$DEPLOYMENT_LOG"
        echo "   🔑 SERVER CONNECTION SETUP:" | tee -a "$DEPLOYMENT_LOG"
        echo "   You need to configure these variables for your server:" | tee -a "$DEPLOYMENT_LOG"
        echo "" | tee -a "$DEPLOYMENT_LOG"

        # Create deployment script template
        cat > "$PACKAGE_DIR/deploy_manual_ssh.sh" << DEPLOY_SCRIPT
    #!/bin/bash

    # =============================================================================
    # Manual SSH Deployment Script for Method A
    # Generated: $(date)
    # =============================================================================

    # CONFIGURE THESE VARIABLES FOR YOUR SERVER
    # -----------------------------------------
    SERVER_HOST="your-server.com"                    # Your server hostname/IP
    SERVER_USER="your-username"                      # Your SSH username
    SERVER_PATH="/var/www/your-app"                  # Your app directory on server
    BACKUP_PATH="/var/www/backups"                   # Backup directory on server

    # DEPLOYMENT PACKAGE INFO
    # -----------------------
    LOCAL_PACKAGE="$(realpath "$DEPLOYMENT_PACKAGE")"
    PACKAGE_NAME="$(basename "$DEPLOYMENT_PACKAGE")"

    echo "🚀 Manual SSH Deployment Script"
    echo "Package: \$LOCAL_PACKAGE"
    echo "Size: $PACKAGE_SIZE"
    echo "Target: \$SERVER_USER@\$SERVER_HOST:\$SERVER_PATH"
    echo ""

    # Check if package exists
    if [ ! -f "\$LOCAL_PACKAGE" ]; then
        echo "❌ Package not found: \$LOCAL_PACKAGE"
        exit 1
    fi

    # Step 1: Test SSH connection
    echo "🔑 Testing SSH connection..."
    if ssh -o ConnectTimeout=10 "\$SERVER_USER@\$SERVER_HOST" "echo 'SSH connection successful'"; then
        echo "✅ SSH connection successful"
    else
        echo "❌ SSH connection failed"
        echo "Please check your SSH configuration and server details"
        exit 1
    fi

    # Step 2: Create backup of current deployment
    echo "💾 Creating backup of current deployment..."
    BACKUP_NAME="backup_\$(date +%Y%m%d_%H%M%S).tar.gz"
    ssh "\$SERVER_USER@\$SERVER_HOST" "
        mkdir -p \$BACKUP_PATH
        cd \$SERVER_PATH
        tar -czf \$BACKUP_PATH/\$BACKUP_NAME . --exclude=storage/logs --exclude=storage/framework/cache --exclude=storage/framework/sessions
        echo 'Backup created: \$BACKUP_PATH/\$BACKUP_NAME'
    "

    # Step 3: Upload deployment package
    echo "📤 Uploading deployment package..."
    if scp "\$LOCAL_PACKAGE" "\$SERVER_USER@\$SERVER_HOST:/tmp/\$PACKAGE_NAME"; then
        echo "✅ Package uploaded successfully"
    else
        echo "❌ Package upload failed"
        exit 1
    fi

    # Step 4: Deploy on server
    echo "🚀 Deploying application on server..."
    ssh "\$SERVER_USER@\$SERVER_HOST" "
        cd \$SERVER_PATH

        # Create temporary deployment directory
        mkdir -p /tmp/deployment_temp
        cd /tmp/deployment_temp

        # Extract package
        echo 'Extracting deployment package...'
        tar -xzf /tmp/\$PACKAGE_NAME

        # Stop any running processes (if applicable)
        echo 'Stopping application services...'
        # sudo systemctl stop your-app 2>/dev/null || echo 'No services to stop'

        # Atomic deployment: move files quickly
        echo 'Deploying files atomically...'
        rsync -av --delete --exclude=.env --exclude=storage/logs/ /tmp/deployment_temp/ \$SERVER_PATH/

        # Set permissions
        echo 'Setting permissions...'
        chown -R www-data:www-data \$SERVER_PATH 2>/dev/null || echo 'Permission setting skipped'
        chmod -R 755 \$SERVER_PATH
        chmod -R 775 \$SERVER_PATH/storage \$SERVER_PATH/bootstrap/cache

        # Run post-deployment commands
        echo 'Running post-deployment commands...'
        cd \$SERVER_PATH

        # Clear Laravel caches
        php artisan config:clear 2>/dev/null || echo 'Config clear skipped'
        php artisan cache:clear 2>/dev/null || echo 'Cache clear skipped'

        # Run migrations (if safe to do so)
        # php artisan migrate --force 2>/dev/null || echo 'Migration skipped'

        # Restart services
        echo 'Restarting application services...'
        # sudo systemctl start your-app 2>/dev/null || echo 'No services to restart'

        # Clean up
        rm -rf /tmp/deployment_temp /tmp/\$PACKAGE_NAME

        echo 'Deployment completed successfully!'
    "

    # Step 5: Verify deployment
    echo "🧪 Verifying deployment..."
    if ssh "\$SERVER_USER@\$SERVER_HOST" "cd \$SERVER_PATH && php artisan --version"; then
        echo "✅ Deployment verification successful"
        echo "🎉 Manual SSH deployment completed successfully!"
    else
        echo "⚠️  Deployment verification failed - check application manually"
    fi

    echo ""
    echo "📋 Post-Deployment Checklist:"
    echo "1. Test application functionality in browser"
    echo "2. Check application logs for errors"
    echo "3. Verify database connectivity"
    echo "4. Test custom functions (if protected mode)"
    echo "5. Monitor performance"
    DEPLOY_SCRIPT

        chmod +x "$PACKAGE_DIR/deploy_manual_ssh.sh"
        echo "   ✅ Deployment script created: $PACKAGE_DIR/deploy_manual_ssh.sh" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "Method A Script: $PACKAGE_DIR/deploy_manual_ssh.sh" >> "$STEP_BASELINE"

        echo "" | tee -a "$DEPLOYMENT_LOG"
        echo "   📋 NEXT STEPS FOR METHOD A:" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "   1. Edit the deployment script with your server details:" | tee -a "$DEPLOYMENT_LOG"
        echo "      $PACKAGE_DIR/deploy_manual_ssh.sh" | tee -a "$DEPLOYMENT_LOG"
        echo "   2. Run the deployment script:" | tee -a "$DEPLOYMENT_LOG"
        echo "      bash $PACKAGE_DIR/deploy_manual_ssh.sh" | tee -a "$DEPLOYMENT_LOG"
        echo "   3. Verify deployment using Step 09" | tee -a "$DEPLOYMENT_LOG"

        # Update tracking plan
        sed -i.bak 's/- \[ \] Configuration files prepared/- [x] Configuration files prepared/' "$STEP_PLAN"

        echo "Method A Status: READY - Script created, manual execution required" >> "$STEP_BASELINE"
        echo "✅ Method A deployment assets prepared successfully" | tee -a "$STEP_EXECUTION"
    fi
    ```

---

## **8.3: Method B - GitHub Actions Deployment**

### **GitHub Actions Automated Deployment:**

1. **Method B: GitHub Actions execution:**

    ```bash
    if [ "$DEPLOY_METHOD" = "github_actions" ]; then
        echo "🔨 Executing Method B: GitHub Actions Deployment" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "   Strategy: Automated build and deployment via GitHub Actions" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"

        # Update tracking plan
        sed -i.bak 's/- \[ \] Method B: GitHub Actions deployment/- [x] Method B: GitHub Actions deployment/' "$STEP_PLAN"

        echo "Method B - GitHub Actions: STARTING" >> "$STEP_BASELINE"

        # Check git status
        if ! git status > /dev/null 2>&1; then
            echo "   ❌ Not a git repository" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method B Status: FAILED - Not a git repository" >> "$STEP_BASELINE"
            exit 1
        fi

        CURRENT_BRANCH=$(git branch --show-current)
        echo "   📋 Current branch: $CURRENT_BRANCH" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "Method B Branch: $CURRENT_BRANCH" >> "$STEP_BASELINE"

        # Check for uncommitted changes
        if ! git diff-index --quiet HEAD --; then
            echo "   ⚠️  Uncommitted changes detected" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "   Committing changes before deployment..." | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"

            git add . >> "$DEPLOYMENT_LOG" 2>&1
            git commit -m "Vendor update deployment - $(date +%Y%m%d_%H%M%S)" >> "$DEPLOYMENT_LOG" 2>&1
            echo "   ✅ Changes committed" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method B Git Status: Changes committed automatically" >> "$STEP_BASELINE"
        else
            echo "Method B Git Status: Repository clean, no uncommitted changes" >> "$STEP_BASELINE"
        fi

        # Check for GitHub Actions workflow
        WORKFLOW_DIR=".github/workflows"
        if [ ! -d "$WORKFLOW_DIR" ]; then
            echo "   📝 Creating GitHub Actions workflow directory..." | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            mkdir -p "$WORKFLOW_DIR"
            echo "Method B Workflow Directory: Created $WORKFLOW_DIR" >> "$STEP_BASELINE"
        else
            echo "Method B Workflow Directory: Exists $WORKFLOW_DIR" >> "$STEP_BASELINE"
        fi

        # Create/update deployment workflow
        WORKFLOW_FILE="$WORKFLOW_DIR/deploy.yml"
        echo "   📝 Creating/updating GitHub Actions workflow..." | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "Method B Workflow File: $WORKFLOW_FILE" >> "$STEP_BASELINE"

        cat > "$WORKFLOW_FILE" << WORKFLOW
    name: Deploy Laravel Application

    on:
      push:
        branches: [ main, master, production ]
      workflow_dispatch:

    jobs:
      deploy:
        runs-on: ubuntu-latest

        steps:
        - name: Checkout code
          uses: actions/checkout@v4

        - name: Setup PHP
          uses: shivammathur/setup-php@v2
          with:
            php-version: '8.1'
            extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql, dom, filter, gd, iconv, json, mbstring, pdo

        - name: Setup Node.js
          uses: actions/setup-node@v4
          with:
            node-version: '18'
            cache: 'npm'

        - name: Cache Composer dependencies
          uses: actions/cache@v3
          with:
            path: vendor
            key: \${{ runner.os }}-composer-\${{ hashFiles('**/composer.lock') }}
            restore-keys: \${{ runner.os }}-composer-

        - name: Install Composer dependencies
          run: composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

        - name: Install NPM dependencies
          run: npm ci

        - name: Build frontend assets
          run: npm run build

        - name: Create deployment artifact
          run: |
            tar --exclude='.git' \\
                --exclude='node_modules' \\
                --exclude='tests' \\
                --exclude='.env*' \\
                --exclude='storage/logs/*' \\
                --exclude='storage/framework/cache/*' \\
                --exclude='storage/framework/sessions/*' \\
                -czf deployment.tar.gz .

        - name: Deploy to server
          env:
            DEPLOY_HOST: \${{ secrets.DEPLOY_HOST }}
            DEPLOY_USER: \${{ secrets.DEPLOY_USER }}
            DEPLOY_KEY: \${{ secrets.DEPLOY_KEY }}
            DEPLOY_PATH: \${{ secrets.DEPLOY_PATH }}
          run: |
            # Setup SSH key
            mkdir -p ~/.ssh
            echo "\$DEPLOY_KEY" > ~/.ssh/deploy_key
            chmod 600 ~/.ssh/deploy_key
            ssh-keyscan -H \$DEPLOY_HOST >> ~/.ssh/known_hosts

            # Upload and deploy
            scp -i ~/.ssh/deploy_key deployment.tar.gz \$DEPLOY_USER@\$DEPLOY_HOST:/tmp/

            ssh -i ~/.ssh/deploy_key \$DEPLOY_USER@\$DEPLOY_HOST '
              cd '"'"'\${{ secrets.DEPLOY_PATH }}'"'"'

              # Backup current deployment
              tar -czf /tmp/backup_\$(date +%Y%m%d_%H%M%S).tar.gz . --exclude=storage/logs 2>/dev/null || true

              # Deploy new version
              tar -xzf /tmp/deployment.tar.gz

              # Set permissions
              chown -R www-data:www-data . 2>/dev/null || true
              chmod -R 755 .
              chmod -R 775 storage bootstrap/cache

              # Post-deployment commands
              php artisan config:clear 2>/dev/null || true
              php artisan cache:clear 2>/dev/null || true
              php artisan migrate --force 2>/dev/null || echo "Migrations skipped"

              # Clean up
              rm /tmp/deployment.tar.gz
            '
    WORKFLOW

        echo "   ✅ GitHub Actions workflow created/updated" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"

        # Commit workflow if new/changed
        if git diff --quiet HEAD -- "$WORKFLOW_FILE" 2>/dev/null; then
            echo "   ℹ️  Workflow unchanged" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method B Workflow Status: No changes to commit" >> "$STEP_BASELINE"
        else
            git add "$WORKFLOW_FILE" >> "$DEPLOYMENT_LOG" 2>&1
            git commit -m "Add/update GitHub Actions deployment workflow" >> "$DEPLOYMENT_LOG" 2>&1
            echo "   ✅ Workflow committed" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method B Workflow Status: New/updated workflow committed" >> "$STEP_BASELINE"
        fi

        # Push to trigger deployment
        echo "   📤 Pushing to GitHub to trigger deployment..." | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        if git push origin "$CURRENT_BRANCH" >> "$DEPLOYMENT_LOG" 2>&1; then
            echo "   ✅ Pushed to GitHub - deployment triggered" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method B Push Status: SUCCESS - Deployment triggered" >> "$STEP_BASELINE"
        else
            echo "   ❌ Push failed" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method B Push Status: FAILED - Check logs for details" >> "$STEP_BASELINE"
            tail -5 "$DEPLOYMENT_LOG"
            exit 1
        fi

        # Update tracking plan
        sed -i.bak 's/- \[ \] Configuration files prepared/- [x] Configuration files prepared/' "$STEP_PLAN"

        echo "" | tee -a "$DEPLOYMENT_LOG"
        echo "   📋 NEXT STEPS FOR METHOD B:" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "   1. Check GitHub Actions tab in your repository" | tee -a "$DEPLOYMENT_LOG"
        echo "   2. Monitor the deployment workflow progress" | tee -a "$DEPLOYMENT_LOG"
        echo "   3. Configure these GitHub Secrets in repository settings:" | tee -a "$DEPLOYMENT_LOG"
        echo "      - DEPLOY_HOST: your-server.com" | tee -a "$DEPLOYMENT_LOG"
        echo "      - DEPLOY_USER: your-username" | tee -a "$DEPLOYMENT_LOG"
        echo "      - DEPLOY_KEY: your-private-ssh-key" | tee -a "$DEPLOYMENT_LOG"
        echo "      - DEPLOY_PATH: /var/www/your-app" | tee -a "$DEPLOYMENT_LOG"
        echo "   4. Re-run workflow after configuring secrets" | tee -a "$DEPLOYMENT_LOG"
        echo "   5. Verify deployment using Step 09" | tee -a "$DEPLOYMENT_LOG"

        echo "Method B Status: READY - Workflow created and triggered" >> "$STEP_BASELINE"
        echo "✅ Method B deployment workflow prepared and triggered successfully" | tee -a "$STEP_EXECUTION"
    fi
    ```

---

## **8.4: Method C - DeployHQ Professional Deployment**

### **DeployHQ Professional Service Deployment:**

1. **Method C: DeployHQ execution:**

    ```bash
    if [ "$DEPLOY_METHOD" = "deployhq" ]; then
        echo "🔨 Executing Method C: DeployHQ Professional Deployment" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "   Strategy: Professional build and deployment service" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"

        # Update tracking plan
        sed -i.bak 's/- \[ \] Method C: DeployHQ deployment/- [x] Method C: DeployHQ deployment/' "$STEP_PLAN"

        echo "Method C - DeployHQ: STARTING" >> "$STEP_BASELINE"

        # Check git status
        if ! git status > /dev/null 2>&1; then
            echo "   ❌ Not a git repository" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method C Status: FAILED - Not a git repository" >> "$STEP_BASELINE"
            exit 1
        fi

        CURRENT_BRANCH=$(git branch --show-current)
        echo "   📋 Current branch: $CURRENT_BRANCH" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "Method C Branch: $CURRENT_BRANCH" >> "$STEP_BASELINE"

        # Check for uncommitted changes
        if ! git diff-index --quiet HEAD --; then
            echo "   ⚠️  Uncommitted changes detected" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "   Committing changes before deployment..." | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"

            git add . >> "$DEPLOYMENT_LOG" 2>&1
            git commit -m "Vendor update deployment - $(date +%Y%m%d_%H%M%S)" >> "$DEPLOYMENT_LOG" 2>&1
            echo "   ✅ Changes committed" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method C Git Status: Changes committed automatically" >> "$STEP_BASELINE"
        else
            echo "Method C Git Status: Repository clean, no uncommitted changes" >> "$STEP_BASELINE"
        fi

        # Create/update DeployHQ configuration
        DEPLOYHQ_CONFIG=".deployhq"
        echo "   📝 Creating/updating DeployHQ configuration..." | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"

        cat > "$DEPLOYHQ_CONFIG" << DEPLOYHQ
    # DeployHQ Configuration for Laravel Application
    # Generated: $(date)

    [build]
    # Build commands that DeployHQ will execute
    commands = [
        "composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction",
        "npm ci",
        "npm run build",
        "php artisan config:cache",
        "php artisan route:cache",
        "php artisan view:cache"
    ]

    [deploy]
    # Files/directories to exclude from deployment
    exclude = [
        ".git",
        ".gitignore",
        "node_modules",
        "tests",
        ".env*",
        "storage/logs/*",
        "storage/framework/cache/*",
        "storage/framework/sessions/*",
        ".deployhq",
        "webpack.mix.js",
        "package.json",
        "package-lock.json"
    ]

    # Post-deployment commands
    post_commands = [
        "chown -R www-data:www-data {DEPLOY_PATH}",
        "chmod -R 755 {DEPLOY_PATH}",
        "chmod -R 775 {DEPLOY_PATH}/storage {DEPLOY_PATH}/bootstrap/cache",
        "cd {DEPLOY_PATH} && php artisan config:clear",
        "cd {DEPLOY_PATH} && php artisan cache:clear"
    ]
    DEPLOYHQ

        echo "Method C Config File: $DEPLOYHQ_CONFIG created successfully" >> "$STEP_BASELINE"

        # Create detailed deployment instructions
        DEPLOYHQ_INSTRUCTIONS="$LATEST_STAGING/deployment_logs/DEPLOYHQ_SETUP_INSTRUCTIONS.md"
        echo "   📝 Creating DeployHQ setup instructions..." | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "Method C Instructions: $DEPLOYHQ_INSTRUCTIONS" >> "$STEP_BASELINE"
        cat > "$DEPLOYHQ_INSTRUCTIONS" << INSTRUCTIONS
    # DeployHQ Professional Deployment Setup

    **Generated:** $(date)
    **Method:** C - DeployHQ Professional

    ## 1. DeployHQ Account Setup

    1. **Sign up for DeployHQ:** https://www.deployhq.com/
    2. **Create new project** in DeployHQ dashboard
    3. **Connect repository:** Link your Git repository to DeployHQ

    ## 2. Project Configuration

    ### Repository Settings
    - **Repository URL:** $(git config --get remote.origin.url 2>/dev/null || echo "Configure your Git remote")
    - **Branch:** $CURRENT_BRANCH
    - **Build Pack:** Auto-detect (PHP/Laravel + Node.js)

    ### Build Configuration
    Add these build commands in DeployHQ project settings:
    \`\`\`bash
    composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
    npm ci
    npm run build
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    \`\`\`

    ### Server Configuration
    - **Protocol:** SFTP/SSH
    - **Hostname:** your-server.com
    - **Username:** your-username
    - **Port:** 22
    - **Deployment Path:** /var/www/your-app

    ### Excluded Files
    Configure these exclusions in DeployHQ:
    \`\`\`
    .git
    .gitignore
    node_modules
    tests
    .env*
    storage/logs/*
    storage/framework/cache/*
    storage/framework/sessions/*
    .deployhq
    webpack.mix.js
    package.json
    package-lock.json
    \`\`\`

    ### Post-Deployment Commands
    Add these commands to run after deployment:
    \`\`\`bash
    chown -R www-data:www-data {DEPLOY_PATH}
    chmod -R 755 {DEPLOY_PATH}
    chmod -R 775 {DEPLOY_PATH}/storage {DEPLOY_PATH}/bootstrap/cache
    cd {DEPLOY_PATH} && php artisan config:clear
    cd {DEPLOY_PATH} && php artisan cache:clear
    \`\`\`

    ## 3. Environment Variables

    Configure these in DeployHQ environment settings:
    - **APP_ENV=production**
    - **APP_DEBUG=false**
    - **APP_KEY=** (your application key)
    - **Database settings**
    - **Mail settings**
    - **Any custom environment variables**

    ## 4. Deployment Execution

    1. **Automatic Deployment:** Push to $CURRENT_BRANCH to trigger deployment
    2. **Manual Deployment:** Use DeployHQ dashboard "Deploy Now" button
    3. **Monitor:** Watch deployment progress in DeployHQ dashboard

    ## 5. Customization Handling

    **Mode:** $CUSTOMIZATION_MODE

    $(if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
        echo "- ✅ app/Custom/ directory will be preserved"
        echo "- ⚠️  Ensure custom files are in Git repository"
        echo "- 🔧 Test custom functions after deployment"
    else
        echo "- ✅ Simple mode - no custom files to preserve"
    fi)

    ## 6. Post-Deployment Verification

    After deployment completes:
    1. Check application URL responds correctly
    2. Verify database connectivity
    3. Test application functionality
    4. Monitor logs for errors
    $(if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
        echo "5. Test custom functions work correctly"
    fi)

    ## 7. Troubleshooting

    - **Build fails:** Check build logs in DeployHQ
    - **Deployment fails:** Verify server credentials
    - **App errors:** Check Laravel logs on server
    - **Permission issues:** Verify post-deployment commands
    INSTRUCTIONS

        # Commit configuration if new/changed
        if git diff --quiet HEAD -- "$DEPLOYHQ_CONFIG" 2>/dev/null; then
            echo "   ℹ️  DeployHQ config unchanged" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method C Config Status: No changes to commit" >> "$STEP_BASELINE"
        else
            git add "$DEPLOYHQ_CONFIG" >> "$DEPLOYMENT_LOG" 2>&1
            git commit -m "Add/update DeployHQ configuration" >> "$DEPLOYMENT_LOG" 2>&1
            echo "   ✅ DeployHQ config committed" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method C Config Status: New/updated config committed" >> "$STEP_BASELINE"
        fi

        # Push changes
        echo "   📤 Pushing changes to repository..." | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        if git push origin "$CURRENT_BRANCH" >> "$DEPLOYMENT_LOG" 2>&1; then
            echo "   ✅ Changes pushed to repository" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method C Push Status: SUCCESS" >> "$STEP_BASELINE"
        else
            echo "   ❌ Push failed" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method C Push Status: FAILED - Check logs for details" >> "$STEP_BASELINE"
            tail -5 "$DEPLOYMENT_LOG"
            exit 1
        fi

        # Update tracking plan
        sed -i.bak 's/- \[ \] Configuration files prepared/- [x] Configuration files prepared/' "$STEP_PLAN"

        echo "" | tee -a "$DEPLOYMENT_LOG"
        echo "   📋 NEXT STEPS FOR METHOD C:" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "   1. Follow setup instructions: $DEPLOYHQ_INSTRUCTIONS" | tee -a "$DEPLOYMENT_LOG"
        echo "   2. Configure DeployHQ project with your repository" | tee -a "$DEPLOYMENT_LOG"
        echo "   3. Set up server connection in DeployHQ" | tee -a "$DEPLOYMENT_LOG"
        echo "   4. Configure environment variables" | tee -a "$DEPLOYMENT_LOG"
        echo "   5. Trigger deployment from DeployHQ dashboard" | tee -a "$DEPLOYMENT_LOG"
        echo "   6. Verify deployment using Step 09" | tee -a "$DEPLOYMENT_LOG"

        echo "Method C Status: READY - Instructions and config created, manual setup required" >> "$STEP_BASELINE"
        echo "✅ Method C deployment assets prepared and instructions provided" | tee -a "$STEP_EXECUTION"
    fi
    ```

---

## **8.5: Method D - GitHub + Manual Build Deployment**

### **GitHub Source + Manual Build Artifact Deployment:**

1. **Method D: GitHub + Manual execution:**

    ```bash
    if [ "$DEPLOY_METHOD" = "github_manual" ]; then
        echo "🔨 Executing Method D: GitHub + Manual Build Deployment" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "   Strategy: Source from GitHub + Local build + Artifact deployment" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"

        # Update tracking plan
        sed -i.bak 's/- \[ \] Method D: GitHub + Manual deployment/- [x] Method D: GitHub + Manual deployment/' "$STEP_PLAN"

        echo "Method D - GitHub + Manual: STARTING" >> "$STEP_BASELINE"

        # Check git status
        if ! git status > /dev/null 2>&1; then
            echo "   ❌ Not a git repository" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method D Status: FAILED - Not a git repository" >> "$STEP_BASELINE"
            exit 1
        fi

        CURRENT_BRANCH=$(git branch --show-current)
        echo "   📋 Current branch: $CURRENT_BRANCH" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "Method D Branch: $CURRENT_BRANCH" >> "$STEP_BASELINE"

        # Check for uncommitted changes
        if ! git diff-index --quiet HEAD --; then
            echo "   ⚠️  Uncommitted changes detected" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "   Committing changes before deployment..." | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"

            git add . >> "$DEPLOYMENT_LOG" 2>&1
            git commit -m "Vendor update deployment - $(date +%Y%m%d_%H%M%S)" >> "$DEPLOYMENT_LOG" 2>&1
            echo "   ✅ Changes committed" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method D Git Status: Changes committed automatically" >> "$STEP_BASELINE"
        else
            echo "Method D Git Status: Repository clean, no uncommitted changes" >> "$STEP_BASELINE"
        fi

        # Push to GitHub first
        echo "   📤 Pushing to GitHub..." | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        if git push origin "$CURRENT_BRANCH" >> "$DEPLOYMENT_LOG" 2>&1; then
            echo "   ✅ Pushed to GitHub" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method D Push Status: SUCCESS" >> "$STEP_BASELINE"
        else
            echo "   ❌ Push failed" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "Method D Push Status: FAILED - Check logs for details" >> "$STEP_BASELINE"
            tail -5 "$DEPLOYMENT_LOG"
            exit 1
        fi

        # Check artifact exists from Step 07
        ARTIFACT_DIR="$LATEST_STAGING/github_artifacts"
        PRODUCTION_ARTIFACT="$ARTIFACT_DIR/production_artifact.tar.gz"

        if [ ! -f "$PRODUCTION_ARTIFACT" ]; then
            echo "   ❌ Production artifact not found: $PRODUCTION_ARTIFACT" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
            echo "   Please run Step 07 to create production artifact" | tee -a "$STEP_EXECUTION"
            echo "Method D Artifact Status: FAILED - Artifact not found" >> "$STEP_BASELINE"
            exit 1
        fi

        ARTIFACT_SIZE=$(du -sh "$PRODUCTION_ARTIFACT" | cut -f1)
        echo "   📦 Production artifact ready: $ARTIFACT_SIZE" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "Method D Artifact: $PRODUCTION_ARTIFACT ($ARTIFACT_SIZE)" >> "$STEP_BASELINE"

        # Create enhanced deployment script for Method D
        cat > "$ARTIFACT_DIR/deploy_github_manual.sh" << DEPLOY_D_SCRIPT
    #!/bin/bash

    # =============================================================================
    # Method D: GitHub + Manual Build Deployment Script
    # Generated: $(date)
    # =============================================================================

    # CONFIGURE THESE VARIABLES FOR YOUR SERVER
    # -----------------------------------------
    SERVER_HOST="your-server.com"                    # Your server hostname/IP
    SERVER_USER="your-username"                      # Your SSH username
    SERVER_PATH="/var/www/your-app"                  # Your app directory on server
    BACKUP_PATH="/var/www/backups"                   # Backup directory on server

    # GITHUB REPOSITORY INFO
    # ----------------------
    GITHUB_REPO="$(git config --get remote.origin.url | sed 's/\.git\$//' | sed 's|.*github\.com[/:]||')"
    CURRENT_BRANCH="$CURRENT_BRANCH"

    # ARTIFACT INFO
    # -------------
    LOCAL_ARTIFACT="$(realpath "$PRODUCTION_ARTIFACT")"
    ARTIFACT_NAME="$(basename "$PRODUCTION_ARTIFACT")"

    echo "🚀 Method D: GitHub + Manual Build Deployment"
    echo "Source: GitHub repository \$GITHUB_REPO (branch: \$CURRENT_BRANCH)"
    echo "Artifact: \$LOCAL_ARTIFACT"
    echo "Size: $ARTIFACT_SIZE"
    echo "Target: \$SERVER_USER@\$SERVER_HOST:\$SERVER_PATH"
    echo ""
    echo "📋 Method D Strategy:"
    echo "1. ✅ Source code: GitHub repository"
    echo "2. ✅ Build: Local production build (completed)"
    echo "3. 🚀 Deploy: Upload production artifact (runtime only)"
    echo ""

    # Check if artifact exists
    if [ ! -f "\$LOCAL_ARTIFACT" ]; then
        echo "❌ Production artifact not found: \$LOCAL_ARTIFACT"
        echo "Please run Step 07 to create the production artifact"
        exit 1
    fi

    # Verify artifact contains build files
    echo "🔍 Verifying production artifact contents..."
    if tar -tzf "\$LOCAL_ARTIFACT" | grep -q "public/build\|bootstrap/cache"; then
        echo "✅ Artifact contains production build files"
    else
        echo "❌ Artifact missing production build files"
        echo "Please re-run Step 06 and Step 07 to create proper build"
        exit 1
    fi

    # Step 1: Test SSH connection
    echo "🔑 Testing SSH connection..."
    if ssh -o ConnectTimeout=10 "\$SERVER_USER@\$SERVER_HOST" "echo 'SSH connection successful'"; then
        echo "✅ SSH connection successful"
    else
        echo "❌ SSH connection failed"
        echo "Please check your SSH configuration and server details"
        exit 1
    fi

    # Step 2: Create backup of current deployment
    echo "💾 Creating backup of current deployment..."
    BACKUP_NAME="backup_\$(date +%Y%m%d_%H%M%S).tar.gz"
    ssh "\$SERVER_USER@\$SERVER_HOST" "
        mkdir -p \$BACKUP_PATH
        cd \$SERVER_PATH
        tar -czf \$BACKUP_PATH/\$BACKUP_NAME . --exclude=storage/logs --exclude=storage/framework/cache --exclude=storage/framework/sessions 2>/dev/null || echo 'Backup creation had issues'
        echo 'Backup created: \$BACKUP_PATH/\$BACKUP_NAME'
    "

    # Step 3: Upload production artifact
    echo "📤 Uploading production artifact..."
    if scp "\$LOCAL_ARTIFACT" "\$SERVER_USER@\$SERVER_HOST:/tmp/\$ARTIFACT_NAME"; then
        echo "✅ Production artifact uploaded successfully"
    else
        echo "❌ Artifact upload failed"
        exit 1
    fi

    # Step 4: Deploy production artifact on server
    echo "🚀 Deploying production artifact on server..."
    ssh "\$SERVER_USER@\$SERVER_HOST" "
        cd \$SERVER_PATH

        # Create temporary deployment directory
        mkdir -p /tmp/deployment_temp_d
        cd /tmp/deployment_temp_d

        # Extract production artifact
        echo 'Extracting production artifact...'
        tar -xzf /tmp/\$ARTIFACT_NAME

        # Verify artifact extraction
        if [ ! -d 'public/build' ] || [ ! -f 'bootstrap/cache/config.php' ]; then
            echo 'Warning: Production build files may be missing from artifact'
        else
            echo 'Production build files verified in artifact'
        fi

        # Stop any running processes (if applicable)
        echo 'Stopping application services...'
        # sudo systemctl stop your-app 2>/dev/null || echo 'No services to stop'

        # Atomic deployment: move production files quickly
        echo 'Deploying production files atomically...'
        rsync -av --delete --exclude=.env --exclude=storage/logs/ /tmp/deployment_temp_d/ \$SERVER_PATH/

        # Set permissions
        echo 'Setting permissions...'
        chown -R www-data:www-data \$SERVER_PATH 2>/dev/null || echo 'Permission setting skipped'
        chmod -R 755 \$SERVER_PATH
        chmod -R 775 \$SERVER_PATH/storage \$SERVER_PATH/bootstrap/cache

        # Post-deployment commands (minimal since artifact is pre-built)
        echo 'Running post-deployment commands...'
        cd \$SERVER_PATH

        # Clear any cached configs (but shouldn't be needed with artifact)
        php artisan config:clear 2>/dev/null || echo 'Config clear skipped'

        # NOTE: No need to run build commands - artifact is pre-built!
        # No need for: composer install, npm install, npm run build
        # This is the key difference of Method D!

        # Run migrations (if safe to do so)
        # php artisan migrate --force 2>/dev/null || echo 'Migration skipped'

        # Restart services
        echo 'Restarting application services...'
        # sudo systemctl start your-app 2>/dev/null || echo 'No services to restart'

        # Clean up
        rm -rf /tmp/deployment_temp_d /tmp/\$ARTIFACT_NAME

        echo 'Method D deployment completed successfully!'
        echo 'Deployed: Production artifact (runtime only, no source code)'
    "

    # Step 5: Verify deployment
    echo "🧪 Verifying deployment..."
    if ssh "\$SERVER_USER@\$SERVER_HOST" "cd \$SERVER_PATH && php artisan --version"; then
        echo "✅ Deployment verification successful"
        echo "🎉 Method D (GitHub + Manual) deployment completed successfully!"
    else
        echo "⚠️  Deployment verification failed - check application manually"
    fi

    echo ""
    echo "📋 Method D Deployment Summary:"
    echo "✅ Source: Pulled from GitHub repository \$GITHUB_REPO"
    echo "✅ Build: Local production build artifact"
    echo "✅ Deploy: Runtime files only (no source code on server)"
    echo "✅ Server: No build process required"
    echo ""
    echo "📋 Post-Deployment Checklist:"
    echo "1. Test application functionality in browser"
    echo "2. Check application logs for errors"
    echo "3. Verify database connectivity"
    echo "4. Test custom functions (if protected mode)"
    echo "5. Monitor performance"
    echo "6. Confirm no source files on server (security benefit)"
    DEPLOY_D_SCRIPT

        chmod +x "$ARTIFACT_DIR/deploy_github_manual.sh"
        echo "   ✅ Method D deployment script created: $ARTIFACT_DIR/deploy_github_manual.sh" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "Method D Script: $ARTIFACT_DIR/deploy_github_manual.sh created successfully" >> "$STEP_BASELINE"

        # Create Method D summary
        echo "   📝 Creating Method D deployment summary..." | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        cat > "$ARTIFACT_DIR/METHOD_D_DEPLOYMENT_SUMMARY.md" << METHOD_D_SUMMARY
    # Method D: GitHub + Manual Build Deployment Summary

    **Generated:** $(date)
    **Source Repository:** $(git config --get remote.origin.url 2>/dev/null || echo "Not configured")
    **Branch:** $CURRENT_BRANCH
    **Artifact Size:** $ARTIFACT_SIZE

    ## Method D Advantages

    1. **Security:** No source code on production server
    2. **Performance:** Pre-built artifacts, no server build time
    3. **Reliability:** Local build testing before deployment
    4. **Flexibility:** GitHub source + manual control
    5. **Customization:** Preserves app/Custom/ in artifact

    ## Deployment Workflow

    1. **Source:** ✅ Code stored in GitHub repository
    2. **Build:** ✅ Local production build with all optimizations
    3. **Package:** ✅ Create runtime artifact (no source files)
    4. **Deploy:** 🚀 Upload and extract artifact on server
    5. **Verify:** 📋 Test functionality with Step 09

    ## What's Deployed (Runtime Only)

    - ✅ Built frontend assets (public/build/)
    - ✅ Optimized PHP dependencies (vendor/)
    - ✅ Cached Laravel configurations
    - ✅ Application runtime files
    - ✅ Custom functions (if protected mode)

    ## What's NOT Deployed (Security)

    - ❌ Source JavaScript/TypeScript files
    - ❌ Source SCSS/CSS files
    - ❌ Node.js dependencies (node_modules/)
    - ❌ Build configuration files
    - ❌ Development/testing files
    - ❌ Git repository

    ## Customization Handling

    **Mode:** $CUSTOMIZATION_MODE

    $(if [ "$CUSTOMIZATION_MODE" = "protected" ]; then
        echo "- ✅ app/Custom/ directory included in artifact"
        echo "- ✅ Custom functions preserved and deployable"
        echo "- 🔧 Custom files will be tested in Step 09"
    else
        echo "- ✅ Simple mode - no custom files to handle"
    fi)

    ## Next Steps

    1. Edit deployment script with your server details
    2. Run: bash $ARTIFACT_DIR/deploy_github_manual.sh
    3. Verify deployment with Step 09
    METHOD_D_SUMMARY

        # Update tracking plan
        sed -i.bak 's/- \[ \] Configuration files prepared/- [x] Configuration files prepared/' "$STEP_PLAN"

        echo "" | tee -a "$DEPLOYMENT_LOG"
        echo "   📋 NEXT STEPS FOR METHOD D:" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "   1. Review Method D summary: $ARTIFACT_DIR/METHOD_D_DEPLOYMENT_SUMMARY.md" | tee -a "$DEPLOYMENT_LOG"
        echo "   2. Edit deployment script with your server details:" | tee -a "$DEPLOYMENT_LOG"
        echo "      $ARTIFACT_DIR/deploy_github_manual.sh" | tee -a "$DEPLOYMENT_LOG"
        echo "   3. Run the deployment script:" | tee -a "$DEPLOYMENT_LOG"
        echo "      bash $ARTIFACT_DIR/deploy_github_manual.sh" | tee -a "$DEPLOYMENT_LOG"
        echo "   4. Verify deployment using Step 09" | tee -a "$DEPLOYMENT_LOG"
        echo "" | tee -a "$DEPLOYMENT_LOG"
        echo "   🎯 Method D Key Benefit: Production server gets runtime artifact only (no source code)" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"

        echo "Method D Status: READY - Scripts and artifacts prepared, manual execution required" >> "$STEP_BASELINE"
        echo "Method D Summary: $ARTIFACT_DIR/METHOD_D_DEPLOYMENT_SUMMARY.md" >> "$STEP_BASELINE"
        echo "✅ Method D deployment assets prepared and ready for execution" | tee -a "$STEP_EXECUTION"
    fi
    ```

---

## **8.6: Post-Deployment Summary**

### **Create Deployment Summary:**

1. **Generate deployment summary:**

    ```bash
    echo "📊 Creating deployment summary..." | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"

    # Update tracking plan
    sed -i.bak 's/- \[ \] Deployment summary created/- [x] Deployment summary created/' "$STEP_PLAN"

    DEPLOYMENT_SUMMARY="$LATEST_STAGING/DEPLOYMENT_SUMMARY.md"
    echo "Creating deployment summary: $DEPLOYMENT_SUMMARY" >> "$STEP_BASELINE"

    cat > "$DEPLOYMENT_SUMMARY" << SUMMARY
    # Deployment Summary

    **Deployment Date:** $(date)
    **Deployment Method:** $DEPLOY_METHOD
    **Customization Mode:** $CUSTOMIZATION_MODE
    **Branch:** $(git branch --show-current 2>/dev/null || echo "Not available")

    ## Deployment Execution

    $(case "$DEPLOY_METHOD" in
        "manual_ssh")
            echo "**Method A: Manual SSH**"
            echo "- ✅ Deployment package created and ready"
            echo "- 📋 Manual deployment script provided"
            echo "- 🔑 SSH configuration required"
            echo "- 📦 Package: $LATEST_STAGING/deployment_package/deployment.tar.gz"
            ;;
        "github_actions")
            echo "**Method B: GitHub Actions**"
            echo "- ✅ Code pushed to GitHub"
            echo "- 🔄 GitHub Actions workflow triggered"
            echo "- 🤖 Automated build and deployment"
            echo "- 🔐 GitHub Secrets configuration required"
            ;;
        "deployhq")
            echo "**Method C: DeployHQ Professional**"
            echo "- ✅ Code pushed to repository"
            echo "- 🏢 Professional deployment service"
            echo "- 📋 DeployHQ setup instructions provided"
            echo "- ⚙️  Service configuration required"
            ;;
        "github_manual")
            echo "**Method D: GitHub + Manual Build**"
            echo "- ✅ Code pushed to GitHub"
            echo "- 🏗️  Production artifact created locally"
            echo "- 📦 Runtime deployment (no source code)"
            echo "- 🔒 Enhanced security (artifact deployment)"
            echo "- 📦 Artifact: $LATEST_STAGING/github_artifacts/production_artifact.tar.gz"
            ;;
    esac)

    ## Files Changed in Update

    - **Vendor Files:** Updated from new CodeCanyon version
    - **Dependencies:** $([ "$PHP_DEPS_CHANGED" = true ] && echo "PHP updated" || echo "PHP verified"), $([ "$JS_DEPS_CHANGED" = true ] && echo "Frontend updated" || echo "Frontend verified")
    - **Custom Files:** $([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "Preserved in app/Custom/" || echo "None (simple mode)")

    ## Deployment Assets

    $(case "$DEPLOY_METHOD" in
        "manual_ssh")
            echo "- **Deployment Script:** $LATEST_STAGING/deployment_package/deploy_manual_ssh.sh"
            echo "- **Package Size:** $(du -sh "$LATEST_STAGING/deployment_package/deployment.tar.gz" 2>/dev/null | cut -f1 || echo "N/A")"
            ;;
        "github_actions")
            echo "- **Workflow File:** .github/workflows/deploy.yml"
            echo "- **Repository:** $(git config --get remote.origin.url 2>/dev/null || echo "Not configured")"
            ;;
        "deployhq")
            echo "- **Instructions:** $LATEST_STAGING/deployment_logs/DEPLOYHQ_SETUP_INSTRUCTIONS.md"
            echo "- **Config File:** .deployhq"
            ;;
        "github_manual")
            echo "- **Deployment Script:** $LATEST_STAGING/github_artifacts/deploy_github_manual.sh"
            echo "- **Artifact Size:** $(du -sh "$LATEST_STAGING/github_artifacts/production_artifact.tar.gz" 2>/dev/null | cut -f1 || echo "N/A")"
            echo "- **Summary:** $LATEST_STAGING/github_artifacts/METHOD_D_DEPLOYMENT_SUMMARY.md"
            ;;
    esac)

    ## Next Steps

    1. **Execute Deployment:** Follow method-specific instructions above
    2. **Monitor Process:** Watch for deployment completion
    3. **Verify Deployment:** Continue to Step 09 for verification
    4. **Test Application:** Ensure all functionality works correctly
    $([ "$CUSTOMIZATION_MODE" = "protected" ] && echo "5. **Test Custom Functions:** Verify customizations work correctly")

    ## Deployment Logs

    - **Main Log:** $DEPLOYMENT_LOG
    - **All Logs:** $LATEST_STAGING/deployment_logs/

    ## Support Information

    If deployment fails:
    1. Check method-specific logs and instructions
    2. Verify server connectivity and credentials
    3. Review application logs on server
    4. Test rollback procedures if needed
    SUMMARY

    echo "✅ Deployment summary created: $DEPLOYMENT_SUMMARY" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
    echo "Deployment Summary: $DEPLOYMENT_SUMMARY created successfully" >> "$STEP_BASELINE"
    ```

2. **Update the update log:**

    ```bash
    # Update the current update log with project-agnostic paths
    LATEST_LOG=$(find "$ADMIN_LOCAL/update_logs" -name "update_*.md" | sort | tail -1)

    if [ -n "$LATEST_LOG" ]; then
        # Mark Step 08 as complete
        sed -i.bak 's/- \[ \] Step 08: Deploy Updates/- [x] Step 08: Deploy Updates/' "$LATEST_LOG"

        # Add Step 08 completion details
        cat >> "$LATEST_LOG" << LOG_UPDATE

    ## Step 08 Completed
    - **Deployment Method:** $DEPLOY_METHOD
    - **Status:** ✅ Deployment initiated
    - **Assets:** $(case "$DEPLOY_METHOD" in
        "manual_ssh") echo "Package + script ready" ;;
        "github_actions") echo "Workflow triggered" ;;
        "deployhq") echo "Instructions provided" ;;
        "github_manual") echo "Artifact + script ready" ;;
    esac)
    - **Summary:** $DEPLOYMENT_SUMMARY
    - **Next:** Execute deployment and run Step 09
    LOG_UPDATE

        echo "✅ Update log updated: $LATEST_LOG" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "Update Log: $LATEST_LOG updated with Step 08 completion" >> "$STEP_BASELINE"

        # Update tracking plan
        sed -i.bak 's/- \[ \] Update logs updated/- [x] Update logs updated/' "$STEP_PLAN"
    else
        echo "⚠️  No update log found to update" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
        echo "Update Log Status: NOT_FOUND - No update log to update" >> "$STEP_BASELINE"
    fi

    # Complete step tracking
    echo "Step 08 Completion: $(date)" >> "$STEP_BASELINE"
    echo "Step 08 Final Status: COMPLETED - All deployment assets prepared" >> "$STEP_BASELINE"

    # Update tracking plan final status
    sed -i.bak 's/- \[ \] Step 08 completed successfully/- [x] Step 08 completed successfully/' "$STEP_PLAN"

    echo "" | tee -a "$DEPLOYMENT_LOG"
    echo "🎉 Step 08 deployment preparation completed!" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
    echo "📋 Follow your method-specific instructions to execute deployment" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"
    echo "🔍 After deployment, continue to Step 09 for verification" | tee -a "$DEPLOYMENT_LOG" | tee -a "$STEP_EXECUTION"

    # Final tracking completion message
    echo "✅ Step 08 tracking integration completed successfully" | tee -a "$STEP_EXECUTION"
    echo "📝 All tracking files updated: Plan, Baseline, Execution log" | tee -a "$STEP_EXECUTION"
    ```

---

## **✅ Step 08 Completion Checklist**

-   [ ] Pre-deployment verification completed
-   [ ] Method-specific deployment assets created
-   [ ] Scripts/workflows/instructions provided
-   [ ] Code committed and pushed (where applicable)
-   [ ] Deployment packages/artifacts ready
-   [ ] Configuration files created
-   [ ] Deployment summary generated

---

## **Next Steps**

**All Methods:** After executing your deployment, continue to [Step 09: Verify Deployment](Step_09_Verify_Deployment.md)

**Method-specific actions required:**

-   **Method A:** Run deployment script with your server details
-   **Method B:** Configure GitHub Secrets and monitor workflow
-   **Method C:** Set up DeployHQ project and trigger deployment
-   **Method D:** Run deployment script to upload production artifact

**Key files to use:**

-   **Summary:** `$DEPLOYMENT_SUMMARY`
-   **Logs:** `$LATEST_STAGING/deployment_logs/`

---

## **Troubleshooting**

### **Issue: Git push fails**

```bash
# Check remote configuration
git remote -v
git push origin $(git branch --show-current) --force-with-lease
```

### **Issue: SSH connection fails**

```bash
# Test SSH connection
ssh -vvv user@server
ssh-keygen -R server-hostname  # Remove old host key
```

### **Issue: Package/artifact missing**

```bash
# Re-run previous steps
bash Step_06_Update_Dependencies.md
bash Step_07_Test_Build_Process.md
```
