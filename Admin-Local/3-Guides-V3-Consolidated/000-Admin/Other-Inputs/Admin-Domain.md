# Setup New Project on Server  [**`Admin-Domain`]**

1. Define variables
    
    
    1. Define Domains 
        
        ```bash
        Production_Domain="**residoro**.com"
        Staging_Domain="staging.**residoro**.**com**"
        Test_Domain="test.**residoro**.**com**"
        ```
        
    
    1. **Navigate to Domain root - `test.residoro.com`**
        
        ```bash
        # Navigate to your Domain root on server-
        cd ~/domains/test.**residoro.com**
        pwd
        ```
        
2. Create `Project` new websites (on hostinger UI)
    1. go to hostinger → create new website :ex: `Residoro.com`, `test.Residoro.com`. This creates the `domains` folders (`/domains/residoro.com`,`/domains/test.residoro.com`)  for these websites
3. **Enter SSH Hosting**
    - **1-Enter SSH Hosting**
        1.  **Connect to Server using code below or `Remote` extension**
            
            ```bash
            # Connect via VSCode Remote SSH or terminal
            ssh hostinger-factolo
            # OR your configured server alias
            
            ```
            
4. Commit, Push `Clean` versions of the added websites
    - Steps
        
        Push clean version of the `Server` Github rep (not project-Server) but rather root Server repo)- These are: `Clean` versions of the added websites 
        
        Navigate to Root of server, commit and push
        
        ```bash
        cd ~
        ```
        
        ```bash
        # Commit
        
        ✅ New Project- Residoro.com - V0 - Websites Created ✅
        ```
        
5. **Setup Domain Directory (`Admin-Domain`**)  
    
    `do any of the below needed` (do for the ones u have website)
    
    - **Setup Domain Directory - `test` domain**
        - **Understanding the Directory Architecture- Goal is to setup below**
            - **Root Level Structure (per domain)**
                
                ```bash
                ~/domains/staging.waraq.ai/
                ├── Admin-Domain/          # Domain management & deployment tools
                ├── releases/              # Zero-downtime deployment releases
                └── shared/               # Persistent data across deployments
                ```
                
            - **Admin-Domain Structure:**
                
                ```bash
                Admin-Domain/
                ├── 0_Admin/
                │   ├── 1_General/
                │   │   └── 1_DomainSetup/
                │   └── 2_Deployments/
                │       ├── 1_Guides/
                │       ├── 2_Scripts/
                │       │   ├── 0_First_Deployment_Only/
                │       │   ├── 1_All_Deployments/
                │       │   │   ├── 1-Preperation/
                │       │   │   ├── 2-Deployment/
                │       │   │   ├── 3-Activation/
                │       │   │   └── 4-Post-Deployment/
                │       │   └── 9_Utilities/
                │       ├── 3_Templates/
                │       ├── 4_Config/
                │       └── 5_Misc/
                ├── 1_GitClone/           # Repository cloning area
                ├── 2_Deployments/        # Deployment history tracking
                ├── 3_Domain_Logs/        # Centralized logging
                ├── 4_Domain_SnapShots/   # Backup snapshots
                ├── 5_Maintenance/        # Maintenance tools
                ├── 6_Monitoring/         # Monitoring configurations
                └── 7_SSL_Certs/         # SSL certificate management
                
                ```
                
            - **Shared Structure (Laravel-specific):**
                
                ```bash
                shared/
                ├── .env                  # Environment configuration
                ├── bootstrap/cache/      # Bootstrap cache
                ├── public/
                │   ├── qrcodes/         # QR code storage
                │   ├── storage/         # Public storage symlink
                │   └── user-uploads/    # User file uploads
                └── storage/
                    ├── app/public/      # App public storage
                    ├── framework/       # Framework cache & sessions
                    │   ├── cache/
                    │   ├── sessions/
                    │   └── views/
                    └── logs/           # Application logs
                
                ```
                
        
        ---
        
        1. **Navigate to Domain root - `test.residoro.com`**
            
            ```bash
            # Navigate to your Domain root on server-
            cd ~/domains/test.**residoro.com**
            pwd
            ```
            
        2. **Run Command below- Create Directory** 
            1. Create directory for setup script
                
                ```bash
                mkdir -p Admin-Domain/0_Admin/1_General/1_DomainSetup
                ```
                
            2. Create comprehensive directory setup script
                
                ```bash
                cat > Admin-Domain/0_Admin/1_General/1_DomainSetup/1_Setup_Domain_Folders.sh << 'EOF'
                #!/bin/bash
                echo "🏗️  Creating enhanced server directory structure ..."
                
                # Core directories at root
                mkdir -p releases
                mkdir -p shared
                
                # Shared Laravel directories with proper permissions
                mkdir -p shared/bootstrap/cache
                mkdir -p shared/public/user-uploads
                mkdir -p shared/public/qrcodes
                mkdir -p shared/public/storage
                mkdir -p shared/storage/app/public
                mkdir -p shared/storage/framework/cache
                mkdir -p shared/storage/framework/sessions
                mkdir -p shared/storage/framework/views
                mkdir -p shared/storage/logs
                
                # Create secure .env file
                touch shared/.env
                chmod 600 shared/.env
                
                # Security hardening for public directories
                for dir in shared/public/user-uploads shared/public/qrcodes; do
                    echo "Options -Indexes" > "$dir/.htaccess"
                    echo "<?php http_response_code(404); ?>" > "$dir/index.php"
                done
                
                # Admin-Domain structure
                mkdir -p Admin-Domain/0_Admin/1_General/1_DomainSetup
                mkdir -p Admin-Domain/0_Admin/2_Deployments/1_Guides
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/0_First_Deployment_Only
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/1_All_Deployments/1-Preperation
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/1_All_Deployments/2-Deployment
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/1_All_Deployments/3-Activation
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/1_All_Deployments/4-Post-Deployment
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/9_Utilities
                mkdir -p Admin-Domain/0_Admin/2_Deployments/3_Templates
                mkdir -p Admin-Domain/0_Admin/2_Deployments/4_Config
                mkdir -p Admin-Domain/0_Admin/2_Deployments/5_Misc
                
                # Management directories
                mkdir -p Admin-Domain/1_GitClone
                mkdir -p Admin-Domain/2_Deployments
                
                # Logging architecture
                mkdir -p Admin-Domain/3_Domain_Logs/1_Access
                mkdir -p Admin-Domain/3_Domain_Logs/2_Error
                mkdir -p Admin-Domain/3_Domain_Logs/3_Application
                mkdir -p Admin-Domain/3_Domain_Logs/4_Deployment
                
                # Operational directories
                mkdir -p Admin-Domain/4_Domain_SnapShots
                mkdir -p Admin-Domain/5_Maintenance
                mkdir -p Admin-Domain/6_Monitoring
                mkdir -p Admin-Domain/7_SSL_Certs
                
                echo "✅ Directory structure created successfully for Waraq domain"
                echo "📊 Structure verification:"
                tree -L 3 . 2>/dev/null || find . -type d | sort
                EOF
                ```
                
            3. Make script executable
                
                ```bash
                # Make script executable
                chmod +x Admin-Domain/0_Admin/1_General/1_DomainSetup/1_Setup_Domain_Folders.sh
                ```
                
        3. **Execute Directory Creation Script** 
            1. **Navigate to Domain root - `test.residoro.com`**
                
                ```bash
                # Navigate to your Domain root on server-
                cd ~/domains/test.**residoro.com**
                pwd
                ```
                
            2. Run the structure creation script
                
                ```bash
                ./Admin-Domain/0_Admin/1_General/1_DomainSetup/1_Setup_Domain_Folders.sh
                
                ```
                
                - Results: `28` directories, `2` files
        
        4. **Verify Staging Structure-update domain below**
        
        1. Run 
            1. Define Domains 
                
                ```bash
                Production_Domain="**residoro**.com"
                Staging_Domain="staging.**residoro**.**com**"
                Test_Domain="test.**residoro**.**com**"
                ```
                
        2. Run 
            
            ```bash
            echo "===  STRUCTURE VERIFICATION ==="
            cd ~/domains/$Test_Domain && pwd
            tree -L 2 . 2>/dev/null || find . -type d | head -20
            ```
            
            - u should see tree of folders and files: `15` directories, `2` files
    - **Setup Domain Directory - `staging` domain (check inside)**
        - **Understanding the Directory Architecture- Goal is to setup below**
            - **Root Level Structure (per domain)**
                
                ```bash
                ~/domains/staging.waraq.ai/
                ├── Admin-Domain/          # Domain management & deployment tools
                ├── releases/              # Zero-downtime deployment releases
                └── shared/               # Persistent data across deployments
                ```
                
            - **Admin-Domain Structure:**
                
                ```bash
                Admin-Domain/
                ├── 0_Admin/
                │   ├── 1_General/
                │   │   └── 1_DomainSetup/
                │   └── 2_Deployments/
                │       ├── 1_Guides/
                │       ├── 2_Scripts/
                │       │   ├── 0_First_Deployment_Only/
                │       │   ├── 1_All_Deployments/
                │       │   │   ├── 1-Preperation/
                │       │   │   ├── 2-Deployment/
                │       │   │   ├── 3-Activation/
                │       │   │   └── 4-Post-Deployment/
                │       │   └── 9_Utilities/
                │       ├── 3_Templates/
                │       ├── 4_Config/
                │       └── 5_Misc/
                ├── 1_GitClone/           # Repository cloning area
                ├── 2_Deployments/        # Deployment history tracking
                ├── 3_Domain_Logs/        # Centralized logging
                ├── 4_Domain_SnapShots/   # Backup snapshots
                ├── 5_Maintenance/        # Maintenance tools
                ├── 6_Monitoring/         # Monitoring configurations
                └── 7_SSL_Certs/         # SSL certificate management
                
                ```
                
            - **Shared Structure (Laravel-specific):**
                
                ```bash
                shared/
                ├── .env                  # Environment configuration
                ├── bootstrap/cache/      # Bootstrap cache
                ├── public/
                │   ├── qrcodes/         # QR code storage
                │   ├── storage/         # Public storage symlink
                │   └── user-uploads/    # User file uploads
                └── storage/
                    ├── app/public/      # App public storage
                    ├── framework/       # Framework cache & sessions
                    │   ├── cache/
                    │   ├── sessions/
                    │   └── views/
                    └── logs/           # Application logs
                
                ```
                
        
        ---
        
        1. **Navigate to Domain root - `staging.residoro.com`**
            
            ```bash
            # Navigate to your Domain root on server-
            cd ~/domains/**staging**.**residoro.com**
            pwd
            ```
            
        2. **Run Command below- Create Directory** 
            1. Create directory for setup script
                
                ```bash
                mkdir -p Admin-Domain/0_Admin/1_General/1_DomainSetup
                ```
                
            2. Create comprehensive directory setup script
                
                ```bash
                cat > Admin-Domain/0_Admin/1_General/1_DomainSetup/1_Setup_Domain_Folders.sh << 'EOF'
                #!/bin/bash
                echo "🏗️  Creating enhanced server directory structure ..."
                
                # Core directories at root
                mkdir -p releases
                mkdir -p shared
                
                # Shared Laravel directories with proper permissions
                mkdir -p shared/bootstrap/cache
                mkdir -p shared/public/user-uploads
                mkdir -p shared/public/qrcodes
                mkdir -p shared/public/storage
                mkdir -p shared/storage/app/public
                mkdir -p shared/storage/framework/cache
                mkdir -p shared/storage/framework/sessions
                mkdir -p shared/storage/framework/views
                mkdir -p shared/storage/logs
                
                # Create secure .env file
                touch shared/.env
                chmod 600 shared/.env
                
                # Security hardening for public directories
                for dir in shared/public/user-uploads shared/public/qrcodes; do
                    echo "Options -Indexes" > "$dir/.htaccess"
                    echo "<?php http_response_code(404); ?>" > "$dir/index.php"
                done
                
                # Admin-Domain structure
                mkdir -p Admin-Domain/0_Admin/1_General/1_DomainSetup
                mkdir -p Admin-Domain/0_Admin/2_Deployments/1_Guides
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/0_First_Deployment_Only
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/1_All_Deployments/1-Preperation
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/1_All_Deployments/2-Deployment
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/1_All_Deployments/3-Activation
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/1_All_Deployments/4-Post-Deployment
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/9_Utilities
                mkdir -p Admin-Domain/0_Admin/2_Deployments/3_Templates
                mkdir -p Admin-Domain/0_Admin/2_Deployments/4_Config
                mkdir -p Admin-Domain/0_Admin/2_Deployments/5_Misc
                
                # Management directories
                mkdir -p Admin-Domain/1_GitClone
                mkdir -p Admin-Domain/2_Deployments
                
                # Logging architecture
                mkdir -p Admin-Domain/3_Domain_Logs/1_Access
                mkdir -p Admin-Domain/3_Domain_Logs/2_Error
                mkdir -p Admin-Domain/3_Domain_Logs/3_Application
                mkdir -p Admin-Domain/3_Domain_Logs/4_Deployment
                
                # Operational directories
                mkdir -p Admin-Domain/4_Domain_SnapShots
                mkdir -p Admin-Domain/5_Maintenance
                mkdir -p Admin-Domain/6_Monitoring
                mkdir -p Admin-Domain/7_SSL_Certs
                
                echo "✅ Directory structure created successfully for Waraq domain"
                echo "📊 Structure verification:"
                tree -L 3 . 2>/dev/null || find . -type d | sort
                EOF
                ```
                
            3. Make script executable
                
                ```bash
                # Make script executable
                chmod +x Admin-Domain/0_Admin/1_General/1_DomainSetup/1_Setup_Domain_Folders.sh
                ```
                
        3. **Execute Directory Creation Script** 
            1. **Navigate to Domain root - `staging.residoro.com`**
                
                ```bash
                # Navigate to your Domain root on server-
                cd ~/domains/**staging**.**residoro.com**
                pwd
                ```
                
            2. Run the structure creation script
                
                ```bash
                ./Admin-Domain/0_Admin/1_General/1_DomainSetup/1_Setup_Domain_Folders.sh
                
                ```
                
                - Results: `28` directories, `2` files
        
        4. **Verify Staging Structure-update domain below**
        
        1. Run 
            1. Define Domains 
                
                ```bash
                Production_Domain="**residoro**.com"
                Staging_Domain="staging.**residoro**.**com**"
                Test_Domain="test.**residoro**.**com**"
                ```
                
        2. Run 
            
            ```bash
            echo "===  STRUCTURE VERIFICATION ==="
            cd ~/domains/$Staging_Domain && pwd
            tree -L 2 . 2>/dev/null || find . -type d | head -20
            ```
            
            - u should see tree of folders and files: `15` directories, `2` files
    - **Setup Domain Directory - `production` domain**
        - **Understanding the Directory Architecture- Goal is to setup below**
            - **Root Level Structure (per domain)**
                
                ```bash
                ~/domains/staging.waraq.ai/
                ├── Admin-Domain/          # Domain management & deployment tools
                ├── releases/              # Zero-downtime deployment releases
                └── shared/               # Persistent data across deployments
                ```
                
            - **Admin-Domain Structure:**
                
                ```bash
                Admin-Domain/
                ├── 0_Admin/
                │   ├── 1_General/
                │   │   └── 1_DomainSetup/
                │   └── 2_Deployments/
                │       ├── 1_Guides/
                │       ├── 2_Scripts/
                │       │   ├── 0_First_Deployment_Only/
                │       │   ├── 1_All_Deployments/
                │       │   │   ├── 1-Preperation/
                │       │   │   ├── 2-Deployment/
                │       │   │   ├── 3-Activation/
                │       │   │   └── 4-Post-Deployment/
                │       │   └── 9_Utilities/
                │       ├── 3_Templates/
                │       ├── 4_Config/
                │       └── 5_Misc/
                ├── 1_GitClone/           # Repository cloning area
                ├── 2_Deployments/        # Deployment history tracking
                ├── 3_Domain_Logs/        # Centralized logging
                ├── 4_Domain_SnapShots/   # Backup snapshots
                ├── 5_Maintenance/        # Maintenance tools
                ├── 6_Monitoring/         # Monitoring configurations
                └── 7_SSL_Certs/         # SSL certificate management
                
                ```
                
            - **Shared Structure (Laravel-specific):**
                
                ```bash
                shared/
                ├── .env                  # Environment configuration
                ├── bootstrap/cache/      # Bootstrap cache
                ├── public/
                │   ├── qrcodes/         # QR code storage
                │   ├── storage/         # Public storage symlink
                │   └── user-uploads/    # User file uploads
                └── storage/
                    ├── app/public/      # App public storage
                    ├── framework/       # Framework cache & sessions
                    │   ├── cache/
                    │   ├── sessions/
                    │   └── views/
                    └── logs/           # Application logs
                
                ```
                
        
        ---
        
        1. **Navigate to Domain root - `residoro.com`**
            
            ```bash
            # Navigate to your Domain root on server-
            cd ~/domains/**residoro.com**
            pwd
            ```
            
        2. **Run Command below- Create Directory** 
            1. Create directory for setup script
                
                ```bash
                mkdir -p Admin-Domain/0_Admin/1_General/1_DomainSetup
                ```
                
            2. Create comprehensive directory setup script
                
                ```bash
                cat > Admin-Domain/0_Admin/1_General/1_DomainSetup/1_Setup_Domain_Folders.sh << 'EOF'
                #!/bin/bash
                echo "🏗️  Creating enhanced server directory structure ..."
                
                # Core directories at root
                mkdir -p releases
                mkdir -p shared
                
                # Shared Laravel directories with proper permissions
                mkdir -p shared/bootstrap/cache
                mkdir -p shared/public/user-uploads
                mkdir -p shared/public/qrcodes
                mkdir -p shared/public/storage
                mkdir -p shared/storage/app/public
                mkdir -p shared/storage/framework/cache
                mkdir -p shared/storage/framework/sessions
                mkdir -p shared/storage/framework/views
                mkdir -p shared/storage/logs
                
                # Create secure .env file
                touch shared/.env
                chmod 600 shared/.env
                
                # Security hardening for public directories
                for dir in shared/public/user-uploads shared/public/qrcodes; do
                    echo "Options -Indexes" > "$dir/.htaccess"
                    echo "<?php http_response_code(404); ?>" > "$dir/index.php"
                done
                
                # Admin-Domain structure
                mkdir -p Admin-Domain/0_Admin/1_General/1_DomainSetup
                mkdir -p Admin-Domain/0_Admin/2_Deployments/1_Guides
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/0_First_Deployment_Only
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/1_All_Deployments/1-Preperation
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/1_All_Deployments/2-Deployment
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/1_All_Deployments/3-Activation
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/1_All_Deployments/4-Post-Deployment
                mkdir -p Admin-Domain/0_Admin/2_Deployments/2_Scripts/9_Utilities
                mkdir -p Admin-Domain/0_Admin/2_Deployments/3_Templates
                mkdir -p Admin-Domain/0_Admin/2_Deployments/4_Config
                mkdir -p Admin-Domain/0_Admin/2_Deployments/5_Misc
                
                # Management directories
                mkdir -p Admin-Domain/1_GitClone
                mkdir -p Admin-Domain/2_Deployments
                
                # Logging architecture
                mkdir -p Admin-Domain/3_Domain_Logs/1_Access
                mkdir -p Admin-Domain/3_Domain_Logs/2_Error
                mkdir -p Admin-Domain/3_Domain_Logs/3_Application
                mkdir -p Admin-Domain/3_Domain_Logs/4_Deployment
                
                # Operational directories
                mkdir -p Admin-Domain/4_Domain_SnapShots
                mkdir -p Admin-Domain/5_Maintenance
                mkdir -p Admin-Domain/6_Monitoring
                mkdir -p Admin-Domain/7_SSL_Certs
                
                echo "✅ Directory structure created successfully for Waraq domain"
                echo "📊 Structure verification:"
                tree -L 3 . 2>/dev/null || find . -type d | sort
                EOF
                ```
                
            3. Make script executable
                
                ```bash
                # Make script executable
                chmod +x Admin-Domain/0_Admin/1_General/1_DomainSetup/1_Setup_Domain_Folders.sh
                ```
                
        3. **Execute Directory Creation Script** 
            1. **Navigate to Domain root - `residoro.com`**
                
                ```bash
                # Navigate to your Domain root on server-
                cd ~/domains/**residoro.com**
                pwd
                ```
                
            2. Run the structure creation script
                
                ```bash
                ./Admin-Domain/0_Admin/1_General/1_DomainSetup/1_Setup_Domain_Folders.sh
                
                ```
                
                - Results: `28` directories, `2` files
        
        4. **Verify Staging Structure-update domain below**
        
        1. Run 
            1. Define Domains 
                
                ```bash
                Production_Domain="**residoro**.com"
                Staging_Domain="staging.**residoro**.**com**"
                Test_Domain="test.**residoro**.**com**"
                ```
                
        2. Run 
            
            ```bash
            echo ""
            echo "=== PRODUCTION STRUCTURE VERIFICATION ==="
            cd ~/domains/$Production_Domain && pwd
            tree -L 2 . 2>/dev/null || find . -type d | head -20
            ```
            
            - u should see tree of folders and files: `15` directories, `2` files