# Master Checklist for Phase 1: Project Setup

This checklist consolidates all the necessary steps for Phase 1 of the project setup. Follow each step carefully to ensure a smooth and successful deployment.

1.  **Step 00: AI Assistant Instructions**
    1.  **Purpose:** This step provides a comprehensive guide for using AI coding assistants with the V3 Laravel deployment guide. It includes instructions for AI-assisted development, error resolution, and continuous improvement.
    2.  **When to Use:** Before starting any step, when encountering errors, or when seeking improvements.

2.  **Step 01: Project Information Card**
    1.  **Purpose:** Document project metadata for team reference, deployment configuration, and AI coding assistance.
    2.  **Action:** Collect and document all project-specific information, including project name, domain, hosting details, GitHub repository, local development paths, and database credentials.

3.  **Step 02: Create GitHub Repository**
    1.  **Purpose:** Establish a version control foundation for deployment workflows.
    2.  **Action:**
        1.  Create a private repository on GitHub with the project name.
        2.  Ensure the repository is created without a README, .gitignore, or license file.
        3.  Note the SSH URL for cloning.

4.  **Step 03: Setup Local Project Structure**
    1.  **Purpose:** Establish a local development directory structure.
    2.  **Action:**
        1.  Navigate to the base apps directory.
            ```bash
            cd /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps
            ```
        2.  Create the project directory structure.
            ```bash
            mkdir -p SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root
            ```
        3.  Navigate to the project root.
            ```bash
            cd "/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"
            ```

5.  **Step 03.1: Setup Admin-Local Foundation**
    1.  **Purpose:** Establish the basic Admin-Local structure and install the portable zaj-Guides system.
    2.  **Action:**
        1.  Create the core Admin-Local directory and essential subdirectories.
            ```bash
            mkdir -p Admin-Local/0-Admin
            mkdir -p Admin-Local/1-CurrentProject
            ```
        2.  Install the zaj-Guides system by copying the directory to `Admin-Local/0-Admin/zaj-Guides/`.
        3.  Create project-specific tracking directories.
            ```bash
            mkdir -p Admin-Local/1-CurrentProject/Current-Session
            mkdir -p Admin-Local/1-CurrentProject/Deployment-History
            mkdir -p Admin-Local/1-CurrentProject/Installation-Records
            mkdir -p Admin-Local/1-CurrentProject/Maintenance-Logs
            mkdir -p Admin-Local/1-CurrentProject/Project-Trackers/Audit-Trail
            mkdir -p Admin-Local/1-CurrentProject/Project-Trackers/Conflict-Resolution
            mkdir -p Admin-Local/1-CurrentProject/Project-Trackers/Custom-Changes
            mkdir -p Admin-Local/1-CurrentProject/Project-Trackers/Vendor-Snapshots
            ```
        4.  Copy initial templates from zaj-Guides.
        5.  Update the .gitignore file to protect project-specific data.

6.  **Step 04: Clone GitHub Repository**
    1.  **Purpose:** Pull the GitHub repository into the local project structure.
    2.  **Action:**
        1.  Clone the repository into the current directory using the SSH URL.
            ```bash
            git clone git@github.com:rovony/SocietyPal.git .
            ```
        2.  Verify the clone by checking for the .git directory.
            ```bash
            ls -la
            ```

7.  **Step 05: Setup Git Branching Strategy**
    1.  **Purpose:** Establish a Git workflow for development, staging, and production deployments.
    2.  **Action:**
        1.  Create and push the following branches: `development`, `staging`, `production`, `vendor/original`, and `customized`.
            ```bash
            git checkout main && git pull origin main
            git checkout -b development && git push -u origin development
            git checkout main && git checkout -b staging && git push -u origin staging
            git checkout main && git checkout -b production && git push -u origin production
            git checkout main && git checkout -b vendor/original && git push -u origin vendor/original
            git checkout main && git checkout -b customized && git push -u origin customized
            git checkout main
            ```
        2.  Return to the main branch.

8.  **Step 06: Create Universal .gitignore**
    1.  **Purpose:** Create a comprehensive .gitignore file for CodeCanyon project deployments.
    2.  **Action:**
        1.  Create a `.gitignore` file in the project root.
        2.  Add the universal .gitignore content.
        3.  Verify the file creation and its contents.
        4.  **Commit the .gitignore file to prevent accidental commits of sensitive files.**
            ```bash
            git add .gitignore
            git commit -m "feat: add universal .gitignore for CodeCanyon deployment"
            ```

9.  **Step 06.1: Commit Admin-Local Foundation**
    1.  **Purpose:** Version control the Admin-Local structure before adding CodeCanyon files.
    2.  **Action:**
        1.  Stage and commit the Admin-Local foundation.
            ```bash
            git add Admin-Local/
            git commit -m "feat: establish Admin-Local foundation structure"
            ```

10. **Step 07: Install Project Dependencies**
    1.  **Purpose:** Install PHP and Node.js dependencies before running artisan commands.
    2.  **Action:**
        1.  Install PHP dependencies with Composer.
            ```bash
            composer install
            ```
        2.  Install Node.js dependencies with npm.
            ```bash
            npm install
            ```
        3.  Verify installation by checking for vendor/ and node_modules/ directories.

11. **Step 08: Download and Extract CodeCanyon Application**
    1.  **Purpose:** Download and integrate the CodeCanyon application into the project structure.
    2.  **Action:**
        1.  Create a temporary extraction directory.
            ```bash
            mkdir -p tmp-zip-extract
            ```
        2.  Download the CodeCanyon ZIP file and move it to the temporary directory.
        3.  Extract the application files.
            ```bash
            cd tmp-zip-extract
            unzip SocietyPro-v1.0.42.zip
            ```
        4.  Copy the application files to the project root.
        5.  Clean up the temporary files.
            ```bash
            rm -rf tmp-zip-extract
            ```

10. **Step 08: Commit Original Vendor Files**
    1.  **Purpose:** Preserve the original CodeCanyon files before any modifications.
    2.  **Action:**
        1.  Verify that only clean, unmodified CodeCanyon author files are present.
        2.  Commit the original vendor files to the `vendor/original` branch with a version tag.
            ```bash
            git add .
            git commit -m "feat: initial CodeCanyon files v1.0.42 (original vendor state)"
            git checkout -b vendor/original
            git tag -a v1.0.42 -m "CodeCanyon SocietyPro v1.0.42 - Original vendor files"
            ```
        3.  Push the branch and tag to the origin.
            ```bash
            git push -u origin vendor/original
            git push origin v1.0.42
            ```
        4.  Return to the main branch.
            ```bash
            git checkout main
            ```

11. **Step 09: Expand Admin-Local Directory Structure**
    1.  **Purpose:** Complete the directory structure for customizations, documentation, and deployment tools.
    2.  **Action:**
        1.  Expand the Admin-Local structure with directories for application customizations, project-specific tracking, documentation, server deployment, and backups.
        2.  Create `.gitkeep` files to preserve empty directories.

12. **Step 10: CodeCanyon Configuration & License Management**
    1.  **Purpose:** Establish CodeCanyon license tracking and update management system.
    2.  **Action:**
        1.  Detect if the application is a CodeCanyon application.
        2.  Set up a comprehensive license management structure with addon support.
        3.  Backup installer components for future reference.
        4.  Create a license tracking system with user-provided information.
        5.  Create an update capture and comparison system.

13. **Step 10.1: Branch Synchronization & Progress Checkpoint**
    1.  **Purpose:** Ensure consistent commit history across all deployment branches with strategic checkpoint naming.
    2.  **Action:**
        1.  Verify the current Git status and branch.
        2.  Stage all current changes.
        3.  Create a strategic checkpoint commit with professional naming.
        4.  Execute the multi-branch synchronization script.
        5.  Verify that all branches are synchronized.

14. **Step 11: Create Environment Files**
    1.  **Purpose:** Set up environment configuration files for all deployment stages.
    2.  **Action:**
        1.  Create and configure `.env.local`, `.env.staging`, and `.env.production` files.
        2.  Generate unique application keys for each environment.
            ```bash
            php artisan key:generate --env=local
            php artisan key:generate --env=staging
            php artisan key:generate --env=production
            ```
        3.  Verify that all environment files are correctly configured.

15. **Step 12: Setup Local Development Site in Herd**
    1.  **Purpose:** Configure Laravel Herd for the local development environment.
    2.  **Action:**
        1.  Upgrade to Herd Pro and enable MySQL and Redis services.
        2.  Add the site to Herd.
        3.  Verify the site configuration by opening it in a browser.

16. **Step 13: Create Local Database**
    1.  **Purpose:** Set up a local MySQL database in Herd for development.
    2.  **Action:**
        1.  Verify that the Herd Pro MySQL service is running.
        2.  Connect to the MySQL CLI and create the local database.
            ```bash
            mysql -u root -h 127.0.0.1 -P 3306 -p
            CREATE DATABASE societypal_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            exit;
            ```
        3.  Update the `.env.local` file.
        4.  Clear the Laravel configuration cache and test the database connection.
            ```bash
            php artisan config:clear
            php artisan cache:clear
            php artisan migrate:status
            ```
19. **Step 15: Create Storage Link**
    1.  **Purpose:** Create symbolic link for public file access (essential Laravel setup step).
    2.  **Action:**
        1.  Create the storage link to make uploaded files accessible from the web.
            ```bash
            php artisan storage:link
            ```
        2.  Verify the link was created successfully.
            ```bash
            ls -la public/storage
            ```


20. **Step 16: Run Local & CodeCanyon Installation**
    1.  **Purpose:** Launch the local development environment and complete the CodeCanyon installation process.
    2.  **Action:**
        1.  Set up the environment and generate the application key.
            ```bash
            cp .env.local .env
            php artisan key:generate
            ```
        2.  Clear and rebuild Laravel caches.
            ```bash
            php artisan config:clear
            php artisan cache:clear
            php artisan view:clear
            php artisan route:clear
            ```
        3.  Set the necessary Laravel permissions.
            ```bash
            chmod -R 775 storage/
            chmod -R 775 bootstrap/cache/
            ```
        4.  Start the required services in Herd.
        5.  Access the application and complete the web-based installation.
        6.  Capture the installation credentials and configuration.
        7.  Perform a post-installation security lockdown.
        8.  Run a post-installation analysis to document changes.