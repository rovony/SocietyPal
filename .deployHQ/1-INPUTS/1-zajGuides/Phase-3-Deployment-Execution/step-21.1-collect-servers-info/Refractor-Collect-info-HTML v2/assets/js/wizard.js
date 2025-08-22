// wizard.js - Step navigation and form management

/**
 * Main Wizard Application
 */
class WizardApp {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 7;
        this.configuration = {};
        this.initialized = false;
    }

    /**
     * Initialize the wizard
     */
    init() {
        if (this.initialized) return;

        this.loadFromLocalStorage();
        this.restoreSavedStep();
        this.updateUI();
        this.setDefaultValues();
        this.initialized = true;

        console.log("Wizard initialized");
    }

    /**
     * Load saved configuration from localStorage
     */
    loadFromLocalStorage() {
        const saved = localStorage.getItem("deploymentConfig");
        if (saved) {
            try {
                this.configuration = JSON.parse(saved);
                this.populateFormFields();
            } catch (error) {
                console.error("Error loading saved configuration:", error);
            }
        }
    }

    /**
     * Restore saved step position
     */
    restoreSavedStep() {
        const savedStep = localStorage.getItem("deploymentConfigStep");
        if (savedStep && !isNaN(savedStep)) {
            const step = parseInt(savedStep);
            if (step > 1 && step <= this.totalSteps) {
                this.navigateToStep(step);
            }
        }
    }

    /**
     * Populate form fields with saved data
     */
    populateFormFields() {
        Object.keys(this.configuration).forEach((key) => {
            setFieldValue(
                key,
                this.configuration[key],
                document.getElementById(key)?.type === "checkbox"
            );
        });
    }

    /**
     * Set default values
     */
    setDefaultValues() {
        const savePath = document.getElementById("savePath");
        const finalSavePath = document.getElementById("finalSavePath");
        const defaultPath = "Admin-Local/1-Current-Project/1-secrets";

        if (savePath && !savePath.value) {
            savePath.value = defaultPath;
        }
        if (finalSavePath && !finalSavePath.value) {
            finalSavePath.value = savePath?.value || defaultPath;
        }
    }

    /**
     * Navigate to a specific step
     * @param {number} stepNumber - Target step number
     */
    navigateToStep(stepNumber) {
        if (stepNumber < 1 || stepNumber > this.totalSteps) return;

        // Hide current step
        const currentStepElement = document.getElementById(
            `step${this.currentStep}`
        );
        if (currentStepElement) {
            currentStepElement.classList.remove("active");
        }

        // Update step number
        this.currentStep = stepNumber;

        // Show new step
        const newStepElement = document.getElementById(
            `step${this.currentStep}`
        );
        if (newStepElement) {
            newStepElement.classList.add("active");
        }

        // Save step position
        localStorage.setItem(
            "deploymentConfigStep",
            this.currentStep.toString()
        );

        // Update UI
        this.updateUI();

        // Generate preview if on final step
        if (this.currentStep === this.totalSteps) {
            this.generatePreview();
        }
    }

    /**
     * Change step (next/previous) with improved auto-save and debugging
     * @param {number} direction - 1 for next, -1 for previous
     */
    async changeStep(direction) {
        console.log(
            `Attempting to change step. Current: ${this.currentStep}, Direction: ${direction}`
        );

        if (direction === 1 && !validateCurrentStep(this.currentStep)) {
            console.log("Validation failed for step", this.currentStep);
            showToast("Please fill in all required fields", "error");
            return;
        }

        console.log("Validation passed, collecting step data...");

        // Collect current step data before saving
        this.collectCurrentStepData();

        console.log("Step data collected, attempting auto-save...");

        // Save current step data with auto-save (no modal prompts)
        if (window.storageManager) {
            try {
                await window.storageManager.autoSave(
                    this.configuration,
                    this.currentStep
                );
                console.log("Auto-save completed successfully");
            } catch (error) {
                console.error("Auto-save error during step change:", error);
                // Don't block navigation for save errors - just log them
            }
        }

        console.log(`Navigating to step ${this.currentStep + direction}`);

        // Navigate to new step
        this.navigateToStep(this.currentStep + direction);
    }

    /**
     * Collect current step data into configuration
     */
    collectCurrentStepData() {
        const currentStepElement = document.getElementById(
            `step${this.currentStep}`
        );
        if (!currentStepElement) return;

        // Collect all form fields from current step
        const inputs = currentStepElement.querySelectorAll(
            "input, select, textarea"
        );
        inputs.forEach((input) => {
            if (input.type === "checkbox") {
                this.configuration[input.id] = input.checked;
            } else if (input.type === "radio") {
                if (input.checked) {
                    this.configuration[input.name] = input.value;
                }
            } else {
                this.configuration[input.id] = input.value;
            }
        });

        // Also save to localStorage immediately
        localStorage.setItem(
            "deploymentConfig",
            JSON.stringify(this.configuration)
        );
    }

    /**
     * Update UI elements (progress bar, buttons, indicators)
     */
    updateUI() {
        this.updateProgressBar();
        this.updateButtons();
        this.updateStepIndicators();
    }

    /**
     * Update progress bar
     */
    updateProgressBar() {
        const progress = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
        const progressFill = document.getElementById("progressFill");
        if (progressFill) {
            progressFill.style.width = `${progress}%`;
        }
    }

    /**
     * Update navigation buttons
     */
    updateButtons() {
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");

        if (prevBtn) {
            prevBtn.disabled = this.currentStep === 1;
        }

        if (nextBtn) {
            if (this.currentStep === this.totalSteps) {
                nextBtn.style.display = "none";
            } else {
                nextBtn.style.display = "block";
                nextBtn.innerHTML =
                    this.currentStep === this.totalSteps - 1
                        ? "Review →"
                        : "Next →";
            }
        }
    }

    /**
     * Update step indicators
     */
    updateStepIndicators() {
        document.querySelectorAll(".progress-step").forEach((step, index) => {
            const stepNum = index + 1;
            if (stepNum < this.currentStep) {
                step.classList.add("completed");
                step.classList.remove("active");
            } else if (stepNum === this.currentStep) {
                step.classList.add("active");
                step.classList.remove("completed");
            } else {
                step.classList.remove("active", "completed");
            }
        });
    }

    /**
     * Save current step data with auto-save (non-blocking)
     */
    async saveStepData() {
        // Use the newer collectCurrentStepData method
        this.collectCurrentStepData();

        // Auto-save through storage manager (non-blocking)
        if (window.storageManager) {
            try {
                await window.storageManager.autoSave(
                    this.configuration,
                    this.currentStep
                );
            } catch (error) {
                console.error("Auto-save error in saveStepData:", error);
                // Don't block the UI for save errors
            }
        }
    }

    /**
     * Generate JSON configuration
     * @returns {object} Configuration object
     */
    generateJSON() {
        this.saveStepData();

        const config = {
            project: {
                name: this.configuration.projectName || "",
                environment: this.configuration.environment || "",
                deploymentScenario: this.configuration.deploymentScenario || "",
                savePath:
                    this.configuration.finalSavePath ||
                    this.configuration.savePath ||
                    "Admin-Local/1-Current-Project/1-secrets",
            },
            server: {
                access: {
                    host: this.configuration.serverHost || "",
                    username: this.configuration.sshUsername || "",
                    port: parseInt(this.configuration.sshPort) || 22,
                    keySecured: this.configuration.sshKeySecured || false,
                    keyAdded: this.configuration.sshKeyAdded || false,
                },
                environment: {
                    provider: this.configuration.hostingProvider || "",
                    os: this.configuration.serverOS || "",
                    webServer: this.configuration.webServer || "",
                    phpVersion: this.configuration.phpVersion || "",
                    nodeVersion: this.configuration.nodeVersion || "",
                },
            },
            domain: {
                production: this.configuration.productionDomain || "",
                staging: this.configuration.stagingDomain || "",
                development: this.configuration.developmentDomain || "",
                ssl: {
                    provider: this.configuration.sslProvider || "",
                    autoRenewal: this.configuration.sslAutoRenewal || false,
                    active: this.configuration.sslActive || false,
                },
            },
            database: {
                type: this.configuration.dbType || "",
                host: this.configuration.dbHost || "",
                port: parseInt(this.configuration.dbPort) || 3306,
                name: this.configuration.dbName || "",
                username: this.configuration.dbUsername || "",
                password: this.configuration.dbPassword || "",
                adminAccess: this.configuration.dbAdminAccess || "",
                backupStrategy: this.configuration.dbBackupStrategy || "",
            },
            directories: {
                documentRoot: this.configuration.documentRoot || "",
                projectDirectory: this.configuration.projectDirectory || "",
                sharedStorage: this.configuration.sharedStorage || "",
                permissions: {
                    webUser: this.configuration.webUser || "",
                    fileOwner: this.configuration.fileOwner || "",
                    group: this.configuration.fileGroup || "",
                },
            },
            security: {
                appKeyGenerated: this.configuration.appKeyGenerated || false,
                jwtSecret: this.configuration.jwtSecret || "",
                services: {
                    email: this.configuration.emailService || "",
                    payment: this.configuration.paymentGateway || "",
                    storage: this.configuration.storageService || "",
                    cdn: this.configuration.cdnService || "",
                },
            },
            metadata: {
                createdAt: new Date().toISOString(),
                version: "1.0.0",
            },
        };

        const jsonString = JSON.stringify(config, null, 2);
        const preview = document.getElementById("jsonPreview");
        if (preview) {
            preview.textContent = jsonString;
        }

        showToast("Configuration generated successfully!", "success");
        return config;
    }

    /**
     * Generate preview for review step
     */
    generatePreview() {
        this.generateJSON();
    }

    /**
     * Import configuration from file
     * @param {object} importedConfig - Imported configuration object
     */
    importConfiguration(importedConfig) {
        try {
            // Map imported data to form fields
            this.mapImportedData(importedConfig);
            this.saveStepData();
            showToast("Configuration imported successfully!", "success");
        } catch (error) {
            console.error("Import error:", error);
            showToast(
                "Failed to import configuration: " + error.message,
                "error"
            );
        }
    }

    /**
     * Map imported data to configuration object
     * @param {object} imported - Imported configuration
     */
    mapImportedData(imported) {
        if (imported.project) {
            setFieldValue("projectName", imported.project.name);
            setFieldValue("environment", imported.project.environment);
            setFieldValue(
                "deploymentScenario",
                imported.project.deploymentScenario
            );
            setFieldValue("savePath", imported.project.savePath);
        }

        if (imported.server) {
            setFieldValue("serverHost", imported.server.access.host);
            setFieldValue("sshUsername", imported.server.access.username);
            setFieldValue("sshPort", imported.server.access.port);
            setFieldValue(
                "sshKeySecured",
                imported.server.access.keySecured,
                true
            );
            setFieldValue("sshKeyAdded", imported.server.access.keyAdded, true);
            setFieldValue(
                "hostingProvider",
                imported.server.environment.provider
            );
            setFieldValue("serverOS", imported.server.environment.os);
            setFieldValue("webServer", imported.server.environment.webServer);
            setFieldValue("phpVersion", imported.server.environment.phpVersion);
            setFieldValue(
                "nodeVersion",
                imported.server.environment.nodeVersion
            );
        }

        if (imported.domain) {
            setFieldValue("productionDomain", imported.domain.production);
            setFieldValue("stagingDomain", imported.domain.staging);
            setFieldValue("developmentDomain", imported.domain.development);
            setFieldValue("sslProvider", imported.domain.ssl.provider);
            setFieldValue(
                "sslAutoRenewal",
                imported.domain.ssl.autoRenewal,
                true
            );
            setFieldValue("sslActive", imported.domain.ssl.active, true);
        }

        if (imported.database) {
            setFieldValue("dbType", imported.database.type);
            setFieldValue("dbHost", imported.database.host);
            setFieldValue("dbPort", imported.database.port);
            setFieldValue("dbName", imported.database.name);
            setFieldValue("dbUsername", imported.database.username);
            setFieldValue("dbPassword", imported.database.password);
            setFieldValue("dbAdminAccess", imported.database.adminAccess);
            setFieldValue("dbBackupStrategy", imported.database.backupStrategy);
        }

        if (imported.directories) {
            setFieldValue("documentRoot", imported.directories.documentRoot);
            setFieldValue(
                "projectDirectory",
                imported.directories.projectDirectory
            );
            setFieldValue("sharedStorage", imported.directories.sharedStorage);
            setFieldValue("webUser", imported.directories.permissions?.webUser);
            setFieldValue(
                "fileOwner",
                imported.directories.permissions?.fileOwner
            );
            setFieldValue("fileGroup", imported.directories.permissions?.group);
        }

        if (imported.security) {
            setFieldValue(
                "appKeyGenerated",
                imported.security.appKeyGenerated,
                true
            );
            setFieldValue("jwtSecret", imported.security.jwtSecret);
            setFieldValue("emailService", imported.security.services?.email);
            setFieldValue(
                "paymentGateway",
                imported.security.services?.payment
            );
            setFieldValue(
                "storageService",
                imported.security.services?.storage
            );
            setFieldValue("cdnService", imported.security.services?.cdn);
        }
    }
}

// Global wizard instance
window.WizardApp = new WizardApp();

// Global functions for backward compatibility
async function changeStep(direction) {
    await window.WizardApp.changeStep(direction);
}

function generateJSON() {
    return window.WizardApp.generateJSON();
}
