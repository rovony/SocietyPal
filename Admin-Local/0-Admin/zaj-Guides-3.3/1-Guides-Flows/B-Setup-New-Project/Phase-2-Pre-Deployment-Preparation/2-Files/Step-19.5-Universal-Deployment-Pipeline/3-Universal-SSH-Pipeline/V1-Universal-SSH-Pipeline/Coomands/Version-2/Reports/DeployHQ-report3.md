Running SSH command PhaseA-Pre1: System Requirements Detection & Diagnosis

Executing PhaseA-Pre1: System Requirements Detection & Diagnosis [#!/bin/bash set -e # PhaseA-Pre1: System Requirements Detection & Diagnosis # Purpose: Check server environment vs typical Laravel application requirements # Run: FIRST - before any other scripts (Pre-Deployment Commands - Before Upload) # Action: DETECT and WARN only - stops pipeline if critical system issues found # Version 2.2 - PRODUCTION READY (System-level checks only) echo "=== PhaseA-Pre1: Server Environment Analysis ===" # Initialize variables CRITICAL_ISSUES=0 WARNINGS=0 RECOMMENDATIONS=() echo "? Analyzing server environment for Laravel application compatibility..." # Pre1-01: Server Composer Analysis echo "=== Server Composer Environment ===" echo "? Current working directory: $(pwd)" echo "? Checking server Composer installation..." # Check current system Composer versions echo "?️ System Composer Analysis:" # Check composer (default) if command -v composer &> /dev/null; then COMPOSER_VERSION=$(composer --version 2>/dev/null | head -1) echo " composer: $COMPOSER_VERSION" # Extract major version COMPOSER_MAJOR=$(echo "$COMPOSER_VERSION" | grep -o "version [0-9]" | cut -d' ' -f2 2>/dev/null || echo "unknown") echo " composer major version: $COMPOSER_MAJOR" else echo " composer: NOT FOUND" CRITICAL_ISSUES=$((CRITICAL_ISSUES + 1)) fi # Check composer2 (if available) if command -v composer2 &> /dev/null; then COMPOSER2_VERSION=$(composer2 --version 2>/dev/null | head -1) echo " composer2: $COMPOSER2_VERSION" COMPOSER2_MAJOR=$(echo "$COMPOSER2_VERSION" | grep -o "version [0-9]" | cut -d' ' -f2 2>/dev/null || echo "unknown") echo " composer2 major version: $COMPOSER2_MAJOR" else echo " composer2: NOT FOUND" fi # Server Composer Recommendations echo "? Server Composer Status:" if command -v composer2 &> /dev/null && [[ "$COMPOSER2_MAJOR" == "2" ]]; then echo "✅ EXCELLENT: composer2 (v2.x) available - supports modern Laravel applications" RECOMMENDATIONS+=("Use 'composer2' for Laravel 9+ applications") elif [[ "$COMPOSER_MAJOR" == "2" ]]; then echo "✅ GOOD: composer (v2.x) available - supports modern Laravel applications" RECOMMENDATIONS+=("Use 'composer' for Laravel 9+ applications") elif [[ "$COMPOSER_MAJOR" == "1" ]]; then echo "⚠️ WARNING: Only Composer 1.x available - limited Laravel support" WARNINGS=$((WARNINGS + 1)) RECOMMENDATIONS+=("Install Composer 2.x for Laravel 9+ support: curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer2") RECOMMENDATIONS+=("Composer 1.x only supports Laravel 8 and below") else echo "❌ CRITICAL: No working Composer installation found" CRITICAL_ISSUES=$((CRITICAL_ISSUES + 1)) RECOMMENDATIONS+=("Install Composer: curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer") fi # Pre1-02: PHP Extensions Analysis echo "=== PHP Extensions Analysis ===" # Get current PHP version PHP_VERSION=$(php -v | head -1 | cut -d' ' -f2 | cut -d'-' -f1) echo "? Current PHP: $PHP_VERSION" # Check PHP version compatibility with Laravel echo "? Analyzing PHP version for Laravel compatibility..." PHP_MAJOR_MINOR=$(echo "$PHP_VERSION" | cut -d'.' -f1-2) case "$PHP_MAJOR_MINOR" in "8.3"|"8.2"|"8.1") echo "✅ EXCELLENT: PHP $PHP_VERSION supports Laravel 10+ and all modern features" ;; "8.0") echo "✅ GOOD: PHP $PHP_VERSION supports Laravel 9+ (consider upgrading to 8.1+)" RECOMMENDATIONS+=("Consider upgrading to PHP 8.1+ for better performance and security") ;; "7.4") echo "⚠️ WARNING: PHP $PHP_VERSION only supports Laravel 8 and below" WARNINGS=$((WARNINGS + 1)) RECOMMENDATIONS+=("Upgrade to PHP 8.1+ for Laravel 9+ support") ;; "7.3"|"7.2"|"7.1"|"7.0") echo "❌ CRITICAL: PHP $PHP_VERSION is outdated and unsupported" CRITICAL_ISSUES=$((CRITICAL_ISSUES + 1)) RECOMMENDATIONS+=("Upgrade to PHP 8.1+ immediately for security and Laravel support") ;; *) echo "⚠️ UNKNOWN: PHP $PHP_VERSION compatibility unknown" WARNINGS=$((WARNINGS + 1)) ;; esac # Check for essential Laravel PHP extensions echo "? Checking essential Laravel PHP extensions..." REQUIRED_EXTENSIONS=( "openssl" "pdo" "mbstring" "tokenizer" "xml" "ctype" "json" "bcmath" "curl" "fileinfo" "zip" ) # These are the essential extensions for most Laravel applications echo "? Checking extensions required by typical Laravel applications..." MISSING_EXTENSIONS=() for ext in "${REQUIRED_EXTENSIONS[@]}"; do if php -m | grep -qi "^$ext$" 2>/dev/null; then echo " ✅ $ext: installed" else echo " ❌ $ext: MISSING" MISSING_EXTENSIONS+=("$ext") CRITICAL_ISSUES=$((CRITICAL_ISSUES + 1)) fi done if [ ${#MISSING_EXTENSIONS[@]} -gt 0 ]; then echo "❌ CRITICAL: Missing PHP extensions: ${MISSING_EXTENSIONS[*]}" RECOMMENDATIONS+=("Install missing PHP extensions: apt-get install $(printf 'php-*%s ' "${MISSING_EXTENSIONS[@]}")") fi # Pre1-03: Additional Server Capabilities echo "=== Additional Server Capabilities ===" # Check MySQL/MariaDB client if command -v mysql &> /dev/null; then MYSQL_VERSION=$(mysql --version 2>/dev/null | head -1) echo "✅ MySQL client: $MYSQL_VERSION" else echo "⚠️ MySQL client not found - database operations may be limited" WARNINGS=$((WARNINGS + 1)) RECOMMENDATIONS+=("Install MySQL client: apt-get install mysql-client") fi # Check Git if command -v git &> /dev/null; then GIT_VERSION=$(git --version 2>/dev/null) echo "✅ Git: $GIT_VERSION" else echo "❌ CRITICAL: Git not found - required for deployments" CRITICAL_ISSUES=$((CRITICAL_ISSUES + 1)) RECOMMENDATIONS+=("Install Git: apt-get install git") fi # Check Node.js (for frontend builds) if command -v node &> /dev/null; then NODE_VERSION=$(node --version 2>/dev/null) echo "✅ Node.js: $NODE_VERSION" if command -v npm &> /dev/null; then NPM_VERSION=$(npm --version 2>/dev/null) echo "✅ NPM: $NPM_VERSION" else echo "⚠️ NPM not found - frontend builds may fail" WARNINGS=$((WARNINGS + 1)) fi else echo "ℹ️ Node.js not found - only needed for frontend builds" fi # Pre1-04: General Build Recommendations echo "=== General Build Recommendations ===" echo "? Based on server environment, recommended commands for Laravel applications:" # Determine best composer command if command -v composer2 &> /dev/null && [[ "$COMPOSER2_MAJOR" == "2" ]]; then RECOMMENDED_COMPOSER="composer2" echo " ? For Laravel 9+: Use composer2 commands" elif [[ "$COMPOSER_MAJOR" == "2" ]]; then RECOMMENDED_COMPOSER="composer" echo " ? For Laravel 9+: Use composer commands" elif [[ "$COMPOSER_MAJOR" == "1" ]]; then RECOMMENDED_COMPOSER="composer" echo " ⚠️ For Laravel 8 only: Use composer commands (upgrade to Composer 2.x for newer Laravel)" else RECOMMENDED_COMPOSER="composer (INSTALL REQUIRED)" echo " ❌ Install Composer first" fi if [ "$RECOMMENDED_COMPOSER" != "composer (INSTALL REQUIRED)" ]; then echo "" echo "? Standard Laravel build commands:" echo " 1. Dependencies: $RECOMMENDED_COMPOSER install --no-dev --optimize-autoloader --no-interaction" echo " 2. Clear caches: $RECOMMENDED_COMPOSER clear-cache" echo " 3. Dump autoload: $RECOMMENDED_COMPOSER dump-autoload --optimize --no-dev" if command -v npm &> /dev/null; then echo " 4. Frontend deps: npm ci --production" echo " 5. Frontend build: npm run production" else echo " 4. Frontend: Node.js/NPM not available (install if needed for frontend builds)" fi fi # Pre1-05: Final Report echo "=== Server Environment Analysis Summary ===" echo "? Server Environment Status:" echo " ? Critical Issues: $CRITICAL_ISSUES" echo " ? Warnings: $WARNINGS" if [ $CRITICAL_ISSUES -gt 0 ]; then echo "" echo "❌ DEPLOYMENT BLOCKED - Critical server issues must be resolved" echo "" echo "? Required Server Actions:" for rec in "${RECOMMENDATIONS[@]}"; do echo " • $rec" done echo "" echo "? After resolving server issues, re-run deployment" exit 1 elif [ $WARNINGS -gt 0 ]; then echo "" echo "⚠️ DEPLOYMENT PROCEEDING WITH WARNINGS" echo "" echo "? Recommended Server Improvements:" for rec in "${RECOMMENDATIONS[@]}"; do echo " • $rec" done echo "" echo "✅ Server environment acceptable - proceeding to deployment phases..." else echo "" echo "✅ SERVER ENVIRONMENT OPTIMAL" echo "✅ All requirements satisfied - proceeding to deployment phases..." fi echo "=== PhaseA-Pre1 Server Analysis Complete ===" echo "✅ PhaseA-Pre1 completed successfully" ]

=== PhaseA-Pre1: Server Environment Analysis ===
? Analyzing server environment for Laravel application compatibility...
=== Server Composer Environment ===
? Current working directory: /home/u227177893
? Checking server Composer installation...
? System Composer Analysis:
   composer: Composer version 1.10.26 2022-04-13 16:39:56
   composer major version: 1
   composer2: Composer version 2.5.5 2023-03-21 11:50:05
   composer2 major version: 2
? Server Composer Status:
 EXCELLENT: composer2 (v2.x) available - supports modern Laravel applications
=== PHP Extensions Analysis ===
? Current PHP: 8.2.28
? Analyzing PHP version for Laravel compatibility...
 EXCELLENT: PHP 8.2.28 supports Laravel 10+ and all modern features
? Checking essential Laravel PHP extensions...
? Checking extensions required by typical Laravel applications...
    openssl: installed
    pdo: installed
    mbstring: installed
    tokenizer: installed
    xml: installed
    ctype: installed
    json: installed
    bcmath: installed
    curl: installed
    fileinfo: installed
    zip: installed
=== Additional Server Capabilities ===
 MySQL client: mysql  Ver 15.1 Distrib 10.11.10-MariaDB, for Linux (x86_64) using  EditLine wrapper
 Git: git version 2.43.5
 Node.js not found - only needed for frontend builds
=== General Build Recommendations ===
? Based on server environment, recommended commands for Laravel applications:
   ? For Laravel 9+: Use composer2 commands

? Standard Laravel build commands:
   1. Dependencies: composer2 install --no-dev --optimize-autoloader --no-interaction
   2. Clear caches: composer2 clear-cache
   3. Dump autoload: composer2 dump-autoload --optimize --no-dev
   4. Frontend: Node.js/NPM not available (install if needed for frontend builds)
=== Server Environment Analysis Summary ===
? Server Environment Status:
   ? Critical Issues: 0
   ? Warnings: 0

 SERVER ENVIRONMENT OPTIMAL
 All requirements satisfied - proceeding to deployment phases...
=== PhaseA-Pre1 Server Analysis Complete ===
 PhaseA-Pre1 completed successfully
Time taken to run PhaseA-Pre1: System Requirements Detection & Diagnosis: 2.78 seconds


Running SSH command Phase A: Pre-Deployment Commands (Before Upload)

Executing Phase A: Pre-Deployment Commands (Before Upload) [#!/bin/bash set -e # Phase A: Pre-Deployment Commands (Before Upload) # Purpose: System checks, backups, maintenance mode, environment preparation # Version 2 - PRODUCTION READY (Enhanced with deployment report fixes) echo "=== Phase A: Pre-Deployment Setup (V2) ===" # A01: System Pre-flight Checks echo "=== System Pre-flight Checks ===" php --version || { echo "❌ PHP not found"; exit 1; } # Check for Composer 2 (required for Laravel 12+) if command -v composer2 &> /dev/null; then composer2 --version echo "✅ Using Composer 2 for Laravel 12+ compatibility" export COMPOSER_CMD="composer2" elif composer --version | grep -q "version 2\."; then composer --version echo "✅ Composer 2 detected" export COMPOSER_CMD="composer" else composer --version echo "⚠️ WARNING: Composer 1.x detected, Laravel 12+ requires Composer 2.x" echo "ℹ️ Will attempt to use available composer, but may cause issues" export COMPOSER_CMD="composer" fi node --version 2>/dev/null || echo "ℹ️ Node not required" # Check disk space AVAILABLE_DISK=$(df -k "/home/u227177893/domains/staging.societypal.com/deploy" 2>/dev/null | awk 'NR==2 {print $4}') if [ "$AVAILABLE_DISK" -lt 524288 ]; then # 512MB minimum echo "❌ Insufficient disk space" exit 1 fi # A02: Create Universal Shared Structure (COMPREHENSIVE) echo "=== Initialize Universal Shared Structure ===" # Core Laravel directories (standard) mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/storage/{app/public,framework/{cache/data,sessions,views},logs} mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/bootstrap/cache mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/backups mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/.well-known # For SSL certificates # 1. User Content Variations Pattern (UNIVERSAL COVERAGE) echo "Creating user content directories..." mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/upload # Covers: upload mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/uploads # Covers: uploads mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/uploaded # Covers: uploaded mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/user-upload # Covers: user-upload mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/user-uploads # Covers: user-uploads mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/media # Covers: media mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/medias # Covers: medias mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/avatar # Covers: avatar mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/avatars # Covers: avatars mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/clientAvatar # Covers: clientAvatar mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/attachment # Covers: attachment mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/attachments # Covers: attachments mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/document # Covers: document mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/documents # Covers: documents mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/file # Covers: file mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/files # Covers: files mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/images # Covers: images (user-generated) # 2. User Generated Content (DYNAMIC CONTENT) echo "Creating generated content directories..." mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/qrcode # Generated QR codes mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/qrcodes # Generated QR codes (plural) mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/barcode # Generated barcodes mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/barcodes # Generated barcodes (plural) mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/certificate # Generated certificates mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/certificates # Generated certificates (plural) mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/report # Generated reports mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/reports # Generated reports (plural) mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/temp # Temporary files mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/temporary # Temporary files (alternative) # 3. CodeCanyon Application Specific (PRESERVATION PATTERNS) echo "Creating CodeCanyon-specific directories..." mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/storage/app # For modules_statuses.json (CRITICAL!) mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/Modules # Custom modules (root level) mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/favicon # Custom favicons # Set proper permissions for all shared directories echo "Setting permissions for shared directories..." chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/storage chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/bootstrap/cache chmod 755 /home/u227177893/domains/staging.societypal.com/deploy/shared/backups chmod 755 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/.well-known # User content permissions (writable) chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/upload* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/user-upload* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/media* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/avatar* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/attachment* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/document* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/file* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/images # Generated content permissions (writable) chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/qrcode* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/barcode* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/certificate* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/report* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/temp* # CodeCanyon specific permissions chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/storage/app # CRITICAL for modules_statuses.json chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/Modules chmod 755 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/favicon echo "✅ Shared structure ready for DeployHQ symlinks" # A03: Backup Current Production (if exists) echo "=== Backup Current Release ===" if [ -L "/home/u227177893/domains/staging.societypal.com/deploy/current" ] && [ -d "/home/u227177893/domains/staging.societypal.com/deploy/current" ]; then TIMESTAMP=$(date +"%Y%m%d_%H%M%S") BACKUP_DIR="/home/u227177893/domains/staging.societypal.com/deploy/shared/backups/release_${TIMESTAMP}" mkdir -p "$BACKUP_DIR" # Quick backup of critical files only cd /home/u227177893/domains/staging.societypal.com/deploy/current tar -czf "$BACKUP_DIR/app_backup.tar.gz" \ --exclude='vendor' \ --exclude='node_modules' \ --exclude='storage' \ --exclude='bootstrap/cache' \ app config database resources routes 2>/dev/null || true echo "✅ Backup created: $BACKUP_DIR" # Keep only last 5 backups cd /home/u227177893/domains/staging.societypal.com/deploy/shared/backups ls -t | tail -n +6 | xargs rm -rf 2>/dev/null || true fi # A04: Database Backup (ENHANCED VERSION - FIXED from deployment report) echo "=== Database Backup ===" if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ]; then echo "Parsing .env file for database credentials..." # Use PHP to safely parse .env file (more reliable than bash) cat > /tmp/parse_env_v2.php << 'EOF' <?php if (empty($argv[1]) || !is_file($argv[1])) { exit(1); } $lines = file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); $env = []; foreach ($lines as $line) { $line = trim($line); if (empty($line) || strpos($line, '#') === 0) { continue; } if (strpos($line, '=') === false) { continue; } list($name, $value) = explode('=', $line, 2); $name = trim($name); $value = trim($value); // Remove quotes if present if (preg_match('/^"(.*)"$/', $value, $matches)) { $value = $matches[1]; } elseif (preg_match('/^\'(.*)\'$/', $value, $matches)) { $value = $matches[1]; } $env[$name] = $value; } // Output only the variables we need echo "DB_CONNECTION=" . ($env['DB_CONNECTION'] ?? 'mysql') . "\n"; echo "DB_HOST=" . ($env['DB_HOST'] ?? 'localhost') . "\n"; echo "DB_PORT=" . ($env['DB_PORT'] ?? '3306') . "\n"; echo "DB_DATABASE=" . ($env['DB_DATABASE'] ?? '') . "\n"; echo "DB_USERNAME=" . ($env['DB_USERNAME'] ?? '') . "\n"; echo "DB_PASSWORD=" . ($env['DB_PASSWORD'] ?? '') . "\n"; EOF # Parse the .env file and extract variables ENV_VARS=$(php /tmp/parse_env_v2.php "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" 2>/dev/null) rm -f /tmp/parse_env_v2.php if [ -n "$ENV_VARS" ]; then # Load the variables into the current shell eval "$ENV_VARS" echo "Database configuration:" echo " Host: $DB_HOST" echo " Port: $DB_PORT" echo " Database: $DB_DATABASE" echo " Username: $DB_USERNAME" if [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then BACKUP_FILE="/home/u227177893/domains/staging.societypal.com/deploy/shared/backups/db_$(date +%Y%m%d_%H%M%S).sql.gz" echo "Creating database backup..." # Test database connection first (ENHANCED from deployment report) if command -v mysql &> /dev/null; then # Test connection with timeout if timeout 10 mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" 2>/dev/null; then echo "✅ Database connection test passed" # Create backup with better error handling if mysqldump -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" \ --single-transaction --routines --triggers --add-drop-table \ "$DB_DATABASE" 2>/dev/null | gzip > "$BACKUP_FILE"; then if [ -f "$BACKUP_FILE" ] && [ -s "$BACKUP_FILE" ]; then BACKUP_SIZE=$(du -sh "$BACKUP_FILE" | cut -f1) echo "✅ Database backed up: $BACKUP_FILE ($BACKUP_SIZE)" # Set secure permissions on backup chmod 600 "$BACKUP_FILE" else echo "⚠️ Database backup failed or empty" rm -f "$BACKUP_FILE" 2>/dev/null fi else echo "⚠️ mysqldump failed - check credentials and permissions" rm -f "$BACKUP_FILE" 2>/dev/null fi else echo "⚠️ Cannot connect to database - skipping backup" echo "ℹ️ This may be normal for first deployment or if database is not ready" fi else echo "⚠️ mysql client not available - skipping database backup" echo "ℹ️ Install mysql-client package if database backups are needed" fi else echo "⚠️ Incomplete database configuration in .env" echo "ℹ️ Ensure DB_DATABASE and DB_USERNAME are set" fi else echo "⚠️ Failed to parse .env file" echo "ℹ️ Check .env file syntax and permissions" fi else echo "ℹ️ No .env file found - skipping database backup" echo "ℹ️ This is normal for first deployment" fi # A05: Enter Maintenance Mode (ENHANCED VERSION) echo "=== Enter Maintenance Mode ===" # Create maintenance flag for all hosting types touch /home/u227177893/domains/staging.societypal.com/deploy/.maintenance # Try Laravel maintenance if current release exists if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/current/artisan" ]; then cd /home/u227177893/domains/staging.societypal.com/deploy/current # Enhanced error handling for artisan down command if php artisan down --render="errors::503" 2>/dev/null; then echo "✅ Laravel maintenance mode activated" else echo "⚠️ Laravel maintenance mode failed, using maintenance flag only" echo "ℹ️ This may be due to missing dependencies (will be fixed in Phase B)" fi else echo "ℹ️ No current release (first deployment) - using maintenance flag only" fi echo "✅ Phase A completed successfully" ]

=== Phase A: Pre-Deployment Setup (V2) ===
=== System Pre-flight Checks ===
PHP 8.2.28 (cli) (built: Mar 12 2025 00:00:00) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.2.28, Copyright (c) Zend Technologies
    with Zend OPcache v8.2.28, Copyright (c), by Zend Technologies
Composer version 2.5.5 2023-03-21 11:50:05
 Using Composer 2 for Laravel 12+ compatibility
 Node not required
=== Initialize Universal Shared Structure ===
Creating user content directories...
Creating generated content directories...
Creating CodeCanyon-specific directories...
Setting permissions for shared directories...
 Shared structure ready for DeployHQ symlinks
=== Backup Current Release ===
=== Database Backup ===
 No .env file found - skipping database backup
 This is normal for first deployment
=== Enter Maintenance Mode ===
 No current release (first deployment) - using maintenance flag only
 Phase A completed successfully
Time taken to run Phase A: Pre-Deployment Commands (Before Upload): 0.51 seconds


Preparing release directory


Transferring changed files



Show file destinations?
Uploading resources/views/vendor/mail/text/button.blade.php

Uploading resources/views/vendor/mail/text/footer.blade.php

Uploading resources/views/vendor/mail/text/header.blade.php

Uploading resources/views/vendor/mail/text/layout.blade.php

Uploading resources/views/vendor/mail/text/message.blade.php

Uploading resources/views/vendor/mail/text/panel.blade.php

Uploading resources/views/vendor/mail/text/subcopy.blade.php

Uploading resources/views/vendor/mail/text/table.blade.php

Uploading resources/views/vendor/notifications/email.blade.php

Uploading resources/views/vendor/translation-manager/.gitkeep

Uploading resources/views/vendor/translation-manager/index.php

Uploading resources/views/visitorsManagement/approval.blade.php

Uploading resources/views/visitorsManagement/filters.blade.php

Uploading resources/views/visitorsManagement/index.blade.php

Uploading resources/views/visitorsManagement/print.blade.php

Uploading resources/views/welcome.blade.php

Uploading routes/api.php

Uploading routes/console.php

Uploading routes/web.php

Uploading saas

Uploading societypro-saas

Uploading storage/.ignore_locales

Uploading storage/app/.gitignore

Uploading storage/app/private/.gitignore

Uploading storage/app/public/.gitignore

Uploading storage/debugbar/.gitignore

Uploading storage/framework/.gitignore

Uploading storage/framework/cache/.gitignore

Uploading storage/framework/cache/data/.gitignore

Uploading storage/framework/sessions/.gitignore

Uploading storage/framework/testing/.gitignore

Uploading storage/framework/views/.gitignore

Uploading storage/logs/.gitignore

Uploading tailwind.config.js

Uploading tests/Feature/ApiTokenPermissionsTest.php

Uploading tests/Feature/AuthenticationTest.php

Uploading tests/Feature/BrowserSessionsTest.php

Uploading tests/Feature/CreateApiTokenTest.php

Uploading tests/Feature/DeleteAccountTest.php

Uploading tests/Feature/DeleteApiTokenTest.php

Uploading tests/Feature/EmailVerificationTest.php

Uploading tests/Feature/ExampleTest.php

Uploading tests/Feature/PasswordConfirmationTest.php

Uploading tests/Feature/PasswordResetTest.php

Uploading tests/Feature/ProfileInformationTest.php

Uploading tests/Feature/RegistrationTest.php

Uploading tests/Feature/TwoFactorAuthenticationSettingsTest.php

Uploading tests/Feature/UpdatePasswordTest.php

Uploading tests/TestCase.php

Uploading tests/Unit/ExampleTest.php


Uploading config files

Uploading /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/.env


Linking files from shared path to release

Symlinking backups to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/backups

Symlinking bootstrap/cache to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/bootstrap/cache

Symlinking Modules to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/Modules

Symlinking public/favicon to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/favicon

Symlinking public/avatars to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/avatars

Symlinking public/documents to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/documents

Symlinking public/clientAvatar to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/clientAvatar

Symlinking public/barcodes to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/barcodes

Symlinking public/attachments to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/attachments

Symlinking public/.well-known to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/.well-known

Symlinking public/attachment to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/attachment

Symlinking public/temporary to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/temporary

Symlinking public/user-upload to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/user-upload

Symlinking public/qrcodes to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/qrcodes

Symlinking public/document to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/document

Symlinking public/barcode to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/barcode

Symlinking public/temp to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/temp

Symlinking public/files to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/files

Symlinking public/report to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/report

Symlinking public/certificate to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/certificate

Symlinking public/file to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/file

Symlinking public/uploads to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/uploads

Symlinking public/images to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/images

Symlinking public/certificates to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/certificates

Symlinking public/avatar to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/avatar

Symlinking public/qrcode to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/qrcode

Symlinking public/medias to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/medias

Symlinking public/media to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/media

Symlinking public/reports to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/reports

Symlinking public/uploaded to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/uploaded

Symlinking public/upload to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/upload


Running SSH command Phase B-First: Symlink Fallback Verification (After Upload, Before Release)

Executing Phase B-First: Symlink Fallback Verification (After Upload, Before Release) [#!/bin/bash set -e # Phase B-First: Symlink Fallback Verification (After Upload, Before Release) # Purpose: Verify and create missing core Laravel symlinks that DeployHQ might not create # Timing: After file upload, before current symlink switch - RUNS BEFORE Phase B # Version 2.0 - PRODUCTION READY echo "=== Phase B-First: Symlink Fallback Verification ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814 # A-Post-01: Verify Core Laravel Symlinks echo "=== Verifying Core Laravel Symlinks ===" # Check if storage symlink exists and is correct if [ ! -L "storage" ] || [ ! -e "storage" ]; then echo "⚠️ Storage symlink missing or broken - creating fallback symlink" rm -rf storage 2>/dev/null || true ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/storage storage echo "✅ Created storage → shared/storage symlink" else STORAGE_TARGET=$(readlink storage) echo "✅ Storage symlink exists: storage → $STORAGE_TARGET" fi # Check if bootstrap/cache symlink exists and is correct if [ ! -L "bootstrap/cache" ] || [ ! -e "bootstrap/cache" ]; then echo "⚠️ Bootstrap cache symlink missing or broken - creating fallback symlink" rm -rf bootstrap/cache 2>/dev/null || true ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/bootstrap/cache bootstrap/cache echo "✅ Created bootstrap/cache → shared/bootstrap/cache symlink" else BOOTSTRAP_TARGET=$(readlink bootstrap/cache) echo "✅ Bootstrap cache symlink exists: bootstrap/cache → $BOOTSTRAP_TARGET" fi # A-Post-02: Verify .env Symlink (Critical) echo "=== Verifying Environment Symlink ===" if [ ! -L ".env" ] || [ ! -e ".env" ]; then echo "⚠️ .env symlink missing or broken - creating fallback symlink" rm -rf .env 2>/dev/null || true ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/.env .env echo "✅ Created .env → shared/.env symlink" else ENV_TARGET=$(readlink .env) echo "✅ .env symlink exists: .env → $ENV_TARGET" fi # A-Post-03: Verify Modules Symlink (CodeCanyon) echo "=== Verifying Modules Symlink ===" if [ -d "/home/u227177893/domains/staging.societypal.com/deploy/shared/Modules" ]; then if [ ! -L "Modules" ] || [ ! -e "Modules" ]; then echo "⚠️ Modules symlink missing or broken - creating fallback symlink" rm -rf Modules 2>/dev/null || true ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/Modules Modules echo "✅ Created Modules → shared/Modules symlink" else MODULES_TARGET=$(readlink Modules) echo "✅ Modules symlink exists: Modules → $MODULES_TARGET" fi else echo "ℹ️ No shared Modules directory found - skipping" fi # A-Post-04: Verify Backups Symlink echo "=== Verifying Backups Symlink ===" if [ ! -L "backups" ] || [ ! -e "backups" ]; then echo "⚠️ Backups symlink missing or broken - creating fallback symlink" rm -rf backups 2>/dev/null || true ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/backups backups echo "✅ Created backups → shared/backups symlink" else BACKUPS_TARGET=$(readlink backups) echo "✅ Backups symlink exists: backups → $BACKUPS_TARGET" fi # A-Post-05: Initialize Storage Structure (if needed) echo "=== Initializing Storage Structure ===" if [ -L "storage" ] && [ -e "storage" ]; then # Only create structure if storage is properly symlinked mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/storage/{app/public,framework/{cache/data,sessions,views},logs} chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/storage echo "✅ Storage structure initialized" else echo "❌ Storage symlink not working - structure initialization skipped" fi # A-Post-06: Final Verification Report echo "=== Symlink Verification Summary ===" SYMLINK_ISSUES=0 # Check all critical symlinks for LINK in storage bootstrap/cache .env backups; do if [ -L "$LINK" ] && [ -e "$LINK" ]; then TARGET=$(readlink "$LINK") echo "✅ $LINK → $TARGET" else echo "❌ $LINK: BROKEN OR MISSING" SYMLINK_ISSUES=$((SYMLINK_ISSUES + 1)) fi done # Check Modules if it should exist if [ -d "/home/u227177893/domains/staging.societypal.com/deploy/shared/Modules" ]; then if [ -L "Modules" ] && [ -e "Modules" ]; then MODULES_TARGET=$(readlink Modules) echo "✅ Modules → $MODULES_TARGET" else echo "❌ Modules: BROKEN OR MISSING" SYMLINK_ISSUES=$((SYMLINK_ISSUES + 1)) fi fi if [ $SYMLINK_ISSUES -eq 0 ]; then echo "? All critical symlinks verified successfully!" else echo "⚠️ $SYMLINK_ISSUES symlink issues detected - may need manual intervention" fi echo "✅ Phase B-First symlink verification completed successfully" ]

=== Phase B-First: Symlink Fallback Verification ===
=== Verifying Core Laravel Symlinks ===
 Storage symlink missing or broken - creating fallback symlink
 Created storage  shared/storage symlink
 Bootstrap cache symlink exists: bootstrap/cache  ../../../shared/bootstrap/cache
=== Verifying Environment Symlink ===
 .env symlink missing or broken - creating fallback symlink
 Created .env  shared/.env symlink
=== Verifying Modules Symlink ===
 Modules symlink exists: Modules  ../../shared/Modules
=== Verifying Backups Symlink ===
 Backups symlink exists: backups  ../../shared/backups
=== Initializing Storage Structure ===
 Storage structure initialized
=== Symlink Verification Summary ===
 storage  /home/u227177893/domains/staging.societypal.com/deploy/shared/storage
 bootstrap/cache  ../../../shared/bootstrap/cache
 .env: BROKEN OR MISSING
 backups  ../../shared/backups
 Modules  ../../shared/Modules
 1 symlink issues detected - may need manual intervention
 Phase B-First symlink verification completed successfully
Time taken to run Phase B-First: Symlink Fallback Verification (After Upload, Before Release): 0.33 seconds


Running SSH command Phase B: Pre-Release Commands (After Upload, Before Release)

Executing Phase B: Pre-Release Commands (After Upload, Before Release) [#!/bin/bash set -e # Phase B: Pre-Release Commands (After Upload, Before Release) # Purpose: Configure release, set security, link shared resources # Note: DeployHQ has already uploaded files to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814 # Version 2 - PRODUCTION READY (Enhanced with deployment report fixes) echo "=== Phase B: Pre-Release Configuration (V2) ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814 # B01: Environment Setup (ENHANCED VERSION) echo "=== Environment Configuration ===" # DeployHQ has uploaded .env to release - handle it properly if [ ! -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ]; then # First deployment: move uploaded .env to shared if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/.env" ]; then mv /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/.env /home/u227177893/domains/staging.societypal.com/deploy/shared/.env echo "✅ Moved uploaded .env to shared (first deployment)" elif [ -f "/home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/.env.example" ]; then cp /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/.env.example /home/u227177893/domains/staging.societypal.com/deploy/shared/.env echo "⚠️ Created .env from example - configure database!" else echo "❌ No .env file found - this may cause issues" fi # Generate APP_KEY if missing if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ]; then if ! grep -q "^APP_KEY=base64:" /home/u227177893/domains/staging.societypal.com/deploy/shared/.env; then KEY=$(php -r 'echo "base64:".base64_encode(random_bytes(32));') sed -i "s|^APP_KEY=.*|APP_KEY=$KEY|" /home/u227177893/domains/staging.societypal.com/deploy/shared/.env echo "✅ Generated APP_KEY" fi fi else # Subsequent deployment: remove uploaded .env (we use shared one) if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/.env" ]; then rm -f /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/.env echo "✅ Removed uploaded .env (using shared version)" fi fi # Set secure permissions on shared .env chmod 600 /home/u227177893/domains/staging.societypal.com/deploy/shared/.env 2>/dev/null || true # CRITICAL: Ensure .env symlink exists (DeployHQ sometimes fails to create it) if [ ! -L "/home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/.env" ]; then echo "⚠️ .env symlink missing - creating it manually" ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/.env /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/.env echo "✅ Created .env symlink: .env -> ../../shared/.env" else echo "✅ .env symlink already exists" fi # B02: Security - Create/Update .htaccess files (ENHANCED VERSION) echo "=== Security Configuration ===" # Root .htaccess (redirect all to public) cat > /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/.htaccess << 'EOF' <IfModule mod_rewrite.c> RewriteEngine On RewriteRule ^(.*)$ public/$1 [L] </IfModule> # Deny access to sensitive files <FilesMatch "^\."> Order allow,deny Deny from all </FilesMatch> <Files "composer.json"> Order allow,deny Deny from all </Files> <Files "package.json"> Order allow,deny Deny from all </Files> <Files "artisan"> Order allow,deny Deny from all </Files> # Additional security headers <IfModule mod_headers.c> Header always set X-Content-Type-Options nosniff Header always set X-Frame-Options DENY Header always set X-XSS-Protection "1; mode=block" Header always set Referrer-Policy "strict-origin-when-cross-origin" </IfModule> EOF # Public .htaccess for Laravel (ENHANCED VERSION) cat > /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814/public/.htaccess << 'EOF' <IfModule mod_rewrite.c> <IfModule mod_negotiation.c> Options -MultiViews -Indexes </IfModule> RewriteEngine On # Handle Authorization Header RewriteCond %{HTTP:Authorization} . RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}] # Redirect Trailing Slashes If Not A Folder... RewriteCond %{REQUEST_FILENAME} !-d RewriteCond %{REQUEST_URI} (.+)/$ RewriteRule ^ %1 [L,R=301] # Send Requests To Front Controller... RewriteCond %{REQUEST_FILENAME} !-d RewriteCond %{REQUEST_FILENAME} !-f RewriteRule ^ index.php [L] </IfModule> # Security Headers <IfModule mod_headers.c> Header set X-Content-Type-Options "nosniff" Header set X-Frame-Options "SAMEORIGIN" Header set X-XSS-Protection "1; mode=block" Header set Referrer-Policy "strict-origin-when-cross-origin" Header set Permissions-Policy "geolocation=(), microphone=(), camera=()" </IfModule> # Disable directory browsing Options -Indexes # Block access to hidden files except .well-known <Files ".*"> <IfModule mod_authz_core.c> Require all denied </IfModule> </Files> <FilesMatch "^\.well-known"> <IfModule mod_authz_core.c> Require all granted </IfModule> </FilesMatch> # Compress text files <IfModule mod_deflate.c> AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json </IfModule> # Cache static assets <IfModule mod_expires.c> ExpiresActive On ExpiresByType image/jpg "access plus 1 month" ExpiresByType image/jpeg "access plus 1 month" ExpiresByType image/gif "access plus 1 month" ExpiresByType image/png "access plus 1 month" ExpiresByType text/css "access plus 1 week" ExpiresByType application/javascript "access plus 1 week" </IfModule> EOF echo "✅ Security configurations created" # B03: Fix Composer Dependencies (ENHANCED VERSION - CRITICAL FIX) echo "=== Verify Dependencies ===" # Use Composer 2 if available (required for Laravel 12+) COMPOSER_CMD="composer" if command -v composer2 &> /dev/null; then COMPOSER_CMD="composer2" echo "✅ Using Composer 2 for Laravel 12+ compatibility" elif composer --version | grep -q "version 1\."; then echo "⚠️ WARNING: Using Composer 1.x with Laravel 12+ may cause issues" fi if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then echo "Installing composer dependencies with $COMPOSER_CMD..." if $COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction; then echo "✅ Dependencies installed successfully" else echo "❌ Composer install failed" echo "Checking Composer version compatibility..." $COMPOSER_CMD --version exit 1 fi else echo "✅ Dependencies already installed" fi # B04: Enhanced Inertia Detection (IMPROVED - Multiple Location Check) echo "=== Comprehensive Inertia Detection ===" INERTIA_DETECTED=false # Check 1: composer.json for inertiajs/inertia-laravel if [ -f "composer.json" ] && grep -q "inertiajs/inertia-laravel" composer.json 2>/dev/null; then echo "✅ Inertia detected in composer.json" INERTIA_DETECTED=true fi # Check 2: config/app.php for Inertia ServiceProvider if [ -f "config/app.php" ] && grep -q "Inertia\\\ServiceProvider" config/app.php 2>/dev/null; then echo "✅ Inertia ServiceProvider detected in config/app.php" INERTIA_DETECTED=true fi # Check 3: package.json for @inertiajs/ packages if [ -f "package.json" ] && grep -q "@inertiajs/" package.json 2>/dev/null; then echo "✅ Inertia frontend packages detected in package.json" INERTIA_DETECTED=true fi # Check 4: vendor directory for actual installation if [ -d "vendor/inertiajs" ] && [ -f "vendor/inertiajs/inertia-laravel/src/ServiceProvider.php" ]; then echo "✅ Inertia Laravel package found in vendor" INERTIA_DETECTED=true fi if [ "$INERTIA_DETECTED" = true ]; then echo "? Inertia detected - verifying installation..." # Verify Inertia is actually installed in vendor if [ ! -d "vendor/inertiajs" ] || [ ! -f "vendor/inertiajs/inertia-laravel/src/ServiceProvider.php" ]; then echo "⚠️ Inertia detected in config but not installed - installing..." $COMPOSER_CMD require inertiajs/inertia-laravel --no-interaction 2>/dev/null || { echo "⚠️ Failed to install Inertia via $COMPOSER_CMD, trying alternative..." # Try to install from source if composer fails if [ -f "composer.json" ]; then # Add Inertia to composer.json if not present if ! grep -q "inertiajs/inertia-laravel" composer.json; then echo "Adding Inertia to composer.json..." sed -i '/"require": {/a\ "inertiajs/inertia-laravel": "^0.6.0",' composer.json $COMPOSER_CMD update --no-dev --optimize-autoloader --no-interaction 2>/dev/null || true fi fi } else echo "✅ Inertia Laravel package properly installed" fi # Additional check: Verify Inertia middleware exists if [ -f "app/Http/Kernel.php" ] && grep -q "HandleInertiaRequests" app/Http/Kernel.php 2>/dev/null; then echo "✅ Inertia middleware detected" else echo "ℹ️ Inertia middleware not found (may need manual setup)" fi else echo "ℹ️ Inertia not detected in any location (composer.json, config/app.php, package.json, vendor)" fi # B05: Set Secure Permissions (ENHANCED VERSION) echo "=== Setting Secure Permissions ===" # Default secure permissions for release files find . -type f -exec chmod 644 {} \; find . -type d -exec chmod 755 {} \; # Make artisan executable [ -f "artisan" ] && chmod 755 artisan # Make any shell scripts executable find . -name "*.sh" -type f -exec chmod 755 {} \; 2>/dev/null || true # Protect .git if exists in release [ -d ".git" ] && chmod -R 700 .git # Note: Shared directories permissions are set in Phase A # DeployHQ symlinks will inherit the shared directory permissions echo "✅ Release permissions secured" # B06: Database Migrations (ENHANCED VERSION with better error handling) echo "=== Database Operations ===" if [ -f "artisan" ]; then # Wait for DeployHQ to create .env symlink, then test database echo "Testing database connection..." # Give DeployHQ a moment to create symlinks if needed sleep 1 if [ -f ".env" ] || [ -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ]; then # Test database connection using Laravel's built-in method if php artisan tinker --execute="echo 'DB connection test: '; try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAILED: ' . \$e->getMessage(); }" 2>/dev/null | grep -q "OK"; then echo "✅ Database connection successful, running migrations..." php artisan migrate --force || echo "⚠️ Migration failed but continuing" echo "✅ Migrations completed" else echo "⚠️ Database not accessible - skip migrations" echo "ℹ️ This may be normal for first deployment or if database is not configured" fi else echo "⚠️ No .env file available - skip migrations" echo "ℹ️ DeployHQ will create .env symlink after this phase" fi else echo "ℹ️ No artisan file found - not a Laravel application" fi # B07: Laravel Optimization (ENHANCED VERSION - NO BUILD DEPENDENCIES) echo "=== Application Optimization ===" if [ -f "artisan" ]; then # Clear old caches first echo "Clearing old caches..." php artisan cache:clear 2>/dev/null || echo "ℹ️ Cache clear skipped" php artisan config:clear 2>/dev/null || echo "ℹ️ Config clear skipped" php artisan route:clear 2>/dev/null || echo "ℹ️ Route clear skipped" php artisan view:clear 2>/dev/null || echo "ℹ️ View clear skipped" # Build new caches with better error handling echo "Building new caches..." if php artisan config:cache 2>/dev/null; then echo "✅ Configuration cached" else echo "⚠️ Config cache failed - will retry in Phase C" fi if php artisan route:cache 2>/dev/null; then echo "✅ Routes cached" else echo "⚠️ Route cache failed - will retry in Phase C" fi if php artisan view:cache 2>/dev/null; then echo "✅ Views cached" else echo "⚠️ View cache failed - will retry in Phase C" fi # Optional: Event cache (if using events) if [ -f "app/Providers/EventServiceProvider.php" ]; then php artisan event:cache 2>/dev/null && echo "✅ Events cached" || echo "ℹ️ Event cache skipped" fi # Note: Storage link will be created in Phase C after current symlink exists echo "ℹ️ Storage link will be created in Phase C" else echo "ℹ️ No artisan file - skipping Laravel optimization" fi # B09: Universal Content Symlinks (COMPREHENSIVE COVERAGE) echo "=== Creating Universal Content Symlinks ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814 # Function to create symlink if directory exists in release create_content_symlink() { local DIR_PATH="$1" local DIR_NAME=$(basename "$DIR_PATH") if [ -d "$DIR_PATH" ] && [ ! -L "$DIR_PATH" ]; then SHARED_DIR="/home/u227177893/domains/staging.societypal.com/deploy/shared/public/$DIR_NAME" # Migrate any existing content to shared (first deployment) if [ "$(ls -A "$DIR_PATH" 2>/dev/null)" ]; then echo "? Migrating existing content: $DIR_NAME" mkdir -p "$SHARED_DIR" cp -r "$DIR_PATH"/* "$SHARED_DIR"/ 2>/dev/null || true chmod -R 775 "$SHARED_DIR" 2>/dev/null || true fi # Remove directory and create symlink rm -rf "$DIR_PATH" ln -sf "../../shared/public/$DIR_NAME" "$DIR_PATH" echo "✅ Created symlink: $DIR_NAME -> shared/public/$DIR_NAME" elif [ -L "$DIR_PATH" ]; then echo "ℹ️ Symlink already exists: $DIR_NAME" fi } # 1. User Content Variations Pattern echo "Processing user content directories..." create_content_symlink "public/upload" create_content_symlink "public/uploads" create_content_symlink "public/uploaded" create_content_symlink "public/user-upload" create_content_symlink "public/user-uploads" create_content_symlink "public/media" create_content_symlink "public/medias" create_content_symlink "public/avatar" create_content_symlink "public/avatars" create_content_symlink "public/clientAvatar" create_content_symlink "public/attachment" create_content_symlink "public/attachments" create_content_symlink "public/document" create_content_symlink "public/documents" create_content_symlink "public/file" create_content_symlink "public/files" create_content_symlink "public/images" # 2. User Generated Content echo "Processing generated content directories..." create_content_symlink "public/qrcode" create_content_symlink "public/qrcodes" create_content_symlink "public/barcode" create_content_symlink "public/barcodes" create_content_symlink "public/certificate" create_content_symlink "public/certificates" create_content_symlink "public/report" create_content_symlink "public/reports" create_content_symlink "public/temp" create_content_symlink "public/temporary" # 3. CodeCanyon Specific Patterns echo "Processing CodeCanyon-specific patterns..." # Handle favicon files (preserve custom favicons) if [ -f "public/favicon.ico" ]; then if [ ! -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/public/favicon/favicon.ico" ]; then echo "? Preserving custom favicon.ico" mkdir -p "/home/u227177893/domains/staging.societypal.com/deploy/shared/public/favicon" cp "public/favicon.ico" "/home/u227177893/domains/staging.societypal.com/deploy/shared/public/favicon/" chmod 644 "/home/u227177893/domains/staging.societypal.com/deploy/shared/public/favicon/favicon.ico" fi rm -f "public/favicon.ico" ln -sf "../shared/public/favicon/favicon.ico" "public/favicon.ico" echo "✅ Created symlink: favicon.ico -> shared" fi # Handle Modules directory (root level) if [ -d "Modules" ] && [ ! -L "Modules" ]; then echo "? Preserving Modules directory" if [ "$(ls -A "Modules" 2>/dev/null)" ]; then mkdir -p "/home/u227177893/domains/staging.societypal.com/deploy/shared/Modules" cp -r "Modules"/* "/home/u227177893/domains/staging.societypal.com/deploy/shared/Modules"/ 2>/dev/null || true chmod -R 775 "/home/u227177893/domains/staging.societypal.com/deploy/shared/Modules" 2>/dev/null || true fi rm -rf "Modules" ln -sf "../shared/Modules" "Modules" echo "✅ Created symlink: Modules -> shared/Modules" elif [ -L "Modules" ]; then echo "ℹ️ Modules symlink already exists" fi # Handle modules_statuses.json (in storage/app) if [ -f "storage/app/modules_statuses.json" ]; then echo "? Preserving modules_statuses.json" mkdir -p "/home/u227177893/domains/staging.societypal.com/deploy/shared/storage/app" cp "storage/app/modules_statuses.json" "/home/u227177893/domains/staging.societypal.com/deploy/shared/storage/app/" 2>/dev/null || true chmod 644 "/home/u227177893/domains/staging.societypal.com/deploy/shared/storage/app/modules_statuses.json" 2>/dev/null || true echo "✅ Preserved modules_statuses.json in shared storage" fi echo "✅ Universal content symlinks completed" # Note: No build commands here - build pipeline is handled separately echo "ℹ️ Build pipeline handled separately - no build dependencies" echo "✅ Phase B completed successfully" ]

=== Phase B: Pre-Release Configuration (V2) ===
=== Environment Configuration ===
 Created .env from example - configure database!
 .env symlink already exists
=== Security Configuration ===
 Security configurations created
=== Verify Dependencies ===
 Using Composer 2 for Laravel 12+ compatibility
Installing composer dependencies with composer2...
Installing dependencies from lock file
Verifying lock file contents can be installed on current platform.
Package operations: 136 installs, 0 updates, 0 removals
  - Downloading wikimedia/composer-merge-plugin (v2.1.0)
  - Downloading aws/aws-crt-php (v1.2.7)
  - Downloading dasprid/enum (1.0.6)
  - Downloading bacon/bacon-qr-code (v3.0.1)
  - Downloading voku/portable-ascii (2.0.3)
  - Downloading symfony/polyfill-php80 (v1.32.0)
  - Downloading symfony/polyfill-mbstring (v1.32.0)
  - Downloading symfony/polyfill-ctype (v1.32.0)
  - Downloading phpoption/phpoption (1.9.3)
  - Downloading graham-campbell/result-type (v1.1.3)
  - Downloading vlucas/phpdotenv (v5.6.2)
  - Downloading symfony/css-selector (v7.3.0)
  - Downloading tijsverkoyen/css-to-inline-styles (v2.3.0)
  - Downloading symfony/deprecation-contracts (v3.6.0)
  - Downloading symfony/var-dumper (v7.3.0)
  - Downloading symfony/polyfill-uuid (v1.32.0)
  - Downloading symfony/uid (v7.3.0)
  - Downloading symfony/routing (v7.3.0)
  - Downloading symfony/process (v7.3.0)
  - Downloading symfony/polyfill-php83 (v1.32.0)
  - Downloading symfony/polyfill-intl-normalizer (v1.32.0)
  - Downloading symfony/polyfill-intl-idn (v1.32.0)
  - Downloading symfony/mime (v7.3.0)
  - Downloading psr/container (2.0.2)
  - Downloading symfony/service-contracts (v3.6.0)
  - Downloading psr/event-dispatcher (1.0.0)
  - Downloading symfony/event-dispatcher-contracts (v3.6.0)
  - Downloading symfony/event-dispatcher (v7.3.0)
  - Downloading psr/log (3.0.2)
  - Downloading doctrine/lexer (3.0.1)
  - Downloading egulias/email-validator (4.0.4)
  - Downloading symfony/mailer (v7.3.0)
  - Downloading symfony/http-foundation (v7.3.0)
  - Downloading symfony/error-handler (v7.3.0)
  - Downloading symfony/http-kernel (v7.3.0)
  - Downloading symfony/finder (v7.3.0)
  - Downloading symfony/polyfill-intl-grapheme (v1.32.0)
  - Downloading symfony/string (v7.3.0)
  - Downloading symfony/console (v7.3.0)
  - Downloading ramsey/collection (2.1.1)
  - Downloading brick/math (0.13.1)
  - Downloading ramsey/uuid (4.8.1)
  - Downloading psr/simple-cache (3.0.0)
  - Downloading nunomaduro/termwind (v2.3.1)
  - Downloading symfony/translation-contracts (v3.6.0)
  - Downloading symfony/translation (v7.3.0)
  - Downloading psr/clock (1.0.0)
  - Downloading symfony/clock (v7.3.0)
  - Downloading carbonphp/carbon-doctrine-types (3.2.0)
  - Downloading nesbot/carbon (3.9.1)
  - Downloading monolog/monolog (3.9.0)
  - Downloading psr/http-message (2.0)
  - Downloading psr/http-factory (1.1.0)
  - Downloading league/uri-interfaces (7.5.0)
  - Downloading league/uri (7.5.1)
  - Downloading league/mime-type-detection (1.16.0)
  - Downloading league/flysystem-local (3.29.0)
  - Downloading league/flysystem (3.29.1)
  - Downloading nette/utils (v4.0.7)
  - Downloading nette/schema (v1.3.2)
  - Downloading dflydev/dot-access-data (v3.0.3)
  - Downloading league/config (v1.2.0)
  - Downloading league/commonmark (2.7.0)
  - Downloading laravel/serializable-closure (v2.0.4)
  - Downloading laravel/prompts (v0.3.5)
  - Downloading guzzlehttp/uri-template (v1.0.4)
  - Downloading psr/http-client (1.0.3)
  - Downloading ralouphie/getallheaders (3.0.3)
  - Downloading guzzlehttp/psr7 (2.7.1)
  - Downloading guzzlehttp/promises (2.2.0)
  - Downloading guzzlehttp/guzzle (7.9.3)
  - Downloading fruitcake/php-cors (v1.3.0)
  - Downloading webmozart/assert (1.11.0)
  - Downloading dragonmantank/cron-expression (v3.4.0)
  - Downloading doctrine/inflector (2.0.10)
  - Downloading laravel/framework (v12.17.0)
  - Downloading masterminds/html5 (2.9.0)
  - Downloading sabberworm/php-css-parser (v8.8.0)
  - Downloading dompdf/php-svg-lib (1.0.0)
  - Downloading dompdf/php-font-lib (1.0.1)
  - Downloading dompdf/dompdf (v3.1.0)
  - Downloading barryvdh/laravel-dompdf (v3.1.1)
  - Downloading barryvdh/laravel-translation-manager (v0.6.8)
  - Downloading billowapp/payfast (0.6.6)
  - Downloading blade-ui-kit/blade-icons (1.8.0)
  - Downloading blade-ui-kit/blade-heroicons (2.6.0)
  - Downloading php-http/promise (1.3.1)
  - Downloading php-http/httplug (2.4.1)
  - Downloading php-http/guzzle7-adapter (1.1.0)
  - Downloading composer/ca-bundle (1.5.7)
  - Downloading flutterwavedev/flutterwave-v3 (1.1.0)
  - Downloading froiden/envato (5.1.1)
  - Downloading froiden/laravel-installer (11.0.0)
  - Downloading froiden/laravel-rest-api (12.0.0)
  - Downloading intervention/image (2.7.2)
  - Downloading livewire/livewire (v3.6.3)
  - Downloading jantinnerezo/livewire-alert (v3.0.3)
  - Downloading ladumor/laravel-pwa (v0.0.4)
  - Downloading symfony/polyfill-intl-icu (v1.32.0)
  - Downloading stripe/stripe-php (v16.6.0)
  - Downloading moneyphp/money (v4.7.1)
  - Downloading laravel/cashier (v15.6.4)
  - Downloading psr/cache (3.0.0)
  - Downloading mobiledetect/mobiledetectlib (4.8.09)
  - Downloading paragonie/constant_time_encoding (v3.0.0)
  - Downloading pragmarx/google2fa (v8.0.3)
  - Downloading laravel/fortify (v1.25.4)
  - Downloading laravel/jetstream (v5.3.6)
  - Downloading laravel/sanctum (v4.1.1)
  - Downloading nikic/php-parser (v5.5.0)
  - Downloading psy/psysh (v0.12.8)
  - Downloading laravel/tinker (v2.10.1)
  - Downloading mtdowling/jmespath.php (2.8.0)
  - Downloading aws/aws-sdk-php (3.344.2)
  - Downloading league/flysystem-aws-s3-v3 (3.29.0)
  - Downloading markbaker/matrix (3.0.1)
  - Downloading markbaker/complex (3.0.2)
  - Downloading maennchen/zipstream-php (3.1.2)
  - Downloading ezyang/htmlpurifier (v4.18.0)
  - Downloading composer/pcre (3.3.2)
  - Downloading phpoffice/phpspreadsheet (1.29.10)
  - Downloading composer/semver (3.4.3)
  - Downloading maatwebsite/excel (3.1.64)
  - Downloading macellan/laravel-zip (1.0.5)
  - Downloading spomky-labs/pki-framework (1.2.3)
  - Downloading web-token/jwt-library (4.0.4)
  - Downloading spomky-labs/base64url (v2.0.4)
  - Downloading minishlink/web-push (v9.0.2)
  - Downloading nwidart/laravel-modules (v12.0.3)
  - Downloading paypal/rest-api-sdk-php (dev-laravel10 d5c36a2)
  - Downloading rmccue/requests (v2.0.15)
  - Downloading razorpay/razorpay (2.9.1)
  - Downloading spatie/laravel-permission (6.19.0)
  - Downloading spatie/laravel-package-tools (1.92.4)
  - Downloading spatie/laravel-translatable (6.11.4)
  - Downloading unicodeveloper/laravel-paystack (dev-l12-compatibility ae92cdc)
   0/136 [>---------------------------]   0%  18/136 [===>------------------------]  13%  31/136 [======>---------------------]  22%  44/136 [=========>------------------]  32%  61/136 [============>---------------]  44%  68/136 [==============>-------------]  50%  84/136 [=================>----------]  61% 100/136 [====================>-------]  73% 111/136 [======================>-----]  81% 127/136 [==========================>-]  93% 136/136 [============================] 100%  - Installing wikimedia/composer-merge-plugin (v2.1.0): Extracting archive
  - Installing aws/aws-crt-php (v1.2.7): Extracting archive
  - Installing dasprid/enum (1.0.6): Extracting archive
  - Installing bacon/bacon-qr-code (v3.0.1): Extracting archive
  - Installing voku/portable-ascii (2.0.3): Extracting archive
  - Installing symfony/polyfill-php80 (v1.32.0): Extracting archive
  - Installing symfony/polyfill-mbstring (v1.32.0): Extracting archive
  - Installing symfony/polyfill-ctype (v1.32.0): Extracting archive
  - Installing phpoption/phpoption (1.9.3): Extracting archive
  - Installing graham-campbell/result-type (v1.1.3): Extracting archive
  - Installing vlucas/phpdotenv (v5.6.2): Extracting archive
  - Installing symfony/css-selector (v7.3.0): Extracting archive
  - Installing tijsverkoyen/css-to-inline-styles (v2.3.0): Extracting archive
  - Installing symfony/deprecation-contracts (v3.6.0): Extracting archive
  - Installing symfony/var-dumper (v7.3.0): Extracting archive
  - Installing symfony/polyfill-uuid (v1.32.0): Extracting archive
  - Installing symfony/uid (v7.3.0): Extracting archive
  - Installing symfony/routing (v7.3.0): Extracting archive
  - Installing symfony/process (v7.3.0): Extracting archive
  - Installing symfony/polyfill-php83 (v1.32.0): Extracting archive
  - Installing symfony/polyfill-intl-normalizer (v1.32.0): Extracting archive
  - Installing symfony/polyfill-intl-idn (v1.32.0): Extracting archive
  - Installing symfony/mime (v7.3.0): Extracting archive
  - Installing psr/container (2.0.2): Extracting archive
  - Installing symfony/service-contracts (v3.6.0): Extracting archive
  - Installing psr/event-dispatcher (1.0.0): Extracting archive
  - Installing symfony/event-dispatcher-contracts (v3.6.0): Extracting archive
  - Installing symfony/event-dispatcher (v7.3.0): Extracting archive
  - Installing psr/log (3.0.2): Extracting archive
  - Installing doctrine/lexer (3.0.1): Extracting archive
  - Installing egulias/email-validator (4.0.4): Extracting archive
  - Installing symfony/mailer (v7.3.0): Extracting archive
  - Installing symfony/http-foundation (v7.3.0): Extracting archive
  - Installing symfony/error-handler (v7.3.0): Extracting archive
  - Installing symfony/http-kernel (v7.3.0): Extracting archive
  - Installing symfony/finder (v7.3.0): Extracting archive
  - Installing symfony/polyfill-intl-grapheme (v1.32.0): Extracting archive
  - Installing symfony/string (v7.3.0): Extracting archive
  - Installing symfony/console (v7.3.0): Extracting archive
  - Installing ramsey/collection (2.1.1): Extracting archive
  - Installing brick/math (0.13.1): Extracting archive
  - Installing ramsey/uuid (4.8.1): Extracting archive
  - Installing psr/simple-cache (3.0.0): Extracting archive
  - Installing nunomaduro/termwind (v2.3.1): Extracting archive
  - Installing symfony/translation-contracts (v3.6.0): Extracting archive
  - Installing symfony/translation (v7.3.0): Extracting archive
  - Installing psr/clock (1.0.0): Extracting archive
  - Installing symfony/clock (v7.3.0): Extracting archive
  - Installing carbonphp/carbon-doctrine-types (3.2.0): Extracting archive
  - Installing nesbot/carbon (3.9.1): Extracting archive
  - Installing monolog/monolog (3.9.0): Extracting archive
  - Installing psr/http-message (2.0): Extracting archive
  - Installing psr/http-factory (1.1.0): Extracting archive
  - Installing league/uri-interfaces (7.5.0): Extracting archive
  - Installing league/uri (7.5.1): Extracting archive
  - Installing league/mime-type-detection (1.16.0): Extracting archive
  - Installing league/flysystem-local (3.29.0): Extracting archive
  - Installing league/flysystem (3.29.1): Extracting archive
  - Installing nette/utils (v4.0.7): Extracting archive
  - Installing nette/schema (v1.3.2): Extracting archive
  - Installing dflydev/dot-access-data (v3.0.3): Extracting archive
  - Installing league/config (v1.2.0): Extracting archive
  - Installing league/commonmark (2.7.0): Extracting archive
  - Installing laravel/serializable-closure (v2.0.4): Extracting archive
  - Installing laravel/prompts (v0.3.5): Extracting archive
  - Installing guzzlehttp/uri-template (v1.0.4): Extracting archive
  - Installing psr/http-client (1.0.3): Extracting archive
  - Installing ralouphie/getallheaders (3.0.3): Extracting archive
  - Installing guzzlehttp/psr7 (2.7.1): Extracting archive
  - Installing guzzlehttp/promises (2.2.0): Extracting archive
  - Installing guzzlehttp/guzzle (7.9.3): Extracting archive
  - Installing fruitcake/php-cors (v1.3.0): Extracting archive
  - Installing webmozart/assert (1.11.0): Extracting archive
  - Installing dragonmantank/cron-expression (v3.4.0): Extracting archive
  - Installing doctrine/inflector (2.0.10): Extracting archive
  - Installing laravel/framework (v12.17.0): Extracting archive
  - Installing masterminds/html5 (2.9.0): Extracting archive
  - Installing sabberworm/php-css-parser (v8.8.0): Extracting archive
  - Installing dompdf/php-svg-lib (1.0.0): Extracting archive
  - Installing dompdf/php-font-lib (1.0.1): Extracting archive
  - Installing dompdf/dompdf (v3.1.0): Extracting archive
  - Installing barryvdh/laravel-dompdf (v3.1.1): Extracting archive
  - Installing barryvdh/laravel-translation-manager (v0.6.8): Extracting archive
  - Installing billowapp/payfast (0.6.6): Extracting archive
  - Installing blade-ui-kit/blade-icons (1.8.0): Extracting archive
  - Installing blade-ui-kit/blade-heroicons (2.6.0): Extracting archive
  - Installing php-http/promise (1.3.1): Extracting archive
  - Installing php-http/httplug (2.4.1): Extracting archive
  - Installing php-http/guzzle7-adapter (1.1.0): Extracting archive
  - Installing composer/ca-bundle (1.5.7): Extracting archive
  - Installing flutterwavedev/flutterwave-v3 (1.1.0): Extracting archive
  - Installing froiden/envato (5.1.1): Extracting archive
  - Installing froiden/laravel-installer (11.0.0): Extracting archive
  - Installing froiden/laravel-rest-api (12.0.0): Extracting archive
  - Installing intervention/image (2.7.2): Extracting archive
  - Installing livewire/livewire (v3.6.3): Extracting archive
  - Installing jantinnerezo/livewire-alert (v3.0.3): Extracting archive
  - Installing ladumor/laravel-pwa (v0.0.4): Extracting archive
  - Installing symfony/polyfill-intl-icu (v1.32.0): Extracting archive
  - Installing stripe/stripe-php (v16.6.0): Extracting archive
  - Installing moneyphp/money (v4.7.1): Extracting archive
  - Installing laravel/cashier (v15.6.4): Extracting archive
  - Installing psr/cache (3.0.0): Extracting archive
  - Installing mobiledetect/mobiledetectlib (4.8.09): Extracting archive
  - Installing paragonie/constant_time_encoding (v3.0.0): Extracting archive
  - Installing pragmarx/google2fa (v8.0.3): Extracting archive
  - Installing laravel/fortify (v1.25.4): Extracting archive
  - Installing laravel/jetstream (v5.3.6): Extracting archive
  - Installing laravel/sanctum (v4.1.1): Extracting archive
  - Installing nikic/php-parser (v5.5.0): Extracting archive
  - Installing psy/psysh (v0.12.8): Extracting archive
  - Installing laravel/tinker (v2.10.1): Extracting archive
  - Installing mtdowling/jmespath.php (2.8.0): Extracting archive
  - Installing aws/aws-sdk-php (3.344.2): Extracting archive
  - Installing league/flysystem-aws-s3-v3 (3.29.0): Extracting archive
  - Installing markbaker/matrix (3.0.1): Extracting archive
  - Installing markbaker/complex (3.0.2): Extracting archive
  - Installing maennchen/zipstream-php (3.1.2): Extracting archive
  - Installing ezyang/htmlpurifier (v4.18.0): Extracting archive
  - Installing composer/pcre (3.3.2): Extracting archive
  - Installing phpoffice/phpspreadsheet (1.29.10): Extracting archive
  - Installing composer/semver (3.4.3): Extracting archive
  - Installing maatwebsite/excel (3.1.64): Extracting archive
  - Installing macellan/laravel-zip (1.0.5): Extracting archive
  - Installing spomky-labs/pki-framework (1.2.3): Extracting archive
  - Installing web-token/jwt-library (4.0.4): Extracting archive
  - Installing spomky-labs/base64url (v2.0.4): Extracting archive
  - Installing minishlink/web-push (v9.0.2): Extracting archive
  - Installing nwidart/laravel-modules (v12.0.3): Extracting archive
  - Installing paypal/rest-api-sdk-php (dev-laravel10 d5c36a2): Extracting archive
  - Installing rmccue/requests (v2.0.15): Extracting archive
  - Installing razorpay/razorpay (2.9.1): Extracting archive
  - Installing spatie/laravel-permission (6.19.0): Extracting archive
  - Installing spatie/laravel-package-tools (1.92.4): Extracting archive
  - Installing spatie/laravel-translatable (6.11.4): Extracting archive
  - Installing unicodeveloper/laravel-paystack (dev-l12-compatibility ae92cdc): Extracting archive
   0/135 [>---------------------------]   0%  20/135 [====>-----------------------]  14%  30/135 [======>---------------------]  22%  48/135 [=========>------------------]  35%  57/135 [===========>----------------]  42%  75/135 [===============>------------]  55%  82/135 [=================>----------]  60%  97/135 [====================>-------]  71% 109/135 [======================>-----]  80% 124/135 [=========================>--]  91% 134/135 [===========================>]  99% 135/135 [============================] 100%Generating optimized autoload files
> Illuminate\Foundation\ComposerScripts::postAutoloadDump
> @php artisan package:discover --ansi

   INFO  Discovering packages.  

  barryvdh/laravel-dompdf ............................................... DONE
  barryvdh/laravel-translation-manager .................................. DONE
  billowapp/payfast ..................................................... DONE
  blade-ui-kit/blade-heroicons .......................................... DONE
  blade-ui-kit/blade-icons .............................................. DONE
  froiden/envato ........................................................ DONE
  intervention/image .................................................... DONE
  jantinnerezo/livewire-alert ........................................... DONE
  ladumor/laravel-pwa ................................................... DONE
  laravel/cashier ....................................................... DONE
  laravel/fortify ....................................................... DONE
  laravel/jetstream ..................................................... DONE
  laravel/sanctum ....................................................... DONE
  laravel/tinker ........................................................ DONE
  livewire/livewire ..................................................... DONE
  maatwebsite/excel ..................................................... DONE
  macellan/laravel-zip .................................................. DONE
  nesbot/carbon ......................................................... DONE
  nunomaduro/termwind ................................................... DONE
  nwidart/laravel-modules ............................................... DONE
  spatie/laravel-permission ............................................. DONE
  spatie/laravel-translatable ........................................... DONE
  unicodeveloper/laravel-paystack ....................................... DONE

70 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
 Dependencies installed successfully
=== Comprehensive Inertia Detection ===
 Inertia not detected in any location (composer.json, config/app.php, package.json, vendor)
=== Setting Secure Permissions ===
 Release permissions secured
=== Database Operations ===
Testing database connection...
 Database not accessible - skip migrations
 This may be normal for first deployment or if database is not configured
=== Application Optimization ===
Clearing old caches...

   INFO  Application cache cleared successfully.  


   INFO  Configuration cache cleared successfully.  


   INFO  Route cache cleared successfully.  


   INFO  Compiled views cleared successfully.  

Building new caches...

   INFO  Configuration cached successfully.  

 Configuration cached

   INFO  Routes cached successfully.  

 Routes cached


   INFO  Blade templates cached successfully.  

 Views cached
 Storage link will be created in Phase C
=== Creating Universal Content Symlinks ===
Processing user content directories...
 Symlink already exists: upload
 Symlink already exists: uploads
 Symlink already exists: uploaded
 Symlink already exists: user-upload
? Migrating existing content: user-uploads
 Created symlink: user-uploads -> shared/public/user-uploads
 Symlink already exists: media
 Symlink already exists: medias
 Symlink already exists: avatar
 Symlink already exists: avatars
 Symlink already exists: clientAvatar
 Symlink already exists: attachment
 Symlink already exists: attachments
 Symlink already exists: document
 Symlink already exists: documents
 Symlink already exists: file
 Symlink already exists: files
 Symlink already exists: images
Processing generated content directories...
 Symlink already exists: qrcode
 Symlink already exists: qrcodes
 Symlink already exists: barcode
 Symlink already exists: barcodes
 Symlink already exists: certificate
 Symlink already exists: certificates
 Symlink already exists: report
 Symlink already exists: reports
 Symlink already exists: temp
 Symlink already exists: temporary
Processing CodeCanyon-specific patterns...
? Preserving custom favicon.ico
 Created symlink: favicon.ico -> shared
 Modules symlink already exists
 Universal content symlinks completed
 Build pipeline handled separately - no build dependencies
 Phase B completed successfully
Time taken to run Phase B: Pre-Release Commands (After Upload, Before Release): 33.85 seconds


Linking release directory to current

Symlinking /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814 to /home/u227177893/domains/staging.societypal.com/deploy/current


Running SSH command Phase C-1: Post-Deployment Commands (After Release)

Executing Phase C-1: Post-Deployment Commands (After Release) [#!/bin/bash set -e # Phase C-1: Post-Deployment Commands (After Release) # Purpose: Activate release, setup public access, exit maintenance, verify # Note: DeployHQ has already created the current symlink # Version 2 - PRODUCTION READY (Enhanced with deployment report fixes) # Works for both first-time and subsequent deployments echo "=== Phase C: Post-Deployment Finalization (V2) ===" # C01: Setup Public Access for All Hosting Types (UNIVERSAL VERSION) echo "=== Configure Public Access ===" # Detect hosting structure DOMAIN_ROOT=$(dirname "/home/u227177893/domains/staging.societypal.com/deploy") cd "$DOMAIN_ROOT" echo "? Analyzing hosting environment..." echo " Domain root: $DOMAIN_ROOT" echo " Deploy path: /home/u227177893/domains/staging.societypal.com/deploy" # Detect hosting type and handle accordingly HOSTING_TYPE="unknown" if [ -d "public_html" ] && [ ! -L "public_html" ]; then HOSTING_TYPE="hostinger" echo "? Detected: Hostinger-style hosting (existing public_html directory)" elif [ -L "public_html" ]; then HOSTING_TYPE="existing_symlink" echo "? Detected: Existing public_html symlink" elif [ -d "www" ] && [ ! -L "www" ]; then HOSTING_TYPE="www_based" echo "? Detected: WWW-based hosting" else HOSTING_TYPE="cpanel_secure" echo "?️ Detected: cPanel-style hosting (will create secure symlink)" fi case $HOSTING_TYPE in "hostinger") echo "? Hostinger Setup: Handling existing public_html directory..." # Check if public_html has content if [ "$(ls -A public_html 2>/dev/null | grep -v '^\.' | wc -l)" -gt 0 ]; then BACKUP_NAME="public_html.backup.$(date +%Y%m%d_%H%M%S)" echo " ? Backing up existing content to: $BACKUP_NAME" mv public_html "$BACKUP_NAME" || { echo " ⚠️ Backup failed, removing existing content..." rm -rf public_html } else echo " ?️ Removing empty public_html directory..." rm -rf public_html fi # Create symlink to Laravel public directory if ln -sf deploy/current/public public_html; then echo " ✅ Created: public_html -> deploy/current/public" else echo " ❌ Failed to create symlink - check permissions" fi ;; "existing_symlink") echo "? Existing Symlink: Verifying and updating..." CURRENT_TARGET=$(readlink public_html 2>/dev/null || echo "broken") if [ "$CURRENT_TARGET" != "deploy/current/public" ]; then echo " ? Updating symlink target from: $CURRENT_TARGET" rm -f public_html if ln -sf deploy/current/public public_html; then echo " ✅ Updated: public_html -> deploy/current/public" else echo " ❌ Failed to update symlink" fi else echo " ✅ Symlink already correct: $CURRENT_TARGET" fi ;; "www_based") echo "? WWW-based Setup: Handling www directory..." if [ "$(ls -A www 2>/dev/null | grep -v '^\.' | wc -l)" -gt 0 ]; then mv www "www.backup.$(date +%Y%m%d_%H%M%S)" echo " ? Backed up existing www directory" else rm -rf www fi if ln -sf deploy/current/public www; then echo " ✅ Created: www -> deploy/current/public" else echo " ❌ Failed to create www symlink" fi ;; "cpanel_secure") echo "? cPanel Secure Setup: Creating secure public_html symlink..." # This is the most secure approach - create symlink to Laravel public if ln -sf deploy/current/public public_html; then echo " ✅ Created secure symlink: public_html -> deploy/current/public" echo " ?️ Laravel app files remain outside web-accessible directory" else echo " ⚠️ Primary symlink failed, trying alternative approach..." # Alternative: create from within deploy directory cd deploy/current && ln -sf public ../../public_html && cd ../.. if [ -L "public_html" ]; then echo " ✅ Alternative symlink creation successful" else echo " ❌ All symlink attempts failed - manual intervention required" echo " ? Suggestion: Check directory permissions and server configuration" fi fi ;; *) echo "❓ Unknown hosting type - using default secure approach" ln -sf deploy/current/public public_html 2>/dev/null && echo "✅ Default symlink created" || echo "⚠️ Default symlink failed" ;; esac # Verify the final setup echo "? Verifying public access setup..." if [ -L "public_html" ] && [ -e "public_html" ]; then FINAL_TARGET=$(readlink public_html) echo " ✅ public_html -> $FINAL_TARGET" # Test if Laravel public files are accessible if [ -f "public_html/index.php" ]; then echo " ✅ Laravel application accessible via public_html" else echo " ⚠️ Laravel index.php not found in public_html" fi else echo " ❌ public_html symlink verification failed" echo " ? Manual setup may be required for this hosting environment" fi # C02: Create index.php fallback in domain root (security) if [ "$DOMAIN_ROOT" != "/home/u227177893/domains/staging.societypal.com/deploy" ]; then cat > "$DOMAIN_ROOT/index.php" << 'EOF' <?php // Security redirect to Laravel public directory header("Location: /public_html/"); exit(); EOF echo "✅ Created security redirect in domain root" fi # C03: Exit Maintenance Mode (ENHANCED VERSION with better error handling) echo "=== Exit Maintenance Mode ===" rm -f /home/u227177893/domains/staging.societypal.com/deploy/.maintenance if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/current/artisan" ]; then cd /home/u227177893/domains/staging.societypal.com/deploy/current # Try to exit Laravel maintenance mode, but don't fail if it doesn't work if php artisan up 2>/dev/null; then echo "✅ Laravel maintenance mode deactivated" else echo "⚠️ Failed to exit Laravel maintenance mode via artisan (continuing)" echo "ℹ️ This may be due to missing dependencies - check Phase B completion" echo "ℹ️ You may need to check the application manually" fi else echo "ℹ️ No artisan file found, maintenance flag removed only" fi echo "✅ Application is now live" # C04: Laravel Final Setup (ENHANCED VERSION) echo "=== Laravel Final Setup ===" cd /home/u227177893/domains/staging.societypal.com/deploy/current if [ -f "artisan" ]; then # CRITICAL: Verify and fix storage symlinks echo "Verifying storage symlinks..." # Check if public/storage is directory instead of symlink (common issue) if [ -d "public/storage" ] && [ ! -L "public/storage" ]; then echo "⚠️ public/storage is directory, converting to symlink..." rm -rf public/storage ln -sfn ../storage/app/public public/storage echo "✅ Converted public/storage directory to symlink" fi # Create/verify storage link echo "Creating storage link..." if php artisan storage:link --force 2>/dev/null; then echo "✅ Storage link created via artisan" else echo "⚠️ Artisan storage:link failed - likely exec() function disabled on shared hosting" echo " Attempting manual symlink creation (fallback method)..." # Fallback 1: Standard manual creation rm -f public/storage 2>/dev/null if ln -sfn ../storage/app/public public/storage; then echo "✅ Manual storage link created (fallback 1)" else echo "⚠️ Fallback 1 failed - trying exec() bypass method (fallback 2)..." # Fallback 2: Enhanced exec() bypass method (suggested improvement) STORAGE_TARGET="../storage/app/public" STORAGE_LINK="public/storage" # Remove existing if present rm -f "$STORAGE_LINK" 2>/dev/null # Create symlink manually (this doesn't use exec()) if ln -sfn "$STORAGE_TARGET" "$STORAGE_LINK"; then echo "✅ Storage link created via exec() bypass method (fallback 2)" else echo "⚠️ Fallback 2 failed - trying absolute path (fallback 3)..." # Fallback 3: Absolute path ABSOLUTE_TARGET="/home/u227177893/domains/staging.societypal.com/deploy/shared/storage/app/public" if ln -sfn "$ABSOLUTE_TARGET" "$STORAGE_LINK"; then echo "✅ Absolute path storage link created (fallback 3)" else echo "❌ All storage link attempts failed - manual intervention required" echo " Please create symlink manually: ln -sfn /home/u227177893/domains/staging.societypal.com/deploy/shared/storage/app/public public/storage" fi fi fi fi # Verify final storage symlink if [ -L "public/storage" ] && [ -e "public/storage" ]; then STORAGE_TARGET=$(readlink public/storage) echo "✅ Storage symlink verified: public/storage -> $STORAGE_TARGET" else echo "❌ Storage symlink verification failed - manual fix required" fi # Retry any caches that failed in Phase B echo "Finalizing Laravel caches..." php artisan config:cache 2>/dev/null && echo "✅ Config cache finalized" || echo "ℹ️ Config cache skipped" php artisan route:cache 2>/dev/null && echo "✅ Route cache finalized" || echo "ℹ️ Route cache skipped" php artisan view:cache 2>/dev/null && echo "✅ View cache finalized" || echo "ℹ️ View cache skipped" # Restart queue workers if needed if [ -f ".env" ]; then QUEUE_CONNECTION=$(grep "^QUEUE_CONNECTION=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" 2>/dev/null) if [ "$QUEUE_CONNECTION" != "sync" ] && [ -n "$QUEUE_CONNECTION" ]; then if php artisan queue:restart 2>/dev/null; then echo "✅ Queue workers signaled to restart" else echo "⚠️ Queue restart failed (continuing)" fi else echo "ℹ️ Queue using sync driver (no workers)" fi fi else echo "ℹ️ No artisan file - not a Laravel application" fi # C05: Clear Runtime Caches echo "=== Clear Runtime Caches ===" # Clear PHP OPcache if php -r "echo function_exists('opcache_reset') ? 'yes' : 'no';" 2>/dev/null | grep -q "yes"; then php -r "opcache_reset();" 2>/dev/null || true echo "✅ OPcache cleared" else echo "ℹ️ OpCache not available" fi # Clear composer cache (use composer command from Phase A) ${COMPOSER_CMD:-composer} clear-cache 2>/dev/null || true # C06: Security Verification (ENHANCED VERSION) echo "=== Security Verification ===" cd /home/u227177893/domains/staging.societypal.com/deploy/current # Check for exposed sensitive files EXPOSED_FILES=0 for FILE in .env .env.example composer.json package.json .git; do if [ -e "public/$FILE" ]; then echo "❌ SECURITY WARNING: $FILE exposed in public!" EXPOSED_FILES=$((EXPOSED_FILES + 1)) fi done if [ $EXPOSED_FILES -eq 0 ]; then echo "✅ No sensitive files exposed" fi # Verify .htaccess files exist [ -f ".htaccess" ] && echo "✅ Root .htaccess present" || echo "⚠️ Root .htaccess missing" [ -f "public/.htaccess" ] && echo "✅ Public .htaccess present" || echo "⚠️ Public .htaccess missing" # Check critical permissions [ -r "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ] && [ ! -w "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ] || echo "⚠️ .env file should not be world-writable" # C07: Health Checks (ENHANCED VERSION with better error handling) echo "=== Application Health Checks ===" HEALTH_PASSED=true # Check Laravel installation if [ -f "artisan" ]; then if php artisan --version >/dev/null 2>&1; then echo "✅ Laravel framework operational" LARAVEL_VERSION=$(php artisan --version 2>/dev/null | head -1) echo " Version: $LARAVEL_VERSION" else echo "❌ Laravel framework error" HEALTH_PASSED=false # Try to identify the issue echo " Debug info:" php artisan --version 2>&1 | head -3 echo " This may be due to:" echo " - Missing dependencies (check Phase B completion)" echo " - exec() function disabled on shared hosting" echo " - PHP configuration issues" fi else echo "⚠️ No artisan file found in current release" fi # Check critical paths echo "=== Critical Path Verification ===" [ -L "storage" ] && echo "✅ Storage symlink valid" || echo "❌ Storage symlink broken" [ -f ".env" ] && echo "✅ Environment configured" || echo "❌ Environment missing" [ -d "vendor" ] && echo "✅ Dependencies installed" || echo "❌ Dependencies missing" # Test database connection (ENHANCED from deployment report) if [ -f ".env" ]; then echo "Testing database connection..." if php artisan tinker --execute="echo 'DB connection test: '; try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAILED: ' . \$e->getMessage(); }" 2>/dev/null | grep -q "OK"; then echo "✅ Database connection working" else echo "⚠️ Database connection failed" echo "ℹ️ This may be due to:" echo " - Database credentials incorrect" echo " - Database server not ready" echo " - exec() function disabled (preventing artisan tinker)" echo " - Network connectivity issues" fi fi # Test HTTP response (ENHANCED with better error handling) if [ -f ".env" ]; then APP_URL=$(grep "^APP_URL=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'") if [ -n "$APP_URL" ] && command -v curl &> /dev/null; then echo "Testing application response..." HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL" --max-time 5 2>/dev/null || echo "000") case $HTTP_CODE in 200|201|301|302) echo "✅ Application responding (HTTP $HTTP_CODE)" ;; 503) echo "⚠️ Application in maintenance mode" ;; 000) echo "⚠️ Application not reachable" ;; *) echo "⚠️ Application returned HTTP $HTTP_CODE" ;; esac else echo "ℹ️ Cannot test web accessibility (no APP_URL or curl)" fi fi # C08: Cleanup Old Releases (ENHANCED VERSION) echo "=== Cleanup Old Releases ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases KEEP_RELEASES=3 TOTAL=$(ls -1t 2>/dev/null | wc -l) if [ "$TOTAL" -gt "$KEEP_RELEASES" ]; then echo "Cleaning old releases (keeping $KEEP_RELEASES)..." RELEASES_TO_DELETE=$((TOTAL - KEEP_RELEASES)) echo "Will delete $RELEASES_TO_DELETE old releases" ls -1t | tail -n +$((KEEP_RELEASES + 1)) | while read OLD; do if [ -n "$OLD" ] && [ -d "$OLD" ]; then echo "Removing: $OLD" rm -rf "$OLD" 2>/dev/null || echo "⚠️ Failed to remove $OLD" fi done echo "✅ Old releases cleanup completed" else echo "ℹ️ Only $TOTAL releases found, no cleanup needed" fi # C09: Final Report (ENHANCED VERSION) echo "=== Deployment Summary ===" echo "? Release: /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814" echo "? Current: /home/u227177893/domains/staging.societypal.com/deploy/current" echo "? Commit: f6adbf02b5c35c2e49b10683bcdd8c493e6546dc" echo "? Deployed by: Zaj Apps" echo "? Environment: staging" echo "? Status: $( [ "$HEALTH_PASSED" = true ] && echo '✅ Healthy' || echo '⚠️ Check warnings above' )" # Log deployment echo "[$(date '+%Y-%m-%d %H:%M:%S')] staging deployment - Release: /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814 - Commit: f6adbf02b5c35c2e49b10683bcdd8c493e6546dc - Status: Success" >> /home/u227177893/domains/staging.societypal.com/deploy/shared/deployments.log echo "✅ Deployment completed successfully!" exit 0 ]

=== Phase C: Post-Deployment Finalization (V2) ===
=== Configure Public Access ===
? Analyzing hosting environment...
   Domain root: /home/u227177893/domains/staging.societypal.com
   Deploy path: /home/u227177893/domains/staging.societypal.com/deploy
? Detected: Existing public_html symlink
? Existing Symlink: Verifying and updating...
    Symlink already correct: deploy/current/public
? Verifying public access setup...
    public_html -> deploy/current/public
    Laravel application accessible via public_html
 Created security redirect in domain root
=== Exit Maintenance Mode ===

   INFO  Application is already up.  

 Laravel maintenance mode deactivated
 Application is now live
=== Laravel Final Setup ===
Verifying storage symlinks...
Creating storage link...

In Filesystem.php line 358:
                                                           
  Call to undefined function Illuminate\Filesystem\exec()  
                                                           

 Artisan storage:link failed - likely exec() function disabled on shared hosting
   Attempting manual symlink creation (fallback method)...
 Manual storage link created (fallback 1)
 Storage symlink verified: public/storage -> ../storage/app/public
Finalizing Laravel caches...

   INFO  Configuration cached successfully.  

 Config cache finalized

   INFO  Routes cached successfully.  

 Route cache finalized


   INFO  Blade templates cached successfully.  

 View cache finalized
 Queue using sync driver (no workers)
=== Clear Runtime Caches ===
 OPcache cleared
=== Security Verification ===
 No sensitive files exposed
 Root .htaccess present
 Public .htaccess present
 .env file should not be world-writable
=== Application Health Checks ===
 Laravel framework operational
  Version: Laravel Framework 12.17.0
=== Critical Path Verification ===
 Storage symlink valid
 Environment configured
 Dependencies installed
Testing database connection...
 Database connection failed
 This may be due to:
   - Database credentials incorrect
   - Database server not ready
   - exec() function disabled (preventing artisan tinker)
   - Network connectivity issues
Testing application response...
 Application returned HTTP 000000
=== Cleanup Old Releases ===
 Only 1 releases found, no cleanup needed
=== Deployment Summary ===
? Release: /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814
? Current: /home/u227177893/domains/staging.societypal.com/deploy/current
? Commit: f6adbf02b5c35c2e49b10683bcdd8c493e6546dc
? Deployed by: Zaj Apps
? Environment: staging
? Status:  Healthy
 Deployment completed successfully!
Time taken to run Phase C-1: Post-Deployment Commands (After Release): 5.88 seconds


Running SSH command Phase C-2: Comprehensive Deployment Verification & Reporting

Executing Phase C-2: Comprehensive Deployment Verification & Reporting [#!/bin/bash set -e # Phase C-2: Comprehensive Deployment Verification & Reporting # Purpose: Complete post-deployment analysis, verification, and detailed reporting # Run after Phase C - provides comprehensive deployment health check and documentation # Version 2.1 - PRODUCTION READY with Directory Validation # Working Directory: Expected to run from deploy/ directory (e.g., domains/xxx/deploy) echo "=== Phase C-2: Comprehensive Deployment Verification & Reporting ===" # C2-00: Initialize DeployHQ Path Variables (Same as Phases A, B, C) # These variables match DeployHQ's standard path structure # When running in DeployHQ, these will be replaced with actual paths # When testing manually, we detect the current directory structure if [[ "/home/u227177893/domains/staging.societypal.com/deploy" == *"%"* ]]; then # We're running manually for testing - detect paths from current directory CURRENT_DIR="$(pwd)" if [[ "$CURRENT_DIR" =~ .*/domains/[^/]+/deploy$ ]] && [[ -d "releases" ]] && [[ -d "shared" ]]; then export DEPLOY_PATH="$(pwd)" export SHARED_PATH="$(pwd)/shared" # Find current release if [ -L "current" ]; then CURRENT_RELEASE=$(readlink current | sed 's|^./releases/||') export CURRENT_PATH="$(pwd)/current" export RELEASE_PATH="$(pwd)/releases/$CURRENT_RELEASE" else export CURRENT_PATH="" export RELEASE_PATH="" fi echo "? TESTING MODE: Detected paths from current directory" else echo "❌ ERROR: Not in a valid deployment directory for testing" echo " Expected: domains/xxx/deploy (containing 'releases' and 'shared' directories)" echo " Current: $CURRENT_DIR" exit 1 fi else # We're running in DeployHQ - use the provided variables export DEPLOY_PATH="/home/u227177893/domains/staging.societypal.com/deploy" export RELEASE_PATH="/home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814" export SHARED_PATH="/home/u227177893/domains/staging.societypal.com/deploy/shared" export CURRENT_PATH="/home/u227177893/domains/staging.societypal.com/deploy/current" echo "? DEPLOYHQ MODE: Using DeployHQ path variables" fi echo "? Path Variables:" echo " Deploy Path: $DEPLOY_PATH" echo " Release Path: $RELEASE_PATH" echo " Shared Path: $SHARED_PATH" echo " Current Path: $CURRENT_PATH" # C2-01: Initialize Report Structure TIMESTAMP=$(date +"%Y%m%d_%H%M%S") DOMAIN_ROOT=$(dirname "$DEPLOY_PATH") DOMAIN_NAME=$(basename "$DOMAIN_ROOT") REPORT_DIR="$DOMAIN_ROOT/deployment-reports" REPORT_FILE="$REPORT_DIR/deployment-report-$TIMESTAMP.md" echo "? Starting comprehensive deployment verification..." echo " Domain: $DOMAIN_NAME" echo " Deploy Path: $DEPLOY_PATH" echo " Report: $REPORT_FILE" # Create report directory mkdir -p "$REPORT_DIR" # Start report file cat > "$REPORT_FILE" << EOF # ? Deployment Verification Report **Domain:** $DOMAIN_NAME **Timestamp:** $(date '+%Y-%m-%d %H:%M:%S') **Deploy Path:** \`$DEPLOY_PATH\` **Generated By:** Phase C-2 Verification Script --- ## ? TL;DR - QUICK SUMMARY EOF # C2-02: Environment Detection and Basic Info echo "=== Environment Detection ===" cd "$DEPLOY_PATH" # Detect current release if [ -L "current" ]; then CURRENT_RELEASE=$(readlink current | sed 's|^./releases/||') echo "✅ Current release detected: $CURRENT_RELEASE" else echo "❌ No current symlink found" CURRENT_RELEASE="NONE" fi # Count releases RELEASE_COUNT=$(ls -1 releases/ 2>/dev/null | wc -l) echo "? Total releases: $RELEASE_COUNT" # Check shared directory SHARED_SIZE=$(du -sh "$SHARED_PATH" 2>/dev/null | cut -f1 || echo "0") echo "? Shared directory size: $SHARED_SIZE" # Add initial report structure (TLDR will be updated later) cat >> "$REPORT_FILE" << EOF <!-- TLDR_PLACEHOLDER --> --- ## ? DETAILED ANALYSIS ### ?️ Infrastructure Status ### Directory Structure \`\`\` $DOMAIN_NAME/ ├── deploy/ │ ├── current -> releases/$CURRENT_RELEASE │ ├── releases/ ($RELEASE_COUNT releases) │ └── shared/ ($SHARED_SIZE) ├── public_html -> $([ -L "$DOMAIN_ROOT/public_html" ] && readlink "$DOMAIN_ROOT/public_html" || echo "NOT FOUND") └── deployment-reports/ ($(ls -1 "$REPORT_DIR" 2>/dev/null | wc -l) reports) \`\`\` EOF # C2-03: Critical Symlink Verification echo "=== Critical Symlink Verification ===" SYMLINK_STATUS="HEALTHY" # Function to check symlink check_symlink() { local LINK_PATH="$1" local DESCRIPTION="$2" local EXPECTED_TARGET="$3" if [ -L "$LINK_PATH" ]; then local TARGET=$(readlink "$LINK_PATH") local RESOLVED=$(readlink -f "$LINK_PATH" 2>/dev/null || echo "BROKEN") if [ -e "$LINK_PATH" ]; then echo "✅ $DESCRIPTION: $TARGET" echo " → $DESCRIPTION | ✅ VALID | $TARGET | $RESOLVED" >> /tmp/symlink_report else echo "❌ $DESCRIPTION: BROKEN ($TARGET)" echo " → $DESCRIPTION | ❌ BROKEN | $TARGET | BROKEN" >> /tmp/symlink_report SYMLINK_STATUS="ISSUES_FOUND" fi else echo "❌ $DESCRIPTION: NOT A SYMLINK" echo " → $DESCRIPTION | ❌ NOT_SYMLINK | N/A | N/A" >> /tmp/symlink_report SYMLINK_STATUS="ISSUES_FOUND" fi } # Initialize symlink report echo "" > /tmp/symlink_report # Check public_html (web root) cd "$DOMAIN_ROOT" check_symlink "public_html" "Web Root (public_html)" "deploy/current/public" # Check current release symlink cd "$DEPLOY_PATH" check_symlink "current" "Current Release" "releases/$CURRENT_RELEASE" # Check Laravel symlinks (if current exists) if [ -n "$CURRENT_RELEASE" ] && [ "$CURRENT_RELEASE" != "NONE" ] && [ -d "$CURRENT_PATH" ]; then cd "$CURRENT_PATH" check_symlink ".env" "Environment File" "$SHARED_PATH/.env" check_symlink "storage" "Storage Directory" "$SHARED_PATH/storage" check_symlink "bootstrap/cache" "Bootstrap Cache" "$SHARED_PATH/bootstrap/cache" check_symlink "public/storage" "Public Storage" "../storage/app/public" fi # Add symlink report to main report cat >> "$REPORT_FILE" << EOF ### ? Symlink Health Check EOF # Convert technical symlink report to user-friendly format while IFS='|' read -r component status target resolved; do if [[ "$component" =~ "Web Root" ]]; then if [[ "$status" =~ "VALID" ]]; then echo "✅ **Website Access**: Working correctly" >> "$REPORT_FILE" else echo "❌ **Website Access**: BROKEN - visitors cannot reach your site!" >> "$REPORT_FILE" fi elif [[ "$component" =~ "Current Release" ]]; then if [[ "$status" =~ "VALID" ]]; then echo "✅ **Active Version**: Properly linked" >> "$REPORT_FILE" else echo "❌ **Active Version**: BROKEN - no active deployment!" >> "$REPORT_FILE" fi elif [[ "$component" =~ "Environment File" ]]; then if [[ "$status" =~ "VALID" ]]; then echo "✅ **Configuration**: Database & settings connected" >> "$REPORT_FILE" else echo "❌ **Configuration**: MISSING - app cannot start!" >> "$REPORT_FILE" fi elif [[ "$component" =~ "Storage Directory" ]]; then if [[ "$status" =~ "VALID" ]]; then echo "✅ **File Storage**: User uploads preserved" >> "$REPORT_FILE" else echo "❌ **File Storage**: BROKEN - user files may be lost!" >> "$REPORT_FILE" fi elif [[ "$component" =~ "Public Storage" ]]; then if [[ "$status" =~ "VALID" ]]; then echo "✅ **Public Files**: Images & uploads accessible" >> "$REPORT_FILE" else echo "❌ **Public Files**: BROKEN - images won't display!" >> "$REPORT_FILE" fi fi done < /tmp/symlink_report cat >> "$REPORT_FILE" << EOF **? Overall Status:** $([ "$SYMLINK_STATUS" = "HEALTHY" ] && echo "✅ ALL SYSTEMS WORKING" || echo "⚠️ CRITICAL ISSUES FOUND") EOF # C2-04: Shared Directory Analysis echo "=== Shared Directory Analysis ===" cd "$DEPLOY_PATH" # Function to analyze shared directory analyze_shared_dir() { local DIR_PATH="$1" local DESCRIPTION="$2" if [ -d "$SHARED_PATH/$DIR_PATH" ]; then local SIZE=$(du -sh "$SHARED_PATH/$DIR_PATH" 2>/dev/null | cut -f1) local COUNT=$(find "$SHARED_PATH/$DIR_PATH" -type f 2>/dev/null | wc -l) echo "✅ $DESCRIPTION: $SIZE ($COUNT files)" echo "| $DESCRIPTION | ✅ EXISTS | $SIZE | $COUNT files |" >> /tmp/shared_report else echo "⚠️ $DESCRIPTION: NOT FOUND" echo "| $DESCRIPTION | ⚠️ MISSING | N/A | N/A |" >> /tmp/shared_report fi } # Initialize shared report echo "" > /tmp/shared_report # Analyze core shared directories analyze_shared_dir "storage" "Laravel Storage" analyze_shared_dir "bootstrap/cache" "Bootstrap Cache" # Check .env file separately since it's a file, not a directory if [ -f "$SHARED_PATH/.env" ]; then ENV_SIZE=$(du -sh "$SHARED_PATH/.env" 2>/dev/null | cut -f1) echo "✅ Environment File: $ENV_SIZE (1 file)" echo "| Environment File | ✅ EXISTS | $ENV_SIZE | 1 file |" >> /tmp/shared_report else echo "⚠️ Environment File: NOT FOUND" echo "| Environment File | ⚠️ MISSING | N/A | N/A |" >> /tmp/shared_report fi # Analyze user content directories (universal patterns) USER_CONTENT_DIRS=("public/uploads" "public/user-uploads" "public/media" "public/avatars" "public/attachments" "public/documents" "public/files" "public/images") USER_CONTENT_FOUND=0 for DIR in "${USER_CONTENT_DIRS[@]}"; do if [ -d "$SHARED_PATH/$DIR" ]; then analyze_shared_dir "$DIR" "User Content: $(basename "$DIR")" USER_CONTENT_FOUND=$((USER_CONTENT_FOUND + 1)) fi done # Analyze generated content directories GENERATED_DIRS=("public/qrcodes" "public/barcodes" "public/certificates" "public/reports") GENERATED_FOUND=0 for DIR in "${GENERATED_DIRS[@]}"; do if [ -d "$SHARED_PATH/$DIR" ]; then analyze_shared_dir "$DIR" "Generated: $(basename "$DIR")" GENERATED_FOUND=$((GENERATED_FOUND + 1)) fi done # CodeCanyon specific analyze_shared_dir "Modules" "CodeCanyon Modules" # Add shared analysis to report cat >> "$REPORT_FILE" << EOF ### ? Data Protection Status EOF # Convert technical shared report to user-friendly format PROTECTED_DATA_COUNT=0 while IFS='|' read -r description status size content; do if [[ "$description" =~ "Laravel Storage" ]]; then if [[ "$status" =~ "EXISTS" ]]; then echo "✅ **App Data**: $size safely stored ($content)" >> "$REPORT_FILE" PROTECTED_DATA_COUNT=$((PROTECTED_DATA_COUNT + 1)) else echo "❌ **App Data**: MISSING - application files not preserved!" >> "$REPORT_FILE" fi elif [[ "$description" =~ "User Content:" ]]; then CONTENT_TYPE=$(echo "$description" | sed 's/User Content: //') if [[ "$status" =~ "EXISTS" ]]; then echo "✅ **User $CONTENT_TYPE**: $size preserved ($content)" >> "$REPORT_FILE" PROTECTED_DATA_COUNT=$((PROTECTED_DATA_COUNT + 1)) fi elif [[ "$description" =~ "Generated:" ]]; then CONTENT_TYPE=$(echo "$description" | sed 's/Generated: //') if [[ "$status" =~ "EXISTS" ]]; then echo "✅ **Generated $CONTENT_TYPE**: $size preserved ($content)" >> "$REPORT_FILE" PROTECTED_DATA_COUNT=$((PROTECTED_DATA_COUNT + 1)) fi elif [[ "$description" =~ "CodeCanyon" ]]; then if [[ "$status" =~ "EXISTS" ]]; then echo "✅ **App Modules**: $size preserved ($content)" >> "$REPORT_FILE" PROTECTED_DATA_COUNT=$((PROTECTED_DATA_COUNT + 1)) fi fi done < /tmp/shared_report cat >> "$REPORT_FILE" << EOF **? Data Protection Summary:** - **Protected Data Types**: $PROTECTED_DATA_COUNT found - **User Uploads**: $([ $USER_CONTENT_FOUND -gt 0 ] && echo "$USER_CONTENT_FOUND types preserved" || echo "None found") - **Generated Files**: $([ $GENERATED_FOUND -gt 0 ] && echo "$GENERATED_FOUND types preserved" || echo "None found") $([ $PROTECTED_DATA_COUNT -gt 0 ] && echo "✅ **Zero Data Loss**: All user content survives deployments" || echo "ℹ️ **Clean Install**: No user data found (normal for new apps)") EOF # C2-05: Laravel Application Health Check echo "=== Laravel Application Health Check ===" LARAVEL_STATUS="UNKNOWN" PHP_VERSION="UNKNOWN" COMPOSER_VERSION="UNKNOWN" if [ -n "$CURRENT_RELEASE" ] && [ "$CURRENT_RELEASE" != "NONE" ] && [ -d "$CURRENT_PATH" ]; then cd "$CURRENT_PATH" # Check PHP version PHP_VERSION=$(php -v 2>/dev/null | head -1 | cut -d' ' -f2 || echo "ERROR") echo "? PHP Version: $PHP_VERSION" # Check Composer version if command -v composer2 &> /dev/null; then COMPOSER_VERSION=$(composer2 --version 2>/dev/null | cut -d' ' -f3 || echo "ERROR") COMPOSER_CMD="composer2" elif composer --version 2>/dev/null | grep -q "version 2\."; then COMPOSER_VERSION=$(composer --version 2>/dev/null | cut -d' ' -f3 || echo "ERROR") COMPOSER_CMD="composer" else COMPOSER_VERSION=$(composer --version 2>/dev/null | cut -d' ' -f3 || echo "ERROR") COMPOSER_CMD="composer (1.x)" fi echo "? Composer: $COMPOSER_VERSION ($COMPOSER_CMD)" # Check exec() function availability (critical for shared hosting) EXEC_STATUS=$(php -r "echo function_exists('exec') ? 'AVAILABLE' : 'DISABLED';" 2>/dev/null || echo "ERROR") echo "⚙️ exec() Function: $EXEC_STATUS" if [ "$EXEC_STATUS" = "DISABLED" ]; then echo " ⚠️ exec() disabled - some artisan commands may fail (common on shared hosting)" echo " ℹ️ Our scripts use manual fallbacks to bypass exec() limitations" fi # Check Laravel if [ -f "artisan" ]; then LARAVEL_VERSION=$(php artisan --version 2>/dev/null | head -1 || echo "ERROR") echo "? Laravel: $LARAVEL_VERSION" LARAVEL_STATUS="DETECTED" # Test database connection DB_STATUS="UNKNOWN" if php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAILED'; }" 2>/dev/null | grep -q "OK"; then DB_STATUS="✅ CONNECTED" echo "?️ Database: Connected" else DB_STATUS="❌ FAILED" echo "?️ Database: Connection Failed" fi # Test cache driver CACHE_STATUS="UNKNOWN" CACHE_DRIVER=$(grep "^CACHE_DRIVER=" "$SHARED_PATH/.env" | cut -d'=' -f2 || echo "file") echo "?️ Cache Driver: $CACHE_DRIVER" if php artisan tinker --execute="try { Cache::put('test_key', 'test_value', 60); Cache::get('test_key'); echo 'OK'; } catch(Exception \$e) { echo 'FAILED: ' . \$e->getMessage(); }" 2>/dev/null | grep -q "OK"; then CACHE_STATUS="✅ WORKING" echo "✅ Cache: Working ($CACHE_DRIVER)" else CACHE_STATUS="❌ FAILED" CACHE_ERROR=$(php artisan tinker --execute="try { Cache::put('test_key', 'test_value', 60); echo 'OK'; } catch(Exception \$e) { echo \$e->getMessage(); }" 2>/dev/null | grep -v "OK" | head -1) echo "❌ Cache: Failed ($CACHE_DRIVER) - $CACHE_ERROR" fi # Test session driver SESSION_STATUS="UNKNOWN" SESSION_DRIVER=$(grep "^SESSION_DRIVER=" "$SHARED_PATH/.env" | cut -d'=' -f2 || echo "file") echo "? Session Driver: $SESSION_DRIVER" if php artisan tinker --execute="try { session(['test_session' => 'test_value']); session('test_session'); echo 'OK'; } catch(Exception \$e) { echo 'FAILED'; }" 2>/dev/null | grep -q "OK"; then SESSION_STATUS="✅ WORKING" echo "✅ Sessions: Working ($SESSION_DRIVER)" else SESSION_STATUS="❌ FAILED" SESSION_ERROR=$(php artisan tinker --execute="try { session(['test_session' => 'test_value']); echo 'OK'; } catch(Exception \$e) { echo \$e->getMessage(); }" 2>/dev/null | grep -v "OK" | head -1) echo "❌ Sessions: Failed ($SESSION_DRIVER) - $SESSION_ERROR" fi # Check critical Laravel directories VENDOR_STATUS=$([ -d "vendor" ] && echo "✅ EXISTS" || echo "❌ MISSING") STORAGE_STATUS=$([ -L "storage" ] && echo "✅ SYMLINKED" || echo "❌ NOT_SYMLINKED") ENV_STATUS=$([ -L ".env" ] && echo "✅ SYMLINKED" || echo "❌ NOT_SYMLINKED") echo "? Vendor: $VENDOR_STATUS" echo "? Storage: $STORAGE_STATUS" echo "⚙️ Environment: $ENV_STATUS" else echo "⚠️ No artisan file found - not a Laravel application" LARAVEL_STATUS="NOT_LARAVEL" fi else echo "⚠️ No current release to analyze" fi # Add Laravel analysis to report cat >> "$REPORT_FILE" << EOF ### ? Laravel Application Health EOF # Add user-friendly Laravel status if [ "$LARAVEL_STATUS" = "DETECTED" ]; then cat >> "$REPORT_FILE" << EOF ✅ **Laravel Framework**: $LARAVEL_VERSION running on PHP $PHP_VERSION ✅ **Dependencies**: Composer $COMPOSER_VERSION with vendor directory intact ✅ **Environment**: Configuration properly symlinked and accessible $([ "$EXEC_STATUS" = "DISABLED" ] && echo "⚠️ **exec() Function**: Disabled (shared hosting) - using manual fallbacks" || echo "✅ **exec() Function**: Available for full artisan support") **Core System Tests:** - **Database**: $([ "$DB_STATUS" = "✅ CONNECTED" ] && echo "✅ Connected and responding" || echo "❌ Connection failed") - **Cache System**: $([ "$CACHE_STATUS" = "✅ WORKING" ] && echo "✅ $CACHE_DRIVER driver working" || echo "❌ $CACHE_DRIVER driver failed - $CACHE_ERROR") - **Session System**: $([ "$SESSION_STATUS" = "✅ WORKING" ] && echo "✅ $SESSION_DRIVER driver working" || echo "❌ $SESSION_DRIVER driver failed - $SESSION_ERROR") - **File Storage**: $([ "$STORAGE_STATUS" = "✅ SYMLINKED" ] && echo "✅ Properly symlinked for zero-downtime" || echo "❌ Not symlinked - data loss risk") **Infrastructure:** - **Dependencies**: $([ "$VENDOR_STATUS" = "✅ EXISTS" ] && echo "$(find "$CURRENT_PATH/vendor" -name "*.php" 2>/dev/null | wc -l) PHP files loaded" || echo "❌ Missing vendor directory") - **Configuration**: $([ "$ENV_STATUS" = "✅ SYMLINKED" ] && echo "✅ Environment shared across deployments" || echo "❌ Not properly configured") EOF else cat >> "$REPORT_FILE" << EOF ⚠️ **Laravel Framework**: Not detected or not working properly - **Status**: $LARAVEL_STATUS - **PHP**: $PHP_VERSION - **Composer**: $COMPOSER_VERSION EOF fi cat >> "$REPORT_FILE" << EOF EOF # C2-06: Security Verification echo "=== Security Verification ===" SECURITY_ISSUES=0 cd "$DOMAIN_ROOT" # Check public_html security if [ -L "public_html" ]; then PUBLIC_TARGET=$(readlink "public_html") if [[ "$PUBLIC_TARGET" == *"/public" ]]; then echo "✅ Web root correctly points to Laravel public directory" else echo "⚠️ Web root does not point to Laravel public directory" SECURITY_ISSUES=$((SECURITY_ISSUES + 1)) fi else echo "❌ Web root is not a symlink - potential security risk" SECURITY_ISSUES=$((SECURITY_ISSUES + 1)) fi # Check for exposed sensitive files in public_html if [ -d "public_html" ]; then cd "public_html" EXPOSED_FILES=() for FILE in .env .env.example composer.json composer.lock package.json .git; do if [ -e "$FILE" ]; then EXPOSED_FILES+=("$FILE") SECURITY_ISSUES=$((SECURITY_ISSUES + 1)) fi done if [ ${#EXPOSED_FILES[@]} -eq 0 ]; then echo "✅ No sensitive files exposed in web root" else echo "❌ Exposed sensitive files: ${EXPOSED_FILES[*]}" fi fi # Check .env permissions cd "$DEPLOY_PATH" if [ -f "$SHARED_PATH/.env" ]; then ENV_PERMS=$(stat -c "%a" "$SHARED_PATH/.env" 2>/dev/null || echo "000") if [ "$ENV_PERMS" = "600" ] || [ "$ENV_PERMS" = "644" ]; then echo "✅ .env file has secure permissions ($ENV_PERMS)" else echo "⚠️ .env file permissions may be too open ($ENV_PERMS)" SECURITY_ISSUES=$((SECURITY_ISSUES + 1)) fi else echo "❌ .env file not found in shared directory" SECURITY_ISSUES=$((SECURITY_ISSUES + 1)) fi # Add security analysis to report cat >> "$REPORT_FILE" << EOF ### Security Analysis | Check | Status | Details | |-------|--------|---------| | Web Root Symlink | $([ -L "$DOMAIN_ROOT/public_html" ] && echo "✅ SECURE" || echo "❌ INSECURE") | Points to: $([ -L "$DOMAIN_ROOT/public_html" ] && readlink "$DOMAIN_ROOT/public_html" || echo "NOT_SYMLINK") | | Exposed Sensitive Files | $([ ${#EXPOSED_FILES[@]} -eq 0 ] && echo "✅ NONE" || echo "❌ FOUND") | $([ ${#EXPOSED_FILES[@]} -gt 0 ] && echo "${EXPOSED_FILES[*]}" || echo "Clean") | | .env Permissions | $([ -f "$SHARED_PATH/.env" ] && echo "✅ $(stat -c "%a" "$SHARED_PATH/.env")" || echo "❌ MISSING") | $([ -f "$SHARED_PATH/.env" ] && echo "File secured" || echo "Environment file not found") | **Security Issues Found:** $SECURITY_ISSUES EOF # C2-07: Performance & Optimization Check echo "=== Performance & Optimization Check ===" OPTIMIZATION_STATUS="UNKNOWN" if [ -n "$CURRENT_RELEASE" ] && [ "$CURRENT_RELEASE" != "NONE" ] && [ -d "$CURRENT_PATH" ]; then cd "$CURRENT_PATH" # Check Laravel caches CONFIG_CACHE=$([ -f "bootstrap/cache/config.php" ] && echo "✅ CACHED" || echo "⚠️ NOT_CACHED") ROUTE_CACHE=$([ -f "bootstrap/cache/routes-v7.php" ] && echo "✅ CACHED" || echo "⚠️ NOT_CACHED") VIEW_CACHE=$(find "storage/framework/views" -name "*.php" 2>/dev/null | wc -l) VIEW_STATUS=$([ $VIEW_CACHE -gt 0 ] && echo "✅ CACHED ($VIEW_CACHE files)" || echo "⚠️ NO_CACHE") echo "⚙️ Config Cache: $CONFIG_CACHE" echo "?️ Route Cache: $ROUTE_CACHE" echo "?️ View Cache: $VIEW_STATUS" # Check Composer optimization if [ -f "vendor/composer/autoload_classmap.php" ]; then CLASSMAP_COUNT=$(wc -l < "vendor/composer/autoload_classmap.php") COMPOSER_OPT=$([ $CLASSMAP_COUNT -gt 100 ] && echo "✅ OPTIMIZED ($CLASSMAP_COUNT classes)" || echo "⚠️ NOT_OPTIMIZED") else COMPOSER_OPT="❌ NO_CLASSMAP" fi echo "? Composer Autoload: $COMPOSER_OPT" # Check OPcache OPCACHE_STATUS=$(php -r "echo function_exists('opcache_get_status') ? 'AVAILABLE' : 'NOT_AVAILABLE';" 2>/dev/null || echo "ERROR") echo "? OPcache: $OPCACHE_STATUS" fi # Add performance analysis to report cat >> "$REPORT_FILE" << EOF ### Performance & Optimization | Component | Status | Details | |-----------|--------|---------| | Config Cache | $([ -n "$CONFIG_CACHE" ] && echo "$CONFIG_CACHE" || echo "⚠️ NOT_CHECKED") | $([ -f "$CURRENT_PATH/bootstrap/cache/config.php" ] && echo "Cached configuration loaded" || echo "No config cache") | | Route Cache | $([ -n "$ROUTE_CACHE" ] && echo "$ROUTE_CACHE" || echo "⚠️ NOT_CHECKED") | $([ -f "$CURRENT_PATH/bootstrap/cache/routes-v7.php" ] && echo "Cached routes loaded" || echo "No route cache") | | View Cache | $([ -n "$VIEW_STATUS" ] && echo "$VIEW_STATUS" || echo "⚠️ NOT_CHECKED") | Compiled view templates | | Composer Autoload | $([ -n "$COMPOSER_OPT" ] && echo "$COMPOSER_OPT" || echo "⚠️ NOT_CHECKED") | Class mapping optimization | | OPcache | $([ "$OPCACHE_STATUS" = "AVAILABLE" ] && echo "✅ $OPCACHE_STATUS" || echo "⚠️ $OPCACHE_STATUS") | PHP bytecode caching | EOF # C2-08: Release Management Analysis echo "=== Release Management Analysis ===" cd "$DEPLOY_PATH" # Analyze releases if [ -d "releases" ]; then RELEASES=($(ls -1t releases/ 2>/dev/null)) TOTAL_RELEASES=${#RELEASES[@]} echo "? Total releases: $TOTAL_RELEASES" if [ $TOTAL_RELEASES -gt 0 ]; then LATEST_RELEASE=${RELEASES[0]} echo "? Latest release: $LATEST_RELEASE" # Calculate release sizes for i in "${!RELEASES[@]}"; do if [ $i -lt 5 ]; then # Only show last 5 releases RELEASE=${RELEASES[$i]} SIZE=$(du -sh "releases/$RELEASE" 2>/dev/null | cut -f1) AGE=$(stat -c %Y "releases/$RELEASE" 2>/dev/null) CURRENT_TIME=$(date +%s) AGE_HOURS=$(( (CURRENT_TIME - AGE) / 3600 )) if [ "$RELEASE" = "$CURRENT_RELEASE" ]; then echo "? $RELEASE: $SIZE (${AGE_HOURS}h ago) [CURRENT]" else echo " $RELEASE: $SIZE (${AGE_HOURS}h ago)" fi fi done # Check for old releases OLD_RELEASES=$(( TOTAL_RELEASES - 3 )) if [ $OLD_RELEASES -gt 0 ]; then echo "?️ Old releases to cleanup: $OLD_RELEASES" fi fi fi # Add release analysis to report cat >> "$REPORT_FILE" << EOF ### Release Management | Metric | Value | Details | |--------|-------|---------| | Total Releases | $TOTAL_RELEASES | Stored in releases/ directory | | Current Release | $CURRENT_RELEASE | $([ -n "$CURRENT_RELEASE" ] && echo "Active deployment" || echo "No active deployment") | | Latest Release | $([ $TOTAL_RELEASES -gt 0 ] && echo "$LATEST_RELEASE" || echo "None") | Most recent in releases/ | | Cleanup Needed | $([ $OLD_RELEASES -gt 0 ] && echo "$OLD_RELEASES releases" || echo "None") | Releases beyond keep limit (3) | #### Recent Releases \`\`\` EOF if [ $TOTAL_RELEASES -gt 0 ]; then for i in "${!RELEASES[@]}"; do if [ $i -lt 5 ]; then RELEASE=${RELEASES[$i]} SIZE=$(du -sh "releases/$RELEASE" 2>/dev/null | cut -f1) AGE=$(stat -c %Y "releases/$RELEASE" 2>/dev/null) CURRENT_TIME=$(date +%s) AGE_HOURS=$(( (CURRENT_TIME - AGE) / 3600 )) if [ "$RELEASE" = "$CURRENT_RELEASE" ]; then echo "$RELEASE: $SIZE (${AGE_HOURS}h ago) [CURRENT]" >> "$REPORT_FILE" else echo "$RELEASE: $SIZE (${AGE_HOURS}h ago)" >> "$REPORT_FILE" fi fi done else echo "No releases found" >> "$REPORT_FILE" fi cat >> "$REPORT_FILE" << EOF \`\`\` EOF # C2-09: HTTP Health Check echo "=== HTTP Health Check ===" HTTP_STATUS="UNKNOWN" RESPONSE_TIME="UNKNOWN" if [ -n "$CURRENT_RELEASE" ] && [ "$CURRENT_RELEASE" != "NONE" ] && [ -f "$SHARED_PATH/.env" ]; then APP_URL=$(grep "^APP_URL=" "$SHARED_PATH/.env" | cut -d'=' -f2 | tr -d '"' | tr -d "'") if [ -n "$APP_URL" ] && command -v curl &> /dev/null; then echo "? Testing: $APP_URL" # Test main page START_TIME=$(date +%s.%N) HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL" --max-time 10 2>/dev/null || echo "000") END_TIME=$(date +%s.%N) RESPONSE_TIME=$(echo "$END_TIME - $START_TIME" | bc 2>/dev/null || echo "unknown") # Test install page first (for fresh deployments) INSTALL_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/install" --max-time 10 2>/dev/null || echo "000") case $HTTP_CODE in 200|201|301|302) HTTP_STATUS="✅ HEALTHY ($HTTP_CODE)" echo "✅ Application responding: HTTP $HTTP_CODE (${RESPONSE_TIME}s)" ;; 503) HTTP_STATUS="⚠️ MAINTENANCE ($HTTP_CODE)" echo "⚠️ Application in maintenance mode: HTTP $HTTP_CODE" ;; 500) if [ "$INSTALL_CODE" = "200" ]; then HTTP_STATUS="✅ PENDING_INSTALL ($HTTP_CODE)" echo "✅ Fresh deployment detected: Main site HTTP $HTTP_CODE, Install page accessible" echo "ℹ️ This is EXPECTED - complete installation at: $APP_URL/install" else HTTP_STATUS="❌ ERROR ($HTTP_CODE)" echo "❌ Application error: HTTP $HTTP_CODE (install page also failed)" fi ;; 000) HTTP_STATUS="❌ UNREACHABLE" echo "❌ Application unreachable (timeout/connection error)" ;; *) HTTP_STATUS="⚠️ ERROR ($HTTP_CODE)" echo "⚠️ Application returned: HTTP $HTTP_CODE" if [ "$INSTALL_CODE" = "200" ]; then echo "ℹ️ Install page accessible: HTTP $INSTALL_CODE" fi ;; esac else echo "⚠️ Cannot test HTTP (no APP_URL or curl unavailable)" fi else echo "⚠️ Cannot determine APP_URL" fi # Add HTTP analysis to report cat >> "$REPORT_FILE" << EOF ### ? Website Status EOF # Add user-friendly HTTP status if [[ "$HTTP_STATUS" =~ "PENDING_INSTALL" ]]; then cat >> "$REPORT_FILE" << EOF ✅ **Fresh Deployment Ready**: Your app is deployed correctly and ready for setup - **Main Site**: Shows setup screen (HTTP $HTTP_CODE) - This is EXPECTED - **Install Page**: \`$APP_URL/install\` is accessible (HTTP $INSTALL_CODE) - **Next Step**: Complete installation wizard at \`$APP_URL/install\` ? **Status**: Ready for first-time setup EOF elif [[ "$HTTP_STATUS" =~ "HEALTHY" ]]; then cat >> "$REPORT_FILE" << EOF ✅ **Website Online**: Your application is running properly - **Main Site**: \`$APP_URL\` responding (HTTP $HTTP_CODE) - **Response Time**: ${RESPONSE_TIME}s - **Status**: Fully operational ? **Status**: Production ready EOF elif [[ "$HTTP_STATUS" =~ "MAINTENANCE" ]]; then cat >> "$REPORT_FILE" << EOF ⚠️ **Maintenance Mode**: Site temporarily offline for updates - **Main Site**: \`$APP_URL\` in maintenance (HTTP $HTTP_CODE) - **Status**: Planned downtime ? **Status**: Maintenance in progress EOF else cat >> "$REPORT_FILE" << EOF ❌ **Website Issues**: Problems detected with site access - **Main Site**: \`$APP_URL\` error (HTTP $HTTP_CODE) - **Install Page**: $([ "$INSTALL_CODE" = "200" ] && echo "Accessible (HTTP $INSTALL_CODE)" || echo "Also failed (HTTP $INSTALL_CODE)") ? **Status**: Needs troubleshooting EOF fi cat >> "$REPORT_FILE" << EOF EOF # C2-10: Final Summary and Recommendations echo "=== Generating Final Summary ===" # Calculate overall health score TOTAL_CHECKS=12 PASSED_CHECKS=0 [ "$SYMLINK_STATUS" = "HEALTHY" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$LARAVEL_STATUS" = "DETECTED" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$DB_STATUS" = "✅ CONNECTED" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$CACHE_STATUS" = "✅ WORKING" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$SESSION_STATUS" = "✅ WORKING" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$VENDOR_STATUS" = "✅ EXISTS" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$STORAGE_STATUS" = "✅ SYMLINKED" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$ENV_STATUS" = "✅ SYMLINKED" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ $SECURITY_ISSUES -eq 0 ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$CONFIG_CACHE" = "✅ CACHED" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) # HTTP check passes if: working (200/301/302), pending install (500 + install accessible), or maintenance (503) ([ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "301" ] || [ "$HTTP_CODE" = "302" ] || [ "$HTTP_CODE" = "503" ] || [[ "$HTTP_STATUS" =~ "PENDING_INSTALL" ]]) && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ $TOTAL_RELEASES -gt 0 ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) HEALTH_PERCENTAGE=$(( (PASSED_CHECKS * 100) / TOTAL_CHECKS )) # Determine overall status if [ $HEALTH_PERCENTAGE -ge 90 ]; then OVERALL_STATUS="? EXCELLENT" elif [ $HEALTH_PERCENTAGE -ge 75 ]; then OVERALL_STATUS="? GOOD" elif [ $HEALTH_PERCENTAGE -ge 50 ]; then OVERALL_STATUS="? NEEDS_ATTENTION" else OVERALL_STATUS="? CRITICAL" fi echo "? Overall Health: $HEALTH_PERCENTAGE% ($PASSED_CHECKS/$TOTAL_CHECKS checks passed)" echo "? Status: $OVERALL_STATUS" # Add final summary to report cat >> "$REPORT_FILE" << EOF --- ## ? FINAL ASSESSMENT ### Overall Health Score: $HEALTH_PERCENTAGE% ($PASSED_CHECKS/$TOTAL_CHECKS) ### Status: $OVERALL_STATUS ### Summary by Category: - **Infrastructure:** $([ "$SYMLINK_STATUS" = "HEALTHY" ] && echo "✅ Healthy" || echo "⚠️ Issues") - **Laravel Application:** $([ "$LARAVEL_STATUS" = "DETECTED" ] && echo "✅ Detected" || echo "⚠️ Issues") - **Database:** $([ "$DB_STATUS" = "✅ CONNECTED" ] && echo "✅ Connected" || echo "⚠️ Issues") - **Cache System:** $([ "$CACHE_STATUS" = "✅ WORKING" ] && echo "✅ Working ($CACHE_DRIVER)" || echo "⚠️ Failed ($CACHE_DRIVER)") - **Session System:** $([ "$SESSION_STATUS" = "✅ WORKING" ] && echo "✅ Working ($SESSION_DRIVER)" || echo "⚠️ Failed ($SESSION_DRIVER)") - **Security:** $([ $SECURITY_ISSUES -eq 0 ] && echo "✅ Secure" || echo "⚠️ $SECURITY_ISSUES issues") - **Performance:** $([ "$CONFIG_CACHE" = "✅ CACHED" ] && echo "✅ Optimized" || echo "⚠️ Not optimized") - **HTTP Response:** $(if [[ "$HTTP_STATUS" =~ "HEALTHY" ]]; then echo "✅ Healthy"; elif [[ "$HTTP_STATUS" =~ "PENDING_INSTALL" ]]; then echo "✅ Ready for setup"; elif [[ "$HTTP_STATUS" =~ "MAINTENANCE" ]]; then echo "⚠️ Maintenance"; else echo "⚠️ Issues"; fi) ### Recommendations: EOF # Generate recommendations if [ "$SYMLINK_STATUS" != "HEALTHY" ]; then echo "- ? **Fix symlink issues** - Critical for zero-downtime deployment" >> "$REPORT_FILE" fi if [ $SECURITY_ISSUES -gt 0 ]; then echo "- ? **Address security issues** - $SECURITY_ISSUES problems found" >> "$REPORT_FILE" fi if [ "$CACHE_STATUS" = "❌ FAILED" ]; then echo "- ?️ **Fix Cache System** - $CACHE_DRIVER driver failed. Consider switching to \`file\` driver in .env" >> "$REPORT_FILE" fi if [ "$SESSION_STATUS" = "❌ FAILED" ]; then echo "- ? **Fix Session System** - $SESSION_DRIVER driver failed. Consider switching to \`file\` driver in .env" >> "$REPORT_FILE" fi if [ "$CONFIG_CACHE" != "✅ CACHED" ]; then echo "- ⚡ **Enable Laravel caching** - Run \`php artisan config:cache\` and \`php artisan route:cache\`" >> "$REPORT_FILE" fi if [ $OLD_RELEASES -gt 0 ]; then echo "- ?️ **Cleanup old releases** - Remove $OLD_RELEASES old releases to save disk space" >> "$REPORT_FILE" fi if [[ "$HTTP_STATUS" =~ "PENDING_INSTALL" ]]; then echo "- ? **Complete Installation** - Visit \`$APP_URL/install\` to finish setup" >> "$REPORT_FILE" elif [ "$HTTP_CODE" != "200" ] && [ "$HTTP_CODE" != "301" ] && [ "$HTTP_CODE" != "302" ] && [ "$HTTP_CODE" != "503" ]; then echo "- ? **Fix HTTP issues** - Application not responding correctly (HTTP $HTTP_CODE)" >> "$REPORT_FILE" fi if [ "$DB_STATUS" != "✅ CONNECTED" ]; then echo "- ?️ **Fix database connection** - Application cannot connect to database" >> "$REPORT_FILE" fi # Add no issues message if everything is good if [ $HEALTH_PERCENTAGE -ge 90 ]; then echo "- ✅ **No critical issues found** - Deployment is healthy and ready for production" >> "$REPORT_FILE" fi cat >> "$REPORT_FILE" << EOF --- ## ? DETAILED LOGS ### Environment Variables \`\`\`bash DOMAIN_NAME=$DOMAIN_NAME DEPLOY_PATH=$DEPLOY_PATH CURRENT_RELEASE=$CURRENT_RELEASE TOTAL_RELEASES=$TOTAL_RELEASES SHARED_SIZE=$SHARED_SIZE PHP_VERSION=$PHP_VERSION COMPOSER_VERSION=$COMPOSER_VERSION \`\`\` ### File Permissions \`\`\`bash EOF # Add file permissions if [ -f "$SHARED_PATH/.env" ]; then echo "shared/.env: $(stat -c "%a %U:%G" "$SHARED_PATH/.env" 2>/dev/null)" >> "$REPORT_FILE" fi if [ -d "$SHARED_PATH/storage" ]; then echo "shared/storage: $(stat -c "%a %U:%G" "$SHARED_PATH/storage" 2>/dev/null)" >> "$REPORT_FILE" fi cat >> "$REPORT_FILE" << EOF \`\`\` ### Disk Usage \`\`\`bash EOF du -sh releases/* 2>/dev/null | tail -5 >> "$REPORT_FILE" || echo "No releases found" >> "$REPORT_FILE" cat >> "$REPORT_FILE" << EOF \`\`\` --- **Report Generated:** $(date '+%Y-%m-%d %H:%M:%S') **Script Version:** Phase C-2 v2.0 **Next Report:** Run Phase C-2 after next deployment EOF # C2-11: Cleanup and Final Output echo "=== Finalizing Report ===" # Clean up temporary files rm -f /tmp/symlink_report /tmp/shared_report # Set proper permissions on report chmod 644 "$REPORT_FILE" # Create latest report symlink cd "$REPORT_DIR" rm -f latest-report.md ln -sf "$(basename "$REPORT_FILE")" latest-report.md echo "✅ Comprehensive verification completed" echo "? Report saved: $REPORT_FILE" echo "? Latest report: $REPORT_DIR/latest-report.md" echo "? Overall Health: $HEALTH_PERCENTAGE% - $OVERALL_STATUS" # Return to original directory cd "$DEPLOY_PATH" echo "? Phase C-2 completed successfully" exit 0 ]

=== Phase C-2: Comprehensive Deployment Verification & Reporting ===
? DEPLOYHQ MODE: Using DeployHQ path variables
? Path Variables:
   Deploy Path: /home/u227177893/domains/staging.societypal.com/deploy
   Release Path: /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819034814
   Shared Path: /home/u227177893/domains/staging.societypal.com/deploy/shared
   Current Path: /home/u227177893/domains/staging.societypal.com/deploy/current
? Starting comprehensive deployment verification...
   Domain: staging.societypal.com
   Deploy Path: /home/u227177893/domains/staging.societypal.com/deploy
   Report: /home/u227177893/domains/staging.societypal.com/deployment-reports/deployment-report-20250819_035715.md
=== Environment Detection ===
 Current release detected: 20250819034814
? Total releases: 1
? Shared directory size: 12M
=== Critical Symlink Verification ===
 Web Root (public_html): deploy/current/public
 Current Release: ./releases/20250819034814
 Environment File: /home/u227177893/domains/staging.societypal.com/deploy/shared/.env
 Storage Directory: /home/u227177893/domains/staging.societypal.com/deploy/shared/storage
 Bootstrap Cache: ../../../shared/bootstrap/cache
 Public Storage: ../storage/app/public
=== Shared Directory Analysis ===
 Laravel Storage: 11M (484 files)
 Bootstrap Cache: 584K (5 files)
 Environment File: 8.0K (1 file)
 User Content: uploads: 4.0K (0 files)
 User Content: user-uploads: 4.0K (0 files)
 User Content: media: 4.0K (0 files)
 User Content: avatars: 4.0K (0 files)
 User Content: attachments: 4.0K (0 files)
 User Content: documents: 4.0K (0 files)
 User Content: files: 4.0K (0 files)
 User Content: images: 4.0K (0 files)
 Generated: qrcodes: 4.0K (0 files)
 Generated: barcodes: 4.0K (0 files)
 Generated: certificates: 4.0K (0 files)
 Generated: reports: 4.0K (0 files)
 CodeCanyon Modules: 4.0K (0 files)
=== Laravel Application Health Check ===
? PHP Version: 8.2.28
? Composer: 2.5.5 (composer2)
 exec() Function: DISABLED
    exec() disabled - some artisan commands may fail (common on shared hosting)
    Our scripts use manual fallbacks to bypass exec() limitations
? Laravel: Laravel Framework 12.17.0
? Database: Connection Failed
? Cache Driver: 
 Cache: Working ()
? Session Driver: 
 Sessions: Working ()
? Vendor:  EXISTS
? Storage:  SYMLINKED
 Environment:  SYMLINKED
=== Security Verification ===
 Web root correctly points to Laravel public directory
 No sensitive files exposed in web root
 .env file has secure permissions (600)
=== Performance & Optimization Check ===
 Config Cache:  CACHED
? Route Cache:  CACHED
? View Cache:  CACHED (483 files)
? Composer Autoload:  OPTIMIZED (9430 classes)
? OPcache: AVAILABLE
=== Release Management Analysis ===
? Total releases: 1
? Latest release: 20250819034814
? 20250819034814: 238M (0h ago) [CURRENT]
=== HTTP Health Check ===
? Testing: https://societypro-saas.test
 Application returned: HTTP 000000
=== Generating Final Summary ===
? Overall Health: 83% (10/12 checks passed)
? Status: ? GOOD
=== Finalizing Report ===
 Comprehensive verification completed
? Report saved: /home/u227177893/domains/staging.societypal.com/deployment-reports/deployment-report-20250819_035715.md
? Latest report: /home/u227177893/domains/staging.societypal.com/deployment-reports/latest-report.md
? Overall Health: 83% - ? GOOD
? Phase C-2 completed successfully
Time taken to run Phase C-2: Comprehensive Deployment Verification & Reporting: 3.8 seconds


Cleaning up old releases

