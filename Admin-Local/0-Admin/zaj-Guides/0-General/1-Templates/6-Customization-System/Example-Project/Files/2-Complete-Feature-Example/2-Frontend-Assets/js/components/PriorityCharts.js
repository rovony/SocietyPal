// Priority Charts Component
// Handles Chart.js visualizations for task analytics

class PriorityCharts {
    constructor() {
        this.charts = {};
        this.chartOptions = {};
        this.isChartJsLoaded = false;

        this.init();
    }

    async init() {
        console.log("📈 Initializing Priority Charts component");

        // Check if Chart.js is available
        await this.ensureChartJsLoaded();

        if (this.isChartJsLoaded) {
            this.setupDefaultOptions();
            this.initializeCharts();
            this.bindEvents();
        } else {
            console.error(
                "❌ Chart.js is required for Priority Charts component"
            );
        }
    }

    async ensureChartJsLoaded() {
        if (typeof Chart !== "undefined") {
            this.isChartJsLoaded = true;
            return;
        }

        // Try to load Chart.js if not available
        try {
            await this.loadChartJs();
            this.isChartJsLoaded = true;
        } catch (error) {
            console.error("❌ Failed to load Chart.js:", error);
            this.showChartError(
                "Chart.js library is required for analytics visualizations"
            );
        }
    }

    async loadChartJs() {
        return new Promise((resolve, reject) => {
            if (typeof Chart !== "undefined") {
                resolve();
                return;
            }

            const script = document.createElement("script");
            script.src =
                "https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js";
            script.onload = () => resolve();
            script.onerror = () => reject(new Error("Failed to load Chart.js"));
            document.head.appendChild(script);
        });
    }

    setupDefaultOptions() {
        // Default chart configuration
        this.chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: "easeInOutQuart",
            },
            interaction: {
                intersect: false,
                mode: "index",
            },
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12,
                        },
                    },
                },
                tooltip: {
                    backgroundColor: "rgba(0, 0, 0, 0.8)",
                    titleColor: "white",
                    bodyColor: "white",
                    borderColor: "#2563eb",
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                },
            },
        };
    }

    initializeCharts() {
        // Find and initialize all chart containers
        this.initializeCompletionTrendChart();
        this.initializePriorityDistributionChart();
        this.initializeProductivityTimelineChart();
        this.initializeHourlyActivityChart();
    }

    async initializeCompletionTrendChart() {
        const canvas = document.getElementById("completion-trend-chart");
        if (!canvas) return;

        console.log("📊 Initializing completion trend chart");

        try {
            const data = await this.fetchChartData("completion-trend");

            const chart = new Chart(canvas, {
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
                            pointBackgroundColor: "#2563eb",
                            pointBorderColor: "#ffffff",
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        },
                        {
                            label: "Created Tasks",
                            data: data.created,
                            borderColor: "#7c3aed",
                            backgroundColor: "rgba(124, 58, 237, 0.1)",
                            fill: false,
                            tension: 0.4,
                            borderDash: [5, 5],
                            pointBackgroundColor: "#7c3aed",
                            pointBorderColor: "#ffffff",
                            pointBorderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                        },
                    ],
                },
                options: {
                    ...this.chartOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: "rgba(0, 0, 0, 0.1)",
                            },
                            ticks: {
                                stepSize: 1,
                            },
                        },
                        x: {
                            grid: {
                                display: false,
                            },
                        },
                    },
                    plugins: {
                        ...this.chartOptions.plugins,
                        title: {
                            display: true,
                            text: "Task Completion Trend",
                            font: {
                                size: 16,
                                weight: "bold",
                            },
                        },
                    },
                },
            });

            this.charts.completionTrend = chart;
            this.setupChartControls(canvas, "completion-trend");
        } catch (error) {
            console.error(
                "❌ Failed to initialize completion trend chart:",
                error
            );
            this.showChartError(canvas, "Failed to load completion trend data");
        }
    }

    async initializePriorityDistributionChart() {
        const canvas = document.getElementById("priority-distribution-chart");
        if (!canvas) return;

        console.log("🎯 Initializing priority distribution chart");

        try {
            const data = await this.fetchChartData("priority-distribution");

            const chart = new Chart(canvas, {
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
                            backgroundColor: [
                                "#dc2626", // Red for high
                                "#d97706", // Orange for medium
                                "#059669", // Green for low
                            ],
                            borderWidth: 0,
                            hoverOffset: 8,
                            cutout: "60%",
                        },
                    ],
                },
                options: {
                    ...this.chartOptions,
                    plugins: {
                        ...this.chartOptions.plugins,
                        title: {
                            display: true,
                            text: "Priority Distribution",
                            font: {
                                size: 16,
                                weight: "bold",
                            },
                        },
                        legend: {
                            position: "bottom",
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                generateLabels: (chart) => {
                                    const data = chart.data;
                                    const total = data.datasets[0].data.reduce(
                                        (a, b) => a + b,
                                        0
                                    );

                                    return data.labels.map((label, index) => {
                                        const value =
                                            data.datasets[0].data[index];
                                        const percentage =
                                            total > 0
                                                ? Math.round(
                                                      (value / total) * 100
                                                  )
                                                : 0;

                                        return {
                                            text: `${label}: ${value} (${percentage}%)`,
                                            fillStyle:
                                                data.datasets[0]
                                                    .backgroundColor[index],
                                            pointStyle: "circle",
                                            hidden: false,
                                            index: index,
                                        };
                                    });
                                },
                            },
                        },
                    },
                },
            });

            this.charts.priorityDistribution = chart;
            this.setupChartControls(canvas, "priority-distribution");
        } catch (error) {
            console.error(
                "❌ Failed to initialize priority distribution chart:",
                error
            );
            this.showChartError(
                canvas,
                "Failed to load priority distribution data"
            );
        }
    }

    async initializeProductivityTimelineChart() {
        const canvas = document.getElementById("productivity-timeline-chart");
        if (!canvas) return;

        console.log("⏱️ Initializing productivity timeline chart");

        try {
            const data = await this.fetchChartData("productivity-timeline");

            const chart = new Chart(canvas, {
                type: "bar",
                data: {
                    labels: data.hours, // 24 hour labels
                    datasets: [
                        {
                            label: "Tasks Completed",
                            data: data.completions,
                            backgroundColor: "rgba(37, 99, 235, 0.7)",
                            borderColor: "#2563eb",
                            borderWidth: 1,
                            borderRadius: 4,
                            borderSkipped: false,
                        },
                    ],
                },
                options: {
                    ...this.chartOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: "rgba(0, 0, 0, 0.1)",
                            },
                            title: {
                                display: true,
                                text: "Tasks Completed",
                            },
                        },
                        x: {
                            grid: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: "Hour of Day",
                            },
                        },
                    },
                    plugins: {
                        ...this.chartOptions.plugins,
                        title: {
                            display: true,
                            text: "Productivity by Hour",
                            font: {
                                size: 16,
                                weight: "bold",
                            },
                        },
                        legend: {
                            display: false,
                        },
                    },
                },
            });

            this.charts.productivityTimeline = chart;
            this.setupChartControls(canvas, "productivity-timeline");
        } catch (error) {
            console.error(
                "❌ Failed to initialize productivity timeline chart:",
                error
            );
            this.showChartError(
                canvas,
                "Failed to load productivity timeline data"
            );
        }
    }

    async initializeHourlyActivityChart() {
        const canvas = document.getElementById("hourly-activity-chart");
        if (!canvas) return;

        console.log("📅 Initializing hourly activity chart");

        try {
            const data = await this.fetchChartData("hourly-activity");

            const chart = new Chart(canvas, {
                type: "radar",
                data: {
                    labels: data.days, // Days of week
                    datasets: [
                        {
                            label: "This Week",
                            data: data.thisWeek,
                            borderColor: "#2563eb",
                            backgroundColor: "rgba(37, 99, 235, 0.2)",
                            pointBackgroundColor: "#2563eb",
                            pointBorderColor: "#ffffff",
                            pointBorderWidth: 2,
                            tension: 0.4,
                        },
                        {
                            label: "Last Week",
                            data: data.lastWeek,
                            borderColor: "#94a3b8",
                            backgroundColor: "rgba(148, 163, 184, 0.1)",
                            pointBackgroundColor: "#94a3b8",
                            pointBorderColor: "#ffffff",
                            pointBorderWidth: 2,
                            borderDash: [5, 5],
                            tension: 0.4,
                        },
                    ],
                },
                options: {
                    ...this.chartOptions,
                    scales: {
                        r: {
                            beginAtZero: true,
                            grid: {
                                color: "rgba(0, 0, 0, 0.1)",
                            },
                            pointLabels: {
                                font: {
                                    size: 12,
                                },
                            },
                            ticks: {
                                stepSize: 1,
                                showLabelBackdrop: false,
                            },
                        },
                    },
                    plugins: {
                        ...this.chartOptions.plugins,
                        title: {
                            display: true,
                            text: "Weekly Activity Pattern",
                            font: {
                                size: 16,
                                weight: "bold",
                            },
                        },
                    },
                },
            });

            this.charts.hourlyActivity = chart;
            this.setupChartControls(canvas, "hourly-activity");
        } catch (error) {
            console.error(
                "❌ Failed to initialize hourly activity chart:",
                error
            );
            this.showChartError(canvas, "Failed to load hourly activity data");
        }
    }

    setupChartControls(canvas, chartType) {
        const container = canvas.closest(".chart-container");
        if (!container) return;

        // Find control buttons
        const controls = container.querySelectorAll(".chart-controls button");

        controls.forEach((button) => {
            button.addEventListener("click", (e) => {
                e.preventDefault();

                // Update active state
                controls.forEach((btn) => btn.classList.remove("active"));
                button.classList.add("active");

                // Handle control action
                const action = button.dataset.action;
                this.handleChartControl(
                    chartType,
                    action,
                    button.dataset.value
                );
            });
        });
    }

    async handleChartControl(chartType, action, value) {
        console.log(`🎛️ Chart control: ${chartType} - ${action} - ${value}`);

        switch (action) {
            case "period":
                await this.updateChartPeriod(chartType, value);
                break;

            case "type":
                await this.changeChartType(chartType, value);
                break;

            case "export":
                this.exportChart(chartType);
                break;

            case "refresh":
                await this.refreshChart(chartType);
                break;
        }
    }

    async updateChartPeriod(chartType, period) {
        const chart = this.charts[chartType];
        if (!chart) return;

        try {
            // Show loading state
            this.setChartLoading(chart, true);

            // Fetch new data
            const data = await this.fetchChartData(chartType, { period });

            // Update chart data
            chart.data = this.transformDataForChart(chartType, data);
            chart.update("active");
        } catch (error) {
            console.error(
                `❌ Failed to update chart period for ${chartType}:`,
                error
            );
            this.showChartError(chart.canvas, "Failed to update chart data");
        } finally {
            this.setChartLoading(chart, false);
        }
    }

    async changeChartType(chartType, newType) {
        const canvas = this.charts[chartType]?.canvas;
        if (!canvas) return;

        // Destroy existing chart
        this.charts[chartType].destroy();

        // Recreate with new type
        await this.createChart(canvas, chartType, newType);
    }

    exportChart(chartType) {
        const chart = this.charts[chartType];
        if (!chart) return;

        // Create download link
        const url = chart.toBase64Image();
        const a = document.createElement("a");
        a.href = url;
        a.download = `${chartType}-chart-${
            new Date().toISOString().split("T")[0]
        }.png`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        console.log(`📷 Exported chart: ${chartType}`);
    }

    async refreshChart(chartType) {
        const chart = this.charts[chartType];
        if (!chart) return;

        try {
            this.setChartLoading(chart, true);

            // Fetch fresh data
            const data = await this.fetchChartData(chartType, {
                refresh: true,
            });

            // Update chart
            chart.data = this.transformDataForChart(chartType, data);
            chart.update("active");

            console.log(`🔄 Refreshed chart: ${chartType}`);
        } catch (error) {
            console.error(`❌ Failed to refresh chart ${chartType}:`, error);
        } finally {
            this.setChartLoading(chart, false);
        }
    }

    async fetchChartData(chartType, params = {}) {
        const queryParams = new URLSearchParams(params);
        const url = `/api/analytics/charts/${chartType}?${queryParams}`;

        const response = await fetch(url, {
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return await response.json();
    }

    transformDataForChart(chartType, data) {
        // Transform API data into Chart.js format
        switch (chartType) {
            case "completion-trend":
                return {
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
                };

            case "priority-distribution":
                return {
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
                            hoverOffset: 8,
                        },
                    ],
                };

            default:
                return data;
        }
    }

    setChartLoading(chart, loading) {
        const container = chart.canvas.closest(".chart-container");
        if (!container) return;

        if (loading) {
            container.classList.add("loading");
            chart.canvas.style.opacity = "0.5";
        } else {
            container.classList.remove("loading");
            chart.canvas.style.opacity = "1";
        }
    }

    showChartError(canvasOrChart, message) {
        const canvas = canvasOrChart.canvas || canvasOrChart;
        const container = canvas.closest(".chart-container");

        if (container) {
            const errorDiv = document.createElement("div");
            errorDiv.className = "chart-error";
            errorDiv.innerHTML = `
                <div class="chart-error-content">
                    <span class="chart-error-icon">⚠️</span>
                    <span class="chart-error-message">${message}</span>
                    <button class="chart-error-retry" onclick="location.reload()">Retry</button>
                </div>
            `;

            canvas.style.display = "none";
            container.appendChild(errorDiv);
        }

        console.error(`📊 Chart error: ${message}`);
    }

    bindEvents() {
        // Listen for period changes
        document.addEventListener("periodChanged", (event) => {
            this.updateAllChartsPeriod(event.detail.period);
        });

        // Listen for data updates
        document.addEventListener("taskUpdated", () => {
            this.refreshAllCharts();
        });

        document.addEventListener("taskCompleted", () => {
            this.refreshAllCharts();
        });

        // Window resize
        window.addEventListener("resize", () => {
            this.resizeAllCharts();
        });
    }

    async updateAllChartsPeriod(period) {
        console.log(`📊 Updating all charts for period: ${period}`);

        const updatePromises = Object.keys(this.charts).map((chartType) =>
            this.updateChartPeriod(chartType, period)
        );

        await Promise.all(updatePromises);
    }

    async refreshAllCharts() {
        console.log("🔄 Refreshing all charts");

        const refreshPromises = Object.keys(this.charts).map((chartType) =>
            this.refreshChart(chartType)
        );

        await Promise.all(refreshPromises);
    }

    resizeAllCharts() {
        Object.values(this.charts).forEach((chart) => {
            chart.resize();
        });
    }

    destroy() {
        console.log("🧹 Destroying all charts");

        // Destroy all chart instances
        Object.values(this.charts).forEach((chart) => {
            chart.destroy();
        });

        // Clear references
        this.charts = {};

        // Remove event listeners
        window.removeEventListener("resize", this.resizeAllCharts);
    }
}

// Initialize charts component
const priorityCharts = new PriorityCharts();

// Make available globally
window.priorityCharts = priorityCharts;

export default PriorityCharts;
