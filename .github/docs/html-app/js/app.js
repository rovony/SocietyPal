// GitHub CI/CD Learning Hub - Interactive Application
class GitHubLearningHub {
    constructor() {
        this.currentSection = 'learn';
        this.progress = {
            basics: 0,
            laravel: 0,
            security: 0,
            advanced: 0,
            steps: 0,
            checklists: 0
        };
        this.folderProgress = {
            'initial-setup': 0,
            'environment-config': 0,
            'secrets-config': 0,
            'first-deployment': 0
        };
        this.init();
    }

    init() {
        this.setupNavigation();
        this.setupModuleCards();
        this.setupStepItems();
        this.setupChecklists();
        this.setupFolderInteraction();
        this.loadProgress();
        this.updateOverallProgress();
        this.setupKeyboardNavigation();
    }

    // Navigation Setup
    setupNavigation() {
        const navButtons = document.querySelectorAll('.nav-btn');
        const sections = document.querySelectorAll('.content-section');

        navButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetSection = btn.dataset.section;
                this.switchSection(targetSection);
            });
        });
    }

    switchSection(sectionName) {
        // Update navigation buttons
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.section === sectionName) {
                btn.classList.add('active');
            }
        });

        // Update content sections
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.remove('active');
            if (section.id === sectionName) {
                section.classList.add('active');
            }
        });

        this.currentSection = sectionName;
        this.updateOverallProgress();

        // Track section change
        this.trackEvent('section_changed', { section: sectionName });
    }

    // Module Cards Setup
    setupModuleCards() {
        const startButtons = document.querySelectorAll('.start-btn');

        startButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const moduleCard = e.target.closest('.module-card');
                const moduleName = moduleCard.dataset.module;
                this.startModule(moduleName);
            });
        });
    }

    startModule(moduleName) {
        // Simulate module progress
        const progressInterval = setInterval(() => {
            if (this.progress[moduleName] < 100) {
                this.progress[moduleName] += Math.random() * 15;
                if (this.progress[moduleName] > 100) {
                    this.progress[moduleName] = 100;
                }
                this.updateModuleProgress(moduleName);
                this.updateOverallProgress();
            } else {
                clearInterval(progressInterval);
                this.completeModule(moduleName);
            }
        }, 500);

        // Store progress in localStorage
        this.saveProgress();
        this.trackEvent('module_started', { module: moduleName });
    }

    updateModuleProgress(moduleName) {
        const moduleCard = document.querySelector(`[data-module="${moduleName}"]`);
        if (moduleCard) {
            const progressFill = moduleCard.querySelector('.progress-fill');
            const progressText = moduleCard.querySelector('.progress-text');

            if (progressFill && progressText) {
                progressFill.style.width = `${this.progress[moduleName]}%`;
                progressText.textContent = `${Math.round(this.progress[moduleName])}% Complete`;
            }
        }
    }

    completeModule(moduleName) {
        const moduleCard = document.querySelector(`[data-module="${moduleName}"]`);
        if (moduleCard) {
            const startBtn = moduleCard.querySelector('.start-btn');
            startBtn.textContent = 'Completed!';
            startBtn.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
            startBtn.disabled = true;

            this.showNotification(`🎉 Module "${moduleName}" completed!`, 'success');
            this.trackEvent('module_completed', { module: moduleName });
        }
    }

    // Step Items Setup with Folder Support
    setupStepItems() {
        const stepItems = document.querySelectorAll('.step-item');

        stepItems.forEach((item, index) => {
            item.addEventListener('click', () => {
                this.completeStep(item, index + 1);
            });
        });
    }

    completeStep(stepItem, stepNumber) {
        if (!stepItem.classList.contains('completed')) {
            stepItem.classList.add('completed');
            const icon = stepItem.querySelector('.step-icon');
            icon.className = 'fas fa-check-circle step-icon';

            // Update steps progress
            const completedSteps = document.querySelectorAll('.step-item.completed').length;
            const totalSteps = document.querySelectorAll('.step-item').length;
            this.progress.steps = (completedSteps / totalSteps) * 100;

            // Update folder progress
            this.updateFolderProgress();

            this.updateOverallProgress();
            this.saveProgress();

            // Show completion notification
            const stepText = stepItem.querySelector('.step-text').textContent;
            this.showNotification(`✅ Step completed: ${stepText}`, 'success');
            this.trackEvent('step_completed', { step: stepNumber, text: stepText });
        }
    }

    // Folder Interaction Setup
    setupFolderInteraction() {
        const stepFolders = document.querySelectorAll('.step-folder');

        stepFolders.forEach(folder => {
            const folderHeader = folder.querySelector('.folder-header');
            const stepList = folder.querySelector('.step-list');

            if (folderHeader && stepList) {
                folderHeader.addEventListener('click', () => {
                    this.toggleFolder(folder, stepList);
                });

                // Add folder progress indicator
                this.addFolderProgressIndicator(folder);
            }
        });
    }

    toggleFolder(folder, stepList) {
        const isExpanded = stepList.style.display !== 'none';

        if (isExpanded) {
            stepList.style.display = 'none';
            folder.classList.remove('expanded');
        } else {
            stepList.style.display = 'block';
            folder.classList.add('expanded');
        }

        this.trackEvent('folder_toggled', {
            folder: folder.querySelector('h3').textContent,
            expanded: !isExpanded
        });
    }

    addFolderProgressIndicator(folder) {
        const folderHeader = folder.querySelector('.folder-header');
        const folderCount = folder.querySelector('.folder-count');

        if (folderHeader && folderCount) {
            // Add progress bar to folder header
            const progressBar = document.createElement('div');
            progressBar.className = 'folder-progress-bar';
            progressBar.style.cssText = `
                width: 100%;
                height: 4px;
                background: #e0e0e0;
                border-radius: 2px;
                overflow: hidden;
                margin-top: 10px;
            `;

            const progressFill = document.createElement('div');
            progressFill.className = 'folder-progress-fill';
            progressFill.style.cssText = `
                height: 100%;
                background: linear-gradient(90deg, #667eea, #764ba2);
                transition: width 0.3s ease;
                width: 0%;
            `;

            progressBar.appendChild(progressFill);
            folderHeader.appendChild(progressBar);
        }
    }

    updateFolderProgress() {
        const folders = document.querySelectorAll('.step-folder');

        folders.forEach((folder, folderIndex) => {
            const steps = folder.querySelectorAll('.step-item');
            const completedSteps = folder.querySelectorAll('.step-item.completed');
            const progressPercentage = (completedSteps.length / steps.length) * 100;

            // Update folder progress
            const folderKey = this.getFolderKey(folderIndex);
            this.folderProgress[folderKey] = progressPercentage;

            // Update visual progress
            const progressFill = folder.querySelector('.folder-progress-fill');
            if (progressFill) {
                progressFill.style.width = `${progressPercentage}%`;
            }

            // Update folder count with progress
            const folderCount = folder.querySelector('.folder-count');
            if (folderCount) {
                folderCount.textContent = `${completedSteps.length}/${steps.length} steps`;

                // Change color based on completion
                if (progressPercentage === 100) {
                    folderCount.style.background = '#28a745';
                } else if (progressPercentage > 50) {
                    folderCount.style.background = '#ffc107';
                } else {
                    folderCount.style.background = '#667eea';
                }
            }
        });
    }

    getFolderKey(folderIndex) {
        const keys = ['initial-setup', 'environment-config', 'secrets-config', 'first-deployment'];
        return keys[folderIndex] || `folder-${folderIndex}`;
    }

    // Checklists Setup
    setupChecklists() {
        const checkboxes = document.querySelectorAll('.checklist-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.updateChecklistProgress();
            });
        });
    }

    updateChecklistProgress() {
        const allCheckboxes = document.querySelectorAll('.checklist-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.checklist-checkbox:checked');

        this.progress.checklists = (checkedCheckboxes.length / allCheckboxes.length) * 100;
        this.updateOverallProgress();
        this.saveProgress();

        // Track checklist progress
        this.trackEvent('checklist_updated', {
            completed: checkedCheckboxes.length,
            total: allCheckboxes.length
        });
    }

    // Progress Management
    updateOverallProgress() {
        const progressValues = Object.values(this.progress);
        const averageProgress = progressValues.reduce((sum, val) => sum + val, 0) / progressValues.length;

        const overallProgressBar = document.getElementById('overall-progress');
        const overallProgressText = document.querySelector('.progress-text');

        if (overallProgressBar && overallProgressText) {
            overallProgressBar.style.width = `${averageProgress}%`;
            overallProgressText.textContent = `Overall Progress: ${Math.round(averageProgress)}%`;
        }
    }

    // Local Storage
    saveProgress() {
        const progressData = {
            progress: this.progress,
            folderProgress: this.folderProgress,
            timestamp: new Date().toISOString()
        };
        localStorage.setItem('github-learning-progress', JSON.stringify(progressData));
    }

    loadProgress() {
        const savedProgress = localStorage.getItem('github-learning-progress');
        if (savedProgress) {
            const data = JSON.parse(savedProgress);
            this.progress = { ...this.progress, ...data.progress };

            if (data.folderProgress) {
                this.folderProgress = { ...this.folderProgress, ...data.folderProgress };
            }

            // Update UI with saved progress
            Object.keys(this.progress).forEach(moduleName => {
                if (moduleName !== 'steps' && moduleName !== 'checklists') {
                    this.updateModuleProgress(moduleName);
                }
            });

            // Update checklists and folder progress
            this.updateChecklistProgress();
            this.updateFolderProgress();
        }
    }

    // Utility Functions
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;

        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Keyboard Navigation
    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            switch(e.key) {
                case '1':
                    this.switchSection('learn');
                    break;
                case '2':
                    this.switchSection('steps');
                    break;
                case '3':
                    this.switchSection('commands');
                    break;
                case '4':
                    this.switchSection('checklists');
                    break;
                case '5':
                    this.switchSection('structure');
                    break;
                case '6':
                    this.switchSection('resources');
                    break;
                case 'Escape':
                    // Reset current module progress
                    if (this.currentSection === 'learn') {
                        this.resetModuleProgress();
                    }
                    break;
                case 'h':
                case 'H':
                    // Show help
                    this.showHelp();
                    break;
                case 'f':
                case 'F':
                    // Toggle all folders
                    this.toggleAllFolders();
                    break;
            }
        });
    }

    toggleAllFolders() {
        const folders = document.querySelectorAll('.step-folder');
        const stepLists = document.querySelectorAll('.step-list');

        const isAnyExpanded = Array.from(stepLists).some(list => list.style.display !== 'none');

        stepLists.forEach(list => {
            list.style.display = isAnyExpanded ? 'none' : 'block';
        });

        folders.forEach(folder => {
            if (isAnyExpanded) {
                folder.classList.remove('expanded');
            } else {
                folder.classList.add('expanded');
            }
        });

        this.showNotification(
            isAnyExpanded ? 'All folders collapsed' : 'All folders expanded',
            'info'
        );
    }

    resetModuleProgress() {
        Object.keys(this.progress).forEach(moduleName => {
            if (moduleName !== 'steps' && moduleName !== 'checklists') {
                this.progress[moduleName] = 0;
                this.updateModuleProgress(moduleName);
            }
        });

        // Reset folder progress
        Object.keys(this.folderProgress).forEach(key => {
            this.folderProgress[key] = 0;
        });

        this.updateOverallProgress();
        this.updateFolderProgress();
        this.saveProgress();
        this.showNotification('Module progress reset!', 'info');
        this.trackEvent('progress_reset');
    }

    showHelp() {
        const helpMessage = `
🚀 GitHub CI/CD Learning Hub - Keyboard Shortcuts

Navigation:
• 1 - Learn section
• 2 - Steps section
• 3 - Commands section
• 4 - Checklists section
• 5 - Structure section
• 6 - Resources section

Actions:
• Escape - Reset module progress
• H - Show this help
• F - Toggle all folders

Happy Learning! 🎓
        `;

        this.showNotification('Check console for keyboard shortcuts!', 'info');
        console.log(helpMessage);
    }

    // Analytics and Tracking
    trackEvent(eventName, data = {}) {
        // In a real app, you'd send this to analytics
        console.log('Event tracked:', eventName, data);

        // Store locally for demo purposes
        const events = JSON.parse(localStorage.getItem('github-learning-events') || '[]');
        events.push({
            event: eventName,
            data: data,
            timestamp: new Date().toISOString()
        });
        localStorage.setItem('github-learning-events', JSON.stringify(events));
    }

    // Export Progress
    exportProgress() {
        const progressData = {
            progress: this.progress,
            folderProgress: this.folderProgress,
            timestamp: new Date().toISOString(),
            version: '1.1.0'
        };

        const blob = new Blob([JSON.stringify(progressData, null, 2)], {
            type: 'application/json'
        });

        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'github-learning-progress.json';
        a.click();

        URL.revokeObjectURL(url);
        this.showNotification('Progress exported successfully!', 'success');
        this.trackEvent('progress_exported');
    }

    // Import Progress
    importProgress(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                const data = JSON.parse(e.target.result);
                if (data.progress) {
                    this.progress = { ...this.progress, ...data.progress };
                    if (data.folderProgress) {
                        this.folderProgress = { ...this.folderProgress, ...data.folderProgress };
                    }
                    this.loadProgress();
                    this.updateOverallProgress();
                    this.updateFolderProgress();
                    this.showNotification('Progress imported successfully!', 'success');
                    this.trackEvent('progress_imported');
                }
            } catch (error) {
                this.showNotification('Invalid progress file!', 'error');
            }
        };
        reader.readAsText(file);
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const app = new GitHubLearningHub();

    // Make app globally accessible for debugging
    window.githubLearningHub = app;

    // Add export/import functionality
    const exportBtn = document.createElement('button');
    exportBtn.textContent = '📊 Export Progress';
    exportBtn.style.cssText = `
        position: fixed;
        top: 20px;
        left: 20px;
        background: rgba(255,255,255,0.9);
        border: none;
        padding: 10px 15px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    `;
    exportBtn.addEventListener('click', () => app.exportProgress());
    document.body.appendChild(exportBtn);

    // Add import functionality
    const importInput = document.createElement('input');
    importInput.type = 'file';
    importInput.accept = '.json';
    importInput.style.display = 'none';
    importInput.addEventListener('change', (e) => {
        if (e.target.files[0]) {
            app.importProgress(e.target.files[0]);
        }
    });

    const importBtn = document.createElement('button');
    importBtn.textContent = '📥 Import Progress';
    importBtn.style.cssText = `
        position: fixed;
        top: 60px;
        left: 20px;
        background: rgba(255,255,255,0.9);
        border: none;
        padding: 10px 15px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    `;
    importBtn.addEventListener('click', () => importInput.click());
    document.body.appendChild(importBtn);
    document.body.appendChild(importInput);

    // Welcome message
    setTimeout(() => {
        app.showNotification('Welcome to GitHub CI/CD Learning Hub! 🚀', 'success');
    }, 1000);
});

// Add some helpful console messages
console.log(`
🚀 GitHub CI/CD Learning Hub Loaded! (v1.1.0)

Keyboard Shortcuts:
- 1: Switch to Learn section
- 2: Switch to Steps section
- 3: Switch to Commands section
- 4: Switch to Checklists section
- 5: Switch to Structure section
- 6: Switch to Resources section
- Escape: Reset module progress
- H: Show help
- F: Toggle all folders

New Features:
- Folder-based step organization with progress tracking
- Enhanced numbering system (1.1, 1.2, 2.1, 2.2, etc.)
- Interactive folder headers with progress indicators
- Improved step completion tracking
- Better visual organization and hierarchy

Happy Learning! 🎓
`);
