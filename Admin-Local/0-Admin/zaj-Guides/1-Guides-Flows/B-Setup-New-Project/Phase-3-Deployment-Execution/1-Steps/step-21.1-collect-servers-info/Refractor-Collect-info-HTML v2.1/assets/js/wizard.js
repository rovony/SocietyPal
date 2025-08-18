// wizard.js - Enhanced Step navigation and form management for v2.1

/**
 * Main Wizard Application - Enhanced for Site-Centric Management
 */
class WizardApp {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 24; // Extended to include Steps 22-24
        this.configuration = {};
        this.initialized = false;
        this.siteCentricMode = true;
    }

    /**
     * Initialize the wizard
     */
    init() {
        if (this.initialized) return;

        // Initialize site-centric manager if not already done
        if (this.siteCentricMode && window.siteCentricManager) {
            window.siteCentricManager.init();
            this.loadFromSiteCentricManager();
        } else {
            this.loadFromLocalStorage();
        }

        this.restoreSavedStep();
        this.updateUI();
        this.setDefaultValues();
        this.setupEventListeners();
        this.initialized = true;

        console.log(
            "Wizard v2.1 initialized with site-centric mode:",
            this.siteCentricMode
        );
    }

    /**
     * Load configuration from site-centric manager
     */
    loadFromSiteCentricManager() {
        if (!window.siteCentricManager) return;

        const deployment = window.siteCentricManager.getCurrentDeployment();
        if (deployment) {
            // Map site-centric deployment to wizard configuration
            this.configuration = {
                ...deployment.config.hosting,
                ...deployment.config.deployment,
                ...deployment.config.database,
                hostingProvider: deployment.hostingProvider,
                deploymentScenario: deployment.deploymentStrategy,
                environment: deployment.environment,
                domain: deployment.domain,
                branch: deployment.branch,
            };

            this.populateFormFields();
        }
    }

    /**
     * Load saved configuration from localStorage (legacy mode)
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
     * Save configuration to appropriate storage
     */
    saveConfiguration() {
        if (this.siteCentricMode && window.siteCentricManager) {
            this.saveToSiteCentricManager();
        } else {
            this.saveToLocalStorage();
        }

        // Also trigger auto-backup through storage manager
        if (window.storageManager) {
            const data =
                this.siteCentricMode && window.siteCentricManager
                    ? window.siteCentricManager.getProjectForExport()
                    : this.configuration;
            window.storageManager.autoSave(data, this.currentStep);
        }
    }

    /**
     * Save to site-centric manager
     */
    saveToSiteCentricManager() {
        if (!window.siteCentricManager) return;

        const site = window.siteCentricManager.getCurrentSite();
        const deployment = window.siteCentricManager.getCurrentDeployment();

        if (!site || !deployment) return;

        // Update deployment configuration
        window.siteCentricManager.updateDeploymentConfig(
            site.id,
            deployment.id,
            {
                hostingProvider: this.configuration.hostingProvider,
                deploymentStrategy: this.configuration.deploymentScenario,
                domain: this.configuration.domain,
                branch: this.configuration.branch,
                hostingConfig: this.extractHostingConfig(),
                deploymentConfig: this.extractDeploymentConfig(),
                databaseConfig: this.extractDatabaseConfig(),
            }
        );
    }

    /**
     * Save to localStorage (legacy mode)
     */
    saveToLocalStorage() {
        localStorage.setItem(
            "deploymentConfig",
            JSON.stringify(this.configuration)
        );
        localStorage.setItem(
            "deploymentConfigStep",
            this.currentStep.toString()
        );
    }

    /**
     * Extract hosting configuration from form
     */
    extractHostingConfig() {
        const hostingFields = [
            "serverHost",
            "sshUsername",
            "sshPort",
            "documentRoot",
            "webServer",
            "phpVersion",
            "sslProvider",
        ];

        const config = {};
        hostingFields.forEach((field) => {
            if (this.configuration[field] !== undefined) {
                config[field] = this.configuration[field];
            }
        });

        return config;
    }

    /**
     * Extract deployment configuration from form
     */
    extractDeploymentConfig() {
        const deploymentFields = [
            "repository",
            "branch",
            "buildCommand",
            "triggerType",
            "postDeployCommands",
        ];

        const config = {};
        deploymentFields.forEach((field) => {
            if (this.configuration[field] !== undefined) {
                config[field] = this.configuration[field];
            }
        });

        return config;
    }

    /**
     * Extract database configuration from form
     */
    extractDatabaseConfig() {
        const databaseFields = [
            "dbType",
            "dbHost",
            "dbPort",
            "dbName",
            "dbUsername",
            "dbPassword",
        ];

        const config = {};
        databaseFields.forEach((field) => {
            if (this.configuration[field] !== undefined) {
                config[field] = this.configuration[field];
            }
        });

        return config;
    }

    /**
     * Collect current step form data
     */
    collectCurrentStepData() {
        // Get all form inputs in the current step
        const currentStepElement = document.querySelector(
            `#step${this.currentStep}`
        );
        if (!currentStepElement) return;

        const inputs = currentStepElement.querySelectorAll(
            "input, select, textarea"
        );
        inputs.forEach((input) => {
            const value =
                input.type === "checkbox" ? input.checked : input.value;
            if (input.id && value !== "") {
                this.configuration[input.id] = value;
            }
        });
    }

    /**
     * Setup enhanced event listeners
     */
    setupEventListeners() {
        // Form field change listeners with auto-save
        document.addEventListener("input", (e) => {
            if (e.target.matches("input, select, textarea")) {
                this.handleFieldChange(e.target);
            }
        });

        // Provider/strategy change listeners for dynamic forms
        document.addEventListener("change", (e) => {
            if (e.target.id === "hostingProvider") {
                this.handleHostingProviderChange(e.target.value);
            }

            if (e.target.id === "deploymentScenario") {
                this.handleDeploymentStrategyChange(e.target.value);
            }
        });

        // Site context change listener
        document.addEventListener("siteContextChanged", (e) => {
            this.handleSiteContextChange(e.detail);
        });
    }

    /**
     * Handle form field changes with auto-save
     */
    handleFieldChange(field) {
        const fieldId = field.id;
        const value = field.type === "checkbox" ? field.checked : field.value;

        this.configuration[fieldId] = value;
        this.saveConfiguration();
    }

    /**
     * Handle hosting provider change with dynamic form update
     */
    handleHostingProviderChange(provider) {
        this.configuration.hostingProvider = provider;

        // Update form fields dynamically using template engine
        if (window.templateEngine && this.currentStep >= 2) {
            this.updateDynamicFields("hosting", provider);
        }

        this.saveConfiguration();
    }

    /**
     * Handle deployment strategy change with dynamic form update
     */
    handleDeploymentStrategyChange(strategy) {
        this.configuration.deploymentScenario = strategy;

        // Update form fields dynamically using template engine
        if (window.templateEngine && this.currentStep >= 5) {
            this.updateDynamicFields("deployment", strategy);
        }

        this.saveConfiguration();
    }

    /**
     * Update dynamic form fields based on template
     */
    updateDynamicFields(type, selection) {
        const container = document.querySelector(`#${type}-dynamic-fields`);
        if (!container) return;

        let fields;
        if (type === "hosting") {
            fields = window.templateEngine.generateHostingFields(selection);
        } else if (type === "deployment") {
            fields = window.templateEngine.generateDeploymentFields(selection);
        }

        if (fields) {
            container.innerHTML = window.templateEngine.renderFormFields(
                fields,
                this.configuration
            );
        }
    }

    /**
     * Handle site context changes
     */
    handleSiteContextChange(detail) {
        if (this.siteCentricMode) {
            this.loadFromSiteCentricManager();
        }
    }

    /**
     * Restore saved step position
     */
    restoreSavedStep() {
        let savedStep;

        if (this.siteCentricMode && window.siteCentricManager) {
            // In site-centric mode, start from step 1 (skip step 0)
            savedStep = 1;
        } else {
            savedStep = localStorage.getItem("deploymentConfigStep");
        }

        if (savedStep && !isNaN(savedStep)) {
            const step = parseInt(savedStep);
            if (step >= 1 && step <= this.totalSteps) {
                this.currentStep = step;
            }
        }
    }

    /**
     * Collect current step form data
     */
    collectCurrentStepData() {
        // Get all form inputs in the current step
        const currentStepElement = document.querySelector(
            `#step${this.currentStep}`
        );
        if (!currentStepElement) return;

        const inputs = currentStepElement.querySelectorAll(
            "input, select, textarea"
        );
        inputs.forEach((input) => {
            const value =
                input.type === "checkbox" ? input.checked : input.value;
            if (input.id && value !== "") {
                this.configuration[input.id] = value;
            }
        });
    }

    /**
     * Navigate to specific step
     */
    goToStep(stepNumber, skipValidation = false) {
        if (stepNumber < 1 || stepNumber > this.totalSteps) return;

        // Validate current step before proceeding (unless skipped)
        if (!skipValidation && !this.validateCurrentStep()) {
            return;
        }

        // Save current state
        this.collectCurrentStepData();
        this.saveConfiguration();

        // Hide current step
        const currentStepElement = document.querySelector(
            `#step${this.currentStep}`
        );
        if (currentStepElement) {
            currentStepElement.style.display = "none";
        }

        // Update step number
        this.currentStep = stepNumber;

        // Show new step
        const newStepElement = document.querySelector(
            `#step${this.currentStep}`
        );
        if (newStepElement) {
            newStepElement.style.display = "block";
            this.populateCurrentStepFields();
        }

        // Update UI
        this.updateUI();
        this.updateProgress();

        // Handle special steps
        if (this.currentStep >= 22) {
            this.handlePostDeploymentSteps();
        }

        console.log(`Navigated to step ${this.currentStep}`);
    }

    /**
     * Handle post-deployment steps (22-24)
     */
    handlePostDeploymentSteps() {
        const deployment = this.siteCentricMode
            ? window.siteCentricManager?.getCurrentDeployment()
            : null;

        switch (this.currentStep) {
            case 22:
                this.setupStep22_GitPush();
                break;
            case 23:
                this.setupStep23_DeploymentExecution();
                break;
            case 24:
                this.setupStep24_VerificationGuide();
                break;
        }
    }

    /**
     * Setup Step 22: Git Push Guide
     */
    setupStep22_GitPush() {
        const container = document.querySelector("#step-22");
        if (!container) return;

        const deployment = this.siteCentricMode
            ? window.siteCentricManager?.getCurrentDeployment()
            : null;

        const gitGuideContent = this.generateGitPushGuide(deployment);

        const dynamicContainer =
            container.querySelector(".git-push-guide") ||
            container.querySelector(".dynamic-content");

        if (dynamicContainer) {
            dynamicContainer.innerHTML = gitGuideContent;
        }
    }

    /**
     * Setup Step 23: Deployment Execution Guide
     */
    setupStep23_DeploymentExecution() {
        const container = document.querySelector("#step-23");
        if (!container) return;

        const deployment = this.siteCentricMode
            ? window.siteCentricManager?.getCurrentDeployment()
            : null;

        const deploymentGuideContent =
            this.generateDeploymentExecutionGuide(deployment);

        const dynamicContainer =
            container.querySelector(".deployment-execution-guide") ||
            container.querySelector(".dynamic-content");

        if (dynamicContainer) {
            dynamicContainer.innerHTML = deploymentGuideContent;
        }
    }

    /**
     * Setup Step 24: Verification Guide
     */
    setupStep24_VerificationGuide() {
        const container = document.querySelector("#step-24");
        if (!container) return;

        const deployment = this.siteCentricMode
            ? window.siteCentricManager?.getCurrentDeployment()
            : null;

        const verificationGuideContent =
            this.generateVerificationGuide(deployment);

        const dynamicContainer =
            container.querySelector(".verification-guide") ||
            container.querySelector(".dynamic-content");

        if (dynamicContainer) {
            dynamicContainer.innerHTML = verificationGuideContent;
        }
    }

    /**
     * Generate Git Push Guide content
     */
    generateGitPushGuide(deployment) {
        if (!deployment) return "<p>No deployment context available</p>";

        const repository =
            deployment.config?.deployment?.repository || "your-repository";
        const branch = deployment.branch || "main";

        return `
            <div class="guide-content">
                <h3><i class="fab fa-git-alt"></i> Git Push Guide</h3>
                <div class="guide-steps">
                    <div class="guide-step">
                        <h4>Step 1: Commit your changes</h4>
                        <div class="code-block">
                            <code>git add .</code><br>
                            <code>git commit -m "Deploy ${deployment.environment} environment"</code>
                        </div>
                    </div>
                    
                    <div class="guide-step">
                        <h4>Step 2: Push to repository</h4>
                        <div class="code-block">
                            <code>git push origin ${branch}</code>
                        </div>
                    </div>
                    
                    <div class="guide-step">
                        <h4>Step 3: Verify push</h4>
                        <p>Check your repository at: <a href="${repository}" target="_blank">${repository}</a></p>
                        <p>Ensure the latest commit appears on the <strong>${branch}</strong> branch</p>
                    </div>
                </div>
                
                <div class="guide-checklist">
                    <h4>Pre-push Checklist:</h4>
                    <label><input type="checkbox"> All files are committed</label>
                    <label><input type="checkbox"> No sensitive data in commits</label>
                    <label><input type="checkbox"> Tests are passing</label>
                    <label><input type="checkbox"> Environment variables are configured</label>
                </div>
            </div>
        `;
    }

    /**
     * Generate Deployment Execution Guide content
     */
    generateDeploymentExecutionGuide(deployment) {
        if (!deployment) return "<p>No deployment context available</p>";

        const strategy = deployment.deploymentStrategy;
        const provider = deployment.hostingProvider;

        let guideContent = `
            <div class="guide-content">
                <h3><i class="fas fa-rocket"></i> Deployment Execution Guide</h3>
                <div class="deployment-info">
                    <p><strong>Strategy:</strong> ${strategy}</p>
                    <p><strong>Hosting:</strong> ${provider}</p>
                    <p><strong>Environment:</strong> ${deployment.environment}</p>
                </div>
        `;

        // Generate strategy-specific instructions
        switch (strategy) {
            case "github-actions":
                guideContent += this.generateGitHubActionsGuide(deployment);
                break;
            case "deployhq":
                guideContent += this.generateDeployHQGuide(deployment);
                break;
            case "local-ssh":
                guideContent += this.generateLocalSSHGuide(deployment);
                break;
            case "git-pull":
                guideContent += this.generateGitPullGuide(deployment);
                break;
            case "ftp-upload":
                guideContent += this.generateFTPUploadGuide(deployment);
                break;
            default:
                guideContent +=
                    "<p>Manual deployment - follow your custom deployment process</p>";
        }

        guideContent += "</div>";
        return guideContent;
    }

    /**
     * Generate Verification Guide content
     */
    generateVerificationGuide(deployment) {
        if (!deployment) return "<p>No deployment context available</p>";

        const domain = deployment.domain || "your-domain.com";

        return `
            <div class="guide-content">
                <h3><i class="fas fa-check-circle"></i> Deployment Verification</h3>
                
                <div class="verification-steps">
                    <div class="verification-step">
                        <h4>1. Website Accessibility</h4>
                        <p>Visit your website: <a href="https://${domain}" target="_blank">${domain}</a></p>
                        <label><input type="checkbox"> Website loads successfully</label>
                        <label><input type="checkbox"> SSL certificate is working</label>
                        <label><input type="checkbox"> No broken links or resources</label>
                    </div>
                    
                    <div class="verification-step">
                        <h4>2. Functionality Testing</h4>
                        <label><input type="checkbox"> User authentication works</label>
                        <label><input type="checkbox"> Database connections successful</label>
                        <label><input type="checkbox"> Form submissions work</label>
                        <label><input type="checkbox"> File uploads function properly</label>
                    </div>
                    
                    <div class="verification-step">
                        <h4>3. Performance Check</h4>
                        <label><input type="checkbox"> Page load times acceptable</label>
                        <label><input type="checkbox"> Images and assets optimized</label>
                        <label><input type="checkbox"> Mobile responsiveness verified</label>
                    </div>
                    
                    <div class="verification-step">
                        <h4>4. Technical Verification</h4>
                        <label><input type="checkbox"> Error logs are clean</label>
                        <label><input type="checkbox"> Cron jobs are running</label>
                        <label><input type="checkbox"> Email functionality tested</label>
                        <label><input type="checkbox"> Backup systems operational</label>
                    </div>
                </div>
                
                <div class="next-steps">
                    <h4>🎉 Deployment Complete!</h4>
                    <p>Your application has been successfully deployed. Consider these next steps:</p>
                    <ul>
                        <li>Set up monitoring and alerting</li>
                        <li>Configure automated backups</li>
                        <li>Document the deployment process</li>
                        <li>Plan for future updates</li>
                    </ul>
                </div>
            </div>
        `;
    }

    /**
     * Generate GitHub Actions specific guide
     */
    generateGitHubActionsGuide(deployment) {
        return `
            <div class="strategy-guide">
                <h4>GitHub Actions Deployment</h4>
                <div class="guide-steps">
                    <div class="guide-step">
                        <h5>1. Check Workflow Status</h5>
                        <p>Go to your repository's Actions tab to monitor the deployment workflow</p>
                        <p>URL: ${deployment.config?.deployment?.repository}/actions</p>
                    </div>
                    
                    <div class="guide-step">
                        <h5>2. Monitor Deployment</h5>
                        <p>Wait for the workflow to complete successfully</p>
                        <p>Check for any error messages in the logs</p>
                    </div>
                    
                    <div class="guide-step">
                        <h5>3. Troubleshooting</h5>
                        <p>If deployment fails, check:</p>
                        <ul>
                            <li>Secrets are properly configured</li>
                            <li>Server permissions are correct</li>
                            <li>File paths are accurate</li>
                        </ul>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Generate DeployHQ specific guide
     */
    generateDeployHQGuide(deployment) {
        return `
            <div class="strategy-guide">
                <h4>DeployHQ Deployment</h4>
                <div class="guide-steps">
                    <div class="guide-step">
                        <h5>1. Access DeployHQ Dashboard</h5>
                        <p>Login to your DeployHQ account and select your project</p>
                    </div>
                    
                    <div class="guide-step">
                        <h5>2. Trigger Deployment</h5>
                        <p>Click "Deploy Now" for the ${deployment.environment} environment</p>
                        <p>Monitor the deployment progress in real-time</p>
                    </div>
                    
                    <div class="guide-step">
                        <h5>3. Review Deployment Log</h5>
                        <p>Check the deployment log for any warnings or errors</p>
                        <p>Verify all files were uploaded successfully</p>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Generate Local SSH specific guide
     */
    generateLocalSSHGuide(deployment) {
        const serverHost =
            deployment.config?.hosting?.serverHost || "your-server.com";
        const username = deployment.config?.hosting?.sshUsername || "username";
        const documentRoot =
            deployment.config?.hosting?.documentRoot || "/var/www/html";

        return `
            <div class="strategy-guide">
                <h4>Local SSH Deployment</h4>
                <div class="guide-steps">
                    <div class="guide-step">
                        <h5>1. Connect to Server</h5>
                        <div class="code-block">
                            <code>ssh ${username}@${serverHost}</code>
                        </div>
                    </div>
                    
                    <div class="guide-step">
                        <h5>2. Navigate to Document Root</h5>
                        <div class="code-block">
                            <code>cd ${documentRoot}</code>
                        </div>
                    </div>
                    
                    <div class="guide-step">
                        <h5>3. Pull Latest Changes</h5>
                        <div class="code-block">
                            <code>git pull origin ${deployment.branch}</code>
                        </div>
                    </div>
                    
                    <div class="guide-step">
                        <h5>4. Run Post-Deployment Commands</h5>
                        <div class="code-block">
                            <code>composer install --no-dev</code><br>
                            <code>php artisan migrate --force</code><br>
                            <code>php artisan config:cache</code><br>
                            <code>php artisan route:cache</code>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Generate Git Pull specific guide
     */
    generateGitPullGuide(deployment) {
        return `
            <div class="strategy-guide">
                <h4>Git Pull Deployment</h4>
                <div class="guide-steps">
                    <div class="guide-step">
                        <h5>1. Access Server Control Panel</h5>
                        <p>Login to your hosting control panel or file manager</p>
                    </div>
                    
                    <div class="guide-step">
                        <h5>2. Use Terminal/SSH</h5>
                        <p>Access the terminal or SSH functionality in your control panel</p>
                    </div>
                    
                    <div class="guide-step">
                        <h5>3. Pull Latest Code</h5>
                        <p>Navigate to your application directory and run:</p>
                        <div class="code-block">
                            <code>git pull origin ${deployment.branch}</code>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Generate FTP Upload specific guide
     */
    generateFTPUploadGuide(deployment) {
        return `
            <div class="strategy-guide">
                <h4>FTP Upload Deployment</h4>
                <div class="guide-steps">
                    <div class="guide-step">
                        <h5>1. Prepare Files</h5>
                        <p>Build your project locally and prepare files for upload</p>
                        <div class="code-block">
                            <code>composer install --no-dev</code><br>
                            <code>npm run build</code>
                        </div>
                    </div>
                    
                    <div class="guide-step">
                        <h5>2. Connect via FTP</h5>
                        <p>Use your preferred FTP client to connect to the server</p>
                        <p>Server: ${
                            deployment.config?.hosting?.serverHost ||
                            "your-server.com"
                        }</p>
                    </div>
                    
                    <div class="guide-step">
                        <h5>3. Upload Files</h5>
                        <p>Upload all files to the document root directory</p>
                        <p>Ensure file permissions are set correctly</p>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Validate current step
     */
    validateCurrentStep() {
        // Step-specific validation logic
        switch (this.currentStep) {
            case 1:
                return this.validateStep1();
            case 2:
                return this.validateStep2();
            case 3:
                return this.validateStep3();
            case 4:
                return this.validateStep4();
            case 5:
                return this.validateStep5();
            default:
                return true; // No validation for other steps
        }
    }

    /**
     * Validate Step 1 (Project Information)
     */
    validateStep1() {
        const required = ["projectName", "environment", "deploymentScenario"];
        return this.validateRequiredFields(required);
    }

    /**
     * Validate Step 2 (Server Access)
     */
    validateStep2() {
        const required = [
            "serverHost",
            "sshUsername",
            "serverOS",
            "webServer",
            "phpVersion",
        ];
        return this.validateRequiredFields(required);
    }

    /**
     * Validate Step 3 (Domain & SSL)
     */
    validateStep3() {
        const required = ["productionDomain"];
        return this.validateRequiredFields(required);
    }

    /**
     * Validate Step 4 (Database)
     */
    validateStep4() {
        const required = [
            "dbType",
            "dbHost",
            "dbName",
            "dbUsername",
            "dbPassword",
        ];
        return this.validateRequiredFields(required);
    }

    /**
     * Validate Step 5 (Directory Structure)
     */
    validateStep5() {
        const required = ["documentRoot", "projectDirectory"];
        return this.validateRequiredFields(required);
    }

    /**
     * Validate required fields
     */
    validateRequiredFields(fields) {
        for (const field of fields) {
            const value = this.configuration[field];
            if (!value || value.trim() === "") {
                this.showValidationError(`Please fill in the ${field} field`);
                return false;
            }
        }
        return true;
    }

    /**
     * Show validation error
     */
    showValidationError(message) {
        if (window.showToast) {
            showToast(message, "error");
        } else {
            alert(message);
        }
    }

    /**
     * Collect data from current step
     */
    collectCurrentStepData() {
        const currentStepElement = document.querySelector(
            `#step-${this.currentStep}`
        );
        if (!currentStepElement) return;

        const inputs = currentStepElement.querySelectorAll(
            "input, select, textarea"
        );
        inputs.forEach((input) => {
            const value =
                input.type === "checkbox" ? input.checked : input.value;
            this.configuration[input.id] = value;
        });
    }

    /**
     * Populate current step fields
     */
    populateCurrentStepFields() {
        const currentStepElement = document.querySelector(
            `#step-${this.currentStep}`
        );
        if (!currentStepElement) return;

        const inputs = currentStepElement.querySelectorAll(
            "input, select, textarea"
        );
        inputs.forEach((input) => {
            const value = this.configuration[input.id];
            if (value !== undefined) {
                if (input.type === "checkbox") {
                    input.checked = value;
                } else {
                    input.value = value;
                }
            }
        });
    }

    /**
     * Populate all form fields
     */
    populateFormFields() {
        Object.keys(this.configuration).forEach((key) => {
            const element = document.getElementById(key);
            if (element) {
                const value = this.configuration[key];
                if (element.type === "checkbox") {
                    element.checked = value;
                } else {
                    element.value = value;
                }
            }
        });
    }

    /**
     * Set default values
     */
    setDefaultValues() {
        const defaults = {
            sshPort: 22,
            dbPort: 3306,
            environment: "production",
            branch: "main",
        };

        Object.keys(defaults).forEach((key) => {
            if (this.configuration[key] === undefined) {
                this.configuration[key] = defaults[key];
                const element = document.getElementById(key);
                if (element) {
                    element.value = defaults[key];
                }
            }
        });
    }

    /**
     * Update UI elements
     */
    updateUI() {
        this.updateProgress();
        this.updateNavigationButtons();
    }

    /**
     * Update progress indicator
     */
    updateProgress() {
        const progressFill = document.getElementById("progressFill");
        const progressSteps = document.getElementById("progressSteps");

        if (progressFill) {
            const percentage =
                ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
            progressFill.style.width = `${percentage}%`;
        }

        if (progressSteps) {
            const steps = progressSteps.querySelectorAll(".step");
            steps.forEach((step, index) => {
                const stepNumber = index + 1;
                step.classList.toggle(
                    "active",
                    stepNumber === this.currentStep
                );
                step.classList.toggle(
                    "completed",
                    stepNumber < this.currentStep
                );
            });
        }
    }

    /**
     * Update navigation buttons
     */
    updateNavigationButtons() {
        const prevBtn = document.querySelector(".btn-prev");
        const nextBtn = document.querySelector(".btn-next");

        if (prevBtn) {
            prevBtn.style.display = this.currentStep > 1 ? "block" : "none";
        }

        if (nextBtn) {
            if (this.currentStep >= 24) {
                nextBtn.textContent = "Complete";
                nextBtn.classList.add("btn-complete");
            } else {
                nextBtn.textContent = "Next Step";
                nextBtn.classList.remove("btn-complete");
            }
        }
    }

    /**
     * Previous step
     */
    previousStep() {
        if (this.currentStep > 1) {
            this.goToStep(this.currentStep - 1, true);
        }
    }

    /**
     * Next step
     */
    nextStep() {
        if (this.currentStep < this.totalSteps) {
            this.goToStep(this.currentStep + 1);
        } else {
            this.completeWizard();
        }
    }

    /**
     * Complete wizard
     */
    completeWizard() {
        this.collectCurrentStepData();
        this.saveConfiguration();

        // Show completion message
        if (window.showToast) {
            showToast(
                "Deployment configuration completed successfully!",
                "success"
            );
        }

        // Trigger completion event
        const event = new CustomEvent("wizardCompleted", {
            detail: {
                configuration: this.configuration,
                siteCentricMode: this.siteCentricMode,
            },
        });
        document.dispatchEvent(event);
    }
}

// Initialize wizard
document.addEventListener("DOMContentLoaded", () => {
    window.wizard = new WizardApp();
    window.wizard.init();
});

// Export for global access
window.WizardApp = WizardApp;
