function initDashboardCharts() {
    const chartDataElement = document.getElementById('dashboard-chart-data');
    if (!chartDataElement) return;

    if (typeof Chart === 'undefined') {
        setTimeout(initDashboardCharts, 100);
        return;
    }

    try {
        const chartData = JSON.parse(chartDataElement.textContent);
        const chartFont = "'DM Sans', 'Space Grotesk', sans-serif";
        const gridColor = 'rgba(45, 89, 68, 0.08)';

        Chart.defaults.font.family = chartFont;
        Chart.defaults.color = '#71827c';

        // 1. Attendance Trend Line Chart
        const attendanceCanvas = document.getElementById('attendance-trend-chart');
        if (attendanceCanvas) {
            const ctx = attendanceCanvas.getContext('2d');
            const presentGradient = ctx.createLinearGradient(0, 0, 0, 180);
            presentGradient.addColorStop(0, 'rgba(37, 132, 93, 0.35)');
            presentGradient.addColorStop(1, 'rgba(37, 132, 93, 0.01)');

            const absentGradient = ctx.createLinearGradient(0, 0, 0, 180);
            absentGradient.addColorStop(0, 'rgba(232, 166, 106, 0.25)');
            absentGradient.addColorStop(1, 'rgba(232, 166, 106, 0.01)');

            new Chart(attendanceCanvas, {
                type: 'line',
                data: {
                    labels: chartData.attendanceLabels && chartData.attendanceLabels.length ? chartData.attendanceLabels : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [
                        {
                            label: 'Present',
                            data: chartData.weeklyPresent && chartData.weeklyPresent.length ? chartData.weeklyPresent : [0, 0, 0, 0, 0, 0, 0],
                            borderColor: '#25845d',
                            backgroundColor: presentGradient,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.38,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#25845d',
                        },
                        {
                            label: 'Absent',
                            data: chartData.weeklyAbsent && chartData.weeklyAbsent.length ? chartData.weeklyAbsent : [0, 0, 0, 0, 0, 0, 0],
                            borderColor: '#e8a66a',
                            backgroundColor: absentGradient,
                            borderWidth: 2,
                            fill: true,
                            tension: 0.38,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            pointBackgroundColor: '#e8a66a',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11, weight: '500' } } },
                        y: { beginAtZero: true, grid: { color: gridColor }, border: { display: false }, ticks: { precision: 0, font: { size: 10 } } },
                    },
                },
            });
        }

        // 2. Fee Status Doughnut Chart
        const feeCanvas = document.getElementById('fee-status-chart');
        if (feeCanvas) {
            new Chart(feeCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Paid', 'Pending', 'Overdue'],
                    datasets: [{
                        data: chartData.feeStatusCounts && chartData.feeStatusCounts.length ? chartData.feeStatusCounts : [0, 0, 0],
                        backgroundColor: ['#25845d', '#e8a66a', '#d36c5b'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 6,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.label + ': ' + context.raw + ' vouchers';
                                }
                            }
                        }
                    },
                },
            });
        }

        // 3. Class Enrollment Bar Chart
        const classCanvas = document.getElementById('class-enrollment-chart');
        if (classCanvas) {
            new Chart(classCanvas, {
                type: 'bar',
                data: {
                    labels: chartData.classLabels && chartData.classLabels.length ? chartData.classLabels : ['No classes yet'],
                    datasets: [{
                        label: 'Students',
                        data: chartData.classTotals && chartData.classTotals.length ? chartData.classTotals : [0],
                        backgroundColor: ['#25845d', '#31718c', '#e8a66a', '#7852a9', '#bc6342', '#3498db'],
                        borderRadius: 5,
                        barThickness: 14,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { color: gridColor }, border: { display: false }, ticks: { precision: 0, font: { size: 10 } } },
                        y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11, weight: '500' } } },
                    },
                },
            });
        }
    } catch (err) {
        console.error('Dashboard chart error:', err);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboardCharts);
} else {
    initDashboardCharts();
}