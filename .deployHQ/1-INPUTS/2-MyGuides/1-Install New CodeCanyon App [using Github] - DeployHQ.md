### FIRST TIME DEPLOYMENT

1. **STEP 1 - COLLECT INFO**
    
    <aside>
    ✅
    
    COLLECT INFO
    
    - Copy and complete this template before starting:
    
    ```bash
    # PROJECT DETAILS
    PROJECT_NAME="Test-Laravel-React-DeployHQ"
    GITHUB_REPO_SSH="git@github.com:rovony/Test-laravel-React-DeployHQ.git"
    GITHUB_REPO_HTTPS="https://github.com/rovony/Test-laravel-React-DeployHQ.git"
    LOCAL_PROJECT_PATH="/Users/malekokour/Zaj_Master/MyTools/My-Tools-Scripts/Laravel-Tools/DeployHQ-Master/2-Test-DeployHQ-Laravel-React"
    App_Root_Relative_Path="/laravel-react-app"
    
    # SERVER DETAILS
    DOMAIN="deployhqtest.zajaly.com"
    SERVER_IP="93.127.221.221"
    SSH_PORT="65002"  # 22 for cPanel, 65002 for Hostinger
    SSH_USERNAME="u164914061"
    SSH_PASSWORD="password-here"
    
    # HOSTING ENVIRONMENT
    HOSTING_TYPE="hostinger"  # hostinger or cpanel
    DOMAIN_PATH="/home/u164914061/domains/deployhqtest.zajaly.com"
    DEPLOYMENT_PATH="/home/u164914061/domains/deployhqtest.zajaly.com/deploy"
    
    # DATABASE DETAILS
    DB_HOST="localhost"
    DB_PORT="3306"
    DB_NAME="u164914061_test_v1"
    DB_USERNAME="u164914061_test_zaj_1"
    DB_PASSWORD="?dnqD9tR4B"
    
    # SSH KEY (for DeployHQ)
    SSH_KEY_NAME="DeployHQ-Test-Laravel-React"
    SSH_PUBLIC_KEY="ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIBzKjAqNPx0IItk2n6IS1ivKCn0dALGS62JdZrrA5CH8 mac_default_key_20250610"
    ```
    
    </aside>
    
2. STEP 2- Confirm All local dev steps done correctly
    - **Step 1: Set Up Admin-Local Development Structure**
        1. **Open your Laravel project in VSCode:**
            - **File → Open Folder** → Select your Laravel project directory
            - Or drag the project folder into VSCode
        2. **Verify Laravel application structure:**
            - In VSCode Explorer panel, ensure these key files exist:
                - `artisan`
                - `composer.json`
                - `package.json`
                - `.env.example`
        3. **Create Admin-Local structure using VSCode:**
            - **Open VSCode Terminal:** `Ctrl+Shift+\`` (backtick)
            - **Run Admin-Local setup:**
            
            ```bash
            # Create comprehensive Admin-Local structure
            mkdir -p Admin-Local/{myCustomizations/{app,config,routes_custom,_vendor_replacements_,database/migrations_custom,public/assets_source/{css,js,images},resources/{views,lang_custom}},myDocs/{documentation_internal,AppDocs_User,AppDocs_Technical,AppDocs_SuperAdmin,source_assets_branding,project_scripts,vendor_downloads},backups_local/{database,files,releases},maintenance/{scripts,documentation}}
            
            # Create .gitkeep files to preserve empty directories
            find Admin-Local -type d -empty -exec touch {}/.gitkeep \\;
            
            # Create integration points in main project
            mkdir -p public/custom/{css,js,images}
            mkdir -p resources/custom/views
            mkdir -p config/custom
            mkdir -p app/Custom
            find public/custom app/Custom resources/custom config/custom -type d -empty -exec touch {}/.gitkeep \\;
            
            ```
            
        4. **Create customization tracking system:**
            - **Create customizations tracker:**
            
            ```bash
            # Create comprehensive customizations tracker
            cat > Admin-Local/myDocs/documentation_internal/CUSTOMIZATIONS_Tracker.md << 'EOF'
            # Project Customizations Tracker
            
            ## Overview
            This document tracks all customizations made to the original vendor/upstream code.
            
            ## Customization Categories
            
            ### 1. Application Logic (app/)
            - **Controllers**: Custom controller modifications
            - **Models**: Database model customizations
            - **Services**: Custom business logic
            - **Middleware**: Custom middleware implementations
            
            ### 2. Configuration (config/)
            - **Environment**: Custom environment variables
            - **Services**: Service provider modifications
            - **Database**: Custom database configurations
            
            ### 3. Database (database/)
            - **Migrations**: Custom database schema changes
            - **Seeders**: Custom data seeders
            
            ### 4. Frontend (resources/, public/)
            - **Views**: Template customizations
            - **Assets**: Custom CSS, JavaScript, images
            - **Language**: Custom translations
            
            ### 5. Routes (routes/)
            - **Web Routes**: Custom web routes
            - **API Routes**: Custom API endpoints
            
            ## Current Customizations
            
            (Add your customizations here as you create them)
            
            ---
            **Last Updated**: $(date)
            **Project**: $(basename "$(pwd)")
            EOF
            
            ```
            
        5. **Create local development startup script:**
            
            ```bash
            # Create startup script for local development
            cat > Admin-Local/maintenance/scripts/start-local-development.sh << 'EOF'
            #!/bin/bash
            
            PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
            ADMIN_LOCAL_ROOT="$PROJECT_ROOT/Admin-Local"
            
            echo "=== Starting Local Development Environment ==="
            
            # Ensure we're on development branch
            cd "$PROJECT_ROOT"
            CURRENT_BRANCH=$(git branch --show-current)
            if [ "$CURRENT_BRANCH" != "development" ]; then
                echo "Switching to development branch..."
                git checkout development
                git pull origin development
            fi
            
            # Check if .env exists
            if [ ! -f ".env" ]; then
                echo "Creating .env from template..."
                cp .env.example .env
                php artisan key:generate --no-interaction
            fi
            
            # Install dependencies
            composer install --no-interaction
            npm install
            
            # Laravel setup
            php artisan migrate --no-interaction
            php artisan storage:link
            
            # Deploy customizations
            if [ -f "$ADMIN_LOCAL_ROOT/maintenance/scripts/deploy-customizations.sh" ]; then
                "$ADMIN_LOCAL_ROOT/maintenance/scripts/deploy-customizations.sh"
            fi
            
            # Build assets
            npm run dev
            
            echo "🚀 Local development environment ready!"
            EOF
            
            chmod +x Admin-Local/maintenance/scripts/start-local-development.sh
            
            ```
            
        6. **Create proper `.gitignore` and `.deployignore`:**
            - **Right-click in Explorer** → **New File** → Name it `.gitignore`
            - **Copy and paste** this content:
            
            ```
            /node_modules
            /public/hot
            /public/storage
            /storage/*.key
            /vendor
            .env
            .env.backup
            .phpunit.result.cache
            docker-compose.override.yml
            Homestead.json
            Homestead.yaml
            npm-debug.log
            yarn-error.log
            /.idea
            /.vscode
            
            ```
            
            - **Create `.deployignore` file (CRITICAL):**
            
            ```
            # Exclude version control directories
            .git/
            .gitignore
            .deployignore
            
            # Exclude local environment files
            .env
            .env.*
            
            # Exclude documentation and tests
            README.md
            /docs/
            /tests/
            
            # Exclude IDE and system files
            .DS_Store
            Thumbs.db
            .vscode/
            .idea/
            
            # Exclude Admin-Local development files
            Admin-Local/
            
            ```
            
        7. **Prepare environment template:**
            - **Copy `.env` to `.env.example`:**
                - Right-click `.env` → **Copy**
                - Right-click in Explorer → **Paste**
                - Rename the copy to `.env.example`
            - **Edit `.env.example`** to remove sensitive data:
                - Open `.env.example` in VSCode
                - Replace actual passwords with placeholders:
                
                ```
                DB_PASSWORD=your-database-password
                APP_KEY=
                
                ```
                
    - **Step 2: Initialize Git Repository and Push to GitHub**
        1. **Initialize Git repository using VSCode:**
            - **Open Command Palette:** `Ctrl+Shift+P` (Windows/Linux) or `Cmd+Shift+P` (Mac)
            - **Type:** `Git: Initialize Repository`
            - **Select your project folder** when prompted
            - **Verify:** You should see Git icon in the sidebar and files in Source Control panel
        2. **Stage and commit files using VSCode:**
            - **Click Source Control icon** in sidebar (or `Ctrl+Shift+G`)
            - **Review changes** in the Source Control panel
            - **Stage all files:** Click `+` next to "Changes" or stage individual files
            - **Write commit message:** "Initial commit: Laravel deployment setup"
            - **Commit:** Click `✓` checkmark or `Ctrl+Enter`
        3. **Create GitHub repository:**
            - Go to [**GitHub.com**](http://github.com/)
            - Create new **private** repository
            - **Repository name:** Your project name
            - **Do not initialize** with README (we already have code)
            - **Copy the repository URL** (SSH or HTTPS)
        4. **Connect to GitHub using VSCode:**
            - **Open Command Palette:** `Ctrl+Shift+P`
            - **Type:** `Git: Add Remote`
            - **Name:** `origin`
            - **URL:** Paste your GitHub repository URL
            - **Push to GitHub:** Click `...` in Source Control → `Push` → `Push to...` → `origin/main`
        5. **Create additional branches using VSCode (CodeCanyon workflow):**
            - **Open Command Palette:** `Ctrl+Shift+P`
            - **Type:** `Git: Create Branch`
            
            **Create branches in this order:**
            
            **a) Create `original` branch:**
            
            - **Branch name:** `original`
            - **Based on:** `main`
            - **Push:** `...` → `Push` → `Push to...` → `origin/original`
            
            **b) Create `custom` branch:**
            
            - **Switch to main:** Click branch name in status bar → `main`
            - **Create branch:** `Git: Create Branch` → `custom` → based on `main`
            - **Push:** `...` → `Push` → `Push to...` → `origin/custom`
            
            **c) Create `staging` branch:**
            
            - **Switch to custom:** Click branch name → `custom`
            - **Create branch:** `Git: Create Branch` → `staging` → based on `custom`
            - **Push:** `...` → `Push` → `Push to...` → `origin/staging`
            
            **d) Create `dev` branch:**
            
            - **Stay on custom branch**
            - **Create branch:** `Git: Create Branch` → `dev` → based on `custom`
            - **Push:** `...` → `Push` → `Push to...` → `origin/dev`
            
            **Branch Structure:**
            
            - **`original`** – Tracks the **original vendor code** (for merging official updates)
            - **`custom`** – Holds your **customized code** (original + modifications)
            - **`staging`** – For code deployed to the **staging environment**
            - **`main`** – For code deployed to **production**
            - **`dev`** – (Optional) for active **development** or feature testing
    
    ---
    
3. **One-Time Server & DeployHQ Setup**
    - **Step 3: Prepare Server Environment [Create `deploy` folder]**
        - **Option A: Using VSCode Remote SSH Extension (RECOMMENDED)**
            1. **Install Remote SSH Extension:**
                - **Open VSCode Extensions:** `Ctrl+Shift+X`
                - **Search:** "Remote - SSH"
                - **Install:** Microsoft's "Remote - SSH" extension
            2. **Connect to server using VSCode:**
                - **Open Command Palette:** `Ctrl+Shift+P`
                - **Type:** `Remote-SSH: Connect to Host...`
                - **Add New SSH Host:** Click "Add New SSH Host..."
                - **Enter SSH command:**
                    
                    ```
                    # For Hostinger:
                    ssh u164914061@deployhqtest.zajaly.com -p 65002
                    
                    # For cPanel:
                    ssh username@yourdomain.com -p 22
                    
                    ```
                    
                - **Select config file:** Choose your SSH config file (usually first option)
                - **Connect:** Click "Connect" when the host appears in the list
            3. **Navigate to domain directory using VSCode:**
                - **Open Terminal in VSCode:** `Ctrl+Shift+`` (backtick)
                - **Navigate to domain directory:**
                    
                    ```bash
                    cd /home/u164914061/domains/deployhqtest.zajaly.com
                    pwd# Verify you're in the right location
                    ```
                    
                - **Open folder in VSCode:** `File → Open Folder` → Select the domain directory
            4. **Create deployment directory using VSCode:**
                - **Right-click in Explorer** → **New Folder** → Name it `deploy`
                - **Or use integrated terminal:**
                    
                    ```bash
                    mkdir -p deploy
                    ls -la deploy# Verify it was created
                    ```
                    
            5. **Clean up existing files (Hostinger only):**
                - **Check existing files** in VSCode Explorer
                - **If `public_html` exists as a folder** (not symlink):
                    - **Right-click `public_html`** → **Rename** → `public_html.backup.YYYYMMDD`
                    - **Or use terminal:**
                    
                    ```bash
                    mv public_html public_html.backup.$(date +%Y%m%d-%H%M%S)
                    ```
                    
        - **Option B: Traditional SSH (Alternative)**
            1. **SSH into your server:**
                
                ```bash
                # For Hostinger:ssh u164914061@deployhqtest.zajaly.com -p 65002
                
                # For cPanel:ssh username@yourdomain.com -p 22
                
                ```
                
            2. **Navigate and create directories:**
                
                ```bash
                cd /home/u164914061/domains/deployhqtest.zajaly.com
                mkdir -p deploy
                ls -la deploy
                
                ```
                
        - **Option C: Manual on Hosting File Manager**
            1. Open the hosting → website → File manager 
            2. go to the website domain root (ex: home/u164914061/domains/deployhqtest.zajaly.com)
            3. Create folder named `deploy`
    - **Step 4: Configure SSH Keys**
        1. **Add your SSH public key to the server:**
            
            **For Hostinger:**
            
            - Go to hPanel → Websites → Manage → Advanced → SSH Access
            - Click "Add SSH Key"
            - Name: `DeployHQ-Laravel-Project`
            - Paste your public key
            - Click "Add"
            
            **For cPanel:**
            
            - Go to cPanel → SSH Access → Manage SSH Keys
            - Click "Import Key"
            - Name: `DeployHQ-Laravel-Project`
            - Paste public key in "Public Key" field
            - Click "Import"
            - Click "Manage" → "Authorize"
        2. **Test SSH key authentication:**
            
            ```bash
            # Test from your local machinessh -i ~/.ssh/id_ed25519 u164914061@deployhqtest.zajaly.com -p 65002
            
            ```
            
    - **Step 4.1: Allow DeployHQ's IP ranges In Hostinger**
        1. Allow DeployHQ's IP ranges In Hostinger
            - 1.4. Allow DeployHQ's IP ranges In Hostinger
                
                <aside>
                🟧
                
                DO the below steps for both [`custojo.com](http://custojo.com)` then `staging.custojo.com`
                
                </aside>
                
                ---
                
                <aside>
                🟧
                
                Note: we got the below DeployHQ IP addresses when we tries to create a server.
                
                </aside>
                
                ## **Step 1: Access the IP Manager**
                
                1. Log in to your `Hostinger` hPanel.
                2. We need to repeat the steps below for both Staging and Production
                3. Navigate to **`Websites`** → `custojo.com` → **Manage** for the specific website.
                4. In the sidebar, search for and click on **`IP Manager`**.
                
                ## **Step 2: Allow DeployHQ IPs**
                
                1. In the "`Allow an IP Address`" section:
                    - Enter each DeployHQ IP range individually:
                        - **`185.22.211.30`**
                        - **`185.22.211.31`**
                        - **`152.89.76.109`**
                    - Add a note for each IP (e.g., "`DeployHQ Access`").
                    - Click **Add** after entering each IP.
                
                ## **Step 3: Repeat for Staging**
                
                1. Go to staging.custojo.com and repeat the above steps
                
                ---
                
                <aside>
                🟧
                
                ## **Important Notes:**
                
                - Hostinger's IP Manager does not support adding entire ranges (e.g., **`185.22.211.*`**). If you need to allow a broader range, consider using **`.htaccess`** rules instead.
                - For advanced configurations or if you encounter issues, contact Hostinger support for assistance.
                </aside>
                
    - **Step 5: Set Up DeployHQ Project**
        - **1-CREATE PROJECT Create new project in DeployHQ:**
            - Project name: `Laravel React Project Template`
            - Repository: Connect to GitHub
            - Select your repository
            - Zone: `US East` (or closest to your server)
            - SSH Connection: `ED25519` (recommended) - Generate new Key
        - **2A- Setup Production Server-Configure server in DeployHQ:** `Production` server
            1. Server Details
                - Server name: `Production Server`
                - Protocol: `SSH/SFTP`
                - Hostname: Your server IP
                - Port: `65002` (Hostinger) or `22` (cPanel)
                - Username: Your SSH username
                - ✅ Check "Use SSH key rather than password"
                - Copy the provided SSH public key
                - Add this key to your server (same process as Step 4)
            2. **Configure deployment settings:**
                - **Deployment Path (`domain-root/deploy` , start with `backslash`):** `/home/u164914061/domains/deployhqtest.zajaly.com/deploy`
                    - **keep unchecked ❌ :  Unlink existing file before uploading new version**
                    - **Check ✅ : Use turbo deployments (Compressed TAR-based deployments)**
                    - Check ✅ : Perform zero-downtime deployments on this server
                        - **Atomic strategy:** "`Copy previous release before uploading changes`"
                        - **Atomic retention:** `3 releases`
                - **Deployment Options**
                    - **Branch to deploy:** `main` for `production`, staging for `staging`
                    - **Environment:** `production`
                        - **Note:**
                            - **Standard Environments (as defined in our SSH Commands)**
                                - `development` - For local/dev work
                                - `testing` - For automated testing
                                - `staging` - For pre-production testing
                                - `production` - For live systems
                                - `integration` - For integration testing
                                - `qa` - For quality assurance
                - **Subdirectory to deploy from:** Leave blank (unless app is in subfolder for example if app in github is root, leave blank, if app is in folder, add relative path without backslack; example app is in `/laravel-react-app` , so the subdirectory to deploy is `laravel-react-app`)
            3. **Click: Create Server ✅**
        - **2A- Setup Staging Server-**  Repeat- **Configure server in DeployHQ: `staging`** server
            - create new website first, and dataset.
            - Deployment path: use staging for staging  ex: `/home/u164914061/domains/staging.zajaly.com/deploy`
            - **Branch to deploy:** `main` for `production`, staging for `staging`
            - **Environment**
                - `testing` - For automated testing
                - `staging` - For pre-production testing
    - **Setup `Config`- Create Shared Environment File (`.env`) - per Environment**
        - When creating your **`.env`** config file in DeployHQ:
            1. Navigate to **Settings** → **Config Files**
            2. Click **"New Config File"**
            3. **File Path**: Enter **`shared/.env`** (not `.env`, not **`deploy/shared/.env`**)
                1. select: plain text
                2. fill: using one of these templates- ensure to update per project details
                    - Template - Production -.env
                        
                        ```
                        
                        ```
                        
                    - Template - staging -.env
                        
                        ```
                        
                        ```
                        
                    - Template - test -.env
                        
                        ```
                        
                        ```
                        
            4. Target: check yes for ✅
                1. Use this file with the Build Pipeline?
                2. Upload this file to all current and future servers or server groups?
            5. Save
    - **Step 6: Setup `Excluded` Files in DeployHQ or using  - DONE USING `.deployignore`**
        - **FINAL EXCLUSION LIST (or .deployignore)**
            - **Always Excluded (Don't Deploy):**
                
                ```bash
                # Development & Local Files
                .git/
                .gitignore
                .deployignore
                README.md
                /docs/
                /tests/
                .DS_Store
                .vscode/
                .idea/
                Admin-Local/
                .env.local
                .env.testing
                ```
                
            - Shared Directories (Symlinked, Not Synchronized)
                
                ```bash
                # Shared Directories (Symlinked, Not Synchronized)
                .env
                .env.*
                /storage/
                /bootstrap/cache/
                /public/.well-known/
                ```
                
            - User Content Patterns (Shared) (keep in mind above **re-Populated Content-And-FIRST-DEPLOYMENT.**
                
                ```bash
                # User Content Patterns (Shared)
                /public/*upload*/
                /public/*avatar*/
                /public/*media*/
                /public/qr*/
                ```
                
    - Step 7 - Setup SSH Commands - tbd
        
        
    - Setup shared folders- cant be done via DeployHQ UI - we did it using SSH Commands
    - Step `Build` pipeline - tbd
    - Deploy
        - define commits (initial and end)
        - Schedule deployment: Start immediately
        - For 🟢 Full Pre-Built Strategy `[use this if we want to push all from local (CodeCanyon Apps]`
            - **`Check`:** Copy config files to server
                
                *Deploy essential environment configs*
                
            - **`Uncheck`:** Run build commands
                
                *Everything (including dependencies) is pre-built*
                
            - **`Uncheck`:** Use build cache
                
                *No build steps to cache*
                
        
    
    ---
    
    <aside>
    ✅
    
    DeployHQ Template saved
    
    - **Laravel - React - Full PreBuilt - No Built- V1 (Aug 1, 25)**
        - or after if v2 or 3 or more
    </aside>
    
    Guide: For all Strategies
    
    - Click: Show Advanced Options
        - For 🟢 Standard Pre-Built Strategy
            - **Check:** Copy config files to server
                
                *Deploy essential environment configs*
                
            - **Uncheck:** Run build commands
                
                *Assets are pre-built in repo; no need to build on server*
                
            - **Uncheck:** Use build cache
                
                *No build process required*
                
        - For 🟢 Full Pre-Built Strategy `[use this if we want to push all from local (CodeCanyon Apps]`
            - **Check:** Copy config files to server
                
                *Deploy essential environment configs*
                
            - **Uncheck:** Run build commands
                
                *Everything (including dependencies) is pre-built*
                
            - **Uncheck:** Use build cache
                
                *No build steps to cache*
                
        - For 🔴 Build-On-Server Strategy
            - **Check:** Copy config files to server
                
                *Need environment configs on server*
                
            - **Check:** Run build commands
                
                *Dependencies/assets not in repo; must build on server*
                
            - **Check:** Use build cache
                
                *Speeds up future deployments*
                
        - For 🟡 Hybrid Strategy
            - **Check:** Copy config files to server
                
                *Deploy essential environment configs*
                
            - **Check:** Run build commands
                
                *Some assets built on server (not all in repo)*
                
            - **Check:** Use build cache
                
                *Improves performance when building parts on server*
