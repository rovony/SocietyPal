# Laravel Permissions Scripts

**Ready-to-use scripts for managing Laravel file permissions during CodeCanyon installations and updates.**

## 📋 Scripts Overview

| Script                              | Purpose                                           | Usage                                      |
| ----------------------------------- | ------------------------------------------------- | ------------------------------------------ |
| `1-permissions-pre-install.sh`      | Set temporary 777 permissions before installation | Before CodeCanyon frontend install/update  |
| `2-permissions-post-install.sh`     | Restore secure permissions after installation     | Immediately after install/update completes |
| `permissions-emergency-security.sh` | Emergency security recovery                       | When you forgot to run post-install script |

## 🚀 Quick Usage

### **Before CodeCanyon Installation/Update:**

```bash
cd /path/to/your/laravel/project
./Admin-Local/0-Setup-Operations/1-First-Setup/1-StepsScripts/install-scripts/1-permissions-pre-install.sh
```

### **After CodeCanyon Installation/Update Completes:**

```bash
./Admin-Local/0-Setup-Operations/1-First-Setup/1-StepsScripts/install-scripts/2-permissions-post-install.sh
```

### **Emergency Security Recovery:**

```bash
./Admin-Local/0-Setup-Operations/1-First-Setup/1-StepsScripts/install-scripts/permissions-emergency-security.sh --auto
```

## 🎛️ Script Options

All scripts support these options:

-   `--interactive` - Prompt for confirmation before each action
-   `--auto` - Run without prompts (non-interactive mode)
-   `--env=local|staging|production` - Force specific environment
-   `--help` - Show help information

## 🔒 Security Features

-   **Environment Detection**: Automatically detects local/staging/production from `.env`
-   **Smart Permissions**: Different permission levels for different environments
-   **Security Audits**: Built-in checks for dangerous 777 permissions
-   **Logging**: Detailed logs in `install-permissions.log` and `emergency-security.log`
-   **Rollback Safety**: Creates status files to track operations

## 📚 Complete Documentation

For comprehensive permissions guide, see:
[Laravel File Permissions Security Guide](../../../3-Guides-V3-Consolidated/99-Understand/Laravel_File_Permissions_Security_Guide.md)
