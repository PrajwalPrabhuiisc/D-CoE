<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --primary-color: #4361ee; --secondary-color: #3f37c9; --accent-color: #4cc9f0; --light-color: #f8f9fa; --dark-color: #212529; --success-color: #4CAF50; --warning-color: #ff9800; --danger-color: #f44336; }
        body { font-family: 'Poppins', sans-serif; background-color: #f5f7fa; color: #333; }
        .navbar { background-color: white !important; box-shadow: 0 2px 15px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; color: var(--primary-color) !important; font-size: 1.5rem; }
        .nav-link { font-weight: 500; color: #555 !important; margin: 0 5px; transition: all 0.3s ease; }
        .nav-link:hover { color: var(--primary-color) !important; transform: translateY(-2px); }
        .nav-link.active { color: var(--primary-color) !important; border-bottom: 3px solid var(--primary-color); }
        .dashboard-container { padding: 30px 0; }
        .page-title { font-weight: 700; color: var(--dark-color); margin-bottom: 30px; border-left: 5px solid var(--primary-color); padding-left: 15px; }
        .card { border-radius: 10px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: transform 0.3s ease; margin-bottom: 25px; overflow: hidden; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .card-header { background-color: white; border-bottom: 1px solid rgba(0,0,0,0.05); font-weight: 600; font-size: 1.1rem; padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; }
        .card-header i { margin-right: 10px; color: var(--primary-color); }
        .card-body { padding: 20px; }
        .chart-actions { display: flex; gap: 10px; }
        .chart-action-btn { font-size: 0.9rem; padding: 5px 10px; }
        .load-more-btn { display: block; margin: 10px auto; }
        .filters { display: flex; gap: 15px; margin-bottom: 20px; align-items: center; }
        .filter-label { font-weight: 500; color: #555; }
        .filter-input { border: 1px solid #ddd; border-radius: 5px; padding: 8px; }
        .filter-btn { background-color: var(--primary-color); color: white; border: none; border-radius: 5px; padding: 8px 15px; font-weight: 500; }
        .filter-btn:hover { background-color: var(--secondary-color); }
        @media (max-width: 768px) { .filters { flex-direction: column; align-items: stretch; } .filter-input { width: 100%; } }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-book-open me-2"></i>Diary System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="task_mapping.php"><i class="fas fa-map me-1"></i> Task Mapping</a></li>
                    <li class="nav-item"><a class="nav-link active" href="analytics.php"><i class="fas fa-chart-line me-1"></i> Analytics</a></li>
                    <li class="nav-item"><a class="nav-link" href="kanban.php"><i class="fas fa-columns me-1"></i> Kanban</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="insights.php">
                            <i class="fas fa-lightbulb me-1"></i> Insights
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container dashboard-container">
        <h1 class="page-title">Analytics Dashboard</h1>

        <!-- Date Range Filter -->
        <div class="filters">
            <div>
                <label class="filter-label">Time Range:</label>
                <select id="timeFilter" class="filter-input">
                    <option value="all" selected>Lifetime</option>
                    <option value="30days">Last 30 Days</option>
                    <option value="7days">Last 7 Days</option>
                </select>
            </div>
            <button class="filter-btn" onclick="applyFilters()">Apply Filters</button>
        </div>

        <div class="row">
            <!-- Task Status Distribution -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <span><i class="fas fa-chart-pie"></i> Task Status Distribution</span>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-outline-primary chart-action-btn download-btn" data-chart="taskStatus">Download Data</button>
                        </div>
                    </div>
                    <div class="card-body"><canvas id="taskStatusChart" height="250"></canvas></div>
                </div>
            </div>
            
            <!-- Task Time Deviation -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <span><i class="fas fa-clock"></i> Task Time Deviation</span>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-outline-primary chart-action-btn download-btn" data-chart="timeDeviation">Download Data</button>
                        </div>
                    </div>
                    <div class="card-body"><canvas id="timeDeviationChart" height="250"></canvas></div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <!-- Blocked Tasks Trend -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <span><i class="fas fa-lock"></i> Blocked Tasks Trend</span>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-outline-primary chart-action-btn toggle-type-btn" data-chart="blockedTasks">Toggle Chart Type</button>
                            <button class="btn btn-sm btn-outline-primary chart-action-btn download-btn" data-chart="blockedTasks">Download Data</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="blockedTasksChart" height="250"></canvas>
                        <button class="btn btn-sm btn-outline-primary load-more-btn" data-chart="blockedTasks" style="display: none;">Load More</button>
                    </div>
                </div>
            </div>
            
            <!-- Daily Diary Submission Trend -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <span><i class="fas fa-check-circle"></i> Daily Diary Submission Trend</span>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-outline-primary chart-action-btn toggle-type-btn" data-chart="submissionRate">Toggle Chart Type</button>
                            <button class="btn btn-sm btn-outline-primary chart-action-btn download-btn" data-chart="submissionRate">Download Data</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="submissionRateChart" height="250"></canvas>
                        <button class="btn btn-sm btn-outline-primary load-more-btn" data-chart="submissionRate" style="display: none;">Load More</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <!-- Task Completion Rate Over Time -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <span><i class="fas fa-check-square"></i> Task Completion Rate Over Time</span>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-outline-primary chart-action-btn toggle-type-btn" data-chart="completionRate">Toggle Chart Type</button>
                            <button class="btn btn-sm btn-outline-primary chart-action-btn download-btn" data-chart="completionRate">Download Data</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="completionRateChart" height="250"></canvas>
                        <button class="btn btn-sm btn-outline-primary load-more-btn" data-chart="completionRate" style="display: none;">Load More</button>
                    </div>
                </div>
            </div>
            
            <!-- Average Task Duration by Status -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <span><i class="fas fa-hourglass-half"></i> Average Task Duration by Status</span>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-outline-primary chart-action-btn download-btn" data-chart="avgTaskDuration">Download Data</button>
                        </div>
                    </div>
                    <div class="card-body"><canvas id="avgTaskDurationChart" height="250"></canvas></div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <!-- Project Completion -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <span><i class="fas fa-project-diagram"></i> Project Completion</span>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-outline-primary chart-action-btn download-btn" data-chart="projectCompletion">Download Data</button>
                        </div>
                    </div>
                    <div class="card-body"><canvas id="projectCompletionChart" height="250"></canvas></div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <!-- User Workload -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <span><i class="fas fa-user-check"></i> User Workload</span>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-outline-primary chart-action-btn download-btn" data-chart="userWorkload">Download Data</button>
                        </div>
                    </div>
                    <div class="card-body"><canvas id="userWorkloadChart" height="250"></canvas></div>
                </div>
            </div>
            
            <!-- SA Observations by Category -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <span><i class="fas fa-eye"></i> SA Observations by Category</span>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-outline-primary chart-action-btn download-btn" data-chart="saObservations">Download Data</button>
                        </div>
                    </div>
                    <div class="card-body"><canvas id="saObservationsChart" height="250"></canvas></div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <!-- User Activity Heatmap -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <span><i class="fas fa-fire"></i> User Activity Heatmap</span>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-outline-primary chart-action-btn download-btn" data-chart="userActivity">Download Data</button>
                        </div>
                    </div>
                    <div class="card-body"><canvas id="userActivityChart" height="250"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let charts = {};
        let chartTypes = {
            blockedTasks: 'line',
            submissionRate: 'line',
            completionRate: 'line'
        };
        let chartPages = {
            blockedTasks: 1,
            submissionRate: 1,
            completionRate: 1
        };
        const PAGE_SIZE = 30;
        let currentTimeFilter = 'all';

        function fetchAnalytics(page = 1, chartName = null) {
            const timeFilter = currentTimeFilter;
            const url = `get_analytics.php?page=${page}&pageSize=${PAGE_SIZE}&time=${timeFilter}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (chartName) {
                        // Update only the specific chart
                        charts[chartName].destroy();
                        updateChart(chartName, data);
                    } else {
                        // Update all charts
                        Object.values(charts).forEach(chart => chart.destroy());

                        charts.taskStatus = new Chart(document.getElementById('taskStatusChart').getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: data.taskStatus.labels,
                                datasets: [{
                                    data: data.taskStatus.data,
                                    backgroundColor: data.taskStatus.backgroundColors,
                                    borderColor: data.taskStatus.borderColors,
                                    borderWidth: 1
                                }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                        });

                        charts.timeDeviation = new Chart(document.getElementById('timeDeviationChart').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.timeDeviation.labels,
                                datasets: [
                                    { label: 'Allocated', data: data.timeDeviation.allocated, backgroundColor: 'rgba(67, 97, 238, 0.7)', borderColor: 'rgba(67, 97, 238, 1)', borderWidth: 1, borderRadius: 5 },
                                    { label: 'Actual', data: data.timeDeviation.actual, backgroundColor: 'rgba(255, 152, 0, 0.7)', borderColor: 'rgba(255, 152, 0, 1)', borderWidth: 1, borderRadius: 5 }
                                ]
                            },
                            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Hours' } } }, plugins: { legend: { position: 'bottom' } } }
                        });

                        charts.blockedTasks = new Chart(document.getElementById('blockedTasksChart').getContext('2d'), {
                            type: chartTypes.blockedTasks,
                            data: {
                                labels: data.blockedTasks.labels,
                                datasets: [{ label: 'Blocked Tasks', data: data.blockedTasks.data, borderColor: 'rgba(244, 67, 54, 1)', backgroundColor: chartTypes.blockedTasks === 'line' ? 'rgba(244, 67, 54, 0.2)' : 'rgba(244, 67, 54, 0.7)', fill: chartTypes.blockedTasks === 'line', tension: 0.3, borderWidth: 1, borderRadius: chartTypes.blockedTasks === 'bar' ? 5 : 0 }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Number of Blocked Tasks' } } }, plugins: { legend: { position: 'bottom' } } }
                        });
                        document.querySelector(`.load-more-btn[data-chart="blockedTasks"]`).style.display = data.blockedTasks.hasMore ? 'block' : 'none';

                        charts.submissionRate = new Chart(document.getElementById('submissionRateChart').getContext('2d'), {
                            type: chartTypes.submissionRate,
                            data: {
                                labels: data.submissionRate.labels,
                                datasets: [{ label: 'Diary Submissions', data: data.submissionRate.data, borderColor: 'rgba(76, 175, 80, 1)', backgroundColor: chartTypes.submissionRate === 'line' ? 'rgba(76, 175, 80, 0.2)' : 'rgba(76, 175, 80, 0.7)', fill: chartTypes.submissionRate === 'line', tension: 0.3, borderWidth: 1, borderRadius: chartTypes.submissionRate === 'bar' ? 5 : 0 }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Number of Submissions' } } }, plugins: { legend: { position: 'bottom' } } }
                        });
                        document.querySelector(`.load-more-btn[data-chart="submissionRate"]`).style.display = data.submissionRate.hasMore ? 'block' : 'none';

                        charts.completionRate = new Chart(document.getElementById('completionRateChart').getContext('2d'), {
                            type: chartTypes.completionRate,
                            data: {
                                labels: data.completionRate.labels,
                                datasets: [{ label: 'Completed Tasks', data: data.completionRate.data, borderColor: 'rgba(76, 175, 80, 1)', backgroundColor: chartTypes.completionRate === 'line' ? 'rgba(76, 175, 80, 0.2)' : 'rgba(76, 175, 80, 0.7)', fill: chartTypes.completionRate === 'line', tension: 0.3, borderWidth: 1, borderRadius: chartTypes.completionRate === 'bar' ? 5 : 0 }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Number of Completed Tasks' } } }, plugins: { legend: { position: 'bottom' } } }
                        });
                        document.querySelector(`.load-more-btn[data-chart="completionRate"]`).style.display = data.completionRate.hasMore ? 'block' : 'none';

                        charts.avgTaskDuration = new Chart(document.getElementById('avgTaskDurationChart').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.avgTaskDuration.labels,
                                datasets: [{ label: 'Average Duration (min)', data: data.avgTaskDuration.data, backgroundColor: 'rgba(67, 97, 238, 0.7)', borderColor: 'rgba(67, 97, 238, 1)', borderWidth: 1, borderRadius: 5 }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Hours' } } }, plugins: { legend: { position: 'bottom' } } }
                        });

                        charts.projectCompletion = new Chart(document.getElementById('projectCompletionChart').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.projectCompletion.labels,
                                datasets: [{ label: 'Completion %', data: data.projectCompletion.data, backgroundColor: 'rgba(67, 97, 238, 0.7)', borderColor: 'rgba(67, 97, 238, 1)', borderWidth: 1, borderRadius: 5 }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 100, title: { display: true, text: 'Percentage' } }, x: { grid: { display: false } } }, plugins: { legend: { position: 'bottom' } } }
                        });

                        charts.userWorkload = new Chart(document.getElementById('userWorkloadChart').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.userWorkload.labels,
                                datasets: [
                                    { label: 'Pending', data: data.userWorkload.pending, backgroundColor: 'rgba(255, 152, 0, 0.7)', borderColor: 'rgba(255, 152, 0, 1)', borderWidth: 1, borderRadius: 5 },
                                    { label: 'Active', data: data.userWorkload.active, backgroundColor: 'rgba(76, 201, 240, 0.7)', borderColor: 'rgba(76, 201, 240, 1)', borderWidth: 1, borderRadius: 5 },
                                    { label: 'Completed', data: data.userWorkload.completed, backgroundColor: 'rgba(76, 175, 80, 0.7)', borderColor: 'rgba(76, 175, 80, 1)', borderWidth: 1, borderRadius: 5 }
                                ]
                            },
                            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Number of Tasks' } }, x: { grid: { display: false } } }, plugins: { legend: { position: 'bottom' } } }
                        });

                        charts.saObservations = new Chart(document.getElementById('saObservationsChart').getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: data.saObservations.labels,
                                datasets: [{ data: data.saObservations.data, backgroundColor: ['rgba(67, 97, 238, 0.7)', 'rgba(76, 201, 240, 0.7)', 'rgba(76, 175, 80, 0.7)', 'rgba(255, 152, 0, 0.7)'], borderColor: ['rgba(67, 97, 238, 1)', 'rgba(76, 201, 240, 1)', 'rgba(76, 175, 80, 1)', 'rgba(255, 152, 0, 1)'], borderWidth: 1 }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                        });

                        charts.userActivity = new Chart(document.getElementById('userActivityChart').getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: data.userActivity.labels,
                                datasets: [
                                    { label: 'Diary Entries', data: data.userActivity.diary, backgroundColor: 'rgba(76, 175, 80, 0.7)', borderColor: 'rgba(76, 175, 80, 1)', borderWidth: 1, borderRadius: 5 },
                                    { label: 'Observations', data: data.userActivity.observations, backgroundColor: 'rgba(76, 201, 240, 0.7)', borderColor: 'rgba(76, 201, 240, 1)', borderWidth: 1, borderRadius: 5 },
                                    { label: 'Tasks', data: data.userActivity.tasks, backgroundColor: 'rgba(255, 152, 0, 0.7)', borderColor: 'rgba(255, 152, 0, 1)', borderWidth: 1, borderRadius: 5 }
                                ]
                            },
                            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Activity Count' } }, x: { title: { display: true, text: 'Day of Week' } } }, plugins: { legend: { position: 'bottom' } } }
                        });
                    }
                })
                .catch(error => console.error('Error fetching analytics:', error));
        }

        function updateChart(chartName, data) {
            if (chartName === 'blockedTasks') {
                charts.blockedTasks = new Chart(document.getElementById('blockedTasksChart').getContext('2d'), {
                    type: chartTypes.blockedTasks,
                    data: {
                        labels: data.blockedTasks.labels,
                        datasets: [{ label: 'Blocked Tasks', data: data.blockedTasks.data, borderColor: 'rgba(244, 67, 54, 1)', backgroundColor: chartTypes.blockedTasks === 'line' ? 'rgba(244, 67, 54, 0.2)' : 'rgba(244, 67, 54, 0.7)', fill: chartTypes.blockedTasks === 'line', tension: 0.3, borderWidth: 1, borderRadius: chartTypes.blockedTasks === 'bar' ? 5 : 0 }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Number of Blocked Tasks' } } }, plugins: { legend: { position: 'bottom' } } }
                });
                document.querySelector(`.load-more-btn[data-chart="blockedTasks"]`).style.display = data.blockedTasks.hasMore ? 'block' : 'none';
            } else if (chartName === 'submissionRate') {
                charts.submissionRate = new Chart(document.getElementById('submissionRateChart').getContext('2d'), {
                    type: chartTypes.submissionRate,
                    data: {
                        labels: data.submissionRate.labels,
                        datasets: [{ label: 'Diary Submissions', data: data.submissionRate.data, borderColor: 'rgba(76, 175, 80, 1)', backgroundColor: chartTypes.submissionRate === 'line' ? 'rgba(76, 175, 80, 0.2)' : 'rgba(76, 175, 80, 0.7)', fill: chartTypes.submissionRate === 'line', tension: 0.3, borderWidth: 1, borderRadius: chartTypes.submissionRate === 'bar' ? 5 : 0 }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Number of Submissions' } } }, plugins: { legend: { position: 'bottom' } } }
                });
                document.querySelector(`.load-more-btn[data-chart="submissionRate"]`).style.display = data.submissionRate.hasMore ? 'block' : 'none';
            } else if (chartName === 'completionRate') {
                charts.completionRate = new Chart(document.getElementById('completionRateChart').getContext('2d'), {
                    type: chartTypes.completionRate,
                    data: {
                        labels: data.completionRate.labels,
                        datasets: [{ label: 'Completed Tasks', data: data.completionRate.data, borderColor: 'rgba(76, 175, 80, 1)', backgroundColor: chartTypes.completionRate === 'line' ? 'rgba(76, 175, 80, 0.2)' : 'rgba(76, 175, 80, 0.7)', fill: chartTypes.completionRate === 'line', tension: 0.3, borderWidth: 1, borderRadius: chartTypes.completionRate === 'bar' ? 5 : 0 }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Number of Completed Tasks' } } }, plugins: { legend: { position: 'bottom' } } }
                });
                document.querySelector(`.load-more-btn[data-chart="completionRate"]`).style.display = data.completionRate.hasMore ? 'block' : 'none';
            }
        }

        function applyFilters() {
            currentTimeFilter = document.getElementById('timeFilter').value;
            chartPages = { blockedTasks: 1, submissionRate: 1, completionRate: 1 }; // Reset pagination
            fetchAnalytics();
        }

        // Toggle chart type
        document.querySelectorAll('.toggle-type-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const chartName = btn.dataset.chart;
                chartTypes[chartName] = chartTypes[chartName] === 'line' ? 'bar' : 'line';
                fetchAnalytics(chartPages[chartName], chartName);
            });
        });

        // Load more data
        document.querySelectorAll('.load-more-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const chartName = btn.dataset.chart;
                chartPages[chartName]++;
                fetchAnalytics(chartPages[chartName], chartName);
            });
        });

        // Download chart data
        document.querySelectorAll('.download-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const chartName = btn.dataset.chart;
                const chart = charts[chartName];
                if (!chart) return;

                let csvContent = '';
                if (chartName === 'taskStatus' || chartName === 'saObservations' || chartName === 'avgTaskDuration') {
                    csvContent = 'Label,Value\n';
                    chart.data.labels.forEach((label, i) => {
                        csvContent += `${label},${chart.data.datasets[0].data[i]}\n`;
                    });
                } else if (chartName === 'timeDeviation' || chartName === 'userWorkload' || chartName === 'userActivity') {
                    csvContent = 'Label,' + chart.data.datasets.map(ds => ds.label).join(',') + '\n';
                    chart.data.labels.forEach((label, i) => {
                        const values = chart.data.datasets.map(ds => ds.data[i]);
                        csvContent += `${label},${values.join(',')}\n`;
                    });
                } else if (chartName === 'blockedTasks' || chartName === 'submissionRate' || chartName === 'completionRate') {
                    csvContent = 'Date,Value\n';
                    chart.data.labels.forEach((label, i) => {
                        csvContent += `${label},${chart.data.datasets[0].data[i]}\n`;
                    });
                } else if (chartName === 'projectCompletion') {
                    csvContent = 'Project,Completion %\n';
                    chart.data.labels.forEach((label, i) => {
                        csvContent += `${label},${chart.data.datasets[0].data[i]}\n`;
                    });
                }

                const blob = new Blob([csvContent], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `${chartName}_data_${new Date().toISOString().split('T')[0]}.csv`;
                a.click();
                window.URL.revokeObjectURL(url);
            });
        });

        // Initial load
        fetchAnalytics();
    </script>
</body>
</html>
