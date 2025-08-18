// utils.js - Utility functions

/**
 * Toast notification system
 * @param {string} message - Message to display
 * @param {string} type - Type of toast (success, error, warning)
 */
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast ${type} show`;
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

/**
 * Copy command to clipboard
 * @param {HTMLElement} button - The copy button element
 */
function copyCommand(button) {
    const codeBlock = button.parentElement.querySelector('pre');
    navigator.clipboard.writeText(codeBlock.textContent).then(() => {
        const originalText = button.textContent;
        button.textContent = '✓ Copied';
        button.style.background = 'var(--success)';
        setTimeout(() => {
            button.textContent = originalText;
            button.style.background = 'var(--primary)';
        }, 2000);
    }).catch(() => {
        showToast('Failed to copy command', 'error');
    });
}

/**
 * Toggle collapsible sections
 * @param {HTMLElement} element - The collapsible trigger element
 */
function toggleCollapse(element) {
    element.classList.toggle('collapsed');
    const content = element.nextElementSibling;
    content.classList.toggle('collapsed');
}

/**
 * Set form field value safely
 * @param {string} id - Element ID
 * @param {any} value - Value to set
 * @param {boolean} isCheckbox - Whether the field is a checkbox
 */
function setFieldValue(id, value, isCheckbox = false) {
    const field = document.getElementById(id);
    if (field) {
        if (isCheckbox) {
            field.checked = Boolean(value);
        } else {
            field.value = value || '';
        }
        return true;
    }
    return false;
}

/**
 * Get form field value safely
 * @param {string} id - Element ID
 * @param {boolean} isCheckbox - Whether the field is a checkbox
 * @returns {any} Field value
 */
function getFieldValue(id, isCheckbox = false) {
    const field = document.getElementById(id);
    if (field) {
        return isCheckbox ? field.checked : field.value;
    }
    return isCheckbox ? false : '';
}

/**
 * Copy JSON to clipboard
 */
function copyJSON() {
    const preview = document.getElementById('jsonPreview');
    if (preview && preview.textContent) {
        navigator.clipboard.writeText(preview.textContent).then(() => {
            showToast('JSON copied to clipboard!', 'success');
        }).catch(() => {
            showToast('Failed to copy JSON', 'error');
        });
    }
}

/**
 * Download JSON configuration
 */
function downloadJSON() {
    const config = generateJSON();
    const jsonString = JSON.stringify(config, null, 2);
    const blob = new Blob([jsonString], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `deployment-config-${config.project.name || 'unnamed'}-${Date.now()}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('Configuration downloaded!', 'success');
}

/**
 * Update scenario help text
 */
function updateScenarioHelp() {
    const scenario = document.getElementById('deploymentScenario').value;
    const helpBox = document.getElementById('scenarioHelp');
    const title = document.getElementById('scenarioTitle');
    const description = document.getElementById('scenarioDescription');
    
    const scenarios = {
        'local-ssh': {
            title: 'Local Build + SSH',
            description: 'Build locally and deploy via SSH/SFTP. Best for simple projects with full control.'
        },
        'github-actions': {
            title: 'GitHub Actions',
            description: 'Automated CI/CD with GitHub. Best for team collaboration and automated testing.'
        },
        'deployhq': {
            title: 'DeployHQ Professional',
            description: 'Professional deployment service with advanced features and monitoring.'
        },
        'git-pull': {
            title: 'Git Pull + Manual',
            description: 'Traditional approach using git on server. Best for shared hosting/cPanel.'
        }
    };
    
    if (scenario && scenarios[scenario]) {
        helpBox.style.display = 'block';
        title.textContent = scenarios[scenario].title;
        description.textContent = scenarios[scenario].description;
    } else {
        helpBox.style.display = 'none';
    }
}

/**
 * Update save path
 */
function updateSavePath() {
    const finalPath = document.getElementById('finalSavePath').value;
    if (finalPath) {
        // Save to configuration
        if (window.WizardApp && window.WizardApp.configuration) {
            window.WizardApp.configuration.finalSavePath = finalPath;
            window.WizardApp.saveStepData();
        }
        showToast('Save path updated', 'success');
    }
}

/**
 * Clear all form data
 */
function clearAll() {
    if (confirm('Are you sure you want to clear all data? This cannot be undone.')) {
        // Clear localStorage
        localStorage.removeItem('deploymentConfig');
        localStorage.removeItem('deploymentConfigStep');
        
        // Reset configuration
        if (window.WizardApp) {
            window.WizardApp.configuration = {};
        }
        
        // Clear form fields
        document.querySelectorAll('input, select, textarea').forEach(input => {
            if (input.type === 'checkbox') {
                input.checked = false;
            } else if (input.id !== 'savePath' && input.id !== 'finalSavePath') {
                input.value = '';
            }
        });
        
        // Reset to first step
        if (window.WizardApp && window.WizardApp.currentStep !== 1) {
            document.getElementById(`step${window.WizardApp.currentStep}`).classList.remove('active');
            window.WizardApp.currentStep = 1;
            document.getElementById('step1').classList.add('active');
            window.WizardApp.updateProgressBar();
            window.WizardApp.updateButtons();
            window.WizardApp.updateStepIndicators();
        }
        
        showToast('All data cleared', 'success');
    }
}

/**
 * Auto-save functionality
 */
function startAutoSave() {
    // Auto-save every 30 seconds
    setInterval(() => {
        if (window.WizardApp && window.WizardApp.saveStepData) {
            window.WizardApp.saveStepData();
        }
    }, 30000);
}