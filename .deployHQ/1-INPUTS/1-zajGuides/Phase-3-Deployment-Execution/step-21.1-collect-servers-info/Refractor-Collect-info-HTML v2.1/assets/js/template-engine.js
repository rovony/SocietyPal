// Template Engine for unified hosting and deployment templates
// Handles dynamic form generation based on selected providers/strategies

class TemplateEngine {
    constructor() {
        this.hostingTemplate = null;
        this.deploymentTemplate = null;
        this.currentProvider = null;
        this.currentStrategy = null;
        this.loadedTemplates = new Map();
    }

    /**
     * Initialize template engine
     */
    async init() {
        try {
            await this.loadTemplates();
            console.log('Template engine initialized successfully');
        } catch (error) {
            console.error('Template engine initialization failed:', error);
        }
    }

    /**
     * Load unified templates
     */
    async loadTemplates() {
        try {
            // Load hosting template
            const hostingResponse = await fetch('./templates/unified/hosting-template.json');
            this.hostingTemplate = await hostingResponse.json();
            this.loadedTemplates.set('hosting', this.hostingTemplate);

            // Load deployment template
            const deploymentResponse = await fetch('./templates/unified/deployment-template.json');
            this.deploymentTemplate = await deploymentResponse.json();
            this.loadedTemplates.set('deployment', this.deploymentTemplate);

            console.log('Templates loaded successfully');
        } catch (error) {
            console.error('Failed to load templates:', error);
            throw error;
        }
    }

    /**
     * Get hosting providers list
     * @returns {Array} List of hosting providers
     */
    getHostingProviders() {
        if (!this.hostingTemplate) return [];
        
        return Object.entries(this.hostingTemplate.providers)
            .filter(([_, config]) => config.active)
            .map(([key, config]) => ({
                value: key,
                label: config.displayName,
                category: config.category,
                icon: config.icon,
                complexity: config.complexity,
                features: config.features || []
            }))
            .sort((a, b) => a.complexity === 'low' ? -1 : 1);
    }

    /**
     * Get deployment strategies list
     * @returns {Array} List of deployment strategies
     */
    getDeploymentStrategies() {
        if (!this.deploymentTemplate) return [];
        
        return Object.entries(this.deploymentTemplate.strategies)
            .filter(([_, config]) => config.active)
            .map(([key, config]) => ({
                value: key,
                label: config.displayName,
                category: config.category,
                icon: config.icon,
                complexity: config.complexity,
                automation: config.automation,
                features: config.features || []
            }))
            .sort((a, b) => {
                // Sort by complexity: low, medium, high
                const complexityOrder = { 'low': 1, 'medium': 2, 'high': 3 };
                return complexityOrder[a.complexity] - complexityOrder[b.complexity];
            });
    }

    /**
     * Generate form fields for selected hosting provider
     * @param {string} provider - Selected hosting provider
     * @returns {Array} Form field configurations
     */
    generateHostingFields(provider) {
        if (!this.hostingTemplate || !provider) return [];
        
        this.currentProvider = provider;
        const fields = this.hostingTemplate.fields;
        const categories = this.hostingTemplate.categories;
        
        return Object.values(fields)
            .filter(field => this.isFieldApplicableForProvider(field, provider))
            .map(field => this.processFieldForProvider(field, provider))
            .sort((a, b) => {
                // Sort by category order, then field order
                const catOrderA = categories[a.category]?.order || 999;
                const catOrderB = categories[b.category]?.order || 999;
                if (catOrderA !== catOrderB) return catOrderA - catOrderB;
                return a.order - b.order;
            });
    }

    /**
     * Generate form fields for selected deployment strategy
     * @param {string} strategy - Selected deployment strategy
     * @returns {Array} Form field configurations
     */
    generateDeploymentFields(strategy) {
        if (!this.deploymentTemplate || !strategy) return [];
        
        this.currentStrategy = strategy;
        const fields = this.deploymentTemplate.fields;
        const categories = this.deploymentTemplate.categories;
        
        return Object.values(fields)
            .filter(field => this.isFieldApplicableForStrategy(field, strategy))
            .map(field => this.processFieldForStrategy(field, strategy))
            .sort((a, b) => {
                const catOrderA = categories[a.category]?.order || 999;
                const catOrderB = categories[b.category]?.order || 999;
                if (catOrderA !== catOrderB) return catOrderA - catOrderB;
                return a.order - b.order;
            });
    }

    /**
     * Check if field is applicable for hosting provider
     * @param {Object} field - Field configuration
     * @param {string} provider - Hosting provider
     * @returns {boolean} Whether field applies
     */
    isFieldApplicableForProvider(field, provider) {
        const providerConfig = field.providers?.[provider];
        return providerConfig !== undefined;
    }

    /**
     * Check if field is applicable for deployment strategy
     * @param {Object} field - Field configuration
     * @param {string} strategy - Deployment strategy
     * @returns {boolean} Whether field applies
     */
    isFieldApplicableForStrategy(field, strategy) {
        const strategyConfig = field.strategies?.[strategy];
        return strategyConfig !== undefined && !strategyConfig.disabled;
    }

    /**
     * Process field configuration for specific hosting provider
     * @param {Object} field - Base field configuration
     * @param {string} provider - Hosting provider
     * @returns {Object} Processed field configuration
     */
    processFieldForProvider(field, provider) {
        const providerConfig = field.providers[provider];
        const baseField = { ...field };
        
        // Apply provider-specific overrides
        if (providerConfig.default !== undefined) {
            baseField.defaultValue = providerConfig.default;
        }
        
        if (providerConfig.placeholder) {
            baseField.placeholder = providerConfig.placeholder;
        }
        
        if (providerConfig.hint) {
            baseField.hint = providerConfig.hint;
        }
        
        if (providerConfig.required !== undefined) {
            baseField.required = providerConfig.required;
        }
        
        if (providerConfig.locked) {
            baseField.readonly = true;
            baseField.locked = true;
        }
        
        if (providerConfig.template) {
            baseField.template = providerConfig.template;
            baseField.isTemplate = true;
        }
        
        if (providerConfig.pattern) {
            baseField.pattern = providerConfig.pattern;
        }
        
        if (providerConfig.options) {
            baseField.options = providerConfig.options;
        }
        
        if (providerConfig.available) {
            baseField.availableOptions = providerConfig.available;
        }
        
        return baseField;
    }

    /**
     * Process field configuration for specific deployment strategy
     * @param {Object} field - Base field configuration
     * @param {string} strategy - Deployment strategy
     * @returns {Object} Processed field configuration
     */
    processFieldForStrategy(field, strategy) {
        const strategyConfig = field.strategies[strategy];
        const baseField = { ...field };
        
        // Apply strategy-specific overrides
        if (strategyConfig.default !== undefined) {
            baseField.defaultValue = strategyConfig.default;
        }
        
        if (strategyConfig.placeholder) {
            baseField.placeholder = strategyConfig.placeholder;
        }
        
        if (strategyConfig.hint) {
            baseField.hint = strategyConfig.hint;
        }
        
        if (strategyConfig.required !== undefined) {
            baseField.required = strategyConfig.required;
        }
        
        if (strategyConfig.customizable !== undefined) {
            baseField.customizable = strategyConfig.customizable;
        }
        
        if (strategyConfig.template) {
            baseField.template = strategyConfig.template;
            baseField.isTemplate = true;
        }
        
        if (strategyConfig.available) {
            baseField.availableOptions = strategyConfig.available;
        }
        
        if (strategyConfig.disabled) {
            baseField.disabled = true;
            baseField.value = strategyConfig.value || 'na';
        }
        
        return baseField;
    }

    /**
     * Render form fields into HTML
     * @param {Array} fields - Field configurations
     * @param {string} sectionId - Section identifier
     * @returns {string} HTML for form fields
     */
    renderFormFields(fields, sectionId) {
        const fieldsByCategory = this.groupFieldsByCategory(fields);
        let html = '';
        
        Object.entries(fieldsByCategory).forEach(([categoryKey, categoryFields]) => {
            const category = this.getCategoryInfo(categoryKey, sectionId);
            
            html += `
                <div class="form-section" data-category="${categoryKey}">
                    <div class="section-title">
                        ${category.icon} ${category.title}
                    </div>
                    <div class="section-subtitle">
                        ${category.description}
                    </div>
                    ${categoryFields.map(field => this.renderField(field)).join('')}
                </div>
            `;
        });
        
        return html;
    }

    /**
     * Group fields by category
     * @param {Array} fields - Field configurations
     * @returns {Object} Fields grouped by category
     */
    groupFieldsByCategory(fields) {
        return fields.reduce((groups, field) => {
            const category = field.category || 'general';
            if (!groups[category]) groups[category] = [];
            groups[category].push(field);
            return groups;
        }, {});
    }

    /**
     * Get category information
     * @param {string} categoryKey - Category key
     * @param {string} sectionType - hosting or deployment
     * @returns {Object} Category info
     */
    getCategoryInfo(categoryKey, sectionType) {
        const template = sectionType === 'deployment' ? this.deploymentTemplate : this.hostingTemplate;
        const category = template?.categories?.[categoryKey];
        
        return {
            title: category?.title || 'Configuration',
            description: category?.description || '',
            icon: category?.icon || '⚙️',
            order: category?.order || 999
        };
    }

    /**
     * Render individual form field
     * @param {Object} field - Field configuration
     * @returns {string} HTML for field
     */
    renderField(field) {
        const requiredMark = field.required ? '<span class="required">*</span>' : '';
        const readonlyAttr = field.readonly || field.locked ? 'readonly' : '';
        const disabledAttr = field.disabled ? 'disabled' : '';
        
        let fieldHtml = '';
        
        switch (field.type) {
            case 'select':
                fieldHtml = this.renderSelectField(field);
                break;
            case 'textarea':
                fieldHtml = this.renderTextareaField(field);
                break;
            case 'number':
                fieldHtml = this.renderNumberField(field);
                break;
            case 'array':
                fieldHtml = this.renderArrayField(field);
                break;
            case 'object':
                fieldHtml = this.renderObjectField(field);
                break;
            default:
                fieldHtml = this.renderTextField(field);
        }
        
        return `
            <div class="form-group" data-field="${field.id}">
                <label>
                    ${field.label}
                    ${requiredMark}
                </label>
                ${fieldHtml}
                ${field.hint ? `<div class="input-hint">${field.hint}</div>` : ''}
                ${field.locked ? '<div class="locked-notice">🔒 This field is locked by the hosting provider</div>' : ''}
                <div class="error-message"></div>
            </div>
        `;
    }

    /**
     * Render select field
     * @param {Object} field - Field configuration
     * @returns {string} HTML for select field
     */
    renderSelectField(field) {
        const options = field.availableOptions || field.options || [];
        const optionsHtml = options.map(option => {
            const selected = option.value === field.defaultValue ? 'selected' : '';
            return `<option value="${option.value}" ${selected}>${option.label}</option>`;
        }).join('');
        
        return `
            <select id="${field.id}" ${field.disabled ? 'disabled' : ''}>
                <option value="">Select ${field.label}</option>
                ${optionsHtml}
            </select>
        `;
    }

    /**
     * Render textarea field
     * @param {Object} field - Field configuration
     * @returns {string} HTML for textarea field
     */
    renderTextareaField(field) {
        return `
            <textarea 
                id="${field.id}" 
                placeholder="${field.placeholder || ''}"
                ${field.readonly ? 'readonly' : ''}
                ${field.disabled ? 'disabled' : ''}
                rows="4"
            >${field.defaultValue || ''}</textarea>
        `;
    }

    /**
     * Render number field
     * @param {Object} field - Field configuration
     * @returns {string} HTML for number field
     */
    renderNumberField(field) {
        return `
            <input 
                type="number" 
                id="${field.id}"
                placeholder="${field.placeholder || ''}"
                value="${field.defaultValue || ''}"
                ${field.readonly ? 'readonly' : ''}
                ${field.disabled ? 'disabled' : ''}
            />
        `;
    }

    /**
     * Render text field
     * @param {Object} field - Field configuration
     * @returns {string} HTML for text field
     */
    renderTextField(field) {
        return `
            <input 
                type="text" 
                id="${field.id}"
                placeholder="${field.placeholder || ''}"
                value="${field.defaultValue || ''}"
                ${field.pattern ? `pattern="${field.pattern}"` : ''}
                ${field.readonly ? 'readonly' : ''}
                ${field.disabled ? 'disabled' : ''}
            />
        `;
    }

    /**
     * Render array field (for lists like secrets, exclude files)
     * @param {Object} field - Field configuration
     * @returns {string} HTML for array field
     */
    renderArrayField(field) {
        const defaultItems = field.defaultValue || [];
        const itemsHtml = defaultItems.map((item, index) => `
            <div class="array-item">
                <input type="text" value="${item}" />
                <button type="button" class="btn-remove-item" onclick="removeArrayItem(this)">×</button>
            </div>
        `).join('');
        
        return `
            <div class="array-field" id="${field.id}">
                <div class="array-items">
                    ${itemsHtml}
                </div>
                <button type="button" class="btn-add-item" onclick="addArrayItem('${field.id}')">
                    + Add ${field.label}
                </button>
            </div>
        `;
    }

    /**
     * Render object field (for key-value pairs like environment variables)
     * @param {Object} field - Field configuration
     * @returns {string} HTML for object field
     */
    renderObjectField(field) {
        const defaultObj = field.defaultValue || {};
        const entriesHtml = Object.entries(defaultObj).map(([key, value]) => `
            <div class="object-entry">
                <input type="text" placeholder="Key" value="${key}" class="object-key" />
                <input type="text" placeholder="Value" value="${value}" class="object-value" />
                <button type="button" class="btn-remove-entry" onclick="removeObjectEntry(this)">×</button>
            </div>
        `).join('');
        
        return `
            <div class="object-field" id="${field.id}">
                <div class="object-entries">
                    ${entriesHtml}
                </div>
                <button type="button" class="btn-add-entry" onclick="addObjectEntry('${field.id}')">
                    + Add Variable
                </button>
            </div>
        `;
    }

    /**
     * Validate field value
     * @param {Object} field - Field configuration
     * @param {any} value - Field value
     * @returns {Object} Validation result
     */
    validateField(field, value) {
        const result = { valid: true, message: '' };
        
        // Check required fields
        if (field.required && (!value || value.toString().trim() === '')) {
            result.valid = false;
            result.message = `${field.label} is required`;
            return result;
        }
        
        // Skip validation for empty optional fields
        if (!value || value.toString().trim() === '') {
            return result;
        }
        
        // Pattern validation
        if (field.pattern && !new RegExp(field.pattern).test(value)) {
            result.valid = false;
            result.message = `${field.label} format is invalid`;
            return result;
        }
        
        // Template validation rules
        const template = this.currentProvider ? this.hostingTemplate : this.deploymentTemplate;
        if (template?.validation?.rules) {
            // Apply validation rules from template
            const validationKey = field.validation || field.id;
            const rule = template.validation.rules[validationKey];
            if (rule && !new RegExp(rule).test(value)) {
                result.valid = false;
                result.message = `${field.label} format is invalid`;
                return result;
            }
        }
        
        return result;
    }

    /**
     * Process template variables in field values
     * @param {string} template - Template string
     * @param {Object} variables - Variable values
     * @returns {string} Processed string
     */
    processTemplate(template, variables) {
        if (!template || typeof template !== 'string') return template;
        
        let processed = template;
        Object.entries(variables).forEach(([key, value]) => {
            const placeholder = `{${key}}`;
            processed = processed.replace(new RegExp(placeholder.replace(/[{}]/g, '\\$&'), 'g'), value || '');
        });
        
        return processed;
    }

    /**
     * Get step integration info
     * @param {string} strategy - Deployment strategy
     * @returns {Object} Step integration information
     */
    getStepIntegration(strategy) {
        if (!this.deploymentTemplate?.stepIntegration) return null;
        
        const integration = this.deploymentTemplate.stepIntegration;
        const strategyConfig = this.deploymentTemplate.strategies[strategy];
        
        return {
            steps: integration,
            stepsGuide: strategyConfig?.stepsGuide,
            documentation: strategyConfig?.documentation
        };
    }
}

// Initialize template engine
window.templateEngine = new TemplateEngine();