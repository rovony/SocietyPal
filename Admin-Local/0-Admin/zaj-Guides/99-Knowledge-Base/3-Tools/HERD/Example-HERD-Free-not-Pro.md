# Local Dev Setup [HERD]-example

Below is example of setting up using HERD

### PHASE 2: Setup local dev (using `dev` branch) - Using `Herd`, `MySQL`, `HeidiSQL` or `TablePlus`

- **Step 1: Configure `Herd` for Your Project**
    1. **Start Herd and Configure Paths**:
        - Open `Herd` → Click `Settings` → Navigate to the `Paths` tab
        - Add the parent folder of your project to the paths:
            
            ```bash
            /Users/malekokour/My_Projects/PHP/Custojo/workdo_Dash/setup_Feb25/AppParent
            ```
            
            > 🟧 IMPORTANT: Don't include the AppRoot folder in the path. The path should end with the parent folder (AppParent), not the actual project folder (CustojoRoot).
            > 
    2. **Configure PHP Version**:
        - In Herd Settings, go to the `PHP` tab
        - Select PHP 8.2 (or the version required by your application)
        - Increase memory limit if needed (recommended: 512 M or higher)
        - Click `Save` and close the settings
    3. **Access Your Site**:
        - In Herd, click on the `Sites` tab
        - You should see your site listed (e.g., `CustojoRoot.test`)
        - Click on the URL to open the application in your browser
- **Step 2: Set Up MySQL and Database Connection**
    1. **Open the `AppRoot` Folder in `VSCode`** and in the terminal, run the below
    2. **Verify MySQL Installation and Start Service**:
        - For Mac users with Homebrew: (`zsh`)
            
            ```bash
            # Check if MySQL is installed via Homebrew
            brew list | grep mysql
            # mysql@8.4
            
            # Check if MySQL service is running
            brew services list | grep mysql
            # mysql@8.4     none
            
            # If MySQL is not running, start it
            # brew services start mysql
            
            ---
            Troubleshooting
            
            # Find MySQL binary location
            which mysql
            
            # If MySQL is not in PATH, find it
            find /usr/local -name mysql -type f 2>/dev/null
            find /opt -name mysql -type f 2>/dev/null
            
            # For MySQL installed via DMG, check standard location
            ls -la /usr/local/mysql/bin/mysql
            
            # Connect to MySQL (use the appropriate path based on your installation)
            # If MySQL is in your PATH:
            mysql -u root -p
            
            # If MySQL is installed via Homebrew but not in PATH:
            /usr/local/opt/mysql/bin/mysql -u root -p
            
            # If MySQL is installed via DMG:
            /usr/local/mysql/bin/mysql -u root -p
            
            ```
            
        - For Windows users: (or Mac `bash`)
            
            ```bash
            # Check if MySQL service is running
            sc query mysql
            
            # Start MySQL service if not running
            net start mysql
            
            ```
            
    3. **Configure Database Connection**:
        - Open your project in VSCode
        - Copy the example environment file if you haven't already:
            
            ```bash
            cp .env.example .env
            
            ```
            
        - Update the database settings in `.env`:
            
            ```bash
            DB_CONNECTION=mysql
            DB_HOST=127.0.0.1
            DB_PORT=3306
            DB_DATABASE=custojo_app #this changes per app
            DB_USERNAME=root #this is always root
            DB_PASSWORD=zajdemo123 #this is the root password
            
            ```
            
    4. **Create Database (`optional`, it will be done `automatically` below when we push `migrations`)**:
        - Connect to `MySQL` and create the database:
            
            ```bash
            mysql -u root -p
            
            ```
            
- **Step 3: Confirm Requirements & File Permissions**
    - Confirm Requirements [PHP, Node, Extensions, etc]
    1. **Check the script Docs requirements**:
        - For Example, for `Workdo Dash`, the requirements are in the [docs](https://workdo.io/documents/documentation-for-set-up/) they say:
            - **PHP >= 8.2**
            - —-
            - **`BCMath` PHP Extension**
            - **Ctype PHP Extension**
            - **Fileinfo PHP extension**
            - **JSON PHP Extension**
            - **Mbstring PHP Extension**
            - **OpenSSL PHP Extension**
            - **PDO PHP Extension**
            - **Tokenizer PHP Extension**
            - **XML PHP Extension**
    2. **PHP Versions**:
        1. Ensure what is right PHP version to use (check `script Docs`)
        2. **Install PHP needed in Herd**: (Herd → Settings → PHP)
            - Click the "`Install`" button next to PHP 8.2 for example
            - Wait for the installation to complete
        3. To Switch between PHP versions (assuming they are installed in Herd), you can do that by:
            - There are two ways to switch between PHP versions in Laravel Herd:
                1. **Using the Herd GUI**:
                    - Click on the Herd icon in the menu bar
                    - Select the desired PHP version from the dropdown menu
                        
                        ![image.png](attachment:f10a9ddf-13c0-451a-9bdf-ed10b53cb38f:image.png)
                        
                2. **Using the CLI**: Run the following command (bash)
                    
                    ```bash
                    herd use 8.2
                    # Replace "8.2" with the version you want to use
                    
                    ```
                    
            - Remember that Herd uses the global PHP version for all sites that are not isolated. If you need to set different PHP versions for specific sites, you can use the isolate function
            - You can configure the PHP version per site in the [**Site Manager**](https://herd.laravel.com/docs/macos/sites/managing-sites). This gives you a list of all your sites and allows you to configure the PHP version that each site uses.
                
                ![image.png](attachment:a4a80fcf-c442-4bec-95e8-90c9cb9f011b:image.png)
                
    3. **PHP Extensions**:
        1. **Check Required PHP Extensions**:
            - For Laravel/PHP projects, verify that all required extensions are installed and enabled
            - For Mac users with Herd, most extensions are pre-installed with PHP
            - To check installed extensions, run: then compare vs the script list from the script docs
                
                ```bash
                # Basic check of installed PHP extensions
                herd php -m
                
                # For alphabetically sorted list (recommended for easy verification)
                herd php -m | grep -v "^\\[" | tr '[:upper:]' '[:lower:]' | sort | uniq
                
                ```
                
                - then compare vs the `script extension list` from the `script docs`
                - for Workdo Dash: all extensions are already installed, no need to install any new extension.
        2. **Install Missing Extensions in Herd**: [**`ONLY`** IF any extension is needed and not part of the installed list of extensions]
            - **Steps: Install Missing Extensions**
                - Most common extensions are already bundled with Herd's PHP installations
                - If you need to install additional extensions that are not included out of the box:
                    1. **Using Homebrew and PECL (for Mac)**:
                        
                        ```bash
                        # STEP 1: Check if PHP is already installed via Homebrew
                        brew list | grep php
                        # If this returns a PHP version (like php@8.2), then PHP is already installed
                        
                        # If not installed, then install PHP via Homebrew (only needed for compiling extensions)
                        brew install php
                        
                        # STEP 2: Check if PECL is installed and working
                        which pecl
                        # This should return the path to the PECL executable if installed
                        
                        # STEP 3: Check which extensions are already installed via PECL
                        pecl list
                        
                        # STEP 4: Check if a specific extension is already available in Herd's PHP
                        herd php -m | grep -i "extension_name"
                        
                        # STEP 5: Install the extension via PECL (only if not already installed)
                        pecl install [extension-name]
                        
                        # STEP 6: Activate the extension by editing the php.ini file
                        # The file is located at:
                        # ~/Library/Application Support/Herd/config/php/<version>/php.ini
                        
                        # Add this line to php.ini (for M1/M2 Mac):
                        # extension=/opt/homebrew/lib/php/pecl/20220829/[extension].so
                        
                        # Or for Intel Mac:
                        # extension=/usr/local/lib/php/pecl/[extension].so
                        
                        # STEP 7: Restart Herd after making changes
                        
                        ```
                        
                    2. **For Windows users**:
                        - Edit your php.ini file to uncomment or add extension lines
                        - Restart your web server after making changes
        3. **Verify Extensions are Working**:
            
            ```bash
            # Create a test PHP file directly in the public directory
            echo "<?php phpinfo(); ?>" > public/phpinfo.php
            
            # Set proper permissions
            chmod 644 public/phpinfo.php
            
            # Access it through your browser at
            # http://yourproject.test/phpinfo.php
            # example: custojoroot.test/phpinfo.php
            ```
            
            - In Laravel projects, publicly accessible files should be in the `public` directory, not the project root.
            - Note: If your application is in `installation` (`/install`) mode, you may need to complete the `installation` process `before` you can access `phpinfo.php`. The installation wizard may intercept all requests until installation is complete.
    4. **Node.js Requirements**:
        1. **Check Node.js Installation**:
            
            ```bash
            # Check if Node.js is installed
            node -v
            
            # Check npm version
            npm -v
            
            ```
            
        2. **Install Node.js (`ONLY` if Needed)**
            - For Mac users:
                
                ```bash
                # Using Homebrew
                brew install node
                
                # Or using NVM (Node Version Manager) for better version control
                curl -o- <https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh> | bash
                nvm install 18  # Install Node.js 18 LTS or another version
                
                ```
                
            - For Windows users:
                - Download and install from [nodejs.org](https://nodejs.org/)
                - Or use NVM for Windows
- **Step 4: Confirm File and Folders Permissions**
    1. **Set Standard Permissions First**:
        
        ```bash
        # Set standard permissions for all folders and subfolders
        find . -type d -exec chmod 755 {} \\;
        
        # Set standard permissions for all files
        find . -type f -exec chmod 644 {} \\;
        
        # Secure the environment file
        chmod 600 .env
        
        ```
        
        > 🟧 IMPORTANT: These are the standard secure permissions for web applications:
        > 
        > - All Folders (and recursive subfolders): `755`
        > - All Files (and recursive subfiles): `644`
        > - `.env` file: `600`
    2. **Check Script Documentation for Installation Requirements**:
        
        > 🟧 IMPORTANT: Always check the script's documentation for specific permission requirements during installation. Some scripts require more permissive settings temporarily.
        > 
        
        ```bash
        # Example from documentation for WorkDo Dash:
        # "storage directory should be writable (chmod 775 or 777 during installation)"
        
        ```
        
    3. **Check Current Permissions**:
        
        ```bash
        # List permissions of key directories
        ls -la
        ls -la storage/
        ls -la bootstrap/
        
        ```
        
    4. **Set Installation Permissions** (based on script documentation):
        1. based on workdo dash :
            1. we need these as `777`:
            - storage/
            - storage/app
            - storage/framework/
            - storage/logs/
            - bootstrap/cache/
            - public
        
        ```bash
        # Example for Laravel projects during installation
        # (Use the specific values mentioned in your script's documentation)
        chmod -R 777 storage
        chmod -R 777 storage/app
        chmod -R 777 storage/framework
        chmod -R 777 storage/logs
        chmod -R 777 bootstrap/cache
        chmod -R 777 public
        
        ```
        
    5. **After Installation - Revert to Secure Permissions**:
        
        > 🟧 IMPORTANT: After installation is complete, revert any 777 permissions to more secure 755/775 permissions.
        > 
        
        ```bash
        # Revert to more secure permissions after installation
        chmod -R 775 storage
        chmod -R 775 bootstrap/cache
        
        # Verify the standard permissions are still in place
        find . -type d -exec chmod 755 {} \\;
        find . -type f -exec chmod 644 {} \\;
        chmod 600 .env
        
        ```
        
    6. **Verify Final Permissions**:
        
        ```bash
        # Verify storage directory permissions
        ls -la storage/
        
        # Verify bootstrap/cache permissions
        ls -la bootstrap/
        
        # Verify .env file permissions
        ls -la .env
        
        ```
        
- **Step 5: add details to `.env` and create the `Database`**
    1. update .env to include database details 
        
        ```bash
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=custojo_local
        DB_USERNAME=root
        DB_PASSWORD=zajdemo123
        ```
        
    2. **Create Database (if needed)**:
        - Connect to MySQL and create the database:
            
            ```bash
            /usr/local/mysql/bin/mysql -u root -p
            #enter password: zajdemo123
            ```
            
        - After entering your password, run these SQL commands:
            
            ```sql
            CREATE DATABASE custojo_local;
            GRANT ALL PRIVILEGES ON custojo_local.* TO 'root'@'localhost';
            FLUSH PRIVILEGES;
            EXIT;
            ```
            
    3. **Verify database connection**:
        
        ```bash
        /usr/local/mysql/bin/mysql -u root -p -e "SHOW DATABASES;"
        
        ```
        
        This should show a list of migrations and their status. If you see an error about connecting to the database, double-check your .env settings and make sure MySQL is running.
        
        ---
        
        `next steps may be replaced with the /install` 
        
        ---
        
    4. **Run migrations to set up database tables**:
        
        ```bash
        php artisan migrate
        
        ```
        
    5. **Seed the database with initial data (if applicable)**:
        
        ```bash
        php artisan db:seed
        
        ```
        
- **Step 4 : Start the install process (go to `website.com/install`)**
    
    and for the database setp, fill the below 
    
    ```bash
    # App Name
    Custojo
    
    #App Url # the Herd url 
    http://custojoroot.test
    
    # Database Connection
    #mysql
    
    #Database Host
    127.0.0.1
    
    #Database Port
    3306
    
    #Database Name
    
    # Database User Name
    
    # Database Password
    
    ```
    
- **Step 5: (`SCRIPT`) Install Dependencies and Set Up Project [IF NEEDED `ONLY` as these codecanyon scripts comes usually built]**
    
    ### Automatic Project Setup Helper
    
    - `scripts/detect-commands/project-setup-helper.sh`
        
        # path: scripts/detect-commands/project-setup-helper.sh
        
        ```bash
        #!/bin/bash
        # path: scripts/detect-commands/project-setup-helper.sh
        
        # Text formatting
        BOLD="\033[1m"
        GREEN="\033[0;32m"
        BLUE="\033[0;34m"
        YELLOW="\033[0;33m"
        RED="\033[0;31m"
        CYAN="\033[0;36m"
        MAGENTA="\033[0;35m"
        NC="\033[0m" # No Color
        
        # Initialize Markdown report content
        MARKDOWN_REPORT="# Project Setup Report\n\n"
        REPORT_FILE="$(dirname "$0")/setup-report.md"
        TIMESTAMP=$(date "+%Y-%m-%d %H:%M:%S")
        
        # Add timestamp to report
        MARKDOWN_REPORT+="**Generated on:** $TIMESTAMP\n\n"
        
        # Function to append to Markdown report
        append_to_markdown() {
            MARKDOWN_REPORT+="$1\n"
        }
        
        # Function to print section headers
        print_header() {
            echo -e "\n${BOLD}${BLUE}$1${NC}"
            echo -e "${CYAN}$(printf '=%.0s' {1..50})${NC}"
        
            # Add to Markdown report
            append_to_markdown "\n## $1\n"
        }
        
        # Function to print command with explanation
        print_command() {
            local cmd="$1"
            local explanation="$2"
            local path="$3"
        
            if [ -n "$path" ]; then
                echo -e "${YELLOW}# Path: $path${NC}"
                append_to_markdown "**Path:** $path"
            fi
        
            echo -e "${GREEN}$cmd${NC}"
            append_to_markdown "\`\`\`bash\n$cmd\n\`\`\`"
        
            if [ -n "$explanation" ]; then
                echo -e "${MAGENTA}# $explanation${NC}"
                append_to_markdown "$explanation"
            fi
        
            echo ""
            append_to_markdown ""
        }
        
        # Function to get relative path
        get_relative_path() {
            local abs_path="$1"
            local rel_path="${abs_path#$PWD/}"
        
            # If the path is the same as PWD, return "."
            if [ "$abs_path" = "$PWD" ]; then
                echo "."
            # If the path starts with PWD, return the relative path
            elif [[ "$abs_path" == "$PWD"* ]]; then
                echo "./$rel_path"
            # Otherwise, return the absolute path
            else
                echo "$abs_path"
            fi
        }
        
        # Function to detect project root paths
        detect_paths() {
            # Initialize paths
            PROJECT_ROOT="$PWD"
            COMPOSER_PATH="$PROJECT_ROOT"
            NPM_PATH="$PROJECT_ROOT"
            ARTISAN_PATH="$PROJECT_ROOT"
        
            # Check for nested project structures
            if [ -d "app" ] && [ ! -f "artisan" ] && [ -d "app/public" ]; then
                # This might be a parent directory with the actual app in a subdirectory
                for dir in */; do
                    if [ -f "${dir}artisan" ]; then
                        ARTISAN_PATH="$PROJECT_ROOT/$dir"
                        echo -e "${YELLOW}Laravel project detected in subdirectory: $dir${NC}"
                    fi
                    if [ -f "${dir}composer.json" ]; then
                        COMPOSER_PATH="$PROJECT_ROOT/$dir"
                    fi
                    if [ -f "${dir}package.json" ]; then
                        NPM_PATH="$PROJECT_ROOT/$dir"
                    fi
                done
            fi
        
            # Check for frontend/backend separation
            if [ -d "frontend" ] && [ -d "backend" ]; then
                if [ -f "backend/artisan" ]; then
                    ARTISAN_PATH="$PROJECT_ROOT/backend"
                    echo -e "${YELLOW}Laravel backend detected in: backend/${NC}"
                fi
                if [ -f "backend/composer.json" ]; then
                    COMPOSER_PATH="$PROJECT_ROOT/backend"
                fi
                if [ -f "frontend/package.json" ]; then
                    NPM_PATH="$PROJECT_ROOT/frontend"
                    echo -e "${YELLOW}Frontend project detected in: frontend/${NC}"
                fi
            fi
        
            # Check for client/server separation
            if [ -d "client" ] && [ -d "server" ]; then
                if [ -f "server/artisan" ]; then
                    ARTISAN_PATH="$PROJECT_ROOT/server"
                    echo -e "${YELLOW}Laravel server detected in: server/${NC}"
                fi
                if [ -f "server/composer.json" ]; then
                    COMPOSER_PATH="$PROJECT_ROOT/server"
                fi
                if [ -f "client/package.json" ]; then
                    NPM_PATH="$PROJECT_ROOT/client"
                    echo -e "${YELLOW}Client project detected in: client/${NC}"
                fi
            fi
        }
        
        # Function to get a more descriptive project name
        get_project_name() {
            local project_name=""
            local project_type=""
        
            # Try to get name from package.json
            if [ -f "$NPM_PATH/package.json" ]; then
                # Try to get name from package.json
                local npm_name=$(grep -o '"name": *"[^"]*"' "$NPM_PATH/package.json" | cut -d'"' -f4)
                if [ -n "$npm_name" ]; then
                    project_name="$npm_name"
                fi
        
                # Determine project type from dependencies
                if grep -q '"react"' "$NPM_PATH/package.json" || grep -q '"react-dom"' "$NPM_PATH/package.json"; then
                    project_type="React"
                elif grep -q '"vue"' "$NPM_PATH/package.json"; then
                    project_type="Vue.js"
                elif grep -q '"next"' "$NPM_PATH/package.json"; then
                    project_type="Next.js"
                elif grep -q '"nuxt"' "$NPM_PATH/package.json"; then
                    project_type="Nuxt.js"
                elif grep -q '"express"' "$NPM_PATH/package.json"; then
                    project_type="Express.js"
                elif grep -q '"@nestjs"' "$NPM_PATH/package.json"; then
                    project_type="NestJS"
                fi
            fi
        
            # Try to get name from composer.json
            if [ -f "$COMPOSER_PATH/composer.json" ]; then
                local composer_name=$(grep -o '"name": *"[^"]*"' "$COMPOSER_PATH/composer.json" | cut -d'"' -f4)
                if [ -n "$composer_name" ]; then
                    if [ -n "$project_name" ]; then
                        # If we already have a name from package.json, append this as the backend
                        project_name="$project_name (Backend: $composer_name)"
                    else
                        project_name="$composer_name"
                    fi
                fi
        
                # Check if it's a Laravel project
                if [ -f "$ARTISAN_PATH/artisan" ]; then
                    if [ -z "$project_type" ]; then
                        project_type="Laravel"
                    else
                        project_type="$project_type + Laravel"
                    fi
        
                    # Try to get Laravel version
                    if [ -f "$ARTISAN_PATH/composer.json" ]; then
                        LARAVEL_VERSION=$(grep -o '"laravel/framework": ".*"' "$ARTISAN_PATH/composer.json" | grep -o '[0-9]\+\.[0-9]\+' | head -1)
                        if [ -n "$LARAVEL_VERSION" ]; then
                            project_type="$project_type $LARAVEL_VERSION"
                        fi
                    fi
                fi
            fi
        
            # If we couldn't determine a name, use the directory name
            if [ -z "$project_name" ]; then
                project_name=$(basename "$PWD")
            fi
        
            # Combine name and type if both are available
            if [ -n "$project_type" ]; then
                echo "$project_name ($project_type)"
            else
                echo "$project_name"
            fi
        }
        
        print_header "🔍 Project Setup Helper"
        echo -e "This script will help you set up your project by detecting its type, structure, and providing the right commands with explanations."
        
        # Detect paths for different components
        detect_paths
        
        # Get a more descriptive project name
        PROJECT_NAME=$(get_project_name)
        echo -e "${BOLD}Project:${NC} $PROJECT_NAME"
        echo -e "${BOLD}Location:${NC} $PWD"
        
        # Add project information to Markdown report
        append_to_markdown "## Project Information\n"
        append_to_markdown "- **Project Name:** $PROJECT_NAME"
        append_to_markdown "- **Location:** $PWD\n"
        
        # Initialize detection variables
        IS_LARAVEL=false
        IS_REACT=false
        IS_VUE=false
        IS_NEXT=false
        IS_NUXT=false
        IS_EXPRESS=false
        IS_NEST=false
        HAS_COMPOSER=false
        HAS_NPM=false
        HAS_YARN=false
        HAS_PNPM=false
        HAS_VITE=false
        HAS_WEBPACK=false
        HAS_TAILWIND=false
        HAS_BOOTSTRAP=false
        HAS_TYPESCRIPT=false
        HAS_DOCKER=false
        HAS_LERNA=false
        HAS_MONOREPO=false
        
        print_header "Detecting Project Type and Features"
        append_to_markdown "## Detected Features\n"
        
        # Check for Laravel
        if [ -f "$ARTISAN_PATH/artisan" ]; then
            IS_LARAVEL=true
            echo -e "${GREEN}✓${NC} Laravel project detected"
            append_to_markdown "- ✅ Laravel project"
        
            # Check Laravel version
            if [ -f "$ARTISAN_PATH/composer.json" ]; then
                LARAVEL_VERSION=$(grep -o '"laravel/framework": ".*"' "$ARTISAN_PATH/composer.json" | grep -o '[0-9]\+\.[0-9]\+' | head -1)
                if [ -n "$LARAVEL_VERSION" ]; then
                    echo -e "${GREEN}✓${NC} Laravel version: $LARAVEL_VERSION"
                    append_to_markdown "- ✅ Laravel version: $LARAVEL_VERSION"
                fi
            fi
        fi
        
        # Check for Composer
        if [ -f "$COMPOSER_PATH/composer.json" ]; then
            HAS_COMPOSER=true
            echo -e "${GREEN}✓${NC} PHP/Composer project detected"
            append_to_markdown "- ✅ PHP/Composer project"
        
            # Check for specific PHP packages
            if grep -q '"symfony/' "$COMPOSER_PATH/composer.json"; then
                echo -e "${GREEN}✓${NC} Symfony components detected"
                append_to_markdown "- ✅ Symfony components"
            fi
        fi
        
        # Check for package managers and JS frameworks
        if [ -f "$NPM_PATH/package.json" ]; then
            HAS_NPM=true
            echo -e "${GREEN}✓${NC} JavaScript/Node.js project detected"
            append_to_markdown "- ✅ JavaScript/Node.js project"
        
            # Check for package manager lock files
            if [ -f "$NPM_PATH/yarn.lock" ]; then
                HAS_YARN=true
                echo -e "${GREEN}✓${NC} Yarn package manager detected"
                append_to_markdown "- ✅ Yarn package manager"
            elif [ -f "$NPM_PATH/pnpm-lock.yaml" ]; then
                HAS_PNPM=true
                echo -e "${GREEN}✓${NC} PNPM package manager detected"
                append_to_markdown "- ✅ PNPM package manager"
            else
                echo -e "${GREEN}✓${NC} NPM package manager detected"
                append_to_markdown "- ✅ NPM package manager"
            fi
        
            # Check for specific frameworks
            if grep -q '"react"' "$NPM_PATH/package.json" || grep -q '"react-dom"' "$NPM_PATH/package.json"; then
                IS_REACT=true
                echo -e "${GREEN}✓${NC} React framework detected"
                append_to_markdown "- ✅ React framework"
            fi
        
            if grep -q '"vue"' "$NPM_PATH/package.json"; then
                IS_VUE=true
                echo -e "${GREEN}✓${NC} Vue.js framework detected"
                append_to_markdown "- ✅ Vue.js framework"
            fi
        
            if grep -q '"next"' "$NPM_PATH/package.json"; then
                IS_NEXT=true
                echo -e "${GREEN}✓${NC} Next.js framework detected"
                append_to_markdown "- ✅ Next.js framework"
            fi
        
            if grep -q '"nuxt"' "$NPM_PATH/package.json"; then
                IS_NUXT=true
                echo -e "${GREEN}✓${NC} Nuxt.js framework detected"
                append_to_markdown "- ✅ Nuxt.js framework"
            fi
        
            if grep -q '"express"' "$NPM_PATH/package.json"; then
                IS_EXPRESS=true
                echo -e "${GREEN}✓${NC} Express.js framework detected"
                append_to_markdown "- ✅ Express.js framework"
            fi
        
            if grep -q '"@nestjs"' "$NPM_PATH/package.json"; then
                IS_NEST=true
                echo -e "${GREEN}✓${NC} NestJS framework detected"
                append_to_markdown "- ✅ NestJS framework"
            fi
        
            # Check for build tools
            if grep -q '"vite"' "$NPM_PATH/package.json"; then
                HAS_VITE=true
                echo -e "${GREEN}✓${NC} Vite build tool detected"
                append_to_markdown "- ✅ Vite build tool"
            fi
        
            if grep -q '"webpack"' "$NPM_PATH/package.json"; then
                HAS_WEBPACK=true
                echo -e "${GREEN}✓${NC} Webpack build tool detected"
                append_to_markdown "- ✅ Webpack build tool"
            fi
        
            # Check for CSS frameworks
            if grep -q '"tailwindcss"' "$NPM_PATH/package.json"; then
                HAS_TAILWIND=true
                echo -e "${GREEN}✓${NC} Tailwind CSS detected"
                append_to_markdown "- ✅ Tailwind CSS"
            fi
        
            if grep -q '"bootstrap"' "$NPM_PATH/package.json"; then
                HAS_BOOTSTRAP=true
                echo -e "${GREEN}✓${NC} Bootstrap detected"
                append_to_markdown "- ✅ Bootstrap"
            fi
        
            # Check for TypeScript
            if grep -q '"typescript"' "$NPM_PATH/package.json" || [ -f "$NPM_PATH/tsconfig.json" ]; then
                HAS_TYPESCRIPT=true
                echo -e "${GREEN}✓${NC} TypeScript detected"
                append_to_markdown "- ✅ TypeScript"
            fi
        
            # Check for monorepo setup
            if grep -q '"workspaces"' "$NPM_PATH/package.json" || [ -f "$NPM_PATH/lerna.json" ]; then
                HAS_MONOREPO=true
                echo -e "${GREEN}✓${NC} Monorepo structure detected"
                append_to_markdown "- ✅ Monorepo structure"
        
                if [ -f "$NPM_PATH/lerna.json" ]; then
                    HAS_LERNA=true
                    echo -e "${GREEN}✓${NC} Lerna monorepo tool detected"
                    append_to_markdown "- ✅ Lerna monorepo tool"
                fi
            fi
        else
            echo -e "${YELLOW}! No package.json found. Skipping JavaScript/Node.js detection.${NC}"
            append_to_markdown "- ⚠️ No package.json found. Skipping JavaScript/Node.js detection."
        fi
        
        # Check for Docker
        if [ -f "Dockerfile" ] || [ -f "docker-compose.yml" ] || [ -f "docker-compose.yaml" ]; then
            HAS_DOCKER=true
            echo -e "${GREEN}✓${NC} Docker configuration detected"
            append_to_markdown "- ✅ Docker configuration"
        fi
        
        print_header "📋 Setup Commands with Explanations"
        echo -e "Run these commands in the specified order. Each command includes an explanation of what it does."
        
        # --- STEP 1: PHP Dependencies ---
        if [ "$HAS_COMPOSER" = true ]; then
            print_header "STEP 1: Install PHP Dependencies"
        
            # Check if composer.lock exists
            if [ -f "$COMPOSER_PATH/composer.lock" ]; then
                print_command "cd $COMPOSER_PATH && composer install" "Installs PHP dependencies based on the locked versions in composer.lock" "$COMPOSER_PATH"
            else
                print_command "cd $COMPOSER_PATH && composer update" "Installs and updates PHP dependencies to their latest versions according to composer.json" "$COMPOSER_PATH"
            fi
        
            print_command "composer dump-autoload -o" "Optimizes the autoloader for better performance" "$COMPOSER_PATH"
        fi
        
        # --- STEP 2: Environment Setup ---
        if [ "$IS_LARAVEL" = true ]; then
            print_header "STEP 2: Set Up Environment"
        
            print_command "cd $ARTISAN_PATH && cp .env.example .env" "Creates environment configuration file from the example template" "$ARTISAN_PATH"
            print_command "php artisan key:generate" "Generates a secure application key for encryption" "$ARTISAN_PATH"
        
            if [ -f "$ARTISAN_PATH/.env.example" ]; then
                # Check if .env.example contains specific variables
                if grep -q "DB_CONNECTION" "$ARTISAN_PATH/.env.example"; then
                    echo -e "${YELLOW}# Don't forget to update database credentials in .env file:${NC}"
                    echo -e "${YELLOW}# - DB_CONNECTION${NC}"
                    echo -e "${YELLOW}# - DB_HOST${NC}"
                    echo -e "${YELLOW}# - DB_PORT${NC}"
                    echo -e "${YELLOW}# - DB_DATABASE${NC}"
                    echo -e "${YELLOW}# - DB_USERNAME${NC}"
                    echo -e "${YELLOW}# - DB_PASSWORD${NC}"
                    echo ""
                fi
        
                if grep -q "MAIL_" "$ARTISAN_PATH/.env.example"; then
                    echo -e "${YELLOW}# Mail configuration may be required later:${NC}"
                    echo -e "${YELLOW}# - MAIL_MAILER${NC}"
                    echo -e "${YELLOW}# - MAIL_HOST${NC}"
                    echo -e "${YELLOW}# - MAIL_PORT${NC}"
                    echo -e "${YELLOW}# - MAIL_USERNAME${NC}"
                    echo -e "${YELLOW}# - MAIL_PASSWORD${NC}"
                    echo ""
                fi
            fi
        fi
        
        # --- STEP 3: Database Setup ---
        if [ "$IS_LARAVEL" = true ]; then
            print_header "STEP 3: Set Up Database"
        
            print_command "cd $ARTISAN_PATH && php artisan migrate" "Runs database migrations to create tables" "$ARTISAN_PATH"
            print_command "php artisan db:seed" "Seeds the database with initial data (optional)" "$ARTISAN_PATH"
        
            echo -e "${YELLOW}# If you encounter errors during migration:${NC}"
            echo -e "${YELLOW}# 1. Check that your database exists and credentials are correct in .env${NC}"
            echo -e "${YELLOW}# 2. You might need to create the database manually:${NC}"
            echo -e "${YELLOW}#    mysql -u root -p -e \"CREATE DATABASE your_database_name;\"${NC}"
            echo ""
        fi
        
        # --- STEP 4: JavaScript Dependencies ---
        if [ "$HAS_NPM" = true ]; then
            print_header "STEP 4: Install JavaScript Dependencies"
        
            if [ "$HAS_YARN" = true ]; then
                print_command "cd $NPM_PATH && yarn install" "Installs JavaScript dependencies using Yarn" "$NPM_PATH"
            elif [ "$HAS_PNPM" = true ]; then
                print_command "cd $NPM_PATH && pnpm install" "Installs JavaScript dependencies using PNPM" "$NPM_PATH"
            else
                print_command "cd $NPM_PATH && npm install" "Installs JavaScript dependencies using NPM" "$NPM_PATH"
            fi
        
            if [ "$HAS_MONOREPO" = true ]; then
                if [ "$HAS_LERNA" = true ]; then
                    print_command "npx lerna bootstrap" "Sets up dependencies between local packages in the monorepo" "$NPM_PATH"
                else
                    echo -e "${YELLOW}# For monorepo setups, you might need additional steps to link packages${NC}"
                    echo ""
                fi
            fi
        
            # --- STEP 5: Build Assets ---
            print_header "STEP 5: Build Assets"
        
            # Determine the right build command based on detected frameworks and tools
            if [ "$IS_LARAVEL" = true ] && [ "$HAS_VITE" = true ]; then
                print_command "cd $NPM_PATH && npm run build" "Builds frontend assets for production using Vite" "$NPM_PATH"
                print_command "npm run dev" "Starts development server with hot-reloading (for development only)" "$NPM_PATH"
            elif [ "$IS_NEXT" = true ]; then
                print_command "cd $NPM_PATH && npm run build" "Builds the Next.js application for production" "$NPM_PATH"
                print_command "npm run dev" "Starts Next.js development server with hot-reloading" "$NPM_PATH"
            elif [ "$IS_NUXT" = true ]; then
                print_command "cd $NPM_PATH && npm run build" "Builds the Nuxt.js application for production" "$NPM_PATH"
                print_command "npm run dev" "Starts Nuxt.js development server with hot-reloading" "$NPM_PATH"
            elif [ "$IS_REACT" = true ] || [ "$IS_VUE" = true ]; then
                print_command "cd $NPM_PATH && npm run build" "Builds the application for production" "$NPM_PATH"
                print_command "npm run dev" "Starts development server with hot-reloading" "$NPM_PATH"
            elif [ "$IS_EXPRESS" = true ] || [ "$IS_NEST" = true ]; then
                if [ "$HAS_TYPESCRIPT" = true ]; then
                    print_command "cd $NPM_PATH && npm run build" "Compiles TypeScript code to JavaScript" "$NPM_PATH"
                fi
                print_command "npm run start" "Starts the server" "$NPM_PATH"
                print_command "npm run dev" "Starts the server with auto-restart on changes (if configured)" "$NPM_PATH"
            else
                # Generic commands based on what's likely in package.json
                echo -e "${YELLOW}# Check package.json for available scripts, common ones are:${NC}"
                print_command "cd $NPM_PATH && npm run dev" "Development build/server" "$NPM_PATH"
                print_command "npm run build" "Production build" "$NPM_PATH"
                print_command "npm start" "Start server (usually for production)" "$NPM_PATH"
            fi
        else
            echo -e "${YELLOW}# Skipping JavaScript dependencies and build steps (no package.json found)${NC}"
            append_to_markdown "### JavaScript Setup\n"
            append_to_markdown "JavaScript dependencies and build steps were skipped because no package.json was found.\n"
            append_to_markdown "If you need to add JavaScript to this project, consider initializing with:\n"
            append_to_markdown "```bash\nnpm init\n```\n"
        fi
        
        # --- STEP 6: Cache Clearing ---
        if [ "$IS_LARAVEL" = true ]; then
            print_header "STEP 6: Clear Cache"
        
            print_command "cd $ARTISAN_PATH && php artisan optimize:clear" "Clears all caches (config, route, view, compiled)" "$ARTISAN_PATH"
            print_command "php artisan config:cache" "Creates a cached file of all configuration settings" "$ARTISAN_PATH"
            print_command "php artisan route:cache" "Creates a cached file of all route registrations" "$ARTISAN_PATH"
            print_command "php artisan view:cache" "Compiles all Blade templates" "$ARTISAN_PATH"
        
            echo -e "${YELLOW}# Note: During development, you might want to skip caching to see changes immediately${NC}"
            echo -e "${YELLOW}# Use caching only in production for better performance${NC}"
            echo ""
        fi
        
        # --- STEP 7: Storage Link ---
        if [ "$IS_LARAVEL" = true ]; then
            print_header "STEP 7: Link Storage"
        
            print_command "cd $ARTISAN_PATH && php artisan storage:link" "Creates a symbolic link from public/storage to storage/app/public" "$ARTISAN_PATH"
        
            echo -e "${YELLOW}# This allows public access to files in the storage/app/public directory${NC}"
            echo -e "${YELLOW}# Important for file uploads that need to be publicly accessible${NC}"
            echo ""
        fi
        
        # --- STEP 8: Docker Setup (if applicable) ---
        if [ "$HAS_DOCKER" = true ]; then
            print_header "STEP 8: Docker Setup"
        
            if [ -f "docker-compose.yml" ] || [ -f "docker-compose.yaml" ]; then
                print_command "docker-compose up -d" "Starts all services defined in docker-compose.yml in detached mode" "$PROJECT_ROOT"
                print_command "docker-compose down" "Stops and removes containers, networks, and volumes" "$PROJECT_ROOT"
            elif [ -f "Dockerfile" ]; then
                print_command "docker build -t your-app-name ." "Builds a Docker image from the Dockerfile" "$PROJECT_ROOT"
                print_command "docker run -p 8000:8000 your-app-name" "Runs a container from the built image" "$PROJECT_ROOT"
            fi
        
            echo -e "${YELLOW}# Docker commands may need to be adjusted based on your specific setup${NC}"
            echo -e "${YELLOW}# Check the Dockerfile and docker-compose.yml for details${NC}"
            echo ""
        fi
        
        print_header "🚀 Next Steps"
        
        if [ "$IS_LARAVEL" = true ]; then
            echo -e "1. Visit your site at: http://localhost or your configured domain"
            echo -e "2. Complete any installation wizards if present"
            echo -e "3. After installation, secure permissions: chmod -R 775 storage bootstrap/cache"
            echo -e "4. Set up proper error logging and monitoring for production"
        elif [ "$IS_REACT" = true ] || [ "$IS_VUE" = true ] || [ "$IS_NEXT" = true ] || [ "$IS_NUXT" = true ]; then
            echo -e "1. Start development server: npm run dev"
            echo -e "2. Visit your site at: http://localhost:3000 or the port shown in terminal"
            echo -e "3. Set up proper environment variables for production deployment"
        elif [ "$IS_EXPRESS" = true ] || [ "$IS_NEST" = true ]; then
            echo -e "1. Start the server: npm run start or npm run dev"
            echo -e "2. Access your API at: http://localhost:3000 or the configured port"
            echo -e "3. Set up proper environment variables and security for production"
        else
            echo -e "1. Check the project's README.md for additional setup instructions"
            echo -e "2. Look for any installation guides in the documentation"
            echo -e "3. Review package.json scripts to understand available commands"
        fi
        
        echo ""
        echo -e "${YELLOW}Note: Always check the project's documentation for specific setup instructions.${NC}"
        echo -e "${YELLOW}This script provides general guidance but may need adjustments for your specific project.${NC}"
        
        # Function to save the markdown report
        save_markdown_report() {
            # Write the collected Markdown content to a file
            echo -e "$MARKDOWN_REPORT" > "$REPORT_FILE"
            echo -e "${YELLOW}Markdown report created: ${GREEN}$REPORT_FILE${NC}"
        }
        
        print_header "📝 Command Summary"
        echo -e "Here's a quick reference of all commands for your project type:"
        
        # Get relative paths for display
        COMPOSER_REL_PATH=$(get_relative_path "$COMPOSER_PATH")
        NPM_REL_PATH=$(get_relative_path "$NPM_PATH")
        ARTISAN_REL_PATH=$(get_relative_path "$ARTISAN_PATH")
        
        # Create a summary based on detected project type
        if [ "$IS_LARAVEL" = true ]; then
            echo -e "${BOLD}Laravel Project Setup:${NC}"
            echo -e "1. ${GREEN}cd $COMPOSER_REL_PATH && composer install${NC}"
            echo -e "2. ${GREEN}cd $ARTISAN_REL_PATH && cp .env.example .env${NC}"
            echo -e "3. ${GREEN}cd $ARTISAN_REL_PATH && php artisan key:generate${NC}"
            echo -e "4. ${GREEN}cd $ARTISAN_REL_PATH && php artisan migrate${NC}"
            if [ "$HAS_NPM" = true ]; then
                echo -e "5. ${GREEN}cd $NPM_REL_PATH && npm install${NC}"
                echo -e "6. ${GREEN}cd $NPM_REL_PATH && npm run build${NC}"
            fi
            echo -e "7. ${GREEN}cd $ARTISAN_REL_PATH && php artisan optimize:clear${NC}"
            echo -e "8. ${GREEN}cd $ARTISAN_REL_PATH && php artisan storage:link${NC}"
        elif [ "$HAS_NPM" = true ] && ([ "$IS_REACT" = true ] || [ "$IS_VUE" = true ]); then
            echo -e "${BOLD}Frontend Project Setup:${NC}"
            if [ "$HAS_YARN" = true ]; then
                echo -e "1. ${GREEN}cd $NPM_REL_PATH && yarn install${NC}"
                echo -e "2. ${GREEN}cd $NPM_REL_PATH && yarn dev${NC} (development)"
                echo -e "3. ${GREEN}cd $NPM_REL_PATH && yarn build${NC} (production)"
            else
                echo -e "1. ${GREEN}cd $NPM_REL_PATH && npm install${NC}"
                echo -e "2. ${GREEN}cd $NPM_REL_PATH && npm run dev${NC} (development)"
                echo -e "3. ${GREEN}cd $NPM_REL_PATH && npm run build${NC} (production)"
            fi
        elif [ "$HAS_NPM" = true ] && ([ "$IS_EXPRESS" = true ] || [ "$IS_NEST" = true ]); then
            echo -e "${BOLD}Node.js Backend Setup:${NC}"
            echo -e "1. ${GREEN}cd $NPM_REL_PATH && npm install${NC}"
            if [ "$HAS_TYPESCRIPT" = true ]; then
                echo -e "2. ${GREEN}cd $NPM_REL_PATH && npm run build${NC}"
            fi
            echo -e "3. ${GREEN}cd $NPM_REL_PATH && npm run start${NC}"
        elif [ "$HAS_COMPOSER" = true ]; then
            echo -e "${BOLD}PHP Project Setup:${NC}"
            echo -e "1. ${GREEN}cd $COMPOSER_REL_PATH && composer install${NC}"
            echo -e "2. ${GREEN}composer dump-autoload -o${NC}"
        elif [ "$HAS_NPM" = true ]; then
            echo -e "${BOLD}JavaScript Project Setup:${NC}"
            if [ "$HAS_YARN" = true ]; then
                echo -e "1. ${GREEN}cd $NPM_REL_PATH && yarn install${NC}"
                echo -e "2. ${GREEN}cd $NPM_REL_PATH && yarn build${NC} (if available)"
            elif [ "$HAS_PNPM" = true ]; then
                echo -e "1. ${GREEN}cd $NPM_REL_PATH && pnpm install${NC}"
                echo -e "2. ${GREEN}cd $NPM_REL_PATH && pnpm run build${NC} (if available)"
            else
                echo -e "1. ${GREEN}cd $NPM_REL_PATH && npm install${NC}"
                echo -e "2. ${GREEN}cd $NPM_REL_PATH && npm run build${NC} (if available)"
            fi
        else
            echo -e "${BOLD}No specific setup commands detected${NC}"
            echo -e "This directory doesn't appear to contain a recognized project structure."
            echo -e "You may need to initialize a project first:"
            echo -e "- For PHP: ${GREEN}composer init${NC}"
            echo -e "- For JavaScript: ${GREEN}npm init${NC} or ${GREEN}yarn init${NC}"
            echo -e "- For Laravel: ${GREEN}composer create-project laravel/laravel my-project${NC}"
        fi
        
        echo ""
        echo -e "${BOLD}${GREEN}Happy coding!${NC}"
        
        # Save markdown report
        save_markdown_report
        
        ```
        
    
    We've created (copy and paste the above script) a script that automatically detects your project type and gives you the exact commands to run:
    
    ```bash
    # Run the project setup helper script
    ./scripts/detect-commands/project-setup-helper.sh
    
    ```
    
    This script will:
    
    1. Detect your project type (Laravel, React, Vue, etc.)
    2. Identify build tools and dependencies
    3. Provide you with the exact commands to run in the correct order
    4. Include helpful notes and next steps
    
    ### Manual Reference Guide
    
    If you prefer a manual approach, here's a quick reference for common project types:
    
    ### Laravel Projects
    
    ```bash
    # 1. Install PHP dependencies
    composer install
    
    # 2. Set up environment
    cp .env.example .env
    php artisan key:generate
    
    # 3. Set up database
    php artisan migrate
    php artisan db:seed  # Optional
    
    # 4. Install JavaScript dependencies (if needed)
    npm install
    
    # 5. Build assets (if needed)
    npm run build
    
    # 6. Clear cache
    php artisan optimize:clear
    
    # 7. Link storage (if needed)
    php artisan storage:link
    
    ```
    
    ### React Projects
    
    ```bash
    # 1. Install dependencies
    npm install
    
    # 2. Start development server
    npm run dev
    
    # 3. Build for production
    npm run build
    
    ```
    
    ### Vue Projects
    
    ```bash
    # 1. Install dependencies
    npm install
    
    # 2. Start development server
    npm run dev
    
    # 3. Build for production
    npm run build
    
    ```
    
- Step 5: Set Up `HeidiSQL`, `TablePlus`, or  `DBeaver`, or `Beekeeper Studio`  for Database Management
    1. **Open TablePlus**:
        - Launch TablePlus from your Applications folder
    2. **Create a New Connection**:
        - Click "Create a new connection"
        - Select "MySQL" from the connection options
    3. **Configure Connection Details**:
        - Name: `Custojo App`
        - Host: `127.0.0.1`
        - Port: `3306`
        - User: `root`
        - Password: `your_mysql_password`
        - Database: `custojo_app`
    4. **Test and Connect**:
        - Click "Test" to verify the connection works
        - Click "Connect" to open the database
    5. **Verify Database Structure**:
        - Check that all tables were created correctly
        - Verify that seed data was properly inserted
- Step 6: Final Verification
    1. **Check Application Status**:
        - Visit your site URL (e.g., `http://CustojoRoot.test`)
        - Verify that the application loads without errors
        - Test basic functionality to ensure database connection is working
    2. **Troubleshooting Common Issues**:
        - If you encounter a "500 Server Error", check the Laravel logs:
            
            ```bash
            tail -f storage/logs/laravel.log
            
            ```
            
        - If database connection fails:
            - Verify MySQL is running
            - Check credentials in `.env` file
            - Ensure the database exists
    3. **Additional Configuration** (if needed):
        - Set up storage links:
            
            ```bash
            php artisan storage:link
            
            ```
            
        - Configure queue worker (if using queues):
            
            ```bash
            php artisan queue:work
            
            ```
            
    
    Would you like me to create this as a Markdown file for you? Or would you like me to expand on any specific section?