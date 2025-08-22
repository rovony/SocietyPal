Got it. I’ll give you a **full, polished “Universal Deployment Pipeline — Speculative SOP”** that includes:

1. **Descriptions & Definitions** of the four strategies at the start.
2. The **10-phase universal pipeline**, clean and professional.
3. Each phase annotated with **where it executes** (🟢 Local, 🟡 CI/CD Runner VM, 🔴 Server).
4. Hooks (Pre, Mid, Post) clearly defined.
5. No “v2” mention — just final, standalone documentation.

---

# Universal Deployment Pipeline — Speculative SOP

> 💡 **Note:**
> This is a **universal pipeline model** to understand how modern deployment workflows operate.
> It can be applied to different strategies (local builds, CI/CD runners, SaaS tools, or hybrid builds).
> The **10 phases** below remain consistent, while each strategy plugs into them at different points.
> This framework helps in migrating between tools, setting up best practices, or troubleshooting deployments.

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

  * Execute custom commands (e.g., `composer install`, `npm run build`).
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
* Flexible **Pre, Mid, Post hooks** to adapt any workflow.

---

Would you like me to now create a **comparison matrix (table)** that shows each of the 4 strategies mapped across the **10 phases** (Local / Runner / Server responsibility at each step)? That would make it a single-glance decision aid.
