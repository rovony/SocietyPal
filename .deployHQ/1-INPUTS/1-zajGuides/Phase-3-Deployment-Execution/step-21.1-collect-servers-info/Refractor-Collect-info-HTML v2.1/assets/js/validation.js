// validation.js - Form validation logic

/**
 * Validation rules for different field types
 */
const ValidationRules = {
    required: (value) => value && value.toString().trim() !== '',
    email: (value) => !value || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
    url: (value) => !value || /^https?:\/\/.+/.test(value),
    number: (value) => !value || !isNaN(value),
    minLength: (min) => (value) => !value || value.length >= min,
    maxLength: (max) => (value) => !value || value.length <= max
};

/**
 * Field validation configuration
 */
const FieldValidationConfig = {
    // Step 1 - Project Info
    projectName: [ValidationRules.required],
    environment: [ValidationRules.required],
    deploymentScenario: [ValidationRules.required],
    
    // Step 2 - Server Access
    serverHost: [ValidationRules.required],
    sshUsername: [ValidationRules.required],
    serverOS: [ValidationRules.required],
    webServer: [ValidationRules.required],
    phpVersion: [ValidationRules.required],
    
    // Step 3 - Domain & SSL
    productionDomain: [ValidationRules.required, ValidationRules.url],
    stagingDomain: [ValidationRules.url],
    developmentDomain: [ValidationRules.url],
    
    // Step 4 - Database
    dbType: [ValidationRules.required],
    dbHost: [ValidationRules.required],
    dbName: [ValidationRules.required],
    dbUsername: [ValidationRules.required],
    dbPassword: [ValidationRules.required],
    
    // Step 5 - Directories
    documentRoot: [ValidationRules.required],
    projectDirectory: [ValidationRules.required]
};

/**
 * Validate a single field
 * @param {string} fieldId - Field identifier
 * @param {any} value - Field value
 * @returns {boolean} Is field valid
 */
function validateField(fieldId, value) {
    const rules = FieldValidationConfig[fieldId];
    if (!rules) return true;
    
    return rules.every(rule => rule(value));
}

/**
 * Validate current step
 * @param {number} stepNumber - Current step number
 * @returns {boolean} Is step valid
 */
function validateCurrentStep(stepNumber) {
    const step = document.getElementById(`step${stepNumber}`);
    if (!step) return true;
    
    const fieldsToValidate = getRequiredFieldsForStep(stepNumber);
    let isValid = true;
    
    fieldsToValidate.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        const formGroup = field.closest('.form-group');
        const value = field.type === 'checkbox' ? field.checked : field.value;
        
        if (!validateField(fieldId, value)) {
            formGroup.classList.add('error');
            isValid = false;
        } else {
            formGroup.classList.remove('error');
        }
    });
    
    return isValid;
}

/**
 * Get required fields for a specific step
 * @param {number} stepNumber - Step number
 * @returns {string[]} Array of field IDs
 */
function getRequiredFieldsForStep(stepNumber) {
    const stepFields = {
        1: ['projectName', 'environment', 'deploymentScenario'],
        2: ['serverHost', 'sshUsername', 'serverOS', 'webServer', 'phpVersion'],
        3: ['productionDomain'],
        4: ['dbType', 'dbHost', 'dbName', 'dbUsername', 'dbPassword'],
        5: ['documentRoot', 'projectDirectory'],
        6: [],
        7: []
    };
    
    return stepFields[stepNumber] || [];
}

/**
 * Validate entire form
 * @returns {object} Validation result with details
 */
function validateEntireForm() {
    const result = {
        isValid: true,
        errors: [],
        stepErrors: {}
    };
    
    for (let step = 1; step <= 7; step++) {
        const stepValid = validateCurrentStep(step);
        if (!stepValid) {
            result.isValid = false;
            result.stepErrors[step] = getStepErrors(step);
        }
    }
    
    return result;
}

/**
 * Get validation errors for a specific step
 * @param {number} stepNumber - Step number
 * @returns {string[]} Array of error messages
 */
function getStepErrors(stepNumber) {
    const errors = [];
    const fieldsToValidate = getRequiredFieldsForStep(stepNumber);
    
    fieldsToValidate.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        const value = field.type === 'checkbox' ? field.checked : field.value;
        if (!validateField(fieldId, value)) {
            const label = field.closest('.form-group').querySelector('label');
            const fieldName = label ? label.textContent.replace('*', '').trim() : fieldId;
            errors.push(`${fieldName} is required`);
        }
    });
    
    return errors;
}

/**
 * Show validation summary
 * @param {object} validationResult - Result from validateEntireForm
 */
function showValidationSummary(validationResult) {
    if (validationResult.isValid) {
        showToast('All fields are valid!', 'success');
        return;
    }
    
    const stepNumbers = Object.keys(validationResult.stepErrors);
    const stepNames = {
        1: 'Project Information',
        2: 'Server Access',
        3: 'Domain & SSL',
        4: 'Database',
        5: 'Directory Structure',
        6: 'Security',
        7: 'Review'
    };
    
    const errorSteps = stepNumbers.map(step => stepNames[step]).join(', ');
    showToast(`Please fix errors in: ${errorSteps}`, 'error');
}

/**
 * Real-time validation for input fields
 */
function setupRealTimeValidation() {
    document.addEventListener('input', (event) => {
        const field = event.target;
        if (!field.id || !(field.id in FieldValidationConfig)) return;
        
        const formGroup = field.closest('.form-group');
        if (!formGroup) return;
        
        const value = field.type === 'checkbox' ? field.checked : field.value;
        
        if (validateField(field.id, value)) {
            formGroup.classList.remove('error');
        } else if (value) { // Only show error if user has entered something
            formGroup.classList.add('error');
        }
    });
    
    document.addEventListener('blur', (event) => {
        const field = event.target;
        if (!field.id || !(field.id in FieldValidationConfig)) return;
        
        const formGroup = field.closest('.form-group');
        if (!formGroup) return;
        
        const value = field.type === 'checkbox' ? field.checked : field.value;
        
        if (!validateField(field.id, value)) {
            formGroup.classList.add('error');
        }
    });
}