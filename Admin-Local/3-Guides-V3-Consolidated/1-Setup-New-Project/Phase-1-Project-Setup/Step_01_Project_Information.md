# Step 01: Project Information Card

**Essential project metadata for team reference and AI coding assistance:**

> 📋 **Analysis Source:** V2 Step 0 - V1 had nothing, taking V2 entirely
>
> 🎯 **Purpose:** Document project metadata for team reference, deployment configuration, and AI coding assistance

---

## **🤖 AI CODING ASSISTANT SETUP**

**INSTRUCT-USER: Project Information Collection for AI Assistance**

```bash
echo "🤖 AI CODING ASSISTANT INFORMATION COLLECTION"
echo "============================================="
echo "⚠️  HUMAN INPUT REQUIRED - Project Configuration"
echo ""
echo "📋 Please provide the following information for AI coding assistance:"
echo ""
echo "1. PROJECT BASICS:"
echo "   - Project Name: [e.g., SocietyPal]"
echo "   - CodeCanyon App Name: [e.g., SocietyPro]"
echo "   - App Version: [e.g., v1.0.4]"
echo ""
echo "2. DOMAIN CONFIGURATION:"
echo "   - Production URL: [e.g., societypal.com]"
echo "   - Staging URL: [e.g., staging.societypal.com]"
echo "   - Local URL: [e.g., societypal.test]"
echo ""
echo "3. HOSTING DETAILS:"
echo "   - Provider: [e.g., Hostinger]"
echo "   - SSH Alias: [e.g., hostinger-factolo]"
echo "   - IP Address: [e.g., 31.97.195.108]"
echo "   - Username: [e.g., u227177893]"
echo "   - SSH Port: [e.g., 65002]"
echo ""
echo "4. GITHUB REPOSITORY:"
echo "   - Repository Owner: [e.g., rovony]"
echo "   - Repository Name: [e.g., SocietyPal]"
echo ""
echo "5. LOCAL DEVELOPMENT PATHS (CUSTOMIZABLE PER PROJECT):"
echo "   - Mac Project Base Path: [e.g., /Users/[username]/Projects]"
echo "   - Project Folder Name: [e.g., SocietyPal-Project]"
echo "   - App Root Folder Name: [e.g., SocietyPalApp-Root]"
echo ""
echo "6. DATABASE CREDENTIALS:"
echo "   - Production DB Name: [e.g., u227177893_p_zaj_socpal_d]"
echo "   - Production DB User: [e.g., u227177893_p_zaj_socpal_u]"
echo "   - Staging DB Name: [e.g., u227177893_s_zaj_socpal_d]"
echo "   - Staging DB User: [e.g., u227177893_s_zaj_socpal_u]"
echo ""
echo "💡 AI Assistant will use this information to:"
echo "   ✅ Generate project-specific commands"
echo "   ✅ Create accurate file paths"
echo "   ✅ Configure deployment scripts"
echo "   ✅ Set up proper directory structures"
echo "   ✅ Customize all subsequent steps"
echo ""
echo "💡 Complete this information before proceeding to Step 02"
```

**END-INSTRUCT-USER**

---

## **🤖 AI ASSISTANT INSTRUCTIONS**

**⚠️ READ THIS WHEN WORKING WITH AI CODING ASSISTANTS**

### **📋 How to Use AI Assistance with This Guide**

1. **Provide Project Information:**
   ```bash
   "Please help me with Laravel deployment using the V3 guide. 
   My project details are:
   - Project Name: [YourProjectName]
   - Domain: [yourproject.com]  
   - Hosting: [YourProvider]
   - GitHub: [yourusername/YourRepo]
   - Local Path: [/your/project/path]
   
   Use these details to customize all commands and file paths."
   ```

2. **Request Error Resolution:**
   ```bash
   "I encountered this error in Step [X]: [error message]
   Please help me:
   1. Diagnose the issue
   2. Provide a fix
   3. Suggest step improvements to prevent this error
   4. Update the step documentation if needed"
   ```

3. **Ask for Step Improvements:**
   ```bash
   "Review Step [X] and suggest improvements for:
   - Clarity and ease of understanding
   - Reliability and error prevention
   - Modern best practices
   - Missing edge cases or scenarios
   - Better automation opportunities"
   ```

### **🛠️ AI Error Handling Protocol**

**When AI Finds Issues:**
1. **Document the Error:** Include step number, error message, context
2. **Provide Fix:** Give immediate solution
3. **Improve Step:** Suggest permanent step enhancement
4. **Track Changes:** Note version updates and reasoning

**Example AI Response Format:**
```markdown
## 🚨 Error Resolution - Step [X]

**Issue Found:** [Description]
**Immediate Fix:** [Commands/actions to resolve]
**Step Improvement:** [How to prevent this in future]
**Version Update:** [Track this change]
```

### **📈 Continuous Improvement System**

**AI Should Always:**
- ✅ Test commands before suggesting them
- ✅ Consider different environments (Mac/Linux/Windows)
- ✅ Suggest more reliable alternatives
- ✅ Flag outdated practices
- ✅ Recommend modern Laravel best practices
- ✅ Provide fallback options for edge cases

---

## **🔧 UNIVERSAL PATH CONFIGURATION TEMPLATE**

**⚠️ EMBEDDED TEMPLATE - CUSTOMIZE FOR EACH PROJECT**

### **Common Project Structure Patterns:**

```bash
# PATTERN 1: Simple Structure
export PROJECT_BASE_PATH="/Users/username/Projects"
export PROJECT_FOLDER_NAME="MyApp"
export APP_ROOT_FOLDER_NAME="MyApp-Root"
# Result: /Users/username/Projects/MyApp/MyApp-Root

# PATTERN 2: Organized by Technology  
export PROJECT_BASE_PATH="/Users/username/Development/Laravel"
export PROJECT_FOLDER_NAME="EcommerceApp-Project"
export APP_ROOT_FOLDER_NAME="EcommerceApp-Master/EcommerceApp-Root"
# Result: /Users/username/Development/Laravel/EcommerceApp-Project/EcommerceApp-Master/EcommerceApp-Root

# PATTERN 3: Client-Based Organization
export PROJECT_BASE_PATH="/Users/username/Clients/ClientName"
export PROJECT_FOLDER_NAME="ProjectName-Laravel"
export APP_ROOT_FOLDER_NAME="app"
# Result: /Users/username/Clients/ClientName/ProjectName-Laravel/app
```

### **Template Customization Steps:**

1. **Choose Your Pattern** from above
2. **Replace Placeholders:**
   - `[USERNAME]` → Your actual username
   - `[ProjectName]` → Your project name
   - `[yourproject]` → Your domain name
   - `[YourProvider]` → Your hosting provider
   - `[yourusername]` → Your GitHub username
   - All IP addresses, usernames, passwords with your actual values

3. **Verify Your Configuration:**
   ```bash
   echo "Project Root: $PROJECT_ROOT"
   echo "Admin-Local: $ADMIN_LOCAL" 
   echo "Production Domain: $PRODUCTION_DOMAIN"
   ```

### **Files Using These Variables:**
- All Subsequent-Deployment steps (Step_01 through Step_09)
- CodeCanyon configuration steps
- Build and deployment scripts
- Backup and maintenance procedures

---

## **📝 Project Information Template**

**⚠️ NOTE: This template uses SocietyPal as an example. Customize all values for your specific project.**

```bash
# =======================================================================
# PROJECT CONFIGURATION - CUSTOMIZE FOR EACH NEW PROJECT
# =======================================================================
# ⚠️  IMPORTANT: Change these values for your specific project
# This template uses SocietyPal as an example - replace with your project details

# PATH VARIABLES - CUSTOMIZE THESE FOR YOUR PROJECT
export PROJECT_BASE_PATH="/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps"
export PROJECT_FOLDER_NAME="SocietyPal-Project"  # ⚠️ CHANGE THIS
export APP_ROOT_FOLDER_NAME="SocietyPalApp-Master/SocietyPalApp-Root"  # ⚠️ CHANGE THIS
export PROJECT_ROOT="$PROJECT_BASE_PATH/$PROJECT_FOLDER_NAME/$APP_ROOT_FOLDER_NAME"
export ADMIN_LOCAL="$PROJECT_ROOT/Admin-Local"
export PROJECT_NAME="SocietyPal"  # ⚠️ CHANGE THIS

# DOMAIN CONFIGURATION - CUSTOMIZE FOR YOUR PROJECT
export PRODUCTION_DOMAIN="societypal.com"  # ⚠️ CHANGE THIS
export STAGING_DOMAIN="staging.societypal.com"  # ⚠️ CHANGE THIS
export LOCAL_DOMAIN="societypal.test"  # ⚠️ CHANGE THIS

# HOSTING CONFIGURATION - CUSTOMIZE FOR YOUR HOSTING
export HOSTING_PROVIDER="Hostinger"  # ⚠️ CHANGE THIS
export SSH_ALIAS="hostinger-factolo"  # ⚠️ CHANGE THIS
export SERVER_IP="31.97.195.108"  # ⚠️ CHANGE THIS
export SERVER_USERNAME="u227177893"  # ⚠️ CHANGE THIS
export SSH_PORT="65002"  # ⚠️ CHANGE THIS

# GITHUB CONFIGURATION - CUSTOMIZE FOR YOUR REPOSITORY
export GITHUB_OWNER="rovony"  # ⚠️ CHANGE THIS
export GITHUB_REPO="SocietyPal"  # ⚠️ CHANGE THIS
export GITHUB_HTTPS="https://github.com/$GITHUB_OWNER/$GITHUB_REPO"
export GITHUB_SSH="git@github.com:$GITHUB_OWNER/$GITHUB_REPO.git"

# =======================================================================
# EXAMPLE PROJECT DETAILS - SocietyPal (FOR REFERENCE)
# =======================================================================

# PROJECT DETAILS
Project Name: SocietyPal
Production URL: societypal.com
Staging URL: staging.societypal.com
Local URL: societypal.test

# CODECANYON APPLICATION
App Name: SocietyPro - Society Management Software
App URL: https://codecanyon.net/item/societypro-society-management-software/56828726
Current Version: v1.0.4
Changelog: https://envato.froid.works/version-log/societypro

# HOSTING DETAILS
Provider: Hostinger
SSH Alias: hostinger-factolo
IP Address: 31.97.195.108
Username: u227177893
SSH Port: 65002

# GITHUB REPOSITORY
HTTPS: https://github.com/rovony/SocietyPal
SSH: git@github.com:rovony/SocietyPal.git

# LOCAL DEVELOPMENT PATHS
Mac Project Base: /Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps
Project Folder: SocietyPal-Project
App Root Path: $PROJECT_ROOT
Admin-Local Path: $ADMIN_LOCAL

# SERVER STRUCTURE (Admin-Domain concept)
Domain Root: ~/domains/$PRODUCTION_DOMAIN
Admin-Domain: ~/domains/$PRODUCTION_DOMAIN/Admin-Domain
Server Scripts: ~/domains/$PRODUCTION_DOMAIN/Admin-Domain/scripts
Server Docs: ~/domains/$PRODUCTION_DOMAIN/Admin-Domain/docs
Deployment: ~/domains/$PRODUCTION_DOMAIN/Admin-Domain/deployment

# DATABASE CREDENTIALS (EXAMPLE - USE YOUR ACTUAL CREDENTIALS)
Production: u227177893_p_zaj_socpal_d / u227177893_p_zaj_socpal_u / "t5TmP9$[iG7hu2eYRWUIWH@IRF2"
Staging: u227177893_s_zaj_socpal_d / u227177893_s_zaj_socpal_u / "V0Z^G=I2:=r^f2"
```

**Expected Result:** Project metadata documented for team reference and deployment configuration.

---

**Next Step:** [Step 02: Create GitHub Repository](Step_02_Create_GitHub_Repository.md)
