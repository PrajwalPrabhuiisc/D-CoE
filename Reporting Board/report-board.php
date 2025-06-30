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
            margin: 0;
            padding-top: 20px;
        }
        
        .dashboard-container {
            padding: 20px 0;
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
        
        .slideshow-container {
            max-height: 600px;
        }
        
        .carousel-item {
            padding: 20px;
        }
        
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table th {
            background-color: var(--light-color);
            font-weight: 600;
            color: var(--dark-color);
            border-top: none;
            position: sticky;
            top: 0;
            z-index: 1;
            background-clip: padding-box;
            padding: 12px 15px;
        }
        
        .table td {
            vertical-align: middle;
            padding: 12px 15px;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .table td:hover {
            white-space: normal;
            overflow: visible;
            text-overflow: unset;
        }
        
        .table tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .table tbody tr:nth-child(even) {
            background-color: #ffffff;
        }
        
        .table tbody tr:nth-child(odd) {
            background-color: #f9fbfd;
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
        
        /* Kanban Board Styles */
        .kanban-board {
            display: flex;
            overflow-x: auto;
            gap: 15px;
            max-height: 400px;
        }
        
        .kanban-column {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            min-width: 250px;
            max-width: 250px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow-y: auto;
        }
        
        .kanban-column h6 {
            margin: 0;
            padding-bottom: 5px;
            border-bottom: 2px solid var(--primary-color);
            text-align: center;
            font-weight: 600;
        }
        
        .kanban-card {
            background-color: white;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .kanban-card:hover {
            transform: translateY(-2px);
        }
        
        .kanban-card.dragging {
            opacity: 0.5;
        }
        
        .kanban-card .task-title {
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .kanban-card .task-user {
            font-size: 0.8rem;
            color: #666;
        }
        
        .kanban-card .task-date {
            font-size: 0.7rem;
            color: #888;
        }
        
        @media (max-width: 768px) {
            .carousel-item {
                padding: 10px;
            }
            .table-responsive {
                overflow-x: auto;
            }
            .kanban-board {
                flex-direction: column;
                overflow-x: visible;
            }
            .kanban-column {
                min-width: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <h1 class="page-title">SA Digital Dashboard</h1>
        
        <!-- Slideshow -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-sliders-h"></i> Dashboard Overview
                    </div>
                    <div class="card-body slideshow-container">
                        <div id="dashboardCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="10000">
                            <div class="carousel-inner">
                                <!-- Recent Diary Entries (Kanban Board) Slide -->
                                <div class="carousel-item active">
                                    <h5 class="text-center mb-3">Recent Diary Entries</h5>
                                    <div class="kanban-board">
                                        <?php
                                        $stmt = $pdo->query("SELECT w.TaskDescription, w.TaskStatus, u.Username, w.EntryDate 
                                                            FROM WorkDiary w 
                                                            JOIN Users u ON w.UserID = u.UserID 
                                                            ORDER BY w.EntryDate DESC LIMIT 10");
                                        $tasksByStatus = [
                                            'Not Started' => [],
                                            'In Progress' => [],
                                            'Completed' => [],
                                            'Blocked' => []
                                        ];
                                        while ($row = $stmt->fetch()) {
                                            $status = $row['TaskStatus'];
                                            if (array_key_exists($status, $tasksByStatus)) {
                                                $tasksByStatus[$status][] = $row;
                                            }
                                        }
                                        foreach ($tasksByStatus as $status => $tasks) {
                                            echo "<div class='kanban-column'>";
                                            echo "<h6>" . htmlspecialchars($status) . "</h6>";
                                            $count = 0;
                                            foreach ($tasks as $task) {
                                                if ($count >= 5) break;
                                                $statusClass = '';
                                                switch (strtolower($status)) {
                                                    case 'in progress':
                                                        $statusClass = 'status-active';
                                                        break;
                                                    case 'completed':
                                                        $statusClass = 'status-completed';
                                                        break;
                                                    case 'not started':
                                                    case 'blocked':
                                                        $statusClass = 'status-pending';
                                                        break;
                                                }
                                                echo "<div class='kanban-card' draggable='true'>";
                                                echo "<div class='task-title'>" . htmlspecialchars($task['TaskDescription']) . "</div>";
                                                echo "<div class='task-user'><i class='fas fa-user me-1'></i>" . htmlspecialchars($task['Username']) . "</div>";
                                                echo "<div class='task-date'>" . htmlspecialchars($task['EntryDate']) . "</div>";
                                                echo "<span class='status-badge $statusClass'>" . htmlspecialchars($status) . "</span>";
                                                echo "</div>";
                                                $count++;
                                            }
                                            echo "</div>";
                                        }
                                        ?>
                                    </div>
                                    <div class="view-all">
                                        <a href="report-diaryentries.php">View all entries <i class="fas fa-arrow-right ms-1"></i></a>
                                    </div>
                                </div>
                                <!-- Active Tasks Slide -->
                                <div class="carousel-item">
                                    <h5 class="text-center mb-3">Active Tasks</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Task Description</th>
                                                    <th>Status</th>
                                                    <th>User</th>
                                                    <th>Entry Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $stmt = $pdo->query("SELECT w.TaskDescription, w.TaskStatus, u.Username, w.EntryDate 
                                                                    FROM WorkDiary w 
                                                                    JOIN Users u ON w.UserID = u.UserID 
                                                                    WHERE w.TaskStatus = 'In Progress' 
                                                                    ORDER BY w.EntryDate DESC LIMIT 5");
                                                while ($row = $stmt->fetch()) {
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($row['TaskDescription']) . "</td>";
                                                    echo "<td><span class='status-badge status-active'>" . htmlspecialchars($row['TaskStatus']) . "</span></td>";
                                                    echo "<td><i class='fas fa-user me-1'></i>" . htmlspecialchars($row['Username']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['EntryDate']) . "</td>";
                                                    echo "</tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Top Contributors Slide -->
                                <div class="carousel-item">
                                    <h5 class="text-center mb-3">Top Contributors</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Rank</th>
                                                    <th>Username</th>
                                                    <th>Total Entries</th>
                                                    <th>Timeliness (%)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $startDate = date('Y-m-d', strtotime('-30 days'));
                                                $sql = "
                                                    SELECT 
                                                        u.Username,
                                                        COUNT(w.EntryID) as total_entries,
                                                        COALESCE(
                                                            ROUND(
                                                                (SUM(CASE WHEN DATE(w.CreatedAt) = DATE(w.EntryDate) THEN 1 ELSE 0 END) / 
                                                                NULLIF(COUNT(w.EntryID), 0)) * 100, 
                                                                2
                                                            ),
                                                            0
                                                        ) as timeliness_percentage,
                                                        @rank := @rank + 1 as rank
                                                    FROM workdiary w
                                                    JOIN users u ON w.UserID = u.UserID
                                                    CROSS JOIN (SELECT @rank := 0) as init
                                                    WHERE w.EntryDate >= :startDate
                                                    GROUP BY u.UserID, u.Username
                                                    ORDER BY total_entries DESC, timeliness_percentage DESC
                                                    LIMIT 5
                                                ";
                                                $stmt = $pdo->prepare($sql);
                                                $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
                                                $stmt->execute();
                                                if ($stmt->rowCount() > 0) {
                                                    while ($row = $stmt->fetch()) {
                                                        echo "<tr>";
                                                        echo "<td>#" . htmlspecialchars($row['rank']) . "</td>";
                                                        echo "<td><i class='fas fa-user me-1'></i>" . htmlspecialchars($row['Username']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['total_entries']) . "</td>";
                                                        echo "<td><span class='status-badge status-active'>" . htmlspecialchars($row['timeliness_percentage']) . "%</span></td>";
                                                        echo "</tr>";
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='4' class='text-center'>No contributor data available</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="view-all">
                                        <a href="report-saobservations.php">View all contributors <i class="fas fa-arrow-right ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#dashboardCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#dashboardCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
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
                                            COALESCE(SUM(CASE WHEN w.AllocatedTime IS NOT NULL THEN w.AllocatedTime ELSE 0 END), 0) AS total_allocated,
                                            COALESCE(SUM(CASE WHEN w.ActualTime IS NOT NULL THEN w.ActualTime ELSE 0 END), 0) AS total_actual,
                                            COALESCE(ROUND(AVG(CASE WHEN w.ActualTime IS NOT NULL AND w.AllocatedTime IS NOT NULL THEN w.ActualTime - w.AllocatedTime END), 2), 0) AS avg_deviation
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Kanban Drag-and-Drop (read-only)
        document.querySelectorAll('.kanban-card').forEach(card => {
            card.addEventListener('dragstart', () => {
                card.classList.add('dragging');
            });
            card.addEventListener('dragend', () => {
                card.classList.remove('dragging');
            });
        });

        document.querySelectorAll('.kanban-column').forEach(column => {
            column.addEventListener('dragover', e => {
                e.preventDefault();
            });
            column.addEventListener('drop', e => {
                e.preventDefault();
                const draggingCard = document.querySelector('.dragging');
                if (draggingCard) {
                    column.appendChild(draggingCard);
                }
            });
        });

        // Auto-refresh page every 5 minutes (300,000 milliseconds)
        setTimeout(function() {
            window.location.reload();
        }, 300000);

        // Auto-toggle to next page (report-diaryentries.php) after 2 minutes (120,000 milliseconds)
        setTimeout(function() {
            window.location.href = 'report-diaryentries.php';
        }, 120000);
    </script>
</body>
</html>
