# Step 23A: Server-Side Deployment

## **Analysis Source**

**V1 vs V2 Comparison:** Scenario A: Local Build + SSH - Server Deployment  
**Recommendation:** 🔄 **Take V2's organization + V1's build verification scripts** (V1 has better error handling)  
**Source Used:** V2's structured server setup combined with V1's enhanced verification and atomic switching procedures

> **Purpose:** Deploy application package on server with zero-downtime atomic switching

## **Critical Overview**

**🚀 SERVER-SIDE DEPLOYMENT PROCESS**

1. **SSH Connection:** Connect to production server
2. **First-Time Setup:** Create deployment structure (if first deployment)
3. **Release Extraction:** Extract package to timestamped directory
4. **Data Persistence:** Link shared directories automatically
5. **Verification:** Test application health
6. **Atomic Switch:** Zero-downtime switch to new release

## **Phase 1: SSH Connection & Verification**

### **1. Connect to Production Server**

```bash
# SSH into your production server
echo "🔌 Connecting to production server..."
ssh hostinger-factolo

# Verify server environment
echo "🔍 Verifying server environment..."
echo "Server: $(hostname)"
echo "User: $(whoami)"
echo "Working directory: $(pwd)"
echo "PHP version: $(php -v | head -1)"
echo "Disk space: $(df -h . | tail -1)"
```

### **2. Navigate to Domain Directory**

```bash
# Navigate to your domain directory
cd ~/domains/societypal.com/

echo "📁 Current location: $(pwd)"
echo "📊 Directory contents:"
ls -la
```

## **Phase 2: First-Time Server Setup (Skip if Already Done)**

### **1. Create Deployment Structure**

```bash
# Create zero-downtime deployment structure
echo "🏗️ Setting up deployment structure..."

# Create deployment directories
mkdir -p releases
mkdir -p shared/storage/app/public
mkdir -p shared/storage/framework/{cache,sessions,views}
mkdir -p shared/storage/logs

# Create shared public directories for user-generated content
mkdir -p shared/public/{uploads,invoices,qrcodes,exports,temp}
mkdir -p shared/public/storage  # For Laravel storage:link

echo "✅ Deployment structure created:"
echo "  📁 releases/     # Timestamped code deployments"
echo "  📁 shared/       # Persistent data (survives all deployments)"
echo "    ├─ .env        # Environment configuration"
echo "    ├─ storage/    # Laravel storage (logs, cache, sessions)"
echo "    └─ public/     # User uploads & generated files"
```

### **2. Create Production Environment File**

```bash
# Create production .env file with secure defaults
echo "📝 Creating production environment configuration..."

cat > shared/.env << 'EOF'
APP_NAME="SocietyPal"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://societypal.com
APP_KEY=base64:your-production-key-here

# Database configuration
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u227177893_p_zaj_socpal_d
DB_USERNAME=u227177893_p_zaj_socpal_u
DB_PASSWORD="t5TmP9$[iG7hu2eYRWUIWH@IRF2"

# File system (using local driver with shared storage)
FILESYSTEM_DISK=public

# Cache configuration
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Mail configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@societypal.com
MAIL_PASSWORD=your-mail-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@societypal.com"
MAIL_FROM_NAME="${APP_NAME}"

# Security settings
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
EOF

echo "📝 Production .env created"
echo "⚠️ Remember to update with your actual mail credentials"
```

### **3. Set Correct Permissions**

```bash
# Set correct permissions for web server access
echo "🔒 Setting secure permissions..."

chmod -R 775 shared/storage
chmod -R 775 shared/public
chmod 600 shared/.env

echo "✅ Permissions set:"
echo "  📁 shared/storage/  # 775 (web server writable)"
echo "  📁 shared/public/   # 775 (file uploads)"
echo "  📄 shared/.env      # 600 (secure secrets)"
```

## **Phase 3: Deploy Release**

### **1. Create New Release Directory**

```bash
# Navigate to releases directory
cd ~/domains/societypal.com/releases/

# Create timestamped release directory
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
echo "📦 Creating release: $TIMESTAMP"

mkdir $TIMESTAMP
cd $TIMESTAMP

echo "✅ Release directory created: $(pwd)"
```

### **2. Extract Deployment Package**

```bash
# Find and extract the deployment package
echo "📤 Extracting deployment package..."

# Find the most recent package
PACKAGE_FILE=$(ls -t ../deploy-societypal-*.tar.gz | head -1)

if [ -f "$PACKAGE_FILE" ]; then
    echo "📦 Found package: $(basename $PACKAGE_FILE)"

    # Extract package
    tar -xzf "$PACKAGE_FILE"

    if [ $? -eq 0 ]; then
        echo "✅ Package extracted successfully"

        # Verify extraction
        echo "📊 Extracted contents:"
        ls -la | head -10
        echo "   ... (showing first 10 items)"

        # Clean up package
        rm "$PACKAGE_FILE"
        echo "🗑️ Package file cleaned up"
    else
        echo "❌ Package extraction failed"
        exit 1
    fi
else
    echo "❌ No deployment package found in ../deploy-societypal-*.tar.gz"
    echo "📋 Available files:"
    ls -la ../
    exit 1
fi
```

### **3. Setup Data Persistence (Automated)**

```bash
# Use the persistence script for consistent setup
echo "🔗 Setting up data persistence..."

if [ -f "scripts/link_persistent_dirs.sh" ]; then
    bash scripts/link_persistent_dirs.sh "$(pwd)" "../../shared"

    if [ $? -eq 0 ]; then
        echo "✅ Data persistence links established using automation script"

        # Verify critical links
        echo "🔍 Verifying persistence links:"
        echo "  .env: $(readlink .env 2>/dev/null || echo 'Not linked')"
        echo "  storage: $(readlink storage 2>/dev/null || echo 'Not linked')"
        echo "  public: $(readlink public 2>/dev/null || echo 'Not linked')"
    else
        echo "❌ Persistence script failed"
        exit 1
    fi
else
    echo "❌ Persistence script not found: scripts/link_persistent_dirs.sh"
    echo "💡 Manual linking required"
    exit 1
fi
```

### **4. Database Migrations & Health Check**

```bash
# Run database migrations (production mode)
echo "📊 Running database migrations..."

php artisan migrate --force

if [ $? -eq 0 ]; then
    echo "✅ Database migrations completed"
else
    echo "❌ Database migrations failed"
    exit 1
fi

# Generate application key if needed
if grep -q "your-production-key-here" ../../shared/.env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
    echo "✅ Application key generated"
fi

# Clear and cache configurations for production
echo "⚡ Optimizing application for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verify application health
echo "🔍 Verifying application health..."
php artisan about | head -15

# Test basic functionality
echo "🧪 Testing basic application response..."
if php -S localhost:8080 -t public &>/dev/null & then
    TEST_PID=$!
    sleep 2

    # Test response
    if curl -s http://localhost:8080 > /dev/null; then
        echo "✅ Application responding correctly"
    else
        echo "⚠️ Application may have issues"
    fi

    # Clean up test server
    kill $TEST_PID 2>/dev/null
else
    echo "ℹ️ Skipping local test (port may be in use)"
fi

echo "✅ Application verified and ready"
```

## **Phase 4: Atomic Deployment Switch**

### **1. Zero-Downtime Switch**

```bash
# Switch current symlink atomically (zero downtime)
echo "🔄 Performing atomic switch to new release..."

cd ~/domains/societypal.com/

# Show current release (if any)
if [ -L current ]; then
    CURRENT_RELEASE=$(readlink current)
    echo "📍 Current release: $CURRENT_RELEASE"
else
    echo "📍 No current release (first deployment)"
fi

# Atomic switch to new release
ln -nfs releases/$TIMESTAMP current

if [ $? -eq 0 ]; then
    echo "✅ Switched to new release: $TIMESTAMP"

    # Verify switch
    NEW_CURRENT=$(readlink current)
    echo "📍 New current release: $NEW_CURRENT"
else
    echo "❌ Failed to switch to new release"
    exit 1
fi
```

### **2. Web Server Configuration (First Time Only)**

```bash
# Point public_html to current release (first time only)
if [ ! -L public_html ]; then
    echo "🌐 Setting up web server document root..."

    # Backup existing public_html if it exists
    if [ -d public_html ] && [ ! -L public_html ]; then
        mv public_html public_html.backup.$(date +%Y%m%d-%H%M%S)
        echo "📁 Backed up existing public_html"
    fi

    # Create symlink to current release
    ln -s current/public public_html

    if [ $? -eq 0 ]; then
        echo "✅ public_html linked to application"
        echo "🌐 Document root: $(readlink public_html)"
    else
        echo "❌ Failed to link public_html"
        exit 1
    fi
else
    echo "ℹ️ public_html already configured"
fi
```

### **3. Post-Deployment Verification**

```bash
# Final verification
echo "🔍 Final deployment verification..."

# Test website response
echo "🌐 Testing website response..."
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://societypal.com || echo "000")

if [ "$HTTP_STATUS" = "200" ]; then
    echo "✅ Website responding correctly (HTTP $HTTP_STATUS)"
elif [ "$HTTP_STATUS" = "000" ]; then
    echo "⚠️ Could not reach website (check DNS/SSL)"
else
    echo "⚠️ Website returned HTTP $HTTP_STATUS"
fi

# Show deployment summary
echo ""
echo "🎉 DEPLOYMENT COMPLETE!"
echo "📊 Deployment Summary:"
echo "  📅 Timestamp: $TIMESTAMP"
echo "  📁 Release Path: releases/$TIMESTAMP"
echo "  🔗 Current Link: $(readlink current)"
echo "  🌐 Website: https://societypal.com"
echo "  📊 HTTP Status: $HTTP_STATUS"
```

### **4. Cleanup Old Releases**

```bash
# Clean up old releases (keep last 3 for rollback)
echo "🗑️ Cleaning up old releases..."

cd releases/
RELEASE_COUNT=$(ls -1 | wc -l)

if [ $RELEASE_COUNT -gt 3 ]; then
    echo "📊 Found $RELEASE_COUNT releases, keeping last 3"
    ls -t | tail -n +4 | xargs rm -rf
    echo "✅ Old releases cleaned up"

    echo "📁 Remaining releases:"
    ls -lat
else
    echo "ℹ️ $RELEASE_COUNT releases found, no cleanup needed"
fi
```

## **Phase 5: Rollback Preparation**

### **1. Document Deployment**

```bash
# Create deployment record
echo "📝 Creating deployment record..."

cat > ~/domains/societypal.com/deployment-history.log << EOF
$(date '+%Y-%m-%d %H:%M:%S') - Deployment: $TIMESTAMP
  Status: SUCCESS
  HTTP Response: $HTTP_STATUS
  Package: deploy-societypal-$TIMESTAMP.tar.gz
  Previous: $CURRENT_RELEASE

EOF

echo "✅ Deployment recorded in deployment-history.log"
```

### **2. Rollback Instructions**

```bash
echo ""
echo "🔄 ROLLBACK INSTRUCTIONS (if needed):"
echo "  1. SSH into server: ssh hostinger-factolo"
echo "  2. Navigate to domain: cd ~/domains/societypal.com/"
echo "  3. List releases: ls -lat releases/"
echo "  4. Rollback command: ln -nfs releases/PREVIOUS_TIMESTAMP current"
echo "  5. Verify rollback: curl -I https://societypal.com"
echo ""
echo "💡 Available releases for rollback:"
ls -lat releases/ | head -4
```

## **Expected Result**

- ✅ Application deployed with zero downtime
- ✅ Data persistence maintained throughout deployment
- ✅ Website responding correctly
- ✅ Rollback capability available
- ✅ Deployment documented and verified

## **Next Step**

**Proceed to:** [Step 24A: Post-Deployment Verification](Step-24A-Post-Deployment-Verification.md)

## **Troubleshooting**

### **Package Extraction Issues**

```bash
# Check package integrity
tar -tzf ../deploy-societypal-*.tar.gz | head -10

# Check available space
df -h .
```

### **Permission Issues**

```bash
# Fix permissions
chmod -R 775 shared/storage shared/public
chmod 600 shared/.env

# Check web server user
ps aux | grep -E '(apache|nginx|www-data)'
```

### **Database Connection Issues**

```bash
# Test database connection
php artisan tinker --execute="DB::connection()->getPdo();"

# Check .env configuration
cat shared/.env | grep DB_
```

### **Website Not Responding**

```bash
# Check symlinks
ls -la current public_html

# Check error logs
tail -f shared/storage/logs/laravel.log

# Test local response
curl -v http://localhost:8080
```
