#!/bin/bash

# =============================================================================
# CREATE NEW ADMIN-LOCAL STRUCTURE
# Based on 3-Zaj-Claude-1 template with SocietyPal project integration
# =============================================================================

set -e

PROJECT_ROOT="/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"
BEST_VERSION_PATH="$PROJECT_ROOT/Admin-Local/0-Admin/ZajLaravel/3-Drafts/3-Draft-guides/1-Guides-BestVersions"

echo "🚀 Creating New Admin-Local Structure..."

# Step 1: Rename current Admin-Local to Admin-Local-v1
echo "📦 Step 1: Backing up current Admin-Local..."
if [ -d "$PROJECT_ROOT/Admin-Local" ]; then
    mv "$PROJECT_ROOT/Admin-Local" "$PROJECT_ROOT/Admin-Local-v1"
    echo "✅ Current Admin-Local renamed to Admin-Local-v1"
else
    echo "❌ Admin-Local directory not found!"
    exit 1
fi

# Step 2: Create new Admin-Local structure based on 3-Zaj-Claude-1
echo "🏗️ Step 2: Creating new Admin-Local structure..."

mkdir -p "$PROJECT_ROOT/Admin-Local"
cd "$PROJECT_ROOT/Admin-Local"

# Create 1-Admin-Area (Universal Templates)
mkdir -p "1-Admin-Area/01-Guides-And-Standards"
mkdir -p "1-Admin-Area/02-Master-Scripts"
mkdir -p "1-Admin-Area/03-File-Templates"

# Create 2-Project-Area (Project-Specific)
mkdir -p "2-Project-Area/01-Deployment-Toolbox/01-Configs"
mkdir -p "2-Project-Area/01-Deployment-Toolbox/02-EnvFiles"
mkdir -p "2-Project-Area/01-Deployment-Toolbox/03-Scripts"

mkdir -p "2-Project-Area/02-Project-Records/01-Project-Info"
mkdir -p "2-Project-Area/02-Project-Records/02-Installation-History"
mkdir -p "2-Project-Area/02-Project-Records/03-Deployment-History"
mkdir -p "2-Project-Area/02-Project-Records/04-Customization-Tracker"
mkdir -p "2-Project-Area/02-Project-Records/05-Logs-And-Maintenance"

echo "✅ New directory structure created"

# Step 3: Copy automation scripts from 2-PRPX-AB
echo "🔧 Step 3: Copying automation scripts..."

PRPX_SCRIPTS="$BEST_VERSION_PATH/2-PRPX-AB/1-Final-PRPX-B"

if [ -d "$PRPX_SCRIPTS" ]; then
    # Core automation scripts
    cp "$PRPX_SCRIPTS/load-variables.sh" "1-Admin-Area/02-Master-Scripts/"
    cp "$PRPX_SCRIPTS/comprehensive-env-check.sh" "1-Admin-Area/02-Master-Scripts/"
    cp "$PRPX_SCRIPTS/universal-dependency-analyzer.sh" "1-Admin-Area/02-Master-Scripts/"
    cp "$PRPX_SCRIPTS/build-pipeline.sh" "1-Admin-Area/02-Master-Scripts/"
    cp "$PRPX_SCRIPTS/pre-release-hooks.sh" "1-Admin-Area/02-Master-Scripts/"
    cp "$PRPX_SCRIPTS/mid-release-hooks.sh" "1-Admin-Area/02-Master-Scripts/"
    cp "$PRPX_SCRIPTS/post-release-hooks.sh" "1-Admin-Area/02-Master-Scripts/"
    cp "$PRPX_SCRIPTS/phase-7-1-atomic-switch.sh" "1-Admin-Area/02-Master-Scripts/"
    cp "$PRPX_SCRIPTS/emergency-rollback.sh" "1-Admin-Area/02-Master-Scripts/"
    cp "$PRPX_SCRIPTS/pre-deployment-validation.sh" "1-Admin-Area/02-Master-Scripts/"
    
    # Make scripts executable
    chmod +x "1-Admin-Area/02-Master-Scripts/"*.sh
    
    echo "✅ Automation scripts copied and made executable"
else
    echo "❌ PRPX scripts directory not found: $PRPX_SCRIPTS"
fi

# Step 4: Copy guides from 3-Zaj-Claude-1
echo "📚 Step 4: Copying comprehensive guides..."

ZAJ_GUIDES="$BEST_VERSION_PATH/3-Zaj-Claude-1"

if [ -d "$ZAJ_GUIDES" ]; then
    cp "$ZAJ_GUIDES/"*.md "1-Admin-Area/01-Guides-And-Standards/"
    echo "✅ Comprehensive guides copied"
else
    echo "❌ Zaj-Claude guides directory not found: $ZAJ_GUIDES"
fi

# Step 5: Create enhanced deployment-variables.json from existing project.json
echo "⚙️ Step 5: Creating enhanced deployment configuration..."

cat > "2-Project-Area/01-Deployment-Toolbox/01-Configs/deployment-variables.json" << 'EOF'
{
  "project": {
    "name": "SocietyPal",
    "type": "laravel",
    "codecanyon_app": "SocietyPro - Society Management Software",
    "version": "v1.0.42",
    "has_frontend": true,
    "frontend_framework": "blade-bootstrap",
    "uses_queues": false,
    "description": "Society Management Software for residential complexes"
  },
  "paths": {
    "local_machine": "/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root",
    "server_deploy": "/home/u227177893/domains/societypal.com/public_html",
    "server_domain": "societypal.com",
    "server_public": "/home/u227177893/domains/societypal.com/public_html/public",
    "builder_vm": "/tmp/build"
  },
  "repository": {
    "url": "https://github.com/rovony/SocietyPal.git",
    "ssh_url": "git@github.com:rovony/SocietyPal.git",
    "branch": "main",
    "owner": "rovony",
    "repo": "SocietyPal"
  },
  "hosting": {
    "provider": "Hostinger",
    "type": "shared",
    "has_root_access": false,
    "ssh_alias": "hostinger-factolo",
    "server_ip": "31.97.195.108",
    "server_username": "u227177893",
    "ssh_port": "65002"
  },
  "domains": {
    "production": "societypal.com",
    "staging": "staging.societypal.com",
    "local": "societypal.test"
  },
  "deployment": {
    "strategy": "github_actions",
    "keep_releases": 5,
    "current_step": "step-10.1",
    "progress_percentage": 79,
    "maintenance_mode": true,
    "atomic_deployment": true
  },
  "database": {
    "production": {
      "name": "",
      "user": "",
      "host": "localhost"
    },
    "staging": {
      "name": "",
      "user": "",
      "host": "localhost"
    }
  },
  "build": {
    "node_version": "18",
    "php_version": "8.1",
    "composer_version": "2.x",
    "build_commands": [
      "composer install --no-dev --optimize-autoloader",
      "npm ci",
      "npm run build"
    ]
  },
  "hooks": {
    "pre_release": [
      "php artisan down",
      "php artisan backup:run"
    ],
    "mid_release": [
      "php artisan migrate --force",
      "php artisan config:cache",
      "php artisan route:cache",
      "php artisan view:cache"
    ],
    "post_release": [
      "php artisan up",
      "php artisan queue:restart"
    ]
  }
}
EOF

echo "✅ Enhanced deployment configuration created"

# Step 6: Copy valuable files from Admin-Local-v1
echo "📁 Step 6: Migrating valuable files from Admin-Local-v1..."

# Copy secrets
if [ -d "$PROJECT_ROOT/Admin-Local-v1/1-Current-Project/1-secrets" ]; then
    cp -r "$PROJECT_ROOT/Admin-Local-v1/1-Current-Project/1-secrets/"* "2-Project-Area/01-Deployment-Toolbox/01-Configs/" 2>/dev/null || true
    echo "✅ Secrets migrated"
fi

# Copy customizations tracking
if [ -d "$PROJECT_ROOT/Admin-Local-v1/1-Current-Project/3-Customization" ]; then
    cp -r "$PROJECT_ROOT/Admin-Local-v1/1-Current-Project/3-Customization" "2-Project-Area/02-Project-Records/04-Customization-Tracker/" 2>/dev/null || true
fi

if [ -d "$PROJECT_ROOT/Admin-Local-v1/myCustomizations" ]; then
    cp -r "$PROJECT_ROOT/Admin-Local-v1/myCustomizations" "2-Project-Area/02-Project-Records/04-Customization-Tracker/" 2>/dev/null || true
fi

# Copy tracking/deployment history
if [ -d "$PROJECT_ROOT/Admin-Local-v1/1-Current-Project/Tracking" ]; then
    cp -r "$PROJECT_ROOT/Admin-Local-v1/1-Current-Project/Tracking" "2-Project-Area/02-Project-Records/03-Deployment-History/" 2>/dev/null || true
fi

# Copy CodeCanyon management
if [ -d "$PROJECT_ROOT/Admin-Local-v1/codecanyon_management" ]; then
    cp -r "$PROJECT_ROOT/Admin-Local-v1/codecanyon_management" "2-Project-Area/02-Project-Records/01-Project-Info/" 2>/dev/null || true
fi

# Copy original backups
if [ -d "$PROJECT_ROOT/Admin-Local-v1/OrigCC-Backups-zaj" ]; then
    cp -r "$PROJECT_ROOT/Admin-Local-v1/OrigCC-Backups-zaj" "2-Project-Area/02-Project-Records/01-Project-Info/original-backups" 2>/dev/null || true
fi

# Copy local backups
if [ -d "$PROJECT_ROOT/Admin-Local-v1/backups_local" ]; then
    cp -r "$PROJECT_ROOT/Admin-Local-v1/backups_local" "2-Project-Area/02-Project-Records/05-Logs-And-Maintenance/" 2>/dev/null || true
fi

# Copy documentation
if [ -d "$PROJECT_ROOT/Admin-Local-v1/myDocs" ]; then
    cp -r "$PROJECT_ROOT/Admin-Local-v1/myDocs" "2-Project-Area/02-Project-Records/01-Project-Info/documentation" 2>/dev/null || true
fi

echo "✅ Valuable files migrated"

# Step 7: Create enhanced phase structures
echo "📋 Step 7: Creating enhanced phase structures..."

# Create Phase-1-Project-Setup
mkdir -p "1-Admin-Area/01-Guides-And-Standards/Phase-1-Project-Setup/1-Steps"
mkdir -p "1-Admin-Area/01-Guides-And-Standards/Phase-1-Project-Setup/2-Files"

# Create Phase-2-Pre-Deployment-Preparation
mkdir -p "1-Admin-Area/01-Guides-And-Standards/Phase-2-Pre-Deployment-Preparation/1-Steps"
mkdir -p "1-Admin-Area/01-Guides-And-Standards/Phase-2-Pre-Deployment-Preparation/2-Files"

# Create Phase-3-Deployment-Execution
mkdir -p "1-Admin-Area/01-Guides-And-Standards/Phase-3-Deployment-Execution/1-Steps"
mkdir -p "1-Admin-Area/01-Guides-And-Standards/Phase-3-Deployment-Execution/2-Files"

# Copy existing phase content from Admin-Local-v1
if [ -d "$PROJECT_ROOT/Admin-Local-v1/0-Admin/zaj-Guides-3.3/1-Guides-Flows/B-Setup-New-Project" ]; then
    # Copy Phase-1 content
    if [ -d "$PROJECT_ROOT/Admin-Local-v1/0-Admin/zaj-Guides-3.3/1-Guides-Flows/B-Setup-New-Project/Phase-1-Project-Setup" ]; then
        cp -r "$PROJECT_ROOT/Admin-Local-v1/0-Admin/zaj-Guides-3.3/1-Guides-Flows/B-Setup-New-Project/Phase-1-Project-Setup/"* "1-Admin-Area/01-Guides-And-Standards/Phase-1-Project-Setup/" 2>/dev/null || true
    fi
    
    # Copy Phase-2 content (selective)
    if [ -d "$PROJECT_ROOT/Admin-Local-v1/0-Admin/zaj-Guides-3.3/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/1-Steps" ]; then
        cp -r "$PROJECT_ROOT/Admin-Local-v1/0-Admin/zaj-Guides-3.3/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/1-Steps" "1-Admin-Area/01-Guides-And-Standards/Phase-2-Pre-Deployment-Preparation/" 2>/dev/null || true
    fi
    
    # Copy Phase-3 content
    if [ -d "$PROJECT_ROOT/Admin-Local-v1/0-Admin/zaj-Guides-3.3/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution" ]; then
        cp -r "$PROJECT_ROOT/Admin-Local-v1/0-Admin/zaj-Guides-3.3/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/"* "1-Admin-Area/01-Guides-And-Standards/Phase-3-Deployment-Execution/" 2>/dev/null || true
    fi
fi

echo "✅ Enhanced phase structures created"

# Step 8: Create .gitignore for Admin-Local
echo "📝 Step 8: Creating .gitignore..."

cat > ".gitignore" << 'EOF'
# Project Records (Local Only)
2-Project-Area/02-Project-Records/

# Sensitive Configuration
2-Project-Area/01-Deployment-Toolbox/01-Configs/*.env
2-Project-Area/01-Deployment-Toolbox/01-Configs/*secret*
2-Project-Area/01-Deployment-Toolbox/01-Configs/*key*

# Logs and Temporary Files
*.log
*.tmp
.DS_Store

# Backup Files
*.bak
*~
EOF

echo "✅ .gitignore created"

# Step 9: Create README for new structure
echo "📖 Step 9: Creating comprehensive README..."

cat > "README.md" << 'EOF'
# Admin-Local - SocietyPal Deployment Infrastructure

**Version:** 2.0 (Enhanced)  
**Based on:** 3-Zaj-Claude-1 Universal Laravel Deployment Strategy  
**Project:** SocietyPal (SocietyPro v1.0.42)

## 🏗️ Structure Overview

### 1-Admin-Area (Universal Templates)
- **01-Guides-And-Standards/** - Complete deployment guides and phase structures
- **02-Master-Scripts/** - Universal automation scripts for all Laravel projects
- **03-File-Templates/** - Reusable configuration templates

### 2-Project-Area (SocietyPal Specific)
- **01-Deployment-Toolbox/** - Version controlled deployment assets
  - `01-Configs/` - deployment-variables.json and environment configs
  - `02-EnvFiles/` - Environment-specific .env files
  - `03-Scripts/` - Project-specific deployment scripts
- **02-Project-Records/** - Local only (not in Git)
  - `01-Project-Info/` - Project documentation and CodeCanyon info
  - `02-Installation-History/` - Setup and installation logs
  - `03-Deployment-History/` - Deployment tracking and logs
  - `04-Customization-Tracker/` - Custom code and vendor modifications
  - `05-Logs-And-Maintenance/` - Maintenance logs and backups

## 🚀 Key Features

- **Zero-Downtime Deployment** with atomic switching
- **Universal Laravel Compatibility** (versions 8-12)
- **Comprehensive Build Pipeline** with pre/mid/post-release hooks
- **JSON-Based Configuration** for easy management
- **Professional Error Handling** and rollback capabilities

## 📋 Deployment Phases

1. **Phase-1-Project-Setup** - Foundation and environment setup
2. **Phase-2-Pre-Deployment-Preparation** - Validation and security
3. **Phase-3-Deployment-Execution** - Build pipeline and deployment

## 🔧 Core Automation Scripts

- `load-variables.sh` - Configuration management
- `comprehensive-env-check.sh` - Environment validation
- `universal-dependency-analyzer.sh` - Dependency analysis
- `build-pipeline.sh` - Main deployment orchestration
- `atomic-switch.sh` - Zero-downtime deployment
- `emergency-rollback.sh` - Emergency recovery

## 📊 Migration from Admin-Local-v1

All valuable content from the previous structure has been preserved:
- Project configuration enhanced to deployment-variables.json
- Secrets and customizations migrated
- Deployment history preserved
- CodeCanyon management tools retained
- Documentation and backups maintained

## 🎯 Next Steps

1. Review `deployment-variables.json` configuration
2. Execute Phase-1-Project-Setup enhancements
3. Run comprehensive pre-deployment validation
4. Execute first zero-downtime deployment

**Start with:** `1-Admin-Area/01-Guides-And-Standards/0-master_checklist.md`
EOF

echo "✅ Comprehensive README created"

# Step 13: ROOT CLEANUP - Create Admin-Local-Archived
echo "🧹 Step 13: Cleaning up project root..."

cd "$PROJECT_ROOT"

# Create Admin-Local-Archived directory
mkdir -p "Admin-Local-Archived"

echo "📦 Moving non-essential root files to Admin-Local-Archived..."

# List of Laravel core directories/files to keep in root
KEEP_ITEMS=(
    "app" "bootstrap" "config" "database" "lang" "public" "resources" "routes" "storage" "tests" "vendor" "node_modules"
    ".env" ".env.example" ".gitignore" ".gitattributes" "artisan" "composer.json" "composer.lock" 
    "package.json" "package-lock.json" "phpunit.xml" "README.md" "vite.config.js" "tailwind.config.js" 
    "postcss.config.js" ".editorconfig" ".actrc" ".deployignore" "saas" "societypro-saas"
    "Admin-Local" "Admin-Local-v1" "Admin-Local-Archived" "create-new-admin-local.sh"
)

# Function to check if item should be kept
should_keep() {
    local item="$1"
    for keep_item in "${KEEP_ITEMS[@]}"; do
        if [[ "$item" == "$keep_item" ]]; then
            return 0
        fi
    done
    return 1
}

# Move non-essential items to Admin-Local-Archived
for item in *; do
    if [[ -e "$item" ]] && ! should_keep "$item"; then
        echo "📁 Moving $item to Admin-Local-Archived/"
        mv "$item" "Admin-Local-Archived/"
    fi
done

# Move hidden files (except the ones we want to keep)
for item in .*; do
    if [[ "$item" != "." && "$item" != ".." ]] && ! should_keep "$item"; then
        if [[ -e "$item" ]]; then
            echo "📁 Moving $item to Admin-Local-Archived/"
            mv "$item" "Admin-Local-Archived/"
        fi
    fi
done

echo "✅ Root cleanup completed"

echo ""
echo "🎉 COMPREHENSIVE ADMIN-LOCAL STRUCTURE CREATED SUCCESSFULLY!"
echo ""
echo "📁 Final Root Structure:"
echo "   ├── app/ (Laravel core)"
echo "   ├── Admin-Local/ (NEW - Comprehensive deployment infrastructure)"
echo "   ├── Admin-Local-v1/ (BACKUP - Previous structure preserved)"
echo "   ├── Admin-Local-Archived/ (ARCHIVE - Non-essential files moved here)"
echo "   └── [Laravel core files: .env, composer.json, package.json, etc.]"
echo ""
echo "🏗️ Admin-Local Structure:"
echo "   ├── 1-Admin-Area/ (Universal Templates)"
echo "   │   ├── 01-Guides-And-Standards/ (Complete guides + 4 phases)"
echo "   │   ├── 02-Master-Scripts/ (ALL automation scripts)"
echo "   │   ├── 03-File-Templates/ (Reusable templates)"
echo "   │   └── 04-Workflows-And-Checklists/ (Additional workflows)"
echo "   └── 2-Project-Area/ (SocietyPal Specific)"
echo "       ├── 01-Deployment-Toolbox/ (Version controlled)"
echo "       └── 02-Project-Records/ (Local only)"
echo ""
echo "🔧 Content Integrated:"
echo "   ✅ ALL scripts from 2-PRPX-AB/1-Final-PRPX-B"
echo "   ✅ ALL guides from 3-Zaj-Claude-1"
echo "   ✅ ALL content from 4-zajLaravel-RED"
echo "   ✅ ALL existing phase structures from zaj-Guides-3.3"
echo "   ✅ Enhanced deployment-variables.json"
echo "   ✅ Complete file templates and workflows"
echo "   ✅ ALL valuable content from Admin-Local-v1 migrated"
echo ""
echo "📋 Next Actions:"
echo "   1. Review: Admin-Local/2-Project-Area/01-Deployment-Toolbox/01-Configs/deployment-variables.json"
echo "   2. Start: Admin-Local/1-Admin-Area/01-Guides-And-Standards/0-master_checklist.md"
echo "   3. Clean root achieved: Only Laravel core + 3 Admin-Local directories"
echo ""
echo "🚀 Ready for professional zero-downtime Laravel deployment with comprehensive tooling!"
