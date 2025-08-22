# Step 02.5: Custom Data Persistence

## 🛡️ Ensuring Custom Data Directories for Customization

### **Integration with Universal Data Persistence**

This guide ensures that custom data directories are properly set up for app customization and integrated with the universal data persistence system.

---

## **Custom Data Classification**

### **Protected Custom Code (Customization Layer)**

```bash
# These are protected by customization system - NOT data persistence
app/Custom/                    # Custom controllers, models, services
resources/Custom/              # Custom views, components
public/Custom/                 # Custom assets (build artifacts)
```

### **Custom User Data (Data Persistence)**

```bash
# These need universal data persistence protection
public/custom-uploads/         # Custom upload functionality
public/custom-media/           # Custom media galleries  
public/custom-exports/         # Custom report exports
storage/app/custom/            # Custom file storage
```

---

## **Before Starting Customization**

### **Setup Custom Data Directories**

```bash
# Ensure custom data directories exist for customization
bash ../../../B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-18-Files/ultimate-persistence.sh "$(pwd)" "$(pwd)/../shared" "custom" "local"
```

**What this does**:

-   ✅ **Creates Custom Directories**: Ensures `public/custom-*/` and `storage/app/custom/` exist
-   ✅ **Verifies Structure**: Confirms directories are ready for custom development
-   ✅ **Local Focus**: Preparation for development, no symlinks
-   ✅ **Future-Proof**: Sets up structure for eventual server persistence
```

### **Create Custom Assets Backup**

```bash
# Create backup before major customizations
echo "📦 Creating pre-customization backup..."

# Backup current custom code
if [[ -d "app/Custom" ]]; then
    tar -czf "custom-backup-$(date +%Y%m%d-%H%M).tar.gz" app/Custom/ resources/Custom/ public/Custom/ 2>/dev/null
    echo "✅ Custom code backed up"
fi

# Backup shared user data
tar -czf "shared-backup-$(date +%Y%m%d-%H%M).tar.gz" -C .. shared/
echo "✅ User data backed up"
```

---

## **During Customization Development**

### **Custom Upload Functionality**

If your customization includes new upload features:

```bash
# Example: Adding custom document upload feature
# 1. Create upload directory in shared space (not in Git)
mkdir -p ../shared/public/custom-documents

# 2. Create symlink from public directory
ln -sf ../../shared/public/custom-documents public/custom-documents

# 3. Update .gitignore to exclude the symlink
echo "public/custom-documents" >> .gitignore

# 4. Update data persistence configuration
echo "public/custom-documents/" >> ../shared/.custom-shared-patterns
```

### **Custom Media Galleries**

```bash
# Example: Adding custom photo gallery
mkdir -p ../shared/public/custom-gallery

# Create symlink
ln -sf ../../shared/public/custom-gallery public/custom-gallery

# Add to gitignore
echo "public/custom-gallery" >> .gitignore

# Update persistence patterns
echo "public/custom-gallery/" >> ../shared/.custom-shared-patterns
```

### **Custom Export/Report Features**

```bash
# Example: Adding custom report exports
mkdir -p ../shared/public/custom-reports

# Create symlink
ln -sf ../../shared/public/custom-reports public/custom-reports

# Add to gitignore
echo "public/custom-reports" >> .gitignore

# Update persistence patterns
echo "public/custom-reports/" >> ../shared/.custom-shared-patterns
```

---

## **Custom Storage Integration**

### **Laravel Custom Storage**

```bash
# For custom file storage within Laravel storage
mkdir -p ../shared/storage/app/custom

# Laravel storage is already symlinked, so custom subdirectories work automatically
# Access via: Storage::disk('local')->put('custom/filename.ext', $content)
```

### **Custom Public Storage**

```bash
# For custom public storage accessible via web
mkdir -p ../shared/storage/app/public/custom

# This works with existing Laravel public storage symlink
# Access via: Storage::disk('public')->put('custom/filename.ext', $content)
# Web accessible at: /storage/custom/filename.ext
```

---

## **Testing Custom Data Persistence**

### **Functionality Tests**

```bash
# Test custom upload functionality
echo "🧪 Testing custom upload persistence..."

# 1. Upload file through custom functionality
# 2. Verify file appears in shared directory
ls -la ../shared/public/custom-*/

# 3. Test symlink integrity
ls -la public/ | grep "custom-"

# 4. Verify web accessibility
curl -I http://your-domain.com/custom-documents/test-file.pdf
```

### **Deployment Simulation Test**

```bash
# Simulate deployment to test persistence
echo "🧪 Simulating deployment to test custom data persistence..."

# 1. Create test custom content
echo "Test content" > ../shared/public/custom-documents/test.txt

# 2. Re-run persistence system (simulating deployment)
bash shared/emergency-recovery.sh "$(pwd)"

# 3. Verify custom content survived
cat ../shared/public/custom-documents/test.txt

# 4. Verify symlinks recreated correctly
ls -la public/custom-documents/

echo "✅ Custom data persistence test passed"
```

---

## **Integration with Deployment Workflows**

### **C-Deploy-Vendor-Updates Integration**

When deploying vendor updates with custom features:

```bash
# Custom assets are automatically preserved because:
# 1. Step 17 protects app/Custom/ code
# 2. Step 18 protects custom user data via symlinks
# 3. Custom shared patterns are detected and preserved

echo "🔍 Verifying custom integration before vendor update..."

# Check custom code protection
ls -la app/Custom/ resources/Custom/

# Check custom data persistence
bash shared/health-check.sh

# Verify custom shared patterns
cat ../shared/.custom-shared-patterns

echo "✅ Custom features ready for vendor update"
```

### **Future Deployments**

```bash
# Custom features work seamlessly in future deployments
# The persistence system automatically:
# 1. Detects custom shared directories
# 2. Preserves custom user content
# 3. Recreates custom symlinks
# 4. Maintains custom functionality

# Manual verification after deployment:
bash shared/health-check.sh
ls -la public/ | grep "custom-"
```

---

## **Custom Assets Security**

### **Secure Custom Upload Directories**

```bash
# Secure custom upload directories
echo "🔒 Securing custom upload directories..."

# Set proper permissions
find ../shared/public/custom-* -type d -exec chmod 755 {} \;
find ../shared/public/custom-* -type f -exec chmod 644 {} \;

# Add security headers for custom directories
cat > public/custom-documents/.htaccess << 'EOF'
# Security for custom documents
<Files "*.php">
    Order Allow,Deny
    Deny from all
</Files>

<Files "*.sh">
    Order Allow,Deny
    Deny from all
</Files>
EOF
```

### **Custom Directory Access Control**

```bash
# Implement access control for sensitive custom content
cat > public/custom-private/.htaccess << 'EOF'
# Private custom content - require authentication
AuthType Basic
AuthName "Private Custom Content"
AuthUserFile /path/to/.htpasswd
Require valid-user
EOF
```

---

## **Documentation & Tracking**

### **Document Custom Integrations**

```bash
# Create custom integration documentation
cat > ../shared/CUSTOM-INTEGRATIONS.md << EOF
# Custom Integrations Documentation

**Last Updated:** $(date)
**Customization Phase:** E-Customize-App

## Custom Shared Directories Created

$(cat ../shared/.custom-shared-patterns)

## Custom Code Locations

- app/Custom/ - Custom application logic
- resources/Custom/ - Custom views and components
- public/Custom/ - Custom static assets (if any)

## Custom Data Directories

$(ls -la ../shared/public/ | grep "custom-")

## Custom Functionality Added

- [List custom features implemented]
- [Include testing instructions]
- [Document any special configuration]

## Maintenance Notes

- Custom content is automatically preserved during deployments
- Run health check to verify custom symlinks: \`bash shared/health-check.sh\`
- Custom backups included in standard shared backup process

EOF

echo "✅ Custom integration documentation created"
```

### **Update Deployment Summary**

```bash
# Update deployment summary with custom information
cat >> ../shared/DEPLOYMENT-SUMMARY.md << EOF

## Custom Integrations

**Custom Features Added:** $(date)

### Custom Shared Directories
$(cat ../shared/.custom-shared-patterns 2>/dev/null | head -10)

### Custom Code Protection
- app/Custom/ protected by Step 17 customization system
- Custom user data protected by Step 18 data persistence
- Integration tested and verified

EOF
```

---

## **Troubleshooting Custom Data Issues**

### **Custom Symlink Issues**

```bash
# If custom symlinks break
echo "🔧 Fixing custom symlink issues..."

# Recreate custom symlinks based on patterns
while IFS= read -r pattern; do
    [[ -z "$pattern" ]] && continue

    local dir_path="$pattern"
    local shared_target="../../shared/$pattern"

    rm -rf "$dir_path"
    ln -sf "$shared_target" "$dir_path"
    echo "✅ Recreated: $dir_path → $shared_target"

done < ../shared/.custom-shared-patterns

echo "✅ Custom symlinks recreated"
```

### **Custom Content Recovery**

```bash
# If custom content is lost
echo "🚨 Custom content recovery..."

# Restore from backup if available
LATEST_CUSTOM_BACKUP=$(ls -t custom-backup-*.tar.gz 2>/dev/null | head -1)
if [[ -n "$LATEST_CUSTOM_BACKUP" ]]; then
    echo "📦 Restoring custom code from: $LATEST_CUSTOM_BACKUP"
    tar -xzf "$LATEST_CUSTOM_BACKUP"
fi

LATEST_SHARED_BACKUP=$(ls -t shared-backup-*.tar.gz 2>/dev/null | head -1)
if [[ -n "$LATEST_SHARED_BACKUP" ]]; then
    echo "📦 Restoring shared data from: $LATEST_SHARED_BACKUP"
    cd ..
    tar -xzf "$LATEST_SHARED_BACKUP"
    cd project_directory
fi

# Recreate symlinks
bash shared/emergency-recovery.sh "$(pwd)"

echo "✅ Custom content recovery completed"
```

---

This integration ensures that custom features developed during app customization are properly protected and persist through all future deployments, working seamlessly with both the customization protection system (Step 17) and the data persistence system (Step 18).
