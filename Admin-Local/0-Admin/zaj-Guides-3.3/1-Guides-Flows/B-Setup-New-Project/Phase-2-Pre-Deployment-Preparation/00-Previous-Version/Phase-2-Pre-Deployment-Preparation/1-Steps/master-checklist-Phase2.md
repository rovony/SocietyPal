# Master Checklist for Phase 2: Pre-Deployment Preparation

This checklist consolidates all the necessary steps for Phase 2 of the project setup. Follow each step carefully to ensure a smooth and successful deployment.

1.  **Step 15: Install Dependencies & Generate Lock Files**

    1.  **Purpose:** Install and verify all project dependencies for reproducible builds.
    2.  **Action:**
        1.  Navigate to the project root.
        2.  Install PHP dependencies.
            ```bash
            composer install
            ```
        3.  Install JavaScript dependencies (if applicable).
            ```bash
            npm install
            ```
        4.  Verify that `composer.lock` and `package-lock.json` files are created.

2.  **NEW: Run Database Migrations**

    1.  **Purpose:** Ensure the database schema is up-to-date with the application's requirements.
    2.  **Action:**
        1.  Run the following command to apply any pending migrations:
            ```bash
            php artisan migrate
            ```

3.  **Step 16: Test Build Process**

    1.  **Purpose:** Verify the production build process works before deployment.
    2.  **Action:**
        1.  Clean previous builds.
            ```bash
            rm -rf vendor node_modules public/build
            ```
        2.  Test the production PHP build.
            ```bash
            composer install --no-dev --prefer-dist --optimize-autoloader
            ```
        3.  Build frontend assets.
            ```bash
            npm ci
            npm run build
            ```
        4.  Apply Laravel caching.
            ```bash
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            ```
        5.  Test the built version locally.
        6.  Restore the development environment.
            ```bash
            php artisan config:clear
            php artisan route:clear
            php artisan view:clear
            composer install
            npm install
            ```

4.  **NEW: Run Security Scans**

    1.  **Purpose:** Identify and fix potential security vulnerabilities before deployment.
    2.  **Action:**
        1.  Use a tool like Snyk or Larastan to scan the codebase for vulnerabilities.

5.  **Step 17: Customization Protection**

    1.  **Purpose:** Implement a Laravel (with or without js)-compatible customization layer to protect changes during team or upstream vendor updates, and establish comprehensive investment protection documentation for the customization project.
    2.  **Action: Create and use scripts from Universal Customization System and Investment Protection System templates**
        1.  **Customization Layer Setup** - Use `setup-customization.sh` (ensure this customization project adapts per tech stack of marketplace app):
            -   Create a protected directory structure for customizations in `app/Custom`, `config/custom`, `database/migrations/custom`, `resources/views/custom`, and `public/custom`.
            -   Create custom configuration files (e.g., `config/custom-app.php`, `config/custom-database.php`).
            -   Create a `CustomizationServiceProvider` to load custom routes, views, migrations, and blade directives.
            -   Register the `CustomizationServiceProvider` in `config/app.php`.
            -   Add custom environment variables to the `.env` file for feature toggles, branding, and other settings.
            -   Create an update-safe asset strategy using a separate webpack configuration (e.g., `webpack.custom.js`).
        2.  **Investment Protection Documentation** - Use `setup-customization-protection.sh`:
            -   Set up comprehensive investment tracking and documentation system for the customization project
            -   Create investment summary and ROI analysis tools for customization project
            -   Implement customization catalog and business logic documentation
            -   Generate API extensions, frontend changes, and database modifications documentation
            -   Set up security enhancements and performance optimization tracking for customization project
            -   Create integration points documentation and recovery procedures
            -   Generate automated customization project documentation and team handoff materials
        3.  **Stack Variations:**
            -   **Blade-only:** Focus on `CustomizationServiceProvider` for backend logic and `resources/views/custom` for frontend overrides.
            -   **Inertia/Vue/React:** Use the `CustomizationServiceProvider` for backend logic and create a separate, namespaced frontend component library within `resources/js/Custom` to avoid conflicts with vendor components.

6.  **Step 18: Data Persistence Strategy**

    1.  **Purpose:** Implement a zero data loss system with smart content protection during deployments.
    2.  **Action:**
        1.  Create an advanced persistence script (`link_persistent_dirs.sh`) with automatic framework detection and smart build artifact exclusions.
        2.  Test the persistence script locally.
        3.  Create data persistence documentation.
        4.  Create a verification script (`verify_persistence.sh`).

7.  **Step 19: Commit Pre-Deployment Setup**

    1.  **Purpose:** Commit all preparation work to the repository with comprehensive documentation.
    2.  **Action:**
        1.  Verify the current status of the repository and ensure sensitive files are excluded.
        2.  Run a comprehensive verification script to ensure all Phase 2 steps are complete.
        3.  Add all appropriate files to the staging area.
        4.  Create a comprehensive commit message documenting all preparation work.
        5.  Push the changes to the repository.
        6.  Create a deployment readiness report.
