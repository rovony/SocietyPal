// Custom Theme Component
// This file is safe from vendor updates

class CustomTheme {
    constructor(options = {}) {
        this.options = {
            autoDetect: options.autoDetect !== false, // Default true
            storage: options.storage !== false, // Default true
            storageKey: options.storageKey || 'custom_theme_settings',
            transitions: options.transitions !== false, // Default true
            rtlSupport: options.rtlSupport || false,
            colorSchemes: options.colorSchemes || ['auto', 'light', 'dark'],
            defaultTheme: options.defaultTheme || 'auto',
            ...options
        };
        
        this.currentTheme = null;
        this.systemTheme = null;
        this.settings = {
            colorScheme: this.options.defaultTheme,
            primaryColor: null,
            accentColor: null,
            fontSize: 'medium',
            density: 'comfortable',
            animations: true,
            rtl: false
        };
        
        this.init();
    }
    
    init() {
        this.loadSettings();
        this.detectSystemPreferences();
        this.createStyleSheet();
        this.bindEvents();
        this.applyTheme();
        
        console.log('🎨 Custom Theme initialized', this.settings);
    }
    
    loadSettings() {
        if (this.options.storage && typeof localStorage !== 'undefined') {
            try {
                const stored = localStorage.getItem(this.options.storageKey);
                if (stored) {
                    this.settings = { ...this.settings, ...JSON.parse(stored) };
                }
            } catch (e) {
                console.warn('Custom Theme: Could not load settings from storage', e);
            }
        }
        
        // Load from Laravel config if available
        if (typeof window.customConfig !== 'undefined' && window.customConfig.theme) {
            this.settings = { ...this.settings, ...window.customConfig.theme };
        }
    }
    
    saveSettings() {
        if (this.options.storage && typeof localStorage !== 'undefined') {
            try {
                localStorage.setItem(this.options.storageKey, JSON.stringify(this.settings));
            } catch (e) {
                console.warn('Custom Theme: Could not save settings to storage', e);
            }
        }
    }
    
    detectSystemPreferences() {
        if (typeof window.matchMedia !== 'undefined') {
            // Dark mode preference
            const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
            this.systemTheme = darkModeQuery.matches ? 'dark' : 'light';
            
            // Listen for changes
            darkModeQuery.addEventListener('change', (e) => {
                this.systemTheme = e.matches ? 'dark' : 'light';
                if (this.settings.colorScheme === 'auto') {
                    this.applyTheme();
                }
            });
            
            // Reduced motion preference
            const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
            if (motionQuery.matches && this.settings.animations !== false) {
                this.settings.animations = false;
            }
            
            motionQuery.addEventListener('change', (e) => {
                if (e.matches) {
                    this.updateSetting('animations', false);
                }
            });
            
            // High contrast preference
            const contrastQuery = window.matchMedia('(prefers-contrast: high)');
            if (contrastQuery.matches) {
                document.documentElement.classList.add('custom-theme--high-contrast');
            }
            
            contrastQuery.addEventListener('change', (e) => {
                document.documentElement.classList.toggle('custom-theme--high-contrast', e.matches);
            });
        }
    }
    
    createStyleSheet() {
        // Remove existing stylesheet if any
        const existing = document.querySelector('#custom-theme-styles');
        if (existing) {
            existing.remove();
        }
        
        const stylesheet = document.createElement('style');
        stylesheet.id = 'custom-theme-styles';
        stylesheet.textContent = `
            :root {
                /* Default theme variables */
                --custom-primary: #3490dc;
                --custom-primary-dark: #2779bd;
                --custom-primary-light: #6cb2eb;
                --custom-accent: #f39c12;
                --custom-accent-dark: #e67e22;
                --custom-accent-light: #f8c471;
                
                /* Neutral colors */
                --custom-gray-50: #f9fafb;
                --custom-gray-100: #f3f4f6;
                --custom-gray-200: #e5e7eb;
                --custom-gray-300: #d1d5db;
                --custom-gray-400: #9ca3af;
                --custom-gray-500: #6b7280;
                --custom-gray-600: #4b5563;
                --custom-gray-700: #374151;
                --custom-gray-800: #1f2937;
                --custom-gray-900: #111827;
                
                /* Semantic colors */
                --custom-success: #10b981;
                --custom-warning: #f59e0b;
                --custom-error: #ef4444;
                --custom-info: #3b82f6;
                
                /* Typography */
                --custom-font-size-xs: 0.75rem;
                --custom-font-size-sm: 0.875rem;
                --custom-font-size-base: 1rem;
                --custom-font-size-lg: 1.125rem;
                --custom-font-size-xl: 1.25rem;
                --custom-font-size-2xl: 1.5rem;
                --custom-font-size-3xl: 1.875rem;
                --custom-font-size-4xl: 2.25rem;
                
                /* Spacing */
                --custom-spacing-xs: 0.25rem;
                --custom-spacing-sm: 0.5rem;
                --custom-spacing-md: 1rem;
                --custom-spacing-lg: 1.5rem;
                --custom-spacing-xl: 2rem;
                --custom-spacing-2xl: 3rem;
                
                /* Border radius */
                --custom-radius-sm: 0.25rem;
                --custom-radius-md: 0.375rem;
                --custom-radius-lg: 0.5rem;
                --custom-radius-xl: 1rem;
                --custom-radius-full: 9999px;
                
                /* Shadows */
                --custom-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                --custom-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                --custom-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                
                /* Transitions */
                --custom-transition-fast: 150ms ease;
                --custom-transition-normal: 300ms ease;
                --custom-transition-slow: 500ms ease;
            }
            
            /* Light theme */
            [data-theme="light"], :root {
                --custom-bg-primary: #ffffff;
                --custom-bg-secondary: #f8fafc;
                --custom-bg-tertiary: #f1f5f9;
                --custom-text-primary: #1e293b;
                --custom-text-secondary: #475569;
                --custom-text-tertiary: #64748b;
                --custom-border-color: #e2e8f0;
                --custom-border-light: #f1f5f9;
            }
            
            /* Dark theme */
            [data-theme="dark"] {
                --custom-bg-primary: #0f172a;
                --custom-bg-secondary: #1e293b;
                --custom-bg-tertiary: #334155;
                --custom-text-primary: #f8fafc;
                --custom-text-secondary: #cbd5e1;
                --custom-text-tertiary: #94a3b8;
                --custom-border-color: #334155;
                --custom-border-light: #1e293b;
                
                /* Adjust colors for dark theme */
                --custom-gray-50: #1f2937;
                --custom-gray-100: #374151;
                --custom-gray-200: #4b5563;
                --custom-gray-300: #6b7280;
                --custom-gray-400: #9ca3af;
                --custom-gray-500: #d1d5db;
                --custom-gray-600: #e5e7eb;
                --custom-gray-700: #f3f4f6;
                --custom-gray-800: #f9fafb;
                --custom-gray-900: #ffffff;
            }
            
            /* Font size variations */
            [data-font-size="small"] {
                --custom-font-size-base: 0.875rem;
                --custom-font-size-sm: 0.75rem;
                --custom-font-size-lg: 1rem;
                --custom-font-size-xl: 1.125rem;
            }
            
            [data-font-size="large"] {
                --custom-font-size-base: 1.125rem;
                --custom-font-size-sm: 1rem;
                --custom-font-size-lg: 1.25rem;
                --custom-font-size-xl: 1.5rem;
            }
            
            /* Density variations */
            [data-density="compact"] {
                --custom-spacing-xs: 0.125rem;
                --custom-spacing-sm: 0.25rem;
                --custom-spacing-md: 0.5rem;
                --custom-spacing-lg: 0.75rem;
                --custom-spacing-xl: 1rem;
            }
            
            [data-density="spacious"] {
                --custom-spacing-xs: 0.375rem;
                --custom-spacing-sm: 0.75rem;
                --custom-spacing-md: 1.5rem;
                --custom-spacing-lg: 2rem;
                --custom-spacing-xl: 3rem;
            }
            
            /* RTL Support */
            [dir="rtl"] {
                --custom-text-align: right;
                --custom-float: right;
            }
            
            [dir="ltr"] {
                --custom-text-align: left;
                --custom-float: left;
            }
            
            /* Reduced motion */
            [data-animations="false"] * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            
            /* High contrast mode */
            .custom-theme--high-contrast {
                --custom-border-color: #000000;
                --custom-text-primary: #000000;
            }
            
            .custom-theme--high-contrast [data-theme="dark"] {
                --custom-border-color: #ffffff;
                --custom-text-primary: #ffffff;
            }
            
            /* Utility classes */
            .custom-theme-transition {
                transition: background-color var(--custom-transition-normal),
                           color var(--custom-transition-normal),
                           border-color var(--custom-transition-normal);
            }
        `;
        
        document.head.appendChild(stylesheet);
    }
    
    bindEvents() {
        // Listen for custom theme events
        document.addEventListener('custom:setTheme', (e) => {
            this.setColorScheme(e.detail.scheme || e.detail);
        });
        
        document.addEventListener('custom:updateTheme', (e) => {
            this.updateSettings(e.detail);
        });
        
        document.addEventListener('custom:resetTheme', () => {
            this.reset();
        });
        
        // Add theme switcher if enabled
        if (this.options.autoDetect) {
            this.createThemeSwitcher();
        }
    }
    
    applyTheme() {
        const resolvedScheme = this.resolveColorScheme();
        this.currentTheme = resolvedScheme;
        
        // Apply to document
        document.documentElement.setAttribute('data-theme', resolvedScheme);
        document.documentElement.setAttribute('data-font-size', this.settings.fontSize);
        document.documentElement.setAttribute('data-density', this.settings.density);
        document.documentElement.setAttribute('data-animations', this.settings.animations);
        
        // Apply RTL if enabled
        if (this.settings.rtl) {
            document.documentElement.setAttribute('dir', 'rtl');
        } else {
            document.documentElement.setAttribute('dir', 'ltr');
        }
        
        // Apply custom colors if set
        if (this.settings.primaryColor) {
            document.documentElement.style.setProperty('--custom-primary', this.settings.primaryColor);
        }
        
        if (this.settings.accentColor) {
            document.documentElement.style.setProperty('--custom-accent', this.settings.accentColor);
        }
        
        // Add theme transition class to body
        if (this.options.transitions) {
            document.body.classList.add('custom-theme-transition');
        }
        
        // Dispatch event
        document.dispatchEvent(new CustomEvent('custom:themeChanged', {
            detail: {
                theme: resolvedScheme,
                settings: this.settings
            }
        }));
    }
    
    resolveColorScheme() {
        if (this.settings.colorScheme === 'auto') {
            return this.systemTheme || 'light';
        }
        return this.settings.colorScheme;
    }
    
    setColorScheme(scheme) {
        if (this.options.colorSchemes.includes(scheme)) {
            this.updateSetting('colorScheme', scheme);
        }
    }
    
    setPrimaryColor(color) {
        this.updateSetting('primaryColor', color);
    }
    
    setAccentColor(color) {
        this.updateSetting('accentColor', color);
    }
    
    setFontSize(size) {
        const validSizes = ['small', 'medium', 'large'];
        if (validSizes.includes(size)) {
            this.updateSetting('fontSize', size);
        }
    }
    
    setDensity(density) {
        const validDensities = ['compact', 'comfortable', 'spacious'];
        if (validDensities.includes(density)) {
            this.updateSetting('density', density);
        }
    }
    
    toggleAnimations() {
        this.updateSetting('animations', !this.settings.animations);
    }
    
    toggleRTL() {
        this.updateSetting('rtl', !this.settings.rtl);
    }
    
    updateSetting(key, value) {
        this.settings[key] = value;
        this.applyTheme();
        this.saveSettings();
    }
    
    updateSettings(settings) {
        this.settings = { ...this.settings, ...settings };
        this.applyTheme();
        this.saveSettings();
    }
    
    reset() {
        this.settings = {
            colorScheme: this.options.defaultTheme,
            primaryColor: null,
            accentColor: null,
            fontSize: 'medium',
            density: 'comfortable',
            animations: true,
            rtl: false
        };
        
        // Remove custom properties
        document.documentElement.style.removeProperty('--custom-primary');
        document.documentElement.style.removeProperty('--custom-accent');
        
        this.applyTheme();
        this.saveSettings();
    }
    
    createThemeSwitcher() {
        // Only create if not already exists
        if (document.querySelector('#custom-theme-switcher')) {
            return;
        }
        
        const switcher = document.createElement('div');
        switcher.id = 'custom-theme-switcher';
        switcher.innerHTML = `
            <button class="custom-theme-toggle" title="Toggle theme">
                <span class="custom-theme-toggle__icon"></span>
            </button>
        `;
        
        // Add styles
        const switcherStyles = document.createElement('style');
        switcherStyles.textContent = `
            #custom-theme-switcher {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 9999;
            }
            
            .custom-theme-toggle {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                border: none;
                background: var(--custom-primary);
                color: white;
                cursor: pointer;
                box-shadow: var(--custom-shadow-lg);
                transition: all var(--custom-transition-normal);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .custom-theme-toggle:hover {
                transform: scale(1.1);
                box-shadow: var(--custom-shadow-xl);
            }
            
            .custom-theme-toggle__icon::before {
                content: '☀️';
                font-size: 20px;
            }
            
            [data-theme="dark"] .custom-theme-toggle__icon::before {
                content: '🌙';
            }
            
            @media (max-width: 768px) {
                #custom-theme-switcher {
                    bottom: 80px;
                    right: 15px;
                }
                
                .custom-theme-toggle {
                    width: 44px;
                    height: 44px;
                }
            }
        `;
        
        document.head.appendChild(switcherStyles);
        
        // Add click handler
        switcher.addEventListener('click', () => {
            const current = this.settings.colorScheme;
            let next;
            
            if (current === 'auto') {
                next = this.systemTheme === 'dark' ? 'light' : 'dark';
            } else if (current === 'light') {
                next = 'dark';
            } else {
                next = 'light';
            }
            
            this.setColorScheme(next);
        });
        
        document.body.appendChild(switcher);
    }
    
    // Utility methods
    getCurrentTheme() {
        return this.currentTheme;
    }
    
    getSettings() {
        return { ...this.settings };
    }
    
    isRTL() {
        return this.settings.rtl;
    }
    
    isDarkMode() {
        return this.getCurrentTheme() === 'dark';
    }
    
    isLightMode() {
        return this.getCurrentTheme() === 'light';
    }
    
    getSystemTheme() {
        return this.systemTheme;
    }
    
    // Color utilities
    generateColorShades(baseColor) {
        // Simple color shade generation (can be enhanced)
        const shades = {};
        const base = this.hexToHsl(baseColor);
        
        for (let i = 50; i <= 900; i += 50) {
            const lightness = Math.max(5, Math.min(95, base.l + (500 - i) * 0.1));
            shades[i] = this.hslToHex(base.h, base.s, lightness);
        }
        
        return shades;
    }
    
    hexToHsl(hex) {
        const r = parseInt(hex.slice(1, 3), 16) / 255;
        const g = parseInt(hex.slice(3, 5), 16) / 255;
        const b = parseInt(hex.slice(5, 7), 16) / 255;
        
        const max = Math.max(r, g, b);
        const min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;
        
        if (max === min) {
            h = s = 0;
        } else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }
        
        return { h: h * 360, s: s * 100, l: l * 100 };
    }
    
    hslToHex(h, s, l) {
        h /= 360;
        s /= 100;
        l /= 100;
        
        const hue2rgb = (p, q, t) => {
            if (t < 0) t += 1;
            if (t > 1) t -= 1;
            if (t < 1/6) return p + (q - p) * 6 * t;
            if (t < 1/2) return q;
            if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
            return p;
        };
        
        const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        const p = 2 * l - q;
        const r = hue2rgb(p, q, h + 1/3);
        const g = hue2rgb(p, q, h);
        const b = hue2rgb(p, q, h - 1/3);
        
        return `#${Math.round(r * 255).toString(16).padStart(2, '0')}${Math.round(g * 255).toString(16).padStart(2, '0')}${Math.round(b * 255).toString(16).padStart(2, '0')}`;
    }
    
    destroy() {
        const switcher = document.querySelector('#custom-theme-switcher');
        if (switcher) {
            switcher.remove();
        }
        
        const styles = document.querySelector('#custom-theme-styles');
        if (styles) {
            styles.remove();
        }
        
        // Reset document attributes
        document.documentElement.removeAttribute('data-theme');
        document.documentElement.removeAttribute('data-font-size');
        document.documentElement.removeAttribute('data-density');
        document.documentElement.removeAttribute('data-animations');
        document.documentElement.removeAttribute('dir');
        
        // Remove custom properties
        document.documentElement.style.removeProperty('--custom-primary');
        document.documentElement.style.removeProperty('--custom-accent');
    }
}

// Auto-initialize if theme is enabled
if (Custom.isEnabled('custom_theme')) {
    window.customTheme = new CustomTheme();
    
    // Global helper functions
    window.theme = {
        setColorScheme: (scheme) => window.customTheme.setColorScheme(scheme),
        setPrimaryColor: (color) => window.customTheme.setPrimaryColor(color),
        setAccentColor: (color) => window.customTheme.setAccentColor(color),
        setFontSize: (size) => window.customTheme.setFontSize(size),
        setDensity: (density) => window.customTheme.setDensity(density),
        toggleAnimations: () => window.customTheme.toggleAnimations(),
        toggleRTL: () => window.customTheme.toggleRTL(),
        reset: () => window.customTheme.reset(),
        getCurrentTheme: () => window.customTheme.getCurrentTheme(),
        getSettings: () => window.customTheme.getSettings(),
        isDarkMode: () => window.customTheme.isDarkMode(),
        isLightMode: () => window.customTheme.isLightMode()
    };
}

// Export for manual use
export default CustomTheme;