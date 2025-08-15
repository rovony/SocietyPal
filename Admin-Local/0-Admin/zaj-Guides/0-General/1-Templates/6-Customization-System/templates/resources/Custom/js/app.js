// Custom Application JavaScript
// This file is safe from vendor updates

// Import custom components
import './components/CustomDashboard';
import './components/CustomNotifications';
import './components/CustomTheme';

// Custom app initialization
class CustomApp {
    constructor() {
        this.version = document.querySelector('meta[name="custom-version"]')?.content || '1.0.0';
        this.debug = document.querySelector('meta[name="app-debug"]')?.content === 'true';
        
        this.init();
    }
    
    init() {
        console.log(`🛡️ Custom Layer v${this.version} initialized`);
        
        // Initialize custom components
        this.initializeFeatures();
        this.bindEvents();
        
        if (this.debug) {
            this.showCustomIndicators();
        }
    }
    
    initializeFeatures() {
        // Initialize enabled features based on config
        const features = window.customConfig?.features || {};
        
        Object.keys(features).forEach(feature => {
            if (features[feature]) {
                console.log(`✅ Custom feature enabled: ${feature}`);
                this.initializeFeature(feature);
            }
        });
    }
    
    initializeFeature(featureName) {
        // Feature initialization logic
        const initMethod = `init${this.capitalize(featureName)}`;
        if (typeof this[initMethod] === 'function') {
            this[initMethod]();
        }
    }
    
    bindEvents() {
        // Custom event bindings
        document.addEventListener('DOMContentLoaded', () => {
            console.log('🎯 Custom layer DOM ready');
        });
        
        // Custom layer identification for debugging
        if (this.debug) {
            document.addEventListener('click', (e) => {
                if (e.target.className && e.target.className.includes('custom-')) {
                    console.log('🛡️ Custom element clicked:', e.target);
                }
            });
        }
    }
    
    showCustomIndicators() {
        // Show visual indicators for custom elements in debug mode
        document.querySelectorAll('[class*="custom-"]').forEach(el => {
            el.style.outline = '2px dashed #28a745';
            el.title = '🛡️ Custom Layer Element';
        });
        
        // Add custom layer badge to body
        const badge = document.createElement('div');
        badge.innerHTML = '🛡️ Custom Layer Active';
        badge.style.cssText = `
            position: fixed;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            z-index: 10000;
            font-family: monospace;
        `;
        document.body.appendChild(badge);
        
        console.log('🔍 Debug mode: Custom layer indicators enabled');
    }
    
    // Feature initialization methods
    initCustomDashboard() {
        console.log('🎯 Initializing custom dashboard...');
        // Custom dashboard initialization logic here
    }
    
    initCustomAuth() {
        console.log('🔐 Initializing custom auth...');
        // Custom auth initialization logic here
    }
    
    initCustomNotifications() {
        console.log('🔔 Initializing custom notifications...');
        // Custom notifications initialization logic here
    }
    
    initCustomReports() {
        console.log('📊 Initializing custom reports...');
        // Custom reports initialization logic here
    }
    
    initSaasMode() {
        console.log('☁️ Initializing SaaS mode...');
        // SaaS mode initialization logic here
    }
    
    initMultiTenant() {
        console.log('🏢 Initializing multi-tenant features...');
        // Multi-tenant initialization logic here
    }
    
    initCustomApi() {
        console.log('🔌 Initializing custom API...');
        // Custom API initialization logic here
    }
    
    initCustomWebhooks() {
        console.log('🔗 Initializing custom webhooks...');
        // Custom webhooks initialization logic here
    }
    
    // Utility methods
    capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1).replace(/_/g, '');
    }
    
    // Asset helper methods
    getCustomAsset(path) {
        return `/Custom/${path}`;
    }
    
    loadCustomStyles(filename = 'app.css') {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = this.getCustomAsset(`css/${filename}`);
        document.head.appendChild(link);
    }
    
    // Configuration helpers
    getCustomConfig(key, defaultValue = null) {
        const config = window.customConfig || {};
        return key.split('.').reduce((obj, k) => obj?.[k], config) ?? defaultValue;
    }
    
    isFeatureEnabled(feature) {
        return this.getCustomConfig(`features.${feature}`, false);
    }
    
    getBrandingConfig(key, defaultValue = null) {
        return this.getCustomConfig(`branding.${key}`, defaultValue);
    }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new CustomApp());
} else {
    new CustomApp();
}

// Export for manual initialization if needed
window.CustomApp = CustomApp;

// Global custom utilities
window.Custom = {
    App: CustomApp,
    
    // Quick feature check
    isEnabled: (feature) => {
        return window.customConfig?.features?.[feature] || false;
    },
    
    // Quick config access
    config: (key, defaultValue = null) => {
        const config = window.customConfig || {};
        return key.split('.').reduce((obj, k) => obj?.[k], config) ?? defaultValue;
    },
    
    // Asset helper
    asset: (path) => `/Custom/${path}`,
    
    // Debug logger
    log: (...args) => {
        if (window.customConfig?.debug || document.querySelector('meta[name="app-debug"]')?.content === 'true') {
            console.log('🛡️ Custom:', ...args);
        }
    }
};