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
     8. **Step 03.3: Universal Build System Detection**
         1. **Purpose:** Detect and configure support for all major Laravel build systems (Vite, Laravel Mix, Webpack) to ensure universal applicability across different project setups.
         2. **When:** After comprehensive environment analysis and before cloning repository.
         3. **Action:**
             1. Create universal build system detection script.
                 
                 ```bash
                 cat > Admin-Local/Deployment/Scripts/detect-build-system.sh << 'EOF'
                 #!/bin/bash
                 
                 # Load deployment variables
                 source %path-localMachine%/Admin-Local/Deployment/Scripts/load-variables.sh
                 
                 echo "╔══════════════════════════════════════════════════════════╗"
                 echo "║     Universal Build System Detection v2.0               ║"
                 echo "╚══════════════════════════════════════════════════════════╝"
                 
                 # Create build system detection report
                 BUILD_DETECTION="Admin-Local/Deployment/Configs/build-detection.env"
                 REPORT="Admin-Local/Deployment/Logs/build-system-analysis-$(date +%Y%m%d-%H%M%S).md"
                 
                 echo "# Build System Detection Report" > $REPORT
                 echo "Generated: $(date)" >> $REPORT
                 echo "Project: $PROJECT_NAME" >> $REPORT
                 echo "" >> $REPORT
                 
                 # Initialize variables
                 BUILD_SYSTEM="unknown"
                 BUILD_CONFIG_FILE=""
                 FRONTEND_FRAMEWORK="unknown"
                 
                 # 1. Detect Build System
                 echo "## Build System Detection" >> $REPORT
                 
                 if [[ -f "vite.config.js" ]] || [[ -f "vite.config.ts" ]]; then
                     BUILD_SYSTEM="vite"
                     BUILD_CONFIG_FILE=$(find . -maxdepth 1 -name "vite.config.*" | head -1)
                     echo "- ✅ **Vite** detected: $BUILD_CONFIG_FILE" >> $REPORT
                 elif [[ -f "webpack.mix.js" ]]; then
                     BUILD_SYSTEM="laravel-mix"
                     BUILD_CONFIG_FILE="webpack.mix.js"
                     echo "- ✅ **Laravel Mix** detected: $BUILD_CONFIG_FILE" >> $REPORT
                 elif [[ -f "webpack.config.js" ]]; then
                     BUILD_SYSTEM="webpack"
                     BUILD_CONFIG_FILE="webpack.config.js"
                     echo "- ✅ **Webpack** detected: $BUILD_CONFIG_FILE" >> $REPORT
                 else
                     echo "- ⚠️ **No build system detected** - will use npm defaults" >> $REPORT
                 fi
                 
                 # 2. Detect Frontend Framework
                 echo "" >> $REPORT
                 echo "## Frontend Framework Detection" >> $REPORT
                 
                 if [[ -f "package.json" ]]; then
                     # Check for Vue.js
                     if grep -q '"vue"' package.json; then
                         FRONTEND_FRAMEWORK="vue"
                         echo "- ✅ **Vue.js** detected" >> $REPORT
                     # Check for React
                     elif grep -q '"react"' package.json; then
                         FRONTEND_FRAMEWORK="react"
                         echo "- ✅ **React** detected" >> $REPORT
                     # Check for Inertia.js
                     elif grep -q '"@inertiajs"' package.json; then
                         FRONTEND_FRAMEWORK="inertia"
                         echo "- ✅ **Inertia.js** detected" >> $REPORT
                     # Default to Blade
                     else
                         FRONTEND_FRAMEWORK="blade"
                         echo "- ✅ **Blade** (default Laravel templating)" >> $REPORT
                     fi
                 else
                     FRONTEND_FRAMEWORK="blade"
                     echo "- ✅ **Blade** (no package.json found)" >> $REPORT
                 fi
                 
                 # 3. Generate Build Commands
                 echo "" >> $REPORT
                 echo "## Universal Build Commands" >> $REPORT
                 
                 case "$BUILD_SYSTEM" in
                     "vite")
                         BUILD_DEV_CMD="npm run dev"
                         BUILD_PROD_CMD="npm run build"
                         BUILD_WATCH_CMD="npm run dev"
                         echo "- Development: \`$BUILD_DEV_CMD\`" >> $REPORT
                         echo "- Production: \`$BUILD_PROD_CMD\`" >> $REPORT
                         echo "- Watch: \`$BUILD_WATCH_CMD\`" >> $REPORT
                         ;;
                     "laravel-mix")
                         BUILD_DEV_CMD="npm run development"
                         BUILD_PROD_CMD="npm run production"
                         BUILD_WATCH_CMD="npm run watch"
                         echo "- Development: \`$BUILD_DEV_CMD\`" >> $REPORT
                         echo "- Production: \`$BUILD_PROD_CMD\`" >> $REPORT
                         echo "- Watch: \`$BUILD_WATCH_CMD\`" >> $REPORT
                         ;;
                     "webpack")
                         BUILD_DEV_CMD="npm run dev"
                         BUILD_PROD_CMD="npm run prod"
                         BUILD_WATCH_CMD="npm run watch"
                         echo "- Development: \`$BUILD_DEV_CMD\`" >> $REPORT
                         echo "- Production: \`$BUILD_PROD_CMD\`" >> $REPORT
                         echo "- Watch: \`$BUILD_WATCH_CMD\`" >> $REPORT
                         ;;
                     *)
                         BUILD_DEV_CMD="npm run dev"
                         BUILD_PROD_CMD="npm run build"
                         BUILD_WATCH_CMD="npm run watch"
                         echo "- Development: \`$BUILD_DEV_CMD\` (default)" >> $REPORT
                         echo "- Production: \`$BUILD_PROD_CMD\` (default)" >> $REPORT
                         echo "- Watch: \`$BUILD_WATCH_CMD\` (default)" >> $REPORT
                         ;;
                 esac
                 
                 # 4. Save configuration
                 cat > $BUILD_DETECTION << BUILD_ENV_EOF
                 # Universal Build System Configuration
                 # Generated: $(date)
                 BUILD_SYSTEM="$BUILD_SYSTEM"
                 BUILD_CONFIG_FILE="$BUILD_CONFIG_FILE"
                 FRONTEND_FRAMEWORK="$FRONTEND_FRAMEWORK"
                 BUILD_DEV_CMD="$BUILD_DEV_CMD"
                 BUILD_PROD_CMD="$BUILD_PROD_CMD"
                 BUILD_WATCH_CMD="$BUILD_WATCH_CMD"
                 BUILD_ENV_EOF
                 
                 # 5. Generate recommendations
                 echo "" >> $REPORT
                 echo "## 🎯 Recommendations" >> $REPORT
                 
                 if [[ "$BUILD_SYSTEM" == "unknown" ]]; then
                     echo "### Setup Build System" >> $REPORT
                     echo "Consider adding a build system for better asset management:" >> $REPORT
                     echo "- **Vite** (recommended for new projects): \`npm install vite laravel-vite-plugin --save-dev\`" >> $REPORT
                     echo "- **Laravel Mix** (legacy support): \`npm install laravel-mix --save-dev\`" >> $REPORT
                 fi
                 
                 if [[ "$FRONTEND_FRAMEWORK" == "blade" ]] && [[ -f "package.json" ]]; then
                     echo "### Consider Frontend Framework" >> $REPORT
                     echo "You have npm dependencies but using Blade. Consider:" >> $REPORT
                     echo "- **Vue.js**: \`npm install vue @vitejs/plugin-vue\`" >> $REPORT
                     echo "- **React**: \`npm install react react-dom @vitejs/plugin-react\`" >> $REPORT
                     echo "- **Inertia.js**: \`composer require inertiajs/inertia-laravel\`" >> $REPORT
                 fi
                 
                 echo "✅ Build system detection complete!"
                 echo "📋 Configuration saved to: $BUILD_DETECTION"
                 echo "📋 Full report saved to: $REPORT"
                 echo ""
                 echo "🔧 Detected Configuration:"
                 echo "   Build System: $BUILD_SYSTEM"
                 echo "   Frontend: $FRONTEND_FRAMEWORK"
                 echo "   Production Command: $BUILD_PROD_CMD"
                 EOF
                 
                 chmod +x Admin-Local/Deployment/Scripts/detect-build-system.sh
                 
                 ```
                 
             2. Run the universal build system detection.
                 
                 ```bash
                 %path-localMachine%/Admin-Local/Deployment/Scripts/detect-build-system.sh
                 
                 ```
                 
             3. Review the build system analysis report and verify the detected configuration matches your project setup.
     9. **Step 04: Clone GitHub Repository**
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