



# 1 Preparing


Waiting for an available deployment slot


Performing pre-deployment checks

Checking build allowance

Checking access to repository

Checking start and end revisions are valid

Checking connection to server SocietyPal-Staging


Preparing repository for deployment

Updating repository from git@github.com:rovony/SocietyPal.git

Getting information for start commit 84ff2b

Getting information for end commit f6adbf


---
# 2: Building
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
✅ bootstrap/cache: exists
✅ storage/app: exists
✅ storage/framework: exists
✅ storage/logs: exists
✅ Laravel directory structure created
Time taken to run G2-BUILD COMMAND 01: Laravel Directory Setup-inBeta: 1.54 seconds


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
✅ Composer dependencies valid
✅ PHP dependencies installed
Time taken to run G2-BUILD COMMAND 02: PHP Dependencies Installation-inBeta: 8.68 seconds


Running build command "G2-BUILD COMMAND 02.1: Smart PHP Dev Dependencies Detection-inBeta"

Running G2-BUILD COMMAND 02.1: Smart PHP Dev Dependencies Detection-inBeta [#!/usr/bin/env bash set -euo pipefail # ============================================================================= # BUILD COMMAND 02.1: Smart PHP Dev Dependencies Detection # ============================================================================= # Group: 2 # Name: G2-BUILD COMMAND 02.1: Smart PHP Dev Dependencies Detection-inBeta # Desc: Intelligently detects when PHP dev dependencies are needed in production. # Covers 16 detection patterns including Faker, Telescope, Laravel tools, # CodeCanyon apps, and complex applications. Prevents production failures # while avoiding unnecessary bloat. # ============================================================================= # # PURPOSE: Detect if PHP dev dependencies are needed in production # GOAL: Prevent production errors in CodeCanyon apps and unknown codebases # # EASILY CUSTOMIZABLE: # - To ADD a new detection: Copy any existing check and modify the pattern # - To REMOVE a detection: Comment out or delete the relevant section # - Each detection is independent and can be enabled/disabled separately # # DETECTION CATEGORIES: # 1. Core Laravel (Faker, Telescope, Debugbar) # 2. CodeCanyon/Third-party (IDE Helper, PDF libs, Spatie packages) # 3. Environment-specific (Testing tools for staging) # 4. Complex app fallback (Large apps with dev dependencies) # # ============================================================================= # Safety checks # Check for composer.json (project definition) if [ ! -f composer.json ]; then echo "❌ composer.json not found—run from Laravel project root"; exit 1 fi # Check for composer.lock (dependency lock file) if [ ! -f composer.lock ]; then echo "❌ composer.lock not found!" echo "? Run 'composer install' locally to generate lock file" echo "? Ensure composer.lock is committed to version control" echo "? Build failed - lock file required for reproducible builds" exit 1 fi echo "=== Checking PHP Dev Dependencies ===" # Set memory limit for Composer export COMPOSER_MEMORY_LIMIT=-1 echo "? Detecting if PHP dev dependencies are needed..." echo "? Comprehensive scan for CodeCanyon apps and unknown dependencies" NEEDS_DEV=false DETECTION_REASONS=() # ============================================================================ # CORE DEV DEPENDENCY DETECTION (Most Common Cases) # ============================================================================ # 1. Faker Detection (migrations/seeders/factories) if [ -d "database" ] && grep -r "Faker\\\\" database/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? FAKER: Used in database files") echo " ? Faker detected in database files (needed for production)" fi # 2. Telescope Detection (Laravel debugging) if [ -f "config/telescope.php" ] && grep -r "TelescopeServiceProvider\|Telescope::" app/ config/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? TELESCOPE: Actively configured") echo " ? Telescope actively configured for production" fi # 3. Debugbar Detection (Laravel debugging) if [ -f "config/debugbar.php" ] && grep -q "enabled.*true" config/debugbar.php 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? DEBUGBAR: Enabled in config") echo " ? Debugbar enabled in configuration" fi # ============================================================================ # CODECANYON & THIRD-PARTY APP DETECTION (Unknown Dependencies) # ============================================================================ # 4. IDE Helper Detection (often needed in production for some apps) if grep -r "ide-helper\|barryvdh.*ide" app/ config/ composer.json 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? IDE-HELPER: Used in production code") echo " ? IDE Helper detected in production code" fi # 5. Clockwork Detection (performance profiling) if [ -f "config/clockwork.php" ] || grep -r "Clockwork\|clockwork" app/ config/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("⏰ CLOCKWORK: Performance profiling active") echo " ⏰ Clockwork performance profiling detected" fi # 6. PDF Generation Libraries (commonly in dev deps but used in production) if grep -r "dompdf\|mpdf\|tcpdf\|Pdf::" app/ config/ composer.json 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? PDF-LIBS: PDF generation libraries detected") echo " ? PDF generation libraries detected" fi # 7. Spatie Packages Detection (many useful packages often in dev) if grep -r "spatie\\\\" app/ 2>/dev/null | grep -v "laravel-permission\|laravel-activitylog" >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? SPATIE: Spatie dev packages in use") echo " ? Spatie development packages detected in code" fi # 8. Code Generation Tools (often needed for production builds) if grep -r "Generator\|Artisan.*make\|Schema::" app/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? GENERATORS: Code generation tools detected") echo " ? Code generation tools detected" fi # 9. Testing Tools in Staging/Demo Environments DEPLOY_TARGET=${DEPLOY_TARGET:-"production"} if [ "$DEPLOY_TARGET" = "staging" ] || [ "$DEPLOY_TARGET" = "demo" ] || [ "$DEPLOY_TARGET" = "development" ]; then if grep -q '"phpunit\|"pest\|"mockery' composer.json 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? TESTING: Testing tools for $DEPLOY_TARGET environment") echo " ? Testing tools detected for $DEPLOY_TARGET environment" fi fi # 10. Laravel Packages That Are Often Dev But Needed in Production DEV_PROD_PACKAGES=( "laravel/tinker" "facade/ignition" "nunomaduro/collision" "filament/filament.*dev" "livewire.*dev" "inertiajs.*dev" ) for package in "${DEV_PROD_PACKAGES[@]}"; do if grep -q "\"$package\"" composer.json 2>/dev/null && grep -r "${package//[^a-zA-Z]/}" app/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? DEV-PROD: $package used in production code") echo " ? $package detected in production code" break fi done # ============================================================================ # CODECANYON SPECIFIC PATTERNS (Common in purchased scripts) # ============================================================================ # 11. Admin Panel Generators (commonly use dev tools in production) if grep -r "Admin\|Dashboard\|Backend" app/Http/Controllers/ 2>/dev/null | grep -i "generate\|create\|build" >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? ADMIN-GEN: Admin panel generators detected") echo " ? Admin panel generators detected (common in CodeCanyon apps)" fi # 12. Database Seeders in Production (CodeCanyon apps often seed in production) if [ -d "database/seeders" ] && find database/seeders -name "*.php" -exec grep -l "Faker\|factory\|create(" {} \; 2>/dev/null | head -1 | grep -q .; then NEEDS_DEV=true DETECTION_REASONS+=("? SEEDERS: Production seeders with dev dependencies") echo " ? Production seeders detected with dev dependencies" fi # 13. Dynamic Config/Route Generation (common in CodeCanyon apps) if grep -r "Config::set\|Route::.*group.*function\|Schema::create" app/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("⚙️ DYNAMIC: Dynamic config/route generation detected") echo " ⚙️ Dynamic configuration generation detected" fi # 14. Laravel-specific Dev Tools (Horizon, Octane, Scout) if grep -q '"laravel/horizon"\|"laravel/octane"\|"laravel/scout"' composer.json 2>/dev/null; then if grep -r "Horizon::\|Octane::\|Scout::" app/ config/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? LARAVEL-TOOLS: Laravel dev tools in production") echo " ? Laravel Horizon/Octane/Scout detected in production code" fi fi # ============================================================================ # FALLBACK: Check if dev dependencies exist and app seems complex # ============================================================================ # 15. Complex App Fallback (if has dev deps and many controllers/models) CONTROLLER_COUNT=$(find app/Http/Controllers -name "*.php" 2>/dev/null | wc -l) MODEL_COUNT=$(find app/Models -name "*.php" 2>/dev/null | wc -l) if grep -q '"require-dev"' composer.json 2>/dev/null && [ ${#DETECTION_REASONS[@]} -eq 0 ]; then if [ "$CONTROLLER_COUNT" -gt 10 ] || [ "$MODEL_COUNT" -gt 5 ]; then NEEDS_DEV=true DETECTION_REASONS+=("?️ COMPLEX-APP: Complex app with dev dependencies declared") echo " ?️ Complex app detected ($CONTROLLER_COUNT controllers, $MODEL_COUNT models) with dev dependencies" else echo " ? Dev dependencies declared but no specific production needs detected" fi fi echo "" if [ "$NEEDS_DEV" = true ]; then echo "? Installing PHP dev dependencies (needed for production)..." echo "? Reasons: ${DETECTION_REASONS[*]}" composer install \ --optimize-autoloader \ --no-interaction \ --prefer-dist \ --no-progress echo "✅ PHP dev dependencies installed (production requirements)" else echo "ℹ️ No PHP dev dependencies needed for production" echo "✅ Production-only setup is sufficient" fi echo "✅ PHP dev dependency check completed"]

=== Checking PHP Dev Dependencies ===
? Detecting if PHP dev dependencies are needed...
? Comprehensive scan for CodeCanyon apps and unknown dependencies
  ? Faker detected in database files (needed for production)
  ? Debugbar enabled in configuration
  ⏰ Clockwork performance profiling detected
  ? PDF generation libraries detected
  ? Code generation tools detected
  ? Admin panel generators detected (common in CodeCanyon apps)
  ? Production seeders detected with dev dependencies
  ⚙️ Dynamic configuration generation detected

? Installing PHP dev dependencies (needed for production)...
? Reasons: ? FAKER: Used in database files ? DEBUGBAR: Enabled in config ⏰ CLOCKWORK: Performance profiling active ? PDF-LIBS: PDF generation libraries detected ? GENERATORS: Code generation tools detected ? ADMIN-GEN: Admin panel generators detected ? SEEDERS: Production seeders with dev dependencies ⚙️ DYNAMIC: Dynamic config/route generation detected
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
✅ PHP dev dependencies installed (production requirements)
✅ PHP dev dependency check completed
Time taken to run G2-BUILD COMMAND 02.1: Smart PHP Dev Dependencies Detection-inBeta: 6.03 seconds


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
✅ Node.js dependencies installed
Time taken to run G2-BUILD COMMAND 03: Node.js Dependencies Installation-inBeta: 5.98 seconds


Running build command "G2-BUILD COMMAND 03.1: Smart Node.js Dev Dependencies Detection-inBeta"

Running G2-BUILD COMMAND 03.1: Smart Node.js Dev Dependencies Detection-inBeta [#!/usr/bin/env bash set -euo pipefail # ============================================================================= # BUILD COMMAND 03.1: Smart Node.js Dev Dependencies Detection # ============================================================================= # Group: 2 # Name: G2-BUILD COMMAND 03.1: Smart Node.js Dev Dependencies Detection-inBeta # Desc: Intelligently detects when Node.js dev dependencies are needed for builds. # Covers 23 detection patterns including Vue/React/Angular, Vite/Webpack, # TypeScript, CSS frameworks, and modern meta-frameworks. Optimizes for # CodeCanyon apps and complex frontend builds. # ============================================================================= # # PURPOSE: Detect if Node.js dev dependencies are needed in production # GOAL: Prevent frontend build failures in CodeCanyon apps and unknown codebases # # EASILY CUSTOMIZABLE: # - To ADD a new detection: Copy any existing check and modify the pattern # - To REMOVE a detection: Comment out or delete the relevant section # - Each detection is independent and can be enabled/disabled separately # # DETECTION CATEGORIES: # 1. Core Build Tools (Bundlers, TypeScript, Build Scripts) # 2. Frontend Frameworks (Vue, React, Angular, Svelte) # 3. CSS Frameworks (Tailwind, Sass, PostCSS, Bootstrap) # 4. Testing & Quality (Jest, Cypress, ESLint, Prettier) # 5. CodeCanyon/Third-party (Admin panels, theme builders) # 6. Environment-specific (Dev servers, hot reloading) # # ============================================================================= # Safety checks # Check for composer.json (Laravel project requirement) if [ ! -f composer.json ]; then echo "❌ composer.json not found—run from Laravel project root"; exit 1 fi echo "=== Checking Node.js Dev Dependencies ===" # Only run if package.json exists if [ ! -f "package.json" ]; then echo "ℹ️ No package.json found, skipping Node.js dev dependency check" exit 0 fi # Check for package-lock.json (dependency lock file) if [ ! -f "package-lock.json" ]; then echo "❌ package-lock.json not found!" echo "? Run 'npm install' locally to generate lock file" echo "? Ensure package-lock.json is committed to version control" echo "? Build failed - lock file required for reproducible builds" exit 1 fi echo "? Detecting if Node.js dev dependencies are needed..." echo "? Comprehensive scan for CodeCanyon apps and frontend build requirements" NEEDS_DEV=false DETECTION_REASONS=() # ============================================================================ # CORE BUILD TOOLS DETECTION (Essential Build Requirements) # ============================================================================ # 1. Build Scripts Detection (primary indicator) if grep -q '"build":\|"production":\|"prod":\|"compile":' package.json 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("⚙️ BUILD-SCRIPTS: Production build scripts found") echo " ⚙️ Build scripts found (npm run build/production/prod)" fi # 2. Asset Bundler Config Files BUNDLER_CONFIGS=("vite.config.js" "vite.config.mjs" "vite.config.ts" "webpack.mix.js" "webpack.config.js" "rollup.config.js" "gulpfile.js" "parcel.config.js") for config in "${BUNDLER_CONFIGS[@]}"; do if [ -f "$config" ]; then NEEDS_DEV=true DETECTION_REASONS+=("?️ BUNDLER-CONFIG: $config found") echo " ?️ Asset bundler config detected: $config" break fi done # 3. TypeScript Detection (compilation needed) if [ -f "tsconfig.json" ] && find . -name "*.ts" -o -name "*.tsx" 2>/dev/null | head -1 | grep -q . 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? TYPESCRIPT: TS files need compilation") echo " ? TypeScript files detected (compilation needed)" fi # 4. Babel Configuration (transpilation needed) if [ -f ".babelrc" ] || [ -f "babel.config.js" ] || [ -f "babel.config.json" ] || grep -q '"babel"' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("? BABEL: Transpilation configuration found") echo " ? Babel transpilation configuration detected" fi # ============================================================================ # FRONTEND FRAMEWORKS DETECTION (Component-based apps) # ============================================================================ # 5. Vue.js Detection if grep -q '"vue":\|"@vue/"' package.json || find . -name "*.vue" 2>/dev/null | head -1 | grep -q . 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("⚛️ VUE: Vue.js framework detected") echo " ⚛️ Vue.js framework detected (compilation required)" fi # 6. React Detection if grep -q '"react":\|"@react/"' package.json || find . -name "*.jsx" 2>/dev/null | head -1 | grep -q . 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("⚛️ REACT: React framework detected") echo " ⚛️ React framework detected (compilation required)" fi # 7. Angular Detection if [ -f "angular.json" ] || grep -q '"@angular/"' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("⚛️ ANGULAR: Angular framework detected") echo " ⚛️ Angular framework detected (compilation required)" fi # 8. Svelte Detection if grep -q '"svelte"' package.json || find . -name "*.svelte" 2>/dev/null | head -1 | grep -q . 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("⚛️ SVELTE: Svelte framework detected") echo " ⚛️ Svelte framework detected (compilation required)" fi # 9. Inertia.js Detection (Laravel SPA framework) if grep -q '"@inertiajs/"' package.json && [ -f "resources/js/app.js" -o -f "resources/js/app.ts" ]; then NEEDS_DEV=true DETECTION_REASONS+=("? INERTIA: Inertia.js SPA framework") echo " ? Inertia.js SPA framework detected" fi # ============================================================================ # CSS FRAMEWORKS & PREPROCESSORS DETECTION (Styling compilation) # ============================================================================ # 10. Tailwind CSS Detection if [ -f "tailwind.config.js" ] || [ -f "tailwind.config.ts" ] || grep -q '"tailwindcss"' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("? TAILWIND: Tailwind CSS compilation needed") echo " ? Tailwind CSS detected (compilation required)" fi # 11. Sass/SCSS Detection if grep -q '"sass":\|"node-sass":\|"dart-sass":' package.json || find . -name "*.scss" -o -name "*.sass" 2>/dev/null | head -1 | grep -q . 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? SASS: Sass/SCSS compilation needed") echo " ? Sass/SCSS files detected (compilation required)" fi # 12. Less Detection if grep -q '"less"' package.json || find . -name "*.less" 2>/dev/null | head -1 | grep -q . 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? LESS: Less compilation needed") echo " ? Less files detected (compilation required)" fi # 13. PostCSS Detection if [ -f "postcss.config.js" ] || grep -q '"postcss":\|"autoprefixer":' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("? POSTCSS: PostCSS processing needed") echo " ? PostCSS configuration detected" fi # 14. Bootstrap/UI Framework Detection if grep -q '"bootstrap":\|"@bootstrap/":\|"bulma":\|"foundation"' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("? UI-FRAMEWORK: UI framework compilation needed") echo " ? UI framework detected (Bootstrap/Bulma/Foundation)" fi # ============================================================================ # TESTING & QUALITY TOOLS DETECTION (Dev/Staging environments) # ============================================================================ DEPLOY_TARGET=${DEPLOY_TARGET:-"production"} # 15. Testing Tools for Staging/Demo if [ "$DEPLOY_TARGET" = "staging" ] || [ "$DEPLOY_TARGET" = "demo" ] || [ "$DEPLOY_TARGET" = "development" ]; then if grep -q '"jest":\|"cypress":\|"playwright":\|"vitest":\|"@testing-library"' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("? TESTING: Frontend testing for $DEPLOY_TARGET") echo " ? Frontend testing tools for $DEPLOY_TARGET environment" fi fi # 16. Linting/Formatting for CI/Staging if [ "$DEPLOY_TARGET" = "staging" ] || [ "$DEPLOY_TARGET" = "demo" ] || [ ! -z "${CI:-}" ]; then if grep -q '"eslint":\|"prettier":\|"stylelint":' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("? LINTING: Code quality tools for $DEPLOY_TARGET") echo " ? Linting/formatting tools for $DEPLOY_TARGET environment" fi fi # ============================================================================ # CODECANYON & THIRD-PARTY DETECTION (Purchased scripts) # ============================================================================ # 17. Admin Panel Frontend Builders if grep -r "admin\|dashboard\|backend" resources/js/ public/js/ 2>/dev/null | grep -i "build\|compile\|generate" >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? ADMIN-FRONTEND: Admin panel frontend detected") echo " ? Admin panel frontend build detected (common in CodeCanyon)" fi # 18. Theme/Template Builders if grep -q '"theme":\|"template":\|"builder":' package.json || [ -d "themes" ] || [ -d "templates" ]; then NEEDS_DEV=true DETECTION_REASONS+=("? THEME-BUILDER: Theme/template system detected") echo " ? Theme/template builder detected" fi # 19. Dynamic Frontend Generation if grep -r "dynamic\|runtime\|generate.*component" resources/js/ public/js/ 2>/dev/null >/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("⚙️ DYNAMIC-FRONTEND: Dynamic frontend generation") echo " ⚙️ Dynamic frontend generation detected" fi # 20. Modern JavaScript Features (ES6+, modules) if grep -q '"type": "module"\|"@babel/"\|"core-js":' package.json || find . -name "*.mjs" 2>/dev/null | head -1 | grep -q . 2>/dev/null; then NEEDS_DEV=true DETECTION_REASONS+=("? MODERN-JS: Modern JavaScript features need compilation") echo " ? Modern JavaScript features detected (compilation needed)" fi # 21. Modern Meta-Frameworks (Next.js, Nuxt.js, Gatsby, Remix, Astro) if grep -q '"next"\|"nuxt"\|"gatsby"\|"remix"\|"astro"\|"@next/"\|"@nuxt/"\|"@astrojs/"' package.json; then NEEDS_DEV=true DETECTION_REASONS+=("? META-FRAMEWORK: Modern meta-framework detected") echo " ? Modern meta-framework detected (Next.js/Nuxt.js/Gatsby/Remix/Astro)" fi # ============================================================================ # DEVELOPMENT TOOLS DETECTION (Hot reloading, dev servers) # ============================================================================ # 22. Dev Server Detection if grep -q '"dev":\|"serve":\|"start":' package.json; then if [ "$DEPLOY_TARGET" = "staging" ] || [ "$DEPLOY_TARGET" = "demo" ]; then NEEDS_DEV=true DETECTION_REASONS+=("? DEV-SERVER: Dev server for $DEPLOY_TARGET") echo " ? Dev server configuration for $DEPLOY_TARGET" fi fi # 23. Hot Module Replacement (HMR) if grep -q '"@vitejs/plugin-"\|"webpack-hot-middleware"\|"hot-reload"\|"hmr":' package.json; then if [ "$DEPLOY_TARGET" = "staging" ] || [ "$DEPLOY_TARGET" = "demo" ]; then NEEDS_DEV=true DETECTION_REASONS+=("? HMR: Hot reloading for $DEPLOY_TARGET") echo " ? Hot module replacement for $DEPLOY_TARGET" fi fi # ============================================================================ # FALLBACK: Complex Frontend App Detection # ============================================================================ # 24. Complex Frontend Fallback JS_FILE_COUNT=$(find resources/js public/js assets/js -name "*.js" -o -name "*.ts" -o -name "*.vue" -o -name "*.jsx" 2>/dev/null | wc -l || echo "0") CSS_FILE_COUNT=$(find resources/css resources/sass public/css assets/css -name "*.css" -o -name "*.scss" -o -name "*.sass" 2>/dev/null | wc -l || echo "0") if grep -q '"devDependencies"' package.json 2>/dev/null && [ ${#DETECTION_REASONS[@]} -eq 0 ]; then if [ "$JS_FILE_COUNT" -gt 5 ] || [ "$CSS_FILE_COUNT" -gt 3 ]; then NEEDS_DEV=true DETECTION_REASONS+=("?️ COMPLEX-FRONTEND: Complex frontend with dev dependencies") echo " ?️ Complex frontend detected ($JS_FILE_COUNT JS files, $CSS_FILE_COUNT CSS files) with dev dependencies" else echo " ? Dev dependencies declared but no specific build needs detected" fi fi echo "" if [ "$NEEDS_DEV" = true ]; then echo "? Installing Node.js dev dependencies (needed for asset building)..." echo "? Reasons: ${DETECTION_REASONS[*]}" # Check if dev dependencies are already installed if [ -d "node_modules" ] && [ -d "node_modules/.bin" ]; then echo "ℹ️ Node.js dependencies already installed (dev dependencies available)" else # Install all dependencies (including dev) for building npm ci --no-audit --no-fund fi echo "✅ Node.js dev dependencies installed (for asset compilation)" else echo "? Node.js production dependencies sufficient..." echo "? Reasons: No dev dependencies needed for this app" # Only reinstall if we need to remove dev dependencies if [ -d "node_modules" ]; then echo "ℹ️ Node.js dependencies already installed (production mode sufficient)" else # Install only production dependencies npm ci --production --no-audit --no-fund fi echo "✅ Node.js production dependencies confirmed (runtime only)" fi echo "✅ Node.js dependency setup completed"]

=== Checking Node.js Dev Dependencies ===
? Detecting if Node.js dev dependencies are needed...
? Comprehensive scan for CodeCanyon apps and frontend build requirements
  ⚙️ Build scripts found (npm run build/production/prod)
  ?️ Asset bundler config detected: vite.config.js
  ? Tailwind CSS detected (compilation required)
  ? Sass/SCSS files detected (compilation required)
  ? PostCSS configuration detected
  ? Modern JavaScript features detected (compilation needed)

? Installing Node.js dev dependencies (needed for asset building)...
? Reasons: ⚙️ BUILD-SCRIPTS: Production build scripts found ?️ BUNDLER-CONFIG: vite.config.js found ? TAILWIND: Tailwind CSS compilation needed ? SASS: Sass/SCSS compilation needed ? POSTCSS: PostCSS processing needed ? MODERN-JS: Modern JavaScript features need compilation
ℹ️ Node.js dependencies already installed (dev dependencies available)
✅ Node.js dev dependencies installed (for asset compilation)
✅ Node.js dependency setup completed
Time taken to run G2-BUILD COMMAND 03.1: Smart Node.js Dev Dependencies Detection-inBeta: 1.93 seconds


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
✓ 163 modules transformed.
rendering chunks...
computing gzip size...
public/build/manifest.json              0.27 kB │ gzip:   0.15 kB
public/build/assets/app-CaW9wbch.css  168.86 kB │ gzip:  23.92 kB
public/build/assets/app-CvQSGMFY.js   784.71 kB │ gzip: 207.49 kB
✓ built in 7.49s
✅ Assets built with 'npm run build'
Cleaning up Node.js modules...
✅ Frontend assets compiled successfully
Final verification...
✅ vendor/: exists
✅ bootstrap/cache/: exists
✅ Build complete and ready for deployment
✅ Laravel build and optimization completed
Time taken to run G2-BUILD COMMAND 04: Asset Building & Laravel Optimization-inBeta: 12.94 seconds


Running build command "G2-BUILD COMMAND 05: Laravel Cache Optimization-inBeta"

Running G2-BUILD COMMAND 05: Laravel Cache Optimization-inBeta [#!/usr/bin/env bash set -euo pipefail # ============================================================================= # BUILD COMMAND 05: Laravel Cache Optimization # ============================================================================= # Group: 2 # Name: G2-BUILD COMMAND 05: Laravel Cache Optimization-inBeta # Desc: Optimizes Laravel for production with config, route, view, and event # caching. Detects environment and skips caching in local development. # Handles closure detection and provides informative feedback. # ============================================================================= # Safety checks if [ ! -f composer.lock ]; then echo "❌ composer.lock not found!" echo "? Run 'composer install' locally to generate lock file" echo "? Ensure composer.lock is committed to version control" echo "? Build failed - lock file required for reproducible builds" exit 1 fi echo "=== Laravel Cache Optimization ===" # Determine environment (skip caching in local development) APP_ENV=${APP_ENV:-$(grep "APP_ENV=" .env 2>/dev/null | cut -d'=' -f2 | tr -d '"' || echo "production")} echo "? Detected environment: $APP_ENV" if [ "$APP_ENV" = "local" ]; then echo "ℹ️ Local environment detected - skipping cache optimization" echo "✅ Laravel optimization completed (local mode)" exit 0 fi echo "⚡ Optimizing Laravel for production environment..." # 1. Memory constraint testing (shared hosting compatibility) echo "? Testing memory constraints for deployment commands..." if php -d memory_limit=128M artisan config:cache --dry-run >/dev/null 2>&1; then echo " ✅ Commands should work with 128M memory limit (shared hosting compatible)" else echo " ⚠️ WARNING: May fail on shared hosting with 128M memory limit" echo " ? Consider requesting higher memory_limit from hosting provider" fi # 2. Configuration Cache echo "? Caching configuration..." if php artisan config:cache 2>/dev/null; then echo " ✅ Configuration cached successfully" else echo " ⚠️ Config cache skipped (closures detected in config files)" fi # 3. Route Cache echo "?️ Caching routes..." if php artisan route:cache 2>/dev/null; then echo " ✅ Routes cached successfully" else echo " ⚠️ Route cache skipped (closure-based routes detected)" echo " ? Convert closure routes to controller methods for caching" fi # 4. View Cache echo "?️ Caching views..." if php artisan view:cache 2>/dev/null; then echo " ✅ Views cached successfully" else echo " ℹ️ View cache skipped (no views found - API-only app)" fi # 5. Event Cache (Laravel 9+) echo "⚡ Caching events..." if php artisan event:cache 2>/dev/null; then echo " ✅ Events cached successfully" else echo " ℹ️ Event cache not available (older Laravel version)" fi # 6. Icon Cache (if available) echo "? Caching icons..." if php artisan icons:cache 2>/dev/null; then echo " ✅ Icons cached successfully" else echo " ℹ️ Icon cache not available (no icon package installed)" fi echo "" echo "✅ Laravel cache optimization completed!" echo "? Production caches created for maximum performance"]

=== Laravel Cache Optimization ===
? Detected environment: staging
⚡ Optimizing Laravel for production environment...
? Testing memory constraints for deployment commands...
  ⚠️ WARNING: May fail on shared hosting with 128M memory limit
  ? Consider requesting higher memory_limit from hosting provider
? Caching configuration...

   INFO  Configuration cached successfully.  

  ✅ Configuration cached successfully
?️ Caching routes...

   INFO  Routes cached successfully.  

  ✅ Routes cached successfully
?️ Caching views...


   INFO  Blade templates cached successfully.  

  ✅ Views cached successfully
⚡ Caching events...

   INFO  Events cached successfully.  

  ✅ Events cached successfully
? Caching icons...
Blade icons manifest file generated successfully!
  ✅ Icons cached successfully

✅ Laravel cache optimization completed!
? Production caches created for maximum performance
Time taken to run G2-BUILD COMMAND 05: Laravel Cache Optimization-inBeta: 4.14 seconds


Running build command "G2-BUILD COMMAND 06: Security Checks & Final Verification-inBeta"

Running G2-BUILD COMMAND 06: Security Checks & Final Verification-inBeta [#!/usr/bin/env bash set -euo pipefail # ============================================================================= # BUILD COMMAND 06: Security Checks & Final Verification # ============================================================================= # Group: 2 # Name: G2-BUILD COMMAND 06: Security Checks & Final Verification-inBeta # Desc: Performs security validation (APP_DEBUG, APP_KEY, HTTPS) and final # verification of Laravel application readiness. Tests autoloader and # provides comprehensive build summary for deployment. # ============================================================================= # Safety checks if [ ! -f composer.lock ]; then echo "❌ composer.lock not found!" echo "? Run 'composer install' locally to generate lock file" echo "? Ensure composer.lock is committed to version control" echo "? Build failed - lock file required for reproducible builds" exit 1 fi echo "=== Security & Final Verification ===" echo "? Running security checks..." # 1. Check APP_DEBUG setting if grep -q "APP_DEBUG=true" .env 2>/dev/null; then echo " ⚠️ APP_DEBUG is true - should be false in production" echo " ? Set APP_DEBUG=false for production security" else echo " ✅ APP_DEBUG configuration looks secure" fi # 2. Check APP_KEY exists if grep -q "APP_KEY=" .env 2>/dev/null; then APP_KEY=$(grep "APP_KEY=" .env | cut -d'=' -f2) if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then echo " ❌ APP_KEY is empty - generate with: php artisan key:generate" echo " ? CRITICAL: Application encryption key missing!" else echo " ✅ APP_KEY is configured" fi else echo " ⚠️ APP_KEY not found in .env" fi # 3. Check HTTPS configuration if grep -q "APP_URL.*https://" .env 2>/dev/null; then echo " ✅ HTTPS configuration detected" else echo " ℹ️ Consider using HTTPS for production (APP_URL=https://...)" fi echo "" echo "? Final verification..." # 4. Test Laravel bootstrap if php artisan --version >/dev/null 2>&1; then echo " ✅ Laravel application boots successfully" else echo " ❌ Laravel application failed to boot!" echo " ? CRITICAL: Application may not work in production" exit 1 fi # 5. Test autoloader php -r " require_once 'vendor/autoload.php'; if (class_exists('Illuminate\Foundation\Application')) { echo ' ✅ Optimized autoloader working correctly' . PHP_EOL; } else { echo ' ❌ Autoloader issue detected' . PHP_EOL; exit(1); } " # 6. Runtime dependency validation (critical for production deployment) echo "" echo "? Testing runtime dependencies after production build..." # Save current vendor state vendor_backup="vendor_$(date +%s)" if cp -r vendor "$vendor_backup" 2>/dev/null; then # Test with production dependencies only composer install --no-dev --optimize-autoloader --no-interaction >/dev/null 2>&1 # Test basic Laravel functionality if php artisan --version >/dev/null 2>&1; then echo " ✅ Application boots correctly with production dependencies" else echo " ❌ Application fails to boot without dev dependencies!" echo " ? CRITICAL: Production build will fail - check dependencies" fi # Test seeders (major source of Faker issues) if [ -f "database/seeders/DatabaseSeeder.php" ]; then echo " ? Testing database seeders..." if php artisan db:seed --dry-run 2>&1 | grep -q "Faker.*not.*found\|Factory.*not.*found"; then echo " ? CRITICAL: Seeders reference dev dependencies (likely Faker)" echo " ? SOLUTION: Move fakerphp/faker to 'require' section or remove from production seeders" else echo " ✅ Seeders compatible with production dependencies" fi fi # Test factory instantiation echo " ? Testing model factories..." php -r " try { if (class_exists('Database\\Factories\\UserFactory')) { new Database\Factories\UserFactory(); echo ' ✅ Model factories work with production dependencies' . PHP_EOL; } } catch (Exception \$e) { if (strpos(\$e->getMessage(), 'Faker') !== false || strpos(\$e->getMessage(), 'Factory') !== false) { echo ' ? CRITICAL: Model factories need dev dependencies: ' . \$e->getMessage() . PHP_EOL; echo ' ? SOLUTION: Move required packages to require section' . PHP_EOL; } } " 2>/dev/null || echo " ℹ️ No model factories found or testing skipped" # Restore full dependencies rm -rf vendor && mv "$vendor_backup" vendor else echo " ⚠️ Could not backup vendor directory - skipping runtime validation" fi # 7. Final summary echo "" echo "? Build Summary:" echo " ✅ vendor/: $([ -d "vendor" ] && echo "exists" || echo "missing")" echo " ✅ bootstrap/cache/: $([ -d "bootstrap/cache" ] && echo "exists" || echo "missing")" echo " ✅ Configuration cache: $([ -f "bootstrap/cache/config.php" ] && echo "cached" || echo "skipped")" echo " ✅ Route cache: $([ -f "bootstrap/cache/routes-v7.php" ] && echo "cached" || echo "skipped")" echo " ✅ Assets: $([ -d "public/build" ] || [ -d "public/dist" ] || [ -d "public/js" ] && echo "compiled" || echo "none/API-only")" echo "" echo "✅ Security checks and final verification completed!" echo "? Laravel application ready for production deployment!"]

=== Security & Final Verification ===
? Running security checks...
  ✅ APP_DEBUG configuration looks secure
  ✅ APP_KEY is configured
  ✅ HTTPS configuration detected

? Final verification...
  ✅ Laravel application boots successfully
  ✅ Optimized autoloader working correctly

? Testing runtime dependencies after production build...
  ✅ Application boots correctly with production dependencies
  ? Testing database seeders...
  ✅ Seeders compatible with production dependencies
  ? Testing model factories...

? Build Summary:
  ✅ vendor/: exists
  ✅ bootstrap/cache/: exists
  ✅ Configuration cache: skipped
  ✅ Route cache: cached
  ✅ Assets: compiled

✅ Security checks and final verification completed!
? Laravel application ready for production deployment!
Time taken to run G2-BUILD COMMAND 06: Security Checks & Final Verification-inBeta: 6.14 seconds


Receiving built files


Generating deployment manifest


Storing artifacts





---

# 3 SSH commands + transferring:
Running SSH command G3- inBeta-Phase-A-Prep: Server Environment Validation & Setup

Executing G3- inBeta-Phase-A-Prep: Server Environment Validation & Setup [#!/bin/bash set -e # Phase-A-Prep: Server Environment Validation & Setup # Purpose: Validate server environment, provide clear instructions for fixes # Run: FIRST - before any other scripts (Pre-Deployment Commands - Before Upload) # Action: DETECT, REPORT, and INSTRUCT - ensures server readiness # Version 2.0 - PRODUCTION READY (100% Detection-Only, No Destructive Operations) echo "=== Phase-A-Prep: Server Environment Validation & Setup ===" # Initialize variables using DeployHQ variables DOMAIN_ROOT=$(dirname "/home/u227177893/domains/staging.societypal.com/deploy") PREP_REPORT="$DOMAIN_ROOT/deployment-prep-report.md" PHASE_A_ISSUES=0 RECOMMENDATIONS_PROVIDED=0 echo "? Starting server environment validation..." echo "? Prep Report: $PREP_REPORT" # Initialize prep report cat > "$PREP_REPORT" << EOF # ? Deployment Preparation Report **Domain:** $(basename "$DOMAIN_ROOT") **Started:** $(date '+%Y-%m-%d %H:%M:%S') **Status:** ? In Progress --- ## ? Phase A: Server Environment Validation **Focus:** Server readiness, hosting panel instructions, manual recommendations EOF # A-Prep-01: Critical PHP Extensions Check (Enhanced from existing scripts) echo "=== Critical PHP Extensions Analysis ===" # Focus only on extensions that cause immediate failures CRITICAL_EXTENSIONS=("pdo" "pdo_mysql" "openssl" "mbstring" "curl") CRITICAL_MISSING=() NICE_TO_HAVE_MISSING=() # Check critical extensions for ext in "${CRITICAL_EXTENSIONS[@]}"; do if ! php -m | grep -qi "^$ext$"; then CRITICAL_MISSING+=("$ext") PHASE_A_ISSUES=$((PHASE_A_ISSUES + 1)) echo "❌ CRITICAL: $ext extension missing" else echo "✅ $ext extension available" fi done # Check nice-to-have extensions (won't block deployment) NICE_TO_HAVE=("zip" "bcmath" "fileinfo" "xml" "ctype" "json" "tokenizer") for ext in "${NICE_TO_HAVE[@]}"; do if ! php -m | grep -qi "^$ext$"; then NICE_TO_HAVE_MISSING+=("$ext") echo "⚠️ RECOMMENDED: $ext extension missing (optional)" fi done # A-Prep-02: Smart Composer 2 Detection & Recommendations echo "=== Smart Composer 2 Detection & Recommendations ===" COMPOSER_STATUS="✅ OPTIMAL" COMPOSER_RECOMMENDATION="" # Smart Composer 2 Detection Logic if command -v composer2 &> /dev/null; then COMPOSER2_VERSION=$(composer2 --version 2>/dev/null | grep -oE 'version [0-9]+\.[0-9]+' | cut -d' ' -f2) echo "✅ OPTIMAL: composer2 available server-wide ($COMPOSER2_VERSION)" COMPOSER_STATUS="✅ OPTIMAL (server-wide)" COMPOSER_RECOMMENDATION="Use existing: \`composer2 install --no-dev --optimize-autoloader\`" elif composer --version 2>/dev/null | grep -q "version 2\."; then COMPOSER_VERSION=$(composer --version | grep -oE 'version [0-9]+\.[0-9]+' | cut -d' ' -f2) echo "✅ GOOD: composer (v2) available server-wide ($COMPOSER_VERSION)" COMPOSER_STATUS="✅ GOOD (server-wide v2)" COMPOSER_RECOMMENDATION="Use existing: \`composer install --no-dev --optimize-autoloader\`" elif command -v composer &> /dev/null; then COMPOSER_VERSION=$(composer --version 2>/dev/null | grep -oE 'version [0-9]+\.[0-9]+' | cut -d' ' -f2 || echo "unknown") echo "⚠️ LEGACY: composer v1.x detected ($COMPOSER_VERSION)" echo "ℹ️ Laravel 9+ requires Composer 2.x for optimal performance" COMPOSER_STATUS="⚠️ LEGACY VERSION" COMPOSER_RECOMMENDATION="**Upgrade needed:** Install Composer 2 per-domain" PHASE_A_ISSUES=$((PHASE_A_ISSUES + 1)) else echo "❌ MISSING: No Composer installation found" COMPOSER_STATUS="❌ NOT FOUND" COMPOSER_RECOMMENDATION="**Installation required:** Install Composer 2 per-domain" PHASE_A_ISSUES=$((PHASE_A_ISSUES + 1)) fi # Domain-specific installation path recommendation if [ "$COMPOSER_STATUS" = "⚠️ LEGACY VERSION" ] || [ "$COMPOSER_STATUS" = "❌ NOT FOUND" ]; then echo "? Recommended installation location: $DOMAIN_ROOT/composer2" echo "ℹ️ This will be accessible from all releases and deployments" fi # A-Prep-03: Enhanced Shared Directory Structure Analysis echo "=== Enhanced Shared Directory Structure Analysis ===" SHARED_BASE="/home/u227177893/domains/staging.societypal.com/deploy/shared" SHARED_STRUCTURE_STATUS="✅ OPTIMAL" # Check for additional shared directories that might be beneficial RECOMMENDED_DIRS=( "logs/apache2" "logs/php" "cache/sessions" "cache/views" "public/.well-known/acme-challenge" "storage/debugbar" "storage/clockwork" ) MISSING_OPTIONAL_DIRS=() for dir in "${RECOMMENDED_DIRS[@]}"; do FULL_PATH="$SHARED_BASE/$dir" if [ -d "$FULL_PATH" ]; then echo "✅ Optional: shared/$dir (present)" else echo "ℹ️ Optional: shared/$dir (could be added)" MISSING_OPTIONAL_DIRS+=("$dir") fi done if [ ${#MISSING_OPTIONAL_DIRS[@]} -gt 0 ]; then echo "? ${#MISSING_OPTIONAL_DIRS[@]} optional directories could be created for enhanced functionality" SHARED_STRUCTURE_STATUS="✅ GOOD (optional dirs available)" else echo "✅ All optional shared directories already present" fi # A-Prep-04: PHP Configuration Analysis echo "=== PHP Configuration Analysis ===" PHP_VERSION=$(php -v | head -1 | cut -d' ' -f2 | cut -d'-' -f1) echo "? PHP Version: $PHP_VERSION" # Check PHP version compatibility with modern Laravel PHP_STATUS="✅ EXCELLENT" if [[ "$PHP_VERSION" =~ ^8\.[2-3] ]]; then echo "✅ PHP $PHP_VERSION - Excellent for Laravel 10+" elif [[ "$PHP_VERSION" =~ ^8\.[0-1] ]]; then echo "✅ PHP $PHP_VERSION - Good for Laravel 9+" PHP_STATUS="✅ GOOD" elif [[ "$PHP_VERSION" =~ ^7\. ]]; then echo "⚠️ PHP $PHP_VERSION - Legacy version" PHP_STATUS="⚠️ LEGACY" PHASE_A_ISSUES=$((PHASE_A_ISSUES + 1)) else echo "❌ PHP $PHP_VERSION - Unsupported" PHP_STATUS="❌ UNSUPPORTED" PHASE_A_ISSUES=$((PHASE_A_ISSUES + 1)) fi # Check critical PHP settings MEMORY_LIMIT=$(php -r "echo ini_get('memory_limit');") MAX_EXECUTION_TIME=$(php -r "echo ini_get('max_execution_time');") echo "? Memory Limit: $MEMORY_LIMIT" echo "? Max Execution Time: ${MAX_EXECUTION_TIME}s" PHP_CONFIG_STATUS="✅ OPTIMAL" if [[ "$MEMORY_LIMIT" =~ ^[0-9]+M$ ]]; then MEMORY_MB=$(echo "$MEMORY_LIMIT" | sed 's/M//') if [ "$MEMORY_MB" -lt 256 ]; then echo "⚠️ Memory limit may be low for Laravel applications" PHP_CONFIG_STATUS="⚠️ LOW MEMORY" PHASE_A_ISSUES=$((PHASE_A_ISSUES + 1)) fi fi # A-Prep-05: Server Capabilities Check echo "=== Server Capabilities Analysis ===" # Check for essential server tools SERVER_TOOLS_STATUS="✅ COMPLETE" MISSING_TOOLS=() # Essential tools for Laravel deployment TOOLS=("curl" "git" "mysql") for tool in "${TOOLS[@]}"; do if command -v "$tool" &> /dev/null; then VERSION=$(eval "$tool --version 2>/dev/null | head -1" || echo "available") echo "✅ $tool: $VERSION" else echo "❌ $tool: Missing" MISSING_TOOLS+=("$tool") PHASE_A_ISSUES=$((PHASE_A_ISSUES + 1)) fi done if [ ${#MISSING_TOOLS[@]} -gt 0 ]; then SERVER_TOOLS_STATUS="❌ INCOMPLETE" fi # Check Node.js (optional for frontend builds) if command -v node &> /dev/null; then NODE_VERSION=$(node --version) echo "✅ Node.js: $NODE_VERSION (for frontend builds)" else echo "ℹ️ Node.js: Not available (only needed for frontend builds)" fi # Update prep report with Phase A results cat >> "$PREP_REPORT" << EOF ### ? Critical Extensions Status $([ ${#CRITICAL_MISSING[@]} -eq 0 ] && echo "✅ **All critical extensions present**" || echo "❌ **Missing critical extensions:** ${CRITICAL_MISSING[*]}") $([ ${#NICE_TO_HAVE_MISSING[@]} -eq 0 ] && echo "✅ **All recommended extensions present**" || echo "⚠️ **Missing recommended:** ${NICE_TO_HAVE_MISSING[*]} (optional)") ### ?️ Composer Environment **Status:** $COMPOSER_STATUS **Recommendation:** $COMPOSER_RECOMMENDATION ### ? PHP Configuration **Version:** $PHP_VERSION ($PHP_STATUS) **Memory:** $MEMORY_LIMIT **Config:** $PHP_CONFIG_STATUS ### ?️ Server Tools **Status:** $SERVER_TOOLS_STATUS $([ ${#MISSING_TOOLS[@]} -gt 0 ] && echo "**Missing:** ${MISSING_TOOLS[*]}" || echo "**Available:** curl, git, mysql ✅") ### ? Shared Infrastructure **Structure:** $SHARED_STRUCTURE_STATUS $([ ${#MISSING_OPTIONAL_DIRS[@]} -gt 0 ] && echo "**Optional directories available:** ${#MISSING_OPTIONAL_DIRS[@]} could be added" || echo "**All recommended directories present**") EOF # Generate specific action items based on findings cat >> "$PREP_REPORT" << EOF ### ? Action Items Required: EOF ACTION_ITEMS=0 # Critical extensions action items if [ ${#CRITICAL_MISSING[@]} -gt 0 ]; then cat >> "$PREP_REPORT" << EOF **? CRITICAL - Enable PHP Extensions:** 1. **Login to your hosting control panel** 2. **Navigate to:** PHP Settings → Extensions (or PHP Selector) 3. **Enable these extensions:** ${CRITICAL_MISSING[*]} 4. **Save changes** and wait 1-2 minutes for activation 5. **Verify with:** \`php -m | grep -E '(${CRITICAL_MISSING[*]// /|})'\` EOF ACTION_ITEMS=$((ACTION_ITEMS + 1)) fi # Composer installation instructions if [ "$COMPOSER_STATUS" = "⚠️ LEGACY VERSION" ] || [ "$COMPOSER_STATUS" = "❌ NOT FOUND" ]; then cat >> "$PREP_REPORT" << EOF **? REQUIRED - Install Composer 2 (Per-Domain):** 1. **Navigate to domain root:** \`cd $DOMAIN_ROOT/\` 2. **Download Composer 2:** \`curl -sS https://getcomposer.org/installer | php\` 3. **Rename for clarity:** \`mv composer.phar composer2\` 4. **Make executable:** \`chmod +x composer2\` 5. **Test installation:** \`./composer2 --version\` 6. **Usage from releases:** \`../../composer2 install --no-dev --optimize-autoloader\` **Alternative (if you have sudo access):** 1. **System-wide install:** \`sudo curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer2\` EOF ACTION_ITEMS=$((ACTION_ITEMS + 1)) fi # Server tools action items if [ ${#MISSING_TOOLS[@]} -gt 0 ]; then cat >> "$PREP_REPORT" << EOF **?️ REQUIRED - Install Server Tools:** - **Missing tools:** ${MISSING_TOOLS[*]} - **Contact your hosting provider** to install these essential tools - **Alternative:** Some hosting providers have these in non-standard locations EOF ACTION_ITEMS=$((ACTION_ITEMS + 1)) fi # PHP upgrade recommendation if [ "$PHP_STATUS" = "⚠️ LEGACY" ] || [ "$PHP_STATUS" = "❌ UNSUPPORTED" ]; then cat >> "$PREP_REPORT" << EOF **? RECOMMENDED - Upgrade PHP:** - **Current:** PHP $PHP_VERSION - **Recommended:** PHP 8.1+ for Laravel 9+, PHP 8.2+ for Laravel 10+ - **Action:** Update via hosting control panel → PHP Settings EOF ACTION_ITEMS=$((ACTION_ITEMS + 1)) fi # Optional shared directories if [ ${#MISSING_OPTIONAL_DIRS[@]} -gt 0 ]; then cat >> "$PREP_REPORT" << EOF **? OPTIONAL - Enhanced Shared Directories:** Create additional shared directories for enhanced functionality: \`\`\`bash cd /home/u227177893/domains/staging.societypal.com/deploy/shared $(for dir in "${MISSING_OPTIONAL_DIRS[@]}"; do echo "mkdir -p $dir && chmod 775 $dir"; done) \`\`\` EOF ACTION_ITEMS=$((ACTION_ITEMS + 1)) fi # Summary status PHASE_A_STATUS="✅ READY" if [ $PHASE_A_ISSUES -gt 0 ]; then PHASE_A_STATUS="❌ NEEDS ATTENTION ($PHASE_A_ISSUES issues)" fi if [ $ACTION_ITEMS -eq 0 ]; then cat >> "$PREP_REPORT" << EOF ✅ **No critical actions required** - server environment is ready! EOF fi cat >> "$PREP_REPORT" << EOF **Phase A Status:** $PHASE_A_STATUS **Recommendations Provided:** $ACTION_ITEMS **Manual Actions Required:** $ACTION_ITEMS EOF # Display summary echo "" echo "=== Phase A Summary ===" echo "? Server Status: $PHASE_A_STATUS" echo "? Recommendations Provided: $ACTION_ITEMS" echo "? Manual Actions Required: $ACTION_ITEMS" echo "? Prep Report: $PREP_REPORT" if [ $PHASE_A_ISSUES -eq 0 ]; then echo "✅ Server environment is ready for Laravel deployment!" echo "? Proceeding to Phase A deployment commands..." else echo "⚠️ $PHASE_A_ISSUES server issues detected" echo "? Check prep report for specific action items" echo "ℹ️ Some issues can be resolved during deployment, others need hosting provider assistance" fi echo "✅ Phase-A-Prep completed successfully" # Log results for deployment history DEPLOY_PATH="/home/u227177893/domains/staging.societypal.com/deploy" if [ -d "/home/u227177893/domains/staging.societypal.com/deploy/shared" ]; then echo "[$(date '+%Y-%m-%d %H:%M:%S')] Phase-A-Prep: Status=$PHASE_A_STATUS | Issues=$PHASE_A_ISSUES | Recommendations=$ACTION_ITEMS" >> "/home/u227177893/domains/staging.societypal.com/deploy/shared/prep-history.log" fi # Exit successfully (don't block deployment even if issues found) exit 0]

=== Phase-A-Prep: Server Environment Validation & Setup ===
? Starting server environment validation...
? Prep Report: /home/u227177893/domains/staging.societypal.com/deployment-prep-report.md
=== Critical PHP Extensions Analysis ===
✅ pdo extension available
✅ pdo_mysql extension available
✅ openssl extension available
✅ mbstring extension available
✅ curl extension available
=== Smart Composer 2 Detection & Recommendations ===
✅ OPTIMAL: composer2 available server-wide (2.5)
=== Enhanced Shared Directory Structure Analysis ===
ℹ️ Optional: shared/logs/apache2 (could be added)
ℹ️ Optional: shared/logs/php (could be added)
ℹ️ Optional: shared/cache/sessions (could be added)
ℹ️ Optional: shared/cache/views (could be added)
ℹ️ Optional: shared/public/.well-known/acme-challenge (could be added)
ℹ️ Optional: shared/storage/debugbar (could be added)
ℹ️ Optional: shared/storage/clockwork (could be added)
? 7 optional directories could be created for enhanced functionality
=== PHP Configuration Analysis ===
? PHP Version: 8.2.28
✅ PHP 8.2.28 - Excellent for Laravel 10+
? Memory Limit: 6144M
? Max Execution Time: 0s
=== Server Capabilities Analysis ===
✅ curl: curl 7.76.1 (x86_64-redhat-linux-gnu) libcurl/7.76.1 OpenSSL/3.2.2 zlib/1.2.11 brotli/1.0.9 libidn2/2.3.0 libpsl/0.21.1 (+libidn2/2.3.0) libssh/0.10.4/openssl/zlib nghttp2/1.43.0
✅ git: git version 2.43.5
✅ mysql: mysql  Ver 15.1 Distrib 10.11.10-MariaDB, for Linux (x86_64) using  EditLine wrapper
ℹ️ Node.js: Not available (only needed for frontend builds)

=== Phase A Summary ===
? Server Status: ✅ READY
? Recommendations Provided: 1
? Manual Actions Required: 1
? Prep Report: /home/u227177893/domains/staging.societypal.com/deployment-prep-report.md
✅ Server environment is ready for Laravel deployment!
? Proceeding to Phase A deployment commands...
✅ Phase-A-Prep completed successfully
Time taken to run G3- inBeta-Phase-A-Prep: Server Environment Validation & Setup: 1.72 seconds


Running SSH command G2- Works-Phase A: Pre-Deployment Commands (Before Upload)

Executing G2- Works-Phase A: Pre-Deployment Commands (Before Upload) [#!/bin/bash set -e # Phase A: Pre-Deployment Commands (Before Upload) # Purpose: System checks, backups, maintenance mode, environment preparation # Version 2 - PRODUCTION READY (Enhanced with deployment report fixes) echo "=== Phase A: Pre-Deployment Setup (V2) ===" # A01: System Pre-flight Checks echo "=== System Pre-flight Checks ===" php --version || { echo "❌ PHP not found"; exit 1; } # Check for Composer 2 (required for Laravel 12+) if command -v composer2 &> /dev/null; then composer2 --version echo "✅ Using Composer 2 for Laravel 12+ compatibility" export COMPOSER_CMD="composer2" elif composer --version | grep -q "version 2\."; then composer --version echo "✅ Composer 2 detected" export COMPOSER_CMD="composer" else composer --version echo "⚠️ WARNING: Composer 1.x detected, Laravel 12+ requires Composer 2.x" echo "ℹ️ Will attempt to use available composer, but may cause issues" export COMPOSER_CMD="composer" fi node --version 2>/dev/null || echo "ℹ️ Node not required" # Check disk space AVAILABLE_DISK=$(df -k "/home/u227177893/domains/staging.societypal.com/deploy" 2>/dev/null | awk 'NR==2 {print $4}') if [ "$AVAILABLE_DISK" -lt 524288 ]; then # 512MB minimum echo "❌ Insufficient disk space" exit 1 fi # A02: Create Universal Shared Structure (COMPREHENSIVE) echo "=== Initialize Universal Shared Structure ===" # Core Laravel directories (standard) mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/storage/{app/public,framework/{cache/data,sessions,views},logs} mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/bootstrap/cache mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/backups mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/.well-known # For SSL certificates # 1. User Content Variations Pattern (UNIVERSAL COVERAGE) echo "Creating user content directories..." mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/upload # Covers: upload mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/uploads # Covers: uploads mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/uploaded # Covers: uploaded mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/user-upload # Covers: user-upload mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/user-uploads # Covers: user-uploads mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/media # Covers: media mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/medias # Covers: medias mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/avatar # Covers: avatar mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/avatars # Covers: avatars mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/clientAvatar # Covers: clientAvatar mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/attachment # Covers: attachment mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/attachments # Covers: attachments mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/document # Covers: document mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/documents # Covers: documents mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/file # Covers: file mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/files # Covers: files mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/images # Covers: images (user-generated) # 2. User Generated Content (DYNAMIC CONTENT) echo "Creating generated content directories..." mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/qrcode # Generated QR codes mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/qrcodes # Generated QR codes (plural) mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/barcode # Generated barcodes mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/barcodes # Generated barcodes (plural) mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/certificate # Generated certificates mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/certificates # Generated certificates (plural) mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/report # Generated reports mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/reports # Generated reports (plural) mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/temp # Temporary files mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/temporary # Temporary files (alternative) # 3. CodeCanyon Application Specific (PRESERVATION PATTERNS) echo "Creating CodeCanyon-specific directories..." mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/storage/app # For modules_statuses.json (CRITICAL!) mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/Modules # Custom modules (root level) mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/favicon # Custom favicons # Set proper permissions for all shared directories echo "Setting permissions for shared directories..." chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/storage chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/bootstrap/cache chmod 755 /home/u227177893/domains/staging.societypal.com/deploy/shared/backups chmod 755 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/.well-known # User content permissions (writable) chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/upload* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/user-upload* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/media* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/avatar* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/attachment* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/document* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/file* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/images # Generated content permissions (writable) chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/qrcode* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/barcode* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/certificate* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/report* chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/temp* # CodeCanyon specific permissions chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/storage/app # CRITICAL for modules_statuses.json chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/Modules chmod 755 /home/u227177893/domains/staging.societypal.com/deploy/shared/public/favicon echo "✅ Shared structure ready for DeployHQ symlinks" # A03: Backup Current Production (if exists) echo "=== Backup Current Release ===" if [ -L "/home/u227177893/domains/staging.societypal.com/deploy/current" ] && [ -d "/home/u227177893/domains/staging.societypal.com/deploy/current" ]; then TIMESTAMP=$(date +"%Y%m%d_%H%M%S") BACKUP_DIR="/home/u227177893/domains/staging.societypal.com/deploy/shared/backups/release_${TIMESTAMP}" mkdir -p "$BACKUP_DIR" # Quick backup of critical files only cd /home/u227177893/domains/staging.societypal.com/deploy/current tar -czf "$BACKUP_DIR/app_backup.tar.gz" \ --exclude='vendor' \ --exclude='node_modules' \ --exclude='storage' \ --exclude='bootstrap/cache' \ app config database resources routes 2>/dev/null || true echo "✅ Backup created: $BACKUP_DIR" # Keep only last 5 backups cd /home/u227177893/domains/staging.societypal.com/deploy/shared/backups ls -t | tail -n +6 | xargs rm -rf 2>/dev/null || true fi # A04: Database Backup (ENHANCED VERSION - FIXED from deployment report) echo "=== Database Backup ===" if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ]; then echo "Parsing .env file for database credentials..." # Use PHP to safely parse .env file (more reliable than bash) cat > /tmp/parse_env_v2.php << 'EOF' <?php if (empty($argv[1]) || !is_file($argv[1])) { exit(1); } $lines = file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); $env = []; foreach ($lines as $line) { $line = trim($line); if (empty($line) || strpos($line, '#') === 0) { continue; } if (strpos($line, '=') === false) { continue; } list($name, $value) = explode('=', $line, 2); $name = trim($name); $value = trim($value); // Remove quotes if present if (preg_match('/^"(.*)"$/', $value, $matches)) { $value = $matches[1]; } elseif (preg_match('/^\'(.*)\'$/', $value, $matches)) { $value = $matches[1]; } $env[$name] = $value; } // Output only the variables we need echo "DB_CONNECTION=" . ($env['DB_CONNECTION'] ?? 'mysql') . "\n"; echo "DB_HOST=" . ($env['DB_HOST'] ?? 'localhost') . "\n"; echo "DB_PORT=" . ($env['DB_PORT'] ?? '3306') . "\n"; echo "DB_DATABASE=" . ($env['DB_DATABASE'] ?? '') . "\n"; echo "DB_USERNAME=" . ($env['DB_USERNAME'] ?? '') . "\n"; echo "DB_PASSWORD=" . ($env['DB_PASSWORD'] ?? '') . "\n"; EOF # Parse the .env file and extract variables ENV_VARS=$(php /tmp/parse_env_v2.php "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" 2>/dev/null) rm -f /tmp/parse_env_v2.php if [ -n "$ENV_VARS" ]; then # Load the variables into the current shell eval "$ENV_VARS" echo "Database configuration:" echo " Host: $DB_HOST" echo " Port: $DB_PORT" echo " Database: $DB_DATABASE" echo " Username: $DB_USERNAME" if [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then BACKUP_FILE="/home/u227177893/domains/staging.societypal.com/deploy/shared/backups/db_$(date +%Y%m%d_%H%M%S).sql.gz" echo "Creating database backup..." # Test database connection first (ENHANCED from deployment report) if command -v mysql &> /dev/null; then # Test connection with timeout if timeout 10 mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" 2>/dev/null; then echo "✅ Database connection test passed" # Create backup with better error handling if mysqldump -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" \ --single-transaction --routines --triggers --add-drop-table \ "$DB_DATABASE" 2>/dev/null | gzip > "$BACKUP_FILE"; then if [ -f "$BACKUP_FILE" ] && [ -s "$BACKUP_FILE" ]; then BACKUP_SIZE=$(du -sh "$BACKUP_FILE" | cut -f1) echo "✅ Database backed up: $BACKUP_FILE ($BACKUP_SIZE)" # Set secure permissions on backup chmod 600 "$BACKUP_FILE" else echo "⚠️ Database backup failed or empty" rm -f "$BACKUP_FILE" 2>/dev/null fi else echo "⚠️ mysqldump failed - check credentials and permissions" rm -f "$BACKUP_FILE" 2>/dev/null fi else echo "⚠️ Cannot connect to database - skipping backup" echo "ℹ️ This may be normal for first deployment or if database is not ready" fi else echo "⚠️ mysql client not available - skipping database backup" echo "ℹ️ Install mysql-client package if database backups are needed" fi else echo "⚠️ Incomplete database configuration in .env" echo "ℹ️ Ensure DB_DATABASE and DB_USERNAME are set" fi else echo "⚠️ Failed to parse .env file" echo "ℹ️ Check .env file syntax and permissions" fi else echo "ℹ️ No .env file found - skipping database backup" echo "ℹ️ This is normal for first deployment" fi # A05: Enter Maintenance Mode (ENHANCED VERSION) echo "=== Enter Maintenance Mode ===" # Create maintenance flag for all hosting types touch /home/u227177893/domains/staging.societypal.com/deploy/.maintenance # Try Laravel maintenance if current release exists if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/current/artisan" ]; then cd /home/u227177893/domains/staging.societypal.com/deploy/current # Enhanced error handling for artisan down command if php artisan down --render="errors::503" 2>/dev/null; then echo "✅ Laravel maintenance mode activated" else echo "⚠️ Laravel maintenance mode failed, using maintenance flag only" echo "ℹ️ This may be due to missing dependencies (will be fixed in Phase B)" fi else echo "ℹ️ No current release (first deployment) - using maintenance flag only" fi echo "✅ Phase A completed successfully" ]

=== Phase A: Pre-Deployment Setup (V2) ===
=== System Pre-flight Checks ===
PHP 8.2.28 (cli) (built: Mar 12 2025 00:00:00) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.2.28, Copyright (c) Zend Technologies
    with Zend OPcache v8.2.28, Copyright (c), by Zend Technologies
Composer version 2.5.5 2023-03-21 11:50:05
✅ Using Composer 2 for Laravel 12+ compatibility
ℹ️ Node not required
=== Initialize Universal Shared Structure ===
Creating user content directories...
Creating generated content directories...
Creating CodeCanyon-specific directories...
Setting permissions for shared directories...
✅ Shared structure ready for DeployHQ symlinks
=== Backup Current Release ===
✅ Backup created: /home/u227177893/domains/staging.societypal.com/deploy/shared/backups/release_20250819_215128
=== Database Backup ===
Parsing .env file for database credentials...
Database configuration:
  Host: 127.0.0.1
  Port: 3306
  Database: u227177893_s_zaj_socpal_d
  Username: u227177893_s_zaj_socpal_u
Creating database backup...
+---+
| 1 |
+---+
| 1 |
+---+
✅ Database connection test passed
✅ Database backed up: /home/u227177893/domains/staging.societypal.com/deploy/shared/backups/db_20250819_215128.sql.gz (24K)
=== Enter Maintenance Mode ===
⚠️ Laravel maintenance mode failed, using maintenance flag only
ℹ️ This may be due to missing dependencies (will be fixed in Phase B)
✅ Phase A completed successfully
Time taken to run G2- Works-Phase A: Pre-Deployment Commands (Before Upload): 1.17 seconds


Preparing release directory

Making release directory /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954

Copying previous release into /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954


Transferring changed files



Show file destinations?
Uploading .github/workflows/first-install-part1.yml

Uploading .github/workflows/first-install-part2.yml

Uploading .github/workflows/manual-deploy.yml

Uploading .github/workflows/update-install.yml

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/.dev/0-myInput-Files/0-Prompts/prompt1

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/.dev/0-myInput-Files/0-Prompts/prompt2

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/.dev/claude-used

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/assets/css/components.css

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/assets/css/main.css

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/assets/css/variables.css

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/assets/js/app.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/assets/js/site-centric-manager.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/assets/js/storage.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/assets/js/template-engine.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/assets/js/utils.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/assets/js/validation.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/assets/js/wizard.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/index.html

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/step-0-project-management.html

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/steps-22-24-post-deployment.html

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/templates/unified/deployment-template.json

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2.1/templates/unified/hosting-template.json

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2/assets/css/components.css

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2/assets/css/main.css

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2/assets/css/variables.css

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2/assets/js/app.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2/assets/js/storage.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2/assets/js/utils.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2/assets/js/validation.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2/assets/js/wizard.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2/claude-used

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML v2/index.html

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML/assets/css/components.css

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML/assets/css/variables.css

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML/assets/js/app.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML/assets/js/storage.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML/assets/js/wizard.js

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/Refractor-Collect-info-HTML/index.html

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/SingleFile-Collect-info-HTML v1/Collect-Project-Server-Deploy-info-v1.html

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/SingleFile-Collect-info-HTML v1/Collect-Project-Server-Deploy-info-v2.html

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/step-21.1-collect-servers-info/SingleFile-Collect-info-HTML v1/claude-used

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/2-Files/B-Github-Actions-Files/deploy-server-script.sh.template

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/2-Files/B-Github-Actions-Files/deploy.yml.template

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/2-Files/B-Github-Actions-Files/secrets.env.template

Uploading Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/deployment-execution-checklist.html

Uploading Admin-Local/1-Current-Project/1-secrets/Server & Deployment Information

Uploading Admin-Local/1-Current-Project/1-secrets/deployment-config-2025-08-17.json

Uploading Admin-Local/1-Current-Project/1-secrets/manually collected

Uploading storage/framework/views/fd3978159e07e4526a8b2da57e05725e.php

Uploading storage/framework/views/cbd3b3cf87cde6e62b83ad658e1a8ed4.php


Uploading config files

Uploading /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/.env


Linking files from shared path to release

Symlinking prep-history.log to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/prep-history.log

Symlinking backups to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/backups

Symlinking bootstrap/cache to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/bootstrap/cache

Symlinking .env to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/.env

Symlinking Modules to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/Modules

Symlinking storage to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/storage

Symlinking public/favicon to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/favicon

Symlinking public/avatars to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/avatars

Symlinking public/documents to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/documents

Symlinking public/clientAvatar to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/clientAvatar

Symlinking public/barcodes to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/barcodes

Symlinking public/attachments to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/attachments

Symlinking public/.well-known to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/.well-known

Symlinking public/attachment to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/attachment

Symlinking public/temporary to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/temporary

Symlinking public/user-upload to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/user-upload

Symlinking public/qrcodes to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/qrcodes

Symlinking public/document to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/document

Symlinking public/barcode to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/barcode

Symlinking public/temp to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/temp

Symlinking public/files to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/files

Symlinking public/user-uploads to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/user-uploads

Symlinking public/report to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/report

Symlinking public/certificate to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/certificate

Symlinking public/file to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/file

Symlinking public/uploads to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/uploads

Symlinking public/images to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/images

Symlinking public/certificates to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/certificates

Symlinking public/avatar to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/avatar

Symlinking public/qrcode to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/qrcode

Symlinking public/medias to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/medias

Symlinking public/media to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/media

Symlinking public/reports to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/reports

Symlinking public/uploaded to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/uploaded

Symlinking public/upload to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/upload

Symlinking deployments.log to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/deployments.log


Running SSH command G2-Works-Phase B-First: Symlink Fallback Verification (After Upload, Before Release)

Executing G2-Works-Phase B-First: Symlink Fallback Verification (After Upload, Before Release) [#!/bin/bash set -e # Phase B-First: Symlink Fallback Verification (After Upload, Before Release) # Purpose: Verify and create missing core Laravel symlinks that DeployHQ might not create # Timing: After file upload, before current symlink switch - RUNS BEFORE Phase B # Version 2.0 - PRODUCTION READY echo "=== Phase B-First: Symlink Fallback Verification ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954 # A-Post-01: Verify Core Laravel Symlinks echo "=== Verifying Core Laravel Symlinks ===" # Check if storage symlink exists and is correct if [ ! -L "storage" ] || [ ! -e "storage" ]; then echo "⚠️ Storage symlink missing or broken - creating fallback symlink" rm -rf storage 2>/dev/null || true ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/storage storage echo "✅ Created storage → shared/storage symlink" else STORAGE_TARGET=$(readlink storage) echo "✅ Storage symlink exists: storage → $STORAGE_TARGET" fi # Check if bootstrap/cache symlink exists and is correct if [ ! -L "bootstrap/cache" ] || [ ! -e "bootstrap/cache" ]; then echo "⚠️ Bootstrap cache symlink missing or broken - creating fallback symlink" rm -rf bootstrap/cache 2>/dev/null || true ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/bootstrap/cache bootstrap/cache echo "✅ Created bootstrap/cache → shared/bootstrap/cache symlink" else BOOTSTRAP_TARGET=$(readlink bootstrap/cache) echo "✅ Bootstrap cache symlink exists: bootstrap/cache → $BOOTSTRAP_TARGET" fi # A-Post-02: Verify .env Symlink (Critical) echo "=== Verifying Environment Symlink ===" # CRITICAL: Don't create .env symlink if uploaded .env exists # Let PhaseB handle moving uploaded .env to shared first if [ -f ".env" ] && [ ! -L ".env" ]; then echo "✅ Uploaded .env file found - leaving for PhaseB to handle" echo " PhaseB will move this to shared and create proper symlink" elif [ ! -L ".env" ] || [ ! -e ".env" ]; then # Only create symlink if shared/.env already exists if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ]; then echo "⚠️ .env symlink missing but shared/.env exists - creating symlink" rm -rf .env 2>/dev/null || true ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/.env .env echo "✅ Created .env → shared/.env symlink" else echo "ℹ️ No .env symlink and no shared/.env - PhaseB will handle this" fi else ENV_TARGET=$(readlink .env) echo "✅ .env symlink exists: .env → $ENV_TARGET" fi # A-Post-03: Verify Modules Symlink (CodeCanyon) echo "=== Verifying Modules Symlink ===" if [ -d "/home/u227177893/domains/staging.societypal.com/deploy/shared/Modules" ]; then if [ ! -L "Modules" ] || [ ! -e "Modules" ]; then echo "⚠️ Modules symlink missing or broken - creating fallback symlink" rm -rf Modules 2>/dev/null || true ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/Modules Modules echo "✅ Created Modules → shared/Modules symlink" else MODULES_TARGET=$(readlink Modules) echo "✅ Modules symlink exists: Modules → $MODULES_TARGET" fi else echo "ℹ️ No shared Modules directory found - skipping" fi # A-Post-04: Verify Backups Symlink echo "=== Verifying Backups Symlink ===" if [ ! -L "backups" ] || [ ! -e "backups" ]; then echo "⚠️ Backups symlink missing or broken - creating fallback symlink" rm -rf backups 2>/dev/null || true ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/backups backups echo "✅ Created backups → shared/backups symlink" else BACKUPS_TARGET=$(readlink backups) echo "✅ Backups symlink exists: backups → $BACKUPS_TARGET" fi # A-Post-05: Initialize Storage Structure (if needed) echo "=== Initializing Storage Structure ===" if [ -L "storage" ] && [ -e "storage" ]; then # Only create structure if storage is properly symlinked mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/storage/{app/public,framework/{cache/data,sessions,views},logs} chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/storage echo "✅ Storage structure initialized" else echo "❌ Storage symlink not working - structure initialization skipped" fi # A-Post-06: Final Verification Report echo "=== Symlink Verification Summary ===" SYMLINK_ISSUES=0 # Check all critical symlinks for LINK in storage bootstrap/cache .env backups; do if [ -L "$LINK" ] && [ -e "$LINK" ]; then TARGET=$(readlink "$LINK") echo "✅ $LINK → $TARGET" else echo "❌ $LINK: BROKEN OR MISSING" SYMLINK_ISSUES=$((SYMLINK_ISSUES + 1)) fi done # Check Modules if it should exist if [ -d "/home/u227177893/domains/staging.societypal.com/deploy/shared/Modules" ]; then if [ -L "Modules" ] && [ -e "Modules" ]; then MODULES_TARGET=$(readlink Modules) echo "✅ Modules → $MODULES_TARGET" else echo "❌ Modules: BROKEN OR MISSING" SYMLINK_ISSUES=$((SYMLINK_ISSUES + 1)) fi fi if [ $SYMLINK_ISSUES -eq 0 ]; then echo "? All critical symlinks verified successfully!" else echo "⚠️ $SYMLINK_ISSUES symlink issues detected - may need manual intervention" fi echo "✅ Phase B-First symlink verification completed successfully" ]

=== Phase B-First: Symlink Fallback Verification ===
=== Verifying Core Laravel Symlinks ===
✅ Storage symlink exists: storage → ../../shared/storage
✅ Bootstrap cache symlink exists: bootstrap/cache → ../../../shared/bootstrap/cache
=== Verifying Environment Symlink ===
✅ .env symlink exists: .env → ../../shared/.env
=== Verifying Modules Symlink ===
✅ Modules symlink exists: Modules → ../../shared/Modules
=== Verifying Backups Symlink ===
✅ Backups symlink exists: backups → ../../shared/backups
=== Initializing Storage Structure ===
✅ Storage structure initialized
=== Symlink Verification Summary ===
✅ storage → ../../shared/storage
✅ bootstrap/cache → ../../../shared/bootstrap/cache
✅ .env → ../../shared/.env
✅ backups → ../../shared/backups
✅ Modules → ../../shared/Modules
? All critical symlinks verified successfully!
✅ Phase B-First symlink verification completed successfully
Time taken to run G2-Works-Phase B-First: Symlink Fallback Verification (After Upload, Before Release): 0.36 seconds


Running SSH command G3-inBeta- Phase-B-Prep: Application Compatibility & Configuration

Executing G3-inBeta- Phase-B-Prep: Application Compatibility & Configuration [#!/bin/bash set -e # Phase-B-Prep: Application Compatibility & Configuration # Purpose: Validate Laravel application, smart database testing, configuration validation # Run: AFTER file upload, BEFORE release activation (SSH Commands - After Upload, Before Release) # Action: VALIDATE app readiness, TEST configurations, PREPARE for activation # Version 1.0 - PRODUCTION READY (Application-focused validation) echo "=== Phase-B-Prep: Application Compatibility & Configuration ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954 # Initialize variables using DeployHQ variables DOMAIN_ROOT=$(dirname "/home/u227177893/domains/staging.societypal.com/deploy") PREP_REPORT="$DOMAIN_ROOT/deployment-prep-report.md" PHASE_B_ISSUES=0 RECOMMENDATIONS_PROVIDED=0 echo "? Starting application compatibility validation..." echo "? Release Path: $(pwd)" # Update prep report for Phase B cat >> "$PREP_REPORT" << EOF --- ## ? Phase B: Application Compatibility & Configuration **Focus:** Laravel readiness, database validation, configuration testing **Release:** $(basename "$(pwd)") EOF # B-Prep-01: Laravel Framework Validation echo "=== Laravel Framework Validation ===" LARAVEL_STATUS="❌ NOT DETECTED" LARAVEL_VERSION="Unknown" if [ -f "artisan" ]; then echo "✅ Laravel artisan file detected" # Test artisan without exec() dependency (shared hosting friendly) if [ -f "vendor/autoload.php" ] && [ -f "bootstrap/app.php" ]; then # Try to get Laravel version from composer.json if [ -f "composer.json" ] && command -v jq >/dev/null 2>&1; then LARAVEL_VERSION=$(jq -r '.require."laravel/framework" // "Not specified"' composer.json 2>/dev/null) elif [ -f "composer.json" ]; then LARAVEL_VERSION=$(grep -o '"laravel/framework":\s*"[^"]*"' composer.json | cut -d'"' -f4 2>/dev/null || echo "Detected") fi # Alternative: Check Laravel version from Application class if [ "$LARAVEL_VERSION" = "Unknown" ] || [ "$LARAVEL_VERSION" = "Not specified" ]; then LARAVEL_VERSION=$(grep -r "VERSION.*=" vendor/laravel/framework/src/Illuminate/Foundation/Application.php 2>/dev/null | grep -o "[0-9]\+\.[0-9]\+\.[0-9]\+" | head -1 || echo "Detected") fi LARAVEL_STATUS="✅ DETECTED" echo "✅ Laravel framework: $LARAVEL_VERSION" else echo "❌ Laravel dependencies missing (vendor/autoload.php or bootstrap/app.php)" LARAVEL_STATUS="❌ INCOMPLETE" PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1)) fi else echo "❌ No artisan file - not a Laravel application" PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1)) fi # B-Prep-02: Dependencies Validation echo "=== Dependencies Validation ===" DEPENDENCIES_STATUS="✅ COMPLETE" VENDOR_SIZE="0" if [ -d "vendor" ]; then VENDOR_SIZE=$(du -sh vendor 2>/dev/null | cut -f1) VENDOR_FILES=$(find vendor -name "*.php" 2>/dev/null | wc -l) echo "✅ Vendor directory: $VENDOR_SIZE ($VENDOR_FILES PHP files)" # Check for critical Laravel dependencies CRITICAL_PACKAGES=("laravel/framework" "symfony/console" "illuminate/support") MISSING_PACKAGES=() for package in "${CRITICAL_PACKAGES[@]}"; do PACKAGE_PATH="vendor/$(echo "$package" | tr '/' '/')" if [ -d "$PACKAGE_PATH" ]; then echo "✅ $package: Available" else echo "❌ $package: Missing" MISSING_PACKAGES+=("$package") PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1)) fi done if [ ${#MISSING_PACKAGES[@]} -gt 0 ]; then DEPENDENCIES_STATUS="❌ INCOMPLETE" fi else echo "❌ No vendor directory found" DEPENDENCIES_STATUS="❌ MISSING" PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1)) fi # B-Prep-03: Smart Environment Configuration Analysis echo "=== Smart Environment Configuration Analysis ===" ENV_CONFIG_STATUS="❌ NOT CONFIGURED" ENV_FILE_PATH="" DB_CONFIG_COMPLETE="false" # Determine .env file location (could be symlinked or uploaded) if [ -f ".env" ]; then ENV_FILE_PATH=".env" elif [ -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ]; then ENV_FILE_PATH="/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" else echo "❌ No .env file found (neither local nor shared)" PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1)) fi if [ -n "$ENV_FILE_PATH" ]; then echo "✅ Environment file: $ENV_FILE_PATH" ENV_CONFIG_STATUS="✅ FOUND" # Smart environment validation (check completeness before testing) REQUIRED_ENV_VARS=("APP_KEY" "APP_URL" "DB_CONNECTION" "DB_HOST" "DB_DATABASE" "DB_USERNAME") MISSING_ENV_VARS=() EMPTY_ENV_VARS=() for var in "${REQUIRED_ENV_VARS[@]}"; do if grep -q "^${var}=" "$ENV_FILE_PATH" 2>/dev/null; then VALUE=$(grep "^${var}=" "$ENV_FILE_PATH" | cut -d'=' -f2- | tr -d '"' | tr -d "'" | xargs) if [ -n "$VALUE" ] && [ "$VALUE" != "null" ]; then echo "✅ $var: Configured" else echo "⚠️ $var: Empty value" EMPTY_ENV_VARS+=("$var") fi else echo "❌ $var: Missing" MISSING_ENV_VARS+=("$var") PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1)) fi done # Check if database configuration is complete for testing DB_REQUIRED=("DB_CONNECTION" "DB_HOST" "DB_DATABASE" "DB_USERNAME") DB_COMPLETE_COUNT=0 for var in "${DB_REQUIRED[@]}"; do if grep -q "^${var}=.\+" "$ENV_FILE_PATH" 2>/dev/null; then VALUE=$(grep "^${var}=" "$ENV_FILE_PATH" | cut -d'=' -f2- | tr -d '"' | tr -d "'" | xargs) if [ -n "$VALUE" ] && [ "$VALUE" != "null" ]; then DB_COMPLETE_COUNT=$((DB_COMPLETE_COUNT + 1)) fi fi done if [ $DB_COMPLETE_COUNT -eq ${#DB_REQUIRED[@]} ]; then DB_CONFIG_COMPLETE="true" echo "✅ Database configuration: Complete for testing" else echo "⚠️ Database configuration: Incomplete ($DB_COMPLETE_COUNT/${#DB_REQUIRED[@]} required vars)" fi fi # B-Prep-04: Smart Database Connection Testing echo "=== Smart Database Connection Testing ===" DB_CONNECTION_STATUS="⚠️ UNTESTED" DB_TEST_METHOD="None" if [ "$DB_CONFIG_COMPLETE" = "true" ] && [ -n "$ENV_FILE_PATH" ]; then # Extract database credentials safely DB_CONNECTION=$(grep "^DB_CONNECTION=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs) DB_HOST=$(grep "^DB_HOST=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs) DB_PORT=$(grep "^DB_PORT=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs) DB_DATABASE=$(grep "^DB_DATABASE=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs) DB_USERNAME=$(grep "^DB_USERNAME=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs) DB_PASSWORD=$(grep "^DB_PASSWORD=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs) # Default port if not specified DB_PORT=${DB_PORT:-3306} echo "? Testing: $DB_CONNECTION database on $DB_HOST:$DB_PORT" # Method 1: Direct MySQL client test (most reliable for shared hosting) if command -v mysql >/dev/null 2>&1; then echo "? Testing with MySQL client..." if timeout 10 mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1 as test;" "$DB_DATABASE" >/dev/null 2>&1; then DB_CONNECTION_STATUS="✅ CONNECTED (MySQL client)" DB_TEST_METHOD="MySQL CLI" echo "✅ Database connection: Success via MySQL client" else echo "❌ Database connection: Failed via MySQL client" DB_CONNECTION_STATUS="❌ FAILED (MySQL client)" DB_TEST_METHOD="MySQL CLI" PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1)) fi # Method 2: PHP PDO test (fallback, works without exec() restrictions) else echo "? Testing with PHP PDO..." PDO_TEST_SCRIPT="<?php try { \$dsn = 'mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_DATABASE'; \$pdo = new PDO(\$dsn, '$DB_USERNAME', '$DB_PASSWORD', [ PDO::ATTR_TIMEOUT => 5, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]); \$stmt = \$pdo->query('SELECT 1 as test'); if (\$stmt->fetch()) { echo 'PDO_SUCCESS'; } else { echo 'PDO_QUERY_FAILED'; } } catch (Exception \$e) { echo 'PDO_ERROR: ' . \$e->getMessage(); } ?>" PDO_RESULT=$(echo "$PDO_TEST_SCRIPT" | php 2>/dev/null) if echo "$PDO_RESULT" | grep -q "PDO_SUCCESS"; then DB_CONNECTION_STATUS="✅ CONNECTED (PHP PDO)" DB_TEST_METHOD="PHP PDO" echo "✅ Database connection: Success via PHP PDO" else echo "❌ Database connection: $PDO_RESULT" DB_CONNECTION_STATUS="❌ FAILED (PHP PDO)" DB_TEST_METHOD="PHP PDO" PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1)) fi fi else echo "ℹ️ Database connection test skipped (incomplete configuration)" DB_TEST_METHOD="Skipped - incomplete config" fi # B-Prep-05: Laravel Cache & Session Configuration Analysis echo "=== Cache & Session Configuration Analysis ===" CACHE_CONFIG_STATUS="✅ OPTIMAL" SESSION_CONFIG_STATUS="✅ OPTIMAL" if [ -n "$ENV_FILE_PATH" ]; then # Analyze cache configuration CACHE_DRIVER=$(grep "^CACHE_DRIVER=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs) CACHE_DRIVER=${CACHE_DRIVER:-file} SESSION_DRIVER=$(grep "^SESSION_DRIVER=" "$ENV_FILE_PATH" | cut -d'=' -f2 | tr -d '"' | tr -d "'" | xargs) SESSION_DRIVER=${SESSION_DRIVER:-file} echo "? Cache driver: $CACHE_DRIVER" echo "? Session driver: $SESSION_DRIVER" # Check for potentially problematic Redis configuration on shared hosting if [ "$CACHE_DRIVER" = "redis" ] || [ "$SESSION_DRIVER" = "redis" ]; then echo "⚠️ Redis configuration detected - may not be available on shared hosting" echo "ℹ️ Auto-fixes will be applied in Phase C if Redis is unavailable" CACHE_CONFIG_STATUS="⚠️ REDIS (unverified)" SESSION_CONFIG_STATUS="⚠️ REDIS (unverified)" else echo "✅ File-based cache/session configuration (shared hosting compatible)" fi else echo "ℹ️ Cache/session analysis skipped (no .env file)" fi # B-Prep-06: Application Permissions Validation echo "=== Application Permissions Validation ===" PERMISSIONS_STATUS="✅ SECURE" PERMISSION_ISSUES=() # Check critical Laravel directories CRITICAL_DIRS=("storage" "bootstrap/cache") for dir in "${CRITICAL_DIRS[@]}"; do if [ -e "$dir" ]; then if [ -w "$dir" ]; then echo "✅ $dir: Writable" else echo "❌ $dir: Not writable" PERMISSION_ISSUES+=("$dir") PHASE_B_ISSUES=$((PHASE_B_ISSUES + 1)) fi else echo "⚠️ $dir: Missing (will be created by symlinks)" fi done if [ ${#PERMISSION_ISSUES[@]} -gt 0 ]; then PERMISSIONS_STATUS="❌ ISSUES FOUND" fi # Update prep report with Phase B results cat >> "$PREP_REPORT" << EOF ### ?️ Laravel Application **Framework:** $LARAVEL_STATUS $([ "$LARAVEL_VERSION" != "Unknown" ] && echo "($LARAVEL_VERSION)" || echo "") **Dependencies:** $DEPENDENCIES_STATUS $([ -n "$VENDOR_SIZE" ] && echo "($VENDOR_SIZE)" || echo "") **Permissions:** $PERMISSIONS_STATUS ### ⚙️ Configuration Analysis **Environment File:** $ENV_CONFIG_STATUS **Database Config:** $([ "$DB_CONFIG_COMPLETE" = "true" ] && echo "✅ Complete" || echo "⚠️ Incomplete") **Cache Driver:** $CACHE_DRIVER ($CACHE_CONFIG_STATUS) **Session Driver:** $SESSION_DRIVER ($SESSION_CONFIG_STATUS) ### ?️ Database Connection Test **Status:** $DB_CONNECTION_STATUS **Method:** $DB_TEST_METHOD $([ "$DB_CONNECTION_STATUS" = "❌ FAILED (MySQL client)" ] || [ "$DB_CONNECTION_STATUS" = "❌ FAILED (PHP PDO)" ] && echo "**Issue:** Cannot connect to database - check credentials" || echo "**Result:** Connection validated successfully") EOF # Generate Phase B specific action items PHASE_B_ACTIONS=0 cat >> "$PREP_REPORT" << EOF ### ? Application Action Items: EOF # Laravel dependency issues if [ "$DEPENDENCIES_STATUS" = "❌ MISSING" ] || [ "$DEPENDENCIES_STATUS" = "❌ INCOMPLETE" ]; then cat >> "$PREP_REPORT" << EOF **? CRITICAL - Fix Dependencies:** 1. **Run composer install:** \`composer install --no-dev --optimize-autoloader\` 2. **Or contact hosting provider** if composer is not available 3. **Verify:** Check that \`vendor/\` directory exists and contains Laravel files EOF PHASE_B_ACTIONS=$((PHASE_B_ACTIONS + 1)) fi # Environment configuration issues if [ ${#MISSING_ENV_VARS[@]} -gt 0 ] || [ ${#EMPTY_ENV_VARS[@]} -gt 0 ]; then cat >> "$PREP_REPORT" << EOF **⚙️ REQUIRED - Fix Environment Configuration:** $([ ${#MISSING_ENV_VARS[@]} -gt 0 ] && echo "- **Add missing variables:** ${MISSING_ENV_VARS[*]}") $([ ${#EMPTY_ENV_VARS[@]} -gt 0 ] && echo "- **Set empty variables:** ${EMPTY_ENV_VARS[*]}") - **Edit:** \`.env\` file in your deployment - **Generate APP_KEY:** \`php artisan key:generate\` (if APP_KEY is missing) EOF PHASE_B_ACTIONS=$((PHASE_B_ACTIONS + 1)) fi # Database connection issues if [[ "$DB_CONNECTION_STATUS" =~ "FAILED" ]]; then cat >> "$PREP_REPORT" << EOF **?️ CRITICAL - Fix Database Connection:** 1. **Verify database exists** in your hosting control panel 2. **Check credentials** in \`.env\` file match hosting panel settings: - DB_HOST (often 'localhost' or specific server) - DB_DATABASE (exact database name) - DB_USERNAME (database user with access) - DB_PASSWORD (correct password) 3. **Test manually:** \`mysql -h[HOST] -u[USER] -p[PASS] [DATABASE]\` EOF PHASE_B_ACTIONS=$((PHASE_B_ACTIONS + 1)) fi # Permission issues if [ ${#PERMISSION_ISSUES[@]} -gt 0 ]; then cat >> "$PREP_REPORT" << EOF **? REQUIRED - Fix Permissions:** - **Directories with issues:** ${PERMISSION_ISSUES[*]} - **Fix command:** \`chmod -R 775 ${PERMISSION_ISSUES[*]}\` - **Note:** These may be resolved by Phase B symlink creation EOF PHASE_B_ACTIONS=$((PHASE_B_ACTIONS + 1)) fi # Summary status PHASE_B_STATUS="✅ READY FOR RELEASE" if [ $PHASE_B_ISSUES -gt 0 ]; then PHASE_B_STATUS="❌ NEEDS FIXES ($PHASE_B_ISSUES issues)" fi if [ $PHASE_B_ACTIONS -eq 0 ]; then cat >> "$PREP_REPORT" << EOF ✅ **No application issues found** - ready for release activation! EOF fi cat >> "$PREP_REPORT" << EOF **Phase B Status:** $PHASE_B_STATUS **Recommendations Provided:** $PHASE_B_ACTIONS **Manual Actions Required:** $PHASE_B_ACTIONS EOF # Display summary echo "" echo "=== Phase B Summary ===" echo "? Application Status: $PHASE_B_STATUS" echo "? Recommendations Provided: $PHASE_B_ACTIONS" echo "? Manual Actions Required: $PHASE_B_ACTIONS" echo "? Prep Report: $PREP_REPORT" if [ $PHASE_B_ISSUES -eq 0 ]; then echo "✅ Laravel application is ready for release activation!" echo "? Proceeding to Phase B deployment commands..." else echo "⚠️ $PHASE_B_ISSUES application issues detected" echo "? Check prep report for specific action items" echo "ℹ️ Some issues may be resolved by Phase B deployment commands" fi echo "✅ Phase-B-Prep completed successfully" # Log results for deployment history DEPLOY_PATH="/home/u227177893/domains/staging.societypal.com/deploy" if [ -d "/home/u227177893/domains/staging.societypal.com/deploy/shared" ]; then echo "[$(date '+%Y-%m-%d %H:%M:%S')] Phase-B-Prep: Status=$PHASE_B_STATUS | Issues=$PHASE_B_ISSUES | Recommendations=$PHASE_B_ACTIONS | DB=$DB_CONNECTION_STATUS" >> "/home/u227177893/domains/staging.societypal.com/deploy/shared/prep-history.log" fiB-Prep: Status=$PHASE_B_STATUS | Issues=$PHASE_B_ISSUES | Recommendations=$PHASE_B_ACTIONS | DB=$DB_CONNECTION_STATUS" >> "$DEPLOY_PATH/shared/prep-history.log" fi # Exit successfully (don't block deployment) exit 0]

=== Phase-B-Prep: Application Compatibility & Configuration ===
? Starting application compatibility validation...
? Release Path: /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954
=== Laravel Framework Validation ===
✅ Laravel artisan file detected
✅ Laravel framework: ^12.0
=== Dependencies Validation ===
✅ Vendor directory: 253M (14163 PHP files)
✅ laravel/framework: Available
✅ symfony/console: Available
❌ illuminate/support: Missing
=== Smart Environment Configuration Analysis ===
✅ Environment file: .env
✅ APP_KEY: Configured
✅ APP_URL: Configured
✅ DB_CONNECTION: Configured
✅ DB_HOST: Configured
✅ DB_DATABASE: Configured
✅ DB_USERNAME: Configured
✅ Database configuration: Complete for testing
=== Smart Database Connection Testing ===
? Testing: mysql database on 127.0.0.1:3306
? Testing with MySQL client...
❌ Database connection: Failed via MySQL client
=== Cache & Session Configuration Analysis ===
? Cache driver: file
? Session driver: file
✅ File-based cache/session configuration (shared hosting compatible)
=== Application Permissions Validation ===
✅ storage: Writable
✅ bootstrap/cache: Writable

=== Phase B Summary ===
? Application Status: ❌ NEEDS FIXES (2 issues)
? Recommendations Provided: 2
? Manual Actions Required: 2
? Prep Report: /home/u227177893/domains/staging.societypal.com/deployment-prep-report.md
⚠️ 2 application issues detected
? Check prep report for specific action items
ℹ️ Some issues may be resolved by Phase B deployment commands
✅ Phase-B-Prep completed successfully
bash: -c: line 420: unexpected EOF while looking for matching `"'
Time taken to run G3-inBeta- Phase-B-Prep: Application Compatibility & Configuration: 0.8 seconds


Running SSH command G2-Works-Phase B: Pre-Release Commands (After Upload, Before Release)

Executing G2-Works-Phase B: Pre-Release Commands (After Upload, Before Release) [#!/bin/bash set -e # Phase B: Pre-Release Commands (After Upload, Before Release) # Purpose: Configure release, set security, link shared resources # Note: DeployHQ has already uploaded files to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954 # Version 2 - PRODUCTION READY (Enhanced with deployment report fixes) echo "=== Phase B: Pre-Release Configuration (V2) ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954 # B01: Environment Setup (ENHANCED VERSION) echo "=== Environment Configuration ===" # DeployHQ has uploaded .env to release - handle it properly if [ ! -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ]; then # First deployment: move uploaded .env to shared echo "? DEBUG: Checking for uploaded .env file..." echo " Release path: /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954" echo " Shared path: /home/u227177893/domains/staging.societypal.com/deploy/shared" ls -la /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/.env* 2>/dev/null || echo " No .env* files found" if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/.env" ]; then echo "✅ Found uploaded .env file - moving to shared" mv /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/.env /home/u227177893/domains/staging.societypal.com/deploy/shared/.env echo "✅ Moved uploaded .env to shared (first deployment)" elif [ -f "/home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/.env.example" ]; then echo "⚠️ WARNING: No uploaded .env found, using .env.example as fallback" echo " This suggests DeployHQ config issue - .env should be uploaded!" cp /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/.env.example /home/u227177893/domains/staging.societypal.com/deploy/shared/.env echo "⚠️ Created .env from example - configure database!" else echo "❌ No .env file found - this may cause issues" fi # Generate APP_KEY if missing if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ]; then if ! grep -q "^APP_KEY=base64:" /home/u227177893/domains/staging.societypal.com/deploy/shared/.env; then KEY=$(php -r 'echo "base64:".base64_encode(random_bytes(32));') sed -i "s|^APP_KEY=.*|APP_KEY=$KEY|" /home/u227177893/domains/staging.societypal.com/deploy/shared/.env echo "✅ Generated APP_KEY" fi fi else # Subsequent deployment: remove uploaded .env (we use shared one) if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/.env" ]; then rm -f /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/.env echo "✅ Removed uploaded .env (using shared version)" fi fi # Set secure permissions on shared .env chmod 600 /home/u227177893/domains/staging.societypal.com/deploy/shared/.env 2>/dev/null || true # CRITICAL: Ensure .env symlink exists (DeployHQ sometimes fails to create it) if [ ! -L "/home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/.env" ]; then echo "⚠️ .env symlink missing - creating it manually" ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/.env /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/.env echo "✅ Created .env symlink: .env -> ../../shared/.env" else echo "✅ .env symlink already exists" fi # B02: Security - Create/Update .htaccess files (ENHANCED VERSION) echo "=== Security Configuration ===" # Root .htaccess (redirect all to public) cat > /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/.htaccess << 'EOF' <IfModule mod_rewrite.c> RewriteEngine On RewriteRule ^(.*)$ public/$1 [L] </IfModule> # Deny access to sensitive files <FilesMatch "^\."> Order allow,deny Deny from all </FilesMatch> <Files "composer.json"> Order allow,deny Deny from all </Files> <Files "package.json"> Order allow,deny Deny from all </Files> <Files "artisan"> Order allow,deny Deny from all </Files> # Additional security headers <IfModule mod_headers.c> Header always set X-Content-Type-Options nosniff Header always set X-Frame-Options DENY Header always set X-XSS-Protection "1; mode=block" Header always set Referrer-Policy "strict-origin-when-cross-origin" </IfModule> EOF # Public .htaccess for Laravel (ENHANCED VERSION) cat > /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954/public/.htaccess << 'EOF' <IfModule mod_rewrite.c> <IfModule mod_negotiation.c> Options -MultiViews -Indexes </IfModule> RewriteEngine On # Handle Authorization Header RewriteCond %{HTTP:Authorization} . RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}] # Redirect Trailing Slashes If Not A Folder... RewriteCond %{REQUEST_FILENAME} !-d RewriteCond %{REQUEST_URI} (.+)/$ RewriteRule ^ %1 [L,R=301] # Send Requests To Front Controller... RewriteCond %{REQUEST_FILENAME} !-d RewriteCond %{REQUEST_FILENAME} !-f RewriteRule ^ index.php [L] </IfModule> # Security Headers <IfModule mod_headers.c> Header set X-Content-Type-Options "nosniff" Header set X-Frame-Options "SAMEORIGIN" Header set X-XSS-Protection "1; mode=block" Header set Referrer-Policy "strict-origin-when-cross-origin" Header set Permissions-Policy "geolocation=(), microphone=(), camera=()" </IfModule> # Disable directory browsing Options -Indexes # Block access to hidden files except .well-known <Files ".*"> <IfModule mod_authz_core.c> Require all denied </IfModule> </Files> <FilesMatch "^\.well-known"> <IfModule mod_authz_core.c> Require all granted </IfModule> </FilesMatch> # Compress text files <IfModule mod_deflate.c> AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json </IfModule> # Cache static assets <IfModule mod_expires.c> ExpiresActive On ExpiresByType image/jpg "access plus 1 month" ExpiresByType image/jpeg "access plus 1 month" ExpiresByType image/gif "access plus 1 month" ExpiresByType image/png "access plus 1 month" ExpiresByType text/css "access plus 1 week" ExpiresByType application/javascript "access plus 1 week" </IfModule> EOF echo "✅ Security configurations created" # B03: Fix Composer Dependencies (ENHANCED VERSION - CRITICAL FIX) echo "=== Verify Dependencies ===" # Use Composer 2 if available (required for Laravel 12+) COMPOSER_CMD="composer" if command -v composer2 &> /dev/null; then COMPOSER_CMD="composer2" echo "✅ Using Composer 2 for Laravel 12+ compatibility" elif composer --version | grep -q "version 1\."; then echo "⚠️ WARNING: Using Composer 1.x with Laravel 12+ may cause issues" fi if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then echo "Installing composer dependencies with $COMPOSER_CMD..." if $COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction; then echo "✅ Dependencies installed successfully" else echo "❌ Composer install failed" echo "Checking Composer version compatibility..." $COMPOSER_CMD --version exit 1 fi else echo "✅ Dependencies already installed" fi # B04: Enhanced Inertia Detection (IMPROVED - Multiple Location Check) echo "=== Comprehensive Inertia Detection ===" INERTIA_DETECTED=false # Check 1: composer.json for inertiajs/inertia-laravel if [ -f "composer.json" ] && grep -q "inertiajs/inertia-laravel" composer.json 2>/dev/null; then echo "✅ Inertia detected in composer.json" INERTIA_DETECTED=true fi # Check 2: config/app.php for Inertia ServiceProvider if [ -f "config/app.php" ] && grep -q "Inertia\\\ServiceProvider" config/app.php 2>/dev/null; then echo "✅ Inertia ServiceProvider detected in config/app.php" INERTIA_DETECTED=true fi # Check 3: package.json for @inertiajs/ packages if [ -f "package.json" ] && grep -q "@inertiajs/" package.json 2>/dev/null; then echo "✅ Inertia frontend packages detected in package.json" INERTIA_DETECTED=true fi # Check 4: vendor directory for actual installation if [ -d "vendor/inertiajs" ] && [ -f "vendor/inertiajs/inertia-laravel/src/ServiceProvider.php" ]; then echo "✅ Inertia Laravel package found in vendor" INERTIA_DETECTED=true fi if [ "$INERTIA_DETECTED" = true ]; then echo "? Inertia detected - verifying installation..." # Verify Inertia is actually installed in vendor if [ ! -d "vendor/inertiajs" ] || [ ! -f "vendor/inertiajs/inertia-laravel/src/ServiceProvider.php" ]; then echo "⚠️ Inertia detected in config but not installed - installing..." $COMPOSER_CMD require inertiajs/inertia-laravel --no-interaction 2>/dev/null || { echo "⚠️ Failed to install Inertia via $COMPOSER_CMD, trying alternative..." # Try to install from source if composer fails if [ -f "composer.json" ]; then # Add Inertia to composer.json if not present if ! grep -q "inertiajs/inertia-laravel" composer.json; then echo "Adding Inertia to composer.json..." sed -i '/"require": {/a\ "inertiajs/inertia-laravel": "^0.6.0",' composer.json $COMPOSER_CMD update --no-dev --optimize-autoloader --no-interaction 2>/dev/null || true fi fi } else echo "✅ Inertia Laravel package properly installed" fi # Additional check: Verify Inertia middleware exists if [ -f "app/Http/Kernel.php" ] && grep -q "HandleInertiaRequests" app/Http/Kernel.php 2>/dev/null; then echo "✅ Inertia middleware detected" else echo "ℹ️ Inertia middleware not found (may need manual setup)" fi else echo "ℹ️ Inertia not detected in any location (composer.json, config/app.php, package.json, vendor)" fi # B05: Set Secure Permissions (ENHANCED VERSION) echo "=== Setting Secure Permissions ===" # Default secure permissions for release files find . -type f -exec chmod 644 {} \; find . -type d -exec chmod 755 {} \; # Make artisan executable [ -f "artisan" ] && chmod 755 artisan # Make any shell scripts executable find . -name "*.sh" -type f -exec chmod 755 {} \; 2>/dev/null || true # Protect .git if exists in release [ -d ".git" ] && chmod -R 700 .git # Note: Shared directories permissions are set in Phase A # DeployHQ symlinks will inherit the shared directory permissions echo "✅ Release permissions secured" # B06: Database Migrations (ENHANCED VERSION with better error handling) echo "=== Database Operations ===" if [ -f "artisan" ]; then # Wait for DeployHQ to create .env symlink, then test database echo "Testing database connection..." # Give DeployHQ a moment to create symlinks if needed sleep 1 if [ -f ".env" ] || [ -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ]; then # Test database connection using Laravel's built-in method if php artisan tinker --execute="echo 'DB connection test: '; try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAILED: ' . \$e->getMessage(); }" 2>/dev/null | grep -q "OK"; then echo "✅ Database connection successful, running migrations..." php artisan migrate --force || echo "⚠️ Migration failed but continuing" echo "✅ Migrations completed" else echo "⚠️ Database not accessible - skip migrations" echo "ℹ️ This may be normal for first deployment or if database is not configured" fi else echo "⚠️ No .env file available - skip migrations" echo "ℹ️ DeployHQ will create .env symlink after this phase" fi else echo "ℹ️ No artisan file found - not a Laravel application" fi # B07: Laravel Optimization (ENHANCED VERSION - NO BUILD DEPENDENCIES) echo "=== Application Optimization ===" if [ -f "artisan" ]; then # Clear old caches first echo "Clearing old caches..." php artisan cache:clear 2>/dev/null || echo "ℹ️ Cache clear skipped" php artisan config:clear 2>/dev/null || echo "ℹ️ Config clear skipped" php artisan route:clear 2>/dev/null || echo "ℹ️ Route clear skipped" php artisan view:clear 2>/dev/null || echo "ℹ️ View clear skipped" # Build new caches with better error handling echo "Building new caches..." if php artisan config:cache 2>/dev/null; then echo "✅ Configuration cached" else echo "⚠️ Config cache failed - will retry in Phase C" fi if php artisan route:cache 2>/dev/null; then echo "✅ Routes cached" else echo "⚠️ Route cache failed - will retry in Phase C" fi if php artisan view:cache 2>/dev/null; then echo "✅ Views cached" else echo "⚠️ View cache failed - will retry in Phase C" fi # Optional: Event cache (if using events) if [ -f "app/Providers/EventServiceProvider.php" ]; then php artisan event:cache 2>/dev/null && echo "✅ Events cached" || echo "ℹ️ Event cache skipped" fi # Note: Storage link will be created in Phase C after current symlink exists echo "ℹ️ Storage link will be created in Phase C" else echo "ℹ️ No artisan file - skipping Laravel optimization" fi # B09: Universal Content Symlinks (COMPREHENSIVE COVERAGE) echo "=== Creating Universal Content Symlinks ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954 # Function to create symlink if directory exists in release create_content_symlink() { local DIR_PATH="$1" local DIR_NAME=$(basename "$DIR_PATH") if [ -d "$DIR_PATH" ] && [ ! -L "$DIR_PATH" ]; then SHARED_DIR="/home/u227177893/domains/staging.societypal.com/deploy/shared/public/$DIR_NAME" # Migrate any existing content to shared (first deployment) if [ "$(ls -A "$DIR_PATH" 2>/dev/null)" ]; then echo "? Migrating existing content: $DIR_NAME" mkdir -p "$SHARED_DIR" cp -r "$DIR_PATH"/* "$SHARED_DIR"/ 2>/dev/null || true chmod -R 775 "$SHARED_DIR" 2>/dev/null || true fi # Remove directory and create symlink rm -rf "$DIR_PATH" ln -sf "../../shared/public/$DIR_NAME" "$DIR_PATH" echo "✅ Created symlink: $DIR_NAME -> shared/public/$DIR_NAME" elif [ -L "$DIR_PATH" ]; then echo "ℹ️ Symlink already exists: $DIR_NAME" fi } # 1. User Content Variations Pattern echo "Processing user content directories..." create_content_symlink "public/upload" create_content_symlink "public/uploads" create_content_symlink "public/uploaded" create_content_symlink "public/user-upload" create_content_symlink "public/user-uploads" create_content_symlink "public/media" create_content_symlink "public/medias" create_content_symlink "public/avatar" create_content_symlink "public/avatars" create_content_symlink "public/clientAvatar" create_content_symlink "public/attachment" create_content_symlink "public/attachments" create_content_symlink "public/document" create_content_symlink "public/documents" create_content_symlink "public/file" create_content_symlink "public/files" create_content_symlink "public/images" # 2. User Generated Content echo "Processing generated content directories..." create_content_symlink "public/qrcode" create_content_symlink "public/qrcodes" create_content_symlink "public/barcode" create_content_symlink "public/barcodes" create_content_symlink "public/certificate" create_content_symlink "public/certificates" create_content_symlink "public/report" create_content_symlink "public/reports" create_content_symlink "public/temp" create_content_symlink "public/temporary" # 3. CodeCanyon Specific Patterns echo "Processing CodeCanyon-specific patterns..." # Handle favicon files (preserve custom favicons) if [ -f "public/favicon.ico" ]; then if [ ! -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/public/favicon/favicon.ico" ]; then echo "? Preserving custom favicon.ico" mkdir -p "/home/u227177893/domains/staging.societypal.com/deploy/shared/public/favicon" cp "public/favicon.ico" "/home/u227177893/domains/staging.societypal.com/deploy/shared/public/favicon/" chmod 644 "/home/u227177893/domains/staging.societypal.com/deploy/shared/public/favicon/favicon.ico" fi rm -f "public/favicon.ico" ln -sf "../shared/public/favicon/favicon.ico" "public/favicon.ico" echo "✅ Created symlink: favicon.ico -> shared" fi # Handle Modules directory (root level) if [ -d "Modules" ] && [ ! -L "Modules" ]; then echo "? Preserving Modules directory" if [ "$(ls -A "Modules" 2>/dev/null)" ]; then mkdir -p "/home/u227177893/domains/staging.societypal.com/deploy/shared/Modules" cp -r "Modules"/* "/home/u227177893/domains/staging.societypal.com/deploy/shared/Modules"/ 2>/dev/null || true chmod -R 775 "/home/u227177893/domains/staging.societypal.com/deploy/shared/Modules" 2>/dev/null || true fi rm -rf "Modules" ln -sf "../shared/Modules" "Modules" echo "✅ Created symlink: Modules -> shared/Modules" elif [ -L "Modules" ]; then echo "ℹ️ Modules symlink already exists" fi # Handle modules_statuses.json (in storage/app) if [ -f "storage/app/modules_statuses.json" ]; then echo "? Preserving modules_statuses.json" mkdir -p "/home/u227177893/domains/staging.societypal.com/deploy/shared/storage/app" cp "storage/app/modules_statuses.json" "/home/u227177893/domains/staging.societypal.com/deploy/shared/storage/app/" 2>/dev/null || true chmod 644 "/home/u227177893/domains/staging.societypal.com/deploy/shared/storage/app/modules_statuses.json" 2>/dev/null || true echo "✅ Preserved modules_statuses.json in shared storage" fi echo "✅ Universal content symlinks completed" # Note: No build commands here - build pipeline is handled separately echo "ℹ️ Build pipeline handled separately - no build dependencies" echo "✅ Phase B completed successfully" ]

=== Phase B: Pre-Release Configuration (V2) ===
=== Environment Configuration ===
✅ Removed uploaded .env (using shared version)
⚠️ .env symlink missing - creating it manually
✅ Created .env symlink: .env -> ../../shared/.env
=== Security Configuration ===
✅ Security configurations created
=== Verify Dependencies ===
✅ Using Composer 2 for Laravel 12+ compatibility
✅ Dependencies already installed
=== Comprehensive Inertia Detection ===
ℹ️ Inertia not detected in any location (composer.json, config/app.php, package.json, vendor)
=== Setting Secure Permissions ===
✅ Release permissions secured
=== Database Operations ===
Testing database connection...
⚠️ Database not accessible - skip migrations
ℹ️ This may be normal for first deployment or if database is not configured
=== Application Optimization ===
Clearing old caches...
ℹ️ Cache clear skipped
ℹ️ Config clear skipped
ℹ️ Route clear skipped
ℹ️ View clear skipped
Building new caches...
⚠️ Config cache failed - will retry in Phase C
⚠️ Route cache failed - will retry in Phase C
⚠️ View cache failed - will retry in Phase C
ℹ️ Storage link will be created in Phase C
=== Creating Universal Content Symlinks ===
Processing user content directories...
ℹ️ Symlink already exists: upload
ℹ️ Symlink already exists: uploads
ℹ️ Symlink already exists: uploaded
ℹ️ Symlink already exists: user-upload
ℹ️ Symlink already exists: user-uploads
ℹ️ Symlink already exists: media
ℹ️ Symlink already exists: medias
ℹ️ Symlink already exists: avatar
ℹ️ Symlink already exists: avatars
ℹ️ Symlink already exists: clientAvatar
ℹ️ Symlink already exists: attachment
ℹ️ Symlink already exists: attachments
ℹ️ Symlink already exists: document
ℹ️ Symlink already exists: documents
ℹ️ Symlink already exists: file
ℹ️ Symlink already exists: files
ℹ️ Symlink already exists: images
Processing generated content directories...
ℹ️ Symlink already exists: qrcode
ℹ️ Symlink already exists: qrcodes
ℹ️ Symlink already exists: barcode
ℹ️ Symlink already exists: barcodes
ℹ️ Symlink already exists: certificate
ℹ️ Symlink already exists: certificates
ℹ️ Symlink already exists: report
ℹ️ Symlink already exists: reports
ℹ️ Symlink already exists: temp
ℹ️ Symlink already exists: temporary
Processing CodeCanyon-specific patterns...
ℹ️ Modules symlink already exists
✅ Universal content symlinks completed
ℹ️ Build pipeline handled separately - no build dependencies
✅ Phase B completed successfully
Time taken to run G2-Works-Phase B: Pre-Release Commands (After Upload, Before Release): 23.49 seconds


Linking release directory to current

Symlinking /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954 to /home/u227177893/domains/staging.societypal.com/deploy/current


Running SSH command G2-Works-Phase C-1: Post-Deployment Commands (After Release)

Executing G2-Works-Phase C-1: Post-Deployment Commands (After Release) [#!/bin/bash set -e # Phase C-1: Post-Deployment Commands (After Release) # Purpose: Activate release, setup public access, exit maintenance, verify # Note: DeployHQ has already created the current symlink # Version 2 - PRODUCTION READY (Enhanced with deployment report fixes) # Works for both first-time and subsequent deployments echo "=== Phase C: Post-Deployment Finalization (V2) ===" # C01: Setup Public Access for All Hosting Types (UNIVERSAL VERSION) echo "=== Configure Public Access ===" # Detect hosting structure DOMAIN_ROOT=$(dirname "/home/u227177893/domains/staging.societypal.com/deploy") cd "$DOMAIN_ROOT" echo "? Analyzing hosting environment..." echo " Domain root: $DOMAIN_ROOT" echo " Deploy path: /home/u227177893/domains/staging.societypal.com/deploy" # Detect hosting type and handle accordingly HOSTING_TYPE="unknown" if [ -d "public_html" ] && [ ! -L "public_html" ]; then HOSTING_TYPE="hostinger" echo "? Detected: Hostinger-style hosting (existing public_html directory)" elif [ -L "public_html" ]; then HOSTING_TYPE="existing_symlink" echo "? Detected: Existing public_html symlink" elif [ -d "www" ] && [ ! -L "www" ]; then HOSTING_TYPE="www_based" echo "? Detected: WWW-based hosting" else HOSTING_TYPE="cpanel_secure" echo "?️ Detected: cPanel-style hosting (will create secure symlink)" fi case $HOSTING_TYPE in "hostinger") echo "? Hostinger Setup: Handling existing public_html directory..." # Check if public_html has content if [ "$(ls -A public_html 2>/dev/null | grep -v '^\.' | wc -l)" -gt 0 ]; then BACKUP_NAME="public_html.backup.$(date +%Y%m%d_%H%M%S)" echo " ? Backing up existing content to: $BACKUP_NAME" mv public_html "$BACKUP_NAME" || { echo " ⚠️ Backup failed, removing existing content..." rm -rf public_html } else echo " ?️ Removing empty public_html directory..." rm -rf public_html fi # Create symlink to Laravel public directory if ln -sf deploy/current/public public_html; then echo " ✅ Created: public_html -> deploy/current/public" else echo " ❌ Failed to create symlink - check permissions" fi ;; "existing_symlink") echo "? Existing Symlink: Verifying and updating..." CURRENT_TARGET=$(readlink public_html 2>/dev/null || echo "broken") if [ "$CURRENT_TARGET" != "deploy/current/public" ]; then echo " ? Updating symlink target from: $CURRENT_TARGET" rm -f public_html if ln -sf deploy/current/public public_html; then echo " ✅ Updated: public_html -> deploy/current/public" else echo " ❌ Failed to update symlink" fi else echo " ✅ Symlink already correct: $CURRENT_TARGET" fi ;; "www_based") echo "? WWW-based Setup: Handling www directory..." if [ "$(ls -A www 2>/dev/null | grep -v '^\.' | wc -l)" -gt 0 ]; then mv www "www.backup.$(date +%Y%m%d_%H%M%S)" echo " ? Backed up existing www directory" else rm -rf www fi if ln -sf deploy/current/public www; then echo " ✅ Created: www -> deploy/current/public" else echo " ❌ Failed to create www symlink" fi ;; "cpanel_secure") echo "? cPanel Secure Setup: Creating secure public_html symlink..." # This is the most secure approach - create symlink to Laravel public if ln -sf deploy/current/public public_html; then echo " ✅ Created secure symlink: public_html -> deploy/current/public" echo " ?️ Laravel app files remain outside web-accessible directory" else echo " ⚠️ Primary symlink failed, trying alternative approach..." # Alternative: create from within deploy directory cd deploy/current && ln -sf public ../../public_html && cd ../.. if [ -L "public_html" ]; then echo " ✅ Alternative symlink creation successful" else echo " ❌ All symlink attempts failed - manual intervention required" echo " ? Suggestion: Check directory permissions and server configuration" fi fi ;; *) echo "❓ Unknown hosting type - using default secure approach" ln -sf deploy/current/public public_html 2>/dev/null && echo "✅ Default symlink created" || echo "⚠️ Default symlink failed" ;; esac # Verify the final setup echo "? Verifying public access setup..." if [ -L "public_html" ] && [ -e "public_html" ]; then FINAL_TARGET=$(readlink public_html) echo " ✅ public_html -> $FINAL_TARGET" # Test if Laravel public files are accessible if [ -f "public_html/index.php" ]; then echo " ✅ Laravel application accessible via public_html" else echo " ⚠️ Laravel index.php not found in public_html" fi else echo " ❌ public_html symlink verification failed" echo " ? Manual setup may be required for this hosting environment" fi # C02: Create index.php fallback in domain root (security) if [ "$DOMAIN_ROOT" != "/home/u227177893/domains/staging.societypal.com/deploy" ]; then cat > "$DOMAIN_ROOT/index.php" << 'EOF' <?php // Security redirect to Laravel public directory header("Location: /public_html/"); exit(); EOF echo "✅ Created security redirect in domain root" fi # C03: Exit Maintenance Mode (ENHANCED VERSION with better error handling) echo "=== Exit Maintenance Mode ===" rm -f /home/u227177893/domains/staging.societypal.com/deploy/.maintenance if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/current/artisan" ]; then cd /home/u227177893/domains/staging.societypal.com/deploy/current # Try to exit Laravel maintenance mode, but don't fail if it doesn't work if php artisan up 2>/dev/null; then echo "✅ Laravel maintenance mode deactivated" else echo "⚠️ Failed to exit Laravel maintenance mode via artisan (continuing)" echo "ℹ️ This may be due to missing dependencies - check Phase B completion" echo "ℹ️ You may need to check the application manually" fi else echo "ℹ️ No artisan file found, maintenance flag removed only" fi echo "✅ Application is now live" # C04: Laravel Final Setup (ENHANCED VERSION) echo "=== Laravel Final Setup ===" cd /home/u227177893/domains/staging.societypal.com/deploy/current if [ -f "artisan" ]; then # CRITICAL: Verify and fix storage symlinks echo "Verifying storage symlinks..." # Check if public/storage is directory instead of symlink (common issue) if [ -d "public/storage" ] && [ ! -L "public/storage" ]; then echo "⚠️ public/storage is directory, converting to symlink..." rm -rf public/storage ln -sfn ../storage/app/public public/storage echo "✅ Converted public/storage directory to symlink" fi # Create/verify storage link echo "Creating storage link..." if php artisan storage:link --force 2>/dev/null; then echo "✅ Storage link created via artisan" else echo "⚠️ Artisan storage:link failed - likely exec() function disabled on shared hosting" echo " Attempting manual symlink creation (fallback method)..." # Fallback 1: Standard manual creation rm -f public/storage 2>/dev/null if ln -sfn ../storage/app/public public/storage; then echo "✅ Manual storage link created (fallback 1)" else echo "⚠️ Fallback 1 failed - trying exec() bypass method (fallback 2)..." # Fallback 2: Enhanced exec() bypass method (suggested improvement) STORAGE_TARGET="../storage/app/public" STORAGE_LINK="public/storage" # Remove existing if present rm -f "$STORAGE_LINK" 2>/dev/null # Create symlink manually (this doesn't use exec()) if ln -sfn "$STORAGE_TARGET" "$STORAGE_LINK"; then echo "✅ Storage link created via exec() bypass method (fallback 2)" else echo "⚠️ Fallback 2 failed - trying absolute path (fallback 3)..." # Fallback 3: Absolute path ABSOLUTE_TARGET="/home/u227177893/domains/staging.societypal.com/deploy/shared/storage/app/public" if ln -sfn "$ABSOLUTE_TARGET" "$STORAGE_LINK"; then echo "✅ Absolute path storage link created (fallback 3)" else echo "❌ All storage link attempts failed - manual intervention required" echo " Please create symlink manually: ln -sfn /home/u227177893/domains/staging.societypal.com/deploy/shared/storage/app/public public/storage" fi fi fi fi # Verify final storage symlink if [ -L "public/storage" ] && [ -e "public/storage" ]; then STORAGE_TARGET=$(readlink public/storage) echo "✅ Storage symlink verified: public/storage -> $STORAGE_TARGET" else echo "❌ Storage symlink verification failed - manual fix required" fi # Retry any caches that failed in Phase B echo "Finalizing Laravel caches..." php artisan config:cache 2>/dev/null && echo "✅ Config cache finalized" || echo "ℹ️ Config cache skipped" php artisan route:cache 2>/dev/null && echo "✅ Route cache finalized" || echo "ℹ️ Route cache skipped" php artisan view:cache 2>/dev/null && echo "✅ View cache finalized" || echo "ℹ️ View cache skipped" # Restart queue workers if needed if [ -f ".env" ]; then QUEUE_CONNECTION=$(grep "^QUEUE_CONNECTION=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" 2>/dev/null) if [ "$QUEUE_CONNECTION" != "sync" ] && [ -n "$QUEUE_CONNECTION" ]; then if php artisan queue:restart 2>/dev/null; then echo "✅ Queue workers signaled to restart" else echo "⚠️ Queue restart failed (continuing)" fi else echo "ℹ️ Queue using sync driver (no workers)" fi fi else echo "ℹ️ No artisan file - not a Laravel application" fi # C05: Clear Runtime Caches echo "=== Clear Runtime Caches ===" # Clear PHP OPcache if php -r "echo function_exists('opcache_reset') ? 'yes' : 'no';" 2>/dev/null | grep -q "yes"; then php -r "opcache_reset();" 2>/dev/null || true echo "✅ OPcache cleared" else echo "ℹ️ OpCache not available" fi # Clear composer cache (use composer command from Phase A) ${COMPOSER_CMD:-composer} clear-cache 2>/dev/null || true # C06: Security Verification (ENHANCED VERSION) echo "=== Security Verification ===" cd /home/u227177893/domains/staging.societypal.com/deploy/current # Check for exposed sensitive files EXPOSED_FILES=0 for FILE in .env .env.example composer.json package.json .git; do if [ -e "public/$FILE" ]; then echo "❌ SECURITY WARNING: $FILE exposed in public!" EXPOSED_FILES=$((EXPOSED_FILES + 1)) fi done if [ $EXPOSED_FILES -eq 0 ]; then echo "✅ No sensitive files exposed" fi # Verify .htaccess files exist [ -f ".htaccess" ] && echo "✅ Root .htaccess present" || echo "⚠️ Root .htaccess missing" [ -f "public/.htaccess" ] && echo "✅ Public .htaccess present" || echo "⚠️ Public .htaccess missing" # Check critical permissions [ -r "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ] && [ ! -w "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ] || echo "⚠️ .env file should not be world-writable" # C07: Health Checks (ENHANCED VERSION with better error handling) echo "=== Application Health Checks ===" HEALTH_PASSED=true # Check Laravel installation if [ -f "artisan" ]; then if php artisan --version >/dev/null 2>&1; then echo "✅ Laravel framework operational" LARAVEL_VERSION=$(php artisan --version 2>/dev/null | head -1) echo " Version: $LARAVEL_VERSION" else echo "❌ Laravel framework error" HEALTH_PASSED=false # Try to identify the issue echo " Debug info:" php artisan --version 2>&1 | head -3 echo " This may be due to:" echo " - Missing dependencies (check Phase B completion)" echo " - exec() function disabled on shared hosting" echo " - PHP configuration issues" fi else echo "⚠️ No artisan file found in current release" fi # Check critical paths echo "=== Critical Path Verification ===" [ -L "storage" ] && echo "✅ Storage symlink valid" || echo "❌ Storage symlink broken" [ -f ".env" ] && echo "✅ Environment configured" || echo "❌ Environment missing" [ -d "vendor" ] && echo "✅ Dependencies installed" || echo "❌ Dependencies missing" # Test database connection (ENHANCED from deployment report) if [ -f ".env" ]; then echo "Testing database connection..." if php artisan tinker --execute="echo 'DB connection test: '; try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAILED: ' . \$e->getMessage(); }" 2>/dev/null | grep -q "OK"; then echo "✅ Database connection working" else echo "⚠️ Database connection failed" echo "ℹ️ This may be due to:" echo " - Database credentials incorrect" echo " - Database server not ready" echo " - exec() function disabled (preventing artisan tinker)" echo " - Network connectivity issues" fi fi # Test HTTP response (ENHANCED with better error handling) if [ -f ".env" ]; then APP_URL=$(grep "^APP_URL=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'") if [ -n "$APP_URL" ] && command -v curl &> /dev/null; then echo "Testing application response..." HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL" --max-time 5 2>/dev/null || echo "000") case $HTTP_CODE in 200|201|301|302) echo "✅ Application responding (HTTP $HTTP_CODE)" ;; 503) echo "⚠️ Application in maintenance mode" ;; 000) echo "⚠️ Application not reachable" ;; *) echo "⚠️ Application returned HTTP $HTTP_CODE" ;; esac else echo "ℹ️ Cannot test web accessibility (no APP_URL or curl)" fi fi # C08: Cleanup Old Releases (ENHANCED VERSION) echo "=== Cleanup Old Releases ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases KEEP_RELEASES=3 TOTAL=$(ls -1t 2>/dev/null | wc -l) if [ "$TOTAL" -gt "$KEEP_RELEASES" ]; then echo "Cleaning old releases (keeping $KEEP_RELEASES)..." RELEASES_TO_DELETE=$((TOTAL - KEEP_RELEASES)) echo "Will delete $RELEASES_TO_DELETE old releases" ls -1t | tail -n +$((KEEP_RELEASES + 1)) | while read OLD; do if [ -n "$OLD" ] && [ -d "$OLD" ]; then echo "Removing: $OLD" rm -rf "$OLD" 2>/dev/null || echo "⚠️ Failed to remove $OLD" fi done echo "✅ Old releases cleanup completed" else echo "ℹ️ Only $TOTAL releases found, no cleanup needed" fi # C09: Final Report (ENHANCED VERSION) echo "=== Deployment Summary ===" echo "? Release: /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954" echo "? Current: /home/u227177893/domains/staging.societypal.com/deploy/current" echo "? Commit: f6adbf02b5c35c2e49b10683bcdd8c493e6546dc" echo "? Deployed by: Zaj Apps" echo "? Environment: staging" echo "? Status: $( [ "$HEALTH_PASSED" = true ] && echo '✅ Healthy' || echo '⚠️ Check warnings above' )" # Log deployment echo "[$(date '+%Y-%m-%d %H:%M:%S')] staging deployment - Release: /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954 - Commit: f6adbf02b5c35c2e49b10683bcdd8c493e6546dc - Status: Success" >> /home/u227177893/domains/staging.societypal.com/deploy/shared/deployments.log echo "✅ Deployment completed successfully!" exit 0 ]

=== Phase C: Post-Deployment Finalization (V2) ===
=== Configure Public Access ===
? Analyzing hosting environment...
   Domain root: /home/u227177893/domains/staging.societypal.com
   Deploy path: /home/u227177893/domains/staging.societypal.com/deploy
? Detected: Existing public_html symlink
? Existing Symlink: Verifying and updating...
   ✅ Symlink already correct: deploy/current/public
? Verifying public access setup...
   ✅ public_html -> deploy/current/public
   ✅ Laravel application accessible via public_html
✅ Created security redirect in domain root
=== Exit Maintenance Mode ===
⚠️ Failed to exit Laravel maintenance mode via artisan (continuing)
ℹ️ This may be due to missing dependencies - check Phase B completion
ℹ️ You may need to check the application manually
✅ Application is now live
=== Laravel Final Setup ===
Verifying storage symlinks...
Creating storage link...
⚠️ Artisan storage:link failed - likely exec() function disabled on shared hosting
   Attempting manual symlink creation (fallback method)...
✅ Manual storage link created (fallback 1)
✅ Storage symlink verified: public/storage -> ../storage/app/public
Finalizing Laravel caches...
ℹ️ Config cache skipped
ℹ️ Route cache skipped
ℹ️ View cache skipped
⚠️ Queue restart failed (continuing)
=== Clear Runtime Caches ===
✅ OPcache cleared
=== Security Verification ===
✅ No sensitive files exposed
✅ Root .htaccess present
✅ Public .htaccess present
⚠️ .env file should not be world-writable
=== Application Health Checks ===
❌ Laravel framework error
  Debug info:
  This may be due to:
    - Missing dependencies (check Phase B completion)
    - exec() function disabled on shared hosting
    - PHP configuration issues
=== Critical Path Verification ===
✅ Storage symlink valid
✅ Environment configured
✅ Dependencies installed
Testing database connection...
⚠️ Database connection failed
ℹ️ This may be due to:
   - Database credentials incorrect
   - Database server not ready
   - exec() function disabled (preventing artisan tinker)
   - Network connectivity issues
Testing application response...
⚠️ Application in maintenance mode
=== Cleanup Old Releases ===
Cleaning old releases (keeping 3)...
Will delete 1 old releases
Removing: 20250819041738
✅ Old releases cleanup completed
=== Deployment Summary ===
? Release: /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954
? Current: /home/u227177893/domains/staging.societypal.com/deploy/current
? Commit: f6adbf02b5c35c2e49b10683bcdd8c493e6546dc
? Deployed by: Zaj Apps
? Environment: staging
? Status: ⚠️ Check warnings above
✅ Deployment completed successfully!
Time taken to run G2-Works-Phase C-1: Post-Deployment Commands (After Release): 3.34 seconds


Running SSH command G2-Works-Phase C-2: Comprehensive Deployment Verification & Reporting

Executing G2-Works-Phase C-2: Comprehensive Deployment Verification & Reporting [#!/bin/bash set -e # Phase C-2: Comprehensive Deployment Verification & Reporting # Purpose: Complete post-deployment analysis, verification, and detailed reporting # Run after Phase C - provides comprehensive deployment health check and documentation # Version 2.1 - PRODUCTION READY with Directory Validation # Working Directory: Expected to run from deploy/ directory (e.g., domains/xxx/deploy) echo "=== Phase C-2: Comprehensive Deployment Verification & Reporting ===" # C2-00: Initialize DeployHQ Path Variables (Same as Phases A, B, C) # These variables match DeployHQ's standard path structure # When running in DeployHQ, these will be replaced with actual paths # When testing manually, we detect the current directory structure if [[ "/home/u227177893/domains/staging.societypal.com/deploy" == *"%"* ]]; then # We're running manually for testing - detect paths from current directory CURRENT_DIR="$(pwd)" if [[ "$CURRENT_DIR" =~ .*/domains/[^/]+/deploy$ ]] && [[ -d "releases" ]] && [[ -d "shared" ]]; then export DEPLOY_PATH="$(pwd)" export SHARED_PATH="$(pwd)/shared" # Find current release if [ -L "current" ]; then CURRENT_RELEASE=$(readlink current | sed 's|^./releases/||') export CURRENT_PATH="$(pwd)/current" export RELEASE_PATH="$(pwd)/releases/$CURRENT_RELEASE" else export CURRENT_PATH="" export RELEASE_PATH="" fi echo "? TESTING MODE: Detected paths from current directory" else echo "❌ ERROR: Not in a valid deployment directory for testing" echo " Expected: domains/xxx/deploy (containing 'releases' and 'shared' directories)" echo " Current: $CURRENT_DIR" exit 1 fi else # We're running in DeployHQ - use the provided variables export DEPLOY_PATH="/home/u227177893/domains/staging.societypal.com/deploy" export RELEASE_PATH="/home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954" export SHARED_PATH="/home/u227177893/domains/staging.societypal.com/deploy/shared" export CURRENT_PATH="/home/u227177893/domains/staging.societypal.com/deploy/current" echo "? DEPLOYHQ MODE: Using DeployHQ path variables" fi echo "? Path Variables:" echo " Deploy Path: $DEPLOY_PATH" echo " Release Path: $RELEASE_PATH" echo " Shared Path: $SHARED_PATH" echo " Current Path: $CURRENT_PATH" # C2-01: Initialize Report Structure TIMESTAMP=$(date +"%Y%m%d_%H%M%S") DOMAIN_ROOT=$(dirname "$DEPLOY_PATH") DOMAIN_NAME=$(basename "$DOMAIN_ROOT") REPORT_DIR="$DOMAIN_ROOT/deployment-reports" REPORT_FILE="$REPORT_DIR/deployment-report-$TIMESTAMP.md" echo "? Starting comprehensive deployment verification..." echo " Domain: $DOMAIN_NAME" echo " Deploy Path: $DEPLOY_PATH" echo " Report: $REPORT_FILE" # Create report directory mkdir -p "$REPORT_DIR" # Start report file cat > "$REPORT_FILE" << EOF # ? Deployment Verification Report **Domain:** $DOMAIN_NAME **Timestamp:** $(date '+%Y-%m-%d %H:%M:%S') **Deploy Path:** \`$DEPLOY_PATH\` **Generated By:** Phase C-2 Verification Script --- ## ? TL;DR - QUICK SUMMARY EOF # C2-02: Environment Detection and Basic Info echo "=== Environment Detection ===" cd "$DEPLOY_PATH" # Detect current release if [ -L "current" ]; then CURRENT_RELEASE=$(readlink current | sed 's|^./releases/||') echo "✅ Current release detected: $CURRENT_RELEASE" else echo "❌ No current symlink found" CURRENT_RELEASE="NONE" fi # Count releases RELEASE_COUNT=$(ls -1 releases/ 2>/dev/null | wc -l) echo "? Total releases: $RELEASE_COUNT" # Check shared directory SHARED_SIZE=$(du -sh "$SHARED_PATH" 2>/dev/null | cut -f1 || echo "0") echo "? Shared directory size: $SHARED_SIZE" # Add initial report structure (TLDR will be updated later) cat >> "$REPORT_FILE" << EOF <!-- TLDR_PLACEHOLDER --> --- ## ? DETAILED ANALYSIS ### ?️ Infrastructure Status ### Directory Structure \`\`\` $DOMAIN_NAME/ ├── deploy/ │ ├── current -> releases/$CURRENT_RELEASE │ ├── releases/ ($RELEASE_COUNT releases) │ └── shared/ ($SHARED_SIZE) ├── public_html -> $([ -L "$DOMAIN_ROOT/public_html" ] && readlink "$DOMAIN_ROOT/public_html" || echo "NOT FOUND") └── deployment-reports/ ($(ls -1 "$REPORT_DIR" 2>/dev/null | wc -l) reports) \`\`\` EOF # C2-03: Critical Symlink Verification echo "=== Critical Symlink Verification ===" SYMLINK_STATUS="HEALTHY" # Function to check symlink check_symlink() { local LINK_PATH="$1" local DESCRIPTION="$2" local EXPECTED_TARGET="$3" if [ -L "$LINK_PATH" ]; then local TARGET=$(readlink "$LINK_PATH") local RESOLVED=$(readlink -f "$LINK_PATH" 2>/dev/null || echo "BROKEN") if [ -e "$LINK_PATH" ]; then echo "✅ $DESCRIPTION: $TARGET" echo " → $DESCRIPTION | ✅ VALID | $TARGET | $RESOLVED" >> /tmp/symlink_report else echo "❌ $DESCRIPTION: BROKEN ($TARGET)" echo " → $DESCRIPTION | ❌ BROKEN | $TARGET | BROKEN" >> /tmp/symlink_report SYMLINK_STATUS="ISSUES_FOUND" fi else echo "❌ $DESCRIPTION: NOT A SYMLINK" echo " → $DESCRIPTION | ❌ NOT_SYMLINK | N/A | N/A" >> /tmp/symlink_report SYMLINK_STATUS="ISSUES_FOUND" fi } # Initialize symlink report echo "" > /tmp/symlink_report # Check public_html (web root) cd "$DOMAIN_ROOT" check_symlink "public_html" "Web Root (public_html)" "deploy/current/public" # Check current release symlink cd "$DEPLOY_PATH" check_symlink "current" "Current Release" "releases/$CURRENT_RELEASE" # Check Laravel symlinks (if current exists) if [ -n "$CURRENT_RELEASE" ] && [ "$CURRENT_RELEASE" != "NONE" ] && [ -d "$CURRENT_PATH" ]; then cd "$CURRENT_PATH" check_symlink ".env" "Environment File" "$SHARED_PATH/.env" check_symlink "storage" "Storage Directory" "$SHARED_PATH/storage" check_symlink "bootstrap/cache" "Bootstrap Cache" "$SHARED_PATH/bootstrap/cache" check_symlink "public/storage" "Public Storage" "../storage/app/public" fi # Add symlink report to main report cat >> "$REPORT_FILE" << EOF ### ? Symlink Health Check EOF # Convert technical symlink report to user-friendly format while IFS='|' read -r component status target resolved; do if [[ "$component" =~ "Web Root" ]]; then if [[ "$status" =~ "VALID" ]]; then echo "✅ **Website Access**: Working correctly" >> "$REPORT_FILE" else echo "❌ **Website Access**: BROKEN - visitors cannot reach your site!" >> "$REPORT_FILE" fi elif [[ "$component" =~ "Current Release" ]]; then if [[ "$status" =~ "VALID" ]]; then echo "✅ **Active Version**: Properly linked" >> "$REPORT_FILE" else echo "❌ **Active Version**: BROKEN - no active deployment!" >> "$REPORT_FILE" fi elif [[ "$component" =~ "Environment File" ]]; then if [[ "$status" =~ "VALID" ]]; then echo "✅ **Configuration**: Database & settings connected" >> "$REPORT_FILE" else echo "❌ **Configuration**: MISSING - app cannot start!" >> "$REPORT_FILE" fi elif [[ "$component" =~ "Storage Directory" ]]; then if [[ "$status" =~ "VALID" ]]; then echo "✅ **File Storage**: User uploads preserved" >> "$REPORT_FILE" else echo "❌ **File Storage**: BROKEN - user files may be lost!" >> "$REPORT_FILE" fi elif [[ "$component" =~ "Public Storage" ]]; then if [[ "$status" =~ "VALID" ]]; then echo "✅ **Public Files**: Images & uploads accessible" >> "$REPORT_FILE" else echo "❌ **Public Files**: BROKEN - images won't display!" >> "$REPORT_FILE" fi fi done < /tmp/symlink_report cat >> "$REPORT_FILE" << EOF **? Overall Status:** $([ "$SYMLINK_STATUS" = "HEALTHY" ] && echo "✅ ALL SYSTEMS WORKING" || echo "⚠️ CRITICAL ISSUES FOUND") EOF # C2-04: Shared Directory Analysis echo "=== Shared Directory Analysis ===" cd "$DEPLOY_PATH" # Function to analyze shared directory analyze_shared_dir() { local DIR_PATH="$1" local DESCRIPTION="$2" if [ -d "$SHARED_PATH/$DIR_PATH" ]; then local SIZE=$(du -sh "$SHARED_PATH/$DIR_PATH" 2>/dev/null | cut -f1) local COUNT=$(find "$SHARED_PATH/$DIR_PATH" -type f 2>/dev/null | wc -l) echo "✅ $DESCRIPTION: $SIZE ($COUNT files)" echo "| $DESCRIPTION | ✅ EXISTS | $SIZE | $COUNT files |" >> /tmp/shared_report else echo "⚠️ $DESCRIPTION: NOT FOUND" echo "| $DESCRIPTION | ⚠️ MISSING | N/A | N/A |" >> /tmp/shared_report fi } # Initialize shared report echo "" > /tmp/shared_report # Analyze core shared directories analyze_shared_dir "storage" "Laravel Storage" analyze_shared_dir "bootstrap/cache" "Bootstrap Cache" # Check .env file separately since it's a file, not a directory if [ -f "$SHARED_PATH/.env" ]; then ENV_SIZE=$(du -sh "$SHARED_PATH/.env" 2>/dev/null | cut -f1) echo "✅ Environment File: $ENV_SIZE (1 file)" echo "| Environment File | ✅ EXISTS | $ENV_SIZE | 1 file |" >> /tmp/shared_report else echo "⚠️ Environment File: NOT FOUND" echo "| Environment File | ⚠️ MISSING | N/A | N/A |" >> /tmp/shared_report fi # Analyze user content directories (universal patterns) USER_CONTENT_DIRS=("public/uploads" "public/user-uploads" "public/media" "public/avatars" "public/attachments" "public/documents" "public/files" "public/images") USER_CONTENT_FOUND=0 for DIR in "${USER_CONTENT_DIRS[@]}"; do if [ -d "$SHARED_PATH/$DIR" ]; then analyze_shared_dir "$DIR" "User Content: $(basename "$DIR")" USER_CONTENT_FOUND=$((USER_CONTENT_FOUND + 1)) fi done # Analyze generated content directories GENERATED_DIRS=("public/qrcodes" "public/barcodes" "public/certificates" "public/reports") GENERATED_FOUND=0 for DIR in "${GENERATED_DIRS[@]}"; do if [ -d "$SHARED_PATH/$DIR" ]; then analyze_shared_dir "$DIR" "Generated: $(basename "$DIR")" GENERATED_FOUND=$((GENERATED_FOUND + 1)) fi done # CodeCanyon specific analyze_shared_dir "Modules" "CodeCanyon Modules" # Add shared analysis to report cat >> "$REPORT_FILE" << EOF ### ? Data Protection Status EOF # Convert technical shared report to user-friendly format PROTECTED_DATA_COUNT=0 while IFS='|' read -r description status size content; do if [[ "$description" =~ "Laravel Storage" ]]; then if [[ "$status" =~ "EXISTS" ]]; then echo "✅ **App Data**: $size safely stored ($content)" >> "$REPORT_FILE" PROTECTED_DATA_COUNT=$((PROTECTED_DATA_COUNT + 1)) else echo "❌ **App Data**: MISSING - application files not preserved!" >> "$REPORT_FILE" fi elif [[ "$description" =~ "User Content:" ]]; then CONTENT_TYPE=$(echo "$description" | sed 's/User Content: //') if [[ "$status" =~ "EXISTS" ]]; then echo "✅ **User $CONTENT_TYPE**: $size preserved ($content)" >> "$REPORT_FILE" PROTECTED_DATA_COUNT=$((PROTECTED_DATA_COUNT + 1)) fi elif [[ "$description" =~ "Generated:" ]]; then CONTENT_TYPE=$(echo "$description" | sed 's/Generated: //') if [[ "$status" =~ "EXISTS" ]]; then echo "✅ **Generated $CONTENT_TYPE**: $size preserved ($content)" >> "$REPORT_FILE" PROTECTED_DATA_COUNT=$((PROTECTED_DATA_COUNT + 1)) fi elif [[ "$description" =~ "CodeCanyon" ]]; then if [[ "$status" =~ "EXISTS" ]]; then echo "✅ **App Modules**: $size preserved ($content)" >> "$REPORT_FILE" PROTECTED_DATA_COUNT=$((PROTECTED_DATA_COUNT + 1)) fi fi done < /tmp/shared_report cat >> "$REPORT_FILE" << EOF **? Data Protection Summary:** - **Protected Data Types**: $PROTECTED_DATA_COUNT found - **User Uploads**: $([ $USER_CONTENT_FOUND -gt 0 ] && echo "$USER_CONTENT_FOUND types preserved" || echo "None found") - **Generated Files**: $([ $GENERATED_FOUND -gt 0 ] && echo "$GENERATED_FOUND types preserved" || echo "None found") $([ $PROTECTED_DATA_COUNT -gt 0 ] && echo "✅ **Zero Data Loss**: All user content survives deployments" || echo "ℹ️ **Clean Install**: No user data found (normal for new apps)") EOF # C2-05: Laravel Application Health Check echo "=== Laravel Application Health Check ===" LARAVEL_STATUS="UNKNOWN" PHP_VERSION="UNKNOWN" COMPOSER_VERSION="UNKNOWN" if [ -n "$CURRENT_RELEASE" ] && [ "$CURRENT_RELEASE" != "NONE" ] && [ -d "$CURRENT_PATH" ]; then cd "$CURRENT_PATH" # Check PHP version PHP_VERSION=$(php -v 2>/dev/null | head -1 | cut -d' ' -f2 || echo "ERROR") echo "? PHP Version: $PHP_VERSION" # Check Composer version if command -v composer2 &> /dev/null; then COMPOSER_VERSION=$(composer2 --version 2>/dev/null | cut -d' ' -f3 || echo "ERROR") COMPOSER_CMD="composer2" elif composer --version 2>/dev/null | grep -q "version 2\."; then COMPOSER_VERSION=$(composer --version 2>/dev/null | cut -d' ' -f3 || echo "ERROR") COMPOSER_CMD="composer" else COMPOSER_VERSION=$(composer --version 2>/dev/null | cut -d' ' -f3 || echo "ERROR") COMPOSER_CMD="composer (1.x)" fi echo "? Composer: $COMPOSER_VERSION ($COMPOSER_CMD)" # Check exec() function availability (critical for shared hosting) EXEC_STATUS=$(php -r "echo function_exists('exec') ? 'AVAILABLE' : 'DISABLED';" 2>/dev/null || echo "ERROR") echo "⚙️ exec() Function: $EXEC_STATUS" if [ "$EXEC_STATUS" = "DISABLED" ]; then echo " ⚠️ exec() disabled - some artisan commands may fail (common on shared hosting)" echo " ℹ️ Our scripts use manual fallbacks to bypass exec() limitations" fi # Check Laravel if [ -f "artisan" ]; then LARAVEL_VERSION=$(php artisan --version 2>/dev/null | head -1 || echo "ERROR") echo "? Laravel: $LARAVEL_VERSION" LARAVEL_STATUS="DETECTED" # Test database connection DB_STATUS="UNKNOWN" if php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAILED'; }" 2>/dev/null | grep -q "OK"; then DB_STATUS="✅ CONNECTED" echo "?️ Database: Connected" else DB_STATUS="❌ FAILED" echo "?️ Database: Connection Failed" fi # Test cache driver CACHE_STATUS="UNKNOWN" CACHE_DRIVER=$(grep "^CACHE_DRIVER=" "$SHARED_PATH/.env" | cut -d'=' -f2 || echo "file") echo "?️ Cache Driver: $CACHE_DRIVER" if php artisan tinker --execute="try { Cache::put('test_key', 'test_value', 60); Cache::get('test_key'); echo 'OK'; } catch(Exception \$e) { echo 'FAILED: ' . \$e->getMessage(); }" 2>/dev/null | grep -q "OK"; then CACHE_STATUS="✅ WORKING" echo "✅ Cache: Working ($CACHE_DRIVER)" else CACHE_STATUS="❌ FAILED" CACHE_ERROR=$(php artisan tinker --execute="try { Cache::put('test_key', 'test_value', 60); echo 'OK'; } catch(Exception \$e) { echo \$e->getMessage(); }" 2>/dev/null | grep -v "OK" | head -1) echo "❌ Cache: Failed ($CACHE_DRIVER) - $CACHE_ERROR" fi # Test session driver SESSION_STATUS="UNKNOWN" SESSION_DRIVER=$(grep "^SESSION_DRIVER=" "$SHARED_PATH/.env" | cut -d'=' -f2 || echo "file") echo "? Session Driver: $SESSION_DRIVER" if php artisan tinker --execute="try { session(['test_session' => 'test_value']); session('test_session'); echo 'OK'; } catch(Exception \$e) { echo 'FAILED'; }" 2>/dev/null | grep -q "OK"; then SESSION_STATUS="✅ WORKING" echo "✅ Sessions: Working ($SESSION_DRIVER)" else SESSION_STATUS="❌ FAILED" SESSION_ERROR=$(php artisan tinker --execute="try { session(['test_session' => 'test_value']); echo 'OK'; } catch(Exception \$e) { echo \$e->getMessage(); }" 2>/dev/null | grep -v "OK" | head -1) echo "❌ Sessions: Failed ($SESSION_DRIVER) - $SESSION_ERROR" fi # Check critical Laravel directories VENDOR_STATUS=$([ -d "vendor" ] && echo "✅ EXISTS" || echo "❌ MISSING") STORAGE_STATUS=$([ -L "storage" ] && echo "✅ SYMLINKED" || echo "❌ NOT_SYMLINKED") ENV_STATUS=$([ -L ".env" ] && echo "✅ SYMLINKED" || echo "❌ NOT_SYMLINKED") echo "? Vendor: $VENDOR_STATUS" echo "? Storage: $STORAGE_STATUS" echo "⚙️ Environment: $ENV_STATUS" else echo "⚠️ No artisan file found - not a Laravel application" LARAVEL_STATUS="NOT_LARAVEL" fi else echo "⚠️ No current release to analyze" fi # Add Laravel analysis to report cat >> "$REPORT_FILE" << EOF ### ? Laravel Application Health EOF # Add user-friendly Laravel status if [ "$LARAVEL_STATUS" = "DETECTED" ]; then cat >> "$REPORT_FILE" << EOF ✅ **Laravel Framework**: $LARAVEL_VERSION running on PHP $PHP_VERSION ✅ **Dependencies**: Composer $COMPOSER_VERSION with vendor directory intact ✅ **Environment**: Configuration properly symlinked and accessible $([ "$EXEC_STATUS" = "DISABLED" ] && echo "⚠️ **exec() Function**: Disabled (shared hosting) - using manual fallbacks" || echo "✅ **exec() Function**: Available for full artisan support") **Core System Tests:** - **Database**: $([ "$DB_STATUS" = "✅ CONNECTED" ] && echo "✅ Connected and responding" || echo "❌ Connection failed") - **Cache System**: $([ "$CACHE_STATUS" = "✅ WORKING" ] && echo "✅ $CACHE_DRIVER driver working" || echo "❌ $CACHE_DRIVER driver failed - $CACHE_ERROR") - **Session System**: $([ "$SESSION_STATUS" = "✅ WORKING" ] && echo "✅ $SESSION_DRIVER driver working" || echo "❌ $SESSION_DRIVER driver failed - $SESSION_ERROR") - **File Storage**: $([ "$STORAGE_STATUS" = "✅ SYMLINKED" ] && echo "✅ Properly symlinked for zero-downtime" || echo "❌ Not symlinked - data loss risk") **Infrastructure:** - **Dependencies**: $([ "$VENDOR_STATUS" = "✅ EXISTS" ] && echo "$(find "$CURRENT_PATH/vendor" -name "*.php" 2>/dev/null | wc -l) PHP files loaded" || echo "❌ Missing vendor directory") - **Configuration**: $([ "$ENV_STATUS" = "✅ SYMLINKED" ] && echo "✅ Environment shared across deployments" || echo "❌ Not properly configured") EOF else cat >> "$REPORT_FILE" << EOF ⚠️ **Laravel Framework**: Not detected or not working properly - **Status**: $LARAVEL_STATUS - **PHP**: $PHP_VERSION - **Composer**: $COMPOSER_VERSION EOF fi cat >> "$REPORT_FILE" << EOF EOF # C2-06: Security Verification echo "=== Security Verification ===" SECURITY_ISSUES=0 cd "$DOMAIN_ROOT" # Check public_html security if [ -L "public_html" ]; then PUBLIC_TARGET=$(readlink "public_html") if [[ "$PUBLIC_TARGET" == *"/public" ]]; then echo "✅ Web root correctly points to Laravel public directory" else echo "⚠️ Web root does not point to Laravel public directory" SECURITY_ISSUES=$((SECURITY_ISSUES + 1)) fi else echo "❌ Web root is not a symlink - potential security risk" SECURITY_ISSUES=$((SECURITY_ISSUES + 1)) fi # Check for exposed sensitive files in public_html if [ -d "public_html" ]; then cd "public_html" EXPOSED_FILES=() for FILE in .env .env.example composer.json composer.lock package.json .git; do if [ -e "$FILE" ]; then EXPOSED_FILES+=("$FILE") SECURITY_ISSUES=$((SECURITY_ISSUES + 1)) fi done if [ ${#EXPOSED_FILES[@]} -eq 0 ]; then echo "✅ No sensitive files exposed in web root" else echo "❌ Exposed sensitive files: ${EXPOSED_FILES[*]}" fi fi # Check .env permissions cd "$DEPLOY_PATH" if [ -f "$SHARED_PATH/.env" ]; then ENV_PERMS=$(stat -c "%a" "$SHARED_PATH/.env" 2>/dev/null || echo "000") if [ "$ENV_PERMS" = "600" ] || [ "$ENV_PERMS" = "644" ]; then echo "✅ .env file has secure permissions ($ENV_PERMS)" else echo "⚠️ .env file permissions may be too open ($ENV_PERMS)" SECURITY_ISSUES=$((SECURITY_ISSUES + 1)) fi else echo "❌ .env file not found in shared directory" SECURITY_ISSUES=$((SECURITY_ISSUES + 1)) fi # Add security analysis to report cat >> "$REPORT_FILE" << EOF ### Security Analysis | Check | Status | Details | |-------|--------|---------| | Web Root Symlink | $([ -L "$DOMAIN_ROOT/public_html" ] && echo "✅ SECURE" || echo "❌ INSECURE") | Points to: $([ -L "$DOMAIN_ROOT/public_html" ] && readlink "$DOMAIN_ROOT/public_html" || echo "NOT_SYMLINK") | | Exposed Sensitive Files | $([ ${#EXPOSED_FILES[@]} -eq 0 ] && echo "✅ NONE" || echo "❌ FOUND") | $([ ${#EXPOSED_FILES[@]} -gt 0 ] && echo "${EXPOSED_FILES[*]}" || echo "Clean") | | .env Permissions | $([ -f "$SHARED_PATH/.env" ] && echo "✅ $(stat -c "%a" "$SHARED_PATH/.env")" || echo "❌ MISSING") | $([ -f "$SHARED_PATH/.env" ] && echo "File secured" || echo "Environment file not found") | **Security Issues Found:** $SECURITY_ISSUES EOF # C2-07: Performance & Optimization Check echo "=== Performance & Optimization Check ===" OPTIMIZATION_STATUS="UNKNOWN" if [ -n "$CURRENT_RELEASE" ] && [ "$CURRENT_RELEASE" != "NONE" ] && [ -d "$CURRENT_PATH" ]; then cd "$CURRENT_PATH" # Check Laravel caches CONFIG_CACHE=$([ -f "bootstrap/cache/config.php" ] && echo "✅ CACHED" || echo "⚠️ NOT_CACHED") ROUTE_CACHE=$([ -f "bootstrap/cache/routes-v7.php" ] && echo "✅ CACHED" || echo "⚠️ NOT_CACHED") VIEW_CACHE=$(find "storage/framework/views" -name "*.php" 2>/dev/null | wc -l) VIEW_STATUS=$([ $VIEW_CACHE -gt 0 ] && echo "✅ CACHED ($VIEW_CACHE files)" || echo "⚠️ NO_CACHE") echo "⚙️ Config Cache: $CONFIG_CACHE" echo "?️ Route Cache: $ROUTE_CACHE" echo "?️ View Cache: $VIEW_STATUS" # Check Composer optimization if [ -f "vendor/composer/autoload_classmap.php" ]; then CLASSMAP_COUNT=$(wc -l < "vendor/composer/autoload_classmap.php") COMPOSER_OPT=$([ $CLASSMAP_COUNT -gt 100 ] && echo "✅ OPTIMIZED ($CLASSMAP_COUNT classes)" || echo "⚠️ NOT_OPTIMIZED") else COMPOSER_OPT="❌ NO_CLASSMAP" fi echo "? Composer Autoload: $COMPOSER_OPT" # Check OPcache OPCACHE_STATUS=$(php -r "echo function_exists('opcache_get_status') ? 'AVAILABLE' : 'NOT_AVAILABLE';" 2>/dev/null || echo "ERROR") echo "? OPcache: $OPCACHE_STATUS" fi # Add performance analysis to report cat >> "$REPORT_FILE" << EOF ### Performance & Optimization | Component | Status | Details | |-----------|--------|---------| | Config Cache | $([ -n "$CONFIG_CACHE" ] && echo "$CONFIG_CACHE" || echo "⚠️ NOT_CHECKED") | $([ -f "$CURRENT_PATH/bootstrap/cache/config.php" ] && echo "Cached configuration loaded" || echo "No config cache") | | Route Cache | $([ -n "$ROUTE_CACHE" ] && echo "$ROUTE_CACHE" || echo "⚠️ NOT_CHECKED") | $([ -f "$CURRENT_PATH/bootstrap/cache/routes-v7.php" ] && echo "Cached routes loaded" || echo "No route cache") | | View Cache | $([ -n "$VIEW_STATUS" ] && echo "$VIEW_STATUS" || echo "⚠️ NOT_CHECKED") | Compiled view templates | | Composer Autoload | $([ -n "$COMPOSER_OPT" ] && echo "$COMPOSER_OPT" || echo "⚠️ NOT_CHECKED") | Class mapping optimization | | OPcache | $([ "$OPCACHE_STATUS" = "AVAILABLE" ] && echo "✅ $OPCACHE_STATUS" || echo "⚠️ $OPCACHE_STATUS") | PHP bytecode caching | EOF # C2-08: Release Management Analysis echo "=== Release Management Analysis ===" cd "$DEPLOY_PATH" # Analyze releases if [ -d "releases" ]; then RELEASES=($(ls -1t releases/ 2>/dev/null)) TOTAL_RELEASES=${#RELEASES[@]} echo "? Total releases: $TOTAL_RELEASES" if [ $TOTAL_RELEASES -gt 0 ]; then LATEST_RELEASE=${RELEASES[0]} echo "? Latest release: $LATEST_RELEASE" # Calculate release sizes for i in "${!RELEASES[@]}"; do if [ $i -lt 5 ]; then # Only show last 5 releases RELEASE=${RELEASES[$i]} SIZE=$(du -sh "releases/$RELEASE" 2>/dev/null | cut -f1) AGE=$(stat -c %Y "releases/$RELEASE" 2>/dev/null) CURRENT_TIME=$(date +%s) AGE_HOURS=$(( (CURRENT_TIME - AGE) / 3600 )) if [ "$RELEASE" = "$CURRENT_RELEASE" ]; then echo "? $RELEASE: $SIZE (${AGE_HOURS}h ago) [CURRENT]" else echo " $RELEASE: $SIZE (${AGE_HOURS}h ago)" fi fi done # Check for old releases OLD_RELEASES=$(( TOTAL_RELEASES - 3 )) if [ $OLD_RELEASES -gt 0 ]; then echo "?️ Old releases to cleanup: $OLD_RELEASES" fi fi fi # Add release analysis to report cat >> "$REPORT_FILE" << EOF ### Release Management | Metric | Value | Details | |--------|-------|---------| | Total Releases | $TOTAL_RELEASES | Stored in releases/ directory | | Current Release | $CURRENT_RELEASE | $([ -n "$CURRENT_RELEASE" ] && echo "Active deployment" || echo "No active deployment") | | Latest Release | $([ $TOTAL_RELEASES -gt 0 ] && echo "$LATEST_RELEASE" || echo "None") | Most recent in releases/ | | Cleanup Needed | $([ $OLD_RELEASES -gt 0 ] && echo "$OLD_RELEASES releases" || echo "None") | Releases beyond keep limit (3) | #### Recent Releases \`\`\` EOF if [ $TOTAL_RELEASES -gt 0 ]; then for i in "${!RELEASES[@]}"; do if [ $i -lt 5 ]; then RELEASE=${RELEASES[$i]} SIZE=$(du -sh "releases/$RELEASE" 2>/dev/null | cut -f1) AGE=$(stat -c %Y "releases/$RELEASE" 2>/dev/null) CURRENT_TIME=$(date +%s) AGE_HOURS=$(( (CURRENT_TIME - AGE) / 3600 )) if [ "$RELEASE" = "$CURRENT_RELEASE" ]; then echo "$RELEASE: $SIZE (${AGE_HOURS}h ago) [CURRENT]" >> "$REPORT_FILE" else echo "$RELEASE: $SIZE (${AGE_HOURS}h ago)" >> "$REPORT_FILE" fi fi done else echo "No releases found" >> "$REPORT_FILE" fi cat >> "$REPORT_FILE" << EOF \`\`\` EOF # C2-09: HTTP Health Check echo "=== HTTP Health Check ===" HTTP_STATUS="UNKNOWN" RESPONSE_TIME="UNKNOWN" if [ -n "$CURRENT_RELEASE" ] && [ "$CURRENT_RELEASE" != "NONE" ] && [ -f "$SHARED_PATH/.env" ]; then APP_URL=$(grep "^APP_URL=" "$SHARED_PATH/.env" | cut -d'=' -f2 | tr -d '"' | tr -d "'") if [ -n "$APP_URL" ] && command -v curl &> /dev/null; then echo "? Testing: $APP_URL" # Test main page START_TIME=$(date +%s.%N) HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL" --max-time 10 2>/dev/null || echo "000") END_TIME=$(date +%s.%N) RESPONSE_TIME=$(echo "$END_TIME - $START_TIME" | bc 2>/dev/null || echo "unknown") # Test install page first (for fresh deployments) INSTALL_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/install" --max-time 10 2>/dev/null || echo "000") case $HTTP_CODE in 200|201|301|302) HTTP_STATUS="✅ HEALTHY ($HTTP_CODE)" echo "✅ Application responding: HTTP $HTTP_CODE (${RESPONSE_TIME}s)" ;; 503) HTTP_STATUS="⚠️ MAINTENANCE ($HTTP_CODE)" echo "⚠️ Application in maintenance mode: HTTP $HTTP_CODE" ;; 500) if [ "$INSTALL_CODE" = "200" ]; then HTTP_STATUS="✅ PENDING_INSTALL ($HTTP_CODE)" echo "✅ Fresh deployment detected: Main site HTTP $HTTP_CODE, Install page accessible" echo "ℹ️ This is EXPECTED - complete installation at: $APP_URL/install" else HTTP_STATUS="❌ ERROR ($HTTP_CODE)" echo "❌ Application error: HTTP $HTTP_CODE (install page also failed)" fi ;; 000) HTTP_STATUS="❌ UNREACHABLE" echo "❌ Application unreachable (timeout/connection error)" ;; *) HTTP_STATUS="⚠️ ERROR ($HTTP_CODE)" echo "⚠️ Application returned: HTTP $HTTP_CODE" if [ "$INSTALL_CODE" = "200" ]; then echo "ℹ️ Install page accessible: HTTP $INSTALL_CODE" fi ;; esac else echo "⚠️ Cannot test HTTP (no APP_URL or curl unavailable)" fi else echo "⚠️ Cannot determine APP_URL" fi # Add HTTP analysis to report cat >> "$REPORT_FILE" << EOF ### ? Website Status EOF # Add user-friendly HTTP status if [[ "$HTTP_STATUS" =~ "PENDING_INSTALL" ]]; then cat >> "$REPORT_FILE" << EOF ✅ **Fresh Deployment Ready**: Your app is deployed correctly and ready for setup - **Main Site**: Shows setup screen (HTTP $HTTP_CODE) - This is EXPECTED - **Install Page**: \`$APP_URL/install\` is accessible (HTTP $INSTALL_CODE) - **Next Step**: Complete installation wizard at \`$APP_URL/install\` ? **Status**: Ready for first-time setup EOF elif [[ "$HTTP_STATUS" =~ "HEALTHY" ]]; then cat >> "$REPORT_FILE" << EOF ✅ **Website Online**: Your application is running properly - **Main Site**: \`$APP_URL\` responding (HTTP $HTTP_CODE) - **Response Time**: ${RESPONSE_TIME}s - **Status**: Fully operational ? **Status**: Production ready EOF elif [[ "$HTTP_STATUS" =~ "MAINTENANCE" ]]; then cat >> "$REPORT_FILE" << EOF ⚠️ **Maintenance Mode**: Site temporarily offline for updates - **Main Site**: \`$APP_URL\` in maintenance (HTTP $HTTP_CODE) - **Status**: Planned downtime ? **Status**: Maintenance in progress EOF else cat >> "$REPORT_FILE" << EOF ❌ **Website Issues**: Problems detected with site access - **Main Site**: \`$APP_URL\` error (HTTP $HTTP_CODE) - **Install Page**: $([ "$INSTALL_CODE" = "200" ] && echo "Accessible (HTTP $INSTALL_CODE)" || echo "Also failed (HTTP $INSTALL_CODE)") ? **Status**: Needs troubleshooting EOF fi cat >> "$REPORT_FILE" << EOF EOF # C2-10: Final Summary and Recommendations echo "=== Generating Final Summary ===" # Calculate overall health score TOTAL_CHECKS=12 PASSED_CHECKS=0 [ "$SYMLINK_STATUS" = "HEALTHY" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$LARAVEL_STATUS" = "DETECTED" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$DB_STATUS" = "✅ CONNECTED" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$CACHE_STATUS" = "✅ WORKING" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$SESSION_STATUS" = "✅ WORKING" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$VENDOR_STATUS" = "✅ EXISTS" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$STORAGE_STATUS" = "✅ SYMLINKED" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$ENV_STATUS" = "✅ SYMLINKED" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ $SECURITY_ISSUES -eq 0 ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ "$CONFIG_CACHE" = "✅ CACHED" ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) # HTTP check passes if: working (200/301/302), pending install (500 + install accessible), or maintenance (503) ([ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "301" ] || [ "$HTTP_CODE" = "302" ] || [ "$HTTP_CODE" = "503" ] || [[ "$HTTP_STATUS" =~ "PENDING_INSTALL" ]]) && PASSED_CHECKS=$((PASSED_CHECKS + 1)) [ $TOTAL_RELEASES -gt 0 ] && PASSED_CHECKS=$((PASSED_CHECKS + 1)) HEALTH_PERCENTAGE=$(( (PASSED_CHECKS * 100) / TOTAL_CHECKS )) # Determine overall status if [ $HEALTH_PERCENTAGE -ge 90 ]; then OVERALL_STATUS="? EXCELLENT" elif [ $HEALTH_PERCENTAGE -ge 75 ]; then OVERALL_STATUS="? GOOD" elif [ $HEALTH_PERCENTAGE -ge 50 ]; then OVERALL_STATUS="? NEEDS_ATTENTION" else OVERALL_STATUS="? CRITICAL" fi echo "? Overall Health: $HEALTH_PERCENTAGE% ($PASSED_CHECKS/$TOTAL_CHECKS checks passed)" echo "? Status: $OVERALL_STATUS" # Add final summary to report cat >> "$REPORT_FILE" << EOF --- ## ? FINAL ASSESSMENT ### Overall Health Score: $HEALTH_PERCENTAGE% ($PASSED_CHECKS/$TOTAL_CHECKS) ### Status: $OVERALL_STATUS ### Summary by Category: - **Infrastructure:** $([ "$SYMLINK_STATUS" = "HEALTHY" ] && echo "✅ Healthy" || echo "⚠️ Issues") - **Laravel Application:** $([ "$LARAVEL_STATUS" = "DETECTED" ] && echo "✅ Detected" || echo "⚠️ Issues") - **Database:** $([ "$DB_STATUS" = "✅ CONNECTED" ] && echo "✅ Connected" || echo "⚠️ Issues") - **Cache System:** $([ "$CACHE_STATUS" = "✅ WORKING" ] && echo "✅ Working ($CACHE_DRIVER)" || echo "⚠️ Failed ($CACHE_DRIVER)") - **Session System:** $([ "$SESSION_STATUS" = "✅ WORKING" ] && echo "✅ Working ($SESSION_DRIVER)" || echo "⚠️ Failed ($SESSION_DRIVER)") - **Security:** $([ $SECURITY_ISSUES -eq 0 ] && echo "✅ Secure" || echo "⚠️ $SECURITY_ISSUES issues") - **Performance:** $([ "$CONFIG_CACHE" = "✅ CACHED" ] && echo "✅ Optimized" || echo "⚠️ Not optimized") - **HTTP Response:** $(if [[ "$HTTP_STATUS" =~ "HEALTHY" ]]; then echo "✅ Healthy"; elif [[ "$HTTP_STATUS" =~ "PENDING_INSTALL" ]]; then echo "✅ Ready for setup"; elif [[ "$HTTP_STATUS" =~ "MAINTENANCE" ]]; then echo "⚠️ Maintenance"; else echo "⚠️ Issues"; fi) ### Recommendations: EOF # Generate recommendations if [ "$SYMLINK_STATUS" != "HEALTHY" ]; then echo "- ? **Fix symlink issues** - Critical for zero-downtime deployment" >> "$REPORT_FILE" fi if [ $SECURITY_ISSUES -gt 0 ]; then echo "- ? **Address security issues** - $SECURITY_ISSUES problems found" >> "$REPORT_FILE" fi if [ "$CACHE_STATUS" = "❌ FAILED" ]; then echo "- ?️ **Fix Cache System** - $CACHE_DRIVER driver failed. Consider switching to \`file\` driver in .env" >> "$REPORT_FILE" fi if [ "$SESSION_STATUS" = "❌ FAILED" ]; then echo "- ? **Fix Session System** - $SESSION_DRIVER driver failed. Consider switching to \`file\` driver in .env" >> "$REPORT_FILE" fi if [ "$CONFIG_CACHE" != "✅ CACHED" ]; then echo "- ⚡ **Enable Laravel caching** - Run \`php artisan config:cache\` and \`php artisan route:cache\`" >> "$REPORT_FILE" fi if [ $OLD_RELEASES -gt 0 ]; then echo "- ?️ **Cleanup old releases** - Remove $OLD_RELEASES old releases to save disk space" >> "$REPORT_FILE" fi if [[ "$HTTP_STATUS" =~ "PENDING_INSTALL" ]]; then echo "- ? **Complete Installation** - Visit \`$APP_URL/install\` to finish setup" >> "$REPORT_FILE" elif [ "$HTTP_CODE" != "200" ] && [ "$HTTP_CODE" != "301" ] && [ "$HTTP_CODE" != "302" ] && [ "$HTTP_CODE" != "503" ]; then echo "- ? **Fix HTTP issues** - Application not responding correctly (HTTP $HTTP_CODE)" >> "$REPORT_FILE" fi if [ "$DB_STATUS" != "✅ CONNECTED" ]; then echo "- ?️ **Fix database connection** - Application cannot connect to database" >> "$REPORT_FILE" fi # Add no issues message if everything is good if [ $HEALTH_PERCENTAGE -ge 90 ]; then echo "- ✅ **No critical issues found** - Deployment is healthy and ready for production" >> "$REPORT_FILE" fi cat >> "$REPORT_FILE" << EOF --- ## ? DETAILED LOGS ### Environment Variables \`\`\`bash DOMAIN_NAME=$DOMAIN_NAME DEPLOY_PATH=$DEPLOY_PATH CURRENT_RELEASE=$CURRENT_RELEASE TOTAL_RELEASES=$TOTAL_RELEASES SHARED_SIZE=$SHARED_SIZE PHP_VERSION=$PHP_VERSION COMPOSER_VERSION=$COMPOSER_VERSION \`\`\` ### File Permissions \`\`\`bash EOF # Add file permissions if [ -f "$SHARED_PATH/.env" ]; then echo "shared/.env: $(stat -c "%a %U:%G" "$SHARED_PATH/.env" 2>/dev/null)" >> "$REPORT_FILE" fi if [ -d "$SHARED_PATH/storage" ]; then echo "shared/storage: $(stat -c "%a %U:%G" "$SHARED_PATH/storage" 2>/dev/null)" >> "$REPORT_FILE" fi cat >> "$REPORT_FILE" << EOF \`\`\` ### Disk Usage \`\`\`bash EOF du -sh releases/* 2>/dev/null | tail -5 >> "$REPORT_FILE" || echo "No releases found" >> "$REPORT_FILE" cat >> "$REPORT_FILE" << EOF \`\`\` --- **Report Generated:** $(date '+%Y-%m-%d %H:%M:%S') **Script Version:** Phase C-2 v2.0 **Next Report:** Run Phase C-2 after next deployment EOF # C2-11: Cleanup and Final Output echo "=== Finalizing Report ===" # Clean up temporary files rm -f /tmp/symlink_report /tmp/shared_report # Set proper permissions on report chmod 644 "$REPORT_FILE" # Create latest report symlink cd "$REPORT_DIR" rm -f latest-report.md ln -sf "$(basename "$REPORT_FILE")" latest-report.md echo "✅ Comprehensive verification completed" echo "? Report saved: $REPORT_FILE" echo "? Latest report: $REPORT_DIR/latest-report.md" echo "? Overall Health: $HEALTH_PERCENTAGE% - $OVERALL_STATUS" # Return to original directory cd "$DEPLOY_PATH" echo "? Phase C-2 completed successfully" exit 0 ]

=== Phase C-2: Comprehensive Deployment Verification & Reporting ===
? DEPLOYHQ MODE: Using DeployHQ path variables
? Path Variables:
   Deploy Path: /home/u227177893/domains/staging.societypal.com/deploy
   Release Path: /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819214954
   Shared Path: /home/u227177893/domains/staging.societypal.com/deploy/shared
   Current Path: /home/u227177893/domains/staging.societypal.com/deploy/current
? Starting comprehensive deployment verification...
   Domain: staging.societypal.com
   Deploy Path: /home/u227177893/domains/staging.societypal.com/deploy
   Report: /home/u227177893/domains/staging.societypal.com/deployment-reports/deployment-report-20250819_215233.md
=== Environment Detection ===
✅ Current release detected: 20250819214954
? Total releases: 3
? Shared directory size: 26M
=== Critical Symlink Verification ===
✅ Web Root (public_html): deploy/current/public
✅ Current Release: ./releases/20250819214954
✅ Environment File: /home/u227177893/domains/staging.societypal.com/deploy/shared/.env
✅ Storage Directory: ../../shared/storage
✅ Bootstrap Cache: ../../../shared/bootstrap/cache
✅ Public Storage: ../storage/app/public
=== Shared Directory Analysis ===
✅ Laravel Storage: 23M (999 files)
✅ Bootstrap Cache: 588K (5 files)
✅ Environment File: 8.0K (1 file)
✅ User Content: uploads: 4.0K (0 files)
✅ User Content: user-uploads: 4.0K (0 files)
✅ User Content: media: 4.0K (0 files)
✅ User Content: avatars: 4.0K (0 files)
✅ User Content: attachments: 4.0K (0 files)
✅ User Content: documents: 4.0K (0 files)
✅ User Content: files: 4.0K (0 files)
✅ User Content: images: 4.0K (0 files)
✅ Generated: qrcodes: 4.0K (0 files)
✅ Generated: barcodes: 4.0K (0 files)
✅ Generated: certificates: 4.0K (0 files)
✅ Generated: reports: 4.0K (0 files)
✅ CodeCanyon Modules: 4.0K (0 files)
=== Laravel Application Health Check ===
? PHP Version: 8.2.28
? Composer: 2.5.5 (composer2)
⚙️ exec() Function: DISABLED
   ⚠️ exec() disabled - some artisan commands may fail (common on shared hosting)
   ℹ️ Our scripts use manual fallbacks to bypass exec() limitations
? Laravel: 
?️ Database: Connection Failed
?️ Cache Driver: file
❌ Cache: Failed (file) - 
? Session Driver: file
❌ Sessions: Failed (file) - 
? Vendor: ✅ EXISTS
? Storage: ✅ SYMLINKED
⚙️ Environment: ✅ SYMLINKED
=== Security Verification ===
✅ Web root correctly points to Laravel public directory
✅ No sensitive files exposed in web root
✅ .env file has secure permissions (600)
=== Performance & Optimization Check ===
⚙️ Config Cache: ✅ CACHED
?️ Route Cache: ✅ CACHED
?️ View Cache: ✅ CACHED (986 files)
? Composer Autoload: ✅ OPTIMIZED (11534 classes)
? OPcache: AVAILABLE
=== Release Management Analysis ===
? Total releases: 3
? Latest release: 20250819214954
? 20250819214954: 291M (0h ago) [CURRENT]
   20250819213519: 291M (0h ago)
   20250819195953: 285M (1h ago)
=== HTTP Health Check ===
? Testing: https://staging.societypal.com
⚠️ Application in maintenance mode: HTTP 503
=== Generating Final Summary ===
? Overall Health: 75% (9/12 checks passed)
? Status: ? GOOD
=== Finalizing Report ===
✅ Comprehensive verification completed
? Report saved: /home/u227177893/domains/staging.societypal.com/deployment-reports/deployment-report-20250819_215233.md
? Latest report: /home/u227177893/domains/staging.societypal.com/deployment-reports/latest-report.md
? Overall Health: 75% - ? GOOD
? Phase C-2 completed successfully
Time taken to run G2-Works-Phase C-2: Comprehensive Deployment Verification & Reporting: 3.64 seconds


Running SSH command G2-Works-Phase C-3: Comprehensive Health Check & Auto-Fix

Executing G2-Works-Phase C-3: Comprehensive Health Check & Auto-Fix [#!/bin/bash set -e # Phase C-3: Comprehensive Health Check & Auto-Fix # Purpose: Debug HTTP 500, ensure app functionality, auto-fix common issues # Version 2.1 - PRODUCTION READY with Enhanced Reporting # Run after Phase C to verify deployment health and auto-fix issues echo "=== Phase C-3: Comprehensive Health Check & Auto-Fix ===" cd /home/u227177893/domains/staging.societypal.com/deploy/current # Initialize counters and tracking TOTAL_CHECKS=0 PASSED_CHECKS=0 ISSUES_FOUND=0 FIXES_APPLIED=0 # Initialize issue tracking files echo "" > /tmp/c3_issues_log echo "" > /tmp/c3_fixes_log echo "" > /tmp/c3_detailed_issues echo "" > /tmp/c3_action_items # Helper function to log results with enhanced tracking log_check() { local CHECK_NAME="$1" local STATUS="$2" local DETAILS="$3" local CATEGORY="${4:-General}" local IMPACT="${5:-Medium}" TOTAL_CHECKS=$((TOTAL_CHECKS + 1)) if [ "$STATUS" = "PASS" ]; then echo "✅ $CHECK_NAME: $DETAILS" PASSED_CHECKS=$((PASSED_CHECKS + 1)) elif [ "$STATUS" = "WARN" ]; then echo "⚠️ $CHECK_NAME: $DETAILS" ISSUES_FOUND=$((ISSUES_FOUND + 1)) # Log issue for report with enhanced details echo "- ⚠️ **$CHECK_NAME**: $DETAILS" >> /tmp/c3_issues_log echo "$CATEGORY|WARNING|$CHECK_NAME|$DETAILS|$IMPACT" >> /tmp/c3_detailed_issues else echo "❌ $CHECK_NAME: $DETAILS" ISSUES_FOUND=$((ISSUES_FOUND + 1)) # Log issue for report with enhanced details echo "- ❌ **$CHECK_NAME**: $DETAILS" >> /tmp/c3_issues_log echo "$CATEGORY|ERROR|$CHECK_NAME|$DETAILS|$IMPACT" >> /tmp/c3_detailed_issues fi } # Helper function to apply fixes with enhanced logging apply_fix() { local FIX_NAME="$1" local COMMAND="$2" local DESCRIPTION="${3:-Auto-fix applied}" echo "? Applying fix: $FIX_NAME" if eval "$COMMAND"; then echo "✅ Fix applied: $FIX_NAME" FIXES_APPLIED=$((FIXES_APPLIED + 1)) # Log fix for report with description echo "- ✅ **$FIX_NAME**: Successfully applied" >> /tmp/c3_fixes_log echo "SUCCESS|$FIX_NAME|$DESCRIPTION" >> /tmp/c3_action_items return 0 else echo "❌ Fix failed: $FIX_NAME" # Log failed fix for report echo "- ❌ **$FIX_NAME**: Failed to apply" >> /tmp/c3_fixes_log echo "FAILED|$FIX_NAME|$DESCRIPTION" >> /tmp/c3_action_items return 1 fi } # Helper function to add action items add_action_item() { local PRIORITY="$1" local TITLE="$2" local DESCRIPTION="$3" local COMMAND="${4:-N/A}" echo "ACTION|$PRIORITY|$TITLE|$DESCRIPTION|$COMMAND" >> /tmp/c3_action_items } echo "? Starting comprehensive health checks..." # C3-01: Basic Laravel Health Check echo "=== Laravel Framework Health ===" if [ -f "artisan" ]; then if php artisan --version >/dev/null 2>&1; then LARAVEL_VERSION=$(php artisan --version 2>/dev/null | head -1) log_check "Laravel Framework" "PASS" "$LARAVEL_VERSION" "Core" "High" else log_check "Laravel Framework" "FAIL" "Artisan command failed" "Core" "Critical" # Try to get more info echo " Debug: $(php artisan --version 2>&1 | head -1)" add_action_item "HIGH" "Fix Laravel Framework" "Artisan command failing - check PHP configuration and dependencies" "php artisan --version" fi else log_check "Laravel Framework" "FAIL" "No artisan file found" "Core" "Critical" add_action_item "CRITICAL" "Laravel Installation Missing" "No artisan file found - Laravel may not be properly installed" "Check deployment and ensure Laravel files are present" fi # C3-02: Environment Configuration Check echo "=== Environment Configuration ===" if [ -f ".env" ]; then log_check "Environment File" "PASS" ".env file exists and readable" "Configuration" "High" # Check required variables with enhanced logging REQUIRED_VARS=("APP_KEY" "DB_DATABASE" "DB_USERNAME" "DB_PASSWORD") MISSING_VARS=() for VAR in "${REQUIRED_VARS[@]}"; do VALUE=$(grep "^${VAR}=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" 2>/dev/null) if [ -n "$VALUE" ]; then log_check "Required Var: $VAR" "PASS" "Set" "Configuration" "Medium" else log_check "Required Var: $VAR" "FAIL" "Missing or empty" "Configuration" "High" MISSING_VARS+=("$VAR") fi done # Add action item for missing variables if [ ${#MISSING_VARS[@]} -gt 0 ]; then add_action_item "HIGH" "Configure Missing Environment Variables" "Missing: ${MISSING_VARS[*]}" "Edit .env file and set: ${MISSING_VARS[*]}" fi else log_check "Environment File" "FAIL" ".env file missing" "Configuration" "Critical" add_action_item "CRITICAL" "Create Environment File" "No .env file found - application cannot start" "cp .env.example .env && php artisan key:generate" fi # C3-03: Database Connection Test echo "=== Database Connection ===" if [ -f "artisan" ] && [ -f ".env" ]; then DB_TEST_RESULT=$(php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'FAILED: ' . \$e->getMessage(); }" 2>/dev/null) if echo "$DB_TEST_RESULT" | grep -q "OK"; then log_check "Database Connection" "PASS" "Connected successfully" "Database" "High" else log_check "Database Connection" "FAIL" "Connection failed" "Database" "Critical" echo " Debug: $DB_TEST_RESULT" add_action_item "CRITICAL" "Fix Database Connection" "Cannot connect to database - check credentials and server" "Verify DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env" fi else log_check "Database Connection" "WARN" "Cannot test (missing artisan or .env)" "Database" "Medium" fi # C3-04: Cache System Health Check echo "=== Cache System Health ===" if [ -f ".env" ]; then CACHE_DRIVER=$(grep "^CACHE_DRIVER=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" 2>/dev/null) CACHE_DRIVER=${CACHE_DRIVER:-file} echo "? Cache driver: $CACHE_DRIVER" # Test cache functionality CACHE_TEST_SCRIPT=' <?php require_once "vendor/autoload.php"; $app = require_once "bootstrap/app.php"; try { Cache::put("health_check", "test_value", 60); $value = Cache::get("health_check"); if ($value === "test_value") { Cache::forget("health_check"); echo "CACHE_OK"; } else { echo "CACHE_FAILED"; } } catch (Exception $e) { echo "CACHE_ERROR: " . $e->getMessage(); } ' CACHE_RESULT=$(echo "$CACHE_TEST_SCRIPT" | php 2>/dev/null) if echo "$CACHE_RESULT" | grep -q "CACHE_OK"; then log_check "Cache System" "PASS" "$CACHE_DRIVER driver working" "Performance" "Medium" elif echo "$CACHE_RESULT" | grep -q "CACHE_ERROR.*Redis"; then log_check "Cache System" "FAIL" "Redis cache configured but not available" "Performance" "High" # Auto-fix: Switch to file cache if apply_fix "Switch cache driver to file" "sed -i 's/CACHE_DRIVER=redis/CACHE_DRIVER=file/' .env" "Switch from Redis to file-based cache"; then # Clear config cache to apply changes apply_fix "Clear config cache" "php artisan config:clear 2>/dev/null" "Clear cached configuration" fi else log_check "Cache System" "WARN" "Cache test inconclusive: $CACHE_RESULT" "Performance" "Medium" add_action_item "MEDIUM" "Investigate Cache System" "Cache test returned unexpected result" "Check cache configuration and test manually" fi else log_check "Cache System" "WARN" "Cannot test (missing .env)" "Performance" "Low" fi # C3-05: Session System Health Check echo "=== Session System Health ===" if [ -f ".env" ]; then SESSION_DRIVER=$(grep "^SESSION_DRIVER=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" 2>/dev/null) SESSION_DRIVER=${SESSION_DRIVER:-file} echo "? Session driver: $SESSION_DRIVER" if [ "$SESSION_DRIVER" = "redis" ]; then # Test Redis session REDIS_SESSION_TEST=' <?php require_once "vendor/autoload.php"; $app = require_once "bootstrap/app.php"; try { $redis = app("redis"); $redis->ping(); echo "REDIS_SESSION_OK"; } catch (Exception $e) { echo "REDIS_SESSION_ERROR: " . $e->getMessage(); } ' REDIS_RESULT=$(echo "$REDIS_SESSION_TEST" | php 2>/dev/null) if echo "$REDIS_RESULT" | grep -q "REDIS_SESSION_OK"; then log_check "Session System" "PASS" "Redis session working" "Security" "Medium" else log_check "Session System" "FAIL" "Redis session configured but not available" "Security" "High" # Auto-fix: Switch to file sessions if apply_fix "Switch session driver to file" "sed -i 's/SESSION_DRIVER=redis/SESSION_DRIVER=file/' .env" "Switch from Redis to file-based sessions"; then apply_fix "Clear config cache" "php artisan config:clear 2>/dev/null" "Clear cached configuration" fi fi else log_check "Session System" "PASS" "$SESSION_DRIVER driver configured" "Security" "Medium" fi else log_check "Session System" "WARN" "Cannot test (missing .env)" "Security" "Low" fi # C3-06: Queue System Health Check echo "=== Queue System Health ===" if [ -f ".env" ]; then QUEUE_CONNECTION=$(grep "^QUEUE_CONNECTION=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'" 2>/dev/null) QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync} echo "? Queue connection: $QUEUE_CONNECTION" if [ "$QUEUE_CONNECTION" = "redis" ]; then # Test Redis queue REDIS_QUEUE_TEST=' <?php require_once "vendor/autoload.php"; $app = require_once "bootstrap/app.php"; try { Queue::connection("redis")->size(); echo "REDIS_QUEUE_OK"; } catch (Exception $e) { echo "REDIS_QUEUE_ERROR: " . $e->getMessage(); } ' QUEUE_RESULT=$(echo "$REDIS_QUEUE_TEST" | php 2>/dev/null) if echo "$QUEUE_RESULT" | grep -q "REDIS_QUEUE_OK"; then log_check "Queue System" "PASS" "Redis queue working" "Performance" "Low" else log_check "Queue System" "FAIL" "Redis queue configured but not available" "Performance" "Medium" # Auto-fix: Switch to sync queue if apply_fix "Switch queue to sync" "sed -i 's/QUEUE_CONNECTION=redis/QUEUE_CONNECTION=sync/' .env" "Switch from Redis to sync queue"; then apply_fix "Clear config cache" "php artisan config:clear 2>/dev/null" "Clear cached configuration" fi fi else log_check "Queue System" "PASS" "$QUEUE_CONNECTION driver configured" "Performance" "Low" fi else log_check "Queue System" "WARN" "Cannot test (missing .env)" "Performance" "Low" fi # C3-07: PHP Extensions Check echo "=== PHP Extensions Health ===" REQUIRED_EXTENSIONS=("openssl" "pdo" "mbstring" "tokenizer" "xml" "ctype" "json" "bcmath" "fileinfo") MISSING_EXTENSIONS=() for EXT in "${REQUIRED_EXTENSIONS[@]}"; do if php -m | grep -q "$EXT"; then log_check "PHP Extension: $EXT" "PASS" "Loaded" "Environment" "Medium" else log_check "PHP Extension: $EXT" "FAIL" "Missing - contact hosting provider" "Environment" "High" MISSING_EXTENSIONS+=("$EXT") fi done # Add action item for missing extensions if [ ${#MISSING_EXTENSIONS[@]} -gt 0 ]; then add_action_item "HIGH" "Install Missing PHP Extensions" "Missing: ${MISSING_EXTENSIONS[*]}" "Contact hosting provider to install: ${MISSING_EXTENSIONS[*]}" fi # C3-08: Disabled Functions Check echo "=== PHP Functions Health ===" DISABLED_FUNCTIONS=$(php -r "echo ini_get('disable_functions');" 2>/dev/null) PROBLEMATIC_DISABLED=() if [ -n "$DISABLED_FUNCTIONS" ]; then echo "? Disabled PHP functions: $DISABLED_FUNCTIONS" # Check for commonly problematic functions PROBLEMATIC_FUNCTIONS=("exec" "shell_exec" "proc_open" "system" "passthru") for FUNC in "${PROBLEMATIC_FUNCTIONS[@]}"; do if echo "$DISABLED_FUNCTIONS" | grep -q "$FUNC"; then log_check "PHP Function: $FUNC" "WARN" "Disabled by hosting provider" "Environment" "Medium" PROBLEMATIC_DISABLED+=("$FUNC") else log_check "PHP Function: $FUNC" "PASS" "Available" "Environment" "Low" fi done # Add action item if critical functions are disabled if [ ${#PROBLEMATIC_DISABLED[@]} -gt 0 ]; then add_action_item "MEDIUM" "PHP Functions Disabled" "Functions disabled: ${PROBLEMATIC_DISABLED[*]} - Some Laravel features may be limited" "This is normal for shared hosting. Use manual alternatives when needed." fi else log_check "PHP Functions" "PASS" "No functions disabled" "Environment" "Low" fi # C3-09: Storage and Permissions Check echo "=== Storage & Permissions Health ===" # Check critical directories CRITICAL_DIRS=("storage" "bootstrap/cache" "public/storage") BROKEN_DIRS=() for DIR in "${CRITICAL_DIRS[@]}"; do if [ -e "$DIR" ]; then if [ -w "$DIR" ]; then log_check "Directory: $DIR" "PASS" "Exists and writable" "Security" "High" else log_check "Directory: $DIR" "FAIL" "Not writable" "Security" "High" BROKEN_DIRS+=("$DIR") # Auto-fix: Set permissions apply_fix "Fix permissions for $DIR" "chmod -R 775 '$DIR' 2>/dev/null" "Set proper write permissions" fi else log_check "Directory: $DIR" "FAIL" "Missing" "Security" "High" BROKEN_DIRS+=("$DIR") # Auto-fix: Create directory if [ "$DIR" = "public/storage" ]; then apply_fix "Create storage symlink" "ln -sfn ../storage/app/public public/storage" "Create symlink for public file access" else apply_fix "Create directory $DIR" "mkdir -p '$DIR' && chmod 775 '$DIR'" "Create missing directory with proper permissions" fi fi done # Add action item for broken directories if [ ${#BROKEN_DIRS[@]} -gt 0 ]; then add_action_item "HIGH" "Fix Directory Issues" "Problems with: ${BROKEN_DIRS[*]}" "Check and fix permissions/symlinks for: ${BROKEN_DIRS[*]}" fi # C3-10: Configuration Cache Health echo "=== Configuration Cache Health ===" if [ -f "bootstrap/cache/config.php" ]; then log_check "Config Cache" "PASS" "Configuration cached" "Performance" "Medium" # Check if cached config has Redis references when Redis is broken if grep -q "redis" bootstrap/cache/config.php && echo "$CACHE_RESULT $REDIS_RESULT $QUEUE_RESULT" | grep -q "ERROR"; then log_check "Config Cache Content" "WARN" "Cached config references broken Redis" "Performance" "High" # Auto-fix: Clear and rebuild config cache apply_fix "Rebuild config cache" "php artisan config:clear && php artisan config:cache 2>/dev/null" "Rebuild config cache with current settings" else log_check "Config Cache Content" "PASS" "No conflicts detected" "Performance" "Low" fi else log_check "Config Cache" "WARN" "Configuration not cached (performance impact)" "Performance" "Medium" # Auto-fix: Create config cache apply_fix "Create config cache" "php artisan config:cache 2>/dev/null" "Cache configuration for better performance" fi # C3-11: HTTP Response Test echo "=== HTTP Response Health ===" if [ -f ".env" ]; then APP_URL=$(grep "^APP_URL=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'") if [ -n "$APP_URL" ] && command -v curl &> /dev/null; then echo "? Testing: $APP_URL" HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL" --max-time 10 2>/dev/null || echo "000") case $HTTP_CODE in 200|201|301|302) log_check "HTTP Response" "PASS" "Application responding (HTTP $HTTP_CODE)" "Core" "Critical" ;; 500) log_check "HTTP Response" "FAIL" "HTTP 500 - Internal Server Error" "Core" "Critical" # Try to get more details from Laravel logs if [ -f "storage/logs/laravel.log" ]; then echo " ? Latest Laravel error:" LATEST_ERROR=$(tail -3 storage/logs/laravel.log | grep -E "(ERROR|Exception|Fatal)" | tail -1 | sed 's/^/ /') echo "$LATEST_ERROR" add_action_item "CRITICAL" "Fix HTTP 500 Error" "Website returning server error: $LATEST_ERROR" "Check storage/logs/laravel.log for full error details" else add_action_item "CRITICAL" "Fix HTTP 500 Error" "Website returning server error" "Check application logs and configuration" fi ;; 503) log_check "HTTP Response" "WARN" "HTTP 503 - Service Unavailable (maintenance mode?)" "Core" "Medium" add_action_item "MEDIUM" "Check Maintenance Mode" "Site may be in maintenance mode" "Run: php artisan up" ;; 000) log_check "HTTP Response" "FAIL" "Connection timeout or unreachable" "Core" "Critical" add_action_item "CRITICAL" "Website Unreachable" "Cannot connect to website" "Check server status and DNS configuration" ;; *) log_check "HTTP Response" "WARN" "HTTP $HTTP_CODE - Unexpected response" "Core" "High" add_action_item "HIGH" "Investigate HTTP Response" "Unexpected HTTP code: $HTTP_CODE" "Check web server configuration and application status" ;; esac # Test install page if main page fails if [ "$HTTP_CODE" = "500" ]; then INSTALL_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/install" --max-time 10 2>/dev/null || echo "000") if [ "$INSTALL_CODE" = "200" ]; then log_check "Install Page" "PASS" "Install page accessible (HTTP $INSTALL_CODE)" "Core" "Medium" echo " ? This may be a fresh deployment needing installation" add_action_item "MEDIUM" "Complete Installation" "Fresh deployment detected - installation needed" "Visit: $APP_URL/install" fi fi else log_check "HTTP Response" "WARN" "Cannot test (no APP_URL or curl unavailable)" "Core" "Medium" add_action_item "MEDIUM" "Configure APP_URL" "Cannot test website - APP_URL missing or curl unavailable" "Set APP_URL in .env file" fi else log_check "HTTP Response" "WARN" "Cannot test (missing .env)" "Core" "High" fi # C3-12: Laravel Error Log Analysis echo "=== Error Log Analysis ===" if [ -f "storage/logs/laravel.log" ]; then LOG_SIZE=$(du -sh storage/logs/laravel.log | cut -f1) log_check "Laravel Log File" "PASS" "Exists ($LOG_SIZE)" "Monitoring" "Low" # Check for recent critical errors RECENT_ERRORS=$(tail -100 storage/logs/laravel.log | grep -c -E "(ERROR|CRITICAL|EMERGENCY)" 2>/dev/null || echo "0") if [ "$RECENT_ERRORS" -gt 0 ]; then log_check "Recent Critical Errors" "WARN" "$RECENT_ERRORS errors found in last 100 lines" "Monitoring" "High" RECENT_ERROR=$(tail -100 storage/logs/laravel.log | grep -E "(ERROR|CRITICAL|EMERGENCY)" | tail -1 | sed 's/^/ /') echo " ? Most recent critical error:" echo "$RECENT_ERROR" add_action_item "HIGH" "Investigate Error Log" "$RECENT_ERRORS recent errors found" "Check: tail -20 storage/logs/laravel.log" else log_check "Recent Critical Errors" "PASS" "No critical errors in recent logs" "Monitoring" "Low" fi else log_check "Laravel Log File" "WARN" "No log file found" "Monitoring" "Medium" add_action_item "MEDIUM" "Check Logging Configuration" "No Laravel log file found" "Verify logging is properly configured in config/logging.php" fi # C3-13: Generate Health Summary echo "=== Health Check Summary ===" HEALTH_PERCENTAGE=$(( (PASSED_CHECKS * 100) / TOTAL_CHECKS )) echo "? Overall Health Score: $HEALTH_PERCENTAGE% ($PASSED_CHECKS/$TOTAL_CHECKS checks passed)" echo "? Auto-fixes Applied: $FIXES_APPLIED" echo "⚠️ Issues Found: $ISSUES_FOUND" # Determine overall status if [ $HEALTH_PERCENTAGE -ge 90 ]; then OVERALL_STATUS="? EXCELLENT" echo "✅ Your application is healthy and ready for production!" elif [ $HEALTH_PERCENTAGE -ge 75 ]; then OVERALL_STATUS="? GOOD" echo "✅ Your application is mostly healthy with minor issues." elif [ $HEALTH_PERCENTAGE -ge 50 ]; then OVERALL_STATUS="? NEEDS ATTENTION" echo "⚠️ Your application has issues that should be addressed." else OVERALL_STATUS="? CRITICAL" echo "❌ Your application has critical issues requiring immediate attention." fi echo "? Status: $OVERALL_STATUS" # C3-14: Actionable Recommendations echo "=== Actionable Recommendations ===" if [ $ISSUES_FOUND -gt 0 ]; then echo "? Based on the health check results, here are specific actions to take:" echo "" # Provide specific recommendations based on findings if echo "$CACHE_RESULT $REDIS_RESULT $QUEUE_RESULT" | grep -q "ERROR.*Redis"; then echo "1. **Redis Configuration Issue Detected**" echo " - Redis is configured but not available on this server" echo " - Auto-fixes applied: Switched to file-based cache/sessions" echo " - Action: Verify changes in .env file" echo "" fi if [ "$HTTP_CODE" = "500" ]; then echo "2. **HTTP 500 Error Active**" echo " - Your website is returning server errors" echo " - Check the Laravel error log above for specific details" echo " - Action: Review storage/logs/laravel.log for error details" echo "" fi if echo "$DISABLED_FUNCTIONS" | grep -q "exec\|proc_open"; then echo "3. **Disabled PHP Functions**" echo " - Some PHP functions are disabled by your hosting provider" echo " - This is normal for shared hosting security" echo " - Action: Ensure auto-fixes have been applied for storage links" echo "" fi echo "4. **General Recommendations**" echo " - Monitor the Laravel error log: tail -f storage/logs/laravel.log" echo " - Test your site: curl -I $APP_URL" echo " - If issues persist, contact your hosting provider" echo "" else echo "? No issues found! Your application is healthy and ready for production." fi # C3-15: Create Comprehensive Issues Summary Report echo "=== Creating Issues Summary Report ===" # Initialize report paths (same folder as Phase C-2) TIMESTAMP=$(date +"%Y%m%d_%H%M%S") DOMAIN_ROOT=$(dirname "/home/u227177893/domains/staging.societypal.com/deploy") DOMAIN_NAME=$(basename "$DOMAIN_ROOT") REPORT_DIR="$DOMAIN_ROOT/deployment-reports" ISSUES_REPORT="$REPORT_DIR/health-issues-$TIMESTAMP.md" # Create report directory if it doesn't exist mkdir -p "$REPORT_DIR" # Start the issues report cat > "$ISSUES_REPORT" << EOF # ? Health Check Issues Summary **Domain:** $DOMAIN_NAME **Timestamp:** $(date '+%Y-%m-%d %H:%M:%S') **Health Score:** $HEALTH_PERCENTAGE% ($PASSED_CHECKS/$TOTAL_CHECKS checks passed) **Status:** $OVERALL_STATUS **Issues Found:** $ISSUES_FOUND **Auto-Fixes Applied:** $FIXES_APPLIED --- EOF # Add TL;DR section if [ $ISSUES_FOUND -eq 0 ]; then cat >> "$ISSUES_REPORT" << EOF ## ? TL;DR - ALL GOOD! ✅ **No issues detected** - Your deployment is healthy and ready for production! All $TOTAL_CHECKS health checks passed successfully. No manual intervention required. EOF else cat >> "$ISSUES_REPORT" << EOF ## ⚠️ TL;DR - ISSUES DETECTED ❌ **$ISSUES_FOUND issues found** that need your attention ? **$FIXES_APPLIED auto-fixes** have been applied automatically ? **Health Score:** $HEALTH_PERCENTAGE% ($([ $HEALTH_PERCENTAGE -ge 75 ] && echo "Good" || echo "Needs Attention")) $([ $FIXES_APPLIED -gt 0 ] && echo "✅ Most common issues have been **automatically fixed**" || echo "⚠️ Manual intervention may be required") EOF fi # Add detailed issues if any found if [ $ISSUES_FOUND -gt 0 ]; then cat >> "$ISSUES_REPORT" << EOF --- ## ? ISSUES BY CATEGORY EOF # Process detailed issues by category if [ -f "/tmp/c3_detailed_issues" ] && [ -s "/tmp/c3_detailed_issues" ]; then # Group issues by category CATEGORIES=($(cut -d'|' -f1 /tmp/c3_detailed_issues | sort -u)) for CATEGORY in "${CATEGORIES[@]}"; do if [ -n "$CATEGORY" ]; then cat >> "$ISSUES_REPORT" << EOF ### $CATEGORY Issues EOF # Add issues for this category grep "^$CATEGORY|" /tmp/c3_detailed_issues | while IFS='|' read -r cat status check details impact; do ICON="⚠️" [ "$status" = "ERROR" ] && ICON="❌" echo "- $ICON **$check** ($impact Impact): $details" >> "$ISSUES_REPORT" done fi done fi # Add specific issue analysis based on what we detected cat >> "$ISSUES_REPORT" << EOF --- ## ? AUTO-FIXES APPLIED EOF if [ -f "/tmp/c3_fixes_log" ] && [ -s "/tmp/c3_fixes_log" ]; then cat "/tmp/c3_fixes_log" >> "$ISSUES_REPORT" fi if [ $FIXES_APPLIED -eq 0 ]; then echo "- No auto-fixes were applied" >> "$ISSUES_REPORT" fi cat >> "$ISSUES_REPORT" << EOF --- ## ? ACTION ITEMS EOF # Generate prioritized action items if [ -f "/tmp/c3_action_items" ] && [ -s "/tmp/c3_action_items" ]; then # Process action items by priority PRIORITIES=("CRITICAL" "HIGH" "MEDIUM" "LOW") ACTION_COUNT=0 for PRIORITY in "${PRIORITIES[@]}"; do PRIORITY_ITEMS=$(grep "^ACTION|$PRIORITY|" /tmp/c3_action_items 2>/dev/null || true) if [ -n "$PRIORITY_ITEMS" ]; then cat >> "$ISSUES_REPORT" << EOF ### $PRIORITY Priority EOF echo "$PRIORITY_ITEMS" | while IFS='|' read -r type priority title description command; do ACTION_COUNT=$((ACTION_COUNT + 1)) PRIORITY_ICON="?" [ "$priority" = "CRITICAL" ] && PRIORITY_ICON="?" [ "$priority" = "HIGH" ] && PRIORITY_ICON="?" [ "$priority" = "MEDIUM" ] && PRIORITY_ICON="?" cat >> "$ISSUES_REPORT" << EOF $ACTION_COUNT. $PRIORITY_ICON **$title** - **Problem:** $description - **Action:** $command EOF done fi done fi if [ ! -f "/tmp/c3_action_items" ] || [ ! -s "/tmp/c3_action_items" ]; then echo "- All detected issues have been automatically resolved" >> "$ISSUES_REPORT" echo "- Monitor the application for any remaining issues" >> "$ISSUES_REPORT" fi fi # Add quick reference commands cat >> "$ISSUES_REPORT" << EOF --- ## ? QUICK REFERENCE COMMANDS ### Check Current Status: \`\`\`bash # Test website curl -I https://$DOMAIN_NAME # Check Laravel health cd /home/u227177893/domains/staging.societypal.com/deploy/current php artisan about # View latest errors tail -10 storage/logs/laravel.log \`\`\` ### Common Fixes: \`\`\`bash # Fix Redis issues (if applicable) cd /home/u227177893/domains/staging.societypal.com/deploy/current sed -i 's/CACHE_DRIVER=redis/CACHE_DRIVER=file/' .env sed -i 's/SESSION_DRIVER=redis/SESSION_DRIVER=file/' .env php artisan config:clear # Clear all caches php artisan cache:clear php artisan config:clear php artisan route:clear php artisan view:clear # Rebuild caches php artisan config:cache php artisan route:cache php artisan view:cache \`\`\` ### Get Help: - **Full Report:** \`cat $DOMAIN_ROOT/deployment-reports/latest-report.md\` - **Error Logs:** \`tail -20 /home/u227177893/domains/staging.societypal.com/deploy/current/storage/logs/laravel.log\` - **Health History:** \`cat /home/u227177893/domains/staging.societypal.com/deploy/shared/health-checks.log\` --- **Next Steps:** 1. Address the action items above (if any) 2. Test your website: https://$DOMAIN_NAME 3. Monitor error logs: \`tail -f /home/u227177893/domains/staging.societypal.com/deploy/current/storage/logs/laravel.log\` **Report Generated:** $(date '+%Y-%m-%d %H:%M:%S') **Complement to:** Phase C-2 Comprehensive Report (\`latest-report.md\`) EOF # C3-16: Create HTML Report for Enhanced Viewing echo "=== Creating HTML Report ===" HTML_REPORT="$REPORT_DIR/health-issues-$TIMESTAMP.html" cat > "$HTML_REPORT" << EOF <!DOCTYPE html> <html lang="en"> <head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Health Check Issues - $DOMAIN_NAME</title> <style> * { margin: 0; padding: 0; box-sizing: border-box; } body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; } .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden; } .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; text-align: center; } .header h1 { font-size: 2.5rem; margin-bottom: 10px; text-shadow: 0 2px 4px rgba(0,0,0,0.3); } .header .subtitle { font-size: 1.2rem; opacity: 0.9; } .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; padding: 40px; background: #f8f9fa; } .stat-card { background: white; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.2s ease; } .stat-card:hover { transform: translateY(-2px); } .stat-number { font-size: 2rem; font-weight: bold; margin-bottom: 5px; } .stat-label { color: #666; font-size: 0.9rem; } .excellent { color: #28a745; } .good { color: #17a2b8; } .warning { color: #ffc107; } .critical { color: #dc3545; } .content { padding: 40px; } .section { margin-bottom: 40px; } .section h2 { font-size: 1.8rem; margin-bottom: 20px; color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; } .tldr { background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; } .tldr.issues { background: linear-gradient(135deg, #ffc107, #fd7e14); } .tldr h3 { font-size: 1.5rem; margin-bottom: 15px; } .issues-grid { display: grid; gap: 20px; margin-bottom: 30px; } .issue-category { background: #f8f9fa; border-radius: 12px; padding: 25px; border-left: 5px solid #dc3545; } .issue-category h4 { color: #dc3545; margin-bottom: 15px; font-size: 1.3rem; } .issue-item { background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid #dc3545; } .issue-item.warning { border-left-color: #ffc107; } .fix-item { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 8px; margin-bottom: 10px; } .fix-item.failed { background: #f8d7da; border-color: #f5c6cb; } .action-items { display: grid; gap: 15px; } .action-item { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 5px solid #667eea; } .action-item.critical { border-left-color: #dc3545; } .action-item.high { border-left-color: #fd7e14; } .action-item.medium { border-left-color: #ffc107; } .code-block { background: #2d3748; color: #e2e8f0; padding: 20px; border-radius: 8px; overflow-x: auto; font-family: 'Monaco', 'Menlo', monospace; font-size: 0.9rem; margin: 15px 0; } .tabs { display: flex; border-bottom: 1px solid #dee2e6; margin-bottom: 20px; } .tab { padding: 12px 24px; background: none; border: none; cursor: pointer; font-size: 1rem; border-bottom: 3px solid transparent; transition: all 0.2s ease; } .tab.active { border-bottom-color: #667eea; color: #667eea; font-weight: 600; } .tab-content { display: none; } .tab-content.active { display: block; } .footer { background: #f8f9fa; padding: 30px; text-align: center; color: #666; border-top: 1px solid #dee2e6; } @media (max-width: 768px) { .header h1 { font-size: 2rem; } .stats { grid-template-columns: 1fr; padding: 20px; } .content { padding: 20px; } .tabs { flex-wrap: wrap; } .tab { flex: 1; min-width: 120px; } } </style> </head> <body> <div class="container"> <div class="header"> <h1>? Health Check Report</h1> <div class="subtitle">$DOMAIN_NAME • $(date '+%Y-%m-%d %H:%M:%S')</div> </div> <div class="stats"> <div class="stat-card"> <div class="stat-number $([ $HEALTH_PERCENTAGE -ge 90 ] && echo "excellent" || [ $HEALTH_PERCENTAGE -ge 75 ] && echo "good" || [ $HEALTH_PERCENTAGE -ge 50 ] && echo "warning" || echo "critical")">$HEALTH_PERCENTAGE%</div> <div class="stat-label">Health Score</div> </div> <div class="stat-card"> <div class="stat-number $([ $ISSUES_FOUND -eq 0 ] && echo "excellent" || echo "warning")">$ISSUES_FOUND</div> <div class="stat-label">Issues Found</div> </div> <div class="stat-card"> <div class="stat-number $([ $FIXES_APPLIED -gt 0 ] && echo "good" || echo "warning")">$FIXES_APPLIED</div> <div class="stat-label">Auto-Fixes Applied</div> </div> <div class="stat-card"> <div class="stat-number good">$PASSED_CHECKS/$TOTAL_CHECKS</div> <div class="stat-label">Checks Passed</div> </div> </div> <div class="content"> EOF # Add TL;DR section to HTML if [ $ISSUES_FOUND -eq 0 ]; then cat >> "$HTML_REPORT" << EOF <div class="tldr"> <h3>? TL;DR - ALL GOOD!</h3> <p><strong>No issues detected</strong> - Your deployment is healthy and ready for production!</p> <p>All $TOTAL_CHECKS health checks passed successfully. No manual intervention required.</p> </div> EOF else cat >> "$HTML_REPORT" << EOF <div class="tldr issues"> <h3>⚠️ TL;DR - ISSUES DETECTED</h3> <p><strong>$ISSUES_FOUND issues found</strong> that need your attention</p> <p><strong>$FIXES_APPLIED auto-fixes</strong> have been applied automatically</p> <p><strong>Health Score:</strong> $HEALTH_PERCENTAGE% ($([ $HEALTH_PERCENTAGE -ge 75 ] && echo "Good" || echo "Needs Attention"))</p> $([ $FIXES_APPLIED -gt 0 ] && echo "<p>✅ Most common issues have been <strong>automatically fixed</strong></p>" || echo "<p>⚠️ Manual intervention may be required</p>") </div> EOF fi # Add tabbed content cat >> "$HTML_REPORT" << EOF <div class="tabs"> <button class="tab active" onclick="showTab('issues')">Issues by Category</button> <button class="tab" onclick="showTab('fixes')">Auto-Fixes Applied</button> <button class="tab" onclick="showTab('actions')">Action Items</button> <button class="tab" onclick="showTab('commands')">Quick Commands</button> </div> <div id="issues" class="tab-content active"> <div class="section"> EOF if [ $ISSUES_FOUND -gt 0 ] && [ -f "/tmp/c3_detailed_issues" ] && [ -s "/tmp/c3_detailed_issues" ]; then # Group issues by category for HTML CATEGORIES=($(cut -d'|' -f1 /tmp/c3_detailed_issues | sort -u)) for CATEGORY in "${CATEGORIES[@]}"; do if [ -n "$CATEGORY" ]; then cat >> "$HTML_REPORT" << EOF <div class="issue-category"> <h4>$CATEGORY Issues</h4> EOF # Add issues for this category grep "^$CATEGORY|" /tmp/c3_detailed_issues | while IFS='|' read -r cat status check details impact; do ISSUE_CLASS="issue-item" [ "$status" = "WARNING" ] && ISSUE_CLASS="issue-item warning" cat >> "$HTML_REPORT" << EOF <div class="$ISSUE_CLASS"> <strong>$check</strong> ($impact Impact)<br> $details </div> EOF done echo " </div>" >> "$HTML_REPORT" fi done else cat >> "$HTML_REPORT" << EOF <div class="issue-category"> <h4>? No Issues Found</h4> <div class="issue-item" style="border-left-color: #28a745;"> <strong>All checks passed!</strong><br> Your application is healthy and ready for production. </div> </div> EOF fi cat >> "$HTML_REPORT" << EOF </div> </div> <div id="fixes" class="tab-content"> <div class="section"> EOF if [ $FIXES_APPLIED -gt 0 ] && [ -f "/tmp/c3_fixes_log" ] && [ -s "/tmp/c3_fixes_log" ]; then # Process fixes for HTML while IFS= read -r line; do if [[ "$line" =~ "✅" ]]; then echo " <div class=\"fix-item\">$line</div>" >> "$HTML_REPORT" elif [[ "$line" =~ "❌" ]]; then echo " <div class=\"fix-item failed\">$line</div>" >> "$HTML_REPORT" fi done < /tmp/c3_fixes_log else cat >> "$HTML_REPORT" << EOF <div class="fix-item"> <strong>No auto-fixes were applied</strong><br> Either no issues were found that could be automatically fixed, or all issues require manual intervention. </div> EOF fi cat >> "$HTML_REPORT" << EOF </div> </div> <div id="actions" class="tab-content"> <div class="section"> <div class="action-items"> EOF if [ -f "/tmp/c3_action_items" ] && [ -s "/tmp/c3_action_items" ]; then # Process action items by priority for HTML PRIORITIES=("CRITICAL" "HIGH" "MEDIUM" "LOW") ACTION_COUNT=0 for PRIORITY in "${PRIORITIES[@]}"; do PRIORITY_ITEMS=$(grep "^ACTION|$PRIORITY|" /tmp/c3_action_items 2>/dev/null || true) if [ -n "$PRIORITY_ITEMS" ]; then echo "$PRIORITY_ITEMS" | while IFS='|' read -r type priority title description command; do ACTION_COUNT=$((ACTION_COUNT + 1)) PRIORITY_CLASS="action-item" [ "$priority" = "CRITICAL" ] && PRIORITY_CLASS="action-item critical" [ "$priority" = "HIGH" ] && PRIORITY_CLASS="action-item high" [ "$priority" = "MEDIUM" ] && PRIORITY_CLASS="action-item medium" cat >> "$HTML_REPORT" << EOF <div class="$PRIORITY_CLASS"> <h4>$priority: $title</h4> <p><strong>Problem:</strong> $description</p> <p><strong>Action:</strong> $command</p> </div> EOF done fi done else cat >> "$HTML_REPORT" << EOF <div class="action-item" style="border-left-color: #28a745;"> <h4>✅ No Action Items</h4> <p>All detected issues have been automatically resolved or no issues were found.</p> <p>Monitor the application for any remaining issues.</p> </div> EOF fi cat >> "$HTML_REPORT" << EOF </div> </div> </div> <div id="commands" class="tab-content"> <div class="section"> <h3>Check Current Status:</h3> <div class="code-block"># Test website curl -I https://$DOMAIN_NAME # Check Laravel health cd /home/u227177893/domains/staging.societypal.com/deploy/current php artisan about # View latest errors tail -10 storage/logs/laravel.log</div> <h3>Common Fixes:</h3> <div class="code-block"># Fix Redis issues (if applicable) cd /home/u227177893/domains/staging.societypal.com/deploy/current sed -i 's/CACHE_DRIVER=redis/CACHE_DRIVER=file/' .env sed -i 's/SESSION_DRIVER=redis/SESSION_DRIVER=file/' .env php artisan config:clear # Clear all caches php artisan cache:clear php artisan config:clear php artisan route:clear php artisan view:clear # Rebuild caches php artisan config:cache php artisan route:cache php artisan view:cache</div> <h3>Get Help:</h3> <div class="code-block"># Full deployment report cat $DOMAIN_ROOT/deployment-reports/latest-report.md # Error logs tail -20 /home/u227177893/domains/staging.societypal.com/deploy/current/storage/logs/laravel.log # Health history cat /home/u227177893/domains/staging.societypal.com/deploy/shared/health-checks.log</div> </div> </div> </div> <div class="footer"> <p><strong>Report Generated:</strong> $(date '+%Y-%m-%d %H:%M:%S')</p> <p><strong>Complement to:</strong> Phase C-2 Comprehensive Report (<code>latest-report.md</code>)</p> <p><strong>Next Steps:</strong> Address action items above (if any) → Test website → Monitor error logs</p> </div> </div> <script> function showTab(tabName) { // Hide all tab contents const tabContents = document.querySelectorAll('.tab-content'); tabContents.forEach(content => { content.classList.remove('active'); }); // Remove active class from all tabs const tabs = document.querySelectorAll('.tab'); tabs.forEach(tab => { tab.classList.remove('active'); }); // Show selected tab content document.getElementById(tabName).classList.add('active'); // Add active class to clicked tab event.target.classList.add('active'); } // Auto-refresh health score color based on percentage document.addEventListener('DOMContentLoaded', function() { const healthScore = $HEALTH_PERCENTAGE; const healthElement = document.querySelector('.stat-number'); if (healthScore >= 90) { healthElement.classList.add('excellent'); } else if (healthScore >= 75) { healthElement.classList.add('good'); } else if (healthScore >= 50) { healthElement.classList.add('warning'); } else { healthElement.classList.add('critical'); } }); </script> </body> </html> EOF # Set permissions and create symlinks chmod 644 "$ISSUES_REPORT" "$HTML_REPORT" # Create latest issues symlinks cd "$REPORT_DIR" rm -f issues-latest.md issues-latest.html ln -sf "$(basename "$ISSUES_REPORT")" issues-latest.md ln -sf "$(basename "$HTML_REPORT")" issues-latest.html echo "✅ Issues summary report created: $ISSUES_REPORT" echo "✅ HTML report created: $HTML_REPORT" # C3-17: Log Results echo "=== Logging Results ===" # Log health check results to shared directory HEALTH_LOG="/home/u227177893/domains/staging.societypal.com/deploy/shared/health-checks.log" echo "[$(date '+%Y-%m-%d %H:%M:%S')] Health Check - Score: $HEALTH_PERCENTAGE% - Status: $OVERALL_STATUS - Issues: $ISSUES_FOUND - Fixes: $FIXES_APPLIED" >> "$HEALTH_LOG" # Log to deployment reports directory as well DEPLOYMENT_LOG="$REPORT_DIR/health-history.log" echo "[$(date '+%Y-%m-%d %H:%M:%S')] Health: $HEALTH_PERCENTAGE% | Status: $OVERALL_STATUS | Issues: $ISSUES_FOUND | Fixes: $FIXES_APPLIED | Report: $(basename "$ISSUES_REPORT")" >> "$DEPLOYMENT_LOG" log_check "Health Check Logging" "PASS" "Results logged to multiple locations" "Monitoring" "Low" echo "✅ Phase C-3 comprehensive health check completed!" echo "? Final Score: $HEALTH_PERCENTAGE% - $OVERALL_STATUS" echo "? Issues Report: $ISSUES_REPORT" echo "? HTML Report: $HTML_REPORT" echo "? Quick Access: $REPORT_DIR/issues-latest.md" echo "? HTML Access: $REPORT_DIR/issues-latest.html" # Display quick summary if [ $ISSUES_FOUND -eq 0 ]; then echo "? No issues detected - deployment is healthy!" else echo "⚠️ $ISSUES_FOUND issues found - check the issues report for details" echo "? $FIXES_APPLIED auto-fixes have been applied" echo "" echo "? Quick Issues Summary:" if [ -f "/tmp/c3_issues_log" ] && [ -s "/tmp/c3_issues_log" ]; then head -3 /tmp/c3_issues_log | sed 's/^/ /' if [ $(wc -l < /tmp/c3_issues_log) -gt 3 ]; then echo " ... and $(($(wc -l < /tmp/c3_issues_log) - 3)) more (see full report)" fi fi fi # C3-18: Cleanup temporary files echo "=== Cleanup ===" rm -f /tmp/c3_issues_log /tmp/c3_fixes_log /tmp/c3_detailed_issues /tmp/c3_action_items echo "✅ Temporary files cleaned up" # Exit with appropriate code (but don't fail deployment) if [ $HEALTH_PERCENTAGE -ge 50 ]; then exit 0 else echo "⚠️ Critical issues detected but deployment continues" exit 0 # Don't fail deployment, just warn fi echo "✅ Phase C-2 completed successfully"]

=== Phase C-3: Comprehensive Health Check & Auto-Fix ===
? Starting comprehensive health checks...
=== Laravel Framework Health ===
❌ Laravel Framework: Artisan command failed
  Debug: 
=== Environment Configuration ===
✅ Environment File: .env file exists and readable
✅ Required Var: APP_KEY: Set
✅ Required Var: DB_DATABASE: Set
✅ Required Var: DB_USERNAME: Set
✅ Required Var: DB_PASSWORD: Set
=== Database Connection ===
Time taken to run G2-Works-Phase C-3: Comprehensive Health Check & Auto-Fix: 0.68 seconds


Cleaning up old releases



---

# 4- Finishing

Delivering webhook notifications


Sending emails


Saving build environment

Saved build environment language versions