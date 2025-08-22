# Exclusion-Based Data Classification Strategy

## Core Principle
**Default Assumption**: Everything is app code (vendor assets) unless explicitly defined as shared user data.

## Classification Logic

### 🔒 App Code (Vendor Assets) - DEFAULT
Everything not in the exclusion list is considered app code that should be replaced during vendor updates.

**Examples from SocietyPal:**
```
public/flags/          ✅ App code - static country flags (vendor assets)
public/img/            ✅ App code - application images (vendor assets)  
public/css/            ✅ App code - compiled stylesheets (vendor assets)
public/js/             ✅ App code - compiled JavaScript (vendor assets)
resources/views/       ✅ App code - template files (vendor assets)
app/                   ✅ App code - application logic (vendor assets)
```

### 📂 Shared Data (User Data) - EXPLICIT EXCLUSIONS ONLY

#### User Uploads & Generated Content
```bash
public/user-uploads/   # User uploaded files (avatars, documents)
public/qrcodes/        # Generated QR codes
public/invoices/       # Generated invoice PDFs
storage/app/public/    # Laravel public storage symlink target
```

#### User Configurations
```bash
.env                   # Environment configuration
config/custom.php      # Custom configurations (if exists)
```

#### User Database & Logs
```bash
database/database.sqlite    # SQLite database (if used)
storage/logs/              # Application logs
storage/debugbar/          # Debug information
```

#### Custom Development (Step 17 Protected)
```bash
app/Custom/               # Custom code layer (Step 17 protected)
resources/Custom/         # Custom views (Step 17 protected)
public/Custom/           # Custom assets (Step 17 protected)
```

## Implementation Pattern

### 1. Define Exclusion Array
```bash
# shared_data_exclusions.conf
SHARED_DATA_PATHS=(
    "public/user-uploads"
    "public/qrcodes" 
    "public/invoices"
    "storage/app/public"
    "storage/logs"
    ".env"
    "app/Custom"
    "resources/Custom"
    "public/Custom"
)
```

### 2. Processing Logic
```bash
for item in public/*; do
    if [[ " ${SHARED_DATA_PATHS[*]} " =~ " ${item} " ]]; then
        echo "SHARED DATA: $item (preserve during updates)"
        # Move to shared location
    else
        echo "APP CODE: $item (replace during updates)"
        # Replace with vendor version
    fi
done
```

## Validation Questions

For each directory/file, ask:

1. **User Created?** - Did users upload or create this content?
2. **User Configured?** - Did users modify settings/configuration?
3. **Generated Runtime?** - Created by application based on user actions?
4. **Custom Development?** - Part of Step 17 customization layer?

If **ALL answers are NO** → It's app code (vendor assets)
If **ANY answer is YES** → Add to exclusion list (shared data)

## Laravel-Specific Patterns

### ✅ Typically App Code
- `public/build/` - Vite compiled assets
- `public/vendor/` - Package assets
- `vendor/` - Composer packages
- `node_modules/` - NPM packages
- `resources/views/` - Blade templates
- `resources/css/` - Source stylesheets
- `resources/js/` - Source JavaScript

### ✅ Typically Shared Data
- `storage/app/` - File uploads
- `storage/logs/` - Application logs
- `bootstrap/cache/` - Framework cache
- `.env` - Environment configuration

### ⚠️ Context Dependent
- `public/images/` - Could be app assets OR user uploads
- `config/` - Mostly app code, but custom configs might be shared
- `database/migrations/` - App code, but custom migrations in Custom/ are shared

## Anti-Patterns to Avoid

### ❌ Wrong: Static Asset as Shared Data
```bash
# INCORRECT - flags are vendor static assets
public/flags/ → shared_data/
```

### ❌ Wrong: Complex Multi-Tier Classification
```bash
# INCORRECT - overcomplicated
if vendor_static; then app_code
elif user_uploads; then shared_data  
elif generated_static; then maybe_shared
```

### ✅ Correct: Simple Exclusion
```bash
# CORRECT - simple exclusion list
if in_exclusion_list; then shared_data
else app_code
```

## Template Customization

### For Each New Laravel App:
1. **Start with base exclusion list**
2. **Analyze actual user data** in the application
3. **Add only confirmed user data** to exclusions
4. **Test with staging deployment**
5. **Document any app-specific patterns**

### Documentation Template:
```markdown
# App-Specific Data Classification
## App: [Name] v[Version]
## Analysis Date: [Date]

### Confirmed User Data (Added to Exclusions):
- `public/custom-uploads/` - User uploaded content
- `config/tenant.php` - Tenant-specific configuration

### Confirmed App Code (Default Classification):
- `public/themes/` - Vendor theme assets
- `public/plugins/` - Vendor plugin assets

### Notes:
- Special consideration for [specific feature]
- Integration with [external service]
```

This exclusion-based strategy ensures we protect actual user data while properly treating vendor assets as replaceable application code.
