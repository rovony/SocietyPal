Perfect, thanks for clarifying — you want me to **add an intro section** before the “Deployment Strategy Definitions” in that document, which covers the **Builder Pipeline Commands** and **User SSH Hooks** concepts. I’ll keep everything else intact and polished. Here’s the full updated version:

---

# Universal Deployment Pipeline — Speculative SOP

> 💡 **Note:**
> This is a **universal pipeline model** to understand how modern deployment workflows operate.
> It can be applied to different strategies (local builds, CI/CD runners, SaaS tools, or hybrid builds).
> The **10 phases** below remain consistent, while each strategy plugs into them at different points.
> This framework helps in migrating between tools, setting up best practices, or troubleshooting deployments.

---

## Builder Pipeline Commands

* **Purpose:**
  Isolate technology-specific build steps from the universal deployment flow. This ensures the main deployment script stays generic and reusable.
* **Strategy:**
  Each project maintains a `build-commands.sh` script containing all the commands needed to build the app. The deployment pipeline simply calls this script, regardless of whether the build runs locally, on a CI/CD runner, or on the server.
* **Example:**

```bash
# build-commands.sh (Laravel + Vite)
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

---

## User SSH Hooks

* **Purpose:**
  Provide customizable entry points for project-specific commands during deployment.

* **Strategy:**
  The deployment script calls three separate hook scripts at key stages of the pipeline. These are optional but highly recommended for flexible, idempotent deployments.

  * `pre-release-hook.sh` → Runs **before changes are applied** on the server.
  * `mid-release-hook.sh` → Runs **after files are uploaded but before the atomic switch**.
  * `post-release-hook.sh` → Runs **after the deployment is live**.

* **Example:**

```bash
# post-release-hook.sh
php artisan migrate --force
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Deployment Strategy Definitions

### **Strategy 1: Local Build + Manual Deployment**

* **Description:**
  Build is performed on a developer’s local machine. Artifacts (`zip` or `tar.gz`) are uploaded manually via SSH/SFTP to the server.
* **When to Use:**
  Simple projects, limited infra, or no CI/CD runner available.
* **Flow:**

  1. 🟢 Local Machine: Build dependencies, generate lockfiles, package artifact.
  2. 🔴 Server: Upload/extract artifact, run pre/post scripts, activate release.

---

### **Strategy 2: GitHub Actions + Automated Deployment**

* **Description:**
  GitHub Actions runner performs builds/tests, then automatically transfers artifacts to the server and triggers deployment scripts.
* **When to Use:**
  Team environments, CI/CD automation, versioned workflows.
* **Flow:**

  1. 🟢 Local Machine: Initial setup, branching, commit to GitHub.
  2. 🟡 GitHub Actions Runner: Builds, validates, and packages artifact.
  3. 🔴 Server: Receives files, symlinks, activates release.

---

### **Strategy 3: DeployHQ (SaaS CI/CD Tool)**

* **Description:**
  DeployHQ (or equivalent SaaS CI/CD tool) pulls from GitHub, runs build pipelines on its own VM, and deploys directly to target servers with hooks.
* **When to Use:**
  Professional multi-project environments with managed build infra.
* **Flow:**

  1. 🟢 Local Machine: Commit/push code.
  2. 🟡 DeployHQ VM: Build, validate, package.
  3. 🔴 Server: Deploy and activate release with hooks.

---

### **Strategy 4: Hybrid Server Build**

* **Description:**
  Source code is pulled directly onto the server, and the build (Composer, NPM, etc.) runs there.
* **When to Use:**
  When the production server has enough resources and installing build tools is acceptable.
* **Flow:**

  1. 🟢 Local Machine: Push to GitHub.
  2. 🔴 Server: Git pull, build, symlink, release activation.

---

## Universal 10-Phase Deployment Pipeline

---

### 1 - 🖥️ Preparing Builder / Runner Environment

* **Objectives:**

  * Validate repository (branch, commits, revisions).
  * Confirm connectivity to server(s).
  * Prepare metadata for build.
* **Location:**

  * 🟢 Local (Strategy 1)
  * 🟡 CI/CD Runner (Strategy 2, 3)
  * 🔴 Server (Strategy 4)

---

### 2 - ⚙️ Building Application

* **Objectives:**

  * Checkout target commit.
  * Install dependencies.
  * Restore caches (`vendor/`, `node_modules/`).
  * Prepare configs/env.
* **Location:**

  * 🟢 Local build (Strategy 1)
  * 🟡 CI/CD Runner (Strategy 2, 3)
  * 🔴 Server-local build (Strategy 4)

---

### 3 - 🧑‍💻 Running Build Pipeline Commands

* **Objectives:**

  * Execute `build-commands.sh`.
  * Run automated tests, linting, validation.
* **Location:**

  * 🟢 Local (Strategy 1)
  * 🟡 CI/CD Runner (Strategy 2, 3)
  * 🔴 Server (Strategy 4)

---

### 4 - 📦 Packaging & Artifact Preparation

* **Objectives:**

  * Package build output (`tar/zip`).
  * Generate manifest of changed files.
  * Store for rollback.
* **Location:**

  * 🟢 Local packaging (Strategy 1)
  * 🟡 CI/CD artifact storage (Strategy 2, 3)
  * 🔴 Server build output (Strategy 4)

---

### 5 - 📡 Connecting & Transferring to Target Server

* **Objectives:**

  * Establish SSH/SFTP connection.
  * Create timestamped release dir.
  * Upload changed files + configs.
  * Link shared paths (storage, cache, modules).
* **Location:**

  * 🔴 Server (all strategies)
  * Source varies:

    * 🟢 Manual upload (Strategy 1)
    * 🟡 CI/CD push (Strategy 2, 3)
    * 🔴 Server-local git pull (Strategy 4)

---

### 6 - 1️⃣ Pre-release Hooks

* **Definition:** Executed **before changes are applied**, once server connection is established.
* **Use cases:**

  * Set up deploy folders (`current/shared/releases`)
  * Enable maintenance mode
  * Database backups
* **Location:** 🔴 Server (all strategies)

---

### 7 - 🔗 Activating New Release

* **Objectives:**

  * Update `current` symlink → new release directory.
  * Ensure atomic switch with zero downtime.
* **Location:** 🔴 Server (all strategies)

---

### 8 - 2️⃣ Mid-release Hooks

* **Definition:** Executed **after files uploaded but before release is active.**
* **Use cases:**

  * Run DB migrations
  * Cache warming
  * Zero-downtime checks
* **Location:** 🔴 Server (all strategies)

---

### 9 - 🧹 Cleaning Up

* **Objectives:**

  * Remove old releases per retention policy.
  * Free disk space, keep rollback safety.
* **Location:** 🔴 Server (all strategies)

---

### 10 - 3️⃣ Post-release Hooks

* **Definition:** Executed **after release is live.**
* **Use cases:**

  * Clear caches
  * Restart services
  * Notify monitoring/alerting tools
* **Location:** 🔴 Server (all strategies)

---

### 11 - ✅ Finishing Deployment

* **Objectives:**

  * Send webhooks/notifications
  * Email stakeholders
  * Record environment metadata (PHP, Node versions)
* **Location:**

  * 🟢 Local logs (Strategy 1)
  * 🟡 CI/CD Runner (Strategy 2, 3)
  * 🔴 Server logs (Strategy 4)

---

✅ This framework gives you:

* A **consistent 10-phase pipeline** across strategies.
* Clear **server vs local vs runner responsibilities**.
* Flexible **Builder Commands** and **Pre, Mid, Post SSH hooks** for full customization.

---

Do you want me to now extend this with a **file/folder scaffold** (`/deploy/builder/build-commands.sh`, `/deploy/hooks/pre-release-hook.sh`, etc.) so you can see the recommended directory structure?
