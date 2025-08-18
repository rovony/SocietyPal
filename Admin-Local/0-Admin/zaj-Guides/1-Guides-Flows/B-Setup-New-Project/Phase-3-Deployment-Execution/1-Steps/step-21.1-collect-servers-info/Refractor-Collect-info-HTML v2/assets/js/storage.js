// storage.js - JSON persistence with backup and version control

/**
 * Storage Manager for deployment configuration
 * Handles JSON saves, backups, and version control
 */
class StorageManager {
    constructor() {
        this.baseFileName = "deployment-config";
        this.backupFolder = "backup-versions";
        this.maxBackups = 10;
        this.currentConfig = null;
        this.lastSavedConfig = null;
        this.defaultSavePath = "Admin-Local/1-Current-Project/1-secrets";
    }

    /**
     * Get the user-specified save path from the form
     * @returns {string} Save path
     */
    getSavePath() {
        const savePathField =
            document.getElementById("savePath") ||
            document.getElementById("finalSavePath");
        return savePathField
            ? savePathField.value.trim()
            : this.defaultSavePath;
    }

    /**
     * Get the full file path for saving
     * @returns {string} Full file path
     */
    getFullFilePath() {
        const savePath = this.getSavePath();
        return `${savePath}/${this.baseFileName}.json`;
    }

    /**
     * Get backup file path
     * @param {string} timestamp - Backup timestamp
     * @returns {string} Backup file path
     */
    getBackupFilePath(timestamp) {
        const savePath = this.getSavePath();
        return `${savePath}/${this.backupFolder}/${this.baseFileName}-${timestamp}.json`;
    }

    /**
     * Initialize storage manager
     */
    init() {
        this.loadExistingConfig();
        this.createBackupFolder();
    }

    /**
     * Load existing configuration from localStorage
     */
    loadExistingConfig() {
        const saved = localStorage.getItem("deploymentConfig");
        if (saved) {
            try {
                this.lastSavedConfig = JSON.parse(saved);
                this.currentConfig = { ...this.lastSavedConfig };
            } catch (error) {
                console.error("Error loading saved configuration:", error);
            }
        }
    }

    /**
     * Create backup folder structure and ensure path exists
     */
    createBackupFolder() {
        const savePath = this.getSavePath();
        const backupPath = `${savePath}/${this.backupFolder}`;

        // Store the backup path for later use
        localStorage.setItem("deploymentConfigBackupPath", backupPath);

        // Create the backup folder structure indicator
        const folderIndicatorKey = `deploymentConfigFolder_${btoa(backupPath)}`;
        localStorage.setItem(
            folderIndicatorKey,
            JSON.stringify({
                path: backupPath,
                created: new Date().toISOString(),
                type: "backup-folder",
            })
        );

        console.log(`Backup system initialized for path: ${backupPath}`);
        console.log(`Main config will be saved to: ${this.getFullFilePath()}`);

        // Update UI to show the backup path
        this.updateBackupPathDisplay(backupPath);
    }

    /**
     * Update UI to show backup path information
     * @param {string} backupPath - Backup path to display
     */
    updateBackupPathDisplay(backupPath) {
        // Update save status to show backup path
        const saveStatus = document.getElementById("saveStatus");
        if (saveStatus) {
            const pathDisplay = saveStatus.querySelector("small");
            if (pathDisplay) {
                pathDisplay.innerHTML = `📁 ${this.getSavePath()}<br>🗂️ Backups: ${backupPath}`;
            }
        }
    }

    /**
     * Auto-save configuration on step change with automatic backup
     * @param {Object} config - Current configuration object
     * @param {number} currentStep - Current wizard step
     * @returns {Promise<boolean>} Success status
     */
    async autoSave(config, currentStep) {
        this.currentConfig = { ...config, lastStep: currentStep };

        try {
            // Save to localStorage immediately
            localStorage.setItem(
                "deploymentConfig",
                JSON.stringify(this.currentConfig)
            );
            localStorage.setItem(
                "deploymentConfigStep",
                currentStep.toString()
            );

            // Check if we need to save to file system
            if (this.hasSignificantChanges()) {
                return await this.performAutoSave(); // Auto-save with automatic backup
            }

            return true;
        } catch (error) {
            console.error("Auto-save failed:", error);
            showToast("Auto-save failed", "error");
            return false;
        }
    }

    /**
     * Perform automatic save with backup - no user prompts
     * @returns {Promise<boolean>} Success status
     */
    async performAutoSave() {
        const config = this.currentConfig || this.getCurrentFormData();

        try {
            // Check if file exists and if content differs
            const existingConfig = await this.loadFromFile();

            if (existingConfig) {
                const hasChanges = this.hasChanges(config, existingConfig);

                if (hasChanges) {
                    // Automatically create backup without asking
                    await this.createAutoBackup(existingConfig);
                }
            }

            // Save the configuration
            const success = await this.writeJSONFile(config);

            if (success) {
                this.lastSavedConfig = { ...config };
                this.updateSaveStatus(true);

                // Show subtle notification without blocking navigation
                this.showQuietNotification("Auto-saved", "success");
            }

            return success;
        } catch (error) {
            console.error("Auto-save failed:", error);
            this.showQuietNotification("Auto-save failed", "error");
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
            const savePath = this.getSavePath();
            const backupPath = this.getBackupFilePath(timestamp);
            const backupKey = `deploymentConfigBackup_${btoa(
                savePath
            )}_${timestamp}`;

            const backupData = {
                timestamp,
                originalFilename: `${this.baseFileName}.json`,
                originalPath: this.getFullFilePath(),
                backupPath: backupPath,
                savePath: savePath,
                configuration: existingConfig,
                autoBackup: true, // Mark as auto-backup
            };

            localStorage.setItem(backupKey, JSON.stringify(backupData));

            // Clean up old backups for this specific path
            await this.cleanupOldBackups();

            console.log(`Auto-backup created: ${backupPath}`);
            return true;
        } catch (error) {
            console.error("Auto-backup creation failed:", error);
            return false;
        }
    }

    /**
     * Show quiet notification that doesn't block navigation
     * @param {string} message - Notification message
     * @param {string} type - Notification type
     */
    showQuietNotification(message, type = "info") {
        // Use the existing toast system but make it less intrusive
        if (window.showToast) {
            showToast(message, type, 1000); // Short duration
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }

    /**
     * Save configuration to JSON file with backup handling
     * @param {boolean} forceOverwrite - Whether to skip overwrite confirmation
     * @returns {Promise<boolean>} Success status
     */
    async saveToFile(forceOverwrite = false) {
        const config = this.currentConfig || this.getCurrentFormData();

        try {
            // Check if file exists and if content differs
            const existingConfig = await this.loadFromFile();

            if (existingConfig && !forceOverwrite) {
                const hasChanges = this.hasChanges(config, existingConfig);

                if (hasChanges) {
                    const userChoice = await this.showOverwriteDialog();

                    if (userChoice === "cancel") {
                        return false;
                    } else if (userChoice === "backup") {
                        await this.createBackup(existingConfig);
                    }
                }
            }

            // Save the configuration
            const success = await this.writeJSONFile(config);

            if (success) {
                this.lastSavedConfig = { ...config };
                showToast("Configuration saved successfully!", "success");
                this.updateSaveStatus(true);
            }

            return success;
        } catch (error) {
            console.error("Save failed:", error);
            showToast("Failed to save configuration", "error");
            return false;
        }
    }

    /**
     * Load configuration from JSON file
     * @returns {Promise<Object|null>} Loaded configuration or null
     */
    async loadFromFile() {
        try {
            const filePath = this.getFullFilePath();
            const fileKey = `deploymentConfigFile_${btoa(filePath)}`;

            // In a real implementation, this would read from the file system at the specified path
            // For now, simulate with localStorage using path-specific keys
            const saved = localStorage.getItem(fileKey);

            if (saved) {
                const fileData = JSON.parse(saved);
                console.log(`Loaded configuration from: ${filePath}`);
                return fileData.configuration || fileData; // Handle both old and new formats
            }

            return null;
        } catch (error) {
            console.error("Load failed:", error);
            return null;
        }
    }

    /**
     * Write JSON configuration to file
     * @param {Object} config - Configuration to save
     * @returns {Promise<boolean>} Success status
     */
    async writeJSONFile(config) {
        try {
            const filePath = this.getFullFilePath();
            const fileKey = `deploymentConfigFile_${btoa(filePath)}`;
            const jsonString = JSON.stringify(config, null, 2);

            // Add metadata including save path
            const fileData = {
                metadata: {
                    version: "1.0.0",
                    created: new Date().toISOString(),
                    generator: "Deployment Configuration Wizard",
                    savePath: filePath,
                    checksum: this.generateChecksum(jsonString),
                },
                configuration: config,
            };

            // Save to localStorage with path-specific key
            localStorage.setItem(fileKey, JSON.stringify(fileData));

            // Also trigger download with path information
            this.downloadJSON(fileData, filePath);

            console.log(`Configuration saved to: ${filePath}`);
            return true;
        } catch (error) {
            console.error("Write failed:", error);
            return false;
        }
    }

    /**
     * Create backup of existing configuration
     * @param {Object} existingConfig - Configuration to backup
     * @returns {Promise<boolean>} Success status
     */
    async createBackup(existingConfig) {
        try {
            const timestamp = new Date().toISOString().replace(/[:.]/g, "-");
            const savePath = this.getSavePath();
            const backupPath = this.getBackupFilePath(timestamp);
            const backupKey = `deploymentConfigBackup_${btoa(
                savePath
            )}_${timestamp}`;

            const backupData = {
                timestamp,
                originalFilename: `${this.baseFileName}.json`,
                originalPath: this.getFullFilePath(),
                backupPath: backupPath,
                savePath: savePath,
                configuration: existingConfig,
            };

            localStorage.setItem(backupKey, JSON.stringify(backupData));

            // Clean up old backups for this specific path
            await this.cleanupOldBackups();

            showToast(`Backup created in: ${backupPath}`, "success");
            return true;
        } catch (error) {
            console.error("Backup creation failed:", error);
            showToast("Failed to create backup", "error");
            return false;
        }
    }

    /**
     * Clean up old backups to maintain the maximum limit
     */
    async cleanupOldBackups() {
        try {
            const currentPathEncoded = btoa(this.getSavePath());
            const backupKeys = Object.keys(localStorage)
                .filter((key) =>
                    key.startsWith(
                        `deploymentConfigBackup_${currentPathEncoded}_`
                    )
                )
                .sort()
                .reverse(); // Most recent first

            // Remove excess backups for this specific path
            if (backupKeys.length > this.maxBackups) {
                const keysToRemove = backupKeys.slice(this.maxBackups);
                keysToRemove.forEach((key) => localStorage.removeItem(key));

                console.log(
                    `Cleaned up ${
                        keysToRemove.length
                    } old backups for path: ${this.getSavePath()}`
                );
            }
        } catch (error) {
            console.error("Backup cleanup failed:", error);
        }
    }

    /**
     * Show overwrite confirmation dialog
     * @returns {Promise<string>} User choice: 'overwrite', 'backup', or 'cancel'
     */
    showOverwriteDialog() {
        return new Promise((resolve) => {
            // Create modal dialog
            const modal = document.createElement("div");
            modal.className = "modal-overlay";
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>⚠️ Configuration Exists</h3>
                    </div>
                    <div class="modal-body">
                        <p>A configuration file already exists with different content.</p>
                        <p>What would you like to do?</p>
                        <div class="backup-info">
                            <small>💡 Creating a backup will preserve the existing file before overwriting.</small>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button class="btn btn-secondary" onclick="resolveOverwrite('cancel')">
                            Cancel
                        </button>
                        <button class="btn btn-warning" onclick="resolveOverwrite('overwrite')">
                            Overwrite
                        </button>
                        <button class="btn btn-primary" onclick="resolveOverwrite('backup')">
                            Backup & Overwrite
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            // Store resolve function globally for button access
            window.resolveOverwrite = (choice) => {
                document.body.removeChild(modal);
                delete window.resolveOverwrite;
                resolve(choice);
            };
        });
    }

    /**
     * Check if current config has significant changes from last saved
     * @returns {boolean} Whether there are significant changes
     */
    hasSignificantChanges() {
        if (!this.lastSavedConfig) return true;
        return this.hasChanges(this.currentConfig, this.lastSavedConfig);
    }

    /**
     * Compare two configurations for changes
     * @param {Object} config1 - First configuration
     * @param {Object} config2 - Second configuration
     * @returns {boolean} Whether configurations differ
     */
    hasChanges(config1, config2) {
        if (!config1 || !config2) return true;

        // Deep comparison (simplified)
        const str1 = JSON.stringify(config1, Object.keys(config1).sort());
        const str2 = JSON.stringify(config2, Object.keys(config2).sort());

        return str1 !== str2;
    }

    /**
     * Get current form data
     * @returns {Object} Current form configuration
     */
    getCurrentFormData() {
        // This would collect all form data
        // Implementation depends on the wizard structure
        return window.wizard ? window.wizard.configuration : {};
    }

    /**
     * Generate checksum for data integrity
     * @param {string} data - Data to generate checksum for
     * @returns {string} Simple checksum
     */
    generateChecksum(data) {
        let hash = 0;
        for (let i = 0; i < data.length; i++) {
            const char = data.charCodeAt(i);
            hash = (hash << 5) - hash + char;
            hash = hash & hash; // Convert to 32bit integer
        }
        return hash.toString(16);
    }

    /**
     * Download JSON configuration
     * @param {Object} data - Data to download
     * @param {string} filePath - Optional file path for naming
     */
    downloadJSON(data, filePath = null) {
        const jsonString = JSON.stringify(data, null, 2);
        const blob = new Blob([jsonString], { type: "application/json" });
        const url = URL.createObjectURL(blob);

        // Create filename based on save path
        const savePath = filePath || this.getFullFilePath();
        const pathParts = savePath.split("/");
        const pathSuffix =
            pathParts.length > 1 ? `-${pathParts[pathParts.length - 2]}` : "";

        const a = document.createElement("a");
        a.href = url;
        a.download = `${this.baseFileName}${pathSuffix}-${
            new Date().toISOString().split("T")[0]
        }.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        console.log(`Downloaded configuration for path: ${savePath}`);
    }

    /**
     * Update save status indicator
     * @param {boolean} saved - Whether data is saved
     */
    updateSaveStatus(saved) {
        const indicator = document.getElementById("saveStatus");
        if (indicator) {
            const savePath = this.getSavePath();
            const pathDisplay =
                savePath.length > 30 ? `...${savePath.slice(-27)}` : savePath;

            indicator.innerHTML = saved
                ? `✅ Saved<br><small>📁 ${pathDisplay}</small>`
                : `📝 Unsaved<br><small>📁 ${pathDisplay}</small>`;
            indicator.className = saved
                ? "save-status saved"
                : "save-status unsaved";
        }
    }

    /**
     * Get backup history for current save path
     * @returns {Array} List of backups
     */
    getBackupHistory() {
        const currentPathEncoded = btoa(this.getSavePath());
        const backupKeys = Object.keys(localStorage)
            .filter((key) =>
                key.startsWith(`deploymentConfigBackup_${currentPathEncoded}_`)
            )
            .sort()
            .reverse();

        return backupKeys
            .map((key) => {
                try {
                    const backup = JSON.parse(localStorage.getItem(key));
                    return {
                        key,
                        timestamp: backup.timestamp,
                        filename: backup.originalFilename,
                        path: backup.backupPath || backup.originalPath,
                        savePath: backup.savePath,
                        size: JSON.stringify(backup.configuration).length,
                    };
                } catch (error) {
                    return null;
                }
            })
            .filter(Boolean);
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

            // Update localStorage and form
            localStorage.setItem(
                "deploymentConfig",
                JSON.stringify(this.currentConfig)
            );

            if (window.wizard) {
                window.wizard.configuration = { ...this.currentConfig };
                window.wizard.populateFormFields();
            }

            showToast("Configuration restored from backup", "success");
            return true;
        } catch (error) {
            console.error("Restore failed:", error);
            showToast("Failed to restore from backup", "error");
            return false;
        }
    }
}

// Initialize storage manager
window.storageManager = new StorageManager();
