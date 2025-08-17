### 0- PHASE 0: Setup **Systems and Tools**

- **STEPS**
    - **1- Setup Computer Tools**
        - **1- Setup HERD**
            1. **1.1. Laravel Herd Installation & Configuration**
                1. **Download & Install Herd:**
                    1. **Visit** **`https://herd.laravel.com`** on your Mac
                    2. **Click** "Download for macOS"
                    3. **Open** the downloaded **`.dmg`** file
                    4. **Drag** Laravel Herd to Applications folder
                    5. **Launch** Herd from Applications
                2. **Initial Setup**
                    1. **Grant permissions** when prompted (required for DNS/networking)
                    2. **Enter** your macOS password
                    3. **Herd automatically configures:**
                        - PHP 8.3 (latest)
                        - Nginx web server
                        - DNS masq for **`.test`** domains
                        - Composer globally available
                3. **Verify Installation**
                    1. open terminal 
                    2. run commands
                    
                    ```bash
                    # Open Terminal and verify
                    php --version          # Should show PHP 8.3.x
                    composer --version     # Should show Composer 2.x
                    node --version        # Should show Node.js (via NVM)
                    ```
                    
            2. **Herd Pro Upgrade (Recommended)** 
                
                <aside>
                💡
                
                **Why upgrade to Pro:** Built-in MySQL, Redis, Mail server, Dumps viewer, and Services management
                
                </aside>
                
                1. **Purchase & Activate:**
                    1. **Visit** Herd settings → **Upgrade to Pro**
                    2. **Purchase** $99/year license
                    3. **Enter license key** in Herd
                    4. **Restart** Herd application
                    
                    <aside>
                    💡
                    
                    **Expected Result:** Services tab appears in Herd with database options
                    
                    </aside>
                    
            3. `OPTIONAL` - **Create Development Directory Structure**
                
                ```bash
                # Create organized project structure
                mkdir -p ~/Herd/codecanyon-projects
                cd ~/Herd/codecanyon-projects
                mkdir yourapp-v2.4
                cd yourapp-v2.4
                ```
                
                - if you do this, you can simply add the path and use codecanyon-projects where u can create apps folders inside and they will have links - same php, same env stuff tho.
                - **Why this structure:** Herd automatically serves any project in **`~/Herd/`** as **`projectname.test`**
            4. **Setup Development Services (Herd Pro)**
                1. **MySQL Database Setup:**
                    1. **Open** `Herd` → `Services` tab
                    2. **Click** "`Create Service`" →Database→  `MySQL 8.0` or higher (ex: `8.4.2`)
                    3. **Configure:**
                        - Name: **`yourapp_mysql. example: Residoro_MySQL`**
                        - Port: 3306 (default)
                        - Root password: `zaj123`
                        - Authostart: check yes
                    4. **Click** Create Service
                2. **Redis Cache Setup:**
                    1. **Services** → Create Service →Cache →  Redis 7.0
                    2. **Configure:**
                        - Name: **`Yourapp_Redis` `example: Residoro_Redis`**
                        - Port: `6379` (default)
                    3. **Click** Create Service
                    
                    <aside>
                    💡
                    
                    as we may use same service for varous apps, maybe we can name them
                    
                    - `Local_MySQL`
                    - `Loca_Redis`
                    </aside>
                    
                3. Note down connection details - for local setup
                    1. click serivces→ mysql → click on `Local_MySQL`→ copy environemnetal variables 
                        
                        ```bash
                        DB_CONNECTION=mysql
                        DB_HOST=127.0.0.1
                        DB_PORT=3306
                        DB_DATABASE=laravel
                        DB_USERNAME=root
                        DB_PASSWORD=
                        ```
                        
                    2. click serivces→ mysql → click on `Loca_Redis`→ copy environemnetal variables 
                        
                        ```bash
                        REDIS_HOST=127.0.0.1
                        REDIS_PASSWORD=null
                        REDIS_PORT=6379
                        ```
                        
                
                **Expected Result:** Both services show "Running" status in Herd Services panel
                
    - **2- Setup Server + Server Github Repo**
        1. Setup New Server - `Done once per Server`
            1. `On Local VSCode`
                - 1 Setup Local VSCode SSH + Hostings  - `Done once`
                    - **Steps**
                        1. **Open ssh`config` file in VSCode**
                            
                            ```bash
                            # For Mac
                            code ~/.ssh/config
                            
                            # For Windows
                            code "$env:USERPROFILE\.ssh\config"
                            
                            ```
                            
                        2. Ensure the hosting is added to **ssh`config` file**
                            
                            ```bash
                            # Hostinger Zajaly Connection
                            Host hostinger-zajaly
                                HostName 93.127.221.221
                                User u164914061
                                Port 65002
                            
                            ```
                            
                        3. Ensure to get SSH key and have it added to Hostinger → SSH as key
                            - **Steps: Get and Add SSH Key to Hosting**
                                - Steps- Mac
                                    1. Get SSH key 
                                        
                                        ```json
                                        # For Mac (Terminal)
                                        pbcopy < ~/.ssh/id_ed25519.pub
                                        echo "✅ Public key copied to clipboard"
                                        ```
                                        
                                    2. **Add Key to Hostinger,i**n the hPanel, go to `SSH Access`, paste the key, and give it a descriptive title - ex: `MacBook`, or `Windows Desktop`
                        4. **Save the ssh`config` file**  in VSCode (`Ctrl+S` or `Cmd+S`).
                        5. **Test the connection to Hostinger: Open your terminal**
                            
                            ```bash
                            ssh hostinger-zajaly
                            ssh hostinger-factolo
                            
                            ```
                            
                            - **What This Does:** This short command uses the alias `hostinger` from your `.ssh/config` file. It's a shortcut for the much longer command: `ssh -p 65002 -i ~/.ssh/id_ed25519 [YOUR_HOSTINGER_USERNAME]@[YOUR_HOSTINGER_IP]`
                            - **First Time Connecting:** Type `yes` and press `Enter` if asked to confirm the host's authenticity (`Fingerprint`).
                            - **Expected Result:** You are logged into your Hostinger server and see a command prompt like `[YOUR_HOSTINGER_USERNAME]@server:~$`.
                            - **Action:** Type `exit` and press `Enter` to close the connection.
                            - Do i need to enter ssh password i set on hostinger for first time ssh connection? no - but in case u already have previous ssh key added to hostinger, delete it and add again.
                    
                - **2- SSH into the server**
                    1. **SSH Into Your Server - or use Remote SSH Extension**
                        
                        ```bash
                        # Connect via VSCode Remote SSH or terminal
                        ssh hostinger-zajaly
                        ssh hostinger-factolo
                        
                        ```
                        
                    2. Click in Vsdode sidebar → open Folder: ex `/home/u22dasd2r2w3/` 
                    3. clcik ok
                    4. First Clean Hosting Looks like this 
                    
                    ```bash
                    › .cache
                    › cagefs
                    › .cl.selector
                    › .dotnet
                    › logs
                    › .ssh
                    › vscode-server
                    › domains
                    E .api_token
                    = .msmtprc
                    -profile
                    E wget-hsts
                    ```
                    
            
            ---
            
            b. `On Server:` after u SSH
            
            - **1- Create `Server-GitHub` `SSH` Key on Server & Add it to Github**
                - **Steps- Server SSH Setup (Done Once Per Server) -**
                    - **1- Generate `Server-GitHub` SSH Key**
                        1. **SSH Into Your Server**
                            
                            ```bash
                            # Connect via VSCode Remote SSH or terminal
                            ssh hostinger
                            or
                            ssh hostinger-zajaly
                            ```
                            
                        2. Notice `.ssh` is empty at this stage and only 1 file `authorized_keys` which has the local machine to server key we setup above.
                        3. **Open Server VSCODE Terminal and Generate Server-Wide SSH Key**
                            
                            ```bash
                            # Navigate to home directory
                            cd ~
                            # pwd
                            # ex:  /home/u22dasd2r2w3/ 
                            
                            # Generate new ed25519 key for server
                            ssh-keygen -t ed25519 -C "hostinger-server-$(date +%Y%m%d)" -f ~/.ssh/hostinger_github_key
                            
                            ```
                            
                            **Important:** When prompted:
                            
                            - `Enter passphrase (empty for no passphrase):` → **Press `Enter` (leave empty)**
                            - `Enter same passphrase again:` → **Press `Enter` again (leave empty)**
                            
                            **Why no passphrase?** This enables automated Git operations without manual password entry.
                            
                            This creates:
                            
                            - `~/.ssh/hostinger_github_key` (private key)
                            - `~/.ssh/hostinger_github_key.pub` (public key)
                        4. **Set Correct Permissions**
                            
                            ```bash
                            # Secure the SSH keys
                            chmod 600 ~/.ssh/hostinger_github_key
                            chmod 644 ~/.ssh/hostinger_github_key.pub
                            
                            # Verify permissions
                            ls -l ~/.ssh/hostinger_github_key ~/.ssh/hostinger_github_key.pub
                            
                            ```
                            
                        5. **Configure SSH for GitHub**
                            
                            ```bash
                            # Create or update SSH config file
                            cat > ~/.ssh/config << 'EOF'
                            Host github.com
                                HostName github.com
                                User git
                                IdentityFile ~/.ssh/hostinger_github_key
                                IdentitiesOnly yes
                            EOF
                            
                            # Set config permissions
                            chmod 600 ~/.ssh/config
                            
                            # Verify configuration
                            cat ~/.ssh/config
                            
                            ```
                            
                        6. **Get Public Key for Deploy Keys**
                            
                            ```bash
                            # Display the public key - you'll need this for each repository
                            cat ~/.ssh/hostinger_github_key.pub
                            
                            ```
                            
                            **Copy this entire output** - you'll use it for every repository's deploy key.
                            
                        7. **Add Server SSH key to Github**
                            - **The setup method depends on whether you own the GitHub account or need repository-specific access**
                            - **for Personal Github Accounts, ex: `rovony`**
                                - Add the server's public SSH key to your personal GitHub account at `github.com/settings/keys`
                            - **for Organizations Github Accounts, ex: `Zajaly`, `Zaj-Dev`**
                                - Add the server's public SSH key to each specific repository's "`Deploy keys`" settings
                        8. **Test SSH Authentication**
                            
                            ```bash
                            # Test SSH connection to GitHub
                            ssh -T git@github.com
                            
                            # Expected Result:
                            # Hi username! You've successfully authenticated, but GitHub does not provide shell access.
                            
                            ```
                            
                            - first time you may get asked `Are you sure you want to continue connecting (yes/no/[fingerprint])` type `yes`
                            - creates new file named `.ssh/known_hosts`
            - **2- Setup `Git` for Server +Create `Github Server Repo`**
                1. Create a Github Repo, example: `rovony/Server-Hostinger-Zajaly` -private, no Readme, no gitignore
                    - private, no Readme, no gitignore
                2. On Server Run the below, **Configure Git User** 
                    
                    ```bash
                    # Set your Git user name and email globally
                    git config --global user.name rovony
                    git config --global user.email zajalyapps@gmail.com
                    
                    ```
                    
                    - This creates/updates server root file `.gitconfig` and inside it adds the name and email.
                3. **Initialize Server-Level Git Repository [update using the server Github repo**
                    - Server Github Repo SSH URL:  [`git@github.com](mailto:git@github.com):rovony/Server-Hostinger-Zajaly.git`
                        
                        ```bash
                        # Navigate to server root (home directory)
                        cd ~
                        
                        # Initialize Git repository
                        git init
                        
                        # Add remote origin pointing to your server repository
                        git remote add origin git@github.com:rovony/Server-Hostinger-Factolo.git
                        
                        # Verify remote configuration
                        git remote -v
                        # Expected Results
                        #origin  git@github.com:rovony/Server-Hostinger-Zajaly.git (fetch)
                        #origin  git@github.com:rovony/Server-Hostinger-Zajaly.git (push)
                        ```
                        
                4. **Create Comprehensive Server-Level `.gitignore`**
                    - A good `.gitignore` is crucial for avoiding committing sensitive files and unnecessary clutter. Create a `.gitignore` file in the server root.
                    
                    > 📋 Note: For a comprehensive, production-ready .gitignore template suitable for most server environments, refer to the one provided in Appendix 1: Complete Server Management Scripts.
                    > 
                    1. create server root file named `.gitignore`
                    2. File the `.gitignore` file with below content
                        - Using the template below: the server is now properly configured for **one-click restore** and **server duplication**! 🎯
                        - `.gitiginore` -template
                            
                            ```markdown
                            .cache/
                            .cagefs/
                            *.trash
                            .vscode-server/data/CachedExtensionVSIXs/
                            .vscode-server/data/CachedProfilesData/
                            .vscode-server/cli/
                            .vscode-server/code-*/
                            .vscode-server/data/logs/
                            .vscode-server/data/clp/
                            .dotnet/corefx/cryptography/crls/
                            .local/share/gk/
                            .vscode-server/data/User/workspaceStorage/
                            .vscode-server/.cli.*.log
                            
                            # ────────────────────────────────────
                            # 1. SECURITY: Sensitive Keys & Credentials
                            # ────────────────────────────────────
                            # EXCLUDE: These contain secrets that should never be in version control
                            .ssh/*
                            !.ssh/README.md
                            !.ssh/config
                            # API tokens and secrets
                            .api_token
                            .api_key
                            *.token
                            # SSL certificates and private keys
                            *.pem               # SSL private keys - security risk
                            *.key               # Private keys - security risk  
                            *.crt               # Can be reissued
                            *.csr               # Certificate signing requests
                            *.p12               # PKCS#12 certificates
                            *.pfx               # Personal Information Exchange
                            # Database access
                            .my.cnf             # MySQL credentials - security risk
                            .pgpass             # PostgreSQL passwords
                            # cPanel/WHM access
                            .accesshash         # WHM access hash - security risk
                            
                            # Environment files with secrets
                            .env                # Contains database passwords, API keys
                            .env.*              # All environment variants
                            !/.env.example      # INCLUDE: Template files are safe
                            !/.env.template     # INCLUDE: No actual secrets
                            
                            # ────────────────────────────────────
                            # 2. TRANSIENT: System Runtime & Temporary Files  
                            # ────────────────────────────────────
                            # EXCLUDE: These are created by OS/processes and not needed for restore
                            core                # Core dumps - huge files, temporary
                            core.*              # Core dump variants
                            *.pid               # Process IDs - runtime only
                            *.swp               # Vim swap files - temporary
                            *.lock              # Lock files - runtime only
                            *.sock              # Unix socket files - runtime only
                            *.tmp               # Temporary files
                            # User history and caches
                            .bash_history       # Command history - privacy + not needed
                            .mysql_history      # MySQL command history
                            .wget-hsts          # wget cache - can be rebuilt
                            .lesshst            # less history cache
                            # Virtual filesystems (never back these up)
                            /proc/              # Process filesystem - runtime only
                            /sys/               # System filesystem - runtime only  
                            /dev/               # Device files - managed by OS
                            /run/               # Runtime data - temporary
                            
                            # ────────────────────────────────────
                            # 3. CACHES: User & Application Caches
                            # ────────────────────────────────────
                            # EXCLUDE: These improve performance but can be rebuilt
                            .cache/                     # User cache directory - ALL contents
                            .cache/*                    # All cache files
                            .local/share/               # User application data cache
                            .local/lib/                 # User library cache
                            .local/*                    # All local files
                            .config/*/cache/            # Application-specific caches
                            .dotnet/                    # .NET cache - ALL contents
                            .dotnet/*                   # All .NET files
                            # VS Code server - exclude large files but keep settings (handled in section 0 & 9)
                            .filebrowser/               # File browser cache
                            .subversion/                # SVN cache data
                            .cagefs/                    # CloudLinux CageFS cache - ALL contents
                            .cagefs/*                   # All CageFS files
                            
                            # NOTE: .cl.selector/ is NOT excluded - contains PHP configs needed for restore!
                            
                            # ────────────────────────────────────
                            # 4. LOGS: High-Volume System Logs
                            # ────────────────────────────────────
                            # EXCLUDE: Massive files that grow continuously
                            /var/log/messages*  # System messages - very large
                            /var/log/syslog*    # System logs - very large  
                            /var/log/kern.log*  # Kernel logs - large
                            /var/log/mail.log*  # Mail logs - can be huge
                            /var/cpanel/logs/   # cPanel operation logs - large
                            /usr/local/cpanel/logs/ # More cPanel logs
                            # Generic log patterns
                            access_log*         # Web server access logs - huge
                            error_log.*         # Rotated error logs
                            logs/               # Generic log directories
                            .logs/              # Hidden log directories
                            cache/              # Cache directories
                            tmp/                # Temporary directories
                            temp/               # Temporary directories
                            
                            # INCLUDE: Keep directory structure
                            !/.gitkeep          # Preserve empty directories
                            !/logs/.gitkeep     # Keep logs directory structure
                            
                            # INCLUDE: Keep critical error logs for troubleshooting
                            !domains/*/logs/error.log    # Current domain error logs - needed for debugging
                            !**/error.log               # Current error logs - troubleshooting
                            !.gitignore                 # This file itself
                            
                            # ────────────────────────────────────
                            # 5. LARGE FILES: Prevent files over GitHub's 100MB limit
                            # ────────────────────────────────────
                            # EXCLUDE: These are massive and exceed GitHub's file size limits
                            # VS Code server binaries - these are massive
                            .vscode-server/cli/
                            .vscode-server/bin/
                            .vscode-server/data/logs/
                            .vscode-server/extensions/
                            .vscode-server/data/CachedExtensions/
                            .vscode-server/data/clp/
                            # Large binary executables
                            **/node
                            **/server/node
                            **/node_modules/.bin/
                            # Other large files
                            *.iso
                            *.dmg
                            *.img
                            # Large archives
                            *.tar.gz
                            *.zip
                            *.rar
                            # Database dumps
                            *.sql
                            *.sql.gz
                            
                            # ────────────────────────────────────
                            # 6. LARGE FILES: Backups & Database Dumps
                            # ────────────────────────────────────
                            # EXCLUDE: These are massive and should use external storage
                            *.sql.bz2           # Compressed DB dumps
                            *.dump              # Database dumps
                            *.tar.bz2           # Compressed archives
                            *.bak               # Backup files
                            *.backup            # Backup files
                            *backup*            # Any backup-named files
                            backup-*            # cPanel backup files
                            *.tar               # Tar archives
                            
                            # Use external storage (S3, etc.) for database backups instead of Git
                            
                            # ────────────────────────────────────
                            # 7. USER CONTENT: Uploads & Dynamic Data
                            # ────────────────────────────────────
                            # EXCLUDE: User-generated content - security risk + large files
                            domains/*/public_html/wp-content/uploads/  # WordPress uploads - large files
                            domains/*/public_html/uploads/             # Generic uploads
                            domains/*/shared/public/user-uploads/      # Application uploads
                            domains/*/shared/public/storage/           # Dynamic storage
                            domains/*/shared/bootstrap/cache/          # Laravel bootstrap cache
                            domains/*/shared/storage/                  # Application storage
                            # Domain artifacts
                            domains/*/myFiles-Server/3_Domain_Logs/    # Domain-specific logs
                            domains/*/myFiles-Server/4_Domain_SnapShots/ # Snapshots - use external backup
                            # WordPress cache and backups
                            domains/*/public_html/wp-content/cache/    # WordPress cache - rebuilds
                            domains/*/public_html/wp-content/backup*/  # WordPress backups - external storage
                            
                            # ────────────────────────────────────
                            # 8. DEVELOPMENT: Dependencies & Build Artifacts
                            # ────────────────────────────────────
                            # Exclude staging copy domain
                            domains/staging.resiboard.com\ copy/       # Staging copy - not needed
                            
                            # Include staging domain but exclude development artifacts
                            !domains/staging.resiboard.com/            # INCLUDE: Staging domain code
                            # Exclude dependencies - can be reinstalled
                            domains/staging.resiboard.com/**/vendor/   # PHP Composer dependencies
                            domains/staging.resiboard.com/**/node_modules/ # NPM dependencies  
                            domains/staging.resiboard.com/**/bower_components/ # Bower dependencies
                            # Exclude environment files with secrets
                            domains/staging.resiboard.com/**/.env      # Environment secrets
                            domains/staging.resiboard.com/**/.env.*    # Environment variants
                            !domains/staging.resiboard.com/**/.env.example # INCLUDE: Templates
                            # Exclude lock files - version conflicts
                            domains/staging.resiboard.com/**/composer.lock   # PHP dependency locks
                            domains/staging.resiboard.com/**/package-lock.json # NPM locks
                            domains/staging.resiboard.com/**/yarn.lock        # Yarn locks
                            
                            # ────────────────────────────────────
                            # 9. EDITOR ARTIFACTS: IDE & Editor Files
                            # ────────────────────────────────────
                            # EXCLUDE: OS and editor temporary files
                            .DS_Store           # macOS folder metadata
                            .DS_Store?          # macOS variants
                            Thumbs.db           # Windows thumbnail cache
                            *~                  # Editor temporary files
                            *.swp               # Vim swap files
                            *.swo               # Vim swap files
                            .*.swp              # Hidden swap files
                            */.svn/             # Subversion directories
                            */.hg/              # Mercurial directories
                            
                            # INCLUDE: Allow nested Git repos in domains
                            !domains/*/.git     # Domain-specific Git repos
                            
                            # ────────────────────────────────────
                            # 10. CRITICAL INCLUSIONS: Force Include Essential Files
                            # ────────────────────────────────────
                            # INCLUDE: These are absolutely required for server restore
                            
                            # User environment - CRITICAL for restore
                            !.gitconfig                 # Git configuration - CRITICAL
                            !.msmtprc                   # Mail configuration - CRITICAL  
                            !.profile                   # Shell profile - CRITICAL
                            !.bashrc                    # Bash configuration
                            !.bash_profile              # Bash profile
                            
                            # Server configurations - CRITICAL
                            !.cl.selector/              # PHP Selector configs - CRITICAL for PHP setup
                            !.cl.selector/*.cfg         # PHP configuration files like alt_php82.cfg
                            !.htaccess                  # Web server configs - CRITICAL
                            !.htpasswd                  # Password files - CRITICAL  
                            !*.conf                     # Configuration files - CRITICAL
                            !*.config                   # Config files - CRITICAL
                            
                            # VS Code settings (not logs/cache) - keep user settings
                            !.vscode-server/data/User/  # User settings and extensions
                            .vscode-server/data/User/globalStorage/
                            .vscode-server/data/User/History/
                            !.vscode-server/*.json      # Configuration files
                            
                            # CageFS configs (not cache) - CloudLinux configurations
                            !.cagefs/*.conf             # CageFS configuration files
                            !.cagefs/etc/               # CageFS etc directory
                            
                            # cPanel user data - CRITICAL for account restore
                            !.contactemail              # Account contact info
                            !.lastlogin                 # Login tracking
                            !.cpanel/                   # cPanel user configurations
                            
                            # Custom management and configuration
                            !README.md                  # Documentation
                            !Admin-Server/              # Server administration scripts - CRITICAL
                            !domains/                   # Domain configurations and code - CRITICAL
                            !scripts/                   # Custom scripts - CRITICAL
                            !configuration/             # Server configuration - CRITICAL  
                            !Malek/                     # User directory - CRITICAL
                            !Documents/                 # Important documents - CRITICAL
                            
                            ```
                            
                5. Ensure to Fix email to avoid issues
                    - Potential Issue: The issue if that your email `zajalyapps@gmail.com` is set to private on GitHub, but you're trying to commit with it. GitHub protects users' private emails from being exposed in commit history. Since `zajalyapps@gmail.com` is marked private on your GitHub account, GitHub rejected the push to prevent email exposure
                    1. **Option 1**: If Email is set as private on Github - **Make Your Email Public,** 
                        1. Go to: https://github.com/settings/emails
                        2. Uncheck "Keep my email addresses private"
                    2. Option 2:  **Use GitHub's No-Reply Email, by setting up  - `USED`**
                        1. Get User GitHub ID:
                            - Note: The GitHub no-reply email format is:
                                
                                ```bash
                                [YOUR_GITHUB_USER_ID]+[YOUR_USERNAME]@users.noreply.github.com
                                # Example format:
                                # 191882927+rovony@users.noreply.github.com
                                ```
                                
                                - You can Verify your GitHub user ID
                                    
                                    ```bash
                                    curl -s https://api.github.com/users/rovony | grep '"id"'
                                    # Expected results:
                                    #   "id": 191882927,
                                    ```
                                    
                                
                        2. **Step 2: Update Git Configuration with Correct Email**
                        
                        ```bash
                        # Update to the CORRECT GitHub user ID (191882927, not 164914061)
                        git config --global user.email "191882927+rovony@users.noreply.github.com"
                        
                        # Verify the configuration
                        git config --global user.email
                        git config --global user.name
                        
                        # Expected output:
                        # 191882927+rovony@users.noreply.github.com
                        # rovony
                        ```
                        
                        - THis will update the email in `.gitconfig` file.
                6. **Initial Git Commit and Push -**`.gitignore` File
                    
                    ```bash
                    # Add the .gitignore first
                    git add .gitignore
                    git commit -m "feat: add server .gitignore"
                    
                    # Rename branch to main (if needed)
                    git branch -M main
                    
                    # Push the initial commit
                    git push -u origin main
                    
                    ```
                    
                7. **Initial Git Commit and Push -**Everything Else
                    
                    ```bash
                    # Add Everything Else
                    git add .
                    git commit -m "First Server Push: Before any Changes"
                    
                    # Rename branch to main (if needed)
                    git branch -M main
                    
                    # Push the initial commit
                    git push -u origin main
                    
                    ```
                    
            - 3- create server readme
                - Template Readme file at server root
                    
                    ```markdown
                    # Server Configuration Repository
                    
                    ## Overview
                    
                    This repository contains the essential configuration files for this server. The primary purpose of this repository is to facilitate a **one-click restore** of the server's configuration and to enable **server duplication**.
                    
                    By storing key configuration files in this version-controlled repository, we can ensure consistency, track changes over time, and quickly set up a new server with the same configuration.
                    
                    ## What's Included
                    
                    This repository includes, but is not limited to:
                    
                    *   **.gitconfig**: Global Git configuration for the server.
                    *   **.gitignore**: A comprehensive list of files and directories to be ignored by Git, preventing sensitive or unnecessary files from being committed.
                    *   **.ssh/config**: SSH client configuration, including host aliases and settings for connecting to other servers.
                    *   **.ssh/README.md**: A detailed guide on how to set up and manage SSH keys for both local and server-to-server connections.
                    *   **VS Code Server Settings**: Essential user settings for the VS Code remote development environment, ensuring a consistent editor setup.
                    
                    ## How to Use
                    
                    ### One-Click Restore
                    
                    To restore the configuration on a new or existing server, you can clone this repository to the home directory:
                    
                    ```bash
                    git clone git@github.com:rovony/Server-Hostinger-Factolo.git ~/
                    ```
                    
                    This will place all the configuration files in their correct locations.
                    
                    ### Server Duplication
                    
                    To duplicate this server's configuration on a new server, follow the same steps as the one-click restore. This will provide a consistent base configuration across all your servers.
                    
                    ## Important Notes
                    
                    *   **Sensitive Files**: The `.gitignore` file is carefully crafted to exclude sensitive information such as private keys, API tokens, and passwords. Always review the `.gitignore` file before committing to ensure no sensitive data is included.
                    *   **Local Overrides**: If you need to make local modifications to a configuration file without committing them to the repository, you can use the following command:
                        ```bash
                        git update-index --assume-unchanged <file>
                        ```
                        To start tracking changes again, use:
                        ```bash
                        git update-index --no-assume-unchanged <file>
                    
                    ```
                    
            
            ---
            
            `Add depolyment system`
            
            - **3- Copy The `Server-Admin` Folder to Local Project Root and Push It**
                - **Goal:** Transfer the validated Admin-Server folder from the saved folders location to your local project root, integrate it properly, and push changes to repository.
                    
                    **Prerequisites:**
                    
                    - Admin-Server folder validated and ready for integration
                    - Local project repository properly set up and accessible
                    - Git repository configured and connected
                
                Steps 
                
                1. 3.1: Install Admin-Server on Server Using VS Code SSH Remote Extension
                    
                    **Purpose:** Install Admin-Server as a server-level tool that can manage multiple projects on the server.
                    
                    **Prerequisites:**
                    
                    - VS Code with Remote-SSH extension installed
                    - Server SSH access credentials
                    - Admin-Server validated in `1-Current-Versions/Admin-Server/`
                    - **Method 1: VS Code SSH Remote Extension (Recommended)**
                        1. Open VS Code
                        2. Press `Ctrl+Shift+P` (or `Cmd+Shift+P` on Mac)
                        3. Type "Remote-SSH: Connect to Host"
                        4. Select your server from the list or enter connection details
                        5. Once connected to server, open terminal in VS Code
                        6. Navigate to server root: `cd ~`
                        7. Create Admin-Server directory: `mkdir -p ~/Admin-Server`
                        8. Copy Admin-Server files from local to server:
                            - Open local folder with Admin-Server files
                            - Select all files in `1-Current-Versions/Admin-Server/`
                            - Copy and paste to server `~/Admin-Server/` directory
                        9. Verify installation: `ls -la ~/Admin-Server/`
                    - **Method 2: SCP Command (Alternative)**
                        
                        ```bash
                        scp -r ./1-Current-Versions/Admin-Server/ user@server:~/Admin-Server/
                        ssh user@server "chmod +x ~/Admin-Server/2-Operations/1-deploy/*.sh"
                        
                        ```
                        
                    
                    **Verification:**
                    
                    - [ ]  Admin-Server installed in `~/Admin-Server/` on server
                    - [ ]  All shell scripts have executable permissions
                    - [ ]  Directory structure matches local version
                    - [ ]  Server can access all Admin-Server modules
                2. Ensure **ALL shell scripts** in the Admin-Server directory have executable permissions
                    
                    ```bash
                    # Navigate to Admin-Server directory
                    cd ~/Admin-Server
                    pwd
                    
                    # Set executable permissions for ALL shell scripts recursively
                    find . -name "*.sh" -type f -exec chmod +x {} \;
                    # Alternative single command (does the same thing)
                    # chmod +x $(find ~/Admin-Server -name "*.sh" -type f)
                    
                    # Verification Command
                    # Verify all .sh files are executable
                    find ~/Admin-Server -name "*.sh" -type f -exec ls -la {} \;
                    # You should see 'x' permissions like: -rwxr-xr-x
                    
                    ```
                    
                3. Push Server Git and push to Github 
                    1. **Navigate to server root directory** 
                        
                        ```bash
                        cd ~
                        pwd
                        ```
                        
                    2. **Stage Admin-Server for commit** 
                        
                        ```bash
                        # Add Admin-Server directory and all its contents
                        git add Admin-Server/
                        
                        ```
                        
                    3. **Commit Admin-Server installation  and Push**
                        
                        ```bash
                        git commit -m "Added: Admin-Server 
                        - Added Admin-Server to ~/Admin-Server/ for server-level deployment management
                        - All shell scripts configured with executable permissions
                        - Ready for multi-project deployment operations"
                        
                        # Push to the main branch of your server repository
                        git push origin main
                        
                        ```
                        
                    
                
                ### 
                
            - 4- Make sh files in `Server-Admin` executable and Secure them (`700`)
                1. make all shell scripts in Admin-Server executable and secure them 
                    
                    ```bash
                    find ~/Admin-Server -name "*.sh" -type f -exec chmod 700 {} \;
                    ```
                    
                2. make all shell scripts in Admin-Server executable with secure permissions  `700`
                    
                    ```bash
                    find /home/u164914061/Admin-Server -name "*.sh" -type f -exec chmod 700 {} \;
                    ```