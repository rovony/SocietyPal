<div
    class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800"
    x-data="pieChartComponent(@json($onlineCount), @json($offlineCount))"
    x-init="$nextTick(() => renderChart())"
    @update-chart.window="updateChart($event.detail)"
>
    <div class="w-full">
        <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">
            @lang('modules.dashboard.onlineOfflineSubscriptionPayment')
        </h3>
        <div class="relative flex items-center justify-center h-80">
            @if ($onlineCount == 0 && $offlineCount == 0)
                <p class="text-center text-gray-500 dark:text-gray-400">@lang('messages.noRecordFound')</p>
            @else
                <canvas id="globalInvoiceChart" class="w-full h-full"></canvas>
            @endif
        </div>
    </div>

    <!-- Ensure Chart.js is loaded -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        function pieChartComponent(onlineCount, offlineCount) {
            return {
                chartInstance: null,
                onlineCount: onlineCount,
                offlineCount: offlineCount,

                renderChart() {
                    console.log("Initializing chart with:", this.onlineCount, this.offlineCount);

                    const canvas = document.getElementById('globalInvoiceChart');
                    if (!canvas) {
                        console.error("Canvas element not found");
                        return;
                    }

                    const ctx = canvas.getContext('2d');

                    if (this.chartInstance) {
                        this.chartInstance.destroy();
                    }

                    this.chartInstance = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Online Payment', 'Offline Payment'],
                            datasets: [{
                                data: [this.onlineCount, this.offlineCount],
                                backgroundColor: ['#76C7C0', '#FFB347'],
                                borderColor: ['#4ECDC4', '#FFA500'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        font: { size: 14 },
                                        color: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#4B5563'
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total ? Math.round((value / total) * 100) : 0;
                                            return `${label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                },

                updateChart(data) {
                    console.log("Updating chart with:", data);
                    this.onlineCount = data.onlineCount;
                    this.offlineCount = data.offlineCount;
                    this.renderChart();
                }
            };
        }
    </script>
</div>
