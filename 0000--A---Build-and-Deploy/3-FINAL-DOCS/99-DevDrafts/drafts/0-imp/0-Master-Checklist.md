# Master Checklist for Universal Laravel Zero-Downtime Deployment

**Version:** 1.0
**Last Updated:** August 21, 2025
**Purpose:** This document provides a complete, abbreviated checklist for deploying any Laravel application with zero downtime. It consolidates all necessary steps from project setup to final deployment.

---

## **SECTION A: Project Setup (One-Time)**

1.  **[initial-setup] - Initial Project & Git Setup**
2.  **[admin-local] - Configure Admin-Local Structure**
3.  **[env-setup] - Configure Environment Files**
4.  **[dependency-install] - Install All Dependencies**
5.  **[dependency-analysis] - Analyze Dependencies**
6.  **[version-lock] - Create Version Lock File**
7.  **[pre-commit-check] - Run Pre-Commit Checks**
8.  **[initial-commit] - Initial Commit to GitHub**

---

## **SECTION B: Pre-Deployment Preparation (Before Each Deployment)**

1.  **[update-codebase] - Update Local Codebase**
2.  **[run-analysis] - Run Dependency & Environment Analysis**
3.  **[run-tests] - Run Automated Tests**
4.  **[build-assets] - Build Frontend Assets**
5.  **[pre-deployment-validation] - Run Pre-Deployment Validation**
6.  **[commit-changes] - Commit All Changes to GitHub**

---

## **SECTION C: Build and Deploy (For Each Deployment)**

### **Phase 1: Initialize Deployment**
1.  **[connect-server] - Connect to Server**
2.  **[create-release-dir] - Create New Release Directory**

### **Phase 2: Build Application**
1.  **[clone-repo] - Clone Repository**
2.  **[install-dependencies] - Install Composer & NPM Dependencies**
3.  **[build-assets] - Build Frontend Assets**
4.  **[run-optimizations] - Run Laravel Optimizations**

### **Phase 3: Configure Release**
1.  **[symlink-env] - Symlink `.env` File**
2.  **[symlink-storage] - Symlink Storage Directory**
3.  **[set-permissions] - Set File & Directory Permissions**

### **Phase 4: Pre-Activation Hooks**
1.  **[run-pre-activation-hooks] - Run Pre-Activation Hooks**

### **Phase 5: Activate Release**
1.  **[atomic-switch] - Atomic Symlink Switch**

### **Phase 6: Post-Activation Hooks**
1.  **[run-migrations] - Run Database Migrations**
2.  **[clear-cache] - Clear All Caches**
3.  **[restart-services] - Restart Queue Workers & Services**
4.  **[run-post-activation-hooks] - Run Post-Activation Hooks**

### **Phase 7: Health Check**
1.  **[run-health-checks] - Run Automated Health Checks**

### **Phase 8: Cleanup**
1.  **[cleanup-old-releases] - Cleanup Old Releases**

### **Phase 9: Final Verification**
1.  **[manual-verification] - Manual Verification of Application**

### **Phase 10: Emergency Rollback**
1.  **[rollback] - Revert to Previous Release if Necessary**