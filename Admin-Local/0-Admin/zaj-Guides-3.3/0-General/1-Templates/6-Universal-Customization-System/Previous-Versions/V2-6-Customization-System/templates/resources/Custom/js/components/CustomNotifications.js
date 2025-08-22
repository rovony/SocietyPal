// Custom Notifications Component
// This file is safe from vendor updates

class CustomNotifications {
    constructor(options = {}) {
        this.options = {
            container: options.container || 'custom-notifications-container',
            autoClose: options.autoClose !== false, // Default true
            closeDelay: options.closeDelay || 5000,
            position: options.position || 'top-right', // top-right, top-left, bottom-right, bottom-left
            maxNotifications: options.maxNotifications || 5,
            animations: options.animations !== false, // Default true
            sounds: options.sounds || false,
            ...options
        };
        
        this.notifications = [];
        this.container = null;
        
        this.init();
    }
    
    init() {
        this.createContainer();
        this.bindEvents();
        this.loadFromStorage();
        
        console.log('🔔 Custom Notifications initialized', this.options);
    }
    
    createContainer() {
        // Remove existing container if any
        const existing = document.getElementById(this.options.container);
        if (existing) {
            existing.remove();
        }
        
        // Create new container
        this.container = document.createElement('div');
        this.container.id = this.options.container;
        this.container.className = `custom-notifications custom-notifications--${this.options.position}`;
        
        // Add CSS if not loaded
        if (!document.querySelector('#custom-notifications-styles')) {
            this.injectStyles();
        }
        
        document.body.appendChild(this.container);
    }
    
    injectStyles() {
        const styles = document.createElement('style');
        styles.id = 'custom-notifications-styles';
        styles.textContent = `
            .custom-notifications {
                position: fixed;
                z-index: 10000;
                pointer-events: none;
                max-width: 400px;
                width: 100%;
            }
            
            .custom-notifications--top-right {
                top: 20px;
                right: 20px;
            }
            
            .custom-notifications--top-left {
                top: 20px;
                left: 20px;
            }
            
            .custom-notifications--bottom-right {
                bottom: 20px;
                right: 20px;
            }
            
            .custom-notifications--bottom-left {
                bottom: 20px;
                left: 20px;
            }
            
            .custom-notification {
                pointer-events: auto;
                margin-bottom: 10px;
                padding: 16px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                background: white;
                border-left: 4px solid #007bff;
                position: relative;
                overflow: hidden;
                transition: all 0.3s ease;
                transform: translateX(100%);
                opacity: 0;
            }
            
            .custom-notification.show {
                transform: translateX(0);
                opacity: 1;
            }
            
            .custom-notification.hide {
                transform: translateX(100%);
                opacity: 0;
                margin-bottom: 0;
                padding-top: 0;
                padding-bottom: 0;
                max-height: 0;
            }
            
            .custom-notification--success {
                border-left-color: #28a745;
                background: #f8fff9;
            }
            
            .custom-notification--error {
                border-left-color: #dc3545;
                background: #fff8f8;
            }
            
            .custom-notification--warning {
                border-left-color: #ffc107;
                background: #fffdf8;
            }
            
            .custom-notification--info {
                border-left-color: #17a2b8;
                background: #f8ffff;
            }
            
            .custom-notification__header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 8px;
            }
            
            .custom-notification__title {
                font-weight: 600;
                font-size: 14px;
                color: #333;
                margin: 0;
            }
            
            .custom-notification__close {
                background: none;
                border: none;
                font-size: 18px;
                color: #999;
                cursor: pointer;
                padding: 0;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: color 0.2s ease;
            }
            
            .custom-notification__close:hover {
                color: #666;
            }
            
            .custom-notification__message {
                font-size: 13px;
                color: #666;
                margin: 0;
                line-height: 1.4;
            }
            
            .custom-notification__actions {
                margin-top: 12px;
                display: flex;
                gap: 8px;
            }
            
            .custom-notification__action {
                padding: 6px 12px;
                border: none;
                border-radius: 4px;
                font-size: 12px;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            
            .custom-notification__action--primary {
                background: #007bff;
                color: white;
            }
            
            .custom-notification__action--primary:hover {
                background: #0056b3;
            }
            
            .custom-notification__action--secondary {
                background: #f8f9fa;
                color: #666;
                border: 1px solid #dee2e6;
            }
            
            .custom-notification__action--secondary:hover {
                background: #e9ecef;
            }
            
            .custom-notification__progress {
                position: absolute;
                bottom: 0;
                left: 0;
                height: 2px;
                background: currentColor;
                opacity: 0.3;
                transition: width linear;
            }
            
            @media (max-width: 480px) {
                .custom-notifications {
                    left: 10px !important;
                    right: 10px !important;
                    max-width: none;
                }
                
                .custom-notification {
                    margin-bottom: 8px;
                }
            }
        `;
        
        document.head.appendChild(styles);
    }
    
    bindEvents() {
        // Listen for custom events
        document.addEventListener('custom:notify', (e) => {
            this.show(e.detail);
        });
        
        document.addEventListener('custom:clearNotifications', () => {
            this.clear();
        });
        
        // Handle page unload
        window.addEventListener('beforeunload', () => {
            this.saveToStorage();
        });
    }
    
    show(options) {
        if (typeof options === 'string') {
            options = { message: options };
        }
        
        const notification = {
            id: this.generateId(),
            type: options.type || 'info',
            title: options.title,
            message: options.message || '',
            actions: options.actions || [],
            autoClose: options.autoClose !== false && this.options.autoClose,
            closeDelay: options.closeDelay || this.options.closeDelay,
            persistent: options.persistent || false,
            timestamp: Date.now(),
            ...options
        };
        
        // Limit notifications
        if (this.notifications.length >= this.options.maxNotifications) {
            this.remove(this.notifications[0].id);
        }
        
        this.notifications.push(notification);
        this.render(notification);
        
        // Auto close if enabled
        if (notification.autoClose && !notification.persistent) {
            setTimeout(() => {
                this.remove(notification.id);
            }, notification.closeDelay);
        }
        
        // Play sound if enabled
        if (this.options.sounds) {
            this.playSound(notification.type);
        }
        
        return notification.id;
    }
    
    render(notification) {
        const element = document.createElement('div');
        element.className = `custom-notification custom-notification--${notification.type}`;
        element.dataset.id = notification.id;
        
        let actionsHtml = '';
        if (notification.actions && notification.actions.length > 0) {
            actionsHtml = '<div class="custom-notification__actions">';
            notification.actions.forEach(action => {
                actionsHtml += `
                    <button class="custom-notification__action custom-notification__action--${action.style || 'secondary'}" 
                            data-action="${action.action || ''}">${action.text}</button>
                `;
            });
            actionsHtml += '</div>';
        }
        
        let progressHtml = '';
        if (notification.autoClose && !notification.persistent) {
            progressHtml = `<div class="custom-notification__progress" style="width: 100%; transition-duration: ${notification.closeDelay}ms;"></div>`;
        }
        
        element.innerHTML = `
            <div class="custom-notification__header">
                ${notification.title ? `<h4 class="custom-notification__title">${notification.title}</h4>` : ''}
                <button class="custom-notification__close" data-action="close">&times;</button>
            </div>
            ${notification.message ? `<p class="custom-notification__message">${notification.message}</p>` : ''}
            ${actionsHtml}
            ${progressHtml}
        `;
        
        // Add event listeners
        element.addEventListener('click', (e) => {
            this.handleClick(e, notification);
        });
        
        // Add to container
        this.container.appendChild(element);
        
        // Trigger animation
        setTimeout(() => {
            element.classList.add('show');
            
            // Start progress bar animation
            const progress = element.querySelector('.custom-notification__progress');
            if (progress) {
                setTimeout(() => {
                    progress.style.width = '0%';
                }, 100);
            }
        }, 10);
    }
    
    handleClick(e, notification) {
        const action = e.target.dataset.action;
        
        if (action === 'close') {
            this.remove(notification.id);
            return;
        }
        
        if (action && notification.callback) {
            notification.callback(action, notification);
        }
        
        // Auto-close unless specified otherwise
        const actionConfig = notification.actions?.find(a => a.action === action);
        if (!actionConfig || actionConfig.autoClose !== false) {
            this.remove(notification.id);
        }
    }
    
    remove(id) {
        const notification = this.notifications.find(n => n.id === id);
        const element = this.container.querySelector(`[data-id="${id}"]`);
        
        if (!element) return;
        
        element.classList.add('hide');
        
        setTimeout(() => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
            this.notifications = this.notifications.filter(n => n.id !== id);
        }, 300);
    }
    
    clear() {
        this.notifications.forEach(notification => {
            this.remove(notification.id);
        });
    }
    
    success(message, options = {}) {
        return this.show({ ...options, type: 'success', message });
    }
    
    error(message, options = {}) {
        return this.show({ ...options, type: 'error', message });
    }
    
    warning(message, options = {}) {
        return this.show({ ...options, type: 'warning', message });
    }
    
    info(message, options = {}) {
        return this.show({ ...options, type: 'info', message });
    }
    
    generateId() {
        return 'notif_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    playSound(type) {
        // Simple sound implementation
        if (typeof Audio !== 'undefined') {
            try {
                const context = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = context.createOscillator();
                const gainNode = context.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(context.destination);
                
                // Different frequencies for different types
                const frequencies = {
                    success: 800,
                    error: 300,
                    warning: 600,
                    info: 500
                };
                
                oscillator.frequency.value = frequencies[type] || 500;
                oscillator.type = 'sine';
                
                gainNode.gain.setValueAtTime(0.1, context.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, context.currentTime + 0.3);
                
                oscillator.start(context.currentTime);
                oscillator.stop(context.currentTime + 0.3);
            } catch (e) {
                console.warn('Custom notifications: Could not play sound', e);
            }
        }
    }
    
    saveToStorage() {
        if (typeof localStorage !== 'undefined') {
            const persistentNotifications = this.notifications.filter(n => n.persistent);
            if (persistentNotifications.length > 0) {
                localStorage.setItem('custom_notifications', JSON.stringify(persistentNotifications));
            }
        }
    }
    
    loadFromStorage() {
        if (typeof localStorage !== 'undefined') {
            try {
                const stored = localStorage.getItem('custom_notifications');
                if (stored) {
                    const notifications = JSON.parse(stored);
                    notifications.forEach(notification => {
                        this.show({ ...notification, autoClose: false });
                    });
                    localStorage.removeItem('custom_notifications');
                }
            } catch (e) {
                console.warn('Custom notifications: Could not load from storage', e);
            }
        }
    }
    
    // Utility methods
    getNotifications() {
        return [...this.notifications];
    }
    
    getNotification(id) {
        return this.notifications.find(n => n.id === id);
    }
    
    updateNotification(id, updates) {
        const notification = this.notifications.find(n => n.id === id);
        if (notification) {
            Object.assign(notification, updates);
            // Re-render if needed
            const element = this.container.querySelector(`[data-id="${id}"]`);
            if (element) {
                this.remove(id);
                this.render(notification);
            }
        }
    }
    
    count() {
        return this.notifications.length;
    }
    
    destroy() {
        this.clear();
        if (this.container && this.container.parentNode) {
            this.container.parentNode.removeChild(this.container);
        }
        
        // Remove event listeners
        document.removeEventListener('custom:notify', this.handleCustomNotify);
        document.removeEventListener('custom:clearNotifications', this.handleClearNotifications);
    }
}

// Auto-initialize if notifications are enabled
if (Custom.isEnabled('custom_notifications')) {
    window.customNotifications = new CustomNotifications();
    
    // Global helper functions
    window.notify = {
        show: (message, options) => window.customNotifications.show(message, options),
        success: (message, options) => window.customNotifications.success(message, options),
        error: (message, options) => window.customNotifications.error(message, options),
        warning: (message, options) => window.customNotifications.warning(message, options),
        info: (message, options) => window.customNotifications.info(message, options),
        clear: () => window.customNotifications.clear()
    };
}

// Export for manual use
export default CustomNotifications;