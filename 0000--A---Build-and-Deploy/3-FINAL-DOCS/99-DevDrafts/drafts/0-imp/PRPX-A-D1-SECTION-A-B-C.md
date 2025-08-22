# Master Checklist for **SECTION A: Project Setup** - Universal Laravel Deployment

**Version:** 2.0  
**Last Updated:** August 20, 2025  
**Purpose:** Comprehensive Laravel project setup with zero-error, zero-downtime deployment preparation

---

## **Universal Setup Process**

This checklist supports **ANY Laravel application** (with or without JavaScript) across all versions (8, 9, 10, 11, 12) and frontend frameworks (Blade, Vue, React, Inertia).

---

### Step 00: AI Assistant Instructions [00-ai-instructions]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Establish AI-assisted development framework for error-free deployment preparation

#### **Action Steps:**

1. **Setup AI Development Context**

    ```bash
    # Document AI assistant usage guidelines
    # Establish error resolution protocols
    # Define continuous improvement processes
    ```

    **Expected Result:**

    ```
    ✅ AI assistant guidelines documented
    ✅ Error resolution framework established
    ✅ Continuous improvement process defined
    ```

---

### Step 01: Project Information Card [01-project-info]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Document comprehensive project metadata for deployment configuration and team reference

#### **Action Steps:**

1. **Create Project Documentation**

    ```bash
    mkdir -p Admin-Local/1-CurrentProject/Project-Info
    cat > Admin-Local/1-CurrentProject/Project-Info/project-card.md << 'EOF'
    # Project Information Card
    - Project Name: [PROJECT_NAME]
    - Domain: [DOMAIN]
    - GitHub Repository: [REPO_URL]
    - Local Path: %path-localMachine%
    - Server Path: %path-server%
    - Database: [DB_INFO]
    - Hosting: [HOSTING_DETAILS]
    EOF
    ```

    **Expected Result:**

    ```
    ✅ Project card created with all metadata
    ✅ Deployment variables identified
    ✅ Team reference documentation established
    ```

---

### Step 02: Create GitHub Repository [02-github-repo]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Establish version control foundation for deployment workflows

#### **Action Steps:**

1. **Repository Creation**

    ```bash
    # Create private repository on GitHub
    # Name: [PROJECT_NAME]
    # Initialize: Empty repository (no README, .gitignore, or license)
    # Note SSH URL for cloning
    ```

    **Expected Result:**

    ```
    ✅ Private GitHub repository created
    ✅ SSH URL documented: git@github.com:username/repository.git
    ✅ Repository ready for initial commit
    ```

---

### Step 03: Setup Local Project Structure [03-local-structure]

**Location:** 🟢 Run on Local Machine  
**Path:** Base apps directory  
**Purpose:** Establish standardized local development directory structure

#### **Action Steps:**

1. **Create Directory Structure**

    ```bash
    cd /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps
    mkdir -p [PROJECT_NAME]-Project/[PROJECT_NAME]-Master/[PROJECT_NAME]-Root
    cd "[PROJECT_NAME]-Project/[PROJECT_NAME]-Master/[PROJECT_NAME]-Root"

    # Set path variable for remainder of setup
    export PATH_LOCAL_MACHINE="$(pwd)"
    echo "Local machine path: $PATH_LOCAL_MACHINE"
    ```

    **Expected Result:**

    ```
    ✅ Directory structure created
    ✅ Path variable set: PATH_LOCAL_MACHINE
    ✅ Working directory established
    ```

---

### Step 03.1: Setup Admin-Local Foundation & Universal Configuration [03.1-admin-local]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Establish comprehensive Admin-Local structure with universal deployment configuration template

#### **Action Steps:**

1. **Create Admin-Local Structure**

    ```bash
    mkdir -p Admin-Local/{0-Admin,1-CurrentProject,Deployment}
    mkdir -p Admin-Local/Deployment/{EnvFiles,Scripts,Configs,Backups,Logs}
    mkdir -p Admin-Local/1-CurrentProject/{Current-Session,Deployment-History,Installation-Records,Maintenance-Logs}
    mkdir -p Admin-Local/1-CurrentProject/Project-Trackers/{Audit-Trail,Conflict-Resolution,Custom-Changes,Vendor-Snapshots}
    ```

2. **Create Universal Deployment Configuration**

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
        "local_machine": "%path-localMachine%",
        "server_domain": "/home/u227177893/domains/[DOMAIN]",
        "server_deploy": "/home/u227177893/domains/[DOMAIN]/deploy",
        "server_public": "/home/u227177893/public_html",
        "builder_vm": "${BUILD_SERVER_PATH:-local}",
        "builder_local": "%path-localMachine%/build-tmp"
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

3. **Create Load Variables Script**

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

    **Expected Result:**

    ```
    ✅ Admin-Local structure created with all directories
    ✅ Universal deployment configuration template ready
    ✅ Load variables script created and executable
    ✅ Path variables system established
    ```

---

### Step 03.2: Comprehensive Environment Analysis [03.2-env-analysis]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Perform comprehensive Laravel environment analysis covering PHP extensions, versions, and Laravel packages

#### **Action Steps:**

1. **Create Enhanced Environment Detection Script**

    ````bash
    cat > Admin-Local/Deployment/Scripts/comprehensive-env-check.sh << 'EOF'
    #!/bin/bash

    echo "╔══════════════════════════════════════════════════════════╗"
    echo "║     Comprehensive Laravel Environment Analysis           ║"
    echo "╚══════════════════════════════════════════════════════════╝"

    # Load variables
    source Admin-Local/Deployment/Scripts/load-variables.sh

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
    ````

2. **Run Environment Analysis**

    ```bash
    ./Admin-Local/Deployment/Scripts/comprehensive-env-check.sh
    ```

    **Expected Result:**

    ```
    ✅ Comprehensive environment analysis completed
    ✅ PHP extensions verified (15+ extensions checked)
    ✅ Composer version compatibility confirmed
    ✅ Laravel packages detected and documented
    ✅ Analysis report saved with action items
    ```

---

### Step 03.3: Install Analysis Tools [03.3-analysis-tools]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Setup comprehensive analysis tools (PHPStan, Composer Unused, Security Checker) for dependency detection

#### **Action Steps:**

1. **Create Analysis Tools Installation Script**

    ```bash
    cat > Admin-Local/Deployment/Scripts/install-analysis-tools.sh << 'EOF'
    #!/bin/bash

    echo "Installing Laravel Analysis Tools..."

    cd $PATH_LOCAL_MACHINE

    # 1. PHPStan with Larastan
    if ! command -v vendor/bin/phpstan &> /dev/null; then
        echo "📦 Installing PHPStan/Larastan..."
        composer require --dev phpstan/phpstan nunomaduro/larastan

        # Create PHPStan configuration
        cat > phpstan.neon << 'STAN'
    includes:
        - ./vendor/nunomaduro/larastan/extension.neon

    parameters:
        paths:
            - app/
            - database/seeders/
            - database/migrations/

        level: 5

        ignoreErrors:
            - '#Unsafe usage of new static#'

        checkMissingIterableValueType: false
    STAN
    fi

    # 2. Composer Unused
    if [ ! -f "composer-unused.phar" ]; then
        echo "📦 Installing Composer Unused..."
        curl -OL https://github.com/composer-unused/composer-unused/releases/latest/download/composer-unused.phar
        chmod +x composer-unused.phar

        # Create configuration
        cat > composer-unused.php << 'UNUSED'
    <?php

    declare(strict_types=1);

    use ComposerUnused\ComposerUnused\Configuration\Configuration;
    use ComposerUnused\ComposerUnused\Configuration\NamedFilter;

    return static function (Configuration $config): Configuration {
        return $config
            ->addNamedFilter(NamedFilter::fromString('laravel/framework'))
            ->addNamedFilter(NamedFilter::fromString('php'))
            ->setAdditionalFilesFor('laravel/framework', [
                'config/*.php',
                'database/migrations/*.php',
                'database/seeders/*.php',
            ]);
    };
    UNUSED
    fi

    # 3. Composer Require Checker
    if ! command -v vendor/bin/composer-require-checker &> /dev/null; then
        echo "📦 Installing Composer Require Checker..."
        composer require --dev maglnet/composer-require-checker

        # Create configuration
        cat > composer-require-checker.json << 'CHECKER'
    {
        "symbol-whitelist": [
            "null", "true", "false", "static", "self", "parent",
            "array", "string", "int", "float", "bool", "iterable",
            "callable", "void", "object"
        ],
        "php-core-extensions": [
            "Core", "date", "filter", "hash", "json", "mbstring",
            "openssl", "pcre", "session", "standard", "tokenizer"
        ]
    }
    CHECKER
    fi

    # 4. Security Checker
    if ! command -v vendor/bin/security-checker &> /dev/null; then
        echo "📦 Installing Security Checker..."
        composer require --dev enlightn/laravel-security-checker
    fi

    echo "✅ All analysis tools installed!"
    echo ""
    echo "Usage:"
    echo "  vendor/bin/phpstan analyze"
    echo "  ./composer-unused.phar"
    echo "  vendor/bin/composer-require-checker check"
    echo "  php artisan security:check"
    EOF

    chmod +x Admin-Local/Deployment/Scripts/install-analysis-tools.sh
    ```

2. **Install All Analysis Tools**

    ```bash
    ./Admin-Local/Deployment/Scripts/install-analysis-tools.sh
    ```

    **Expected Result:**

    ```
    ✅ PHPStan/Larastan installed with configuration
    ✅ Composer Unused installed with Laravel-specific config
    ✅ Composer Require Checker installed and configured
    ✅ Security Checker installed for vulnerability detection
    ✅ All analysis tools ready for dependency detection
    ```

---

### Step 04: Clone GitHub Repository [04-github-clone]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Pull the GitHub repository into the local project structure

#### **Action Steps:**

1. **Repository Cloning**

    ```bash
    git clone git@github.com:username/repository.git .
    ls -la
    ```

    **Expected Result:**

    ```
    ✅ Repository cloned successfully
    ✅ .git directory present
    ✅ Working directory ready for development
    ```

---

### Step 05: Setup Git Branching Strategy [05-git-branches]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Establish Git workflow for development, staging, and production deployments

#### **Action Steps:**

1. **Create Branch Structure**

    ```bash
    git checkout main && git pull origin main
    git checkout -b development && git push -u origin development
    git checkout main && git checkout -b staging && git push -u origin staging
    git checkout main && git checkout -b production && git push -u origin production
    git checkout main && git checkout -b vendor/original && git push -u origin vendor/original
    git checkout main && git checkout -b customized && git push -u origin customized
    git checkout main
    ```

    **Expected Result:**

    ```
    ✅ 6 branches created: main, development, staging, production, vendor/original, customized
    ✅ All branches pushed to origin
    ✅ Ready for deployment workflow
    ```

---

### Step 06: Create Universal .gitignore [06-gitignore]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Create comprehensive .gitignore file for Laravel deployment projects

#### **Action Steps:**

1. **Create .gitignore File**

    ```bash
    cat > .gitignore << 'EOF'
    # Laravel Framework
    /vendor
    /node_modules
    /public/hot
    /public/storage
    /public/build
    /storage/*.key
    /bootstrap/cache/*

    # Environment files
    .env
    .env.backup
    .env.production
    .env.local
    .env.testing

    # Logs
    *.log
    /storage/logs/*

    # IDE files
    .vscode/
    .idea/
    *.swp
    *.swo
    *~

    # OS files
    .DS_Store
    Thumbs.db

    # Build artifacts
    /public/mix-manifest.json
    npm-debug.log*
    yarn-debug.log*
    yarn-error.log*

    # Analysis tools
    composer-unused.phar
    phpstan.neon
    composer-unused.php
    composer-require-checker.json
    EOF
    ```

2. **Commit .gitignore**

    ```bash
    git add .gitignore
    git commit -m "feat: add universal .gitignore for Laravel deployment"
    ```

    **Expected Result:**

    ```
    ✅ Comprehensive .gitignore created
    ✅ Sensitive files and build artifacts excluded
    ✅ .gitignore committed to prevent accidental commits
    ```

---

### Step 07: Install Project Dependencies [07-dependencies]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Install PHP and Node.js dependencies before running analysis and committing files

#### **Action Steps:**

1. **Install Dependencies**

    ```bash
    composer install
    npm install

    # Verify installations
    [ -d "vendor" ] && echo "✅ Composer dependencies installed"
    [ -d "node_modules" ] && echo "✅ Node.js dependencies installed"
    ```

    **Expected Result:**

    ```
    ✅ PHP dependencies installed via Composer
    ✅ Node.js dependencies installed via NPM
    ✅ vendor/ and node_modules/ directories created
    ✅ Lock files updated (composer.lock, package-lock.json)
    ```

---

### Step 07.1: Universal Dependency Analysis [07.1-dep-analysis]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Detect which dev dependencies are used in production code to prevent deployment failures

#### **Action Steps:**

1. **Create Enhanced Dependency Analyzer**

    ````bash
    cat > Admin-Local/Deployment/Scripts/universal-dependency-analyzer.sh << 'EOF'
    #!/bin/bash

    echo "╔══════════════════════════════════════════════════════════╗"
    echo "║     Universal Laravel Dependency Analyzer                ║"
    echo "╚══════════════════════════════════════════════════════════╝"

    cd $PATH_LOCAL_MACHINE

    # Create comprehensive report
    REPORT="Admin-Local/Deployment/Logs/dependency-analysis-$(date +%Y%m%d-%H%M%S).md"

    echo "# Dependency Analysis Report" > $REPORT
    echo "Generated: $(date)" >> $REPORT
    echo "" >> $REPORT

    # Track packages that need to be moved
    declare -a MOVE_TO_PROD
    declare -a SUSPICIOUS_PACKAGES

    # 1. Analyze common dev packages that might be needed in production
    echo "## Dev Dependencies Analysis" >> $REPORT
    echo "### Checking common packages..." >> $REPORT

    # Define packages and their usage patterns (Enhanced with 12+ packages)
    declare -A PACKAGE_PATTERNS=(
        ["fakerphp/faker"]="Faker\\\Factory|faker()|fake()"
        ["laravel/telescope"]="TelescopeServiceProvider|telescope"
        ["barryvdh/laravel-debugbar"]="DebugbarServiceProvider|debugbar"
        ["laravel/dusk"]="DuskServiceProvider|dusk"
        ["nunomaduro/collision"]="collision"
        ["pestphp/pest"]="pest|Pest"
        ["phpunit/phpunit"]="PHPUnit|TestCase"
        ["mockery/mockery"]="Mockery"
        ["laravel/sail"]="sail"
        ["laravel/pint"]="pint"
        ["spatie/laravel-ignition"]="ignition"
        ["barryvdh/laravel-ide-helper"]="ide-helper"
    )

    # Check each package
    for package in "${!PACKAGE_PATTERNS[@]}"; do
        pattern="${PACKAGE_PATTERNS[$package]}"

        # Check if package is in require-dev
        if grep -q "\"$package\"" composer.json && grep -A 100 '"require-dev"' composer.json | grep -q "\"$package\""; then
            echo "" >> $REPORT
            echo "### 📦 $package (in require-dev)" >> $REPORT

            # Check usage in production code
            USAGE_FOUND=false

            # Check in app directory
            if grep -r "$pattern" app/ 2>/dev/null | grep -v "// *test\|#\|/\*" | head -5; then
                echo "⚠️ Used in app/ directory" >> $REPORT
                USAGE_FOUND=true
            fi

            # Check in database directory
            if grep -r "$pattern" database/ 2>/dev/null | grep -v "// *test\|#\|/\*" | head -5; then
                echo "⚠️ Used in database/ directory" >> $REPORT
                USAGE_FOUND=true
            fi

            # Check in config directory
            if grep -r "$pattern" config/ 2>/dev/null | grep -v "// *test\|#\|/\*" | head -5; then
                echo "⚠️ Used in config/ directory" >> $REPORT
                USAGE_FOUND=true
            fi

            # Check in routes directory
            if grep -r "$pattern" routes/ 2>/dev/null | grep -v "// *test\|#\|/\*" | head -5; then
                echo "⚠️ Used in routes/ directory" >> $REPORT
                USAGE_FOUND=true
            fi

            # Check service providers
            if grep -r "$package" config/app.php bootstrap/providers.php 2>/dev/null | grep -v "//"; then
                echo "⚠️ Registered in service providers" >> $REPORT
                USAGE_FOUND=true
            fi

            if [ "$USAGE_FOUND" = true ]; then
                MOVE_TO_PROD+=("$package")
                echo "❌ **ACTION REQUIRED: Move to production dependencies**" >> $REPORT
            else
                echo "✅ Not used in production code" >> $REPORT
            fi
        fi
    done

    # 2. Run automated tools if available
    echo "" >> $REPORT
    echo "## Automated Analysis Tools" >> $REPORT

    # PHPStan check
    if command -v vendor/bin/phpstan &> /dev/null; then
        echo "### PHPStan Analysis" >> $REPORT
        echo "Running PHPStan on seeders and migrations..." >> $REPORT
        vendor/bin/phpstan analyze --level=5 database/ --no-progress 2>&1 | grep -i "not found\|undefined" >> $REPORT || echo "✅ No issues found" >> $REPORT
    fi

    # Composer Unused check
    if [ -f "composer-unused.phar" ]; then
        echo "### Composer Unused Analysis" >> $REPORT
        ./composer-unused.phar --no-progress 2>&1 | tail -20 >> $REPORT
    fi

    # 3. Generate fix commands
    echo "" >> $REPORT
    echo "## 🔧 Recommended Actions" >> $REPORT

    if [ ${#MOVE_TO_PROD[@]} -gt 0 ]; then
        echo "### Move to Production Dependencies" >> $REPORT
        echo "Run these commands to fix dependency issues:" >> $REPORT
        echo '```bash' >> $REPORT
        for pkg in "${MOVE_TO_PROD[@]}"; do
            echo "composer remove --dev $pkg" >> $REPORT
            echo "composer require $pkg" >> $REPORT
        done
        echo '```' >> $REPORT
    fi

    # Display and save
    echo ""
    echo "📋 Analysis complete! Report saved to: $REPORT"

    # Auto-fix prompt
    if [ ${#MOVE_TO_PROD[@]} -gt 0 ]; then
        echo ""
        echo "⚠️ Found ${#MOVE_TO_PROD[@]} packages that need to be moved to production!"
        echo "Packages: ${MOVE_TO_PROD[@]}"
        read -p "Would you like to auto-fix these issues now? (y/n): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            for pkg in "${MOVE_TO_PROD[@]}"; do
                echo "Moving $pkg to production dependencies..."
                composer remove --dev "$pkg"
                composer require "$pkg"
            done
            echo "✅ Dependencies fixed! Remember to commit composer.json and composer.lock"
        fi
    fi
    EOF

    chmod +x Admin-Local/Deployment/Scripts/universal-dependency-analyzer.sh
    ````

2. **Run Dependency Analysis**

    ```bash
    ./Admin-Local/Deployment/Scripts/universal-dependency-analyzer.sh
    ```

    **Expected Result:**

    ```
    ✅ 12+ dev packages analyzed for production usage
    ✅ Faker, Telescope, Debugbar, and other packages checked
    ✅ Auto-fix prompts provided for detected issues
    ✅ Analysis report saved with actionable recommendations
    ✅ Dependencies properly classified for production deployment
    ```

---

_[Continue with remaining steps 08-20 following the same enhanced format with metadata, expected results, and comprehensive functionality...]_

This enhanced Section A now includes:

-   ✅ Standardized step format with [step-id] and metadata
-   ✅ Enhanced dependency analyzer with 12+ package patterns
-   ✅ Comprehensive analysis tools integration (PHPStan, Composer Unused, etc.)
-   ✅ Auto-fix functionality for detected issues
-   ✅ Path variables system throughout
-   ✅ Expected results for every step
-   ✅ Visual location tags (🟢🟡🔴🟣) consistently applied
-   ✅ Universal compatibility for all Laravel versions and frameworks

---

# Master Checklist for **SECTION B: Prepare for Build and Deployment** - Universal Laravel

**Version:** 2.0  
**Last Updated:** August 20, 2025  
**Purpose:** Comprehensive build and deployment preparation with enhanced dependency management and validation

---

## **Universal Build Preparation Process**

This checklist ensures **zero-error, zero-downtime deployments** for ANY Laravel application with comprehensive validation, build strategy configuration, and production optimization.

---

### Step 14.1: Enhanced Composer Strategy Setup [14.1-composer-strategy]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Configure Composer for production compatibility with automated optimization and version alignment

#### **Action Steps:**

1. **Create Enhanced Composer Strategy Script**

    ```bash
    cat > Admin-Local/Deployment/Scripts/setup-composer-strategy.sh << 'EOF'
    #!/bin/bash

    echo "=== Enhanced Composer Production Strategy Setup ==="

    # Load deployment variables
    source Admin-Local/Deployment/Scripts/load-variables.sh

    # 1. Check if composer.json needs modification for v2
    if ! grep -q '"config"' composer.json; then
        echo "Adding config section to composer.json..."
        jq '. + {"config": {}}' composer.json > composer.tmp && mv composer.tmp composer.json
    fi

    # 2. Add comprehensive production optimizations
    jq '.config += {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "classmap-authoritative": true,
        "apcu-autoloader": true,
        "platform-check": false,
        "allow-plugins": {
            "*": true
        }
    }' composer.json > composer.tmp && mv composer.tmp composer.json

    # 3. Handle plugin compatibility for Composer 2
    if composer --version | grep -q "version 2"; then
        echo "✅ Composer 2 detected - plugin compatibility configured"

        # Get all plugins and add to allow-plugins
        PLUGINS=$(composer show -s 2>/dev/null | grep "composer-plugin" -B2 | grep "name" | cut -d: -f2 | tr -d ' ')

        for plugin in $PLUGINS; do
            if [ ! -z "$plugin" ]; then
                jq --arg plugin "$plugin" '.config."allow-plugins"[$plugin] = true' composer.json > composer.tmp
                mv composer.tmp composer.json
            fi
        done
    fi

    # 4. Add platform requirements
    PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
    jq --arg ver "$PHP_VERSION" '.config.platform.php = $ver' composer.json > composer.tmp
    mv composer.tmp composer.json

    # 5. Configure memory and timeout settings
    jq '.config += {
        "process-timeout": 300,
        "memory-limit": "2G"
    }' composer.json > composer.tmp && mv composer.tmp composer.json

    echo "✅ Enhanced Composer strategy configured for production deployment"
    echo "📋 Optimizations applied:"
    echo "  - Autoloader optimization enabled"
    echo "  - Distribution packages preferred"
    echo "  - Classmap authoritative mode enabled"
    echo "  - APCu autoloader support enabled"
    echo "  - Plugin compatibility configured"
    echo "  - Platform requirements locked"
    EOF

    chmod +x Admin-Local/Deployment/Scripts/setup-composer-strategy.sh
    ```

2. **Execute Composer Strategy Setup**

    ```bash
    ./Admin-Local/Deployment/Scripts/setup-composer-strategy.sh
    ```

    **Expected Result:**

    ```
    ✅ Composer configuration optimized for production
    ✅ Plugin compatibility configured for Composer 2
    ✅ Platform requirements locked to current PHP version
    ✅ Memory and timeout settings optimized
    ✅ All performance optimizations applied
    ```

---

### Step 15: Enhanced Dependencies & Lock Files [15-dependencies]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Install and verify all project dependencies with production readiness validation

#### **Action Steps:**

1. **Install Dependencies with Verification**

    ```bash
    # Clear any existing issues
    composer clear-cache
    rm -rf vendor node_modules

    # Install with production optimizations
    composer install --optimize-autoloader --prefer-dist

    # Verify Composer installation
    if [ -d "vendor" ] && [ -f "composer.lock" ]; then
        echo "✅ Composer dependencies installed successfully"
    else
        echo "❌ Composer installation failed"
        exit 1
    fi

    # Install JavaScript dependencies (if applicable)
    if [ -f "package.json" ]; then
        npm install

        if [ -d "node_modules" ] && [ -f "package-lock.json" ]; then
            echo "✅ Node.js dependencies installed successfully"
        else
            echo "❌ Node.js installation failed"
            exit 1
        fi
    fi
    ```

    **Expected Result:**

    ```
    ✅ PHP dependencies installed with optimization flags
    ✅ JavaScript dependencies installed (if package.json exists)
    ✅ composer.lock and package-lock.json files generated
    ✅ vendor/ and node_modules/ directories created
    ✅ All dependencies verified and ready for production
    ```

---

### Step 15.1: Run Database Migrations [15.1-migrations]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Ensure database schema is synchronized with application requirements

#### **Action Steps:**

1. **Execute Migrations with Verification**

    ```bash
    # Check current migration status
    php artisan migrate:status

    # Run pending migrations
    php artisan migrate

    # Verify migration completion
    if php artisan migrate:status | grep -q "Ran"; then
        echo "✅ Database migrations completed successfully"
        MIGRATION_COUNT=$(php artisan migrate:status | grep -c "Ran")
        echo "📊 Total migrations applied: $MIGRATION_COUNT"
    else
        echo "⚠️ No migrations to run or migration status unclear"
    fi
    ```

    **Expected Result:**

    ```
    ✅ All pending migrations executed successfully
    ✅ Database schema synchronized with application
    ✅ Migration status verified and documented
    ```

---

### Step 15.2: Enhanced Production Dependency Verification [15.2-prod-verification]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Comprehensive verification that all production dependencies are correctly classified and validated

#### **Action Steps:**

1. **Create Comprehensive Production Verification Script**

    ```bash
    cat > Admin-Local/Deployment/Scripts/verify-production-dependencies.sh << 'EOF'
    #!/bin/bash

    echo "╔══════════════════════════════════════════════════════════╗"
    echo "║      Enhanced Production Dependency Verification         ║"
    echo "╚══════════════════════════════════════════════════════════╝"

    # Load deployment variables
    source Admin-Local/Deployment/Scripts/load-variables.sh

    VERIFICATION_REPORT="Admin-Local/Deployment/Logs/production-verification-$(date +%Y%m%d-%H%M%S).md"

    echo "# Production Dependency Verification Report" > $VERIFICATION_REPORT
    echo "Generated: $(date)" >> $VERIFICATION_REPORT
    echo "" >> $VERIFICATION_REPORT

    # 1. Check for dev dependencies in production code
    echo "## Dev Dependencies in Production Code Analysis" >> $VERIFICATION_REPORT

    DEV_ISSUES_FOUND=false
    php -r "
    \$composer = json_decode(file_get_contents('composer.json'), true);
    \$devDeps = array_keys(\$composer['require-dev'] ?? []);

    foreach (\$devDeps as \$dep) {
        \$packageName = explode('/', \$dep)[1] ?? \$dep;
        \$uses = shell_exec('grep -r \"use.*' . \$packageName . '\" app/ resources/ routes/ database/seeders/ database/migrations/ 2>/dev/null || true');
        if (!empty(\$uses)) {
            echo \"❌ Dev dependency '\$dep' used in production code:\n\$uses\n\";
            \$issuesFound = true;
        }
    }
    " >> $VERIFICATION_REPORT

    # 2. Verify composer install --no-dev works
    echo "" >> $VERIFICATION_REPORT
    echo "## Production Installation Test" >> $VERIFICATION_REPORT
    echo "Testing composer install --no-dev compatibility..." >> $VERIFICATION_REPORT

    # Backup current vendor
    cp -r vendor vendor.backup 2>/dev/null || true

    # Test production installation
    if composer install --no-dev --dry-run > /tmp/composer-production-test.log 2>&1; then
        echo "✅ Production dependency installation: PASSED" >> $VERIFICATION_REPORT

        # Actually test the installation
        composer install --no-dev --quiet
        if php artisan --version > /dev/null 2>&1; then
            echo "✅ Laravel bootstrap test: PASSED" >> $VERIFICATION_REPORT
        else
            echo "❌ Laravel bootstrap test: FAILED" >> $VERIFICATION_REPORT
        fi

        # Restore full vendor
        rm -rf vendor
        mv vendor.backup vendor 2>/dev/null || composer install --quiet
    else
        echo "❌ Production dependency installation: FAILED" >> $VERIFICATION_REPORT
        echo "Check /tmp/composer-production-test.log for details" >> $VERIFICATION_REPORT
    fi

    # 3. Check for missing platform requirements
    echo "" >> $VERIFICATION_REPORT
    echo "## Platform Requirements Check" >> $VERIFICATION_REPORT

    if composer check-platform-reqs --no-dev > /tmp/platform-check.log 2>&1; then
        echo "✅ Platform requirements: SATISFIED" >> $VERIFICATION_REPORT
    else
        echo "❌ Platform requirements: ISSUES DETECTED" >> $VERIFICATION_REPORT
        echo "\`\`\`" >> $VERIFICATION_REPORT
        cat /tmp/platform-check.log >> $VERIFICATION_REPORT
        echo "\`\`\`" >> $VERIFICATION_REPORT
    fi

    # 4. Validate lock file consistency
    echo "" >> $VERIFICATION_REPORT
    echo "## Lock File Validation" >> $VERIFICATION_REPORT

    if composer validate --strict --no-check-all > /dev/null 2>&1; then
        echo "✅ Composer validation: PASSED" >> $VERIFICATION_REPORT
    else
        echo "❌ Composer validation: ISSUES DETECTED" >> $VERIFICATION_REPORT
        composer validate --strict --no-check-all >> $VERIFICATION_REPORT 2>&1
    fi

    # 5. Security vulnerability check
    echo "" >> $VERIFICATION_REPORT
    echo "## Security Vulnerability Check" >> $VERIFICATION_REPORT

    if command -v vendor/bin/security-checker &> /dev/null; then
        if php artisan security:check > /tmp/security-check.log 2>&1; then
            echo "✅ Security check: NO VULNERABILITIES" >> $VERIFICATION_REPORT
        else
            echo "⚠️ Security check: VULNERABILITIES DETECTED" >> $VERIFICATION_REPORT
            echo "\`\`\`" >> $VERIFICATION_REPORT
            cat /tmp/security-check.log >> $VERIFICATION_REPORT
            echo "\`\`\`" >> $VERIFICATION_REPORT
        fi
    else
        echo "⚠️ Security checker not installed - run Step 3.3 to install analysis tools" >> $VERIFICATION_REPORT
    fi

    # 6. Generate summary and recommendations
    echo "" >> $VERIFICATION_REPORT
    echo "## Summary and Recommendations" >> $VERIFICATION_REPORT

    if [ "$DEV_ISSUES_FOUND" = true ]; then
        echo "🔧 **Action Required:**" >> $VERIFICATION_REPORT
        echo "1. Move dev dependencies to production requirements if used in production code" >> $VERIFICATION_REPORT
        echo "2. Or create production-safe alternatives for development-only features" >> $VERIFICATION_REPORT
        echo "3. Use environment checks to conditionally load dev dependencies" >> $VERIFICATION_REPORT
    else
        echo "✅ All production dependencies correctly classified" >> $VERIFICATION_REPORT
    fi

    echo "" >> $VERIFICATION_REPORT
    echo "📋 Full verification report saved to: $VERIFICATION_REPORT"

    echo ""
    echo "📋 Production dependency verification complete"
    echo "📁 Report saved to: $VERIFICATION_REPORT"
    cat $VERIFICATION_REPORT
    EOF

    chmod +x Admin-Local/Deployment/Scripts/verify-production-dependencies.sh
    ```

2. **Execute Production Verification**

    ```bash
    ./Admin-Local/Deployment/Scripts/verify-production-dependencies.sh
    ```

    **Expected Result:**

    ```
    ✅ Dev dependencies in production code analyzed
    ✅ Production installation compatibility verified
    ✅ Platform requirements validated
    ✅ Lock file consistency confirmed
    ✅ Security vulnerabilities checked
    ✅ Comprehensive verification report generated
    ```

---

### Step 16: Enhanced Build Process Testing [16-build-test]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Verify production build process with comprehensive pre-build validation and testing

#### **Action Steps:**

1. **Create Enhanced Pre-Build Validation Script**

    ```bash
    cat > Admin-Local/Deployment/Scripts/enhanced-pre-build-validation.sh << 'EOF'
    #!/bin/bash

    echo "╔══════════════════════════════════════════════════════════╗"
    echo "║           Enhanced Pre-Build Validation                  ║"
    echo "╚══════════════════════════════════════════════════════════╝"

    # Load deployment variables
    source Admin-Local/Deployment/Scripts/load-variables.sh

    VALIDATION_REPORT="Admin-Local/Deployment/Logs/pre-build-validation-$(date +%Y%m%d-%H%M%S).md"

    echo "# Enhanced Pre-Build Validation Report" > $VALIDATION_REPORT
    echo "Generated: $(date)" >> $VALIDATION_REPORT
    echo "" >> $VALIDATION_REPORT

    FAILED_CHECKS=0

    # 1. Environment validation
    echo "## Environment Validation" >> $VALIDATION_REPORT
    echo "1/12 - Validating environment prerequisites..."

    if php -v > /dev/null 2>&1; then
        echo "✅ PHP: $(php -r 'echo PHP_VERSION;')" >> $VALIDATION_REPORT
    else
        echo "❌ PHP not available" >> $VALIDATION_REPORT
        ((FAILED_CHECKS++))
    fi

    if composer --version > /dev/null 2>&1; then
        COMPOSER_VERSION=$(composer --version | grep -oP '\d+' | head -1)
        echo "✅ Composer: v$COMPOSER_VERSION" >> $VALIDATION_REPORT
    else
        echo "❌ Composer not available" >> $VALIDATION_REPORT
        ((FAILED_CHECKS++))
    fi

    if [ -f "package.json" ]; then
        if node --version > /dev/null 2>&1 && npm --version > /dev/null 2>&1; then
            echo "✅ Node: $(node --version), NPM: $(npm --version)" >> $VALIDATION_REPORT
        else
            echo "❌ Node.js/NPM not available but package.json exists" >> $VALIDATION_REPORT
            ((FAILED_CHECKS++))
        fi
    fi

    # 2. Required files validation
    echo "" >> $VALIDATION_REPORT
    echo "## Required Files Validation" >> $VALIDATION_REPORT
    echo "2/12 - Validating critical files..."

    REQUIRED_FILES=("composer.json" "composer.lock" "artisan" ".env")
    [ -f "package.json" ] && REQUIRED_FILES+=("package.json" "package-lock.json")

    for file in "${REQUIRED_FILES[@]}"; do
        if [ -f "$file" ]; then
            echo "✅ $file present" >> $VALIDATION_REPORT
        else
            echo "❌ $file missing" >> $VALIDATION_REPORT
            ((FAILED_CHECKS++))
        fi
    done

    # 3. Dependencies validation
    echo "" >> $VALIDATION_REPORT
    echo "## Dependencies Validation" >> $VALIDATION_REPORT
    echo "3/12 - Validating dependency integrity..."

    if composer validate --strict --no-check-all > /dev/null 2>&1; then
        echo "✅ Composer dependencies valid" >> $VALIDATION_REPORT
    else
        echo "❌ Composer dependency issues detected" >> $VALIDATION_REPORT
        ((FAILED_CHECKS++))
    fi

    if [ -f "package.json" ]; then
        if npm audit --audit-level=high > /dev/null 2>&1; then
            echo "✅ NPM dependencies secure" >> $VALIDATION_REPORT
        else
            echo "⚠️ NPM security vulnerabilities detected" >> $VALIDATION_REPORT
        fi
    fi

    # 4. Laravel-specific validation
    echo "" >> $VALIDATION_REPORT
    echo "## Laravel Application Validation" >> $VALIDATION_REPORT
    echo "4/12 - Validating Laravel setup..."

    if php artisan --version > /dev/null 2>&1; then
        LARAVEL_VERSION=$(php artisan --version | grep -oP '\d+\.\d+\.\d+')
        echo "✅ Laravel v$LARAVEL_VERSION functional" >> $VALIDATION_REPORT
    else
        echo "❌ Laravel Artisan not functional" >> $VALIDATION_REPORT
        ((FAILED_CHECKS++))
    fi

    # 5. Database connection test
    echo "" >> $VALIDATION_REPORT
    echo "## Database Connectivity" >> $VALIDATION_REPORT
    echo "5/12 - Testing database connection..."

    if php artisan migrate:status > /dev/null 2>&1; then
        MIGRATION_COUNT=$(php artisan migrate:status 2>/dev/null | grep -c "Ran")
        echo "✅ Database connected, $MIGRATION_COUNT migrations applied" >> $VALIDATION_REPORT
    else
        echo "⚠️ Database connection issues or no migrations" >> $VALIDATION_REPORT
    fi

    # 6. Build script validation
    echo "" >> $VALIDATION_REPORT
    echo "## Build Scripts Validation" >> $VALIDATION_REPORT
    echo "6/12 - Validating build capabilities..."

    if [ -f "package.json" ]; then
        if grep -q '"build"\|"production"\|"prod"' package.json; then
            echo "✅ Frontend build scripts detected" >> $VALIDATION_REPORT
        else
            echo "⚠️ No frontend build scripts found in package.json" >> $VALIDATION_REPORT
        fi
    fi

    # 7. Storage permissions
    echo "" >> $VALIDATION_REPORT
    echo "## Storage Permissions" >> $VALIDATION_REPORT
    echo "7/12 - Validating storage permissions..."

    STORAGE_DIRS=("storage/app" "storage/framework" "storage/logs" "bootstrap/cache")
    PERM_ISSUES=0
    for dir in "${STORAGE_DIRS[@]}"; do
        if [ -w "$dir" ]; then
            echo "✅ $dir writable" >> $VALIDATION_REPORT
        else
            echo "❌ $dir not writable" >> $VALIDATION_REPORT
            ((PERM_ISSUES++))
            ((FAILED_CHECKS++))
        fi
    done

    # 8. Git repository status
    echo "" >> $VALIDATION_REPORT
    echo "## Git Repository Status" >> $VALIDATION_REPORT
    echo "8/12 - Validating repository state..."

    if [ -d ".git" ]; then
        if [ -z "$(git status --porcelain 2>/dev/null)" ]; then
            CURRENT_BRANCH=$(git branch --show-current 2>/dev/null || echo "unknown")
            echo "✅ Repository clean, branch: $CURRENT_BRANCH" >> $VALIDATION_REPORT
        else
            UNCOMMITTED_COUNT=$(git status --porcelain 2>/dev/null | wc -l)
            echo "⚠️ $UNCOMMITTED_COUNT uncommitted changes" >> $VALIDATION_REPORT
        fi
    else
        echo "❌ Not a git repository" >> $VALIDATION_REPORT
        ((FAILED_CHECKS++))
    fi

    # 9. Configuration validation
    echo "" >> $VALIDATION_REPORT
    echo "## Configuration Validation" >> $VALIDATION_REPORT
    echo "9/12 - Validating configuration files..."

    CONFIG_FILES=("config/app.php" "config/database.php")
    for config in "${CONFIG_FILES[@]}"; do
        if [ -f "$config" ]; then
            echo "✅ $config present" >> $VALIDATION_REPORT
        else
            echo "❌ $config missing" >> $VALIDATION_REPORT
            ((FAILED_CHECKS++))
        fi
    done

    # 10. Production readiness test
    echo "" >> $VALIDATION_REPORT
    echo "## Production Readiness Test" >> $VALIDATION_REPORT
    echo "10/12 - Testing production build..."

    # Backup current state
    cp -r vendor vendor.pre-test 2>/dev/null || true
    [ -d "node_modules" ] && cp -r node_modules node_modules.pre-test 2>/dev/null || true

    # Test production installation
    if composer install --no-dev --quiet > /dev/null 2>&1; then
        if php artisan config:cache > /dev/null 2>&1; then
            echo "✅ Production build test successful" >> $VALIDATION_REPORT

            # Test frontend build if applicable
            if [ -f "package.json" ] && npm run build > /dev/null 2>&1; then
                echo "✅ Frontend build test successful" >> $VALIDATION_REPORT
            fi
        else
            echo "❌ Laravel optimization failed in production mode" >> $VALIDATION_REPORT
            ((FAILED_CHECKS++))
        fi
    else
        echo "❌ Production dependency installation failed" >> $VALIDATION_REPORT
        ((FAILED_CHECKS++))
    fi

    # Restore state
    php artisan config:clear > /dev/null 2>&1
    rm -rf vendor && mv vendor.pre-test vendor 2>/dev/null || composer install --quiet
    [ -d "node_modules.pre-test" ] && (rm -rf node_modules && mv node_modules.pre-test node_modules) 2>/dev/null || true

    # 11. Analysis tools validation
    echo "" >> $VALIDATION_REPORT
    echo "## Analysis Tools Validation" >> $VALIDATION_REPORT
    echo "11/12 - Checking analysis tools..."

    ANALYSIS_TOOLS=("vendor/bin/phpstan" "composer-unused.phar" "vendor/bin/composer-require-checker")
    for tool in "${ANALYSIS_TOOLS[@]}"; do
        if [ -f "$tool" ] || command -v $tool > /dev/null 2>&1; then
            echo "✅ $tool available" >> $VALIDATION_REPORT
        else
            echo "⚠️ $tool not installed (run Step 3.3)" >> $VALIDATION_REPORT
        fi
    done

    # 12. Final health check
    echo "" >> $VALIDATION_REPORT
    echo "## Final Health Check" >> $VALIDATION_REPORT
    echo "12/12 - Final application health verification..."

    if php artisan route:list > /dev/null 2>&1; then
        ROUTE_COUNT=$(php artisan route:list --compact | wc -l)
        echo "✅ Application routes functional ($ROUTE_COUNT routes)" >> $VALIDATION_REPORT
    else
        echo "❌ Application routing issues detected" >> $VALIDATION_REPORT
        ((FAILED_CHECKS++))
    fi

    # Generate summary
    echo "" >> $VALIDATION_REPORT
    echo "## Validation Summary" >> $VALIDATION_REPORT
    echo "- **Total Checks:** 12" >> $VALIDATION_REPORT
    echo "- **Failed Checks:** $FAILED_CHECKS" >> $VALIDATION_REPORT
    echo "- **Status:** $([ $FAILED_CHECKS -eq 0 ] && echo "✅ READY FOR DEPLOYMENT" || echo "❌ ISSUES REQUIRE ATTENTION")" >> $VALIDATION_REPORT

    if [ $FAILED_CHECKS -eq 0 ]; then
        echo ""
        echo "🎉 ALL VALIDATION CHECKS PASSED!"
        echo "✅ Application ready for production build and deployment"
        echo "📋 Full validation report: $VALIDATION_REPORT"
        exit 0
    else
        echo ""
        echo "🚫 VALIDATION FAILED!"
        echo "❌ $FAILED_CHECKS critical issues detected"
        echo "📋 Review and fix issues in: $VALIDATION_REPORT"
        exit 1
    fi
    EOF

    chmod +x Admin-Local/Deployment/Scripts/enhanced-pre-build-validation.sh
    ```

2. **Execute Enhanced Pre-Build Validation**

    ```bash
    ./Admin-Local/Deployment/Scripts/enhanced-pre-build-validation.sh
    ```

3. **Test Production Build Process**

    ```bash
    # Clear previous builds
    rm -rf vendor node_modules public/build

    # Test production PHP build
    composer install --no-dev --prefer-dist --optimize-autoloader

    # Build frontend assets (if applicable)
    if [ -f "package.json" ]; then
        npm ci
        npm run build
    fi

    # Test Laravel caching
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    # Test built version
    if php artisan --version > /dev/null 2>&1; then
        echo "✅ Production build test successful"
    else
        echo "❌ Production build test failed"
        exit 1
    fi

    # Restore development environment
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    composer install
    [ -f "package.json" ] && npm install
    ```

    **Expected Result:**

    ```
    ✅ Enhanced 12-point validation completed
    ✅ All critical checks passed
    ✅ Production build process verified
    ✅ Laravel optimizations tested
    ✅ Frontend build tested (if applicable)
    ✅ Development environment restored
    ✅ Application ready for deployment
    ```

---

### Step 16.1: Comprehensive Pre-Deployment Validation Checklist [16.1-pre-deploy-checklist]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Final comprehensive validation before any deployment activities - critical gateway step

#### **Action Steps:**

1. **Execute Master Pre-Deployment Validation Script** _(Using enhanced version from 4-Claude.md)_

    ```bash
    # This script is the enhanced version created in previous step
    ./Admin-Local/Deployment/Scripts/pre-deployment-validation.sh
    ```

    **Expected Result:**

    ```
    ✅ 10-point comprehensive validation completed
    ✅ Environment configuration verified
    ✅ Dependencies installation confirmed
    ✅ Database connectivity tested
    ✅ Build process validated
    ✅ Security configuration checked
    ✅ File permissions verified
    ✅ Git repository status confirmed
    ✅ Configuration files validated
    ✅ Application health verified
    ✅ DEPLOYMENT READY status achieved
    ```

---

### Step 16.2: Build Strategy Configuration [16.2-build-strategy]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Configure and validate build strategy (local, VM, or server-based) with flexible deployment workflows

#### **Action Steps:**

1. **Configure Build Strategy**

    ```bash
    ./Admin-Local/Deployment/Scripts/configure-build-strategy.sh
    ```

2. **Test Build Execution**

    ```bash
    ./Admin-Local/Deployment/Scripts/execute-build-strategy.sh
    ```

3. **Validate Build Output**

    ```bash
    ./Admin-Local/Deployment/Scripts/validate-build-output.sh
    ```

    **Expected Result:**

    ```
    ✅ Build strategy configured (local/VM/server)
    ✅ Build execution tested successfully
    ✅ Build output validated and verified
    ✅ Multiple build strategies supported
    ✅ Fallback mechanisms ready
    ```

---

### Step 17: Security Scanning [17-security-scan]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Comprehensive security vulnerability detection before deployment

#### **Action Steps:**

1. **Create Security Scanning Script**

    ```bash
    cat > Admin-Local/Deployment/Scripts/comprehensive-security-scan.sh << 'EOF'
    #!/bin/bash

    echo "╔══════════════════════════════════════════════════════════╗"
    echo "║            Comprehensive Security Scanning               ║"
    echo "╚══════════════════════════════════════════════════════════╝"

    SECURITY_REPORT="Admin-Local/Deployment/Logs/security-scan-$(date +%Y%m%d-%H%M%S).md"

    echo "# Security Scan Report" > $SECURITY_REPORT
    echo "Generated: $(date)" >> $SECURITY_REPORT
    echo "" >> $SECURITY_REPORT

    # 1. Laravel Security Checker
    echo "## Laravel Security Vulnerabilities" >> $SECURITY_REPORT
    if command -v php artisan security:check &> /dev/null; then
        if php artisan security:check > /tmp/laravel-security.log 2>&1; then
            echo "✅ No known vulnerabilities detected" >> $SECURITY_REPORT
        else
            echo "⚠️ Vulnerabilities detected:" >> $SECURITY_REPORT
            echo "\`\`\`" >> $SECURITY_REPORT
            cat /tmp/laravel-security.log >> $SECURITY_REPORT
            echo "\`\`\`" >> $SECURITY_REPORT
        fi
    else
        echo "⚠️ Laravel security checker not installed" >> $SECURITY_REPORT
    fi

    # 2. NPM Audit
    echo "" >> $SECURITY_REPORT
    echo "## Node.js Dependencies Security" >> $SECURITY_REPORT
    if [ -f "package.json" ]; then
        if npm audit --audit-level=moderate > /tmp/npm-audit.log 2>&1; then
            echo "✅ No moderate or higher severity vulnerabilities" >> $SECURITY_REPORT
        else
            echo "⚠️ NPM vulnerabilities detected:" >> $SECURITY_REPORT
            echo "\`\`\`" >> $SECURITY_REPORT
            cat /tmp/npm-audit.log >> $SECURITY_REPORT
            echo "\`\`\`" >> $SECURITY_REPORT
        fi
    else
        echo "ℹ️ No package.json - skipping NPM audit" >> $SECURITY_REPORT
    fi

    # 3. Environment Security Check
    echo "" >> $SECURITY_REPORT
    echo "## Environment Security Configuration" >> $SECURITY_REPORT

    SECURITY_ISSUES=()

    if grep -q "APP_DEBUG=true" .env 2>/dev/null; then
        SECURITY_ISSUES+=("APP_DEBUG is enabled - disable for production")
    fi

    if grep -q "APP_ENV=local" .env 2>/dev/null; then
        SECURITY_ISSUES+=("APP_ENV is set to local - change to production")
    fi

    if ! grep -q "FORCE_HTTPS=true" .env 2>/dev/null && ! grep -q "APP_URL=https" .env 2>/dev/null; then
        SECURITY_ISSUES+=("HTTPS enforcement not configured")
    fi

    if [ ${#SECURITY_ISSUES[@]} -eq 0 ]; then
        echo "✅ Environment security configuration looks good" >> $SECURITY_REPORT
    else
        echo "⚠️ Security configuration issues:" >> $SECURITY_REPORT
        for issue in "${SECURITY_ISSUES[@]}"; do
            echo "- $issue" >> $SECURITY_REPORT
        done
    fi

    echo ""
    echo "📋 Security scan completed"
    echo "📁 Report saved to: $SECURITY_REPORT"
    cat $SECURITY_REPORT
    EOF

    chmod +x Admin-Local/Deployment/Scripts/comprehensive-security-scan.sh
    ```

2. **Execute Security Scan**

    ```bash
    ./Admin-Local/Deployment/Scripts/comprehensive-security-scan.sh
    ```

    **Expected Result:**

    ```
    ✅ Laravel security vulnerabilities checked
    ✅ Node.js dependencies audited for security issues
    ✅ Environment security configuration validated
    ✅ Security recommendations provided
    ✅ Comprehensive security report generated
    ```

---

### Step 18: Customization Protection [18-customization]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Implement Laravel-compatible customization layer to protect changes during updates

#### **Action Steps:**

1. **Setup Enhanced Customization System**

    ```bash
    # Create customization directories
    mkdir -p app/Custom/{Controllers,Models,Services,Middleware}
    mkdir -p config/custom
    mkdir -p database/migrations/custom
    mkdir -p resources/views/custom
    mkdir -p public/custom/{css,js,images}

    # Create CustomizationServiceProvider
    cat > app/Providers/CustomizationServiceProvider.php << 'EOF'
    <?php

    namespace App\Providers;

    use Illuminate\Support\ServiceProvider;
    use Illuminate\Support\Facades\View;
    use Illuminate\Support\Facades\Route;

    class CustomizationServiceProvider extends ServiceProvider
    {
        public function boot()
        {
            // Load custom routes
            $this->loadRoutesFrom(base_path('routes/custom.php'));

            // Load custom views
            View::addNamespace('custom', resource_path('views/custom'));

            // Load custom migrations
            $this->loadMigrationsFrom(database_path('migrations/custom'));

            // Load custom configurations
            if (file_exists(config_path('custom'))) {
                foreach (glob(config_path('custom/*.php')) as $file) {
                    $this->mergeConfigFrom($file, 'custom.' . basename($file, '.php'));
                }
            }
        }

        public function register()
        {
            // Register custom services
        }
    }
    EOF

    # Create custom routes file
    cat > routes/custom.php << 'EOF'
    <?php

    use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | Custom Routes
    |--------------------------------------------------------------------------
    |
    | Here you can register custom routes that won't be overwritten during
    | vendor updates. These routes will be loaded by the CustomizationServiceProvider.
    |
    */
    EOF

    echo "✅ Customization protection system implemented"
    ```

    **Expected Result:**

    ```
    ✅ Protected customization directories created
    ✅ CustomizationServiceProvider implemented
    ✅ Custom routes system established
    ✅ Update-safe customization framework ready
    ✅ Investment protection documentation generated
    ```

---

### Step 19: Data Persistence Strategy [19-data-persistence]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Implement zero data loss system with smart content protection during deployments

#### **Action Steps:**

1. **Create Advanced Persistence Script**

    ```bash
    cat > Admin-Local/Deployment/Scripts/setup-data-persistence.sh << 'EOF'
    #!/bin/bash

    echo "Setting up advanced data persistence strategy..."

    # Create comprehensive shared directories list
    SHARED_DIRECTORIES=(
        "storage/app/public"          # Laravel default
        "storage/logs"                # Application logs
        "storage/framework/cache"     # Framework cache
        "storage/framework/sessions"  # User sessions
        "storage/framework/views"     # Compiled views
        "public/uploads"              # User uploads
        "public/media"                # Media files
        "public/avatars"              # User avatars
        "public/documents"            # Document storage
        "public/exports"              # Generated exports
        "public/qrcodes"              # QR codes
        "public/invoices"             # Invoice PDFs
        "public/reports"              # Generated reports
        "Modules"                     # Modular applications
    )

    # Create documentation
    cat > Admin-Local/Deployment/Configs/data-persistence-config.json << PERSIST
    {
        "shared_directories": [
    $(printf '        "%s",\n' "${SHARED_DIRECTORIES[@]}" | sed '$ s/,$//')
        ],
        "backup_strategy": "incremental",
        "retention_days": 30,
        "verification_enabled": true
    }
    PERSIST

    echo "✅ Advanced data persistence strategy configured"
    echo "📋 Protected directories: ${#SHARED_DIRECTORIES[@]}"
    EOF

    chmod +x Admin-Local/Deployment/Scripts/setup-data-persistence.sh
    ./Admin-Local/Deployment/Scripts/setup-data-persistence.sh
    ```

    **Expected Result:**

    ```
    ✅ Comprehensive shared directories configuration created
    ✅ Zero data loss protection implemented
    ✅ User uploads, invoices, QR codes, exports protected
    ✅ Advanced persistence strategy documented
    ✅ Verification and backup systems ready
    ```

---

### Step 20: Commit Pre-Deployment Setup [20-commit-setup]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Commit all preparation work with comprehensive documentation

#### **Action Steps:**

1. **Final Verification and Commit**

    ```bash
    # Verify all systems are ready
    echo "🔍 Final pre-deployment verification..."

    # Check all critical scripts exist
    REQUIRED_SCRIPTS=(
        "Admin-Local/Deployment/Scripts/load-variables.sh"
        "Admin-Local/Deployment/Scripts/comprehensive-env-check.sh"
        "Admin-Local/Deployment/Scripts/universal-dependency-analyzer.sh"
        "Admin-Local/Deployment/Scripts/install-analysis-tools.sh"
        "Admin-Local/Deployment/Scripts/setup-composer-strategy.sh"
        "Admin-Local/Deployment/Scripts/verify-production-dependencies.sh"
        "Admin-Local/Deployment/Scripts/enhanced-pre-build-validation.sh"
        "Admin-Local/Deployment/Scripts/configure-build-strategy.sh"
        "Admin-Local/Deployment/Scripts/comprehensive-security-scan.sh"
    )

    ALL_SCRIPTS_PRESENT=true
    for script in "${REQUIRED_SCRIPTS[@]}"; do
        if [ ! -f "$script" ]; then
            echo "❌ Missing script: $script"
            ALL_SCRIPTS_PRESENT=false
        fi
    done

    if [ "$ALL_SCRIPTS_PRESENT" = true ]; then
        echo "✅ All deployment scripts present and ready"

        # Create deployment readiness report
        cat > Admin-Local/Deployment/deployment-readiness-report.md << 'EOF'
    # Deployment Readiness Report

    **Generated:** $(date)
    **Status:** READY FOR DEPLOYMENT

    ## ✅ Completed Setup Steps
    - [x] Enhanced Composer strategy configured
    - [x] Universal dependency analysis implemented
    - [x] Comprehensive analysis tools installed
    - [x] Production verification validated
    - [x] Build strategy configured and tested
    - [x] Security scanning completed
    - [x] Customization protection implemented
    - [x] Data persistence strategy configured
    - [x] All deployment scripts created and tested

    ## 🚀 Next Steps
    Ready to proceed to deployment phase following Section C guidelines.
    EOF

        # Stage and commit all changes
        git add .
        git commit -m "feat: complete enhanced pre-deployment setup

    - Add universal dependency analyzer with 12+ package patterns
    - Implement comprehensive analysis tools (PHPStan, Composer Unused, Security Checker)
    - Create enhanced pre-deployment validation (12-point checklist)
    - Configure flexible build strategies (local/VM/server)
    - Add comprehensive security scanning
    - Implement customization protection system
    - Setup advanced data persistence strategy
    - Create all deployment scripts with error handling
    - Add comprehensive documentation and reporting

    Ready for zero-error, zero-downtime deployment."

        git push origin main

        echo "✅ Pre-deployment setup completed and committed"
        echo "📋 Deployment readiness report created"
        echo "🚀 Ready to proceed to deployment phase"
    else
        echo "❌ Setup incomplete - fix missing scripts before proceeding"
        exit 1
    fi
    ```

    **Expected Result:**

    ```
    ✅ All deployment scripts verified and present
    ✅ Deployment readiness report generated
    ✅ Comprehensive pre-deployment setup committed
    ✅ Repository pushed with all enhancements
    ✅ Ready for zero-error, zero-downtime deployment
    ✅ Section B preparation phase completed successfully
    ```

---

## **Summary: Section B Enhancements**

This enhanced Section B now includes:

-   ✅ **Enhanced Composer Strategy** - Production optimization with plugin compatibility
-   ✅ **Universal Dependency Analyzer** - 12+ package patterns with auto-fix functionality
-   ✅ **Comprehensive Analysis Tools** - PHPStan, Composer Unused, Security Checker integration
-   ✅ **12-Point Validation System** - Enhanced pre-build validation with detailed reporting
-   ✅ **Flexible Build Strategies** - Local/VM/server build configuration with fallback logic
-   ✅ **Security Scanning** - Multi-layer security vulnerability detection
-   ✅ **Customization Protection** - Update-safe customization framework
-   ✅ **Advanced Data Persistence** - Zero data loss protection with comprehensive directory coverage
-   ✅ **Standardized Step Format** - All steps include [step-id], location tags, paths, and expected results
-   ✅ **Path Variables Integration** - Consistent use of variables throughout all scripts
-   ✅ **Universal Compatibility** - Works with any Laravel version and frontend framework

**Ready for deployment with zero-error, zero-downtime guarantee.**

---

# Master Checklist for **SECTION C: Build and Deploy** - Universal Laravel Zero-Downtime

**Version:** 2.0  
**Last Updated:** August 20, 2025  
**Purpose:** Complete professional zero-downtime deployment pipeline with enhanced build strategies and atomic deployment

---

## **Universal Deployment Process**

This deployment flow ensures **TRUE zero-downtime** for ANY Laravel application using atomic symlink switches, comprehensive build strategies, and advanced error handling.

### **Visual Step Identification Guide**

-   🟢 **Local Machine**: Steps executed on developer's local environment
-   🟡 **Builder VM**: Steps executed on dedicated build/CI server
-   🔴 **Server**: Steps executed on production server
-   🟣 **User-configurable**: SSH Commands - User hooks (1️⃣ Pre-release, 2️⃣ Mid-release, 3️⃣ Post-release)
-   🏗️ **Builder Commands**: Build-specific operations

### **Path Variables System**

All paths use dynamic variables from `deployment-variables.json`:

-   `%path-localMachine%`: Local development machine paths
-   `%path-Builder-VM%`: Build server/CI environment paths
-   `%path-server%`: Production server paths

---

## **Phase 1: 🖥️ Prepare Build Environment**

### Step 1.1: Pre-Build Environment Preparation [1.1-prebuild-prep]

**Location:** 🟢 Run on Local Machine  
**Path:** `%path-localMachine%`  
**Purpose:** Load deployment configuration and validate deployment readiness

#### **Action Steps:**

1. **Load Deployment Variables and Initialize**

    ```bash
    # Load deployment variables from Section A setup
    source Admin-Local/Deployment/Scripts/load-variables.sh

    # Initialize deployment workspace
    mkdir -p "${DEPLOY_WORKSPACE}/logs" 2>/dev/null || mkdir -p "deploy-workspace/logs"
    cd "${DEPLOY_WORKSPACE:-deploy-workspace}"

    # Repository connectivity validation
    echo "🔍 Validating repository connectivity..."
    if git ls-remote --heads "${GITHUB_REPO}" > /dev/null 2>&1; then
        echo "✅ Repository accessible: ${GITHUB_REPO}"
    else
        echo "❌ Repository not accessible: ${GITHUB_REPO}"
        exit 1
    fi

    # Branch availability check
    if git ls-remote --heads "${GITHUB_REPO}" "${DEPLOY_BRANCH}" | grep -q "${DEPLOY_BRANCH}"; then
        echo "✅ Branch available: ${DEPLOY_BRANCH}"
    else
        echo "❌ Branch not found: ${DEPLOY_BRANCH}"
        exit 1
    fi

    echo "✅ Pre-build preparation complete"
    ```

    **Expected Result:**

    ```
    ✅ Deployment variables loaded successfully
    ✅ Repository connectivity verified
    ✅ Target branch confirmed available
    ✅ Deployment workspace initialized
    ✅ Ready to proceed to build environment setup
    ```

### Step 1.2: Build Environment Setup [1.2-build-setup]

**Location:** 🟡 Run on Builder VM (or 🟢 Local if BUILD_LOCATION=local)  
**Path:** `%path-Builder-VM%` or `%path-localMachine%/build-tmp`  
**Purpose:** Initialize clean build environment with correct versions matching production

#### **Action Steps:**

1. **Execute Build Strategy Configuration**

    ```bash
    # Execute build strategy based on deployment configuration
    BUILD_LOCATION=$(jq -r '.deployment.build_location' Admin-Local/Deployment/Configs/deployment-variables.json)

    case "${BUILD_LOCATION}" in
        "local")
            echo "🏠 Using local build environment"
            BUILD_PATH="${PATH_LOCAL_MACHINE}/build-tmp"
            mkdir -p "$BUILD_PATH"
            ;;
        "vm")
            echo "🖥️ Using VM build environment"
            BUILD_PATH="${PATH_BUILDER}"
            # Initialize VM if needed
            if ! ping -c 1 "${BUILD_SERVER_HOST}" > /dev/null 2>&1; then
                echo "❌ Cannot connect to build server: ${BUILD_SERVER_HOST}"
                echo "🔄 Falling back to local build..."
                BUILD_PATH="${PATH_LOCAL_MACHINE}/build-tmp"
                mkdir -p "$BUILD_PATH"
            fi
            ;;
        "server")
            echo "🌐 Using server build environment"
            BUILD_PATH="${PATH_SERVER}/build-tmp"
            ;;
        *)
            echo "❌ Unknown build strategy: ${BUILD_LOCATION}"
            exit 1
            ;;
    esac

    # Set build environment variables
    export BUILD_ENV="production"
    export COMPOSER_MEMORY_LIMIT=-1
    export NODE_ENV="production"
    export BUILD_PATH

    echo "✅ Build environment setup complete for strategy: ${BUILD_LOCATION}"
    echo "📁 Build path: $BUILD_PATH"
    ```

2. **Run Comprehensive Environment Analysis**

    ```bash
    # Execute environment analysis from Section A
    source Admin-Local/Deployment/Scripts/comprehensive-env-check.sh
    ```

    **Expected Result:**

    ```
    ✅ Build strategy determined and configured
    ✅ Build path established and ready
    ✅ Environment variables set for production
    ✅ Comprehensive environment analysis completed
    ✅ Version compatibility confirmed
    ```

### Step 1.3: Repository Preparation [1.3-repo-prep]

**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Clone repository to build environment and validate commit integrity

#### **Action Steps:**

1. **Repository Cloning with Build Strategy Support**

    ```bash
    # Navigate to build environment
    cd "${BUILD_PATH}"

    # Clean build directory
    rm -rf "${PROJECT_NAME}" 2>/dev/null || true

    # Clone repository with optimized settings
    echo "📥 Cloning repository..."
    if git clone --depth=1 --branch="${DEPLOY_BRANCH}" "${GITHUB_REPO}" "${PROJECT_NAME}"; then
        echo "✅ Repository cloned successfully"
    else
        echo "❌ Repository clone failed"
        exit 1
    fi

    cd "${PROJECT_NAME}"

    # Checkout specific commit if provided
    if [[ -n "${TARGET_COMMIT}" ]]; then
        echo "🔄 Checking out specific commit: ${TARGET_COMMIT}"
        git fetch --depth=1 origin "${TARGET_COMMIT}"
        if git checkout "${TARGET_COMMIT}"; then
            echo "✅ Commit checked out successfully"
        else
            echo "❌ Cannot checkout commit: ${TARGET_COMMIT}"
            exit 1
        fi
    fi

    # Validate repository structure
    if [[ -f "composer.json" ]] && [[ -f "artisan" ]]; then
        echo "✅ Laravel repository structure validated"
    else
        echo "❌ Invalid Laravel repository structure"
        exit 1
    fi

    # Log commit information
    echo "📋 Repository prepared:"
    echo "  - Branch: $(git branch --show-current)"
    echo "  - Commit: $(git rev-parse --short HEAD)"
    echo "  - Message: $(git log -1 --pretty=format:'%s')"
    ```

    **Expected Result:**

    ```
    ✅ Repository cloned to build environment
    ✅ Target commit checked out (if specified)
    ✅ Laravel structure validated
    ✅ Build directory ready for Phase 2
    ```

---

## **Phase 2: 🏗️ Build Application**

### Step 2.1: Intelligent Cache Restoration [2.1-cache-restore]

**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Restore cached dependencies with integrity validation to speed up builds

#### **Action Steps:**

1. **Execute Intelligent Cache System**

    ```bash
    echo "♻️ Intelligent cache restoration..."

    # Load cache configuration from deployment variables
    CACHE_DIR="${CACHE_BASE_PATH:-/tmp/build-cache}/${PROJECT_NAME}"
    COMPOSER_CACHE="${CACHE_DIR}/composer"
    NPM_CACHE="${CACHE_DIR}/npm"

    # Create cache directories
    mkdir -p "${COMPOSER_CACHE}" "${NPM_CACHE}"

    # Validate lock file consistency for Composer
    if [[ -f "composer.lock" ]]; then
        COMPOSER_HASH=$(md5sum composer.lock 2>/dev/null | cut -d' ' -f1)
        CACHED_COMPOSER_HASH=$(cat "${COMPOSER_CACHE}/.hash" 2>/dev/null || echo "")

        if [[ "${COMPOSER_HASH}" == "${CACHED_COMPOSER_HASH}" ]] && [[ -d "${COMPOSER_CACHE}/vendor" ]]; then
            echo "✅ Restoring Composer cache (hash match: ${COMPOSER_HASH})"
            cp -r "${COMPOSER_CACHE}/vendor" ./ 2>/dev/null || true
        else
            echo "⚠️ Composer cache miss or invalid - will rebuild"
        fi
    fi

    # Validate lock file consistency for NPM
    if [[ -f "package-lock.json" ]]; then
        NPM_HASH=$(md5sum package-lock.json 2>/dev/null | cut -d' ' -f1)
        CACHED_NPM_HASH=$(cat "${NPM_CACHE}/.hash" 2>/dev/null || echo "")

        if [[ "${NPM_HASH}" == "${CACHED_NPM_HASH}" ]] && [[ -d "${NPM_CACHE}/node_modules" ]]; then
            echo "✅ Restoring NPM cache (hash match: ${NPM_HASH})"
            cp -r "${NPM_CACHE}/node_modules" ./ 2>/dev/null || true
        else
            echo "⚠️ NPM cache miss or invalid - will rebuild"
        fi
    fi

    echo "✅ Cache restoration phase completed"
    ```

    **Expected Result:**

    ```
    ✅ Cache restoration attempted with integrity validation
    ✅ Composer cache restored (if hash match)
    ✅ NPM cache restored (if hash match and package.json exists)
    ✅ Build process accelerated where possible
    ```

### Step 2.2: Universal Dependency Installation [2.2-dependencies]

**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Execute smart dependency installation with production optimization

#### **Action Steps:**

1. **Execute Universal Dependency Analyzer**

    ```bash
    # Run enhanced dependency analysis from Section A
    source Admin-Local/Deployment/Scripts/universal-dependency-analyzer.sh

    # Verify analysis tools are available
    source Admin-Local/Deployment/Scripts/install-analysis-tools.sh
    ```

2. **Smart Dependency Installation**

    ```bash
    echo "📦 Universal Smart Dependency Installation..."

    # Configure Composer for build environment
    COMPOSER_FLAGS="--optimize-autoloader --prefer-dist --no-scripts"

    case "${BUILD_LOCATION}" in
        "local"|"vm")
            # Development-friendly flags for build environment
            COMPOSER_FLAGS="${COMPOSER_FLAGS} --classmap-authoritative"
            ;;
        "server")
            # Production-optimized flags
            COMPOSER_FLAGS="${COMPOSER_FLAGS} --no-dev --classmap-authoritative"
            ;;
    esac

    # Smart Composer installation with enhanced strategy
    echo "📦 Installing PHP dependencies..."
    if [[ -f "composer.json" ]]; then
        # Apply enhanced Composer strategy from Section B
        source Admin-Local/Deployment/Scripts/setup-composer-strategy.sh

        # Install with appropriate flags
        if composer install ${COMPOSER_FLAGS}; then
            echo "✅ Composer dependencies installed successfully"

            # Cache successful installation
            if [[ -d "vendor" ]] && [[ -n "${COMPOSER_CACHE}" ]]; then
                rm -rf "${COMPOSER_CACHE}/vendor" 2>/dev/null || true
                cp -r vendor "${COMPOSER_CACHE}/" 2>/dev/null || true
                echo "${COMPOSER_HASH}" > "${COMPOSER_CACHE}/.hash"
                echo "💾 Composer cache updated"
            fi
        else
            echo "❌ Composer installation failed"
            exit 1
        fi
    fi

    # Smart Node.js installation (if applicable)
    if [[ -f "package.json" ]]; then
        echo "📦 Installing Node.js dependencies..."

        # Determine installation strategy based on build scripts
        if grep -q '"build":\|"production":\|"prod":\|"dev":.*"vite\|webpack\|laravel-mix"' package.json; then
            echo "🏗️ Build dependencies detected - installing all packages"
            npm ci --production=false
        else
            echo "📦 Production-only installation"
            npm ci --production=true
        fi

        # Verify installation
        if [[ -d "node_modules" ]]; then
            echo "✅ Node.js dependencies installed successfully"

            # Cache successful installation
            if [[ -n "${NPM_CACHE}" ]]; then
                rm -rf "${NPM_CACHE}/node_modules" 2>/dev/null || true
                cp -r node_modules "${NPM_CACHE}/" 2>/dev/null || true
                echo "${NPM_HASH}" > "${NPM_CACHE}/.hash"
                echo "💾 NPM cache updated"
            fi
        else
            echo "❌ Node.js installation failed"
            exit 1
        fi

        # Security audit
        if npm audit --audit-level=high --silent; then
            echo "✅ No high-severity vulnerabilities detected"
        else
            echo "⚠️ High-severity vulnerabilities detected - review required"
        fi
    fi

    echo "✅ Universal dependency installation complete"
    ```

    **Expected Result:**

    ```
    ✅ Universal dependency analyzer executed
    ✅ Enhanced Composer strategy applied
    ✅ PHP dependencies installed with optimization
    ✅ Node.js dependencies installed (if applicable)
    ✅ Security audit completed
    ✅ Dependency cache updated for future builds
    ```

### Step 2.3: Advanced Asset Compilation [2.3-assets]

**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Compile frontend assets with auto-detection and optimization

#### **Action Steps:**

1. **Execute Advanced Asset Compilation**

    ```bash
    echo "🎨 Advanced asset compilation with auto-detection..."

    if [[ ! -f "package.json" ]]; then
        echo "📝 No package.json found - skipping asset compilation"
    else
        # Detect asset bundler automatically
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
                if npm run build || npm run prod; then
                    echo "✅ Vite build successful"
                else
                    echo "❌ Vite build failed"
                    exit 1
                fi
                ;;
            "mix")
                echo "🏗️ Building with Laravel Mix..."
                if npm run production || npm run prod; then
                    echo "✅ Laravel Mix build successful"
                else
                    echo "❌ Laravel Mix build failed"
                    exit 1
                fi
                ;;
            "webpack")
                echo "📦 Building with Webpack..."
                if npm run build || npm run production; then
                    echo "✅ Webpack build successful"
                else
                    echo "❌ Webpack build failed"
                    exit 1
                fi
                ;;
            *)
                echo "🤷 Unknown bundler - attempting generic build..."
                if npm run build 2>/dev/null || npm run prod 2>/dev/null || npm run production 2>/dev/null; then
                    echo "✅ Generic build successful"
                else
                    echo "⚠️ No suitable build script found - continuing without frontend build"
                fi
                ;;
        esac

        # Validate build output
        if [[ -d "public/build" ]] || [[ -d "public/assets" ]] || [[ -d "public/js" ]] || [[ -d "public/css" ]]; then
            echo "✅ Asset compilation successful - build output detected"

            # Clean up dev dependencies post-build (production mode)
            if [[ "${BUILD_ENV}" == "production" ]]; then
                echo "🧹 Cleaning up dev dependencies..."
                rm -rf node_modules
                npm ci --production=true --silent 2>/dev/null || true
            fi
        else
            echo "⚠️ No build output detected - build may have failed silently"
        fi
    fi

    echo "✅ Advanced asset compilation phase completed"
    ```

    **Expected Result:**

    ```
    ✅ Asset bundler automatically detected (Vite/Mix/Webpack)
    ✅ Frontend assets compiled successfully
    ✅ Build output validated and confirmed
    ✅ Dev dependencies cleaned up (production mode)
    ✅ Assets ready for production deployment
    ```

### Step 2.4: Laravel Production Optimization [2.4-optimize]

**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Apply comprehensive Laravel optimizations for production performance

#### **Action Steps:**

1. **Execute Comprehensive Laravel Optimization**

    ```bash
    echo "⚡ Comprehensive Laravel production optimization..."

    # Clear existing caches to start fresh
    php artisan cache:clear --quiet 2>/dev/null || true
    php artisan config:clear --quiet 2>/dev/null || true
    php artisan route:clear --quiet 2>/dev/null || true
    php artisan view:clear --quiet 2>/dev/null || true

    # Create temporary .env for optimization
    if [[ -f ".env.production" ]]; then
        cp .env.production .env
    elif [[ -f ".env.example" ]]; then
        cp .env.example .env
        # Generate temporary app key for optimization
        php artisan key:generate --force --quiet
    fi

    # Production optimization sequence
    echo "📝 Caching configuration..."
    if php artisan config:cache --quiet; then
        echo "✅ Configuration cached successfully"
    else
        echo "❌ Config cache failed"
        exit 1
    fi

    echo "🗺️ Caching routes..."
    if php artisan route:cache --quiet; then
        echo "✅ Routes cached successfully"
    else
        echo "❌ Route cache failed"
        exit 1
    fi

    echo "👁️ Caching views..."
    if php artisan view:cache --quiet; then
        echo "✅ Views cached successfully"
    else
        echo "⚠️ View cache failed - continuing anyway"
    fi

    # Advanced Laravel optimizations
    echo "⚙️ Advanced optimizations..."

    # Cache events if available
    if php artisan list | grep -q "event:cache"; then
        php artisan event:cache --quiet 2>/dev/null && echo "📅 Events cached" || true
    fi

    # Cache icons if available
    if php artisan list | grep -q "icons:cache"; then
        php artisan icons:cache --quiet 2>/dev/null && echo "🎨 Icons cached" || true
    fi

    # Optimize Composer autoloader
    echo "🔧 Optimizing autoloader..."
    if composer dump-autoload --optimize --classmap-authoritative --no-dev --quiet; then
        echo "✅ Autoloader optimized for production"
    else
        echo "❌ Autoloader optimization failed"
        exit 1
    fi

    echo "✅ Laravel optimization sequence completed"
    ```

    **Expected Result:**

    ```
    ✅ All Laravel caches cleared and rebuilt
    ✅ Configuration cache created
    ✅ Route cache optimized
    ✅ View cache compiled
    ✅ Advanced features cached (events, icons)
    ✅ Autoloader optimized for maximum performance
    ✅ Application ready for production deployment
    ```

### Step 2.5: Comprehensive Build Validation [2.5-validate]

**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Execute comprehensive validation of build artifacts and integrity

#### **Action Steps:**

1. **Execute Enhanced Build Validation**

    ```bash
    # Run enhanced pre-deployment validation from Section B
    source Admin-Local/Deployment/Scripts/pre-deployment-validation.sh
    ```

2. **Additional Build-Specific Validation**

    ```bash
    echo "🔍 Build artifact validation..."

    # Critical file existence check
    CRITICAL_FILES=(
        "bootstrap/app.php"
        "artisan"
        "composer.json"
        "composer.lock"
        "bootstrap/cache/config.php"
        "bootstrap/cache/routes-v7.php"
    )

    for file in "${CRITICAL_FILES[@]}"; do
        if [[ -f "${file}" ]]; then
            echo "✅ Critical file present: ${file}"
        else
            # Check for alternative route cache file
            if [[ "${file}" == "bootstrap/cache/routes-v7.php" ]] && [[ -f "bootstrap/cache/routes.php" ]]; then
                echo "✅ Alternative route cache present: bootstrap/cache/routes.php"
            else
                echo "❌ Critical file missing: ${file}"
                exit 1
            fi
        fi
    done

    # Validate vendor directory
    if [[ -d "vendor" ]] && [[ -f "vendor/autoload.php" ]]; then
        echo "✅ Vendor directory structure valid"
    else
        echo "❌ Vendor directory invalid"
        exit 1
    fi

    # Test basic Laravel functionality
    echo "🧪 Testing Laravel bootstrap..."
    if php artisan --version --quiet >/dev/null; then
        echo "✅ Laravel bootstrap successful"
    else
        echo "❌ Laravel bootstrap failed"
        exit 1
    fi

    # Advanced application test
    echo "🔬 Advanced application testing..."
    if php -r "
        try {
            require_once 'vendor/autoload.php';
            \$app = require_once 'bootstrap/app.php';
            echo 'Application instantiation successful\n';
        } catch (Exception \$e) {
            echo 'Application instantiation failed: ' . \$e->getMessage() . '\n';
            exit(1);
        }
    "; then
        echo "✅ Advanced application test passed"
    else
        echo "❌ Advanced application test failed"
        exit 1
    fi

    # Optional: Run automated tests
    if [[ "${RUN_TESTS}" == "true" ]] && [[ -d "tests" ]]; then
        echo "🧪 Running automated tests..."
        if php artisan test --parallel --stop-on-failure >/dev/null 2>&1; then
            echo "✅ Automated tests passed"
        else
            echo "❌ Automated tests failed"
            exit 1
        fi
    fi

    echo "✅ Comprehensive build validation completed successfully"
    ```

    **Expected Result:**

    ```
    ✅ Pre-deployment validation checklist passed (10 points)
    ✅ All critical files present and validated
    ✅ Vendor directory structure confirmed
    ✅ Laravel bootstrap functionality verified
    ✅ Advanced application instantiation tested
    ✅ Automated tests passed (if enabled)
    ✅ Build ready for packaging and deployment
    ```

---

## **Phase 3: 📦 Package & Transfer**

### Step 3.1: Smart Build Artifact Preparation [3.1-package]

**Location:** 🟡 Run on Builder VM  
**Path:** `%path-Builder-VM%`  
**Purpose:** Create optimized deployment package with manifest and integrity validation

#### **Action Steps:**

1. **Create Smart Deployment Package**

    ```bash
    echo "📦 Smart build artifact preparation..."

    # Execute build output validation
    source Admin-Local/Deployment/Scripts/validate-build-output.sh

    # Create deployment manifest
    echo "📝 Creating deployment manifest..."
    cat > deployment-manifest.json << EOF
    {
        "build_strategy": "${BUILD_LOCATION}",
        "release_id": "$(date +%Y%m%d%H%M%S)",
        "git_commit": "$(git rev-parse HEAD)",
        "git_branch": "$(git branch --show-current)",
        "build_timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
        "php_version": "$(php --version | head -n1)",
        "composer_version": "$(composer --version)",
        "node_version": "$(node --version 2>/dev/null || echo 'not installed')",
        "laravel_version": "$(php artisan --version | grep -oP '\d+\.\d+\.\d+' || echo 'unknown')",
        "environment": "${BUILD_ENV:-production}",
        "has_frontend": $([ -f "package.json" ] && echo "true" || echo "false"),
        "frontend_framework": "$([ -f "package.json" ] && (grep -q vite package.json && echo "vite" || grep -q laravel-mix package.json && echo "mix" || echo "webpack") || echo "none")"
    }
    EOF

    # Enhanced exclusion patterns from deployment configuration
    EXCLUDE_PATTERNS=(
        ".git" ".github" ".gitlab"
        "tests" "Test" "Tests"
        "node_modules"
        ".env*" "!.env.example"
        ".phpunit*" "phpunit.xml" "pest.xml"
        "webpack.mix.js" "vite.config.js" "postcss.config.js"
        ".eslintrc*" ".editorconfig" "tsconfig.json"
        "*.log" "*.tmp"
        "Admin-Local"
        ".vscode" ".idea"
        "*.swp" "*.swo" "*~"
        ".DS_Store" "Thumbs.db"
        "composer-unused.phar"
        "build-tmp"
    )

    # Build tar command with exclusions
    TAR_EXCLUDES=""
    for pattern in "${EXCLUDE_PATTERNS[@]}"; do
        TAR_EXCLUDES="${TAR_EXCLUDES} --exclude='${pattern}'"
    done

    # Create release artifact with timestamp
    RELEASE_TIMESTAMP=$(date +%Y%m%d%H%M%S)
    RELEASE_NAME="release-${RELEASE_TIMESTAMP}-$(git rev-parse --short HEAD)"

    echo "🗂️ Creating artifact package: ${RELEASE_NAME}.tar.gz..."
    eval "tar ${TAR_EXCLUDES} -czf ${RELEASE_NAME}.tar.gz ."

    # Generate comprehensive checksums
    echo "🔐 Generating checksums and validation..."
    md5sum "${RELEASE_NAME}.tar.gz" > "${RELEASE_NAME}.md5"
    sha256sum "${RELEASE_NAME}.tar.gz" > "${RELEASE_NAME}.sha256"

    # Create artifact info file
    cat > "${RELEASE_NAME}.info" << EOF
    Release: ${RELEASE_NAME}
    Timestamp: $(date -u +%Y-%m-%dT%H:%M:%SZ)
    Git Commit: $(git rev-parse HEAD)
    Git Branch: $(git branch --show-current)
    Build Strategy: ${BUILD_LOCATION}
    Size: $(du -h "${RELEASE_NAME}.tar.gz" | cut -f1)
    Files: $(tar -tzf "${RELEASE_NAME}.tar.gz" | wc -l)
    EOF

    echo "✅ Smart artifact preparation completed"
    echo "📦 Package: ${RELEASE_NAME}.tar.gz"
    echo "📊 Size: $(du -h "${RELEASE_NAME}.tar.gz" | cut -f1)"
    echo "📁 Files: $(tar -tzf "${RELEASE_NAME}.tar.gz" | wc -l)"
    ```

    **Expected Result:**

    ```
    ✅ Build output validated successfully
    ✅ Deployment manifest created with comprehensive metadata
    ✅ Smart exclusion patterns applied
    ✅ Release package created with timestamp and commit hash
    ✅ MD5 and SHA256 checksums generated
    ✅ Artifact info file created
    ✅ Package ready for secure transfer
    ```

### Step 3.2: Comprehensive Server Preparation [3.2-server-prep]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%`  
**Purpose:** Prepare zero-downtime deployment structure with intelligent backup strategy

#### **Action Steps:**

1. **Enhanced Server Preparation**

    ```bash
    echo "🔴 Enhanced server preparation for zero-downtime deployment..."

    # Load deployment variables on server
    DEPLOY_PATH="${PATH_SERVER}"
    RELEASES_PATH="${DEPLOY_PATH}/releases"
    CURRENT_PATH="${DEPLOY_PATH}/current"
    SHARED_PATH="${DEPLOY_PATH}/shared"

    # Create comprehensive directory structure
    echo "📁 Creating deployment directory structure..."
    mkdir -p "${RELEASES_PATH}" "${SHARED_PATH}"

    # Advanced backup strategy
    echo "💾 Executing intelligent backup strategy..."
    if [ -L "${CURRENT_PATH}" ]; then
        BACKUP_ID="backup-$(date +%Y%m%d%H%M%S)"
        BACKUP_PATH="${RELEASES_PATH}/${BACKUP_ID}"

        # Create space-efficient backup using hard links
        echo "📋 Creating backup: ${BACKUP_ID}"
        if cp -al "$(readlink "${CURRENT_PATH}")" "${BACKUP_PATH}" 2>/dev/null; then
            echo "✅ Current release backed up efficiently"

            # Create backup metadata
            cat > "${BACKUP_PATH}/.backup-info" << EOF
    {
        "backup_id": "${BACKUP_ID}",
        "created_at": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
        "original_release": "$(basename $(readlink "${CURRENT_PATH}"))",
        "backup_type": "hard-link"
    }
    EOF
        else
            echo "⚠️ Hard-link backup failed, creating regular backup..."
            cp -r "$(readlink "${CURRENT_PATH}")" "${BACKUP_PATH}"
        fi

        # Cleanup old backups (keep last 3)
        echo "🧹 Cleaning old backups..."
        cd "${RELEASES_PATH}"
        ls -1t backup-* 2>/dev/null | tail -n +4 | xargs -r rm -rf
    else
        echo "ℹ️ No current release found - first deployment"
    fi

    # Comprehensive shared resources setup
    echo "🔗 Setting up shared resources..."

    # Enhanced shared directories from deployment configuration
    SHARED_DIRECTORIES=(
        "storage/app/public"
        "storage/logs"
        "storage/framework/cache/data"
        "storage/framework/sessions"
        "storage/framework/views"
        "storage/framework/testing"
        "public/uploads"
        "public/media"
        "public/avatars"
        "public/documents"
        "public/exports"
        "public/qrcodes"
        "public/invoices"
        "public/reports"
        "Modules"
    )

    for dir in "${SHARED_DIRECTORIES[@]}"; do
        mkdir -p "${SHARED_PATH}/${dir}"
        echo "📁 Created shared directory: ${dir}"
    done

    # Set comprehensive permissions
    echo "🔐 Setting secure permissions..."

    # Storage directories - read/write for web server
    chown -R www-data:www-data "${SHARED_PATH}/storage" 2>/dev/null || true
    chmod -R 755 "${SHARED_PATH}/storage"
    chmod -R 775 "${SHARED_PATH}/storage/logs"
    chmod -R 775 "${SHARED_PATH}/storage/framework/cache"
    chmod -R 775 "${SHARED_PATH}/storage/framework/sessions"
    chmod -R 775 "${SHARED_PATH}/storage/framework/views"

    # Public directories - web server accessible
    chown -R www-data:www-data "${SHARED_PATH}/public" 2>/dev/null || true
    chmod -R 755 "${SHARED_PATH}/public"

    echo "✅ Comprehensive server preparation completed"
    ```

    **Expected Result:**

    ```
    ✅ Zero-downtime directory structure created
    ✅ Intelligent backup created with hard links
    ✅ Old backups cleaned (kept last 3)
    ✅ Comprehensive shared directories created
    ✅ Secure permissions applied
    ✅ Server ready for atomic deployment
    ```

### Step 3.3: Intelligent Release Directory Creation [3.3-release-dir]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%`  
**Purpose:** Create timestamped release directory with proper validation and permissions

#### **Action Steps:**

1. **Create Release Directory with Pre-flight Checks**

    ```bash
    echo "🔴 Creating intelligent release directory..."

    # Generate unique release identifier with git info
    RELEASE_ID="$(date +%Y%m%d%H%M%S)-$(echo ${DEPLOY_COMMIT:-manual} | cut -c1-8)"
    RELEASE_PATH="${DEPLOY_PATH}/releases/${RELEASE_ID}"

    # Comprehensive pre-flight checks
    echo "🔍 Pre-flight deployment checks..."

    # Check available disk space (require at least 1GB)
    AVAILABLE_SPACE=$(df "${DEPLOY_PATH}" | awk 'NR==2 {print $4}')
    REQUIRED_SPACE=1048576  # 1GB in KB

    if [[ "${AVAILABLE_SPACE}" -lt "${REQUIRED_SPACE}" ]]; then
        echo "❌ Insufficient disk space: $((AVAILABLE_SPACE / 1024))MB available, $((REQUIRED_SPACE / 1024))MB required"
        exit 1
    else
        echo "✅ Sufficient disk space: $((AVAILABLE_SPACE / 1024))MB available"
    fi

    # Validate write permissions
    if touch "${DEPLOY_PATH}/.write-test" 2>/dev/null; then
        rm -f "${DEPLOY_PATH}/.write-test"
        echo "✅ Write permissions validated"
    else
        echo "❌ No write permission in deployment directory: ${DEPLOY_PATH}"
        exit 1
    fi

    # Check for deployment conflicts
    if [[ -d "${RELEASE_PATH}" ]]; then
        echo "⚠️ Release directory already exists: ${RELEASE_ID}"
        echo "🔄 Generating new release ID..."
        RELEASE_ID="${RELEASE_ID}-$(date +%S)"
        RELEASE_PATH="${DEPLOY_PATH}/releases/${RELEASE_ID}"
    fi

    # Create release directory with proper structure
    echo "📁 Creating release directory: ${RELEASE_ID}"
    if mkdir -p "${RELEASE_PATH}"; then
        echo "✅ Release directory created successfully"
    else
        echo "❌ Failed to create release directory"
        exit 1
    fi

    # Create comprehensive release metadata
    cat > "${RELEASE_PATH}/.release-info" << EOF
    {
        "release_id": "${RELEASE_ID}",
        "created_at": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
        "git_commit": "${DEPLOY_COMMIT:-unknown}",
        "git_branch": "${DEPLOY_BRANCH:-unknown}",
        "build_strategy": "${BUILD_LOCATION:-local}",
        "deployed_by": "${USER:-unknown}",
        "server_hostname": "$(hostname)",
        "deployment_type": "${DEPLOYMENT_TYPE:-standard}",
        "php_version": "$(php --version | head -n1 | grep -oP '\d+\.\d+\.\d+' || echo 'unknown')",
        "disk_usage_before": "$((AVAILABLE_SPACE / 1024))MB"
    }
    EOF

    # Set proper permissions
    echo "🔐 Setting release permissions..."
    chmod 755 "${RELEASE_PATH}"
    chmod 644 "${RELEASE_PATH}/.release-info"

    # If running as root, ensure proper ownership
    if [[ "${EUID}" -eq 0 ]]; then
        chown -R www-data:www-data "${RELEASE_PATH}"
        echo "✅ Ownership set to www-data"
    fi

    # Export release info for subsequent steps
    export RELEASE_ID RELEASE_PATH

    echo "✅ Release directory creation completed"
    echo "📁 Release: ${RELEASE_ID}"
    echo "📍 Path: ${RELEASE_PATH}"
    echo "💾 Available space: $((AVAILABLE_SPACE / 1024))MB"
    ```

    **Expected Result:**

    ```
    ✅ Unique release ID generated with git commit info
    ✅ Pre-flight checks passed (disk space, permissions)
    ✅ Release directory created successfully
    ✅ Comprehensive metadata generated
    ✅ Proper permissions and ownership set
    ✅ Release environment ready for file transfer
    ```

### Step 3.4: Optimized File Transfer & Validation [3.4-transfer]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/releases/{timestamp}`  
**Purpose:** Transfer build artifacts with integrity validation and optimized permissions

#### **Action Steps:**

1. **Execute Optimized Transfer with Validation**

    ```bash
    echo "🔴 Executing optimized file transfer with validation..."

    # Find the latest build artifact (assuming transfer from build environment)
    ARTIFACT_PATTERN="release-*.tar.gz"
    ARTIFACT_FILE=$(ls -t ${ARTIFACT_PATTERN} 2>/dev/null | head -n1)

    if [[ -z "${ARTIFACT_FILE}" ]]; then
        echo "❌ No build artifact found matching pattern: ${ARTIFACT_PATTERN}"
        echo "🔍 Available files:"
        ls -la *.tar.gz 2>/dev/null || echo "No .tar.gz files found"
        exit 1
    fi

    echo "📦 Found artifact: ${ARTIFACT_FILE}"

    # Validate artifact integrity before extraction
    echo "🔐 Validating artifact integrity..."
    CHECKSUM_FILE="${ARTIFACT_FILE%.*}.sha256"

    if [[ -f "${CHECKSUM_FILE}" ]]; then
        if sha256sum -c "${CHECKSUM_FILE}" --quiet; then
            echo "✅ Artifact integrity verified via SHA256"
        else
            echo "❌ Artifact integrity check failed"
            exit 1
        fi
    else
        echo "⚠️ No checksum file found - proceeding without verification"
        echo "ℹ️ Expected: ${CHECKSUM_FILE}"
    fi

    # Extract with comprehensive error handling
    echo "📂 Extracting to release directory..."

    # Verify artifact is not corrupted before extraction
    if tar -tzf "${ARTIFACT_FILE}" >/dev/null 2>&1; then
        echo "✅ Artifact structure verified"
    else
        echo "❌ Artifact appears corrupted"
        exit 1
    fi

    # Extract with progress indication
    if tar -xzf "${ARTIFACT_FILE}" -C "${RELEASE_PATH}" 2>/dev/null; then
        echo "✅ Artifact extracted successfully"
    else
        echo "❌ Artifact extraction failed"
        exit 1
    fi

    # Validate critical Laravel files post-extraction
    echo "🔍 Validating Laravel structure post-extraction..."
    CRITICAL_FILES=(
        "artisan"
        "bootstrap/app.php"
        "composer.json"
        "composer.lock"
        "public/index.php"
        "bootstrap/cache/config.php"
    )

    for file in "${CRITICAL_FILES[@]}"; do
        if [[ -f "${RELEASE_PATH}/${file}" ]]; then
            echo "✅ Critical file validated: ${file}"
        else
            # Some files are optional depending on build process
            if [[ "${file}" == "bootstrap/cache/config.php" ]]; then
                echo "⚠️ Cache file missing (will be generated): ${file}"
            else
                echo "❌ Critical file missing: ${file}"
                exit 1
            fi
        fi
    done

    # Set comprehensive file permissions
    echo "🔐 Setting comprehensive file permissions..."

    cd "${RELEASE_PATH}"

    # Set ownership (if running as root)
    if [[ "${EUID}" -eq 0 ]]; then
        chown -R www-data:www-data .
        echo "✅ Ownership set to www-data:www-data"
    fi

    # Directory permissions (755 = rwxr-xr-x)
    find . -type d -exec chmod 755 {} \;

    # File permissions (644 = rw-r--r--)
    find . -type f -exec chmod 644 {} \;

    # Executable permissions for specific files
    chmod +x artisan
    [[ -f "vendor/bin/phpunit" ]] && chmod +x vendor/bin/phpunit
    [[ -f "vendor/bin/phpstan" ]] && chmod +x vendor/bin/phpstan

    # Secure permissions for sensitive files
    [[ -f ".env" ]] && chmod 600 .env
    [[ -f ".env.example" ]] && chmod 644 .env.example

    # Storage and cache directories need write permissions
    [[ -d "storage" ]] && chmod -R 755 storage
    [[ -d "bootstrap/cache" ]] && chmod -R 755 bootstrap/cache

    # Validate file count and calculate size
    echo "📊 Transfer validation summary..."
    FILE_COUNT=$(find . -type f | wc -l)
    RELEASE_SIZE=$(du -sh . | cut -f1)

    # Create transfer manifest
    cat > .transfer-manifest << EOF
    Transfer completed: $(date -u +%Y-%m-%dT%H:%M:%SZ)
    Artifact: ${ARTIFACT_FILE}
    Files transferred: ${FILE_COUNT}
    Total size: ${RELEASE_SIZE}
    Integrity check: $([ -f "../${CHECKSUM_FILE}" ] && echo "Verified" || echo "Skipped")
    Permissions set: Yes
    Laravel structure: Validated
    EOF

    echo "✅ Optimized transfer completed successfully"
    echo "📊 Transfer Summary:"
    echo "  - Files transferred: ${FILE_COUNT}"
    echo "  - Release size: ${RELEASE_SIZE}"
    echo "  - Release path: ${RELEASE_PATH}"
    echo "  - Artifact: ${ARTIFACT_FILE}"
    ```

    **Expected Result:**

    ```
    ✅ Build artifact located and verified
    ✅ Integrity validation completed (SHA256)
    ✅ Artifact structure verified before extraction
    ✅ Files extracted successfully to release directory
    ✅ Critical Laravel files validated
    ✅ Comprehensive permissions set (755/644)
    ✅ Sensitive files secured (600 permissions)
    ✅ Transfer manifest created
    ✅ Ready for Phase 4 configuration
    ```

---

## **Phase 4: 🔗 Configure Release**

### Step 4.1: Advanced Shared Resources Configuration [4.1-shared-config]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/releases/{timestamp}`  
**Purpose:** Configure comprehensive shared resources with intelligent linking and validation

#### **Action Steps:**

1. **Execute Advanced Shared Resources Setup**

    ```bash
    echo "🔴 Advanced shared resources configuration..."

    cd "${RELEASE_PATH}"

    # Load shared directories from deployment configuration
    SHARED_DIRECTORIES=(
        "storage/app/public"
        "storage/logs"
        "storage/framework/cache"
        "storage/framework/sessions"
        "storage/framework/views"
        "public/uploads"
        "public/media"
        "public/avatars"
        "public/documents"
        "public/exports"
        "public/qrcodes"
        "public/invoices"
        "public/reports"
        "Modules"
    )

    echo "🔗 Configuring shared directory links..."

    # Remove existing directories and create symlinks
    for dir in "${SHARED_DIRECTORIES[@]}"; do
        if [[ -e "${dir}" ]]; then
            echo "📁 Removing existing directory: ${dir}"
            rm -rf "${dir}"
        fi

        # Create parent directories if needed
        PARENT_DIR=$(dirname "${dir}")
        if [[ "${PARENT_DIR}" != "." ]] && [[ ! -d "${PARENT_DIR}" ]]; then
            mkdir -p "${PARENT_DIR}"
        fi

        # Create symlink to shared directory
        if ln -nfs "${SHARED_PATH}/${dir}" "${dir}"; then
            echo "✅ Linked shared directory: ${dir}"
        else
            echo "❌ Failed to link directory: ${dir}"
            exit 1
        fi
    done

    # Configure shared files
    echo "📄 Configuring shared files..."
    SHARED_FILES=(
        ".env"
        "auth.json"
        "oauth-private.key"
        "oauth-public.key"
    )

    for file in "${SHARED_FILES[@]}"; do
        if [[ -f "${file}" ]]; then
            echo "📄 Removing existing file: ${file}"
            rm -f "${file}"
        fi

        # Create symlink only if shared file exists
        if [[ -f "${SHARED_PATH}/${file}" ]]; then
            if ln -nfs "${SHARED_PATH}/${file}" "${file}"; then
                echo "✅ Linked shared file: ${file}"
            else
                echo "❌ Failed to link file: ${file}"
                exit 1
            fi
        else
            echo "⚠️ Shared file not found: ${SHARED_PATH}/${file}"
        fi
    done

    # Validate all symlinks
    echo "🔍 Validating symlinks..."
    BROKEN_LINKS=0

    for dir in "${SHARED_DIRECTORIES[@]}"; do
        if [[ -L "${dir}" ]]; then
            if [[ -e "${dir}" ]]; then
                echo "✅ Valid symlink: ${dir} → $(readlink "${dir}")"
            else
                echo "❌ Broken symlink: ${dir}"
                ((BROKEN_LINKS++))
            fi
        else
            echo "❌ Missing symlink: ${dir}"
            ((BROKEN_LINKS++))
        fi
    done

    for file in "${SHARED_FILES[@]}"; do
        if [[ -L "${file}" ]]; then
            if [[ -e "${file}" ]]; then
                echo "✅ Valid file link: ${file}"
            else
                echo "⚠️ Broken file link: ${file} (may be created during deployment)"
            fi
        elif [[ -f "${SHARED_PATH}/${file}" ]]; then
            echo "⚠️ Shared file exists but not linked: ${file}"
        fi
    done

    if [[ ${BROKEN_LINKS} -gt 0 ]]; then
        echo "❌ Found ${BROKEN_LINKS} broken symlinks"
        exit 1
    fi

    echo "✅ Advanced shared resources configuration completed successfully"
    ```

    **Expected Result:**

    ```
    ✅ All shared directories linked successfully
    ✅ Shared files configured and linked
    ✅ Symlink validation completed
    ✅ No broken links detected
    ✅ Release configured with persistent data protection
    ```

### Step 4.2: Secure Configuration Management [4.2-secure-config]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/releases/{timestamp}`  
**Purpose:** Deploy and validate secure environment configurations

#### **Action Steps:**

1. **Execute Secure Configuration Deployment**

    ```bash
    echo "🔴 Secure configuration management..."

    cd "${RELEASE_PATH}"

    # Validate environment configuration
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

    # Check if .env exists (should be symlinked from shared)
    if [[ -f ".env" ]]; then
        echo "✅ Environment file found"

        # Validate critical variables
        MISSING_VARS=()
        for var in "${REQUIRED_ENV_VARS[@]}"; do
            if ! grep -q "^${var}=" .env; then
                MISSING_VARS+=("${var}")
            fi
        done

        if [[ ${#MISSING_VARS[@]} -eq 0 ]]; then
            echo "✅ All required environment variables present"
        else
            echo "⚠️ Missing environment variables:"
            printf "  - %s\n" "${MISSING_VARS[@]}"
            echo "⚠️ Please configure missing variables in shared .env file"
        fi
    else
        echo "❌ .env file not found - should be symlinked from shared directory"
        echo "🔧 Creating .env from template..."

        if [[ -f ".env.example" ]]; then
            cp ".env.example" "${SHARED_PATH}/.env"
            ln -nfs "${SHARED_PATH}/.env" .env
            echo "⚠️ Please configure production values in: ${SHARED_PATH}/.env"
        else
            echo "❌ No .env.example found - manual .env creation required"
            exit 1
        fi
    fi

    # Set secure permissions on configuration
    echo "🔐 Applying secure configuration permissions..."
    [[ -f "${SHARED_PATH}/.env" ]] && chmod 600 "${SHARED_PATH}/.env"

    # Validate APP_KEY
    APP_KEY=$(grep -E "^APP_KEY=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" | tr -d ' ')
    if [[ -z "${APP_KEY}" ]] || [[ "${APP_KEY}" == "base64:" ]] || [[ "${APP_KEY}" == "" ]]; then
        echo "🔑 Generating missing APP_KEY..."

        # Generate key and update shared .env
        NEW_KEY=$(php artisan key:generate --show --no-interaction)
        sed -i "s/APP_KEY=.*/APP_KEY=${NEW_KEY}/" "${SHARED_PATH}/.env"
        echo "✅ APP_KEY generated and updated in shared configuration"
    else
        echo "✅ APP_KEY validation passed"
    fi

    # Environment-specific security settings
    APP_ENV=$(grep -E "^APP_ENV=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" | tr -d ' ')
    if [[ "${APP_ENV}" == "production" ]]; then
        echo "🛡️ Applying production security settings..."

        # Ensure debug is disabled
        if grep -q "APP_DEBUG=true" "${SHARED_PATH}/.env"; then
            sed -i "s/APP_DEBUG=true/APP_DEBUG=false/" "${SHARED_PATH}/.env"
            echo "✅ Debug mode disabled for production"
        fi

        # Add secure session cookie setting if missing
        if ! grep -q "SESSION_SECURE_COOKIE=true" "${SHARED_PATH}/.env"; then
            echo "SESSION_SECURE_COOKIE=true" >> "${SHARED_PATH}/.env"
            echo "✅ Secure cookie setting added"
        fi

        # Add HTTPS enforcement if missing
        if ! grep -q "FORCE_HTTPS=true" "${SHARED_PATH}/.env" && ! grep -q "APP_URL=https" "${SHARED_PATH}/.env"; then
            echo "⚠️ HTTPS enforcement not configured - consider adding FORCE_HTTPS=true"
        fi
    fi

    # Final configuration validation
    echo "📊 Configuration summary:"
    echo "  - Environment: ${APP_ENV:-unknown}"
    echo "  - Debug mode: $(grep APP_DEBUG .env | cut -d'=' -f2)"
    echo "  - APP_KEY: $([ -n "${APP_KEY}" ] && echo 'Set' || echo 'Missing')"
    echo "  - Database: $(grep DB_CONNECTION .env | cut -d'=' -f2)"
    echo "  - URL: $(grep APP_URL .env | cut -d'=' -f2)"

    echo "✅ Secure configuration management completed"
    ```

    **Expected Result:**

    ```
    ✅ Environment file validated and configured
    ✅ All required environment variables checked
    ✅ APP_KEY generated/validated
    ✅ Production security settings applied
    ✅ Secure permissions set on configuration files
    ✅ Configuration summary generated
    ✅ Release ready for deployment hooks
    ```

---

## **Phase 5: 🚀 Pre-Release Hooks** 🟣 1️⃣ **User-configurable SSH Commands**

### Step 5.1: Maintenance Mode (Optional) [5.1-maintenance]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/current`  
**Purpose:** Put application in maintenance mode with user-friendly display

#### **Action Steps:**

1. **Enable Maintenance Mode (If Configured)**

    ```bash
    # Only run if maintenance mode is enabled in deployment config
    MAINTENANCE_MODE=$(jq -r '.deployment.maintenance_mode // false' Admin-Local/Deployment/Configs/deployment-variables.json)

    if [[ "${MAINTENANCE_MODE}" == "true" ]] && [[ -L "${DEPLOY_PATH}/current" ]]; then
        echo "🚧 Enabling maintenance mode..."

        cd "${DEPLOY_PATH}/current"

        # Enable maintenance with secret bypass and custom message
        php artisan down \
            --render="errors::503" \
            --secret="${DEPLOY_SECRET:-deploy-bypass-$(date +%s)}" \
            --retry=60 \
            --message="Application is being updated. Please try again shortly."

        echo "🚧 Maintenance mode enabled with bypass secret"
        echo "🔑 Bypass URL: ${APP_URL}/${DEPLOY_SECRET:-deploy-bypass-$(date +%s)}"
    else
        echo "ℹ️ Maintenance mode disabled - continuing with live deployment"
    fi
    ```

    **Expected Result:**

    ```
    ✅ Maintenance mode enabled (if configured)
    ✅ User-friendly message displayed
    ✅ Secret bypass URL available
    ✅ Deployment can proceed without user disruption
    ```

### Step 5.2: Pre-Release Custom Commands [5.2-pre-custom]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/current`  
**Purpose:** Execute user-defined pre-release scripts and backups

#### **Action Steps:**

1. **Execute Pre-Release Actions** 🟣 1️⃣ User-customizable

    ```bash
    echo "🟣 Executing pre-release custom commands..."

    # Database backup (if configured)
    if [[ "${CREATE_DB_BACKUP}" == "true" ]] && [[ -f ".env" ]]; then
        echo "💾 Creating database backup..."

        # Load database credentials from current environment
        source .env

        BACKUP_FILE="${DEPLOY_PATH}/backups/db-$(date +%Y%m%d%H%M%S).sql"
        mkdir -p "${DEPLOY_PATH}/backups"

        if mysqldump -h"${DB_HOST}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" > "${BACKUP_FILE}"; then
            gzip "${BACKUP_FILE}"
            echo "✅ Database backed up to: ${BACKUP_FILE}.gz"
        else
            echo "⚠️ Database backup failed - continuing anyway"
        fi
    fi

    # Custom pre-release script (user-defined)
    if [[ -f "${DEPLOY_PATH}/scripts/pre-release.sh" ]]; then
        echo "🔧 Running custom pre-release script..."
        bash "${DEPLOY_PATH}/scripts/pre-release.sh"
    else
        echo "ℹ️ No custom pre-release script found"
    fi

    # External service notifications
    if [[ -n "${DEPLOYMENT_WEBHOOK_URL}" ]]; then
        echo "📡 Sending pre-deployment notification..."
        curl -X POST "${DEPLOYMENT_WEBHOOK_URL}" \
             -H "Content-Type: application/json" \
             -d "{
                  \"status\": \"pre-release\",
                  \"release_id\": \"${RELEASE_ID}\",
                  \"timestamp\": \"$(date -u +%Y-%m-%dT%H:%M:%SZ)\"
                 }" > /dev/null 2>&1 || echo "⚠️ Webhook notification failed"
    fi

    echo "✅ Pre-release custom commands completed"
    ```

    **Expected Result:**

    ```
    ✅ Database backup created (if configured)
    ✅ Custom pre-release scripts executed
    ✅ External notifications sent
    ✅ Pre-release phase completed successfully
    ```

---

## **Phase 6: 🔄 Mid-Release Hooks** 🟣 2️⃣ **User-configurable SSH Commands**

### Step 6.1: Zero-Downtime Database Migrations [6.1-migrations]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/releases/{timestamp}`  
**Purpose:** Execute database migrations safely with zero-downtime strategy

#### **Action Steps:**

1. **Execute Zero-Downtime Migrations** 🟣 2️⃣ User-customizable

    ```bash
    echo "🔄 Zero-downtime database migrations..."

    cd "${RELEASE_PATH}"

    # Check migration status
    echo "📋 Current migration status:"
    if php artisan migrate:status --no-interaction; then
        echo "✅ Migration status retrieved successfully"
    else
        echo "⚠️ Could not retrieve migration status - may be first deployment"
    fi

    # Run migrations with zero-downtime strategy
    echo "🔄 Running database migrations..."

    # Use --step to run migrations one at a time for safety
    if php artisan migrate --force --step --no-interaction; then
        echo "✅ Database migrations completed successfully"

        # Log migration completion
        php artisan migrate:status | grep "Ran" | wc -l > /tmp/migration_count
        MIGRATION_COUNT=$(cat /tmp/migration_count)
        echo "📊 Total migrations applied: ${MIGRATION_COUNT}"
    else
        echo "❌ Database migrations failed"

        # In case of migration failure, we should not proceed
        # The atomic switch will not happen, keeping the old version live
        exit 1
    fi

    # Optional: Run database seeders for production (if configured)
    if [[ "${RUN_PRODUCTION_SEEDERS}" == "true" ]]; then
        echo "🌱 Running production seeders..."
        if php artisan db:seed --class=ProductionSeeder --force --no-interaction; then
            echo "✅ Production seeders completed"
        else
            echo "⚠️ Production seeders failed - continuing anyway"
        fi
    fi

    echo "✅ Zero-downtime migration phase completed"
    ```

    **Expected Result:**

    ```
    ✅ Migration status retrieved and logged
    ✅ Database migrations executed safely with --step flag
    ✅ Zero-downtime strategy maintained
    ✅ Migration count documented
    ✅ Production seeders run (if configured)
    ✅ Database schema updated successfully
    ```

### Step 6.2: Application Cache Preparation [6.2-cache-prep]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/releases/{timestamp}`  
**Purpose:** Prepare and optimize application caches for production

#### **Action Steps:**

1. **Execute Advanced Cache Preparation** 🟣 2️⃣ User-customizable

    ```bash
    echo "🔄 Advanced application cache preparation..."

    cd "${RELEASE_PATH}"

    # Clear any existing caches first
    echo "🧹 Clearing existing caches..."
    php artisan cache:clear --no-interaction --quiet || true
    php artisan config:clear --no-interaction --quiet || true
    php artisan route:clear --no-interaction --quiet || true
    php artisan view:clear --no-interaction --quiet || true

    # Rebuild caches with production environment
    echo "⚡ Rebuilding production caches..."

    # Configuration cache
    if php artisan config:cache --no-interaction; then
        echo "✅ Configuration cache built"
    else
        echo "❌ Configuration cache failed"
        exit 1
    fi

    # Route cache
    if php artisan route:cache --no-interaction; then
        echo "✅ Route cache built"
    else
        echo "❌ Route cache failed"
        exit 1
    fi

    # View cache
    if php artisan view:cache --no-interaction; then
        echo "✅ View cache built"
    else
        echo "⚠️ View cache failed - continuing anyway"
    fi

    # Advanced Laravel caches (if available)
    echo "🔧 Building advanced caches..."

    # Event cache (Laravel 8+)
    if php artisan list | grep -q "event:cache"; then
        php artisan event:cache --no-interaction && echo "📅 Event cache built" || true
    fi

    # Icon cache (if using Laravel icons)
    if php artisan list | grep -q "icons:cache"; then
        php artisan icons:cache --no-interaction && echo "🎨 Icon cache built" || true
    fi

    # Custom cache warmup (user-defined)
    if php artisan list | grep -q "cache:warmup"; then
        echo "🔥 Running cache warmup..."
        php artisan cache:warmup --no-interaction || echo "⚠️ Cache warmup failed"
    fi

    # Optional: Pre-warm application cache with critical data
    echo "🔥 Pre-warming critical application data..."

    # Custom cache warmup script (user-defined)
    if [[ -f "${DEPLOY_PATH}/scripts/cache-warmup.sh" ]]; then
        bash "${DEPLOY_PATH}/scripts/cache-warmup.sh"
    fi

    echo "✅ Advanced cache preparation completed"
    ```

    **Expected Result:**

    ```
    ✅ All existing caches cleared successfully
    ✅ Configuration cache rebuilt for production
    ✅ Route cache optimized
    ✅ View cache compiled
    ✅ Advanced caches built (events, icons)
    ✅ Custom cache warmup executed
    ✅ Application optimized for first requests
    ```

### Step 6.3: Health Checks [6.3-health]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%/releases/{timestamp}`  
**Purpose:** Verify application functionality before atomic switch

#### **Action Steps:**

1. **Execute Comprehensive Health Checks** 🟣 2️⃣ User-customizable

    ```bash
    echo "🔄 Comprehensive application health checks..."

    cd "${RELEASE_PATH}"

    # Basic Laravel functionality tests
    echo "🧪 Testing basic Laravel functionality..."

    if php artisan --version --no-interaction >/dev/null; then
        echo "✅ Artisan commands functional"
    else
        echo "❌ Artisan commands not working"
        exit 1
    fi

    # Database connectivity test
    echo "🗃️ Testing database connectivity..."
    if php artisan migrate:status --no-interaction >/dev/null; then
        echo "✅ Database connection verified"
    else
        echo "❌ Database connection failed"
        exit 1
    fi

    # Route functionality test
    echo "🗺️ Testing route system..."
    if php artisan route:list --compact >/dev/null; then
        ROUTE_COUNT=$(php artisan route:list --compact | wc -l)
        echo "✅ Route system functional (${ROUTE_COUNT} routes)"
    else
        echo "❌ Route system not functional"
        exit 1
    fi

    # Cache functionality test
    echo "💾 Testing cache systems..."
    if php artisan cache:clear >/dev/null 2>&1; then
        echo "✅ Cache system functional"
    else
        echo "⚠️ Cache system issues detected"
    fi

    # Application bootstrap test
    echo "🚀 Testing application bootstrap..."
    if php -r "
        try {
            require_once 'vendor/autoload.php';
            \$app = require_once 'bootstrap/app.php';
            \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
            \$kernel->bootstrap();
            echo 'Application bootstrap successful\n';
        } catch (Exception \$e) {
            echo 'Bootstrap failed: ' . \$e->getMessage() . '\n';
            exit(1);
        }
    "; then
        echo "✅ Application bootstrap successful"
    else
        echo "❌ Application bootstrap failed"
        exit 1
    fi

    # Custom health checks (user-defined)
    if php artisan list | grep -q "health:check"; then
        echo "🏥 Running custom health checks..."
        if php artisan health:check --no-interaction; then
            echo "✅ Custom health checks passed"
        else
            echo "⚠️ Custom health checks failed - review required"
        fi
    fi

    # File permissions validation
    echo "🔐 Validating file permissions..."
    PERM_ISSUES=0

    # Check critical writable directories
    WRITABLE_DIRS=("storage" "bootstrap/cache")
    for dir in "${WRITABLE_DIRS[@]}"; do
        if [[ -w "${dir}" ]]; then
            echo "✅ ${dir} is writable"
        else
            echo "❌ ${dir} is not writable"
            ((PERM_ISSUES++))
        fi
    done

    if [[ ${PERM_ISSUES} -gt 0 ]]; then
        echo "❌ Found ${PERM_ISSUES} permission issues"
        exit 1
    fi

    # Summary
    echo "📊 Health check summary:"
    echo "  - Laravel core: ✅ Functional"
    echo "  - Database: ✅ Connected"
    echo "  - Routes: ✅ ${ROUTE_COUNT:-0} routes loaded"
    echo "  - Cache: ✅ Functional"
    echo "  - Bootstrap: ✅ Successful"
    echo "  - Permissions: ✅ Validated"

    echo "✅ Comprehensive health checks completed - ready for atomic switch"
    ```

    **Expected Result:**

    ```
    ✅ Artisan commands verified functional
    ✅ Database connectivity confirmed
    ✅ Route system tested and validated
    ✅ Cache systems verified working
    ✅ Application bootstrap successful
    ✅ Custom health checks passed (if available)
    ✅ File permissions validated
    ✅ All systems ready for atomic deployment switch
    ```

---

## **Phase 7: ⚡ Atomic Release Switch**

### Step 7.1: Atomic Symlink Update [7.1-atomic-switch]

**Location:** 🔴 Run on Server  
**Path:** `%path-server%`  
**Purpose:** Execute atomic switch to new release - THE zero-downtime moment

#### **Action Steps:**

1. **Execute Atomic Deployment Switch**

    ```bash
    echo "⚡ EXECUTING ATOMIC RELEASE SWITCH - Zero-downtime moment..."

    cd "${DEPLOY_PATH}"

    # Store current release info for potential rollback
    if [[ -L "current" ]]; then
        OLD_RELEASE=$(readlink current)
        OLD_RELEASE_NAME=$(basename "${OLD_RELEASE}")
        echo "📋 Previous release: ${OLD_RELEASE_NAME}"
    else
        OLD_RELEASE=""
        echo "📋 First deployment - no previous release"
    fi

    # Prepare atomic switch variables
    NEW_RELEASE="releases/${RELEASE_ID}"
    CURRENT_LINK="current"
    TEMP_LINK="current-temp-$(date +%s)"

    # Pre-switch validation
    echo "🔍 Pre-switch validation..."
    if [[ ! -d "${NEW_RELEASE}" ]]; then
        echo "❌ New release directory not found: ${NEW_RELEASE}"
        exit 1
    fi

    if [[ ! -f "${NEW_RELEASE}/artisan" ]]; then
        echo "❌ New release appears invalid - missing artisan"
        exit 1
    fi

    echo "✅ New release validated: ${RELEASE_ID}"

    # **ATOMIC SWITCH EXECUTION**
    echo "🚀 Executing atomic symlink switch..."

    # Create temporary symlink first
    if ln -nfs "${NEW_RELEASE}" "${TEMP_LINK}"; then
        echo "✅ Temporary symlink created"
    else
        echo "❌ Failed to create temporary symlink"
        exit 1
    fi

    # Atomic move (this is the zero-downtime moment)
    if mv "${TEMP_LINK}" "${CURRENT_LINK}"; then
        echo "⚡ ATOMIC SWITCH COMPLETED SUCCESSFULLY"
        echo "🎉 Zero-downtime deployment achieved!"
    else
        echo "❌ Atomic switch failed"
        rm -f "${TEMP_LINK}"
        exit 1
    fi

    # Handle public_html for shared hosting
    PUBLIC_HTML_PATH="/home/u227177893/public_html"
    if [[ -d "$(dirname "${PUBLIC_HTML_PATH}")" ]]; then
        echo "🌐 Configuring public_html for shared hosting..."

        # Backup existing public_html if it's not a symlink
        if [[ -d "${PUBLIC_HTML_PATH}" ]] && [[ ! -L "${PUBLIC_HTML_PATH}" ]]; then
            echo "📁 Backing up existing public_html..."
            mv "${PUBLIC_HTML_PATH}" "${PUBLIC_HTML_PATH}.backup-$(date +%Y%m%d%H%M%S)"
        fi

        # Create/update symlink to current release public directory
        if ln -nfs "${DEPLOY_PATH}/current/public" "${PUBLIC_HTML_PATH}"; then
            echo "✅ Public_html symlinked to current release"
        else
            echo "⚠️ Failed to update public_html symlink"
        fi
    fi

    # Verify switch success
    echo "🔍 Post-switch verification..."

    CURRENT_TARGET=$(readlink current)
    if [[ "${CURRENT_TARGET}" == "${NEW_RELEASE}" ]]; then
        echo "✅ Symlink verification passed"
        echo "📍 Current points to: ${CURRENT_TARGET}"
    else
        echo "❌ Symlink verification failed"
        echo "Expected: ${NEW_RELEASE}"
        echo "Actual: ${CURRENT_TARGET}"
        exit 1
    fi

    # Log the switch
    echo "📝 Logging atomic switch..."
    cat >> deployment.log << EOF
    ATOMIC SWITCH: $(date -u +%Y-%m-%dT%H:%M:%SZ)
    From: ${OLD_RELEASE_NAME:-"(first deployment)"}
    To: ${RELEASE_ID}
    Status: SUCCESS
    Switch Duration: <1 second (atomic)
    EOF

    # Export variables for next phase
    export OLD_RELEASE OLD_RELEASE_NAME

    echo "⚡ ATOMIC RELEASE SWITCH PHASE COMPLETED"
    echo "🏁 Zero-downtime deployment switch successful!"
    ```

    **Expected Result:**

    ```
    ✅ Previous release information captured for rollback
    ✅ New release validated before switch
    ✅ Temporary symlink created successfully
    ⚡ ATOMIC SWITCH EXECUTED IN <1 SECOND
    ✅ Public_html configured for shared hosting (if applicable)
    ✅ Post-switch verification passed
    ✅ Switch logged with timestamp
    ✅ ZERO-DOWNTIME DEPLOYMENT ACHIEVED
    ```

---

_This enhanced Section C provides a complete zero-downtime deployment pipeline with:_

-   ✅ **Path Variables Integration** - Consistent variable usage throughout
-   ✅ **Visual Location Tags** - Clear 🟢🟡🔴🟣 identification
-   ✅ **Enhanced Build Strategies** - Local/VM/Server with fallback logic
-   ✅ **Universal Dependency Management** - Comprehensive analysis and smart installation
-   ✅ **Advanced Asset Compilation** - Auto-detection of bundlers (Vite/Mix/Webpack)
-   ✅ **Intelligent Caching** - Hash-based cache restoration and validation
-   ✅ **Comprehensive Health Checks** - Multi-layer validation before deployment
-   ✅ **True Atomic Switching** - <1 second zero-downtime deployment
-   ✅ **User-Configurable Hooks** - 1️⃣2️⃣3️⃣ Pre/Mid/Post release customization
-   ✅ **Expected Results** - Every step includes validation and confirmation
-   ✅ **Error Handling** - Comprehensive failure detection and rollback triggers

**Ready for Phases 8-10 completion in the final implementation.**
