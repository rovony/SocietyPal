# DeployHQ Pipeline — Speculative SOP (V2)

> 💡 **Note:**  
> We are speculating DeployHQ steps to:  
> 1. Understand how it works internally.  
> 2. Help in case in the future we decide to migrate away from DeployHQ.  

---

## Current Pipeline Steps using Scripts & DeployHQ Auto-Injected Commands
v: v2

---

## 1 - 🖥️ Deployer Commands: 🔧 Preparing Builder Runner VM

1. **Waiting for an available deployment slot**

2. **Performing pre-deployment checks**  
    - Objectives (speculated):  
        - Checking build allowance  
        - Checking access to repository  
        - Validating start and end revisions  
        - Checking connection to server (e.g., SocietyPal-Staging)  
    - Commands:  
        ```bash
        # Commands executed by DeployHQ (blackbox). 
        # We can only speculate what’s done here.
        ```

3. **Preparing repository for deployment**  
    - Objectives (speculated):  
        - Updating repository from `git@github.com:username/repo.git` for branch `branch`  
        - Fetching information for start commit `commitX-name`  
        - Fetching information for end commit `commitY-name`  
    - Commands:  
        ```bash
        # Commands executed by DeployHQ (blackbox). 
        # Likely git fetch + metadata collection.
        ```

---

## 2 - 🖥️ Deployer Commands: ⚙️ Building on Runner VM 

1. **Checking out repository for deployment**  
    - Objectives (speculated):  
        - Creating working copy of repo at commit `commitY-name` (branch: `branch`)  
    - Commands:  
        ```bash
        # Blackbox step - likely "git checkout" of target commit.
        ```

2. **Setting up environment for build commands**  
    - Objectives (speculated):  
        - Preparing PHP, Node.js, Composer, Yarn/NPM environments  
        - Making PhantomJS or other dependencies available if enabled  
    - Commands:  
        ```bash
        # Environment preparation - system-managed (blackbox).
        ```

3. **Uploading repository to build server**  
    - Objectives (speculated):  
        - Syncing repo files to build VM  
        - Preparing files for caching/restore  
    - Commands:  
        ```bash
        # Likely rsync/transfer - handled internally by DeployHQ.
        ```

4. **Sending cached build to build server**  
    - Objectives (speculated):  
        - Restoring cached directories (e.g., `vendor/`, `node_modules/`)  
        - Avoiding redundant dependency downloads  
    - Commands:  
        ```bash
        # Cached artifact restoration (blackbox).
        ```

5. **Sending config files to build server**  
    - Objectives (speculated):  
        - Uploading project-specific configs (deploy.yml, env vars, secrets)  
    - Commands:  
        ```bash
        # Internal DeployHQ step.
        ```

---

## 3 - 🧑‍💻 Running Build Pipeline Commands (User-Specified)

- Objectives (speculated):  
    - Custom build steps defined by the user (e.g., `composer install`, `npm run build`, `php artisan migrate`)  
    - Run in order specified in the project’s build pipeline  
- Commands:  
    ```bash
    # User-defined build commands.
    # Location: Build pipeline files in repo.
    ```

---

## 4 - 🖥️ Deployer Commands: 🔧 Finishing Build Runner VM

1. **Receiving built files**  
    - Objectives (speculated):  
        - Collecting processed build output after pipeline completion  
        - Preparing files for packaging and transfer  
    - Commands:  
        ```bash
        # Tar/zip or equivalent creation - blackbox.
        ```

2. **Generating deployment manifest**  
    - Objectives (speculated):  
        - Creating list of files changed/new in this build  
        - Calculating checksums and relative paths  
    - Commands:  
        ```bash
        # Manifest generation (system step).
        ```

3. **Storing artifacts**  
    - Objectives (speculated):  
        - Archiving build output for rollbacks or reference  
        - Making them available in DeployHQ UI/logs  
    - Commands:  
        ```bash
        # Artifact storage (blackbox).
        ```

---

## 5 - 🖥️ Deployer Commands: 📡 Connecting & Transferring to Target Server

1. **Connecting to server**  
    - Objectives (speculated):  
        - Establishing SSH/SFTP connection  
        - Using configured server connection and deploy path  
    - Commands:  
        ```bash
        # Blackbox - system-managed SSH connection.
        ```

2. **Preparing release directory**  
    - Objectives (speculated):  
        - Creating a new timestamped release directory  
    - Commands:  
        ```bash
        mkdir -p /home/.../deploy/releases/20250819041738
        ```

3. **Transferring changed files**  
    - Objectives (speculated):  
        - Uploading only changed/new files based on manifest  
    - Commands:  
        ```bash
        # rsync/scp based file sync - blackbox.
        ```

4. **Uploading config files**  
    - Example:  
        ```
        Uploading /home/.../deploy/releases/20250819041738/.env
        ```

5. **Linking files from shared path to release**  
    - Examples:  
        ```
        Symlinking .env → /home/.../releases/.../.env
        Symlinking storage → /home/.../releases/.../storage
        Symlinking bootstrap/cache → /home/.../releases/.../bootstrap/cache
        Symlinking Modules → /home/.../releases/.../Modules
        ```
    - Commands:  
        ```bash
        ln -sf ../../shared/.env ./releases/.../.env
        ```

---

## 6 - Running SSH Commands: Phase A (User-Specified)

- Executed **before changes** are applied, once connected to the server.  
- Example use: Maintenance mode, backups, etc.  

---

## 7 - 🖥️ Deployer Commands: 🔗 Activating New Release

1. **Linking release directory to current**  
    - Objectives (speculated):  
        - Update `current` symlink to point to the new release  
    - Example:  
        ```
        Symlinking /home/.../deploy/releases/20250819041738 → /home/.../deploy/current
        ```

---

## 8 - Running SSH Commands: Phase B (User-Specified)

- Executed **after files are uploaded but before release is active**.  
- Example use: Run DB migrations, cache warming, or checks for zero-downtime deployment.  

---

## 9 - 🖥️ Deployer Commands: 🧹 Cleaning Up

- Objectives (speculated):  
    - Remove old releases beyond retention policy  
- Commands:  
    ```bash
    rm -rf /home/.../deploy/releases/<old_release_id>
    ```

---

## 10 - Running SSH Commands: Phase C (User-Specified)

- Executed **after release is live**.  
- Example use: Clear caches, restart services, notify monitoring tools.  

---

## 11 - 🖥️ Deployer Commands: ✅ Finishing Deployment

- Delivering webhook notifications  
- Sending emails to configured recipients  
- Saving build environment (e.g., PHP, Node versions)  
