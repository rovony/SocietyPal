# Step 12: Setup Local Development Site in Herd

**Configure Laravel Herd for local development environment**

> 📋 **Analysis Source:** V2 Step 8 - V1 had nothing, taking V2 entirely
>
> 🎯 **Purpose:** Setup local development site accessible via HTTPS

---

## 🚨 Human Interaction Required

**⚠️ This step includes tasks that must be performed manually outside this codebase:**

-   Opening Herd application GUI and configuring site settings
-   Clicking through Herd interface menus and buttons
-   Logging into Herd and providing license key
-   **All other operations in this step are automated/AI-executable**

**🏷️ Tag Instruct-User 👤** markers indicate the specific substeps requiring human action.

---

## 1. Human Interactive Step Preparation

### 1.1. Interactive Guide Access

**🏷️ Tag Instruct-User 👤** This step requires manual configuration through the Herd application interface

**Interactive Guide Available:** [FirstSetup-Instructions.html](../../0-Setup-Operations/1-First-Setup/2-StepsHuman-Instructions/FirstSetup-Instructions.html#herd-config)

### 1.2. Interactive Guide Features

**🏷️ Tag Instruct-User 👤** Click the **"🖥️ Herd Config"** tab in the interactive guide for:

-   Step-by-step Herd configuration instructions
-   Visual checklist for completion tracking
-   Command copy buttons for easy terminal usage
-   Progress tracking and auto-save functionality

---

## 2. PHP Version Requirements Detection

### 2.1. Check Project PHP Requirements

**Current Project Requirement:** PHP ^8.2 (confirmed from composer.json)

```bash
cat composer.json | grep php
# Result: "php": "^8.2"
# Select PHP 8.2 or 8.3 in Herd configuration
```

### 2.2. Command Explanation and Usage

**Purpose**: Determines the minimum PHP version required by the Laravel application

**Process Steps:**

1. Searches `composer.json` for PHP version constraints
2. Shows requirement line (e.g., "php": "^8.2")
3. Guides PHP version selection in Herd (8.2 or higher)

**Example Output Interpretation:** If output shows "^8.2", select PHP 8.2 or 8.3 in Herd settings

---

## 3. Herd Site Configuration Steps

### 3.1. Add Site to Herd

**🏷️ Tag Instruct-User 👤** Required Steps:

1. Open Herd application from system tray
2. Navigate to **Settings → Sites**
3. Click **Add** or **+** button
4. Configure the following settings:
    - **Project Root Folder:** `/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root`
    - **Project Name:** `societypal`
    - **PHP Version:** 8.2+ (per composer.json requirement)
    - **HTTPS:** `yes` (enabled)

### 3.2. Verify Site Configuration

**🏷️ Tag Instruct-User 👤** Verification Steps:

1. Click **"Save"** or **"Add Site"** in Herd
2. Click **"Open in browser"** from Herd interface
3. **Expected Result:** Browser opens to `https://societypal.test`
4. **Success Indicator:** Local development site accessible at https://societypal.test

### 3.3. Expected CodeCanyon Installation Screen

**✅ NORMAL BEHAVIOR - Installation Required**

When you successfully access `https://societypal.test`, you should see:

- **"Societypro-saas Installation Required"** message
- Server requirements checklist (all should show ✅ Compatible)
- **"Launch Installer"** button

**📸 Visual Reference:** See screenshots in [`Admin-Local/3-Guides-V3-Consolidated/999-misc/images/1-First-Setup/Step_12_Herd_Setup/`](../../../999-misc/images/1-First-Setup/Step_12_Herd_Setup/)

**🎯 Why This Happens:**
- **CodeCanyon Applications:** Show installation screens on first access (common for marketplace apps)
- **Non-CodeCanyon Applications:** Typically go directly to login or home page
- **Next Steps:** This installation screen will be completed in Step 14: Run Local Installation

### 3.4. Troubleshooting Common Issues

**❌ If site doesn't load:**

1. Verify project path is correct in Herd
2. Confirm PHP version matches composer.json requirement
3. Check HTTPS is enabled
4. Restart Herd service if necessary

**❌ If Herd shows login/license errors:**

1. **🏷️ Tag Instruct-User 👤** Ensure you're logged into Herd with valid account
2. **🏷️ Tag Instruct-User 👤** Verify Herd Pro license key is entered if required
3. **🏷️ Tag Instruct-User 👤** Check for any Herd updates and install if available
4. Restart Herd after license verification

**❌ If installation screen doesn't appear:**

1. Check that `.env` file exists in project root
2. Verify Laravel application is properly configured
3. Check browser console for JavaScript errors
4. Clear browser cache and try again

---

**Next Step:** [Step 13: Create Local Database](Step_13_Create_Local_Database.md)
