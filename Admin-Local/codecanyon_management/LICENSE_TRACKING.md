# CodeCanyon License Tracking - SocietyPro

## Application Details
- **Script Name:** SocietyPro - Society Management Software
- **Purchase Code:** [Enter your purchase code]
- **Version Purchased:** v1.0.42 (confirmed from git tags - source of truth)
- **Version Files:** v1.0.41 (public/version.txt - vendor file, keep as-is)
- **Purchase Date:** [Enter purchase date]
- **License Type:** Regular License (or Extended License)
- **Vendor:** Froiden Technologies (confirmed via config/froiden_envato.php)

## 📋 Discovered CodeCanyon Patterns in SocietyPro

### 🔍 Detection Patterns Found:
| Pattern | Location | Purpose | Notes |
|---------|----------|---------|-------|
| `froiden_envato.php` | `/config/` | Envato API integration | Primary license verification |
| `installer/` | `/public/` | Web-based installer | Multi-step setup process |
| `error_install.php` | `/public/` | Installation error handling | Custom error pages |
| `version.txt` | `/public/` | App version display | Shows v1.0.41 (vendor file) |
| `install-version.txt` | `/public/` | Installation status | Shows 'complete' |
| `installer.php` | `/config/` | Installer configuration | Installation settings |
| Installer views | `/resources/views/vendor/froiden-envato/` | Installation UI templates | Envato-specific views |
| Custom installer | `/resources/views/custom-modules/install.blade.php` | Custom installation modules | Extended functionality |
| Language support | `/lang/eng/installer_messages.php` | Installer translations | Multi-language support |
| Vendor package | `/vendor/froiden/laravel-installer/` | Core installer logic | Composer package |

### 🏗️ Architecture Patterns:
- **Froiden Integration:** Uses Froiden Technologies' Laravel installer package
- **Multi-Layer Installation:** Web installer + config files + custom modules
- **Version Management:** Separate version files for different purposes
- **Error Handling:** Custom error pages for installation issues
- **Multi-Language:** Built-in language support for installers

### 🔌 Addon Support Structure:
- **Custom Modules:** `/resources/views/custom-modules/` - Indicates addon support
- **Modular Architecture:** Suggests plugin/addon capability
- **Separate Licensing:** Each addon likely requires separate purchase codes

## Version History
| Version | Release Date | Purchase Date | Purchase Code | Git Tag | Notes |
|---------|--------------|---------------|---------------|---------|-------|
| v1.0.42 | [Date] | [Date] | [Code] | ✅ v1.0.42 | Current (git source of truth) |
| v1.0.41 | [Date] | [Date] | [Code] | - | Version file display |

## License Files Location
- **Production:** `shared/licenses/` (on server)
- **Backup:** `Admin-Local/codecanyon_management/licenses/`
- **Original:** Store securely offline
- **Config:** `config/froiden_envato.php` (license verification)

## Addon Management
### Addon Directory Structure:
```
Admin-Local/codecanyon_management/addons/
├── purchased/          # Downloaded addon files
├── licenses/          # Addon license files
├── installed/         # Currently active addons
└── updates/           # Addon update files
```

### Addon Installation Patterns:
- **Web Install:** Via admin panel (one-click)
- **Manual Install:** Via codebase (like main app)
- **License Requirements:** Each addon needs separate purchase code
- **Update Strategy:** Similar to main app but independent

## Update Strategy
### 🔄 Main App Updates:
1. **Before Updates:** 
   - Run: `bash Admin-Local/codecanyon_management/update_tracking/capture_changes.sh`
   - Backup current version and customizations
   - Document all custom modifications in `app/Custom/`

2. **During Updates:** 
   - **NEVER overwrite** `app/Custom/` directory (customization layer)
   - **PRESERVE** license files and configuration
   - **MAINTAIN** addon compatibility

3. **After Updates:** 
   - Run: `bash Admin-Local/codecanyon_management/update_tracking/compare_changes.sh <snapshot_dir>`
   - Test all custom functionality
   - Verify addon compatibility
   - Test license verification

4. **License Migration:** 
   - Copy license files to new version
   - Update `config/froiden_envato.php` if needed
   - Verify Envato API connectivity

### 🔌 Addon Updates:
- **Independent Updates:** Each addon updates separately
- **Compatibility Check:** Verify main app version requirements
- **License Validation:** Ensure addon licenses remain valid
- **Testing:** Test addon functionality after updates

## Support Information
- **Author:** Froiden Technologies (confirmed)
- **Support Until:** [Date + 6 months from purchase]
- **Documentation:** [Link to documentation]
- **API Integration:** Envato API for license verification
- **Installer Package:** `froiden/laravel-installer` (Composer)

## 🚨 Critical Notes
- **Version Source Truth:** Git tags (v1.0.42) override version files (v1.0.41)
- **Customization Policy:** Use `app/Custom/` layer, minimize vendor file changes
- **License Verification:** Handled via `config/froiden_envato.php` + Envato API
- **Installation Status:** Tracked in `public/install-version.txt` (shows 'complete')
- **Multi-Component:** Main app + potential addons require separate management

## 📊 Installation Flow Analysis
```
1. Download from CodeCanyon
2. Extract to server
3. Access /public/installer/ 
4. Follow web-based installation
5. License verification via Envato API
6. Database setup and seeding
7. Configuration completion
8. Installation status: 'complete'
```

## 🔧 Maintenance Commands
```bash
# Check installation status
cat public/install-version.txt

# View current version
cat public/version.txt

# Backup before updates
bash Admin-Local/codecanyon_management/update_tracking/capture_changes.sh

# Compare after updates
bash Admin-Local/codecanyon_management/update_tracking/compare_changes.sh <snapshot_dir>