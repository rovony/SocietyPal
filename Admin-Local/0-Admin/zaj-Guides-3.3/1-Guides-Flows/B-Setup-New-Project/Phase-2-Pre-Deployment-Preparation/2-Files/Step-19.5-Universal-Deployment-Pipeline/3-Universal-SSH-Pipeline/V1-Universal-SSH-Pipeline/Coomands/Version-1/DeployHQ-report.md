



# 1 Preparing

Waiting for an available deployment slot


Performing pre-deployment checks

Checking build allowance

Checking access to repository

Checking start and end revisions are valid

Checking connection to server SocietyPal-Staging


Preparing repository for deployment

Updating repository from git@github.com:rovony/SocietyPal.git

Getting information for start commit f6adbf

Getting information for end commit 785480

---
# 2: Building


Checking out repository for deployment

Checking out working copy of your repository at 785480 (staging)


Setting up environment for build commands

Preparing environment for build commands

Uploading repository to build server

Sending config files to build server


Running build command "1 Create Laravel Directories"

Running 1 Create Laravel Directories [#!/bin/bash set -e echo "=== Creating Laravel Directory Structure ===" # Create ALL required Laravel directories (covers all Laravel versions 8-12) echo "Creating Laravel directories..." mkdir -p bootstrap/cache mkdir -p storage/app/public mkdir -p storage/framework/cache/data mkdir -p storage/framework/sessions mkdir -p storage/framework/testing mkdir -p storage/framework/views mkdir -p storage/logs mkdir -p storage/clockwork mkdir -p storage/debugbar mkdir -p public/storage # Set proper permissions for all directories echo "Setting directory permissions..." chmod -R 755 bootstrap/cache chmod -R 755 storage find storage -type d -exec chmod 775 {} \; # Create .gitkeep files for empty directories echo "Creating .gitkeep files..." find storage -type d -empty -exec touch {}/.gitkeep \; 2>/dev/null || true touch bootstrap/cache/.gitkeep 2>/dev/null || true # Verify directory structure echo "Verifying directory structure..." echo "✅ bootstrap/cache: $([ -d "bootstrap/cache" ] && echo "exists" || echo "missing")" echo "✅ storage/app: $([ -d "storage/app" ] && echo "exists" || echo "missing")" echo "✅ storage/framework: $([ -d "storage/framework" ] && echo "exists" || echo "missing")" echo "✅ storage/logs: $([ -d "storage/logs" ] && echo "exists" || echo "missing")" echo "✅ Laravel directory structure created"]

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
Time taken to run 1 Create Laravel Directories: 1.68 seconds


Running build command "2 Install PHP Dependencies"

Running 2 Install PHP Dependencies [#!/bin/bash set -e # Note: Composer (composer install) Uses composer.lock by default echo "=== Installing PHP Dependencies ===" # Set memory limit for Composer export COMPOSER_MEMORY_LIMIT=-1 # Verify Composer composer --version # Install production dependencies echo "Installing PHP dependencies..." composer install \ --no-dev \ --optimize-autoloader \ --no-interaction \ --prefer-dist \ --no-progress # Verify installation echo "Verifying Composer installation..." if composer validate --no-check-publish --quiet; then echo "✅ Composer dependencies valid" else echo "⚠️ Composer validation warnings (continuing)" fi echo "✅ PHP dependencies installed"]

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
Time taken to run 2 Install PHP Dependencies: 9.08 seconds


Running build command "3 Install Node.js Dependencies"

Running 3 Install Node.js Dependencies [#!/bin/bash set -e echo "=== Installing Node.js Dependencies ===" # Note: npm (npm ci) Uses package-lock.json # Check if frontend build is required if [ ! -f "package.json" ]; then echo "ℹ️ No package.json found, skipping Node.js dependencies" exit 0 fi # Verify Node.js and npm node --version npm --version # Install dependencies from package-lock.json (production + dev for building) echo "Installing Node.js dependencies from package-lock.json..." npm ci --no-audit --no-fund echo "✅ Node.js dependencies installed"]

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
Time taken to run 3 Install Node.js Dependencies: 5.84 seconds


Running build command "4 Build Assets & Optimize"

Running 4 Build Assets & Optimize [#!/bin/bash set -e echo "=== Building Assets & Optimizing Laravel ===" # Generate optimized autoloader echo "Generating optimized autoloader..." composer dump-autoload --optimize --classmap-authoritative # Build frontend assets if Node.js dependencies exist if [ -f "package.json" ]; then echo "Building production assets..." # Try different build commands (covers all Laravel asset compilation methods) if npm run build 2>/dev/null; then echo "✅ Assets built with 'npm run build'" elif npm run production 2>/dev/null; then echo "✅ Assets built with 'npm run production'" elif npm run prod 2>/dev/null; then echo "✅ Assets built with 'npm run prod'" elif npm run dev 2>/dev/null; then echo "⚠️ Built with 'npm run dev' (not optimized for production)" else echo "ℹ️ No suitable build script found, skipping asset compilation" fi # Clean up node_modules to reduce deployment size echo "Cleaning up Node.js modules..." rm -rf node_modules # Verify build output exists if [ -d "public/build" ] || [ -d "public/dist" ] || [ -d "public/js" ] || [ -d "public/css" ] || [ -d "public/assets" ]; then echo "✅ Frontend assets compiled successfully" else echo "ℹ️ No build output detected (may be normal for API-only apps)" fi else echo "ℹ️ No package.json found, skipping asset compilation" fi # Final verification echo "Final verification..." echo "✅ vendor/: $([ -d "vendor" ] && echo "exists" || echo "missing")" echo "✅ bootstrap/cache/: $([ -d "bootstrap/cache" ] && echo "exists" || echo "missing")" echo "✅ Build complete and ready for deployment" echo "✅ Laravel build and optimization completed"]

=== Building Assets & Optimizing Laravel ===
Generating optimized autoloader...
Generating optimized autoload files (authoritative)
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

Generated optimized autoload files (authoritative) containing 9421 classes
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
✓ built in 6.97s
✅ Assets built with 'npm run build'
Cleaning up Node.js modules...
✅ Frontend assets compiled successfully
Final verification...
✅ vendor/: exists
✅ bootstrap/cache/: exists
✅ Build complete and ready for deployment
✅ Laravel build and optimization completed
Time taken to run 4 Build Assets & Optimize: 11.82 seconds


Receiving built files


Generating deployment manifest


Storing artifacts


---

# 3 SSH commands + transferring:

Running SSH command Phase A: Pre-Deployment Commands (Before Upload) v2

Executing Phase A: Pre-Deployment Commands (Before Upload) v2 [#!/bin/bash set -e # Phase A: Pre-Deployment Commands (Before Upload) # Purpose: Prepare environment, backup, enter maintenance mode echo "=== Phase A: Pre-Deployment Setup ===" # A01: System Checks echo "=== System Pre-flight Checks ===" php --version composer --version node --version 2>/dev/null || echo "Node not required" # Verify disk space AVAILABLE_DISK=$(df -k "/home/u227177893/domains/staging.societypal.com/deploy" 2>/dev/null | awk 'NR==2 {print $4}') if [ "$AVAILABLE_DISK" -lt 1048576 ]; then echo "⚠️ Warning: Low disk space" fi # A02: Initialize Shared Structure (First deployment safe) echo "=== Initialize Shared Structure ===" mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/storage/{app/public,framework/{cache/data,sessions,views},logs} mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/bootstrap/cache mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/backups mkdir -p /home/u227177893/domains/staging.societypal.com/deploy/shared/public/.well-known # Set permissions chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/storage 2>/dev/null || true chmod -R 775 /home/u227177893/domains/staging.societypal.com/deploy/shared/bootstrap/cache 2>/dev/null || true # A03: Backup Current Release (if exists) echo "=== Backup Current Release ===" if [ -L "/home/u227177893/domains/staging.societypal.com/deploy/current" ] && [ -d "/home/u227177893/domains/staging.societypal.com/deploy/current" ]; then BACKUP_TIMESTAMP=$(date +"%Y%m%d_%H%M%S") BACKUP_DIR="/home/u227177893/domains/staging.societypal.com/deploy/shared/backups/release_${BACKUP_TIMESTAMP}" # Create compressed backup echo "Creating backup..." mkdir -p "$BACKUP_DIR" cd /home/u227177893/domains/staging.societypal.com/deploy/releases CURRENT_RELEASE=$(basename "$(readlink "/home/u227177893/domains/staging.societypal.com/deploy/current")") tar -czf "$BACKUP_DIR/release_${CURRENT_RELEASE}.tar.gz" "$CURRENT_RELEASE" 2>/dev/null || true echo "✅ Backup created: $BACKUP_DIR" else echo "ℹ️ No current release to backup (first deployment)" fi # A04: Database Backup echo "=== Database Backup ===" if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ]; then # Parse .env file safely export $(grep -v '^#' /home/u227177893/domains/staging.societypal.com/deploy/shared/.env | xargs -0) 2>/dev/null || true if [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then BACKUP_FILE="/home/u227177893/domains/staging.societypal.com/deploy/shared/backups/db_$(date +%Y%m%d_%H%M%S).sql.gz" echo "Backing up database..." mysqldump -h"${DB_HOST:-localhost}" -P"${DB_PORT:-3306}" -u"$DB_USERNAME" -p"$DB_PASSWORD" \ --single-transaction --routines --triggers --add-drop-table \ "$DB_DATABASE" 2>/dev/null | gzip > "$BACKUP_FILE" || echo "⚠️ DB backup failed (continuing)" [ -f "$BACKUP_FILE" ] && echo "✅ Database backed up" fi else echo "ℹ️ No .env file yet (first deployment)" fi # A05: Enter Maintenance Mode echo "=== Enter Maintenance Mode ===" touch /home/u227177893/domains/staging.societypal.com/deploy/maintenance_mode_active if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/current/artisan" ]; then cd /home/u227177893/domains/staging.societypal.com/deploy/current php artisan down --render="errors::503" 2>/dev/null || echo "⚠️ Artisan maintenance failed" echo "✅ Maintenance mode activated" else echo "ℹ️ No current release (first deployment)" fi echo "✅ Phase A completed"]

=== Phase A: Pre-Deployment Setup ===
=== System Pre-flight Checks ===
PHP 8.2.28 (cli) (built: Mar 12 2025 00:00:00) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.2.28, Copyright (c) Zend Technologies
    with Zend OPcache v8.2.28, Copyright (c), by Zend Technologies
Composer version 1.10.26 2022-04-13 16:39:56
Node not required
=== Initialize Shared Structure ===
=== Backup Current Release ===
Creating backup...
✅ Backup created: /home/u227177893/domains/staging.societypal.com/deploy/shared/backups/release_20250818_193642
=== Database Backup ===
ℹ️ No .env file yet (first deployment)
=== Enter Maintenance Mode ===
⚠️ Artisan maintenance failed
✅ Maintenance mode activated
✅ Phase A completed
Time taken to run Phase A: Pre-Deployment Commands (Before Upload) v2: 5.88 seconds


Preparing release directory

Making release directory /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527

Copying previous release into /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527


Transferring changed files



Show file destinations?
Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/Step-17-Customization-Protection.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/Step-18-Data-Persistence.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/Step-19-Documentation.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/Step-20-Commit-Pre-Deploy.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/Step_15_Install_Dependencies.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/Step_16_Test_Build_Process.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-3-Deployment-Execution/Step-21-Choose-Deployment-Scenario.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-3-Deployment-Execution/Step-22A-Local-Build-Process.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-3-Deployment-Execution/Step-22B-GitHub-Actions-Workflow-Setup.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-3-Deployment-Execution/Step-22C-DeployHQ-Professional-Setup.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-3-Deployment-Execution/Step-22D-Git-Pull-Configuration.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-3-Deployment-Execution/Step-23A-Server-Deployment.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-3-Deployment-Execution/Step-23B-Automated-Build-and-Deployment.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-3-Deployment-Execution/Step-23C-Professional-Build-and-Deployment.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-3-Deployment-Execution/Step-24A-Post-Deployment-Verification.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-3-Deployment-Execution/Step-24B-Post-Deployment-Monitoring.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-3-Deployment-Execution/Step-24C-Enterprise-Monitoring-and-Management.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-3-Deployment-Execution/Step_23_PostDeploy_Verification.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-4-Post-Deployment-Maintenance/Step-25-Setup-Server-Monitoring.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-4-Post-Deployment-Maintenance/Step-26-Setup-Security-Hardening.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-4-Post-Deployment-Maintenance/Step-27-Performance-Monitoring.md

Uploading Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-4-Post-Deployment-Maintenance/Step-28-Emergency-Procedures.md

Uploading Admin-Local/3-Guides-V3-Consolidated/2-Subsequent-Deployment/Step_01_Pre_Update_Backup.md

Uploading Admin-Local/3-Guides-V3-Consolidated/2-Subsequent-Deployment/Step_02_Download_New_CodeCanyon_Version.md

Uploading Admin-Local/3-Guides-V3-Consolidated/2-Subsequent-Deployment/Step_03_Compare_Changes.md

Uploading Admin-Local/3-Guides-V3-Consolidated/2-Subsequent-Deployment/Step_04_Update_Vendor_Files.md

Uploading Admin-Local/3-Guides-V3-Consolidated/2-Subsequent-Deployment/Step_05_Test_Custom_Functions.md

Uploading Admin-Local/3-Guides-V3-Consolidated/2-Subsequent-Deployment/Step_06_Update_Dependencies.md

Uploading Admin-Local/3-Guides-V3-Consolidated/2-Subsequent-Deployment/Step_07_Test_Build_Process.md

Uploading Admin-Local/3-Guides-V3-Consolidated/2-Subsequent-Deployment/Step_08_Deploy_Updates.md

Uploading Admin-Local/3-Guides-V3-Consolidated/2-Subsequent-Deployment/Step_09_Verify_Deployment.md

Uploading Admin-Local/3-Guides-V3-Consolidated/3-Maintenance/Backup_Management.md

Uploading Admin-Local/3-Guides-V3-Consolidated/3-Maintenance/Emergency_Procedures.md

Uploading Admin-Local/3-Guides-V3-Consolidated/3-Maintenance/Performance_Monitoring.md

Uploading Admin-Local/3-Guides-V3-Consolidated/3-Maintenance/Security_Updates.md

Uploading Admin-Local/3-Guides-V3-Consolidated/3-Maintenance/Server_Monitoring.md

Uploading Admin-Local/3-Guides-V3-Consolidated/99-Understand/Best_Practices.md

Uploading Admin-Local/3-Guides-V3-Consolidated/99-Understand/CodeCanyon_Specifics.md

Uploading Admin-Local/3-Guides-V3-Consolidated/99-Understand/Deployment_Concepts.md

Uploading Admin-Local/3-Guides-V3-Consolidated/99-Understand/FAQ_Common_Issues.md

Uploading Admin-Local/3-Guides-V3-Consolidated/99-Understand/Introduction_Complete_Overview.md

Uploading Admin-Local/3-Guides-V3-Consolidated/99-Understand/README.md

Uploading Admin-Local/3-Guides-V3-Consolidated/99-Understand/Terminology_Definitions.md

Uploading Admin-Local/3-Guides-V3-Consolidated/99-Understand/Troubleshooting_Guide.md

Uploading Admin-Local/3-Guides-V3-Consolidated/README.md

Uploading Admin-Local/3-Guides-V3-Consolidated/archived/README.md

Uploading Admin-Local/3-Guides-V3-Consolidated/prompt1.md

Uploading V3-Laravel-Deployment-Progress-Tracker.md

Uploading package-lock.json

Uploading storage/logs/laravel.log


Uploading config files

Uploading /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/.env


Linking files from shared path to release

Symlinking backups to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/backups

Symlinking public/.well-known to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/public/.well-known


Running SSH command Phase B: Pre-Release Commands (After Upload, Before Release) v2

Executing Phase B: Pre-Release Commands (After Upload, Before Release) v2 [#!/bin/bash set -e # Phase B: Pre-Release Commands (After Upload, Before Release) # Purpose: Setup release, link shared resources, run migrations echo "=== Phase B: Pre-Release Setup ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527 # B01: Setup Environment File echo "=== Setup Environment ===" if [ ! -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ]; then echo "Creating shared .env file..." # Try multiple sources for .env if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/.env" ]; then cp /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/.env /home/u227177893/domains/staging.societypal.com/deploy/shared/.env echo "✅ Using uploaded .env" elif [ -f "/home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/.env.example" ]; then cp /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/.env.example /home/u227177893/domains/staging.societypal.com/deploy/shared/.env echo "✅ Using .env.example" else echo "⚠️ No .env found - create one manually" fi # Generate APP_KEY if missing if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/shared/.env" ]; then if ! grep -q "^APP_KEY=base64:" /home/u227177893/domains/staging.societypal.com/deploy/shared/.env; then cd /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527 php artisan key:generate --force 2>/dev/null || true cp /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/.env /home/u227177893/domains/staging.societypal.com/deploy/shared/.env 2>/dev/null || true fi fi fi # Link shared .env to release rm -f /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/.env ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/.env /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/.env echo "✅ Environment linked" # B02: Link Shared Directories echo "=== Link Shared Directories ===" # Remove release directories and link to shared rm -rf /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/storage ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/storage /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/storage rm -rf /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/bootstrap/cache ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/bootstrap/cache /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/bootstrap/cache # Link public directories if needed if [ -d "/home/u227177893/domains/staging.societypal.com/deploy/shared/public/.well-known" ]; then rm -rf /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/public/.well-known ln -sf /home/u227177893/domains/staging.societypal.com/deploy/shared/public/.well-known /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527/public/.well-known fi echo "✅ Shared directories linked" # B03: Install/Update Composer Dependencies (if needed) echo "=== Verify Composer Dependencies ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527 # Check if vendor directory exists and is complete if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then echo "Installing composer dependencies..." composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist elif [ -f "composer.lock" ]; then # Check if composer.lock is newer than vendor if [ "composer.lock" -nt "vendor/autoload.php" ]; then echo "Updating composer dependencies..." composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist fi fi # Fix for missing Inertia ServiceProvider if grep -q "Inertia\\\ServiceProvider" config/app.php 2>/dev/null; then if [ ! -d "vendor/inertiajs" ]; then echo "Installing Inertia..." composer require inertiajs/inertia-laravel --no-interaction 2>/dev/null || true fi fi echo "✅ Dependencies verified" # B04: Set Permissions echo "=== Set Permissions ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527 # Set directory permissions find . -type d -exec chmod 755 {} \; 2>/dev/null || true find . -type f -exec chmod 644 {} \; 2>/dev/null || true # Make artisan executable [ -f "artisan" ] && chmod +x artisan # Ensure storage is writable chmod -R 775 storage bootstrap/cache 2>/dev/null || true echo "✅ Permissions set" # B05: Run Migrations echo "=== Run Database Migrations ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527 if [ -f "artisan" ]; then # Check database connection first if php artisan db:show 2>/dev/null; then php artisan migrate --force --no-interaction || echo "⚠️ Migration failed" else echo "⚠️ Database not configured or accessible" fi else echo "⚠️ No artisan file found" fi # B06: Laravel Optimizations echo "=== Optimize Laravel ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527 # Clear old caches php artisan cache:clear 2>/dev/null || true php artisan config:clear 2>/dev/null || true php artisan route:clear 2>/dev/null || true php artisan view:clear 2>/dev/null || true # Create new caches php artisan config:cache 2>/dev/null || echo "⚠️ Config cache failed" php artisan route:cache 2>/dev/null || echo "⚠️ Route cache failed" php artisan view:cache 2>/dev/null || echo "⚠️ View cache failed" # Create storage link php artisan storage:link --force 2>/dev/null || echo "ℹ️ Storage link exists" echo "✅ Phase B completed"]

=== Phase B: Pre-Release Setup ===
=== Setup Environment ===
Creating shared .env file...
✅ Using uploaded .env
✅ Environment linked
=== Link Shared Directories ===
✅ Shared directories linked
=== Verify Composer Dependencies ===
✅ Dependencies verified
=== Set Permissions ===
✅ Permissions set
=== Run Database Migrations ===
⚠️ Database not configured or accessible
=== Optimize Laravel ===
⚠️ Config cache failed
⚠️ Route cache failed
⚠️ View cache failed
ℹ️ Storage link exists
✅ Phase B completed
Time taken to run Phase B: Pre-Release Commands (After Upload, Before Release) v2: 22.0 seconds


Linking release directory to current

Symlinking /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527 to /home/u227177893/domains/staging.societypal.com/deploy/current


Running SSH command Phase C: Post-Deployment Commands (After)

Executing Phase C: Post-Deployment Commands (After) [#!/bin/bash set -e # B04: Activate New Release echo "=== Activating New Release ===" cd .. CURRENT_RELEASE=$(basename "$(pwd)/releases/%RELEASE%") echo "Activating release: $CURRENT_RELEASE" # Atomic symlink switch ln -nfs "releases/%RELEASE%" current echo "✅ Current symlink updated" # Setup public_html for shared hosting (if needed) if [ ! -L public_html ] && [ ! -d public_html ]; then ln -s current/public public_html echo "✅ public_html symlink created" elif [ -L public_html ]; then echo "ℹ️ public_html symlink already exists" else echo "ℹ️ public_html directory exists (not symlinking)" fi # B05: Exit Maintenance Mode echo "=== Exiting Maintenance Mode ===" cd current if php artisan up 2>/dev/null; then echo "✅ Application is now live" else echo "⚠️ Failed to exit maintenance mode (check manually)" fi # B06: Cleanup Old Releases echo "=== Cleaning Up Old Releases ===" cd ../releases RELEASES_TO_KEEP=3 TOTAL_RELEASES=$(ls -1 | wc -l) if [ "$TOTAL_RELEASES" -gt "$RELEASES_TO_KEEP" ]; then RELEASES_TO_DELETE=$((TOTAL_RELEASES - RELEASES_TO_KEEP)) echo "Keeping $RELEASES_TO_KEEP releases, removing $RELEASES_TO_DELETE old releases" ls -t | tail -n +$((RELEASES_TO_KEEP + 1)) | xargs rm -rf 2>/dev/null || true echo "✅ Old releases cleaned up" else echo "ℹ️ Only $TOTAL_RELEASES releases found, no cleanup needed" fi echo "? Deployment completed successfully!" echo "? Active release: %RELEASE%" echo "? Site should be live at your domain"]

=== Activating New Release ===
Activating release: %RELEASE%
ln: failed to create symbolic link 'current': Permission denied
Time taken to run Phase C: Post-Deployment Commands (After): 0.23 seconds


Running SSH command Phase C: Post-Deployment Commands (After Release) v2

Executing Phase C: Post-Deployment Commands (After Release) v2 [#!/bin/bash set -e # Phase C: Post-Deployment Commands (After Release) # Purpose: Activate release, exit maintenance, cleanup, verify echo "=== Phase C: Post-Deployment ===" # C01: Setup Public Access (Hostinger/cPanel compatibility) echo "=== Setup Public Access ===" DOMAIN_ROOT=$(dirname "/home/u227177893/domains/staging.societypal.com/deploy") cd "$DOMAIN_ROOT" # Handle public_html symlink for shared hosting if [ ! -L "public_html" ]; then if [ -d "public_html" ]; then # Backup existing public_html if it has content if [ "$(ls -A public_html 2>/dev/null)" ]; then BACKUP_NAME="public_html.backup.$(date +%Y%m%d_%H%M%S)" mv public_html "$BACKUP_NAME" echo "✅ Backed up public_html to $BACKUP_NAME" else rm -rf public_html fi fi # Create symlink to current/public ln -sf deploy/current/public public_html echo "✅ public_html symlink created" else # Verify symlink points to correct location CURRENT_TARGET=$(readlink public_html) if [ "$CURRENT_TARGET" != "deploy/current/public" ]; then rm -f public_html ln -sf deploy/current/public public_html echo "✅ public_html symlink updated" fi fi # C02: Exit Maintenance Mode echo "=== Exit Maintenance Mode ===" rm -f /home/u227177893/domains/staging.societypal.com/deploy/maintenance_mode_active if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/current/artisan" ]; then cd /home/u227177893/domains/staging.societypal.com/deploy/current php artisan up 2>/dev/null || echo "⚠️ Artisan up command failed" echo "✅ Maintenance mode deactivated" fi # C03: Clear OpCache echo "=== Clear OpCache ===" if php -r "echo function_exists('opcache_reset') ? 'yes' : 'no';" | grep -q "yes"; then php -r "opcache_reset();" 2>/dev/null || true echo "✅ OpCache cleared" fi # C04: Restart Queue Workers (if applicable) echo "=== Restart Queue Workers ===" cd /home/u227177893/domains/staging.societypal.com/deploy/current if [ -f ".env" ]; then QUEUE_CONNECTION=$(grep "^QUEUE_CONNECTION=" .env | cut -d'=' -f2 | tr -d '"' | tr -d "'") if [ "$QUEUE_CONNECTION" != "sync" ] && [ -n "$QUEUE_CONNECTION" ]; then php artisan queue:restart 2>/dev/null || true echo "✅ Queue workers signaled to restart" else echo "ℹ️ Queue using sync driver (no workers)" fi fi # C05: Health Checks echo "=== Health Checks ===" HEALTH_STATUS=0 # Check current symlink if [ -L "/home/u227177893/domains/staging.societypal.com/deploy/current" ]; then echo "✅ Current symlink exists" else echo "❌ Current symlink missing" HEALTH_STATUS=1 fi # Check Laravel installation if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/current/artisan" ]; then cd /home/u227177893/domains/staging.societypal.com/deploy/current if php artisan --version >/dev/null 2>&1; then echo "✅ Laravel installation working" else echo "❌ Laravel installation broken" HEALTH_STATUS=1 fi fi # Check critical files [ -f "/home/u227177893/domains/staging.societypal.com/deploy/current/.env" ] && echo "✅ .env exists" || echo "❌ .env missing" [ -L "/home/u227177893/domains/staging.societypal.com/deploy/current/storage" ] && echo "✅ Storage linked" || echo "❌ Storage not linked" [ -d "/home/u227177893/domains/staging.societypal.com/deploy/current/vendor" ] && echo "✅ Vendor directory exists" || echo "❌ Vendor missing" # Check web accessibility if [ -f "/home/u227177893/domains/staging.societypal.com/deploy/current/.env" ]; then APP_URL=$(grep "^APP_URL=" /home/u227177893/domains/staging.societypal.com/deploy/current/.env | cut -d'=' -f2 | tr -d '"' | tr -d "'") if [ -n "$APP_URL" ] && command -v curl &> /dev/null; then HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL" --max-time 10 || echo "000") if [[ "$HTTP_CODE" == "200" || "$HTTP_CODE" == "503" ]]; then echo "✅ Application responding (HTTP $HTTP_CODE)" else echo "⚠️ Application returned HTTP $HTTP_CODE" fi fi fi # C06: Cleanup Old Releases echo "=== Cleanup Old Releases ===" cd /home/u227177893/domains/staging.societypal.com/deploy/releases KEEP_RELEASES=3 TOTAL_RELEASES=$(ls -1t | wc -l) if [ "$TOTAL_RELEASES" -gt "$KEEP_RELEASES" ]; then echo "Cleaning old releases (keeping $KEEP_RELEASES)..." ls -1t | tail -n +$((KEEP_RELEASES + 1)) | while read OLD_RELEASE; do rm -rf "$OLD_RELEASE" echo "Removed: $OLD_RELEASE" done fi # C07: Log Deployment echo "=== Log Deployment ===" DEPLOYMENT_LOG="/home/u227177893/domains/staging.societypal.com/deploy/shared/deployments.log" echo "[$(date '+%Y-%m-%d %H:%M:%S')] Deployment completed - Release: /home/u227177893/domains/staging.societypal.com/deploy/releases/20250818193527 - Commit: 7854807b15ee3c4a5c95cdc82f071c1669e1ab90" >> "$DEPLOYMENT_LOG" # Final status if [ "$HEALTH_STATUS" -eq 0 ]; then echo "✅ Deployment completed successfully!" else echo "⚠️ Deployment completed with warnings" fi echo "✅ Phase C completed" exit 0]

=== Phase C: Post-Deployment ===
=== Setup Public Access ===
✅ Backed up public_html to public_html.backup.20250818_194219
✅ public_html symlink created
=== Exit Maintenance Mode ===
⚠️ Artisan up command failed
✅ Maintenance mode deactivated
=== Clear OpCache ===
✅ OpCache cleared
=== Restart Queue Workers ===
✅ Queue workers signaled to restart
=== Health Checks ===
✅ Current symlink exists
❌ Laravel installation broken
✅ .env exists
✅ Storage linked
✅ Vendor directory exists
⚠️ Application returned HTTP 500
=== Cleanup Old Releases ===
=== Log Deployment ===
⚠️ Deployment completed with warnings
✅ Phase C completed
Time taken to run Phase C: Post-Deployment Commands (After Release) v2: 0.85 seconds


Cleaning up old releases

---

# 4- Finishing



Delivering webhook notifications


Sending emails


Saving build environment

Saved build environment language versions