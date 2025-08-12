# Step 11: Setup Local Development Site in Herd

**Configure Laravel Herd for local development environment**

> 📋 **Analysis Source:** V2 Step 8 - V1 had nothing, taking V2 entirely
> 
> 🎯 **Purpose:** Setup local development site accessible via HTTPS

---

## **Herd Site Configuration**

1. **Add site to Herd:**
   - Open Herd → Settings → Sites
   - Add the project root folder: `/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root`
   - Change Project Name: `societypal`
   - Select PHP Version: Check `composer.json` for requirement
   ```bash
   cat composer.json | grep php
   # Look for: "php": "^8.2"
   ```
   - Choose PHP 8.2 or 8.3 per app requirement
   - HTTPS: `yes`

2. **Verify site configuration:**
   - Click "Open in browser"
   - Should open: `https://societypal.test`

**Expected Result:** Local development site accessible at https://societypal.test.

---

**Next Step:** [Step 12: Create Environment Files](Step_12_Create_Environment_Files.md)
