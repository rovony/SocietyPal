# 🚀 Site-Centric Multi-Environment Deployment Wizard v2.1

## 📋 **IMPLEMENTATION PLAN & ARCHITECTURE**

### **🎯 CORE VISION**
Transform the existing environment-centric wizard into a flexible site-centric system supporting:
- **Project → Multiple Sites → Multiple Deployments → Multiple Hosting Providers**
- **Unified Templates**: Single JSON templates for hosting providers and deployment strategies
- **Steps 22-24 Integration**: Seamless workflow continuation with deployment execution
- **Future SaaS Ready**: Database-compatible JSON structure for future conversion

---

## 🏗️ **ARCHITECTURAL OVERVIEW**

### **Data Model Hierarchy**
```
Project
├── Sites (multiple)
│   ├── Site 1 (e.g., main-app)
│   │   ├── Deployments (multiple environments)
│   │   │   ├── Production (hosting: hostinger, deploy: github-actions)
│   │   │   ├── Staging (hosting: digitalocean, deploy: manual)
│   │   │   └── Development (hosting: local, deploy: git-pull)
│   │   └── Repository Info
│   └── Site 2 (e.g., admin-portal)
│       ├── Deployments
│       └── Repository Info
└── Global Settings
    ├── Save Path
    ├── Project Metadata
    └── Team Information
```

### **Template System Architecture**
```
templates/
├── unified/
│   ├── hosting-template.json      # Single template for ALL hosting providers
│   ├── deployment-template.json   # Single template for ALL deployment strategies
│   └── validation-rules.json     # Unified validation for all templates
├── providers/
│   ├── hostinger/
│   │   ├── config.json           # Provider-specific overrides
│   │   ├── guide.md             # From existing hosting guides
│   │   └── validation.js        # Provider-specific validation
│   ├── digitalocean/
│   ├── aws-ec2/
│   └── cpanel/
└── deployment-strategies/
    ├── github-actions/
    │   ├── config.json           # Strategy-specific overrides
    │   ├── workflow-template.yml # GitHub Actions workflow template
    │   └── steps-guide.md        # From existing Step 22B guides
    ├── deployhq/
    ├── local-ssh/
    └── git-pull/
```

---

## 🔧 **IMPLEMENTATION PHASES**

### **Phase 1: Storage System Refactor (Priority: Critical)**

#### **Issues to Fix:**
- Auto-backup without native modal selection
- Proper change detection and overwrite warnings
- Path-based backup isolation
- Seamless next/previous navigation

#### **Implementation:**
1. **Enhanced StorageManager** with automatic backup handling
2. **Conflict Resolution** without modal prompts
3. **Path Isolation** for different save locations
4. **Change Detection** with smart diffing

### **Phase 2: Unified Template System**

#### **Single Hosting Template Structure:**
```json
{
  "templateVersion": "2.1.0",
  "providers": {
    "hostinger": {
      "displayName": "Hostinger (hPanel)",
      "category": "managed-shared",
      "icon": "🏠",
      "active": true
    },
    "digitalocean": {
      "displayName": "DigitalOcean Droplets",
      "category": "cloud-vps", 
      "icon": "🌊",
      "active": true
    },
    "aws-ec2": {
      "displayName": "AWS EC2",
      "category": "cloud-infrastructure",
      "icon": "☁️",
      "active": true
    },
    "cpanel": {
      "displayName": "Generic cPanel",
      "category": "shared-hosting",
      "icon": "🔧",
      "active": true
    }
  },
  "fields": {
    "serverHost": {
      "label": "Server IP/Host",
      "type": "text",
      "required": true,
      "providers": {
        "hostinger": { "pattern": "^\\d+\\.\\d+\\.\\d+\\.\\d+$", "placeholder": "185.185.185.185" },
        "digitalocean": { "pattern": "^\\d+\\.\\d+\\.\\d+\\.\\d+$", "placeholder": "143.244.xx.xx" },
        "aws-ec2": { "pattern": "^(\\d+\\.\\d+\\.\\d+\\.\\d+|ec2-.+\\.amazonaws\\.com)$", "placeholder": "ec2-xx-xx-xx-xx.compute-1.amazonaws.com" },
        "cpanel": { "pattern": "^.+$", "placeholder": "server.yourhost.com" }
      }
    },
    "sshUsername": {
      "label": "SSH Username", 
      "type": "text",
      "required": true,
      "providers": {
        "hostinger": { "pattern": "^u\\d+$", "placeholder": "u123456789" },
        "digitalocean": { "default": "root", "placeholder": "root" },
        "aws-ec2": { "default": "ubuntu", "placeholder": "ubuntu" },
        "cpanel": { "placeholder": "cpaneluser" }
      }
    },
    "sshPort": {
      "label": "SSH Port",
      "type": "number", 
      "required": true,
      "providers": {
        "hostinger": { "default": 65002 },
        "digitalocean": { "default": 22 },
        "aws-ec2": { "default": 22 },
        "cpanel": { "default": 22 }
      }
    },
    "documentRoot": {
      "label": "Document Root",
      "type": "text",
      "required": true,
      "providers": {
        "hostinger": { "template": "/home/{sshUsername}/domains/{domain}/public_html", "locked": true },
        "digitalocean": { "template": "/var/www/{domain}", "locked": false },
        "aws-ec2": { "template": "/var/www/html", "locked": false },
        "cpanel": { "template": "/home/{sshUsername}/public_html", "locked": true }
      }
    },
    "webServer": {
      "label": "Web Server",
      "type": "select",
      "options": [
        { "value": "apache", "label": "Apache" },
        { "value": "nginx", "label": "Nginx" },
        { "value": "litespeed", "label": "LiteSpeed" }
      ],
      "providers": {
        "hostinger": { "default": "litespeed", "options": ["apache", "litespeed"] },
        "digitalocean": { "default": "nginx", "options": ["apache", "nginx"] },
        "aws-ec2": { "default": "nginx", "options": ["apache", "nginx"] },
        "cpanel": { "default": "apache", "options": ["apache"] }
      }
    },
    "sslProvider": {
      "label": "SSL Certificate",
      "type": "select",
      "options": [
        { "value": "letsencrypt", "label": "Let's Encrypt" },
        { "value": "cloudflare", "label": "CloudFlare" },
        { "value": "custom", "label": "Custom Certificate" },
        { "value": "na", "label": "Not Applicable" }
      ],
      "providers": {
        "hostinger": { "default": "letsencrypt", "available": ["letsencrypt", "custom"] },
        "digitalocean": { "default": "letsencrypt", "available": ["letsencrypt", "cloudflare", "custom"] },
        "aws-ec2": { "default": "letsencrypt", "available": ["letsencrypt", "cloudflare", "custom"] },
        "cpanel": { "default": "letsencrypt", "available": ["letsencrypt", "custom"] }
      }
    }
  }
}
```

#### **Single Deployment Template Structure:**
```json
{
  "templateVersion": "2.1.0",
  "strategies": {
    "github-actions": {
      "displayName": "GitHub Actions CI/CD",
      "category": "automated-ci-cd",
      "icon": "🤖",
      "complexity": "medium",
      "automation": "high",
      "active": true
    },
    "deployhq": {
      "displayName": "DeployHQ Professional",
      "category": "third-party-service",
      "icon": "🚀",
      "complexity": "medium", 
      "automation": "high",
      "active": true
    },
    "local-ssh": {
      "displayName": "Local Build + SSH",
      "category": "manual-deployment",
      "icon": "💻",
      "complexity": "low",
      "automation": "manual",
      "active": true
    },
    "git-pull": {
      "displayName": "Git Pull + Manual Build",
      "category": "server-side-build",
      "icon": "📥",
      "complexity": "low",
      "automation": "partial",
      "active": true
    }
  },
  "fields": {
    "repository": {
      "label": "Repository URL",
      "type": "text",
      "required": true,
      "strategies": {
        "github-actions": { "pattern": "^https://github\\.com/.+\\.git$", "required": true },
        "deployhq": { "pattern": "^https://.+\\.git$", "required": true },
        "local-ssh": { "required": false, "note": "Optional for local builds" },
        "git-pull": { "pattern": "^https://.+\\.git$", "required": true }
      }
    },
    "branch": {
      "label": "Deployment Branch",
      "type": "text",
      "required": true,
      "strategies": {
        "github-actions": { "default": "main", "options": ["main", "master", "production", "develop"] },
        "deployhq": { "default": "main", "options": ["main", "master", "production", "develop"] },
        "local-ssh": { "default": "main", "note": "Branch to build locally" },
        "git-pull": { "default": "main", "note": "Branch to pull on server" }
      }
    },
    "buildCommand": {
      "label": "Build Command",
      "type": "text",
      "strategies": {
        "github-actions": { "default": "composer install --no-dev && npm ci && npm run build", "customizable": true },
        "deployhq": { "default": "composer install --no-dev && npm ci && npm run build", "customizable": true },
        "local-ssh": { "default": "composer install --no-dev && npm ci && npm run build", "customizable": true },
        "git-pull": { "default": "composer install --no-dev", "note": "Executed on server after git pull" }
      }
    },
    "secretsRequired": {
      "label": "Required Secrets/Environment Variables",
      "type": "array",
      "strategies": {
        "github-actions": { 
          "default": ["SSH_PRIVATE_KEY", "SSH_HOST", "SSH_USER", "SSH_PORT"],
          "additional": ["STAGING_SSH_HOST", "PRODUCTION_SSH_HOST"]
        },
        "deployhq": {
          "default": ["DEPLOYMENT_KEY"],
          "note": "Managed through DeployHQ interface"
        },
        "local-ssh": {
          "default": ["SSH_CONFIG"],
          "note": "SSH config file on local machine"
        },
        "git-pull": {
          "default": ["GIT_TOKEN"],
          "note": "For private repositories only"
        }
      }
    },
    "postDeployCommands": {
      "label": "Post-Deployment Commands",
      "type": "array",
      "strategies": {
        "github-actions": { 
          "default": [
            "php artisan migrate --force",
            "php artisan cache:clear",
            "php artisan config:cache",
            "php artisan route:cache"
          ]
        },
        "deployhq": {
          "default": [
            "php artisan migrate --force", 
            "php artisan cache:clear",
            "php artisan config:cache"
          ]
        },
        "local-ssh": {
          "default": [
            "ssh {server} 'cd {path} && php artisan migrate --force'",
            "ssh {server} 'cd {path} && php artisan cache:clear'"
          ]
        },
        "git-pull": {
          "default": [
            "php artisan migrate --force",
            "php artisan cache:clear"
          ]
        }
      }
    },
    "triggerType": {
      "label": "Deployment Trigger",
      "type": "select",
      "options": [
        { "value": "push", "label": "On Git Push" },
        { "value": "manual", "label": "Manual Trigger" },
        { "value": "scheduled", "label": "Scheduled" },
        { "value": "na", "label": "Not Applicable" }
      ],
      "strategies": {
        "github-actions": { "default": "push", "available": ["push", "manual", "scheduled"] },
        "deployhq": { "default": "push", "available": ["push", "manual", "scheduled"] },
        "local-ssh": { "default": "manual", "available": ["manual"] },
        "git-pull": { "default": "manual", "available": ["manual"] }
      }
    }
  }
}
```

### **Phase 3: Site-Centric Wizard Interface**

#### **New Step 0: Project & Sites Definition**
```html
<div class="form-step" id="step0">
    <h2>🏗️ Project & Sites Definition</h2>
    
    <!-- Project Information -->
    <div class="form-section">
        <div class="section-title">📋 Project Overview</div>
        <input type="text" id="projectName" placeholder="Project Name" />
        <textarea id="projectDescription" placeholder="Project description"></textarea>
        <input type="text" id="savePath" placeholder="Save path" />
    </div>

    <!-- Sites Management -->
    <div class="form-section">
        <div class="section-title">🌐 Sites in this Project</div>
        <div id="sitesContainer">
            <!-- Dynamic site cards will be inserted here -->
        </div>
        <button class="btn-add" onclick="addNewSite()">+ Add Site</button>
    </div>

    <!-- Site Template -->
    <template id="siteTemplate">
        <div class="site-card" data-site-id="">
            <div class="site-header">
                <input type="text" class="site-name" placeholder="Site name (e.g., main-app)" />
                <input type="text" class="site-display-name" placeholder="Display name" />
                <button class="btn-remove" onclick="removeSite(this)">🗑️</button>
            </div>
            <div class="site-details">
                <input type="text" class="site-repository" placeholder="Repository URL (optional)" />
                <textarea class="site-description" placeholder="Site description"></textarea>
            </div>
            <div class="deployments-section">
                <div class="section-subtitle">Deployment Environments</div>
                <div class="deployments-container">
                    <!-- Deployment cards will be added here -->
                </div>
                <button class="btn-add-deployment" onclick="addDeployment(this)">+ Add Environment</button>
            </div>
        </div>
    </template>
</div>
```

#### **Enhanced Steps 1-6: Site-Aware Configuration**
- Each step now operates on selected site and deployment
- Header shows current site/deployment context
- Templates dynamically adjust fields based on provider selection
- Validation rules adapt to selected hosting/deployment combination

### **Phase 4: Steps 22-24 Integration**

#### **Dynamic Guide Generation**
Based on selected deployment strategy, generate step-by-step guides:

```javascript
// Guide Generator Example
function generateDeploymentGuide(site, deployment) {
    const strategy = deployment.deploymentStrategy;
    const hosting = deployment.hostingProvider;
    
    return {
        step22: getGuideTemplate(`steps-22-24/${strategy}/step-22-${strategy}.md`),
        step23: getGuideTemplate(`steps-22-24/${strategy}/step-23-${strategy}.md`),
        step24: getGuideTemplate(`steps-22-24/${strategy}/step-24-${strategy}.md`),
        step24_1: getGuideTemplate(`step-24.1/post-deployment.md`),
        variables: {
            siteName: site.displayName,
            domain: deployment.domain,
            serverHost: deployment.config.serverHost,
            // ... all template variables
        }
    };
}
```

#### **Step Templates Integration**
```
templates/
└── steps-22-24/
    ├── github-actions/
    │   ├── step-22-github-actions.md
    │   ├── step-23-github-actions.md
    │   ├── step-24-github-actions.md
    │   └── workflow-templates/
    │       ├── laravel-deploy.yml
    │       └── multi-environment.yml
    ├── deployhq/
    ├── local-ssh/
    └── git-pull/
```

---

## 🔄 **STORAGE SYSTEM FIXES**

### **Enhanced StorageManager Class**
```javascript
class EnhancedStorageManager {
    constructor() {
        this.currentPath = '';
        this.autoBackup = true;
        this.maxBackups = 10;
    }

    // Auto-backup without modal prompts
    async saveWithAutoBackup(data, path) {
        const key = this.getStorageKey(path);
        const existing = this.loadFromStorage(key);
        
        if (existing && this.hasChanges(existing, data)) {
            // Auto-create backup
            await this.createAutoBackup(existing, path);
            
            // Show non-blocking notification
            this.showChangeNotification(path);
        }
        
        // Save new data
        this.saveToStorage(key, data);
        this.updateSaveStatus('saved', path);
    }

    // Auto-backup creation
    async createAutoBackup(data, path) {
        const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
        const backupKey = `${this.getStorageKey(path)}_backup_${timestamp}`;
        
        this.saveToStorage(backupKey, {
            ...data,
            metadata: {
                ...data.metadata,
                backupTimestamp: timestamp,
                originalPath: path
            }
        });

        // Cleanup old backups
        this.cleanupOldBackups(path);
    }

    // Smart change detection
    hasChanges(oldData, newData) {
        const oldConfig = this.normalizeConfig(oldData.configuration);
        const newConfig = this.normalizeConfig(newData.configuration);
        
        return JSON.stringify(oldConfig) !== JSON.stringify(newConfig);
    }

    // Backup discovery for specific path
    getBackupsForPath(path) {
        const baseKey = this.getStorageKey(path);
        const allKeys = Object.keys(localStorage);
        
        return allKeys
            .filter(key => key.startsWith(`${baseKey}_backup_`))
            .map(key => {
                const data = this.loadFromStorage(key);
                return {
                    key,
                    timestamp: data.metadata.backupTimestamp,
                    data
                };
            })
            .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
    }
}
```

---

## 📊 **DATABASE-READY JSON STRUCTURE**

### **SaaS Migration Ready Schema**
```json
{
  "schemaVersion": "2.1.0",
  "project": {
    "id": "uuid-v4-here",
    "name": "SocietyPal",
    "description": "Multi-site Laravel application",
    "created": "2025-08-17T10:00:00Z",
    "updated": "2025-08-17T10:00:00Z",
    "owner": "user-id-here",
    "team": ["user-id-1", "user-id-2"],
    "settings": {
      "savePath": "Admin-Local/1-Current-Project/1-secrets",
      "autoBackup": true,
      "notifications": {
        "email": "user@example.com",
        "slack": "webhook-url"
      }
    }
  },
  "sites": [
    {
      "id": "site-uuid-1",
      "name": "main-app",
      "displayName": "Main SocietyPal Application",
      "description": "Primary user-facing application",
      "repository": "https://github.com/user/societypal.git",
      "created": "2025-08-17T10:00:00Z",
      "deployments": [
        {
          "id": "deployment-uuid-1",
          "environment": "production",
          "domain": "societypal.com",
          "branch": "main",
          "hostingProvider": "hostinger",
          "deploymentStrategy": "github-actions",
          "active": true,
          "config": {
            "hosting": {
              "serverHost": "185.185.185.185",
              "sshUsername": "u123456789",
              "sshPort": 65002,
              "documentRoot": "/home/u123456789/domains/societypal.com/public_html"
            },
            "deployment": {
              "repository": "https://github.com/user/societypal.git",
              "branch": "main",
              "buildCommand": "composer install --no-dev && npm ci && npm run build",
              "triggerType": "push"
            },
            "database": {
              "type": "mysql",
              "host": "localhost",
              "port": 3306,
              "name": "u123456789_societypal",
              "username": "u123456789_user"
            }
          },
          "lastDeployment": "2025-08-17T09:30:00Z",
          "status": "active"
        }
      ]
    }
  ],
  "metadata": {
    "version": "2.1.0",
    "created": "2025-08-17T10:00:00Z",
    "generator": "Site-Centric Deployment Wizard v2.1",
    "checksum": "sha256-hash-here"
  }
}
```

This structure can easily map to database tables:
- `projects` table
- `sites` table (belongs to project)
- `deployments` table (belongs to site)
- `configurations` table (polymorphic for hosting/deployment configs)

---

## 🎯 **IMPLEMENTATION PRIORITIES**

### **Phase 1 (Immediate):**
1. ✅ Fix storage system auto-backup
2. ✅ Create unified templates structure
3. ✅ Implement site-centric data model

### **Phase 2 (Week 1):**
1. ✅ Build Step 0 interface
2. ✅ Create template engine
3. ✅ Update existing steps for site-awareness

### **Phase 3 (Week 2):**
1. ✅ Integrate Steps 22-24 guide generation
2. ✅ Create dynamic workflow templates
3. ✅ Add comprehensive testing

### **Phase 4 (Week 3):**
1. ✅ Documentation and user guides
2. ✅ SaaS preparation features
3. ✅ Performance optimization

This plan addresses all your requirements while maintaining simplicity and scalability for future SaaS conversion.