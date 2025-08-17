- Shared, and Excluded Files
    - Important Notes
        - **Images, logos, and static application assets should NOT be shared (i.e., added to shared folder in zero downtime deployment folder)** - they should be deployed with your code. The confusion comes from thinking everything in `public/` needs to be shared, but that's incorrect.
        - Summary of strategy
            - **Core Principle**: "Is this user/runtime data?" → Yes = Share it, No = Deploy as code
            - **Universal Shared List**: `.env`, `storage/`, `bootstrap/cache/`, `public/.well-known/`
            - **Pattern-Based Detection**: `public/*upload*`, `public/*avatar*`, `public/*media*`, `public/qr*`
            - **Safe Default**: When in doubt, deploy as application code (prevents data loss)
    - Definitions
        - **SHARED FOLDERS and Files (Zero-Downtime Assets):**
            
            **Definition**: Files and directories that must persist across deployments to prevent data loss and maintain application state.
            
            **Characteristics:**
            
            - User-generated content that would be lost if overwritten
            - Application runtime data (cache, sessions, logs)
            - Environment-specific configuration that shouldn't change
            - Must exist in the same location across all releases
            
            **Actions Required:**
            
            1. ✅ Add to shared folders (symlinked from `/shared/`)
            2. ✅ Add to Excluded Files or `.deployignore` (excluded from file sync)
            3. ✅ Add to `.gitignore` (excluded from version control)
        - **🚀 APPLICATION CODE (Deployable Assets)**
            
            **Definition**: Files that are part of your application's codebase and should be updated with each deployment.
            
            **Characteristics:**
            
            - Static assets that are part of the app even public (flags, logos, themes)
            - Compiled/built assets (CSS, JS, optimized images)
            - Application logic and configuration templates
            - Custom code and design changes
            
            **Actions Required:**
            
            1. ✅ Keep in version control (Git)
            2. ✅ Deploy with each release
            3. ❌ Do NOT add to shared folders
            4. ❌ Do NOT exclude from deployment
    - **Universal Exclusion-Based Strategy - The Simple Rule - LISTS** ⭐
        1. **STEP 1:** Identify what goes to SHARED folders (and therefore is excluded (or deployignore). 
        2. **STEP 2:** Everything else is APPLICATION CODE
            
            This is much cleaner than trying to predict every possible folder name
            
            ---
            
        
        **Easy 3-Step Strategy**
        
        1. **Step 1: Always add these to Shared  (Universal List)** (and therefore is excluded (or deployignore). 
            
            ```bash
            # ALWAYS SHARED - No matter what the app is:
            .env                    # Environment config
            storage/                # All Laravel storage (logs, cache, sessions, user files)
            bootstrap/cache/        # Laravel system cache
            
            # ALWAYS SHARED - User content (any naming variation):
            public/*upload*/        # Any folder with "upload" in name
            public/*avatar*/        # Any folder with "avatar" in name  
            public/*media*/         # Any folder with "media" in name
            public/qr*/             # QR codes or similar generated content
            public/.well-known/     # SSL certificates
            
            ```
            
        2. **Step 2: Everything Else = Application Code**
            - NO DECISIONS NEEDED - If it's not in Step 1, it's app code. `examples` can include:
                
                ```bash
                public/flags/           # App code (even if varies by app)
                public/img/             # App code (logos, icons, etc.)
                public/themes/          # App code (app themes)
                public/build/           # App code (compiled assets)
                public/.htaccess        # App code (web config)
                public/robots.txt       # App code (SEO config)
                vendor/                 # App code (dependencies)
                # ... literally everything else
                
                ```
                
        3. **Step 3: Verification Questions**
            - Ask yourself for ANY folder: **"If this gets overwritten during deployment, will users lose their data or will the app break functionality?"**
                - **Users lose data** → Add to Shared folder  (and therefore excluded (or added to deployignore).
                - **App breaks/missing features** → Deploy it as code
                - **Not sure** → Deploy as code (safer default)
    - **SHARED PATTERNS**: Common Patterns of Files and Folders should be in Shared Folders (and excluded)
        1. User Content Variations Pattern: Anything users create, upload etc → added to SHARED folders (and therefore is excluded (or deployignore). 
            - `public/upload*/`              # Covers: `public/upload`, `public/uploads`, `public/uploaded`
            - `public/user-upload*/`         # Covers: `public/user-uploads`, `public/user-upload`
            - `public/media*/`               # Covers: `public/media`, `public/medias`
            - `public/avatar*/`              # Covers: `public/avatar`, `public/avatars`, `public/clientAvatar`
            - `public/attachment*`/          # Covers: `public/attachments`, `public/attachment`
            - `public/document*/`            # Covers: `public/documents`, `public/document`
            - `public/file*/`                # Covers: `public/files`, `public/file`
            - `public/image*/`             # Covers: `public/images` when user-generated (not app assets)
        2. User Generated Content: 
            - public/qrcode*/              # Generated QR codes
            - public/barcode*/             # Generated barcodes
            - public/certificate*/         # Generated certificates
            - public/report*/              # Generated reports
            - public/temp*/                # Temporary files
        3. Runtime System Data
    - **Expert-Identified Edge Cases**
        
        Based on the Perplexity expert analysis:
        
        all below should be treated as code, which further validates our 3 steps strategy.
        
        - **CodeCanyon Patterns**:  `public/themes/`, `public/plugins/`, `public/assets/uploads/`
        - **Multi-tenant**: `public/tenant-{id}/`, `storage/app/tenants/`
        - **CMS-Style**: `public/gallery/`, `public/attachments/`
        - **Package-Specific**: `storage/app/livewire-tmp/`, `storage/app/public/`
        
        HOWEVER:  The pre-populated content logic is brilliant and solves the CodeCanyon demo data challenge. You're absolutely right that most edge cases are simply "`app code`" - therefore our 3-step strategy covers everything. 
        
        - **CodeCanyon themes/plugins** → App code (unless user-customizable)
        - **Multi-tenant patterns** → App code (structure) + Shared (user data)
        - **CMS galleries** → App code (system) + Shared (user uploads)
        
        **The rule remains**: "Is this user/runtime data?" → Yes = Share, No = App code
        
    - **Pre-Populated Content-And-FIRST-DEPLOYMENT Strategy (Critical Addition)**
        
        New Logic for guides steps and for AI Template:
        
        ### Step 1A: Check for Pre-Populated Content
        
        For each potential shared directory:
        
        1. **If directory has files** → Upload first during initial deployment → Then add to shared & exclude
        2. **If directory is empty** → Add to shared & exclude from first deployment
        3. **Reasoning**: Preserves demo data, admin examples, seed content that becomes user-modifiable
    - **Build Strategy Scenarios (Color-Coded)**
        
        **🏗️ Scenario A: Pre-Build (Recommended Production)**
        
        ```markdown
        ### Step X: Build Assets
        🟢 **Scenario A (Pre-Build)**: 
        - Build locally or in CI/CD
        - Include in Git: `vendor/`, `node_modules/`, `public/build/`
        - ✅ Faster deployments
        - ✅ Consistent builds
        
        🔴 **Scenario B (Build on Server)**: Skip this step
        🟡 **Scenario C (Hybrid)**: Build only CSS/JS, include vendor/
        ```
        
        **🏗️ Scenario B: Build on Server**
        
        ```markdown
        ### Step X: Install Dependencies  
        🔴 **Scenario B (Build on Server)**:
        - SSH Command: `composer install --no-dev --optimize-autoloader`
        - SSH Command: `npm ci && npm run production`
        - ⚠️ Longer deployment time
        - ⚠️ Requires Node.js on server
        
        🟢 **Scenario A (Pre-Build)**: Skip this step  
        🟡 **Scenario C (Hybrid)**: Only `composer install`
        
        ```
        
        **🏗️ Scenario C: Hybrid**
        
        ```markdown
        ### Step X: Mixed Build Strategy
        🟡 **Scenario C (Hybrid)**:
        - Pre-build: CSS/JS assets (`npm run production` in CI)
        - Server-build: Dependencies (`composer install` via SSH)
        - Include in Git: `public/build/`
        - Exclude from Git: `vendor/`, `node_modules/`
        - ⚖️ Balanced approach
        
        ```
        
    - **FINAL SYMLINKS SETUP LIST**
        - **🌐 Primary Web Entry Point (CRITICAL)**
            
            ```bash
            # Main web root symlink - THIS IS THE MOST IMPORTANT ONE
            # Points the web server to the current release's public directory
            ln -sfn %deploy_path%/current/public %home_path%/public_html
            
            # Alternative naming based on hosting provider:
            # ln -sfn %deploy_path%/current/public %home_path%/www
            # ln -sfn %deploy_path%/current/public %home_path%/htdocs
            
            ```
            
        - **📁 Core Laravel Symlinks (Standard)**
            
            ```bash
            # Laravel storage symlink (standard Laravel)
            ln -sfn %shared_path%/storage/app/public %release_path%/public/storage
            
            # Environment file
            ln -sfn %shared_path%/.env %release_path%/.env
            
            # Bootstrap cache (directory symlink)
            rm -rf %release_path%/bootstrap/cache
            ln -sfn %shared_path%/bootstrap/cache %release_path%/bootstrap/cache
            
            # Storage directory (full directory symlink)  
            rm -rf %release_path%/storage
            ln -sfn %shared_path%/storage %release_path%/storage
            
            # SSL/Domain verification
            ln -sfn %shared_path%/public/.well-known %release_path%/public/.well-known
            
            ```
            
             The `public_html` symlink is the **most critical** symlink for zero-downtime deployment and securty
            
        - **📤 User Content Symlinks (App-Specific, varies , should be decided based on manual inspection and AI template)**
            
            ```bash
            # Pattern-based (generated per app):
            ln -sfn %shared_path%/public/user-uploads %release_path%/public/user-uploads
            ln -sfn %shared_path%/public/qrcodes %release_path%/public/qrcodes
            ln -sfn %shared_path%/public/upload %release_path%/public/upload
            ln -sfn %shared_path%/public/uploads %release_path%/public/uploads
            ln -sfn %shared_path%/public/avatar %release_path%/public/avatar
            # ... (one for each detected user content directory)
            
            ```
            
    - **Directory Structure Visualization**
        
        ```markdown
        /home/username/
        ├── deploy/                           # Deployment directory
        │   ├── current -> releases/20240131-123456/  # Current release symlink
        │   ├── releases/                     # All releases
        │   │   ├── 20240131-123456/         # Latest release
        │   │   │   ├── app/
        │   │   │   ├── public/              # Laravel public directory
        │   │   │   │   ├── index.php
        │   │   │   │   ├── storage -> /shared/storage/app/public
        │   │   │   │   ├── user-uploads -> /shared/public/user-uploads
        │   │   │   │   └── .well-known -> /shared/public/.well-known
        │   │   │   ├── storage -> /shared/storage
        │   │   │   ├── bootstrap/cache -> /shared/bootstrap/cache
        │   │   │   └── .env -> /shared/.env
        │   │   └── 20240131-120000/         # Previous release
        │   └── shared/                       # Shared data
        │       ├── storage/
        │       ├── bootstrap/cache/
        │       ├── .env
        │       └── public/
        │           ├── user-uploads/
        │           ├── .well-known/
        │           └── qrcodes/
        └── public_html -> deploy/current/public/  # 🌟 WEB ROOT SYMLINK 🌟
        
        ```