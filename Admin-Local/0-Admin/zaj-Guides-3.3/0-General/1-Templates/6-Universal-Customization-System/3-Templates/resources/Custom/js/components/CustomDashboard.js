// Custom Dashboard Component
// This file is safe from vendor updates

class CustomDashboard {
    constructor() {
        this.initialized = false;
        this.widgets = new Map();
        
        this.init();
    }
    
    init() {
        if (this.initialized) return;
        
        console.log('🎯 CustomDashboard: Initializing...');
        
        this.bindEvents();
        this.loadWidgets();
        
        this.initialized = true;
        console.log('✅ CustomDashboard: Ready');
    }
    
    bindEvents() {
        // Dashboard-specific event bindings
        document.addEventListener('DOMContentLoaded', () => {
            this.setupDashboardLayout();
        });
        
        // Widget interaction events
        document.addEventListener('click', (e) => {
            if (e.target.closest('.custom-dashboard-widget')) {
                this.handleWidgetClick(e);
            }
        });
        
        // Responsive layout adjustments
        window.addEventListener('resize', () => {
            this.adjustLayout();
        });
    }
    
    setupDashboardLayout() {
        const dashboardContainer = document.querySelector('.custom-dashboard');
        if (!dashboardContainer) return;
        
        // Add custom dashboard classes
        dashboardContainer.classList.add('custom-layer');
        
        // Initialize grid layout
        this.initializeGrid();
        
        Custom.log('Dashboard layout initialized');
    }
    
    initializeGrid() {
        const grid = document.querySelector('.custom-dashboard-grid');
        if (!grid) return;
        
        // Add responsive grid classes
        grid.classList.add('custom-grid');
        
        // Auto-arrange widgets
        this.arrangeWidgets();
    }
    
    loadWidgets() {
        const enabledWidgets = Custom.config('dashboard.widgets', []);
        
        enabledWidgets.forEach(widget => {
            this.loadWidget(widget);
        });
    }
    
    loadWidget(widgetConfig) {
        const widget = {
            id: widgetConfig.id,
            type: widgetConfig.type,
            title: widgetConfig.title,
            config: widgetConfig.config || {},
            element: null
        };
        
        this.widgets.set(widget.id, widget);
        this.renderWidget(widget);
        
        Custom.log(`Widget loaded: ${widget.title}`);
    }
    
    renderWidget(widget) {
        const container = document.querySelector('.custom-dashboard-grid');
        if (!container) return;
        
        const widgetElement = document.createElement('div');
        widgetElement.className = 'custom-dashboard-widget custom-layer';
        widgetElement.dataset.widgetId = widget.id;
        widgetElement.dataset.widgetType = widget.type;
        
        widgetElement.innerHTML = `
            <div class="custom-widget-header">
                <h3 class="custom-widget-title">${widget.title}</h3>
                <div class="custom-widget-actions">
                    <button class="custom-widget-refresh" title="Refresh">
                        <span class="icon">🔄</span>
                    </button>
                    <button class="custom-widget-settings" title="Settings">
                        <span class="icon">⚙️</span>
                    </button>
                </div>
            </div>
            <div class="custom-widget-content">
                <div class="custom-widget-loading">
                    <span class="loading-spinner">⏳</span>
                    <span class="loading-text">Loading...</span>
                </div>
            </div>
        `;
        
        container.appendChild(widgetElement);
        widget.element = widgetElement;
        
        // Load widget-specific content
        this.loadWidgetContent(widget);
    }
    
    loadWidgetContent(widget) {
        const contentContainer = widget.element.querySelector('.custom-widget-content');
        
        // Simulate loading delay
        setTimeout(() => {
            contentContainer.innerHTML = this.getWidgetContent(widget);
            
            // Trigger custom event
            widget.element.dispatchEvent(new CustomEvent('widget-loaded', {
                detail: { widget }
            }));
            
        }, 1000);
    }
    
    getWidgetContent(widget) {
        switch (widget.type) {
            case 'stats':
                return this.getStatsWidgetContent(widget);
            case 'chart':
                return this.getChartWidgetContent(widget);
            case 'activity':
                return this.getActivityWidgetContent(widget);
            case 'quick-actions':
                return this.getQuickActionsContent(widget);
            default:
                return `<p>Widget type "${widget.type}" not implemented yet.</p>`;
        }
    }
    
    getStatsWidgetContent(widget) {
        return `
            <div class="custom-stats-grid">
                <div class="custom-stat-item">
                    <div class="stat-value">1,234</div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="custom-stat-item">
                    <div class="stat-value">56</div>
                    <div class="stat-label">Active Today</div>
                </div>
                <div class="custom-stat-item">
                    <div class="stat-value">$12,345</div>
                    <div class="stat-label">Revenue</div>
                </div>
            </div>
        `;
    }
    
    getChartWidgetContent(widget) {
        return `
            <div class="custom-chart-container">
                <canvas id="chart-${widget.id}" width="300" height="200"></canvas>
                <p class="chart-placeholder">📊 Chart will render here</p>
            </div>
        `;
    }
    
    getActivityWidgetContent(widget) {
        return `
            <div class="custom-activity-feed">
                <div class="activity-item">
                    <span class="activity-icon">👤</span>
                    <span class="activity-text">New user registered</span>
                    <span class="activity-time">2 min ago</span>
                </div>
                <div class="activity-item">
                    <span class="activity-icon">💰</span>
                    <span class="activity-text">Payment received</span>
                    <span class="activity-time">5 min ago</span>
                </div>
                <div class="activity-item">
                    <span class="activity-icon">📊</span>
                    <span class="activity-text">Report generated</span>
                    <span class="activity-time">10 min ago</span>
                </div>
            </div>
        `;
    }
    
    getQuickActionsContent(widget) {
        return `
            <div class="custom-quick-actions">
                <button class="custom-action-btn primary">
                    <span class="btn-icon">➕</span>
                    <span class="btn-text">Add User</span>
                </button>
                <button class="custom-action-btn secondary">
                    <span class="btn-icon">📊</span>
                    <span class="btn-text">Generate Report</span>
                </button>
                <button class="custom-action-btn">
                    <span class="btn-icon">⚙️</span>
                    <span class="btn-text">Settings</span>
                </button>
            </div>
        `;
    }
    
    handleWidgetClick(e) {
        const widget = e.target.closest('.custom-dashboard-widget');
        const widgetId = widget.dataset.widgetId;
        const action = e.target.closest('[class*="widget-"]');
        
        if (!action) return;
        
        if (action.classList.contains('custom-widget-refresh')) {
            this.refreshWidget(widgetId);
        } else if (action.classList.contains('custom-widget-settings')) {
            this.showWidgetSettings(widgetId);
        }
    }
    
    refreshWidget(widgetId) {
        const widget = this.widgets.get(widgetId);
        if (!widget) return;
        
        const contentContainer = widget.element.querySelector('.custom-widget-content');
        contentContainer.innerHTML = `
            <div class="custom-widget-loading">
                <span class="loading-spinner">🔄</span>
                <span class="loading-text">Refreshing...</span>
            </div>
        `;
        
        // Reload content after delay
        setTimeout(() => {
            this.loadWidgetContent(widget);
        }, 500);
        
        Custom.log(`Widget refreshed: ${widget.id}`);
    }
    
    showWidgetSettings(widgetId) {
        const widget = this.widgets.get(widgetId);
        if (!widget) return;
        
        // Show settings modal/panel
        Custom.log(`Widget settings: ${widget.id}`);
        
        // For now, just alert
        alert(`Settings for: ${widget.title}\n(Implementation pending)`);
    }
    
    arrangeWidgets() {
        // Auto-arrange widgets in optimal layout
        const widgets = document.querySelectorAll('.custom-dashboard-widget');
        
        widgets.forEach((widget, index) => {
            widget.style.animationDelay = `${index * 0.1}s`;
            widget.classList.add('fade-in');
        });
    }
    
    adjustLayout() {
        // Responsive layout adjustments
        const container = document.querySelector('.custom-dashboard-grid');
        if (!container) return;
        
        const screenWidth = window.innerWidth;
        
        if (screenWidth < 768) {
            container.classList.add('mobile-layout');
        } else if (screenWidth < 1024) {
            container.classList.add('tablet-layout');
        } else {
            container.classList.add('desktop-layout');
        }
        
        Custom.log('Dashboard layout adjusted for screen size:', screenWidth);
    }
    
    // Public API methods
    addWidget(widgetConfig) {
        this.loadWidget(widgetConfig);
    }
    
    removeWidget(widgetId) {
        const widget = this.widgets.get(widgetId);
        if (widget && widget.element) {
            widget.element.remove();
            this.widgets.delete(widgetId);
        }
    }
    
    getWidget(widgetId) {
        return this.widgets.get(widgetId);
    }
    
    getAllWidgets() {
        return Array.from(this.widgets.values());
    }
}

// Auto-initialize if dashboard is enabled
if (Custom.isEnabled('custom_dashboard')) {
    window.customDashboard = new CustomDashboard();
}

// Export for manual use
export default CustomDashboard;