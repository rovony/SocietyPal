// Analytics Dashboard Component
// Handles the main dashboard functionality

class AnalyticsDashboard {
    constructor(container) {
        this.container =
            container || document.querySelector(".analytics-dashboard");
        this.metrics = {};
        this.charts = {};

        if (this.container) {
            this.init();
        }
    }

    init() {
        console.log("🎯 Initializing Analytics Dashboard component");

        this.setupDashboardLayout();
        this.initializeMetricCards();
        this.setupDashboardControls();
        this.bindDashboardEvents();
    }

    setupDashboardLayout() {
        // Ensure proper responsive layout
        this.container.classList.add("analytics-dashboard");

        // Add loading states to metric cards
        const metricCards = this.container.querySelectorAll(".metric-card");
        metricCards.forEach((card) => {
            if (!card.dataset.loaded) {
                card.classList.add("loading");
            }
        });
    }

    initializeMetricCards() {
        const metricCards = this.container.querySelectorAll(".metric-card");

        metricCards.forEach((card) => {
            const metricType = card.dataset.metric;

            // Create metric instance
            this.metrics[metricType] = {
                element: card,
                type: metricType,
                value: 0,
                change: 0,
                loading: false,
            };

            // Set up metric-specific features
            this.setupMetricCard(card, metricType);
        });
    }

    setupMetricCard(card, metricType) {
        // Add hover effects
        card.addEventListener("mouseenter", () => {
            this.highlightRelatedData(metricType);
        });

        card.addEventListener("mouseleave", () => {
            this.clearHighlights();
        });

        // Add click handler for drill-down
        card.addEventListener("click", () => {
            this.openMetricDetails(metricType);
        });

        // Mark as interactive
        card.style.cursor = "pointer";
        card.setAttribute("tabindex", "0");
        card.setAttribute("role", "button");
        card.setAttribute("aria-label", `View details for ${metricType}`);
    }

    setupDashboardControls() {
        // Time period selector
        const timeSelector = this.container.querySelector(
            ".time-selector select"
        );
        if (timeSelector) {
            timeSelector.addEventListener("change", (e) => {
                this.changePeriod(e.target.value);
            });
        }

        // Refresh button
        const refreshBtn = this.container.querySelector(".refresh-dashboard");
        if (refreshBtn) {
            refreshBtn.addEventListener("click", () => {
                this.refreshDashboard();
            });
        }

        // Export button
        const exportBtn = this.container.querySelector(".export-dashboard");
        if (exportBtn) {
            exportBtn.addEventListener("click", () => {
                this.exportDashboardData();
            });
        }
    }

    bindDashboardEvents() {
        // Keyboard navigation
        this.container.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") {
                const activeElement = document.activeElement;
                if (activeElement.classList.contains("metric-card")) {
                    e.preventDefault();
                    activeElement.click();
                }
            }
        });

        // Window resize handler
        window.addEventListener("resize", () => {
            this.handleResize();
        });
    }

    async updateMetric(metricType, data) {
        const metric = this.metrics[metricType];
        if (!metric) return;

        const { element } = metric;
        const valueElement = element.querySelector(".metric-value");
        const changeElement = element.querySelector(".metric-change");
        const iconElement = element.querySelector(".metric-icon");

        // Update value with animation
        if (valueElement && data.value !== undefined) {
            await this.animateMetricValue(
                valueElement,
                metric.value,
                data.value
            );
            metric.value = data.value;
        }

        // Update change indicator
        if (changeElement && data.change !== undefined) {
            changeElement.textContent = `${data.change > 0 ? "+" : ""}${
                data.change
            }%`;
            changeElement.className = `metric-change ${
                data.change >= 0 ? "positive" : "negative"
            }`;
            metric.change = data.change;
        }

        // Update icon state
        if (iconElement && data.status) {
            iconElement.setAttribute("data-status", data.status);
        }

        // Remove loading state
        element.classList.remove("loading");
        element.dataset.loaded = "true";

        // Trigger update animation
        element.classList.add("updated");
        setTimeout(() => {
            element.classList.remove("updated");
        }, 1000);
    }

    async animateMetricValue(element, fromValue, toValue) {
        return new Promise((resolve) => {
            const duration = 1000;
            const startTime = performance.now();
            const difference = toValue - fromValue;

            const animate = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                // Easing function (ease-out)
                const easeOut = 1 - Math.pow(1 - progress, 3);
                const currentValue = Math.round(
                    fromValue + difference * easeOut
                );

                element.textContent = this.formatMetricValue(currentValue);

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    resolve();
                }
            };

            requestAnimationFrame(animate);
        });
    }

    formatMetricValue(value) {
        // Format numbers with appropriate suffixes
        if (value >= 1000000) {
            return (value / 1000000).toFixed(1) + "M";
        } else if (value >= 1000) {
            return (value / 1000).toFixed(1) + "K";
        } else {
            return value.toLocaleString();
        }
    }

    highlightRelatedData(metricType) {
        // Highlight related charts and data points
        const relatedElements = this.container.querySelectorAll(
            `[data-related="${metricType}"]`
        );
        relatedElements.forEach((element) => {
            element.classList.add("highlighted");
        });

        // Dim unrelated elements
        const allMetrics = this.container.querySelectorAll(".metric-card");
        allMetrics.forEach((card) => {
            if (card.dataset.metric !== metricType) {
                card.classList.add("dimmed");
            }
        });
    }

    clearHighlights() {
        // Remove all highlights and dimming
        const highlightedElements =
            this.container.querySelectorAll(".highlighted");
        highlightedElements.forEach((element) => {
            element.classList.remove("highlighted");
        });

        const dimmedElements = this.container.querySelectorAll(".dimmed");
        dimmedElements.forEach((element) => {
            element.classList.remove("dimmed");
        });
    }

    async changePeriod(period) {
        console.log(`📅 Dashboard: Changing period to ${period}`);

        // Show loading state
        this.setLoadingState(true);

        try {
            // Fetch new data for the period
            const response = await fetch(
                `/api/analytics/dashboard?period=${period}`
            );
            const data = await response.json();

            // Update all metrics
            for (const [metricType, metricData] of Object.entries(
                data.metrics
            )) {
                await this.updateMetric(metricType, metricData);
            }

            // Trigger custom event for other components
            this.container.dispatchEvent(
                new CustomEvent("periodChanged", {
                    detail: { period, data },
                })
            );
        } catch (error) {
            console.error("❌ Failed to change dashboard period:", error);
            this.showError("Failed to load dashboard data");
        } finally {
            this.setLoadingState(false);
        }
    }

    async refreshDashboard() {
        console.log("🔄 Refreshing dashboard data");

        const refreshBtn = this.container.querySelector(".refresh-dashboard");
        if (refreshBtn) {
            refreshBtn.classList.add("spinning");
        }

        try {
            const response = await fetch("/api/analytics/dashboard/refresh", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            const data = await response.json();

            // Update all metrics
            for (const [metricType, metricData] of Object.entries(
                data.metrics
            )) {
                await this.updateMetric(metricType, metricData);
            }

            // Show success feedback
            this.showSuccessMessage("Dashboard refreshed successfully");
        } catch (error) {
            console.error("❌ Failed to refresh dashboard:", error);
            this.showError("Failed to refresh dashboard");
        } finally {
            if (refreshBtn) {
                refreshBtn.classList.remove("spinning");
            }
        }
    }

    async exportDashboardData() {
        console.log("📊 Exporting dashboard data");

        try {
            const response = await fetch("/api/analytics/export", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    type: "dashboard",
                    period: this.getCurrentPeriod(),
                    format: "csv",
                }),
            });

            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = url;
                a.download = `task-analytics-${
                    new Date().toISOString().split("T")[0]
                }.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);

                this.showSuccessMessage("Dashboard data exported successfully");
            }
        } catch (error) {
            console.error("❌ Failed to export dashboard:", error);
            this.showError("Failed to export dashboard data");
        }
    }

    openMetricDetails(metricType) {
        console.log(`🔍 Opening details for metric: ${metricType}`);

        // In a real application, this would open a detailed view
        // For now, we'll just dispatch an event
        this.container.dispatchEvent(
            new CustomEvent("metricDetailRequested", {
                detail: { metricType, metric: this.metrics[metricType] },
            })
        );
    }

    setLoadingState(loading) {
        if (loading) {
            this.container.classList.add("loading");
            Object.values(this.metrics).forEach((metric) => {
                metric.element.classList.add("loading");
            });
        } else {
            this.container.classList.remove("loading");
            Object.values(this.metrics).forEach((metric) => {
                metric.element.classList.remove("loading");
            });
        }
    }

    getCurrentPeriod() {
        const timeSelector = this.container.querySelector(
            ".time-selector select"
        );
        return timeSelector ? timeSelector.value : "last-7-days";
    }

    showError(message) {
        // Create error notification
        console.error(message);
        // In a real app, show user-friendly error notification
    }

    showSuccessMessage(message) {
        // Create success notification
        console.log(`✅ ${message}`);
        // In a real app, show user-friendly success notification
    }

    handleResize() {
        // Handle responsive layout changes
        const isMobile = window.innerWidth < 768;
        this.container.classList.toggle("mobile-layout", isMobile);
    }

    destroy() {
        // Clean up event listeners and resources
        console.log("🧹 Cleaning up Analytics Dashboard component");

        // Remove event listeners
        const timeSelector = this.container.querySelector(
            ".time-selector select"
        );
        if (timeSelector) {
            timeSelector.removeEventListener("change", this.changePeriod);
        }

        window.removeEventListener("resize", this.handleResize);

        // Clear metrics
        this.metrics = {};
        this.charts = {};
    }
}

// Auto-initialize when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    const dashboardElement = document.querySelector(".analytics-dashboard");
    if (dashboardElement) {
        window.analyticsDashboard = new AnalyticsDashboard(dashboardElement);
    }
});

export default AnalyticsDashboard;
