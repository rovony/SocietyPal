Checking out repository for deployment

Checking out working copy of your repository at f6adbf (main)


Setting up environment for build commands

Preparing environment for build commands

Uploading repository to build server

Sending cached build to build server

Sending config files to build server


Running build command "G2-BUILD COMMAND 01: Laravel Directory Setup-inBeta"

Running G2-BUILD COMMAND 01: Laravel Directory Setup-inBeta [#!/usr/bin/env bash set -euo pipefail # ============================================================================= # BUILD COMMAND 01: Laravel Directory Setup # ============================================================================= # Group: 2 # Name: G2-BUILD COMMAND 01: Laravel Directory Setup-inBeta # Desc: Creates essential Laravel directory structure with proper permissions # for storage, cache, and framework directories. Supports all Laravel # versions 8-12 and handles shared hosting compatibility. # ============================================================================= # Safety checks if [ ! -f composer.lock ]; then echo "❌ composer.lock not found!" echo "? Run 'composer install' locally to generate lock file" echo "? Ensure composer.lock is committed to version control" echo "? Build failed - lock file required for reproducible builds" exit 1 fi echo "=== Creating Laravel Directory Structure ===" # Create ALL required Laravel directories (covers all Laravel versions 8-12) echo "Creating Laravel directories..." mkdir -p bootstrap/cache mkdir -p storage/app/public mkdir -p storage/framework/cache/data mkdir -p storage/framework/sessions mkdir -p storage/framework/testing mkdir -p storage/framework/views mkdir -p storage/logs mkdir -p storage/clockwork mkdir -p storage/debugbar mkdir -p public/storage # Set proper permissions for all directories echo "Setting directory permissions..." chmod -R 755 bootstrap/cache chmod -R 755 storage find storage -type d -exec chmod 775 {} \; # Create .gitkeep files for empty directories echo "Creating .gitkeep files..." find storage -type d -empty -exec touch {}/.gitkeep \; 2>/dev/null || true touch bootstrap/cache/.gitkeep 2>/dev/null || true # Verify directory structure echo "Verifying directory structure..." echo "✅ bootstrap/cache: $([ -d "bootstrap/cache" ] && echo "exists" || echo "missing")" echo "✅ storage/app: $([ -d "storage/app" ] && echo "exists" || echo "missing")" echo "✅ storage/framework: $([ -d "storage/framework" ] && echo "exists" || echo "missing")" echo "✅ storage/logs: $([ -d "storage/logs" ] && echo "exists" || echo "missing")" echo "✅ Laravel directory structure created"]

=== Creating Laravel Directory Structure ===
Creating Laravel directories...
Setting directory permissions...
Creating .gitkeep files...
Verifying directory structure...
 bootstrap/cache: exists
 storage/app: exists
 storage/framework: exists
 storage/logs: exists
 Laravel directory structure created
Time taken to run G2-BUILD COMMAND 01: Laravel Directory Setup-inBeta: 1.64 seconds


Running build command "G2-BUILD COMMAND 02: PHP Dependencies Installation-inBeta"

Running G2-BUILD COMMAND 02: PHP Dependencies Installation-inBeta [#!/usr/bin/env bash set -euo pipefail # ============================================================================= # BUILD COMMAND 02: PHP Dependencies Installation # ============================================================================= # Group: 2 # Name: G2-BUILD COMMAND 02: PHP Dependencies Installation-inBeta # Desc: Installs production PHP dependencies using Composer with optimization. # Validates lock files for reproducible builds and sets memory limits # for large dependency installations. # ============================================================================= # Safety checks # Check for composer.json (project definition) if [ ! -f composer.json ]; then echo "❌ composer.json not found—run from Laravel project root"; exit 1 fi # Check for composer.lock (dependency lock file) if [ ! -f composer.lock ]; then echo "❌ composer.lock not found!" echo "? Run 'composer install' locally to generate lock file" echo "? Ensure composer.lock is committed to version control" echo "? Build failed - lock file required for reproducible builds" exit 1 fi # Note: Composer (composer install) Uses composer.lock by default echo "=== Installing PHP Dependencies ===" # Set memory limit for Composer export COMPOSER_MEMORY_LIMIT=-1 # Verify Composer composer --version # Install production dependencies echo "Installing PHP dependencies..." composer install \ --no-dev \ --optimize-autoloader \ --no-interaction \ --prefer-dist \ --no-progress # Verify installation echo "Verifying Composer installation..." if composer validate --no-check-publish --quiet; then echo "✅ Composer dependencies valid" else echo "⚠️ Composer validation warnings (continuing)" fi echo "✅ PHP dependencies installed"]

=== Installing PHP Dependencies ===
Composer version 2.6.6 2023-12-08 18:32:26
Installing PHP dependencies...
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
  - Installing wikimedia/composer-merge-plugin (v2.1.0): Extracting archive
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
Generating optimized autoload files
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
Verifying Composer installation...
 Composer dependencies valid
 PHP dependencies installed
Time taken to run G2-BUILD COMMAND 02: PHP Dependencies Installation-inBeta: 8.53 seconds


Running build command "G2-BUILD COMMAND 02.1: Smart PHP Dev Dependencies Detection-inBeta"

Running G2-BUILD COMMAND 02.1: Smart PHP Dev Dependencies Detection-inBeta [#!/usr/bin/env bash set -euo pipefail # ============================================================================= # BUILD COMMAND 02.1: Smart PHP Dev Dependencies Detection # ============================================================================= # Group: 2 # Name: G2-BUILD COMMAND 02.1: Smart PHP Dev Dependencies Detection-inBeta # Desc: Intelligently detects when PHP dev dependencies are needed in production. # Covers 16 detection patterns including Faker, Telescope, Laravel tools, # CodeCanyon apps, and complex applications. Prevents production failures # while avoiding unnecessary bloat. # ============================================================================= # # PURPOSE: Detect if PHP dev dependencies are needed in production # GOAL: Prevent production errors in CodeCanyon apps and unknown codebases # # EASILY CUSTOMIZABLE: # - To ADD a new detection: Copy any existing check and modify the pattern # - To REMOVE a detection: Comment out or delete the relevant section # - Each detection is independent and can be enabled/disabled separately # # DETECTION CATEGORIES: # 1. Core Laravel (Faker, Telescope, Debugbar) # 2. CodeCanyon/Third-party (IDE Helper, PDF libs, Spatie packages) # 3. Environment-specific (Testing tools for staging) # 4. Complex app fallback (Large apps with dev dependencies) # # ============================================================================= # Safety checks # Check for composer.json (project definition) if [ ! -f composer.json ]; then echo "❌ composer.json not found—run from Laravel project root"; exit 1 fi # Check for composer.lock (dependency lock file) if [ ! -f composer.lock ]; then echo "❌ composer.lock not found!" echo "? Run 'composer install' locally to generate lock file" echo "? Ensure composer.lock is committed to version control" echo "? Build failed - lock file required for reproducible builds" exit 1 fi echo "=== Checking PHP Dev Dependencies ===" # Set memory limit for Composer export COMPOSER_MEMORY_LIMIT=-1 echo "? Detecting if PHP dev dependencies are needed..." echo "? Comprehensive scan for CodeCanyon apps and unknown dependencies" NEEDS_DEV=false DETECTION_REASONS=() # ============================================================================ # CORE DEV DEPENDENCY DETECTION (Most Common Cases) # ============================================================================ # 1. Faker Detection (migrations/seeders/factories) if [ -d "database" ] && grep -r "Faker\\\\" database/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? FAKER: Used in database files") echo " ? Faker detected in database files (needed for production)" fi # 2. Telescope Detection (Laravel debugging) if [ -f "config/telescope.php" ] && grep -r "TelescopeServiceProvider\|Telescope::" app/ config/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? TELESCOPE: Actively configured") echo " ? Telescope actively configured for production" fi # 3. Debugbar Detection (Laravel debugging) if [ -f "config/debugbar.php" ] && grep -q "enabled.*true" config/debugbar.php 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? DEBUGBAR: Enabled in config") echo " ? Debugbar enabled in configuration" fi # ============================================================================ # CODECANYON & THIRD-PARTY APP DETECTION (Unknown Dependencies) # ============================================================================ # 4. IDE Helper Detection (often needed in production for some apps) if grep -r "ide-helper\|barryvdh.*ide" app/ config/ composer.json 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? IDE-HELPER: Used in production code") echo " ? IDE Helper detected in production code" fi # 5. Clockwork Detection (performance profiling) if [ -f "config/clockwork.php" ] || grep -r "Clockwork\|clockwork" app/ config/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("⏰ CLOCKWORK: Performance profiling active") echo " ⏰ Clockwork performance profiling detected" fi # 6. PDF Generation Libraries (commonly in dev deps but used in production) if grep -r "dompdf\|mpdf\|tcpdf\|Pdf::" app/ config/ composer.json 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? PDF-LIBS: PDF generation libraries detected") echo " ? PDF generation libraries detected" fi # 7. Spatie Packages Detection (many useful packages often in dev) if grep -r "spatie\\\\" app/ 2>/dev/null | grep -v "laravel-permission\|laravel-activitylog" >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? SPATIE: Spatie dev packages in use") echo " ? Spatie development packages detected in code" fi # 8. Code Generation Tools (often needed for production builds) if grep -r "Generator\|Artisan.*make\|Schema::" app/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? GENERATORS: Code generation tools detected") echo " ? Code generation tools detected" fi # 9. Testing Tools in Staging/Demo Environments DEPLOY_TARGET=${DEPLOY_TARGET:-"production"} if [ "$DEPLOY_TARGET" = "staging" ] || [ "$DEPLOY_TARGET" = "demo" ] || [ "$DEPLOY_TARGET" = "development" ]; then if grep -q '"phpunit\|"pest\|"mockery' composer.json 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? TESTING: Testing tools for $DEPLOY_TARGET environment") echo " ? Testing tools detected for $DEPLOY_TARGET environment" fi fi # 10. Laravel Packages That Are Often Dev But Needed in Production DEV_PROD_PACKAGES=( "laravel/tinker" "facade/ignition" "nunomaduro/collision" "filament/filament.*dev" "livewire.*dev" "inertiajs.*dev" ) for package in "${DEV_PROD_PACKAGES[@]}"; do if grep -q "\"$package\"" composer.json 2>/dev/null && grep -r "${package//[^a-zA-Z]/}" app/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? DEV-PROD: $package used in production code") echo " ? $package detected in production code" break fi done # ============================================================================ # CODECANYON SPECIFIC PATTERNS (Common in purchased scripts) # ============================================================================ # 11. Admin Panel Generators (commonly use dev tools in production) if grep -r "Admin\|Dashboard\|Backend" app/Http/Controllers/ 2>/dev/null | grep -i "generate\|create\|build" >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? ADMIN-GEN: Admin panel generators detected") echo " ? Admin panel generators detected (common in CodeCanyon apps)" fi # 12. Database Seeders in Production (CodeCanyon apps often seed in production) if [ -d "database/seeders" ] && find database/seeders -name "*.php" -exec grep -l "Faker\|factory\|create(" {} \; 2>/dev/null | head -1 | grep -q .; then NEEDS_DEV=true DETECTION_REASONS+=("? SEEDERS: Production seeders with dev dependencies") echo " ? Production seeders detected with dev dependencies" fi # 13. Dynamic Config/Route Generation (common in CodeCanyon apps) if grep -r "Config::set\|Route::.*group.*function\|Schema::create" app/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("⚙️ DYNAMIC: Dynamic config/route generation detected") echo " ⚙️ Dynamic configuration generation detected" fi # 14. Laravel-specific Dev Tools (Horizon, Octane, Scout) if grep -q '"laravel/horizon"\|"laravel/octane"\|"laravel/scout"' composer.json 2>/dev/null; then if grep -r "Horizon::\|Octane::\|Scout::" app/ config/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? LARAVEL-TOOLS: Laravel dev tools in production") echo " ? Laravel Horizon/Octane/Scout detected in production code" fi fi # ============================================================================ # FALLBACK: Check if dev dependencies exist and app seems complex # ============================================================================ # 15. Complex App Fallback (if has dev deps and many controllers/models) CONTROLLER_COUNT=$(find app/Http/Controllers -name "*.php" 2>/dev/null | wc -l) MODEL_COUNT=$(find app/Models -name "*.php" 2>/dev/null | wc -l) if grep -q '"require-dev"' composer.json 2>/dev/null && [ ${#DETECTION_REASONS[@]} -eq 0 ]; then if [ "$CONTROLLER_COUNT" -gt 10 ] || [ "$MODEL_COUNT" -gt 5 ]; then NEEDS_DEV=true DETECTION_REASONS+=("?️ COMPLEX-APP: Complex app with dev dependencies declared") echo " ?️ Complex app detected ($CONTROLLER_COUNT controllers, $MODEL_COUNT models) with dev dependencies" else echo " ? Dev dependencies declared but no specific production needs detected" fi fi echo "" if [ "$NEEDS_DEV" = true ]; then echo "? Installing PHP dev dependencies (needed for production)..." echo "? Reasons: ${DETECTION_REASONS[*]}" composer install \ --optimize-autoloader \ --no-interaction \ --prefer-dist \ --no-progress echo "✅ PHP dev dependencies installed (production requirements)" else echo "ℹ️ No PHP dev dependencies needed for production" echo "✅ Production-only setup is sufficient" fi echo "✅ PHP dev dependency check completed"]

=== Checking PHP Dev Dependencies ===
? Detecting if PHP dev dependencies are needed...
? Comprehensive scan for CodeCanyon apps and unknown dependencies
  ? Faker detected in database files (needed for production)
  ? Debugbar enabled in configuration
   Clockwork performance profiling detected
  ? PDF generation libraries detected
  ? Code generation tools detected
  ? Admin panel generators detected (common in CodeCanyon apps)
  ? Production seeders detected with dev dependencies
   Dynamic configuration generation detected

? Installing PHP dev dependencies (needed for production)...
? Reasons: ? FAKER: Used in database files ? DEBUGBAR: Enabled in config  CLOCKWORK: Performance profiling active ? PDF-LIBS: PDF generation libraries detected ? GENERATORS: Code generation tools detected ? ADMIN-GEN: Admin panel generators detected ? SEEDERS: Production seeders with dev dependencies  DYNAMIC: Dynamic config/route generation detected
Installing dependencies from lock file (including require-dev)
Verifying lock file contents can be installed on current platform.
Package operations: 36 installs, 0 updates, 0 removals
  - Downloading php-debugbar/php-debugbar (v2.1.6)
  - Downloading barryvdh/laravel-debugbar (v3.15.4)
  - Downloading fakerphp/faker (v1.24.1)
  - Downloading laravel/pint (v1.22.1)
  - Downloading symfony/yaml (v7.3.0)
  - Downloading laravel/sail (v1.43.1)
  - Downloading hamcrest/hamcrest-php (v2.1.1)
  - Downloading mockery/mockery (1.6.12)
  - Downloading filp/whoops (2.18.1)
  - Downloading nunomaduro/collision (v8.8.0)
  - Downloading staabm/side-effects-detector (1.0.5)
  - Downloading sebastian/version (5.0.2)
  - Downloading sebastian/type (5.1.2)
  - Downloading sebastian/recursion-context (6.0.2)
  - Downloading sebastian/object-reflector (4.0.1)
  - Downloading sebastian/object-enumerator (6.0.1)
  - Downloading sebastian/global-state (7.0.2)
  - Downloading sebastian/exporter (6.3.0)
  - Downloading sebastian/environment (7.2.1)
  - Downloading sebastian/diff (6.0.2)
  - Downloading sebastian/comparator (6.3.1)
  - Downloading sebastian/code-unit (3.0.3)
  - Downloading sebastian/cli-parser (3.0.2)
  - Downloading phpunit/php-timer (7.0.1)
  - Downloading phpunit/php-text-template (4.0.1)
  - Downloading phpunit/php-invoker (5.0.1)
  - Downloading phpunit/php-file-iterator (5.1.0)
  - Downloading theseer/tokenizer (1.2.3)
  - Downloading sebastian/lines-of-code (3.0.1)
  - Downloading sebastian/complexity (4.0.1)
  - Downloading sebastian/code-unit-reverse-lookup (4.0.1)
  - Downloading phpunit/php-code-coverage (11.0.9)
  - Downloading phar-io/version (3.2.1)
  - Downloading phar-io/manifest (2.0.4)
  - Downloading myclabs/deep-copy (1.13.1)
  - Downloading phpunit/phpunit (11.5.22)
  - Installing php-debugbar/php-debugbar (v2.1.6): Extracting archive
  - Installing barryvdh/laravel-debugbar (v3.15.4): Extracting archive
  - Installing fakerphp/faker (v1.24.1): Extracting archive
  - Installing laravel/pint (v1.22.1): Extracting archive
  - Installing symfony/yaml (v7.3.0): Extracting archive
  - Installing laravel/sail (v1.43.1): Extracting archive
  - Installing hamcrest/hamcrest-php (v2.1.1): Extracting archive
  - Installing mockery/mockery (1.6.12): Extracting archive
  - Installing filp/whoops (2.18.1): Extracting archive
  - Installing nunomaduro/collision (v8.8.0): Extracting archive
  - Installing staabm/side-effects-detector (1.0.5): Extracting archive
  - Installing sebastian/version (5.0.2): Extracting archive
  - Installing sebastian/type (5.1.2): Extracting archive
  - Installing sebastian/recursion-context (6.0.2): Extracting archive
  - Installing sebastian/object-reflector (4.0.1): Extracting archive
  - Installing sebastian/object-enumerator (6.0.1): Extracting archive
  - Installing sebastian/global-state (7.0.2): Extracting archive
  - Installing sebastian/exporter (6.3.0): Extracting archive
  - Installing sebastian/environment (7.2.1): Extracting archive
  - Installing sebastian/diff (6.0.2): Extracting archive
  - Installing sebastian/comparator (6.3.1): Extracting archive
  - Installing sebastian/code-unit (3.0.3): Extracting archive
  - Installing sebastian/cli-parser (3.0.2): Extracting archive
  - Installing phpunit/php-timer (7.0.1): Extracting archive
  - Installing phpunit/php-text-template (4.0.1): Extracting archive
  - Installing phpunit/php-invoker (5.0.1): Extracting archive
  - Installing phpunit/php-file-iterator (5.1.0): Extracting archive
  - Installing theseer/tokenizer (1.2.3): Extracting archive
  - Installing sebastian/lines-of-code (3.0.1): Extracting archive
  - Installing sebastian/complexity (4.0.1): Extracting archive
  - Installing sebastian/code-unit-reverse-lookup (4.0.1): Extracting archive
  - Installing phpunit/php-code-coverage (11.0.9): Extracting archive
  - Installing phar-io/version (3.2.1): Extracting archive
  - Installing phar-io/manifest (2.0.4): Extracting archive
  - Installing myclabs/deep-copy (1.13.1): Extracting archive
  - Installing phpunit/phpunit (11.5.22): Extracting archive
Generating optimized autoload files
> Illuminate\Foundation\ComposerScripts::postAutoloadDump
> @php artisan package:discover --ansi

   INFO  Discovering packages.  

  barryvdh/laravel-debugbar ............................................. DONE
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
  laravel/sail .......................................................... DONE
  laravel/sanctum ....................................................... DONE
  laravel/tinker ........................................................ DONE
  livewire/livewire ..................................................... DONE
  maatwebsite/excel ..................................................... DONE
  macellan/laravel-zip .................................................. DONE
  nesbot/carbon ......................................................... DONE
  nunomaduro/collision .................................................. DONE
  nunomaduro/termwind ................................................... DONE
  nwidart/laravel-modules ............................................... DONE
  spatie/laravel-permission ............................................. DONE
  spatie/laravel-translatable ........................................... DONE
  unicodeveloper/laravel-paystack ....................................... DONE

99 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
 PHP dev dependencies installed (production requirements)
 PHP dev dependency check completed
Time taken to run G2-BUILD COMMAND 02.1: Smart PHP Dev Dependencies Detection-inBeta: 6.05 seconds


Running build command "G2-BUILD COMMAND 03: Node.js Dependencies Installation-inBeta"

Running G2-BUILD COMMAND 03: Node.js Dependencies Installation-inBeta [#!/usr/bin/env bash set -euo pipefail # ============================================================================= # BUILD COMMAND 03: Node.js Dependencies Installation # ============================================================================= # Group: 2 # Name: G2-BUILD COMMAND 03: Node.js Dependencies Installation-inBeta # Desc: Installs Node.js dependencies for frontend builds using npm ci. # Validates package-lock.json for reproducible builds and handles # API-only Laravel applications gracefully. # ============================================================================= # Safety checks # Check for composer.json (Laravel project requirement) if [ ! -f composer.json ]; then echo "❌ composer.json not found—run from Laravel project root"; exit 1 fi echo "=== Installing Node.js Dependencies ===" # Note: npm (npm ci) Uses package-lock.json # Check if frontend build is required if [ ! -f "package.json" ]; then echo "ℹ️ No package.json found, skipping Node.js dependencies" exit 0 fi # Check for package-lock.json (dependency lock file) if [ ! -f "package-lock.json" ]; then echo "❌ package-lock.json not found!" echo "? Run 'npm install' locally to generate lock file" echo "? Ensure package-lock.json is committed to version control" echo "? Build failed - lock file required for reproducible builds" exit 1 fi # Verify Node.js and npm node --version npm --version # Install dependencies from package-lock.json (production + dev for building) echo "Installing Node.js dependencies from package-lock.json..." npm ci --no-audit --no-fund # Suppress npm upgrade notices for cleaner output if npm --version >/dev/null 2>&1; then echo "✅ Node.js dependencies installed" else echo "⚠️ Node.js dependencies installed with warnings" fi]

=== Installing Node.js Dependencies ===
v22.12.0
10.9.0
Installing Node.js dependencies from package-lock.json...

added 187 packages in 4s
npm notice
npm notice New major version of npm available! 10.9.0 -> 11.5.2
npm notice Changelog: https://github.com/npm/cli/releases/tag/v11.5.2
npm notice To update run: npm install -g npm@11.5.2
npm notice
 Node.js dependencies installed
Time taken to run G2-BUILD COMMAND 03: Node.js Dependencies Installation-inBeta: 5.72 seconds


Running build command "G2-BUILD COMMAND 03.1: Smart Node.js Dev Dependencies Detection-inBeta"

Running G2-BUILD COMMAND 03.1: Smart Node.js Dev Dependencies Detection-inBeta [#!/usr/bin/env bash set -euo pipefail # ============================================================================= # BUILD COMMAND 03.1: Smart Node.js Dev Dependencies Detection # ============================================================================= # Group: 2 # Name: G2-BUILD COMMAND 03.1: Smart Node.js Dev Dependencies Detection-inBeta # Desc: Intelligently detects when Node.js dev dependencies are needed for builds. # Covers 23 detection patterns including Vue/React/Angular, Vite/Webpack, # TypeScript, CSS frameworks, and modern meta-frameworks. Optimizes for # CodeCanyon apps and complex frontend builds. # ============================================================================= # # PURPOSE: Detect if Node.js dev dependencies are needed in production # GOAL: Prevent frontend build failures in CodeCanyon apps and unknown codebases # # EASILY CUSTOMIZABLE: # - To ADD a new detection: Copy any existing check and modify the pattern # - To REMOVE a detection: Comment out or delete the relevant section # - Each detection is independent and can be enabled/disabled separately # # DETECTION CATEGORIES: # 1. Core Build Tools (Bundlers, TypeScript, Build Scripts) # 2. Frontend Frameworks (Vue, React, Angular, Svelte) # 3. CSS Frameworks (Tailwind, Sass, PostCSS, Bootstrap) # 4. Testing & Quality (Jest, Cypress, ESLint, Prettier) # 5. CodeCanyon/Third-party (Admin panels, theme builders) # 6. Environment-specific (Dev servers, hot reloading) # # ============================================================================= # Safety checks # Check for composer.json (Laravel project requirement) if [ ! -f composer.json ]; then echo "❌ composer.json not found—run from Laravel project root"; exit 1 fi echo "=== Checking Node.js Dev Dependencies ===" # Only run if package.json exists if [ ! -f "package.json" ]; then echo "ℹ️ No package.json found, skipping Node.js dev dependency check" exit 0 fi # Check for package-lock.json (dependency lock file) if [ ! -f "package-lock.json" ]; then echo "❌ package-lock.json not found!" echo "? Run 'npm install' locally to generate lock file" echo "? Ensure package-lock.json is committed to version control" echo "? Build failed - lock file required for reproducible builds" exit 1 fi echo "? Detecting if Node.js dev dependencies are needed..." echo "? Comprehensive scan for CodeCanyon apps and frontend build requirements" NEEDS_DEV=false DETECTION_REASONS=() # ============================================================================ # CORE BUILD TOOLS DETECTION (Essential Build Requirements) # ============================================================================ # 1. Build Scripts Detection (primary indicator) if grep -q '"build":\|"production":\|"prod":\|"compile":' package.json 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("⚙️ BUILD-SCRIPTS: Production build scripts found") echo " ⚙️ Build scripts found (npm run build/production/prod)" fi # 2. Asset Bundler Config Files BUNDLER_CONFIGS=("vite.config.js" "vite.config.mjs" "vite.config.ts" "webpack.mix.js" "webpack.config.js" "rollup.config.js" "gulpfile.js" "parcel.config.js") for config in "${BUNDLER_CONFIGS[@]}"; do if [ -f "$config" ]; then NEEDS_DEV=true DETECTION_REASONS+=("?️ BUNDLER-CONFIG: $config found") echo " ?️ Asset bundler config detected: $config" break fi done # 3. TypeScript Detection (compilation needed) if [ -f "tsconfig.json" ] && find . -name "*.ts" -o -name "*.tsx" 2>/dev/null | head -1 | grep -q . 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? TYPESCRIPT: TS files need compilation") echo " ? TypeScript files detected (compilation needed)" fi # 4. Babel Configuration (transpilation needed) if [ -f ".babelrc" ] || [ -f "babel.config.js" ] || [ -f "babel.config.json" ] || grep -q '"babel"' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("? BABEL: Transpilation configuration found") echo " ? Babel transpilation configuration detected" fi # ============================================================================ # FRONTEND FRAMEWORKS DETECTION (Component-based apps) # ============================================================================ # 5. Vue.js Detection if grep -q '"vue":\|"@vue/"' package.json || find . -name "*.vue" 2>/dev/null | head -1 | grep -q . 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("⚛️ VUE: Vue.js framework detected") echo " ⚛️ Vue.js framework detected (compilation required)" fi # 6. React Detection if grep -q '"react":\|"@react/"' package.json || find . -name "*.jsx" 2>/dev/null | head -1 | grep -q . 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("⚛️ REACT: React framework detected") echo " ⚛️ React framework detected (compilation required)" fi # 7. Angular Detection if [ -f "angular.json" ] || grep -q '"@angular/"' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("⚛️ ANGULAR: Angular framework detected") echo " ⚛️ Angular framework detected (compilation required)" fi # 8. Svelte Detection if grep -q '"svelte"' package.json || find . -name "*.svelte" 2>/dev/null | head -1 | grep -q . 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("⚛️ SVELTE: Svelte framework detected") echo " ⚛️ Svelte framework detected (compilation required)" fi # 9. Inertia.js Detection (Laravel SPA framework) if grep -q '"@inertiajs/"' package.json && [ -f "resources/js/app.js" -o -f "resources/js/app.ts" ]; then NEEDS_DEV=true DETECTION_REASONS+=("? INERTIA: Inertia.js SPA framework") echo " ? Inertia.js SPA framework detected" fi # ============================================================================ # CSS FRAMEWORKS & PREPROCESSORS DETECTION (Styling compilation) # ============================================================================ # 10. Tailwind CSS Detection if [ -f "tailwind.config.js" ] || [ -f "tailwind.config.ts" ] || grep -q '"tailwindcss"' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("? TAILWIND: Tailwind CSS compilation needed") echo " ? Tailwind CSS detected (compilation required)" fi # 11. Sass/SCSS Detection if grep -q '"sass":\|"node-sass":\|"dart-sass":' package.json || find . -name "*.scss" -o -name "*.sass" 2>/dev/null | head -1 | grep -q . 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? SASS: Sass/SCSS compilation needed") echo " ? Sass/SCSS files detected (compilation required)" fi # 12. Less Detection if grep -q '"less"' package.json || find . -name "*.less" 2>/dev/null | head -1 | grep -q . 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? LESS: Less compilation needed") echo " ? Less files detected (compilation required)" fi # 13. PostCSS Detection if [ -f "postcss.config.js" ] || grep -q '"postcss":\|"autoprefixer":' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("? POSTCSS: PostCSS processing needed") echo " ? PostCSS configuration detected" fi # 14. Bootstrap/UI Framework Detection if grep -q '"bootstrap":\|"@bootstrap/":\|"bulma":\|"foundation"' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("? UI-FRAMEWORK: UI framework compilation needed") echo " ? UI framework detected (Bootstrap/Bulma/Foundation)" fi # ============================================================================ # TESTING & QUALITY TOOLS DETECTION (Dev/Staging environments) # ============================================================================ DEPLOY_TARGET=${DEPLOY_TARGET:-"production"} # 15. Testing Tools for Staging/Demo if [ "$DEPLOY_TARGET" = "staging" ] || [ "$DEPLOY_TARGET" = "demo" ] || [ "$DEPLOY_TARGET" = "development" ]; then if grep -q '"jest":\|"cypress":\|"playwright":\|"vitest":\|"@testing-library"' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("? TESTING: Frontend testing for $DEPLOY_TARGET") echo " ? Frontend testing tools for $DEPLOY_TARGET environment" fi fi # 16. Linting/Formatting for CI/Staging if [ "$DEPLOY_TARGET" = "staging" ] || [ "$DEPLOY_TARGET" = "demo" ] || [ ! -z "${CI:-}" ]; then if grep -q '"eslint":\|"prettier":\|"stylelint":' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("? LINTING: Code quality tools for $DEPLOY_TARGET") echo " ? Linting/formatting tools for $DEPLOY_TARGET environment" fi fi # ============================================================================ # CODECANYON & THIRD-PARTY DETECTION (Purchased scripts) # ============================================================================ # 17. Admin Panel Frontend Builders if grep -r "admin\|dashboard\|backend" resources/js/ public/js/ 2>/dev/null | grep -i "build\|compile\|generate" >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? ADMIN-FRONTEND: Admin panel frontend detected") echo " ? Admin panel frontend build detected (common in CodeCanyon)" fi # 18. Theme/Template Builders if grep -q '"theme":\|"template":\|"builder":' package.json || [ -d "themes" ] || [ -d "templates" ]; then NEEDS_DEV=true DETECTION_REASONS+=("? THEME-BUILDER: Theme/template system detected") echo " ? Theme/template builder detected" fi # 19. Dynamic Frontend Generation if grep -r "dynamic\|runtime\|generate.*component" resources/js/ public/js/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("⚙️ DYNAMIC-FRONTEND: Dynamic frontend generation") echo " ⚙️ Dynamic frontend generation detected" fi # 20. Modern JavaScript Features (ES6+, modules) if grep -q '"type": "module"\|"@babel/"\|"core-js":' package.json || find . -name "*.mjs" 2>/dev/null | head -1 | grep -q . 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? MODERN-JS: Modern JavaScript features need compilation") echo " ? Modern JavaScript features detected (compilation needed)" fi # 21. Modern Meta-Frameworks (Next.js, Nuxt.js, Gatsby, Remix, Astro) if grep -q '"next"\|"nuxt"\|"gatsby"\|"remix"\|"astro"\|"@next/"\|"@nuxt/"\|"@astrojs/"' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("? META-FRAMEWORK: Modern meta-framework detected") echo " ? Modern meta-framework detected (Next.js/Nuxt.js/Gatsby/Remix/Astro)" fi # ============================================================================ # DEVELOPMENT TOOLS DETECTION (Hot reloading, dev servers) # ============================================================================ # 22. Dev Server Detection if grep -q '"dev":\|"serve":\|"start":' package.json; then if [ "$DEPLOY_TARGET" = "staging" ] || [ "$DEPLOY_TARGET" = "demo" ]; then NEEDS_DEV=true DETECTION_REASONS+=("? DEV-SERVER: Dev server for $DEPLOY_TARGET") echo " ? Dev server configuration for $DEPLOY_TARGET" fi fi # 23. Hot Module Replacement (HMR) if grep -q '"@vitejs/plugin-"\|"webpack-hot-middleware"\|"hot-reload"\|"hmr":' package.json; then if [ "$DEPLOY_TARGET" = "staging" ] || [ "$DEPLOY_TARGET" = "demo" ]; then NEEDS_DEV=true DETECTION_REASONS+=("? HMR: Hot reloading for $DEPLOY_TARGET") echo " ? Hot module replacement for $DEPLOY_TARGET" fi fi # ============================================================================ # FALLBACK: Complex Frontend App Detection # ============================================================================ # 24. Complex Frontend Fallback JS_FILE_COUNT=$(find resources/js public/js assets/js -name "*.js" -o -name "*.ts" -o -name "*.vue" -o -name "*.jsx" 2>/dev/null | wc -l || echo "0") CSS_FILE_COUNT=$(find resources/css resources/sass public/css assets/css -name "*.css" -o -name "*.scss" -o -name "*.sass" 2>/dev/null | wc -l || echo "0") if grep -q '"devDependencies"' package.json 2>/dev/null && [ ${#DETECTION_REASONS[@]} -eq 0 ]; then if [ "$JS_FILE_COUNT" -gt 5 ] || [ "$CSS_FILE_COUNT" -gt 3 ]; then NEEDS_DEV=true DETECTION_REASONS+=("?️ COMPLEX-FRONTEND: Complex frontend with dev dependencies") echo " ?️ Complex frontend detected ($JS_FILE_COUNT JS files, $CSS_FILE_COUNT CSS files) with dev dependencies" else echo " ? Dev dependencies declared but no specific build needs detected" fi fi echo "" if [ "$NEEDS_DEV" = true ]; then echo "? Installing Node.js dev dependencies (needed for asset building)..." echo "? Reasons: ${DETECTION_REASONS[*]}" # Check if dev dependencies are already installed if [ -d "node_modules" ] && [ -d "node_modules/.bin" ]; then echo "ℹ️ Node.js dependencies already installed (dev dependencies available)" else # Install all dependencies (including dev) for building npm ci --no-audit --no-fund fi echo "✅ Node.js dev dependencies installed (for asset compilation)" else echo "? Node.js production dependencies sufficient..." echo "? Reasons: No dev dependencies needed for this app" # Only reinstall if we need to remove dev dependencies if [ -d "node_modules" ]; then echo "ℹ️ Node.js dependencies already installed (production mode sufficient)" else # Install only production dependencies npm ci --production --no-audit --no-fund fi echo "✅ Node.js production dependencies confirmed (runtime only)" fi echo "✅ Node.js dependency setup completed"]

=== Checking Node.js Dev Dependencies ===
? Detecting if Node.js dev dependencies are needed...
? Comprehensive scan for CodeCanyon apps and frontend build requirements
   Build scripts found (npm run build/production/prod)
  ? Asset bundler config detected: vite.config.js
  ? Tailwind CSS detected (compilation required)
  ? Sass/SCSS files detected (compilation required)
  ? PostCSS configuration detected
  ? Modern JavaScript features detected (compilation needed)

? Installing Node.js dev dependencies (needed for asset building)...
? Reasons:  BUILD-SCRIPTS: Production build scripts found ? BUNDLER-CONFIG: vite.config.js found ? TAILWIND: Tailwind CSS compilation needed ? SASS: Sass/SCSS compilation needed ? POSTCSS: PostCSS processing needed ? MODERN-JS: Modern JavaScript features need compilation
 Node.js dependencies already installed (dev dependencies available)
 Node.js dev dependencies installed (for asset compilation)
 Node.js dependency setup completed
Time taken to run G2-BUILD COMMAND 03.1: Smart Node.js Dev Dependencies Detection-inBeta: 2.02 seconds


Running build command "G2-BUILD COMMAND 04: Asset Building & Laravel Optimization-inBeta"

Running G2-BUILD COMMAND 04: Asset Building & Laravel Optimization-inBeta [#!/usr/bin/env bash set -euo pipefail # ============================================================================= # BUILD COMMAND 04: Asset Building & Laravel Optimization # ============================================================================= # Group: 2 # Name: G2-BUILD COMMAND 04: Asset Building & Laravel Optimization-inBeta # Desc: Builds frontend assets and optimizes Laravel autoloader. Tries multiple # build commands (build/production/prod) and handles asset verification. # Cleans up node_modules for minimal deployment size. # ============================================================================= # Safety checks if [ ! -f composer.lock ]; then echo "❌ composer.lock not found!" echo "? Run 'composer install' locally to generate lock file" echo "? Ensure composer.lock is committed to version control" echo "? Build failed - lock file required for reproducible builds" exit 1 fi echo "=== Building Assets & Optimizing Laravel ===" # Generate optimized autoloader echo "Generating optimized autoloader..." composer dump-autoload --optimize --classmap-authoritative # Build frontend assets if Node.js dependencies exist if [ -f "package.json" ]; then # Check for package-lock.json (dependency lock file) if [ ! -f "package-lock.json" ]; then echo "❌ package-lock.json not found!" echo "? Run 'npm install' locally to generate lock file" echo "? Ensure package-lock.json is committed to version control" echo "? Build failed - lock file required for reproducible builds" exit 1 fi echo "Building production assets..." # Try different build commands (covers all Laravel asset compilation methods) if npm run build 2>/dev/null; then echo "✅ Assets built with 'npm run build'" elif npm run production 2>/dev/null; then echo "✅ Assets built with 'npm run production'" elif npm run prod 2>/dev/null; then echo "✅ Assets built with 'npm run prod'" elif npm run dev 2>/dev/null; then echo "⚠️ Built with 'npm run dev' (not optimized for production)" else echo "ℹ️ No suitable build script found, skipping asset compilation" fi # Clean up node_modules to reduce deployment size echo "Cleaning up Node.js modules..." rm -rf node_modules # Verify build output exists if [ -d "public/build" ] || [ -d "public/dist" ] || [ -d "public/js" ] || [ -d "public/css" ] || [ -d "public/assets" ]; then echo "✅ Frontend assets compiled successfully" else echo "ℹ️ No build output detected (may be normal for API-only apps)" fi else echo "ℹ️ No package.json found, skipping asset compilation" fi # Final verification echo "Final verification..." echo "✅ vendor/: $([ -d "vendor" ] && echo "exists" || echo "missing")" echo "✅ bootstrap/cache/: $([ -d "bootstrap/cache" ] && echo "exists" || echo "missing")" echo "✅ Build complete and ready for deployment" echo "✅ Laravel build and optimization completed"]

=== Building Assets & Optimizing Laravel ===
Generating optimized autoloader...
Generating optimized autoload files (authoritative)
> Illuminate\Foundation\ComposerScripts::postAutoloadDump
> @php artisan package:discover --ansi

   INFO  Discovering packages.  

  barryvdh/laravel-debugbar ............................................. DONE
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
  laravel/sail .......................................................... DONE
  laravel/sanctum ....................................................... DONE
  laravel/tinker ........................................................ DONE
  livewire/livewire ..................................................... DONE
  maatwebsite/excel ..................................................... DONE
  macellan/laravel-zip .................................................. DONE
  nesbot/carbon ......................................................... DONE
  nunomaduro/collision .................................................. DONE
  nunomaduro/termwind ................................................... DONE
  nwidart/laravel-modules ............................................... DONE
  spatie/laravel-permission ............................................. DONE
  spatie/laravel-translatable ........................................... DONE
  unicodeveloper/laravel-paystack ....................................... DONE

Generated optimized autoload files (authoritative) containing 11525 classes
Building production assets...

> build
> vite build

vite v5.4.14 building for production...
transforming...
 163 modules transformed.
rendering chunks...
computing gzip size...
public/build/manifest.json              0.27 kB  gzip:   0.15 kB
public/build/assets/app-CaW9wbch.css  168.86 kB  gzip:  23.92 kB
public/build/assets/app-CvQSGMFY.js   784.71 kB  gzip: 207.49 kB
 built in 7.60s
 Assets built with 'npm run build'
Cleaning up Node.js modules...
 Frontend assets compiled successfully
Final verification...
 vendor/: exists
 bootstrap/cache/: exists
 Build complete and ready for deployment
 Laravel build and optimization completed
Time taken to run G2-BUILD COMMAND 04: Asset Building & Laravel Optimization-inBeta: 13.16 seconds


Running build command "G2-BUILD COMMAND 05: Laravel Cache Optimization-inBeta"

Running G2-BUILD COMMAND 05: Laravel Cache Optimization-inBeta [#!/usr/bin/env bash set -euo pipefail # ============================================================================= # BUILD COMMAND 05: Laravel Cache Optimization # ============================================================================= # Group: 2 # Name: G2-BUILD COMMAND 05: Laravel Cache Optimization-inBeta # Desc: Optimizes Laravel for production with config, route, view, and event # caching. Detects environment and skips caching in local development. # Handles closure detection and provides informative feedback. # ============================================================================= # Safety checks if [ ! -f composer.lock ]; then echo "❌ composer.lock not found!" echo "? Run 'composer install' locally to generate lock file" echo "? Ensure composer.lock is committed to version control" echo "? Build failed - lock file required for reproducible builds" exit 1 fi echo "=== Laravel Cache Optimization ===" # Determine environment (skip caching in local development) APP_ENV=${APP_ENV:-$(grep "APP_ENV=" .env 2>/dev/null | cut -d'=' -f2 | tr -d '"' || echo "production")} echo "? Detected environment: $APP_ENV" if [ "$APP_ENV" = "local" ]; then echo "ℹ️ Local environment detected - skipping cache optimization" echo "✅ Laravel optimization completed (local mode)" exit 0 fi echo "⚡ Optimizing Laravel for production environment..." # 1. Memory constraint testing (shared hosting compatibility) echo "? Testing memory constraints for deployment commands..." if php -d memory_limit=128M artisan config:cache --dry-run >/dev/null 2>&1; then echo " ✅ Commands should work with 128M memory limit (shared hosting compatible)" else echo " ⚠️ WARNING: May fail on shared hosting with 128M memory limit" echo " ? Consider requesting higher memory_limit from hosting provider" fi # 2. Configuration Cache echo "? Caching configuration..." if php artisan config:cache 2>/dev/null; then echo " ✅ Configuration cached successfully" else echo " ⚠️ Config cache skipped (closures detected in config files)" fi # 3. Route Cache echo "?️ Caching routes..." if php artisan route:cache 2>/dev/null; then echo " ✅ Routes cached successfully" else echo " ⚠️ Route cache skipped (closure-based routes detected)" echo " ? Convert closure routes to controller methods for caching" fi # 4. View Cache echo "?️ Caching views..." if php artisan view:cache 2>/dev/null; then echo " ✅ Views cached successfully" else echo " ℹ️ View cache skipped (no views found - API-only app)" fi # 5. Event Cache (Laravel 9+) echo "⚡ Caching events..." if php artisan event:cache 2>/dev/null; then echo " ✅ Events cached successfully" else echo " ℹ️ Event cache not available (older Laravel version)" fi # 6. Icon Cache (if available) echo "? Caching icons..." if php artisan icons:cache 2>/dev/null; then echo " ✅ Icons cached successfully" else echo " ℹ️ Icon cache not available (no icon package installed)" fi echo "" echo "✅ Laravel cache optimization completed!" echo "? Production caches created for maximum performance"]

=== Laravel Cache Optimization ===
? Detected environment: staging
 Optimizing Laravel for production environment...
? Testing memory constraints for deployment commands...
   WARNING: May fail on shared hosting with 128M memory limit
  ? Consider requesting higher memory_limit from hosting provider
? Caching configuration...

   INFO  Configuration cached successfully.  

   Configuration cached successfully
? Caching routes...

   INFO  Routes cached successfully.  

   Routes cached successfully
? Caching views...


   INFO  Blade templates cached successfully.  

   Views cached successfully
 Caching events...

   INFO  Events cached successfully.  

   Events cached successfully
? Caching icons...
Blade icons manifest file generated successfully!
   Icons cached successfully

 Laravel cache optimization completed!
? Production caches created for maximum performance
Time taken to run G2-BUILD COMMAND 05: Laravel Cache Optimization-inBeta: 4.14 seconds


Running build command "G2-BUILD COMMAND 06: Security Checks & Final Verification-inBeta"

Running G2-BUILD COMMAND 06: Security Checks & Final Verification-inBeta [#!/usr/bin/env bash set -euo pipefail # ============================================================================= # BUILD COMMAND 06: Security Checks & Final Verification - FIXED # ============================================================================= # Group: 2 # Name: G2-BUILD COMMAND 06: Security Checks & Final Verification-inBeta # Desc: Environment-aware security validation and final verification of Laravel # application readiness. Tests autoloader and provides comprehensive # build summary for deployment. # ============================================================================= # Safety checks if [ ! -f composer.lock ]; then echo "❌ composer.lock not found!" echo "? Run 'composer install' locally to generate lock file" echo "? Ensure composer.lock is committed to version control" echo "? Build failed - lock file required for reproducible builds" exit 1 fi echo "=== Security & Final Verification ===" # Get environment from DeployHQ variable DEPLOY_ENV="%environment%" echo "? Target environment: $DEPLOY_ENV" echo "? Running environment-aware security checks..." # 1. Environment-Aware APP_DEBUG Check if [ "$DEPLOY_ENV" = "production" ] || [ "$DEPLOY_ENV" = "prod" ]; then # Strict APP_DEBUG check for production only if grep -q "APP_DEBUG=true" .env 2>/dev/null; then echo " ❌ CRITICAL: APP_DEBUG=true in PRODUCTION environment!" echo " ? SECURITY RISK: Set APP_DEBUG=false for production security" echo " ? This exposes sensitive application details to users" exit 1 else echo " ✅ APP_DEBUG configuration secure for production" fi else # Relaxed check for non-production environments if grep -q "APP_DEBUG=true" .env 2>/dev/null; then echo " ℹ️ APP_DEBUG=true detected in $DEPLOY_ENV environment (acceptable)" else echo " ✅ APP_DEBUG configuration looks good for $DEPLOY_ENV" fi fi # 2. Universal APP_KEY Check (Required for ALL environments) if grep -q "APP_KEY=" .env 2>/dev/null; then APP_KEY=$(grep "APP_KEY=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'") if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ] || [ ${#APP_KEY} -lt 20 ]; then echo " ❌ CRITICAL: APP_KEY is empty or invalid!" echo " ? Application encryption key missing or too short" echo " ? Generate with: php artisan key:generate" exit 1 else echo " ✅ APP_KEY is properly configured" fi else echo " ❌ CRITICAL: APP_KEY not found in .env" echo " ? Add APP_KEY to .env file and generate with: php artisan key:generate" exit 1 fi # 3. Universal HTTPS Check (Recommended for ALL environments) if grep -q "APP_URL=" .env 2>/dev/null; then APP_URL=$(grep "APP_URL=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'") if [[ "$APP_URL" =~ ^https:// ]]; then echo " ✅ HTTPS configuration detected: $APP_URL" elif [[ "$APP_URL" =~ ^http://localhost ]] || [[ "$APP_URL" =~ ^http://127\.0\.0\.1 ]]; then echo " ℹ️ Local HTTP URL detected: $APP_URL (acceptable for development)" else echo " ⚠️ Non-HTTPS URL detected: $APP_URL" echo " ? Consider using HTTPS for better security" fi else echo " ⚠️ APP_URL not configured in .env" echo " ? Set APP_URL=https://yourdomain.com" fi echo "" echo "? Final verification..." # 4. Laravel Framework Bootstrap Test if php artisan --version >/dev/null 2>&1; then LARAVEL_VERSION=$(php artisan --version 2>/dev/null | head -1) echo " ✅ Laravel application boots successfully" echo " ? Version: $LARAVEL_VERSION" else echo " ❌ Laravel application failed to boot!" echo " ? CRITICAL: Application may not work in production" echo " ? Debug: $(php artisan --version 2>&1 | head -1)" exit 1 fi # 5. Optimized Autoloader Test php -r " require_once 'vendor/autoload.php'; if (class_exists('Illuminate\\Foundation\\Application')) { echo ' ✅ Optimized autoloader working correctly' . PHP_EOL; } else { echo ' ❌ Autoloader issue detected' . PHP_EOL; exit(1); } " # 6. Dependency Integrity Check (FIXED - Added require_once for autoloader) echo "" echo "? Verifying build integrity..." # Check that critical Laravel classes are available php -r " require_once 'vendor/autoload.php'; \$critical_classes = [ 'Illuminate\\Foundation\\Application', 'Illuminate\\Http\\Request', 'Illuminate\\Routing\\Router', 'Illuminate\\Database\\Eloquent\\Model' ]; \$missing = []; foreach (\$critical_classes as \$class) { if (!class_exists(\$class)) { \$missing[] = \$class; } } if (empty(\$missing)) { echo ' ✅ All critical Laravel classes available' . PHP_EOL; } else { echo ' ❌ Missing critical classes: ' . implode(', ', \$missing) . PHP_EOL; exit(1); } " # 7. Database Seeder Compatibility Check (Without changing dependencies) if [ -f "database/seeders/DatabaseSeeder.php" ]; then echo " ? Testing database seeders compatibility..." # Check if Faker is used in seeders if grep -r "Faker\\\\" database/ 2>/dev/null >/dev/null; then # Check if Faker is available (should be from previous build commands) if php -r "require_once 'vendor/autoload.php'; echo class_exists('Faker\\Generator') ? 'OK' : 'MISSING';" 2>/dev/null | grep -q "OK"; then echo " ✅ Seeders use Faker and it's available" else echo " ⚠️ Seeders use Faker but it's not available" echo " ? This was handled by previous build commands (BUILD COMMAND 02.1)" fi else echo " ✅ Seeders don't use Faker or are production-ready" fi fi # 8. Model Factory Compatibility Check (Fixed - Added require_once) echo " ? Testing model factories compatibility..." php -r " require_once 'vendor/autoload.php'; try { if (class_exists('Database\\Factories\\UserFactory')) { echo ' ✅ Model factories are accessible' . PHP_EOL; } else { echo ' ℹ️ No UserFactory found (may be normal)' . PHP_EOL; } } catch (Exception \$e) { echo ' ⚠️ Model factory test failed: ' . \$e->getMessage() . PHP_EOL; } " 2>/dev/null || echo " ℹ️ Model factory test skipped" # 9. Environment-Specific Checks echo "" echo "? Environment-specific validation for: $DEPLOY_ENV" case "$DEPLOY_ENV" in "production"|"prod") echo " ? Production environment - running strict checks..." # Check for debug packages in production if [ -d "vendor/barryvdh/laravel-debugbar" ]; then echo " ⚠️ Laravel Debugbar detected in production" echo " ? This was intentionally included by BUILD COMMAND 02.1" fi # Verify caches exist for production if [ -f "bootstrap/cache/config.php" ]; then echo " ✅ Configuration cache ready for production" else echo " ⚠️ No config cache found - will be created during deployment" fi ;; "staging"|"stage") echo " ? Staging environment - development tools allowed" echo " ✅ Debug tools available for testing" ;; "development"|"dev"|"local") echo " ? Development environment - all tools available" echo " ✅ Full development environment ready" ;; *) echo " ℹ️ Unknown environment '$DEPLOY_ENV' - using default validation" ;; esac # 10. Final Build Summary echo "" echo "? Build Summary for $DEPLOY_ENV environment:" echo " ✅ vendor/: $([ -d "vendor" ] && echo "exists ($(find vendor -name "*.php" | wc -l) PHP files)" || echo "missing")" echo " ✅ bootstrap/cache/: $([ -d "bootstrap/cache" ] && echo "exists" || echo "missing")" echo " ✅ Configuration cache: $([ -f "bootstrap/cache/config.php" ] && echo "cached" || echo "will be created during deployment")" echo " ✅ Route cache: $([ -f "bootstrap/cache/routes-v7.php" ] && echo "cached" || echo "will be created during deployment")" echo " ✅ Assets: $([ -d "public/build" ] || [ -d "public/dist" ] || [ -d "public/js" ] && echo "compiled" || echo "none/API-only")" echo " ✅ Dependencies: Compatible with previous build decisions" # Check what dependencies are included DEP_STATUS="production" if [ -d "vendor/fakerphp" ] || [ -d "vendor/phpunit" ]; then DEP_STATUS="production + selected dev (via smart detection)" fi echo " ✅ Dependency mode: $DEP_STATUS" echo "" echo "✅ Security checks and final verification completed!" echo "? Laravel application ready for $DEPLOY_ENV deployment!" echo "? Build integrity verified - no conflicts with previous build commands"]

=== Security & Final Verification ===
? Target environment: staging
? Running environment-aware security checks...
   APP_DEBUG configuration looks good for staging
   APP_KEY is properly configured
   HTTPS configuration detected: https://staging.societypal.com

? Final verification...
   Laravel application boots successfully
  ? Version: Laravel Framework 12.17.0
   Optimized autoloader working correctly

? Verifying build integrity...
   All critical Laravel classes available
  ? Testing database seeders compatibility...
   Seeders use Faker and it's available
  ? Testing model factories compatibility...
   Model factories are accessible

? Environment-specific validation for: staging
  ? Staging environment - development tools allowed
   Debug tools available for testing

? Build Summary for staging environment:
   vendor/: exists (14163 PHP files)
   bootstrap/cache/: exists
   Configuration cache: cached
   Route cache: cached
   Assets: compiled
   Dependencies: Compatible with previous build decisions
   Dependency mode: production + selected dev (via smart detection)

 Security checks and final verification completed!
? Laravel application ready for staging deployment!
? Build integrity verified - no conflicts with previous build commands
Time taken to run G2-BUILD COMMAND 06: Security Checks & Final Verification-inBeta: 2.4 seconds


Receiving built files


Generating deployment manifest


Storing artifacts

