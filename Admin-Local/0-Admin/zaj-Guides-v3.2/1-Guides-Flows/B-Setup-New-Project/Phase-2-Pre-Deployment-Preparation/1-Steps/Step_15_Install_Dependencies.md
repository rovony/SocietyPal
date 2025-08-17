# Step 15: Install Dependencies & Generate Lock Files

**Install and verify all project dependencies for reproducible builds**

> 📋 **Analysis Source:** V2 Step 10 - identical content, better organization (our analysis: "Take V2 - identical content, better organization")
>
> 🎯 **Purpose:** Install dependencies and generate lock files for consistent deployments

---

## **Install PHP Dependencies**

1. **Navigate to project root:**

   ```bash
   cd "/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"
   ```

2. **Install PHP dependencies (development mode for local work):**
   ```bash
   composer install
   ```

**Expected Result:**

- `vendor/` directory created with all PHP packages
- `composer.lock` file generated with exact versions
- All Composer dependencies available for development

**Why `composer install` not `composer update`:**

- `install` uses exact versions from `composer.lock` (reproducible builds)
- `update` would change versions and potentially break functionality

## **Install JavaScript Dependencies (if applicable)**

3. **Check for frontend dependencies:**
   ```bash
   # Check if package.json exists
   if [ -f "package.json" ]; then
       echo "✅ JavaScript build system detected"
       npm install
       echo "✅ JavaScript dependencies installed"
   else
       echo "ℹ️ No package.json found - PHP-only project"
   fi
   ```

**Expected Result:**

- `node_modules/` directory created (if has JavaScript)
- `package-lock.json` file generated with exact versions
- All frontend build tools available

**Why `npm install` not `npm ci`:**

- `npm install` generates `package-lock.json` if missing
- `npm ci` requires existing `package-lock.json` file

## **Verify Lock Files Created**

4. **Confirm lock files exist:**

   ```bash
   # Check for lock files
   ls -la composer.lock package-lock.json 2>/dev/null || echo "Some lock files missing"

   # Verify vendor directory
   ls -la vendor/ | head -5

   # Verify node_modules (if applicable)
   ls -la node_modules/ | head -5 2>/dev/null || echo "No node_modules (PHP-only project)"
   ```

**Expected Result:** Lock files present, dependencies installed, ready for reproducible builds.

---

**Next Step:** [Step 16: Test Build Process](Step_16_Test_Build_Process.md)
