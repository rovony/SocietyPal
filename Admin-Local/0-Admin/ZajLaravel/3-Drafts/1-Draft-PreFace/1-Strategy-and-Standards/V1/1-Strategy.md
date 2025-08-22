# Universal Laravel Zero-Downtime Deployment Strategy

**Generated:** August 21, 2025
**Purpose:** Comprehensive strategy document for Laravel deployment flows, setup plans, and build strategies to achieve zero-error, zero-downtime deployments.

---
> **Note:** All project-specific names (e.g., paths, domains, etc.) in this document are examples. Please replace them with the actual values for your project. This strategy is designed to be universal and adaptable to any Laravel application, whether it's a fresh installation, a `git pull` from a repository, or a `.zip` file from a marketplace like CodeCanyon.

## **Overall Vision & Goals**

### **Primary Objective**
Create a **truly universal, zero-error, zero-downtime deployment template** that works identically for:
- ✅ All Laravel versions (8, 9, 10, 11, 12)
- ✅ With and without JavaScript (Blade, Vue, React, Inertia)
- ✅ Mix or Vite build systems
- ✅ First and subsequent deployments
- ✅ Any hosting type (dedicated, VPS, shared)
- ✅ Any deployment strategy (manual, GitHub Actions, DeployHQ)

### **Success Criteria**
- **Zero Error Guarantee:** No deployment failures due to dependency issues, version conflicts, or configuration problems.
- **Zero Downtime Promise:** True atomic deployments with instant rollback capability.
- **Universal Compatibility:** Same steps work for purchased CodeCanyon apps and custom developments.
- **Beginner Friendly:** Complete step-by-step guidance without assumptions.

---

## **Architecture Strategy**

### **Three-Section Structure**
1.  **Section A: Project Setup** - One-time configuration and analysis on the local machine.
2.  **Section B: Prepare for Build and Deployment** - Pre-deployment validation and setup before committing code.
3.  **Section C: Build and Deploy** - Actual deployment execution on the server or build environment.

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
A multi-layered approach to catch all dependency and environment issues *before* deployment.

1.  **Automated Tooling Integration**: The workflow will integrate and automate the following tools:
    *   **PHPStan/Larastan**: Static analysis to find missing classes and dependencies used in code.
    *   **Composer Unused**: Detects packages in `composer.json` that are not used anywhere in the code.
    *   **Composer Require Checker**: Finds symbols (classes, functions) used in the code but not listed as a dependency.
    *   **Security Checker**: Identifies known vulnerabilities in dependencies.

2.  **Smart Pattern-Based Detection**: Scripts will scan the codebase for usage patterns of common dev packages in production-critical areas (e.g., migrations, seeders, service providers).
    ```bash
    # Smart Pattern-Based Detection for 12+ Common Packages
    PACKAGE_PATTERNS=(
        ["fakerphp/faker"]="Faker\\Factory|faker()|fake()"
        ["laravel/telescope"]="TelescopeServiceProvider|telescope"
        ["barryvdh/laravel-debugbar"]="DebugbarServiceProvider|debugbar"
        ["barryvdh/laravel-ide-helper"]="ide-helper"
        # ... and others
    )
    ```

3.  **Environment Version Locking**: A `versions.lock` file will be generated and committed, capturing the exact versions of PHP, Composer, Node, and NPM from the local environment to ensure consistency in the build environment.

---

## **Build Strategy Variations**

### **Strategy 1: Local Build + Manual Deployment**
**When to Use:** Simple projects, limited infrastructure, or when a builder VM is unavailable.
**Flow:**
1.  🟢 **Local Machine**: Full dependency analysis, version locking, and production build.
2.  🟢 **Local Machine**: Create a deployment package (`.zip` artifact).
3.  🔴 **Server**: Manual upload and extraction to a new release directory.
4.  🔴 **Server**: Run deployment scripts (symlinking, cache clearing) for an atomic switch.

### **Strategy 2: GitHub Actions + Automated Deployment**
**When to Use:** Team environments, CI/CD integration.
**Flow:**
1.  🟢 **Local Machine**: Run pre-commit analysis and push to GitHub.
2.  🟡 **GitHub Actions**: A workflow is triggered. It checks out the code, validates against `versions.lock`, installs dependencies, and builds assets.
3.  🟡 **GitHub Actions**: The build artifact is packaged and securely transferred to the server.
4.  🔴 **Server**: A script on the server, triggered by GitHub Actions, performs the atomic symlink switch.

### **Strategy 3: DeployHQ + Professional VM**
**When to Use:** Professional environments with multiple projects.
**Flow:**
1.  🟢 **Local Machine**: Run pre-commit analysis and push to GitHub.
2.  🟡 **DeployHQ VM**: Clones the repository, runs the build process in a clean, professional environment.
3.  🟡 **DeployHQ**: Executes pre-release, mid-release, and post-release SSH hooks for custom commands.
4.  🔴 **Server**: Receives the built files and performs the zero-downtime deployment.

### **Strategy 4: Hybrid Server Build**
**When to Use:** When the production server has sufficient resources and build tools installed.
**Flow:**
1.  🟢 **Local Machine**: Pre-commit analysis and push to GitHub.
2.  🔴 **Server**: Git pulls the latest code into a new release directory.
3.  🔴 **Server**: Runs the full dependency installation and asset build process directly on the server.
4.  🔴 **Server**: Performs the atomic deployment in the same environment.

---

## **Zero-Downtime Deployment Flow**

### **Critical Zero-Downtime Elements**
1.  **Atomic Symlink Switch**: The `current` directory is a symlink. The deployment script points it to the new release directory instantly.
2.  **Shared Resources**: Directories like `storage/app/public` and `public/uploads` are symlinked from a shared location into each release, ensuring data persistence.
3.  **Backward-Compatible Database Migrations**: Migrations are written to be non-blocking (e.g., adding columns before removing old ones).
4.  **OPcache & Application Cache Management**: A multi-tier strategy to clear PHP's OPcache, and Laravel's application, config, and route caches.
5.  **Queue Worker Restart**: A graceful restart of queue workers (`php artisan queue:restart`) ensures they use the new code.
6.  **Health Checks & Automated Rollback**: After the symlink switch, a health check verifies application status. If it fails, the symlink is immediately reverted to the previous release.

---

## **Comprehensive Shared Resources Strategy**

To prevent data loss, the following directories (and any project-specific ones) will be stored in a `shared` directory and symlinked into each new release.

### **Standard & User Content Directories**
```bash
shared_directories=(
    "storage/app/public"
    "storage/logs"
    "storage/framework/cache"
    "storage/framework/sessions"
    "storage/framework/views"
    "public/uploads"
    "public/avatars"
    "public/documents"
    "public/media"
    # ... add any other custom user content directories
)
```

---

## **Pitfall Prevention Strategy**

This strategy is designed to prevent all common Laravel deployment pitfalls.

### **Category 1: Dependency & Build Issues**
1.  **Dev Dependencies in Production**: Solved by the Universal Dependency Detection System.
2.  **Composer Version Conflicts**: Solved by forcing Composer v2 in build environments and using `versions.lock`.
3.  **Build Artifact Corruption**: Solved by running validation checks before and after the build process.
4.  **Version Mismatches (PHP, Node)**: Solved by validating against `versions.lock` in the build environment.

### **Category 2: Server & Environment Issues**
1.  **User Data Loss**: Solved by the Comprehensive Shared Resources Strategy.
2.  **OPcache/Application Cache Problems**: Solved by the multi-tier cache clearing strategy.
3.  **Database Migration Downtime**: Addressed by enforcing zero-downtime migration patterns.
4.  **Queue Worker Problems**: Solved by including a graceful `queue:restart` in the post-deploy script.
5.  **Missing PHP Extensions / Disabled Functions**: Solved by a pre-flight check script that validates the server environment against application requirements.

### **Category 3: Hosting & Configuration Issues**
1.  **Shared Hosting (`public_html`)**: The deployment script will handle renaming an existing `public_html` directory and creating a symlink to `current/public`, ensuring a secure document root.
2.  **Incorrect File Permissions**: Post-deployment scripts will set the correct ownership and permissions for directories like `storage` and `bootstrap/cache`.
3.  **Insecure `.env` file handling**: The `.env` file is never committed to git. It is managed securely on the server and symlinked into each release from the `shared` directory.

---

## **Error Recovery & Rollback Strategy**

### **Automatic Rollback Triggers**
- Health check endpoint returns a non-200 status code.
- A critical post-deploy command (e.g., `artisan migrate`) fails.
- Application fails to boot after the switch.

### **Manual Rollback Procedure**
A simple, fast script to revert to the previous release.
```bash
# Quick rollback to previous release
PREVIOUS=$(ls -1t releases/ | head -2 | tail -1)
ln -nfs "releases/$PREVIOUS" current
php artisan up
# ... run cache clearing and restart services
```

---

## **Quality Assurance Strategy**

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