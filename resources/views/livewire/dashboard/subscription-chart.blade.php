<div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-4 dark:bg-gray-800">
    <div class="flex items-center justify-between mb-2">
        <div class="flex-shrink-0">
            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">
               @lang('modules.dashboard.subscriptionTrends')
            </h3>
        </div>
    </div>

    <div id="subscriptionTrendChart"></div>

    @script
    <script>
        if (document.getElementById('subscriptionTrendChart')) {
            const chart = new ApexCharts(document.getElementById('subscriptionTrendChart'), getSubscriptionChartOptions());
            chart.render();

            document.addEventListener('dark-mode', function () {
                chart.updateOptions(getSubscriptionChartOptions());
            });
        }

        function getSubscriptionChartOptions() {
            let chartColors = {};

            if (document.documentElement.classList.contains('dark')) {
                chartColors = {
                    borderColor: '#374151',
                    labelColor: '#9CA3AF',
                    opacityFrom: 0,
                    opacityTo: 0.15,
                };
            } else {
                chartColors = {
                    borderColor: '#F3F4F6',
                    labelColor: '#6B7280',
                    opacityFrom: 0.45,
                    opacityTo: 0,
                }
            }

            // Format dates to 'd M' format
            const formattedDates = @json($dates ?? []).map(date => {
                const options = { day: '2-digit', month: 'short' };
                return new Date(date).toLocaleDateString('en-GB', options);
            });

            return {
                chart: {
                    height: 280,
                    type: 'area',
                    fontFamily: 'Inter, sans-serif',
                    foreColor: chartColors.labelColor,
                    toolbar: { show: false }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        enabled: true,
                        opacityFrom: chartColors.opacityFrom,
                        opacityTo: chartColors.opacityTo
                    }
                },
                dataLabels: { enabled: false },
                tooltip: {
                    style: {
                        fontSize: '14px',
                        fontFamily: 'Inter, sans-serif',
                    },
                },
                grid: {
                    show: true,
                    borderColor: chartColors.borderColor,
                    strokeDashArray: 1,
                    padding: { left: 20, bottom: 10 }
                },
                series: [
                    {
                        name: "Subscriptions",
                        data: @json($subscriptions ?? []),
                        color: '#76C7C0'
                    }
                ],
                markers: {
                    size: 4,
                    strokeColors: '#ffffff',
                    hover: {
                        size: undefined,
                        sizeOffset: 2
                    }
                },
                xaxis: {
                    categories: formattedDates,
                    labels: {
                        style: {
                            colors: [chartColors.labelColor],
                            fontSize: '12px',
                            fontWeight: 500,
                        },
                    },
                    axisBorder: { color: chartColors.borderColor },
                    axisTicks: { color: chartColors.borderColor },
                    crosshairs: {
                        show: true,
                        position: 'back',
                        stroke: {
                            color: chartColors.borderColor,
                            width: 1,
                            dashArray: 10,
                        },
                    },
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: [chartColors.labelColor],
                            fontSize: '12px',
                            fontWeight: 500,
                        },
                    },
                },
                legend: {
                    fontSize: '12px',
                    fontWeight: 500,
                    fontFamily: 'Inter, sans-serif',
                    labels: { colors: [chartColors.labelColor] },
                    itemMargin: { horizontal: 5 }
                },
                responsive: [
                    {
                        breakpoint: 1024,
                        options: {
                            xaxis: {
                                labels: { show: false }
                            }
                        }
                    }
                ]
            }
        }
    </script>
    @endscript
</div>
