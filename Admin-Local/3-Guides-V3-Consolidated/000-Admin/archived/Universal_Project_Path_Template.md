# Universal Project Path Configuration Template

## 🎯 **Purpose**

This template provides a standardized approach for configuring project paths that can be easily adapted for any Laravel/CodeCanyon project.

## ⚠️ **CUSTOMIZATION REQUIRED**

**Before starting any project, customize these variables in Step_01_Project_Information.md**

## 🔧 **Universal Path Variables**

```bash
# =======================================================================
# UNIVERSAL PROJECT PATH CONFIGURATION
# =======================================================================
# Customize these variables for each new project

# 1. PROJECT STRUCTURE VARIABLES
export PROJECT_BASE_PATH="/Users/[USERNAME]/[YOUR_PROJECT_BASE]"
export PROJECT_FOLDER_NAME="[ProjectName]-Project"
export APP_ROOT_FOLDER_NAME="[ProjectName]App-Master/[ProjectName]App-Root"
export PROJECT_ROOT="$PROJECT_BASE_PATH/$PROJECT_FOLDER_NAME/$APP_ROOT_FOLDER_NAME"
export ADMIN_LOCAL="$PROJECT_ROOT/Admin-Local"
export PROJECT_NAME="[ProjectName]"

# 2. DOMAIN CONFIGURATION
export PRODUCTION_DOMAIN="[yourproject].com"
export STAGING_DOMAIN="staging.[yourproject].com"
export LOCAL_DOMAIN="[yourproject].test"

# 3. HOSTING CONFIGURATION
export HOSTING_PROVIDER="[YourProvider]"
export SSH_ALIAS="[provider-alias]"
export SERVER_IP="[xxx.xxx.xxx.xxx]"
export SERVER_USERNAME="[username]"
export SSH_PORT="[port]"

# 4. GITHUB CONFIGURATION
export GITHUB_OWNER="[yourusername]"
export GITHUB_REPO="[ProjectName]"
export GITHUB_HTTPS="https://github.com/$GITHUB_OWNER/$GITHUB_REPO"
export GITHUB_SSH="git@github.com:$GITHUB_OWNER/$GITHUB_REPO.git"

# 5. SERVER PATHS (AUTO-GENERATED)
export DOMAIN_ROOT="~/domains/$PRODUCTION_DOMAIN"
export ADMIN_DOMAIN="$DOMAIN_ROOT/Admin-Domain"
export SERVER_SCRIPTS="$ADMIN_DOMAIN/scripts"
export SERVER_DOCS="$ADMIN_DOMAIN/docs"
export SERVER_DEPLOYMENT="$ADMIN_DOMAIN/deployment"
```

## 📁 **Common Project Structure Patterns**

### **Pattern 1: Simple Structure**

```bash
export PROJECT_BASE_PATH="/Users/username/Projects"
export PROJECT_FOLDER_NAME="MyApp"
export APP_ROOT_FOLDER_NAME="MyApp-Root"
# Result: /Users/username/Projects/MyApp/MyApp-Root
```

### **Pattern 2: Organized by Technology**

```bash
export PROJECT_BASE_PATH="/Users/username/Development/Laravel"
export PROJECT_FOLDER_NAME="EcommerceApp-Project"
export APP_ROOT_FOLDER_NAME="EcommerceApp-Master/EcommerceApp-Root"
# Result: /Users/username/Development/Laravel/EcommerceApp-Project/EcommerceApp-Master/EcommerceApp-Root
```

### **Pattern 3: Client-Based Organization**

```bash
export PROJECT_BASE_PATH="/Users/username/Clients/ClientName"
export PROJECT_FOLDER_NAME="ProjectName-Laravel"
export APP_ROOT_FOLDER_NAME="app"
# Result: /Users/username/Clients/ClientName/ProjectName-Laravel/app
```

## 🔄 **Adaptation Steps for New Projects**

1. **Copy the Universal Template** from Step_01_Project_Information.md
2. **Replace Placeholder Values:**

   - `[USERNAME]` → Your actual username
   - `[YOUR_PROJECT_BASE]` → Your preferred project directory
   - `[ProjectName]` → Your actual project name
   - `[yourproject]` → Your domain name
   - `[YourProvider]` → Your hosting provider
   - `[provider-alias]` → Your SSH alias
   - `[xxx.xxx.xxx.xxx]` → Your server IP
   - `[username]` → Your server username
   - `[port]` → Your SSH port
   - `[yourusername]` → Your GitHub username

3. **Update All Subsequent Steps** to use your customized variables

## 🎯 **Files That Use These Variables**

The following files will automatically use your customized paths:

- All Subsequent-Deployment steps (Step_01 through Step_09)
- CodeCanyon configuration steps
- Build and deployment scripts
- Backup and maintenance procedures

## ✅ **Verification Command**

After customization, verify your paths:

```bash
echo "Project Root: $PROJECT_ROOT"
echo "Admin-Local: $ADMIN_LOCAL"
echo "Production Domain: $PRODUCTION_DOMAIN"
echo "Server Admin-Domain: $ADMIN_DOMAIN"
```

## 💡 **Benefits of This Approach**

- **Universally Applicable**: Works for any Laravel/CodeCanyon project
- **Easy Customization**: Change variables in one place
- **Consistent Structure**: Maintains organization across projects
- **AI-Friendly**: Clear variable definitions for automated assistance
- **Scalable**: Easy to adapt for team environments
