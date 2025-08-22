# Terminology Definitions

**Clear definitions for all technical terms used throughout the deployment system**

---

## 🏗️ **CORE SYSTEM COMPONENTS**

### **CodeCanyon Application**

- **Definition:** A complete Laravel application purchased from CodeCanyon marketplace
- **Example:** Invoice generator, CRM system, e-commerce platform
- **Cost Range:** $50 - $500+ depending on complexity
- **Update Frequency:** Monthly security updates, quarterly feature updates
- **Challenge:** Updates can overwrite customizations

### **Vendor Files**

- **Definition:** Original PHP/Laravel files that came with the CodeCanyon ZIP
- **Location:** Throughout the Laravel app structure
- **Rule:** NEVER edit these directly
- **Why:** Vendor updates will overwrite your changes
- **Example:** `app/Http/Controllers/InvoiceController.php` (if it came with ZIP)

### **Custom Files**

- **Definition:** Your business-specific customizations and additions
- **Location:** Protected directories like `app/Custom/`, `Admin-Local/`
- **Rule:** ONLY edit these files
- **Why:** These survive all vendor updates
- **Example:** `app/Custom/Controllers/MyInvoiceController.php`

---

## 🚀 **DEPLOYMENT ARCHITECTURE**

### **Atomic Deployment**

- **Definition:** All-or-nothing deployment that succeeds completely or fails safely
- **How:** Use symlinks to switch between releases instantly
- **Benefit:** Zero downtime, instant rollback capability
- **Example:** `current -> releases/20250811-152018`

### **Release Directory**

- **Definition:** A timestamped folder containing one complete deployment
- **Naming:** `YYYYMMDD-HHMMSS` format (e.g., `20250811-152018`)
- **Content:** Full application code for that deployment
- **Lifecycle:** Created → Deployed → Archived → Cleaned up (keep last 3-5)

### **Shared Directory**

- **Definition:** Files that persist across all deployments
- **Content:** Environment config, user uploads, storage, logs
- **Why:** User data must survive deployments
- **Symlinked To:** Current release during deployment

### **Symlink (Symbolic Link)**

- **Definition:** A pointer that redirects to another location
- **Purpose:** Allows instant switching between releases
- **Example:** `current -> releases/20250811-152018`
- **Atomic:** Switching symlinks is instantaneous (atomic operation)

---

## 🛡️ **PROTECTION CONCEPTS**

### **Customization Protection**

- **Definition:** Strategy to preserve custom code during vendor updates
- **Method:** Separate vendor and custom code into different directories
- **Override Pattern:** Custom code takes precedence when both exist
- **Result:** Update vendor files without losing customizations

### **Admin-Local Directory**

- **Definition:** Special directory for local customizations
- **Purpose:** Store project-specific modifications safely
- **Location:** Root level of Laravel application
- **Examples:**
  - `Admin-Local/myCustomizations/`
  - `Admin-Local/clientSpecific/`
  - `Admin-Local/deployment-scripts/`

### **Override System**

- **Definition:** Custom files take precedence over vendor files
- **How:** Laravel's service container and autoloader respect custom paths first
- **Example:** If both exist, Laravel loads the custom version:
  - `app/Custom/Models/Invoice.php` (custom - loaded)
  - `app/Models/Invoice.php` (vendor - ignored)

---

## 🔄 **BUILD AND DEPLOYMENT**

### **Build Process**

- **Definition:** Converting source code into production-ready application
- **Includes:** Composer install, NPM build, asset compilation, optimization
- **Result:** Ready-to-run application in timestamped release directory
- **Location:** Can happen locally or on CI/CD systems

### **Local Build**

- **Definition:** Building the application on your development machine
- **Pros:** Full control, no external dependencies
- **Cons:** Requires dev environment setup
- **When:** Simple projects, learning, offline work

### **Remote Build (CI/CD)**

- **Definition:** Building on GitHub Actions, DeployHQ, or similar service
- **Pros:** Consistent environment, automatic triggering
- **Cons:** Requires service configuration
- **When:** Team projects, production systems

### **SSH Deploy**

- **Definition:** Uploading built code to server via SSH
- **Tools:** rsync, scp, custom scripts
- **Security:** SSH key-based authentication
- **Speed:** Incremental uploads (only changed files)

---

## 🗄️ **DATABASE AND PERSISTENCE**

### **Zero Data Loss**

- **Definition:** Guarantee that user-generated content survives all deployments
- **Strategy:** Separate application code from user data
- **Implementation:** Shared directories, database persistence
- **Validation:** Test procedures to verify data preservation

### **Shared Storage Strategy**

- **Definition:** Files that must persist across deployments
- **Auto-Detection:** System automatically identifies persistent vs. temporary files
- **Examples:**
  - **Persistent:** `public/uploads/`, `public/invoices/`, `storage/`
  - **Temporary:** `public/css/`, `public/js/`, `public/build/`

### **Database Migration**

- **Definition:** Updating database schema during deployment
- **Safety:** Always backup before migrations
- **Rollback:** Keep migration rollback procedures ready
- **Testing:** Test migrations on staging first

---

## 🔧 **DEVELOPMENT ENVIRONMENT**

### **Laravel Herd**

- **Definition:** Local development environment for Laravel (by Laravel team)
- **Includes:** PHP, MySQL, Redis, Nginx preconfigured
- **Benefit:** Matches production environment closely
- **Alternative:** XAMPP, WAMP, Docker, Laravel Sail

### **Environment Files (.env)**

- **Definition:** Configuration file with environment-specific settings
- **Content:** Database credentials, API keys, debug settings
- **Security:** NEVER commit to Git (use .env.example instead)
- **Deployment:** Different .env for development vs. production

### **Git Branch Strategy**

- **Definition:** How to organize code versions in Git
- **Recommended:**
  - `main` - Production-ready code
  - `develop` - Integration branch
  - `feature/xyz` - New features
  - `hotfix/xyz` - Emergency fixes

---

## 🚦 **DEPLOYMENT SCENARIOS**

### **Scenario A: Local Build + SSH**

- **Definition:** Build locally, upload via SSH
- **Best For:** Simple hosting, learning, small teams
- **Requirements:** Local development environment, SSH access
- **Automation Level:** Manual to semi-automated

### **Scenario B: GitHub Actions**

- **Definition:** Git push triggers automatic build and deploy
- **Best For:** Team collaboration, modern workflows
- **Requirements:** GitHub repository, server with SSH access
- **Automation Level:** Fully automated

### **Scenario C: DeployHQ**

- **Definition:** Professional deployment service
- **Best For:** Enterprise, complex deployment needs
- **Requirements:** DeployHQ account, Git repository
- **Automation Level:** Fully automated with advanced features

---

## 📊 **MONITORING AND MAINTENANCE**

### **Deployment Monitoring**

- **Definition:** Tracking deployment success/failure and application health
- **Tools:** Log monitoring, uptime checks, error tracking
- **Alerts:** Automated notifications for failures
- **Response:** Defined procedures for different alert types

### **Backup Strategy**

- **Definition:** Regular, tested backups of database and user files
- **Frequency:** Daily automated, weekly verified
- **Storage:** Multiple locations (local, cloud, offsite)
- **Testing:** Regular restore tests to verify backup integrity

### **Rollback Procedure**

- **Definition:** Steps to revert to previous working deployment
- **Speed:** Under 30 seconds for most rollbacks
- **Testing:** Regularly test rollback procedures
- **Documentation:** Step-by-step rollback guides

---

## 🎯 **WORKFLOW PHASES**

### **Phase 0: Setup (One-Time)**

- **Definition:** Initial setup of development environment and server
- **Frequency:** Once per developer, once per server
- **Duration:** 2-4 hours
- **Result:** Foundation ready for multiple projects

### **Phase 1: New Project (First Deploy)**

- **Definition:** Taking CodeCanyon app from ZIP to production
- **Frequency:** Once per new project/application
- **Duration:** 4-8 hours
- **Result:** Live, protected, production-ready application

### **Phase 2: Subsequent Deployments**

- **Definition:** Updates, vendor updates, new features
- **Frequency:** Monthly (vendor) + as needed (features)
- **Duration:** 30-60 minutes
- **Result:** Updated application with preserved customizations

### **Phase 3: Maintenance**

- **Definition:** Ongoing monitoring, backups, optimization
- **Frequency:** Daily automated + weekly manual
- **Duration:** 1-2 hours weekly
- **Result:** Reliable, monitored, backed-up system

---

## 🔑 **KEY SUCCESS PRINCIPLES**

### **Reproducible Deployments**

- **Definition:** Same process produces same result every time
- **How:** Documented procedures, automated scripts, version control
- **Benefit:** Team confidence, reduced errors, predictable outcomes

### **Fail-Safe Design**

- **Definition:** System designed to fail safely without data loss
- **How:** Atomic deployments, backup verification, rollback procedures
- **Benefit:** Production systems stay stable even during deployment failures

### **Investment Protection**

- **Definition:** Protecting money and time invested in customizations
- **How:** Customization separation, override systems, update procedures
- **Benefit:** Vendor updates don't destroy months of custom development

---

**Next:** Review deployment concepts and workflow overview before starting implementation.
