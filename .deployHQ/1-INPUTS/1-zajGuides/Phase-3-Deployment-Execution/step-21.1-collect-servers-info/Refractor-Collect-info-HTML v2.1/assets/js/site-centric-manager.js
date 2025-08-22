// Site-centric data model manager
// Handles project → sites → deployments → hosting/deployment configurations

class SiteCentricManager {
    constructor() {
        this.currentProject = null;
        this.currentSiteId = null;
        this.currentDeploymentId = null;
        this.defaultStructure = this.getDefaultProjectStructure();
    }

    /**
     * Initialize site-centric manager
     */
    init() {
        this.loadOrCreateProject();
        this.setupEventListeners();
    }

    /**
     * Get default project structure
     * @returns {Object} Default project structure
     */
    getDefaultProjectStructure() {
        return {
            schemaVersion: "2.1.0",
            project: {
                id: this.generateId(),
                name: "",
                description: "",
                created: new Date().toISOString(),
                updated: new Date().toISOString(),
                settings: {
                    savePath: "Admin-Local/1-Current-Project/1-secrets",
                    autoBackup: true,
                    currentSite: null,
                    currentDeployment: null
                }
            },
            sites: {},
            metadata: {
                version: "2.1.0",
                created: new Date().toISOString(),
                generator: "Site-Centric Deployment Wizard v2.1",
                checksum: ""
            }
        };
    }

    /**
     * Load existing project or create new one
     */
    loadOrCreateProject() {
        // Try to load from storage manager
        if (window.storageManager && window.storageManager.currentConfig) {
            const config = window.storageManager.currentConfig;
            
            // Check if it's already site-centric format
            if (config.sites) {
                this.currentProject = config;
            } else {
                // Convert legacy format to site-centric
                this.currentProject = this.convertLegacyConfig(config);
            }
        } else {
            this.currentProject = { ...this.defaultStructure };
        }
        
        // Ensure we have at least one site
        if (Object.keys(this.currentProject.sites).length === 0) {
            this.addSite("main-app", "Main Application");
        }
    }

    /**
     * Convert legacy configuration to site-centric format
     * @param {Object} legacyConfig - Legacy configuration
     * @returns {Object} Site-centric configuration
     */
    convertLegacyConfig(legacyConfig) {
        const siteId = "main-app";
        const deploymentId = "production";
        
        const siteCentric = { ...this.defaultStructure };
        
        // Set project info
        siteCentric.project.name = legacyConfig.projectName || "Imported Project";
        siteCentric.project.settings.savePath = legacyConfig.savePath || siteCentric.project.settings.savePath;
        
        // Create main site from legacy config
        siteCentric.sites[siteId] = {
            id: siteId,
            name: siteId,
            displayName: "Main Application",
            description: "Imported from legacy configuration",
            repository: "",
            created: new Date().toISOString(),
            deployments: {}
        };
        
        // Create deployment from legacy config
        siteCentric.sites[siteId].deployments[deploymentId] = {
            id: deploymentId,
            environment: legacyConfig.environment || "production",
            domain: legacyConfig.productionDomain || "",
            branch: "main",
            hostingProvider: legacyConfig.hostingProvider || "",
            deploymentStrategy: legacyConfig.deploymentScenario || "",
            active: true,
            config: {
                hosting: this.extractHostingConfig(legacyConfig),
                deployment: this.extractDeploymentConfig(legacyConfig),
                database: this.extractDatabaseConfig(legacyConfig)
            },
            created: new Date().toISOString(),
            status: "configured"
        };
        
        // Set current context
        siteCentric.project.settings.currentSite = siteId;
        siteCentric.project.settings.currentDeployment = deploymentId;
        
        return siteCentric;
    }

    /**
     * Extract hosting configuration from legacy config
     * @param {Object} legacyConfig - Legacy configuration
     * @returns {Object} Hosting configuration
     */
    extractHostingConfig(legacyConfig) {
        return {
            serverHost: legacyConfig.serverHost || "",
            sshUsername: legacyConfig.sshUsername || "",
            sshPort: legacyConfig.sshPort || 22,
            documentRoot: legacyConfig.documentRoot || "",
            webServer: legacyConfig.webServer || "",
            phpVersion: legacyConfig.phpVersion || "",
            sslProvider: legacyConfig.sslProvider || ""
        };
    }

    /**
     * Extract deployment configuration from legacy config
     * @param {Object} legacyConfig - Legacy configuration
     * @returns {Object} Deployment configuration
     */
    extractDeploymentConfig(legacyConfig) {
        return {
            repository: "",
            branch: "main",
            buildCommand: "",
            triggerType: "manual"
        };
    }

    /**
     * Extract database configuration from legacy config
     * @param {Object} legacyConfig - Legacy configuration
     * @returns {Object} Database configuration
     */
    extractDatabaseConfig(legacyConfig) {
        return {
            type: legacyConfig.dbType || "mysql",
            host: legacyConfig.dbHost || "localhost",
            port: legacyConfig.dbPort || 3306,
            name: legacyConfig.dbName || "",
            username: legacyConfig.dbUsername || ""
        };
    }

    /**
     * Add new site to project
     * @param {string} name - Site name
     * @param {string} displayName - Site display name
     * @param {string} description - Site description
     * @returns {string} Site ID
     */
    addSite(name, displayName = "", description = "") {
        const siteId = name || this.generateId();
        
        this.currentProject.sites[siteId] = {
            id: siteId,
            name: name || siteId,
            displayName: displayName || name || "New Site",
            description: description,
            repository: "",
            created: new Date().toISOString(),
            deployments: {}
        };
        
        // Add default production deployment
        this.addDeployment(siteId, "production", "Production Environment");
        
        this.updateProject();
        return siteId;
    }

    /**
     * Add deployment to site
     * @param {string} siteId - Site ID
     * @param {string} environment - Environment name
     * @param {string} displayName - Deployment display name
     * @returns {string} Deployment ID
     */
    addDeployment(siteId, environment, displayName = "") {
        if (!this.currentProject.sites[siteId]) return null;
        
        const deploymentId = environment || this.generateId();
        
        this.currentProject.sites[siteId].deployments[deploymentId] = {
            id: deploymentId,
            environment: environment,
            displayName: displayName || environment,
            domain: "",
            branch: "main",
            hostingProvider: "",
            deploymentStrategy: "",
            active: true,
            config: {
                hosting: {},
                deployment: {},
                database: {}
            },
            created: new Date().toISOString(),
            lastDeployment: null,
            status: "configured"
        };
        
        this.updateProject();
        return deploymentId;
    }

    /**
     * Get current site
     * @returns {Object|null} Current site
     */
    getCurrentSite() {
        const siteId = this.currentSiteId || this.currentProject.project.settings.currentSite;
        return siteId ? this.currentProject.sites[siteId] : null;
    }

    /**
     * Get current deployment
     * @returns {Object|null} Current deployment
     */
    getCurrentDeployment() {
        const site = this.getCurrentSite();
        if (!site) return null;
        
        const deploymentId = this.currentDeploymentId || this.currentProject.project.settings.currentDeployment;
        return deploymentId ? site.deployments[deploymentId] : null;
    }

    /**
     * Set current site and deployment
     * @param {string} siteId - Site ID
     * @param {string} deploymentId - Deployment ID
     */
    setCurrentContext(siteId, deploymentId = null) {
        this.currentSiteId = siteId;
        this.currentDeploymentId = deploymentId;
        
        this.currentProject.project.settings.currentSite = siteId;
        if (deploymentId) {
            this.currentProject.project.settings.currentDeployment = deploymentId;
        }
        
        this.updateProject();
        this.notifyContextChange();
    }

    /**
     * Update deployment configuration
     * @param {string} siteId - Site ID
     * @param {string} deploymentId - Deployment ID
     * @param {Object} config - Configuration updates
     */
    updateDeploymentConfig(siteId, deploymentId, config) {
        const site = this.currentProject.sites[siteId];
        if (!site || !site.deployments[deploymentId]) return;
        
        const deployment = site.deployments[deploymentId];
        
        // Update deployment properties
        Object.assign(deployment, config);
        
        // Update configuration sections
        if (config.hostingConfig) {
            Object.assign(deployment.config.hosting, config.hostingConfig);
        }
        
        if (config.deploymentConfig) {
            Object.assign(deployment.config.deployment, config.deploymentConfig);
        }
        
        if (config.databaseConfig) {
            Object.assign(deployment.config.database, config.databaseConfig);
        }
        
        deployment.updated = new Date().toISOString();
        this.updateProject();
    }

    /**
     * Get all sites for display
     * @returns {Array} Sites list
     */
    getAllSites() {
        return Object.values(this.currentProject.sites).map(site => ({
            ...site,
            deploymentsCount: Object.keys(site.deployments).length,
            environments: Object.keys(site.deployments)
        }));
    }

    /**
     * Get deployments for a site
     * @param {string} siteId - Site ID
     * @returns {Array} Deployments list
     */
    getSiteDeployments(siteId) {
        const site = this.currentProject.sites[siteId];
        return site ? Object.values(site.deployments) : [];
    }

    /**
     * Remove site
     * @param {string} siteId - Site ID
     */
    removeSite(siteId) {
        if (Object.keys(this.currentProject.sites).length <= 1) {
            if (window.showToast) {
                showToast("Cannot remove the last site", "error");
            }
            return;
        }
        
        delete this.currentProject.sites[siteId];
        
        // Update current context if needed
        if (this.currentSiteId === siteId) {
            const remainingSites = Object.keys(this.currentProject.sites);
            this.setCurrentContext(remainingSites[0]);
        }
        
        this.updateProject();
    }

    /**
     * Remove deployment
     * @param {string} siteId - Site ID
     * @param {string} deploymentId - Deployment ID
     */
    removeDeployment(siteId, deploymentId) {
        const site = this.currentProject.sites[siteId];
        if (!site) return;
        
        if (Object.keys(site.deployments).length <= 1) {
            if (window.showToast) {
                showToast("Cannot remove the last deployment", "error");
            }
            return;
        }
        
        delete site.deployments[deploymentId];
        
        // Update current context if needed
        if (this.currentDeploymentId === deploymentId) {
            const remainingDeployments = Object.keys(site.deployments);
            this.currentDeploymentId = remainingDeployments[0];
            this.currentProject.project.settings.currentDeployment = remainingDeployments[0];
        }
        
        this.updateProject();
    }

    /**
     * Update project metadata and save
     */
    updateProject() {
        this.currentProject.project.updated = new Date().toISOString();
        this.currentProject.metadata.lastUpdated = new Date().toISOString();
        
        // Auto-save through storage manager
        if (window.storageManager) {
            window.storageManager.autoSave(this.currentProject, window.wizard?.currentStep || 0);
        }
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Listen for form changes and update current deployment
        document.addEventListener('input', (e) => {
            if (e.target.matches('input, select, textarea')) {
                this.handleFormChange(e.target);
            }
        });
        
        // Listen for hosting provider changes
        document.addEventListener('change', (e) => {
            if (e.target.id === 'hostingProvider') {
                this.handleHostingProviderChange(e.target.value);
            }
            
            if (e.target.id === 'deploymentScenario') {
                this.handleDeploymentStrategyChange(e.target.value);
            }
        });
    }

    /**
     * Handle form field changes
     * @param {HTMLElement} field - Form field element
     */
    handleFormChange(field) {
        const deployment = this.getCurrentDeployment();
        if (!deployment) return;
        
        const fieldId = field.id;
        const value = field.value;
        
        // Determine configuration section
        if (this.isHostingField(fieldId)) {
            if (!deployment.config.hosting) deployment.config.hosting = {};
            deployment.config.hosting[fieldId] = value;
        } else if (this.isDeploymentField(fieldId)) {
            if (!deployment.config.deployment) deployment.config.deployment = {};
            deployment.config.deployment[fieldId] = value;
        } else if (this.isDatabaseField(fieldId)) {
            if (!deployment.config.database) deployment.config.database = {};
            deployment.config.database[fieldId] = value;
        } else {
            // General deployment properties
            deployment[fieldId] = value;
        }
        
        this.updateProject();
    }

    /**
     * Handle hosting provider change
     * @param {string} provider - Selected hosting provider
     */
    handleHostingProviderChange(provider) {
        const deployment = this.getCurrentDeployment();
        if (!deployment) return;
        
        deployment.hostingProvider = provider;
        
        // Update form fields based on template
        if (window.templateEngine) {
            this.updateFormFieldsForProvider(provider);
        }
        
        this.updateProject();
    }

    /**
     * Handle deployment strategy change
     * @param {string} strategy - Selected deployment strategy
     */
    handleDeploymentStrategyChange(strategy) {
        const deployment = this.getCurrentDeployment();
        if (!deployment) return;
        
        deployment.deploymentStrategy = strategy;
        
        // Update form fields based on template
        if (window.templateEngine) {
            this.updateFormFieldsForStrategy(strategy);
        }
        
        this.updateProject();
    }

    /**
     * Update form fields for hosting provider
     * @param {string} provider - Hosting provider
     */
    updateFormFieldsForProvider(provider) {
        const fields = window.templateEngine.generateHostingFields(provider);
        // This would update the form dynamically
        // Implementation depends on wizard structure
        console.log(`Updating form for hosting provider: ${provider}`, fields);
    }

    /**
     * Update form fields for deployment strategy
     * @param {string} strategy - Deployment strategy
     */
    updateFormFieldsForStrategy(strategy) {
        const fields = window.templateEngine.generateDeploymentFields(strategy);
        // This would update the form dynamically
        console.log(`Updating form for deployment strategy: ${strategy}`, fields);
    }

    /**
     * Check if field belongs to hosting configuration
     * @param {string} fieldId - Field ID
     * @returns {boolean} Is hosting field
     */
    isHostingField(fieldId) {
        const hostingFields = [
            'serverHost', 'sshUsername', 'sshPort', 'documentRoot',
            'webServer', 'phpVersion', 'sslProvider', 'hostingProvider'
        ];
        return hostingFields.includes(fieldId);
    }

    /**
     * Check if field belongs to deployment configuration
     * @param {string} fieldId - Field ID
     * @returns {boolean} Is deployment field
     */
    isDeploymentField(fieldId) {
        const deploymentFields = [
            'repository', 'branch', 'buildCommand', 'triggerType',
            'deploymentScenario', 'postDeployCommands'
        ];
        return deploymentFields.includes(fieldId);
    }

    /**
     * Check if field belongs to database configuration
     * @param {string} fieldId - Field ID
     * @returns {boolean} Is database field
     */
    isDatabaseField(fieldId) {
        const databaseFields = [
            'dbType', 'dbHost', 'dbPort', 'dbName', 'dbUsername', 'dbPassword'
        ];
        return databaseFields.includes(fieldId);
    }

    /**
     * Notify other components of context change
     */
    notifyContextChange() {
        const event = new CustomEvent('siteContextChanged', {
            detail: {
                siteId: this.currentSiteId,
                deploymentId: this.currentDeploymentId,
                site: this.getCurrentSite(),
                deployment: this.getCurrentDeployment()
            }
        });
        document.dispatchEvent(event);
    }

    /**
     * Generate unique ID
     * @returns {string} Unique ID
     */
    generateId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }

    /**
     * Get project for export/storage
     * @returns {Object} Complete project configuration
     */
    getProjectForExport() {
        // Update metadata before export
        this.currentProject.metadata.checksum = this.generateChecksum(JSON.stringify(this.currentProject));
        return this.currentProject;
    }

    /**
     * Generate checksum for data integrity
     * @param {string} data - Data to checksum
     * @returns {string} Checksum
     */
    generateChecksum(data) {
        let hash = 0;
        for (let i = 0; i < data.length; i++) {
            const char = data.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        return hash.toString(16).substring(0, 8);
    }
}

// Initialize site-centric manager
window.siteCentricManager = new SiteCentricManager();