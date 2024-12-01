class PreferencesPreview {
    constructor() {
        this.charts = {};
        this.initializeCharts();
        this.bindEvents();
    }

    initializeCharts() {
        // Sample data for previews
        const sampleData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            values: [1200, 1900, 1600, 2100, 1800]
        };

        // Initialize different chart types
        this.createLineChart(sampleData);
        this.createBarChart(sampleData);
        this.createDoughnutChart(sampleData);
        this.createAreaChart(sampleData);
    }

    createLineChart(data) {
        const ctx = document.getElementById('previewLineChart').getContext('2d');
        this.charts.line = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Sample Data',
                    data: data.values,
                    borderColor: this.getChartColors()[0],
                    tension: 0.4,
                    fill: false
                }]
            },
            options: this.getChartOptions()
        });
    }

    createBarChart(data) {
        const ctx = document.getElementById('previewBarChart').getContext('2d');
        this.charts.bar = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Sample Data',
                    data: data.values,
                    backgroundColor: this.getChartColors()
                }]
            },
            options: this.getChartOptions()
        });
    }

    createDoughnutChart(data) {
        const ctx = document.getElementById('previewDoughnutChart').getContext('2d');
        this.charts.doughnut = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: this.getChartColors()
                }]
            },
            options: {
                ...this.getChartOptions(),
                cutout: '70%'
            }
        });
    }

    createAreaChart(data) {
        const ctx = document.getElementById('previewAreaChart').getContext('2d');
        this.charts.area = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Sample Data',
                    data: data.values,
                    borderColor: this.getChartColors()[0],
                    backgroundColor: this.getChartColors()[0] + '40',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: this.getChartOptions()
        });
    }

    getChartColors() {
        const colorScheme = document.querySelector('[name="chart_colors"]').value;
        const schemes = {
            default: ['#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56', '#9966FF'],
            monochrome: ['#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
            pastel: ['#67e8f9', '#a5b4fc', '#fca5a5', '#86efac', '#fde047'],
            vibrant: ['#ef4444', '#f59e0b', '#10b981', '#6366f1', '#ec4899']
        };
        return schemes[colorScheme] || schemes.default;
    }

    getChartOptions() {
        const showAnimations = document.querySelector('[name="chart_animations"]').checked;
        const showLabels = document.querySelector('[name="chart_labels"]').checked;
        const currencyFormat = document.querySelector('[name="currency_format"]').value;

        return {
            animation: {
                duration: showAnimations ? 1000 : 0
            },
            plugins: {
                datalabels: {
                    display: showLabels,
                    color: '#fff',
                    formatter: (value) => this.formatCurrency(value, currencyFormat)
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => this.formatCurrency(value, currencyFormat)
                    }
                }
            }
        };
    }

    formatCurrency(value, format) {
        switch (format) {
            case 'compact':
                return value >= 1000000 ? `$${(value / 1000000).toFixed(1)}M` :
                       value >= 1000 ? `$${(value / 1000).toFixed(1)}K` :
                       `$${value}`;
            case 'full':
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD'
                }).format(value);
            case 'decimal':
                return `$${value.toFixed(2)}`;
            default:
                return `$${value}`;
        }
    }

    updateCharts() {
        Object.values(this.charts).forEach(chart => {
            chart.options = this.getChartOptions();
            chart.data.datasets.forEach(dataset => {
                if (dataset.backgroundColor) {
                    dataset.backgroundColor = this.getChartColors();
                }
                if (dataset.borderColor) {
                    dataset.borderColor = this.getChartColors()[0];
                }
            });
            chart.update();
        });
    }

    bindEvents() {
        // Update preview when preferences change
        document.querySelectorAll('select, input[type="checkbox"]').forEach(input => {
            input.addEventListener('change', () => this.updateCharts());
        });

        // Theme switcher
        document.querySelector('[name="theme"]').addEventListener('change', (e) => {
            document.documentElement.setAttribute('data-theme', e.target.value);
            this.updateCharts();
        });
    }
}

// Initialize preview when document is ready
document.addEventListener('DOMContentLoaded', () => {
    new PreferencesPreview();
}); 