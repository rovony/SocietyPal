// Priority Task Analytics Dashboard JavaScript
// Safe from vendor updates - located in resources/Custom/js/

// Import analytics components
import "./components/AnalyticsDashboard";
import "./components/TaskMetrics";
import "./components/PriorityCharts";

// External dependencies (loaded via CDN or npm)
// Chart.js for data visualization
// Moment.js for date handling

class TaskAnalyticsApp {
    constructor() {
        this.version =
            document.querySelector('meta[name="analytics-version"]')?.content ||
            "1.0.0";
        this.debug =
            document.querySelector('meta[name="app-debug"]')?.content ===
            "true";
        this.apiEndpoint =
            document.querySelector('meta[name="analytics-api"]')?.content ||
            "/api/analytics";

        // Analytics configuration
        this.config = {
            updateInterval: 30000, // 30 seconds
            chartAnimationDuration: 1000,
            realTimeUpdates: true,
            cacheTimeout: 300000, // 5 minutes
        };

        this.init();
    }

    init() {
        console.log(`🎯 Priority Task Analytics v${this.version} initialized`);

        // Wait for DOM to be ready
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", () =>
                this.initializeAnalytics()
            );
        } else {
            this.initializeAnalytics();
        }
    }

    initializeAnalytics() {
        // Only initialize if analytics dashboard is present
        const dashboardElement = document.querySelector(".analytics-dashboard");
        if (!dashboardElement) {
            console.log("📊 Analytics dashboard not found on this page");
            return;
        }

        // Initialize dashboard components
        this.initializeDashboard();
        this.initializeMetrics();
        this.initializeCharts();
        this.initializeRealTimeUpdates();
        this.bindEvents();

        if (this.debug) {
            this.enableDebugMode();
        }

        console.log("✅ Analytics dashboard fully initialized");
    }

    async initializeDashboard() {
        try {
            // Load initial dashboard data
            const response = await this.fetchAnalyticsData("dashboard");
            this.updateDashboardMetrics(response.metrics);

            // Set up dashboard controls
            this.setupTimeSelector();
            this.setupRefreshButton();
        } catch (error) {
            console.error("❌ Failed to initialize dashboard:", error);
            this.showErrorMessage("Failed to load dashboard data");
        }
    }

    async initializeMetrics() {
        const metricCards = document.querySelectorAll(".metric-card");

        for (const card of metricCards) {
            const metricType = card.dataset.metric;

            try {
                const data = await this.fetchAnalyticsData(
                    `metrics/${metricType}`
                );
                this.updateMetricCard(card, data);
            } catch (error) {
                console.error(`❌ Failed to load metric ${metricType}:`, error);
                this.showMetricError(card);
            }
        }
    }

    async initializeCharts() {
        // Task completion trend chart
        const completionChart = document.getElementById(
            "completion-trend-chart"
        );
        if (completionChart) {
            this.initializeCompletionChart(completionChart);
        }

        // Priority distribution chart
        const priorityChart = document.getElementById(
            "priority-distribution-chart"
        );
        if (priorityChart) {
            this.initializePriorityChart(priorityChart);
        }

        // Productivity timeline chart
        const productivityChart = document.getElementById(
            "productivity-timeline-chart"
        );
        if (productivityChart) {
            this.initializeProductivityChart(productivityChart);
        }
    }

    async initializeCompletionChart(chartElement) {
        try {
            const data = await this.fetchAnalyticsData(
                "charts/completion-trend"
            );

            const chart = new Chart(chartElement, {
                type: "line",
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: "Tasks Completed",
                            data: data.completions,
                            borderColor: "#2563eb",
                            backgroundColor: "rgba(37, 99, 235, 0.1)",
                            fill: true,
                            tension: 0.4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: this.config.chartAnimationDuration,
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: "rgba(0, 0, 0, 0.1)",
                            },
                        },
                        x: {
                            grid: {
                                display: false,
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: "rgba(0, 0, 0, 0.8)",
                            titleColor: "white",
                            bodyColor: "white",
                            borderColor: "#2563eb",
                            borderWidth: 1,
                        },
                    },
                },
            });

            // Store chart instance for updates
            this.charts = this.charts || {};
            this.charts.completion = chart;
        } catch (error) {
            console.error("❌ Failed to initialize completion chart:", error);
            this.showChartError(chartElement);
        }
    }

    async initializePriorityChart(chartElement) {
        try {
            const data = await this.fetchAnalyticsData(
                "charts/priority-distribution"
            );

            const chart = new Chart(chartElement, {
                type: "doughnut",
                data: {
                    labels: [
                        "High Priority",
                        "Medium Priority",
                        "Low Priority",
                    ],
                    datasets: [
                        {
                            data: [data.high, data.medium, data.low],
                            backgroundColor: ["#dc2626", "#d97706", "#059669"],
                            borderWidth: 0,
                            hoverOffset: 4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: this.config.chartAnimationDuration,
                    },
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                            },
                        },
                        tooltip: {
                            backgroundColor: "rgba(0, 0, 0, 0.8)",
                            titleColor: "white",
                            bodyColor: "white",
                        },
                    },
                },
            });

            this.charts = this.charts || {};
            this.charts.priority = chart;
        } catch (error) {
            console.error("❌ Failed to initialize priority chart:", error);
            this.showChartError(chartElement);
        }
    }

    initializeRealTimeUpdates() {
        if (!this.config.realTimeUpdates) return;

        // Set up periodic updates
        this.updateInterval = setInterval(() => {
            this.refreshAnalyticsData();
        }, this.config.updateInterval);

        console.log(
            `🔄 Real-time updates enabled (${this.config.updateInterval}ms interval)`
        );
    }

    async refreshAnalyticsData() {
        try {
            // Show updating indicators
            document.querySelectorAll(".metric-card").forEach((card) => {
                card.classList.add("updating");
            });

            // Fetch fresh data
            const dashboardData = await this.fetchAnalyticsData("dashboard");
            this.updateDashboardMetrics(dashboardData.metrics);

            // Update charts if they exist
            if (this.charts) {
                await this.updateCharts();
            }

            // Remove updating indicators
            setTimeout(() => {
                document.querySelectorAll(".metric-card").forEach((card) => {
                    card.classList.remove("updating");
                });
            }, 500);
        } catch (error) {
            console.error("❌ Failed to refresh analytics data:", error);
        }
    }

    async fetchAnalyticsData(endpoint, options = {}) {
        const url = `${this.apiEndpoint}/${endpoint}`;
        const defaultOptions = {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            credentials: "same-origin",
        };

        const response = await fetch(url, { ...defaultOptions, ...options });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return await response.json();
    }

    updateDashboardMetrics(metrics) {
        Object.entries(metrics).forEach(([key, value]) => {
            const metricElement = document.querySelector(
                `[data-metric="${key}"]`
            );
            if (metricElement) {
                this.updateMetricCard(metricElement, value);
            }
        });
    }

    updateMetricCard(card, data) {
        const valueElement = card.querySelector(".metric-value");
        const changeElement = card.querySelector(".metric-change");

        if (valueElement) {
            // Animate value change
            this.animateValue(
                valueElement,
                parseInt(valueElement.textContent) || 0,
                data.value
            );
        }

        if (changeElement && data.change !== undefined) {
            changeElement.textContent = `${data.change > 0 ? "+" : ""}${
                data.change
            }%`;
            changeElement.className = `metric-change ${
                data.change >= 0 ? "positive" : "negative"
            }`;
        }
    }

    animateValue(element, start, end) {
        const duration = 1000; // 1 second
        const startTime = performance.now();

        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            const current = Math.floor(start + (end - start) * progress);
            element.textContent = current.toLocaleString();

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    bindEvents() {
        // Time selector change
        const timeSelector = document.querySelector(".time-selector select");
        if (timeSelector) {
            timeSelector.addEventListener("change", (e) => {
                this.changePeriod(e.target.value);
            });
        }

        // Manual refresh button
        const refreshButton = document.querySelector(".refresh-button");
        if (refreshButton) {
            refreshButton.addEventListener("click", () => {
                this.refreshAnalyticsData();
            });
        }

        // Chart control buttons
        document
            .querySelectorAll(".chart-controls button")
            .forEach((button) => {
                button.addEventListener("click", (e) => {
                    this.handleChartControl(e);
                });
            });
    }

    setupTimeSelector() {
        const selector = document.querySelector(".time-selector select");
        if (selector) {
            // Set default value
            selector.value = "last-7-days";
        }
    }

    async changePeriod(period) {
        try {
            console.log(`📅 Changing period to: ${period}`);

            // Update all charts and metrics for new period
            const data = await this.fetchAnalyticsData(
                `dashboard?period=${period}`
            );
            this.updateDashboardMetrics(data.metrics);

            if (this.charts) {
                await this.updateChartsForPeriod(period);
            }
        } catch (error) {
            console.error("❌ Failed to change period:", error);
        }
    }

    showErrorMessage(message) {
        // Create or update error notification
        console.error(message);
        // In a real app, show user-friendly error notification
    }

    enableDebugMode() {
        console.log("🐛 Analytics debug mode enabled");

        // Add debug indicators
        const indicator = document.createElement("div");
        indicator.className = "analytics-debug-indicator";
        indicator.textContent = "🎯 Analytics Debug Mode";
        indicator.style.cssText = `
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(37, 99, 235, 0.9);
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 9999;
            font-family: monospace;
        `;
        document.body.appendChild(indicator);

        // Log all analytics events
        this.originalFetch = this.fetchAnalyticsData;
        this.fetchAnalyticsData = async (endpoint, options) => {
            console.log(`📊 Fetching analytics: ${endpoint}`, options);
            const result = await this.originalFetch(endpoint, options);
            console.log(`📈 Analytics response:`, result);
            return result;
        };
    }

    destroy() {
        // Clean up intervals and event listeners
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
        }

        // Destroy charts
        if (this.charts) {
            Object.values(this.charts).forEach((chart) => chart.destroy());
        }

        console.log("🧹 Analytics app cleaned up");
    }
}

// Initialize analytics app when DOM is ready
if (typeof window !== "undefined") {
    window.TaskAnalyticsApp = TaskAnalyticsApp;

    // Auto-initialize
    const analyticsApp = new TaskAnalyticsApp();

    // Make available globally for debugging
    if (window.TaskAnalyticsApp.debug) {
        window.analyticsApp = analyticsApp;
    }
}

export default TaskAnalyticsApp;
