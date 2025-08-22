# 🚀 Deployment Configuration Wizard

A smart, user-friendly wizard for collecting server and deployment information with advanced auto-save and backup features.

## ✨ Key Features

### � Path-Based Storage:

-   **User-specified save paths** - Files saved to the path you specify in Step 1
-   **Path-specific backups** - Each path maintains its own backup history
-   **Automatic path discovery** - Only shows backups for the current save path
-   **Path validation** - Ensures save paths are correctly formatted
-   **Visual path indicator** - Save status shows current path

### �🔄 Auto-Save System

-   **Automatic saving** on every step navigation
-   **Real-time auto-save** every 30 seconds
-   **Visual save indicator** showing current status and save path
-   **Before-unload protection** prevents data loss

### 🗂️ Backup & Version Control

-   **Smart backup creation** when overwriting files
-   **Path-specific version history** with up to 10 backups per path
-   **One-click restore** from any backup
-   **Path-aware backup management interface**

### ⚠️ Overwrite Protection

-   **Conflict detection** warns about existing files
-   **User choice dialog** with multiple options:
    -   🚫 Cancel save
    -   ⚠️ Overwrite without backup
    -   💾 Create backup then overwrite

### 📁 File Management

-   **Structured JSON export** with metadata
-   **Timestamped filenames** for easy organization
-   **Data integrity verification** with checksums
-   **Import validation** with error handling

## 🎯 How It Works

### 1. Path-Aware Auto-Saving

```
Step 1 → Set save path: "Admin-Local/1-Current-Project/1-secrets"
Step 2 → Fill data → Click "Next" → ✅ Auto-saved to specified path
Step 3 → Fill data → Click "Next" → ✅ Auto-saved to specified path
...
```

### 2. Path-Specific Backup Discovery

```
Save Path: "Admin-Local/1-Current-Project/1-secrets"
└── deployment-config.json
└── backup-versions/
    ├── deployment-config-2025-08-17T10-30-15.json
    ├── deployment-config-2025-08-17T09-15-42.json
    └── deployment-config-2025-08-16T16-45-23.json

Different path shows different backups:
Save Path: "Admin-Local/2-Another-Project/secrets"
└── deployment-config.json
└── backup-versions/
    └── deployment-config-2025-08-15T14-20-10.json
```

### 3. Smart Overwrite Protection

```
Attempting to save to: Admin-Local/1-Current-Project/1-secrets/deployment-config.json
Existing file detected → Show dialog:
┌─────────────────────────────────────────┐
│ ⚠️  Configuration Exists                │
│                                         │
│ A configuration file already exists in: │
│ Admin-Local/1-Current-Project/1-secrets │
│ with different content.                 │
│                                         │
│ What would you like to do?              │
│                                         │
│ [Cancel] [Overwrite] [Backup & Save]    │
└─────────────────────────────────────────┘
```

### 4. Path-Aware Backup Management

```
🗂️ Backups → Shows path-specific history:
┌─────────────────────────────────────────┐
│ 🗂️ Backup Management                   │
│ 📁 Save Path: Admin-Local/1-Current... │
│                                         │
│ 📅 2025-08-17 10:30:15 (24KB) [Restore]│
│    📁 Admin-Local/.../backup-versions/  │
│ 📅 2025-08-17 09:15:42 (22KB) [Restore]│
│    � Admin-Local/.../backup-versions/  │
└─────────────────────────────────────────┘
```

## 🛠️ Usage

1. **Open** `index.html` in any modern browser
2. **Fill out** the wizard step by step
3. **Data is automatically saved** on each step
4. **Export** final configuration as JSON
5. **Manage backups** via the backup interface

## 🎮 Keyboard Shortcuts

-   `Ctrl/Cmd + S` - Save/Export current configuration
-   `Ctrl/Cmd + Enter` - Go to next step
-   `Escape` - Go to previous step

## 📊 File Structure

```
deployment-wizard/
├── index.html                 # Main wizard interface
├── assets/
│   ├── css/
│   │   ├── variables.css      # Design tokens
│   │   ├── components.css     # UI components
│   │   └── main.css          # Layout styles
│   └── js/
│       ├── app.js            # App initialization
│       ├── wizard.js         # Core wizard logic
│       ├── validation.js     # Form validation
│       ├── storage.js        # Auto-save & backup system
│       └── utils.js          # Utility functions
```

## 🔧 Technical Details

### Storage Strategy

-   **Primary**: localStorage for instant access
-   **Backup**: Automatic file downloads
-   **Recovery**: Multiple restore points

### Data Integrity

-   **Checksums** for file verification
-   **Metadata** with version info
-   **Error handling** for corrupted data

### Performance

-   **Lazy loading** of heavy operations
-   **Throttled auto-save** to prevent spam
-   **Efficient diff detection** for changes

## 🚀 Getting Started

Simply open `index.html` and start filling out your deployment configuration. The system will handle all the saving, backing up, and protection automatically!

---

**Built with modern web standards • No dependencies • Works offline**
