# 01 - Project Setup and Requirements

**Foundation setup for professional Laravel deployment**

## 🎯 Purpose

Establish project foundation, gather requirements, and prepare your Laravel application for professional deployment with any strategy (DeployHQ, GitHub Actions, Local SSH, or Git Pull).

## ⚡ Quick Reference

- **Time Required**: ~30-45 minutes
- **Prerequisites**: Laravel project, Git repository
- **Output**: Project configured and deployment-ready

---

## 📋 Phase 1: Project Analysis and Requirements

### 1.1 Laravel Project Assessment

```bash
echo "🔍 Laravel Project Assessment"
echo "============================"
echo ""

# Check Laravel version
if [ -f "artisan" ]; then
    LARAVEL_VERSION=$(php artisan --version 2>/dev/null | grep -o 'Laravel Framework [0-9.]*' || echo "Unknown")
    echo "✅ Laravel Version: $LARAVEL_VERSION"
else
    echo "❌ No Laravel project found in current directory"
    echo "Please navigate to your Laravel project root directory"
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✅ PHP Version: $PHP_VERSION"

# Check Composer
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version 2>/dev/null | grep -o 'Composer version [0-9.]*' || echo "Unknown")
    echo "✅ $COMPOSER_VERSION"
else
    echo "❌ Composer not found"
    echo "Please install Composer: https://getcomposer.org/"
fi

# Check Node.js (for frontend assets)
if command -v node &> /dev/null; then
    NODE_VERSION=$(node --version)
    echo "✅ Node.js Version: $NODE_VERSION"
    
    if command -v npm &> /dev/null; then
        NPM_VERSION=$(npm --version)
        echo "✅ NPM Version: $NPM_VERSION"
    fi
else
    echo "⚠️ Node.js not found (needed for frontend assets)"
fi

# Check Git
if command -v git &> /dev/null; then
    GIT_VERSION=$(git --version)
    echo "✅ $GIT_VERSION"
    
    # Check if in Git repository
    if git rev-parse --git-dir > /dev/null 2>&1; then
        echo "✅ Git repository detected"
        CURRENT_BRANCH=$(git branch --show-current)
        echo "   Current branch: $CURRENT_BRANCH"
        
        # Check remote origin
        if git remote get-url origin > /dev/null 2>&1; then
            REMOTE_URL=$(git remote get-url origin)
            echo "   Remote origin: $REMOTE_URL"
        else
            echo "⚠️ No remote origin configured"
        fi
    else
        echo "❌ Not in a Git repository"
        echo "Please initialize Git: git init"
    fi
else
    echo "❌ Git not found"
fi

echo ""
echo "📊 Project Structure Analysis:"
echo "   App files: $(find app/ -name "*.php" | wc -l 2>/dev/null || echo "N/A")"
echo "   Routes: $(find routes/ -name "*.php" | wc -l 2>/dev/null || echo "N/A")"
echo "   Migrations: $(find database/migrations/ -name "*.php" | wc -l 2>/dev/null || echo "N/A")"
echo "   Frontend assets: $([ -f "package.json" ] && echo "Yes" || echo "No")"
```

### 1.2 Requirements Collection

Create a project requirements file:

```bash
# Create Admin-Local directory structure
mkdir -p Admin-Local/1-Current-Project/{1-secrets,3-Customization}
mkdir -p Admin-Local/server_deployment/{configs,scripts}
mkdir -p Admin-Local/monitoring

cat > Admin-Local/1-Current-Project/project-requirements.md << 'EOF'
# Laravel Deployment Project Requirements

## Project Information
- **Project Name**: [Your Laravel App Name]
- **Laravel Version**: [Version from analysis above]
- **PHP Version**: [Version from analysis above]
- **Environment**: [development/staging/production]

## Deployment Requirements

### Technical Requirements
- [ ] Laravel application fully functional locally
- [ ] Database configured and migrations working
- [ ] Frontend assets building successfully (if applicable)
- [ ] Environment variables documented
- [ ] Dependencies up to date

### Infrastructure Requirements
- [ ] Production server access (SSH)
- [ ] Domain name configured
- [ ] SSL certificate available
- [ ] Database server accessible
- [ ] Web server configured (Apache/Nginx)

### Team Requirements
- [ ] Git repository accessible to team
- [ ] Deployment strategy chosen
- [ ] Team permissions configured
- [ ] Documentation requirements defined
- [ ] Maintenance schedule planned

## Deployment Strategy Considerations

### Choose ONE deployment strategy:

#### 🏭 DeployHQ Professional
- **Cost**: $15-50/month
- **Best for**: Enterprise teams, professional deployments
- **Requires**: DeployHQ subscription, team collaboration needs
- **Complexity**: Medium
- **Automation**: High

#### 🤖 GitHub Actions
- **Cost**: Free (GitHub included)
- **Best for**: Teams using GitHub, automated workflows
- **Requires**: GitHub repository, CI/CD knowledge
- **Complexity**: Medium
- **Automation**: High

#### 💻 Local SSH
- **Cost**: Free
- **Best for**: Simple projects, full control, learning
- **Requires**: SSH access, manual process comfort
- **Complexity**: Low
- **Automation**: Manual

#### 📁 Git Pull + Manual
- **Cost**: Free
- **Best for**: Traditional hosting, cPanel/shared hosting
- **Requires**: Server Git access, basic command line
- **Complexity**: Low
- **Automation**: Partial

### Selected Strategy: _______________

### Strategy Justification:
[Explain why this strategy was chosen for this project]

## Environment Configuration

### Development Environment
- **URL**: http://localhost:8000
- **Database**: Local MySQL/SQLite
- **Debug**: Enabled
- **Cache**: Disabled

### Staging Environment (if used)
- **URL**: https://staging.yourapp.com
- **Database**: Staging database
- **Debug**: Enabled
- **Cache**: Enabled

### Production Environment
- **URL**: https://yourapp.com
- **Database**: Production database
- **Debug**: Disabled
- **Cache**: Enabled
- **SSL**: Required

## Security Requirements
- [ ] Environment variables secured
- [ ] Database credentials protected
- [ ] API keys and secrets managed
- [ ] File permissions configured
- [ ] SSL certificate installed
- [ ] Security headers configured

## Performance Requirements
- [ ] Page load time < 3 seconds
- [ ] Database queries optimized
- [ ] Caching strategy implemented
- [ ] Asset optimization configured
- [ ] CDN configured (if needed)

## Monitoring Requirements
- [ ] Error logging configured
- [ ] Performance monitoring setup
- [ ] Uptime monitoring configured
- [ ] Backup verification system
- [ ] Alert notifications configured

## Compliance Requirements
- [ ] Data protection requirements
- [ ] Industry regulations compliance
- [ ] Audit trail requirements
- [ ] Access control requirements
- [ ] Backup retention policies

EOF

echo "✅ Project requirements template created"
echo "📁 Location: Admin-Local/1-Current-Project/project-requirements.md"
echo ""
echo "📝 Next Steps:"
echo "   1. Edit the requirements file with your project details"
echo "   2. Choose your deployment strategy"
echo "   3. Complete the checklist items"
```

### 1.3 Environment Variables Documentation

```bash
# Create environment variables template
cat > Admin-Local/1-Current-Project/1-secrets/environment-variables-template.md << 'EOF'
# Environment Variables Documentation

## Development Environment (.env.local)
```env
APP_NAME="Laravel App"
APP_ENV=local
APP_KEY=base64:your-app-key-here
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_local
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## Staging Environment (.env.staging)
```env
APP_NAME="Laravel App (Staging)"
APP_ENV=staging
APP_KEY=base64:staging-app-key-here
APP_DEBUG=true
APP_URL=https://staging.yourapp.com

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=staging-db-host
DB_PORT=3306
DB_DATABASE=staging_database
DB_USERNAME=staging_user
DB_PASSWORD=staging_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=staging-redis-host
REDIS_PASSWORD=staging-redis-password
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=staging_mail_user
MAIL_PASSWORD=staging_mail_password
MAIL_ENCRYPTION=tls
```

## Production Environment (.env.production)
```env
APP_NAME="Laravel App"
APP_ENV=production
APP_KEY=base64:production-app-key-here
APP_DEBUG=false
APP_URL=https://yourapp.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=production-db-host
DB_PORT=3306
DB_DATABASE=production_database
DB_USERNAME=production_user
DB_PASSWORD=production_secure_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_SECURE_COOKIE=true

REDIS_HOST=production-redis-host
REDIS_PASSWORD=production-redis-password
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=app-specific-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourapp.com"
MAIL_FROM_NAME="${APP_NAME}"

# Additional production variables
SESSION_SAME_SITE_COOKIE=strict
SANCTUM_STATEFUL_DOMAINS=yourapp.com
```

## Environment Variables Checklist

### Required for All Environments
- [ ] APP_NAME
- [ ] APP_ENV
- [ ] APP_KEY (generated with `php artisan key:generate`)
- [ ] APP_DEBUG
- [ ] APP_URL

### Database Configuration
- [ ] DB_CONNECTION
- [ ] DB_HOST
- [ ] DB_PORT
- [ ] DB_DATABASE
- [ ] DB_USERNAME
- [ ] DB_PASSWORD

### Caching and Sessions
- [ ] CACHE_DRIVER
- [ ] SESSION_DRIVER
- [ ] QUEUE_CONNECTION

### Email Configuration
- [ ] MAIL_MAILER
- [ ] MAIL_HOST
- [ ] MAIL_PORT
- [ ] MAIL_USERNAME
- [ ] MAIL_PASSWORD
- [ ] MAIL_FROM_ADDRESS

### Security (Production Only)
- [ ] SESSION_SECURE_COOKIE=true
- [ ] SESSION_SAME_SITE_COOKIE=strict
- [ ] SANCTUM_STATEFUL_DOMAINS

## Security Notes

⚠️ **NEVER commit .env files to Git!**
⚠️ **Use different APP_KEYs for each environment**
⚠️ **Use strong passwords for production databases**
⚠️ **Enable SSL/TLS for production mail**

EOF

# Create .env.example if it doesn't exist
if [ ! -f ".env.example" ]; then
    echo "Creating .env.example template..."
    cp Admin-Local/1-Current-Project/1-secrets/environment-variables-template.md .env.example.md
fi

echo "✅ Environment variables documentation created"
echo "📁 Location: Admin-Local/1-Current-Project/1-secrets/"
```

---

## 📋 Phase 2: Project Configuration

### 2.1 Git Repository Setup

```bash
echo "📦 Git Repository Configuration"
echo "============================="
echo ""

# Ensure we're in a Git repository
if ! git rev-parse --git-dir > /dev/null 2>&1; then
    echo "🔄 Initializing Git repository..."
    git init
    echo "✅ Git repository initialized"
fi

# Check for .gitignore
if [ ! -f ".gitignore" ]; then
    echo "🔄 Creating Laravel .gitignore..."
    cat > .gitignore << 'EOF'
/node_modules
/public/build
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.env.production
.phpunit.result.cache
Homestead.json
Homestead.yaml
auth.json
npm-debug.log
yarn-error.log
/.fleet
/.idea
/.vscode
EOF
    echo "✅ .gitignore created"
fi

# Ensure Admin-Local is in .gitignore
if ! grep -q "Admin-Local" .gitignore; then
    echo "" >> .gitignore
    echo "# Admin and deployment files" >> .gitignore
    echo "Admin-Local/" >> .gitignore
    echo "✅ Admin-Local added to .gitignore"
fi

# Check for README.md
if [ ! -f "README.md" ]; then
    echo "🔄 Creating project README..."
    cat > README.md << EOF
# $(basename "$(pwd)")

Laravel application with professional deployment setup.

## Requirements

- PHP 8.1+
- Composer
- Node.js 18+ (for frontend assets)
- MySQL/PostgreSQL

## Installation

\`\`\`bash
# Clone repository
git clone $(git remote get-url origin 2>/dev/null || echo "your-repo-url")
cd $(basename "$(pwd)")

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start development server
php artisan serve
\`\`\`

## Deployment

This project uses professional deployment workflows. See the deployment documentation in \`Admin-Local/\` directory.

## Contributing

Please follow the established coding standards and deployment procedures.
EOF
    echo "✅ README.md created"
fi

# Initial commit if no commits exist
if ! git rev-parse HEAD > /dev/null 2>&1; then
    echo "🔄 Creating initial commit..."
    git add .
    git commit -m "feat: initial Laravel project setup with deployment framework

- Laravel application foundation
- Professional deployment structure
- Environment configuration templates
- Git repository properly configured"
    echo "✅ Initial commit created"
fi

echo "✅ Git repository configured"
```

### 2.2 Dependencies Verification

```bash
echo "🔍 Dependencies Verification"
echo "==========================="
echo ""

# Check Composer dependencies
echo "📦 Checking Composer dependencies..."
if composer check-platform-reqs 2>/dev/null; then
    echo "✅ All Composer platform requirements met"
else
    echo "⚠️ Some Composer platform requirements not met"
    echo "Run: composer check-platform-reqs"
fi

# Validate composer.json
if composer validate --no-check-publish 2>/dev/null; then
    echo "✅ composer.json is valid"
else
    echo "⚠️ composer.json has issues"
fi

# Check for security vulnerabilities
echo "🔒 Checking for security vulnerabilities..."
composer audit 2>/dev/null || echo "⚠️ Security audit not available or found issues"

# Check NPM dependencies (if package.json exists)
if [ -f "package.json" ]; then
    echo ""
    echo "📦 Checking NPM dependencies..."
    if npm audit --audit-level=moderate 2>/dev/null; then
        echo "✅ NPM dependencies secure"
    else
        echo "⚠️ NPM dependencies have security issues"
        echo "Run: npm audit fix"
    fi
fi

# Test Laravel application
echo ""
echo "🧪 Testing Laravel application..."
if php artisan --version > /dev/null 2>&1; then
    echo "✅ Laravel application responds"
    
    # Test database connection (if .env exists)
    if [ -f ".env" ]; then
        if php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';" 2>/dev/null | grep -q "DB OK"; then
            echo "✅ Database connection working"
        else
            echo "⚠️ Database connection issues"
        fi
    else
        echo "ℹ️ No .env file found for database testing"
    fi
else
    echo "❌ Laravel application not responding"
fi

echo "✅ Dependencies verification completed"
```

### 2.3 Project Structure Validation

```bash
echo "📁 Project Structure Validation"
echo "=============================="
echo ""

# Check required Laravel directories
REQUIRED_DIRS=("app" "bootstrap" "config" "database" "public" "resources" "routes" "storage")
for dir in "${REQUIRED_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        echo "✅ Directory exists: $dir"
    else
        echo "❌ Missing directory: $dir"
    fi
done

# Check required Laravel files
REQUIRED_FILES=("artisan" "composer.json" ".env.example")
for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ File exists: $file"
    else
        echo "❌ Missing file: $file"
    fi
done

# Check storage permissions
echo ""
echo "🔒 Checking storage permissions..."
if [ -w "storage" ] && [ -w "bootstrap/cache" ]; then
    echo "✅ Storage directories are writable"
else
    echo "⚠️ Storage directories may need permission fixes"
    echo "Run: chmod -R 775 storage bootstrap/cache"
fi

# Check for common issues
echo ""
echo "🔍 Checking for common issues..."

# Check for .env in git
if git ls-files | grep -q "^\.env$"; then
    echo "❌ .env file is tracked by Git! Remove it:"
    echo "   git rm --cached .env"
    echo "   git commit -m 'Remove .env from Git tracking'"
fi

# Check for vendor in git
if git ls-files | grep -q "^vendor/"; then
    echo "❌ vendor directory is tracked by Git! Remove it:"
    echo "   git rm -r --cached vendor"
    echo "   git commit -m 'Remove vendor from Git tracking'"
fi

# Check for node_modules in git
if git ls-files | grep -q "^node_modules/"; then
    echo "❌ node_modules directory is tracked by Git! Remove it:"
    echo "   git rm -r --cached node_modules"
    echo "   git commit -m 'Remove node_modules from Git tracking'"
fi

echo "✅ Project structure validation completed"
```

---

## 📋 Phase 3: Deployment Preparation

### 3.1 Create Deployment Configuration

```bash
# Create deployment configuration file
cat > Admin-Local/1-Current-Project/deployment-config.json << EOF
{
  "project": {
    "name": "$(basename "$(pwd)")",
    "version": "1.0.0",
    "created": "$(date -u +"%Y-%m-%dT%H:%M:%SZ")",
    "laravel_version": "$(php artisan --version 2>/dev/null | grep -o '[0-9.]*' | head -1 || echo 'unknown')",
    "php_version": "$(php -r 'echo PHP_VERSION;')"
  },
  "deployment": {
    "strategy": "",
    "environments": ["development", "staging", "production"],
    "auto_deploy": false,
    "require_approval": true
  },
  "infrastructure": {
    "server_type": "",
    "web_server": "",
    "database": "",
    "cache": "",
    "queue": ""
  },
  "features": {
    "zero_downtime": true,
    "automatic_backups": true,
    "health_checks": true,
    "monitoring": true,
    "ssl_required": true
  },
  "team": {
    "size": 1,
    "skill_level": "intermediate",
    "ci_cd_experience": false,
    "maintenance_window": "minimal"
  },
  "compliance": {
    "data_protection": false,
    "audit_required": false,
    "backup_retention_days": 30
  }
}
EOF

echo "✅ Deployment configuration template created"
echo "📁 Location: Admin-Local/1-Current-Project/deployment-config.json"
```

### 3.2 Final Setup Validation

```bash
echo "✅ Project Setup Validation Checklist"
echo "===================================="
echo ""
echo "📋 Foundation Setup:"
echo "   [ ] Laravel project analyzed and working"
echo "   [ ] PHP 8.1+ installed and configured"
echo "   [ ] Composer installed and working"
echo "   [ ] Git repository initialized and configured"
echo "   [ ] Project requirements documented"
echo ""
echo "📋 Environment Configuration:"
echo "   [ ] Environment variables documented"
echo "   [ ] .env.example created"
echo "   [ ] .gitignore properly configured"
echo "   [ ] Security considerations addressed"
echo ""
echo "📋 Deployment Preparation:"
echo "   [ ] Admin-Local structure created"
echo "   [ ] Deployment configuration template ready"
echo "   [ ] Project structure validated"
echo "   [ ] Dependencies verified"
echo ""
echo "🎯 Next Steps:"
echo "   1. Complete project requirements document"
echo "   2. Choose deployment strategy (Step 04)"
echo "   3. Configure environment variables"
echo "   4. Proceed to Step 02 - Environment Configuration"
echo ""
echo "📁 Important Files Created:"
echo "   • Admin-Local/1-Current-Project/project-requirements.md"
echo "   • Admin-Local/1-Current-Project/deployment-config.json"
echo "   • Admin-Local/1-Current-Project/1-secrets/environment-variables-template.md"
echo ""
echo "🎉 Step 01 Complete!"
echo "👉 Next: Step 02 - Environment Configuration"
```

---

## 🎯 Success Indicators

### ✅ Step 01 Complete When You Have:

- **Project Analysis**: Laravel version, PHP version, and dependencies verified
- **Requirements Documented**: Project requirements file completed
- **Git Repository**: Properly configured with appropriate .gitignore
- **Environment Templates**: Variable templates for all environments created
- **Admin Structure**: Admin-Local directory structure established
- **Validation Passed**: All project structure and permission checks passed

## 📋 Next Steps

✅ **01 Complete** - Project setup and requirements gathering finished  
🔄 **Continue to**: [02 - Environment Configuration](../02-Environment-Configuration/README.md)  
🎯 **Parallel Task**: Review and choose deployment strategy for Step 04

---

**🎉 Congratulations!**

Your Laravel project foundation is now properly set up for professional deployment!

👉 **Next Step**: [02 - Environment Configuration](../02-Environment-Configuration/README.md)
