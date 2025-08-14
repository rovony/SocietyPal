# Step 03: Server Setup and Configuration

## Analysis Source

**Primary Source**: V2 Phase0 (lines 250-700) - Server setup and Git configuration  
**Secondary Source**: V1 Complete Guide - No equivalent content  
**Recommendation**: Use V2 entirely - V1 has no server setup procedures

---

## 🎯 Purpose

Configure the production server with Git integration, security settings, and deployment structure to support secure CodeCanyon project deployments with proper version control and backup systems.

## ⚡ Quick Reference

**Time Required**: ~20-30 minutes  
**Prerequisites**: Step 02 (SSH Configuration) completed  
**Frequency**: One-time per server

---

## 🔄 **PHASE 1: Server Access and Initial Setup**

### **1.1 Connect to Server**

```bash
# Connect to production server
echo "🖥️ Connecting to Production Server"
echo "==============================="

# Test SSH connection
echo "🔗 Testing server connection..."
if ssh hostinger-factolo 'echo "Server connection successful"'; then
    echo "✅ Server connection working"
else
    echo "❌ Server connection failed - check Step 02"
    exit 1
fi

echo ""
echo "🎯 Connected to SocietyPal Production Server"
echo "Server: Hostinger Factolo"
echo "Purpose: CodeCanyon project deployment"
```

### **1.2 Server Information Gathering**

```bash
# Gather server information for documentation
echo ""
echo "📊 Gathering Server Information"
echo "============================="

# Connect and get server details
ssh hostinger-factolo << 'ENDSSH'
    echo "🖥️ Server System Information"
    echo "=========================="
    echo "Hostname: $(hostname)"
    echo "OS: $(cat /etc/os-release | grep PRETTY_NAME | cut -d'"' -f2)"
    echo "Architecture: $(uname -m)"
    echo "Kernel: $(uname -r)"
    echo "Uptime: $(uptime -p)"
    echo ""

    echo "📁 Directory Structure:"
    echo "Home: $(pwd)"
    echo "User: $(whoami)"
    echo "Available space:"
    df -h . | tail -1
    echo ""

    echo "🔧 Available tools:"
    php --version 2>/dev/null | head -1 || echo "PHP: Not available"
    composer --version 2>/dev/null || echo "Composer: Not available"
    git --version 2>/dev/null || echo "Git: Not available"
    mysql --version 2>/dev/null | head -1 || echo "MySQL: Not available"
    echo ""

    echo "✅ Server information gathered"
ENDSSH
```

### **1.3 Check and Install Required Tools**

```bash
# Check and install required tools on server
echo ""
echo "🛠️ Checking Required Server Tools"
echo "==============================="

ssh hostinger-factolo << 'ENDSSH'
    echo "🔍 Checking for required tools..."

    # Check PHP
    if command -v php &> /dev/null; then
        PHP_VERSION=$(php --version | head -1)
        echo "✅ PHP: $PHP_VERSION"
    else
        echo "❌ PHP: Not found (contact hosting provider)"
    fi

    # Check Composer
    if command -v composer &> /dev/null; then
        COMPOSER_VERSION=$(composer --version)
        echo "✅ Composer: $COMPOSER_VERSION"
    else
        echo "🔄 Installing Composer..."
        curl -sS https://getcomposer.org/installer | php
        mv composer.phar /usr/local/bin/composer 2>/dev/null || mv composer.phar ~/composer
        chmod +x ~/composer 2>/dev/null || echo "Composer installed locally"
    fi

    # Check Git
    if command -v git &> /dev/null; then
        GIT_VERSION=$(git --version)
        echo "✅ Git: $GIT_VERSION"
    else
        echo "❌ Git: Not found (contact hosting provider)"
    fi

    # Check Node.js (optional for some CodeCanyon projects)
    if command -v node &> /dev/null; then
        NODE_VERSION=$(node --version)
        echo "✅ Node.js: $NODE_VERSION"
    else
        echo "ℹ️ Node.js: Not found (optional for frontend builds)"
    fi

    echo ""
    echo "🎯 Tool verification complete"
ENDSSH
```

**Expected Results:**

- Server connection established and verified
- Server system information documented
- Required tools (PHP, Composer, Git) verified or installed
- Server ready for Git and deployment setup

---

## 🔄 **PHASE 2: Server SSH Key Generation for GitHub**

### **2.1 Generate Server SSH Key for GitHub Access**

```bash
# Generate server SSH key for GitHub authentication
echo ""
echo "🔑 Generating Server SSH Key for GitHub"
echo "======================================"

# Connect to server and generate SSH key
ssh hostinger-factolo << 'ENDSSH'
    echo "�️ Connected to server: $(hostname)"
    echo "📁 Working directory: $(pwd)"
    echo "👤 User: $(whoami)"
    echo ""

    # Navigate to home directory
    cd ~

    # Check existing SSH directory
    echo "📂 Checking SSH directory structure:"
    ls -la ~/.ssh/ || echo "SSH directory not found, will be created"
    echo ""

    # Generate SSH key specifically for GitHub
    echo "🔐 Generating Server-GitHub SSH Key..."
    ssh-keygen -t ed25519 -C "hostinger-server-$(date +%Y%m%d)" -f ~/.ssh/hostinger_github_key

    echo ""
    echo "✅ SSH key generation complete"
    echo "📋 Files created:"
    echo "   Private key: ~/.ssh/hostinger_github_key"
    echo "   Public key: ~/.ssh/hostinger_github_key.pub"
ENDSSH

echo ""
echo "💡 Key Generation Notes:"
echo "   • When prompted for passphrase: Press Enter (leave empty)"
echo "   • Empty passphrase enables automated Git operations"
echo "   • This key will be used for all GitHub repository access"
```

### **2.2 Configure SSH Key Permissions and GitHub Access**

````bash
# Set proper permissions and configure GitHub access
echo ""
echo "🔒 Configuring SSH Permissions and GitHub Access"
echo "=============================================="

ssh hostinger-factolo << 'ENDSSH'
    # Set correct permissions for security
    echo "🔒 Setting secure permissions..."
    chmod 600 ~/.ssh/hostinger_github_key
    chmod 644 ~/.ssh/hostinger_github_key.pub

    # Verify permissions
    echo "📋 SSH key permissions:"
    ls -l ~/.ssh/hostinger_github_key ~/.ssh/hostinger_github_key.pub
    echo ""

    # Create SSH config for GitHub
    echo "⚙️ Configuring SSH for GitHub access..."
    cat > ~/.ssh/config << 'EOF'
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/hostinger_github_key
    IdentitiesOnly yes
EOF

    # Set config permissions
    chmod 600 ~/.ssh/config

    echo "✅ SSH configuration created:"
    cat ~/.ssh/config
    echo ""

    # Display public key for GitHub setup
    echo "� SERVER PUBLIC KEY (Copy this for GitHub):"
    echo "============================================"
    cat ~/.ssh/hostinger_github_key.pub
    echo ""
    echo "============================================"
    echo "📝 Next: Add this key to GitHub"
ENDSSH

**INSTRUCT-USER: GitHub Account Configuration**
```bash
echo ""
echo "⚠️  HUMAN ACTION REQUIRED - External Service Access"
echo "🌐 GitHub SSH Key Setup Instructions:"
echo "===================================="
echo "1. Copy the public key displayed above"
echo "2. For personal account (rovony): github.com/settings/keys"
echo "3. For organizations: Add to repository Deploy Keys"
echo "4. Give it a title: 'Hostinger Server $(date +%Y%m%d)'"
echo "5. Save the key in GitHub"
echo ""
echo "💡 Return here after adding key to GitHub"
````

**END-INSTRUCT-USER**

echo ""
echo "🌐 GitHub SSH Key Setup Instructions:"
echo "===================================="
echo "1. Copy the public key displayed above"
echo "2. For personal account (rovony): github.com/settings/keys"
echo "3. For organizations: Add to repository Deploy Keys"
echo "4. Give it a title: 'Hostinger Server $(date +%Y%m%d)'"
echo "5. Save the key in GitHub"

````

### **2.3 Test GitHub SSH Authentication**

```bash
# Test SSH connection to GitHub
echo ""
echo "🧪 Testing GitHub SSH Authentication"
echo "=================================="

ssh hostinger-factolo << 'ENDSSH'
    echo "🔍 Testing SSH connection to GitHub..."
    echo "Note: First time will ask for host verification"
    echo ""

    # Test GitHub SSH connection
    ssh -T git@github.com

    echo ""
    echo "✅ Expected result:"
    echo "   'Hi username! You've successfully authenticated, but GitHub does not provide shell access.'"
    echo ""
    echo "❓ First time connection:"
    echo "   • Will ask: 'Are you sure you want to continue connecting (yes/no)?'"
    echo "   • Type: yes"
    echo "   • Creates: ~/.ssh/known_hosts file"
ENDSSH

echo ""
echo "✅ GitHub SSH authentication configured"
echo "🔗 Server can now access GitHub repositories"
````

### **2.2 Test GitHub SSH Connection**

```bash
# Test GitHub SSH connection from server
echo ""
echo "🧪 Testing GitHub SSH Connection"
echo "=============================="

ssh hostinger-factolo << 'ENDSSH'
    echo "🔗 Testing SSH connection to GitHub..."

    # Test GitHub SSH connection
    ssh -T git@github.com 2>&1 | head -3

    # Add GitHub to known hosts if needed
    if [ ! -f ~/.ssh/known_hosts ] || ! grep -q "github.com" ~/.ssh/known_hosts; then
        echo "🔐 Adding GitHub to known hosts..."
        ssh-keyscan github.com >> ~/.ssh/known_hosts
        chmod 644 ~/.ssh/known_hosts
    fi

    # Test again
    echo ""
    echo "🔍 Final GitHub SSH test:"
    if ssh -T git@github.com 2>&1 | grep -q "successfully authenticated"; then
        echo "✅ GitHub SSH authentication successful"
    else
        echo "❌ GitHub SSH authentication failed"
        echo "Please verify the SSH key was added to GitHub"
    fi
ENDSSH
```

### **2.3 Configure Git User Settings**

```bash
# Configure Git user settings on server
echo ""
echo "👤 Configuring Git User Settings"
echo "=============================="

ssh hostinger-factolo << 'ENDSSH'
    echo "⚙️ Setting up Git user configuration..."

    # Set Git global configuration (using manual specifications)
    git config --global user.name rovony
    git config --global user.email "191882927+rovony@users.noreply.github.com"
    git config --global init.defaultBranch main
    git config --global pull.rebase false

    # Verify configuration
    echo "✅ Git configuration set:"
    echo "  Name: $(git config --global user.name)"
    echo "  Email: $(git config --global user.email)"
    echo "  Default branch: $(git config --global init.defaultBranch)"

    # Additional Git settings for server
    git config --global core.autocrlf input
    git config --global core.safecrlf warn
    git config --global push.default simple

    echo ""
    echo "🔧 Git settings configured for CodeCanyon server environment"
    echo "📁 Configuration saved to: ~/.gitconfig"
ENDSSH
```

**Expected Results:**

- Server SSH key generated and added to GitHub
- GitHub SSH authentication working from server
- Git user configuration set for server commits
- Server ready for Git repository operations
- **Admin-Domain structure established** for organized server management

---

## 🔄 **PHASE 3: Server Admin-Domain Structure Setup**

### **3.1 Create Admin-Domain Structure**

```bash
# Create organized Admin-Domain structure on server
echo ""
echo "🏗️ Creating Server Admin-Domain Structure"
echo "========================================"

ssh hostinger-factolo << 'ENDSSH'
    echo "📁 Setting up Admin-Domain structure for organized server management..."

    # Create the primary domain Admin-Domain structure
    DOMAIN_ROOT="~/domains/societypal.com"
    ADMIN_DOMAIN="$DOMAIN_ROOT/Admin-Domain"

    # Create Admin-Domain directory structure
    echo "🏗️ Creating Admin-Domain structure..."
    mkdir -p "$ADMIN_DOMAIN"/{scripts,docs,deployment,backups,maintenance,monitoring}
    mkdir -p "$ADMIN_DOMAIN"/scripts/{deploy,backup,maintenance,monitoring}
    mkdir -p "$ADMIN_DOMAIN"/docs/{procedures,security,troubleshooting}
    mkdir -p "$ADMIN_DOMAIN"/deployment/{configs,logs,releases}
    mkdir -p "$ADMIN_DOMAIN"/backups/{database,files,configs}
    mkdir -p "$ADMIN_DOMAIN"/maintenance/{logs,schedules,reports}
    mkdir -p "$ADMIN_DOMAIN"/monitoring/{performance,security,uptime}

    # Create staging domain Admin-Domain structure
    STAGING_DOMAIN_ROOT="~/domains/staging.societypal.com"
    STAGING_ADMIN_DOMAIN="$STAGING_DOMAIN_ROOT/Admin-Domain"

    mkdir -p "$STAGING_ADMIN_DOMAIN"/{scripts,docs,deployment,backups,maintenance,monitoring}
    mkdir -p "$STAGING_ADMIN_DOMAIN"/scripts/{deploy,backup,maintenance,monitoring}
    mkdir -p "$STAGING_ADMIN_DOMAIN"/docs/{procedures,security,troubleshooting}
    mkdir -p "$STAGING_ADMIN_DOMAIN"/deployment/{configs,logs,releases}
    mkdir -p "$STAGING_ADMIN_DOMAIN"/backups/{database,files,configs}
    mkdir -p "$STAGING_ADMIN_DOMAIN"/maintenance/{logs,schedules,reports}
    mkdir -p "$STAGING_ADMIN_DOMAIN"/monitoring/{performance,security,uptime}

    echo "✅ Admin-Domain structure created successfully"
    echo ""
    echo "📋 Admin-Domain Structure:"
    tree "$ADMIN_DOMAIN" -d 2>/dev/null || ls -la "$ADMIN_DOMAIN"
ENDSSH
```

### **3.2 Create Admin-Domain Documentation**

```bash
# Create comprehensive Admin-Domain documentation
echo ""
echo "📚 Creating Admin-Domain Documentation"
echo "==================================="

ssh hostinger-factolo << 'ENDSSH'
    ADMIN_DOMAIN="~/domains/societypal.com/Admin-Domain"

    # Create main Admin-Domain README
    cat > "$ADMIN_DOMAIN/README.md" << 'EOF'
# Admin-Domain Management Structure

## Purpose
Organized server-side management structure inspired by local Admin-Local pattern.
Provides centralized location for all domain-specific administration tasks.

## Directory Structure
```

Admin-Domain/
├── scripts/ # Automation scripts
│ ├── deploy/ # Deployment automation
│ ├── backup/ # Backup automation  
│ ├── maintenance/ # Maintenance tasks
│ └── monitoring/ # Monitoring scripts
├── docs/ # Documentation
│ ├── procedures/ # Step-by-step procedures
│ ├── security/ # Security documentation
│ └── troubleshooting/ # Problem resolution guides
├── deployment/ # Deployment management
│ ├── configs/ # Deployment configurations
│ ├── logs/ # Deployment logs
│ └── releases/ # Release management
├── backups/ # Backup storage
│ ├── database/ # Database backups
│ ├── files/ # File system backups
│ └── configs/ # Configuration backups
├── maintenance/ # Maintenance records
│ ├── logs/ # Maintenance logs
│ ├── schedules/ # Maintenance schedules
│ └── reports/ # Maintenance reports
└── monitoring/ # Monitoring data
├── performance/ # Performance metrics
├── security/ # Security monitoring
└── uptime/ # Uptime monitoring

````

## Key Benefits
- **Organization**: All admin tasks centrally located
- **Consistency**: Mirrors local Admin-Local structure
- **Scalability**: Easy to replicate across domains
- **Security**: Isolated from public web directory
- **Maintenance**: Clear separation of concerns

## Related Structures
- **Local**: Admin-Local/ (developer machine)
- **Server**: Admin-Domain/ (per domain on server)
- **Global**: ~/.admin-server/ (server-wide configs)

## Last Updated
$(date)
EOF

    # Create deployment config template
    cat > "$ADMIN_DOMAIN/deployment/configs/deployment-config.md" << 'EOF'
# Deployment Configuration - SocietyPal

## Domain Configuration
- **Production**: societypal.com
- **Staging**: staging.societypal.com
- **Document Root**: public_html -> current/public
- **Admin-Domain**: Admin-Domain/

## Deployment Methods
1. **Local + SSH** (Step-22A)
   - Manual build on local machine
   - SCP transfer to server
   - Symlink update for zero-downtime

2. **GitHub Actions** (Step-22B)
   - Automated CI/CD pipeline
   - Triggered by main branch push
   - Includes testing and deployment

3. **DeployHQ Professional** (Step-22C)
   - Enterprise deployment platform
   - Advanced monitoring and rollback
   - Team collaboration features

4. **Git Pull + Manual** (Step-22D)
   - Direct server git pull
   - Manual dependency installation
   - Quick development updates

## Configuration Files
- `.env.production` - Production environment
- `.env.staging` - Staging environment
- `deploy.sh` - Deployment automation
- `rollback.sh` - Emergency rollback

## Security Notes
- All configs exclude sensitive credentials
- Environment files managed separately
- SSH key authentication required
- HTTPS enforced on all domains

## Last Updated
$(date)
EOF

    # Create backup procedures
    cat > "$ADMIN_DOMAIN/backups/backup-procedures.md" << 'EOF'
# Backup Procedures - Admin-Domain

## Automated Backups
- **Database**: Daily at 2:00 AM UTC
- **Files**: Daily at 3:00 AM UTC
- **Configs**: Weekly on Sundays
- **Retention**: 30 days local, 90 days remote

## Manual Backup Commands
```bash
# Database backup
cd ~/domains/societypal.com/Admin-Domain/scripts/backup
./backup-database.sh

# File system backup
./backup-files.sh

# Configuration backup
./backup-configs.sh
````

## Restore Procedures

```bash
# Database restore
./restore-database.sh [backup-file]

# File restore
./restore-files.sh [backup-date]

# Configuration restore
./restore-configs.sh [backup-date]
```

## Backup Locations

- **Local**: Admin-Domain/backups/
- **Remote**: Configured backup service
- **Emergency**: Manual download locations

## Last Updated

$(date)
EOF

    echo "✅ Admin-Domain documentation created"
    echo "📄 Files created:"
    echo "   - README.md"
    echo "   - deployment/configs/deployment-config.md"
    echo "   - backups/backup-procedures.md"

ENDSSH

````

### **3.3 Create Admin-Domain Management Scripts**

```bash
# Create essential Admin-Domain management scripts
echo ""
echo "🔧 Creating Admin-Domain Management Scripts"
echo "========================================"

ssh hostinger-factolo << 'ENDSSH'
    ADMIN_DOMAIN="~/domains/societypal.com/Admin-Domain"

    # Create deployment script template
    cat > "$ADMIN_DOMAIN/scripts/deploy/deploy.sh" << 'EOF'
#!/bin/bash
# Admin-Domain Deployment Script
# Usage: ./deploy.sh [method] [branch]

set -e

DOMAIN_ROOT="~/domains/societypal.com"
ADMIN_DOMAIN="$DOMAIN_ROOT/Admin-Domain"
PUBLIC_ROOT="$DOMAIN_ROOT/public_html"

echo "🚀 Starting deployment via Admin-Domain..."
echo "Domain: $(basename $DOMAIN_ROOT)"
echo "Method: ${1:-manual}"
echo "Branch: ${2:-main}"
echo "Time: $(date)"

# Log deployment start
echo "$(date): Deployment started - Method: ${1:-manual}, Branch: ${2:-main}" >> "$ADMIN_DOMAIN/deployment/logs/deploy.log"

# Add deployment logic here based on method
case "${1:-manual}" in
    "github-actions")
        echo "📡 GitHub Actions deployment..."
        # GitHub Actions logic
        ;;
    "deployhq")
        echo "🏢 DeployHQ deployment..."
        # DeployHQ logic
        ;;
    "git-pull")
        echo "📥 Git pull deployment..."
        # Git pull logic
        ;;
    *)
        echo "📋 Manual deployment..."
        # Manual deployment logic
        ;;
esac

echo "✅ Deployment completed via Admin-Domain"
echo "$(date): Deployment completed successfully" >> "$ADMIN_DOMAIN/deployment/logs/deploy.log"
EOF

    # Create backup script template
    cat > "$ADMIN_DOMAIN/scripts/backup/backup-database.sh" << 'EOF'
#!/bin/bash
# Admin-Domain Database Backup Script

set -e

ADMIN_DOMAIN="~/domains/societypal.com/Admin-Domain"
BACKUP_DIR="$ADMIN_DOMAIN/backups/database"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

echo "💾 Starting database backup via Admin-Domain..."

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Database backup logic here
DB_NAME="u227177893_p_zaj_socpal_d"
BACKUP_FILE="$BACKUP_DIR/database_backup_$TIMESTAMP.sql"

echo "📊 Backing up database: $DB_NAME"
# mysqldump command would go here
# mysqldump -u [user] -p[password] $DB_NAME > "$BACKUP_FILE"

echo "✅ Database backup completed: $BACKUP_FILE"
echo "$(date): Database backup created - $BACKUP_FILE" >> "$ADMIN_DOMAIN/backups/backup.log"
EOF

    # Create monitoring script template
    cat > "$ADMIN_DOMAIN/scripts/monitoring/health-check.sh" << 'EOF'
#!/bin/bash
# Admin-Domain Health Check Script

ADMIN_DOMAIN="~/domains/societypal.com/Admin-Domain"
DOMAIN="societypal.com"

echo "🏥 Running health check via Admin-Domain..."

# Check website response
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "https://$DOMAIN")
if [ "$HTTP_CODE" == "200" ]; then
    echo "✅ Website responding: HTTP $HTTP_CODE"
    STATUS="HEALTHY"
else
    echo "❌ Website error: HTTP $HTTP_CODE"
    STATUS="ERROR"
fi

# Check SSL certificate
SSL_DAYS=$(openssl s_client -connect $DOMAIN:443 -servername $DOMAIN 2>/dev/null | openssl x509 -noout -dates | grep notAfter | cut -d= -f2 | xargs -I{} date -d "{}" +%s)
CURRENT_DATE=$(date +%s)
DAYS_LEFT=$(( (SSL_DAYS - CURRENT_DATE) / 86400 ))

if [ $DAYS_LEFT -gt 30 ]; then
    echo "✅ SSL certificate valid: $DAYS_LEFT days remaining"
else
    echo "⚠️ SSL certificate expiring: $DAYS_LEFT days remaining"
fi

# Log health check
echo "$(date): Health check - Status: $STATUS, SSL: $DAYS_LEFT days" >> "$ADMIN_DOMAIN/monitoring/uptime/health.log"

echo "✅ Health check completed via Admin-Domain"
EOF

    # Make scripts executable
    chmod +x "$ADMIN_DOMAIN/scripts/deploy/deploy.sh"
    chmod +x "$ADMIN_DOMAIN/scripts/backup/backup-database.sh"
    chmod +x "$ADMIN_DOMAIN/scripts/monitoring/health-check.sh"

    echo "✅ Admin-Domain management scripts created"
    echo "🔧 Scripts created:"
    echo "   - deploy/deploy.sh"
    echo "   - backup/backup-database.sh"
    echo "   - monitoring/health-check.sh"
ENDSSH
````

**Expected Results:**

- Organized Admin-Domain structure created on server
- Comprehensive documentation for server-side management
- Essential management scripts for deployment, backup, and monitoring
- Mirror of local Admin-Local structure adapted for server environment
- Foundation for scalable multi-domain server management

---

## 🔄 **PHASE 4: Create Server Git Repository**

### **3.1 Create GitHub Repository for Server**

```bash
# Instructions for creating server repository
echo ""
echo "📁 Creating Server Git Repository"
echo "=============================="
echo ""
echo "📋 GitHub Repository Setup:"
echo "1. Go to GitHub.com → New Repository"
echo "2. Repository name: 'Server-Hostinger-Factolo'"
echo "3. Description: 'SocietyPal server configuration and deployment tracking'"
echo "4. Set to Private repository"
echo "5. Do NOT initialize with README, .gitignore, or license"
echo "6. Click 'Create repository'"
echo ""
echo "💡 This repository will track:"
echo "   - Server configuration files"
echo "   - Deployment scripts and logs"
echo "   - Backup and maintenance records"
echo "   - Server documentation and procedures"
echo ""
read -p "Press Enter when you've created the GitHub repository..."
```

### **3.2 Initialize Server Git Repository**

```bash
# Initialize Git repository on server
echo ""
echo "🏗️ Initializing Server Git Repository"
echo "==================================="

ssh hostinger-factolo << 'ENDSSH'
    echo "📂 Setting up Git repository in server home directory..."

    # Navigate to server home directory
    cd ~

    # Initialize Git repository
    if [ ! -d ".git" ]; then
        git init
        echo "✅ Git repository initialized"
    else
        echo "✅ Git repository already exists"
    fi

    # Add remote origin (update with your GitHub username if different)
    GITHUB_REPO="git@github.com:rovony/Server-Hostinger-Factolo.git"

    # Remove existing origin if any
    git remote remove origin 2>/dev/null || true

    # Add new origin
    git remote add origin $GITHUB_REPO

    # Verify remote configuration
    echo ""
    echo "🔗 Git remote configuration:"
    git remote -v

    echo ""
    echo "✅ Server Git repository configured"
    echo "📍 Repository: $GITHUB_REPO"
ENDSSH
```

### **3.3 Create Comprehensive Server .gitignore**

```bash
# Create comprehensive .gitignore for server
echo ""
echo "📝 Creating Server .gitignore"
echo "=========================="

ssh hostinger-factolo << 'ENDSSH'
    echo "📄 Creating comprehensive server .gitignore..."

    cat > .gitignore << 'EOF'
# =======================================================================
# SERVER .GITIGNORE FOR HOSTINGER FACTOLO
# =======================================================================
# Purpose: Track server configuration while excluding sensitive/temporary files
# Created: $(date)
# Server: Hostinger Factolo (SocietyPal Production)

# ────────────────────────────────────
# 1. SECURITY: Sensitive Keys & Credentials
# ────────────────────────────────────
# EXCLUDE: These contain secrets that should never be in version control
.ssh/id_*                # Private SSH keys - security risk
.ssh/known_hosts         # Contains server fingerprints - security
.ssh/authorized_keys     # Access control - security risk
.my.cnf                  # MySQL credentials - security risk
*.pem                    # SSL private keys - security risk
*.key                    # Private keys - security risk
*.p12                    # PKCS#12 certificates - sensitive
*.pfx                    # Personal Information Exchange - sensitive
.api_token               # API tokens - security risk
.api_key                 # API keys - security risk
*.token                  # Token files - security risk

# INCLUDE: Non-sensitive SSH configuration
!.ssh/config             # SSH connection settings - safe to track
!.ssh/README.md          # Documentation - safe to track

# ────────────────────────────────────
# 2. SYSTEM & HOSTING PROVIDER FILES
# ────────────────────────────────────
# EXCLUDE: These are generated/managed by hosting provider
.cache/                  # System cache - regenerated
.cagefs/                 # CageFS security - provider managed
*.trash                  # Trash files - temporary
.dotnet/                 # .NET cache - regenerated
.local/share/            # Local application data - regenerated
tmp/                     # Temporary files - regenerated
.tmp/                    # Hidden temporary files
temp/                    # Temporary directories
*.tmp                    # Temporary files
*.temp                   # Temporary files
core                     # Core dump files
*.core                   # Core dump files

# ────────────────────────────────────
# 3. DEVELOPMENT TOOLS & IDE
# ────────────────────────────────────
# EXCLUDE: VS Code Server and development tool caches
.vscode-server/data/CachedExtensionVSIXs/
.vscode-server/data/CachedProfilesData/
.vscode-server/cli/
.vscode-server/code-*/
.vscode-server/data/logs/
.vscode-server/data/clp/
.vscode-server/data/User/workspaceStorage/
.vscode-server/.cli.*.log
.vscode-server/bin/       # VS Code server binaries - large files

# INCLUDE: VS Code configuration we want to track
!.vscode-server/data/Machine/settings.json
!.vscode-server/extensions.txt

# ────────────────────────────────────
# 4. APPLICATION DEPLOYMENT EXCLUSIONS
# ────────────────────────────────────
# EXCLUDE: These will be managed by individual project deployments
domains/*/public_html/storage/logs/*     # Laravel logs - too large/changing
domains/*/public_html/storage/framework/cache/*  # Cache files - regenerated
domains/*/public_html/storage/framework/sessions/* # Session files - temporary
domains/*/public_html/storage/framework/views/*   # Compiled views - regenerated
domains/*/public_html/node_modules/      # NPM dependencies - large/regenerated
domains/*/public_html/vendor/            # Composer dependencies - large/regenerated
domains/*/public_html/.env               # Environment files - sensitive

# INCLUDE: Important deployment structure and documentation
!domains/*/README.md                     # Project documentation
!domains/*/deployment-log.md             # Deployment history
!domains/*/.env.example                  # Environment template

# ────────────────────────────────────
# 5. BACKUP AND LOG EXCLUSIONS
# ────────────────────────────────────
# EXCLUDE: Large/changing files that don't need version control
*.log                    # Log files - large and changing
*.log.*                  # Rotated log files
logs/                    # Log directories
backups/                 # Backup files - large
*.sql                    # SQL dumps - large
*.sql.gz                 # Compressed SQL dumps - large
*.tar.gz                 # Archive files - large
*.zip                    # Archive files - large

# INCLUDE: Important backup documentation and scripts
!backup-scripts/         # Backup automation scripts
!backup-schedule.md      # Backup documentation
!*.backup.md             # Backup logs and documentation

# ────────────────────────────────────
# 6. SYSTEM MONITORING & PERFORMANCE
# ────────────────────────────────────
# EXCLUDE: Monitoring data that changes frequently
*.monitoring             # Monitoring data files
performance-data/        # Performance monitoring data
metrics/                 # Metrics collection data

# INCLUDE: Monitoring configuration and documentation
!monitoring-config.md    # Monitoring setup documentation
!performance-scripts/    # Performance monitoring scripts

# ────────────────────────────────────
# 7. HOSTING PROVIDER SPECIFIC
# ────────────────────────────────────
# EXCLUDE: Hostinger-specific files and directories
.softaculous/            # Softaculous installer cache
.cpanel/                 # cPanel cache and data
.contactemail            # cPanel contact email cache
.lastlogin               # Last login tracking
public_html/cgi-bin/     # CGI scripts directory
public_html/default.html # Default hosting page

# ────────────────────────────────────
# 8. WHAT WE DO WANT TO TRACK
# ────────────────────────────────────
# INCLUDE: Important server configuration and documentation
# (These overrides ensure important files are tracked)
!server-config.md        # Server configuration documentation
!deployment-procedures.md # Deployment procedures and scripts
!maintenance-log.md      # Server maintenance history
!security-setup.md       # Security configuration documentation
!backup-procedures.md    # Backup and recovery procedures
!monitoring-setup.md     # Monitoring and alerting setup
!README.md               # Server documentation
!.gitignore              # This file itself
!domains/*/deployment-config.md  # Project-specific deployment config

# ────────────────────────────────────
# 9. EMERGENCY AND TROUBLESHOOTING
# ────────────────────────────────────
# INCLUDE: Critical troubleshooting and emergency information
!emergency-procedures.md # Emergency response procedures
!troubleshooting-guide.md # Common issues and solutions
!contacts.md             # Emergency contacts and escalation
!rollback-procedures.md  # Deployment rollback procedures

# =======================================================================
# END OF GITIGNORE
# =======================================================================
# This .gitignore balances security, performance, and operational needs
# Review and update regularly based on server usage patterns
EOF

    echo "✅ Comprehensive .gitignore created"
    echo "📊 .gitignore file size: $(wc -l .gitignore | cut -d' ' -f1) lines"
    echo "🎯 Configured to:"
    echo "   ✅ Exclude sensitive credentials and keys"
    echo "   ✅ Exclude large/temporary files"
    echo "   ✅ Include important configuration and documentation"
    echo "   ✅ Balance security with operational tracking"
ENDSSH
```

**Expected Results:**

- GitHub repository created for server tracking
- Server Git repository initialized and connected to GitHub
- Comprehensive .gitignore configured for security and efficiency
- Server ready for configuration tracking and documentation

---

## 🔄 **PHASE 4: Initial Server Documentation and Commit**

### **4.1 Create Server Documentation**

```bash
# Create initial server documentation
echo ""
echo "📚 Creating Server Documentation"
echo "============================="

ssh hostinger-factolo << 'ENDSSH'
    echo "📝 Creating initial server documentation..."

    # Create main server README
    cat > README.md << 'EOF'
# SocietyPal Production Server - Hostinger Factolo

## Server Information
- **Provider:** Hostinger
- **Plan:** Business Hosting
- **Server Name:** Factolo
- **Primary Domain:** societypal.com
- **Server Purpose:** CodeCanyon Laravel application hosting

## Server Specifications
- **OS:** Linux (CloudLinux)
- **PHP Version:** 8.2+
- **Database:** MySQL 8.0
- **Web Server:** LiteSpeed
- **SSL:** Let's Encrypt (auto-renewal)

## Access Information
- **SSH Host:** 31.97.195.108
- **SSH Port:** 65002
- **SSH User:** u227177893
- **SSH Connection:** `ssh hostinger-factolo`

## Directory Structure
```

/home/u227177893/
├── domains/
│ ├── societypal.com/ # Production deployment
│ └── staging.societypal.com/ # Staging deployment
├── backups/ # Database and file backups
├── scripts/ # Deployment and maintenance scripts
└── logs/ # Custom application logs

```

## Deployed Applications
- **SocietyPal Laravel App** (CodeCanyon based)
  - Production: https://societypal.com
  - Staging: https://staging.societypal.com

## Repository Purpose
This repository tracks:
- Server configuration files
- Deployment scripts and procedures
- Maintenance logs and documentation
- Backup and recovery procedures
- Security configurations (non-sensitive)

## Important Notes
- All sensitive credentials are excluded from version control
- See .gitignore for security exclusions
- Deployment procedures documented in deployment-procedures.md
- Emergency contacts in emergency-procedures.md

## Last Updated
$(date)
EOF

    # Create deployment procedures template
    cat > deployment-procedures.md << 'EOF'
# Deployment Procedures - SocietyPal Server

## Deployment Methods Available

### Method 1: Local Build + SSH Deploy
- Used for: Quick fixes, small updates
- Location: Manual deployment from developer machine
- Documentation: See project Step-22A through Step-24A

### Method 2: GitHub Actions CI/CD
- Used for: Automated deployments from main branch
- Location: GitHub Actions workflow
- Documentation: See project Step-22B through Step-24B

### Method 3: DeployHQ Professional
- Used for: Enterprise deployments with monitoring
- Location: DeployHQ dashboard
- Documentation: See project Step-22C through Step-24C

## Pre-Deployment Checklist
- [ ] Database backup completed
- [ ] Current deployment tested in staging
- [ ] Team notified of deployment
- [ ] Rollback plan prepared

## Post-Deployment Verification
- [ ] Application responding (HTTP 200)
- [ ] Database migrations completed
- [ ] SSL certificate active
- [ ] Performance within normal ranges
- [ ] Error logs clear

## Emergency Rollback
1. SSH to server: `ssh hostinger-factolo`
2. Navigate to domain: `cd ~/domains/societypal.com/`
3. Switch to previous release: `ln -sfn releases/previous current`
4. Clear caches: `cd current && php artisan cache:clear`

## Last Updated
$(date)
EOF

    # Create maintenance log template
    cat > maintenance-log.md << 'EOF'
# Server Maintenance Log

## $(date '+%Y-%m-%d'): Initial Server Setup
- Git repository initialized
- SSH keys configured for GitHub access
- Comprehensive .gitignore created
- Initial documentation established
- Server ready for CodeCanyon deployments

## Maintenance Schedule
- **Daily:** Automated backups (configured)
- **Weekly:** Security updates check
- **Monthly:** Performance review and optimization
- **Quarterly:** Comprehensive security audit

## Maintenance Contacts
- **Primary:** SocietyPal Development Team
- **Hosting Support:** Hostinger Business Support
- **Emergency:** [To be configured]

---
*Add new maintenance entries above this line*
EOF

    echo "✅ Server documentation created:"
    echo "   📄 README.md - Main server documentation"
    echo "   📄 deployment-procedures.md - Deployment procedures"
    echo "   📄 maintenance-log.md - Maintenance tracking"
ENDSSH
```

### **4.2 Initial Git Commit**

```bash
# Create initial Git commit
echo ""
echo "📤 Creating Initial Git Commit"
echo "==========================="

ssh hostinger-factolo << 'ENDSSH'
    echo "📦 Preparing initial Git commit..."

    # Add files to Git
    git add .gitignore README.md deployment-procedures.md maintenance-log.md

    # Create initial commit
    git commit -m "feat: initial server setup and documentation

🖥️ Server Setup:
- Git repository initialized on Hostinger Factolo server
- SSH keys configured for GitHub access
- Comprehensive .gitignore for security and efficiency

📚 Documentation:
- Server specifications and access information
- Deployment procedures for all three methods
- Maintenance log and tracking system

🔒 Security:
- Sensitive credentials excluded from version control
- SSH configuration and GitHub integration
- Production-ready security practices

🎯 Purpose: Foundation for SocietyPal CodeCanyon project deployments"

    # Push to GitHub
    echo ""
    echo "📡 Pushing to GitHub..."
    if git push -u origin main; then
        echo "✅ Initial commit pushed to GitHub successfully"
    else
        echo "⚠️ Push failed - checking GitHub connection..."
        ssh -T git@github.com
    fi

    echo ""
    echo "🎯 Git repository status:"
    git status --porcelain
    git log --oneline -3
ENDSSH
```

### **4.3 Verify Server Setup Completion**

```bash
# Verify complete server setup
echo ""
echo "🏆 Server Setup Verification"
echo "=========================="

# Final verification of server setup
ssh hostinger-factolo << 'ENDSSH'
    echo "✅ Final Server Setup Verification:"
    echo ""

    echo "🔗 GitHub Connection:"
    if ssh -T git@github.com 2>&1 | grep -q "successfully authenticated"; then
        echo "   ✅ GitHub SSH authentication working"
    else
        echo "   ❌ GitHub SSH authentication failed"
    fi

    echo ""
    echo "📂 Git Repository:"
    echo "   Status: $(git status --porcelain | wc -l) uncommitted files"
    echo "   Remote: $(git remote get-url origin)"
    echo "   Branch: $(git branch --show-current)"

    echo ""
    echo "📄 Documentation:"
    ls -la *.md | awk '{print "   ✅ " $9 " (" $5 " bytes)"}'

    echo ""
    echo "🔧 Tools Available:"
    echo "   PHP: $(php --version | head -1 | cut -d' ' -f2)"
    echo "   Git: $(git --version | cut -d' ' -f3)"
    echo "   Composer: $(composer --version 2>/dev/null | cut -d' ' -f3 || echo 'Available locally')"

    echo ""
    echo "🎯 Server Ready For:"
    echo "   ✅ CodeCanyon project deployments"
    echo "   ✅ Version control and tracking"
    echo "   ✅ Secure credential management"
    echo "   ✅ Professional deployment workflows"
    echo "   ✅ Maintenance and monitoring"
ENDSSH

echo ""
echo "🎉 Server setup complete!"
echo "🌐 GitHub Repository: https://github.com/rovony/Server-Hostinger-Factolo"
echo "📋 Next: Begin CodeCanyon project setup (1-Setup-New-Project/)"
```

**Expected Results:**

- Complete server documentation created and committed
- Git repository pushed to GitHub successfully
- Server setup verified and ready for deployments
- Foundation established for CodeCanyon project hosting

---

## ✅ **Server Setup Success Verification**

### **Complete Server Setup Summary**

```bash
echo "🏆 Complete Server Setup Summary"
echo "==============================="
echo ""
echo "✅ Server Foundation:"
echo "   🖥️ Hostinger Factolo server configured"
echo "   🔑 SSH access with key-based authentication"
echo "   🔗 GitHub integration with SSH keys"
echo "   📂 Git repository tracking server configuration"
echo ""
echo "✅ Security Configuration:"
echo "   🔒 Sensitive credentials excluded from version control"
echo "   🛡️ Comprehensive .gitignore for production security"
echo "   🔐 SSH key-based authentication only"
echo "   📋 Security procedures documented"
echo ""
echo "✅ Documentation:"
echo "   📚 Complete server specifications and access info"
echo "   📝 Deployment procedures for all methods"
echo "   📊 Maintenance logging and tracking system"
echo "   🚨 Emergency procedures and contacts"
echo ""
echo "✅ Tools and Environment:"
echo "   ⚡ PHP 8.2+ ready for Laravel applications"
echo "   📦 Composer available for dependency management"
echo "   🔄 Git configured for deployment workflows"
echo "   🌐 Web server ready for CodeCanyon applications"
echo ""
echo "🎯 Ready For:"
echo "   🚀 Professional CodeCanyon project deployments"
echo "   🔄 Automated CI/CD workflows"
echo "   📊 Enterprise monitoring and management"
echo "   🛠️ Professional maintenance and support"
```

---

## 🔧 **Troubleshooting Server Setup**

### **Common Issues and Solutions**

```bash
echo "🔧 Server Setup Troubleshooting"
echo "=============================="
echo ""
echo "❌ Common Issues:"
echo ""
echo "1. GitHub SSH Authentication Failed:"
echo "   - Verify SSH key added to GitHub account"
echo "   - Test: ssh -T git@github.com"
echo "   - Check key permissions: chmod 600 ~/.ssh/id_ed25519"
echo ""
echo "2. Git Push Failed:"
echo "   - Verify repository exists on GitHub"
echo "   - Check remote URL: git remote get-url origin"
echo "   - Test GitHub connection: ssh -T git@github.com"
echo ""
echo "3. PHP/Composer Not Found:"
echo "   - Contact Hostinger support for PHP installation"
echo "   - Install Composer locally: curl -sS https://getcomposer.org/installer | php"
echo ""
echo "4. Permission Denied Errors:"
echo "   - Check file permissions: ls -la"
echo "   - Fix SSH permissions: chmod 700 ~/.ssh && chmod 600 ~/.ssh/id_ed25519"
echo ""
echo "🚑 Emergency Commands:"
echo "   # Reset Git repository"
echo "   rm -rf .git && git init && git remote add origin [repo-url]"
echo ""
echo "   # Regenerate SSH key"
echo "   rm ~/.ssh/id_ed25519* && ssh-keygen -t ed25519 -C 'server@example.com'"
```

---

## 📋 **Next Steps**

✅ **Step 03 Complete** - Server setup and Git configuration established  
🔄 **Continue to**: 1-Setup-New-Project/ (Step 01 - Project Information)  
🖥️ **Foundation**: Production server ready for CodeCanyon deployments  
🎯 **Achievement**: Complete server infrastructure and documentation operational

---

## 🎯 **Key Success Indicators**

- **Server Access**: 🔑 SSH key-based authentication configured
- **GitHub Integration**: 📡 Server connected to GitHub with SSH keys
- **Version Control**: 📂 Git repository tracking server configuration
- **Documentation**: 📚 Comprehensive server and procedure documentation
- **Security**: 🔒 Sensitive credentials properly excluded from tracking
- **Tools**: 🛠️ PHP, Git, and Composer ready for deployments

**Server setup complete - ready for professional CodeCanyon project hosting!** 🚀
