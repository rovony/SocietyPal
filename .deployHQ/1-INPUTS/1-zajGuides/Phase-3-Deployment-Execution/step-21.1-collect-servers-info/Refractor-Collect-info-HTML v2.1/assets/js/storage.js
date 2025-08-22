// Enhanced storage.js - Auto-backup without modal prompts

/**
 * Enhanced Storage Manager for deployment configuration
 * Features:
 * - Auto-backup without modal prompts
 * - Path-based storage isolation
 * - Smart change detection
 * - Seamless navigation with auto-save
 */
class EnhancedStorageManager {
    constructor() {
        this.baseFileName = "deployment-config";
        this.backupFolder = "backup-versions";
        this.maxBackups = 10;
        this.currentConfig = null;
        this.lastSavedConfig = null;
        this.defaultSavePath = "Admin-Local/1-Current-Project/1-secrets";
        this.autoBackupEnabled = true;
        this.saveDebounceTimer = null;
        this.saveDebounceDelay = 1000; // 1 second
    }

    /**
     * Get the user-specified save path from the form
     * @returns {string} Save path
     */
    getSavePath() {
        const savePathField =
            document.getElementById("savePath") ||
            document.getElementById("finalSavePath");
        return savePathField?.value?.trim() || this.defaultSavePath;
    }

    /**
     * Generate storage key for localStorage based on path
     * @param {string} path - Save path
     * @returns {string} Storage key
     */
    getStorageKey(path) {
        return `deploymentConfig_${btoa(path || this.getSavePath())}`;
    }

    /**
     * Get backup storage key for specific path and timestamp
     * @param {string} path - Save path
     * @param {string} timestamp - Backup timestamp
     * @returns {string} Backup storage key
     */
    getBackupKey(path, timestamp) {
        return `backup_${btoa(path)}_${timestamp}`;
    }

    /**
     * Initialize storage manager
     */
    init() {
        this.loadExistingConfig();
        this.setupPathChangeListener();
        this.updateSaveStatus();
    }

    /**
     * Setup listener for save path changes
     */
    setupPathChangeListener() {
        const savePathField = document.getElementById("savePath");
        if (savePathField) {
            savePathField.addEventListener('input', () => {
                // Auto-save current data to old path before switching
                if (this.currentConfig) {
                    this.performAutoSave(this.currentConfig);
                }
                
                // Load config from new path
                setTimeout(() => {
                    this.loadExistingConfig();
                    this.updateSaveStatus();
                }, 100);
            });
        }
    }

    /**
     * Load existing configuration from localStorage for current path
     */
    loadExistingConfig() {
        const storageKey = this.getStorageKey();
        const saved = localStorage.getItem(storageKey);
        
        if (saved) {
            try {
                const data = JSON.parse(saved);
                this.lastSavedConfig = data.configuration || data;
                this.currentConfig = JSON.parse(JSON.stringify(this.lastSavedConfig));
                
                // Populate form if wizard exists
                if (window.wizard && typeof window.wizard.populateFormFields === 'function') {
                    window.wizard.configuration = this.currentConfig;
                    window.wizard.populateFormFields();
                }
                
                console.log(`Loaded config from path: ${this.getSavePath()}`);
            } catch (error) {
                console.error("Error loading saved configuration:", error);
            }
        } else {
            this.currentConfig = null;
            this.lastSavedConfig = null;
        }
    }

    /**
     * Auto-save configuration with smart debouncing
     * @param {Object} config - Current configuration object
     * @param {number} currentStep - Current wizard step
     * @returns {Promise<boolean>} Success status
     */
    async autoSave(config, currentStep) {
        // Clear existing debounce timer
        if (this.saveDebounceTimer) {
            clearTimeout(this.saveDebounceTimer);
        }

        // Update current config
        this.currentConfig = { ...config, lastStep: currentStep };

        // Show saving status immediately
        this.updateSaveStatus('saving');

        // Debounce the actual save operation
        return new Promise((resolve) => {
            this.saveDebounceTimer = setTimeout(async () => {
                const success = await this.performAutoSave(this.currentConfig);
                resolve(success);
            }, this.saveDebounceDelay);
        });
    }

    /**
     * Perform the actual auto-save operation
     * @param {Object} config - Configuration to save
     * @returns {Promise<boolean>} Success status
     */
    async performAutoSave(config) {
        try {
            const storageKey = this.getStorageKey();
            const existingData = localStorage.getItem(storageKey);
            let existingConfig = null;

            if (existingData) {
                try {
                    const parsed = JSON.parse(existingData);
                    existingConfig = parsed.configuration || parsed;
                } catch (e) {
                    console.warn("Could not parse existing config");
                }
            }

            // Check if we need to create a backup
            if (existingConfig && this.hasSignificantChanges(config, existingConfig)) {
                await this.createAutoBackup(existingConfig);
            }

            // Save new configuration
            const saveData = {
                metadata: {
                    version: "2.1.0",
                    created: new Date().toISOString(),
                    generator: "Site-Centric Deployment Wizard",
                    savePath: this.getSavePath(),
                    autoSaved: true,
                    checksum: this.generateChecksum(JSON.stringify(config))
                },
                configuration: config
            };

            localStorage.setItem(storageKey, JSON.stringify(saveData));
            this.lastSavedConfig = JSON.parse(JSON.stringify(config));

            this.updateSaveStatus('saved');
            console.log(`Auto-saved to path: ${this.getSavePath()}`);
            
            return true;
        } catch (error) {
            console.error("Auto-save failed:", error);
            this.updateSaveStatus('error');
            return false;
        }
    }

    /**
     * Create automatic backup without user prompts
     * @param {Object} existingConfig - Configuration to backup
     * @returns {Promise<boolean>} Success status
     */
    async createAutoBackup(existingConfig) {
        try {
            const timestamp = new Date().toISOString().replace(/[:.]/g, "-");
            const path = this.getSavePath();
            const backupKey = this.getBackupKey(path, timestamp);

            const backupData = {
                timestamp,
                originalPath: path,
                configuration: existingConfig,
                metadata: {
                    created: new Date().toISOString(),
                    reason: "auto-backup-before-overwrite"
                }
            };

            localStorage.setItem(backupKey, JSON.stringify(backupData));

            // Clean up old backups for this path
            await this.cleanupOldBackupsForPath(path);

            console.log(`Auto-backup created for path: ${path}`);
            return true;
        } catch (error) {
            console.error("Auto-backup creation failed:", error);
            return false;
        }
    }

    /**
     * Clean up old backups for a specific path
     * @param {string} path - Path to clean backups for
     */
    async cleanupOldBackupsForPath(path) {
        try {
            const pathPrefix = `backup_${btoa(path)}_`;
            const backupKeys = Object.keys(localStorage)
                .filter(key => key.startsWith(pathPrefix))
                .sort()
                .reverse(); // Most recent first

            if (backupKeys.length > this.maxBackups) {
                const keysToRemove = backupKeys.slice(this.maxBackups);
                keysToRemove.forEach(key => localStorage.removeItem(key));
                console.log(`Cleaned up ${keysToRemove.length} old backups for path: ${path}`);
            }
        } catch (error) {
            console.error("Backup cleanup failed:", error);
        }
    }

    /**
     * Manual save to file with download
     * @returns {Promise<boolean>} Success status
     */
    async saveToFile() {
        try {
            const config = this.currentConfig || this.getCurrentFormData();
            const filePath = this.getFullFilePath();
            
            const fileData = {
                metadata: {
                    version: "2.1.0",
                    created: new Date().toISOString(),
                    generator: "Site-Centric Deployment Wizard",
                    savePath: filePath,
                    checksum: this.generateChecksum(JSON.stringify(config))
                },
                configuration: config
            };

            // Download the file
            this.downloadJSON(fileData, filePath);

            // Also save to localStorage for persistence
            await this.performAutoSave(config);

            if (window.showToast) {
                showToast("Configuration exported successfully!", "success");
            }
            
            return true;
        } catch (error) {
            console.error("Manual save failed:", error);
            if (window.showToast) {
                showToast("Failed to save configuration", "error");
            }
            return false;
        }
    }

    /**
     * Check if config has significant changes
     * @param {Object} config1 - First configuration
     * @param {Object} config2 - Second configuration
     * @returns {boolean} Whether configurations differ significantly
     */
    hasSignificantChanges(config1, config2) {
        if (!config1 || !config2) return true;

        // Normalize configs by removing metadata and timestamps
        const normalize = (config) => {
            const normalized = { ...config };
            delete normalized.lastStep;
            delete normalized.metadata;
            delete normalized.created;
            delete normalized.updated;
            return normalized;
        };

        const str1 = JSON.stringify(normalize(config1), Object.keys(normalize(config1)).sort());
        const str2 = JSON.stringify(normalize(config2), Object.keys(normalize(config2)).sort());

        return str1 !== str2;
    }

    /**
     * Get current form data
     * @returns {Object} Current form configuration
     */
    getCurrentFormData() {
        return window.wizard?.configuration || {};
    }

    /**
     * Generate simple checksum
     * @param {string} data - Data to generate checksum for
     * @returns {string} Checksum
     */
    generateChecksum(data) {
        let hash = 0;
        for (let i = 0; i < data.length; i++) {
            const char = data.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32bit integer
        }
        return hash.toString(16).substring(0, 6);
    }

    /**
     * Get the full file path for saving
     * @returns {string} Full file path
     */
    getFullFilePath() {
        const savePath = this.getSavePath();
        const timestamp = new Date().toISOString().split('T')[0];
        return `${savePath}/${this.baseFileName}-${timestamp}.json`;
    }

    /**
     * Download JSON configuration
     * @param {Object} data - Data to download
     * @param {string} filePath - File path for naming
     */
    downloadJSON(data, filePath) {
        const jsonString = JSON.stringify(data, null, 2);
        const blob = new Blob([jsonString], { type: "application/json" });
        const url = URL.createObjectURL(blob);

        const pathParts = filePath.split("/");
        const fileName = pathParts[pathParts.length - 1];

        const a = document.createElement("a");
        a.href = url;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        console.log(`Downloaded: ${fileName}`);
    }

    /**
     * Update save status indicator
     * @param {string} status - Status: 'saved', 'saving', 'unsaved', 'error'
     */
    updateSaveStatus(status = 'saved') {
        const indicator = document.getElementById("saveStatus");
        if (!indicator) return;

        const savePath = this.getSavePath();
        const pathDisplay = savePath.length > 35 ? `...${savePath.slice(-32)}` : savePath;

        const statusConfig = {
            'saved': {
                icon: '✅',
                text: 'Saved',
                class: 'save-status saved'
            },
            'saving': {
                icon: '💾',
                text: 'Saving...',
                class: 'save-status saving'
            },
            'unsaved': {
                icon: '📝',
                text: 'Unsaved',
                class: 'save-status unsaved'
            },
            'error': {
                icon: '❌',
                text: 'Error',
                class: 'save-status error'
            }
        };

        const config = statusConfig[status] || statusConfig['saved'];
        
        indicator.innerHTML = `${config.icon} ${config.text}<br><small>📁 ${pathDisplay}</small>`;
        indicator.className = config.class;
    }

    /**
     * Get backup history for current save path
     * @returns {Array} List of backups
     */
    getBackupHistory() {
        const path = this.getSavePath();
        const pathPrefix = `backup_${btoa(path)}_`;
        
        return Object.keys(localStorage)
            .filter(key => key.startsWith(pathPrefix))
            .map(key => {
                try {
                    const backup = JSON.parse(localStorage.getItem(key));
                    return {
                        key,
                        timestamp: backup.timestamp,
                        path: backup.originalPath,
                        size: JSON.stringify(backup.configuration).length,
                        created: backup.metadata?.created,
                        reason: backup.metadata?.reason
                    };
                } catch (error) {
                    return null;
                }
            })
            .filter(Boolean)
            .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
    }

    /**
     * Restore from backup
     * @param {string} backupKey - Backup key to restore
     * @returns {boolean} Success status
     */
    restoreFromBackup(backupKey) {
        try {
            const backup = localStorage.getItem(backupKey);
            if (!backup) return false;

            const backupData = JSON.parse(backup);
            this.currentConfig = backupData.configuration;

            // Save restored config
            this.performAutoSave(this.currentConfig);

            // Update form if wizard exists
            if (window.wizard) {
                window.wizard.configuration = { ...this.currentConfig };
                if (typeof window.wizard.populateFormFields === 'function') {
                    window.wizard.populateFormFields();
                }
            }

            if (window.showToast) {
                showToast("Configuration restored from backup", "success");
            }
            
            return true;
        } catch (error) {
            console.error("Restore failed:", error);
            if (window.showToast) {
                showToast("Failed to restore from backup", "error");
            }
            return false;
        }
    }

    /**
     * Delete backup
     * @param {string} backupKey - Backup key to delete
     * @returns {boolean} Success status
     */
    deleteBackup(backupKey) {
        try {
            localStorage.removeItem(backupKey);
            console.log(`Deleted backup: ${backupKey}`);
            return true;
        } catch (error) {
            console.error("Delete backup failed:", error);
            return false;
        }
    }

    /**
     * Import configuration from file
     * @param {File} file - JSON file to import
     * @returns {Promise<boolean>} Success status
     */
    async importFromFile(file) {
        try {
            const text = await file.text();
            const data = JSON.parse(text);
            
            // Extract configuration from various formats
            const config = data.configuration || data.config || data;
            
            this.currentConfig = config;

            // Save imported config
            await this.performAutoSave(config);

            // Update form
            if (window.wizard) {
                window.wizard.configuration = { ...config };
                if (typeof window.wizard.populateFormFields === 'function') {
                    window.wizard.populateFormFields();
                }
            }

            if (window.showToast) {
                showToast("Configuration imported successfully!", "success");
            }
            
            return true;
        } catch (error) {
            console.error("Import failed:", error);
            if (window.showToast) {
                showToast("Failed to import configuration", "error");
            }
            return false;
        }
    }
}

// Initialize enhanced storage manager
window.storageManager = new EnhancedStorageManager();