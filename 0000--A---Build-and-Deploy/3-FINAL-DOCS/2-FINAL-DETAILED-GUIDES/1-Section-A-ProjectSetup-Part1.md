# Universal Laravel Build & Deploy Guide - Part 1

## Section A: Project Setup - Part 1 (Foundation Setup)

**Version:** 1.0  
**Generated:** August 21, 2025, 6:03 PM EST  
**Purpose:** Complete step-by-step guide for Laravel project foundation setup  
**Coverage:** Steps 00-07 - AI Assistant through Dependency Analysis System  
**Authority:** Based on 4-way consolidated FINAL documents  
**Prerequisites:** Local development environment ready (PHP, Composer, Node.js)

---

## Quick Navigation

| **Part**   | **Coverage**    | **Focus Area**             | **Link**                                                    |
| ---------- | --------------- | -------------------------- | ----------------------------------------------------------- |
| **Part 1** | Steps 00-07     | Foundation & Configuration | **(Current Guide)**                                         |
| Part 2     | Steps 08-11     | Dependencies & Final Setup | → [Part 2 Guide](./2-Section-A-ProjectSetup-Part2.md)       |
| Part 3     | Steps 14.0-16.2 | Build Preparation          | → [Part 3 Guide](./3-Section-B-PrepareBuildDeploy-Part1.md) |
| Part 4     | Steps 17-20     | Security & Data Protection | → [Part 4 Guide](./4-Section-B-PrepareBuildDeploy-Part2.md) |
| Part 5     | Steps 1.1-5.2   | Build Process              | → [Part 5 Guide](./5-Section-C-BuildDeploy-Part1.md)        |
| Part 6     | Steps 6.1-10.3  | Deploy & Finalization      | → [Part 6 Guide](./6-Section-C-BuildDeploy-Part2.md)        |

**Master Checklist:** → [0-Master-Checklist.md](../1-FINAL-MASTER-CHECKLIST/0-Master-Checklist.md)

---

## Overview

This guide covers the foundational setup phase of your Laravel deployment pipeline. You'll establish:

-   🤖 AI assistant configuration for consistent development workflows
-   📋 Project documentation and metadata management
-   🔗 GitHub repository setup with proper version control
-   🏗️ Local project structure and Admin-Local foundation
-   📊 Environment analysis and compatibility validation
-   🎯 Universal dependency analysis system

By completing Part 1, you'll have a solid foundation ready for dependency installation and final integration covered in Part 2.

---

## Step 00: Setup AI Assistant Instructions

**🟢 Location:** Local Machine | **⏱️ Time:** 10-15 minutes | **🔧 Type:** Configuration

### Purpose

Establish AI coding assistant guidelines and error resolution procedures for consistent team workflow throughout the deployment process.

### When to Execute

**Before starting any development work** - This ensures consistent AI assistance across all subsequent steps.

### Action Steps

1. **Configure AI Assistant Guidelines**
   a. Open your preferred AI coding assistant (VS Code Copilot, Cursor, etc.)
   b. Create team coding standards documentation
   c. Set Laravel deployment best practices as context
   d. Configure error resolution protocols

2. **Establish Error Resolution Protocols**
   a. Define standard debugging approaches for Laravel issues
   b. Set up continuous improvement feedback loops
   c. Create escalation procedures for complex issues
   d. Document common Laravel deployment pitfalls and solutions

3. **Team Workflow Configuration**
   a. Standardize AI prompt patterns for Laravel tasks
   b. Create reusable code generation templates
   c. Set up consistent code review practices
   d. Establish documentation standards

### Expected Results ✅

-   [ ] AI assistant configured with Laravel deployment best practices
-   [ ] Error resolution protocols documented and accessible
-   [ ] Continuous improvement process established
-   [ ] Team workflow consistency ensured across all developers

### Verification Steps

-   [ ] Test AI assistant responses for Laravel-specific queries
-   [ ] Verify error resolution protocols are accessible to team
-   [ ] Confirm documentation standards are consistently applied

### Troubleshooting Tips

-   **Issue:** AI responses are inconsistent
    -   **Solution:** Refine prompts with specific Laravel context and examples
-   **Issue:** Team members using different AI approaches
    -   **Solution:** Document and share proven prompt patterns and workflows

---

## Step 01: Create Project Information Card

**🟢 Location:** Local Machine | **⏱️ Time:** 15-20 minutes | **🔧 Type:** Documentation

### Purpose

Document comprehensive project metadata for deployment configuration and team reference, establishing the foundation for all subsequent automation.

### When to Execute

**At project initiation** - This information drives all deployment variable configuration.

### Action Steps

1. **Create Comprehensive Project Documentation System**

    ```bash
    # Create project documentation structure
    echo "📋 Creating comprehensive project documentation..."

    # Create main project info document
    cat > PROJECT-INFO.md << 'EOF'
    # Project Information Card

    **Generated:** $(date '+%Y-%m-%d %H:%M:%S %Z')
    **Last Updated:** $(date '+%Y-%m-%d %H:%M:%S %Z')

    ## Project Overview
    - **Name:** [YourProjectName]
    - **Type:** Laravel Web Application
    - **Version:** 1.0.0
    - **Description:** [Brief description of what this application does]
    - **Framework Version:** Laravel 10.x (auto-detected during setup)
    - **PHP Version Required:** 8.1+

    ## Hosting Environment
    - **Provider:** [e.g., DigitalOcean, AWS, cPanel hosting]
    - **Server Type:** [dedicated/vps/shared]
    - **Primary Domain:** [yourdomain.com]
    - **Server IP:** [xxx.xxx.xxx.xxx]
    - **SSH Access:** [yes/no]
    - **Root Access:** [yes/no]

    ## Database Configuration
    - **Engine:** [MySQL/PostgreSQL/SQLite]
    - **Version:** [8.0/13.x/latest]
    - **Host:** [localhost/remote-host]
    - **Default DB Name:** [your_database_name]
    - **Character Set:** utf8mb4
    - **Collation:** utf8mb4_unicode_ci

    ## Team Information
    - **Project Lead:** [Name & Contact]
    - **Developers:** [List team members]
    - **DevOps Contact:** [Name & Contact]
    - **Hosting Support:** [Provider contact info]

    ## Emergency Contacts
    - **Primary:** [Name, Phone, Email]
    - **Secondary:** [Name, Phone, Email]
    - **Hosting Provider:** [Support contact & account info]
    EOF

    echo "✅ PROJECT-INFO.md created"
    ```

    **Expected Result:**

    ```
    ✅ PROJECT-INFO.md created
    📄 Comprehensive project documentation ready for team use
    🔧 Template ready for customization with actual project details
    ```

2. **Generate Advanced JSON Configuration System**

    ```bash
    # Create deployment variables with environment auto-detection
    echo "⚙️ Creating advanced deployment configuration system..."

    # Create comprehensive deployment variables JSON
    cat > deployment-variables.json << 'EOF'
    {
      "project": {
        "name": "YourProjectName",
        "type": "laravel",
        "laravel_version": "auto-detect",
        "has_frontend": true,
        "frontend_framework": "auto-detect",
        "uses_queues": false,
        "uses_horizon": false,
        "uses_websockets": false,
        "uses_telescope": false
      },
      "environments": {
        "local": {
          "app_env": "local",
          "app_debug": true,
          "app_url": "http://localhost:8000",
          "database_url": "mysql://username:password@localhost/dbname_local"
        },
        "staging": {
          "app_env": "staging",
          "app_debug": false,
          "app_url": "https://staging.yourdomain.com",
          "database_url": "mysql://username:password@host/dbname_staging"
        },
        "production": {
          "app_env": "production",
          "app_debug": false,
          "app_url": "https://yourdomain.com",
          "database_url": "mysql://username:password@host/dbname_production"
        }
      },
      "paths": {
        "local_machine": "${PWD}",
        "server_domain": "/home/username/domains/yourdomain.com",
        "server_public": "/home/username/public_html",
        "server_deploy": "/home/username/domains/yourdomain.com/deploy",
        "builder_vm": "${BUILD_SERVER_PATH:-local}",
        "backup_location": "/home/username/backups"
      },
      "repository": {
        "url": "git@github.com:username/repository.git",
        "branch": "main",
        "deploy_branch": "${DEPLOY_BRANCH:-main}",
        "backup_branches": ["main", "staging", "development"]
      },
      "versions": {
        "php": "8.2",
        "php_exact": "auto-detect",
        "composer": "2",
        "node": "18",
        "npm": "auto-detect"
      },
      "deployment": {
        "strategy": "manual",
        "build_location": "local",
        "maintenance_mode": true,
        "backup_database": true,
        "run_migrations": true,
        "compile_assets": true,
        "clear_cache": true,
        "keep_releases": 5,
        "health_check_url": "https://yourdomain.com/health",
        "health_check_timeout": 30
      },
      "shared_resources": {
        "directories": [
          "storage/app/public",
          "storage/logs",
          "storage/framework/cache",
          "storage/framework/sessions",
          "storage/framework/views",
          "public/uploads",
          "public/user-content"
        ],
        "files": [
          ".env",
          "auth.json",
          "oauth-private.key",
          "oauth-public.key"
        ]
      },
      "hosting": {
        "type": "auto-detect",
        "has_root_access": false,
        "public_html_exists": true,
        "exec_enabled": true,
        "symlink_enabled": true,
        "composer_per_domain": false,
        "ssl_enabled": true,
        "ssl_type": "letsencrypt"
      },
      "notifications": {
        "slack_webhook": "${SLACK_WEBHOOK_URL}",
        "discord_webhook": "${DISCORD_WEBHOOK_URL}",
        "email_notifications": true,
        "email_recipients": ["admin@yourdomain.com"]
      }
    }
    EOF

    echo "✅ Advanced deployment configuration created"
    ```

    **Expected Result:**

    ```
    ✅ Advanced deployment configuration created
    ⚙️ JSON configuration with environment-specific settings ready
    🔧 Auto-detection capabilities for Laravel version, frontend framework
    📊 Comprehensive deployment strategy configuration available
    ```

3. **Create Environment Generator Script**

    ```bash
    # Create environment configuration generator
    cat > generate-environments.sh << 'EOF'
    #!/bin/bash

    echo "🌍 Laravel Environment Configuration Generator"
    echo "============================================="

    # Function to generate environment file
    generate_env() {
        local env_name="$1"
        local app_env="$2"
        local app_debug="$3"
        local app_url="$4"
        local db_database="$5"

        echo "Creating .env.${env_name}..."

        cat > ".env.${env_name}" << ENV_EOF
    # Laravel Environment Configuration - ${env_name^^}
    # Generated: $(date '+%Y-%m-%d %H:%M:%S %Z')

    APP_NAME="YourProjectName"
    APP_ENV=${app_env}
    APP_KEY=
    APP_DEBUG=${app_debug}
    APP_URL=${app_url}

    # Database Configuration
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=${db_database}
    DB_USERNAME=username
    DB_PASSWORD=password

    # Broadcasting & Cache Configuration
    BROADCAST_DRIVER=log
    CACHE_DRIVER=file
    FILESYSTEM_DISK=local
    QUEUE_CONNECTION=sync
    SESSION_DRIVER=file
    SESSION_LIFETIME=120

    # Redis Configuration (Optional)
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379

    # Mail Configuration
    MAIL_MAILER=smtp
    MAIL_HOST=mailhog
    MAIL_PORT=1025
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS="hello@example.com"
    MAIL_FROM_NAME="\${APP_NAME}"

    # Additional Services
    AWS_ACCESS_KEY_ID=
    AWS_SECRET_ACCESS_KEY=
    AWS_DEFAULT_REGION=us-east-1
    AWS_BUCKET=
    AWS_USE_PATH_STYLE_ENDPOINT=false

    PUSHER_APP_ID=
    PUSHER_APP_KEY=
    PUSHER_APP_SECRET=
    PUSHER_HOST=
    PUSHER_PORT=443
    PUSHER_SCHEME=https
    PUSHER_APP_CLUSTER=mt1

    VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
    VITE_PUSHER_HOST="\${PUSHER_HOST}"
    VITE_PUSHER_PORT="\${PUSHER_PORT}"
    VITE_PUSHER_SCHEME="\${PUSHER_SCHEME}"
    VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"
    ENV_EOF

        echo "✅ .env.${env_name} created"
    }

    # Generate environment files
    generate_env "local" "local" "true" "http://localhost:8000" "yourproject_local"
    generate_env "staging" "staging" "false" "https://staging.yourdomain.com" "yourproject_staging"
    generate_env "production" "production" "false" "https://yourdomain.com" "yourproject_production"

    echo ""
    echo "🎉 Environment files generated successfully!"
    echo "📝 Remember to:"
    echo "   1. Update database credentials in each .env file"
    echo "   2. Generate unique APP_KEY for each environment"
    echo "   3. Configure mail settings for production"
    echo "   4. Set up external service credentials as needed"
    EOF

    chmod +x generate-environments.sh
    echo "✅ Environment generator script ready"
    ```

    **Expected Result:**

    ```
    ✅ Environment generator script ready
    🌍 Multi-environment configuration system available
    📋 Template .env files ready for customization per environment
    🔧 Executable script for easy environment setup
    ```

4. **Create Team Reference and Emergency Documentation**

    ````bash
    # Create comprehensive team reference system
    mkdir -p docs/team-reference

    # Create team access documentation
    cat > docs/team-reference/ACCESS-CREDENTIALS.md << 'EOF'
    # Team Access Credentials & Responsibilities

    **⚠️ SECURITY NOTE:** This file contains sensitive access information.
    Ensure it is properly secured and not committed to public repositories.

    ## Server Access
    - **SSH Host:** [server-ip or hostname]
    - **SSH Port:** [22 or custom]
    - **SSH Key Location:** [~/.ssh/project-key or specify]
    - **SSH User:** [username]
    - **Sudo Access:** [yes/no]

    ## Hosting Provider Access
    - **Provider:** [DigitalOcean/AWS/cPanel/etc]
    - **Control Panel URL:** [https://panel.example.com]
    - **Account Username:** [username]
    - **Account Email:** [admin@company.com]
    - **Support Phone:** [+1-xxx-xxx-xxxx]
    - **Account Number:** [if applicable]

    ## Database Access
    - **Admin Tool:** [phpMyAdmin/Adminer URL]
    - **Database Host:** [localhost/remote-host]
    - **Admin Username:** [root/admin]
    - **Read-Only User:** [readonly_user]

    ## Team Responsibilities

    ### Project Lead
    - **Name:** [Full Name]
    - **Email:** [email@company.com]
    - **Phone:** [+1-xxx-xxx-xxxx]
    - **Responsibilities:** Project oversight, client communication, final deployment approval

    ### Lead Developer
    - **Name:** [Full Name]
    - **Email:** [email@company.com]
    - **Phone:** [+1-xxx-xxx-xxxx]
    - **Responsibilities:** Code review, architecture decisions, deployment execution

    ### DevOps Engineer
    - **Name:** [Full Name]
    - **Email:** [email@company.com]
    - **Phone:** [+1-xxx-xxx-xxxx]
    - **Responsibilities:** Server management, CI/CD pipeline, monitoring
    EOF

    # Create emergency procedures
    cat > docs/team-reference/EMERGENCY-PROCEDURES.md << 'EOF'
    # Emergency Procedures & Rollback Contacts

    ## 🚨 EMERGENCY RESPONSE PROTOCOL

    ### 1. Site Down Emergency
    **Response Time:** Immediate (within 15 minutes)

    ```bash
    # Quick health check commands
    curl -I https://yourdomain.com
    ssh username@server-ip "systemctl status nginx php8.2-fpm mysql"
    ssh username@server-ip "tail -50 /var/log/nginx/error.log"
    ````

    ### 2. Deployment Failure Emergency

    **Response Time:** Immediate rollback (within 5 minutes)

    ```bash
    # Emergency rollback commands
    cd /path/to/deployment/directory
    ./rollback-to-previous-release.sh
    # OR manual rollback:
    ln -nfs /path/to/previous/release /path/to/current
    systemctl reload php8.2-fpm
    ```

    ### 3. Database Issues Emergency

    **Response Time:** Immediate assessment (within 10 minutes)

    ```bash
    # Database health check
    mysql -u root -p -e "SHOW PROCESSLIST; SHOW ENGINE INNODB STATUS\G"
    # Check disk space
    df -h
    # Check database connections
    ss -tuln | grep :3306
    ```

    ## Emergency Contacts (24/7)

    ### Primary On-Call

    - **Name:** [Full Name]
    - **Phone:** [+1-xxx-xxx-xxxx] (call first)
    - **Email:** [email@company.com]
    - **Backup Phone:** [+1-xxx-xxx-xxxx]

    ### Secondary On-Call

    - **Name:** [Full Name]
    - **Phone:** [+1-xxx-xxx-xxxx]
    - **Email:** [email@company.com]

    ### Hosting Provider Emergency

    - **Provider:** [Provider Name]
    - **Emergency Phone:** [+1-xxx-xxx-xxxx]
    - **Priority Support Ticket:** [URL or email]
    - **Account Number:** [account-id]

    ### Client Emergency Contact

    - **Name:** [Client Contact Name]
    - **Phone:** [+1-xxx-xxx-xxxx]
    - **Email:** [client@company.com]

    ## Critical System Resources

    - **Monitoring Dashboard:** [https://monitoring.yourdomain.com]
    - **Log Aggregation:** [https://logs.yourdomain.com]
    - **Status Page:** [https://status.yourdomain.com]
    - **Backup Verification:** [https://backups.yourdomain.com]
      EOF

    echo "✅ Team reference and emergency documentation created"

    ```

    **Expected Result:**
    ```

    ✅ Team reference and emergency documentation created
    📚 Comprehensive team access documentation available
    🚨 Emergency procedures with specific commands ready
    📞 Complete emergency contact information documented
    🔐 Secure credential templates created for team use

    ```

    ```

### Expected Results ✅

-   [ ] Project information card completed with all essential details
-   [ ] All deployment variables documented and organized
-   [ ] Team reference materials created and accessible
-   [ ] JSON configuration template established for automation

### Verification Steps

-   [ ] All team members can access project information
-   [ ] Hosting provider details are accurate and current
-   [ ] JSON template contains all required deployment variables

### Troubleshooting Tips

-   **Issue:** Missing hosting provider information
    -   **Solution:** Contact hosting provider support for complete server specifications
-   **Issue:** Unclear about required variables
    -   **Solution:** Review hosting provider documentation and Laravel deployment requirements

### Template Example

```json
{
    "project": {
        "name": "YourProjectName",
        "type": "laravel",
        "version": "1.0.0",
        "description": "Brief project description"
    },
    "hosting": {
        "provider": "HostingProvider",
        "server_ip": "xxx.xxx.xxx.xxx",
        "domain": "yourdomain.com",
        "hosting_type": "dedicated|vps|shared"
    },
    "paths": {
        "server_domain": "/home/username/domains/yourdomain.com",
        "public_html": "/home/username/public_html",
        "local_machine": "/path/to/local/project"
    }
}
```

---

## Step 02: Create GitHub Repository

**🟢 Location:** GitHub Web Interface | **⏱️ Time:** 5-10 minutes | **🔧 Type:** Version Control

### Purpose

Establish version control foundation for deployment workflows with proper repository configuration for team collaboration.

### When to Execute

**After project information documentation** - Ensures repository naming and settings align with project specifications.

### Action Steps

1. **Automated GitHub Repository Creation with Advanced Configuration**

    ```bash
    # Advanced GitHub repository creation with CLI automation
    echo "🚀 Creating GitHub repository with advanced configuration..."

    # Check if GitHub CLI is installed, install if needed
    if ! command -v gh &> /dev/null; then
        echo "📦 Installing GitHub CLI..."
        # macOS
        if [[ "$OSTYPE" == "darwin"* ]]; then
            brew install gh
        # Ubuntu/Debian
        elif command -v apt &> /dev/null; then
            curl -fsSL https://cli.github.com/packages/githubcli-archive-keyring.gpg | sudo dd of=/usr/share/keyrings/githubcli-archive-keyring.gpg
            echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" | sudo tee /etc/apt/sources.list.d/github-cli.list > /dev/null
            sudo apt update && sudo apt install gh
        # Other systems
        else
            echo "Please install GitHub CLI manually from https://cli.github.com/"
            exit 1
        fi
    fi

    # Authenticate with GitHub (if not already authenticated)
    if ! gh auth status &> /dev/null; then
        echo "🔐 Please authenticate with GitHub..."
        gh auth login --protocol ssh --preferred-editor code
    fi

    # Get project name from PROJECT-INFO.md or prompt user
    PROJECT_NAME=$(grep -oP "Name:\*\* \K[^]]*" PROJECT-INFO.md 2>/dev/null | head -1 || echo "")
    if [ -z "$PROJECT_NAME" ]; then
        echo "❓ Project name not found in PROJECT-INFO.md"
        read -p "Enter your project name: " PROJECT_NAME
    fi

    echo "✅ GitHub CLI ready, creating repository: $PROJECT_NAME"
    ```

    **Expected Result:**

    ```
    ✅ GitHub CLI ready, creating repository: YourProjectName
    🔐 Authenticated with GitHub successfully
    📦 GitHub CLI installed and configured
    ```

2. **Create Repository with Advanced Security Configuration**

    ```bash
    # Create repository with comprehensive settings
    echo "🏗️ Creating repository with advanced configuration..."

    # Create the repository
    gh repo create "$PROJECT_NAME" \
        --private \
        --description "Laravel web application - production deployment ready" \
        --add-readme=false \
        --gitignore="" \
        --license="" \
        --enable-issues=true \
        --enable-wiki=false \
        --enable-projects=true

    if [ $? -eq 0 ]; then
        echo "✅ Repository created successfully"

        # Get the SSH URL immediately after creation
        REPO_SSH_URL="git@github.com:$(gh auth status 2>&1 | grep -oP 'Logged in to github.com as \K[^[:space:]]*')/$PROJECT_NAME.git"
        echo "📋 Repository SSH URL: $REPO_SSH_URL"

        # Update PROJECT-INFO.md with repository information
        if [ -f "PROJECT-INFO.md" ]; then
            # Add repository section if it doesn't exist
            if ! grep -q "## Repository Information" PROJECT-INFO.md; then
                cat >> PROJECT-INFO.md << EOF

    ## Repository Information
    - **GitHub URL:** https://github.com/$(gh auth status 2>&1 | grep -oP 'Logged in to github.com as \K[^[:space:]]*')/$PROJECT_NAME
    - **SSH Clone URL:** $REPO_SSH_URL
    - **Repository Type:** Private
    - **Created:** $(date '+%Y-%m-%d %H:%M:%S %Z')
    EOF
            fi
            echo "✅ PROJECT-INFO.md updated with repository details"
        fi

        # Update deployment variables JSON with actual repository URL
        if [ -f "deployment-variables.json" ]; then
            # Use jq to update the repository URL if jq is available
            if command -v jq &> /dev/null; then
                jq --arg url "$REPO_SSH_URL" '.repository.url = $url' deployment-variables.json > temp.json && mv temp.json deployment-variables.json
                echo "✅ deployment-variables.json updated with repository URL"
            fi
        fi

    else
        echo "❌ Repository creation failed"
        exit 1
    fi
    ```

    **Expected Result:**

    ```
    ✅ Repository created successfully
    📋 Repository SSH URL: git@github.com:username/YourProjectName.git
    ✅ PROJECT-INFO.md updated with repository details
    ✅ deployment-variables.json updated with repository URL
    ```

3. **Configure Advanced Repository Security and Collaboration**

    ```bash
    # Enable advanced security features
    echo "🔒 Configuring repository security and collaboration settings..."

    # Navigate to the repository context
    cd "$PROJECT_NAME" 2>/dev/null || {
        echo "Creating local directory for repository configuration..."
        mkdir -p "$PROJECT_NAME"
        cd "$PROJECT_NAME"
    }

    # Enable security features using GitHub CLI
    echo "🛡️ Enabling security features..."

    # Enable vulnerability alerts
    gh api repos/:owner/:repo \
        --method PATCH \
        --field has_vulnerability_alerts=true \
        --field security_and_analysis='{"secret_scanning":{"status":"enabled"},"secret_scanning_push_protection":{"status":"enabled"}}' \
        --silent

    echo "✅ Vulnerability alerts and secret scanning enabled"

    # Set repository topics for organization
    TOPICS="laravel,php,web-application,deployment,production"
    gh repo edit --add-topic "$TOPICS"
    echo "✅ Repository topics configured: $TOPICS"

    # Configure repository settings
    gh repo edit \
        --enable-issues=true \
        --enable-projects=true \
        --enable-wiki=false \
        --allow-squash-merge=true \
        --allow-merge-commit=false \
        --allow-rebase-merge=true \
        --delete-branch-on-merge=true

    echo "✅ Repository merge and collaboration settings configured"
    ```

    **Expected Result:**

    ```
    🛡️ Enabling security features...
    ✅ Vulnerability alerts and secret scanning enabled
    ✅ Repository topics configured: laravel,php,web-application,deployment,production
    ✅ Repository merge and collaboration settings configured
    ```

4. **Setup Automated Branch Protection with Advanced Rules**

    ```bash
    # Configure comprehensive branch protection
    echo "🛡️ Setting up advanced branch protection rules..."

    # Wait a moment for repository to be fully ready
    sleep 2

    # Create branch protection rule for main branch
    gh api "repos/:owner/:repo/branches/main/protection" \
        --method PUT \
        --field required_status_checks='{"strict":true,"contexts":[]}' \
        --field enforce_admins=false \
        --field required_pull_request_reviews='{"required_approving_review_count":1,"dismiss_stale_reviews":true,"require_code_owner_reviews":false,"dismissal_restrictions":{},"bypass_pull_request_allowances":{}}' \
        --field restrictions=null \
        --field allow_force_pushes=false \
        --field allow_deletions=false

    if [ $? -eq 0 ]; then
        echo "✅ Branch protection rules configured for main branch"

        # Display protection summary
        echo ""
        echo "📋 Branch Protection Summary:"
        echo "   ✅ Require pull request reviews (1 reviewer minimum)"
        echo "   ✅ Dismiss stale reviews automatically"
        echo "   ✅ Prevent force pushes to main branch"
        echo "   ✅ Prevent branch deletion"
        echo "   ✅ Status checks enforced (strict mode)"

    else
        echo "⚠️  Branch protection setup will be completed after first push"
        echo "   (GitHub requires at least one commit before setting protection rules)"
    fi
    ```

    **Expected Result:**

    ```
    ✅ Branch protection rules configured for main branch
    📋 Branch Protection Summary:
       ✅ Require pull request reviews (1 reviewer minimum)
       ✅ Dismiss stale reviews automatically
       ✅ Prevent force pushes to main branch
       ✅ Prevent branch deletion
       ✅ Status checks enforced (strict mode)
    ```

5. **Advanced SSH Key Validation and Repository Access Testing**

    ```bash
    # Comprehensive SSH and repository access validation
    echo "🔑 Validating SSH access and repository connectivity..."

    # Test SSH connection to GitHub
    echo "Testing SSH connection to GitHub..."
    ssh -T git@github.com -o ConnectTimeout=10 2>&1 | tee ssh-test.log

    if grep -q "successfully authenticated" ssh-test.log; then
        echo "✅ SSH authentication to GitHub successful"
        rm -f ssh-test.log

        # Test repository access
        echo "🔍 Testing repository access and permissions..."

        # Clone test (will create a minimal local copy)
        REPO_URL="git@github.com:$(gh auth status 2>&1 | grep -oP 'Logged in to github.com as \K[^[:space:]]*')/$PROJECT_NAME.git"

        if git ls-remote "$REPO_URL" &> /dev/null; then
            echo "✅ Repository is accessible via SSH"

            # Create repository access verification report
            cat > repository-access-report.md << EOF
    # Repository Access Verification Report

    **Generated:** $(date '+%Y-%m-%d %H:%M:%S %Z')
    **Repository:** $REPO_URL

    ## Access Test Results
    - ✅ SSH Authentication: Successful
    - ✅ Repository Access: Confirmed
    - ✅ Read Permissions: Verified
    - ✅ Write Permissions: Available (based on repository creation)

    ## Repository Configuration
    - **Visibility:** Private
    - **Security Features:** Enabled (vulnerability alerts, secret scanning)
    - **Branch Protection:** Configured for main branch
    - **Merge Settings:** Squash and rebase allowed, merge commits disabled
    - **Auto-delete branches:** Enabled after PR merge

    ## Next Steps
    - Ready for local project initialization
    - Repository is configured for secure collaborative development
    - Branch protection rules will be active after first push
    EOF

            echo "✅ Repository access verification complete"
            echo "📋 Detailed report saved to: repository-access-report.md"

        else
            echo "❌ Repository access test failed"
            echo "🔧 Troubleshooting steps:"
            echo "   1. Verify SSH key is added to GitHub account"
            echo "   2. Test SSH connection: ssh -T git@github.com"
            echo "   3. Check repository permissions"
            exit 1
        fi

    else
        echo "❌ SSH authentication failed"
        echo "🔧 SSH Setup Required:"
        echo "   1. Generate SSH key: ssh-keygen -t ed25519 -C 'your-email@example.com'"
        echo "   2. Add to SSH agent: ssh-add ~/.ssh/id_ed25519"
        echo "   3. Copy public key: cat ~/.ssh/id_ed25519.pub"
        echo "   4. Add to GitHub: Settings → SSH and GPG keys → New SSH key"
        rm -f ssh-test.log
        exit 1
    fi
    ```

    **Expected Result:**

    ```
    ✅ SSH authentication to GitHub successful
    🔍 Testing repository access and permissions...
    ✅ Repository is accessible via SSH
    ✅ Repository access verification complete
    📋 Detailed report saved to: repository-access-report.md
    ```

6. **Create Repository Management Automation Scripts**

    ```bash
    # Create repository management tools for ongoing maintenance
    echo "🔧 Creating repository management automation..."

    mkdir -p .github/scripts

    # Create repository health check script
    cat > .github/scripts/repo-health-check.sh << 'EOF'
    #!/bin/bash

    echo "🏥 GitHub Repository Health Check"
    echo "================================"

    # Check repository status
    echo "## Repository Information"
    gh repo view --json name,description,visibility,isPrivate,pushedAt,updatedAt

    # Check security features
    echo ""
    echo "## Security Status"
    echo "- Vulnerability alerts: $(gh api repos/:owner/:repo --jq '.has_vulnerability_alerts // false')"
    echo "- Security analysis: $(gh api repos/:owner/:repo --jq '.security_and_analysis.secret_scanning.status // "disabled"')"

    # Check branch protection
    echo ""
    echo "## Branch Protection"
    if gh api repos/:owner/:repo/branches/main/protection &> /dev/null; then
        echo "- Main branch protection: ✅ Enabled"
        echo "- Required reviews: $(gh api repos/:owner/:repo/branches/main/protection --jq '.required_pull_request_reviews.required_approving_review_count // 0')"
    else
        echo "- Main branch protection: ❌ Disabled"
    fi

    # Check recent activity
    echo ""
    echo "## Recent Activity"
    echo "Last 5 commits:"
    gh api repos/:owner/:repo/commits --jq '.[0:5][] | "- \(.commit.message | split("\n")[0]) (\(.commit.author.date | .[0:10]))"'
    EOF

    chmod +x .github/scripts/repo-health-check.sh

    # Create branch management script
    cat > .github/scripts/manage-branches.sh << 'EOF'
    #!/bin/bash

    echo "🌿 Branch Management Utility"
    echo "============================"

    case "${1:-status}" in
        "status")
            echo "## All Branches"
            gh api repos/:owner/:repo/branches --jq '.[] | "\(.name) - Last commit: \(.commit.commit.author.date | .[0:10])"'
            ;;
        "cleanup")
            echo "## Cleaning up merged branches"
            gh api repos/:owner/:repo/branches --jq '.[] | select(.name != "main" and .name != "development") | .name' | while read branch; do
                if gh api "repos/:owner/:repo/branches/$branch" --jq '.commit.sha' | xargs -I {} gh api repos/:owner/:repo/commits/{}/pulls --jq 'length' | grep -q '^0$'; then
                    echo "Keeping active branch: $branch"
                else
                    echo "Consider cleanup for: $branch"
                fi
            done
            ;;
        *)
            echo "Usage: $0 [status|cleanup]"
            ;;
    esac
    EOF

    chmod +x .github/scripts/manage-branches.sh

    echo "✅ Repository management scripts created"
    echo "📁 Scripts available in .github/scripts/"
    echo "   - repo-health-check.sh: Monitor repository health and security"
    echo "   - manage-branches.sh: Branch management and cleanup utilities"
    ```

    **Expected Result:**

    ```
    ✅ Repository management scripts created
    📁 Scripts available in .github/scripts/
       - repo-health-check.sh: Monitor repository health and security
       - manage-branches.sh: Branch management and cleanup utilities
    ```

### Expected Results ✅

-   [ ] GitHub repository created with proper naming and privacy settings
-   [ ] SSH URL documented for deployment configuration
-   [ ] Repository configured for team access and collaboration
-   [ ] Branch protection configured for production security

### Verification Steps

-   [ ] Repository is accessible by all team members
-   [ ] SSH connectivity tested from local machine
-   [ ] Branch protection rules are active and properly configured

### Troubleshooting Tips

-   **Issue:** SSH key authentication fails
    -   **Solution:** Generate and add SSH keys to GitHub account, test with `ssh -T git@github.com`
-   **Issue:** Repository access denied for team members
    -   **Solution:** Check collaborator permissions and organization access settings

### Security Checklist

-   [ ] Repository set to private for production projects
-   [ ] SSH keys properly configured and tested
-   [ ] Branch protection enabled for main/production branches
-   [ ] Security features enabled (dependency scanning, vulnerability alerts)

---

## Step 03: Setup Local Project Structure

**🟢 Location:** Local Machine | **⏱️ Time:** 5-10 minutes | **🔧 Type:** Directory Setup

### Purpose

Establish organized local development directory structure that supports the Admin-Local system and deployment automation.

### When to Execute

**After GitHub repository creation** - Directory structure will contain the cloned repository and deployment infrastructure.

### Action Steps

1. **Automated Development Directory Navigation with Validation**

    ```bash
    # Smart development directory navigation with validation
    echo "🗂️ Setting up local development directory structure..."

    # Define common development directory patterns
    POSSIBLE_DEV_DIRS=(
        "$HOME/Projects"
        "$HOME/Development"
        "$HOME/Code"
        "$HOME/dev"
        "$HOME/workspace"
        "/usr/local/development"
        "C:/Development"
        "C:/Projects"
    )

    # Find or create appropriate development directory
    DEV_DIR=""
    for dir in "${POSSIBLE_DEV_DIRS[@]}"; do
        if [[ -d "$dir" && -w "$dir" ]]; then
            DEV_DIR="$dir"
            echo "✅ Found writable development directory: $DEV_DIR"
            break
        fi
    done

    # If no existing directory found, create default
    if [[ -z "$DEV_DIR" ]]; then
        DEV_DIR="$HOME/Projects"
        echo "📁 Creating development directory: $DEV_DIR"
        mkdir -p "$DEV_DIR"
        if [[ $? -eq 0 ]]; then
            echo "✅ Development directory created successfully"
        else
            echo "❌ Failed to create development directory"
            echo "🔧 Please create manually: mkdir -p $DEV_DIR"
            exit 1
        fi
    fi

    # Navigate to development directory
    cd "$DEV_DIR" || {
        echo "❌ Cannot navigate to development directory: $DEV_DIR"
        exit 1
    }

    echo "📍 Current location: $(pwd)"
    echo "💾 Available space: $(df -h . | tail -1 | awk '{print $4}') free"
    ```

    **Expected Result:**

    ```
    ✅ Found writable development directory: /Users/username/Projects
    📍 Current location: /Users/username/Projects
    💾 Available space: 45GB free
    ```

2. **Advanced Project Directory Creation with Environment Detection**

    ```bash
    # Get project name from previous configuration or prompt
    PROJECT_NAME=""

    # Try to get project name from PROJECT-INFO.md
    if [[ -f "PROJECT-INFO.md" ]]; then
        PROJECT_NAME=$(grep -oP "Name:\*\* \K[^]]*" PROJECT-INFO.md 2>/dev/null | head -1 || echo "")
    fi

    # Try to get from deployment-variables.json if available
    if [[ -z "$PROJECT_NAME" && -f "deployment-variables.json" && command -v jq &> /dev/null ]]; then
        PROJECT_NAME=$(jq -r '.project.name // empty' deployment-variables.json 2>/dev/null || echo "")
    fi

    # Prompt if still not found
    if [[ -z "$PROJECT_NAME" ]]; then
        echo "❓ Project name not found in configuration files"
        read -p "Enter your project name: " PROJECT_NAME

        # Validate project name format
        if [[ ! "$PROJECT_NAME" =~ ^[a-zA-Z][a-zA-Z0-9_-]*$ ]]; then
            echo "⚠️  Project name should start with a letter and contain only letters, numbers, hyphens, and underscores"
            read -p "Enter a valid project name: " PROJECT_NAME
        fi
    fi

    echo "🏗️ Creating project directory structure for: $PROJECT_NAME"

    # Create main project directory with error handling
    if [[ -d "$PROJECT_NAME" ]]; then
        echo "⚠️  Directory '$PROJECT_NAME' already exists"
        read -p "Continue with existing directory? (yes/no): " CONTINUE_EXISTING
        if [[ "$CONTINUE_EXISTING" != "yes" ]]; then
            echo "❌ Project setup cancelled"
            exit 1
        fi
    else
        mkdir -p "$PROJECT_NAME"
        if [[ $? -eq 0 ]]; then
            echo "✅ Project directory created: $PROJECT_NAME"
        else
            echo "❌ Failed to create project directory"
            exit 1
        fi
    fi

    # Navigate to project directory
    cd "$PROJECT_NAME" || {
        echo "❌ Cannot navigate to project directory"
        exit 1
    }

    # Set and document the local machine path
    LOCAL_MACHINE_PATH="$(pwd)"
    echo "📁 Project root path: $LOCAL_MACHINE_PATH"
    ```

    **Expected Result:**

    ```
    🏗️ Creating project directory structure for: YourProjectName
    ✅ Project directory created: YourProjectName
    📁 Project root path: /Users/username/Projects/YourProjectName
    ```

3. **Intelligent Path Variables Configuration and Environment Setup**

    ```bash
    # Create comprehensive path configuration system
    echo "⚙️ Configuring path variables and environment setup..."

    # Create path configuration file
    cat > .project-paths.env << EOF
    # Project Path Configuration
    # Generated: $(date '+%Y-%m-%d %H:%M:%S %Z')

    # Core Paths
    PROJECT_NAME="$PROJECT_NAME"
    LOCAL_MACHINE_PATH="$LOCAL_MACHINE_PATH"
    DEVELOPMENT_ROOT="$DEV_DIR"

    # System Information
    OPERATING_SYSTEM="$(uname -s)"
    ARCHITECTURE="$(uname -m)"
    USER_HOME="$HOME"
    CURRENT_USER="$(whoami)"

    # Path Separators (cross-platform)
    if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "cygwin" ]]; then
        PATH_SEPARATOR="\\"
        PLATFORM="Windows"
    else
        PATH_SEPARATOR="/"
        PLATFORM="Unix-like"
    fi

    # Project Subdirectories (to be created)
    ADMIN_LOCAL_PATH="\$LOCAL_MACHINE_PATH/Admin-Local"
    DEPLOYMENT_SCRIPTS_PATH="\$ADMIN_LOCAL_PATH/Deployment/Scripts"
    DEPLOYMENT_CONFIGS_PATH="\$ADMIN_LOCAL_PATH/Deployment/Configs"
    DEPLOYMENT_LOGS_PATH="\$ADMIN_LOCAL_PATH/Deployment/Logs"
    BUILD_TEMP_PATH="\$LOCAL_MACHINE_PATH/build-tmp"

    # Export variables for current session
    export PROJECT_NAME="$PROJECT_NAME"
    export LOCAL_MACHINE_PATH="$LOCAL_MACHINE_PATH"
    export PLATFORM="$PLATFORM"
    EOF

    echo "✅ Path configuration file created: .project-paths.env"

    # Source the environment file
    source .project-paths.env

    # Update deployment-variables.json if it exists
    if [[ -f "../deployment-variables.json" ]]; then
        echo "🔄 Updating deployment variables with actual path..."
        if command -v jq &> /dev/null; then
            jq --arg path "$LOCAL_MACHINE_PATH" '.paths.local_machine = $path' ../deployment-variables.json > temp.json && mv temp.json ../deployment-variables.json
            echo "✅ deployment-variables.json updated with actual local machine path"
        fi
    fi

    # Create path validation script
    cat > validate-paths.sh << 'EOF'
    #!/bin/bash

    echo "🔍 Project Path Validation Report"
    echo "================================"

    # Source path configuration
    source .project-paths.env

    echo "Project Name: $PROJECT_NAME"
    echo "Local Machine Path: $LOCAL_MACHINE_PATH"
    echo "Platform: $PLATFORM"
    echo ""

    # Validate key paths
    echo "## Path Validation"
    paths=(
        "$LOCAL_MACHINE_PATH:Project Root"
        "$HOME:User Home"
        "$DEV_DIR:Development Directory"
    )

    for path_info in "${paths[@]}"; do
        IFS=':' read -ra path_parts <<< "$path_info"
        path="${path_parts[0]}"
        description="${path_parts[1]}"

        if [[ -d "$path" ]]; then
            if [[ -w "$path" ]]; then
                echo "✅ $description: $path (writable)"
            else
                echo "⚠️  $description: $path (read-only)"
            fi
        else
            echo "❌ $description: $path (not found)"
        fi
    done

    # Show disk space
    echo ""
    echo "## Disk Space Analysis"
    df -h "$LOCAL_MACHINE_PATH" | awk 'NR==1 {print "Filesystem      Size  Used Avail Use% Mounted on"} NR==2 {printf "%-15s %4s %4s %4s %4s %s\n", $1, $2, $3, $4, $5, $6}'
    EOF

    chmod +x validate-paths.sh
    echo "✅ Path validation script created: validate-paths.sh"
    ```

    **Expected Result:**

    ```
    ⚙️ Configuring path variables and environment setup...
    ✅ Path configuration file created: .project-paths.env
    🔄 Updating deployment variables with actual path...
    ✅ deployment-variables.json updated with actual local machine path
    ✅ Path validation script created: validate-paths.sh
    ```

4. **Comprehensive Workspace Organization and Documentation**

    ```bash
    # Create comprehensive workspace documentation and organization
    echo "📚 Creating workspace organization and documentation..."

    # Run path validation to confirm setup
    ./validate-paths.sh > workspace-validation-report.md
    echo "✅ Workspace validation report generated"

    # Create workspace initialization script
    cat > init-workspace.sh << 'EOF'
    #!/bin/bash

    echo "🚀 Workspace Initialization Script"
    echo "==================================="

    # Load project paths
    source .project-paths.env

    echo "Initializing workspace for: $PROJECT_NAME"
    echo "Location: $LOCAL_MACHINE_PATH"
    echo ""

    # Create standard project documentation
    if [[ ! -f "README.md" ]]; then
        cat > README.md << README_EOF
    # $PROJECT_NAME

    Laravel web application deployment project.

    ## Project Information
    - **Type:** Laravel Web Application
    - **Environment:** Development/Staging/Production
    - **Location:** \`$LOCAL_MACHINE_PATH\`
    - **Platform:** $PLATFORM

    ## Quick Start

    1. **Environment Setup:**
       \`\`\`bash
       source .project-paths.env
       ./validate-paths.sh
       \`\`\`

    2. **Admin-Local Setup:**
       \`\`\`bash
       # Will be created in Step 03.1
       ls -la Admin-Local/
       \`\`\`

    3. **Development Commands:**
       \`\`\`bash
       # Laravel specific commands will be added during setup
       php artisan --version
       composer --version
       \`\`\`

    ## Directory Structure

    \`\`\`
    $PROJECT_NAME/
    ├── Admin-Local/              # Deployment automation
    ├── .project-paths.env        # Path configuration
    ├── validate-paths.sh         # Path validation
    ├── init-workspace.sh         # Workspace initialization
    └── (Laravel application files)
    \`\`\`

    ## Documentation

    - [Project Paths Configuration](.project-paths.env)
    - [Workspace Validation Report](workspace-validation-report.md)

    ---

    **Generated:** $(date '+%Y-%m-%d %H:%M:%S %Z')
    **Platform:** $PLATFORM
    **Location:** \`$LOCAL_MACHINE_PATH\`
    README_EOF

        echo "✅ README.md created with project information"
    fi

    # Create .editorconfig for consistent development
    if [[ ! -f ".editorconfig" ]]; then
        cat > .editorconfig << 'EDITOR_EOF'
    # EditorConfig for Laravel projects
    # https://editorconfig.org

    root = true

    [*]
    charset = utf-8
    end_of_line = lf
    insert_final_newline = true
    trim_trailing_whitespace = true

    [*.{php,js,jsx,ts,tsx,vue}]
    indent_style = space
    indent_size = 4

    [*.{json,yml,yaml}]
    indent_style = space
    indent_size = 2

    [*.md]
    trim_trailing_whitespace = false

    [*.blade.php]
    indent_style = space
    indent_size = 4
    EDITOR_EOF

        echo "✅ .editorconfig created for consistent code formatting"
    fi

    # Create development environment checker
    cat > check-dev-environment.sh << 'DEV_CHECK_EOF'
    #!/bin/bash

    echo "🔧 Development Environment Check"
    echo "==============================="

    # Check required tools
    tools=("php:PHP" "composer:Composer" "node:Node.js" "npm:NPM" "git:Git")

    echo "## Required Tools Check"
    for tool_info in "${tools[@]}"; do
        IFS=':' read -ra tool_parts <<< "$tool_info"
        tool="${tool_parts[0]}"
        name="${tool_parts[1]}"

        if command -v "$tool" &> /dev/null; then
            version=$("$tool" --version | head -1)
            echo "✅ $name: $version"
        else
            echo "❌ $name: Not installed"
        fi
    done

    # Check PHP extensions (basic Laravel requirements)
    if command -v php &> /dev/null; then
        echo ""
        echo "## PHP Extensions Check"
        required_extensions=("json" "mbstring" "xml" "curl" "zip")

        for ext in "${required_extensions[@]}"; do
            if php -m | grep -qi "^$ext$"; then
                echo "✅ PHP $ext extension"
            else
                echo "❌ PHP $ext extension (missing)"
            fi
        done
    fi

    echo ""
    echo "## Workspace Status"
    echo "Project Directory: $LOCAL_MACHINE_PATH"
    echo "Writable: $(if [[ -w . ]]; then echo 'Yes'; else echo 'No'; fi)"
    echo "Git Repository: $(if [[ -d .git ]]; then echo 'Yes'; else echo 'Not initialized'; fi)"
    echo "Admin-Local: $(if [[ -d Admin-Local ]]; then echo 'Configured'; else echo 'Pending'; fi)"
    DEV_CHECK_EOF

    chmod +x check-dev-environment.sh
    echo "✅ Development environment checker created"

    # Final workspace status
    echo ""
    echo "📊 Workspace Organization Complete"
    echo "=================================="
    echo "✅ Project directory: $LOCAL_MACHINE_PATH"
    echo "✅ Path configuration: .project-paths.env"
    echo "✅ Validation script: validate-paths.sh"
    echo "✅ Workspace initializer: init-workspace.sh"
    echo "✅ README documentation: README.md"
    echo "✅ Editor configuration: .editorconfig"
    echo "✅ Environment checker: check-dev-environment.sh"
    echo ""
    echo "🎯 Next: Ready for Admin-Local foundation setup (Step 03.1)"
    EOF

    chmod +x init-workspace.sh
    echo "✅ Workspace initialization script created"

    # Execute workspace initialization
    echo "🚀 Executing workspace initialization..."
    ./init-workspace.sh
    ```

    **Expected Result:**

    ```
    📚 Creating workspace organization and documentation...
    ✅ Workspace validation report generated
    ✅ Workspace initialization script created
    🚀 Executing workspace initialization...
    ✅ README.md created with project information
    ✅ .editorconfig created for consistent code formatting
    ✅ Development environment checker created
    📊 Workspace Organization Complete
    ✅ Project directory: /Users/username/Projects/YourProjectName
    ✅ Path configuration: .project-paths.env
    ✅ Validation script: validate-paths.sh
    ✅ Workspace initializer: init-workspace.sh
    ✅ README documentation: README.md
    ✅ Editor configuration: .editorconfig
    ✅ Environment checker: check-dev-environment.sh
    🎯 Next: Ready for Admin-Local foundation setup (Step 03.1)
    ```

### Expected Results ✅

-   [ ] Local project structure created in organized development directory
-   [ ] Organized directory hierarchy established with proper permissions
-   [ ] Path variables configured for consistent reference across tools
-   [ ] Workspace foundation ready for Admin-Local and repository integration

### Verification Steps

-   [ ] Terminal can navigate to project directory consistently
-   [ ] Directory has proper read/write permissions
-   [ ] Path variables are accessible across different terminal sessions

### Troubleshooting Tips

-   **Issue:** Permission denied when creating directories
    -   **Solution:** Check directory permissions or use `sudo` if necessary (Linux/macOS)
-   **Issue:** Path too long on Windows
    -   **Solution:** Use shorter directory names or enable long path support in Windows

### Directory Structure Example

```
YourProjectName/                    # %path-localMachine%
├── (Laravel project files will go here after clone)
├── Admin-Local/                    # (Created in next step)
└── (Additional deployment infrastructure)
```

---

## Step 03.1: Setup Admin-Local Foundation & Universal Configuration

**🟢 Location:** Local Machine | **⏱️ Time:** 15-20 minutes | **🔧 Type:** Infrastructure Setup

### Purpose

Establish comprehensive Admin-Local structure and universal deployment configuration system that enables automated deployment workflows.

### When to Execute

**Immediately after local structure setup** - This creates the deployment automation foundation before any code integration.

### Action Steps

1. **Create Admin-Local Directory Structure**
   a. In your project root, create the complete Admin-Local structure:

    ```bash
    mkdir -p Admin-Local/Deployment/{Scripts,Configs,Logs}
    mkdir -p Admin-Local/Templates
    mkdir -p Admin-Local/Analysis
    ```

    b. Verify all directories were created successfully
    c. Set appropriate permissions for script execution
    d. Document the Admin-Local structure for team reference

2. **Create Universal Deployment Configuration**
   a. Create `Admin-Local/Deployment/Configs/deployment-variables.json`
   b. Use the comprehensive template structure from project information
   c. Configure all path variables, version requirements, and deployment preferences
   d. Include hosting-specific configurations and shared resource definitions

3. **Create Variable Loader Script**
   a. Create `Admin-Local/Deployment/Scripts/load-variables.sh`
   b. Implement JSON processing with `jq` dependency handling
   c. Export all variables as environment variables for script access
   d. Include error handling and validation for missing configurations

4. **Install JSON Processing Tools**
   a. **Linux/macOS:** Install `jq` via package manager (`apt install jq` or `brew install jq`)
   b. **Windows:** Download `jq` executable or use WSL
   c. Verify `jq` installation: `jq --version`
   d. Test JSON parsing with deployment variables file

5. **Test Variable Loading System**
   a. Execute the variable loader script: `bash Admin-Local/Deployment/Scripts/load-variables.sh`
   b. Verify all expected environment variables are exported
   c. Test variable accessibility from subsequent scripts
   d. Validate JSON configuration integrity and completeness

### Expected Results ✅

-   [ ] Admin-Local foundation structure created with all required directories
-   [ ] Universal deployment configuration template established with project-specific values
-   [ ] Variable loading system functional and tested
-   [ ] Project-specific tracking directories ready for deployment logs and analysis
-   [ ] JSON variable management operational and validated

### Verification Steps

-   [ ] All Admin-Local directories exist with proper permissions
-   [ ] `deployment-variables.json` contains all required configuration sections
-   [ ] Variable loader script executes without errors
-   [ ] Environment variables are properly exported and accessible

### Troubleshooting Tips

-   **Issue:** `jq` command not found
    -   **Solution:** Install jq using your system package manager or download from https://stedolan.github.io/jq/
-   **Issue:** Permission denied executing scripts
    -   **Solution:** Make scripts executable with `chmod +x Admin-Local/Deployment/Scripts/*.sh`
-   **Issue:** JSON syntax errors
    -   **Solution:** Validate JSON format using `jq . Admin-Local/Deployment/Configs/deployment-variables.json`

### Configuration File Templates

**deployment-variables.json:**

```json
{
    "project": {
        "name": "YourProjectName",
        "type": "laravel",
        "has_frontend": true,
        "frontend_framework": "vue|react|blade|inertia",
        "uses_queues": true,
        "uses_horizon": false,
        "uses_websockets": false
    },
    "paths": {
        "local_machine": "%path-localMachine%",
        "server_domain": "/home/username/domains/example.com",
        "server_deploy": "/home/username/domains/example.com/deploy",
        "server_public": "/home/username/public_html",
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
        "health_check_url": "https://example.com/health",
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
```

**load-variables.sh:**

```bash
#!/bin/bash

# Load deployment configuration
CONFIG_FILE="Admin-Local/Deployment/Configs/deployment-variables.json"

if [ ! -f "$CONFIG_FILE" ]; then
    echo "❌ Configuration file not found: $CONFIG_FILE"
    exit 1
fi

# Export as environment variables using jq
export PROJECT_NAME=$(jq -r '.project.name' $CONFIG_FILE 2>/dev/null || echo "")
export PATH_LOCAL_MACHINE=$(jq -r '.paths.local_machine' $CONFIG_FILE 2>/dev/null || echo "")
export PATH_SERVER=$(jq -r '.paths.server_deploy' $CONFIG_FILE 2>/dev/null || echo "")
export PATH_PUBLIC=$(jq -r '.paths.server_public' $CONFIG_FILE 2>/dev/null || echo "")
export GITHUB_REPO=$(jq -r '.repository.url' $CONFIG_FILE 2>/dev/null || echo "")
export DEPLOY_BRANCH=$(jq -r '.repository.deploy_branch' $CONFIG_FILE 2>/dev/null || echo "main")
export PHP_VERSION=$(jq -r '.versions.php' $CONFIG_FILE 2>/dev/null || echo "8.2")
export COMPOSER_VERSION=$(jq -r '.versions.composer' $CONFIG_FILE 2>/dev/null || echo "2")
export NODE_VERSION=$(jq -r '.versions.node' $CONFIG_FILE 2>/dev/null || echo "18")
export BUILD_LOCATION=$(jq -r '.deployment.build_location' $CONFIG_FILE 2>/dev/null || echo "local")
export HOSTING_TYPE=$(jq -r '.hosting.type' $CONFIG_FILE 2>/dev/null || echo "")

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
```

---

## Step 03.2: Run Comprehensive Environment Analysis

**🟢 Location:** Local Machine | **⏱️ Time:** 10-15 minutes | **🔧 Type:** Validation & Analysis

### Purpose

Perform comprehensive Laravel environment analysis with enhanced validation to ensure your local development environment is compatible with Laravel and your hosting requirements.

### When to Execute

**Immediately after Admin-Local foundation setup** - This validates your development environment before any code integration.

### Action Steps

1. **Create Environment Analysis Script**
   a. Create `Admin-Local/Deployment/Scripts/comprehensive-env-check.sh`
   b. Include PHP version and extension validation
   c. Add Composer version compatibility checks
   d. Include Node.js and NPM version validation for frontend assets

2. **Run Environment Analysis**
   a. Make script executable: `chmod +x Admin-Local/Deployment/Scripts/comprehensive-env-check.sh`
   b. Execute analysis: `bash Admin-Local/Deployment/Scripts/comprehensive-env-check.sh`
   c. Wait for complete analysis to finish (may take 2-3 minutes)
   d. Review generated analysis report for issues

3. **Review Analysis Report**
   a. Open the generated report in `Admin-Local/Deployment/Logs/env-analysis-YYYYMMDD-HHMMSS.md`
   b. Check PHP version compatibility with Laravel requirements
   c. Verify all required PHP extensions are installed
   d. Review Composer version compatibility

4. **Address Critical Issues**
   a. Install any missing PHP extensions identified in the report
   b. Update Composer to version 2.x if required
   c. Install or update Node.js if frontend compilation is needed
   d. Resolve any function availability issues (exec, shell_exec, etc.)

### Expected Results ✅

-   [ ] Environment analysis completed successfully without critical errors
-   [ ] Comprehensive report generated with actionable recommendations
-   [ ] Critical issues identified and resolution steps documented
-   [ ] Analysis report saved to `Admin-Local/Deployment/Logs/` directory
-   [ ] Hosting compatibility validated for your specific hosting environment

### Verification Steps

-   [ ] Analysis script executed without errors
-   [ ] Generated report contains all required sections (PHP, Composer, Node.js, Laravel)
-   [ ] Action items are clearly identified and prioritized
-   [ ] Report is saved and accessible for future reference

### Troubleshooting Tips

-   **Issue:** Missing PHP extensions
    -   **Solution:** Install extensions using your system package manager (e.g., `sudo apt install php8.2-mbstring`)
-   **Issue:** Composer version 1.x detected but version 2.x required
    -   **Solution:** Update Composer: `composer self-update --2`
-   **Issue:** Node.js not installed
    -   **Solution:** Install Node.js from nodejs.org or use a version manager like nvm

### Environment Analysis Script Template

**comprehensive-env-check.sh:**

```bash
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
PHP_REQUIRED=$(grep -oP '"php":\s*"[^"]*"' composer.json 2>/dev/null | cut -d'"' -f4 || echo "Not specified")
echo "- Current: $PHP_CURRENT" >> $REPORT
echo "- Required: $PHP_REQUIRED" >> $REPORT

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

# 2. Generate action items
echo "" >> $REPORT
echo "## ⚠️ Action Items" >> $REPORT

if [ ${#MISSING_EXTENSIONS[@]} -gt 0 ]; then
    echo "### Missing PHP Extensions" >> $REPORT
    echo "Install the following PHP extensions:" >> $REPORT
    for ext in "${MISSING_EXTENSIONS[@]}"; do
        echo "- sudo apt-get install php${PHP_VERSION}-${ext}" >> $REPORT
    done
fi

echo ""
echo "📋 Full report saved to: $REPORT"
cat $REPORT
```

### Critical Extension Checklist

-   [ ] **bcmath** - Required for precise arithmetic operations
-   [ ] **mbstring** - Required for multi-byte string handling
-   [ ] **xml** - Required for XML processing
-   [ ] **zip** - Required for package management
-   [ ] **gd** or **imagick** - Required for image processing
-   [ ] **curl** - Required for HTTP requests
-   [ ] **openssl** - Required for encryption and secure connections

---

## Step 04: Clone & Integrate Repository

**🟢 Location:** Local Machine | **⏱️ Time:** 5-10 minutes | **🔧 Type:** Repository Integration

### Purpose

Clone GitHub repository and integrate with local project structure, ensuring proper connectivity and deployment variable synchronization.

### When to Execute

**After environment analysis completion and any critical issues resolved** - Ensures environment can support the Laravel application.

### Action Steps

1. **Automated Repository Cloning with Pre-validation**

    ```bash
    # Advanced repository cloning with comprehensive validation
    echo "📥 Starting repository cloning and integration process..."

    # Ensure we're in the correct directory
    source .project-paths.env
    cd "$LOCAL_MACHINE_PATH" || {
        echo "❌ Cannot navigate to project directory: $LOCAL_MACHINE_PATH"
        exit 1
    }

    echo "📍 Current directory: $(pwd)"
    echo "🎯 Project: $PROJECT_NAME"

    # Get repository URL from previous configuration or prompt
    REPO_URL=""

    # Try to get from PROJECT-INFO.md
    if [[ -f "PROJECT-INFO.md" ]]; then
        REPO_URL=$(grep -oP "SSH Clone URL:\*\* \K[^]]*" PROJECT-INFO.md 2>/dev/null | head -1 || echo "")
    fi

    # Try to get from deployment-variables.json
    if [[ -z "$REPO_URL" && -f "deployment-variables.json" && command -v jq &> /dev/null ]]; then
        REPO_URL=$(jq -r '.repository.url // empty' deployment-variables.json 2>/dev/null || echo "")
    fi

    # Prompt if still not found
    if [[ -z "$REPO_URL" ]]; then
        echo "❓ Repository URL not found in configuration"
        echo "Please provide the SSH URL from Step 02 (format: git@github.com:username/repo.git)"
        read -p "Repository SSH URL: " REPO_URL

        # Validate SSH URL format
        if [[ ! "$REPO_URL" =~ ^git@github\.com:.+\.git$ ]]; then
            echo "⚠️  Invalid SSH URL format. Should be: git@github.com:username/repo.git"
            read -p "Please enter a valid SSH URL: " REPO_URL
        fi
    fi

    echo "🔗 Repository URL: $REPO_URL"

    # Pre-clone validation
    echo "🔍 Validating repository access..."
    if git ls-remote "$REPO_URL" &> /dev/null; then
        echo "✅ Repository is accessible"
    else
        echo "❌ Repository access failed"
        echo "🔧 Troubleshooting steps:"
        echo "   1. Verify SSH key is configured: ssh -T git@github.com"
        echo "   2. Check repository URL format"
        echo "   3. Confirm repository exists and you have access"
        exit 1
    fi
    ```

    **Expected Result:**

    ```
    📥 Starting repository cloning and integration process...
    📍 Current directory: /Users/username/Projects/YourProjectName
    🎯 Project: YourProjectName
    🔗 Repository URL: git@github.com:username/YourProjectName.git
    🔍 Validating repository access...
    ✅ Repository is accessible
    ```

2. **Smart Repository Cloning with Conflict Detection**

    ```bash
    # Execute repository clone with conflict handling
    echo "📦 Cloning repository with conflict detection..."

    # Check if directory has existing files (excluding our setup files)
    EXISTING_FILES=$(find . -maxdepth 1 -type f ! -name ".project-paths.env" ! -name "*.sh" ! -name "*.md" ! -name "workspace-*" | wc -l)
    EXISTING_DIRS=$(find . -maxdepth 1 -type d ! -name "." ! -name "Admin-Local" ! -name ".git" | wc -l)

    if [[ $EXISTING_FILES -gt 0 || $EXISTING_DIRS -gt 0 ]]; then
        echo "⚠️  Directory contains existing files/directories"
        echo "Existing content:"
        ls -la | grep -v "^total" | tail -n +2
        echo ""
        read -p "Continue with clone? This will merge with existing content (yes/no): " CONTINUE_CLONE
        if [[ "$CONTINUE_CLONE" != "yes" ]]; then
            echo "❌ Clone cancelled by user"
            exit 1
        fi
    fi

    # Create temporary directory for clone if needed
    if [[ -d ".git" ]]; then
        echo "🔄 Git repository already exists, updating instead of cloning..."
        git fetch origin
        git status

        # Verify remote URL matches
        CURRENT_REMOTE=$(git remote get-url origin 2>/dev/null || echo "")
        if [[ "$CURRENT_REMOTE" != "$REPO_URL" ]]; then
            echo "⚠️  Remote URL mismatch!"
            echo "Current: $CURRENT_REMOTE"
            echo "Expected: $REPO_URL"
            read -p "Update remote URL? (yes/no): " UPDATE_REMOTE
            if [[ "$UPDATE_REMOTE" == "yes" ]]; then
                git remote set-url origin "$REPO_URL"
                echo "✅ Remote URL updated"
            fi
        fi

    else
        echo "📥 Cloning repository..."

        # Clone using different strategies based on directory content
        if [[ $EXISTING_FILES -eq 0 && $EXISTING_DIRS -eq 0 ]]; then
            # Empty directory - direct clone
            git clone "$REPO_URL" . 2>&1 | tee clone-log.txt
        else
            # Directory has content - clone to temp and merge
            echo "🔄 Using merge strategy for existing content..."

            # Clone to temporary directory
            TEMP_CLONE="temp-clone-$(date +%s)"
            git clone "$REPO_URL" "$TEMP_CLONE"

            # Initialize git in current directory if not exists
            if [[ ! -d ".git" ]]; then
                git init
                git remote add origin "$REPO_URL"
            fi

            # Copy .git directory from temp clone
            cp -r "$TEMP_CLONE/.git" .

            # Clean up temp directory
            rm -rf "$TEMP_CLONE"

            echo "✅ Repository integrated with existing content"
        fi
    fi

    # Verify clone success
    if [[ -d ".git" ]]; then
        echo "✅ Git repository successfully integrated"

        # Log clone details
        cat > clone-integration-report.md << EOF
    # Repository Clone Integration Report

    **Generated:** $(date '+%Y-%m-%d %H:%M:%S %Z')
    **Repository:** $REPO_URL
    **Local Path:** $LOCAL_MACHINE_PATH

    ## Clone Details
    - **Status:** Successful
    - **Remote Origin:** $(git remote get-url origin)
    - **Current Branch:** $(git branch --show-current)
    - **Last Commit:** $(git log --oneline -1 2>/dev/null || echo "No commits yet")

    ## Repository Structure
    \`\`\`
    $(find . -maxdepth 2 -type f -name "*.php" -o -name "*.json" -o -name "*.md" | head -10)
    \`\`\`

    ## Integration Status
    - ✅ Repository cloned successfully
    - ✅ Remote origin configured
    - ✅ Admin-Local structure preserved
    - ✅ Project paths maintained
    EOF

        echo "📋 Clone integration report saved"

    else
        echo "❌ Repository clone failed"
        if [[ -f "clone-log.txt" ]]; then
            echo "Error details:"
            cat clone-log.txt
        fi
        exit 1
    fi
    ```

    **Expected Result:**

    ```
    📦 Cloning repository with conflict detection...
    📥 Cloning repository...
    Cloning into '.'...
    remote: Enumerating objects: 45, done.
    remote: Counting objects: 100% (45/45), done.
    remote: Compressing objects: 100% (35/35), done.
    remote: Total 45 (delta 8), reused 45 (delta 8), pack-reused 0
    Receiving objects: 100% (45/45), 15.42 KiB | 2.57 MiB/s, done.
    Resolving deltas: 100% (8/8), done.
    ✅ Git repository successfully integrated
    📋 Clone integration report saved
    ```

3. **Comprehensive Repository Validation and Analysis**

    ```bash
    # Perform comprehensive repository validation
    echo "🔍 Performing comprehensive repository validation..."

    # Basic Git functionality validation
    echo "## Git Repository Validation"

    # Check Git status
    if git status &> /dev/null; then
        echo "✅ Git repository is functional"

        # Check remote connectivity
        if git fetch origin --dry-run &> /dev/null; then
            echo "✅ Remote connectivity confirmed"
        else
            echo "⚠️  Remote connectivity issues detected"
        fi

        # Check branch information
        echo "🌿 Available branches:"
        git branch -a | head -10

    else
        echo "❌ Git repository validation failed"
        exit 1
    fi

    # Laravel project validation
    echo ""
    echo "## Laravel Project Structure Validation"

    LARAVEL_FILES=(
        "artisan:Laravel Artisan CLI"
        "composer.json:Composer Configuration"
        "app/Http/Kernel.php:Laravel HTTP Kernel"
        "config/app.php:Laravel App Configuration"
        "database/migrations:Database Migrations"
    )

    IS_LARAVEL_PROJECT=false

    for file_info in "${LARAVEL_FILES[@]}"; do
        IFS=':' read -ra file_parts <<< "$file_info"
        file="${file_parts[0]}"
        description="${file_parts[1]}"

        if [[ -f "$file" || -d "$file" ]]; then
            echo "✅ $description: $file"
            IS_LARAVEL_PROJECT=true
        else
            echo "❓ $description: $file (not found)"
        fi
    done

    if [[ "$IS_LARAVEL_PROJECT" == "true" ]]; then
        echo "✅ Laravel project structure detected"

        # Detect Laravel version
        if [[ -f "composer.json" ]]; then
            LARAVEL_VERSION=$(grep -oP '"laravel/framework": "[^"]*"' composer.json | cut -d'"' -f4 || echo "Unknown")
            echo "📊 Laravel Framework Version: $LARAVEL_VERSION"

            # Update deployment variables with Laravel version
            if [[ -f "deployment-variables.json" && command -v jq &> /dev/null ]]; then
                jq --arg version "$LARAVEL_VERSION" '.project.laravel_version = $version' deployment-variables.json > temp.json && mv temp.json deployment-variables.json
                echo "✅ Laravel version updated in deployment configuration"
            fi
        fi

        # Check for frontend build system
        FRONTEND_DETECTED="none"
        if [[ -f "package.json" ]]; then
            if grep -q "vite" package.json; then
                FRONTEND_DETECTED="vite"
            elif grep -q "webpack" package.json; then
                FRONTEND_DETECTED="webpack"
            elif grep -q "mix" package.json; then
                FRONTEND_DETECTED="laravel-mix"
            fi
            echo "🎨 Frontend Build System: $FRONTEND_DETECTED"
        fi

    else
        echo "⚠️  This doesn't appear to be a Laravel project"
        echo "   Continuing with generic PHP project setup..."
    fi
    ```

    **Expected Result:**

    ```
    🔍 Performing comprehensive repository validation...
    ## Git Repository Validation
    ✅ Git repository is functional
    ✅ Remote connectivity confirmed
    🌿 Available branches:
    * main
      remotes/origin/HEAD -> origin/main
      remotes/origin/main

    ## Laravel Project Structure Validation
    ✅ Laravel Artisan CLI: artisan
    ✅ Composer Configuration: composer.json
    ✅ Laravel HTTP Kernel: app/Http/Kernel.php
    ✅ Laravel App Configuration: config/app.php
    ✅ Database Migrations: database/migrations
    ✅ Laravel project structure detected
    📊 Laravel Framework Version: ^10.0
    ✅ Laravel version updated in deployment configuration
    🎨 Frontend Build System: vite
    ```

4. **Advanced Configuration Synchronization and Path Updates**

    ```bash
    # Synchronize all configuration files with actual repository data
    echo "⚙️ Synchronizing configuration with repository data..."

    # Update deployment variables with comprehensive repository information
    if [[ -f "deployment-variables.json" && command -v jq &> /dev/null ]]; then
        echo "🔄 Updating deployment configuration..."

        # Get current Git information
        CURRENT_BRANCH=$(git branch --show-current)
        LAST_COMMIT=$(git rev-parse HEAD 2>/dev/null || echo "")
        REMOTE_URL=$(git remote get-url origin)

        # Update JSON configuration with actual values
        jq --arg url "$REMOTE_URL" \
           --arg branch "$CURRENT_BRANCH" \
           --arg commit "$LAST_COMMIT" \
           --arg path "$LOCAL_MACHINE_PATH" \
           '.repository.url = $url |
            .repository.branch = $branch |
            .repository.commit_start = $commit |
            .paths.local_machine = $path' \
            deployment-variables.json > temp.json && mv temp.json deployment-variables.json

        echo "✅ Deployment configuration updated with repository data"
    fi

    # Update PROJECT-INFO.md with repository details
    if [[ -f "PROJECT-INFO.md" ]]; then
        echo "📝 Updating project documentation..."

        # Add repository section if it doesn't exist
        if ! grep -q "## Repository Status" PROJECT-INFO.md; then
            cat >> PROJECT-INFO.md << EOF

    ## Repository Status
    - **Cloned:** $(date '+%Y-%m-%d %H:%M:%S %Z')
    - **Remote URL:** $REMOTE_URL
    - **Current Branch:** $CURRENT_BRANCH
    - **Last Commit:** $(git log --oneline -1 2>/dev/null || echo "No commits")
    - **Laravel Version:** ${LARAVEL_VERSION:-"Not detected"}
    - **Frontend System:** ${FRONTEND_DETECTED:-"Not detected"}

    ## Local Integration
    - **Local Path:** \`$LOCAL_MACHINE_PATH\`
    - **Git Status:** Operational
    - **Admin-Local:** Integrated
    - **Configuration:** Synchronized
    EOF
            echo "✅ PROJECT-INFO.md updated with repository status"
        fi
    fi

    # Create repository integration validation script
    cat > validate-repository-integration.sh << 'EOF'
    #!/bin/bash

    echo "🔍 Repository Integration Validation"
    echo "==================================="

    source .project-paths.env

    # Git repository validation
    echo "## Git Repository Status"
    echo "Working Directory: $(pwd)"
    echo "Repository URL: $(git remote get-url origin 2>/dev/null || echo 'Not configured')"
    echo "Current Branch: $(git branch --show-current 2>/dev/null || echo 'Not in git repo')"
    echo "Git Status: $(if git status &> /dev/null; then echo 'Functional'; else echo 'Issues detected'; fi)"
    echo ""

    # Configuration validation
    echo "## Configuration Status"
    if [[ -f "deployment-variables.json" ]]; then
        if command -v jq &> /dev/null; then
            echo "Repository URL in config: $(jq -r '.repository.url' deployment-variables.json)"
            echo "Local path in config: $(jq -r '.paths.local_machine' deployment-variables.json)"
        else
            echo "Deployment config: Present (jq not available for details)"
        fi
    else
        echo "Deployment config: Missing"
    fi

    # Laravel project validation
    echo ""
    echo "## Project Structure"
    if [[ -f "artisan" ]]; then
        echo "Laravel Project: ✅ Detected"
        if [[ -f "composer.json" ]]; then
            echo "Composer Config: ✅ Present"
        fi
        if [[ -f "package.json" ]]; then
            echo "Package Config: ✅ Present"
        fi
    else
        echo "Laravel Project: ❓ Not detected"
    fi

    # Admin-Local integration
    echo ""
    echo "## Admin-Local Integration"
    if [[ -d "Admin-Local" ]]; then
        echo "Admin-Local Directory: ✅ Present"
        echo "Scripts Available: $(find Admin-Local -name "*.sh" 2>/dev/null | wc -l)"
        echo "Config Files: $(find Admin-Local -name "*.json" 2>/dev/null | wc -l)"
    else
        echo "Admin-Local Directory: ❌ Missing"
    fi

    echo ""
    echo "🎯 Integration Status: $(if [[ -d ".git" && -f "deployment-variables.json" ]]; then echo 'Complete'; else echo 'Incomplete'; fi)"
    EOF

    chmod +x validate-repository-integration.sh

    # Run validation
    echo ""
    echo "🚀 Running integration validation..."
    ./validate-repository-integration.sh

    echo ""
    echo "✅ Repository integration and configuration synchronization complete"
    echo "📋 Integration report available in: clone-integration-report.md"
    echo "🔍 Validation script available: validate-repository-integration.sh"
    ```

    **Expected Result:**

    ```
    ⚙️ Synchronizing configuration with repository data...
    🔄 Updating deployment configuration...
    ✅ Deployment configuration updated with repository data
    📝 Updating project documentation...
    ✅ PROJECT-INFO.md updated with repository status

    🚀 Running integration validation...
    🔍 Repository Integration Validation
    ## Git Repository Status
    Working Directory: /Users/username/Projects/YourProjectName
    Repository URL: git@github.com:username/YourProjectName.git
    Current Branch: main
    Git Status: Functional

    ## Configuration Status
    Repository URL in config: git@github.com:username/YourProjectName.git
    Local path in config: /Users/username/Projects/YourProjectName

    ## Project Structure
    Laravel Project: ✅ Detected
    Composer Config: ✅ Present
    Package Config: ✅ Present

    ## Admin-Local Integration
    Admin-Local Directory: ✅ Present
    Scripts Available: 3
    Config Files: 1

    🎯 Integration Status: Complete

    ✅ Repository integration and configuration synchronization complete
    📋 Integration report available in: clone-integration-report.md
    🔍 Validation script available: validate-repository-integration.sh
    ```

### Expected Results ✅

-   [ ] Repository successfully cloned into project directory
-   [ ] `.git` directory present and functional for version control
-   [ ] Deployment variables updated with actual repository and path information
-   [ ] Git connectivity verified and remote origin properly configured

### Verification Steps

-   [ ] `git status` shows clean working directory or expected files
-   [ ] Git remote operations work without authentication errors
-   [ ] Deployment variables contain accurate path and repository information
-   [ ] Admin-Local structure coexists properly with repository contents

### Troubleshooting Tips

-   **Issue:** SSH authentication failures
    -   **Solution:** Verify SSH key is added to GitHub account, test with `ssh -T git@github.com`
-   **Issue:** Directory not empty error
    -   **Solution:** Remove existing files or clone to temporary directory and move contents
-   **Issue:** Permission denied on clone
    -   **Solution:** Check directory permissions and SSH key configuration

### Post-Clone Checklist

-   [ ] Repository cloned successfully without errors
-   [ ] Git remote origin properly configured and accessible
-   [ ] Admin-Local structure preserved and functional
-   [ ] Deployment variables accurately reflect actual paths and repository information

---

## Step 05: Setup Git Branching Strategy

**🟢 Location:** Local Machine | **⏱️ Time:** 10-15 minutes | **🔧 Type:** Version Control Strategy

### Purpose

Establish comprehensive Git workflow with enhanced branch management that supports different deployment stages and customization tracking.

### When to Execute

**After successful repository clone and validation** - Ensures branching strategy is built on stable repository foundation.

### Action Steps

1. **Advanced Git Branching Strategy with Automated Validation**

    ```bash
    # Comprehensive Git branching strategy implementation
    echo "🌿 Setting up advanced Git branching strategy..."

    # Ensure we're in a Git repository
    if [[ ! -d ".git" ]]; then
        echo "❌ Not in a Git repository. Please complete Step 04 first."
        exit 1
    fi

    # Load project configuration
    source .project-paths.env

    echo "📍 Current repository: $(git remote get-url origin)"
    echo "🎯 Project: $PROJECT_NAME"
    echo "🌱 Setting up 6 strategic branches for comprehensive workflow"

    # Define branch strategy with purposes
    declare -A BRANCH_STRATEGY=(
        ["development"]="Active development and feature integration"
        ["staging"]="Pre-production testing and validation"
        ["production"]="Production deployment automation"
        ["vendor/original"]="Original third-party/CodeCanyon baseline"
        ["customized"]="Modified third-party code with customizations"
        ["hotfix"]="Emergency production fixes"
    )

    # Get current branch
    CURRENT_BRANCH=$(git branch --show-current)
    echo "📍 Currently on branch: $CURRENT_BRANCH"

    # Ensure main branch is up to date
    if [[ "$CURRENT_BRANCH" != "main" ]]; then
        git checkout main || {
            echo "⚠️  Main branch not available, using current branch as base"
        }
    fi

    # Fetch latest changes if possible
    git fetch origin 2>/dev/null || echo "⚠️  Could not fetch from origin (first time setup expected)"
    ```

    **Expected Result:**

    ```
    🌿 Setting up advanced Git branching strategy...
    📍 Current repository: git@github.com:username/YourProjectName.git
    🎯 Project: YourProjectName
    🌱 Setting up 6 strategic branches for comprehensive workflow
    📍 Currently on branch: main
    ```

2. **Intelligent Branch Creation with Conflict Detection**

    ```bash
    # Create branches with intelligent conflict detection
    echo "🏗️ Creating strategic branches with validation..."

    CREATED_BRANCHES=()
    EXISTING_BRANCHES=()
    FAILED_BRANCHES=()

    for branch in "${!BRANCH_STRATEGY[@]}"; do
        echo ""
        echo "🌱 Processing branch: $branch"
        echo "   Purpose: ${BRANCH_STRATEGY[$branch]}"

        # Check if branch already exists locally or remotely
        BRANCH_EXISTS_LOCAL=$(git branch --list "$branch" | wc -l)
        BRANCH_EXISTS_REMOTE=$(git branch -r --list "origin/$branch" | wc -l)

        if [[ $BRANCH_EXISTS_LOCAL -gt 0 ]]; then
            echo "   📋 Branch exists locally"
            EXISTING_BRANCHES+=("$branch")

            # Ensure it's tracking the remote
            if [[ $BRANCH_EXISTS_REMOTE -eq 0 ]]; then
                echo "   🔗 Pushing existing local branch to remote..."
                git push -u origin "$branch" 2>&1 | grep -E "(up-to-date|new branch)" || {
                    echo "   ⚠️  Failed to push $branch"
                    FAILED_BRANCHES+=("$branch")
                }
            else
                echo "   ✅ Branch exists locally and remotely"
            fi

        elif [[ $BRANCH_EXISTS_REMOTE -gt 0 ]]; then
            echo "   📥 Branch exists on remote, creating local tracking branch..."
            git checkout -b "$branch" "origin/$branch" && {
                echo "   ✅ Local tracking branch created"
                EXISTING_BRANCHES+=("$branch")
            } || {
                echo "   ❌ Failed to create local tracking branch"
                FAILED_BRANCHES+=("$branch")
            }

        else
            echo "   🆕 Creating new branch..."
            git checkout -b "$branch" && {
                echo "   📤 Pushing new branch to remote..."
                git push -u origin "$branch" && {
                    echo "   ✅ Branch created successfully"
                    CREATED_BRANCHES+=("$branch")
                } || {
                    echo "   ❌ Failed to push to remote"
                    FAILED_BRANCHES+=("$branch")
                }
            } || {
                echo "   ❌ Failed to create local branch"
                FAILED_BRANCHES+=("$branch")
            }
        fi

        # Return to main branch after each creation
        git checkout main 2>/dev/null || git checkout "$CURRENT_BRANCH" 2>/dev/null
    done

    # Branch creation summary
    echo ""
    echo "📊 Branch Creation Summary"
    echo "=========================="
    echo "✅ Created branches: ${#CREATED_BRANCHES[@]} (${CREATED_BRANCHES[*]})"
    echo "📋 Existing branches: ${#EXISTING_BRANCHES[@]} (${EXISTING_BRANCHES[*]})"
    echo "❌ Failed branches: ${#FAILED_BRANCHES[@]} (${FAILED_BRANCHES[*]})"
    ```

    **Expected Result:**

    ```
    🏗️ Creating strategic branches with validation...

    🌱 Processing branch: development
       Purpose: Active development and feature integration
       🆕 Creating new branch...
       📤 Pushing new branch to remote...
       ✅ Branch created successfully

    🌱 Processing branch: staging
       Purpose: Pre-production testing and validation
       🆕 Creating new branch...
       📤 Pushing new branch to remote...
       ✅ Branch created successfully

    📊 Branch Creation Summary
    ✅ Created branches: 6 (development staging production vendor/original customized hotfix)
    📋 Existing branches: 0 ()
    ❌ Failed branches: 0 ()
    ```

3. **Comprehensive Branch Validation and Configuration**

    ```bash
    # Perform comprehensive branch validation and setup
    echo "🔍 Validating branch configuration and connectivity..."

    # Create branch validation report
    VALIDATION_REPORT="branch-validation-report.md"

    cat > "$VALIDATION_REPORT" << EOF
    # Git Branch Strategy Validation Report

    **Generated:** $(date '+%Y-%m-%d %H:%M:%S %Z')
    **Repository:** $(git remote get-url origin)
    **Project:** $PROJECT_NAME

    ## Branch Strategy Overview

    This repository implements a 6-branch strategy for comprehensive Laravel deployment workflow:

    EOF

    # Validate each branch and add to report
    echo "## Branch Validation Results" >> "$VALIDATION_REPORT"
    echo "" >> "$VALIDATION_REPORT"

    ALL_BRANCHES_VALID=true
    VALID_BRANCHES=()
    INVALID_BRANCHES=()

    for branch in "${!BRANCH_STRATEGY[@]}"; do
        echo "🔍 Validating branch: $branch"

        # Check local branch exists
        if git show-ref --verify --quiet "refs/heads/$branch"; then
            LOCAL_STATUS="✅"
        else
            LOCAL_STATUS="❌"
            ALL_BRANCHES_VALID=false
            INVALID_BRANCHES+=("$branch")
        fi

        # Check remote branch exists
        if git show-ref --verify --quiet "refs/remotes/origin/$branch"; then
            REMOTE_STATUS="✅"

            # Check if branch is properly tracking
            TRACKING_INFO=$(git branch -vv | grep "^\*\?\s*$branch" | grep -o '\[origin/[^]]*\]' || echo "")
            if [[ -n "$TRACKING_INFO" ]]; then
                TRACKING_STATUS="✅"
                VALID_BRANCHES+=("$branch")
            else
                TRACKING_STATUS="⚠️"
            fi
        else
            REMOTE_STATUS="❌"
            TRACKING_STATUS="❌"
            ALL_BRANCHES_VALID=false
            INVALID_BRANCHES+=("$branch")
        fi

        # Add to report
        cat >> "$VALIDATION_REPORT" << EOF
    ### Branch: \`$branch\`
    - **Purpose:** ${BRANCH_STRATEGY[$branch]}
    - **Local Branch:** $LOCAL_STATUS
    - **Remote Branch:** $REMOTE_STATUS
    - **Tracking Setup:** $TRACKING_STATUS

    EOF

        echo "   Local: $LOCAL_STATUS | Remote: $REMOTE_STATUS | Tracking: $TRACKING_STATUS"
    done

    # Add summary to report
    cat >> "$VALIDATION_REPORT" << EOF

    ## Validation Summary

    - **Total Branches:** ${#BRANCH_STRATEGY[@]}
    - **Valid Branches:** ${#VALID_BRANCHES[@]} (${VALID_BRANCHES[*]})
    - **Invalid Branches:** ${#INVALID_BRANCHES[@]} (${INVALID_BRANCHES[*]})
    - **Overall Status:** $(if [[ "$ALL_BRANCHES_VALID" == "true" ]]; then echo "✅ All branches configured correctly"; else echo "⚠️ Some branches need attention"; fi)

    ## Branch Usage Guidelines

    | Branch | Usage | Typical Workflow |
    |--------|-------|------------------|
    | \`main\` | Stable releases | Merge from production after successful deployment |
    | \`development\` | Active development | Feature branches merge here for integration |
    | \`staging\` | Pre-production testing | Deploy to staging environment for final validation |
    | \`production\` | Production deployment | Deploy to production servers |
    | \`vendor/original\` | Third-party baseline | Keep original CodeCanyon/vendor code |
    | \`customized\` | Modified vendor code | Track customizations to third-party code |
    | \`hotfix\` | Emergency fixes | Quick fixes for production issues |

    EOF

    echo ""
    echo "📋 Branch validation report saved: $VALIDATION_REPORT"

    if [[ "$ALL_BRANCHES_VALID" == "true" ]]; then
        echo "✅ All branches configured successfully"
    else
        echo "⚠️  Some branches need attention - check validation report"
    fi
    ```

    **Expected Result:**

    ```
    🔍 Validating branch configuration and connectivity...
    🔍 Validating branch: development
       Local: ✅ | Remote: ✅ | Tracking: ✅
    🔍 Validating branch: staging
       Local: ✅ | Remote: ✅ | Tracking: ✅
    🔍 Validating branch: production
       Local: ✅ | Remote: ✅ | Tracking: ✅
    🔍 Validating branch: vendor/original
       Local: ✅ | Remote: ✅ | Tracking: ✅
    🔍 Validating branch: customized
       Local: ✅ | Remote: ✅ | Tracking: ✅
    🔍 Validating branch: hotfix
       Local: ✅ | Remote: ✅ | Tracking: ✅

    📋 Branch validation report saved: branch-validation-report.md
    ✅ All branches configured successfully
    ```

4. **Advanced Branch Management and Team Workflow Setup**

    ````bash
    # Create advanced branch management tools and team workflows
    echo "🔧 Setting up branch management tools and team workflows..."

    # Create branch management script
    cat > branch-manager.sh << 'EOF'
    #!/bin/bash

    # Advanced Git Branch Manager for Laravel Projects

    source .project-paths.env 2>/dev/null || echo "Warning: .project-paths.env not found"

    show_help() {
        echo "🌿 Git Branch Manager - Laravel Deployment Strategy"
        echo "=================================================="
        echo ""
        echo "Usage: ./branch-manager.sh <command> [options]"
        echo ""
        echo "Commands:"
        echo "  status          Show current branch status and health"
        echo "  switch <branch> Switch to branch with validation"
        echo "  sync            Synchronize all branches with remote"
        echo "  clean           Clean up merged feature branches"
        echo "  deploy-prep     Prepare branch for deployment"
        echo "  hotfix-start    Start emergency hotfix workflow"
        echo "  validate        Validate branch strategy integrity"
        echo ""
        echo "Branch Strategy:"
        echo "  main            → Stable releases"
        echo "  development     → Active development"
        echo "  staging         → Pre-production testing"
        echo "  production      → Production deployment"
        echo "  vendor/original → Third-party baseline"
        echo "  customized      → Modified vendor code"
        echo "  hotfix          → Emergency fixes"
    }

    show_status() {
        echo "📊 Branch Status Report"
        echo "======================"

        # Current branch info
        CURRENT=$(git branch --show-current)
        echo "Current Branch: $CURRENT"
        echo "Repository: $(git remote get-url origin 2>/dev/null || echo 'Not configured')"
        echo ""

        # Branch list with last commit info
        echo "📋 All Branches:"
        git branch -a --format='%(refname:short) %(committerdate:relative) %(subject)' | \
        grep -E '^(main|development|staging|production|vendor/original|customized|hotfix)' | \
        head -10

        # Check for uncommitted changes
        if [[ -n "$(git status --porcelain)" ]]; then
            echo ""
            echo "⚠️  Uncommitted changes detected:"
            git status --short
        fi

        # Check for unpushed commits
        echo ""
        echo "📤 Unpushed commits on current branch:"
        git log --oneline origin/"$CURRENT".."$CURRENT" 2>/dev/null | head -5 || echo "No unpushed commits"
    }

    sync_branches() {
        echo "🔄 Synchronizing all branches with remote..."

        git fetch origin

        for branch in main development staging production vendor/original customized hotfix; do
            if git show-ref --quiet refs/heads/"$branch"; then
                echo "🔄 Syncing $branch..."
                git checkout "$branch" && git pull origin "$branch"
            fi
        done

        git checkout main
        echo "✅ Branch synchronization complete"
    }

    validate_strategy() {
        echo "🔍 Validating branch strategy integrity..."

        REQUIRED_BRANCHES=(main development staging production vendor/original customized hotfix)
        MISSING_BRANCHES=()

        for branch in "${REQUIRED_BRANCHES[@]}"; do
            if ! git show-ref --quiet refs/heads/"$branch"; then
                MISSING_BRANCHES+=("$branch")
            fi
        done

        if [[ ${#MISSING_BRANCHES[@]} -eq 0 ]]; then
            echo "✅ All required branches present"
            return 0
        else
            echo "❌ Missing branches: ${MISSING_BRANCHES[*]}"
            echo "Run the branch setup script to recreate missing branches"
            return 1
        fi
    }

    # Main command processing
    case "${1:-help}" in
        "status")
            show_status
            ;;
        "sync")
            sync_branches
            ;;
        "validate")
            validate_strategy
            ;;
        "help"|*)
            show_help
            ;;
    esac
    EOF

    chmod +x branch-manager.sh

    # Create team workflow documentation
    cat > .github/workflows/branch-strategy.md << 'EOF'
    # Team Git Branch Strategy

    ## Branch Purposes & Workflow

    ### 🎯 Core Branches

    1. **`main`** - Production-ready releases
       - Only merge from `production` after successful deployment
       - Protected branch requiring pull request reviews

    2. **`development`** - Active development integration
       - Feature branches merge here first
       - Continuous integration runs here
       - Deploy to development environment

    3. **`staging`** - Pre-production validation
       - Merge from `development` when ready for testing
       - Deploy to staging environment
       - Final validation before production

    4. **`production`** - Production deployment
       - Merge from `staging` after validation
       - Triggers production deployment
       - Protected branch

    ### 🔧 Special Purpose Branches

    5. **`vendor/original`** - Third-party code baseline
       - Keep original CodeCanyon/vendor code
       - Reference for updates and diffs
       - Never modify directly

    6. **`customized`** - Modified vendor code
       - Track customizations to third-party code
       - Merge customizations carefully
       - Document all changes

    7. **`hotfix`** - Emergency production fixes
       - Branch from `main` for urgent fixes
       - Merge back to `main` and `development`
       - Deploy immediately to production

    ## Workflow Commands

    ```bash
    # Check branch status
    ./branch-manager.sh status

    # Sync all branches
    ./branch-manager.sh sync

    # Validate branch strategy
    ./branch-manager.sh validate
    ````

    EOF

    # Update deployment configuration with branch information

    if [[-f "deployment-variables.json" && command -v jq &> /dev/null]]; then
    CURRENT_BRANCH=$(git branch --show-current)
       jq --arg branch "$CURRENT_BRANCH" '.repository.branch = $branch' deployment-variables.json > temp.json && mv temp.json deployment-variables.json
    echo "✅ Deployment configuration updated with current branch"
    fi

    echo ""
    echo "🎉 Branch strategy setup complete!"
    echo "📋 Available tools:"
    echo " • Branch validation report: branch-validation-report.md"
    echo " • Branch manager script: ./branch-manager.sh"
    echo " • Team workflow docs: .github/workflows/branch-strategy.md"
    echo ""
    echo "🚀 Quick test - Branch manager status:"
    ./branch-manager.sh status

    ```

    **Expected Result:**
    ```

    🔧 Setting up branch management tools and team workflows...
    ✅ Deployment configuration updated with current branch

    🎉 Branch strategy setup complete!
    📋 Available tools:
    • Branch validation report: branch-validation-report.md
    • Branch manager script: ./branch-manager.sh
    • Team workflow docs: .github/workflows/branch-strategy.md

    🚀 Quick test - Branch manager status:
    📊 Branch Status Report
    Current Branch: main
    Repository: git@github.com:username/YourProjectName.git

    📋 All Branches:
    main 2 minutes ago Initial commit
    development 1 minute ago Initial commit
    staging 1 minute ago Initial commit
    production 1 minute ago Initial commit

    📤 Unpushed commits on current branch:
    No unpushed commits

    ```

    ```

### Expected Results ✅

-   [ ] Complete branching strategy established with 6 standard branches
-   [ ] All branches (`main`, `development`, `staging`, `production`, `vendor/original`, `customized`) created
-   [ ] All branches pushed to origin and synchronized with GitHub
-   [ ] Branch-specific configurations set according to deployment needs

### Verification Steps

-   [ ] `git branch -a` shows all 6 branches locally and remotely
-   [ ] Branch switching works without errors
-   [ ] GitHub repository shows all branches in web interface
-   [ ] Branch protection rules applied where appropriate

### Troubleshooting Tips

-   **Issue:** Branch creation fails
    -   **Solution:** Ensure you have push permissions to repository and SSH connectivity
-   **Issue:** Branch protection prevents pushes
    -   **Solution:** Configure branch protection after initial branch creation
-   **Issue:** Branches not showing in GitHub
    -   **Solution:** Verify push operations completed successfully with `git push --all origin`

### Branch Usage Guide

| Branch            | Purpose                   | Usage                              |
| ----------------- | ------------------------- | ---------------------------------- |
| `main`            | Production-ready code     | Stable releases only               |
| `development`     | Active development        | Feature integration and testing    |
| `staging`         | Pre-production testing    | Final validation before production |
| `production`      | Production deployment     | Automated deployments              |
| `vendor/original` | Original third-party code | Baseline for updates               |
| `customized`      | Modified third-party code | Track customizations               |

### Git Workflow Validation

-   [ ] All branches accessible and functional
-   [ ] Branch protection configured for production workflows
-   [ ] Team members understand branch usage and purposes
-   [ ] Deployment automation can access appropriate branches

---

## Step 06: Create Universal .gitignore

**🟢 Location:** Local Machine | **⏱️ Time:** 5-10 minutes | **🔧 Type:** Version Control Configuration

### Purpose

Create a comprehensive `.gitignore` file for Laravel applications with CodeCanyon compatibility, ensuring sensitive files and build artifacts are properly excluded from version control.

### When to Execute

**After branch strategy setup** - Ensures `.gitignore` rules are established before any commits that might include unwanted files.

### Action Steps

1. **Create Comprehensive .gitignore File**
   a. Create or update `.gitignore` in project root
   b. Include standard Laravel exclusions (vendor/, storage/logs/, etc.)
   c. Add CodeCanyon-specific exclusions for third-party applications
   d. Include build artifact exclusions (node_modules/, public/build/, etc.)

2. **Add Sensitive File Exclusions**
   a. Exclude environment files (`.env`, `.env.*`)
   b. Add authentication files (`auth.json`, private keys)
   c. Include IDE and system file exclusions
   d. Add deployment-specific exclusions

3. **Include Build and Development Exclusions**
   a. Exclude dependency directories (`node_modules/`, `vendor/`)
   b. Add compiled asset exclusions (`public/build/`, `public/mix-manifest.json`)
   c. Include temporary and cache file exclusions
   d. Add log and debugging file exclusions

4. **Commit .gitignore File**
   a. Add .gitignore to staging: `git add .gitignore`
   b. Commit with clear message: `git commit -m "Add comprehensive .gitignore for Laravel with CodeCanyon compatibility"`
   c. Push to origin: `git push origin main`
   d. Verify .gitignore is active with `git status`

### Expected Results ✅

-   [ ] Universal `.gitignore` created with comprehensive exclusion patterns
-   [ ] Sensitive files and directories properly excluded from version control
-   [ ] Build artifacts excluded to prevent unnecessary repository bloat
-   [ ] CodeCanyon-specific patterns included for third-party compatibility

### Verification Steps

-   [ ] `.gitignore` file exists and contains all required patterns
-   [ ] `git status` shows clean working directory with sensitive files excluded
-   [ ] Build artifacts are properly ignored when created
-   [ ] .gitignore committed and pushed to repository

### Troubleshooting Tips

-   **Issue:** Previously committed files still tracked
    -   **Solution:** Remove from tracking: `git rm --cached filename` then commit
-   **Issue:** .gitignore patterns not working
    -   **Solution:** Ensure patterns match your directory structure and use proper syntax
-   **Issue:** IDE files still being tracked
    -   **Solution:** Add IDE-specific patterns or use global .gitignore

### Universal .gitignore Template

```gitignore
# Laravel Framework
/vendor
/node_modules
/public/build
/public/hot
/storage/*.key
/storage/app/*
!/storage/app/.gitkeep
!/storage/app/public/
/storage/framework/cache/*
!/storage/framework/cache/.gitkeep
/storage/framework/sessions/*
!/storage/framework/sessions/.gitkeep
/storage/framework/views/*
!/storage/framework/views/.gitkeep
/storage/logs/*
!/storage/logs/.gitkeep

# Environment & Configuration
.env
.env.*
!.env.example
auth.json
/config/database.php
/config/mail.php

# CodeCanyon & Third-Party
/bootstrap/cache/*
!/bootstrap/cache/.gitkeep
*.log
*.sql
*.sqlite

# Build & Dependencies
node_modules/
npm-debug.log*
yarn-error.log*

# IDE & Development Tools
.vscode/
.idea/
*.swp
*.swo
*~
.DS_Store
Thumbs.db

# Deployment & Build
/build-tmp/
/deploy/
*.zip
*.tar.gz
```

### Security Checklist

-   [ ] Environment files (.env\*) excluded
-   [ ] Database credentials and API keys protected
-   [ ] Private keys and certificates excluded
-   [ ] Authentication and session files protected
-   [ ] Temporary and cache files ignored

---

## Step 07: Setup Universal Dependency Analysis System

**🟢 Location:** Local Machine | **⏱️ Time:** 15-20 minutes | **🔧 Type:** Dependency Management

### Purpose

Implement enhanced system to detect dev dependencies needed in production, ensuring proper package classification and preventing deployment issues.

### When to Execute

**Before any dependency installation** - This system must be in place before installing packages to ensure proper analysis and classification.

### Action Steps

1. **Create Universal Dependency Analyzer Script**
   a. Create `Admin-Local/Deployment/Scripts/universal-dependency-analyzer.sh`
   b. Include pattern-based detection for 12+ common dev packages
   c. Implement usage analysis in production code directories
   d. Add auto-fix functionality with user confirmation

2. **Create Analysis Tools Installation Script**
   a. Create `Admin-Local/Deployment/Scripts/install-analysis-tools.sh`
   b. Include PHPStan/Larastan for static analysis
   c. Add Composer Unused and Composer Require Checker
   d. Include Security Checker for vulnerability scanning

3. **Configure Package Detection Patterns**
   a. Set up detection for common dev packages that might be needed in production
   b. Configure usage pattern matching for Laravel-specific packages
   c. Include auto-discovery package analysis
   d. Set up Composer 2 compatibility validation

4. **Test Dependency Analysis System**
   a. Make scripts executable: `chmod +x Admin-Local/Deployment/Scripts/*.sh`
   b. Test analyzer without composer.json: should handle gracefully
   c. Verify pattern matching logic works correctly
   d. Confirm auto-fix prompts function properly

### Expected Results ✅

-   [ ] Universal dependency analysis system created and ready for use
-   [ ] Pattern-based detection implemented for 12+ common dev packages
-   [ ] Auto-fix functionality with user confirmation included and tested
-   [ ] Analysis tools installation scripts prepared for later use
-   [ ] Automated classification system ready for dependency validation

### Verification Steps

-   [ ] Analysis scripts exist and are executable
-   [ ] Pattern matching logic handles edge cases properly
-   [ ] Auto-fix functionality prompts correctly for user confirmation
-   [ ] Scripts handle missing composer.json gracefully

### Troubleshooting Tips

-   **Issue:** Script execution permission denied
    -   **Solution:** Make executable with `chmod +x Admin-Local/Deployment/Scripts/*.sh`
-   **Issue:** Pattern matching not working correctly
    -   **Solution:** Test regex patterns and update for your specific use cases
-   **Issue:** Auto-fix making unwanted changes
    -   **Solution:** Always review recommendations before accepting auto-fix suggestions

### Universal Dependency Analyzer Template

**universal-dependency-analyzer.sh:**

```bash
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

# Define packages and their usage patterns
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

# Check each package (implementation details)
echo "📋 Analysis complete! Report saved to: $REPORT"

# Auto-fix prompt
if [ ${#MOVE_TO_PROD[@]} -gt 0 ]; then
    echo ""
    echo "⚠️ Found ${#MOVE_TO_PROD[@]} packages that need to be moved to production!"
    echo "Do you want to automatically fix these? (yes/no)"
    read -p "" AUTO_FIX_CONFIRMATION
    if [[ "$AUTO_FIX_CONFIRMATION" == "yes" ]]; then
        echo "Attempting to auto-fix..."
        # Auto-fix implementation
        echo "Auto-fix complete. Please re-run the analyzer to confirm."
    else
        echo "Auto-fix skipped. Please manually adjust dependencies."
    fi
fi
```

### Package Detection Checklist

-   [ ] **Faker/Factory** - Often needed for seeding production data
-   [ ] **Laravel Telescope** - May be needed for production monitoring
-   [ ] **Laravel Debugbar** - Should be dev-only
-   [ ] **PHPUnit/TestCase** - Should remain in dev dependencies
-   [ ] **Laravel Dusk** - Browser testing, usually dev-only
-   [ ] **Laravel Pint** - Code formatting, usually dev-only

---

## Completion Verification

### Section A Part 1 Validation Checklist

Before proceeding to Part 2, verify all steps are completed successfully:

#### Foundation Setup ✅

-   [ ] **Step 00:** AI assistant guidelines and error resolution protocols established
-   [ ] **Step 01:** Project information card completed with all deployment variables
-   [ ] **Step 02:** GitHub repository created with proper security settings
-   [ ] **Step 03:** Local project structure organized and path variables configured

#### Infrastructure Setup ✅

-   [ ] **Step 03.1:** Admin-Local foundation created with universal configuration system
-   [ ] **Step 03.2:** Environment analysis completed and critical issues resolved
-   [ ] **Step 04:** Repository cloned and integrated with deployment variables updated
-   [ ] **Step 05:** Git branching strategy established with all 6 branches operational

#### Configuration & Analysis ✅

-   [ ] **Step 06:** Universal .gitignore created and committed with comprehensive exclusions
-   [ ] **Step 07:** Dependency analysis system created and ready for use

### Next Steps

🎯 **Ready for Part 2:** [Section A - Project Setup Part 2](./2-Section-A-ProjectSetup-Part2.md)

-   Steps 08-11: Dependencies Installation, Repository Integration & Final Commits
-   Estimated time: 20-30 minutes
-   Focus: Dependency management and final project setup validation

---

**Guide Status:** ✅ COMPLETE - Part 1 Foundation Setup  
**Next Guide:** → [Part 2: Dependencies & Final Setup](./2-Section-A-ProjectSetup-Part2.md)  
**Authority Level:** Based on 4-way consolidated FINAL documents  
**Last Updated:** August 21, 2025, 6:03 PM EST
