---

# Universal Laravel Zero-Downtime Deployment Strategy

**Generated:** August 21, 2025
**Purpose:** Comprehensive strategy & Standards document for Laravel deployment flows, setup plans, and build strategies to achieve zero-error, zero-downtime deployments.

---

> Note: All project-specific names (e.g., paths, domains, etc.) in this document are examples. Please replace them with the actual values for your project. This strategy is designed to be universal and adaptable to any Laravel application, whether it's a fresh app installation from scratch, a git pull from a repository, or a .zip file from a marketplace like CodeCanyon.

## **Overall Vision & Goals**

### **Primary Objective**

### **. Primary Goal: A Universal Laravel Deployment Guide**

- **Objective**: Create a single, comprehensive, beginner-friendly guide for "Zero-Error, Zero-Downtime" deployments for any Laravel application (with or without JS).
- **Key Deliverables**:
  1. A master guide structured into three main sections:
     - **SECTION A**: Project Setup (Local) - sequence of md numbered files
     - **SECTION B**: Pre-Deployment & Build Preparation- sequence of md numbered files
     - **SECTION C**: Live Deployment Execution- sequence of md numbered files
  2. The guide must be presented as a sequence of markdown files (`1-xxx.md`, `2-xxx.md`, etc.).
  3. All steps must be detailed with commands, scripts, automation, checks, and expected results. No assumed knowledge.
  4. **Separation of Concerns**:
     - Clearly distinguish between **Laravel-specific build steps** (e.g., `php artisan optimize`) and **universal deployment steps** (e.g., cloning a repo, symlinking). This allows for easier adaptation to other tech stacks in the future.
     - Isolate user-configurable hooks (pre-release, mid-release, post-release) so they can be easily customized without altering the core deployment logic.
  5. **Beginner-Friendly Instructions**: All steps must be explicit. The guide should be usable by someone with limited deployment experience.
  6. **Incorporate All Comments**: Systematically review and integrat
  7. Establish cohesive system standards including:
     - Consistent admin-local structure across all sections
     - Unified path references and tag systems
     - Standardized visual indicators (🟢 Local Machine, 🟡 Builder VM, 🔴 Server, 🟣 SSH Commands)
     - User-configurable hooks (1️⃣ Pre-release, 2️⃣ Mid-release, 3️⃣ Post-release)
     - Builder Commands 🏗️ formatting
     - Proper step ordering and metadata formatting

## User-Customzaiable Pipelines -writtin

1. Build Commands Pipeline 🏗️: commands and config to build
2. User-configurable deplpyment Hooks: written to allow be used with all deployment same pipelines (using conditions, -p, etc)
   1. 1️⃣ Pre-release Hooks
      - Executed **before changes** are applied to server per release, once connected to the server.
      - Example use: settingup folders in deploy (ex: current, shared, releases maybe using -p to act only first time), Maintenance mode, backups, etc.
   2. 2️⃣ Mid-release Hooks
      - Executed **after files are uploaded but before release is active**.
      - Example use: Run DB migrations, cache warming, or checks for zero-downtime deployment.
   3. 3️⃣ Post-release Hooks
      - Executed **after files are uploaded but before release is active**.
      - Example use: Run DB migrations, cache warming, or checks for zero-downtime deployment.

Create a **truly universal, zero-error, zero-downtime deployment template that includes step by step begginer friendly guides, full scripts, exact substeps, tasks etc** that works identically for:

- ✅ All Laravel versions (8, 9, 10, 11, 12)
- ✅ With and without JavaScript (Blade, Vue, React, Inertia)
- ✅ Mix or Vite build systems
- ✅ First and subsequent deployments
- ✅ Any hosting type (dedicated, VPS, shared)
- ✅ Any deployment strategy (manual, GitHub Actions, DeployHQ)

### **Success Criteria**

- **Zero Error Guarantee:** No deployment failures due to dependency issues, version conflicts, or configuration problems, or any other issues..
- **Zero Downtime Promise:** True atomic deployments with instant rollback capability.
- **Universal Compatibility:** Same steps work for purchased CodeCanyon apps and custom developments. universal and adaptable to any Laravel application, whether it's a fresh app installation from scratch, a git pull from a repository, or a .zip file from a marketplace like CodeCanyon.
- **Beginner Friendly:** Complete step-by-step guidance without assumptions. granular, steps, substeps, exact details, full scripts, etc.

- **Build and Deploy Options we want to cover**: all include Github for Codebase. below are for build and deploy:
  1. **Only Local Machine and Server**: no deployer (only local machine and server ssh remote exteions, build on local and ssh zip and github repo codebase for version control),
  2. **DeployHQ** (has builder VM and deployer ssh setup with ability to add `pre-release`, `mid-release`, and `post-release` SSH (user) hooks
  3. Github Actions.
- Local Testing: `HERD PRO`.
  • we must have things as variables so we can use same steps for other projects other websites, different github repos, different commits, etc..we can maybe collect and store project, deployment variables in 1 json file and use or add for next updates, new file for new app, project etc.

1.  - For each Step give it a short name in code format next to its name in brackets, example [`3-xxx xxx` `xxx`] - 2-3 (short, easy to understand step puprose from reading it).
    - For Steps that are to be done on Builder add a tag `🟡-onBuilder`, for steps to be done on local Machine, add a tag `🟢-onLocalMachine`, for steps to be done on Server, add a tag `🔴-onServer`.
    - to help understand what steps that can be changed, edited ..et c- for the Phases A, B, C = AKA as: `pre-release`, `mid-release`, and `post-release` SSH (user) hooks, consider to add to the step another tag [`🟣 1️⃣ User-Commands: Pre-release Hook`], [`🟣` 2️⃣ `User-Commands: Mid-release Hook`],[`🟣` 3️⃣ `User-Commands: Post-release Hook`].
    - Consider including the concept of `deployignore` which shouldnt duplicate `gitignore` rather on top of any thing to ignore from github.
2.

- include variable `%path%` in each step or as u see fit if better per substep as below, with example- this will help ensure we are on right location before we start the step. Also any other paths can be defined based on it in the step, if needs to navigate to inside or outside.
  - including:
    - `%path-localMachine%`: the path at which the step comands start at (like its the location)
      - Example:
    - `%path-server%`: the path at which the step commands start at (like its the location)
    - `%path-Builder-VM%`: not sure if we need buider path if we use VM, but if we do the build on local machine
    and include an initial step to define and identify these path variables to ensure the whole flow doesnt break. it should be 1 value for the whole setup like 1 `%path-localMachine%` and 1
- `add variables` to have set at start to make this more dynamic, like github url, commit-start, end - also see and add variables based on deployHQ. and maybe add initial step to define variables.
- to help me as a begginer try to include explanation, improve the flow, no gaps that makes my wonder or confused or mis step - i mean dont assume that i would know something because its know as i am a begginer, must be begginer friendly.
- Consider that some devdependiecies would be needed for production, composer and npm, should we handle this before the github commit, do people try to identify these and include in production depencicies on local machine before generating lock files, if so how to identify (like if we work on other ppl apps maybe hard to identify as we didnt develop, any command to indetify ) or is the way to handle by commands in the building part.

```

i like the order of steps in 0000--A---Build-and-Deploy/2-MASTER-CHECKLIST-FINAL-Aug2025
but we need to seriously consider Cluade 4 and 3 as it kinda help organize stuff , remove  smoe of the admin local uneeded foldering, tho it seems to has some duplicate in our 0000--A---Build-and-Deploy/2-MASTER-CHECKLIST-FINAL-Aug2025 so we need to be careful, and do QC and create new checklist before final guides.

---
## Organized & Cleaned Step Order

### 1. **Adopt a Master Checklist as the Foundation**
- **Action**: Use the structure from `0000--A---Build-and-Deploy/2-MASTER-CHECKLIST-FINAL-Aug2025` as the primary sequence for the deployment flow.
- **Reasoning**: This checklist provides a validated and logical order of operations that should serve as the backbone of the final, universal guide.

### 2. **Incorporate and Reconcile External Insights**
- **Action**: Review and integrate valuable organizational ideas and streamlined steps from external sources (referred to as "Claude 4 and 3").
- **Numbered Bullets**:
    1.  **Simplify Folder Structure**: Evaluate if the `Admin-Local` folder structure can be simplified or better organized based on insights from the external suggestions.
    2.  **Identify and Merge Duplicates**: Carefully compare the steps from the external sources with the master checklist. Identify any overlapping or duplicate instructions.
    3.  **Prioritize Clarity and Necessity**: When merging, prioritize the version of the step that is clearer, more accurate, or more comprehensive. Eliminate true redundancy.

### 3. **Quality Control (QC) and Finalization**
- **Action**: Perform a thorough quality control pass on the newly merged checklist before creating the final, beginner-friendly guides.
- **Goal**: The output should be a single, cohesive, and non-redundant master checklist that is easy to follow and logically sound. This finalized checklist will then be used to generate the detailed, step-by-step deployment guides.
```

---

## **Architecture Strategy**

### **Three-Section Structure**

1. **Section A: Project Setup** - For When User starts a new Project, this will be the full steps and setups for the project setup, folders, scripts, local testing, herd, customization template, and all configuration and analysis on the local machine.
   - Start Point: On Local machine, to create Project Folder and setup, for subseqeuent deployments- users can skip these setup steps (a tag next to step name -FIRSTTIME-ONLY) can be used to indicate the step is done 1once per project (ex: creating github repo, ex: setting up Admin-Local, creating project card, creating scripts, etc).
2. **Section B: Prepare for Build and Deployment** - Pre-deployment validation and setup before committing code, build testing, scrip based and tools based code validations, testing etc where by end of this codebase goes to github and then the build artifcat is dealt with based on the build and deploy strategy chosen by user.
3. **Section C: Build and Deploy** - Actual deployment execution on the server or build environment.

### **Path Variables System**

A centralized configuration file (`deployment-variables.json`) will define all essential paths.

```json
{
  "paths": {
    "local_machine": "%path-localMachine%",
    "server_deploy": "%path-server%",
    "builder_vm": "%path-Builder-VM%",
    "shared_resources": "%path-shared%"
  }
}
```

### **Visual Identification System**

- 🟢 **Local Machine**: Developer workstation operations.
- 🟡 **Builder VM**: Build server/CI environment operations.
- 🔴 **Server**: Production server operations.
- 🟣 **User-Configurable**: SSH hooks and custom commands.
  - 1️⃣ **Pre-release hooks** - Before deployment.
  - 2️⃣ **Mid-release hooks** - During deployment.
  - 3️⃣ **Post-release hooks** - After deployment.
- 🏗️ **Builder Commands**: Build-specific operations.

---

## **Universal Dependency Management Strategy**

### **Core Problem Solved**

Deployments fail when development dependencies (like Faker, Telescope, Debugbar) are required in production for seeding, migrations, or specific configurations, or when environment versions (Composer, PHP, Node) do not align.

### **Universal Detection System**

### **Automated Tools Integration**

1. **PHPStan/Larastan** - Static analysis to find missing classes
2. **Composer Unused** - Detect unused dependencies
3. **Composer Require Checker** - Find missing dependencies
4. **Security Checker** - Identify vulnerabilities
5. **Custom Analyzers** - Project-specific dependency validation

A multi-layered approach to catch all dependency and environment issues _before_ deployment.

1. **Automated Tooling Integration**: The workflow will integrate and automate the following tools:
   - **PHPStan/Larastan**: Static analysis to find missing classes and dependencies used in code.
   - **Composer Unused**: Detects packages in `composer.json` that are not used anywhere in the code.
   - **Composer Require Checker**: Finds symbols (classes, functions) used in the code but not listed as a dependency.
   - **Security Checker**: Identifies known vulnerabilities in dependencies.
2. **Smart Pattern-Based Detection**: Scripts will scan the codebase for usage patterns of common dev packages in production-critical areas (e.g., migrations, seeders, service providers).

   ```bash
   # Smart Pattern-Based Detection for 12+ Common Packages
   PACKAGE_PATTERNS=(
       ["fakerphp/faker"]="Faker\\Factory|faker()|fake()"
       ["laravel/telescope"]="TelescopeServiceProvider|telescope"
       ["barryvdh/laravel-debugbar"]="DebugbarServiceProvider|debugbar"
       ["laravel/dusk"]="DuskServiceProvider|dusk"
       ["nunomaduro/collision"]="collision"
       ["pestphp/pest"]="pest|Pest"
       ["phpunit/phpunit"]="PHPUnit|TestCase"
       ["mockery/mockery"]="Mockery"
       ["laravel/sail"]="sail"
       ["laravel/pint"]="pint"
       ["spatie/laravel-ignition"]="ignition"
       ["barryvdh/laravel-ide-helper"]="ide-helper"
   )
   ```

3. **Environment Version Locking**: A `versions.lock` file will be generated and committed, capturing the exact versions of PHP, Composer, Node, and NPM from the local environment to ensure consistency in the build environment.

---

## **Build Strategy Variations [All use Github to version and use Codebase and pull to server, they differ where Build is done, and how transfered to Server]**

### **Strategy 1: Local Build + Manual Deployment**

Description:

- From Local machine, codebase is pushed to github with gitignore that also includes build artifacts. Git pull used to get codebase to server location.
- build artifacts are ssh or sftp zip to server location

**When to Use:** Simple projects, limited infrastructure, or when a builder VM is unavailable.
**Flow:**

1. 🟢 **Local Machine**: Full dependency analysis, version locking, and production build.
2. 🟢 **Local Machine**: Create a deployment package (`.zip` build artifact).
3. 🔴 **Server**: Manual upload and extraction to a new release directory.
4. 🔴 **Server**: Run deployment scripts (symlinking, cache clearing) for an atomic switch.

### **Strategy 2: GitHub Actions + Automated Deployment**

Description:

- From Local machine, codebase is pushed to github with gitignore that also includes build artifacts. Git pull used to get codebase to server location.
- build artifacts are **GitHub Actions** to server location
-

**When to Use:** Team environments, CI/CD integration.
**Flow:**

1. 🟢 **Local Machine**:
   1. **Section A:** setup project if first time (project card, Admin-Local full folder, setup git branching, **Universal .gitignore**, create project or pull a repo or extract zip if marketplace codecanyon, commit vendor original, create env files, setup and do local dev with HERD, setup customzation template, do all pre-deployment verfifications using scripts and tools, etc .
   2. Section B: **Install Dependencies & Generate Lock Files,** Pre-deployment validation and setup before committing code, build testing, scrip based and tools based code validations, testing etc where by end of this codebase goes to github and then the build artifcat is dealt with based on the build and deploy strategy chosen by user.
   3. repeat most steps except setup for new updated versions of app (ex get v2 zip, extract , etc to end)
2. 🟢 **Local Machine**: Run pre-commit analysis and push to GitHub.
3. 🟡 **GitHub Actions**: A workflow is triggered. It checks out the code, validates against `versions.lock`, installs dependencies, and builds assets.
4. 🟢🟡 **Local Machine and GitHub Actions**: The build artifact is packaged and securely transferred to the server.
5. 🔴 **Server**: A script on the server, triggered by GitHub Actions, performs the atomic symlink switch.

### **Strategy 3: DeployHQ**

Description:

- From Local machine, codebase is pushed to github with gitignore that also ignores build artifacts. DeployHQ builds + handle pulling code to server location. DeployHQ needs to be provided with exact build commands (build Pipeline), any pre-release, mid-release, post-release hooks.
- build artifacts are **DeployHQ** to server location

**When to Use:** Professional environments with multiple projects.
**Flow:**

1. 🟢 **Local Machine**:
   1. **Section A:** setup project if first time (project card, Admin-Local full folder, setup git branching, **Universal .gitignore**, create project or pull a repo or extract zip if marketplace codecanyon, commit vendor original, create env files, setup and do local dev with HERD, setup customzation template, do all pre-deployment verfifications using scripts and tools, etc .
   2. Section B: **Install Dependencies & Generate Lock Files,** Pre-deployment validation and setup before committing code, build testing, scrip based and tools based code validations, testing etc where by end of this codebase goes to github and then the build artifcat is dealt with based on the build and deploy strategy chosen by user.
   3. repeat most steps except setup for new updated versions of app (ex get v2 zip, extract , etc to end)
2. 🟢 **Local Machine**:Section B Run pre-commit analysis and push to GitHub.
3. 🟡 **DeployHQ**: Clones the repository and get build and transfer (deploy) done
4. 🟡 **DeployHQ VM**: Clones the repository, runs the build process in a clean, professional environment.
5. 🟡 **DeployHQ**: Executes pre-release, mid-release, and post-release SSH hooks for custom commands.
6. 🔴 **Server**: Receives the built files and performs the zero-downtime deployment.

### **Strategy 4: Hybrid Server Build (or any future different way**

Description:

- From Local machine, codebase is pushed to github with gitignore that also includes build artifacts. Git pull used to get codebase to server location.
- build artifacts are **DeployHQ** to server location

Note: having Phases in 3- Build deploy help being able to apply to any as 10-phases allow to use the all phases if user wants to do things them selves, to the extreme of only using build commands (build Pipeline), any pre-release, mid-release, post-release hooks.

**When to Use:** When the production server has sufficient resources and build tools installed.
**Flow:**

1. 🟢 **Local Machine**:
   1. **Section A:** setup project if first time (project card, Admin-Local full folder, setup git branching, **Universal .gitignore**, create project or pull a repo or extract zip if marketplace codecanyon, commit vendor original, create env files, setup and do local dev with HERD, setup customzation template, do all pre-deployment verfifications using scripts and tools, etc .
   2. Section B: **Install Dependencies & Generate Lock Files,** Pre-deployment validation and setup before committing code, build testing, scrip based and tools based code validations, testing etc where by end of this codebase goes to github and then the build artifcat is dealt with based on the build and deploy strategy chosen by user.
   3. repeat most steps except setup for new updated versions of app (ex get v2 zip, extract , etc to end)
2. 🟢 **Local Machine**: Section B Run pre-commit analysis and push to GitHub.
3. 🔴 Local or Builder VM or Server: Runs the full dependency installation and asset build process directly on the server.
4. 🔴 **Server**: Git pulls the latest code into a new release directory.
5. 🔴 **Server**: Performs the atomic deployment in the same environment.

---

## **Zero-Downtime Deployment Flow**

### **Critical Zero-Downtime Elements**

1. **Atomic Symlink Switch**: The `current` directory is a symlink. The deployment script points it to the new release directory instantly.
2. **Shared Resources**: Directories like `storage/app/public` and `public/uploads` are symlinked from a shared location into each release, ensuring data persistence.
3. **Backward-Compatible Database Migrations**: Migrations are written to be non-blocking (e.g., adding columns before removing old ones).
4. **OPcache & Application Cache Management**: A multi-tier strategy to clear PHP's OPcache, and Laravel's application, config, and route caches.
5. **Queue Worker Restart**: A graceful restart of queue workers (`php artisan queue:restart`) ensures they use the new code.
6. **Health Checks & Automated Rollback**: After the symlink switch, a health check verifies application status. If it fails, the symlink is immediately reverted to the previous release.

---

## **Comprehensive Shared Resources Strategy**

To prevent data loss, the following directories (and any project-specific ones) will be stored in a `shared` directory and symlinked into each new release.

### **Standard & User Content Directories**

```bash
shared_directories=(

# Laravel Standard Directories
    "storage/app/public"      # Laravel default storage link
    "storage/logs"           # Application logs
    "storage/framework/cache" # Framework cache
    "storage/framework/sessions" # User sessions
    "storage/framework/views"    # Compiled views

# User Content Directories
    "public/uploads"     # Common upload pattern
    "public/avatars"     # User profile pictures
    "public/documents"   # User documents
    "public/media"       # General media files
    "public/exports"     # Generated reports/exports
    "public/qr-codes"    # Generated QR codes
    "public/invoices"    # Generated invoices

# Application-Specific Directories
    "Modules"           # Modular applications
    "packages"          # Custom packages
    "plugins"           # Plugin systems
    "themes"            # Theme systems
    # ... add any other custom user content directories
)

```

---

## **Pitfall Prevention Strategy**

This strategy is designed to prevent all common Laravel deployment pitfalls.

### **Category 1: Dependency & Build Issues**

1. **Dev Dependencies in Production**: Solved by the Universal Dependency Detection System.
   - Universal detection for 12+ common packages
   - Auto-fix with composer require/remove commands
   - Production build validation
2. **Composer Version Conflicts**: Solved by forcing Composer v2 in build environments and using `versions.lock`.
   - Force Composer 2 installation per domain unless composer 1 is required by app. ensure applied to builder and server.
   - Lock file version validation
   - Plugin compatibility checks
3. **Build Artifact Corruption**: Solved by running validation checks before and after the build process.
   - Smart dependency installation based on analysis
   - Multiple validation checkpoints
   - Integrity verification with checksums
4. **Composer Version Conflicts**
   - Force Composer 2 installation per domain
   - Lock file version validation
   - Plugin compatibility checks

### **Category 2: Environment Issues (High)**

1. **Version Mismatches (PHP, Node)**: Solved by validating against `versions.lock` in the build environment.
   1. **Version Mismatches**
      - PHP, Composer, Node, NPM version alignment
      - Lock file consistency validation
      - Platform requirement checks
   2. **Missing Extensions**
      - Required PHP extension detection
      - Disabled function validation (exec, symlink)
      - Auto-fix command generation

### **Category 3: Server & Environment Issues**

1. **User Data Loss**: Solved by the Comprehensive Shared Resources Strategy.
   - Comprehensive shared directory coverage
   - Smart detection of custom upload paths
   - Atomic operations for all changes
2. **OPcache/Application Cache Problems**: Solved by the multi-tier cache clearing strategy.
   - 3-tier cache clearing (cachetool, web-endpoint, php-fpm-reload)
   - Verification after clearing
   - Fallback methods for shared hosting
3. **Database Migration Downtime**: Addressed by enforcing zero-downtime migration patterns.
   - Zero-downtime migration patterns
   - Backward compatibility requirements
   - Rollback-safe schema changes

### **Category 3: Hosting & Configuration Issues**

1. **Shared Hosting (`public_html`)**: The deployment script will handle renaming an existing `public_html` directory and creating a symlink to `current/public`, ensuring a secure document root.
   - Public_html symlink strategies
   - Function disabled workarounds
   - Permission management
2. **Queue Worker Problems**: Solved by including a graceful `queue:restart` in the post-deploy script.
   - Graceful worker restart procedures
   - Horizon integration handling
   - Supervisor configuration updates
3. **Missing PHP Extensions / Disabled Functions**: Solved by a pre-flight check script that validates the server environment against application requirements.
4. **Incorrect File Permissions**: Post-deployment scripts will set the correct ownership and permissions for directories like `storage` and `bootstrap/cache`.
5. **Insecure `.env` file handling**: The `.env` file is never committed to git. It is managed securely on the server and symlinked into each release from the `shared` directory.

### **Security and Configuration**

- **Objective**: Secure the application and manage environment-specific configurations properly.
- **Numbered Bullets**:
  1. **Environment File (`.env`)**:
     - Establish a secure workflow for managing production `.env` files.
     - The `.env` file should never be in Git. It should be placed in the `shared` directory on the server and symlinked into each new release.
  2. **`.htaccess` Configuration**: Provide template `.htaccess` files for security headers, HTTPS redirection, and routing requests to Laravel's `public` directory.
  3. **File Permissions**: Specify the correct file and directory permissions needed for Laravel to run without issues.
  4. **`deployignore`**: Introduce the concept of a `.deployignore` file to exclude files from the deployment artifact that are not covered by `.gitignore`.

**Shared Resources**:

- Implement a robust symlinking strategy for shared directories (`storage`, `.env`) and user-generated content folders (`public/uploads`, `public/avatars`) to prevent data loss.
- Handle the `public_html` directory structure common on shared hosting.

1. **Database Migrations**: Emphasize zero-downtime migration patterns (e.g., add new columns before removing old ones).
2. **Cache and Queues**: Include mandatory steps to clear application/OPcache and restart queue workers to ensure the new code is active.
3. **Environment Compatibility**:
   - Add pre-flight checks for required PHP extensions (`pdo`, `openssl`, `mbstring`) and server functions (`exec`).
   - Ensure necessary tools (`git`, `curl`, `mysql`) are installed on the server/builder.

---

## **Error Recovery & Rollback Strategy**

### **Automatic Rollback Triggers**

- Health check endpoint returns a non-200 status code.
- Database connection loss
- A critical post-deploy command (e.g., `artisan migrate`) fails.
- Application fails to boot after the switch.
- Critical file system errors
- Application boot failures

### **Recovery Procedures: Rollback Procedure**

A simple, fast script to revert to the previous release.

```bash
# Quick rollback to previous release
PREVIOUS=$(ls -1t releases/ | head -2 | tail -1)
ln -nfs "releases/$PREVIOUS" current
php artisan up
opcache_reset()
systemctl reload nginx
```

### **Rollback Validation**

- Application accessibility test
- Database connectivity verification
- Critical functionality validation
- Performance baseline confirmation

---

## **Quality Assurance Strategy**

### **Pre-Deployment Validation (10-Point Checklist)**

1. Environment configuration validation
2. Dependencies installation verification
3. Database connectivity testing
4. Build process validation
5. Security configuration review
6. File permissions verification
7. Git repository status check
8. Configuration files validation
9. Deployment variables verification
10. Application health validation

### **Post-Deployment Verification**

1. HTTP status code validation
2. Database migration status
3. Queue worker status
4. Cache system functionality
5. File upload capability
6. Authentication system
7. Critical business logic
8. Performance metrics
9. Error log monitoring
10. User acceptance testing

### **Pre-Deployment Validation (Local)**

A script (`pre-deployment-validation.sh`) will run a 10-point checklist locally before allowing a commit to be pushed, covering dependency analysis, environment checks, and build viability.

### **Post-Deployment Verification (Server)**

After the symlink switch, a health check script will verify:

- HTTP status code of the homepage.
- Database connectivity.
- Queue worker status.
- Critical application functionality via a dedicated health check endpoint.

This strategy document serves as the foundation for creating truly universal, beginner-friendly, zero-error Laravel deployment workflows that adapt to any project configuration while maintaining professional deployment standards.

---

## **Staging & Production Environments**

### **Staging Environment**

- **Purpose**: A mirror of the production environment used for final testing before deployment.
- **Workflow**:
  1. Deploy to staging using the same automated process as production.
  2. Run a full suite of tests (automated and manual) to ensure everything is working as expected.
  3. Obtain final approval before deploying to production.

### **Production Environment**

- **Purpose**: The live environment for end-users.
- **Workflow**:
  1. Once the staging environment is verified and approved, the exact same build is deployed to production.
  2. A final health check is performed to ensure the deployment was successful.

---

## **Builder Pipeline Commands & SSH Hooks**

### **Builder Pipeline Commands**

- **Purpose**: To isolate technology-specific commands from the universal deployment flow.
- **Strategy**: Each project will have a `build-commands.sh` script that contains all the necessary commands for building the application. This allows the main deployment script to remain generic and reusable.
- **Example**:
  ```bash
  # build-commands.sh for a Laravel with Vite project
  composer install --no-dev --optimize-autoloader
  npm install
  npm run build

  ```

### **User SSH Hooks**

- **Purpose**: To provide customizable entry points for project-specific commands during the deployment process.
- **Strategy**: The deployment script will call three separate scripts at different stages of the deployment:
  - `pre-release-hook.sh`: Runs before the build process.
  - `mid-release-hook.sh`: Runs after the build but before the atomic switch.
  - `post-release-hook.sh`: Runs after the deployment is live.
- **Example**:
  ```bash
  # post-release-hook.sh
  php artisan migrate --force
  php artisan cache:clear
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear

  ```

---

## **Local Development Environment**

### **Primary Tool: Herd Pro**

- **Purpose**: To provide a consistent and easy-to-use local development environment that mirrors the production server as closely as possible.
- **Strategy**: All developers will use Herd Pro to manage their local stack (PHP, Nginx, DnsMasq), ensuring that all team members are working in the same environment.

### **Automation & Tooling**

- **Purpose**: To streamline the development process and reduce manual errors.
- **Strategy**: We will leverage a suite of automated tools and scripts to handle common tasks, such as dependency analysis, code linting, and pre-deployment checks. While we encourage automation, we will always prioritize powerful and reliable results over shortcuts.
