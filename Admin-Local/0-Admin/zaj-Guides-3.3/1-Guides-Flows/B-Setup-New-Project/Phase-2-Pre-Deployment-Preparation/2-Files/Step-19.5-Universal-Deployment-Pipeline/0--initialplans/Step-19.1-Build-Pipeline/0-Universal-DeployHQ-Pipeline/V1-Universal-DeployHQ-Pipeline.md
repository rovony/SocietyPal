# DeployHQ Pipeline — Speculative SOP

> 💡 **Note:**  
> We are speculating DeployHQ steps to:  
> 1. Understand how it works internally.  
> 2. Help in case in the future we decide to migrate away from DeployHQ.  

---

## Current Pipeline Steps using Scripts & DeployHQ Auto-Injected Commands
v: v1

---

## 1 -  🖥️ Deployer Commands: 🔧 Preparing Builder Runner VM

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
        - Updating repository from `git@github.com:username/repo.git` given a specified branch `branch`. 
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
## 3 - x Running Build Pipeline Commands (in order specified in Pipeline)- `User-Specified`
    - Objectives (speculated):  
        - Placeholder for your build steps (e.g., `composer install`, `npm run build`, `php artisan migrate`)  
    - Commands:  see build pipeline files - specified by user.
--
## 4 -  🖥️ Deployer Commands: 🔧 Finishing Build Runner VM


1. **Running build pipeline 🔨🛠️**  
    - Objectives (speculated):  
        - Placeholder for your build steps (e.g., `composer install`, `npm run build`, `php artisan migrate`)  
    - Commands:  
        ```bash
        # Build steps placeholder - to be defined by user.
        ```

2. **Receiving built files**  
    - Objectives (speculated):  
        - Collecting processed build output after pipeline completion  
        - Preparing files for packaging and transfer  
    - Commands:  
        ```bash
        # Tar/zip or equivalent creation - blackbox.
        ```

3. **Generating deployment manifest**  
    - Objectives (speculated):  
        - Creating list of files changed/new in this build  
        - Calculating checksums and relative paths  
    - Commands:  
        ```bash
        # Manifest generation (system step).
        ```

4. **Storing artifacts**  
    - Objectives (speculated):  
        - Archiving build output for rollbacks or reference  
        - Making them available in DeployHQ UI/logs  
    - Commands:  
        ```bash
        # Artifact storage (blackbox).
        ```

## 5- Transfering to server and connectiong to server
## 6- Running SSH COMMANDS - PHASE A - `User-Specified`
Desc: Custom ssh commands executed before changes and immediately once connected to the server by the deployer (DeployHQ)

## 7-  🖥️ Deployer Commands: x xxx


- Preparing release directory


- Transferring changed files


- Uploading config files
ex: Uploading /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819041738/.env

- Linking files from shared path to release
examples:
Symlinking backups to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819041738/backups

Symlinking bootstrap/cache to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819041738/bootstrap/cache

Symlinking .env to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819041738/.env

Symlinking Modules to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819041738/Modules

Symlinking storage to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819041738/storage

Symlinking public/favicon to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819041738/public/favicon

Symlinking public/avatars to /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819041738/public/avatars

etc

## 8- Running SSH COMMANDS - PHASE B - `User-Specified`

Desc: Custom ssh commands executed after the files has been uploaded/updatedremoved, but before the new release has been made active (when we are doing Zero Down deplyment). 



## 9-  🖥️ Deployer Commands: x xxx
- linking release directory to current
example: 
Symlinking /home/u227177893/domains/staging.societypal.com/deploy/releases/20250819041738 to /home/u227177893/domains/staging.societypal.com/deploy/current

## 10- Running SSH COMMANDS - PHASE C - `User-Specified`

Desc: Custom ssh commands executed after files have been uploaded/removed at the end of the deployment

## 11-  🖥️ Deployer Commands: x xxx
Cleaning up old releases



# 12- 🖥️ Deployer Commands: finishing

Delivering webhook notifications


Sending emails


Saving build environment

Saved build environment language versions
