@props([
    'type' => 'line', // line, bar, area, donut, pie
    'height' => 350,
    'series' => [],
    'categories' => [],
    'title' => null,
    'chartId' => 'chart-' . uniqid()
])

<div x-data="chartComponent_{{ str_replace('-', '_', $chartId) }}()" x-init="initChart()" class="w-100">
    <div x-ref="chart"></div>
</div>

@once
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
@endonce

@push('scripts')
<script>
    (function() {
        const initAlpineChart = () => {
            if (typeof Alpine !== 'undefined') {
                Alpine.data('chartComponent_{{ str_replace('-', '_', $chartId) }}', () => ({
                    chart: null,
                    initChart() {
                        let options = {
                            series: @json($series),
                            chart: {
                                type: '{{ $type }}',
                                height: {{ $height }},
                                toolbar: { show: false },
                                fontFamily: 'inherit',
                                background: 'transparent'
                            },
                            @if($title)
                            title: {
                                text: '{{ $title }}',
                                align: 'left',
                                style: {
                                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#212529',
                                    fontWeight: '600'
                                }
                            },
                            @endif
                            @if(in_array($type, ['pie', 'donut']))
                            labels: @json($categories),
                            @else
                            xaxis: {
                                categories: @json($categories),
                                labels: {
                                    style: {
                                        colors: document.documentElement.classList.contains('dark') ? '#adb5bd' : '#495057'
                                    }
                                },
                                axisBorder: { show: false },
                                axisTicks: { show: false }
                            },
                            yaxis: {
                                labels: {
                                    style: {
                                        colors: document.documentElement.classList.contains('dark') ? '#adb5bd' : '#495057'
                                    }
                                }
                            },
                            grid: {
                                borderColor: document.documentElement.classList.contains('dark') ? '#343a40' : '#dee2e6',
                                strokeDashArray: 4,
                            },
                            @endif
                            theme: {
                                mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                            },
                            colors: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#0dcaf0'],
                            dataLabels: {
                                enabled: false
                            }
                        };

                        if (typeof ApexCharts !== 'undefined') {
                            this.chart = new ApexCharts(this.$refs.chart, options);
                            this.chart.render();

                            const observer = new MutationObserver(() => {
                                const isDark = document.documentElement.classList.contains('dark');
                                let updateOpts = {
                                    theme: { mode: isDark ? 'dark' : 'light' },
                                    title: { style: { color: isDark ? '#fff' : '#212529' } }
                                };
                                @if(!in_array($type, ['pie', 'donut']))
                                updateOpts.xaxis = { labels: { style: { colors: isDark ? '#adb5bd' : '#495057' } } };
                                updateOpts.yaxis = { labels: { style: { colors: isDark ? '#adb5bd' : '#495057' } } };
                                updateOpts.grid = { borderColor: isDark ? '#343a40' : '#dee2e6' };
                                @endif

                                this.chart.updateOptions(updateOpts);
                            });
                            
                            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                        }
                    }
                }));
            }
        };

        if (window.Alpine) {
            initAlpineChart();
        } else {
            document.addEventListener('alpine:init', initAlpineChart);
        }
    })();
</script>
@endpush
