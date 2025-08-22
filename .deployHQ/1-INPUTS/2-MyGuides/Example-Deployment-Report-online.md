Deployment Report
Target
Deployment to Production Server
Timestamp
Finished at Aug 3rd @ 05:58
Time Taken
Took 1 minutes 37 seconds
Deployer
Zaj Apps’s Avatar
Started by Zaj Apps
Status
Completed
Start Commit
Fetching commit c85c7c09

End Commit
Fetching commit c85c7c09

Preparing

Waiting for an available deployment slot


Performing pre-deployment checks

Checking access to repository

Checking start and end revisions are valid

Checking connection to server Production Server


Preparing repository for deployment

Updating repository from git@github.com:rovony/Residoro.git

Getting information for start commit c85c7c

Getting information for end commit c85c7c

Building

Checking out repository for deployment

Checking out working copy of your repository at c85c7c (development)


Generating deployment manifest


Storing artifacts

Skipped due to project/account settings

Transferring

Running SSH command A01-System-Preflight-Checks

Executing A01-System-Preflight-Checks [#!/bin/bash # A01: System Pre-flight Checks # Execution: Every deployment # Purpose: Validate server environment and requirements # Timeout: 5 minutes echo "=== System Pre-flight Checks ===" REQUIRED_COMMANDS=("php" "git" "composer" "curl" "find" "sed" "grep" "date" "touch" "rm" "ln" "mkdir" "chmod" "du" "tail" "xargs" "wc" "awk" "df") FAILED_CHECKS=0 for cmd in "${REQUIRED_COMMANDS[@]}"; do if ! command -v "$cmd" &> /dev/null; then echo "⚠️ WARNING: Required command '$cmd' not found." FAILED_CHECKS=$((FAILED_CHECKS + 1)) else echo "✅ Command '$cmd' found" fi done if command -v php &> /dev/null; then PHP_VERSION=$(php -r "echo PHP_VERSION;") if ! php -r "exit(version_compare(PHP_VERSION, '8.0.0', '>=') ? 0 : 1);" &> /dev/null; then echo "⚠️ WARNING: PHP version $PHP_VERSION < 8.0.0" FAILED_CHECKS=$((FAILED_CHECKS + 1)) else echo "✅ PHP version $PHP_VERSION meets requirements" fi fi MIN_DISK_KB=1048576 AVAILABLE_DISK_KB=$(df -k "/home/u164914061/domains/deployhqtest.zajaly.com/deploy" 2>/dev/null | awk 'NR==2 {print $4}' || echo "999999999") if [ "$AVAILABLE_DISK_KB" -lt "$MIN_DISK_KB" ]; then echo "⚠️ WARNING: Low disk space available" FAILED_CHECKS=$((FAILED_CHECKS + 1)) else echo "✅ Sufficient disk space available" fi if [ "$FAILED_CHECKS" -ne 0 ]; then echo "⚠️ Pre-flight completed with $FAILED_CHECKS warning(s)" else echo "✅ All pre-flight checks passed" fi exit 0]

=== System Pre-flight Checks ===
 Command 'php' found
 Command 'git' found
 Command 'composer' found
 Command 'curl' found
 Command 'find' found
 Command 'sed' found
 Command 'grep' found
 Command 'date' found
 Command 'touch' found
 Command 'rm' found
 Command 'ln' found
 Command 'mkdir' found
 Command 'chmod' found
 Command 'du' found
 Command 'tail' found
 Command 'xargs' found
 Command 'wc' found
 Command 'awk' found
 Command 'df' found
 PHP version 8.2.28 meets requirements
 Sufficient disk space available
 All pre-flight checks passed
Time taken to run A01-System-Preflight-Checks: 0.5 seconds


Running SSH command A02: Backup Current Release-v2

Executing A02: Backup Current Release-v2 [#!/bin/bash # A02: Backup Current Release (FIXED VERSION) # Execution: Every deployment # Purpose: Backup current release files and prepare backup directories # Timeout: 5 minutes echo "=== Backup Current Release ===" # Use DeployHQ's /home/u164914061/domains/deployhqtest.zajaly.com/deploy variable for reliability DEPLOY_BASE="/home/u164914061/domains/deployhqtest.zajaly.com/deploy" SHARED_PATH="$DEPLOY_BASE/shared" CURRENT_PATH="$DEPLOY_BASE/current" # Create backup directory structure BACKUP_ROOT="$SHARED_PATH/backups" DEPLOYMENT_TIMESTAMP=$(date +"%Y%m%d_%H%M%S") COMMIT_HASH=$(echo "%commit%" | cut -c1-7) CURRENT_BACKUP_DIR="$BACKUP_ROOT/deploy_production_${DEPLOYMENT_TIMESTAMP}_${COMMIT_HASH}" echo "Creating backup directories..." mkdir -p "$BACKUP_ROOT" || { echo "❌ Failed to create backup root"; exit 1; } mkdir -p "$CURRENT_BACKUP_DIR" || { echo "❌ Failed to create deployment backup dir"; exit 1; } # Backup current release files (if current release exists) if [ -L "$CURRENT_PATH" ] && [ -d "$CURRENT_PATH" ]; then echo "? Backing up current release files..." # Get the actual release directory name CURRENT_RELEASE=$(basename "$(readlink "$CURRENT_PATH")") BACKUP_NAME="release_${CURRENT_RELEASE}_backup_$(date +%Y%m%d_%H%M%S)" echo "Creating backup of current release: $CURRENT_RELEASE" # Create a compressed backup of the current release cd "$DEPLOY_BASE/releases" || exit 1 if tar -czf "$CURRENT_BACKUP_DIR/$BACKUP_NAME.tar.gz" "$CURRENT_RELEASE" 2>/dev/null; then BACKUP_SIZE=$(du -sh "$CURRENT_BACKUP_DIR/$BACKUP_NAME.tar.gz" | cut -f1) echo "✅ Current release backed up: $BACKUP_NAME.tar.gz ($BACKUP_SIZE)" else echo "⚠️ Failed to create release backup, continuing..." fi else echo "ℹ️ No current release to backup (first deployment)" fi # Handle Hostinger public_html backup (universal detection) cd "$DEPLOY_BASE" || exit 1 if [ -d "public_html" ] && [ ! -L "public_html" ]; then echo "? Hostinger detected: Backing up existing public_html directory..." BACKUP_NAME="public_html.backup.$(date +%Y%m%d_%H%M%S)" mv public_html "$CURRENT_BACKUP_DIR/$BACKUP_NAME" echo "✅ public_html backed up to: $CURRENT_BACKUP_DIR/$BACKUP_NAME" else echo "ℹ️ No public_html directory to backup (cPanel or already symlinked)" fi # Set permissions chmod 755 "$BACKUP_ROOT" chmod 755 "$CURRENT_BACKUP_DIR" # Store backup path for other commands (A03 database backup) echo "$CURRENT_BACKUP_DIR" > "$SHARED_PATH/.current_backup_dir" echo "✅ Backup system initialized: $CURRENT_BACKUP_DIR" ]

=== Backup Current Release ===
Creating backup directories...
? Backing up current release files...
Creating backup of current release: 20250802221641
 Current release backed up: release_20250802221641_backup_20250803_055719.tar.gz (55M)
 No public_html directory to backup (cPanel or already symlinked)
 Backup system initialized: /home/u164914061/domains/deployhqtest.zajaly.com/deploy/shared/backups/deploy_production_20250803_055719_%commit
Time taken to run A02: Backup Current Release-v2: 5.16 seconds


Running SSH command A03: Database Backup v2

Executing A03: Database Backup v2 [#!/bin/bash # A03: Database Backup (FINAL & MOST ROBUST VERSION) # Execution: Every deployment # Purpose: Create compressed database backup before changes # Timeout: 30 minutes echo "=== Database Backup ===" # Use DeployHQ's /home/u164914061/domains/deployhqtest.zajaly.com/deploy variable for reliability DEPLOY_BASE="/home/u164914061/domains/deployhqtest.zajaly.com/deploy" SHARED_PATH="$DEPLOY_BASE/shared" CURRENT_PATH="$DEPLOY_BASE/current" SHARED_ENV_FILE="$SHARED_PATH/.env" CURRENT_ENV_FILE="$CURRENT_PATH/.env" # This file is created by the A02 script to store the backup path BACKUP_DIR_FILE="$SHARED_PATH/.current_backup_dir" if [ -f "$BACKUP_DIR_FILE" ]; then BACKUP_DIR=$(cat "$BACKUP_DIR_FILE") else echo "❌ Backup directory file not found at $BACKUP_DIR_FILE. Cannot proceed." exit 1 fi echo "? Debug: Checking for .env files..." echo "Debug: Shared path: $SHARED_ENV_FILE" ls -la "$SHARED_ENV_FILE" 2>/dev/null || echo "Debug: Shared .env not found or not accessible." echo "Debug: Current path: $CURRENT_ENV_FILE" ls -la "$CURRENT_ENV_FILE" 2>/dev/null || echo "Debug: Current .env not found or not accessible." ENV_FILE="" # Prioritize the permanent shared .env file if [ -f "$SHARED_ENV_FILE" ]; then echo "✅ Found shared .env file." ENV_FILE="$SHARED_ENV_FILE" # Fallback to the current .env file elif [ -f "$CURRENT_ENV_FILE" ]; then echo "✅ Found current .env file as fallback." ENV_FILE="$CURRENT_ENV_FILE" else echo "⚠️ No .env file could be found. Skipping database backup." exit 0 fi echo "⚙️ Using robust parser to read credentials from $ENV_FILE..." # Create a temporary PHP script to parse the .env file using a more reliable method cat > /tmp/read_env.php << 'EOF' <?php if (empty($argv[1]) || !is_file($argv[1])) { exit(1); } $lines = file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); $env = []; foreach ($lines as $line) { $line = trim($line); if (empty($line) || strpos($line, '#') === 0) { continue; } if (strpos($line, '=') === false) { continue; } list($name, $value) = explode('=', $line, 2); $name = trim($name); $value = trim($value); if (preg_match('/^"(.*)"$/', $value, $matches)) { $value = $matches[1]; } elseif (preg_match('/^\'(.*)\'$/', $value, $matches)) { $value = $matches[1]; } $env[$name] = $value; } // Output the specific variables we need echo "DB_CONNECTION=" . ($env['DB_CONNECTION'] ?? 'mysql') . "\n"; echo "DB_HOST=" . ($env['DB_HOST'] ?? 'localhost') . "\n"; echo "DB_PORT=" . ($env['DB_PORT'] ?? '3306') . "\n"; echo "DB_DATABASE=" . ($env['DB_DATABASE'] ?? '') . "\n"; echo "DB_USERNAME=" . ($env['DB_USERNAME'] ?? '') . "\n"; echo "DB_PASSWORD=" . ($env['DB_PASSWORD'] ?? '') . "\n"; EOF # Parse the .env file and extract variables ENV_VARS=$(php /tmp/read_env.php "$ENV_FILE") rm -f /tmp/read_env.php # Load the variables into the current shell eval "$ENV_VARS" echo "? Debug: Parsed variables:" echo "DB_CONNECTION=$DB_CONNECTION" echo "DB_HOST=$DB_HOST" echo "DB_PORT=$DB_PORT" echo "DB_DATABASE=$DB_DATABASE" echo "DB_USERNAME=$DB_USERNAME" echo "DB_PASSWORD=[HIDDEN]" # Check if the variables were loaded correctly if [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ]; then echo "❌ Failed to parse DB credentials from $ENV_FILE. Please check for syntax errors." exit 1 fi TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S") BACKUP_FILE="$BACKUP_DIR/production_${DB_DATABASE}_backup_$TIMESTAMP.sql.gz" echo "Backing up database: $DB_DATABASE ($DB_CONNECTION)" case "$DB_CONNECTION" in mysql|mariadb) if command -v mysqldump &> /dev/null; then mysqldump --host="$DB_HOST" --port="$DB_PORT" --user="$DB_USERNAME" --password="$DB_PASSWORD" \ --single-transaction --routines --triggers --add-drop-table \ "$DB_DATABASE" 2>/dev/null | gzip > "$BACKUP_FILE" BACKUP_EXIT_CODE=${PIPESTATUS[0]} else echo "❌ mysqldump command not available. Cannot create backup." exit 1 fi ;; *) echo "⚠️ Unsupported database type: $DB_CONNECTION. Skipping backup." exit 0 ;; esac if [ $BACKUP_EXIT_CODE -eq 0 ] && [ -f "$BACKUP_FILE" ] && [ -s "$BACKUP_FILE" ]; then chmod 600 "$BACKUP_FILE" FILESIZE=$(du -sh "$BACKUP_FILE" | cut -f1) echo "✅ Database backup created: $BACKUP_FILE ($FILESIZE)" # Cleanup: Keep only the 10 most recent backups for this database find "$(dirname "$BACKUP_DIR")" -name "production_${DB_DATABASE}_backup_*.sql.gz" -type f | \ sort | head -n -10 | xargs rm -f 2>/dev/null || true else echo "❌ Database backup failed. Check credentials and mysqldump command." rm -f "$BACKUP_FILE" exit 1 fi ]

=== Database Backup ===
? Debug: Checking for .env files...
Debug: Shared path: /home/u164914061/domains/deployhqtest.zajaly.com/deploy/shared/.env
-rw-r----- 1 u164914061 o201400976 1295 Aug  2 23:06 /home/u164914061/domains/deployhqtest.zajaly.com/deploy/shared/.env
Debug: Current path: /home/u164914061/domains/deployhqtest.zajaly.com/deploy/current/.env
lrwxrwxrwx 1 u164914061 o201400976 67 Aug  2 23:07 /home/u164914061/domains/deployhqtest.zajaly.com/deploy/current/.env -> /home/u164914061/domains/deployhqtest.zajaly.com/deploy/shared/.env
 Found shared .env file.
 Using robust parser to read credentials from /home/u164914061/domains/deployhqtest.zajaly.com/deploy/shared/.env...
bash: line 88: WtP: command not found
bash: line 88: 3H: command not found
? Debug: Parsed variables:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u164914061_t2_zj_waraq
DB_USERNAME=u164914061_s_zjt2_waraq
DB_PASSWORD=[HIDDEN]
Backing up database: u164914061_t2_zj_waraq (mysql)
 Database backup failed. Check credentials and mysqldump command.
Time taken to run A03: Database Backup v2: 0.46 seconds


Running SSH command A04: Enter Maintenance Mode v2

Executing A04: Enter Maintenance Mode v2 [#!/bin/bash # A04: Enter Maintenance Mode (UNIVERSAL) # Execution: Every deployment # Purpose: Put application in maintenance mode during deployment # Timeout: 5 minutes echo "=== Enter Maintenance Mode ===" # DeployHQ will replace /home/u164914061/domains/deployhqtest.zajaly.com/deploy with the actual deployment path DEPLOY_BASE="/home/u164914061/domains/deployhqtest.zajaly.com/deploy" CURRENT_PATH="$DEPLOY_BASE/current" echo "Debug: Using DEPLOY_BASE: $DEPLOY_BASE" # Create maintenance flag touch "$DEPLOY_BASE/maintenance_mode_active" && echo "✅ Maintenance flag created" || { echo "❌ Failed to create maintenance flag at $DEPLOY_BASE"; exit 1; } # If Laravel exists in the 'current' symlink (from a previous deployment) if [ -f "$CURRENT_PATH/artisan" ] && [ -d "$CURRENT_PATH/vendor" ]; then echo "Putting Laravel application into maintenance mode..." cd "$CURRENT_PATH" || exit 1 # Simple, compatible maintenance mode command if php artisan down 2>/dev/null; then echo "✅ Laravel maintenance mode activated" else echo "⚠️ Artisan maintenance mode failed, using fallback" fi else # This is normal for a brand new site with no 'current' directory yet echo "ℹ️ No Laravel 'current' path found, using maintenance flag only" fi echo "✅ Maintenance mode activated" ]

=== Enter Maintenance Mode ===
Debug: Using DEPLOY_BASE: /home/u164914061/domains/deployhqtest.zajaly.com/deploy
 Maintenance flag created
Putting Laravel application into maintenance mode...

In Application.php line 750:
                                             
  Class "Inertia\ServiceProvider" not found  
                                             

 Artisan maintenance mode failed, using fallback
 Maintenance mode activated
Time taken to run A04: Enter Maintenance Mode v2: 0.5 seconds


Preparing release directory


Transferring changed files


Uploading config files

Uploading /home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653/.env


Linking files from shared path to release

Symlinking .env.backup.20250802_230721 to /home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653/.env.backup.20250802_230721

Symlinking .env to /home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653/.env

Symlinking deployment.log to /home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653/deployment.log

Symlinking storage to /home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653/storage

Symlinking .current_backup_dir to /home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653/.current_backup_dir

Symlinking public/.well-known to /home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653/public/.well-known

Symlinking bootstrap/cache to /home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653/bootstrap/cache

Symlinking backups to /home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653/backups


Running SSH command B01: Initialize Shared Environment

Executing B01: Initialize Shared Environment [#!/bin/bash # B01: Initialize Shared Environment (UNIVERSAL) # Execution: EVERY deployment # Purpose: Smart .env handling - works for first and all subsequent deployments # Timeout: 10 minutes echo "=== Initialize Shared Environment ===" SHARED_ENV_FILE="/home/u164914061/domains/deployhqtest.zajaly.com/deploy/shared/.env" RELEASE_ENV_EXAMPLE="/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653/.env.example" RELEASE_ENV="/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653/.env" mkdir -p "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/shared" # Universal logic: Handle both first deployment and updates if [ ! -f "$SHARED_ENV_FILE" ]; then echo "? First deployment: Creating shared .env file..." CREATE_ENV=true else echo "? Subsequent deployment: Checking for .env updates..." CREATE_ENV=false # Check if we should update from new release if [ -f "$RELEASE_ENV" ]; then if ! cmp -s "$RELEASE_ENV" "$SHARED_ENV_FILE" 2>/dev/null; then echo "⚠️ .env in release differs from shared - manual review recommended" echo "? Copying new .env to shared/.env.new for comparison" cp "$RELEASE_ENV" "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/shared/.env.new" fi fi fi if [ "$CREATE_ENV" = true ]; then SOURCE_FILE="" # Strategy 1: Use .env.example from current release if [ -f "$RELEASE_ENV_EXAMPLE" ]; then echo "✅ Using .env.example from current release" SOURCE_FILE="$RELEASE_ENV_EXAMPLE" # Strategy 2: Use .env from current release (DeployHQ config files) elif [ -f "$RELEASE_ENV" ]; then echo "✅ Using .env from current release (DeployHQ config)" SOURCE_FILE="$RELEASE_ENV" # Strategy 3: Copy from previous release elif [ -d "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases" ]; then PREV_RELEASE=$(ls -1t "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases" | head -n 2 | tail -n 1 2>/dev/null) if [ -n "$PREV_RELEASE" ] && [ -f "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/$PREV_RELEASE/.env" ]; then echo "✅ Copying .env from previous release: $PREV_RELEASE" SOURCE_FILE="/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/$PREV_RELEASE/.env" elif [ -n "$PREV_RELEASE" ] && [ -f "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/$PREV_RELEASE/.env.example" ]; then echo "✅ Using .env.example from previous release: $PREV_RELEASE" SOURCE_FILE="/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/$PREV_RELEASE/.env.example" fi fi # Strategy 4: Create minimal Laravel .env template if [ -z "$SOURCE_FILE" ]; then echo "⚠️ No .env source found, creating minimal Laravel template" cat > "$SHARED_ENV_FILE" << 'EOF' APP_NAME=Laravel APP_ENV=production APP_KEY= APP_DEBUG=false APP_URL=http://localhost LOG_CHANNEL=stack LOG_DEPRECATIONS_CHANNEL=null LOG_LEVEL=error DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=laravel DB_USERNAME=root DB_PASSWORD= BROADCAST_DRIVER=log CACHE_DRIVER=file FILESYSTEM_DRIVER=local QUEUE_CONNECTION=sync SESSION_DRIVER=file SESSION_LIFETIME=120 EOF echo "⚠️ IMPORTANT: Update database credentials in $SHARED_ENV_FILE" else # Copy from source cp "$SOURCE_FILE" "$SHARED_ENV_FILE" || { echo "❌ Failed to create shared .env"; exit 1; } echo "✅ Shared .env created from: $SOURCE_FILE" fi # Generate APP_KEY if missing if ! grep -q -E "^APP_KEY=base64:[A-Za-z0-9+/]{43}=" "$SHARED_ENV_FILE"; then echo "Generating Laravel APP_KEY..." GENERATED_KEY=$(php -r 'echo "base64:".base64_encode(random_bytes(32));' 2>/dev/null) if [[ "$GENERATED_KEY" == base64:* ]] && [ ${#GENERATED_KEY} -eq 51 ]; then if grep -q "^APP_KEY=" "$SHARED_ENV_FILE"; then sed -i "s|^APP_KEY=.*|APP_KEY=$GENERATED_KEY|" "$SHARED_ENV_FILE" else echo -e "\nAPP_KEY=$GENERATED_KEY" >> "$SHARED_ENV_FILE" fi echo "✅ APP_KEY generated and set" else echo "⚠️ Could not generate APP_KEY - please set manually" fi fi chmod 640 "$SHARED_ENV_FILE" echo "✅ Shared .env ready: $SHARED_ENV_FILE" else echo "✅ Shared .env already exists and ready" fi ]

=== Initialize Shared Environment ===
? Subsequent deployment: Checking for .env updates...
 Shared .env already exists and ready
Time taken to run B01: Initialize Shared Environment: 1.09 seconds


Running SSH command B02: Link Shared Environment

Executing B02: Link Shared Environment [#!/bin/bash # B02: Link Shared Environment # Execution: Every deployment # Purpose: Link release .env to shared .env file # Timeout: 5 minutes echo "=== Link Shared Environment ===" SHARED_ENV_FILE="/home/u164914061/domains/deployhqtest.zajaly.com/deploy/shared/.env" RELEASE_ENV_LINK="/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653/.env" if [ ! -f "$SHARED_ENV_FILE" ]; then echo "❌ Shared .env file not found: $SHARED_ENV_FILE" exit 1 fi rm -f "$RELEASE_ENV_LINK" ln -sf "$SHARED_ENV_FILE" "$RELEASE_ENV_LINK" || { echo "❌ Failed to create .env symlink" exit 1 } if [ -L "$RELEASE_ENV_LINK" ] && [ "$(readlink -f "$RELEASE_ENV_LINK")" = "$SHARED_ENV_FILE" ]; then echo "✅ Shared .env linked to release" else echo "❌ .env symlink verification failed" exit 1 fi ]

=== Link Shared Environment ===
 Shared .env linked to release
Time taken to run B02: Link Shared Environment: 0.4 seconds


Running SSH command B03: Create and Link Shared Directories v2

Executing B03: Create and Link Shared Directories v2 [#!/bin/bash # B03: Create and Link Shared Directories (UNIVERSAL) # Execution: EVERY deployment # Purpose: Smart shared directory management with proper Laravel structure # Timeout: 10 minutes echo "=== Create and Link Shared Directories ===" # Use DeployHQ's /home/u164914061/domains/deployhqtest.zajaly.com/deploy variable for reliable path detection DEPLOY_BASE="/home/u164914061/domains/deployhqtest.zajaly.com/deploy" SHARED_PATH="$DEPLOY_BASE/shared" # Get current release path from /home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653 variable RELEASE_PATH="/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653" echo "Debug: DEPLOY_BASE=$DEPLOY_BASE" echo "Debug: SHARED_PATH=$SHARED_PATH" echo "Debug: RELEASE_PATH=$RELEASE_PATH" # Function to safely handle symlinks and directories safe_symlink_setup() { local source_path="$1" local target_path="$2" local link_name="$3" # Remove if broken symlink if [ -L "$source_path" ] && [ ! -e "$source_path" ]; then echo "? Removing broken symlink: $link_name" rm -f "$source_path" fi # Handle existing directory if [ -d "$source_path" ] && [ ! -L "$source_path" ]; then echo "? Found existing directory: $link_name" if [ -d "$target_path" ]; then echo "? Merging contents to shared: $link_name" cp -rn "$source_path"/* "$target_path"/ 2>/dev/null || true else echo "? Moving directory to shared: $link_name" mv "$source_path" "$target_path" fi fi # Create symlink if needed if [ ! -L "$source_path" ]; then mkdir -p "$(dirname "$target_path")" mkdir -p "$target_path" ln -sf "$target_path" "$source_path" echo "✅ Symlinked: $link_name" else echo "✅ Already linked: $link_name" fi } # Create all required Laravel storage directories echo "? Creating Laravel storage structure..." STORAGE_DIRS=( "app" "app/public" "framework" "framework/cache" "framework/cache/data" "framework/sessions" "framework/testing" "framework/views" "logs" ) for dir in "${STORAGE_DIRS[@]}"; do mkdir -p "$SHARED_PATH/storage/$dir" done # Create bootstrap cache directory mkdir -p "$SHARED_PATH/bootstrap/cache" # Create .gitignore files for Laravel echo "? Creating .gitignore files..." echo "* !.gitignore" > "$SHARED_PATH/storage/app/.gitignore" echo "* !public/ !.gitignore" > "$SHARED_PATH/storage/app/public/.gitignore" echo "* !.gitignore" > "$SHARED_PATH/storage/framework/.gitignore" echo "* !.gitignore" > "$SHARED_PATH/storage/framework/cache/.gitignore" echo "* !data/ !.gitignore" > "$SHARED_PATH/storage/framework/cache/data/.gitignore" echo "* !.gitignore" > "$SHARED_PATH/storage/framework/sessions/.gitignore" echo "* !.gitignore" > "$SHARED_PATH/storage/framework/testing/.gitignore" echo "* !.gitignore" > "$SHARED_PATH/storage/framework/views/.gitignore" echo "* !.gitignore" > "$SHARED_PATH/storage/logs/.gitignore" echo "* !.gitignore" > "$SHARED_PATH/bootstrap/cache/.gitignore" # Core Laravel symlinks echo "? Setting up core Laravel symlinks..." safe_symlink_setup \ "$RELEASE_PATH/storage" \ "$SHARED_PATH/storage" \ "storage" safe_symlink_setup \ "$RELEASE_PATH/bootstrap/cache" \ "$SHARED_PATH/bootstrap/cache" \ "bootstrap/cache" # Create public/.well-known if needed if [ ! -d "$RELEASE_PATH/public/.well-known" ]; then mkdir -p "$SHARED_PATH/public/.well-known" ln -sf "$SHARED_PATH/public/.well-known" "$RELEASE_PATH/public/.well-known" echo "✅ Created: public/.well-known" fi # Set proper permissions echo "? Setting permissions..." # Storage directories find "$SHARED_PATH/storage" -type d -exec chmod 775 {} \; 2>/dev/null || true find "$SHARED_PATH/storage" -type f -exec chmod 664 {} \; 2>/dev/null || true # Bootstrap cache find "$SHARED_PATH/bootstrap/cache" -type d -exec chmod 775 {} \; 2>/dev/null || true find "$SHARED_PATH/bootstrap/cache" -type f -exec chmod 664 {} \; 2>/dev/null || true # Ensure web server can write to these directories chmod 777 "$SHARED_PATH/storage/logs" 2>/dev/null || true chmod 777 "$SHARED_PATH/storage/framework/views" 2>/dev/null || true chmod 777 "$SHARED_PATH/storage/framework/sessions" 2>/dev/null || true chmod 777 "$SHARED_PATH/storage/framework/cache" 2>/dev/null || true chmod 777 "$SHARED_PATH/storage/framework/cache/data" 2>/dev/null || true chmod 777 "$SHARED_PATH/bootstrap/cache" 2>/dev/null || true echo "✅ All shared directories ready" ]

=== Create and Link Shared Directories ===
Debug: DEPLOY_BASE=/home/u164914061/domains/deployhqtest.zajaly.com/deploy
Debug: SHARED_PATH=/home/u164914061/domains/deployhqtest.zajaly.com/deploy/shared
Debug: RELEASE_PATH=/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653
? Creating Laravel storage structure...
? Creating .gitignore files...
? Setting up core Laravel symlinks...
 Already linked: storage
 Already linked: bootstrap/cache
? Setting permissions...
 All shared directories ready
Time taken to run B03: Create and Link Shared Directories v2: 0.52 seconds


Running SSH command B04: Run Database Migrations

Executing B04: Run Database Migrations [#!/bin/bash # B04: Run Database Migrations # Execution: Every deployment # Purpose: Apply database schema changes # Timeout: 30 minutes echo "=== Run Database Migrations ===" # Default to true for safer deployments RUN_MIGRATIONS="${RUN_MIGRATIONS:-true}" if [[ "$RUN_MIGRATIONS" != "true" && "$RUN_MIGRATIONS" != "1" ]]; then echo "ℹ️ Migrations skipped (RUN_MIGRATIONS=$RUN_MIGRATIONS)" exit 0 fi ARTISAN_PATH="/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653/artisan" if [ ! -f "$ARTISAN_PATH" ]; then echo "ℹ️ No artisan file found, skipping Laravel migrations" exit 0 fi chmod +x "$ARTISAN_PATH" echo "Running Laravel database migrations..." cd "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653" || { echo "❌ Failed to cd to release path"; exit 1; } if php artisan migrate --force; then echo "✅ Database migrations completed successfully" # Default to false for safer deployments RUN_SEEDERS="${RUN_SEEDERS:-false}" if [[ "$RUN_SEEDERS" == "true" || "$RUN_SEEDERS" == "1" ]]; then echo "Running database seeders..." if php artisan db:seed --force; then echo "✅ Database seeders completed successfully" else echo "❌ Database seeders failed" exit 1 fi fi else echo "❌ Database migrations failed" exit 1 fi ]

=== Run Database Migrations ===
Running Laravel database migrations...

In Application.php line 750:
                                             
  Class "Inertia\ServiceProvider" not found  
                                             

 Database migrations failed
Time taken to run B04: Run Database Migrations: 0.52 seconds


Running SSH command B05: Optimize Application-v3

Executing B05: Optimize Application-v3 [#!/bin/bash # B05: Optimize Application (UNIVERSAL) # Execution: Every deployment # Purpose: Cache configuration and optimize for production with error handling # Timeout: 10 minutes echo "=== Optimize Application ===" # Use DeployHQ's /home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653 variable for reliable path detection RELEASE_PATH="/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653" ARTISAN_PATH="$RELEASE_PATH/artisan" echo "Debug: RELEASE_PATH=$RELEASE_PATH" echo "Debug: ARTISAN_PATH=$ARTISAN_PATH" if [ ! -f "$ARTISAN_PATH" ]; then echo "ℹ️ No artisan file found at $ARTISAN_PATH, skipping Laravel optimization" exit 0 fi cd "$RELEASE_PATH" || { echo "❌ Failed to cd to release path: $RELEASE_PATH" exit 1 } # Ensure storage directories exist before clearing caches echo "? Ensuring storage directories exist..." mkdir -p storage/framework/views mkdir -p storage/framework/cache mkdir -p storage/framework/cache/data mkdir -p storage/framework/sessions mkdir -p bootstrap/cache # Create .gitignore files if missing [ ! -f storage/framework/views/.gitignore ] && echo "* !.gitignore" > storage/framework/views/.gitignore [ ! -f storage/framework/cache/.gitignore ] && echo "* !.gitignore" > storage/framework/cache/.gitignore [ ! -f storage/framework/sessions/.gitignore ] && echo "* !.gitignore" > storage/framework/sessions/.gitignore [ ! -f bootstrap/cache/.gitignore ] && echo "* !.gitignore" > bootstrap/cache/.gitignore echo "? Clearing caches..." php artisan cache:clear 2>/dev/null || echo "ℹ️ Cache clear skipped" php artisan config:clear 2>/dev/null || echo "ℹ️ Config clear skipped" php artisan route:clear 2>/dev/null || echo "ℹ️ Route clear skipped" # Handle view:clear specially due to potential errors if php artisan view:clear 2>/dev/null; then echo "✅ View cache cleared" else echo "ℹ️ View clear skipped - ensuring views directory exists" # Try to get the compiled path from Laravel config VIEW_COMPILED_PATH=$(php -r " \$config = include('config/view.php'); echo \$config['compiled'] ?? 'storage/framework/views'; " 2>/dev/null || echo "storage/framework/views") mkdir -p "$VIEW_COMPILED_PATH" touch "$VIEW_COMPILED_PATH/.gitignore" fi # Get environment from .env file APP_ENV=$(php -r "error_reporting(0); \$env=parse_ini_file('.env'); echo \$env['APP_ENV'] ?? 'production';" 2>/dev/null) if [ "$APP_ENV" = "production" ]; then echo "⚡ Optimizing for production environment..." # Config cache if php artisan config:cache; then echo "✅ Configuration cached" else echo "❌ Config cache failed" exit 1 fi # Route cache if php artisan route:cache; then echo "✅ Routes cached" else echo "❌ Route cache failed" exit 1 fi # View cache with error handling echo "? Caching views..." # First check if there are any blade templates if find resources/views -name "*.blade.php" 2>/dev/null | grep -q .; then if php artisan view:cache 2>&1 | grep -q "View path not found"; then echo "⚠️ View cache skipped - views directory issue" # Ensure the compiled path exists mkdir -p storage/framework/views elif php artisan view:cache; then echo "✅ Views cached" else echo "⚠️ View cache failed but continuing" fi else echo "ℹ️ No blade templates found, skipping view cache" fi # Optional: Event cache (if using events) if [ -f "app/Providers/EventServiceProvider.php" ]; then php artisan event:cache 2>/dev/null && echo "✅ Events cached" || echo "ℹ️ Event cache skipped" fi echo "✅ Application optimized for production" else echo "ℹ️ Skipping optimization for environment: $APP_ENV" fi ]

=== Optimize Application ===
Debug: RELEASE_PATH=/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653
Debug: ARTISAN_PATH=/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653/artisan
? Ensuring storage directories exist...
? Clearing caches...

In Application.php line 750:
                                             
  Class "Inertia\ServiceProvider" not found  
                                             

 Cache clear skipped

In Application.php line 750:
                                             
  Class "Inertia\ServiceProvider" not found  
                                             

 Config clear skipped

In Application.php line 750:
                                             
  Class "Inertia\ServiceProvider" not found  
                                             

 Route clear skipped

In Application.php line 750:
                                             
  Class "Inertia\ServiceProvider" not found  
                                             

 View clear skipped - ensuring views directory exists
 Optimizing for production environment...

In Application.php line 750:
                                             
  Class "Inertia\ServiceProvider" not found  
                                             

 Config cache failed
Time taken to run B05: Optimize Application-v3: 1.16 seconds


Running SSH command B06: Create Directory Structure

Executing B06: Create Directory Structure [#!/bin/bash # B06: Create Directory Structure # Execution: Every deployment # Purpose: Ensure proper directory structure and permissions # Timeout: 5 minutes echo "=== Create Directory Structure ===" cd "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653" || { echo "❌ Failed to cd to release path"; exit 1; } ESSENTIAL_DIRS=( "tmp" "logs" "cache" "uploads" ) for dir in "${ESSENTIAL_DIRS[@]}"; do if [ ! -d "$dir" ]; then mkdir -p "$dir" chmod 755 "$dir" echo "✅ Created directory: $dir" fi done echo "Setting file permissions..." find . -type f -exec chmod 644 {} \; 2>/dev/null || true find . -type d -exec chmod 755 {} \; 2>/dev/null || true if [ -f "artisan" ]; then chmod +x artisan; fi if [ -f "bin/console" ]; then chmod +x bin/console; fi echo "✅ Directory structure and permissions set" ]

=== Create Directory Structure ===
Setting file permissions...
 Directory structure and permissions set
Time taken to run B06: Create Directory Structure: 22.24 seconds


Running SSH command B06: Create Directory Structure -v2 (OPTIMIZED)

Executing B06: Create Directory Structure -v2 (OPTIMIZED) [#!/bin/bash # B06: Create Directory Structure (SECURE & FAST) # Execution: Every deployment # Purpose: Ensure proper directory structure and permissions efficiently and securely # Timeout: 5 minutes # v2: echo "=== Create Directory Structure ===" RELEASE_PATH="/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653" cd "$RELEASE_PATH" || { echo "❌ Failed to cd to release path"; exit 1; } # --- Step 1: Fast Permission Setting for App Code --- echo "? Setting primary application permissions (fast)..." # Set directory permissions (limited depth for speed) find . -maxdepth 4 -type d -exec chmod 755 {} + 2>/dev/null # Set file permissions for common types (limited depth for speed) find . -maxdepth 4 \( -name "*.php" -o -name "*.js" -o -name "*.css" -o -name "*.json" \) -exec chmod 644 {} + 2>/dev/null # Set executable permissions for key files [ -f "artisan" ] && chmod 755 artisan [ -f "bin/console" ] && chmod 755 bin/console # --- Step 2: Comprehensive Security Scan & Correction --- echo "?️ Performing deep security scan for incorrect permissions..." # Find and correct any world-writable directories (e.g., 777, 757) # This is a major security risk. We change them to a safe 755. find . -type d -perm /o+w -exec chmod 755 {} \; -print | sed 's/^/Fixed insecure directory: /' # Find and correct any world-writable files (e.g., 666, 646) # This is also a major risk. We change them to a safe 644. find . -type f -perm /o+w -exec chmod 644 {} \; -print | sed 's/^/Fixed insecure file: /' # Find and correct any files that are executable but shouldn't be (e.g., images, text files) # We remove the execute bit from common non-executable file types. find . \( -name "*.jpg" -o -name "*.png" -o -name "*.txt" -o -name "*.md" \) -perm /a+x -exec chmod 644 {} \; -print | sed 's/^/Removed unnecessary execute permission: /' echo "✅ Directory structure and permissions are secure and correct." ]

=== Create Directory Structure ===
? Setting primary application permissions (fast)...
? Performing deep security scan for incorrect permissions...
 Directory structure and permissions are secure and correct.
Time taken to run B06: Create Directory Structure -v2 (OPTIMIZED): 0.83 seconds


Linking release directory to current

Symlinking /home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250803055653 to /home/u164914061/domains/deployhqtest.zajaly.com/deploy/current


Running SSH command C01: Create Storage Symlink

Executing C01: Create Storage Symlink [#!/bin/bash # C01: Create Storage Symlink (SHARED HOSTING SAFE) # Execution: Every deployment # Purpose: Create Laravel storage symlink - pure bash, no PHP exec() # Timeout: 5 minutes # Flow Summary # 1- ✅ DeployHQ uploads .env to release root # 2- ✅ B01b detects & copies to shared folder with validation # 3- ✅ B02 symlinks shared .env back to release (replaces DeployHQ .env) # 4- ✅ C01 creates storage symlink with Bash fallback echo "=== Create Storage Symlink (Shared Hosting Safe) ===" PUBLIC_STORAGE="/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current/public/storage" STORAGE_TARGET="/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current/storage/app/public" # Ensure target directory exists first mkdir -p "$STORAGE_TARGET" # Check if symlink already exists and is correct if [ -L "$PUBLIC_STORAGE" ] && [ "$(readlink -f "$PUBLIC_STORAGE")" = "$STORAGE_TARGET" ]; then echo "✅ Storage symlink already exists and is correct" exit 0 fi # Remove existing if present (file, directory, or broken symlink) if [ -e "$PUBLIC_STORAGE" ] || [ -L "$PUBLIC_STORAGE" ]; then rm -rf "$PUBLIC_STORAGE" echo "?️ Removed existing public/storage" fi echo "? Creating storage symlink with pure bash..." # Change to public directory for relative symlink creation cd "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current/public" || { echo "❌ Failed to cd to public directory"; exit 1; } # Create relative symlink (most compatible approach) if ln -sf "../storage/app/public" "storage"; then echo "✅ Storage symlink created successfully" # Verify the symlink works if [ -L "storage" ] && [ -d "storage" ]; then echo "✅ Storage symlink verification passed" echo "? Laravel file uploads will work correctly" else echo "⚠️ Storage symlink created but verification inconclusive" fi else echo "❌ Failed to create storage symlink - check file permissions" exit 1 fi]

=== Create Storage Symlink (Shared Hosting Safe) ===
? Removed existing public/storage
? Creating storage symlink with pure bash...
 Storage symlink created successfully
 Storage symlink verification passed
? Laravel file uploads will work correctly
Time taken to run C01: Create Storage Symlink: 0.38 seconds


Running SSH command C02: Restart Services

Executing C02: Restart Services [#!/bin/bash # C02: Restart Services # Execution: Every deployment # Purpose: Restart background services and clear caches # Timeout: 10 minutes echo "=== Restart Services ===" cd "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current" || { echo "❌ Failed to cd to current path"; exit 1; } if [ -f "artisan" ]; then QUEUE_CONNECTION=$(php -r "error_reporting(0); \$env=parse_ini_file('.env'); echo \$env['QUEUE_CONNECTION'] ?? 'sync';" 2>/dev/null) if [ "$QUEUE_CONNECTION" != "sync" ]; then echo "Restarting queue workers (connection: $QUEUE_CONNECTION)..." php artisan queue:restart echo "✅ Queue workers restarted" else echo "ℹ️ Queue connection is 'sync', no workers to restart" fi fi if command -v php &> /dev/null; then php -r "opcache_reset();" 2>/dev/null || true echo "ℹ️ OPcache cleared" fi echo "✅ Services restarted" ]

=== Restart Services ===
  Queue connection is 'sync', no workers to restart
  OPcache cleared
 Services restarted
Time taken to run C02: Restart Services: 0.49 seconds


Running SSH command C03: Exit Maintenance Mode

Executing C03: Exit Maintenance Mode [#!/bin/bash # C03: Exit Maintenance Mode # Execution: Every deployment # Purpose: Remove maintenance mode and restore normal operations # Timeout: 5 minutes echo "=== Exit Maintenance Mode ===" rm -f "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/maintenance_mode_active" if [ -f "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current/artisan" ]; then echo "Exiting Laravel maintenance mode..." cd "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current" || exit 1 if php artisan up; then echo "✅ Laravel maintenance mode deactivated" else echo "⚠️ Failed to exit Laravel maintenance mode via artisan" fi else echo "ℹ️ No artisan file found, maintenance flag removed only" fi echo "✅ Maintenance mode deactivated" ]

=== Exit Maintenance Mode ===
Exiting Laravel maintenance mode...

In Application.php line 750:
                                             
  Class "Inertia\ServiceProvider" not found  
                                             

  Failed to exit Laravel maintenance mode via artisan
 Maintenance mode deactivated
Time taken to run C03: Exit Maintenance Mode: 0.51 seconds


Running SSH command C04: Health Checks

Executing C04: Health Checks [#!/bin/bash # C04: Health Checks # Execution: Every deployment # Purpose: Verify deployment success and application health # Timeout: 10 minutes echo "=== Health Checks ===" HEALTH_PASSED=true if [ ! -L "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current" ]; then echo "❌ Current symlink missing" HEALTH_PASSED=false else echo "✅ Current symlink exists" fi CURRENT_TARGET=$(readlink "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current" 2>/dev/null) if [ -n "$CURRENT_TARGET" ] && [ -d "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current" ]; then echo "✅ Release directory accessible" else echo "❌ Release directory not accessible" HEALTH_PASSED=false fi if [ -f "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current/artisan" ]; then cd "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current" || { echo "❌ Cannot access current path"; exit 1; } if php artisan --version >/dev/null 2>&1; then echo "✅ Laravel artisan working" else echo "❌ Laravel artisan not working" HEALTH_PASSED=false fi if [ -w "storage" ]; then echo "✅ Storage directory writable" else echo "❌ Storage directory not writable" HEALTH_PASSED=false fi if [ -f ".env" ]; then echo "✅ .env file exists" else echo "❌ .env file missing" HEALTH_PASSED=false fi fi if [ -f "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current/.env" ]; then APP_URL=$(php -r "error_reporting(0); \$env=parse_ini_file('/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current/.env'); echo \$env['APP_URL'] ?? '';" 2>/dev/null) if [ -n "$APP_URL" ] && command -v curl &> /dev/null; then HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL" --max-time 10 || echo "000") if [ "$HTTP_CODE" = "200" ]; then echo "✅ Application responding with HTTP 200" else echo "⚠️ Application returned HTTP $HTTP_CODE" fi fi fi if [ "$HEALTH_PASSED" = true ]; then echo "✅ All critical health checks passed" exit 0 else echo "❌ Some health checks failed" exit 1 fi ]

=== Health Checks ===
 Current symlink exists
 Release directory accessible
 Laravel artisan not working
 Storage directory writable
 .env file exists
 Some health checks failed
Time taken to run C04: Health Checks: 0.57 seconds


Running SSH command C05: Fix Symlink Issues-v2

Executing C05: Fix Symlink Issues-v2 [#!/bin/bash # C05: Fix Symlink Issues (SAFER VERSION) # Execution: Every deployment # Purpose: Create universal public_html symlink safely # Timeout: 5 minutes echo "=== Fix Symlink Issues ===" # Use DeployHQ's /home/u164914061/domains/deployhqtest.zajaly.com/deploy variable for reliable path detection DEPLOY_PATH="/home/u164914061/domains/deployhqtest.zajaly.com/deploy" DOMAIN_ROOT=$(dirname "$DEPLOY_PATH") echo "? Detected paths:" echo " Deploy Path: $DEPLOY_PATH" echo " Domain Root: $DOMAIN_ROOT" # Change to domain root directory (where public_html should be) cd "$DOMAIN_ROOT" || { echo "❌ Failed to cd to domain root: $DOMAIN_ROOT" exit 1 } echo "? Creating/updating public_html symlink for web access..." # Handle existing public_html more safely if [ -d "public_html" ] && [ ! -L "public_html" ]; then echo "⚠️ Found existing public_html directory" # Check if it's empty or contains only default files if [ -z "$(ls -A public_html 2>/dev/null)" ]; then echo "? Directory is empty - safe to remove" rm -rf "public_html" echo "✅ Removed empty public_html directory" elif [ -f "public_html/index.html" ] && [ $(ls -1 public_html | wc -l) -eq 1 ]; then echo "? Directory contains only default index.html - backing up and removing" cp "public_html/index.html" "public_html.backup.$(date +%Y%m%d_%H%M%S).html" rm -rf "public_html" echo "✅ Backed up index.html and removed public_html directory" else echo "? Directory contains files - creating backup before removal" BACKUP_NAME="public_html.backup.$(date +%Y%m%d_%H%M%S)" mv "public_html" "$BACKUP_NAME" echo "✅ Backed up public_html to: $BACKUP_NAME" fi elif [ -L "public_html" ] && [ ! -e "public_html" ]; then echo "? Removing broken public_html symlink..." rm -f "public_html" echo "✅ Removed broken symlink" elif [ -L "public_html" ] && [ -e "public_html" ]; then echo "ℹ️ Valid public_html symlink already exists - checking target" CURRENT_TARGET=$(readlink "public_html") if [ "$CURRENT_TARGET" != "deploy/current/public" ]; then echo "? Updating public_html symlink target" rm -f "public_html" else echo "✅ public_html symlink already points to correct target" exit 0 fi else echo "ℹ️ No existing public_html found - will create new symlink" fi # Create public_html symlink pointing to deploy/current/public if [ ! -L "public_html" ]; then echo "? Creating public_html symlink: public_html -> deploy/current/public" ln -sf "deploy/current/public" "public_html" if [ $? -eq 0 ]; then echo "✅ public_html symlink created successfully at: $DOMAIN_ROOT/public_html" else echo "❌ Failed to create public_html symlink" exit 1 fi fi # Verify the symlink is correct if [ -L "public_html" ]; then LINK_TARGET=$(readlink "public_html") echo "? Verification: $DOMAIN_ROOT/public_html -> $LINK_TARGET" if [ -f "$LINK_TARGET/index.php" ]; then echo "✅ public_html symlink verification passed - Laravel app accessible" else echo "⚠️ public_html symlink may not point to correct Laravel public directory" echo " This might be normal for first deployment before current symlink exists" fi fi echo "✅ Symlink issues resolved safely" ]

=== Fix Symlink Issues ===
? Detected paths:
   Deploy Path: /home/u164914061/domains/deployhqtest.zajaly.com/deploy
   Domain Root: /home/u164914061/domains/deployhqtest.zajaly.com
? Creating/updating public_html symlink for web access...
 Valid public_html symlink already exists - checking target
 public_html symlink already points to correct target
Time taken to run C05: Fix Symlink Issues-v2: 0.38 seconds


Running SSH command C06: Build Assets - Build-on-Server strategy only

Executing C06: Build Assets - Build-on-Server strategy only [#!/bin/bash # C06: Build Assets # Execution: Every deployment (Build-on-Server strategy only) # Purpose: Compile frontend assets on the server # Timeout: 30 minutes echo "=== Build Assets ===" BUILD_STRATEGY="${BUILD_STRATEGY:-build-on-server}" if [ "$BUILD_STRATEGY" != "build-on-server" ]; then echo "ℹ️ Skipping asset build (Strategy: $BUILD_STRATEGY)" exit 0 fi echo "? Build-on-Server: Compiling frontend assets..." cd "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current" || { echo "❌ Failed to cd to current path"; exit 1; } if [ ! -f "package.json" ]; then echo "ℹ️ No package.json found, skipping asset compilation" exit 0 fi # Install Node dependencies echo "? Installing Node dependencies..." if command -v npm &> /dev/null; then npm ci --silent --production=false else echo "❌ npm not found" exit 1 fi # Build assets based on available scripts echo "? Building assets..." if npm run --silent build >/dev/null 2>&1; then echo "✅ Assets built successfully with 'npm run build'" elif npm run --silent production >/dev/null 2>&1; then echo "✅ Assets built successfully with 'npm run production'" elif npm run --silent prod >/dev/null 2>&1; then echo "✅ Assets built successfully with 'npm run prod'" else echo "⚠️ No suitable build script found in package.json" echo "Available scripts:" npm run 2>/dev/null | grep -E "^\s+[a-zA-Z]" | head -5 fi # Remove dev dependencies to save space echo "? Cleaning up dev dependencies..." npm prune --production --silent echo "✅ Asset build completed" ]

=== Build Assets ===
? Build-on-Server: Compiling frontend assets...
? Installing Node dependencies...
? Building assets...
 Assets built successfully with 'npm run build'
? Cleaning up dev dependencies...
 Asset build completed
Time taken to run C06: Build Assets - Build-on-Server strategy only: 3.86 seconds


Running SSH command C07: Cleanup Old Releases

Executing C07: Cleanup Old Releases [#!/bin/bash # C07: Cleanup Old Releases # Execution: Every deployment # Purpose: Remove old releases to save disk space # Timeout: 5 minutes echo "=== Cleanup Old Releases ===" cd "/home/u164914061/domains/deployhqtest.zajaly.com/deploy" || { echo "❌ Failed to cd to deployment path"; exit 1; } RELEASES_DIR="releases" KEEP_RELEASES="${KEEP_RELEASES:-5}" if [ ! -d "$RELEASES_DIR" ]; then echo "ℹ️ Releases directory doesn't exist" exit 0 fi cd "$RELEASES_DIR" || { echo "❌ Failed to cd to releases"; exit 1; } # Count current releases RELEASE_COUNT=$(ls -1t | wc -l) echo "? Current releases: $RELEASE_COUNT" echo "? Configured to keep: $KEEP_RELEASES releases" if [ "$RELEASE_COUNT" -le "$KEEP_RELEASES" ]; then echo "ℹ️ No cleanup needed" exit 0 fi # Calculate releases to delete TO_DELETE=$((RELEASE_COUNT - KEEP_RELEASES)) echo "?️ Deleting $TO_DELETE old releases..." # Get old releases (keep most recent ones) OLD_RELEASES=$(ls -1t | tail -n "$TO_DELETE") DELETED_COUNT=0 for release in $OLD_RELEASES; do if [ -d "$release" ]; then echo "Deleting: $release" rm -rf "$release" DELETED_COUNT=$((DELETED_COUNT + 1)) fi done echo "✅ Cleanup completed - deleted $DELETED_COUNT releases, kept $KEEP_RELEASES" ]

=== Cleanup Old Releases ===
? Current releases: 5
? Configured to keep: 5 releases
  No cleanup needed
Time taken to run C07: Cleanup Old Releases: 0.4 seconds


Running SSH command C08: Send Deployment Notification

Executing C08: Send Deployment Notification [#!/bin/bash # C08: Send Deployment Notification # Execution: Every deployment # Purpose: Send success notification and log deployment # Timeout: 5 minutes echo "=== Send Deployment Notification ===" # Deployment information DEPLOYMENT_TIME=$(date '+%Y-%m-%d %H:%M:%S %Z') RELEASE_ID=$(basename "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current" 2>/dev/null | sed 's|.*releases/||' || echo "unknown") CURRENT_COMMIT=$(cd "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current" && git rev-parse --short HEAD 2>/dev/null || echo "unknown") # Get deployment statistics RELEASE_SIZE=$(du -sh "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/current" 2>/dev/null | cut -f1 || echo "unknown") TOTAL_RELEASES=$(ls -1 "/home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases" 2>/dev/null | wc -l || echo "unknown") # Create notification message NOTIFICATION_MESSAGE="? Deployment Completed Successfully ? Deployment Details: • Time: $DEPLOYMENT_TIME • Release ID: $RELEASE_ID • Commit: $CURRENT_COMMIT • Size: $RELEASE_SIZE • Total Releases: $TOTAL_RELEASES • Environment: production" # Log deployment to shared log file DEPLOYMENT_LOG="/home/u164914061/domains/deployhqtest.zajaly.com/deploy/shared/deployment.log" echo "[$DEPLOYMENT_TIME] Release: $RELEASE_ID | Commit: $CURRENT_COMMIT | Size: $RELEASE_SIZE" >> "$DEPLOYMENT_LOG" # Simple notification (can be extended with webhook integrations) echo "$NOTIFICATION_MESSAGE" # Optional: Send to external webhook (if configured) WEBHOOK_URL="${DEPLOYMENT_WEBHOOK_URL:-}" if [ -n "$WEBHOOK_URL" ] && command -v curl &> /dev/null; then echo "? Sending webhook notification..." WEBHOOK_PAYLOAD="{ \"text\": \"$NOTIFICATION_MESSAGE\", \"deployment\": { \"environment\": \"production\", \"release_id\": \"$RELEASE_ID\", \"commit\": \"$CURRENT_COMMIT\", \"timestamp\": \"$DEPLOYMENT_TIME\", \"size\": \"$RELEASE_SIZE\" } }" if curl -s -X POST -H "Content-Type: application/json" -d "$WEBHOOK_PAYLOAD" "$WEBHOOK_URL" >/dev/null; then echo "✅ Webhook notification sent" else echo "⚠️ Webhook notification failed" fi fi echo "✅ Deployment notification completed" ]

=== Send Deployment Notification ===
? Deployment Completed Successfully

? Deployment Details:
 Time: 2025-08-03 05:58:26 UTC
 Release ID: current
 Commit: 9dea814c7
 Size: 0
 Total Releases: 5
 Environment: production
 Deployment notification completed
Time taken to run C08: Send Deployment Notification: 0.41 seconds


Cleaning up old releases

Removed old release at /home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250802212623

Removed old release at /home/u164914061/domains/deployhqtest.zajaly.com/deploy/releases/20250802214412

Finishing

Delivering webhook notifications


Sending emails


Saving build environment

Saved build environment language versions
