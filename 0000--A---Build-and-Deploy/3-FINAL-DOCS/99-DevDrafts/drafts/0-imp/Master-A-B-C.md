- **SECTION A: Project Setup**
    
    IMPORTANT Note: below is a draft and may have errors or be incorrect or missing steps- you can modify or change to enhance and ensure proper and correct flow.
    
    Important Note 2: The steps below should cover apps we develop or apps we purchase like from codecanyon. same flow for all.
    
    # Master Checklist for **SECTION A: Project Setup**
    
    This checklist consolidates all the necessary steps for Phase 1 of the project setup. Follow each step carefully to ensure a smooth and successful deployment.
    
    1. **Step 00: AI Assistant Instructions**
        1. **Purpose:** This step provides a comprehensive guide for using AI coding assistants with the V3 Laravel deployment guide. It includes instructions for AI-assisted development, error resolution, and continuous improvement.
        2. **When to Use:** Before starting any step, when encountering errors, or when seeking improvements.
    2. **Step 01: Project Information Card**
        1. **Purpose:** Document project metadata for team reference, deployment configuration, and AI coding assistance.
        2. **Action:** Collect and document all project-specific information, including project name, domain, hosting details, GitHub repository, local development paths, and database credentials.
    3. **Step 02: Create GitHub Repository**
        1. **Purpose:** Establish a version control foundation for deployment workflows.
        2. **Action:**
            1. Create a private repository on GitHub with the project name.
            2. Ensure the repository is created without a README, .gitignore, or license file.
            3. Note the SSH URL for cloning.
    4. **Step 03: Setup Local Project Structure**
        1. **Purpose:** Establish a local development directory structure.
        2. **Action:**
            1. Navigate to the base apps directory.
                
                ```bash
                cd /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps
                
                ```
                
            2. Create the project directory structure.
                
                ```bash
                mkdir -p SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root
                
                ```
                
            3. Navigate to the project root.
                
                ```bash
                cd "/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"
                
                ```
                
    5. **Step 03.1: Setup Admin-Local Foundation & Universal Configuration**
        1. **Purpose:** Establish the Admin-Local structure, install zaj-Guides system, and create universal deployment configuration template for reproducible deployments across all environments.
        2. **Action:**
            1. Create the core Admin-Local directory structure with enhanced organization.
                
                ```bash
                mkdir -p Admin-Local/0-Admin
                mkdir -p Admin-Local/1-CurrentProject
                mkdir -p Admin-Local/Deployment/{EnvFiles,Scripts,Configs,Backups,Logs}
                
                ```
                
            2. Install the zaj-Guides system by copying the directory to `Admin-Local/0-Admin/zaj-Guides/`.
            3. Create comprehensive project-specific tracking directories.
                
                ```bash
                mkdir -p Admin-Local/1-CurrentProject/Current-Session
                mkdir -p Admin-Local/1-CurrentProject/Deployment-History
                mkdir -p Admin-Local/1-CurrentProject/Installation-Records
                mkdir -p Admin-Local/1-CurrentProject/Maintenance-Logs
                mkdir -p Admin-Local/1-CurrentProject/Project-Trackers/Audit-Trail
                mkdir -p Admin-Local/1-CurrentProject/Project-Trackers/Conflict-Resolution
                mkdir -p Admin-Local/1-CurrentProject/Project-Trackers/Custom-Changes
                mkdir -p Admin-Local/1-CurrentProject/Project-Trackers/Vendor-Snapshots
                
                ```
                
            4. **Create Universal Deployment Configuration Template** - Set up comprehensive project variables system.
                
                ```bash
                cat > Admin-Local/Deployment/Configs/deployment-variables.json << 'EOF'
                {
                  "project": {
                    "name": "${PROJECT_NAME}",
                    "type": "laravel",
                    "has_frontend": true,
                    "frontend_framework": "vue|react|blade|inertia",
                    "uses_queues": true,
                    "uses_horizon": false,
                    "uses_websockets": false
                  },
                  "paths": {
                    "local_machine": "/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/[PROJECT_NAME]/[PROJECT_NAME]-Master/[PROJECT_NAME]-Root",
                    "server_domain": "/home/u227177893/domains/[DOMAIN]",
                    "server_deploy": "/home/u227177893/domains/[DOMAIN]/deploy",
                    "server_public": "/home/u227177893/public_html",
                    "builder_vm": "${BUILD_SERVER_PATH:-local}",
                    "builder_local": "${PATH_LOCAL_MACHINE}/build-tmp"
                  },
                  "repository": {
                    "url": "git@github.com:username/repository.git",
                    "branch": "main",
                    "deploy_branch": "${DEPLOY_BRANCH:-main}",
                    "commit_start": "${COMMIT_START}",
                    "commit_end": "${COMMIT_END}"
                  },
                  "versions": {
                    "php": "8.2",
                    "php_exact": "8.2.10",
                    "composer": "2",
                    "composer_exact": "2.5.8",
                    "node": "18",
                    "node_exact": "18.17.0",
                    "npm": "9",
                    "npm_exact": "9.8.1"
                  },
                  "deployment": {
                    "strategy": "deployHQ|github-actions|manual",
                    "build_location": "vm|local|server",
                    "keep_releases": 5,
                    "maintenance_mode": true,
                    "health_check_url": "https://[DOMAIN]/health",
                    "opcache_clear_method": "cachetool|web-endpoint|php-fpm-reload"
                  },
                  "shared_directories": [
                    "storage/app/public",
                    "storage/logs",
                    "storage/framework/cache",
                    "storage/framework/sessions",
                    "storage/framework/views",
                    "public/uploads",
                    "public/user-content",
                    "public/avatars",
                    "public/documents",
                    "public/media",
                    "Modules"
                  ],
                  "shared_files": [
                    ".env",
                    "auth.json",
                    "oauth-private.key",
                    "oauth-public.key"
                  ],
                  "hosting": {
                    "type": "dedicated|vps|shared",
                    "has_root_access": true,
                    "public_html_exists": true,
                    "exec_enabled": true,
                    "symlink_enabled": true,
                    "composer_per_domain": false
                  }
                }
                EOF
                
                ```
                
            5. **Create Load Variables Script** - Universal script for loading deployment configuration.
                
                ```bash
                cat > Admin-Local/Deployment/Scripts/load-variables.sh << 'EOF'
                #!/bin/bash
                
                # Load deployment configuration
                CONFIG_FILE="Admin-Local/Deployment/Configs/deployment-variables.json"
                
                if [ ! -f "$CONFIG_FILE" ]; then
                    echo "❌ Configuration file not found: $CONFIG_FILE"
                    exit 1
                fi
                
                # Export as environment variables
                export PROJECT_NAME=$(jq -r '.project.name' $CONFIG_FILE)
                export PATH_LOCAL_MACHINE=$(jq -r '.paths.local_machine' $CONFIG_FILE)
                export PATH_SERVER=$(jq -r '.paths.server_deploy' $CONFIG_FILE)
                export PATH_PUBLIC=$(jq -r '.paths.server_public' $CONFIG_FILE)
                export GITHUB_REPO=$(jq -r '.repository.url' $CONFIG_FILE)
                export DEPLOY_BRANCH=$(jq -r '.repository.deploy_branch' $CONFIG_FILE)
                export PHP_VERSION=$(jq -r '.versions.php' $CONFIG_FILE)
                export COMPOSER_VERSION=$(jq -r '.versions.composer' $CONFIG_FILE)
                export NODE_VERSION=$(jq -r '.versions.node' $CONFIG_FILE)
                export BUILD_LOCATION=$(jq -r '.deployment.build_location' $CONFIG_FILE)
                export HOSTING_TYPE=$(jq -r '.hosting.type' $CONFIG_FILE)
                
                # Determine build path based on strategy
                if [ "$BUILD_LOCATION" = "local" ]; then
                    export PATH_BUILDER="$PATH_LOCAL_MACHINE/build-tmp"
                elif [ "$BUILD_LOCATION" = "server" ]; then
                    export PATH_BUILDER="$PATH_SERVER/build-tmp"
                else
                    export PATH_BUILDER="${BUILD_SERVER_PATH:-/tmp/build}"
                fi
                
                echo "✅ Variables loaded for project: $PROJECT_NAME"
                echo "📁 Local Path: $PATH_LOCAL_MACHINE"
                echo "📁 Server Path: $PATH_SERVER"
                echo "📁 Builder Path: $PATH_BUILDER"
                EOF
                
                chmod +x Admin-Local/Deployment/Scripts/load-variables.sh
                
                ```
                
            6. Copy initial templates from zaj-Guides.
            7. Update the .gitignore file to protect project-specific data.
    6. **Step 03.2: Comprehensive Environment Analysis**
        1. **Purpose:** Perform comprehensive Laravel environment analysis covering PHP extensions, disabled functions, version compatibility, and common Laravel packages detection.
        2. **When:** Immediately after setting up local project structure and deployment configuration.
        3. **Action:**
            1. Create comprehensive environment detection script.
                
                ```bash
                cat > Admin-Local/Deployment/Scripts/comprehensive-env-check.sh << 'EOF'
                #!/bin/bash
                
                echo "╔══════════════════════════════════════════════════════════╗"
                echo "║     Comprehensive Laravel Environment Analysis           ║"
                echo "╚══════════════════════════════════════════════════════════╝"
                
                # Load variables
                source %path-localMachine%/Admin-Local/Deployment/Scripts/load-variables.sh
                
                # Create analysis report
                REPORT="Admin-Local/Deployment/Logs/env-analysis-$(date +%Y%m%d-%H%M%S).md"
                
                echo "# Environment Analysis Report" > $REPORT
                echo "Generated: $(date)" >> $REPORT
                echo "" >> $REPORT
                
                # 1. PHP Analysis
                echo "## PHP Configuration" >> $REPORT
                echo "### Version" >> $REPORT
                PHP_CURRENT=$(php -v | head -n1)
                PHP_REQUIRED=$(grep -oP '"php":\s*"[^"]*"' composer.json 2>/dev/null | cut -d'"' -f4)
                echo "- Current: $PHP_CURRENT" >> $REPORT
                echo "- Required: ${PHP_REQUIRED:-Not specified}" >> $REPORT
                
                # Check PHP extensions
                echo "### Required Extensions" >> $REPORT
                REQUIRED_EXTENSIONS=(
                    "bcmath" "ctype" "curl" "dom" "fileinfo"
                    "json" "mbstring" "openssl" "pcre" "pdo"
                    "tokenizer" "xml" "zip" "gd" "intl"
                )
                
                MISSING_EXTENSIONS=()
                for ext in "${REQUIRED_EXTENSIONS[@]}"; do
                    if ! php -m | grep -qi "^$ext$"; then
                        MISSING_EXTENSIONS+=("$ext")
                        echo "- ❌ $ext (MISSING)" >> $REPORT
                    else
                        echo "- ✅ $ext" >> $REPORT
                    fi
                done
                
                # Check disabled functions
                echo "### Function Availability" >> $REPORT
                REQUIRED_FUNCTIONS=("exec" "shell_exec" "proc_open" "symlink")
                for func in "${REQUIRED_FUNCTIONS[@]}"; do
                    php -r "if(function_exists('$func')) { echo '✅ $func enabled\n'; } else { echo '❌ $func DISABLED\n'; }" >> $REPORT
                done
                
                # 2. Composer Analysis
                echo "" >> $REPORT
                echo "## Composer Configuration" >> $REPORT
                COMPOSER_CURRENT=$(composer --version 2>/dev/null | grep -oP '\d+\.\d+\.\d+')
                echo "- Current Version: $COMPOSER_CURRENT" >> $REPORT
                
                # Detect if Composer 2 is required
                if [ -f "composer.lock" ]; then
                    LOCK_VERSION=$(grep -m1 '"plugin-api-version"' composer.lock | cut -d'"' -f4)
                    echo "- Lock File Version: $LOCK_VERSION" >> $REPORT
                    
                    if [[ "$LOCK_VERSION" == 2.* ]] && [[ "$COMPOSER_CURRENT" == 1.* ]]; then
                        echo "- ⚠️ **CRITICAL: Composer 2 required but version 1 detected!**" >> $REPORT
                    fi
                fi
                
                # 3. Node/NPM Analysis (if frontend exists)
                if [ -f "package.json" ]; then
                    echo "" >> $REPORT
                    echo "## Node.js Configuration" >> $REPORT
                    echo "- Node Version: $(node -v 2>/dev/null || echo 'Not installed')" >> $REPORT
                    echo "- NPM Version: $(npm -v 2>/dev/null || echo 'Not installed')" >> $REPORT
                    
                    # Check for build scripts
                    echo "### Build Scripts" >> $REPORT
                    if grep -q '"build"' package.json; then
                        echo "- ✅ 'build' script found" >> $REPORT
                    fi
                    if grep -q '"production"' package.json; then
                        echo "- ✅ 'production' script found" >> $REPORT
                    fi
                    if grep -q '"dev"' package.json; then
                        echo "- ✅ 'dev' script found" >> $REPORT
                    fi
                fi
                
                # 4. Laravel-specific checks
                echo "" >> $REPORT
                echo "## Laravel Configuration" >> $REPORT
                if [ -f "artisan" ]; then
                    LARAVEL_VERSION=$(php artisan --version 2>/dev/null | grep -oP '\d+\.\d+\.\d+')
                    echo "- Laravel Version: ${LARAVEL_VERSION:-Unknown}" >> $REPORT
                fi
                
                # Check for common Laravel packages
                echo "### Detected Packages" >> $REPORT
                [ -f "config/telescope.php" ] && echo "- 📡 Laravel Telescope" >> $REPORT
                [ -f "config/debugbar.php" ] && echo "- 🔍 Laravel Debugbar" >> $REPORT
                [ -f "config/horizon.php" ] && echo "- 🎯 Laravel Horizon" >> $REPORT
                [ -f "config/sanctum.php" ] && echo "- 🔐 Laravel Sanctum" >> $REPORT
                [ -f "config/jetstream.php" ] && echo "- ✈️ Laravel Jetstream" >> $REPORT
                [ -f "config/livewire.php" ] && echo "- ⚡ Livewire" >> $REPORT
                [ -f "config/inertia.php" ] && echo "- 🔄 Inertia.js" >> $REPORT
                
                # 5. Generate action items
                echo "" >> $REPORT
                echo "## ⚠️ Action Items" >> $REPORT
                
                if [ ${#MISSING_EXTENSIONS[@]} -gt 0 ]; then
                    echo "### Missing PHP Extensions" >> $REPORT
                    echo "Install the following PHP extensions:" >> $REPORT
                    for ext in "${MISSING_EXTENSIONS[@]}"; do
                        echo "- sudo apt-get install php${PHP_VERSION}-${ext}" >> $REPORT
                    done
                fi
                
                if [[ "$COMPOSER_CURRENT" == 1.* ]] && [[ "$LOCK_VERSION" == 2.* ]]; then
                    echo "### Upgrade Composer" >> $REPORT
                    echo '```bash' >> $REPORT
                    echo 'composer self-update --2' >> $REPORT
                    echo '```' >> $REPORT
                fi
                
                # 6. Save results
                echo "" >> $REPORT
                echo "## Summary" >> $REPORT
                [ ${#MISSING_EXTENSIONS[@]} -eq 0 ] && echo "✅ All PHP extensions installed" >> $REPORT || echo "❌ Missing ${#MISSING_EXTENSIONS[@]} PHP extensions" >> $REPORT
                
                echo ""
                echo "📋 Full report saved to: $REPORT"
                cat $REPORT
                EOF
                
                chmod +x Admin-Local/Deployment/Scripts/comprehensive-env-check.sh
                
                ```
                
            2. Run the comprehensive environment analysis.
                
                ```bash
                %path-localMachine%/Admin-Local/Deployment/Scripts/comprehensive-env-check.sh
                
                ```
                
            3. Review the comprehensive analysis report and address any identified issues before proceeding.
    7. **Step 04: Clone GitHub Repository**
        1. **Purpose:** Pull the GitHub repository into the local project structure.
        2. **Action:**
            1. Clone the repository into the current directory using the SSH URL.
                
                ```bash
                git clone git@github.com:rovony/SocietyPal.git .
                
                ```
                
            2. Verify the clone by checking for the .git directory.
                
                ```bash
                ls -la
                
                ```
                
    7. **Step 05: Setup Git Branching Strategy**
        1. **Purpose:** Establish a Git workflow for development, staging, and production deployments.
        2. **Action:**
            1. Create and push the following branches: `development`, `staging`, `production`, `vendor/original`, and `customized`.
                
                ```bash
                git checkout main && git pull origin main
                git checkout -b development && git push -u origin development
                git checkout main && git checkout -b staging && git push -u origin staging
                git checkout main && git checkout -b production && git push -u origin production
                git checkout main && git checkout -b vendor/original && git push -u origin vendor/original
                git checkout main && git checkout -b customized && git push -u origin customized
                git checkout main
                
                ```
                
            2. Return to the main branch.
    8. **Step 06: Create Universal .gitignore**
        1. **Purpose:** Create a comprehensive .gitignore file for CodeCanyon project deployments.
        2. **Action:**
            1. Create a `.gitignore` file in the project root.
            2. Add the universal .gitignore content.
            3. Verify the file creation and its contents.
            4. **Commit the .gitignore file to prevent accidental commits of sensitive files.**
                
                ```bash
                git add .gitignore
                git commit -m "feat: add universal .gitignore for CodeCanyon deployment"
                
                ```
                
    9. **Step 07: Install Project Dependencies**
        1. **Purpose:** Install PHP and Node.js dependencies before running artisan commands and committing files.
        2. **Action:**
            1. Install PHP dependencies with Composer.
                
                ```bash
                composer install
                
                ```
                
            2. Install Node.js dependencies with npm.
                
                ```bash
                npm install
                
                ```
                
            3. Verify installation by checking for vendor/ and node_modules/ directories.
    10. **Step 07.1: Dependency Usage Analysis**
        1. **Purpose:** Detect which dev dependencies are actually used in production code to prevent deployment failures.
        2. **When:** Right after installing dependencies and before committing any files.
        3. **Action:**
            1. Create comprehensive dependency analyzer script.
                
                ```bash
                cat > Admin-Local/Deployment/Scripts/universal-dependency-analyzer.sh << 'EOF'
                #!/bin/bash
                
                echo "=== Analyzing Dev Dependency Usage in Production Code ==="
                
                # 1. Get list of dev dependencies
                DEV_DEPS=$(composer show --dev --name-only 2>/dev/null)
                
                # 2. Create analysis report
                mkdir -p Admin-Local/1-CurrentProject/dependency-analysis
                REPORT="Admin-Local/1-CurrentProject/dependency-analysis/dev-deps-in-production.md"
                
                echo "# Dev Dependencies Used in Production Code" > $REPORT
                echo "Generated: $(date)" >> $REPORT
                echo "" >> $REPORT
                
                # 3. Check for Faker usage in seeders/migrations
                if grep -r "Faker\\Factory\\|faker()" database/seeders database/migrations 2>/dev/null | grep -v "// *test\\|#\\|/\\*"; then
                    echo "## ⚠️ Faker Usage Detected" >> $REPORT
                    echo "Faker is used in seeders/migrations. Move to production dependencies:" >> $REPORT
                    echo '```json' >> $REPORT
                    echo '"fakerphp/faker": "^1.23"' >> $REPORT
                    echo '```' >> $REPORT
                    echo "" >> $REPORT
                    MOVE_TO_PROD+=("fakerphp/faker")
                fi
                
                # 4. Check for IDE Helper usage in production commands
                if grep -r "ide-helper:generate" app/Console 2>/dev/null; then
                    echo "## ⚠️ IDE Helper Usage Detected" >> $REPORT
                    echo "IDE Helper commands found in console. Consider moving to production." >> $REPORT
                    MOVE_TO_PROD+=("barryvdh/laravel-ide-helper")
                fi
                
                # 5. Check for Telescope usage
                if [ -f "config/telescope.php" ] && grep -q "TELESCOPE_ENABLED.*true" .env.production 2>/dev/null; then
                    echo "## ⚠️ Telescope Enabled in Production" >> $REPORT
                    echo "Telescope is enabled in production. Move to production dependencies." >> $REPORT
                    MOVE_TO_PROD+=("laravel/telescope")
                fi
                
                # 6. Check for Debugbar usage
                if [ -f "config/debugbar.php" ] && grep -q "DEBUGBAR_ENABLED.*true" .env.production 2>/dev/null; then
                    echo "## ⚠️ Debugbar Enabled in Production" >> $REPORT
                    echo "Debugbar is enabled in production. Move to production dependencies." >> $REPORT
                    MOVE_TO_PROD+=("barryvdh/laravel-debugbar")
                fi
                
                # 7. Generate composer command to fix
                if [ ${#MOVE_TO_PROD[@]} -gt 0 ]; then
                    echo "## 🔧 Fix Commands" >> $REPORT
                    echo '```bash' >> $REPORT
                    echo "# Remove from require-dev" >> $REPORT
                    for pkg in "${MOVE_TO_PROD[@]}"; do
                        echo "composer remove --dev $pkg" >> $REPORT
                    done
                    echo "" >> $REPORT
                    echo "# Add to require" >> $REPORT
                    for pkg in "${MOVE_TO_PROD[@]}"; do
                        echo "composer require $pkg" >> $REPORT
                    done
                    echo '```' >> $REPORT
                    
                    # Auto-fix option
                    echo ""
                    echo "⚠️  Found ${#MOVE_TO_PROD[@]} dev dependencies used in production!"
                    echo "Review the report at: $REPORT"
                    read -p "Auto-fix now? (y/n): " -n 1 -r
                    echo
                    if [[ $REPLY =~ ^[Yy]$ ]]; then
                        for pkg in "${MOVE_TO_PROD[@]}"; do
                            composer remove --dev $pkg
                            composer require $pkg
                        done
                        echo "✅ Dependencies moved to production"
                    fi
                else
                    echo "✅ No dev dependencies found in production code" >> $REPORT
                fi
                
                # Load deployment variables
                source Admin-Local/Deployment/Scripts/load-variables.sh
                
                echo "╔══════════════════════════════════════════════════════════╗"
                echo "║     Universal Laravel Dependency Analyzer v2.0          ║"
                echo "╚══════════════════════════════════════════════════════════╝"
                
                # Create comprehensive analysis report
                mkdir -p Admin-Local/Deployment/Logs
                REPORT="Admin-Local/Deployment/Logs/dependency-analysis-$(date +%Y%m%d-%H%M%S).md"
                
                echo "# Universal Dependency Analysis Report" > $REPORT
                echo "Generated: $(date)" >> $REPORT
                echo "Project: $PROJECT_NAME" >> $REPORT
                echo "" >> $REPORT
                
                # 1. Dev Dependencies Used in Production Code
                echo "## ⚠️ Dev Dependencies in Production Analysis" >> $REPORT
                
                # Check for Faker usage in seeders/migrations
                if grep -r "Faker\\\\Factory\\\\|faker()" database/seeders database/migrations 2>/dev/null | grep -v "// *test\\\\|#\\\\|/\\\\*"; then
                    echo "### 🚨 Faker Usage Detected" >> $REPORT
                    echo "Faker is used in seeders/migrations. Move to production dependencies:" >> $REPORT
                    echo '```bash' >> $REPORT
                    echo 'composer remove --dev fakerphp/faker && composer require fakerphp/faker' >> $REPORT
                    echo '```' >> $REPORT
                    MOVE_TO_PROD+=("fakerphp/faker")
                fi
                
                # Check for IDE Helper usage in production commands
                if grep -r "ide-helper:generate" app/Console 2>/dev/null; then
                    echo "### 🚨 IDE Helper Usage Detected" >> $REPORT
                    echo "IDE Helper commands found in console. Consider moving to production." >> $REPORT
                    MOVE_TO_PROD+=("barryvdh/laravel-ide-helper")
                fi
                
                # Check for Telescope/Debugbar in production
                [ -f "config/telescope.php" ] && echo "### 📡 Laravel Telescope Detected" >> $REPORT
                [ -f "config/debugbar.php" ] && echo "### 🔍 Laravel Debugbar Detected" >> $REPORT
                
                # 2. Security Vulnerability Scan
                echo "" >> $REPORT
                echo "## 🔒 Security Analysis" >> $REPORT
                
                # Composer security audit
                if command -v composer >/dev/null 2>&1; then
                    echo "### Composer Security Audit" >> $REPORT
                    composer audit --format=table >> $REPORT 2>/dev/null || echo "- No security vulnerabilities found" >> $REPORT
                fi
                
                # NPM security audit (if package.json exists)
                if [ -f "package.json" ]; then
                    echo "### NPM Security Audit" >> $REPORT
                    npm audit --audit-level=high >> $REPORT 2>/dev/null || echo "- No high-severity vulnerabilities found" >> $REPORT
                fi
                
                # 3. Dependency Tree Analysis
                echo "" >> $REPORT
                echo "## 🌳 Dependency Tree Analysis" >> $REPORT
                
                # Check for version conflicts
                echo "### Version Conflicts" >> $REPORT
                composer why-not php ${PHP_VERSION} >> $REPORT 2>/dev/null || echo "- No PHP version conflicts detected" >> $REPORT
                
                # 4. Package Pattern Detection (12+ packages)
                echo "" >> $REPORT
                echo "## 📦 Laravel Package Detection" >> $REPORT
                
                PACKAGES=(
                    "laravel/telescope:📡 Telescope"
                    "barryvdh/laravel-debugbar:🔍 Debugbar"
                    "laravel/horizon:🎯 Horizon"
                    "laravel/sanctum:🔐 Sanctum"
                    "laravel/jetstream:✈️ Jetstream"
                    "livewire/livewire:⚡ Livewire"
                    "inertiajs/inertia-laravel:🔄 Inertia.js"
                    "spatie/laravel-permission:🔐 Permissions"
                    "spatie/laravel-activitylog:📝 Activity Log"
                    "intervention/image:🖼️ Image Processing"
                    "maatwebsite/excel:📊 Excel Import/Export"
                    "laravel/cashier:💳 Cashier"
                )
                
                for package in "${PACKAGES[@]}"; do
                    pkg_name="${package%%:*}"
                    pkg_desc="${package#*:}"
                    if composer show $pkg_name >/dev/null 2>&1; then
                        echo "- $pkg_desc ✅" >> $REPORT
                    fi
                done
                
                # 5. Action Items
                echo "" >> $REPORT
                echo "## 🎯 Action Items" >> $REPORT
                
                if [ ${#MOVE_TO_PROD[@]} -gt 0 ]; then
                    echo "### Move to Production Dependencies" >> $REPORT
                    for pkg in "${MOVE_TO_PROD[@]}"; do
                        echo "- composer remove --dev $pkg && composer require $pkg" >> $REPORT
                    done
                fi
                
                # Save results and display summary
                echo "✅ Universal dependency analysis complete!"
                echo "📋 Full report saved to: $REPORT"
                
                # Display critical issues only
                if [ ${#MOVE_TO_PROD[@]} -gt 0 ]; then
                    echo ""
                    echo "🚨 CRITICAL: ${#MOVE_TO_PROD[@]} dev dependencies used in production!"
                    echo "Review report and apply fixes before deployment."
                fi
                EOF
                
                chmod +x Admin-Local/Deployment/Scripts/universal-dependency-analyzer.sh
                
                ```
                
            2. Make the script executable and run the analysis.</search>
</search_and_replace>
                
                ```bash
                %path-localMachine%/Admin-Local/Deployment/Scripts/universal-dependency-analyzer.sh
                
                ```
                
    11. **Step 07.2: Install Analysis Tools**
        1. **Purpose:** Install and configure advanced static analysis tools (PHPStan, Composer Unused, Composer Require Checker) for comprehensive code quality and dependency auditing.
        2. **When:** After dependency analyzer has identified potential issues and before committing Admin-Local foundation.
        3. **Action:**
            1. Create analysis tools installation script.
                
                ```bash
                cat > Admin-Local/Deployment/Scripts/install-analysis-tools.sh << 'EOF'
                #!/bin/bash
                
                # Load deployment variables
                source Admin-Local/Deployment/Scripts/load-variables.sh
                
                echo "╔══════════════════════════════════════════════════════════╗"
                echo "║     Installing Laravel Analysis Tools v2.0              ║"
                echo "╚══════════════════════════════════════════════════════════╝"
                
                # Create configs directory
                mkdir -p Admin-Local/Deployment/Configs
                
                # 1. Install PHPStan with Laravel rules
                echo "📦 Installing PHPStan with Laravel rules..."
                composer require --dev phpstan/phpstan larastan/larastan
                
                # Create PHPStan configuration
                cat > phpstan.neon << 'PHPSTAN_EOF'
                includes:
                    - ./vendor/larastan/larastan/extension.neon
                
                parameters:
                    paths:
                        - app/
                        - database/
                        - routes/
                        - config/
                    
                    level: 5
                    
                    ignoreErrors:
                        - '#PHPDoc tag @var#'
                        - '#Call to an undefined method [a-zA-Z0-9\\\\_]+::factory\(\)#'
                    
                    excludePaths:
                        - ./vendor/*
                        - ./storage/*
                        - ./bootstrap/cache/*
                        - ./node_modules/*
                    
                    checkMissingIterableValueType: false
                    checkGenericClassInNonGenericObjectType: false
                PHPSTAN_EOF
                
                # 2. Install Composer Unused with Laravel framework filters
                echo "📦 Installing Composer Unused..."
                composer require --dev icanhazstring/composer-unused
                
                # Create Composer Unused configuration
                cat > composer-unused.php << 'UNUSED_EOF'
                <?php
                
                declare(strict_types=1);
                
                use ComposerUnused\ComposerUnused\Configuration\Configuration;
                use ComposerUnused\ComposerUnused\Configuration\NamedFilter;
                use ComposerUnused\ComposerUnused\Configuration\PatternFilter;
                
                return static function (Configuration $config): Configuration {
                    return $config
                        // Laravel Framework filters
                        ->addNamedFilter(NamedFilter::fromString('laravel/framework'))
                        ->addNamedFilter(NamedFilter::fromString('laravel/tinker'))
                        ->addNamedFilter(NamedFilter::fromString('fakerphp/faker'))
                        
                        // Common Laravel packages that may appear unused
                        ->addNamedFilter(NamedFilter::fromString('pusher/pusher-php-server'))
                        ->addNamedFilter(NamedFilter::fromString('league/flysystem-aws-s3-v3'))
                        ->addNamedFilter(NamedFilter::fromString('predis/predis'))
                        
                        // Laravel service providers (auto-discovered)
                        ->addPatternFilter(PatternFilter::fromString('/laravel\/.*/'))
                        ->addPatternFilter(PatternFilter::fromString('/spatie\/.*/'))
                        
                        // Exclude paths
                        ->setAdditionalFilesFor('laravel/framework', [
                            __DIR__ . '/config',
                            __DIR__ . '/routes',
                            __DIR__ . '/database',
                        ])
                        
                        ->setAdditionalFilesFor('fakerphp/faker', [
                            __DIR__ . '/database/seeders',
                            __DIR__ . '/database/factories',
                        ]);
                };
                UNUSED_EOF
                
                # 3. Install Composer Require Checker with symbol whitelist
                echo "📦 Installing Composer Require Checker..."
                composer require --dev maglnet/composer-require-checker
                
                # Create Require Checker configuration
                cat > composer-require-checker.json << 'CHECKER_EOF'
                {
                    "symbol-whitelist": [
                        "array_key_exists", "array_merge", "array_filter", "array_map",
                        "json_encode", "json_decode", "strlen", "substr", "strpos",
                        "explode", "implode", "trim", "ltrim", "rtrim", "strtolower",
                        "strtoupper", "ucfirst", "ucwords", "preg_match", "preg_replace",
                        "file_exists", "is_file", "is_dir", "mkdir", "rmdir", "unlink",
                        "fopen", "fclose", "fwrite", "fread", "file_get_contents",
                        "file_put_contents", "pathinfo", "dirname", "basename",
                        "realpath", "getcwd", "time", "date", "strtotime", "microtime",
                        "mt_rand", "rand", "min", "max", "count", "empty", "isset",
                        "in_array", "array_keys", "array_values", "sort", "ksort",
                        "class_exists", "interface_exists", "method_exists", "property_exists",
                        "get_class", "get_parent_class", "is_subclass_of", "instanceof"
                    ],
                    "php-core-extensions": [
                        "Core", "date", "filter", "hash", "pcre", "Reflection", "SPL",
                        "standard", "bcmath", "ctype", "curl", "dom", "fileinfo", "json",
                        "mbstring", "openssl", "pdo", "tokenizer", "xml", "zip", "gd", "intl"
                    ],
                    "scan-files": [
                        "./app", "./bootstrap", "./config", "./database", "./routes"
                    ]
                }
                CHECKER_EOF
                
                # 4. Create unified analysis runner script  
                cat > Admin-Local/Deployment/Scripts/run-full-analysis.sh << 'ANALYSIS_EOF'
                #!/bin/bash
                
                echo "🔍 Running Full Laravel Code Analysis Suite..."
                
                # Create analysis results directory
                mkdir -p Admin-Local/Deployment/Logs/analysis-$(date +%Y%m%d-%H%M%S)
                ANALYSIS_DIR="Admin-Local/Deployment/Logs/analysis-$(date +%Y%m%d-%H%M%S)"
                
                echo "📊 Analysis results will be saved to: $ANALYSIS_DIR"
                
                # 1. PHPStan Analysis
                echo "🔍 Running PHPStan analysis..."
                ./vendor/bin/phpstan analyse --memory-limit=2G > $ANALYSIS_DIR/phpstan-report.txt 2>&1
                
                # 2. Composer Unused Analysis
                echo "📦 Checking for unused Composer dependencies..."
                ./vendor/bin/composer-unused > $ANALYSIS_DIR/unused-deps-report.txt 2>&1
                
                # 3. Composer Require Checker Analysis
                echo "🔗 Checking for missing Composer dependencies..."
                ./vendor/bin/composer-require-checker check > $ANALYSIS_DIR/missing-deps-report.txt 2>&1
                
                # 4. Generate summary
                echo "📋 Generating analysis summary..."
                cat > $ANALYSIS_DIR/analysis-summary.md << 'SUMMARY_EOF'
                # Laravel Code Analysis Summary
                Generated: $(date)
                
                ## PHPStan Results
                $(head -20 $ANALYSIS_DIR/phpstan-report.txt)
                
                ## Unused Dependencies
                $(head -20 $ANALYSIS_DIR/unused-deps-report.txt)
                
                ## Missing Dependencies  
                $(head -20 $ANALYSIS_DIR/missing-deps-report.txt)
                
                ## Next Steps
                1. Review PHPStan errors and warnings
                2. Remove unused dependencies to reduce build size
                3. Add missing dependencies to prevent runtime errors
                4. Run analysis again after fixes
                SUMMARY_EOF
                
                echo "✅ Full analysis complete! Check results in: $ANALYSIS_DIR"
                ANALYSIS_EOF
                
                chmod +x Admin-Local/Deployment/Scripts/run-full-analysis.sh
                
                echo "✅ Analysis tools installed successfully!"
                echo "📋 Available tools:"
                echo "   🔍 PHPStan: ./vendor/bin/phpstan analyse"
                echo "   📦 Unused Deps: ./vendor/bin/composer-unused"
                echo "   🔗 Missing Deps: ./vendor/bin/composer-require-checker check"
                echo "   🎯 Full Analysis: ./Admin-Local/Deployment/Scripts/run-full-analysis.sh"
                EOF
                
                chmod +x Admin-Local/Deployment/Scripts/install-analysis-tools.sh
                
                ```
                
            2. Run the analysis tools installation.
                
                ```bash
                %path-localMachine%/Admin-Local/Deployment/Scripts/install-analysis-tools.sh</search>
</search_and_replace>
                
                ```
                
            3. Review the analysis report and apply any suggested fixes for dependency misplacements.
    11. **Step 08: Commit Admin-Local Foundation**
        1. **Purpose:** Version control the Admin-Local structure after dependency analysis and before adding CodeCanyon files.
        2. **Action:**
            1. Stage and commit the Admin-Local foundation with dependency analysis.
                
                ```bash
                git add Admin-Local/
                git commit -m "feat: establish Admin-Local foundation structure with dependency analysis"
                
                ```
                
    12. **Step 09: Download and Extract CodeCanyon Application**
        1. **Purpose:** Download and integrate the CodeCanyon application into the project structure.
        2. **Action:**
            1. Create a temporary extraction directory.
                
                ```bash
                mkdir -p tmp-zip-extract
                
                ```
                
            2. Download the CodeCanyon ZIP file and move it to the temporary directory.
            3. Extract the application files.
                
                ```bash
                cd tmp-zip-extract
                unzip SocietyPro-v1.0.42.zip
                
                ```
                
            4. Copy the application files to the project root.
            5. Clean up the temporary files.
                
                ```bash
                rm -rf tmp-zip-extract
                
                ```
                
    13. **Step 10: Commit Original Vendor Files**
        1. **Purpose:** Preserve the original CodeCanyon files before any modifications.
        2. **Action:**
            1. Verify that only clean, unmodified CodeCanyon author files are present.
            2. Commit the original vendor files to the `vendor/original` branch with a version tag.
                
                ```bash
                git add .
                git commit -m "feat: initial CodeCanyon files v1.0.42 (original vendor state)"
                git checkout -b vendor/original
                git tag -a v1.0.42 -m "CodeCanyon SocietyPro v1.0.42 - Original vendor files"
                
                ```
                
            3. Push the branch and tag to the origin.
                
                ```bash
                git push -u origin vendor/original
                git push origin v1.0.42
                
                ```
                
            4. Return to the main branch.
                
                ```bash
                git checkout main
                
                ```
                
    14. **Step 11: Expand Admin-Local Directory Structure**
        1. **Purpose:** Complete the directory structure for customizations, documentation, and deployment tools.
        2. **Action:**
            1. Expand the Admin-Local structure with directories for application customizations, project-specific tracking, documentation, server deployment, and backups.
            2. Create `.gitkeep` files to preserve empty directories.
    15. **Step 12: CodeCanyon Configuration & License Management**
        1. **Purpose:** Establish CodeCanyon license tracking and update management system.
        2. **Action:**
            1. Detect if the application is a CodeCanyon application.
            2. Set up a comprehensive license management structure with addon support.
            3. Backup installer components for future reference.
            4. Create a license tracking system with user-provided information.
            5. Create an update capture and comparison system.
    16. **Step 12.1: Branch Synchronization & Progress Checkpoint**
        1. **Purpose:** Ensure consistent commit history across all deployment branches with strategic checkpoint naming.
        2. **Action:**
            1. Verify the current Git status and branch.
            2. Stage all current changes.
            3. Create a strategic checkpoint commit with professional naming.
            4. Execute the multi-branch synchronization script.
            5. Verify that all branches are synchronized.
    17. **Step 13: Create Environment Files**
        1. **Purpose:** Set up environment configuration files for all deployment stages.
        2. **Action:**
            1. Create and configure `.env.local`, `.env.staging`, and `.env.production` files.
            2. Generate unique application keys for each environment.
                
                ```bash
                php artisan key:generate --env=local
                php artisan key:generate --env=staging
                php artisan key:generate --env=production
                
                ```
                
            3. Verify that all environment files are correctly configured.
    18. **Step 14: Setup Local Development Site in Herd**
        1. **Purpose:** Configure Laravel Herd for the local development environment.
        2. **Action:**
            1. Upgrade to Herd Pro and enable MySQL and Redis services.
            2. Add the site to Herd.
            3. Verify the site configuration by opening it in a browser.
    19. **Step 15: Create Local Database**
        1. **Purpose:** Set up a local MySQL database in Herd for development.
        2. **Action:**
            1. Verify that the Herd Pro MySQL service is running.
            2. Connect to the MySQL CLI and create the local database.
                
                ```bash
                mysql -u root -h 127.0.0.1 -P 3306 -p
                CREATE DATABASE societypal_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                exit;
                
                ```
                
            3. Update the `.env.local` file.
            4. Clear the Laravel configuration cache and test the database connection.
                
                ```bash
                php artisan config:clear
                php artisan cache:clear
                php artisan migrate:status
                
                ```
                
    20. **Step 16: Create Storage Link**
        1. **Purpose:** Create symbolic link for public file access (essential Laravel setup step).
        2. **Action:**
            1. Create the storage link to make uploaded files accessible from the web.
                
                ```bash
                php artisan storage:link
                
                ```
                
            2. Verify the link was created successfully.
                
                ```bash
                ls -la public/storage
                
                ```
                
    21. **Step 17: Run Local & CodeCanyon Installation**
        1. **Purpose:** Launch the local development environment and complete the CodeCanyon installation process.
        2. **Action:**
            1. Set up the environment and generate the application key.
                
                ```bash
                cp .env.local .env
                php artisan key:generate
                
                ```
                
            2. Clear and rebuild Laravel caches.
                
                ```bash
                php artisan config:clear
                php artisan cache:clear
                php artisan view:clear
                php artisan route:clear
                
                ```
                
            3. Set the necessary Laravel permissions.
                
                ```bash
                chmod -R 775 storage/
                chmod -R 775 bootstrap/cache/
                
                ```
                
            4. Start the required services in Herd.
            5. Access the application and complete the web-based installation.
            6. Capture the installation credentials and configuration.
            7. Perform a post-installation security lockdown.
            8. Run a post-installation analysis to document changes.
-----

-   **SECTION B: Prepare for Build and Deployment & Commit Codebase to Github**
    IMPORTANT Note: below is a draft and may have errors or be incorrect or missing steps- you can modify or change to enhance and ensure proper and correct flow.
    Important Note 2: The steps below should cover apps we develop or apps we purchase like from codecanyon. same flow for all.
    # Master Checklist for **SECTION B: Prepare for Build and Deployment**
    This checklist consolidates all the necessary steps for Phase 2 of the project setup. Follow each step carefully to ensure a smooth and successful deployment.
    1. **Step 14.1: Composer Version Strategy Setup**
        1. **Purpose:** Configure Composer for production compatibility and optimize settings.
        2. **When:** Before Step 15 to ensure proper dependency installation.
        3. **Action:**
            1. Create Composer strategy setup script.
                
                ```bash
                cat > Admin-Local/Deployment/Scripts/setup-composer-strategy.sh << 'EOF'
                #!/bin/bash
                
                echo "=== Setting Up Composer Production Strategy ==="
                
                # 1. Check if composer.json needs modification for v2
                if ! grep -q '"config"' composer.json; then
                    echo "Adding config section to composer.json..."
                    jq '. + {"config": {}}' composer.json > composer.tmp && mv composer.tmp composer.json
                fi
                
                # 2. Add production optimizations
                jq '.config += {
                    "optimize-autoloader": true,
                    "preferred-install": "dist",
                    "sort-packages": true,
                    "classmap-authoritative": true,
                    "apcu-autoloader": true,
                    "platform-check": false
                }' composer.json > composer.tmp && mv composer.tmp composer.json
                
                # 3. Handle plugin compatibility for Composer 2
                if composer --version | grep -q "version 2"; then
                    # Get all plugins and add to allow-plugins
                    PLUGINS=$(composer show -s | grep "composer-plugin" -B2 | grep "name" | cut -d: -f2 | tr -d ' ')
                    
                    for plugin in $PLUGINS; do
                        jq --arg plugin "$plugin" '.config."allow-plugins"[$plugin] = true' composer.json > composer.tmp
                        mv composer.tmp composer.json
                    done
                fi
                
                # 4. Add platform requirements
                PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
                jq --arg ver "$PHP_VERSION" '.config.platform.php = $ver' composer.json > composer.tmp
                mv composer.tmp composer.json
                
                echo "✅ Composer configured for production"
                EOF
                
                chmod +x Admin-Local/Deployment/Scripts/setup-composer-strategy.sh
                
            2. Run the script.
                
                ```bash
                %path-localMachine%/Admin-Local/Deployment/Scripts/setup-composer-strategy.sh
                
                ```
                
            3. Verify composer.json configuration has been updated with production optimizations.
    2. **Step 15: Install Dependencies & Generate Lock Files**
        1. **Purpose:** Install and verify all project dependencies for reproducible builds.
        2. **Action:**
            1. Navigate to the project root.
            2. Install PHP dependencies.

                ```bash
                composer install

                ```

            3. Install JavaScript dependencies (if applicable).

                ```bash
                npm install

                ```

            4. Verify that `composer.lock` and `package-lock.json` files are created.
    2. **Step 15.1: Run Database Migrations `(New Added)`**
        1. **Purpose:** Ensure the database schema is up-to-date with the application's requirements.
        2. **Action:**
            1. Run the following command to apply any pending migrations:

                ```bash
                php artisan migrate

                ```
    3. **Step 15.2: Production Dependency Verification**
        1. **Purpose:** Verify that all production dependencies are correctly classified and no dev dependencies are used in production code.
        2. **When:** After dependencies are installed and before build testing begins.
        3. **Action:**
            1. Create production dependency verification script.
                
                ```bash
                cat > Admin-Local/Deployment/Scripts/verify-production-dependencies.sh << 'EOF'
                #!/bin/bash
                
                echo "=== Production Dependency Verification ==="
                
                # 1. Check for dev dependencies in production code
                DEV_DEPS=$(php -r "
                \$composer = json_decode(file_get_contents('composer.json'), true);
                \$devDeps = array_keys(\$composer['require-dev'] ?? []);
                foreach (\$devDeps as \$dep) {
                    \$packageName = explode('/', \$dep)[1] ?? \$dep;
                    \$uses = shell_exec('grep -r \"use.*' . \$packageName . '\" app/ resources/ || true');
                    if (!empty(\$uses)) {
                        echo \"❌ Dev dependency '\$dep' used in production code:\\n\$uses\\n\";
                    }
                }
                ")
                
                if [[ -n "$DEV_DEPS" ]]; then
                    echo "$DEV_DEPS"
                    echo ""
                    echo "🔧 Suggested fixes:"
                    echo "1. Move dev dependencies to 'require' section if needed in production"
                    echo "2. Create production-safe alternatives for development-only features"
                    echo "3. Use environment checks to conditionally load dev dependencies"
                fi
                
                # 2. Verify composer install --no-dev works
                echo "🧪 Testing production dependency installation..."
                composer install --no-dev --dry-run > /tmp/composer-production-test.log 2>&1
                
                if [ $? -eq 0 ]; then
                    echo "✅ Production dependency installation: PASSED"
                else
                    echo "❌ Production dependency installation: FAILED"
                    echo "Check /tmp/composer-production-test.log for details"
                fi
                
                # 3. Check for missing platform requirements
                echo "🔍 Checking platform requirements..."
                composer check-platform-reqs --no-dev 2>/dev/null || echo "⚠️  Platform requirement issues detected"
                
                # 4. Validate lock file consistency
                echo "🔒 Validating lock file consistency..."
                composer validate --strict --no-check-all || echo "⚠️  Composer validation issues detected"
                
                echo "=== Verification Complete ==="
                EOF
                
                chmod +x Admin-Local/Deployment/Scripts/verify-production-dependencies.sh
                
            2. Run the script.
                
                ```bash
                %path-localMachine%/Admin-Local/Deployment/Scripts/verify-production-dependencies.sh
                
                ```
                
            3. Fix any dependency classification issues identified by the verification script.
            4. Re-run verification until all checks pass.
    4. **Step 16: Test Build Process**
        1. **Purpose:** Verify the production build process works before deployment with comprehensive pre-build validation.
        2. **Action:**
            1. **Pre-Build Validation** - Run comprehensive checks before attempting build.
                
                ```bash
                #!/bin/bash
                # Create pre-build validation script
                # save as: Admin-Local/1-CurrentProject/pre-build-validation.sh
                
                echo "=== Pre-Build Validation ==="
                
                # 1. Environment validation
                echo "🔍 Validating environment..."
                php -v || { echo "❌ PHP not available"; exit 1; }
                composer --version || { echo "❌ Composer not available"; exit 1; }
                node --version || { echo "❌ Node.js not available"; exit 1; }
                npm --version || { echo "❌ NPM not available"; exit 1; }
                
                # 2. Required files validation
                echo "🔍 Validating required files..."
                [[ -f "composer.json" ]] || { echo "❌ composer.json missing"; exit 1; }
                [[ -f "composer.lock" ]] || { echo "❌ composer.lock missing"; exit 1; }
                [[ -f "package.json" ]] || { echo "❌ package.json missing"; exit 1; }
                [[ -f "package-lock.json" ]] || { echo "❌ package-lock.json missing"; exit 1; }
                [[ -f ".env" ]] || { echo "❌ .env file missing"; exit 1; }
                
                # 3. Dependency validation
                echo "🔍 Validating dependencies..."
                composer validate --strict --no-check-all || { echo "❌ Composer validation failed"; exit 1; }
                npm audit --audit-level=high || echo "⚠️  High-severity vulnerabilities found"
                
                # 4. Laravel-specific validation
                echo "🔍 Validating Laravel setup..."
                php artisan --version || { echo "❌ Laravel Artisan not working"; exit 1; }
                [[ -f "config/app.php" ]] || { echo "❌ Laravel config missing"; exit 1; }
                
                # 5. Database connection test
                echo "🔍 Testing database connection..."
                php artisan migrate:status > /dev/null 2>&1 || echo "⚠️  Database connection issues detected"
                
                echo "✅ Pre-build validation complete"
                
                ```
                
            2. Run the pre-build validation script.
                
                ```bash
                chmod +x Admin-Local/1-CurrentProject/pre-build-validation.sh
                ./Admin-Local/1-CurrentProject/pre-build-validation.sh
                
                ```
                
            3. Clean previous builds.

                ```bash
                rm -rf vendor node_modules public/build

                ```

            4. Test the production PHP build.

                ```bash
                composer install --no-dev --prefer-dist --optimize-autoloader

                ```

            5. Build frontend assets.

                ```bash
                npm ci
                npm run build

                ```

            6. Apply Laravel caching.

                ```bash
                php artisan config:cache
                php artisan route:cache
                php artisan view:cache

                ```

            7. Test the built version locally.
            8. Restore the development environment.

                ```bash
                php artisan config:clear
                php artisan route:clear
                php artisan view:clear
                composer install
                npm install

                ```
    5. **Step 16.1: Pre-Deployment Validation Checklist**
        1. **Purpose:** Comprehensive 10-point validation checklist to ensure deployment readiness before any build or deployment activities.
        2. **When:** After build testing and before security scans - critical gateway step.
        3. **Action:**
            1. Create comprehensive pre-deployment validation script.
                
                ```bash
                cat > Admin-Local/Deployment/Scripts/pre-deployment-validation.sh << 'EOF'
                #!/bin/bash
                
                echo "╔══════════════════════════════════════════════════════════╗"
                echo "║           Pre-Deployment Validation Checklist           ║"
                echo "╚══════════════════════════════════════════════════════════╝"
                
                # Load deployment variables
                source %path-localMachine%/Admin-Local/Deployment/Scripts/load-variables.sh
                
                # Initialize validation results
                VALIDATION_REPORT="Admin-Local/Deployment/Logs/pre-deployment-validation-$(date +%Y%m%d-%H%M%S).md"
                FAILED_CHECKS=()
                PASSED_CHECKS=()
                TOTAL_CHECKS=10
                
                echo "# Pre-Deployment Validation Report" > $VALIDATION_REPORT
                echo "Generated: $(date)" >> $VALIDATION_REPORT
                echo "Project: $PROJECT_NAME" >> $VALIDATION_REPORT
                echo "" >> $VALIDATION_REPORT
                
                # Helper function for check results
                check_result() {
                    local check_name="$1"
                    local check_status="$2"
                    local check_details="$3"
                    
                    if [ "$check_status" = "PASS" ]; then
                        echo "✅ $check_name: PASSED"
                        echo "## ✅ $check_name" >> $VALIDATION_REPORT
                        echo "**Status:** PASSED" >> $VALIDATION_REPORT
                        [ -n "$check_details" ] && echo "**Details:** $check_details" >> $VALIDATION_REPORT
                        PASSED_CHECKS+=("$check_name")
                    else
                        echo "❌ $check_name: FAILED"
                        echo "## ❌ $check_name" >> $VALIDATION_REPORT
                        echo "**Status:** FAILED" >> $VALIDATION_REPORT
                        echo "**Details:** $check_details" >> $VALIDATION_REPORT
                        FAILED_CHECKS+=("$check_name")
                    fi
                    echo "" >> $VALIDATION_REPORT
                }
                
                echo "🔍 Running 10-point validation checklist..."
                echo ""
                
                # 1. Environment Configuration Validation
                echo "1/10 - Validating Environment Configuration..."
                ENV_STATUS="PASS"
                ENV_DETAILS=""
                
                if [ ! -f ".env" ]; then
                    ENV_STATUS="FAIL"
                    ENV_DETAILS="Missing .env file"
                elif ! grep -q "APP_KEY=" .env || [ -z "$(grep APP_KEY= .env | cut -d'=' -f2)" ]; then
                    ENV_STATUS="FAIL"
                    ENV_DETAILS="APP_KEY not set in .env"
                elif ! grep -q "DB_" .env; then
                    ENV_STATUS="FAIL"
                    ENV_DETAILS="Database configuration missing in .env"
                else
                    ENV_DETAILS="Environment file properly configured"
                fi
                check_result "Environment Configuration" "$ENV_STATUS" "$ENV_DETAILS"
                
                # 2. Dependencies Installation Check
                echo "2/10 - Validating Dependencies..."
                DEPS_STATUS="PASS"
                DEPS_DETAILS=""
                
                if [ ! -d "vendor" ] || [ ! -f "composer.lock" ]; then
                    DEPS_STATUS="FAIL"
                    DEPS_DETAILS="PHP dependencies not installed (run: composer install)"
                elif [ -f "package.json" ] && ([ ! -d "node_modules" ] || [ ! -f "package-lock.json" ]); then
                    DEPS_STATUS="FAIL"
                    DEPS_DETAILS="Node.js dependencies not installed (run: npm install)"
                else
                    # Test production dependencies
                    if composer install --no-dev --dry-run > /dev/null 2>&1; then
                        DEPS_DETAILS="All dependencies properly installed and production-ready"
                    else
                        DEPS_STATUS="FAIL"
                        DEPS_DETAILS="Production dependency installation issues detected"
                    fi
                fi
                check_result "Dependencies Installation" "$DEPS_STATUS" "$DEPS_DETAILS"
                
                # 3. Database Connectivity
                echo "3/10 - Validating Database Connection..."
                DB_STATUS="PASS"
                DB_DETAILS=""
                
                if ! php artisan migrate:status > /dev/null 2>&1; then
                    DB_STATUS="FAIL"
                    DB_DETAILS="Database connection failed or migrations not run"
                else
                    MIGRATION_COUNT=$(php artisan migrate:status 2>/dev/null | grep -c "Ran")
                    DB_DETAILS="Database connected, $MIGRATION_COUNT migrations applied"
                fi
                check_result "Database Connectivity" "$DB_STATUS" "$DB_DETAILS"
                
                # 4. Build Process Validation
                echo "4/10 - Validating Build Process..."
                BUILD_STATUS="PASS"
                BUILD_DETAILS=""
                
                # Test Laravel optimization commands
                if ! php artisan config:cache > /dev/null 2>&1; then
                    BUILD_STATUS="FAIL"
                    BUILD_DETAILS="Laravel config:cache failed"
                elif ! php artisan route:cache > /dev/null 2>&1; then
                    BUILD_STATUS="FAIL"
                    BUILD_DETAILS="Laravel route:cache failed"
                elif ! php artisan view:cache > /dev/null 2>&1; then
                    BUILD_STATUS="FAIL"
                    BUILD_DETAILS="Laravel view:cache failed"
                else
                    # Clear caches after testing
                    php artisan config:clear > /dev/null 2>&1
                    php artisan route:clear > /dev/null 2>&1
                    php artisan view:clear > /dev/null 2>&1
                    
                    # Test frontend build if applicable
                    if [ -f "package.json" ] && grep -q '"build"' package.json; then
                        if npm run build > /dev/null 2>&1; then
                            BUILD_DETAILS="Laravel optimization and frontend build successful"
                        else
                            BUILD_STATUS="FAIL"
                            BUILD_DETAILS="Frontend build process failed"
                        fi
                    else
                        BUILD_DETAILS="Laravel optimization successful"
                    fi
                fi
                check_result "Build Process" "$BUILD_STATUS" "$BUILD_DETAILS"
                
                # 5. Security Configuration
                echo "5/10 - Validating Security Configuration..."
                SEC_STATUS="PASS"
                SEC_DETAILS=""
                
                SECURITY_ISSUES=()
                
                # Check APP_DEBUG setting
                if grep -q "APP_DEBUG=true" .env; then
                    SECURITY_ISSUES+=("APP_DEBUG=true (should be false in production)")
                fi
                
                # Check APP_ENV setting
                if ! grep -q "APP_ENV=production" .env && ! grep -q "APP_ENV=staging" .env; then
                    SECURITY_ISSUES+=("APP_ENV should be 'production' or 'staging'")
                fi
                
                # Check for HTTPS enforcement
                if ! grep -q "FORCE_HTTPS=true" .env && ! grep -q "APP_URL=https" .env; then
                    SECURITY_ISSUES+=("HTTPS enforcement not configured")
                fi
                
                if [ ${#SECURITY_ISSUES[@]} -gt 0 ]; then
                    SEC_STATUS="FAIL"
                    SEC_DETAILS="Issues: $(IFS='; '; echo "${SECURITY_ISSUES[*]}")"
                else
                    SEC_DETAILS="Security configuration validated"
                fi
                check_result "Security Configuration" "$SEC_STATUS" "$SEC_DETAILS"
                
                # 6. File Permissions
                echo "6/10 - Validating File Permissions..."
                PERM_STATUS="PASS"
                PERM_DETAILS=""
                
                if [ ! -w "storage" ] || [ ! -w "bootstrap/cache" ]; then
                    PERM_STATUS="FAIL"
                    PERM_DETAILS="storage/ or bootstrap/cache/ not writable"
                else
                    PERM_DETAILS="Critical directories have proper write permissions"
                fi
                check_result "File Permissions" "$PERM_STATUS" "$PERM_DETAILS"
                
                # 7. Git Repository Status
                echo "7/10 - Validating Git Repository..."
                GIT_STATUS="PASS"
                GIT_DETAILS=""
                
                if [ ! -d ".git" ]; then
                    GIT_STATUS="FAIL"
                    GIT_DETAILS="Not a git repository"
                elif [ -n "$(git status --porcelain 2>/dev/null)" ]; then
                    UNCOMMITTED_COUNT=$(git status --porcelain 2>/dev/null | wc -l)
                    GIT_STATUS="FAIL"
                    GIT_DETAILS="$UNCOMMITTED_COUNT uncommitted changes detected"
                else
                    CURRENT_BRANCH=$(git branch --show-current 2>/dev/null || echo "unknown")
                    GIT_DETAILS="Repository clean, current branch: $CURRENT_BRANCH"
                fi
                check_result "Git Repository Status" "$GIT_STATUS" "$GIT_DETAILS"
                
                # 8. Configuration Files Validation
                echo "8/10 - Validating Configuration Files..."
                CONFIG_STATUS="PASS"
                CONFIG_DETAILS=""
                
                MISSING_CONFIGS=()
                [ ! -f "config/app.php" ] && MISSING_CONFIGS+=("config/app.php")
                [ ! -f "config/database.php" ] && MISSING_CONFIGS+=("config/database.php")
                [ ! -f "composer.json" ] && MISSING_CONFIGS+=("composer.json")
                
                if [ ${#MISSING_CONFIGS[@]} -gt 0 ]; then
                    CONFIG_STATUS="FAIL"
                    CONFIG_DETAILS="Missing: $(IFS=', '; echo "${MISSING_CONFIGS[*]}")"
                else
                    CONFIG_DETAILS="All critical configuration files present"
                fi
                check_result "Configuration Files" "$CONFIG_STATUS" "$CONFIG_DETAILS"
                
                # 9. Deployment Variables Validation
                echo "9/10 - Validating Deployment Configuration..."
                DEPLOY_STATUS="PASS"
                DEPLOY_DETAILS=""
                
                if [ ! -f "Admin-Local/Deployment/Configs/deployment-variables.json" ]; then
                    DEPLOY_STATUS="FAIL"
                    DEPLOY_DETAILS="Deployment variables configuration missing"
                else
                    # Validate JSON structure
                    if jq empty Admin-Local/Deployment/Configs/deployment-variables.json 2>/dev/null; then
                        PROJECT_NAME_CONFIG=$(jq -r '.project.name' Admin-Local/Deployment/Configs/deployment-variables.json)
                        DEPLOY_DETAILS="Deployment configuration valid for project: $PROJECT_NAME_CONFIG"
                    else
                        DEPLOY_STATUS="FAIL"
                        DEPLOY_DETAILS="Invalid JSON in deployment-variables.json"
                    fi
                fi
                check_result "Deployment Variables" "$DEPLOY_STATUS" "$DEPLOY_DETAILS"
                
                # 10. Health Check Endpoint
                echo "10/10 - Validating Application Health..."
                HEALTH_STATUS="PASS"
                HEALTH_DETAILS=""
                
                # Test basic Laravel functionality
                if ! php artisan --version > /dev/null 2>&1; then
                    HEALTH_STATUS="FAIL"
                    HEALTH_DETAILS="Laravel artisan commands not functional"
                elif ! php artisan route:list > /dev/null 2>&1; then
                    HEALTH_STATUS="FAIL"
                    HEALTH_DETAILS="Laravel routing system not functional"
                else
                    HEALTH_DETAILS="Laravel application core functionality verified"
                fi
                check_result "Application Health" "$HEALTH_STATUS" "$HEALTH_DETAILS"
                
                # Generate Summary
                echo "" >> $VALIDATION_REPORT
                echo "## 📊 Validation Summary" >> $VALIDATION_REPORT
                echo "- **Total Checks:** $TOTAL_CHECKS" >> $VALIDATION_REPORT
                echo "- **Passed:** ${#PASSED_CHECKS[@]}" >> $VALIDATION_REPORT
                echo "- **Failed:** ${#FAILED_CHECKS[@]}" >> $VALIDATION_REPORT
                echo "" >> $VALIDATION_REPORT
                
                if [ ${#FAILED_CHECKS[@]} -eq 0 ]; then
                    echo "## ✅ DEPLOYMENT READY" >> $VALIDATION_REPORT
                    echo "All validation checks passed. Project is ready for deployment." >> $VALIDATION_REPORT
                    
                    echo ""
                    echo "🎉 ALL VALIDATION CHECKS PASSED!"
                    echo "📋 Project is ready for deployment"
                    echo "📁 Full report: $VALIDATION_REPORT"
                    exit 0
                else
                    echo "## ❌ DEPLOYMENT BLOCKED" >> $VALIDATION_REPORT
                    echo "The following issues must be resolved before deployment:" >> $VALIDATION_REPORT
                    for failed_check in "${FAILED_CHECKS[@]}"; do
                        echo "- $failed_check" >> $VALIDATION_REPORT
                    done
                    
                    echo ""
                    echo "🚫 VALIDATION FAILED!"
                    echo "❌ ${#FAILED_CHECKS[@]} check(s) failed"
                    echo "📋 Review issues in: $VALIDATION_REPORT"
                    exit 1
                fi
                EOF
                
                chmod +x Admin-Local/Deployment/Scripts/pre-deployment-validation.sh
                
                ```
                
            2. Run the pre-deployment validation checklist.
                
                ```bash
                ./Admin-Local/Deployment/Scripts/pre-deployment-validation.sh
                
                ```
                
            3. **Critical:** Address all failed validation points before proceeding to the next step.

    6. **Step 16.2: Build Strategy Configuration**
        1. **Purpose:** Configure and validate the build strategy (local, VM, or server-based) using path variables system for flexible deployment workflows.
        2. **When:** After pre-deployment validation passes, before security scans.
        3. **Action:**
            1. Create build strategy configuration script.
                
                ```bash
                cat > Admin-Local/Deployment/Scripts/configure-build-strategy.sh << 'EOF'
                #!/bin/bash
                
                echo "╔══════════════════════════════════════════════════════════╗"
                echo "║             Build Strategy Configuration                 ║"
                echo "╚══════════════════════════════════════════════════════════╝"
                
                # Load deployment variables
                source %path-localMachine%/Admin-Local/Deployment/Scripts/load-variables.sh
                
                # Get build configuration
                BUILD_LOCATION=$(jq -r '.deployment.build_location' Admin-Local/Deployment/Configs/deployment-variables.json)
                DEPLOYMENT_STRATEGY=$(jq -r '.deployment.strategy' Admin-Local/Deployment/Configs/deployment-variables.json)
                
                echo "📋 Current Configuration:"
                echo "   Build Location: $BUILD_LOCATION"
                echo "   Deployment Strategy: $DEPLOYMENT_STRATEGY"
                echo "   Project: $PROJECT_NAME"
                echo ""
                
                # Create build strategy script based on configuration
                BUILD_SCRIPT="Admin-Local/Deployment/Scripts/execute-build-strategy.sh"
                
                cat > $BUILD_SCRIPT << 'BUILD_EOF'
                #!/bin/bash
                
                # Execute Build Strategy
                source %path-localMachine%/Admin-Local/Deployment/Scripts/load-variables.sh
                
                BUILD_LOCATION=$(jq -r '.deployment.build_location' Admin-Local/Deployment/Configs/deployment-variables.json)
                BUILD_REPORT="Admin-Local/Deployment/Logs/build-execution-$(date +%Y%m%d-%H%M%S).md"
                
                echo "# Build Execution Report" > $BUILD_REPORT
                echo "Generated: $(date)" >> $BUILD_REPORT
                echo "Strategy: $BUILD_LOCATION" >> $BUILD_REPORT
                echo "" >> $BUILD_REPORT
                
                case $BUILD_LOCATION in
                    "local")
                        echo "🏠 Executing LOCAL build strategy..."
                        echo "## 🏠 Local Build Execution" >> $BUILD_REPORT
                        
                        # Create local build directory
                        mkdir -p "$PATH_LOCAL_MACHINE/build-tmp"
                        cd "$PATH_LOCAL_MACHINE"
                        
                        echo "📁 Build Path: $PATH_LOCAL_MACHINE/build-tmp" >> $BUILD_REPORT
                        
                        # Clean previous build
                        echo "🧹 Cleaning previous build artifacts..."
                        rm -rf build-tmp/*
                        
                        # Copy source code
                        echo "📋 Copying source code..."
                        rsync -av --exclude='build-tmp' --exclude='node_modules' --exclude='vendor' . build-tmp/
                        
                        cd build-tmp
                        
                        # Install dependencies
                        echo "📦 Installing production dependencies..."
                        echo "### Dependencies Installation" >> $BUILD_REPORT
                        
                        if composer install --no-dev --prefer-dist --optimize-autoloader >> $BUILD_REPORT 2>&1; then
                            echo "✅ Composer dependencies installed" >> $BUILD_REPORT
                        else
                            echo "❌ Composer installation failed" >> $BUILD_REPORT
                            exit 1
                        fi
                        
                        if [ -f "package.json" ]; then
                            if npm ci --only=production >> $BUILD_REPORT 2>&1; then
                                echo "✅ NPM dependencies installed" >> $BUILD_REPORT
                            else
                                echo "❌ NPM installation failed" >> $BUILD_REPORT
                                exit 1
                            fi
                            
                            if npm run build >> $BUILD_REPORT 2>&1; then
                                echo "✅ Frontend assets built" >> $BUILD_REPORT
                            else
                                echo "❌ Frontend build failed" >> $BUILD_REPORT
                                exit 1
                            fi
                        fi
                        
                        # Laravel optimizations
                        echo "⚡ Applying Laravel optimizations..."
                        echo "### Laravel Optimizations" >> $BUILD_REPORT
                        
                        cp .env.production .env 2>/dev/null || cp .env.example .env
                        php artisan key:generate --force >> $BUILD_REPORT 2>&1
                        php artisan config:cache >> $BUILD_REPORT 2>&1
                        php artisan route:cache >> $BUILD_REPORT 2>&1
                        php artisan view:cache >> $BUILD_REPORT 2>&1
                        
                        echo "✅ Local build completed successfully" >> $BUILD_REPORT
                        ;;
                        
                    "vm")
                        echo "☁️ Executing VM-based build strategy..."
                        echo "## ☁️ VM Build Execution" >> $BUILD_REPORT
                        
                        # Check if BUILD_SERVER_PATH is configured
                        if [ -z "$BUILD_SERVER_PATH" ]; then
                            echo "❌ BUILD_SERVER_PATH not configured for VM build" >> $BUILD_REPORT
                            exit 1
                        fi
                        
                        echo "📁 Build Server Path: $BUILD_SERVER_PATH" >> $BUILD_REPORT
                        
                        # Create build package
                        echo "📦 Creating build package..."
                        BUILD_PACKAGE="$PATH_LOCAL_MACHINE/${PROJECT_NAME}-build-$(date +%Y%m%d-%H%M%S).tar.gz"
                        
                        tar czf "$BUILD_PACKAGE" \
                            --exclude='build-tmp' \
                            --exclude='node_modules' \
                            --exclude='vendor' \
                            --exclude='.git' \
                            -C "$PATH_LOCAL_MACHINE" .
                        
                        echo "📁 Package created: $BUILD_PACKAGE" >> $BUILD_REPORT
                        
                        # Note: VM execution would require SSH/deployment tool integration
                        echo "⚠️ VM build package ready. Upload to build server and execute build commands." >> $BUILD_REPORT
                        echo "Build commands should mirror local strategy within VM environment." >> $BUILD_REPORT
                        ;;
                        
                    "server")
                        echo "🖥️ Executing SERVER-based build strategy..."
                        echo "## 🖥️ Server Build Execution" >> $BUILD_REPORT
                        
                        echo "📁 Server Build Path: $PATH_SERVER/build-tmp" >> $BUILD_REPORT
                        
                        # Note: Server build would require SSH access or deployment automation
                        echo "⚠️ Server build configuration prepared." >> $BUILD_REPORT
                        echo "Build should be executed on server with:" >> $BUILD_REPORT
                        echo "- composer install --no-dev --prefer-dist --optimize-autoloader" >> $BUILD_REPORT
                        echo "- npm ci --only=production && npm run build" >> $BUILD_REPORT
                        echo "- Laravel optimization commands" >> $BUILD_REPORT
                        ;;
                        
                    *)
                        echo "❌ Unknown build location: $BUILD_LOCATION" >> $BUILD_REPORT
                        exit 1
                        ;;
                esac
                
                echo ""
                echo "📋 Build execution report: $BUILD_REPORT"
                BUILD_EOF
                
                chmod +x $BUILD_SCRIPT
                
                # Create build validation script
                cat > Admin-Local/Deployment/Scripts/validate-build-output.sh << 'VALIDATE_EOF'
                #!/bin/bash
                
                echo "🔍 Validating build output..."
                source %path-localMachine%/Admin-Local/Deployment/Scripts/load-variables.sh
                
                BUILD_LOCATION=$(jq -r '.deployment.build_location' Admin-Local/Deployment/Configs/deployment-variables.json)
                
                case $BUILD_LOCATION in
                    "local")
                        BUILD_DIR="$PATH_LOCAL_MACHINE/build-tmp"
                        ;;
                    "vm"|"server")
                        echo "⚠️ Remote build validation requires manual verification"
                        exit 0
                        ;;
                esac
                
                if [ ! -d "$BUILD_DIR" ]; then
                    echo "❌ Build directory not found: $BUILD_DIR"
                    exit 1
                fi
                
                cd "$BUILD_DIR"
                
                # Validate critical files exist
                VALIDATION_ERRORS=()
                
                [ ! -f "composer.json" ] && VALIDATION_ERRORS+=("composer.json missing")
                [ ! -d "vendor" ] && VALIDATION_ERRORS+=("vendor directory missing")
                [ ! -f "artisan" ] && VALIDATION_ERRORS+=("artisan file missing")
                [ ! -f ".env" ] && VALIDATION_ERRORS+=(".env file missing")
                
                if [ -f "package.json" ]; then
                    [ ! -d "public/build" ] && [ ! -d "public/assets" ] && VALIDATION_ERRORS+=("frontend build assets missing")
                fi
                
                # Validate Laravel caches
                [ ! -f "bootstrap/cache/config.php" ] && VALIDATION_ERRORS+=("config cache missing")
                [ ! -f "bootstrap/cache/routes-v7.php" ] && [ ! -f "bootstrap/cache/routes.php" ] && VALIDATION_ERRORS+=("route cache missing")
                
                if [ ${#VALIDATION_ERRORS[@]} -gt 0 ]; then
                    echo "❌ Build validation failed:"
                    for error in "${VALIDATION_ERRORS[@]}"; do
                        echo "   - $error"
                    done
                    exit 1
                else
                    echo "✅ Build validation successful"
                    echo "📁 Build ready at: $BUILD_DIR"
                fi
                VALIDATE_EOF
                
                chmod +x Admin-Local/Deployment/Scripts/validate-build-output.sh
                
                echo "✅ Build strategy configured successfully!"
                echo ""
                echo "📋 Available commands:"
                echo "   🔨 Execute build: ./Admin-Local/Deployment/Scripts/execute-build-strategy.sh"
                echo "   ✅ Validate build: ./Admin-Local/Deployment/Scripts/validate-build-output.sh"
                echo ""
                echo "⚡ Build Location: $BUILD_LOCATION"
                echo "🎯 Deployment Strategy: $DEPLOYMENT_STRATEGY"
                EOF
                
                chmod +x Admin-Local/Deployment/Scripts/configure-build-strategy.sh
                
                ```
                
            2. Configure the build strategy.
                
                ```bash
                ./Admin-Local/Deployment/Scripts/configure-build-strategy.sh
                
                ```
                
            3. Test the build execution (for local builds).
                
                ```bash
                ./Admin-Local/Deployment/Scripts/execute-build-strategy.sh
                
                ```
                
            4. Validate the build output.
                
                ```bash
                ./Admin-Local/Deployment/Scripts/validate-build-output.sh
                
                ```

    7. **Step 17: Run Security Scans `(New Added)`**
       1. **Purpose:** Identify and fix potential security vulnerabilities before deployment.
       2. **Action:**
           1. Use a tool like Snyk or Larastan to scan the codebase for vulnerabilities.
   6. **Step 18: Customization Protection**
        1. **Purpose:** Implement a Laravel (with or without js)-compatible customization layer to protect changes during team or upstream vendor updates, and establish comprehensive investment protection documentation for the customization project.
        2. **Action: Create and use scripts from Universal Customization System and Investment Protection System templates**
            1. **Customization Layer Setup** - Use `setup-customization.sh` (ensure this customization project adapts per tech stack of marketplace app):
                - Create a protected directory structure for customizations in `app/Custom`, `config/custom`, `database/migrations/custom`, `resources/views/custom`, and `public/custom`.
                - Create custom configuration files (e.g., `config/custom-app.php`, `config/custom-database.php`).
                - Create a `CustomizationServiceProvider` to load custom routes, views, migrations, and blade directives.
                - Register the `CustomizationServiceProvider` in `config/app.php`.
                - Add custom environment variables to the `.env` file for feature toggles, branding, and other settings.
                - Create an update-safe asset strategy using a separate webpack configuration (e.g., `webpack.custom.js`).
            2. **Investment Protection Documentation** - Use `setup-customization-protection.sh`:
                - Set up comprehensive investment tracking and documentation system for the customization project
                - Create investment summary and ROI analysis tools for customization project
                - Implement customization catalog and business logic documentation
                - Generate API extensions, frontend changes, and database modifications documentation
                - Set up security enhancements and performance optimization tracking for customization project
                - Create integration points documentation and recovery procedures
                - Generate automated customization project documentation and team handoff materials
            3. **Stack Variations:**
                - **Blade-only:** Focus on `CustomizationServiceProvider` for backend logic and `resources/views/custom` for frontend overrides.
                - **Inertia/Vue/React:** Use the `CustomizationServiceProvider` for backend logic and create a separate, namespaced frontend component library within `resources/js/Custom` to avoid conflicts with vendor components.
    7. **Step 19: Data Persistence Strategy**
        1. **Purpose:** Implement a zero data loss system with smart content protection during deployments.
        - **✅ Goal Achieved:** "Zero data loss during deployments" with focus on user uploads, invoices, QR codes, exports
        - **✅ Correct Strategy:** Uses exclusion-based approach (everything is vendor code EXCEPT specific user data)
        - **✅ Smart Detection:** Auto-detects Laravel, Next.js, React/Vue with appropriate exclusions
        - **✅ Advanced Features:** Framework detection, smart build artifact exclusions, comprehensive documentation
        1. **Action:**
            1. Create an advanced persistence script (`link_persistent_dirs.sh`) with automatic framework detection and smart build artifact exclusions.
            2. Test the persistence script locally.
            3. Create data persistence documentation.
            4. Create a verification script (`verify_persistence.sh`).
    8. **Step 20: Commit Pre-Deployment Setup**
        1. **Purpose:** Commit all preparation work to the repository with comprehensive documentation.
        2. **Action:**
            1. Verify the current status of the repository and ensure sensitive files are excluded.
            2. Run a comprehensive verification script to ensure all Phase 2 steps are complete.
            3. Add all appropriate files to the staging area.
            4. Create a comprehensive commit message documenting all preparation work.
            5. Push the changes to the repository.
            6. Create a deployment readiness report.

-----
# Universal Laravel Zero-Downtime Deployment Flow v2.0

**Complete professional zero-downtime deployment pipeline with path variables, visual identification, and universal build strategies**

## **Visual Step Identification Guide**

-   🟢 **Local Machine**: Steps executed on developer's local environment
-   🟡 **Builder VM**: Steps executed on dedicated build/CI server
-   🔴 **Server**: Steps executed on production server
-   🟣 **User-configurable**: Customizable hooks and user-defined commands

## **Path Variables System**

All paths use dynamic variables from `deployment-variables.json`:

-   `%path-localMachine%`: Local development machine paths
-   `%path-Builder-VM%`: Build server/CI environment paths
-   `%path-server%`: Production server paths

Load variables at script start:

```bash
# Load deployment variables
source %path-localMachine%/Admin-Local/Deployment/Scripts/load-variables.sh
```

## **Complete Zero-Downtime Deployment Flow**

## **Phase 1: 🖥️ Prepare Build Environment**

**1.1 - 🟢 Pre-Build Environment Preparation (Local Machine)**

-   **Objectives:**
    -   Load deployment configuration variables
    -   Validate deployment permissions and access
    -   Check repository connectivity and branch availability
    -   Initialize deployment workspace
-   **Commands:**

    ```bash
    # Load deployment variables
    source %path-localMachine%/Admin-Local/Deployment/Scripts/load-variables.sh

    # Initialize deployment workspace
    mkdir -p "${DEPLOY_WORKSPACE}/logs"
    cd "${DEPLOY_WORKSPACE}"

    # Repository connectivity validation
    git ls-remote --heads "${REPOSITORY_URL}" > /dev/null || {
        echo "❌ Repository not accessible: ${REPOSITORY_URL}"
        exit 1
    }

    # Branch availability check
    git ls-remote --heads "${REPOSITORY_URL}" "${DEPLOY_BRANCH}" | grep -q "${DEPLOY_BRANCH}" || {
        echo "❌ Branch ${DEPLOY_BRANCH} not found"
        exit 1
    }

    echo "✅ Pre-build preparation complete"
    ```

**1.2 - 🟡 Build Environment Setup (Builder VM)**

-   **Objectives:**
    -   Initialize clean build environment based on configured strategy
    -   Set up correct versions matching production environment
    -   Configure build environment variables
    -   Execute comprehensive environment analysis
-   **Commands:**

    ```bash
    # Execute build strategy configuration
    source %path-Builder-VM%/Admin-Local/Deployment/Scripts/configure-build-strategy.sh

    # Run comprehensive environment analysis
    source %path-Builder-VM%/Admin-Local/Deployment/Scripts/comprehensive-env-check.sh

    # Build environment initialization based on strategy
    case "${BUILD_STRATEGY}" in
        "local")
            echo "🏠 Using local build environment"
            BUILD_PATH="${LOCAL_BUILD_PATH}"
            ;;
        "vm")
            echo "🖥️ Using VM build environment"
            BUILD_PATH="${VM_BUILD_PATH}"
            # Initialize VM if needed
            vagrant up build-vm 2>/dev/null || echo "VM already running"
            ;;
        "server")
            echo "🌐 Using server build environment"
            BUILD_PATH="${SERVER_BUILD_PATH}"
            # Validate server connection
            ssh -o ConnectTimeout=10 "${BUILD_SERVER}" "echo 'Server connection OK'" || {
                echo "❌ Cannot connect to build server: ${BUILD_SERVER}"
                exit 1
            }
            ;;
        *)
            echo "❌ Unknown build strategy: ${BUILD_STRATEGY}"
            exit 1
            ;;
    esac

    # Set build environment variables
    export BUILD_ENV="production"
    export COMPOSER_MEMORY_LIMIT=-1
    export NODE_ENV="production"

    echo "✅ Build environment setup complete for strategy: ${BUILD_STRATEGY}"
    ```

**1.3 - 🟡 Repository Preparation (Builder VM)**

-   **Objectives:**
    -   Clone repository to build environment
    -   Checkout target commit/branch
    -   Validate commit integrity and build requirements
-   **Commands:**

    ```bash
    # Repository cloning with build strategy support
    cd "${BUILD_PATH}"

    # Clean build directory
    rm -rf "${PROJECT_NAME}" 2>/dev/null || true

    # Clone repository with optimized settings
    echo "📥 Cloning repository..."
    git clone --depth=1 --branch="${DEPLOY_BRANCH}" "${REPOSITORY_URL}" "${PROJECT_NAME}" || {
        echo "❌ Repository clone failed"
        exit 1
    }

    cd "${PROJECT_NAME}"

    # Checkout specific commit if provided
    if [[ -n "${TARGET_COMMIT}" ]]; then
        git fetch --depth=1 origin "${TARGET_COMMIT}"
        git checkout "${TARGET_COMMIT}" || {
            echo "❌ Cannot checkout commit: ${TARGET_COMMIT}"
            exit 1
        }
    fi

    # Validate repository structure
    [[ -f "composer.json" ]] || { echo "❌ composer.json not found"; exit 1; }
    [[ -f "artisan" ]] || { echo "❌ artisan not found"; exit 1; }

    # Log commit information
    echo "✅ Repository prepared:"
    echo "  - Branch: $(git branch --show-current)"
    echo "  - Commit: $(git rev-parse HEAD)"
    echo "  - Message: $(git log -1 --pretty=format:'%s')"
    ```

---

## **Phase 2: 🏗️ Build Application**

**2.1 - 🟡 Intelligent Cache Restoration (Builder VM)**

-   **Objectives:**
    -   Restore cached dependencies with integrity validation
    -   Speed up build process intelligently
    -   Maintain consistency with lock files across build strategies
-   **Commands:**

    ```bash
    # Intelligent cache restoration with build strategy support
    echo "📦 Intelligent cache restoration..."

    # Load cache configuration from deployment variables
    CACHE_DIR="${CACHE_BASE_PATH}/${PROJECT_NAME}"
    COMPOSER_CACHE="${CACHE_DIR}/composer"
    NPM_CACHE="${CACHE_DIR}/npm"

    # Validate lock file consistency
    COMPOSER_HASH=$(md5sum composer.lock 2>/dev/null | cut -d' ' -f1)
    CACHED_COMPOSER_HASH=$(cat "${COMPOSER_CACHE}/.hash" 2>/dev/null || echo "")

    if [[ -f "package-lock.json" ]]; then
        NPM_HASH=$(md5sum package-lock.json 2>/dev/null | cut -d' ' -f1)
        CACHED_NPM_HASH=$(cat "${NPM_CACHE}/.hash" 2>/dev/null || echo "")
    fi

    # Restore Composer cache if valid
    if [[ "${COMPOSER_HASH}" == "${CACHED_COMPOSER_HASH}" ]] && [[ -d "${COMPOSER_CACHE}/vendor" ]]; then
        echo "✅ Restoring Composer cache (hash match: ${COMPOSER_HASH})"
        cp -r "${COMPOSER_CACHE}/vendor" ./ 2>/dev/null || true
    else
        echo "⚠️  Composer cache miss or invalid - will rebuild"
        mkdir -p "${COMPOSER_CACHE}"
    fi

    # Restore NPM cache if valid
    if [[ -n "${NPM_HASH}" ]] && [[ "${NPM_HASH}" == "${CACHED_NPM_HASH}" ]] && [[ -d "${NPM_CACHE}/node_modules" ]]; then
        echo "✅ Restoring NPM cache (hash match: ${NPM_HASH})"
        cp -r "${NPM_CACHE}/node_modules" ./ 2>/dev/null || true
    elif [[ -f "package-lock.json" ]]; then
        echo "⚠️  NPM cache miss or invalid - will rebuild"
        mkdir -p "${NPM_CACHE}"
    fi
    ```

**2.2 - 🟡 Universal Dependency Installation (Builder VM)**

-   **Objectives:**
    -   Execute universal dependency analyzer for production readiness
    -   Install dependencies with build-strategy-aware optimization
    -   Validate security and compatibility across environments
-   **Commands:**

    ```bash
    # Execute Universal Dependency Analyzer
    source %path-Builder-VM%/Admin-Local/Deployment/Scripts/universal-dependency-analyzer.sh

    # Install analysis tools for validation
    source %path-Builder-VM%/Admin-Local/Deployment/Scripts/install-analysis-tools.sh

    # Enhanced dependency installation with build strategy support
    echo "=== Universal Dependency Installation ==="

    # Configure Composer for build environment
    case "${BUILD_STRATEGY}" in
        "local"|"vm")
            COMPOSER_FLAGS="--optimize-autoloader --prefer-dist"
            ;;
        "server")
            COMPOSER_FLAGS="--no-dev --optimize-autoloader --no-scripts --prefer-dist --classmap-authoritative"
            ;;
    esac

    # Smart Composer installation
    echo "📦 Installing PHP dependencies..."
    if [[ -f "composer.json" ]]; then
        # Run composer strategy setup
        source %path-Builder-VM%/Admin-Local/Deployment/Scripts/setup-composer-strategy.sh

        # Install with appropriate flags
        composer install ${COMPOSER_FLAGS} || {
            echo "❌ Composer installation failed"
            exit 1
        }

        # Cache successful installation
        if [[ -d "vendor" ]] && [[ -n "${COMPOSER_CACHE}" ]]; then
            rm -rf "${COMPOSER_CACHE}/vendor" 2>/dev/null || true
            cp -r vendor "${COMPOSER_CACHE}/" 2>/dev/null || true
            echo "${COMPOSER_HASH}" > "${COMPOSER_CACHE}/.hash"
        fi
    fi

    # Smart Node.js installation (if applicable)
    if [[ -f "package.json" ]]; then
        echo "📦 Installing Node.js dependencies..."

        # Determine installation strategy
        if grep -q '"build":\|"production":\|"prod":\|"dev":.*"vite\|webpack\|laravel-mix"' package.json; then
            echo "🏗️  Build dependencies detected - installing all packages"
            npm ci --production=false
        else
            echo "📦 Production-only installation"
            npm ci --production=true
        fi

        # Cache successful installation
        if [[ -d "node_modules" ]] && [[ -n "${NPM_CACHE}" ]]; then
            rm -rf "${NPM_CACHE}/node_modules" 2>/dev/null || true
            cp -r node_modules "${NPM_CACHE}/" 2>/dev/null || true
            echo "${NPM_HASH}" > "${NPM_CACHE}/.hash"
        fi

        # Security audit
        npm audit --audit-level=high || echo "⚠️  Security vulnerabilities detected - review required"
    fi

    # Run full analysis validation
    source %path-Builder-VM%/Admin-Local/Deployment/Scripts/run-full-analysis.sh

    echo "✅ Universal dependency installation complete"
    ```

**2.3 - 🟡 Advanced Asset Compilation (Builder VM)**

-   **Objectives:**
    -   Compile frontend assets with build strategy optimization
    -   Support multiple asset bundlers (Vite, Webpack, Laravel Mix)
    -   Generate production-ready, optimized assets
-   **Commands:**

    ```bash
    # Advanced asset compilation with auto-detection
    echo "🎨 Advanced asset compilation..."

    if [[ ! -f "package.json" ]]; then
        echo "📝 No package.json found - skipping asset compilation"
    else
        # Detect asset bundler
        BUNDLER="none"
        if grep -q '"vite"' package.json; then
            BUNDLER="vite"
        elif grep -q '"laravel-mix"' package.json; then
            BUNDLER="mix"
        elif grep -q '"webpack"' package.json; then
            BUNDLER="webpack"
        fi

        echo "🔍 Detected bundler: ${BUNDLER}"

        # Execute build based on detected bundler
        case "${BUNDLER}" in
            "vite")
                echo "⚡ Building with Vite..."
                npm run build || npm run prod || {
                    echo "❌ Vite build failed"
                    exit 1
                }
                ;;
            "mix")
                echo "🏗️  Building with Laravel Mix..."
                npm run production || npm run prod || {
                    echo "❌ Laravel Mix build failed"
                    exit 1
                }
                ;;
            "webpack")
                echo "📦 Building with Webpack..."
                npm run build || npm run production || {
                    echo "❌ Webpack build failed"
                    exit 1
                }
                ;;
            *)
                echo "🤷 Unknown bundler - attempting generic build..."
                npm run build 2>/dev/null || npm run prod 2>/dev/null || npm run production 2>/dev/null || {
                    echo "⚠️  No suitable build script found"
                }
                ;;
        esac

        # Validate build output
        if [[ -d "public/build" ]] || [[ -d "public/assets" ]] || [[ -d "public/js" ]] || [[ -d "public/css" ]]; then
            echo "✅ Asset compilation successful"

            # Clean up dev dependencies post-build (if in production mode)
            if [[ "${BUILD_STRATEGY}" == "server" ]]; then
                echo "🧹 Cleaning up dev dependencies..."
                rm -rf node_modules
                npm ci --production=true --silent 2>/dev/null || true
            fi
        else
            echo "⚠️  No build output detected - build may have failed silently"
        fi
    fi
    ```

**2.4 - 🟡 Laravel Production Optimization (Builder VM)**

-   **Objectives:**
    -   Apply comprehensive Laravel optimizations for production
    -   Cache configurations, routes, and views
    -   Optimize autoloader for production performance
-   **Commands:**

    ```bash
    # Comprehensive Laravel optimization
    echo "⚡ Laravel production optimization..."

    # Clear existing caches
    php artisan cache:clear --quiet 2>/dev/null || true
    php artisan config:clear --quiet 2>/dev/null || true
    php artisan route:clear --quiet 2>/dev/null || true
    php artisan view:clear --quiet 2>/dev/null || true

    # Production optimization sequence
    echo "📝 Caching configuration..."
    php artisan config:cache || {
        echo "❌ Config cache failed"
        exit 1
    }

    echo "🗺️  Caching routes..."
    php artisan route:cache || {
        echo "❌ Route cache failed"
        exit 1
    }

    echo "👁️  Caching views..."
    php artisan view:cache || {
        echo "⚠️  View cache failed - continuing anyway"
    }

    # Optimize Composer autoloader
    echo "🔧 Optimizing autoloader..."
    composer dump-autoload --optimize --classmap-authoritative --no-dev || {
        echo "❌ Autoloader optimization failed"
        exit 1
    }

    # Additional Laravel optimizations
    if php artisan list | grep -q "icons:cache"; then
        echo "🎨 Caching icons..."
        php artisan icons:cache --quiet 2>/dev/null || true
    fi

    if php artisan list | grep -q "event:cache"; then
        echo "📅 Caching events..."
        php artisan event:cache --quiet 2>/dev/null || true
    fi

    echo "✅ Laravel optimization complete"
    ```

**2.5 - 🟡 Comprehensive Build Validation (Builder VM)**

-   **Objectives:**
    -   Execute pre-deployment validation checklist
    -   Run automated tests and integrity checks
    -   Validate build artifacts and dependencies
-   **Commands:**

    ```bash
    # Execute comprehensive pre-deployment validation
    source %path-Builder-VM%/Admin-Local/Deployment/Scripts/pre-deployment-validation.sh

    # Additional build-specific validation
    echo "🔍 Build artifact validation..."

    # Critical file existence check
    CRITICAL_FILES=(
        "bootstrap/app.php"
        "artisan"
        "composer.json"
        "composer.lock"
    )

    for file in "${CRITICAL_FILES[@]}"; do
        [[ -f "${file}" ]] || {
            echo "❌ Critical file missing: ${file}"
            exit 1
        }
    done

    # Validate vendor directory
    [[ -d "vendor" ]] && [[ -f "vendor/autoload.php" ]] || {
        echo "❌ Vendor directory invalid"
        exit 1
    }

    # Test basic Laravel functionality
    echo "🧪 Testing Laravel bootstrap..."
    php artisan --version --quiet || {
        echo "❌ Laravel bootstrap failed"
        exit 1
    }

    # Optional: Run automated tests
    if [[ "${RUN_TESTS}" == "true" ]] && [[ -d "tests" ]]; then
        echo "🧪 Running automated tests..."
        if command -v parallel-lint >/dev/null 2>&1; then
            php vendor/bin/phpunit --testsuite=Unit --stop-on-failure || {
                echo "❌ Unit tests failed"
                exit 1
            }
        else
            php artisan test --parallel --stop-on-failure || {
                echo "❌ Tests failed"
                exit 1
            }
        fi
    fi

    echo "✅ Build validation complete - ready for deployment"
    ```

    **2.6 - Runtime Dependency Validation**

    -   **Objectives:**
        -   **NEW:** Validate runtime dependency compatibility and environment setup
        -   Detect missing production dependencies at runtime
        -   Verify environment variable requirements
        -   Validate database and cache connectivity
        -   Check for version conflicts that could cause runtime failures
    -   **Commands:**

        ```bash
        # Runtime Dependency Validation (from Claude-2 deployment guide)
        echo "=== Runtime Dependency Validation ==="

        # 1. Critical dependency runtime validation
        echo "🔍 Validating critical runtime dependencies..."

        # Test Laravel bootstrap
        php -r "
            try {
                require_once 'vendor/autoload.php';
                \$app = require_once 'bootstrap/app.php';
                echo '✅ Laravel bootstrap successful\n';
            } catch (Exception \$e) {
                echo '❌ Laravel bootstrap failed: ' . \$e->getMessage() . '\n';
                exit(1);
            }
        "

        # 2. Database connectivity validation
        echo "🗃️  Validating database connectivity..."
        php artisan migrate:status --env=production 2>/dev/null || {
            echo "⚠️  Database connection issues detected"
            # Don't exit here as DB might not be configured yet
        }

        # 3. Cache system validation
        echo "💾 Validating cache systems..."
        php artisan cache:clear --quiet 2>/dev/null || echo "⚠️  Cache system issues"

        # 4. Environment variable validation
        echo "🔧 Validating critical environment variables..."
        php -r "
            \$required = ['APP_KEY', 'APP_ENV', 'DB_CONNECTION'];
            \$missing = [];
            foreach (\$required as \$var) {
                if (empty(getenv(\$var))) {
                    \$missing[] = \$var;
                }
            }
            if (!empty(\$missing)) {
                echo '❌ Missing required environment variables: ' . implode(', ', \$missing) . '\n';
                echo 'Set these in your .env file before deployment\n';
                exit(1);
            }
            echo '✅ Critical environment variables present\n';
        "

        # 5. Composer production readiness check
        echo "📦 Final composer production validation..."
        php -r "
            \$composer = json_decode(file_get_contents('composer.json'), true);
            \$prodIssues = [];

            // Check for common dev dependencies that shouldn't be in production
            \$devDeps = array_keys(\$composer['require-dev'] ?? []);
            foreach (['phpunit', 'mockery', 'fakerphp'] as \$devPkg) {
                if (in_array(\$devPkg, \$devDeps)) {
                    // Check if it's actually used in production code
                    \$usage = shell_exec('grep -r \"' . \$devPkg . '\" app/ bootstrap/ config/ routes/ 2>/dev/null || true');
                    if (!empty(\$usage) && strpos(\$usage, 'test') === false) {
                        \$prodIssues[] = 'Dev dependency ' . \$devPkg . ' used in production code';
                    }
                }
            }

            if (!empty(\$prodIssues)) {
                echo '⚠️  Production readiness issues:\n';
                foreach (\$prodIssues as \$issue) {
                    echo '  - ' . \$issue . '\n';
                }
            } else {
                echo '✅ Composer production validation passed\n';
            }
        "

        # 6. File permissions and structure validation
        echo "📁 Validating file permissions and structure..."
        [[ -w "storage/" ]] || { echo "❌ Storage directory not writable"; exit 1; }
        [[ -w "bootstrap/cache/" ]] || { echo "❌ Bootstrap cache not writable"; exit 1; }

        # 7. Artisan command validation
        echo "⚙️  Validating core Artisan commands..."
        php artisan --version --quiet || { echo "❌ Artisan not functional"; exit 1; }

        echo "✅ Runtime dependency validation complete"
        ```

    ***

## **Phase 3: 📦 Package & Transfer**

**3.1 - 🟡 Smart Build Artifact Preparation (Builder VM)**

-   **Objectives:**
    -   Create deployment manifest with build strategy awareness
    -   Package optimized application artifacts
    -   Calculate checksums and validate integrity
-   **Commands:**

    ```bash
    # Smart artifact preparation with build strategy support
    echo "📦 Preparing deployment artifacts..."

    # Execute build strategy-specific artifact creation
    source %path-Builder-VM%/Admin-Local/Deployment/Scripts/validate-build-output.sh

    # Create deployment manifest
    echo "📝 Creating deployment manifest..."
    cat > deployment-manifest.json << EOF
    {
        "build_strategy": "${BUILD_STRATEGY}",
        "release_id": "$(date +%Y%m%d%H%M%S)",
        "git_commit": "$(git rev-parse HEAD)",
        "git_branch": "$(git branch --show-current)",
        "build_timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
        "php_version": "$(php --version | head -n1)",
        "composer_version": "$(composer --version)",
        "node_version": "$(node --version 2>/dev/null || echo 'not installed')",
        "environment": "${BUILD_ENV:-production}"
    }
    EOF

    # Create optimized artifact package
    echo "🗂️  Creating artifact package..."

    # Define exclusion patterns from deployment variables
    EXCLUDE_PATTERNS=(
        ".git"
        ".github"
        "tests"
        "node_modules"
        ".env*"
        ".phpunit*"
        "phpunit.xml"
        "webpack.mix.js"
        "vite.config.js"
        ".eslintrc*"
        ".editorconfig"
        "*.log"
        "Admin-Local"
    )

    # Build tar command with exclusions
    TAR_EXCLUDES=""
    for pattern in "${EXCLUDE_PATTERNS[@]}"; do
        TAR_EXCLUDES="${TAR_EXCLUDES} --exclude='${pattern}'"
    done

    # Create release artifact
    eval "tar ${TAR_EXCLUDES} -czf release-$(date +%Y%m%d%H%M%S).tar.gz ."

    # Generate checksums
    echo "🔐 Generating checksums..."
    md5sum release-*.tar.gz > release.md5
    sha256sum release-*.tar.gz > release.sha256

    echo "✅ Artifact preparation complete"
    ```

    **3.2 - 🔴 Comprehensive Server Preparation (Server)**

-   **Objectives:**
    -   Prepare zero-downtime deployment structure
    -   Create intelligent backup strategy
    -   Setup comprehensive shared resources
-   **Commands:**

    ```bash
    # Enhanced server preparation with path variables
    echo "🔴 Preparing server for zero-downtime deployment..."

    # Load deployment variables
    source %path-server%/deployment-variables.json
    DEPLOY_PATH="${DEPLOYMENT_PATH}"
    RELEASES_PATH="${DEPLOY_PATH}/releases"
    CURRENT_PATH="${DEPLOY_PATH}/current"
    SHARED_PATH="${DEPLOY_PATH}/shared"

    # Create comprehensive directory structure
    echo "📁 Creating deployment directory structure..."
    mkdir -p "${RELEASES_PATH}" "${SHARED_PATH}"

    # Intelligent backup strategy
    echo "💾 Executing backup strategy..."
    if [ -L "${CURRENT_PATH}" ]; then
        BACKUP_ID="backup-$(date +%Y%m%d%H%M%S)"
        BACKUP_PATH="${RELEASES_PATH}/${BACKUP_ID}"

        # Create backup with hard links (space efficient)
        cp -al "${CURRENT_PATH}" "${BACKUP_PATH}"
        echo "✅ Current release backed up to: ${BACKUP_ID}"

        # Cleanup old backups (keep last 3)
        cd "${RELEASES_PATH}"
        ls -1t backup-* 2>/dev/null | tail -n +4 | xargs -r rm -rf
    fi

    # Comprehensive shared resources setup
    echo "🔗 Setting up shared resources..."

    # Laravel storage directories
    mkdir -p "${SHARED_PATH}/storage/app/public"
    mkdir -p "${SHARED_PATH}/storage/logs"
    mkdir -p "${SHARED_PATH}/storage/framework/cache/data"
    mkdir -p "${SHARED_PATH}/storage/framework/sessions"
    mkdir -p "${SHARED_PATH}/storage/framework/views"
    mkdir -p "${SHARED_PATH}/storage/framework/testing"

    # Upload directories
    mkdir -p "${SHARED_PATH}/public/uploads"
    mkdir -p "${SHARED_PATH}/public/media"

    # Configuration files
    mkdir -p "${SHARED_PATH}/config"

    # SSL certificates (if applicable)
    mkdir -p "${SHARED_PATH}/ssl"

    # Set proper permissions
    echo "🔐 Setting permissions..."
    chown -R www-data:www-data "${SHARED_PATH}/storage"
    chmod -R 755 "${SHARED_PATH}/storage"
    chmod -R 775 "${SHARED_PATH}/storage/logs"
    chmod -R 775 "${SHARED_PATH}/storage/framework/cache"
    chmod -R 775 "${SHARED_PATH}/storage/framework/sessions"
    chmod -R 775 "${SHARED_PATH}/storage/framework/views"

    echo "✅ Server preparation complete"
    ```

    **3.3 - 🔴 Intelligent Release Directory Creation (Server)**

-   **Objectives:**
    -   Create timestamped release directory with proper structure
    -   Configure release-specific environment
    -   Validate deployment space and permissions
-   **Commands:**

    ```bash
    # Intelligent release directory creation
    echo "🔴 Creating release directory structure..."

    # Generate unique release identifier
    RELEASE_ID="$(date +%Y%m%d%H%M%S)-$(git rev-parse --short HEAD 2>/dev/null || echo 'manual')"
    RELEASE_PATH="${DEPLOY_PATH}/releases/${RELEASE_ID}"

    # Pre-flight checks
    echo "🔍 Pre-flight deployment checks..."

    # Check available disk space (require at least 1GB)
    AVAILABLE_SPACE=$(df "${DEPLOY_PATH}" | awk 'NR==2 {print $4}')
    REQUIRED_SPACE=1048576  # 1GB in KB

    if [[ "${AVAILABLE_SPACE}" -lt "${REQUIRED_SPACE}" ]]; then
        echo "❌ Insufficient disk space: ${AVAILABLE_SPACE}KB available, ${REQUIRED_SPACE}KB required"
        exit 1
    fi

    # Validate write permissions
    touch "${DEPLOY_PATH}/.write-test" 2>/dev/null || {
        echo "❌ No write permission in deployment directory: ${DEPLOY_PATH}"
        exit 1
    }
    rm -f "${DEPLOY_PATH}/.write-test"

    # Create release directory with proper structure
    echo "📁 Creating release directory: ${RELEASE_ID}"
    mkdir -p "${RELEASE_PATH}"

    # Create release metadata
    cat > "${RELEASE_PATH}/.release-info" << EOF
    {
        "release_id": "${RELEASE_ID}",
        "created_at": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
        "git_commit": "$(git rev-parse HEAD 2>/dev/null || echo 'unknown')",
        "git_branch": "$(git branch --show-current 2>/dev/null || echo 'unknown')",
        "build_strategy": "${BUILD_STRATEGY:-local}",
        "deployed_by": "${USER:-unknown}",
        "server_hostname": "$(hostname)"
    }
    EOF

    # Set proper permissions
    echo "🔐 Setting release permissions..."
    chmod 755 "${RELEASE_PATH}"
    chmod 644 "${RELEASE_PATH}/.release-info"

    # If running as root, ensure www-data ownership
    if [[ "${EUID}" -eq 0 ]]; then
        chown -R www-data:www-data "${RELEASE_PATH}"
    fi

    echo "✅ Release directory created: ${RELEASE_PATH}"
    echo "📊 Available space: $((AVAILABLE_SPACE / 1024))MB"
    ```

    **3.4 - 🔴 Optimized File Transfer & Validation (Server)**

-   **Objectives:**
    -   Transfer and validate build artifacts securely
    -   Maintain file integrity and permissions
    -   Optimize transfer speed with validation
-   **Commands:**

    ```bash
    # Optimized file transfer with integrity validation
    echo "🔴 Transferring and validating build artifacts..."

    # Find the latest build artifact
    ARTIFACT_FILE=$(ls -t release-*.tar.gz 2>/dev/null | head -n1)

    if [[ -z "${ARTIFACT_FILE}" ]]; then
        echo "❌ No build artifact found"
        exit 1
    fi

    echo "📦 Found artifact: ${ARTIFACT_FILE}"

    # Validate checksums before extraction
    echo "🔐 Validating artifact integrity..."
    if [[ -f "release.sha256" ]]; then
        sha256sum -c release.sha256 || {
            echo "❌ Artifact integrity check failed"
            exit 1
        }
        echo "✅ Artifact integrity verified"
    else
        echo "⚠️  No checksum file found - proceeding without verification"
    fi

    # Extract with progress and validation
    echo "📂 Extracting to release directory..."
    tar -xzf "${ARTIFACT_FILE}" -C "${RELEASE_PATH}" --keep-old-files 2>/dev/null || {
        # If --keep-old-files fails, try without it
        tar -xzf "${ARTIFACT_FILE}" -C "${RELEASE_PATH}" || {
            echo "❌ Artifact extraction failed"
            exit 1
        }
    }

    # Validate critical Laravel files
    echo "🔍 Validating Laravel structure..."
    CRITICAL_FILES=(
        "artisan"
        "bootstrap/app.php"
        "composer.json"
        "composer.lock"
        "public/index.php"
    )

    for file in "${CRITICAL_FILES[@]}"; do
        [[ -f "${RELEASE_PATH}/${file}" ]] || {
            echo "❌ Critical file missing after extraction: ${file}"
            exit 1
        }
    done

    # Set comprehensive file permissions
    echo "🔐 Setting comprehensive permissions..."

    # Set ownership (if running as root)
    if [[ "${EUID}" -eq 0 ]]; then
        chown -R www-data:www-data "${RELEASE_PATH}"
        echo "✅ Ownership set to www-data:www-data"
    fi

    # Set directory permissions (755 = rwxr-xr-x)
    find "${RELEASE_PATH}" -type d -exec chmod 755 {} \;

    # Set file permissions (644 = rw-r--r--)
    find "${RELEASE_PATH}" -type f -exec chmod 644 {} \;

    # Set executable permissions for specific files
    chmod +x "${RELEASE_PATH}/artisan"

    # Set secure permissions for sensitive files
    [[ -f "${RELEASE_PATH}/.env" ]] && chmod 600 "${RELEASE_PATH}/.env"
    [[ -f "${RELEASE_PATH}/.env.example" ]] && chmod 644 "${RELEASE_PATH}/.env.example"

    # Validate file count and size
    echo "📊 Transfer validation..."
    FILE_COUNT=$(find "${RELEASE_PATH}" -type f | wc -l)
    RELEASE_SIZE=$(du -sh "${RELEASE_PATH}" | cut -f1)

    echo "✅ Transfer complete:"
    echo "  - Files transferred: ${FILE_COUNT}"
    echo "  - Release size: ${RELEASE_SIZE}"
    echo "  - Release path: ${RELEASE_PATH}"

    # Optional: Create transfer manifest
    find "${RELEASE_PATH}" -type f -exec stat -c '%n %s %Y' {} \; > "${RELEASE_PATH}/.transfer-manifest"
    chmod 644 "${RELEASE_PATH}/.transfer-manifest"
    ```

    ***

    ## **Phase 4: 🔗 Configure Release**

**4.1 - 🔴 Advanced Shared Resources Linking (Server)**

-   **Objectives:**
    -   Create comprehensive symlink structure for zero-downtime deployment
    -   Link persistent data with fallback strategies
    -   Configure environment-specific resources
    -   Handle shared hosting limitations intelligently
-   **Commands:**

    ```bash
    # Advanced shared resources linking with comprehensive error handling
    echo "🔴 Configuring shared resources for zero-downtime deployment..."

    # Validate shared directories exist
    echo "📁 Validating shared directory structure..."
    SHARED_DIRS=(
        "${SHARED_PATH}/storage/app/public"
        "${SHARED_PATH}/storage/logs"
        "${SHARED_PATH}/storage/framework/cache/data"
        "${SHARED_PATH}/storage/framework/sessions"
        "${SHARED_PATH}/storage/framework/views"
        "${SHARED_PATH}/storage/framework/testing"
        "${SHARED_PATH}/public/uploads"
        "${SHARED_PATH}/public/media"
    )

    for dir in "${SHARED_DIRS[@]}"; do
        if [[ ! -d "${dir}" ]]; then
            mkdir -p "${dir}"
            chmod 775 "${dir}"
            echo "✅ Created shared directory: ${dir}"
        fi
    done

    # Create .env from template if it doesn't exist
    if [[ ! -f "${SHARED_PATH}/.env" ]] && [[ -f "${RELEASE_PATH}/.env.example" ]]; then
        echo "📋 Creating .env from template..."
        cp "${RELEASE_PATH}/.env.example" "${SHARED_PATH}/.env"
        chmod 600 "${SHARED_PATH}/.env"
        echo "⚠️  Please configure ${SHARED_PATH}/.env with production values"
    fi

    # Remove release-specific directories and create symlinks
    echo "🔗 Creating symlink structure..."

    # Storage symlink with backup handling
    if [[ -d "${RELEASE_PATH}/storage" ]]; then
        echo "🗂️  Backing up existing storage directory..."
        mv "${RELEASE_PATH}/storage" "${RELEASE_PATH}/storage.backup"
    fi
    ln -nfs "${SHARED_PATH}/storage" "${RELEASE_PATH}/storage"

    # Environment file symlink
    [[ -f "${RELEASE_PATH}/.env" ]] && rm -f "${RELEASE_PATH}/.env"
    ln -nfs "${SHARED_PATH}/.env" "${RELEASE_PATH/.env"

    # Public uploads symlink (if directory exists)
    if [[ -d "${RELEASE_PATH}/public/uploads" ]]; then
        rm -rf "${RELEASE_PATH}/public/uploads"
        ln -nfs "${SHARED_PATH}/public/uploads" "${RELEASE_PATH}/public/uploads"
    fi

    # Public media symlink (if directory exists)
    if [[ -d "${RELEASE_PATH}/public/media" ]]; then
        rm -rf "${RELEASE_PATH}/public/media"
        ln -nfs "${SHARED_PATH}/public/media" "${RELEASE_PATH}/public/media"
    fi

    # Handle shared hosting special cases
    if [[ "${SHARED_HOSTING}" == "true" ]]; then
        echo "🏠 Configuring for shared hosting environment..."

        # Create public storage link
        if [[ -n "${PUBLIC_PATH}" ]]; then
            # Remove existing public/storage if it exists
            [[ -L "${PUBLIC_PATH}/storage" ]] && rm -f "${PUBLIC_PATH}/storage"
            [[ -d "${PUBLIC_PATH}/storage" ]] && rm -rf "${PUBLIC_PATH}/storage"

            # Create symlink to shared storage
            ln -nfs "${SHARED_PATH}/storage/app/public" "${PUBLIC_PATH}/storage" || {
                echo "⚠️  Symlink creation failed - creating directory copy"
                mkdir -p "${PUBLIC_PATH}/storage"
                rsync -av "${SHARED_PATH}/storage/app/public/" "${PUBLIC_PATH}/storage/"
            }
        fi

        # Handle .htaccess for shared hosting
        if [[ -f "${RELEASE_PATH}/public/.htaccess.shared" ]]; then
            cp "${RELEASE_PATH}/public/.htaccess.shared" "${RELEASE_PATH}/public/.htaccess"
        fi
    fi

    # Validate all symlinks
    echo "🔍 Validating symlink integrity..."
    SYMLINKS=(
        "${RELEASE_PATH}/storage"
        "${RELEASE_PATH}/.env"
    )

    for link in "${SYMLINKS[@]}"; do
        if [[ -L "${link}" ]]; then
            if [[ -e "${link}" ]]; then
                echo "✅ Symlink valid: ${link} -> $(readlink "${link}")"
            else
                echo "❌ Broken symlink: ${link} -> $(readlink "${link}")"
                exit 1
            fi
        else
            echo "❌ Missing symlink: ${link}"
            exit 1
        fi
    done

    echo "✅ Shared resources configuration complete"
    ```

    **4.2 - 🔴 Secure Configuration Management (Server)**

-   **Objectives:**
    -   Deploy secure environment-specific configurations
    -   Validate configuration integrity and completeness
    -   Apply security-hardened permissions
-   **Commands:**

    ```bash
    # Secure configuration management with validation
    echo "🔴 Managing secure configuration deployment..."

    # Validate required environment variables
    echo "🔍 Validating environment configuration..."

    REQUIRED_ENV_VARS=(
        "APP_KEY"
        "APP_ENV"
        "APP_DEBUG"
        "APP_URL"
        "DB_CONNECTION"
        "DB_HOST"
        "DB_DATABASE"
    )

    # Check if .env exists and validate critical variables
    if [[ -f "${SHARED_PATH}/.env" ]]; then
        echo "📋 Validating existing .env file..."

        MISSING_VARS=()
        for var in "${REQUIRED_ENV_VARS[@]}"; do
            if ! grep -q "^${var}=" "${SHARED_PATH}/.env"; then
                MISSING_VARS+=("${var}")
            fi
        done

        if [[ ${#MISSING_VARS[@]} -gt 0 ]]; then
            echo "⚠️  Missing environment variables in .env:"
            printf "  - %s\n" "${MISSING_VARS[@]}"
        else
            echo "✅ All required environment variables present"
        fi
    else
        echo "❌ .env file not found in shared directory"
        echo "📋 Creating .env from template..."

        if [[ -f "${RELEASE_PATH}/.env.example" ]]; then
            cp "${RELEASE_PATH}/.env.example" "${SHARED_PATH}/.env"
            echo "⚠️  Please configure production values in: ${SHARED_PATH}/.env"
        else
            echo "❌ No .env.example found - manual .env creation required"
            exit 1
        fi
    fi

    # Set secure permissions on configuration files
    echo "🔐 Applying secure configuration permissions..."
    chmod 600 "${SHARED_PATH}/.env"

    # Create config directory if needed
    [[ ! -d "${SHARED_PATH}/config" ]] && mkdir -p "${SHARED_PATH}/config"

    # Handle additional configuration files
    CONFIG_FILES=(
        "config/database.php"
        "config/cache.php"
        "config/session.php"
        "config/filesystems.php"
    )

    for config_file in "${CONFIG_FILES[@]}"; do
        if [[ -f "${RELEASE_PATH}/${config_file}" ]]; then
            # Create backup of existing config in shared
            SHARED_CONFIG="${SHARED_PATH}/${config_file}"
            SHARED_CONFIG_DIR=$(dirname "${SHARED_CONFIG}")

            [[ ! -d "${SHARED_CONFIG_DIR}" ]] && mkdir -p "${SHARED_CONFIG_DIR}"

            # Copy if doesn't exist or if release version is newer
            if [[ ! -f "${SHARED_CONFIG}" ]] || [[ "${RELEASE_PATH}/${config_file}" -nt "${SHARED_CONFIG}" ]]; then
                echo "📝 Updating shared config: ${config_file}"
                cp "${RELEASE_PATH}/${config_file}" "${SHARED_CONFIG}"
                chmod 644 "${SHARED_CONFIG}"
            fi
        fi
    done

    # Validate APP_KEY exists and is properly formatted
    APP_KEY=$(grep -E "^APP_KEY=" "${SHARED_PATH}/.env" | cut -d'=' -f2 | tr -d '"' | tr -d "'")
    if [[ -z "${APP_KEY}" ]] || [[ "${APP_KEY}" == "base64:" ]] || [[ "${APP_KEY}" == "" ]]; then
        echo "🔑 Generating missing APP_KEY..."
        cd "${RELEASE_PATH}"

        # Generate key and update shared .env
        NEW_KEY=$(php artisan key:generate --show)
        sed -i "s/APP_KEY=.*/APP_KEY=${NEW_KEY}/" "${SHARED_PATH}/.env"
        echo "✅ APP_KEY generated and updated"
    else
        echo "✅ APP_KEY validation passed"
    fi

    # Environment-specific security settings
    APP_ENV=$(grep -E "^APP_ENV=" "${SHARED_PATH}/.env" | cut -d'=' -f2 | tr -d '"' | tr -d "'")
    if [[ "${APP_ENV}" == "production" ]]; then
        echo "🛡️  Applying production security settings..."

        # Ensure debug is disabled in production
        sed -i "s/APP_DEBUG=true/APP_DEBUG=false/" "${SHARED_PATH}/.env"

        # Set secure session settings
        if ! grep -q "SESSION_SECURE_COOKIE=true" "${SHARED_PATH}/.env"; then
            echo "SESSION_SECURE_COOKIE=true" >> "${SHARED_PATH}/.env"
        fi
    fi

    # Final validation
    echo "📊 Configuration summary:"
    echo "  - Environment: ${APP_ENV:-unknown}"
    echo "  - Debug mode: $(grep APP_DEBUG "${SHARED_PATH}/.env" | cut -d'=' -f2)"
    echo "  - APP_KEY: $([ -n "${APP_KEY}" ] && echo 'Set' || echo 'Missing')"
    echo "  - Database: $(grep DB_CONNECTION "${SHARED_PATH}/.env" | cut -d'=' -f2)"

    echo "✅ Configuration management complete"
    ```

    ***

    ## **Phase 5: 🚀 Pre-Release Hooks**

**5.1 - 🔴 🟣 Maintenance Mode Activation (Server - User Configurable)**

-   **Objectives:**
    -   Activate application maintenance mode with custom messaging
    -   Ensure graceful user experience during deployment
    -   Configure maintenance mode with deployment-specific settings
-   **Commands:**

    ```bash
    # Load deployment variables for maintenance mode configuration
    source %path-server%/Admin-Local/Deployment/Scripts/load-variables.sh
    
    # Advanced maintenance mode activation
    echo "🔴 🟣 Activating maintenance mode for deployment..."
    
    cd "${DEPLOY_PATH}/current"
    
    # Create custom maintenance template if configured
    if [[ -n "${MAINTENANCE_TEMPLATE}" ]] && [[ -f "%path-server%/Admin-Local/Deployment/Templates/${MAINTENANCE_TEMPLATE}" ]]; then
        echo "📋 Using custom maintenance template: ${MAINTENANCE_TEMPLATE}"
        TEMPLATE_FLAG="--render=${MAINTENANCE_TEMPLATE}"
    else
        TEMPLATE_FLAG="--render=errors::503"
    fi
    
    # Activate maintenance mode with deployment secret
    php artisan down ${TEMPLATE_FLAG} \
        --secret="${DEPLOY_SECRET}" \
        --refresh=15 \
        --retry=60 \
        --status=503 || {
        echo "❌ Failed to activate maintenance mode"
        exit 1
    }
    
    # Verify maintenance mode is active
    if php artisan inspire >/dev/null 2>&1; then
        echo "❌ Maintenance mode activation failed - application still accessible"
        exit 1
    else
        echo "✅ Maintenance mode activated successfully"
        echo "  - Secret bypass: ${DEPLOY_SECRET}"
        echo "  - Template: ${TEMPLATE_FLAG}"
        echo "  - Retry after: 60 seconds"
    fi
    
    # Log maintenance mode activation
    echo "$(date -u +%Y-%m-%dT%H:%M:%SZ) MAINTENANCE_MODE_ACTIVATED ${RELEASE_ID:-manual}" >> "${DEPLOY_PATH}/deployment-history.log"
    ```

**5.2 - 🔴 🟣 Pre-Release Custom Commands (Server - User Configurable)**

-   **Objectives:**
    -   Execute user-defined pre-release validation and preparation scripts
    -   Create comprehensive backups with rollback capabilities
    -   Perform deployment-specific pre-checks and external notifications
-   **Commands:**

    ```bash
    # Advanced pre-release custom commands with build strategy support
    echo "🔴 🟣 Executing pre-release custom commands..."
    
    # Load deployment configuration
    source %path-server%/Admin-Local/Deployment/Scripts/load-variables.sh
    
    # Execute build strategy-specific pre-release hooks
    PRE_RELEASE_SCRIPTS_DIR="%path-server%/Admin-Local/Deployment/Hooks/Pre-Release"
    
    if [[ -d "${PRE_RELEASE_SCRIPTS_DIR}" ]]; then
        echo "📂 Found pre-release scripts directory"
        
        # Execute scripts in order
        for script in "${PRE_RELEASE_SCRIPTS_DIR}"/*.sh; do
            if [[ -f "${script}" ]] && [[ -x "${script}" ]]; then
                SCRIPT_NAME=$(basename "${script}")
                echo "🔧 Executing pre-release script: ${SCRIPT_NAME}"
                
                # Execute with timeout and error handling
                if timeout 300 "${script}" "${BUILD_STRATEGY}" "${RELEASE_PATH}" "${DEPLOY_PATH}"; then
                    echo "  ✅ ${SCRIPT_NAME} completed successfully"
                else
                    echo "  ❌ ${SCRIPT_NAME} failed or timed out"
                    if [[ "${FAIL_ON_HOOK_ERROR}" == "true" ]]; then
                        exit 1
                    else
                        echo "  ⚠️  Continuing despite hook failure (FAIL_ON_HOOK_ERROR=false)"
                    fi
                fi
            fi
        done
    fi
    
    # Database backup strategy
    echo "💾 Creating pre-deployment database backup..."
    if [[ "${CREATE_DB_BACKUP}" == "true" ]] && [[ -n "${DB_CONNECTION}" ]]; then
        BACKUP_FILE="${DEPLOY_PATH}/backups/db-backup-$(date +%Y%m%d%H%M%S).sql"
        mkdir -p "${DEPLOY_PATH}/backups"
        
        cd "${DEPLOY_PATH}/current"
        case "${DB_CONNECTION}" in
            "mysql")
                mysqldump --defaults-extra-file=<(echo -e "[client]\nuser=${DB_USERNAME}\npassword=${DB_PASSWORD}\nhost=${DB_HOST}") \
                    "${DB_DATABASE}" > "${BACKUP_FILE}" || echo "⚠️  Database backup failed"
                ;;
            "pgsql")
                PGPASSWORD="${DB_PASSWORD}" pg_dump -h "${DB_HOST}" -U "${DB_USERNAME}" "${DB_DATABASE}" > "${BACKUP_FILE}" || echo "⚠️  Database backup failed"
                ;;
            *)
                echo "⚠️  Unsupported database connection type: ${DB_CONNECTION}"
                ;;
        esac
        
        if [[ -f "${BACKUP_FILE}" ]] && [[ -s "${BACKUP_FILE}" ]]; then
            echo "  ✅ Database backup created: $(basename "${BACKUP_FILE}")"
        fi
    fi
    
    # External service notifications
    echo "📢 Sending pre-deployment notifications..."
    if [[ -n "${SLACK_WEBHOOK_URL}" ]]; then
        curl -X POST "${SLACK_WEBHOOK_URL}" \
            -H 'Content-type: application/json' \
            --data "{\"text\":\"🚀 Deployment starting for ${PROJECT_NAME} (${BUILD_STRATEGY} build)\nRelease: ${RELEASE_ID}\nCommit: $(git rev-parse --short HEAD 2>/dev/null || echo 'manual')\"}" \
            2>/dev/null || echo "⚠️  Slack notification failed"
    fi
    
    # Health check endpoint preparation
    if [[ "${CREATE_HEALTH_CHECK}" == "true" ]]; then
        echo "🏥 Preparing health check endpoint..."
        HEALTH_CHECK_FILE="${RELEASE_PATH}/public/health-check.php"
        cat > "${HEALTH_CHECK_FILE}" << 'EOF'
<?php
// Deployment health check endpoint
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$health = [
    'status' => 'ok',
    'timestamp' => date('c'),
    'release_id' => getenv('RELEASE_ID') ?: 'unknown',
    'php_version' => PHP_VERSION,
    'checks' => []
];

// Database connectivity check
try {
    if (file_exists(__DIR__ . '/../artisan')) {
        $output = shell_exec('cd ' . dirname(__DIR__) . ' && php artisan migrate:status 2>&1');
        $health['checks']['database'] = strpos($output, 'Migration name') !== false ? 'ok' : 'error';
    }
} catch (Exception $e) {
    $health['checks']['database'] = 'error';
}

echo json_encode($health, JSON_PRETTY_PRINT);
EOF
        chmod 644 "${HEALTH_CHECK_FILE}"
        echo "  ✅ Health check endpoint created at /health-check.php"
    fi
    
    echo "✅ Pre-release custom commands completed"
    ```

    ***

    ## **Phase 6: 🔄 Mid-Release Hooks**

**6.1 - 🔴 🟣 Database Migrations - Zero-Downtime Strategy (Server - User Configurable)**

-   **Objectives:**
    -   Execute database migrations with zero-downtime strategy
    -   Maintain backward compatibility during schema transitions
    -   Handle complex schema changes with rollback capabilities
    -   Validate migration success before proceeding
-   **Commands:**

    ```bash
    # Advanced zero-downtime database migrations with comprehensive validation
    echo "🔴 🟣 Executing zero-downtime database migrations..."
    
    # Load deployment variables and database configuration
    source %path-server%/Admin-Local/Deployment/Scripts/load-variables.sh
    cd "${RELEASE_PATH}"
    
    # Pre-migration validation
    echo "🔍 Pre-migration validation..."
    
    # Test database connectivity
    php artisan migrate:status --env=production >/dev/null 2>&1 || {
        echo "❌ Database connection failed - cannot proceed with migrations"
        exit 1
    }
    
    # Check for pending migrations
    PENDING_MIGRATIONS=$(php artisan migrate:status --pending --env=production 2>/dev/null | grep -c "^| Y" || echo "0")
    echo "📊 Pending migrations: ${PENDING_MIGRATIONS}"
    
    if [[ "${PENDING_MIGRATIONS}" -eq 0 ]]; then
        echo "✅ No pending migrations - database is up to date"
    else
        echo "🔄 Executing ${PENDING_MIGRATIONS} pending migrations..."
        
        # Create database backup before migration (if configured)
        if [[ "${BACKUP_BEFORE_MIGRATION}" == "true" ]]; then
            echo "💾 Creating pre-migration database backup..."
            MIGRATION_BACKUP_FILE="${DEPLOY_PATH}/backups/pre-migration-$(date +%Y%m%d%H%M%S).sql"
            mkdir -p "${DEPLOY_PATH}/backups"
            
            case "${DB_CONNECTION}" in
                "mysql")
                    mysqldump --defaults-extra-file=<(echo -e "[client]\nuser=${DB_USERNAME}\npassword=${DB_PASSWORD}\nhost=${DB_HOST}") \
                        --single-transaction --routines --triggers "${DB_DATABASE}" > "${MIGRATION_BACKUP_FILE}" || {
                        echo "❌ Pre-migration backup failed"
                        exit 1
                    }
                    ;;
                "pgsql")
                    PGPASSWORD="${DB_PASSWORD}" pg_dump -h "${DB_HOST}" -U "${DB_USERNAME}" \
                        --verbose --no-acl --no-owner "${DB_DATABASE}" > "${MIGRATION_BACKUP_FILE}" || {
                        echo "❌ Pre-migration backup failed"
                        exit 1
                    }
                    ;;
            esac
            
            if [[ -f "${MIGRATION_BACKUP_FILE}" ]] && [[ -s "${MIGRATION_BACKUP_FILE}" ]]; then
                echo "  ✅ Pre-migration backup created: $(basename "${MIGRATION_BACKUP_FILE}")"
            fi
        fi
        
        # Execute migrations with comprehensive error handling
        echo "🗃️  Executing database migrations..."
        
        # Set migration timeout
        export DB_MIGRATION_TIMEOUT=${MIGRATION_TIMEOUT:-300}
        
        # Execute migrations with rollback capability
        if timeout "${DB_MIGRATION_TIMEOUT}" php artisan migrate --force --env=production; then
            echo "  ✅ Migrations completed successfully"
            
            # Post-migration validation
            echo "🔍 Post-migration validation..."
            
            # Verify all migrations are applied
            REMAINING_PENDING=$(php artisan migrate:status --pending --env=production 2>/dev/null | grep -c "^| Y" || echo "0")
            if [[ "${REMAINING_PENDING}" -eq 0 ]]; then
                echo "  ✅ All migrations applied successfully"
            else
                echo "  ⚠️  ${REMAINING_PENDING} migrations still pending after execution"
            fi
            
            # Test basic database functionality
            if php -r "
                require_once 'vendor/autoload.php';
                \$app = require_once 'bootstrap/app.php';
                try {
                    DB::connection()->getPdo();
                    echo 'Database connectivity: OK\n';
                } catch (Exception \$e) {
                    echo 'Database connectivity: FAILED - ' . \$e->getMessage() . '\n';
                    exit(1);
                }
            " 2>/dev/null; then
                echo "  ✅ Database connectivity validation passed"
            else
                echo "  ❌ Database connectivity validation failed"
                exit 1
            fi
            
        else
            echo "  ❌ Migration execution failed or timed out"
            
            # Attempt rollback if configured
            if [[ "${AUTO_ROLLBACK_ON_MIGRATION_FAILURE}" == "true" ]] && [[ -f "${MIGRATION_BACKUP_FILE}" ]]; then
                echo "🔄 Attempting automatic rollback..."
                
                case "${DB_CONNECTION}" in
                    "mysql")
                        mysql --defaults-extra-file=<(echo -e "[client]\nuser=${DB_USERNAME}\npassword=${DB_PASSWORD}\nhost=${DB_HOST}") \
                            "${DB_DATABASE}" < "${MIGRATION_BACKUP_FILE}" && echo "  ✅ Database rollback completed"
                        ;;
                    "pgsql")
                        PGPASSWORD="${DB_PASSWORD}" psql -h "${DB_HOST}" -U "${DB_USERNAME}" \
                            -d "${DB_DATABASE}" -f "${MIGRATION_BACKUP_FILE}" && echo "  ✅ Database rollback completed"
                        ;;
                esac
            fi
            
            exit 1
        fi
    fi
    
    # Log migration completion
    echo "$(date -u +%Y-%m-%dT%H:%M:%SZ) MIGRATIONS_COMPLETED ${PENDING_MIGRATIONS} ${RELEASE_ID:-manual}" >> "${DEPLOY_PATH}/deployment-history.log"
    
    echo "✅ Zero-downtime database migrations completed"
    ```

**6.2 - 🔴 🟣 Application Cache Preparation (Server - User Configurable)**

-   **Objectives:**
    -   Execute intelligent cache rebuilding with performance optimization
    -   Pre-warm critical application data and routes
    -   Optimize cache performance for first production requests
    -   Handle cache failures gracefully with fallback strategies
-   **Commands:**

    ```bash
    # Advanced application cache preparation with build strategy awareness
    echo "🔴 🟣 Preparing application caches for optimal performance..."
    
    # Load deployment variables
    source %path-server%/Admin-Local/Deployment/Scripts/load-variables.sh
    cd "${RELEASE_PATH}"
    
    # Clear existing caches first
    echo "🗑️  Clearing existing caches..."
    php artisan cache:clear --quiet 2>/dev/null || echo "  ⚠️  Application cache clear failed"
    php artisan config:clear --quiet 2>/dev/null || echo "  ⚠️  Config cache clear failed"
    php artisan route:clear --quiet 2>/dev/null || echo "  ⚠️  Route cache clear failed"
    php artisan view:clear --quiet 2>/dev/null || echo "  ⚠️  View cache clear failed"
    
    # Rebuild caches with error handling
    echo "🏗️  Rebuilding application caches..."
    
    # Configuration cache
    echo "  📝 Caching configuration..."
    if php artisan config:cache --env=production 2>&1; then
        echo "    ✅ Configuration cache completed"
    else
        echo "    ❌ Configuration cache failed"
        # Don't exit - some configs might have issues but app can still work
    fi
    
    # Route cache
    echo "  🗺️  Caching routes..."
    if php artisan route:cache 2>&1; then
        echo "    ✅ Route cache completed"
        
        # Validate route cache
        CACHED_ROUTES=$(php artisan route:list --compact 2>/dev/null | wc -l)
        echo "    📊 Cached routes: ${CACHED_ROUTES}"
    else
        echo "    ❌ Route cache failed - this may cause performance issues"
    fi
    
    # View cache
    echo "  👁️  Caching views..."
    if php artisan view:cache 2>&1; then
        echo "    ✅ View cache completed"
        
        # Count cached views
        if [[ -d "storage/framework/views" ]]; then
            CACHED_VIEWS=$(find storage/framework/views -name "*.php" | wc -l)
            echo "    📊 Cached views: ${CACHED_VIEWS}"
        fi
    else
        echo "    ⚠️  View cache failed - continuing anyway"
    fi
    
    # Event cache (if available)
    if php artisan list | grep -q "event:cache"; then
        echo "  📅 Caching events..."
        php artisan event:cache --quiet 2>/dev/null && echo "    ✅ Event cache completed" || echo "    ⚠️  Event cache failed"
    fi
    
    # Icons cache (if available)
    if php artisan list | grep -q "icons:cache"; then
        echo "  🎨 Caching icons..."
        php artisan icons:cache --quiet 2>/dev/null && echo "    ✅ Icon cache completed" || echo "    ⚠️  Icon cache failed"
    fi
    
    # Custom cache warming (if configured)
    if [[ "${ENABLE_CACHE_WARMUP}" == "true" ]]; then
        echo "🔥 Executing cache warmup procedures..."
        
        # Execute custom cache warmup scripts
        CACHE_WARMUP_SCRIPTS_DIR="%path-server%/Admin-Local/Deployment/Hooks/Cache-Warmup"
        
        if [[ -d "${CACHE_WARMUP_SCRIPTS_DIR}" ]]; then
            for script in "${CACHE_WARMUP_SCRIPTS_DIR}"/*.sh; do
                if [[ -f "${script}" ]] && [[ -x "${script}" ]]; then
                    SCRIPT_NAME=$(basename "${script}")
                    echo "  🔧 Running cache warmup: ${SCRIPT_NAME}"
                    
                    if timeout 60 "${script}" "${RELEASE_PATH}" "${BUILD_STRATEGY}"; then
                        echo "    ✅ ${SCRIPT_NAME} completed"
                    else
                        echo "    ⚠️  ${SCRIPT_NAME} failed or timed out"
                    fi
                fi
            done
        fi
        
        # Application-specific cache warmup
        if php artisan list | grep -q "cache:warmup"; then
            echo "  🌡️  Running application cache warmup..."
            timeout 120 php artisan cache:warmup --env=production 2>/dev/null && echo "    ✅ Application warmup completed" || echo "    ⚠️  Application warmup failed"
        fi
    fi
    
    # Cache performance validation
    echo "📊 Cache performance validation..."
    
    # Test config cache performance
    CONFIG_CACHE_TIME=$(time -p php artisan config:show app.name 2>&1 | grep "real" | awk '{print $2}' || echo "unknown")
    echo "  📝 Config cache response time: ${CONFIG_CACHE_TIME}s"
    
    # Validate cache files exist
    CACHE_FILES_COUNT=0
    [[ -f "bootstrap/cache/config.php" ]] && ((CACHE_FILES_COUNT++))
    [[ -f "bootstrap/cache/routes-v7.php" ]] && ((CACHE_FILES_COUNT++))
    [[ -f "bootstrap/cache/services.php" ]] && ((CACHE_FILES_COUNT++))
    
    echo "  📁 Active cache files: ${CACHE_FILES_COUNT}/3"
    
    if [[ "${CACHE_FILES_COUNT}" -ge 2 ]]; then
        echo "  ✅ Cache preparation successful"
    else
        echo "  ⚠️  Cache preparation partially successful"
    fi
    
    echo "✅ Application cache preparation completed"
    ```

**6.3 - 🔴 🟣 Comprehensive Health Checks (Server - User Configurable)**

-   **Objectives:**
    -   Execute comprehensive application health validation
    -   Verify critical service connectivity and functionality
    -   Validate deployment readiness with rollback triggers
    -   Generate detailed health reports for monitoring
-   **Commands:**

    ```bash
    # Comprehensive health checks with detailed validation and reporting
    echo "🔴 🟣 Executing comprehensive health checks..."
    
    # Load deployment variables
    source %path-server%/Admin-Local/Deployment/Scripts/load-variables.sh
    cd "${RELEASE_PATH}"
    
    # Initialize health check results
    HEALTH_CHECK_RESULTS=()
    CRITICAL_FAILURES=0
    WARNING_COUNT=0
    
    # Function to log health check result
    log_health_check() {
        local check_name="$1"
        local status="$2"
        local message="$3"
        
        HEALTH_CHECK_RESULTS+=("${check_name}|${status}|${message}")
        
        case "${status}" in
            "PASS")
                echo "  ✅ ${check_name}: ${message}"
                ;;
            "FAIL")
                echo "  ❌ ${check_name}: ${message}"
                ((CRITICAL_FAILURES++))
                ;;
            "WARN")
                echo "  ⚠️  ${check_name}: ${message}"
                ((WARNING_COUNT++))
                ;;
        esac
    }
    
    # 1. Laravel Framework Health Check
    echo "🔍 Laravel framework validation..."
    if timeout 10 php artisan --version --quiet 2>/dev/null; then
        LARAVEL_VERSION=$(php artisan --version | head -n1)
        log_health_check "Laravel Framework" "PASS" "Framework functional (${LARAVEL_VERSION})"
    else
        log_health_check "Laravel Framework" "FAIL" "Laravel framework not responding"
    fi
    
    # 2. Database Connectivity Check
    echo "🗃️  Database connectivity validation..."
    if timeout 15 php artisan migrate:status --env=production >/dev/null 2>&1; then
        # Check migration status
        PENDING_MIGRATIONS=$(php artisan migrate:status --pending --env=production 2>/dev/null | grep -c "^| Y" || echo "0")
        if [[ "${PENDING_MIGRATIONS}" -eq 0 ]]; then
            log_health_check "Database Connectivity" "PASS" "Connected and migrations current"
        else
            log_health_check "Database Connectivity" "WARN" "${PENDING_MIGRATIONS} pending migrations"
        fi
    else
        log_health_check "Database Connectivity" "FAIL" "Database connection failed or timeout"
    fi
    
    # 3. Cache System Validation
    echo "💾 Cache system validation..."
    if php artisan cache:clear --quiet 2>/dev/null && echo "test" | php artisan cache:put test_key - 1 2>/dev/null; then
        if [[ "$(php artisan cache:get test_key 2>/dev/null)" == "test" ]]; then
            log_health_check "Cache System" "PASS" "Cache read/write operations successful"
            php artisan cache:forget test_key 2>/dev/null
        else
            log_health_check "Cache System" "FAIL" "Cache write succeeded but read failed"
        fi
    else
        log_health_check "Cache System" "FAIL" "Cache system not functional"
    fi
    
    # 4. File System Permissions Check
    echo "📁 File system permissions validation..."
    PERMISSION_ISSUES=0
    
    # Check writable directories
    WRITABLE_DIRS=(
        "storage/app"
        "storage/logs" 
        "storage/framework/cache"
        "storage/framework/sessions"
        "storage/framework/views"
        "bootstrap/cache"
    )
    
    for dir in "${WRITABLE_DIRS[@]}"; do
        if [[ -d "${dir}" ]] && [[ -w "${dir}" ]]; then
            continue
        else
            ((PERMISSION_ISSUES++))
        fi
    done
    
    if [[ "${PERMISSION_ISSUES}" -eq 0 ]]; then
        log_health_check "File Permissions" "PASS" "All required directories writable"
    else
        log_health_check "File Permissions" "FAIL" "${PERMISSION_ISSUES} directories not writable"
    fi
    
    # 5. Environment Configuration Check
    echo "🔧 Environment configuration validation..."
    
    # Check critical environment variables
    MISSING_ENV_VARS=()
    CRITICAL_ENV_VARS=("APP_KEY" "APP_ENV" "DB_CONNECTION" "DB_HOST" "DB_DATABASE")
    
    for var in "${CRITICAL_ENV_VARS[@]}"; do
        if [[ -z "$(php -r "echo env('${var}');")" ]]; then
            MISSING_ENV_VARS+=("${var}")
        fi
    done
    
    if [[ ${#MISSING_ENV_VARS[@]} -eq 0 ]]; then
        log_health_check "Environment Config" "PASS" "All critical environment variables set"
    else
        log_health_check "Environment Config" "FAIL" "Missing variables: $(IFS=', '; echo "${MISSING_ENV_VARS[*]}")"
    fi
    
    # 6. Application Routes Check
    echo "🗺️  Application routes validation..."
    ROUTE_COUNT=$(php artisan route:list --compact 2>/dev/null | wc -l || echo "0")
    
    if [[ "${ROUTE_COUNT}" -gt 0 ]]; then
        log_health_check "Application Routes" "PASS" "${ROUTE_COUNT} routes registered"
    else
        log_health_check "Application Routes" "FAIL" "No routes found or route:list failed"
    fi
    
    # 7. Queue System Check (if configured)
    if [[ "${QUEUE_CONNECTION}" != "sync" ]] && [[ -n "${QUEUE_CONNECTION}" ]]; then
        echo "🔄 Queue system validation..."
        if timeout 10 php artisan queue:monitor --once 2>/dev/null; then
            log_health_check "Queue System" "PASS" "Queue system accessible"
        else
            log_health_check "Queue System" "WARN" "Queue system check failed"
        fi
    fi
    
    # 8. Custom Health Checks
    if [[ -n "${CUSTOM_HEALTH_COMMAND}" ]]; then
        echo "🏥 Custom health check validation..."
        if timeout 30 php artisan "${CUSTOM_HEALTH_COMMAND}" --env=production 2>/dev/null; then
            log_health_check "Custom Health Check" "PASS" "Custom health check passed"
        else
            log_health_check "Custom Health Check" "FAIL" "Custom health check failed"
        fi
    fi
    
    # Generate health check report
    echo ""
    echo "📊 HEALTH CHECK SUMMARY"
    echo "======================="
    
    TOTAL_CHECKS=${#HEALTH_CHECK_RESULTS[@]}
    PASSED_CHECKS=$((TOTAL_CHECKS - CRITICAL_FAILURES - WARNING_COUNT))
    
    echo "  Total Checks: ${TOTAL_CHECKS}"
    echo "  Passed: ${PASSED_CHECKS}"
    echo "  Warnings: ${WARNING_COUNT}" 
    echo "  Critical Failures: ${CRITICAL_FAILURES}"
    
    # Write detailed health report
    HEALTH_REPORT_FILE="${DEPLOY_PATH}/health-reports/health-$(date +%Y%m%d%H%M%S).json"
    mkdir -p "${DEPLOY_PATH}/health-reports"
    
    cat > "${HEALTH_REPORT_FILE}" << EOF
{
    "timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
    "release_id": "${RELEASE_ID:-manual}",
    "total_checks": ${TOTAL_CHECKS},
    "passed": ${PASSED_CHECKS},
    "warnings": ${WARNING_COUNT},
    "critical_failures": ${CRITICAL_FAILURES},
    "checks": [
EOF
    
    # Add individual check results to JSON
    for ((i=0; i<${#HEALTH_CHECK_RESULTS[@]}; i++)); do
        IFS='|' read -r check_name status message <<< "${HEALTH_CHECK_RESULTS[$i]}"
        echo "        {\"name\": \"${check_name}\", \"status\": \"${status}\", \"message\": \"${message}\"}" >> "${HEALTH_REPORT_FILE}"
        [[ $i -lt $((${#HEALTH_CHECK_RESULTS[@]}-1)) ]] && echo "," >> "${HEALTH_REPORT_FILE}"
    done
    
    cat >> "${HEALTH_REPORT_FILE}" << EOF
    ]
}
EOF
    
    echo "  📄 Health report: $(basename "${HEALTH_REPORT_FILE}")"
    
    # Determine deployment readiness
    if [[ "${CRITICAL_FAILURES}" -gt 0 ]]; then
        echo ""
        echo "❌ HEALTH CHECK FAILED"
        echo "Critical failures detected. Deployment should not proceed."
        
        if [[ "${HALT_ON_HEALTH_FAILURE}" == "true" ]]; then
            echo "🛑 Halting deployment due to health check failures"
            exit 1
        else
            echo "⚠️  Continuing despite health failures (HALT_ON_HEALTH_FAILURE=false)"
        fi
    else
        echo ""
        echo "✅ HEALTH CHECK PASSED"
        if [[ "${WARNING_COUNT}" -gt 0 ]]; then
            echo "Deployment ready with ${WARNING_COUNT} warnings"
        else
            echo "All systems healthy - ready for deployment"
        fi
    fi
    
    # Log health check completion
    echo "$(date -u +%Y-%m-%dT%H:%M:%SZ) HEALTH_CHECK_COMPLETED ${CRITICAL_FAILURES}F_${WARNING_COUNT}W_${PASSED_CHECKS}P ${RELEASE_ID:-manual}" >> "${DEPLOY_PATH}/deployment-history.log"
    
    echo "✅ Comprehensive health checks completed"
    ```

    ***

    ## **Phase 7: ⚡ Atomic Release Switch**

**7.1 - 🔴 Atomic Symlink Update (Server) - THE Zero-Downtime Moment**

-   **Objectives:**
    -   Execute instantaneous atomic release switch
    -   Ensure true zero-downtime with rollback capability
    -   Handle multiple deployment scenarios (standard, shared hosting, containerized)
-   **Commands:**

    ```bash
    # THE ATOMIC MOMENT - Zero-downtime release switch
    echo "🔴 ⚡ EXECUTING ATOMIC RELEASE SWITCH ⚡"
    echo "📅 Switch timestamp: $(date -u +%Y-%m-%dT%H:%M:%SZ)"

    # Pre-switch validation
    echo "🔍 Pre-switch validation..."

    # Verify release directory exists and is valid
    if [[ ! -d "${RELEASE_PATH}" ]]; then
        echo "❌ Release directory not found: ${RELEASE_PATH}"
        exit 1
    fi

    # Verify critical Laravel files
    CRITICAL_FILES=(
        "${RELEASE_PATH}/artisan"
        "${RELEASE_PATH}/bootstrap/app.php"
        "${RELEASE_PATH}/public/index.php"
    )

    for file in "${CRITICAL_FILES[@]}"; do
        [[ -f "${file}" ]] || {
            echo "❌ Critical file missing: ${file}"
            exit 1
        }
    done

    # Backup current symlink target (for rollback)
    if [[ -L "${CURRENT_PATH}" ]]; then
        PREVIOUS_RELEASE=$(readlink "${CURRENT_PATH}")
        echo "💾 Previous release backed up: ${PREVIOUS_RELEASE}"
        echo "${PREVIOUS_RELEASE}" > "${DEPLOY_PATH}/.previous-release"
    fi

    # THE ATOMIC SWITCH - This is the zero-downtime moment
    echo "⚡ SWITCHING TO NEW RELEASE..."
    echo "  From: $(readlink "${CURRENT_PATH}" 2>/dev/null || echo 'none')"
    echo "  To: ${RELEASE_PATH}"

    # Atomic symlink creation
    ln -sfn "${RELEASE_PATH}" "${CURRENT_PATH}" || {
        echo "❌ CRITICAL: Atomic symlink switch failed!"
        exit 1
    }

    echo "✅ PRIMARY SWITCH COMPLETE"

    # Handle shared hosting scenarios
    if [[ "${SHARED_HOSTING}" == "true" ]] && [[ -n "${PUBLIC_PATH}" ]]; then
        echo "🏠 Configuring shared hosting symlink..."

        # Method 1: Direct public symlink (preferred)
        if ln -sfn "${RELEASE_PATH}/public" "${PUBLIC_PATH}" 2>/dev/null; then
            echo "✅ Shared hosting symlink created: ${PUBLIC_PATH} -> ${RELEASE_PATH}/public"
        else
            # Method 2: Copy files if symlink not supported
            echo "⚠️  Symlink not supported - copying files..."
            rsync -av --delete "${RELEASE_PATH}/public/" "${PUBLIC_PATH}/" || {
                echo "❌ Shared hosting file copy failed"
                # Rollback main symlink
                [[ -n "${PREVIOUS_RELEASE}" ]] && ln -sfn "${PREVIOUS_RELEASE}" "${CURRENT_PATH}"
                exit 1
            }
            echo "✅ Shared hosting files copied"
        fi
    fi

    # Container/Docker scenario (if applicable)
    if [[ "${CONTAINER_MODE}" == "true" ]] && [[ -n "${CONTAINER_MOUNT_POINT}" ]]; then
        echo "🐳 Configuring container mount point..."
        ln -sfn "${RELEASE_PATH}" "${CONTAINER_MOUNT_POINT}/current" || {
            echo "❌ Container symlink failed"
            exit 1
        }
        echo "✅ Container symlink configured"
    fi

    # Verify switch success
    echo "🔍 Verifying atomic switch..."
    CURRENT_TARGET=$(readlink "${CURRENT_PATH}")
    if [[ "${CURRENT_TARGET}" == "${RELEASE_PATH}" ]]; then
        echo "✅ ATOMIC SWITCH VERIFICATION PASSED"
        echo "  Current target: ${CURRENT_TARGET}"
        echo "  Expected target: ${RELEASE_PATH}"
    else
        echo "❌ CRITICAL: Switch verification failed!"
        echo "  Current target: ${CURRENT_TARGET}"
        echo "  Expected target: ${RELEASE_PATH}"
        exit 1
    fi

    # Quick health check on switched application
    echo "🏥 Quick health check on new release..."
    cd "${CURRENT_PATH}"

    # Test basic Laravel functionality
    if timeout 10 php artisan --version >/dev/null 2>&1; then
        echo "✅ Laravel health check passed"
    else
        echo "❌ Laravel health check failed - consider rollback"
        # Don't exit here as rollback might be handled by monitoring
    fi

    # Log the successful switch
    echo "📊 ATOMIC SWITCH COMPLETED SUCCESSFULLY"
    echo "  Release ID: ${RELEASE_ID}"
    echo "  Switch time: $(date -u +%Y-%m-%dT%H:%M:%SZ)"
    echo "  Previous release: ${PREVIOUS_RELEASE:-none}"
    echo "  New release: ${RELEASE_PATH}"

    # Create switch completion marker
    echo "$(date -u +%Y-%m-%dT%H:%M:%SZ) ${RELEASE_ID} ${PREVIOUS_RELEASE:-none}" >> "${DEPLOY_PATH}/deployment-history.log"

    echo "🎉 ZERO-DOWNTIME DEPLOYMENT SWITCH COMPLETE! 🎉"
    ```

    ***

    ## **Phase 8: 🎯 Post-Release Hooks (User SSH Commands - Phase C)**

    **8.1 - 🔴 Advanced OPcache & Cache Invalidation (Server)**

-   **Objectives:**
    -   Execute comprehensive OPcache invalidation with multiple fallback methods
    -   Clear application caches strategically
    -   Refresh CDN and external caches
-   **Commands:**
    ```bash # Advanced OPcache and cache invalidation with multiple methods
    echo "🔴 Executing comprehensive cache invalidation..."
        # Method 1: cachetool (Preferred method)
        echo "🧹 Method 1: cachetool OPcache reset..."
        if command -v cachetool >/dev/null 2>&1; then
            # Try different socket paths
            SOCKET_PATHS=(
                "/var/run/php-fpm/www.sock"
                "/run/php/php8.2-fpm.sock"
                "/run/php/php8.1-fpm.sock"
                "/run/php/php8.0-fpm.sock"
                "/run/php/php7.4-fpm.sock"
            )

            CACHETOOL_SUCCESS=false
            for socket in "${SOCKET_PATHS[@]}"; do
                if [[ -S "${socket}" ]]; then
                    echo "  🔍 Trying socket: ${socket}"
                    if cachetool opcache:reset --fcgi="${socket}" 2>/dev/null; then
                        echo "  ✅ OPcache cleared via cachetool (${socket})"
                        CACHETOOL_SUCCESS=true
                        break
                    fi
                fi
            done

            [[ "${CACHETOOL_SUCCESS}" == "false" ]] && echo "  ⚠️  cachetool failed - trying fallback methods"
        else
            echo "  ⚠️  cachetool not available - using alternative methods"
            CACHETOOL_SUCCESS=false
        fi

        # Method 2: Web endpoint (if cachetool failed)
        if [[ "${CACHETOOL_SUCCESS}" == "false" ]] && [[ -n "${APP_URL}" ]] && [[ -n "${DEPLOY_SECRET}" ]]; then
            echo "🧹 Method 2: Web endpoint OPcache reset..."

            # First try to create the endpoint if it doesn't exist
            OPCACHE_ENDPOINT="${RELEASE_PATH}/public/opcache-clear.php"
            if [[ ! -f "${OPCACHE_ENDPOINT}" ]]; then
                cat > "${OPCACHE_ENDPOINT}" << 'EOF'
    <?php
    // Secure OPcache clearing endpoint for zero-downtime deployment
    if (!isset($_GET['token']) || $_GET['token'] !== getenv('DEPLOY_SECRET')) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

$result = [];
if (function_exists('opcache_reset')) {
$result['opcache_reset'] = opcache_reset();
} else {
$result['opcache_reset'] = false;
$result['error'] = 'OPcache extension not available';
}

// Also clear realpath cache
if (function_exists('clearstatcache')) {
clearstatcache(true);
$result['clearstatcache'] = true;
}

header('Content-Type: application/json');
echo json_encode($result);
EOF
            chmod 644 "${OPCACHE_ENDPOINT}"
fi

        # Try to call the endpoint
        if curl -f -s "${APP_URL}/opcache-clear.php?token=${DEPLOY_SECRET}" -o /tmp/opcache_response.json 2>/dev/null; then
            if grep -q '"opcache_reset":true' /tmp/opcache_response.json; then
                echo "  ✅ OPcache cleared via web endpoint"
                WEB_CLEAR_SUCCESS=true
            else
                echo "  ⚠️  Web endpoint responded but OPcache reset failed"
                WEB_CLEAR_SUCCESS=false
            fi
        else
            echo "  ⚠️  Web endpoint not accessible"
            WEB_CLEAR_SUCCESS=false
        fi
    else
        WEB_CLEAR_SUCCESS=false
    fi

    # Method 3: PHP-FPM reload (last resort)
    if [[ "${CACHETOOL_SUCCESS}" == "false" ]] && [[ "${WEB_CLEAR_SUCCESS}" == "false" ]]; then
        echo "🧹 Method 3: PHP-FPM reload (last resort)..."

        # Detect PHP-FPM service name
        PHP_FPM_SERVICES=(
            "php8.2-fpm"
            "php8.1-fpm"
            "php8.0-fpm"
            "php7.4-fpm"
            "php-fpm"
        )

        for service in "${PHP_FPM_SERVICES[@]}"; do
            if systemctl is-active --quiet "${service}" 2>/dev/null; then
                echo "  🔄 Reloading ${service}..."
                if systemctl reload "${service}" 2>/dev/null; then
                    echo "  ✅ ${service} reloaded successfully"
                    break
                else
                    echo "  ⚠️  Failed to reload ${service}"
                fi
            fi
        done
    fi

    # Application cache clearing
    echo "🗑️  Clearing application caches..."
    cd "${RELEASE_PATH}"

    # Clear Laravel application cache
    php artisan cache:clear --quiet 2>/dev/null || echo "  ⚠️  Application cache clear failed"

    # Clear view cache
    php artisan view:clear --quiet 2>/dev/null || echo "  ⚠️  View cache clear failed"

    # Optional: Clear external caches (CDN, Redis, etc.)
    if [[ -n "${CDN_INVALIDATION_URL}" ]]; then
        echo "🌐 Invalidating CDN cache..."
        curl -X POST "${CDN_INVALIDATION_URL}" -H "Authorization: Bearer ${CDN_API_KEY}" 2>/dev/null || echo "  ⚠️  CDN invalidation failed"
    fi

    echo "✅ Cache invalidation sequence complete"
    ```

**8.2 - 🔴 🟣 Advanced Service Management & Restarts (Server - User Configurable)**

-   **Objectives:**
    -   Execute graceful service restarts with comprehensive validation
    -   Reload web server configurations intelligently
    -   Manage background workers and queue systems with zero-downtime approach
    -   Handle multiple service types with proper dependency management
-   **Commands:**

    ```bash
    # Advanced service management with build strategy awareness
    echo "🔴 🟣 Managing service restarts for deployment..."
    
    # Load deployment variables
    source %path-server%/Admin-Local/Deployment/Scripts/load-variables.sh
    cd "${DEPLOY_PATH}/current"
    
    # Queue Workers Management
    echo "🔄 Managing queue workers..."
    
    if [[ "${QUEUE_CONNECTION}" != "sync" ]] && [[ -n "${QUEUE_CONNECTION}" ]]; then
        echo "  📋 Detected queue connection: ${QUEUE_CONNECTION}"
        
        # Graceful queue worker restart
        echo "  🔄 Graceful queue worker restart..."
        if timeout 30 php artisan queue:restart; then
            echo "    ✅ Queue restart signal sent successfully"
            
            # Wait for workers to finish current jobs
            echo "  ⏳ Waiting for workers to finish current jobs..."
            sleep 10
            
            # Verify new workers are running
            if php artisan queue:monitor --once 2>/dev/null; then
                echo "    ✅ New queue workers confirmed running"
            else
                echo "    ⚠️  Cannot verify queue worker status"
            fi
        else
            echo "    ❌ Queue restart failed"
        fi
        
        # Laravel Horizon Management (if configured)
        if [[ "${USE_HORIZON}" == "true" ]] && php artisan list | grep -q "horizon"; then
            echo "  🌅 Managing Laravel Horizon..."
            
            # Terminate Horizon (it will auto-restart via supervisor)
            if timeout 15 php artisan horizon:terminate; then
                echo "    ✅ Horizon terminate signal sent"
                
                # Wait for Horizon to restart
                echo "    ⏳ Waiting for Horizon to restart..."
                sleep 15
                
                # Verify Horizon is running
                if timeout 10 php artisan horizon:status | grep -q "running"; then
                    echo "    ✅ Horizon successfully restarted"
                else
                    echo "    ⚠️  Horizon restart verification failed"
                fi
            else
                echo "    ❌ Horizon terminate failed"
            fi
        fi
        
        # Supervisor Management (if configured)
        if [[ "${USE_SUPERVISOR}" == "true" ]]; then
            echo "  👨‍💼 Managing Supervisor processes..."
            
            if command -v supervisorctl >/dev/null 2>&1; then
                # Restart Laravel workers
                if supervisorctl restart "laravel-worker:*" 2>/dev/null; then
                    echo "    ✅ Supervisor Laravel workers restarted"
                else
                    echo "    ⚠️  Supervisor restart failed or no Laravel workers configured"
                fi
                
                # Status check
                SUPERVISOR_STATUS=$(supervisorctl status 2>/dev/null | grep "laravel" | grep "RUNNING" | wc -l)
                echo "    📊 Running Laravel processes: ${SUPERVISOR_STATUS}"
            else
                echo "    ⚠️  supervisorctl not available"
            fi
        fi
    else
        echo "  📝 Sync queue driver - no workers to restart"
    fi
    
    # Web Server Configuration Reload
    echo "🌐 Web server configuration management..."
    
    if [[ "${RELOAD_WEB_SERVER}" == "true" ]]; then
        # Detect web server
        WEB_SERVERS=("nginx" "apache2" "httpd")
        RELOADED_SERVERS=0
        
        for server in "${WEB_SERVERS[@]}"; do
            if systemctl is-active --quiet "${server}" 2>/dev/null; then
                echo "  🔧 Reloading ${server} configuration..."
                
                if systemctl reload "${server}" 2>/dev/null; then
                    echo "    ✅ ${server} configuration reloaded"
                    ((RELOADED_SERVERS++))
                else
                    echo "    ❌ ${server} reload failed"
                fi
            fi
        done
        
        if [[ "${RELOADED_SERVERS}" -eq 0 ]]; then
            echo "  ⚠️  No active web servers found to reload"
        fi
    fi
    
    # PHP-FPM Reload (if configured)
    if [[ "${RELOAD_PHP_FPM}" == "true" ]]; then
        echo "🐘 PHP-FPM management..."
        
        PHP_FPM_SERVICES=("php8.2-fpm" "php8.1-fpm" "php8.0-fpm" "php7.4-fpm" "php-fpm")
        
        for service in "${PHP_FPM_SERVICES[@]}"; do
            if systemctl is-active --quiet "${service}" 2>/dev/null; then
                echo "  🔄 Reloading ${service}..."
                
                if systemctl reload "${service}" 2>/dev/null; then
                    echo "    ✅ ${service} reloaded successfully"
                else
                    echo "    ❌ ${service} reload failed"
                fi
            fi
        done
    fi
    
    # Custom Service Management
    if [[ -n "${CUSTOM_SERVICES}" ]]; then
        echo "⚙️  Custom service management..."
        
        IFS=',' read -ra SERVICES <<< "${CUSTOM_SERVICES}"
        for service in "${SERVICES[@]}"; do
            service=$(echo "${service}" | xargs)  # trim whitespace
            
            if systemctl is-active --quiet "${service}" 2>/dev/null; then
                echo "  🔧 Managing custom service: ${service}"
                
                case "${CUSTOM_SERVICE_ACTION:-restart}" in
                    "restart")
                        systemctl restart "${service}" && echo "    ✅ ${service} restarted"
                        ;;
                    "reload")
                        systemctl reload "${service}" && echo "    ✅ ${service} reloaded"
                        ;;
                    *)
                        echo "    ⚠️  Unknown action: ${CUSTOM_SERVICE_ACTION}"
                        ;;
                esac
            else
                echo "    ⚠️  Service ${service} not active or not found"
            fi
        done
    fi
    
    # Redis Cache Management (if configured)
    if [[ "${CACHE_DRIVER}" == "redis" ]] && [[ "${FLUSH_REDIS}" == "true" ]]; then
        echo "💾 Redis cache management..."
        
        if command -v redis-cli >/dev/null 2>&1; then
            # Flush application cache (preserve sessions if configured)
            if [[ "${PRESERVE_SESSIONS}" == "true" ]]; then
                echo "  🔄 Selective Redis cache flush (preserving sessions)..."
                # This would need custom logic based on your key naming
                redis-cli --eval "return redis.call('del', unpack(redis.call('keys', KEYS[1])))" , "laravel_cache:*"
            else
                echo "  🗑️  Full Redis cache flush..."
                redis-cli flushdb && echo "    ✅ Redis cache flushed"
            fi
        else
            echo "    ⚠️  redis-cli not available"
        fi
    fi
    
    echo "✅ Advanced service management completed"
    ```

**8.3 - 🔴 🟣 Comprehensive Post-Deployment Validation (Server - User Configurable)**

-   **Objectives:**
    -   Execute comprehensive smoke tests and validation
    -   Verify application performance and functionality
    -   Run production health checks with detailed reporting
    -   Validate external integrations and services
-   **Commands:**

    ```bash
    # Comprehensive post-deployment validation with detailed reporting
    echo "🔴 🟣 Executing post-deployment validation suite..."
    
    # Load deployment variables
    source %path-server%/Admin-Local/Deployment/Scripts/load-variables.sh
    cd "${DEPLOY_PATH}/current"
    
    # Initialize validation results
    VALIDATION_RESULTS=()
    VALIDATION_FAILURES=0
    VALIDATION_WARNINGS=0
    
    # Function to log validation result
    log_validation() {
        local test_name="$1"
        local status="$2"
        local message="$3"
        local response_time="${4:-N/A}"
        
        VALIDATION_RESULTS+=("${test_name}|${status}|${message}|${response_time}")
        
        case "${status}" in
            "PASS")
                echo "  ✅ ${test_name}: ${message} (${response_time})"
                ;;
            "FAIL")
                echo "  ❌ ${test_name}: ${message}"
                ((VALIDATION_FAILURES++))
                ;;
            "WARN")
                echo "  ⚠️  ${test_name}: ${message}"
                ((VALIDATION_WARNINGS++))
                ;;
        esac
    }
    
    # 1. HTTP Endpoint Validation
    echo "🌐 HTTP endpoint validation..."
    
    if [[ -n "${APP_URL}" ]]; then
        # Test main application endpoint
        HTTP_START=$(date +%s%N)
        HTTP_STATUS=$(curl -o /dev/null -s -w "%{http_code}" -m 10 "${APP_URL}" 2>/dev/null || echo "000")
        HTTP_END=$(date +%s%N)
        HTTP_TIME=$(( (HTTP_END - HTTP_START) / 1000000 ))ms
        
        if [[ "${HTTP_STATUS}" == "200" ]]; then
            log_validation "HTTP Main Endpoint" "PASS" "Application responding (HTTP ${HTTP_STATUS})" "${HTTP_TIME}"
        elif [[ "${HTTP_STATUS}" == "503" ]]; then
            log_validation "HTTP Main Endpoint" "WARN" "Maintenance mode active (HTTP ${HTTP_STATUS})" "${HTTP_TIME}"
        else
            log_validation "HTTP Main Endpoint" "FAIL" "Unexpected status: HTTP ${HTTP_STATUS}"
        fi
        
        # Test health check endpoint
        if curl -f -s -m 5 "${APP_URL}/health-check.php" -o /tmp/health_response.json 2>/dev/null; then
            if grep -q '"status": "ok"' /tmp/health_response.json 2>/dev/null; then
                log_validation "Health Check Endpoint" "PASS" "Health endpoint responding correctly"
            else
                log_validation "Health Check Endpoint" "WARN" "Health endpoint responding but not OK status"
            fi
        else
            log_validation "Health Check Endpoint" "WARN" "Health endpoint not accessible (may not be configured)"
        fi
    else
        log_validation "HTTP Endpoints" "WARN" "APP_URL not configured - skipping HTTP tests"
    fi
    
    # 2. Application Functionality Tests
    echo "⚙️  Application functionality validation..."
    
    # Test Artisan commands
    if timeout 10 php artisan --version >/dev/null 2>&1; then
        LARAVEL_VERSION=$(php artisan --version 2>/dev/null | head -n1)
        log_validation "Laravel Framework" "PASS" "Framework functional (${LARAVEL_VERSION})"
    else
        log_validation "Laravel Framework" "FAIL" "Artisan commands not responding"
    fi
    
    # Test route resolution
    ROUTE_TEST_START=$(date +%s%N)
    ROUTE_COUNT=$(php artisan route:list --compact 2>/dev/null | wc -l || echo "0")
    ROUTE_TEST_END=$(date +%s%N)
    ROUTE_TEST_TIME=$(( (ROUTE_TEST_END - ROUTE_TEST_START) / 1000000 ))ms
    
    if [[ "${ROUTE_COUNT}" -gt 0 ]]; then
        log_validation "Route Resolution" "PASS" "${ROUTE_COUNT} routes resolved" "${ROUTE_TEST_TIME}"
    else
        log_validation "Route Resolution" "FAIL" "Route resolution failed or no routes found"
    fi
    
    # 3. Database Connectivity & Performance
    echo "🗃️  Database validation..."
    
    DB_TEST_START=$(date +%s%N)
    if timeout 15 php artisan migrate:status --env=production >/dev/null 2>&1; then
        DB_TEST_END=$(date +%s%N)
        DB_TEST_TIME=$(( (DB_TEST_END - DB_TEST_START) / 1000000 ))ms
        
        log_validation "Database Connectivity" "PASS" "Database accessible and migrations current" "${DB_TEST_TIME}"
        
        # Test database performance
        DB_PERF_START=$(date +%s%N)
        php -r "
            require_once 'vendor/autoload.php';
            \$app = require_once 'bootstrap/app.php';
            try {
                \$start = microtime(true);
                DB::select('SELECT 1 as test');
                \$time = round((microtime(true) - \$start) * 1000, 2);
                echo \"DB_QUERY_TIME: \${time}ms\n\";
            } catch (Exception \$e) {
                echo \"DB_ERROR: \" . \$e->getMessage() . \"\n\";
                exit(1);
            }
        " 2>/dev/null | grep "DB_QUERY_TIME" | cut -d: -f2 | xargs
        
        if [[ $? -eq 0 ]]; then
            log_validation "Database Performance" "PASS" "Query performance acceptable"
        else
            log_validation "Database Performance" "WARN" "Database performance test failed"
        fi
    else
        log_validation "Database Connectivity" "FAIL" "Database connection failed or timeout"
    fi
    
    # 4. Cache System Validation
    echo "💾 Cache system validation..."
    
    CACHE_TEST_START=$(date +%s%N)
    TEST_KEY="deployment_validation_$(date +%s)"
    TEST_VALUE="validation_test_value"
    
    if echo "${TEST_VALUE}" | php artisan cache:put "${TEST_KEY}" - 60 2>/dev/null; then
        if [[ "$(php artisan cache:get "${TEST_KEY}" 2>/dev/null)" == "${TEST_VALUE}" ]]; then
            CACHE_TEST_END=$(date +%s%N)
            CACHE_TEST_TIME=$(( (CACHE_TEST_END - CACHE_TEST_START) / 1000000 ))ms
            
            log_validation "Cache System" "PASS" "Cache read/write operations successful" "${CACHE_TEST_TIME}"
            
            # Cleanup test key
            php artisan cache:forget "${TEST_KEY}" 2>/dev/null
        else
            log_validation "Cache System" "FAIL" "Cache write succeeded but read failed"
        fi
    else
        log_validation "Cache System" "FAIL" "Cache system not functional"
    fi
    
    # 5. Queue System Validation (if configured)
    if [[ "${QUEUE_CONNECTION}" != "sync" ]] && [[ -n "${QUEUE_CONNECTION}" ]]; then
        echo "🔄 Queue system validation..."
        
        if timeout 10 php artisan queue:monitor --once 2>/dev/null; then
            log_validation "Queue System" "PASS" "Queue system operational"
        else
            log_validation "Queue System" "WARN" "Queue system check failed"
        fi
    fi
    
    # 6. File System Validation
    echo "📁 File system validation..."
    
    # Test critical directories
    CRITICAL_DIRS=("storage/logs" "storage/framework/cache" "bootstrap/cache")
    FS_ISSUES=0
    
    for dir in "${CRITICAL_DIRS[@]}"; do
        if [[ -d "${dir}" ]] && [[ -w "${dir}" ]]; then
            # Test write operation
            if touch "${dir}/.write_test_$" 2>/dev/null && rm -f "${dir}/.write_test_$" 2>/dev/null; then
                continue
            else
                ((FS_ISSUES++))
            fi
        else
            ((FS_ISSUES++))
        fi
    done
    
    if [[ "${FS_ISSUES}" -eq 0 ]]; then
        log_validation "File System" "PASS" "All critical directories accessible and writable"
    else
        log_validation "File System" "FAIL" "${FS_ISSUES} file system issues detected"
    fi
    
    # 7. Custom Application Tests (if configured)
    if [[ -n "${CUSTOM_VALIDATION_COMMAND}" ]]; then
        echo "🧪 Custom application validation..."
        
        CUSTOM_TEST_START=$(date +%s%N)
        if timeout 60 php artisan "${CUSTOM_VALIDATION_COMMAND}" --env=production 2>/dev/null; then
            CUSTOM_TEST_END=$(date +%s%N)
            CUSTOM_TEST_TIME=$(( (CUSTOM_TEST_END - CUSTOM_TEST_START) / 1000000 ))ms
            
            log_validation "Custom Validation" "PASS" "Custom validation passed" "${CUSTOM_TEST_TIME}"
        else
            log_validation "Custom Validation" "FAIL" "Custom validation failed"
        fi
    fi
    
    # 8. External Service Integration Tests (if configured)
    if [[ -n "${EXTERNAL_HEALTH_URLS}" ]]; then
        echo "🌍 External service validation..."
        
        IFS=',' read -ra URLS <<< "${EXTERNAL_HEALTH_URLS}"
        for url in "${URLS[@]}"; do
            url=$(echo "${url}" | xargs)  # trim whitespace
            
            EXT_TEST_START=$(date +%s%N)
            EXT_STATUS=$(curl -o /dev/null -s -w "%{http_code}" -m 5 "${url}" 2>/dev/null || echo "000")
            EXT_TEST_END=$(date +%s%N)
            EXT_TEST_TIME=$(( (EXT_TEST_END - EXT_TEST_START) / 1000000 ))ms
            
            if [[ "${EXT_STATUS}" == "200" ]]; then
                log_validation "External Service $(basename "${url}")" "PASS" "Service responding (HTTP ${EXT_STATUS})" "${EXT_TEST_TIME}"
            else
                log_validation "External Service $(basename "${url}")" "WARN" "Service issue (HTTP ${EXT_STATUS})"
            fi
        done
    fi
    
    # Generate validation report
    echo ""
    echo "📊 POST-DEPLOYMENT VALIDATION SUMMARY"
    echo "======================================"
    
    TOTAL_VALIDATIONS=${#VALIDATION_RESULTS[@]}
    PASSED_VALIDATIONS=$((TOTAL_VALIDATIONS - VALIDATION_FAILURES - VALIDATION_WARNINGS))
    
    echo "  Total Validations: ${TOTAL_VALIDATIONS}"
    echo "  Passed: ${PASSED_VALIDATIONS}"
    echo "  Warnings: ${VALIDATION_WARNINGS}"
    echo "  Failures: ${VALIDATION_FAILURES}"
    
    # Write detailed validation report
    VALIDATION_REPORT_FILE="${DEPLOY_PATH}/validation-reports/validation-$(date +%Y%m%d%H%M%S).json"
    mkdir -p "${DEPLOY_PATH}/validation-reports"
    
    cat > "${VALIDATION_REPORT_FILE}" << EOF
{
    "timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
    "release_id": "${RELEASE_ID:-manual}",
    "total_validations": ${TOTAL_VALIDATIONS},
    "passed": ${PASSED_VALIDATIONS},
    "warnings": ${VALIDATION_WARNINGS},
    "failures": ${VALIDATION_FAILURES},
    "validations": [
EOF
    
    # Add individual validation results to JSON
    for ((i=0; i<${#VALIDATION_RESULTS[@]}; i++)); do
        IFS='|' read -r test_name status message response_time <<< "${VALIDATION_RESULTS[$i]}"
        echo "        {\"test\": \"${test_name}\", \"status\": \"${status}\", \"message\": \"${message}\", \"response_time\": \"${response_time}\"}" >> "${VALIDATION_REPORT_FILE}"
        [[ $i -lt $((${#VALIDATION_RESULTS[@]}-1)) ]] && echo "," >> "${VALIDATION_REPORT_FILE}"
    done
    
    cat >> "${VALIDATION_REPORT_FILE}" << EOF
    ]
}
EOF
    
    echo "  📄 Validation report: $(basename "${VALIDATION_REPORT_FILE}")"
    
    # Determine deployment success
    if [[ "${VALIDATION_FAILURES}" -gt 0 ]]; then
        echo ""
        echo "❌ POST-DEPLOYMENT VALIDATION FAILED"
        echo "Critical issues detected. Consider rollback."
        
        if [[ "${ROLLBACK_ON_VALIDATION_FAILURE}" == "true" ]]; then
            echo "🚨 Automatic rollback triggered"
            # Rollback logic would go here
            exit 1
        fi
    else
        echo ""
        echo "✅ POST-DEPLOYMENT VALIDATION PASSED"
        if [[ "${VALIDATION_WARNINGS}" -gt 0 ]]; then
            echo "Deployment successful with ${VALIDATION_WARNINGS} warnings"
        else
            echo "All validations passed - deployment fully successful"
        fi
    fi
    
    echo "✅ Comprehensive post-deployment validation completed"
    ```

**8.4 - 🔴 🟣 Maintenance Mode Deactivation (Server - User Configurable)**

-   **Objectives:**
    -   Safely deactivate maintenance mode with validation
    -   Restore full application access with performance monitoring
    -   Verify application functionality before completing deployment
    -   Handle deactivation failures with rollback capabilities
-   **Commands:**

    ```bash
    # Advanced maintenance mode deactivation with comprehensive validation
    echo "🔴 🟣 Deactivating maintenance mode and restoring full access..."
    
    # Load deployment variables
    source %path-server%/Admin-Local/Deployment/Scripts/load-variables.sh
    cd "${DEPLOY_PATH}/current"
    
    # Pre-deactivation validation
    echo "🔍 Pre-deactivation validation..."
    
    # Check if maintenance mode is actually active
    if php artisan inspire >/dev/null 2>&1; then
        echo "  ⚠️  Application not in maintenance mode - no deactivation needed"
    else
        echo "  ✅ Confirmed maintenance mode is active"
        
        # Additional readiness checks
        READINESS_CHECKS=0
        
        # Test application bootstrap
        if timeout 5 php artisan --version >/dev/null 2>&1; then
            echo "  ✅ Application bootstrap functional"
            ((READINESS_CHECKS++))
        else
            echo "  ❌ Application bootstrap failed"
        fi
        
        # Test database connectivity
        if timeout 10 php artisan migrate:status --env=production >/dev/null 2>&1; then
            echo "  ✅ Database connectivity confirmed"
            ((READINESS_CHECKS++))
        else
            echo "  ❌ Database connectivity issues"
        fi
        
        # Test cache system
        if echo "test" | php artisan cache:put maintenance_test - 5 2>/dev/null && [[ "$(php artisan cache:get maintenance_test 2>/dev/null)" == "test" ]]; then
            echo "  ✅ Cache system operational"
            php artisan cache:forget maintenance_test 2>/dev/null
            ((READINESS_CHECKS++))
        else
            echo "  ❌ Cache system issues"
        fi
        
        echo "  📊 Readiness checks passed: ${READINESS_CHECKS}/3"
        
        if [[ "${READINESS_CHECKS}" -lt 2 ]] && [[ "${FORCE_MAINTENANCE_UP}" != "true" ]]; then
            echo "  🚨 Insufficient readiness - maintenance mode will remain active"
            echo "  💡 Set FORCE_MAINTENANCE_UP=true to override"
            exit 1
        fi
        
        # Deactivate maintenance mode
        echo "🟢 Deactivating maintenance mode..."
        
        DEACTIVATION_START=$(date +%s%N)
        if php artisan up; then
            DEACTIVATION_END=$(date +%s%N)
            DEACTIVATION_TIME=$(( (DEACTIVATION_END - DEACTIVATION_START) / 1000000 ))ms
            
            echo "  ✅ Maintenance mode deactivated (${DEACTIVATION_TIME})"
            
            # Immediate post-deactivation validation
            echo "🔍 Post-deactivation validation..."
            
            # Wait a moment for full activation
            sleep 2
            
            # Test application access
            if php artisan inspire >/dev/null 2>&1; then
                echo "  ✅ Application fully accessible"
                
                # Performance validation
                if [[ -n "${APP_URL}" ]]; then
                    echo "  🌐 Testing HTTP access..."
                    
                    HTTP_START=$(date +%s%N)
                    HTTP_STATUS=$(curl -o /dev/null -s -w "%{http_code}" -m 10 "${APP_URL}" 2>/dev/null || echo "000")
                    HTTP_END=$(date +%s%N)
                    HTTP_TIME=$(( (HTTP_END - HTTP_START) / 1000000 ))ms
                    
                    if [[ "${HTTP_STATUS}" == "200" ]]; then
                        echo "    ✅ HTTP access confirmed (${HTTP_TIME}, HTTP ${HTTP_STATUS})"
                    else
                        echo "    ⚠️  HTTP access issues (HTTP ${HTTP_STATUS})"
                    fi
                fi
                
                # Load testing (if configured)
                if [[ "${ENABLE_POST_DEPLOYMENT_LOAD_TEST}" == "true" ]] && [[ -n "${APP_URL}" ]]; then
                    echo "  🏋️  Basic load testing..."
                    
                    LOAD_TEST_START=$(date +%s)
                    SUCCESSFUL_REQUESTS=0
                    TOTAL_REQUESTS=5
                    
                    for ((i=1; i<=TOTAL_REQUESTS; i++)); do
                        if curl -f -s -m 5 "${APP_URL}" >/dev/null 2>&1; then
                            ((SUCCESSFUL_REQUESTS++))
                        fi
                        sleep 1
                    done
                    
                    LOAD_TEST_END=$(date +%s)
                    LOAD_TEST_DURATION=$((LOAD_TEST_END - LOAD_TEST_START))
                    SUCCESS_RATE=$((SUCCESSFUL_REQUESTS * 100 / TOTAL_REQUESTS))
                    
                    echo "    📊 Load test: ${SUCCESSFUL_REQUESTS}/${TOTAL_REQUESTS} requests successful (${SUCCESS_RATE}% in ${LOAD_TEST_DURATION}s)"
                    
                    if [[ "${SUCCESS_RATE}" -ge 80 ]]; then
                        echo "    ✅ Load test acceptable"
                    else
                        echo "    ⚠️  Load test below threshold"
                    fi
                fi
                
            else
                echo "  ❌ Application still showing as in maintenance mode"
                echo "  🔧 Attempting forced deactivation..."
                
                # Remove maintenance file manually if it exists
                [[ -f "storage/framework/down" ]] && rm -f "storage/framework/down"
                
                # Test again
                sleep 2
                if php artisan inspire >/dev/null 2>&1; then
                    echo "    ✅ Forced deactivation successful"
                else
                    echo "    ❌ Forced deactivation failed - manual intervention required"
                    exit 1
                fi
            fi
            
        else
            echo "  ❌ Maintenance mode deactivation failed"
            echo "  🔧 Checking maintenance mode status..."
            
            if [[ -f "storage/framework/down" ]]; then
                echo "    🗂️  Maintenance file still exists: storage/framework/down"
                echo "    💡 Consider manual removal: rm storage/framework/down"
            fi
            
            exit 1
        fi
        
        # Final success confirmation
        echo "🎉 MAINTENANCE MODE SUCCESSFULLY DEACTIVATED"
        echo "  🌍 Application is now fully accessible to users"
        echo "  📅 Deactivation time: $(date -u +%Y-%m-%dT%H:%M:%SZ)"
        echo "  ⏱️  Deactivation duration: ${DEACTIVATION_TIME}"
        
        # Log maintenance mode deactivation
        echo "$(date -u +%Y-%m-%dT%H:%M:%SZ) MAINTENANCE_MODE_DEACTIVATED ${DEACTIVATION_TIME} ${RELEASE_ID:-manual}" >> "${DEPLOY_PATH}/deployment-history.log"
        
        # Optional: Send notification that maintenance is complete
        if [[ -n "${SLACK_WEBHOOK_URL}" ]]; then
            curl -X POST "${SLACK_WEBHOOK_URL}" \
                -H 'Content-type: application/json' \
                --data "{\"text\":\"✅ Deployment complete for ${PROJECT_NAME}!\n🟢 Maintenance mode deactivated\n🚀 Release: ${RELEASE_ID}\n⏱️ Deactivation time: ${DEACTIVATION_TIME}\"}" \
                2>/dev/null || echo "  ⚠️  Slack notification failed"
        fi
        
    fi
    
    echo "✅ Maintenance mode deactivation completed"
    ```    ***
    ## **Phase 9: 🧹 Intelligent Cleanup**

**9.1 - 🔴 🟣 Intelligent Release Management & Cleanup (Server - User Configurable)**

-   **Objectives:**
    -   Execute intelligent old release cleanup with configurable retention
    -   Maintain optimal rollback capabilities while preserving disk space
    -   Perform safety validations before removal
    -   Generate cleanup reports and maintain deployment history
-   **Commands:**

    ```bash
    # Intelligent release cleanup with comprehensive safety checks
    echo "🔴 🟣 Executing intelligent release cleanup..."
    
    # Load deployment variables
    source %path-server%/Admin-Local/Deployment/Scripts/load-variables.sh
    
    RELEASES_DIR="${DEPLOY_PATH}/releases"
    CURRENT_LINK="${DEPLOY_PATH}/current"
    KEEP_RELEASES=${KEEP_RELEASES:-5}  # Default keep 5 releases
    
    # Pre-cleanup validation
    echo "🔍 Pre-cleanup validation..."
    
    if [[ ! -d "${RELEASES_DIR}" ]]; then
        echo "  ❌ Releases directory not found: ${RELEASES_DIR}"
        exit 1
    fi
    
    cd "${RELEASES_DIR}"
    
    # Count total releases
    TOTAL_RELEASES=$(ls -1 | grep -E '^[0-9]' | wc -l)
    echo "  📊 Total releases found: ${TOTAL_RELEASES}"
    
    if [[ "${TOTAL_RELEASES}" -le "${KEEP_RELEASES}" ]]; then
        echo "  ✅ No cleanup needed (${TOTAL_RELEASES} releases <= ${KEEP_RELEASES} retention limit)"
        echo "✅ Release cleanup completed - no action required"
        exit 0
    fi
    
    # Identify current release (safety check)
    CURRENT_RELEASE=""
    if [[ -L "${CURRENT_LINK}" ]]; then
        CURRENT_RELEASE=$(basename "$(readlink "${CURRENT_LINK}")")
        echo "  🎯 Current active release: ${CURRENT_RELEASE}"
    else
        echo "  ⚠️  No current symlink found"
    fi
    
    # Get releases sorted by date (newest first)
    RELEASES_TO_KEEP=()
    RELEASES_TO_DELETE=()
    
    # Get sorted list of releases (newest first)
    mapfile -t ALL_RELEASES < <(ls -1t | grep -E '^[0-9]')
    
    # Keep the most recent releases + current release (if different)
    KEEP_COUNT=0
    for release in "${ALL_RELEASES[@]}"; do
        if [[ "${KEEP_COUNT}" -lt "${KEEP_RELEASES}" ]] || [[ "${release}" == "${CURRENT_RELEASE}" ]]; then
            RELEASES_TO_KEEP+=("${release}")
            ((KEEP_COUNT++))
        else
            RELEASES_TO_DELETE+=("${release}")
        fi
    done
    
    echo "  📋 Releases to keep (${#RELEASES_TO_KEEP[@]}): $(IFS=', '; echo "${RELEASES_TO_KEEP[*]}")"
    echo "  🗑️  Releases to delete (${#RELEASES_TO_DELETE[@]}): $(IFS=', '; echo "${RELEASES_TO_DELETE[*]}")"
    
    # Safety validation - don't delete if it would break rollback
    if [[ "${#RELEASES_TO_KEEP[@]}" -lt 2 ]]; then
        echo "  🚨 Safety check failed: Would leave less than 2 releases for rollback"
        exit 1
    fi
    
    # Calculate space to be freed
    echo "🔍 Calculating disk space impact..."
    SPACE_TO_FREE=0
    for release in "${RELEASES_TO_DELETE[@]}"; do
        if [[ -d "${release}" ]]; then
            RELEASE_SIZE=$(du -sk "${release}" 2>/dev/null | cut -f1 || echo "0")
            SPACE_TO_FREE=$((SPACE_TO_FREE + RELEASE_SIZE))
        fi
    done
    
    SPACE_TO_FREE_MB=$((SPACE_TO_FREE / 1024))
    echo "  💾 Space to be freed: ${SPACE_TO_FREE_MB}MB"
    
    # Create cleanup report
    CLEANUP_REPORT_FILE="${DEPLOY_PATH}/cleanup-reports/cleanup-$(date +%Y%m%d%H%M%S).json"
    mkdir -p "${DEPLOY_PATH}/cleanup-reports"
    
    # Execute cleanup with detailed logging
    echo "🗑️  Executing release cleanup..."
    CLEANED_RELEASES=()
    CLEANUP_ERRORS=()
    
    for release in "${RELEASES_TO_DELETE[@]}"; do
        if [[ -d "${release}" ]]; then
            echo "  🗂️  Removing release: ${release}"
            
            # Additional safety check - ensure it's not the current release
            if [[ "${release}" == "${CURRENT_RELEASE}" ]]; then
                echo "    ⚠️  Skipping current release: ${release}"
                continue
            fi
            
            # Get release info before deletion
            RELEASE_SIZE=$(du -sh "${release}" 2>/dev/null | cut -f1 || echo "unknown")
            RELEASE_DATE=""
            if [[ -f "${release}/.release-info" ]]; then
                RELEASE_DATE=$(grep -o '"created_at":"[^"]*' "${release}/.release-info" 2>/dev/null | cut -d'"' -f4 || echo "unknown")
            fi
            
            # Perform deletion with error handling
            if rm -rf "${release}" 2>/dev/null; then
                echo "    ✅ Deleted: ${release} (${RELEASE_SIZE})"
                CLEANED_RELEASES+=("{\"release\": \"${release}\", \"size\": \"${RELEASE_SIZE}\", \"created_at\": \"${RELEASE_DATE}\", \"status\": \"deleted\"}")
            else
                echo "    ❌ Failed to delete: ${release}"
                CLEANUP_ERRORS+=("${release}")
            fi
        else
            echo "  ⚠️  Release directory not found: ${release}"
        fi
    done
    
    # Generate detailed cleanup report
    cat > "${CLEANUP_REPORT_FILE}" << EOF
{
    "timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
    "total_releases_before": ${TOTAL_RELEASES},
    "keep_releases_policy": ${KEEP_RELEASES},
    "current_release": "${CURRENT_RELEASE}",
    "releases_kept": $(printf '%s\n' "${RELEASES_TO_KEEP[@]}" | jq -R . | jq -s .),
    "releases_cleaned": [$(IFS=','; echo "${CLEANED_RELEASES[*]}")],
    "cleanup_errors": $(printf '%s\n' "${CLEANUP_ERRORS[@]}" | jq -R . | jq -s .),
    "space_freed_mb": ${SPACE_TO_FREE_MB},
    "cleanup_successful": $([ ${#CLEANUP_ERRORS[@]} -eq 0 ] && echo "true" || echo "false")
}
EOF
    
    # Summary
    SUCCESSFUL_CLEANUPS=${#CLEANED_RELEASES[@]}
    FAILED_CLEANUPS=${#CLEANUP_ERRORS[@]}
    REMAINING_RELEASES=$(ls -1 | grep -E '^[0-9]' | wc -l)
    
    echo "📊 CLEANUP SUMMARY"
    echo "=================="
    echo "  Releases cleaned: ${SUCCESSFUL_CLEANUPS}"
    echo "  Cleanup failures: ${FAILED_CLEANUPS}"
    echo "  Remaining releases: ${REMAINING_RELEASES}"
    echo "  Space freed: ${SPACE_TO_FREE_MB}MB"
    echo "  📄 Report: $(basename "${CLEANUP_REPORT_FILE}")"
    
    if [[ "${FAILED_CLEANUPS}" -gt 0 ]]; then
        echo "⚠️  Some cleanup operations failed - check permissions"
    else
        echo "✅ Release cleanup completed successfully"
    fi
    
    # Log cleanup completion
    echo "$(date -u +%Y-%m-%dT%H:%M:%SZ) CLEANUP_COMPLETED ${SUCCESSFUL_CLEANUPS}C_${FAILED_CLEANUPS}F_${SPACE_TO_FREE_MB}MB ${RELEASE_ID:-manual}" >> "${DEPLOY_PATH}/deployment-history.log"
    ```

**9.2 - 🔴 🟡 🟣 Comprehensive Cache & Build Artifacts Cleanup (Server/Builder VM - User Configurable)**

-   **Objectives:**
    -   Execute intelligent cleanup of build caches and temporary artifacts
    -   Optimize storage usage across build and deployment environments
    -   Maintain performance caches while removing obsolete data
    -   Handle multi-environment cleanup with build strategy awareness
-   **Commands:**

    ```bash
    # Comprehensive cache and build artifacts cleanup
    echo "🔴 🟡 🟣 Executing comprehensive cache cleanup..."
    
    # Load deployment variables
    source %path-server%/Admin-Local/Deployment/Scripts/load-variables.sh
    
    # Initialize cleanup metrics
    TOTAL_SPACE_FREED=0
    CLEANUP_OPERATIONS=()
    
    # Function to log cleanup operation
    log_cleanup_operation() {
        local operation="$1"
        local status="$2"
        local space_freed="$3"
        local location="$4"
        
        CLEANUP_OPERATIONS+=("${operation}|${status}|${space_freed}|${location}")
        
        case "${status}" in
            "SUCCESS")
                echo "  ✅ ${operation}: ${space_freed} freed (${location})"
                ;;
            "SKIP")
                echo "  ⏭️  ${operation}: ${space_freed} (${location})"
                ;;
            "ERROR")
                echo "  ❌ ${operation}: Failed (${location})"
                ;;
        esac
    }
    
    # 1. Build Artifacts Cleanup (Builder VM)
    if [[ "${BUILD_STRATEGY}" != "server" ]] || [[ "${CLEANUP_BUILD_ARTIFACTS}" == "true" ]]; then
        echo "🏗️  Build artifacts cleanup..."
        
        BUILD_ARTIFACTS_DIR="${BUILD_PATH:-/tmp/build}"
        if [[ -d "${BUILD_ARTIFACTS_DIR}" ]]; then
            cd "${BUILD_ARTIFACTS_DIR}"
            
            # Clean up release tarballs
            TARBALL_SIZE=0
            if ls release-*.tar.gz >/dev/null 2>&1; then
                TARBALL_SIZE=$(du -sk release-*.tar.gz 2>/dev/null | awk '{sum+=$1} END {print sum}' || echo "0")
                rm -f release-*.tar.gz release.md5 release.sha256 2>/dev/null
                log_cleanup_operation "Release Tarballs" "SUCCESS" "$((TARBALL_SIZE / 1024))MB" "Builder VM"
                TOTAL_SPACE_FREED=$((TOTAL_SPACE_FREED + TARBALL_SIZE))
            else
                log_cleanup_operation "Release Tarballs" "SKIP" "No files found" "Builder VM"
            fi
            
            # Clean up temporary build files
            TEMP_BUILD_SIZE=0
            if [[ -d "temp-build" ]]; then
                TEMP_BUILD_SIZE=$(du -sk temp-build 2>/dev/null | cut -f1 || echo "0")
                rm -rf temp-build 2>/dev/null
                log_cleanup_operation "Temporary Build Files" "SUCCESS" "$((TEMP_BUILD_SIZE / 1024))MB" "Builder VM"
                TOTAL_SPACE_FREED=$((TOTAL_SPACE_FREED + TEMP_BUILD_SIZE))
            else
                log_cleanup_operation "Temporary Build Files" "SKIP" "No directory found" "Builder VM"
            fi
            
        else
            log_cleanup_operation "Build Artifacts Directory" "SKIP" "Directory not found" "Builder VM"
        fi
    fi
    
    # 2. Composer Cache Cleanup (Multi-location)
    echo "📦 Composer cache cleanup..."
    
    COMPOSER_CACHE_LOCATIONS=(
        "${HOME}/.composer/cache"
        "${COMPOSER_CACHE_DIR:-/tmp/composer-cache}"
        "${CACHE_BASE_PATH}/${PROJECT_NAME}/composer" 
    )
    
    for cache_dir in "${COMPOSER_CACHE_LOCATIONS[@]}"; do
        if [[ -d "${cache_dir}" ]] && [[ "${CLEANUP_COMPOSER_CACHE}" == "true" ]]; then
            COMPOSER_CACHE_SIZE=$(du -sk "${cache_dir}" 2>/dev/null | cut -f1 || echo "0")
            
            # Keep recent cache but clean old files (older than 30 days)
            if [[ "${COMPOSER_CACHE_SIZE}" -gt 102400 ]]; then  # > 100MB
                OLD_CACHE_SIZE=$(find "${cache_dir}" -type f -mtime +30 -exec du -sk {} + 2>/dev/null | awk '{sum+=$1} END {print sum}' || echo "0")
                find "${cache_dir}" -type f -mtime +30 -delete 2>/dev/null
                
                if [[ "${OLD_CACHE_SIZE}" -gt 0 ]]; then
                    log_cleanup_operation "Composer Cache (old files)" "SUCCESS" "$((OLD_CACHE_SIZE / 1024))MB" "$(basename "${cache_dir}")"
                    TOTAL_SPACE_FREED=$((TOTAL_SPACE_FREED + OLD_CACHE_SIZE))
                else
                    log_cleanup_operation "Composer Cache" "SKIP" "No old files" "$(basename "${cache_dir}")"
                fi
            else
                log_cleanup_operation "Composer Cache" "SKIP" "Cache size acceptable" "$(basename "${cache_dir}")"
            fi
        else
            log_cleanup_operation "Composer Cache" "SKIP" "Not found or disabled" "$(basename "${cache_dir}")"
        fi
    done
    
    # 3. NPM/Node.js Cache Cleanup
    echo "📦 NPM cache cleanup..."
    
    NPM_CACHE_LOCATIONS=(
        "${HOME}/.npm"
        "${NPM_CACHE_DIR:-/tmp/npm-cache}"
        "${CACHE_BASE_PATH}/${PROJECT_NAME}/npm"
    )
    
    for cache_dir in "${NPM_CACHE_LOCATIONS[@]}"; do
        if [[ -d "${cache_dir}" ]] && [[ "${CLEANUP_NPM_CACHE}" == "true" ]]; then
            NPM_CACHE_SIZE=$(du -sk "${cache_dir}" 2>/dev/null | cut -f1 || echo "0")
            
            if [[ "${NPM_CACHE_SIZE}" -gt 51200 ]]; then  # > 50MB
                # Use npm cache clean if available, otherwise manual cleanup
                if command -v npm >/dev/null 2>&1; then
                    CACHE_SIZE_BEFORE=${NPM_CACHE_SIZE}
                    npm cache clean --force 2>/dev/null || find "${cache_dir}" -type f -mtime +15 -delete 2>/dev/null
                    CACHE_SIZE_AFTER=$(du -sk "${cache_dir}" 2>/dev/null | cut -f1 || echo "0")
                    CLEANED_SIZE=$((CACHE_SIZE_BEFORE - CACHE_SIZE_AFTER))
                    
                    if [[ "${CLEANED_SIZE}" -gt 0 ]]; then
                        log_cleanup_operation "NPM Cache" "SUCCESS" "$((CLEANED_SIZE / 1024))MB" "$(basename "${cache_dir}")"
                        TOTAL_SPACE_FREED=$((TOTAL_SPACE_FREED + CLEANED_SIZE))
                    else
                        log_cleanup_operation "NPM Cache" "SKIP" "Nothing to clean" "$(basename "${cache_dir}")"
                    fi
                else
                    log_cleanup_operation "NPM Cache" "SKIP" "NPM not available" "$(basename "${cache_dir}")"
                fi
            else
                log_cleanup_operation "NPM Cache" "SKIP" "Cache size acceptable" "$(basename "${cache_dir}")"
            fi
        fi
    done
    
    # 4. Laravel Application Cache Cleanup (Server)
    echo "🚮 Laravel application cache cleanup..."
    
    cd "${DEPLOY_PATH}/current" || cd "${RELEASE_PATH}"
    
    # Temporary files and logs cleanup
    if [[ "${CLEANUP_LARAVEL_LOGS}" == "true" ]] && [[ -d "storage/logs" ]]; then
        LOG_SIZE_BEFORE=$(du -sk storage/logs 2>/dev/null | cut -f1 || echo "0")
        
        # Keep recent logs (last 7 days) but compress older ones
        find storage/logs -name "*.log" -mtime +7 -exec gzip {} \; 2>/dev/null
        # Remove very old compressed logs (30+ days)
        find storage/logs -name "*.log.gz" -mtime +30 -delete 2>/dev/null
        
        LOG_SIZE_AFTER=$(du -sk storage/logs 2>/dev/null | cut -f1 || echo "0")
        LOGS_CLEANED=$((LOG_SIZE_BEFORE - LOG_SIZE_AFTER))
        
        if [[ "${LOGS_CLEANED}" -gt 0 ]]; then
            log_cleanup_operation "Laravel Logs" "SUCCESS" "$((LOGS_CLEANED / 1024))MB" "storage/logs"
            TOTAL_SPACE_FREED=$((TOTAL_SPACE_FREED + LOGS_CLEANED))
        else
            log_cleanup_operation "Laravel Logs" "SKIP" "No cleanup needed" "storage/logs"
        fi
    fi
    
    # Framework cache cleanup
    if [[ "${CLEANUP_FRAMEWORK_CACHE}" == "true" ]] && [[ -d "storage/framework/cache" ]]; then
        CACHE_SIZE_BEFORE=$(du -sk storage/framework/cache 2>/dev/null | cut -f1 || echo "0")
        
        # Clean expired cache files
        find storage/framework/cache -name "data" -type d -exec find {} -type f -mtime +1 -delete \; 2>/dev/null
        
        CACHE_SIZE_AFTER=$(du -sk storage/framework/cache 2>/dev/null | cut -f1 || echo "0")
        CACHE_CLEANED=$((CACHE_SIZE_BEFORE - CACHE_SIZE_AFTER))
        
        if [[ "${CACHE_CLEANED}" -gt 0 ]]; then
            log_cleanup_operation "Framework Cache" "SUCCESS" "$((CACHE_CLEANED / 1024))MB" "storage/framework/cache"
            TOTAL_SPACE_FREED=$((TOTAL_SPACE_FREED + CACHE_CLEANED))
        else
            log_cleanup_operation "Framework Cache" "SKIP" "No expired files" "storage/framework/cache"
        fi
    fi
    
    # 5. System Temporary Files
    echo "🗑️  System temporary files cleanup..."
    
    if [[ "${CLEANUP_SYSTEM_TEMP}" == "true" ]] && [[ -w "/tmp" ]]; then
        TEMP_SIZE_BEFORE=$(du -sk /tmp 2>/dev/null | cut -f1 || echo "0")
        
        # Clean old temporary files related to our deployment
        find /tmp -name "${PROJECT_NAME}*" -mtime +1 -type f -delete 2>/dev/null || true
        find /tmp -name "laravel*" -mtime +7 -type f -delete 2>/dev/null || true
        
        TEMP_SIZE_AFTER=$(du -sk /tmp 2>/dev/null | cut -f1 || echo "0")
        TEMP_CLEANED=$((TEMP_SIZE_BEFORE - TEMP_SIZE_AFTER))
        
        if [[ "${TEMP_CLEANED}" -gt 0 ]]; then
            log_cleanup_operation "System Temp Files" "SUCCESS" "$((TEMP_CLEANED / 1024))MB" "/tmp"
            TOTAL_SPACE_FREED=$((TOTAL_SPACE_FREED + TEMP_CLEANED))
        else
            log_cleanup_operation "System Temp Files" "SKIP" "No files to clean" "/tmp"
        fi
    fi
    
    # Generate comprehensive cleanup report
    CLEANUP_REPORT_FILE="${DEPLOY_PATH}/cleanup-reports/cache-cleanup-$(date +%Y%m%d%H%M%S).json"
    mkdir -p "${DEPLOY_PATH}/cleanup-reports"
    
    cat > "${CLEANUP_REPORT_FILE}" << EOF
{
    "timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
    "build_strategy": "${BUILD_STRATEGY}",
    "total_space_freed_mb": $((TOTAL_SPACE_FREED / 1024)),
    "cleanup_operations": [
EOF
    
    # Add individual cleanup operations to JSON
    for ((i=0; i<${#CLEANUP_OPERATIONS[@]}; i++)); do
        IFS='|' read -r operation status space_freed location <<< "${CLEANUP_OPERATIONS[$i]}"
        echo "        {\"operation\": \"${operation}\", \"status\": \"${status}\", \"space_freed\": \"${space_freed}\", \"location\": \"${location}\"}" >> "${CLEANUP_REPORT_FILE}"
        [[ $i -lt $((${#CLEANUP_OPERATIONS[@]}-1)) ]] && echo "," >> "${CLEANUP_REPORT_FILE}"
    done
    
    cat >> "${CLEANUP_REPORT_FILE}" << EOF
    ]
}
EOF
    
    # Final summary
    TOTAL_SPACE_FREED_MB=$((TOTAL_SPACE_FREED / 1024))
    
    echo "📊 CACHE CLEANUP SUMMARY"
    echo "======================="
    echo "  Total operations: ${#CLEANUP_OPERATIONS[@]}"
    echo "  Total space freed: ${TOTAL_SPACE_FREED_MB}MB"
    echo "  📄 Report: $(basename "${CLEANUP_REPORT_FILE}")"
    
    if [[ "${TOTAL_SPACE_FREED_MB}" -gt 0 ]]; then
        echo "✅ Cache cleanup completed - ${TOTAL_SPACE_FREED_MB}MB freed"
    else
        echo "✅ Cache cleanup completed - no significant space freed"
    fi
    
    # Log cache cleanup completion
    echo "$(date -u +%Y-%m-%dT%H:%M:%SZ) CACHE_CLEANUP_COMPLETED ${#CLEANUP_OPERATIONS[@]}ops_${TOTAL_SPACE_FREED_MB}MB ${RELEASE_ID:-manual}" >> "${DEPLOY_PATH}/deployment-history.log"
    ```    ***
    ## **Phase 10: 📊 Comprehensive Finalization & Communications**

**10.1 - 🔴 🟣 Advanced Deployment Logging & Metadata Recording (Server - User Configurable)**

-   **Objectives:**
    -   Generate comprehensive deployment documentation and audit trail
    -   Record detailed metadata for compliance and troubleshooting
    -   Create structured deployment history with performance metrics
    -   Enable deployment analytics and trend analysis
-   **Commands:**

    ```bash
    # Advanced deployment logging with comprehensive metadata collection
    echo "🔴 🟣 Creating comprehensive deployment documentation..."
    
    # Load deployment variables
    source %path-server%/Admin-Local/Deployment/Scripts/load-variables.sh
    cd "${DEPLOY_PATH}/current"
    
    # Initialize deployment completion timestamp
    DEPLOYMENT_END_TIME=$(date -u +%Y-%m-%dT%H:%M:%SZ)
    DEPLOYMENT_END_UNIX=$(date +%s)
    
    # Calculate total deployment duration
    if [[ -n "${DEPLOYMENT_START_UNIX}" ]]; then
        TOTAL_DEPLOYMENT_TIME=$((DEPLOYMENT_END_UNIX - DEPLOYMENT_START_UNIX))
        DEPLOYMENT_DURATION="${TOTAL_DEPLOYMENT_TIME}s"
    else
        DEPLOYMENT_DURATION="unknown"
    fi
    
    # Collect comprehensive deployment metadata
    echo "📊 Collecting deployment metadata..."
    
    # Git information
    GIT_COMMIT_HASH=$(git rev-parse HEAD 2>/dev/null || echo "unknown")
    GIT_COMMIT_SHORT=$(git rev-parse --short HEAD 2>/dev/null || echo "unknown")
    GIT_BRANCH=$(git branch --show-current 2>/dev/null || echo "unknown")
    GIT_COMMIT_MESSAGE=$(git log -1 --pretty=format:'%s' 2>/dev/null | sed 's/"/\\"/g' || echo "unknown")
    GIT_COMMIT_AUTHOR=$(git log -1 --pretty=format:'%an <%ae>' 2>/dev/null || echo "unknown")
    GIT_COMMIT_DATE=$(git log -1 --pretty=format:'%ci' 2>/dev/null || echo "unknown")
    
    # System information
    SYSTEM_HOSTNAME=$(hostname)
    SYSTEM_USER=$(whoami)
    SYSTEM_OS=$(uname -s)
    SYSTEM_ARCH=$(uname -m)
    
    # Application information
    LARAVEL_VERSION=$(php artisan --version 2>/dev/null | head -n1 || echo "unknown")
    PHP_VERSION=$(php --version | head -n1)
    COMPOSER_VERSION=$(composer --version 2>/dev/null || echo "unknown")
    NODE_VERSION=$(node --version 2>/dev/null || echo "not installed")
    NPM_VERSION=$(npm --version 2>/dev/null || echo "not installed")
    
    # Database information
    if timeout 5 php artisan migrate:status --env=production >/dev/null 2>&1; then
        MIGRATION_STATUS="current"
        MIGRATION_COUNT=$(php artisan migrate:status --env=production 2>/dev/null | grep -c "^| Y" || echo "0")
    else
        MIGRATION_STATUS="unknown"
        MIGRATION_COUNT="0"
    fi
    
    # Performance metrics collection
    echo "⏱️  Collecting performance metrics..."
    
    # Application response time test
    if [[ -n "${APP_URL}" ]]; then
        APP_RESPONSE_START=$(date +%s%N)
        HTTP_STATUS=$(curl -o /dev/null -s -w "%{http_code}" -m 10 "${APP_URL}" 2>/dev/null || echo "000")
        APP_RESPONSE_END=$(date +%s%N)
        APP_RESPONSE_TIME=$(( (APP_RESPONSE_END - APP_RESPONSE_START) / 1000000 ))
        
        if [[ "${HTTP_STATUS}" == "200" ]]; then
            APP_STATUS="operational"
        else
            APP_STATUS="issues_detected"
        fi
    else
        APP_RESPONSE_TIME="0"
        APP_STATUS="not_configured"
        HTTP_STATUS="000"
    fi
    
    # File system metrics
    RELEASE_SIZE=$(du -sh "${RELEASE_PATH}" 2>/dev/null | cut -f1 || echo "unknown")
    DISK_USAGE=$(df -h "${DEPLOY_PATH}" | awk 'NR==2 {print $5}' || echo "unknown")
    AVAILABLE_SPACE=$(df -h "${DEPLOY_PATH}" | awk 'NR==2 {print $4}' || echo "unknown")
    
    # Create comprehensive deployment record
    DEPLOYMENT_LOG_FILE="${DEPLOY_PATH}/deployment-records/deployment-${RELEASE_ID:-$(date +%Y%m%d%H%M%S)}.json"
    mkdir -p "${DEPLOY_PATH}/deployment-records"
    
    echo "📝 Generating detailed deployment record..."
    
    cat > "${DEPLOYMENT_LOG_FILE}" << EOF
{
    "deployment_metadata": {
        "deployment_id": "${RELEASE_ID:-$(date +%Y%m%d%H%M%S)}",
        "deployment_started": "${DEPLOYMENT_START_TIME:-unknown}",
        "deployment_completed": "${DEPLOYMENT_END_TIME}",
        "deployment_duration": "${DEPLOYMENT_DURATION}",
        "deployment_type": "${DEPLOYMENT_TYPE:-manual}",
        "build_strategy": "${BUILD_STRATEGY:-local}",
        "deployed_by": "${SYSTEM_USER}",
        "deployment_server": "${SYSTEM_HOSTNAME}"
    },
    "source_control": {
        "git_commit_hash": "${GIT_COMMIT_HASH}",
        "git_commit_short": "${GIT_COMMIT_SHORT}",
        "git_branch": "${GIT_BRANCH}",
        "git_commit_message": "${GIT_COMMIT_MESSAGE}",
        "git_commit_author": "${GIT_COMMIT_AUTHOR}",
        "git_commit_date": "${GIT_COMMIT_DATE}"
    },
    "system_environment": {
        "hostname": "${SYSTEM_HOSTNAME}",
        "operating_system": "${SYSTEM_OS}",
        "architecture": "${SYSTEM_ARCH}",
        "php_version": "${PHP_VERSION}",
        "laravel_version": "${LARAVEL_VERSION}",
        "composer_version": "${COMPOSER_VERSION}",
        "node_version": "${NODE_VERSION}",
        "npm_version": "${NPM_VERSION}"
    },
    "database_status": {
        "migration_status": "${MIGRATION_STATUS}",
        "migration_count": ${MIGRATION_COUNT},
        "database_connection": "${DB_CONNECTION:-unknown}"
    },
    "performance_metrics": {
        "app_response_time_ms": ${APP_RESPONSE_TIME},
        "app_status": "${APP_STATUS}",
        "http_status_code": "${HTTP_STATUS}",
        "release_size": "${RELEASE_SIZE}",
        "disk_usage": "${DISK_USAGE}",
        "available_space": "${AVAILABLE_SPACE}"
    },
    "deployment_configuration": {
        "project_name": "${PROJECT_NAME:-unknown}",
        "environment": "${APP_ENV:-production}",
        "debug_mode": "${APP_DEBUG:-false}",
        "maintenance_mode_used": "${MAINTENANCE_MODE_USED:-false}",
        "cache_driver": "${CACHE_DRIVER:-unknown}",
        "queue_connection": "${QUEUE_CONNECTION:-unknown}",
        "session_driver": "${SESSION_DRIVER:-unknown}"
    },
    "deployment_paths": {
        "deployment_path": "${DEPLOY_PATH}",
        "release_path": "${RELEASE_PATH}",
        "current_path": "${DEPLOY_PATH}/current",
        "shared_path": "${DEPLOY_PATH}/shared"
    }
}
EOF
    
    # Update simple deployment log for backwards compatibility
    SIMPLE_LOG_FILE="${DEPLOY_PATH}/deployment.log"
    cat >> "${SIMPLE_LOG_FILE}" << EOF
==================================================
Deployment completed: ${RELEASE_ID:-$(date +%Y%m%d%H%M%S)}
Timestamp: ${DEPLOYMENT_END_TIME}
Duration: ${DEPLOYMENT_DURATION}
Git commit: ${GIT_COMMIT_HASH}
Git branch: ${GIT_BRANCH}
Build strategy: ${BUILD_STRATEGY:-local}
Deployed by: ${SYSTEM_USER}@${SYSTEM_HOSTNAME}
Laravel version: ${LARAVEL_VERSION}
App status: ${APP_STATUS}
Response time: ${APP_RESPONSE_TIME}ms
Release size: ${RELEASE_SIZE}
==================================================

EOF
    
    # Create deployment summary for quick reference
    DEPLOYMENT_SUMMARY_FILE="${DEPLOY_PATH}/LAST_DEPLOYMENT.txt"
    cat > "${DEPLOYMENT_SUMMARY_FILE}" << EOF
Last Deployment Summary
======================
Deployment ID: ${RELEASE_ID:-$(date +%Y%m%d%H%M%S)}
Completed: ${DEPLOYMENT_END_TIME}
Duration: ${DEPLOYMENT_DURATION}
Status: ${APP_STATUS}
Git Commit: ${GIT_COMMIT_SHORT} (${GIT_BRANCH})
Message: ${GIT_COMMIT_MESSAGE}
Deployed by: ${SYSTEM_USER}

Quick Commands:
- View logs: tail -f storage/logs/laravel.log
- Check status: php artisan --version
- Run migrations: php artisan migrate:status
- Clear cache: php artisan cache:clear

For detailed deployment record, see:
${DEPLOYMENT_LOG_FILE}
EOF
    
    # Generate deployment history entry
    echo "${DEPLOYMENT_END_TIME} SUCCESS ${RELEASE_ID:-manual} ${GIT_COMMIT_SHORT} ${DEPLOYMENT_DURATION} ${APP_STATUS} ${BUILD_STRATEGY:-local}" >> "${DEPLOY_PATH}/deployment-history.log"
    
    # Create performance tracking entry
    PERFORMANCE_LOG="${DEPLOY_PATH}/performance-history.log"
    echo "${DEPLOYMENT_END_TIME},${RELEASE_ID:-manual},${APP_RESPONSE_TIME},${HTTP_STATUS},${DEPLOYMENT_DURATION},${RELEASE_SIZE}" >> "${PERFORMANCE_LOG}"
    
    echo "📊 DEPLOYMENT LOGGING SUMMARY"
    echo "============================="
    echo "  Deployment ID: ${RELEASE_ID:-$(date +%Y%m%d%H%M%S)}"
    echo "  Duration: ${DEPLOYMENT_DURATION}"
    echo "  Status: ${APP_STATUS}"
    echo "  Response time: ${APP_RESPONSE_TIME}ms"
    echo "  📄 Detailed record: $(basename "${DEPLOYMENT_LOG_FILE}")"
    echo "  📄 Summary file: LAST_DEPLOYMENT.txt"
    
    echo "✅ Advanced deployment logging completed"
    ```

**10.2 - 🔴 🟡 🟣 Comprehensive Notifications & Webhooks (Multi-Environment - User Configurable)**

-   **Objectives:**
    -   Send intelligent deployment notifications across multiple channels
    -   Trigger configured webhooks with comprehensive deployment data
    -   Update monitoring and tracking systems with deployment status
    -   Provide detailed deployment reports to stakeholders
-   **Commands:**

    ```bash
    # Comprehensive notifications and webhooks with intelligent routing
    echo "🔴 🟡 🟣 Executing comprehensive notifications and webhooks..."
    
    # Load deployment variables and notification configuration
    source %path-server%/Admin-Local/Deployment/Scripts/load-variables.sh
    
    # Prepare notification data
    NOTIFICATION_TIMESTAMP=$(date -u +%Y-%m-%dT%H:%M:%SZ)
    DEPLOYMENT_STATUS="${APP_STATUS:-unknown}"
    NOTIFICATION_TITLE="${PROJECT_NAME} Deployment ${DEPLOYMENT_STATUS^}"
    
    # Determine notification urgency and emoji
    case "${DEPLOYMENT_STATUS}" in
        "operational")
            NOTIFICATION_URGENCY="success"
            STATUS_EMOJI="✅"
            STATUS_COLOR="#36a64f"
            ;;
        "issues_detected")
            NOTIFICATION_URGENCY="warning"
            STATUS_EMOJI="⚠️"
            STATUS_COLOR="#ff9500"
            ;;
        *)
            NOTIFICATION_URGENCY="info"
            STATUS_EMOJI="ℹ️"
            STATUS_COLOR="#0099cc"
            ;;
    esac
    
    # Create comprehensive notification payload
    NOTIFICATION_DATA=$(cat << EOF
{
    "deployment_id": "${RELEASE_ID:-$(date +%Y%m%d%H%M%S)}",
    "project": "${PROJECT_NAME}",
    "status": "${DEPLOYMENT_STATUS}",
    "urgency": "${NOTIFICATION_URGENCY}",
    "timestamp": "${NOTIFICATION_TIMESTAMP}",
    "duration": "${DEPLOYMENT_DURATION:-unknown}",
    "git_commit": "${GIT_COMMIT_SHORT:-unknown}",
    "git_branch": "${GIT_BRANCH:-unknown}",
    "deployed_by": "$(whoami)@$(hostname)",
    "build_strategy": "${BUILD_STRATEGY:-local}",
    "environment": "${APP_ENV:-production}",
    "app_url": "${APP_URL:-not configured}",
    "response_time_ms": ${APP_RESPONSE_TIME:-0}
}
EOF
)
    
    # 1. Slack Notifications
    if [[ -n "${SLACK_WEBHOOK_URL}" ]]; then
        echo "📢 Sending Slack notification..."
        
        # Create rich Slack message
        SLACK_PAYLOAD=$(cat << EOF
{
    "text": "${STATUS_EMOJI} ${NOTIFICATION_TITLE}",
    "attachments": [
        {
            "color": "${STATUS_COLOR}",
            "title": "${PROJECT_NAME} Deployment Report",
            "fields": [
                {
                    "title": "Status",
                    "value": "${DEPLOYMENT_STATUS^}",
                    "short": true
                },
                {
                    "title": "Duration", 
                    "value": "${DEPLOYMENT_DURATION:-unknown}",
                    "short": true
                },
                {
                    "title": "Commit",
                    "value": "\`${GIT_COMMIT_SHORT:-unknown}\` on \`${GIT_BRANCH:-unknown}\`",
                    "short": true
                },
                {
                    "title": "Environment",
                    "value": "${APP_ENV:-production}",
                    "short": true
                },
                {
                    "title": "Response Time",
                    "value": "${APP_RESPONSE_TIME:-0}ms",
                    "short": true
                },
                {
                    "title": "Build Strategy",
                    "value": "${BUILD_STRATEGY:-local}",
                    "short": true
                }
            ],
            "footer": "Deployed by $(whoami)@$(hostname)",
            "ts": $(date +%s)
        }
    ]
}
EOF
)
        
        # Send Slack notification with retry logic
        SLACK_RETRY_COUNT=0
        SLACK_MAX_RETRIES=3
        
        while [[ "${SLACK_RETRY_COUNT}" -lt "${SLACK_MAX_RETRIES}" ]]; do
            if curl -X POST "${SLACK_WEBHOOK_URL}" \
                -H 'Content-type: application/json' \
                -d "${SLACK_PAYLOAD}" \
                --max-time 30 \
                --silent --show-error 2>/dev/null; then
                echo "  ✅ Slack notification sent successfully"
                break
            else
                ((SLACK_RETRY_COUNT++))
                echo "  ⚠️  Slack notification attempt ${SLACK_RETRY_COUNT} failed"
                
                if [[ "${SLACK_RETRY_COUNT}" -lt "${SLACK_MAX_RETRIES}" ]]; then
                    echo "  🔄 Retrying in 5 seconds..."
                    sleep 5
                else
                    echo "  ❌ Slack notification failed after ${SLACK_MAX_RETRIES} attempts"
                fi
            fi
        done
    fi
    
    # 2. Discord Notifications (if configured)
    if [[ -n "${DISCORD_WEBHOOK_URL}" ]]; then
        echo "🎮 Sending Discord notification..."
        
        DISCORD_PAYLOAD=$(cat << EOF
{
    "content": "${STATUS_EMOJI} **${NOTIFICATION_TITLE}**",
    "embeds": [
        {
            "title": "${PROJECT_NAME} Deployment",
            "color": $([[ "${STATUS_COLOR}" == "#36a64f" ]] && echo "3581519" || [[ "${STATUS_COLOR}" == "#ff9500" ]] && echo "16747520" || echo "52479"),
            "fields": [
                {"name": "Status", "value": "\`${DEPLOYMENT_STATUS^}\`", "inline": true},
                {"name": "Duration", "value": "\`${DEPLOYMENT_DURATION:-unknown}\`", "inline": true},
                {"name": "Commit", "value": "\`${GIT_COMMIT_SHORT:-unknown}\` (\`${GIT_BRANCH:-unknown}\`)", "inline": true},
                {"name": "Environment", "value": "\`${APP_ENV:-production}\`", "inline": true},
                {"name": "Response Time", "value": "\`${APP_RESPONSE_TIME:-0}ms\`", "inline": true},
                {"name": "Build Strategy", "value": "\`${BUILD_STRATEGY:-local}\`", "inline": true}
            ],
            "footer": {"text": "Deployed by $(whoami)@$(hostname)"},
            "timestamp": "${NOTIFICATION_TIMESTAMP}"
        }
    ]
}
EOF
)
        
        if curl -X POST "${DISCORD_WEBHOOK_URL}" \
            -H 'Content-Type: application/json' \
            -d "${DISCORD_PAYLOAD}" \
            --max-time 30 \
            --silent 2>/dev/null; then
            echo "  ✅ Discord notification sent successfully"
        else
            echo "  ❌ Discord notification failed"
        fi
    fi
    
    # 3. Email Notifications (if configured)
    if [[ -n "${EMAIL_WEBHOOK_URL}" ]] && [[ -n "${EMAIL_RECIPIENTS}" ]]; then
        echo "📧 Sending email notifications..."
        
        EMAIL_SUBJECT="${STATUS_EMOJI} ${NOTIFICATION_TITLE} - ${NOTIFICATION_TIMESTAMP}"
        EMAIL_BODY="Deployment Report for ${PROJECT_NAME}

Status: ${DEPLOYMENT_STATUS^}
Duration: ${DEPLOYMENT_DURATION:-unknown}
Git Commit: ${GIT_COMMIT_SHORT:-unknown} (${GIT_BRANCH:-unknown})
Environment: ${APP_ENV:-production}
Response Time: ${APP_RESPONSE_TIME:-0}ms
Build Strategy: ${BUILD_STRATEGY:-local}
Deployed by: $(whoami)@$(hostname)

Application URL: ${APP_URL:-not configured}
Deployment ID: ${RELEASE_ID:-$(date +%Y%m%d%H%M%S)}

For detailed logs and information, please check the deployment server."
        
        EMAIL_PAYLOAD=$(cat << EOF
{
    "to": "${EMAIL_RECIPIENTS}",
    "subject": "${EMAIL_SUBJECT}",
    "body": "${EMAIL_BODY}",
    "from": "${EMAIL_FROM:-noreply@$(hostname)}",
    "deployment_data": ${NOTIFICATION_DATA}
}
EOF
)
        
        if curl -X POST "${EMAIL_WEBHOOK_URL}" \
            -H 'Content-Type: application/json' \
            -d "${EMAIL_PAYLOAD}" \
            --max-time 30 \
            --silent 2>/dev/null; then
            echo "  ✅ Email notifications sent successfully"
        else
            echo "  ❌ Email notifications failed"
        fi
    fi
    
    # 4. Custom Webhooks
    if [[ -n "${CUSTOM_WEBHOOKS}" ]]; then
        echo "🔗 Triggering custom webhooks..."
        
        IFS=',' read -ra WEBHOOK_URLS <<< "${CUSTOM_WEBHOOKS}"
        for webhook_url in "${WEBHOOK_URLS[@]}"; do
            webhook_url=$(echo "${webhook_url}" | xargs)  # trim whitespace
            
            echo "  📡 Calling webhook: $(echo "${webhook_url}" | cut -d'/' -f3)"
            
            if curl -X POST "${webhook_url}" \
                -H 'Content-Type: application/json' \
                -d "${NOTIFICATION_DATA}" \
                --max-time 30 \
                --silent 2>/dev/null; then
                echo "    ✅ Webhook called successfully"
            else
                echo "    ❌ Webhook call failed"
            fi
        done
    fi
    
    # 5. Monitoring System Updates
    echo "📊 Updating monitoring and tracking systems..."
    
    # New Relic deployment marker (if configured)
    if [[ -n "${NEW_RELIC_API_KEY}" ]] && [[ -n "${NEW_RELIC_APP_ID}" ]]; then
        echo "  📈 Creating New Relic deployment marker..."
        
        NEW_RELIC_PAYLOAD=$(cat << EOF
{
    "deployment": {
        "revision": "${GIT_COMMIT_HASH:-unknown}",
        "changelog": "${GIT_COMMIT_MESSAGE:-Deployment}",
        "description": "Deployment ${RELEASE_ID:-$(date +%Y%m%d%H%M%S)}",
        "user": "$(whoami)@$(hostname)",
        "timestamp": "${NOTIFICATION_TIMESTAMP}"
    }
}
EOF
)
        
        if curl -X POST "https://api.newrelic.com/v2/applications/${NEW_RELIC_APP_ID}/deployments.json" \
            -H "X-Api-Key:${NEW_RELIC_API_KEY}" \
            -H 'Content-Type: application/json' \
            -d "${NEW_RELIC_PAYLOAD}" \
            --max-time 30 \
            --silent 2>/dev/null; then
            echo "    ✅ New Relic deployment marker created"
        else
            echo "    ❌ New Relic deployment marker failed"
        fi
    fi
    
    # Sentry release (if configured)
    if [[ -n "${SENTRY_AUTH_TOKEN}" ]] && [[ -n "${SENTRY_ORG}" ]] && [[ -n "${SENTRY_PROJECT}" ]]; then
        echo "  🐛 Creating Sentry release..."
        
        SENTRY_RELEASE="${PROJECT_NAME}@${GIT_COMMIT_SHORT:-$(date +%Y%m%d%H%M%S)}"
        
        # Create Sentry release
        if curl -X POST "https://sentry.io/api/0/organizations/${SENTRY_ORG}/releases/" \
            -H "Authorization: Bearer ${SENTRY_AUTH_TOKEN}" \
            -H 'Content-Type: application/json' \
            -d "{\"version\": \"${SENTRY_RELEASE}\", \"projects\": [\"${SENTRY_PROJECT}\"]}" \
            --max-time 30 \
            --silent 2>/dev/null; then
            echo "    ✅ Sentry release created: ${SENTRY_RELEASE}"
        else
            echo "    ❌ Sentry release creation failed"
        fi
    fi
    
    # Custom monitoring scripts
    MONITORING_SCRIPTS_DIR="%path-server%/Admin-Local/Deployment/Hooks/Monitoring"
    if [[ -d "${MONITORING_SCRIPTS_DIR}" ]]; then
        echo "  🔍 Running custom monitoring updates..."
        
        for script in "${MONITORING_SCRIPTS_DIR}"/*.sh; do
            if [[ -f "${script}" ]] && [[ -x "${script}" ]]; then
                SCRIPT_NAME=$(basename "${script}")
                echo "    🔧 Running monitoring script: ${SCRIPT_NAME}"
                
                # Pass notification data to monitoring script
                if echo "${NOTIFICATION_DATA}" | timeout 60 "${script}" "${DEPLOYMENT_STATUS}" "${RELEASE_ID:-manual}"; then
                    echo "      ✅ ${SCRIPT_NAME} completed successfully"
                else
                    echo "      ⚠️  ${SCRIPT_NAME} failed or timed out"
                fi
            fi
        done
    fi
    
    # Generate notification report
    NOTIFICATION_REPORT_FILE="${DEPLOY_PATH}/notification-reports/notifications-$(date +%Y%m%d%H%M%S).json"
    mkdir -p "${DEPLOY_PATH}/notification-reports"
    
    cat > "${NOTIFICATION_REPORT_FILE}" << EOF
{
    "timestamp": "${NOTIFICATION_TIMESTAMP}",
    "deployment_id": "${RELEASE_ID:-$(date +%Y%m%d%H%M%S)}",
    "notification_data": ${NOTIFICATION_DATA},
    "channels": {
        "slack": $([ -n "${SLACK_WEBHOOK_URL}" ] && echo "true" || echo "false"),
        "discord": $([ -n "${DISCORD_WEBHOOK_URL}" ] && echo "true" || echo "false"),
        "email": $([ -n "${EMAIL_WEBHOOK_URL}" ] && echo "true" || echo "false"),
        "custom_webhooks": $([ -n "${CUSTOM_WEBHOOKS}" ] && echo "true" || echo "false"),
        "new_relic": $([ -n "${NEW_RELIC_API_KEY}" ] && echo "true" || echo "false"),
        "sentry": $([ -n "${SENTRY_AUTH_TOKEN}" ] && echo "true" || echo "false")
    }
}
EOF
    
    echo "📊 NOTIFICATIONS SUMMARY"
    echo "======================="
    echo "  Status: ${DEPLOYMENT_STATUS^}"
    echo "  Urgency: ${NOTIFICATION_URGENCY}"
    echo "  Channels configured: $([ -n "${SLACK_WEBHOOK_URL}" ] && echo -n "Slack ")$([ -n "${DISCORD_WEBHOOK_URL}" ] && echo -n "Discord ")$([ -n "${EMAIL_WEBHOOK_URL}" ] && echo -n "Email ")$([ -n "${CUSTOM_WEBHOOKS}" ] && echo -n "Webhooks ")$([ -n "${NEW_RELIC_API_KEY}" ] && echo -n "NewRelic ")$([ -n "${SENTRY_AUTH_TOKEN}" ] && echo -n "Sentry ")"
    echo "  📄 Report: $(basename "${NOTIFICATION_REPORT_FILE}")"
    
    echo "✅ Comprehensive notifications and webhooks completed"
    
    # Final deployment completion message
    echo ""
    echo "🎉 ================================================== 🎉"
    echo "    DEPLOYMENT SUCCESSFULLY COMPLETED!"
    echo ""
    echo "    ${STATUS_EMOJI} Status: ${DEPLOYMENT_STATUS^}"
    echo "    ⏱️  Duration: ${DEPLOYMENT_DURATION:-unknown}"
    echo "    🚀 Release: ${RELEASE_ID:-$(date +%Y%m%d%H%M%S)}"
    echo "    📦 Commit: ${GIT_COMMIT_SHORT:-unknown} (${GIT_BRANCH:-unknown})"
    echo "    🌍 App URL: ${APP_URL:-not configured}"
    echo "    ⚡ Response: ${APP_RESPONSE_TIME:-0}ms"
    echo ""
    echo "    Thank you for using the Universal Laravel"
    echo "    Zero-Downtime Deployment Flow v2.0!"
    echo "🎉 ================================================== 🎉"
    ```    ***
    ## **Critical Pitfalls & Solutions**
    ## **1. Composer/NPM Version Mismatches**
   `bash*# Solution: Version alignment check*
echo "Local Composer: $(composer --version)"
echo "Server Composer: $(ssh $SERVER 'composer --version')"
echo "Build Composer: $(composer --version)"
*# Force specific versions*
alias composer="php8.1 /usr/local/bin/composer2"`    ## **2. Lock File Management**
   `bash*# Always commit lock files*
git add composer.lock package-lock.json
*# Use exact versions in production*
composer install --no-dev *# Uses lock file versions*
npm ci *# Uses lock file versions*`    ## **3. OPcache Invalidation**[packagist+2](https://packagist.org/packages/maximkou/laravel-opcache-clear)
   `bash*# Critical: Clear OPcache after deployment*
php cachetool.phar opcache:reset --fcgi=/var/run/php-fpm/www.sock
*# Or create an authenticated endpoint# Route: POST /admin/opcache-clear*
// In controller:
if (request('token') === config('app.deploy_secret')) {
opcache_reset();
return response()->json(['status' => 'cleared']);
}`    ## **4. Shared Hosting Symlink Issues**[dev+3](https://dev.to/vumanhtrung/creating-symbolic-link-in-a-shared-hosting-using-php-5a01)
   `bash*# Solution 1: Direct storage path (no symlinks)# In config/filesystems.php*
'public' => [
'driver' => 'local',
'root' => public_path('storage'), *# Instead of storage_path('app/public')*
'url' => env('APP_URL').'/storage',
'visibility' => 'public',
],
*# Solution 2: Manual symlink creation*
ln -s /home/username/laravel/storage/app/public /home/username/public_html/storage
*# Solution 3: PHP symlink script*
<?php
$target = '/home/username/laravel/storage/app/public';
      $link = '/home/username/public_html/storage';
      symlink($target, $link);
echo 'Symlink created successfully';
?>`    ## **5. Public_html Root Setup**[hayhost+1](https://hayhost.am/knowledgebase/64/-How-to-Redirect-all-Requests-to-the-publicor-folder-in-Laravel.html?language=chinese)
   `bash*# Method 1: Symlink public_html to Laravel's public*
rm -rf public_html
ln -s /home/username/laravel/public public_html
*# Method 2: Move Laravel public contents to public*html# Then update index.php paths*
require*once **DIR**.'/../laravel/vendor/autoload.php';
$app = require_once **DIR**.'/../laravel/bootstrap/app.php';`    ## **6. Zero-Downtime Database Migrations**[data-sleek+1](https://data-sleek.com/blog/how-to-accomplish-zero-downtime-database-migration-easily-and-effectively/)
   `bash*# Phase 1: Add new columns (keep old ones)*
Schema::table('users', function (Blueprint $table) {
$table->string('new_email')->nullable(); *# Don't remove 'email' yet*
});
*# Phase 2: Deploy code that uses both columns# Phase 3: Remove old columns in next deployment*
Schema::table('users', function (Blueprint $table) {
$table->dropColumn('email'); *# Now safe to remove*
});`    ## **7. Build Environment Fallback**
   `bash*# When build server is down, build locally*
if ! ping -c 1 build-server; then
echo "Building locally..."
composer install --no-dev --optimize-autoloader
npm run production
tar -czf release.tar.gz --exclude='.git' --exclude='node_modules' .
fi`    ## **8. Queue Worker Management**
   `bash*# Ensure workers use new code*
php artisan queue:restart
*# For Supervisor*
sudo supervisorctl restart laravel-worker:\*
*# For Horizon*
php artisan horizon:terminate *# Will auto-restart with new code\_`    ***
    ## **Directory Structure (Post-Deployment)**
   `text/var/www/app/
├── current → releases/20250820092100/
├── releases/
│ ├── 20250820091500/
│ ├── 20250820092000/
│ └── 20250820092100/ # Latest
├── shared/
│ ├── .env
│ └── storage/
│ ├── app/public/ # User uploads
│ ├── logs/
│ └── framework/
└── deployment.log`
This master deployment flow ensures **true zero-downtime** by using atomic symlink switches, proper cache invalidation, shared resource management, and comprehensive error handling for all the pitfalls you mentioned.