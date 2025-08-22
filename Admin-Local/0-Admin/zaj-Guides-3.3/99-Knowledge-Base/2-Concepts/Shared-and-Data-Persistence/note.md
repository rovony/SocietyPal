- **Auto-persist any “custom public output” dirs (no manual lists)**
    
    Add this tiny script to your repo at `scripts/link_persistent_dirs.sh`. It **auto-detects** common runtime output folders under `public/` (uploads, qrcodes, exports, invoices, avatars, etc.), creates matching paths under `shared/`, and symlinks them — so user data survives every deploy with zero guesswork.
    
    Use it in both bootstrap new clone from git, and deploy (see below). It’s safe to re-run; it only links dirs that exist or match the patterns. If a vendor uses a weird name not matched, nothing breaks — you can add that one name to the pattern later, but day-1 deploy still succeeds.
    
    ```
    #!/usr/bin/env bash
    # scripts/link_persistent_dirs.sh
    # Auto-detect & persist runtime public dirs safely (idempotent).
    
    set -euo pipefail
    
    APP_ROOT="${1:-$(pwd)}"
    SHARED_ROOT="${2:-$APP_ROOT/../shared}"   # on servers, shared sits next to releases
    PUB_ROOT="$APP_ROOT/public"
    
    # 1) Always persist framework runtime
    mkdir -p "$SHARED_ROOT"
    if [[ -d "$APP_ROOT/storage" ]]; then
      mkdir -p "$SHARED_ROOT/storage"
      # storage symlink (Laravel)
      if [[ ! -L "$APP_ROOT/storage" ]]; then
        rm -rf "$APP_ROOT/storage" || true
        ln -s "$SHARED_ROOT/storage" "$APP_ROOT/storage"
      fi
      # public/storage symlink (artisan storage:link equivalent)
      mkdir -p "$SHARED_ROOT/public/storage"
      mkdir -p "$PUB_ROOT" || true
      if [[ ! -L "$PUB_ROOT/storage" ]]; then
        rm -rf "$PUB_ROOT/storage" || true
        ln -s "$SHARED_ROOT/public/storage" "$PUB_ROOT/storage"
      fi
    fi
    
    # 2) Detect custom public output dirs by name heuristics (safe set)
    # Add patterns carefully; avoid generic names like "css", "img", "js".
    PATTERN='(upload|user-uploads|uploads|qrcode|qr-codes|export|exports|invoice|invoices|avatar|avatars|profile-photos|media-files|generated|backups|temp|tmp|thumbs)'
    mapfile -t CANDIDATES < <(find "$PUB_ROOT" -maxdepth 2 -type d -regex ".*/$PATTERN" 2>/dev/null || true)
    
    for dir in "${CANDIDATES[@]}"; do
      rel="${dir#$PUB_ROOT/}"                             # e.g., user-uploads
      tgt="$SHARED_ROOT/public/$rel"                      # shared/public/user-uploads
      lnpath="$PUB_ROOT/$rel"                             # public/user-uploads (symlink location)
    
      mkdir -p "$tgt"
      # security & placeholder
      [[ -f "$tgt/.gitkeep" ]] || touch "$tgt/.gitkeep"
      if [[ ! -f "$tgt/.htaccess" ]]; then
        printf "Options -Indexes\n<IfModule mod_autoindex.c>\nIndexOptions -FancyIndexing\n</IfModule>\n" > "$tgt/.htaccess"
      fi
    
      # replace any real dir with symlink
      if [[ -d "$lnpath" && ! -L "$lnpath" ]]; then
        rm -rf "$lnpath"
      fi
      if [[ ! -L "$lnpath" ]]; then
        ln -s "$tgt" "$lnpath"
      fi
    done
    
    echo "Persistent runtime links complete."
    
    ```
    
    ## **One-click commands you run (short and final)**
    
    1. Local (first clone)
        
        ```
        cp .env.example .env
        composer install --no-dev --prefer-dist --optimize-autoloader
        
        # If package.json exists:
        npm ci && npm run build || true
        
        # Laravel niceties (safe if not Laravel):
        php artisan key:generate   || true
        php artisan storage:link   || true
        php artisan config:cache   || true
        php artisan route:cache    || true
        php artisan view:cache     || true
        php artisan migrate --force || true
        
        # Persist runtime dirs locally (optional):
        bash scripts/link_persistent_dirs.sh "$(pwd)" "$(pwd)/.shared"
        
        ```
        
        Start:
        
        - Laravel: `php artisan serve -q --host=0.0.0.0 --port=8000`
        - CI4: `php spark serve`
        - Generic: `php -S 0.0.0.0:8000 -t public`
    2. Server deploy (zero data loss)
        
        ```
        # 1) Build on CI or build box (recommended):
        composer install --no-dev --prefer-dist --optimize-autoloader
        [ -f package.json ] && npm ci && npm run build || true
        
        # 2) Upload repo to a new folder, e.g. /var/www/myapp/releases/2025-08-11_1200
        # 3) Create/update shared and symlinks:
        bash scripts/link_persistent_dirs.sh "/var/www/myapp/releases/2025-08-11_1200" "/var/www/myapp/shared"
        
        # 4) DB migrate (safe if no migrations):
        php artisan migrate --force || true
        
        # 5) Atomically switch:
        ln -sfn /var/www/myapp/releases/2025-08-11_1200 /var/www/myapp/current
        
        ```
        
        Verification (30 seconds):
        
        ```
        test -L /var/www/myapp/current/public/storage
        [ -d /var/www/myapp/shared/public ] && echo "shared public ok"
        
        ```
        
    
    # What you get (your goals checked)
    
    - No user data loss → runtime dirs live in `/shared`, symlinked each release.
    - No overwrites of vendor/admin/demo files → all **source stays in Git** and is redeployed read-only in each release.
    - Reproducible app → `composer install` + optional JS build reconstructs everything.
    - First & subsequent deploys identical → same script, idempotent links.
    
    ---

- `First` vs `Subsequent` deployments
    - **First deploy vs subsequent deploys?**
        - First deploy adds: create `shared/`, set `.env`, `storage:link`, initial DB migrate/seed, set `public_html → current/public`.
        - Subsequent deploys: upload new release, symlink to shared, run migrations (if any), warm caches, switch symlink, prune old releases.
------
- Persistent data (user `shared` data, `storage/` etc)
    - storage/public/symlinks (persistent data)
        - Put persistent data in `shared/`:
            - `shared/.env`
            - `shared/storage/`
            - `shared/public/uploads/` (if app writes there)
        - Symlink them into each new release before switching `current`.
        - On hosts with fixed `public_html`, set `public_html → current/public` once; ensure FollowSymlinks is allowed.
----
- persistent data
    - anything users upload or the app generates must live in `shared/` so it survives releases
        - `shared/storage/` (entire storage)
        - `shared/public/uploads/` (if app writes there)
    - always symlink those into each new release before switching `current`
    - for hosts with fixed `public_html` (folder), during first time setup: backup  the `public_html`folder and create a file point `public_html -> current/public` (one-time). ensure `.htaccess` supports `Options +FollowSymLinks` if required by the host.
    - on domain path in server have the zero down time deployments all in a folder named deploy, which will be kn same level as public_html. adjust symlinks to use these paths.

----
2. on server or using envoyer  - deploy (zero-downtime + shared)
    - shared will have .env,  storage/, public/uploads/  . releases will have timestamped folders. current symlink to timestamped release
    - steps
        1. ENSURE PHP and NODE VERSIONS: Server has PHP/Node versions to match your build environment (e.g., PHP 8.2, Node 18).
        2. PREPARE DOMAIN FOLDERS (FIRST DEPLOY ONLY)
            1. Create inside domain folder the followings:
                1. shared, releases folders
                2. inside shared, create .env and fill it per enviromentment details (create `shared/.env` and fill it w with DB creds, mail, cache, queues.)
                    1. ensure the env has key, if not run run: `php artisan key:generate` and copy key to env file
        3. deploy process is as the following
            1. UPLOAD: from github, upload the built release to new timestamped fodler  `releases/TIMESTAMP/` (exclude .git, node_modules, etc).
            2. SYMLINK: several items 
                1. shared items
                    1. env file: `rm -rf releases/TIMESTAMP/.env && ln -s ../../shared/.env releases/TIMESTAMP/.env`
                    2. storage folder: `rm -rf releases/TIMESTAMP/storage && ln -s ../../shared/storage releases/TIMESTAMP/storage`
                    3. public uploads: `ln -s ../../shared/public/uploads releases/TIMESTAMP/public/uploads`
            3. OPERATIONS - POST-DEPLOY:
                1. migrations: `php artisan migrate --force`
                2. `php artisan storage:link` (first time)
                3. warm caches
            4. ATOMIC POINT CURRENT:
                1. atomically point `current` to new release: `ln -sfn releases/TIMESTAMP current`
            5. OPERATIONS - POST- ATOMIC
                1. smoke test
                2. SERVER  VERIFICATIONS ?
                    - DB connectivity ok (homepage loads; a trivial query page).
                    - assets load (public/build exists in release).
                    - correct PHP version: `php -v` on server.
            
            ```bash
            # upload to releases/TIMESTAMP
            ln -s ../../shared/.env releases/TIMESTAMP/.env
            rm -rf releases/TIMESTAMP/storage && ln -s ../../shared/storage releases/TIMESTAMP/storage
            [ -d shared/public/uploads ] || mkdir -p shared/public/uploads
            ln -s ../../shared/public/uploads releases/TIMESTAMP/public/uploads
            php -d detect_unicode=0 -d opcache.enable_cli=1 artisan migrate --force --no-interaction -q
            php artisan optimize:clear && php artisan config:cache && php artisan view:cache
            ln -sfn releases/TIMESTAMP current
            ```



-----
