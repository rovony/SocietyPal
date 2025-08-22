// Task Metrics Component
// Handles individual metric calculations and updates

class TaskMetrics {
    constructor() {
        this.cache = new Map();
        this.cacheTimeout = 5 * 60 * 1000; // 5 minutes
        this.updateQueue = [];
        this.isProcessing = false;

        this.init();
    }

    init() {
        console.log("📊 Initializing Task Metrics component");

        this.setupMetricDefinitions();
        this.bindEvents();
    }

    setupMetricDefinitions() {
        // Define how each metric is calculated
        this.metricDefinitions = {
            "tasks-completed": {
                label: "Tasks Completed",
                calculation: "count",
                source: "task_completions",
                aggregation: "sum",
                format: "number",
                icon: "check-circle",
                color: "success",
            },

            "priority-high": {
                label: "High Priority Tasks",
                calculation: "count",
                source: "tasks",
                filter: { priority: "high", status: "open" },
                aggregation: "count",
                format: "number",
                icon: "exclamation-triangle",
                color: "warning",
            },

            "productivity-score": {
                label: "Productivity Score",
                calculation: "custom",
                customFunction: this.calculateProductivityScore.bind(this),
                format: "percentage",
                icon: "trending-up",
                color: "info",
            },

            "time-saved": {
                label: "Time Saved (Hours)",
                calculation: "sum",
                source: "productivity_metrics",
                field: "time_saved_minutes",
                aggregation: "sum",
                transform: (value) => Math.round((value / 60) * 10) / 10, // Convert to hours
                format: "decimal",
                icon: "clock",
                color: "primary",
            },

            "completion-rate": {
                label: "Completion Rate",
                calculation: "ratio",
                numerator: { source: "tasks", filter: { status: "completed" } },
                denominator: { source: "tasks" },
                format: "percentage",
                icon: "target",
                color: "success",
            },

            "average-priority": {
                label: "Average Priority Level",
                calculation: "average",
                source: "tasks",
                field: "priority_score", // high=3, medium=2, low=1
                format: "decimal",
                icon: "bar-chart",
                color: "info",
            },
        };
    }

    bindEvents() {
        // Listen for data updates
        document.addEventListener("taskUpdated", (event) => {
            this.handleTaskUpdate(event.detail);
        });

        document.addEventListener("taskCompleted", (event) => {
            this.handleTaskCompletion(event.detail);
        });

        document.addEventListener("periodChanged", (event) => {
            this.clearCache();
            this.recalculateAllMetrics(event.detail.period);
        });
    }

    async calculateMetric(
        metricType,
        period = "last-7-days",
        forceRefresh = false
    ) {
        const cacheKey = `${metricType}_${period}`;

        // Check cache first
        if (!forceRefresh && this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < this.cacheTimeout) {
                console.log(`📈 Using cached metric: ${metricType}`);
                return cached.data;
            }
        }

        const definition = this.metricDefinitions[metricType];
        if (!definition) {
            throw new Error(`Unknown metric type: ${metricType}`);
        }

        console.log(
            `🔢 Calculating metric: ${metricType} for period: ${period}`
        );

        let result;

        try {
            switch (definition.calculation) {
                case "count":
                    result = await this.calculateCount(definition, period);
                    break;

                case "sum":
                    result = await this.calculateSum(definition, period);
                    break;

                case "average":
                    result = await this.calculateAverage(definition, period);
                    break;

                case "ratio":
                    result = await this.calculateRatio(definition, period);
                    break;

                case "custom":
                    result = await definition.customFunction(period);
                    break;

                default:
                    throw new Error(
                        `Unknown calculation type: ${definition.calculation}`
                    );
            }

            // Apply transformation if specified
            if (definition.transform) {
                result.value = definition.transform(result.value);
            }

            // Apply formatting
            result.formatted = this.formatMetricValue(
                result.value,
                definition.format
            );

            // Calculate change from previous period
            result.change = await this.calculatePeriodChange(
                metricType,
                period,
                result.value
            );

            // Cache the result
            this.cache.set(cacheKey, {
                data: result,
                timestamp: Date.now(),
            });

            console.log(
                `✅ Calculated ${metricType}: ${result.formatted} (${
                    result.change > 0 ? "+" : ""
                }${result.change}%)`
            );

            return result;
        } catch (error) {
            console.error(
                `❌ Failed to calculate metric ${metricType}:`,
                error
            );
            throw error;
        }
    }

    async calculateCount(definition, period) {
        const response = await this.fetchMetricData(definition.source, {
            period,
            filter: definition.filter,
            aggregation: "count",
        });

        return {
            value: response.count || 0,
            rawData: response,
        };
    }

    async calculateSum(definition, period) {
        const response = await this.fetchMetricData(definition.source, {
            period,
            field: definition.field,
            filter: definition.filter,
            aggregation: "sum",
        });

        return {
            value: response.sum || 0,
            rawData: response,
        };
    }

    async calculateAverage(definition, period) {
        const response = await this.fetchMetricData(definition.source, {
            period,
            field: definition.field,
            filter: definition.filter,
            aggregation: "average",
        });

        return {
            value: response.average || 0,
            rawData: response,
        };
    }

    async calculateRatio(definition, period) {
        const [numeratorData, denominatorData] = await Promise.all([
            this.fetchMetricData(definition.numerator.source, {
                period,
                filter: definition.numerator.filter,
                aggregation: "count",
            }),
            this.fetchMetricData(definition.denominator.source, {
                period,
                filter: definition.denominator.filter,
                aggregation: "count",
            }),
        ]);

        const numerator = numeratorData.count || 0;
        const denominator = denominatorData.count || 1; // Avoid division by zero
        const ratio = denominator > 0 ? numerator / denominator : 0;

        return {
            value: ratio,
            numerator,
            denominator,
            rawData: { numeratorData, denominatorData },
        };
    }

    async calculateProductivityScore(period) {
        // Custom productivity calculation
        // Factors: completion rate, on-time completion, priority distribution

        const [completionRate, onTimeRate, priorityBalance] = await Promise.all(
            [
                this.fetchMetricData("tasks", {
                    period,
                    calculation: "completion_rate",
                }),
                this.fetchMetricData("tasks", {
                    period,
                    calculation: "on_time_rate",
                }),
                this.fetchMetricData("tasks", {
                    period,
                    calculation: "priority_balance",
                }),
            ]
        );

        // Weighted productivity score
        const score =
            (completionRate.rate || 0) * 0.4 +
            (onTimeRate.rate || 0) * 0.3 +
            (priorityBalance.score || 0) * 0.3;

        return {
            value: Math.round(score * 100) / 100, // Round to 2 decimal places
            components: {
                completionRate: completionRate.rate,
                onTimeRate: onTimeRate.rate,
                priorityBalance: priorityBalance.score,
            },
            rawData: { completionRate, onTimeRate, priorityBalance },
        };
    }

    async calculatePeriodChange(metricType, currentPeriod, currentValue) {
        try {
            // Get previous period for comparison
            const previousPeriod = this.getPreviousPeriod(currentPeriod);
            const previousResult = await this.calculateMetric(
                metricType,
                previousPeriod
            );

            if (previousResult.value === 0) {
                return currentValue > 0 ? 100 : 0;
            }

            const change =
                ((currentValue - previousResult.value) / previousResult.value) *
                100;
            return Math.round(change * 10) / 10; // Round to 1 decimal place
        } catch (error) {
            console.warn(
                `⚠️ Could not calculate change for ${metricType}:`,
                error
            );
            return 0;
        }
    }

    getPreviousPeriod(period) {
        const periodMap = {
            "last-24-hours": "previous-24-hours",
            "last-7-days": "previous-7-days",
            "last-30-days": "previous-30-days",
            "last-90-days": "previous-90-days",
            "last-year": "previous-year",
        };

        return periodMap[period] || "previous-7-days";
    }

    formatMetricValue(value, format) {
        switch (format) {
            case "number":
                return Math.round(value).toLocaleString();

            case "decimal":
                return (Math.round(value * 10) / 10).toLocaleString();

            case "percentage":
                return `${Math.round(value * 100)}%`;

            case "currency":
                return new Intl.NumberFormat("en-US", {
                    style: "currency",
                    currency: "USD",
                }).format(value);

            case "duration":
                const hours = Math.floor(value);
                const minutes = Math.round((value - hours) * 60);
                return `${hours}h ${minutes}m`;

            default:
                return value.toString();
        }
    }

    async fetchMetricData(source, params) {
        const queryParams = new URLSearchParams(params);
        const url = `/api/analytics/metrics/${source}?${queryParams}`;

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

    async handleTaskUpdate(taskData) {
        console.log("📝 Task updated, recalculating affected metrics");

        // Determine which metrics are affected by this task update
        const affectedMetrics = this.getAffectedMetrics(taskData);

        // Queue metrics for update
        affectedMetrics.forEach((metricType) => {
            this.queueMetricUpdate(metricType);
        });

        // Process the queue
        this.processUpdateQueue();
    }

    async handleTaskCompletion(taskData) {
        console.log("✅ Task completed, updating completion metrics");

        // Specific metrics affected by task completion
        const affectedMetrics = [
            "tasks-completed",
            "completion-rate",
            "productivity-score",
        ];

        if (taskData.priority === "high") {
            affectedMetrics.push("priority-high");
        }

        // Queue for immediate update
        affectedMetrics.forEach((metricType) => {
            this.queueMetricUpdate(metricType, true); // Priority update
        });

        this.processUpdateQueue();
    }

    getAffectedMetrics(taskData) {
        const affected = [];

        // Check each metric definition to see if it's affected
        Object.entries(this.metricDefinitions).forEach(
            ([metricType, definition]) => {
                if (this.isMetricAffectedByTask(definition, taskData)) {
                    affected.push(metricType);
                }
            }
        );

        return affected;
    }

    isMetricAffectedByTask(definition, taskData) {
        // Check if the metric's data source includes tasks
        if (
            definition.source === "tasks" ||
            definition.source === "task_completions"
        ) {
            return true;
        }

        // Check custom metrics
        if (definition.calculation === "custom") {
            return true; // Custom metrics might depend on task data
        }

        return false;
    }

    queueMetricUpdate(metricType, priority = false) {
        // Remove existing updates for this metric
        this.updateQueue = this.updateQueue.filter(
            (update) => update.metricType !== metricType
        );

        // Add new update
        const update = {
            metricType,
            priority,
            timestamp: Date.now(),
        };

        if (priority) {
            this.updateQueue.unshift(update); // Add to front
        } else {
            this.updateQueue.push(update); // Add to back
        }
    }

    async processUpdateQueue() {
        if (this.isProcessing || this.updateQueue.length === 0) {
            return;
        }

        this.isProcessing = true;

        try {
            while (this.updateQueue.length > 0) {
                const update = this.updateQueue.shift();

                try {
                    // Recalculate the metric
                    const result = await this.calculateMetric(
                        update.metricType,
                        "current",
                        true
                    );

                    // Dispatch update event
                    document.dispatchEvent(
                        new CustomEvent("metricUpdated", {
                            detail: {
                                metricType: update.metricType,
                                result,
                                timestamp: Date.now(),
                            },
                        })
                    );
                } catch (error) {
                    console.error(
                        `❌ Failed to update metric ${update.metricType}:`,
                        error
                    );
                }

                // Small delay to prevent overwhelming the system
                await new Promise((resolve) => setTimeout(resolve, 100));
            }
        } finally {
            this.isProcessing = false;
        }
    }

    async recalculateAllMetrics(period = "last-7-days") {
        console.log(`🔄 Recalculating all metrics for period: ${period}`);

        const metricTypes = Object.keys(this.metricDefinitions);

        // Calculate all metrics in parallel
        const promises = metricTypes.map(async (metricType) => {
            try {
                const result = await this.calculateMetric(
                    metricType,
                    period,
                    true
                );
                return { metricType, result, success: true };
            } catch (error) {
                console.error(`❌ Failed to recalculate ${metricType}:`, error);
                return { metricType, error, success: false };
            }
        });

        const results = await Promise.all(promises);

        // Dispatch results
        results.forEach(({ metricType, result, success, error }) => {
            if (success) {
                document.dispatchEvent(
                    new CustomEvent("metricUpdated", {
                        detail: { metricType, result, timestamp: Date.now() },
                    })
                );
            } else {
                document.dispatchEvent(
                    new CustomEvent("metricError", {
                        detail: { metricType, error, timestamp: Date.now() },
                    })
                );
            }
        });

        console.log(
            `✅ Recalculated ${results.filter((r) => r.success).length}/${
                metricTypes.length
            } metrics`
        );
    }

    clearCache() {
        console.log("🧹 Clearing metrics cache");
        this.cache.clear();
    }

    getMetricDefinition(metricType) {
        return this.metricDefinitions[metricType];
    }

    getAllMetricTypes() {
        return Object.keys(this.metricDefinitions);
    }
}

// Initialize metrics component
const taskMetrics = new TaskMetrics();

// Make available globally
window.taskMetrics = taskMetrics;

export default TaskMetrics;
