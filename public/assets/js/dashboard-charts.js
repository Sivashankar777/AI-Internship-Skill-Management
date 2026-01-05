// Dashboard Charts JavaScript
class DashboardCharts {
    constructor() {
        this.charts = {};
        this.init();
    }
    
    init() {
        this.initSkillProgressChart();
        this.initTaskCompletionChart();
        this.initLearningPathChart();
        this.initPerformanceChart();
        this.initActivityChart();
    }
    
    initSkillProgressChart() {
        const ctx = document.getElementById('skillProgressChart');
        if (!ctx) return;
        
        this.charts.skillProgress = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['PHP', 'JavaScript', 'MySQL', 'React', 'Node.js', 'Docker'],
                datasets: [{
                    label: 'Current Level',
                    data: [85, 70, 90, 60, 75, 50],
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    borderColor: '#6366f1',
                    borderWidth: 2,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#6366f1'
                }, {
                    label: 'Target Level',
                    data: [90, 85, 95, 85, 90, 80],
                    backgroundColor: 'rgba(168, 85, 247, 0.2)',
                    borderColor: '#a855f7',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointBackgroundColor: '#a855f7',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#a855f7'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        pointLabels: {
                            color: '#cbd5e1',
                            font: {
                                size: 12
                            }
                        },
                        ticks: {
                            display: false,
                            maxTicksLimit: 5
                        },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#cbd5e1',
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(99, 102, 241, 0.5)',
                        borderWidth: 1
                    }
                }
            }
        });
    }
    
    initTaskCompletionChart() {
        const ctx = document.getElementById('taskCompletionChart');
        if (!ctx) return;
        
        this.charts.taskCompletion = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Completed Tasks',
                    data: [8, 12, 9, 15],
                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                    borderColor: '#6366f1',
                    borderWidth: 1,
                    borderRadius: 8,
                    borderSkipped: false
                }, {
                    label: 'Total Tasks',
                    data: [10, 15, 12, 18],
                    backgroundColor: 'rgba(168, 85, 247, 0.3)',
                    borderColor: '#a855f7',
                    borderWidth: 1,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#cbd5e1'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#cbd5e1',
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#cbd5e1'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(99, 102, 241, 0.5)',
                        borderWidth: 1
                    }
                }
            }
        });
    }
    
    initLearningPathChart() {
        const ctx = document.getElementById('learningPathChart');
        if (!ctx) return;
        
        this.charts.learningPath = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Learning Progress',
                    data: [30, 45, 60, 75, 85, 92],
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#cbd5e1'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#cbd5e1',
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(99, 102, 241, 0.5)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return `Progress: ${context.parsed.y}%`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    initPerformanceChart() {
        const ctx = document.getElementById('performanceChart');
        if (!ctx) return;
        
        this.charts.performance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Excellent', 'Good', 'Average', 'Needs Improvement'],
                datasets: [{
                    data: [35, 40, 15, 10],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        '#10b981',
                        '#6366f1',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 2,
                    borderRadius: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#cbd5e1',
                            padding: 20,
                            font: {
                                size: 12
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(99, 102, 241, 0.5)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed}%`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    initActivityChart() {
        const ctx = document.getElementById('activityChart');
        if (!ctx) return;
        
        // Generate random activity data
        const generateData = () => {
            const data = [];
            for (let i = 0; i < 24; i++) {
                data.push(Math.floor(Math.random() * 100));
            }
            return data;
        };
        
        this.charts.activity = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Array.from({length: 24}, (_, i) => `${i}:00`),
                datasets: [{
                    label: 'Activity Level',
                    data: generateData(),
                    backgroundColor: (context) => {
                        const value = context.raw;
                        if (value > 75) return 'rgba(99, 102, 241, 0.9)';
                        if (value > 50) return 'rgba(99, 102, 241, 0.7)';
                        if (value > 25) return 'rgba(99, 102, 241, 0.5)';
                        return 'rgba(99, 102, 241, 0.3)';
                    },
                    borderColor: '#6366f1',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#cbd5e1',
                            maxTicksLimit: 12
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#cbd5e1',
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(99, 102, 241, 0.5)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return `Activity: ${context.parsed.y}%`;
                            }
                        }
                    }
                }
            }
        });
        
        // Update chart with new data every 5 seconds
        setInterval(() => {
            this.charts.activity.data.datasets[0].data = generateData();
            this.charts.activity.update('none');
        }, 5000);
    }
    
    // Export charts data
    exportChartData(chartName) {
        if (!this.charts[chartName]) {
            console.error(`Chart ${chartName} not found`);
            return null;
        }
        
        const chart = this.charts[chartName];
        return {
            labels: chart.data.labels,
            datasets: chart.data.datasets.map(dataset => ({
                label: dataset.label,
                data: dataset.data
            }))
        };
    }
    
    // Update chart data
    updateChart(chartName, newData) {
        if (!this.charts[chartName]) {
            console.error(`Chart ${chartName} not found`);
            return;
        }
        
        const chart = this.charts[chartName];
        
        if (newData.labels) {
            chart.data.labels = newData.labels;
        }
        
        if (newData.datasets) {
            newData.datasets.forEach((dataset, index) => {
                if (chart.data.datasets[index]) {
                    Object.assign(chart.data.datasets[index], dataset);
                }
            });
        }
        
        chart.update();
    }
    
    // Resize charts on window resize
    handleResize() {
        Object.values(this.charts).forEach(chart => {
            chart.resize();
        });
    }
    
    // Destroy all charts
    destroy() {
        Object.values(this.charts).forEach(chart => {
            chart.destroy();
        });
        this.charts = {};
    }
}

// Initialize dashboard charts
document.addEventListener('DOMContentLoaded', () => {
    window.DashboardCharts = new DashboardCharts();
    
    // Handle window resize
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            window.DashboardCharts.handleResize();
        }, 250);
    });
    
    // Export functionality
    const exportBtn = document.getElementById('exportChartsBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            const chartData = {};
            Object.keys(window.DashboardCharts.charts).forEach(chartName => {
                chartData[chartName] = window.DashboardCharts.exportChartData(chartName);
            });
            
            // Create downloadable JSON
            const dataStr = JSON.stringify(chartData, null, 2);
            const dataUri = 'data:application/json;charset=utf-8,' + encodeURIComponent(dataStr);
            
            const exportFileDefaultName = 'dashboard-charts-data.json';
            
            const linkElement = document.createElement('a');
            linkElement.setAttribute('href', dataUri);
            linkElement.setAttribute('download', exportFileDefaultName);
            linkElement.click();
        });
    }
});

// Chart utility functions
const ChartUtils = {
    // Create gradient for chart backgrounds
    createGradient(ctx, color1, color2) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    },
    
    // Format large numbers
    formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        }
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num;
    },
    
    // Generate random color
    randomColor(opacity = 1) {
        const r = Math.floor(Math.random() * 255);
        const g = Math.floor(Math.random() * 255);
        const b = Math.floor(Math.random() * 255);
        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    },
    
    // Darken color
    darkenColor(color, percent) {
        const num = parseInt(color.slice(1), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) - amt;
        const G = (num >> 8 & 0x00FF) - amt;
        const B = (num & 0x0000FF) - amt;
        return `#${(
            0x1000000 +
            (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
            (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
            (B < 255 ? B < 1 ? 0 : B : 255)
        ).toString(16).slice(1)}`;
    }
};

// Register Chart.js plugin for glass effect
Chart.register({
    id: 'glassBackground',
    beforeDraw: function(chart) {
        const ctx = chart.ctx;
        const canvas = chart.canvas;
        
        // Create glass effect background
        ctx.save();
        ctx.globalCompositeOperation = 'destination-over';
        ctx.fillStyle = 'rgba(15, 23, 42, 0.7)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.restore();
    }
});

// Register plugin for custom tooltip styling
Chart.register({
    id: 'customTooltip',
    afterDraw: function(chart) {
        // Custom tooltip styling can be added here
    }
});