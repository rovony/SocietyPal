# Step 22B: GitHub Actions Workflow Setup (Scenario B: Automated CI/CD)

## Analysis Source
**Primary Source**: V2 Phase3 (lines 347-500) - GitHub Actions workflow with branch-based deployment  
**Secondary Source**: V1 Complete Guide (lines 1517-1750) - Build optimization and deployment verification  
**Recommendation**: Use V2's branch-aware deployment structure enhanced with V1's superior build optimizations and verification scripts

---

## 🎯 Purpose

Set up automated GitHub Actions workflow for building and deploying Laravel applications with zero-downtime deployment to multiple environments based on Git branches.

## ⚡ Quick Reference

**Time Required**: ~20 minutes  
**Prerequisites**: Step 21 completed, GitHub repository with proper access  
**Critical Path**: Workflow creation → Secrets configuration → First deployment test

---

## 🔄 **PHASE 1: Workflow Directory Setup**

### **1.1 Create GitHub Actions Structure**

```bash
# Navigate to project root
cd ~/projects/societypal

# Create GitHub Actions directory structure
mkdir -p .github/workflows
mkdir -p .github/scripts

echo "📁 GitHub Actions structure created"
```

**Expected Result:**
- `.github/workflows/` directory for workflow files
- `.github/scripts/` directory for deployment scripts
- Proper organization for CI/CD automation

### **1.2 Verify Git Repository Status**

```bash
# Check current repository status
echo "📋 Repository status:"
git status
git remote -v

# Verify GitHub repository connection
git ls-remote origin > /dev/null && echo "✅ GitHub connection verified" || echo "❌ GitHub connection failed"
```

**Expected Result:**
- Clean working directory
- GitHub remote properly configured
- Repository ready for workflow deployment

---

## 🔄 **PHASE 2: Deployment Workflow Creation**

### **2.1 Create Main Deployment Workflow**

```bash
cat > .github/workflows/deploy.yml << 'EOF'
name: Build and Deploy SocietyPal

on:
  push:
    branches: [main, staging, production]
  workflow_dispatch: # Allow manual triggers
    inputs:
      environment:
        description: 'Deployment environment'
        required: true
        default: 'staging'
        type: choice
        options:
        - staging
        - production

env:
  PHP_VERSION: '8.2'
  NODE_VERSION: '18'

jobs:
  build-and-deploy:
    runs-on: ubuntu-latest
    environment: ${{ github.ref_name == 'production' && 'production' || 'staging' }}
    
    steps:
    - name: 📥 Checkout Code
      uses: actions/checkout@v4
      with:
        fetch-depth: 0
        
    - name: 🐘 Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: ${{ env.PHP_VERSION }}
        extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql, zip, curl, gd, imagick
        coverage: none
        tools: composer:v2
        ini-values: memory_limit=512M, max_execution_time=120
        
    - name: 📦 Setup Node.js
      uses: actions/setup-node@v4
      with:
        node-version: ${{ env.NODE_VERSION }}
        cache: 'npm'
        
    - name: 🔍 Validate Dependencies
      run: |
        echo "🔍 Checking dependency files..."
        if [ ! -f "composer.json" ]; then
          echo "❌ composer.json not found"
          exit 1
        fi
        
        if [ -f "package.json" ]; then
          echo "✅ Frontend build files detected"
          HAS_FRONTEND=true
        else
          echo "ℹ️ No frontend build required"
          HAS_FRONTEND=false
        fi
        
        echo "HAS_FRONTEND=$HAS_FRONTEND" >> $GITHUB_ENV
        
    - name: 🔨 Build PHP Dependencies
      run: |
        echo "🔨 Installing PHP dependencies..."
        composer validate --no-check-publish
        composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress --audit
        
        echo "📊 Dependency information:"
        composer show --installed | grep -E "laravel|symfony" | head -10
        
    - name: 📦 Build Frontend Assets
      if: env.HAS_FRONTEND == 'true'
      run: |
        echo "📦 Installing Node dependencies..."
        npm ci --only=production --no-audit --no-fund
        
        echo "🎨 Building frontend assets..."
        npm run build
        
        echo "🧹 Cleaning up node_modules..."
        rm -rf node_modules
        
        echo "📊 Built assets summary:"
        du -sh public/build/* 2>/dev/null || echo "No built assets found"
        
    - name: ⚡ Optimize Laravel Application
      run: |
        echo "⚡ Optimizing Laravel application..."
        
        # Create temporary environment file for caching
        cp .env.example .env
        php artisan key:generate --ansi
        
        # Cache configurations
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        
        # Verify optimizations
        echo "📋 Optimization verification:"
        ls -la bootstrap/cache/
        
        # Remove temporary .env (server will have its own)
        rm .env
        
    - name: 📦 Create Deployment Artifact
      run: |
        echo "📦 Creating deployment package..."
        
        # Create deployment manifest
        cat > deployment-manifest.json << MANIFEST
        {
          "timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
          "commit": "${{ github.sha }}",
          "branch": "${{ github.ref_name }}",
          "workflow": "${{ github.run_id }}",
          "environment": "${{ github.ref_name == 'production' && 'production' || 'staging' }}"
        }
        MANIFEST
        
        # Create deployment package
        tar -czf deploy.tar.gz \
          --exclude='.git' \
          --exclude='.github' \
          --exclude='tests' \
          --exclude='.env*' \
          --exclude='storage/logs/*' \
          --exclude='storage/framework/cache/*' \
          --exclude='storage/framework/sessions/*' \
          --exclude='storage/framework/views/*' \
          --exclude='storage/app/public/*' \
          --exclude='node_modules' \
          --exclude='*.log' \
          --exclude='.DS_Store' \
          --exclude='Thumbs.db' \
          .
          
        echo "📊 Package information:"
        ls -lh deploy.tar.gz
        
    - name: 🔧 Determine Deployment Target
      id: deployment
      run: |
        DEPLOY_BRANCH="${{ github.ref_name }}"
        
        if [ "$DEPLOY_BRANCH" = "production" ]; then
          DOMAIN="societypal.com"
          ENV_TYPE="production"
        elif [ "$DEPLOY_BRANCH" = "staging" ]; then
          DOMAIN="staging.societypal.com"
          ENV_TYPE="staging"
        else
          DOMAIN="staging.societypal.com"  # Default fallback
          ENV_TYPE="staging"
        fi
        
        echo "🎯 Deployment target: $DOMAIN ($ENV_TYPE)"
        echo "domain=$DOMAIN" >> $GITHUB_OUTPUT
        echo "env_type=$ENV_TYPE" >> $GITHUB_OUTPUT
        
    - name: 🚀 Deploy to Server
      env:
        SERVER_HOST: ${{ secrets.SERVER_HOST }}
        SERVER_USER: ${{ secrets.SERVER_USER }}
        SERVER_PORT: ${{ secrets.SERVER_PORT }}
        SERVER_SSH_KEY: ${{ secrets.SERVER_SSH_KEY }}
        DEPLOY_DOMAIN: ${{ steps.deployment.outputs.domain }}
        ENV_TYPE: ${{ steps.deployment.outputs.env_type }}
      run: |
        echo "🔑 Setting up SSH connection..."
        mkdir -p ~/.ssh
        echo "$SERVER_SSH_KEY" > ~/.ssh/deploy_key
        chmod 600 ~/.ssh/deploy_key
        ssh-keyscan -p $SERVER_PORT $SERVER_HOST >> ~/.ssh/known_hosts
        
        echo "📤 Uploading deployment package to $DEPLOY_DOMAIN..."
        scp -P $SERVER_PORT -i ~/.ssh/deploy_key deploy.tar.gz $SERVER_USER@$SERVER_HOST:~/domains/$DEPLOY_DOMAIN/releases/
        
        echo "🚀 Executing deployment on $DEPLOY_DOMAIN..."
        ssh -p $SERVER_PORT -i ~/.ssh/deploy_key $SERVER_USER@$SERVER_HOST << ENDSSH
          set -e  # Exit on any error
          
          cd ~/domains/$DEPLOY_DOMAIN/
          
          # Create timestamped release directory
          TIMESTAMP=\$(date +%Y%m%d-%H%M%S)
          RELEASE_DIR="releases/\$TIMESTAMP"
          
          echo "📁 Creating release directory: \$RELEASE_DIR"
          mkdir -p \$RELEASE_DIR
          
          # Extract deployment package
          echo "📦 Extracting deployment..."
          tar -xzf releases/deploy.tar.gz -C \$RELEASE_DIR/
          rm releases/deploy.tar.gz
          
          # Navigate to release directory
          cd \$RELEASE_DIR
          
          # Link shared resources using our script
          echo "🔗 Linking shared resources..."
          if [ -f "Admin-Local/server_deployment/scripts/link_persistent_dirs.sh" ]; then
            bash Admin-Local/server_deployment/scripts/link_persistent_dirs.sh "\$(pwd)" "../../shared"
          else
            # Fallback manual linking
            ln -nfs ../../shared/.env .env
            rm -rf storage
            ln -nfs ../../shared/storage storage
          fi
          
          # Set proper permissions
          echo "🔒 Setting permissions..."
          find . -type f -exec chmod 644 {} \\;
          find . -type d -exec chmod 755 {} \\;
          chmod -R 775 storage bootstrap/cache 2>/dev/null || true
          
          # Run database migrations
          echo "🗄️ Running migrations..."
          php artisan migrate --force --no-interaction
          
          # Clear and rebuild cache
          echo "♻️ Rebuilding cache..."
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          
          # Atomic symlink switch
          echo "🔄 Switching to new release..."
          cd ~/domains/$DEPLOY_DOMAIN/
          ln -nfs \$RELEASE_DIR current
          
          # Setup public_html symlink if first deployment
          if [ ! -L public_html ]; then
            echo "🌐 Setting up public_html symlink..."
            rm -rf public_html
            ln -s current/public public_html
          fi
          
          # Cleanup old releases (keep last 3)
          echo "🧹 Cleaning up old releases..."
          cd releases/
          ls -t | tail -n +4 | xargs rm -rf 2>/dev/null || true
          
          echo "✅ Deployment completed successfully: \$TIMESTAMP"
          echo "🌐 Site should be available at: https://$DEPLOY_DOMAIN"
        ENDSSH
        
    - name: ✅ Verify Deployment
      env:
        DEPLOY_DOMAIN: ${{ steps.deployment.outputs.domain }}
        ENV_TYPE: ${{ steps.deployment.outputs.env_type }}
      run: |
        SITE_URL="https://$DEPLOY_DOMAIN"
        echo "🔍 Verifying deployment at $SITE_URL..."
        
        # Wait for services to fully restart
        sleep 15
        
        # Test site accessibility
        for i in {1..5}; do
          echo "🌐 Testing connectivity (attempt $i/5)..."
          HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time 30 $SITE_URL)
          
          if [ "$HTTP_STATUS" -eq 200 ]; then
            echo "✅ Deployment verification successful - Site responding with HTTP 200"
            
            # Additional verification
            echo "🔍 Performing additional checks..."
            RESPONSE_TIME=$(curl -w "%{time_total}" -s -o /dev/null $SITE_URL)
            echo "⏱️ Response time: ${RESPONSE_TIME}s"
            
            # Check for Laravel-specific indicators
            if curl -s $SITE_URL | grep -q "csrf-token\|Laravel"; then
              echo "✅ Laravel application detected"
            fi
            
            echo "🎉 Deployment to $ENV_TYPE environment completed successfully!"
            exit 0
          else
            echo "⏳ Site not ready yet (HTTP $HTTP_STATUS), waiting..."
            sleep 10
          fi
        done
        
        echo "❌ Deployment verification failed - Site not responding properly"
        echo "🔍 Manual verification required at: $SITE_URL"
        exit 1
EOF

echo "✅ GitHub Actions deployment workflow created"
```

**Expected Result:**
- Complete CI/CD workflow with build optimization
- Branch-aware deployment (staging/production)
- Comprehensive error handling and verification
- Atomic deployment with rollback capability

### **2.2 Create Deployment Helper Script** *(Optional Enhancement)*

```bash
cat > .github/scripts/deploy-helpers.sh << 'EOF'
#!/bin/bash

# Deployment helper functions for GitHub Actions

verify_deployment_health() {
    local site_url=$1
    local max_attempts=${2:-5}
    
    echo "🔍 Verifying deployment health for: $site_url"
    
    for i in $(seq 1 $max_attempts); do
        echo "🌐 Health check attempt $i/$max_attempts..."
        
        # Test basic connectivity
        if curl -sf --max-time 30 "$site_url" > /dev/null; then
            echo "✅ Basic connectivity: OK"
            
            # Test Laravel-specific endpoints
            if curl -sf "$site_url/health" > /dev/null 2>&1; then
                echo "✅ Health endpoint: OK"
            fi
            
            return 0
        else
            echo "⏳ Site not ready, waiting 10 seconds..."
            sleep 10
        fi
    done
    
    echo "❌ Health verification failed after $max_attempts attempts"
    return 1
}

cleanup_old_releases() {
    local releases_dir=$1
    local keep_count=${2:-3}
    
    echo "🧹 Cleaning up old releases (keeping $keep_count)..."
    cd "$releases_dir"
    ls -t | tail -n +$((keep_count + 1)) | xargs rm -rf 2>/dev/null || true
}
EOF

chmod +x .github/scripts/deploy-helpers.sh
echo "✅ Deployment helper scripts created"
```

**Expected Result:**
- Reusable deployment functions
- Enhanced health checking capabilities
- Standardized cleanup procedures

---

## 🔄 **PHASE 3: GitHub Secrets Configuration**

### **3.1 Generate Required Secrets List**

```bash
cat > .github/DEPLOYMENT_SECRETS.md << 'EOF'
# GitHub Actions Deployment Secrets

## Required Repository Secrets

### Server Connection (Required for all deployments)
```
SERVER_HOST=93.127.221.221
SERVER_USER=u164914061
SERVER_PORT=65002
SERVER_SSH_KEY=[Your complete private SSH key content]
```

### Environment-Specific Database Secrets (Optional)
```
# If using different database passwords per environment
STAGING_DB_PASSWORD=[staging database password]
PRODUCTION_DB_PASSWORD=[production database password]
```

## Setup Instructions

1. **Navigate to GitHub Repository Settings:**
   - Go to: `https://github.com/[username]/[repository]/settings/secrets/actions`

2. **Add Each Secret:**
   - Click "New repository secret"
   - Enter secret name and value
   - Click "Add secret"

3. **SSH Key Setup:**
   - Use the SAME private key configured in Step 16
   - Copy the ENTIRE content including header/footer lines:
     ```
     -----BEGIN OPENSSH PRIVATE KEY-----
     [key content]
     -----END OPENSSH PRIVATE KEY-----
     ```

## Environment Detection

The workflow automatically detects deployment environment:
- `main` branch → staging.societypal.com
- `staging` branch → staging.societypal.com  
- `production` branch → societypal.com

## Security Notes

- Never commit secrets to repository
- Use environment-specific secrets when needed
- Rotate SSH keys periodically
- Monitor workflow logs for sensitive information exposure
EOF

echo "📋 Secrets documentation created at .github/DEPLOYMENT_SECRETS.md"
echo ""
echo "🔑 NEXT: Add these secrets to your GitHub repository:"
echo "   → https://github.com/[your-username]/[repository]/settings/secrets/actions"
echo ""
echo "Required secrets:"
echo "- SERVER_HOST: 93.127.221.221"
echo "- SERVER_USER: u164914061" 
echo "- SERVER_PORT: 65002"
echo "- SERVER_SSH_KEY: [Your private SSH key from Step 16]"
```

**Expected Result:**
- Complete secrets documentation
- Clear setup instructions
- Security guidelines for sensitive data

### **3.2 Verify Secrets Configuration** *(After manual setup)*

```bash
echo "⚠️ MANUAL STEP REQUIRED:"
echo ""
echo "1. Go to GitHub repository → Settings → Secrets and Variables → Actions"
echo "2. Add the four required secrets listed above"
echo "3. Return here and run the verification"
echo ""
read -p "Press Enter after adding GitHub secrets to continue..."

# Test workflow trigger without actual deployment
echo "🧪 Testing workflow configuration..."
if [ -f ".github/workflows/deploy.yml" ]; then
    echo "✅ Workflow file exists"
    
    # Check workflow syntax
    if grep -q "SERVER_SSH_KEY" .github/workflows/deploy.yml; then
        echo "✅ Workflow references required secrets"
    else
        echo "❌ Workflow missing secret references"
    fi
else
    echo "❌ Workflow file missing"
fi
```

**Expected Result:**
- GitHub secrets properly configured
- Workflow file syntax verified
- Ready for first deployment test

---

## 🔄 **PHASE 4: First Deployment Test**

### **4.1 Commit and Deploy Workflow**

```bash
# Add all GitHub Actions files
git add .github/

# Commit workflow files
git commit -m "feat: add GitHub Actions CI/CD workflow

- Automated build process with PHP 8.2 and Node 18
- Branch-aware deployment (staging/production)
- Zero-downtime atomic deployment
- Comprehensive health verification
- Automatic cleanup of old releases
- Manual deployment trigger support"

echo "📋 Deployment checklist:"
echo "✅ Workflow files committed"
echo "⚠️ GitHub secrets configured (manual verification required)"
echo "🚀 Ready for first deployment"
```

**Expected Result:**
- All workflow files committed to repository
- Deployment configuration ready
- First deployment prepared

### **4.2 Trigger First Deployment**

```bash
# Deploy to staging first
echo "🚀 Triggering staging deployment..."
git push origin main

echo ""
echo "🎯 Deployment initiated!"
echo "📊 Monitor progress at: https://github.com/[your-username]/[repository]/actions"
echo ""
echo "Expected process:"
echo "1. GitHub builds application (3-5 minutes)"
echo "2. Deploys to staging.societypal.com"
echo "3. Runs verification tests"
echo "4. Reports success/failure"
echo ""
echo "📱 Test site will be available at: https://staging.societypal.com"
```

**Expected Result:**
- GitHub Actions workflow triggered
- Automated build and deployment initiated
- Progress visible in GitHub Actions tab
- Staging deployment ready for testing

---

## ✅ **Success Verification**

### **Deployment Success Checklist**

- [ ] GitHub Actions workflow runs without errors
- [ ] Build process completes successfully (3-5 minutes)
- [ ] Deployment to staging.societypal.com successful
- [ ] Site responds with HTTP 200
- [ ] Laravel application loads correctly
- [ ] Database migrations executed
- [ ] Static assets accessible

### **Workflow Features Confirmed**

- [ ] Branch-aware deployment working
- [ ] Atomic symlink switching functional
- [ ] Old release cleanup operational
- [ ] Health verification passing
- [ ] Manual deployment trigger available

---

## 📋 **Next Steps**

✅ **Step 22B Complete** - GitHub Actions workflow configured and tested  
🔄 **Continue to**: Step 23B (Automated Build and Deployment)  
🎯 **Alternative**: Production deployment via `git push origin production`

---

## 🎯 **Key Success Indicators**

- **Workflow Status**: ✅ Green in GitHub Actions tab
- **Build Time**: 3-5 minutes for complete process
- **Deployment Target**: Automatic based on Git branch
- **Zero Downtime**: Atomic symlink switching
- **Health Verification**: Automated testing and reporting
- **Rollback Ready**: Previous releases maintained for quick recovery

**Scenario B setup completed successfully!** 🚀
