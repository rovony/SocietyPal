# Step 07: Download and Extract CodeCanyon Application

**Download and integrate CodeCanyon application into project structure**

> 📋 **Analysis Source:** V2 Step 6 - V1 had nothing, taking V2 entirely
>
> 🎯 **Purpose:** Extract CodeCanyon application files into Git-managed project root

---

## **CodeCanyon Integration Process**

**INSTRUCT-USER: CodeCanyon Account Access Required**

```bash
echo "⚠️  HUMAN ACTION REQUIRED - External Service Access"
echo "📦 CodeCanyon Download Process:"
echo "==============================="
echo "1. Login to your CodeCanyon account"
echo "2. Navigate to your downloads/purchases"
echo "3. Download SocietyPro v1.0.4 from CodeCanyon"
echo "4. Rename to: SocietyPro-v1.0.4.zip"
echo "5. Move to: tmp-zip-extract/ directory"
echo ""
echo "💡 Return here after download completes"
```

**END-INSTRUCT-USER**

1. **Create temporary extraction directory:**

   ```bash
   mkdir -p tmp-zip-extract
   ```

2. **Download CodeCanyon ZIP:**

   - Download SocietyPro v1.0.4 from CodeCanyon
   - Rename to `SocietyPro-v1.0.4.zip`
   - Move to `tmp-zip-extract/` directory

3. **Extract CodeCanyon files:**

   ```bash
   cd tmp-zip-extract
   unzip SocietyPro-v1.0.4.zip
   ```

4. **Copy application files to project root:**

   ```bash
   # Set path variables for consistency - CUSTOMIZE FOR YOUR PROJECT
   # ⚠️ NOTE: This path was configured in Step_01_Project_Information.md
   # If you're working on a different project, update this variable:
   export PROJECT_ROOT="/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root"  # ⚠️ CHANGE FOR YOUR PROJECT

   # Navigate back to project root
   cd "$PROJECT_ROOT"

   # Copy all files from extracted folder to current directory
   # (Manually copy all contents from extracted folder or use provided script)
   ```

5. **Clean up temporary files:**
   ```bash
   rm -rf tmp-zip-extract
   ```

**Expected Result:** CodeCanyon application files in project root with Laravel structure (app/, public/, artisan, composer.json, etc.).

---

**Next Step:** [Step 08: CodeCanyon Configuration](Step_08_CodeCanyon_Configuration.md)
