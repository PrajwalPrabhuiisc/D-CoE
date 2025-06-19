<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diary System Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4CAF50;
            --warning-color: #ff9800;
            --danger-color: #f44336;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .navbar {
            background-color: white !important;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
        }
        
        .nav-link {
            font-weight: 500;
            color: #555 !important;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
            transform: translateY(-2px);
        }
        
        .nav-link.active {
            color: var(--primary-color) !important;
            border-bottom: 3px solid var(--primary-color);
        }
        
        .dashboard-container {
            padding: 30px 0;
        }
        
        .page-title {
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 30px;
            border-left: 5px solid var(--primary-color);
            padding-left: 15px;
        }
        
        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 600;
            font-size: 1.1rem;
            padding: 15px 20px;
            display: flex;
            align-items: center;
        }
        
        .card-header i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .card-body {
            padding: 20px;
        }
        
        .list-group-item {
            padding: 15px;
            border: none;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            background-color: transparent;
            display: flex;
            flex-direction: column;
        }
        
        .list-group-item:last-child {
            border-bottom: none;
        }
        
        .task-item {
            margin-bottom: 5px;
        }
        
        .task-title {
            font-weight: 500;
        }
        
        .task-meta {
            display: flex;
            justify-content: space-between;
            color: #777;
            font-size: 0.85rem;
            margin-top: 5px;
        }
        
        .status-badge {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-active {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--accent-color);
        }
        
        .status-completed {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }
        
        .status-pending {
            background-color: rgba(255, 152, 0, 0.1);
            color: var(--warning-color);
        }
        
        .quick-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .action-card {
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            flex: 1;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .action-icon {
            background-color: rgba(67, 97, 238, 0.1);
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 15px;
        }
        
        .action-icon i {
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        
        .action-text {
            font-weight: 500;
        }
        
        .view-all {
            text-align: center;
            margin-top: 10px;
        }
        
        .view-all a {
            color: var(--primary-color);
            font-weight: 500;
            text-decoration: none;
        }
        
        .view-all a:hover {
            text-decoration: underline;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            background-color: var(--light-color);
            font-weight: 600;
            color: var(--dark-color);
            border-top: none;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        @media (max-width: 768px) {
            .quick-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <h1 class="page-title">SA Digital Board</h1>
        
        <!-- Quick Action Cards -->
        <!-- <div class="quick-actions mb-4"> -->
            <!-- <div class="action-card" onclick="window.location.href='diary_submit.php'">
                <div class="action-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="action-text">New Diary Entry</div>
            </div> -->
            <!-- <div class="action-card" onclick="window.location.href='task_mapping.php'">
                <div class="action-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="action-text">Add Task</div>
            </div> -->
            <!-- <div class="action-card" onclick="window.location.href='sa_observations.php'">
                <div class="action-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="action-text">Add Observation</div>
            </div> -->
            <!-- <div class="action-card" onclick="window.location.href='export_report.php'">
                <div class="action-icon">
                    <i class="fas fa-file-export"></i>
                </div>
                <div class="action-text">Export Report</div>
            </div>
        </div> -->
        
        <div class="row">
            <!-- Recent Diary Entries -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-book"></i> Recent Diary Entries
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group">
                            <?php
                            $stmt = $pdo->query("SELECT w.TaskDescription, w.TaskStatus, u.Username, w.EntryDate 
                                                FROM WorkDiary w 
                                                JOIN Users u ON w.UserID = u.UserID 
                                                ORDER BY w.EntryDate DESC LIMIT 5");
                            while ($row = $stmt->fetch()) {
                                $statusClass = "";
                                switch (strtolower($row['TaskStatus'])) {
                                    case 'in progress':
                                        $statusClass = "status-active";
                                        break;
                                    case 'completed':
                                        $statusClass = "status-completed";
                                        break;
                                    default:
                                        $statusClass = "status-pending";
                                }
                                echo "<li class='list-group-item'>";
                                echo "<div class='task-item'>";
                                echo "<div class='task-title'>{$row['TaskDescription']}</div>";
                                echo "<div class='task-meta'>";
                                echo "<span><i class='fas fa-user me-1'></i>{$row['Username']}</span>";
                                echo "<span class='status-badge {$statusClass}'>{$row['TaskStatus']}</span>";
                                echo "</div>";
                                echo "</div>";
                                echo "</li>";
                            }
                            ?>
                        </ul>
                        <div class="view-all">
                            <a href="report-diaryentries.php">View all entries <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent SA Observations -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-eye"></i> SA Observations
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group">
                            <?php
                            $stmt = $pdo->query("SELECT s.Details, s.Category, u.Username, s.ObservationDate 
                                                FROM SATeamObservations s 
                                                JOIN Users u ON s.UserID = u.UserID 
                                                ORDER BY s.ObservationDate DESC LIMIT 5");
                            while ($row = $stmt->fetch()) {
                                echo "<li class='list-group-item'>";
                                echo "<div class='task-item'>";
                                echo "<div class='task-title'>{$row['Details']}</div>";
                                echo "<div class='task-meta'>";
                                echo "<span><i class='fas fa-user me-1'></i>{$row['Username']}</span>";
                                echo "<span class='status-badge status-active'>{$row['Category']}</span>";
                                echo "</div>";
                                echo "</div>";
                                echo "</li>";
                            }
                            ?>
                        </ul>
                        <div class="view-all">
                            <a href="report-saobservations.php">View all observations <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Active Tasks -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-tasks"></i> Active Tasks
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group">
                            <?php
                            $stmt = $pdo->query("SELECT p.TaskName, p.Status, u.Username 
                                                FROM ProjectTasks p 
                                                JOIN Users u ON p.OwnerID = u.UserID 
                                                WHERE p.Status = 'Active' LIMIT 5");
                            while ($row = $stmt->fetch()) {
                                echo "<li class='list-group-item'>";
                                echo "<div class='task-item'>";
                                echo "<div class='task-title'>{$row['TaskName']}</div>";
                                echo "<div class='task-meta'>";
                                echo "<span><i class='fas fa-user me-1'></i>{$row['Username']}</span>";
                                echo "<span class='status-badge status-active'>{$row['Status']}</span>";
                                echo "</div>";
                                echo "</div>";
                                echo "</li>";
                            }
                            ?>
                        </ul>
                        <div class="view-all">
                            <a href="report-tasks.php">View all tasks <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Summary Charts and Tables Row -->
        <div class="row mt-4">
            <!-- Task Status Distribution -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-pie"></i> Task Status Distribution
                    </div>
                    <div class="card-body">
                        <canvas id="taskStatusChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Weekly Activity -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-bar"></i> Weekly Activity
                    </div>
                    <div class="card-body">
                        <canvas id="activityChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Task Priority Distribution -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-pie"></i> Task Priority Distribution
                    </div>
                    <div class="card-body">
                        <canvas id="priorityChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Deviation Summary -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-clock"></i> Time Deviation Summary
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Total Allocated Time (Hours)</th>
                                        <th>Total Actual Time (Hours)</th>
                                        <th>Average Deviation (Hours)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $stmt = $pdo->query("SELECT 
                                            u.Username,
                                            COALESCE(SUM(w.AllocatedTime), 0) AS total_allocated,
                                            COALESCE(SUM(w.ActualTime), 0) AS total_actual,
                                            ROUND(COALESCE(AVG(w.ActualTime - w.AllocatedTime), 0), 2) AS avg_deviation
                                            FROM Users u
                                            LEFT JOIN WorkDiary w ON w.UserID = u.UserID
                                            WHERE u.Role NOT IN ('SA Team', 'Externals', 'FSID', 'Faculties', 'Smart Factory')
                                            GROUP BY u.UserID, u.Username
                                            ORDER BY u.Username ASC");
                                        
                                        if ($stmt->rowCount() > 0) {
                                            while ($row = $stmt->fetch()) {
                                                echo "<tr>";
                                                echo "<td><i class='fas fa-user me-1'></i>" . htmlspecialchars($row['Username']) . "</td>";
                                                echo "<td>" . number_format($row['total_allocated'], 2) . "</td>";
                                                echo "<td>" . number_format($row['total_actual'], 2) . "</td>";
                                                echo "<td>" . number_format($row['avg_deviation'], 2) . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='4' class='text-center'>No user data available</td></tr>";
                                        }
                                    } catch (PDOException $e) {
                                        echo "<tr><td colspan='4' class='text-center text-danger'>Error loading data: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            <?php
            $stmt = $pdo->query("SELECT TaskStatus, COUNT(*) as count FROM WorkDiary GROUP BY TaskStatus");
            $taskStatusLabels = [];
            $taskStatusData = [];
            while ($row = $stmt->fetch()) {
                $taskStatusLabels[] = $row['TaskStatus'];
                $taskStatusData[] = $row['count'];
            }
            ?>
            const taskStatusCtx = document.getElementById('taskStatusChart').getContext('2d');
            const taskStatusChart = new Chart(taskStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($taskStatusLabels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($taskStatusData); ?>,
                        backgroundColor: [
                            '#FF9800', // Not Started (Orange)
                            '#4CC9F0', // In Progress (Light Blue)
                            '#4CAF50', // Completed (Green)
                            '#F44336'  // Blocked (Red)
                        ],
                        borderColor: ['#ffffff', '#ffffff', '#ffffff'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
            
            // Weekly Activity Chart
            <?php
            $stmt = $pdo->query("SELECT DATE(EntryDate) as date, COUNT(*) as count 
                                FROM WorkDiary 
                                WHERE EntryDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
                                GROUP BY DATE(EntryDate)");
            $diaryDates = [];
            $diaryCounts = [];
            while ($row = $stmt->fetch()) {
                $diaryDates[] = date('D', strtotime($row['date']));
                $diaryCounts[date('Y-m-d', strtotime($row['date']))] = $row['count'];
            }

            $stmt = $pdo->query("SELECT DATE(ObservationDate) as date, COUNT(*) as count 
                                FROM SATeamObservations 
                                WHERE ObservationDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
                                GROUP BY DATE(ObservationDate)");
            $obsDates = [];
            $obsCounts = [];
            while ($row = $stmt->fetch()) {
                $obsDates[] = date('D', strtotime($row['date']));
                $obsCounts[date('Y-m-d', strtotime($row['date']))] = $row['count'];
            }

            $allDates = [];
            $diaryData = [];
            $obsData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $allDates[] = date('D', strtotime($date));
                $diaryData[] = isset($diaryCounts[$date]) ? $diaryCounts[$date] : 0;
                $obsData[] = isset($obsCounts[$date]) ? $obsCounts[$date] : 0;
            }
            ?>
            const activityCtx = document.getElementById('activityChart').getContext('2d');
            const activityChart = new Chart(activityCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($allDates); ?>,
                    datasets: [{
                        label: 'Entries',
                        data: <?php echo json_encode($diaryData); ?>,
                        backgroundColor: '#4361EE',
                        borderRadius: 5
                    }, {
                        label: 'Observations',
                        data: <?php echo json_encode($obsData); ?>,
                        backgroundColor: '#F72585',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } },
                    plugins: { legend: { position: 'bottom' } }
                }
            });

            // Task Priority Distribution Chart
            <?php
            $stmt = $pdo->query("SELECT Priority, COUNT(*) as count FROM ProjectTasks GROUP BY Priority");
            $priorityLabels = [];
            $priorityData = [];
            while ($row = $stmt->fetch()) {
                $priorityLabels[] = $row['Priority'];
                $priorityData[] = $row['count'];
            }
            ?>
            const priorityCtx = document.getElementById('priorityChart').getContext('2d');
            const priorityChart = new Chart(priorityCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($priorityLabels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($priorityData); ?>,
                        backgroundColor: ['#4CAF50', '#FF9800', '#F44336'], // Low, Medium, High
                        borderColor: ['#ffffff', '#ffffff', '#ffffff'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });

            // Auto-refresh page every 5 minutes (300,000 milliseconds)
            setTimeout(function() {
                window.location.reload();
            }, 300000);
        </script>
</body>
</html>
