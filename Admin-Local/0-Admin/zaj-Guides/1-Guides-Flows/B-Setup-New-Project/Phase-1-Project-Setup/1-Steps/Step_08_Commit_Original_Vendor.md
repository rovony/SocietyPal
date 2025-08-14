# Step 08: Commit Original Vendor Files - Preserve pristine CodeCanyon files first

**Preserve original CodeCanyon files before any modifications**

> 📋 **Analysis Source:** V1 Step 5.3 - V2 completely missing this critical step, taking V1 entirely
>
> 🎯 **Purpose:** Create baseline branch with original vendor files for safe updates and comparisons

---

## **📖 TERMINOLOGY CLARIFICATION**

**"Vendor" in this context refers to:**
- ✅ **CodeCanyon Author/Upstream Vendor:** The original author who created the CodeCanyon application (e.g., SocietyPro author)
- ✅ **Third-party Software Provider:** The entity that developed the purchased software/script

**"Vendor" does NOT refer to:**
- ❌ **`vendor/` folder:** The Composer dependencies directory containing PHP packages
- ❌ **Composer packages:** Third-party libraries installed via `composer install`

**In this step:** "Vendor files" = Original CodeCanyon author's files only (app/, config/, resources/, public/, database/, routes/, etc.)

---

## **Original Vendor Preservation**

**🚨 CRITICAL: This branch must contain ONLY clean, unmodified CodeCanyon author files**

1. **Verify clean CodeCanyon author files before commit:**

   ```bash
   # ESSENTIAL: Ensure ONLY original CodeCanyon files are included
   # DO NOT commit any customizations, Admin-Local/, .env files, or non-vendor modifications
   
   # Check what files will be committed (should only be CodeCanyon author files)
   git status --porcelain
   
   # Remove any non-vendor files from staging if they exist
   git restore --staged Admin-Local/       # Remove admin files if accidentally added
   git restore --staged .env*              # Remove environment files if accidentally added
   git restore --staged *.md               # Remove documentation files if accidentally added
   
   # Verify no CodeCanyon author files have been modified from original state
   # If using existing project, compare against fresh CodeCanyon download
   
   # Commit ONLY original CodeCanyon files
   git add .
   git commit -m "feat: initial CodeCanyon files v1.0.42 (original vendor state)"
   ```

2. **Create vendor/original branch with proper tagging:**

   ```bash
   # Create vendor/original branch to preserve original state
   git checkout -b vendor/original
   
   # Create version tag on this branch (matches CodeCanyon version)
   git tag -a v1.0.42 -m "CodeCanyon SocietyPro v1.0.42 - Original vendor files"
   
   # Push branch and tag to origin
   git push -u origin vendor/original
   git push origin v1.0.42
   
   # Verify tag was created correctly
   git tag -l | grep v1.0.42
   git describe --tags --exact-match HEAD
   
   # Return to main branch for development
   git checkout main
   ```

**Expected Result:**

- Original vendor files preserved in separate branch with NO modifications
- Git tag matches CodeCanyon version for reliable version tracking
- Ability to compare changes against original pristine state
- Clean rollback point if needed
- Branch and tag pushed to remote for backup

**Why this matters:** When vendor releases updates, you can compare against original files to see what changed, making updates safer. The vendor/original branch serves as your pristine backup of the author's work.

**⚠️ File Integrity Verification:**

```bash
# Verify vendor/original branch contains only CodeCanyon files
git checkout vendor/original
ls -la  # Should show only CodeCanyon application files
git log --oneline -n 5  # Should show clean vendor commit history

# Check no customization files are present
[ ! -d "Admin-Local" ] && echo "✅ No Admin-Local directory" || echo "❌ Admin-Local found"
[ ! -f ".env" ] && echo "✅ No .env files" || echo "❌ .env files found"

git checkout main  # Return to main branch
```

---

**Next Step:** [Step 09: Create Admin-Local Directory Structure](Step_09_Admin_Local_Structure.md)
