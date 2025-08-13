# Step 00: AI Assistant Instructions

**Comprehensive guide for using AI coding assistants with the V3 Laravel deployment guide**

> 🤖 **Purpose:** Provide clear instructions for AI-assisted development and error resolution
>
> 🎯 **When to Use:** Before starting any step, when encountering errors, or when seeking improvements

---

## **⚠️ CRITICAL: TEMPLATE CUSTOMIZATION REQUIRED**

### **🎯 THIS GUIDE USES EXAMPLE PROJECT DATA - CUSTOMIZE FOR YOUR PROJECT**

> **⚠️ IMPORTANT:** All steps in this guide contain example data that MUST be customized for your specific project:
>
> - **Project Name:** Examples use "SocietyPal" → Replace with YOUR project name
> - **CodeCanyon App:** Examples use "SocietyPro v1.0.42" → Replace with YOUR app and version
> - **Domains:** Examples use "societypal.com" → Replace with YOUR domain
> - **Paths:** Examples use specific user paths → Replace with YOUR system paths
> - **Hosting:** Examples use "Hostinger" → Replace with YOUR hosting provider
> - **Versions:** Examples use specific versions → Confirm YOUR actual versions

### **📋 Before Starting ANY Step:**

1. **✅ Read Step 01:** Configure all your project-specific variables first
2. **✅ Confirm Current State:** Check what already exists in your project
3. **✅ Verify Versions:** Use actual versions from your project files, not guide examples
4. **✅ Ask When Unsure:** Request clarification for anything unclear or mismatched

### **🤖 When Using AI Assistant:**

Always provide your specific project details, not the example data from the guides. AI should customize ALL commands, paths, and configurations for YOUR project setup.

## **📚 GUIDE TERMINOLOGY & CONVENTIONS**

### **⚠️ CRITICAL: "Vendor" Terminology Clarification**

**When we say "Vendor" in these guides, we mean:**

✅ **CodeCanyon Author/Upstream Vendor:**
- The original author/company who created the CodeCanyon application
- The third-party software provider you purchased the script from
- Example: The developer team behind SocietyPro, TaskGo, etc.

✅ **Vendor Files Include:**
- Application code: `app/`, `config/`, `resources/`, `public/`, `database/`, `routes/`
- Framework files: Laravel core application structure
- Author's custom modules, controllers, views, and business logic

❌ **"Vendor" does NOT refer to:**
- **`vendor/` directory:** Composer dependencies folder
- **Composer packages:** Third-party PHP libraries (installed via `composer install`)
- **Laravel framework itself:** The underlying Laravel installation

**Why This Matters:**
- Prevents confusion between "vendor files" (CodeCanyon author's work) and "vendor/ folder" (Composer dependencies)
- Ensures clear understanding when preserving original author files vs managing dependencies
- Critical for proper Git branching and update management strategies

### **File Path Conventions**
- `PROJECT_ROOT` = Base Laravel application directory
- `ADMIN_LOCAL` = Administrative documentation directory
- All paths are relative to PROJECT_ROOT unless specified otherwise

### **Placeholder Conventions**
- `{YOUR_PROJECT}` = Replace with actual project name
- `{YOUR_VERSION}` = Replace with actual version number  
- `{YOUR_DOMAIN}` = Replace with actual domain name
- Text in `{}` requires customization per project

### **Environment Conventions**
- **Local:** Development environment on developer machine
- **Staging:** Pre-production testing environment
- **Production:** Live environment serving end users

---

## **📋 DEPLOYMENT GUIDE BACKGROUND**

### **🎯 Complete Guide System Structure**

📚 **V3 Laravel CodeCanyon Deployment Guide Organization:**

#### **0️⃣ Setup Computer & Server** (Prerequisites)
- **Step 01:** Herd Installation - Local Laravel development environment
- **Step 02:** SSH Configuration - Secure server access setup
- **Step 03:** Server Setup - Production server preparation

#### **1️⃣ Setup New Project** (First-Time Deployment)

**Phase 1: Project Setup (Steps 01-14)**
- **Step 01:** Project Information Card - Environment variables and project metadata
- **Step 02:** Create GitHub Repository - Repository setup and remote configuration
- **Step 03:** Setup Local Structure - Directory hierarchy and organization
- **Step 04:** Clone Repository - Git repository local setup
- **Step 05:** Git Branching Strategy - Multi-environment deployment branches
- **Step 06:** Universal GitIgnore - Deployment-compatible version control exclusions
- **Step 07:** Download CodeCanyon - Vendor application integration
- **Step 08:** Commit Original Vendor Files - Pristine vendor file preservation
- **Step 09:** Admin-Local Directory Structure - Project organization and customization layer
- **Step 10:** CodeCanyon Configuration & License Management - License tracking and update safety
- **Step 11:** Create Environment Files - Environment-specific configuration management
- **Step 12:** Setup Local Development Site - Local development environment configuration
- **Step 13:** Create Local Database - Database setup and initial configuration
- **Step 14:** Run Local Installation - Complete local application installation

**Phase 2: Pre-Deployment Preparation (Steps 15-20)**
- **Step 15:** Install Dependencies - Composer and NPM dependency management
- **Step 16:** Test Build Process - Build verification and optimization
- **Step 17:** Setup Customization Protection System - Custom code protection and backup
- **Step 18:** Data Persistence Planning - Database migration and backup strategies
- **Step 19:** Documentation Preparation - Deployment documentation and procedures
- **Step 20:** Commit Pre-Deploy State - Pre-deployment checkpoint and verification

**Phase 3: Deployment Execution (Steps 21-24)**
- **Step 21:** Choose Deployment Scenario - Select appropriate deployment method
- **Step 22A:** Local Build Process - Manual build and upload workflow
- **Step 22B:** GitHub Actions Workflow Setup - Automated CI/CD pipeline configuration
- **Step 22C:** DeployHQ Professional Setup - Enterprise deployment platform setup
- **Step 22D:** Git Pull Configuration - Server-side git deployment setup
- **Step 23:** Server Deployment - Execute chosen deployment scenario
- **Step 24:** Post-Deployment Verification - Production deployment validation and testing

**Phase 4: Post-Deployment Maintenance (Steps 25-28)**
- **Step 25:** Setup Server Monitoring - Infrastructure monitoring and alerting
- **Step 26:** Setup Security Hardening - Production security configuration
- **Step 27:** Performance Monitoring - Application performance optimization
- **Step 28:** Emergency Procedures - Incident response and disaster recovery

#### **2️⃣ Subsequent Deployment** (Updates & Maintenance)
- **Step 01:** Pre-Update Backup - Safe backup procedures before updates
- **Step 02:** Download New CodeCanyon Version - Vendor update management
- **Step 03:** Compare Changes - Advanced diff analysis for safe updates
- **Step 04:** Update Vendor Files - Controlled vendor file updates
- **Step 05:** Test Custom Functions - Compatibility verification
- **Step 06:** Update Dependencies - Composer and NPM dependency management
- **Step 07:** Test Build Process - Build verification before deployment
- **Step 08:** Deploy Updates - Production update deployment
- **Step 09:** Verify Deployment - Post-update verification and rollback procedures

#### **3️⃣ Maintenance** (Ongoing Operations)
- **Backup Management:** Automated backup systems and recovery procedures
- **Emergency Procedures:** Incident response and disaster recovery
- **Performance Monitoring:** System performance optimization and monitoring
- **Security Updates:** Security patch management and hardening
- **Server Monitoring:** Infrastructure monitoring and alerting

#### **9️⃣9️⃣ Understanding** (Reference Documentation)
- **Best Practices:** Laravel and deployment best practices
- **CodeCanyon Specifics:** Vendor-specific considerations and management
- **Deployment Concepts:** Technical concepts and methodologies
- **FAQ & Common Issues:** Troubleshooting and problem resolution
- **Introduction Complete Overview:** Comprehensive system overview
- **Terminology Definitions:** Technical term definitions and conventions
- **Troubleshooting Guide:** Systematic problem diagnosis and resolution

### **🌟 Current Progress Status** (Phase 1 - Steps 1-10.1 Complete):
- ✅ **Step 01:** Project Information Card - Environment variables and project metadata configured
- ✅ **Step 02:** Create GitHub Repository - Repository setup and SSH/HTTPS configuration
- ✅ **Step 03:** Setup Local Structure - Directory hierarchy established for multi-project organization
- ✅ **Step 04:** Clone Repository - Git repository cloned and verified locally
- ✅ **Step 05:** Git Branching Strategy - Multi-environment deployment branches created
- ✅ **Step 06:** Universal GitIgnore - Deployment-compatible .gitignore with lock file strategy
- ✅ **Step 07:** Download CodeCanyon - Vendor application integrated (SocietyPro v1.0.42)
- ✅ **Step 08:** Commit Original Vendor Files - Pristine vendor files preserved in dedicated branch
- ✅ **Step 09:** Admin-Local Directory Structure - Project organization and customization layer established
- ✅ **Step 10:** CodeCanyon Configuration & License Management - License tracking and update safety implemented
- ✅ **Step 10.1:** Branch Synchronization & Progress Checkpoint - Multi-branch sync automation and progress tracking implemented

### **🎯 Deployment Scenarios Supported**
The guide supports 4 different deployment approaches (all involve build processes):

1. **🔧 Scenario A:** Local Build + Manual Upload - Build locally, upload manually to simple hosting environments
2. **🤖 Scenario B:** GitHub Actions - Automated CI/CD with GitHub workflows (builds on GitHub servers)
3. **🏢 Scenario C:** DeployHQ Professional - Enterprise deployment platform (builds on DeployHQ servers)
4. **📡 Scenario D:** Local Build + Git Pull Deployment - Build locally, deploy via git pull on server

**Build Process Notes:**
- **All scenarios require a build process** for Laravel applications (Composer, NPM, optimization)
- **Local build scenarios (A & D):** Build on your development machine, then deploy
- **Remote build scenarios (B & C):** Build occurs on remote servers/services during deployment
- **Professional approach:** Ensures consistent builds regardless of deployment method

### **🌳 Git Branching Strategy**

**Branch Structure** (6 branches total):
- 🔵 **`main`** - Primary development branch, integration point for all features (current primary)
- 🟢 **`development`** - Feature development and testing branch (may become primary later)
- 🟠 **`staging`** - Pre-production testing environment branch
- 🔴 **`production`** - Live production deployment branch
- ⚪ **`vendor/original`** - Pristine CodeCanyon vendor files (protected, no custom changes)
- 🟣 **`customized`** - Personal customizations and snapshots for CodeCanyon update safety

**Branch Purpose & Strategy:**
- **`vendor/original`**: Protected branch containing pristine CodeCanyon files, never modified
- **`main`**: Currently serves as primary development branch (may transition to `development` later)
- **`development`**: Feature development and testing (potential future primary branch)
- **`staging`**: Pre-production testing environment for deployment validation
- **`production`**: Live production deployment branch
- **`customized`**: Stores personal customizations and modifications as snapshots/backups for when CodeCanyon vendor pushes updates, ensuring custom work is preserved and easily accessible

**Workflow Pattern:**
```
vendor/original (protected) ← Pristine CodeCanyon files (never modified)
    ↓
main/development ← Primary development work
    ↓
staging ← Pre-production testing
    ↓
production ← Live deployment
    
customized ← Personal modifications & snapshots (parallel branch for backup)
```

### **📌 Checkpoint Naming System**

**Strategic Checkpoints** (Not after every step - only at critical milestones):

**Format:** `[COLOR_EMOJI] PHASE-STEP: Descriptive Title - YYYY-MM-DD`

**Color-Coded Visual System:**

#### **🔴 Critical Issues & Errors**
- **Purpose:** Critical problems, system errors, deployment failures, security issues
- **Examples:**
  - `🔴 P1-S12: Database Connection Error Fixed - 2025-01-13`
  - `🔴 P2-S03: Vendor Update Conflict Resolved - 2025-01-13`
- **Usage:** Emergency fixes, critical system failures, blocking issues

#### **🟠 Bugs & Uncertainties**
- **Purpose:** Non-critical bugs, warnings, unclear situations requiring investigation
- **Examples:**
  - `🟠 P1-S15: Build Warning Investigation - 2025-01-13`
  - `🟠 P3-S02: Performance Issue Under Review - 2025-01-13`
- **Usage:** Minor bugs, performance issues, warnings, investigation points

#### **🟢 Working Snapshots & Milestones**
- **Purpose:** Successfully completed work, verified milestones, tested functionality
- **Examples:**
  - `🟢 P1-S10: CodeCanyon Integration Complete - 2025-01-13`
  - `🟢 P1-S14: Local Environment Verified Working - 2025-01-13`
  - `🟢 P1-S10.1: Progress Synced to All Branches - 2025-01-13`
- **Usage:** Major completions, verified working states, successful deployments

#### **🟣 Customization Snapshots**
- **Purpose:** Personal modifications, custom features, customization backups before vendor updates
- **Examples:**
  - `🟣 P1-S17: Custom Auth System Snapshot - 2025-01-13`
  - `🟣 PRE-UPDATE: Custom Features Backed Up - 2025-01-13`
- **Usage:** Before vendor updates, custom feature milestones, personalization checkpoints

#### **⚪ Vendor & Tag-Based Commits**
- **Purpose:** Pristine vendor files, version-specific commits, tag-based releases
- **Examples:**
  - `⚪ VENDOR: SocietyPro v1.0.42 - 2025-01-13`
  - `⚪ TAG: v1.0.42-custom-base - 2025-01-13`
- **Usage:** Vendor/original branch commits, version tags, baseline establishment

#### **Additional Categories:**

**⬛ Major Phase Completion**
- **Purpose:** End of major phases, significant architectural milestones
- **Examples:**
  - `⬛ P1-COMPLETE: First Deployment Ready - 2025-01-13`
  - `⬛ P2-COMPLETE: Update System Established - 2025-01-13`

**⬜ Feature Implementation**
- **Purpose:** New feature additions, functional enhancements, capability extensions
- **Examples:**
  - `⬜ FEATURE: Multi-tenant Support Added - 2025-01-13`
  - `⬜ FEATURE: Advanced Reporting System - 2025-01-13`

**📋 Documentation & Review**
- **Purpose:** Documentation updates, review points, knowledge capture
- **Examples:**
  - `📋 DOC: Deployment Guide Updated - 2025-01-13`
  - `📋 REVIEW: Security Audit Complete - 2025-01-13`

**🔄 Synchronization & Maintenance**
- **Purpose:** Branch syncing, maintenance tasks, housekeeping operations
- **Examples:**
  - `🔄 SYNC: All Branches Updated - 2025-01-13`
  - `🔄 MAINT: Dependencies Updated - 2025-01-13`

**Checkpoint Strategy & Guidelines:**
- **Strategic Timing**: Only at critical milestones, not after every step
- **Visual Recognition**: Colors enable quick identification of checkpoint types
- **Version Correlation**: White (⚪) commits correlate with vendor version tags
- **Customization Safety**: Purple (🟣) snapshots before any vendor updates
- **Professional Organization**: Easy scanning of git history for specific issue types

### **🎯 CodeCanyon Integration Context**

**Application:** SocietyPro v1.0.42 - Society Management Software
- **Vendor Management Strategy:** Minimal vendor file modification, protected customization layer
- **License Management:** Comprehensive tracking with Admin-Local integration
- **Update Safety:** Advanced comparison scripts and integrity verification
- **Version Authority:** Git tags as source of truth (v1.0.42)

---

## **🚀 QUICK START WITH AI ASSISTANT**

### **1. Initial Setup Prompt**

```bash
"I'm using the V3 Laravel CodeCanyon Deployment Guide. Please help me deploy my project.

My project details:
- Project Name: [YourProjectName]  
- CodeCanyon App: [AppName] v[Version]
- Domain: [yourproject.com]
- Hosting Provider: [Provider]
- Server IP: [xxx.xxx.xxx.xxx] 
- GitHub: [yourusername/repository]
- Local Path: [/your/local/project/path]

Please use these details to customize all commands, file paths, and configurations throughout the deployment process."
```

### **2. Step-by-Step Assistance**

```bash
"Help me complete Step [X]: [Step Title]

My current context:
- Current directory: [path]
- Previous step completed: [Yes/No/Partial]
- Any errors encountered: [describe if any]

Please:
1. Review the step requirements
2. Customize commands for my project
3. Guide me through execution
4. Verify successful completion"
```

---

## **🛠️ ERROR RESOLUTION PROTOCOL**

### **When You Encounter Errors:**

```bash
"I encountered an error in Step [X]: [Step Title]

Error Details:
- Error Message: [exact error message]
- Command Executed: [command that failed]
- Current Directory: [path]
- Environment: [Mac/Linux/Windows]
- Laravel Version: [if applicable]
- PHP Version: [if applicable]

Please:
1. Diagnose the root cause
2. Provide immediate fix
3. Suggest step improvements
4. Help me verify the solution works"
```

### **AI Response Format for Errors:**

**Expected AI Response Structure:**
```markdown
## 🚨 Error Resolution - Step [X]

### Diagnosis
[Root cause analysis]

### Immediate Fix
[Commands/actions to resolve now]

### Step Improvement Suggestion
[How to update the step to prevent this error]

### Verification
[Commands to confirm the fix worked]

### Version Update Note
[Track this improvement for future versions]
```

---

## **📈 CONTINUOUS IMPROVEMENT REQUESTS**

### **Request Step Reviews:**

```bash
"Please review Step [X]: [Step Title] and suggest improvements for:

1. CLARITY: Is the step easy to understand?
2. RELIABILITY: Are there potential failure points?
3. MODERN PRACTICES: Are we using current best practices?
4. EDGE CASES: What scenarios might cause issues?
5. AUTOMATION: Can any parts be automated better?
6. CROSS-PLATFORM: Does it work on Mac/Linux/Windows?

Suggest specific improvements with reasoning."
```

### **Request Security Review:**

```bash
"Review the security aspects of Step [X]:
- Are we following Laravel security best practices?
- Are credentials properly protected?
- Are file permissions secure?
- Are there any security vulnerabilities?
- Should we add security hardening steps?"
```

---

## **🔧 PROJECT CUSTOMIZATION ASSISTANCE**

### **Path Configuration Help:**

```bash
"Help me configure the path variables for my project structure:

My Setup:
- Operating System: [Mac/Linux/Windows]
- Base Directory Preference: [e.g., /Users/username/Projects]
- Project Organization Method: [Simple/Technology-based/Client-based]
- Current Project Name: [ProjectName]

Please:
1. Suggest the best path pattern for my setup
2. Generate the complete path variables
3. Verify the configuration makes sense
4. Provide verification commands"
```

### **Hosting Provider Specific Help:**

```bash
"My hosting provider is [Provider] with these specifics:
- Control Panel: [cPanel/Hostinger/Custom]
- SSH Access: [Yes/No/Limited]
- Node.js Support: [Yes/No/Version]
- Composer Access: [Yes/No]
- File Manager: [Available/Restricted]

Please customize the deployment steps for my hosting environment and suggest any provider-specific optimizations."
```

---

## **📚 KNOWLEDGE BASE QUERIES**

### **Laravel Best Practices:**

```bash
"What are the current Laravel best practices for [specific topic] in [current year]? 
Are the methods used in Step [X] still current and secure?"
```

### **CodeCanyon Specific Help:**

```bash
"I'm working with a CodeCanyon Laravel application. 
What special considerations should I have for:
- License compliance
- Update procedures  
- Customization protection
- Vendor file management
- Documentation requirements"
```

### **Performance Optimization:**

```bash
"Review the deployment process for performance optimization opportunities:
- Build process efficiency
- Asset optimization
- Caching strategies
- Database optimization
- Server configuration"
```

---

## **🎯 SPECIALIZED ASSISTANCE REQUESTS**

### **GitHub Actions & CI/CD:**

```bash
"Help me set up automated deployment with:
- GitHub Actions workflows
- Automated testing
- Security scanning
- Performance monitoring
- Rollback procedures"
```

### **Database Management:**

```bash
"Assist with database deployment:
- Migration strategies
- Backup procedures
- Environment-specific configurations
- Seeding data management
- Performance optimization"
```

### **Environment Configuration:**

```bash
"Help configure environment-specific settings for:
- Development environment
- Staging environment  
- Production environment
- Local testing setup
- Team collaboration setup"
```

---

## **🚨 CRITICAL: HUMAN TASK CONFIRMATION PROTOCOL**

### **🏷️ Tag Instruct-User 👤 Usage Rules**

**ONLY use "🏷️ Tag Instruct-User 👤" for tasks that require human interaction OUTSIDE the codebase/terminal:**

✅ **Valid Human Task Examples:**
- Going to Herd GUI interface and configuring settings
- Visiting GitHub.com to create repositories or manage settings
- Logging into hosting control panels (cPanel, Hostinger, etc.)
- Creating databases through hosting provider interfaces
- Configuring SSH keys in GitHub/hosting dashboards
- Manual file uploads through hosting file managers
- Accessing external services or third-party platforms

❌ **NOT Valid for Human Task Tags:**
- Terminal commands that AI can execute (`ls`, `cp`, `php artisan`, `git`, etc.)
- File operations within the codebase (reading, writing, editing files)
- Environment file creation or modification via commands
- Any task the AI can perform programmatically

### **📋 Mandatory Confirmation Process**

**For ANY step containing "🏷️ Tag Instruct-User 👤" tags:**

1. **AI MUST confirm completion before proceeding to next step**
2. **Use `ask_followup_question` tool with this MANDATORY template:**

```
Step XX: [Step Title]
🚨 HUMAN TASK REQUIRED 🚨

Short Description: [Brief task summary]

Steps for human to follow:
1. [Specific numbered action]
2. [Specific numbered action]
3. [Specific numbered action]

Confirmation Question: [Specific question with multiple choice options when possible]
```

3. **Supplement with verification when possible:**
   - Use non-destructive commands to inspect results
   - Check codebase files that should have been affected
   - Verify system states that can be programmatically checked

4. **NEVER proceed without explicit human confirmation**

### **📁 Step File Structure for Human Tasks**

**When a step file contains human tasks, include at the TOP:**

```markdown
# 🚨 HUMAN INTERACTION REQUIRED

**⚠️ This step includes tasks that must be performed manually outside this codebase:**
- [Brief list of what human needs to do]
- **All other operations in this step are automated/AI-executable**

**🏷️ Tag Instruct-User 👤** markers indicate the specific substeps requiring human action.

---
```

### **🤖 AI Assistant Enforcement**

**Any AI following these guides MUST:**
- Automatically recognize "🏷️ Tag Instruct-User 👤" tags
- Stop progression at any step containing these tags
- Execute the mandatory confirmation process
- Document the human task completion in progress trackers
- Never assume human tasks are complete without explicit confirmation

---

## **⚠️ IMPORTANT AI GUIDELINES**

### **What AI Should Always Do:**

- ✅ **Test Commands:** Verify commands work before suggesting
- ✅ **Explain Reasoning:** Why this approach is recommended
- ✅ **Consider Environment:** Account for different OS/hosting setups
- ✅ **Security First:** Always prioritize security best practices
- ✅ **Version Awareness:** Use current Laravel/PHP best practices
- ✅ **Backup First:** Suggest backups before risky operations
- ✅ **Verify Results:** Provide verification steps
- ✅ **Document Changes:** Track improvements and reasoning
- ✅ **Human Task Recognition:** Always identify and confirm human tasks using proper protocols
- ✅ **Progress Verification:** Confirm step completion before proceeding

### **What AI Should Avoid:**

- ❌ **Destructive Commands:** Without proper warnings and backups
- ❌ **Hardcoded Values:** Always use variables and customization
- ❌ **Outdated Practices:** Check for current best practices
- ❌ **Incomplete Solutions:** Ensure full problem resolution
- ❌ **Security Shortcuts:** Never compromise security for convenience
- ❌ **Skipping Human Tasks:** Never proceed past human tasks without confirmation
- ❌ **Misusing Human Tags:** Don't tag automated/programmable tasks as human tasks

---

## **📝 IMPROVEMENT TRACKING SYSTEM**

### **When AI Suggests Improvements:**

```markdown
## 📈 Step Improvement Suggestion

**Step:** [Step Number and Title]
**Version:** [Current Version] → [Proposed Version]
**Improvement Type:** [Bug Fix/Enhancement/Security/Performance]

**Issue Identified:**
[Description of problem or inefficiency]

**Proposed Solution:**
[Detailed improvement recommendation]

**Benefits:**
- [List specific benefits]

**Testing Needed:**
- [Verification steps required]

**Documentation Updates:**
- [What needs to be documented]
```

### **Version Control for Step Updates:**

```bash
# Track all step modifications
echo "Step [X] Updated: [Date] - [Reason]" >> ~/deployment-guide-updates.log
echo "Previous Version: [description]" >> ~/deployment-guide-updates.log  
echo "New Version: [description]" >> ~/deployment-guide-updates.log
echo "Tested: [Yes/No] - [Date]" >> ~/deployment-guide-updates.log
echo "---" >> ~/deployment-guide-updates.log
```

---

**Next Step:** [Step 01: Project Information](Step_01_Project_Information.md)

**📚 Reference:** This guide enables AI-assisted deployment with continuous improvement and error resolution built into the process.
