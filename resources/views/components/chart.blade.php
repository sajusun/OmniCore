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

<!-- Separate Alpine component for each chart to avoid conflicts -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chartComponent_{{ str_replace('-', '_', $chartId) }}', () => ({
            chart: null,
        initChart() {
                console.log('initChart called');
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
                    theme: {
                        mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                    },
                    colors: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1'],
                    dataLabels: {
                        enabled: false
                    }
                };

                this.chart = new ApexCharts(this.$refs.chart, options);
                
                this.chart.render();


                // Listen for dark mode toggle if applicable
                const observer = new MutationObserver(() => {
                    const isDark = document.documentElement.classList.contains('dark');
                    this.chart.updateOptions({
                        theme: { mode: isDark ? 'dark' : 'light' },
                        title: { style: { color: isDark ? '#fff' : '#212529' } },
                        xaxis: { labels: { style: { colors: isDark ? '#adb5bd' : '#495057' } } },
                        yaxis: { labels: { style: { colors: isDark ? '#adb5bd' : '#495057' } } },
                        grid: { borderColor: isDark ? '#343a40' : '#dee2e6' }
                    });
                });
                
                observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            }
        }));
    });
</script>

@endpush

@endonce
