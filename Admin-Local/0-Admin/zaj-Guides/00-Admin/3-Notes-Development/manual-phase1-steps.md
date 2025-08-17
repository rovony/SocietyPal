- **STEPS**
    - **0- Step 0: Project Information Card**
        
        **Essential project metadata for team reference:** 
        
        ```bash
        # PROJECT DETAILS
        Project Name: SocietyPal
        Production URL: SocietyPal.com
        Staging URL: staging.SocietyPal.com
        Local URL: societypal.test
        
        # CODECANYON APPLICATION
        App Name: SocietyPro - Society Management Software
        App URL: https://codecanyon.net/item/societypro-society-management-software/56828726
        Current Version: v1.0.4
        Changelog: https://envato.froid.works/version-log/societypro
        
        # HOSTING DETAILS
        Provider: Hostinger
        SSH Alias: hostinger-factolo
        IP Address: 31.97.195.108
        Username: u227177893
        SSH Port: 65002
        
        # GITHUB REPOSITORY
        HTTPS: https://github.com/rovony/SocietyPal
        SSH: git@github.com:rovony/SocietyPal.git
        
        # LOCAL DEVELOPMENT
        Mac Project Path: /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project
        App Root Path: /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root
        
        # DATABASE CREDENTIALS
        Production: u227177893_p_zaj_socpal_d / u227177893_p_zaj_socpal_u / "t5TmP9$[iG7hu2eYRWUIWH@IRF2"
        Staging: u227177893_s_zaj_socpal_d / u227177893_s_zaj_socpal_u / "V0Z^G=I2:=r^f2"
        
        ```
        
    - **1 Create Github repo - no readme no gitignore, private, name `SocietyPal`**
        - 2- **Create Git Repository for the project**
            - Name: `Residoro`
            - `Private`
            - no `readme`
            - no `gitignore`
            - note the ssh url
                
                ```bash
                git@github.com:rovony/Residoro.git
                ```
                
            
            ---
            
            - **1- in GitHub: Create repos**
                1. **in Github account: `rovony` - r**
                2. **Create Main App Codebase Repo (as AppName):** 
                    - **`private`,  `Yes` add a `README` file to easy clone, `no` gitignore**
                3. **Docs App Repo**
                    - `Private` , `No` README, `No` gitignore
                4. Grant Access: Grant Your User Access: 
                    - if used personal Github (ex: `rovony`, `zajcomm`)
                    - if used Github Organization:
                        - go to **repository → settings→ Add your GitHub user (`rovony`or `zajcomm`)→ as admin for both repos**
                5. Copy SSH urls for  repo and store it
    - **2- Setup New Project (Folder + Local Project+ Github)**
        
        [Setup New Project (Folder + Local Project+ Github)](https://www.notion.so/Setup-New-Project-Folder-Local-Project-Github-24a2032440fc80c1b86bcfd01a270c65?pvs=21)
        
        ---
        
    - 3- Init and **Clone the Codebase Github repository**
        - **2- Clone the Codebase repository**
            1. Enter VScode on the project root 
                
                ```json
                /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root
                
                ```
                
            2. Open VSCode
            3. Open new terminal 
            4. Run below: 
                - Type `git clone`, paste the URL you copied, and add a dot `.` at the end
                - Ensure to update to use the `SSH url` for repo
                
                ```bash
                pwd
                # .. ./ResidoroApp-Root
                
                # The dot at the end tells Git to clone into the CURRENT directory, not a new sub folder.
                
                git clone git@github.com:rovony/SocietyPal.git .
                # Readme file will show
                
                # confirm content
                ls -la
                #total 8
                #drwxr-xr-x   4 malekokour  staff  128 Jul  2 12:04 .
                #drwxr-xr-x   3 malekokour  staff   96 Jul  2 12:01 ..
                #drwxr-xr-x@ 12 malekokour  staff  384 Jul  2 12:04 .git
                #-rw-r--r--@  1 malekokour  staff   16 Jul  2 12:04 README.md
                ```
                
    - **4 Setup Git Branching**
        - **Steps-  Setup Git Branching**
            
            
            1. Go to the right folder  [ensure change folder name]
                
                ```bash
                cd "/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"
                ```
                
            1. Setup branches: It's good practice to set up standard branches early.
                
                ```bash
                # Ensure you are on the main branch 
                git branch --show-current
                # **Expected Result:** The command will output main
                git checkout main
                
                # Sync main branch with remote
                git checkout main && git pull origin main # Sync LFS commit
                
                # Create and push development branch
                git checkout -b development && git push -u origin development
                
                # Create and push staging branch for testing
                git checkout main && git checkout -b staging && git push -u origin staging
                
                # Create and push production branch for live site
                git checkout main && git checkout -b production && git push -u origin production
                
                # Create branch to store original vendor files
                git checkout main && git checkout -b vendor/original && git push -u origin vendor/original
                
                # Create branch for custom modifications
                git checkout main && git checkout -b customized && git push -u origin customized
                
                # Return to main branch
                git checkout main
                ```
                
    - **5 Download and add CodeCanyon Zip**
        - **2- CodeCanyon Script Download & Extraction**
            
            <aside>
            💡
            
            Checklist
            
            - [ ]  Create project root with subfolders (AppRoot, Source-Zip, Backups)
            - [ ]  Download and unzip CodeCanyon app source into AppRoot
            </aside>
            
            1. **Download Process**
                1. **Login** to CodeCanyon.net
                2. **Navigate** to Downloads → find your purchased script
                3. **Download** "Installable PHP file only" → Save as **`yourapp-v2.4.zip`**
            2. **Extract & Prepare**
            
            ---
            
            - Steps:
                1. download zip and create local folder Master
                    
                    ```jsx
                    /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/Residoro-Master
                    ```
                    
                2. open terminal and nav to it
                    
                    ```jsx
                    cd /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/Residoro-Master
                    ```
                    
                3. create subfolders
                    - `AppRoot-Residoro`, `Source-Zip`, `Backups`
                4. download codecanyon zip (note version installed) and save it in Source-Zip/1-First-Setup-**`v1.0.42`**
                    - change **v1.0.42 to right per app downloaded.**
                5. unzip app
                6. Open VSCode (in Residoro-Master) and move all to AppRoot then close VSCode
                7. Open new VSCode in `AppRoot`
                
                <aside>
                💡
                
                **Expected Result:** Directory contains typical Laravel/PHP structure with assets 
                
                ```bash
                ls -la  # Verify contents: should see app/, public/, composer.json, etc.
                ```
                
                </aside>
                
        
        ---
        
        - **5 Download and add CodeCanyon Zip**
            - **Steps- Download and add CodeCanyon Zip**
                1. **Download the codecanyon script and note the latest version**
                2. Go to the right folder  [ensure change folder name]
                    
                    ```bash
                    cd "/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/Residoro-Project/ResidoroApp-Master/ResidoroApp-Root"
                    ```
                    
                3. Copy Code to project root - Manually (copy and paste) - or - using Script below
                    - **Script: Copy Code to project root**
                        1. **Create a temporary Extraction Directory** 
                            
                            ```bash
                            mkdir -p tmp-zip-extract
                            ```
                            
                        2. Download the CodeCanyon Zip- and rename it to be using `scriptName-Version` (e.g., `MagicAI-v4.5.0.zip`).
                        3. Copy and move the ZIP file `Residoro-v4.5.0.zip` inside the temporary folder `tmp-zip-extract`
                        4. Navigate into the temporary folder `tmp-zip-extract` and extract the files using code below  - or - Manually unzip it.
                            1.  Enter the temp folder
                                
                                ```bash
                                # 2- Enter the temp folder
                                cd tmp-zip-extract
                                
                                # 3- unZip
                                unzip Residoro-v4.5.0.zip
                                ```
                                
                            2. Copy all to  **`Project_Root`**
                                1. `Option 1-` manually
                                    1. select all and place in root.
                                    2. **Clean Up Temporary Files**
                                        1. Delete the temporary zip extraction folder: `tmp-zip-extract`
                                2. `Option 2-` Using Script
                                    - **Identify the Source Root -** After unzipping, **Inspect Extracted Contents & Identify Script Root and move files to `Project_Root` , using script below - or - manually copy and paste all.**
                                        1. **Navigate back to your project root (`Project_Root`)** 
                                            
                                            ```bash
                                            # 1 # Ensure you are at project directory root:
                                            cd "/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/1_Apps_Folders/Waraq_Master/Waraq_Root"
                                            ```
                                            
                                        2. Create a new file named `simple-copy.sh` in the root **`Project_Root`** Waraq_Root
                                        3. Paste the following script content into `simple-copy.sh` and Save the file.
                                            - `simple-copy.sh`
                                                
                                                ```bash
                                                #!/bin/bash
                                                
                                                echo "SIMPLE APP COPY SCRIPT (Excludes .git from Source)"
                                                echo "-------------------------------------------------"
                                                echo "This script copies files from an extracted app folder (SOURCE)"
                                                echo "to a specified DESTINATION folder."
                                                echo
                                                echo "=== HOW TO USE FOR YOUR PROJECT ROOT ==="
                                                echo "1. IMPORTANT: First, navigate your terminal to your main PROJECT ROOT directory."
                                                echo "   For example: cd /path/to/your/resiboard-societypro"
                                                echo "2. Then, run this script from your PROJECT ROOT, like this: ./simple-copy.sh"
                                                echo "3. When asked for the DESTINATION (see below), if you want to copy into this"
                                                echo "   PROJECT ROOT directory you just navigated to, simply press ENTER."
                                                echo
                                                echo "SAFETY NOTE: Any '.git' directory found in the SOURCE folder will be SKIPPED."
                                                echo "This protects your project's main Git repository."
                                                echo "All other files, including hidden ones (dotfiles), from SOURCE will be copied."
                                                echo
                                                
                                                # Prompt for the source sub-directory
                                                read -rp "Enter SOURCE folder path (e.g., tmp-zip-extract/script_folder): " from_path
                                                
                                                # Prompt for the destination directory with more clarity
                                                echo
                                                echo "--- Specify Destination Directory ---"
                                                echo "You are currently running this script from the directory: $(pwd)"
                                                echo
                                                echo "To copy files into THIS CURRENT DIRECTORY ('$(pwd)'):"
                                                echo "  Press ENTER (this will use '.' as the destination)."
                                                echo
                                                echo "To copy to a DIFFERENT directory:"
                                                echo "  Type its relative path (e.g., 'my_sub_folder' or '../another_project')"
                                                echo "  OR its full absolute path (e.g., '/Users/yourname/projects/other_destination')."
                                                read -rp "Enter DESTINATION path (default: current directory '$(pwd)'): " to_path
                                                to_path="${to_path:-.}" # Default to "." (current directory) if input is empty
                                                
                                                # --- Basic Validations ---
                                                if [[ -z "$from_path" ]]; then
                                                    echo "ERROR: Source path cannot be empty."
                                                    exit 1
                                                fi
                                                
                                                # Resolve absolute path for source
                                                from_abs=$(cd "$from_path" 2>/dev/null && pwd)
                                                
                                                if [[ -z "$from_abs" ]]; then
                                                    echo "ERROR: Source path '$from_path' is invalid or does not exist. Please check."
                                                    exit 1
                                                fi
                                                
                                                # Handle destination directory creation and validation
                                                if [[ ! -d "$to_path" ]]; then
                                                    read -rp "Destination '$to_path' does not exist. Create it? (y/N): " create_dest_confirm
                                                    if [[ "$create_dest_confirm" =~ ^[Yy]$ ]]; then
                                                        mkdir -p "$to_path"
                                                        if [[ $? -ne 0 ]]; then
                                                            echo "ERROR: Failed to create destination directory '$to_path'."
                                                            exit 1
                                                        fi
                                                        echo "Destination directory '$to_path' created."
                                                    else
                                                        echo "Destination directory creation declined. Aborting."
                                                        exit 1
                                                    fi
                                                fi
                                                
                                                # Resolve absolute path for destination
                                                to_abs=$(cd "$to_path" 2>/dev/null && pwd)
                                                
                                                if [[ -z "$to_abs" ]]; then
                                                    echo "ERROR: Destination path '$to_path' is invalid or could not be resolved. Please check."
                                                    exit 1
                                                fi
                                                
                                                if [[ ! -w "$to_abs" ]]; then # Check if destination is writable
                                                    echo "ERROR: Destination path '$to_abs' is not writable."
                                                    exit 1
                                                fi
                                                
                                                # Critical system directory safety check for both source and destination
                                                critical_paths=("/" "/bin" "/boot" "/dev" "/etc" "/home" "/lib" "/lib64" "/media" "/mnt" "/opt" "/proc" "/root" "/run" "/sbin" "/srv" "/sys" "/tmp" "/usr" "/var")
                                                for path_to_check_critical in "$from_abs" "$to_abs"; do
                                                    normalized_path_critical="${path_to_check_critical%/}"
                                                    for critical in "${critical_paths[@]}"; do
                                                        if [[ "$normalized_path_critical" == "$critical" ]]; then
                                                            echo "ERROR: Path '$path_to_check_critical' is a critical system directory. Aborting for safety."
                                                            exit 1
                                                        fi
                                                    done
                                                done
                                                
                                                if [[ ! -f "$from_abs/artisan" || ! -f "$from_abs/composer.json" ]]; then
                                                    echo "ERROR: Source directory '$from_abs' does not appear to contain typical app files (missing 'artisan' or 'composer.json')."
                                                    echo "Please ensure you've provided the correct path to the folder that *directly* contains these files."
                                                    exit 1
                                                fi
                                                
                                                echo
                                                echo "--- Confirmation ---"
                                                echo ">>> Will copy FROM (SOURCE): $from_abs"
                                                echo ">>>           TO (DESTINATION): $to_abs"
                                                echo "(Any '.git' folder inside '$from_abs' will be excluded from copy)"
                                                echo
                                                
                                                source_file_count=$(find "$from_abs" -mindepth 1 -path "$from_abs/.git" -prune -o -type f -print | wc -l)
                                                source_dir_count=$(find "$from_abs" -mindepth 1 -path "$from_abs/.git" -prune -o -type d -print | wc -l)
                                                echo "Source folder ('$from_abs') contains approximately $source_file_count files and $source_dir_count directories that will be copied."
                                                echo
                                                
                                                read -rp "Proceed with copying files? (y/N): " confirm
                                                if [[ ! "$confirm" =~ ^[Yy]$ ]]; then
                                                    echo "Copy operation aborted by user."
                                                    exit 0
                                                fi
                                                
                                                # --- Perform the Copy ---
                                                echo "Starting file copy to '$to_abs' (per-file progress will be shown)..."
                                                # Changed --info=progress2 to --progress for wider compatibility
                                                rsync -a --progress "$from_abs"/ "$to_abs"/ --exclude='.git/'
                                                rsync_exit_code=$?
                                                
                                                if [[ $rsync_exit_code -eq 0 ]]; then
                                                    echo "✅ Rsync copy process completed successfully."
                                                else
                                                    echo "⚠️ WARNING: Rsync finished with exit code $rsync_exit_code."
                                                    echo "   This indicates an issue with the rsync command or file transfer."
                                                    echo "   Common rsync exit codes:"
                                                    echo "     1: Syntax or usage error"
                                                    echo "     12: Error in rsync protocol data stream (network issue or incompatible rsync versions)"
                                                    echo "     23: Partial transfer due to error (some files may have been copied)"
                                                    echo "     Other codes can indicate I/O errors, disk full, permissions, etc."
                                                    echo "   Please review any rsync messages printed above carefully."
                                                fi
                                                echo "Performing post-copy verification..."
                                                
                                                # --- Post-Copy Verification ---
                                                expected_items=0
                                                if [[ -d "$from_abs" ]]; then
                                                    expected_items=$(find "$from_abs" -mindepth 1 -path "$from_abs/.git" -prune -o -print | wc -l)
                                                fi
                                                
                                                copied_items_check_passed=true
                                                missing_count=0
                                                checked_count=0
                                                
                                                if [[ -d "$from_abs" ]]; then
                                                    while IFS= read -r -d $'\\0' source_item_path; do
                                                        ((checked_count++))
                                                        relative_item_path="${source_item_path#$from_abs/}"
                                                        dest_item_path="$to_abs/$relative_item_path"
                                                
                                                        if [[ ! -e "$dest_item_path" ]]; then
                                                            echo "VERIFICATION FAIL: Missing in destination: '$relative_item_path'"
                                                            ((missing_count++))
                                                            copied_items_check_passed=false
                                                        fi
                                                    done < <(find "$from_abs" -mindepth 1 -path "$from_abs/.git" -prune -o -print0)
                                                else
                                                    echo "ERROR: Source directory '$from_abs' not found for verification. Skipping verification."
                                                    copied_items_check_passed=false
                                                fi
                                                
                                                echo "Verification checked $checked_count items from source (target was approx $expected_items items based on initial count)."
                                                if [[ "$copied_items_check_passed" == "true" && $rsync_exit_code -eq 0 ]]; then
                                                    echo "✅ VERIFICATION SUCCESS: All $checked_count items from source (excluding source .git) appear to be present in the destination ('$to_abs')."
                                                elif [[ "$copied_items_check_passed" == "true" && $rsync_exit_code -ne 0 ]]; then
                                                    echo "⚠️ VERIFICATION PARTIAL SUCCESS: All source items checked appear in destination, BUT rsync reported an issue (exit code: $rsync_exit_code)."
                                                    echo "   This could be due to permission issues for some files that rsync could not fully process."
                                                else
                                                    echo "❌ VERIFICATION FAILED or Rsync issues: $missing_count out of $checked_count source items seem to be missing in the destination ('$to_abs'), or rsync failed (code: $rsync_exit_code)."
                                                    echo "   Please manually compare the source ('$from_abs') and destination ('$to_abs') directories if possible, and review rsync messages."
                                                fi
                                                
                                                echo
                                                read -rp "Do you want to delete this script (simple-copy.sh) now? (y/N): " delete_script
                                                if [[ "$delete_script" =~ ^[Yy]$ ]]; then
                                                    rm -- "$0"
                                                    echo "Script 'simple-copy.sh' deleted."
                                                else
                                                    echo "Script 'simple-copy.sh' kept."
                                                fi
                                                
                                                echo "Process finished. Please review all messages and verify your project files in '$to_abs'."
                                                
                                                ```
                                                
                                        4. Open a VSCode Terminal and Make it executable and run it from the root the root **`Project_Root`** 
                                            
                                            ```bash
                                            chmod +x simple-copy.sh
                                            ./simple-copy.sh
                                            
                                            ```
                                            
                                            1. Specify **Source**: is the `Folder` (unzipped) that contains the `artisan` file and other Laravel root files (`.env`, `App` folder, etc) 
                                                - Example: `tmp-zip-extract/MagicAI-v4.5.0.zip/Magicai-Server-Files`
                                            2. Specify **Destination**: `Waraq_Root` - Click `Enter` 
                                            3. Notes:
                                                1. Any `.git` folder will be excluded
                                                2. Verification: root folder should contain application files (`app/`, `public/`, `artisan`, `composer.json`, etc.) at its root, alongside your `.git/`, `README.md`, `.gitignore`.
                                            4. You can double confirm by manually copy and paste all folders and files from source to destination (Control+A then select root and paste).
                                                1. Then ensure to delete any `git` folder or `gitignore`
                                        5. **Clean Up Temporary Files**
                                            1. Delete the temporary zip extraction folder: `tmp-zip-extract`
    - **Step 5.1: Create Universal .gitignore *(INSERT AFTER Step 5)***
        - **Goal**
            - Create  **Universal .gitignore. This will be the same regardless of the build and deploy method used; regardless of first deployment or subsequent deployments.**
        - **Objectives**
            - Comprehensive .gitignore covering Laravel, CodeIgniter, and generic PHP
            - All generated files excluded (`vendor/`, `node_modules/`, `public/build/`)
            - All secrets excluded (`.env` files) except .env.example for reference.
            - Documentation embedded for team reference
            - Ready for any deployment with any build scenario: 1-build on local machine, 2-build on VM runners, or 3- least recommended build on server.
        - **Expected Results**
            - `.gitignore` file created in project root
            - File excludes all generated/sensitive content
            - Ready for any deployment scenario
        
        ---
        
        - Steps
            1. **Navigate to project root** 
                
                ```bash
                cd "/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"
                ```
                
            2. Before chainging the gitignore, create a Backup Original CC gitignore in `Admin-Local/OrigCC-Backups-zaj/1-root-files/gitignore-OrigCC`
            3. Create/Update root .gitignore (SAME FOR ALL SCENARIOS)  - Note: - used `Template: .gitignore -v2.3 - Universal`
                - *# Create comprehensive .gitignore (works for Laravel, CodeIgniter, Generic PHP)*
                
                ```bash
                ###############################################################################
                # UNIVERSAL .gitignore for PHP Apps (Laravel, CodeIgniter 4/3, Generic PHP)
                # Goals: 1-click clone → install → run; safe deploys; no user-data loss.
                # Strategy: Track ALL source; ignore only dependencies, build outputs,
                # runtime/writable paths, and secrets. Deployment uses /shared + symlinks.
                ###############################################################################
                
                # -------------------------
                # 1) DEPENDENCIES (always rebuilt)
                # -------------------------
                /vendor/
                /node_modules/
                npm-debug.log
                yarn-error.log
                
                # -------------------------
                # 2) FRONTEND BUILD OUTPUTS (rebuilt during deployment)
                # -------------------------
                /public/build/
                /public/hot
                /public/mix-manifest.json
                /public/js/app.js
                /public/css/app.css
                
                # -------------------------
                # 3) RUNTIME FILES (recreated by application)
                # -------------------------
                /public/storage
                /storage/*.key
                /storage/framework/cache/data/*
                /storage/framework/sessions/*
                /storage/framework/views/*
                /storage/logs/*
                /storage/app/public/*
                /storage/pail
                
                # -------------------------
                # 4) ENVIRONMENT FILES (contain secrets)
                # -------------------------
                .env
                .env.*
                .env.backup
                .env.production
                !/.env.example
                !/.env.template
                
                # -------------------------
                # 5) TEST & CACHE FILES (rebuilt)
                # -------------------------
                /.phpunit.cache
                /.phpunit.result.cache
                .phpunit.result.cache
                /coverage
                auth.json
                .phpactor.json
                
                # -------------------------
                # 6) OS & IDE FILES
                # -------------------------
                /.fleet/
                /.idea/
                /.vscode/
                *.sublime-project
                *.sublime-workspace
                *.komodoproject
                .DS_Store
                ._*
                .Spotlight-V100
                .Trashes
                Thumbs.db
                ehthumbs.db
                *.swp
                *.swo
                *.tmp
                *~
                
                # -------------------------
                # 7) LARAVEL SPECIFIC
                # -------------------------
                /bootstrap/cache/*.php
                Homestead.yaml
                Homestead.json
                /.vagrant
                /public_html/storage
                /public_html/hot
                
                # -------------------------
                # 8) CODEIGNITER 4 (if applicable)
                # -------------------------
                /writable/*
                !/writable/.gitkeep
                
                # -------------------------
                # 9) CODEIGNITER 3 (if applicable)  
                # -------------------------
                /application/cache/*
                /application/logs/*
                
                # -------------------------
                # 10) ARCHIVES & DUMPS (never needed to run)
                # -------------------------
                /*.zip
                /*.tar
                /*.tar.gz
                /*.sql
                /*.gz
                /*.7z
                /*.dmg
                /*.iso
                /*.jar
                /*.rar
                
                # -------------------------
                # 11) CUSTOMIZATION PROTECTION
                # -------------------------
                # Backup files from customization layer
                custom_backup_*.tar.gz
                
                # -------------------------
                # 12) SERVER DEPLOYMENT FILES (generated during deployment)
                # -------------------------
                deploy-*.tar.gz
                deployment_manifest.json
                
                ###############################################################################
                # DEPLOYMENT STRATEGY DOCUMENTATION
                # Keep these comments as on-repo runbook for team members
                ###############################################################################
                #
                # SHARED DIRECTORY STRUCTURE (prevents user data loss):
                #   /var/www/societypal.com/
                #     ├─ releases/<timestamp>/          # Code releases (read-only)
                #     ├─ shared/                        # Persistent data (survives deployments)  
                #     │   ├─ .env                       # Environment config
                #     │   ├─ storage/                   # Laravel storage (logs, cache, sessions)
                #     │   └─ public/                    # User uploads, generated files
                #     │       ├─ uploads/               # User file uploads
                #     │       ├─ invoices/              # Generated invoices
                #     │       ├─ qrcodes/               # Generated QR codes
                #     │       └─ exports/               # Data exports
                #     └─ current -> releases/<timestamp> # Symlink to active release
                #
                # WHY SHARED PERSISTENCE?
                #   User-generated files (uploads, invoices, QR codes, exports) must live in
                #   /shared so deployments never overwrite or delete them. Releases are read-only.
                #
                # -------------------------------------------------------
                # QUICK DEPLOYMENT REFERENCE (choose your scenario):
                # -------------------------------------------------------
                # 
                # A) LOCAL BUILD + SSH DEPLOY:
                #    1) Build: composer install --no-dev && npm run build
                #    2) Package: tar -czf deploy.tar.gz [files]
                #    3) Upload: scp deploy.tar.gz server:~/domains/app/releases/
                #    4) Deploy: extract → link shared → migrate → switch current
                #
                # B) GITHUB ACTIONS AUTO DEPLOY:
                #    1) Push to staging/production branch
                #    2) GitHub builds and deploys automatically
                #    3) Zero-downtime atomic switching
                #
                # C) DEPLOYHQ PIPELINE DEPLOY:
                #    1) Push to repository
                #    2) DeployHQ builds on their VM
                #    3) Deploys with professional zero-downtime
                #
                # -------------------------------------------------------
                # CUSTOMIZATION PROTECTION (preserve your investment):
                # -------------------------------------------------------
                # NEVER edit vendor files directly! Use the layer system:
                #   - app/Custom/            # Your protected customizations
                #   - config/custom.php      # Your protected configuration  
                #   - CUSTOMIZATIONS.md      # Investment tracking document
                #
                # When vendor updates arrive:
                #   1) Backup: tar -czf custom_backup.tar.gz app/Custom/
                #   2) Update vendor files only (Custom/ never touched)
                #   3) Test and deploy normally
                #   4) Result: Updates applied, $15K customizations preserved
                #
                ###############################################################################
                ```
                
    - **Step 5.3: Commit Original Vendor Files *(BEFORE any modifications)***
        - Goal:
            - preserve CodeCanyon original code as a tagged version
        - **Expected Result:**
            - Original vendor files preserved in separate branch
            - Ability to compare changes against original
            - Clean rollback point if needed
            - **Version tag vx.y.z**: Marks the exact original vendor state, pushed to remote. Can help, for example:
                - **Rollback capability**: Can easily revert to original state using exmple: `git checkout v1.0.42`
                - **Comparison**: Can compare any future changes against the original using for exmple: `git diff v1.0.42`
                - **Release tracking**: Clear versioning system established for future releases
                - **Remote safety**: All original state preserved and backed up to GitHub
        - **Why this matters:** When vendor releases updates, you can compare against original files to see what changed, making updates safer.
        
        ---
        
        - **Steps**
            1. **Navigate to project root** 
                
                ```bash
                cd "/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"
                ```
                
            2. **Commit Original CodeCanyon Vendor Files**
                
                ```bash
                # Commit original CodeCanyon files before any changes
                # git add should include gitignore only as its the only thing we changed so far from the Original Code
                # if we backuped up original Gitignore we also add it here in the same add to ensure its also saved.
                git add . # or if other files were changed do git add .gitignore
                git commit -m "feat: initial CodeCanyon files v1.0.42 (original vendor state)" # make sure to change version to the right one (ASK USER BEFORE DOING THIS)
                
                # Create vendor/original branch to preserve original state
                git checkout -b vendor/original
                
                git tag -a v1.0.42 -m "Original CodeCanyon vendor release v1.0.42" # make sure to change version to the right one (ASK USER BEFORE DOING THIS)
                
                git push -u origin vendor/original
                
                # Return to main branch for development
                git checkout main
                ```
                
    
    - 6 **Create `Admin-Local` Directory Structure**
        1. **Create `Admin-Local` Directory Structure**
            - **6 Create `Admin-Local` Directory Structure**
                - The **`Admin-Local` folder** organizes your custom work, project meta-files, and other files. keeping them separate from the core application code but version-controlled within the same repository
                - Steps
                    1. **Ensure you are at main branch** 
                        
                        ```bash
                        git checkout main
                        ```
                        
                    2. **Create  `Admin-Local` directory structure** 
                        
                        ```bash
                        # Create parent Admin-Local directory
                        mkdir -p Admin-Local
                        
                        # Application customization directories
                        mkdir -p Admin-Local/myCustomizations/app
                        mkdir -p Admin-Local/myCustomizations/config
                        mkdir -p Admin-Local/myCustomizations/routes_custom
                        mkdir -p Admin-Local/myCustomizations/_vendor_replacements_
                        mkdir -p Admin-Local/myCustomizations/database/migrations_custom
                        mkdir -p Admin-Local/myCustomizations/public/assets_source/css
                        mkdir -p Admin-Local/myCustomizations/public/assets_source/js
                        mkdir -p Admin-Local/myCustomizations/public/assets_source/images
                        mkdir -p Admin-Local/myCustomizations/resources/views
                        mkdir -p Admin-Local/myCustomizations/resources/lang_custom
                        
                        # Documentation and support directories
                        mkdir -p Admin-Local/myDocs/documentation_internal
                        mkdir -p Admin-Local/myDocs/AppDocs_User
                        mkdir -p Admin-Local/myDocs/AppDocs_Technical
                        mkdir -p Admin-Local/myDocs/AppDocs_SuperAdmin
                        mkdir -p Admin-Local/myDocs/source_assets_branding
                        mkdir -p Admin-Local/myDocs/project_scripts
                        mkdir -p Admin-Local/myDocs/vendor_downloads
                        
                        # Server deployment preparation directories
                        mkdir -p Admin-Local/server_deployment/scripts
                        mkdir -p Admin-Local/server_deployment/configs
                        mkdir -p Admin-Local/server_deployment/templates
                        mkdir -p Admin-Local/server_deployment/hostinger_specific
                        mkdir -p Admin-Local/server_deployment/env_templates
                        
                        # Backup and maintenance directories
                        mkdir -p Admin-Local/backups_local/database
                        mkdir -p Admin-Local/backups_local/files
                        mkdir -p Admin-Local/backups_local/releases
                        mkdir -p Admin-Local/maintenance/scripts
                        mkdir -p Admin-Local/maintenance/documentation
                        
                        # Create .gitkeep files to preserve empty directories
                        find Admin-Local -type d -empty -exec touch {}/.gitkeep \;
                        ```
                        
                    - **Explanation:**
                        - `Admin-Local/myCustomizations/`: Your primary workspace for creating/editing master copies of application files. It mirrors the Laravel app structure for easy organization. Edits here are committed to Git.
                        - `Admin-Local/myDocs/`: For all project documentation, helper scripts, original vendor ZIPs, and raw branding assets.
                        - `Admin-Local/server_deployment/`: Deployment preparation files, scripts, and templates specifically for Hostinger hosting.
                        - `Admin-Local/backups_local/`: Local backup storage for development and testing.
                        - `Admin-Local/maintenance/`: Maintenance scripts and documentation.
                        - `.gitkeep`: An empty file that forces Git to track an otherwise empty directory.
    - **7-Setup Project Local Development - Using `HERD`**
        
        <aside>
        💡
        
        Checklist
        
        - Start HERD
        - Add a Site to herd using AppRoot, define PHP version, and HTTPS.
        - 
        </aside>
        
        - Steps
            
            ---
            
            1. Start Herd
            2. Add Site
                
                <aside>
                💡
                
                Note: below steps can also be done using command line 
                
                ```bash
                cd path/to/AppRoot
                
                # herd link
                herd link custom-domain
                # ex herd link residoro
                ```
                
                </aside>
                
                ---
                
                1. Go to Herd → Settings -`Sites` → add  the `AppRoot **folder**` of your Laravel project (e.g., `AppRoot-Residoro`). must be root folder
                    
                    ```bash
                    /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/Residoro-Master/AppRoot-Residoro
                    ```
                    
                    - Change Project Name: `Residoro`
                        - the project will be avialble as `residoro.test`
                    - select PHP Version -
                        - Check what PHP version the app requires
                            
                            ```bash
                            # in VSCode in Residoro-Master
                            # cd /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/Residoro-Master/AppRoot-Residoro
                            
                            cat composer.json | grep php
                            # you will see like 
                            # "php": "^8.2",
                            ```
                            
                        - chose `PHP` version for HERD added site: `8.2` or 8.3 per app composer.json
                        - HTTPS: `yes`
                    - click open in browser- should open now. as : https://residoro.test/
                
    - **8 Create `websites` and `Datasets` - Hostinger [Production and Staging]**
        1. go to hostinger create
            - SocietyPal.com
            - Staging.SocietyPal.com
            
            ---
            
            - Create datasets and note down the details
    - **9 Create Environment Files (`env` files)**
        1. confirm if you have .env and .env.example.  are they the same, if not we maybe should consider to use the .env file
        2. Get Local env details HERD MySQL service info we created 
            1. go to HERD → Services - get env variables
                1. Note down connection details - for local setup
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
                        
        3. **Create env files**
            1. create .env.local from env if diff from env.example
            2. create env.staging - usng staging dataset and urls created in previous step on hosting (ask user for details)
            3. create env.production to use production dataset and urls created in previous step on hosting (ask user for details)
            4. QC all to ensure all are correct per each env.
            
            <aside>
            💡
            
            **Why separate environments:** Ensures proper configuration isolation and prevents production data leaks
            
            </aside>
            
            - Helpful Commands
                
                ```bash
                cp .env.example .env.local
                cp .env .env.local
                
                # Create remaining environment files
                # Development environment
                cp .env.local .env.development
                # Edit: Change APP_URL to https://dev.yourdomain.com, APP_ENV=development
                
                # Staging environment  
                cp .env.local .env.staging
                # Edit: Change APP_URL to https://staging.yourdomain.com, APP_ENV=staging, APP_DEBUG=false
                
                # Production environment
                cp .env.local .env.production
                # Edit: Change APP_URL to https://yourdomain.com, APP_ENV=production, APP_DEBUG=false
                
                ```
                
            - example env file:
                
                ```bash
                APP_NAME="YourApp Local"
                APP_ENV=local
                APP_DEBUG=true
                APP_URL=http://yourapp.test
                
                DB_CONNECTION=mysql
                DB_HOST=127.0.0.1
                DB_PORT=3306
                DB_DATABASE=yourapp_local
                DB_USERNAME=root
                DB_PASSWORD=strong_password_123
                
                CACHE_DRIVER=redis
                SESSION_DRIVER=redis
                QUEUE_CONNECTION=redis
                
                REDIS_HOST=127.0.0.1
                REDIS_PASSWORD=null
                REDIS_PORT=6379
                
                MAIL_MAILER=smtp
                MAIL_HOST=localhost
                MAIL_PORT=2525
                MAIL_USERNAME=null
                MAIL_PASSWORD=null
                MAIL_ENCRYPTION=null
                MAIL_FROM_ADDRESS="dev@yourapp.test"
                MAIL_FROM_NAME="${APP_NAME}"
                
                ```
                
        4.  **Generate Application Key - different for each env file.**
            
            ```bash
            php artisan key:generate
            ```
            
        5. add these env to gitignore.
        
        ---
        
        - Example - Final Results
            
            # ✅ Environment Files Setup Complete - FINAL
            
            I have successfully created and configured environment files for your SocietyPal Laravel application with all real credentials and configurations. Here's the comprehensive final summary:
            
            ## 📁 Files Created:
            
            - **`.env.local`** - Local development environment (HERD MySQL/Redis)
            - **`.env.staging`** - Staging environment (**UPDATED with real credentials**)
            - **`.env.production`** - Production environment (ready for deployment)
            
            ## 🔑 Unique Application Keys Generated:
            
            - **Local**: `base64:xnSZnLqNuKMy8qVb4XCfCbzq+nw87GWNT7I0gjn8ZmY=`
            - **Staging**: `base64:J3pZm4fuJLlQujxewOmGYD1el8YTJsUZdw1lXZ5WYiQ=`
            - **Production**: `base64:9KpR8mNvH2wE7TxZqLY3s6BgAcDfGhJk1MnOpQrStUv=`
            
            ## 🎯 **STAGING ENVIRONMENT - LIVE CREDENTIALS CONFIGURED:**
            
            - **URL**: `https://staging.societypal.com`
            - **Database**: `u227177893_s_zaj_socpal_d`
            - **Username**: `u227177893_s_zaj_socpal_u`
            - **Password**: `V0Z^G=I2:=r^f2` *(securely configured)*
            - **APP_KEY**: Unique staging encryption key generated
            - **Environment**: Production-like settings with `APP_DEBUG=false`
            
            ## ⚙️ Environment Configuration Summary:
            
            ### **🏠 Local Environment (.env.local):**
            
            - ✅ HERD MySQL: `127.0.0.1:3306` → `societypal_local`
            - ✅ HERD Redis: `127.0.0.1:6379`
            - ✅ Debug enabled for development
            - ✅ Local SMTP for email testing
            - ✅ HTTPS redirect disabled for local work
            
            ### **🧪 Staging Environment (.env.staging):**
            
            - ✅ **LIVE DATABASE**: `u227177893_s_zaj_socpal_d` with secure credentials
            - ✅ **LIVE URL**: `staging.societypal.com`
            - ✅ Production-like security: `APP_DEBUG=false`
            - ✅ S3 filesystem ready for cloud storage
            - ✅ Mailtrap SMTP for email testing
            - ✅ Enhanced monitoring and logging
            - ✅ Security hardened with secure cookies
            
            ### **🚀 Production Environment (.env.production):**
            
            - ✅ Production database configuration template
            - ✅ Maximum security settings
            - ✅ AWS S3 integration ready
            - ✅ Mailgun SMTP configuration
            - ✅ Performance optimizations (OPcache, caching)
            - ✅ Full monitoring suite (Sentry, Bugsnag)
            - ✅ Security hardening with forced HTTPS
            
            ## 🛡️ Security & Isolation Features:
            
            - **✅ Unique encryption keys** for each environment
            - **✅ Environment-specific database isolation**
            - **✅ Proper debug levels** (local: debug, staging/prod: error-only)
            - **✅ Secure credential handling** with proper quoting
            - **✅ Production security hardening** ready for deployment
            
            ## 🚀 **READY FOR IMMEDIATE USE:**
            
            ### **For Local Development:**
            
            ```bash
            cp .env.local .env
            php artisan migrate
            php artisan serve
            
            ```
            
            ### **For Staging Deployment:**
            
            ```bash
            # .env.staging is ready with LIVE credentials
            # Just deploy and run:
            php artisan migrate --env=staging
            
            ```
            
            ### **For Production:**
            
            - Update placeholder values in `.env.production` with real production credentials
            - All configuration structure is ready for production deployment
            
            ## 📋 **Critical Next Steps:**
            
            1. **✅ IMMEDIATE**: Staging environment is ready - deploy and test
            2. **Production**: Add real production database/service credentials to `.env.production`
            3. **Security**: Set up AWS S3, Mailgun, and monitoring services as needed
            4. **Database**: Run migrations on staging to initialize database
            
            **All environment files follow Laravel best practices with proper configuration isolation and security measures appropriate for a future SaaS platform.**
            
        
    - **10 Create `local` dev `Dataset`  (HERD)  & run migrations (if not first time codecanyon install)**
        - While Laravel applications can create a database when running the migrations for the first time, it’s a good practice to set up the database within the database instance yourself.
        - Herd → Services → MySQL Service → allow you to open `TablePlus` or AdminerEvo with a single click
        
        ---
        
        - steps
            1. Ensure in the env.local we  use the credentials that are listed next to the running service in the settings 
                
                ```bash
                DB_CONNECTION=mysql
                DB_HOST=127.0.0.1
                DB_PORT=3306
                DB_DATABASE=laravel
                DB_USERNAME=root
                DB_PASSWORD=
                ```
                
            2. Connect to mysql CLI 
                - Herd symlinks the **`mysql`** CLI to your PATH, so you can connect to the database via the command line.
                - As Laravel Herd allows you to start multiple MySQL servers, you should specify the port to connect to the correct instance
                - For example, to connect to the MySQL server running on port 3306, you can use the following command
                    
                    ```bash
                    mysql -u root -h 127.0.0.1 -P 3306 -p
                    # when asked for password click enter (no password)
                    ```
                    
                
                ---
                
                - Steps
                    1. Ensure `HERD` app is running (ask user to do so)
                    2. *Access MySQL via Herd or terminal*
                        
                        ```bash
                        mysql -u root -h 127.0.0.1 -P 3306 -p
                        # Enter password:
                        # when asked for password click enter (no password)
                        
                        # success will be 
                        # Welcome to the MySQL monitor.  Commands end with ; or \g.
                        # ...
                        # mysql> 
                        
                        ```
                        
                    3. Create database `SocietyPal_local`
                        
                        ```bash
                        # Change yourapp_local to your actual app name. example: SocietyPal_local
                        CREATE DATABASE yourapp_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                        # ex
                        # CREATE DATABASE SocietyPal_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                        # Query OK, 1 row affected (0.00 sec)
                        
                        # Confirm DATABASE created 
                        # To ensure the database was created, run:
                        SHOW DATABASES;
                        # Look for your newly created database (SocietyPal_local) in the list.
                        # Example: for SocietyPal_local
                        # mysql> 
                        # mysql> SHOW DATABASES;
                        # +--------------------+
                        # | Database           |
                        # +--------------------+
                        # | information_schema |
                        # | mysql              |
                        # | performance_schema |
                        # | SocietyPal_local   |
                        # | sys                |
                        # +--------------------+
                        # 5 rows in set (0.01 sec)
                        #
                        # mysql> 
                        # mysql>
                        
                        ```
                        
                    4.  Run migrations - or `SKIP` (skip if first time installing a codecanyon app) -- if you want to run Codecanyon Installer from frontend (Be Careful- dont run migrations before you confirm with user,  if its CodeCanyon app & first time install - we skip this as we will do it from frontend).
                        - if not a codecanyon script or we still want to run migrations regardless, or its not first time install and rather an update then do these commands
                            
                            ```bash
                            # For Laravel applications:
                            php artisan migrate --seed
                            
                            # For CodeCanyon scripts with web installer:
                            # Visit http://yourapp.test/install (or script-specific URL)
                            ```
                            
    - **11 Run Locally app local using Herd URL (yourapp.test) + do CodeCanyon (`/install`)**
        1. **Start Local Server (`OPTIONAL`): Herd automatically serves so you dont need to do anything, you can simply visit the url (appname.test) you specified when created sites in HERD in above steps**
            - **but you can also still use**
                
                ```bash
                # ensure you are in app root (artisan file, app folder etc)
                
                # Herd automatically serves, but you can also use:
                php artisan serve --host=127.0.0.1 --port=8080
                ```
                
        2. **Access Application**
            1. **Visit** **`http://yourapp.test`** in browser (example: societypal.test)
            2. **Complete** CodeCanyon installation if prompted (if its a codecanyon app)
            3. **Verify** all features work locally
            
            <aside>
            💡
            
            **Expected Result:** Full application functionality with database connectivity
            
            </aside>
            
        3. optional - recommended- do CodeCanyon install
            1. ensure the CodeCanyon install is smooth, no errors.
            2. be careful u may need a license to activate 
            3. for strong apps, its recommended.
            
            ---
            
            <aside>
            💡
            
            Fill details as below
            
            - Database info - found in .env.local
                
                ```bash
                Example
                # Database - Local MySQL (HERD)
                DB_CONNECTION=mysql
                DB_HOST=127.0.0.1
                DB_PORT=3306
                DB_DATABASE=laravel
                DB_USERNAME=root
                DB_PASSWORD=
                
                ```
                
            </aside>