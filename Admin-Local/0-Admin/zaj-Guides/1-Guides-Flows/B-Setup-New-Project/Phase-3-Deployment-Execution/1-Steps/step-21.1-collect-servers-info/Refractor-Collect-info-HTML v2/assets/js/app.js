// app.js - Main application initialization

/**
 * Application initialization
 */
document.addEventListener("DOMContentLoaded", function () {
    console.log("Deployment Configuration Wizard starting...");

    // Initialize storage manager first
    if (window.storageManager) {
        window.storageManager.init();
    }

    // Initialize wizard - WizardApp is already instantiated at the bottom of wizard.js
    if (window.WizardApp) {
        window.WizardApp.init();
    } else {
        console.error("WizardApp not found - check script loading order");
    }

    // Setup real-time validation
    setupRealTimeValidation();

    // Start auto-save
    startAutoSave();

    // Setup save path change listener
    setupSavePathListener();

    // Prevent form submission
    setupFormPrevention();

    console.log("Application initialized successfully");
});

/**
 * Setup save path change listener with enhanced backup folder creation
 */
function setupSavePathListener() {
    const savePathField = document.getElementById("savePath");
    if (savePathField) {
        savePathField.addEventListener("input", function () {
            // Update the final save path field to match
            const finalSavePathField = document.getElementById("finalSavePath");
            if (finalSavePathField) {
                finalSavePathField.value = this.value;
            }

            // Update storage manager backup path and create backup folder
            if (window.storageManager) {
                window.storageManager.createBackupFolder();
            }
        });

        // Also listen for blur event for final update
        savePathField.addEventListener("blur", function () {
            if (window.storageManager && this.value.trim()) {
                console.log(`Save path updated to: ${this.value.trim()}`);

                // Ensure backup folder is created for the new path
                window.storageManager.createBackupFolder();

                // Update any existing configuration with new path
                if (window.WizardApp && window.WizardApp.configuration) {
                    window.WizardApp.configuration.savePath = this.value.trim();
                }
            }
        });
    }
}

/**
 * Prevent any form submission that might cause page refresh
 */
function setupFormPrevention() {
    document.addEventListener("submit", function (e) {
        e.preventDefault();
        return false;
    });
}

/**
 * Import JSON configuration
 */
function importJSON() {
    const fileInput = document.getElementById("fileInput");
    if (fileInput) {
        fileInput.click();
    }
}

/**
 * Handle file import
 * @param {Event} event - File input change event
 */
function handleFileImport(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        try {
            const imported = JSON.parse(e.target.result);
            window.WizardApp.importConfiguration(imported);
        } catch (error) {
            showToast(
                "Failed to import configuration: Invalid JSON file",
                "error"
            );
            console.error("Import error:", error);
        }
    };
    reader.readAsText(file);

    // Reset file input
    event.target.value = "";
}

/**
 * Export JSON configuration
 */
function exportJSON() {
    try {
        if (window.storageManager) {
            window.storageManager.saveToFile(false);
        } else {
            downloadJSON();
        }
    } catch (error) {
        showToast("Failed to export configuration", "error");
        console.error("Export error:", error);
    }
}

/**
 * Clear all data
 */
function clearAll() {
    if (
        confirm(
            "Are you sure you want to clear all data? This cannot be undone."
        )
    ) {
        localStorage.clear();
        window.location.reload();
    }
}

/**
 * Handle keyboard shortcuts
 */
document.addEventListener("keydown", function (event) {
    // Ctrl/Cmd + S to save/export
    if ((event.ctrlKey || event.metaKey) && event.key === "s") {
        event.preventDefault();
        exportJSON();
        return false;
    }

    // Ctrl/Cmd + Enter to go to next step
    if ((event.ctrlKey || event.metaKey) && event.key === "Enter") {
        event.preventDefault();
        if (window.WizardApp.currentStep < window.WizardApp.totalSteps) {
            window.WizardApp.changeStep(1);
        }
        return false;
    }

    // Escape to go to previous step
    if (event.key === "Escape") {
        event.preventDefault();
        if (window.WizardApp.currentStep > 1) {
            window.WizardApp.changeStep(-1);
        }
        return false;
    }
});

/**
 * Setup real-time validation
 */
function setupRealTimeValidation() {
    document.addEventListener("input", function (event) {
        const field = event.target;
        if (
            field.tagName === "INPUT" ||
            field.tagName === "SELECT" ||
            field.tagName === "TEXTAREA"
        ) {
            validateField(field);
        }
    });

    document.addEventListener("blur", function (event) {
        const field = event.target;
        if (
            field.tagName === "INPUT" ||
            field.tagName === "SELECT" ||
            field.tagName === "TEXTAREA"
        ) {
            validateField(field);
        }
    });
}

/**
 * Start auto-save functionality
 */
function startAutoSave() {
    // Auto-save every 30 seconds
    setInterval(() => {
        if (window.WizardApp && window.storageManager) {
            window.WizardApp.saveStepData();
        }
    }, 30000);

    // Save before page unload
    window.addEventListener("beforeunload", function () {
        if (window.WizardApp) {
            window.WizardApp.saveStepData();
        }
    });

    // Monitor for performance
    window.addEventListener("load", function () {
        setTimeout(() => {
            const loadTime =
                performance.timing.loadEventEnd -
                performance.timing.navigationStart;
            console.log(`Page loaded in ${loadTime}ms`);
        }, 0);
    });
}

/**
 * Service worker registration (if available)
 */
if ("serviceWorker" in navigator) {
    window.addEventListener("load", function () {
        // Uncomment if you add a service worker
        // navigator.serviceWorker.register('/sw.js')
        //     .then(registration => console.log('SW registered'))
        //     .catch(error => console.log('SW registration failed'));
    });
}

/**
 * Debug helpers (only in development)
 */
if (
    window.location.hostname === "localhost" ||
    window.location.hostname === "127.0.0.1"
) {
    window.debugWizard = {
        getConfiguration: () => window.WizardApp.configuration,
        goToStep: (step) => window.WizardApp.navigateToStep(step),
        validateStep: (step) => validateCurrentStep(step),
        clearData: () => clearAll(),
        exportData: () => exportJSON(),
        importData: () => importJSON(),
    };

    console.log("Debug helpers available on window.debugWizard");
}

/**
 * Show backup manager modal
 */
function showBackupManager() {
    if (!window.storageManager) {
        showToast("Storage manager not available", "error");
        return;
    }

    const backups = window.storageManager.getBackupHistory();
    const currentPath = window.storageManager.getSavePath();

    const modal = document.createElement("div");
    modal.className = "modal-overlay";
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>🗂️ Backup Management</h3>
                <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
                    📁 Save Path: <code>${currentPath}</code>
                </p>
            </div>
            <div class="modal-body">
                <p>Manage your configuration backups for this path:</p>
                ${
                    backups.length === 0
                        ? `<p class="backup-details">No backups found for path: <code>${currentPath}</code></p>`
                        : `<div class="backup-list">
                        ${backups
                            .map(
                                (backup) => `
                            <div class="backup-item">
                                <div class="backup-info-item">
                                    <div class="backup-timestamp">${new Date(
                                        backup.timestamp
                                    ).toLocaleString()}</div>
                                    <div class="backup-details">
                                        ${backup.filename} (${Math.round(
                                    backup.size / 1024
                                )}KB)
                                        ${
                                            backup.path
                                                ? `<br><small>📁 ${backup.path}</small>`
                                                : ""
                                        }
                                    </div>
                                </div>
                                <div class="backup-actions">
                                    <button class="btn btn-sm btn-primary" onclick="restoreBackup('${
                                        backup.key
                                    }')">
                                        Restore
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="deleteBackup('${
                                        backup.key
                                    }')">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        `
                            )
                            .join("")}
                    </div>`
                }
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeBackupManager()">
                    Close
                </button>
                <button class="btn btn-primary" onclick="saveCurrentConfig()">
                    Save Current Config
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    modal.id = "backupManagerModal";
}

/**
 * Close backup manager modal
 */
function closeBackupManager() {
    const modal = document.getElementById("backupManagerModal");
    if (modal) {
        document.body.removeChild(modal);
    }
}

/**
 * Restore configuration from backup
 * @param {string} backupKey - Backup key to restore
 */
function restoreBackup(backupKey) {
    if (
        window.storageManager &&
        window.storageManager.restoreFromBackup(backupKey)
    ) {
        closeBackupManager();
        window.location.reload(); // Refresh to show restored data
    }
}

/**
 * Delete a backup
 * @param {string} backupKey - Backup key to delete
 */
function deleteBackup(backupKey) {
    if (confirm("Are you sure you want to delete this backup?")) {
        localStorage.removeItem(backupKey);
        showToast("Backup deleted", "success");

        // Refresh the backup manager
        closeBackupManager();
        showBackupManager();
    }
}

/**
 * Save current configuration
 */
async function saveCurrentConfig() {
    if (window.storageManager) {
        const success = await window.storageManager.saveToFile(false);
        if (success) {
            closeBackupManager();
        }
    }
}

/**
 * Update save path when user changes it
 */
function updateSavePath() {
    const finalSavePath = document.getElementById("finalSavePath");
    const savePath = document.getElementById("savePath");

    if (finalSavePath && savePath) {
        const newPath = finalSavePath.value.trim();
        if (newPath) {
            savePath.value = newPath;

            // Update storage manager
            if (window.storageManager) {
                window.storageManager.createBackupFolder();
                showToast(`Save path updated to: ${newPath}`, "success");
            }
        } else {
            showToast("Please enter a valid path", "error");
        }
    }
}
