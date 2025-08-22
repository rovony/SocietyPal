# Deployment Wizard - Refactored Structure

```
deployment-wizard/
├── index.html                 # Main HTML file
├── assets/
│   ├── css/
│   │   ├── variables.css      # CSS custom properties & design tokens
│   │   ├── components.css     # Reusable component styles
│   │   └── main.css          # Layout & page-specific styles
│   └── js/
│       ├── app.js            # Main application initialization
│       ├── wizard.js         # Step navigation & form management
│       ├── validation.js     # Form validation logic
│       ├── storage.js        # JSON persistence with backup system
│       └── utils.js          # Utility functions (toast, copy, etc.)
```

## Key Benefits:

-   **Modular**: Each file has a clear responsibility
-   **Scalable**: Easy to add new steps, components, or features
-   **Maintainable**: Changes are isolated to specific files
-   **Organized**: Logical separation of concerns
-   **Future-proof**: Structure supports easy expansion

## File Breakdown:

### HTML

-   **index.html**: Clean semantic structure without inline styles/scripts

### CSS Files

-   **variables.css**: Design tokens (colors, spacing, typography, shadows)
-   **components.css**: Reusable UI components (buttons, forms, progress bars)
-   **main.css**: Layout styles and responsive design

### JavaScript Files

-   **app.js**: Application initialization and global event handlers
-   **wizard.js**: Core wizard logic (step navigation, data management)
-   **validation.js**: Form validation rules and real-time validation
-   **storage.js**: JSON persistence with auto-save, backup system, and overwrite protection
-   **utils.js**: Utility functions (toast, copy, file operations)

## How to Extend:

### Adding New Steps:

1. Add HTML structure to `index.html`
2. Add step validation rules to `validation.js`
3. Update `wizard.js` totalSteps if needed

### Adding New Components:

1. Add styles to `components.css`
2. Add JavaScript behavior to appropriate module

### Adding New Features:

1. Create new utility functions in `utils.js`
2. Add validation rules in `validation.js`
3. Update wizard logic in `wizard.js`

## Usage:

Simply open `index.html` in a browser. All assets are loaded relatively, making it easy to deploy or develop locally.

## 🆕 New Features - JSON Auto-Save System:

### Auto-Save Functionality:

-   **Automatic saving** on every step navigation (Next/Previous buttons)
-   **Real-time saving** every 30 seconds while editing
-   **Before unload protection** - saves data when leaving the page
-   **Visual save status indicator** in top-right corner

### Backup & Version Control:

-   **Automatic backup creation** when overwriting existing files
-   **Backup history management** with up to 10 versions
-   **Restore from backup** functionality
-   **Backup browser** accessible via "🗂️ Backups" button

### Smart Overwrite Protection:

-   **Conflict detection** - warns when saving over different content
-   **User choice dialog** with options:
    -   Cancel (abort save)
    -   Overwrite (replace without backup)
    -   Backup & Overwrite (create backup then save)
-   **Data integrity** checks with checksums

### File Management:

-   **JSON export** with metadata (version, timestamp, checksum)
-   **Structured file format** with configuration and metadata sections
-   **Automatic downloads** with timestamped filenames
-   **Import validation** with error handling

### Storage Features:

-   **localStorage persistence** for instant recovery
-   **Cross-session data retention**
-   **Corrupted data recovery**
-   **Development debug tools** for testing

### UI Enhancements:

-   **Save status indicator** (✅ Saved / 📝 Unsaved changes)
-   **Modal dialogs** for backup management
-   **Progress animations** for save operations
-   **Toast notifications** for user feedback
-   **Keyboard shortcuts** (Ctrl/Cmd + S to save)

This enhanced system ensures no data loss and provides professional version control for deployment configurations!
