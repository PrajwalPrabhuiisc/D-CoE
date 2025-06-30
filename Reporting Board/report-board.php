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
        
        .carousel-item canvas {
            margin: 0 auto;
            max-height: 350px;
            width: 100%;
        }
        
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .table {
            margin-bottom: 0;
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
        }
        
        .table td {
            vertical-align: middle;
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
        
        /* Leaderboard-specific styles */
        .leaderboard-wrapper {
            background: var(--light-color);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .leaderboard-header {
            background: var(--primary-color);
            padding: 1rem 1.5rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-title i {
            background: rgba(255, 255, 255, 0.2);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .time-period {
            background: rgba(255, 255, 255, 0.15);
            padding: 0.3rem 0.8rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .info-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .info-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .leaderboard-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .leaderboard-table th {
            background: var(--light-color);
            font-weight: 600;
            color: var(--dark-color);
            padding: 0.8rem 1rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .leaderboard-table tbody tr {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        .leaderboard-table tbody tr:hover {
            transform: translateY(-3px);
        }

        .leaderboard-table td {
            padding: 0.8rem 1rem;
            vertical-align: middle;
        }

        .rank {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .rank-1 { background: #ffca28; color: #4a2c0b; }
        .rank-2 { background: #b0bec5; color: #263238; }
        .rank-3 { background: #ff8f00; color: white; }
        .rank-other { background: #eceff1; color: #546e7a; }

        .username {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-weight: 500;
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }

        .metric-container {
            width: 120px;
        }

        .metric-value {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.2rem;
            font-size: 0.9rem;
        }

        .progress-bar {
            height: 6px;
            background: #eceff1;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            transition: width 0.6s ease-in-out;
        }

        .progress-task { background: var(--success-color); }
        .progress-time { background: var(--primary-color); }
        .progress-consistency { background: var(--accent-color); }

        .total-score {
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .score-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .no-data {
            padding: 2rem;
            text-align: center;
            color: #546e7a;
            font-size: 1rem;
        }

        .no-data i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .modal-content {
            border-radius: 10px;
            background: white;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-bottom: none;
            border-radius: 10px 10px 0 0;
        }

        .modal-title {
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .modal-body {
            padding: 1.2rem;
            color: var(--dark-color);
        }

        .calculation-list {
            list-style: none;
            padding: 0;
        }

        .calculation-list li {
            margin-bottom: 0.8rem;
            padding: 0.8rem;
            background: #f8f9fa;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .calculation-list li:hover {
            background: #eceff1;
            transform: translateX(3px);
        }

        .calculation-list strong {
            color: var(--primary-color);
        }

        .pagination-container {
            margin-top: 15px;
            display: flex;
            justify-content: center;
        }

        .pagination {
            gap: 5px;
        }

        .page-item .page-link {
            border-radius: 6px;
            color: var(--primary-color);
            background: white;
            border: 1px solid rgba(67, 97, 238, 0.2);
            transition: all 0.3s ease;
        }

        .page-item.active .page-link {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .page-item.disabled .page-link {
            color: #546e7a;
            background: #f8f9fa;
            border-color: rgba(67, 97, 238, 0.1);
        }

        .page-item .page-link:hover {
            background: var(--accent-color);
            color: white;
            border-color: var(--accent-color);
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
            .carousel-item canvas {
                max-height: 300px;
            }
            .leaderboard-table { display: block; }
            .leaderboard-table thead { display: none; }
            .leaderboard-table tbody tr {
                display: block;
                margin-bottom: 0.8rem;
                padding: 0.8rem;
            }
            .leaderboard-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem 0.8rem;
                border-bottom: 1px solid rgba(0,0,0,0.05);
            }
            .leaderboard-table td:last-child { border-bottom: none; }
            .leaderboard-table td::before {
                content: attr(data-label);
                font-weight: 500;
                color: var(--dark-color);
                margin-right: 0.8rem;
            }
            .metric-container { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <h1 class="page-title">SA Digital Dashboard</h1>
        
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
                                <!-- Performance Leaderboard Slide -->
                                <div class="carousel-item">
                                    <h5 class="text-center mb-3">Performance Leaderboard</h5>
                                    <div class="leaderboard-wrapper">
                                        <div class="leaderboard-header">
                                            <h2 class="header-title">
                                                <i class="fas fa-trophy"></i>
                                                Top Performers
                                            </h2>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="time-period">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    Last 30 Days
                                                </div>
                                                <button class="info-btn" data-bs-toggle="modal" data-bs-target="#calcInfoModal">
                                                    <i class="fas fa-info"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="leaderboard-table">
                                                <thead>
                                                    <tr>
                                                        <th>Rank</th>
                                                        <th>Team Member</th>
                                                        <th>Task Completion</th>
                                                        <th>Time Efficiency</th>
                                                        <th>Consistency</th>
                                                        <th>Total Score</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    try {
                                                        $itemsPerPage = 5;
                                                        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                                                        $offset = ($page - 1) * $itemsPerPage;
                                                        $startDate = date('Y-m-d', strtotime('-30 days'));

                                                        $sql = "
                                                            SELECT 
                                                                u.Username,
                                                                COALESCE(
                                                                    ROUND(COUNT(CASE WHEN w.TaskStatus = 'Completed' THEN 1 END) * 100.0 / NULLIF(COUNT(w.EntryID), 0), 2),
                                                                    0
                                                                ) AS TaskCompletionScore,
                                                                COALESCE(
                                                                    CASE 
                                                                        WHEN AVG(w.ActualTime - w.AllocatedTime) <= 0 THEN 100
                                                                        ELSE GREATEST(100 - (AVG(w.ActualTime - w.AllocatedTime) * 10), 0)
                                                                    END,
                                                                    100
                                                                ) AS TimeEfficiencyScore,
                                                                COALESCE(
                                                                    ROUND(
                                                                        (SUM(CASE WHEN DATE(w.CreatedAt) = w.EntryDate THEN 1 ELSE 0 END) / 
                                                                        NULLIF(COUNT(w.EntryID), 0)) * 100, 
                                                                        2
                                                                    ),
                                                                    0
                                                                ) AS SubmissionConsistencyScore,
                                                                ROUND(
                                                                    (COALESCE(
                                                                        ROUND(COUNT(CASE WHEN w.TaskStatus = 'Completed' THEN 1 END) * 100.0 / NULLIF(COUNT(w.EntryID), 0), 2),
                                                                        0
                                                                    ) * 0.4) +
                                                                    (COALESCE(
                                                                        CASE 
                                                                            WHEN AVG(w.ActualTime - w.AllocatedTime) <= 0 THEN 100
                                                                            ELSE GREATEST(100 - (AVG(w.ActualTime - w.AllocatedTime) * 10), 0)
                                                                        END,
                                                                        100
                                                                    ) * 0.3) +
                                                                    (COALESCE(
                                                                        ROUND(
                                                                            (SUM(CASE WHEN DATE(w.CreatedAt) = w.EntryDate THEN 1 ELSE 0 END) / 
                                                                            NULLIF(COUNT(w.EntryID), 0)) * 100, 
                                                                            2
                                                                        ),
                                                                        0
                                                                    ) * 0.3),
                                                                    2
                                                                ) AS TotalScore,
                                                                @rank := @rank + 1 as rank
                                                            FROM workdiary w
                                                            JOIN users u ON w.UserID = u.UserID
                                                            CROSS JOIN (SELECT @rank := :offset) as init
                                                            WHERE w.EntryDate >= :startDate AND u.Role = 'Team Member'
                                                            GROUP BY u.UserID, u.Username
                                                            ORDER BY TotalScore DESC
                                                            LIMIT :limit OFFSET :offset
                                                        ";

                                                        $countSql = "
                                                            SELECT COUNT(DISTINCT w.UserID)
                                                            FROM workdiary w
                                                            JOIN users u ON w.UserID = u.UserID
                                                            WHERE w.EntryDate >= :startDate AND u.Role = 'Team Member'
                                                        ";
                                                        $countStmt = $pdo->prepare($countSql);
                                                        $countStmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
                                                        $countStmt->execute();
                                                        $totalUsers = $countStmt->fetchColumn();
                                                        $totalPages = ceil($totalUsers / $itemsPerPage);

                                                        $stmt = $pdo->prepare($sql);
                                                        $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
                                                        $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
                                                        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                                                        $stmt->execute();

                                                        if ($stmt->rowCount() == 0) {
                                                            echo '<tr><td colspan="6" class="no-data"><i class="fas fa-chart-line"></i>No performance data available</td></tr>';
                                                        } else {
                                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                $rankClass = $row['rank'] == 1 ? 'rank-1' : ($row['rank'] == 2 ? 'rank-2' : ($row['rank'] == 3 ? 'rank-3' : 'rank-other'));
                                                                $initial = strtoupper(substr($row['Username'], 0, 1));
                                                                $taskScore = number_format($row['TaskCompletionScore'], 1);
                                                                $timeScore = number_format($row['TimeEfficiencyScore'], 1);
                                                                $consistencyScore = number_format($row['SubmissionConsistencyScore'], 1);
                                                                $totalScore = number_format($row['TotalScore'], 1);

                                                                echo "<tr>";
                                                                echo "<td data-label='Rank'><div class='rank $rankClass'>" . htmlspecialchars($row['rank']) . "</div></td>";
                                                                echo "<td data-label='Team Member'><div class='username'><div class='avatar'>$initial</div>" . htmlspecialchars($row['Username']) . "</div></td>";
                                                                echo "<td data-label='Task Completion'>";
                                                                echo "<div class='metric-container'>";
                                                                echo "<div class='metric-value'>$taskScore%</div>";
                                                                echo "<div class='progress-bar'><div class='progress-fill progress-task' style='width: $taskScore%'></div></div>";
                                                                echo "</div></td>";
                                                                echo "<td data-label='Time Efficiency'>";
                                                                echo "<div class='metric-container'>";
                                                                echo "<div class='metric-value'>$timeScore%</div>";
                                                                echo "<div class='progress-bar'><div class='progress-fill progress-time' style='width: $timeScore%'></div></div>";
                                                                echo "</div></td>";
                                                                echo "<td data-label='Consistency'>";
                                                                echo "<div class='metric-container'>";
                                                                echo "<div class='metric-value'>$consistencyScore%</div>";
                                                                echo "<div class='progress-bar'><div class='progress-fill progress-consistency' style='width: $consistencyScore%'></div></div>";
                                                                echo "</div></td>";
                                                                echo "<td data-label='Total Score'>";
                                                                echo "<div class='total-score'>";
                                                                echo "<div class='score-circle'>$totalScore</div>";
                                                                echo "</div></td>";
                                                                echo "</tr>";
                                                            }
                                                        }
                                                    } catch (PDOException $e) {
                                                        echo "<tr><td colspan='6' class='no-data'><i class='fas fa-exclamation-triangle'></i>Database Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php if ($totalPages > 1): ?>
                                        <div class="pagination-container">
                                            <nav>
                                                <ul class="pagination">
                                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                                    </li>
                                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                                        </li>
                                                    <?php endfor; ?>
                                                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                        <?php endif; ?>
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
                                <!-- Task Analytics Slide -->
                                <div class="carousel-item">
                                    <h5 class="text-center mb-3">Task Analytics</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <canvas id="taskStatusChart" height="350"></canvas>
                                        </div>
                                        <div class="col-md-4">
                                            <canvas id="completionChart" height="350"></canvas>
                                        </div>
                                        <div class="col-md-4">
                                            <canvas id="deviationChart" height="350"></canvas>
                                        </div>
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

        <!-- Calculation Info Modal -->
        <div class="modal fade" id="calcInfoModal" tabindex="-1" aria-labelledby="calcInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="calcInfoModalLabel">
                            <i class="fas fa-calculator"></i> How Scores Are Calculated
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>The leaderboard ranks Team Members based on performance over the last 30 days. Here’s how the total score (out of 100) is calculated:</p>
                        <ul class="calculation-list">
                            <li>
                                <strong>Task Completion (40%):</strong><br>
                                Percentage of tasks marked as "Completed" in the Work Diary.<br>
                                <em>Formula:</em> (Completed Tasks / Total Tasks) × 100 × 0.4<br>
                                <em>Max Points:</em> 40
                            </li>
                            <li>
                                <strong>Time Efficiency (30%):</strong><br>
                                Score based on how closely actual time matches allocated time.<br>
                                <em>Formula:</em> If Actual ≤ Allocated: 100; Else: 100 - (Avg Deviation × 10), min 0 × 0.3<br>
                                <em>Max Points:</em> 30
                            </li>
                            <li>
                                <strong>Consistency (30%):</strong><br>
                                Percentage of diary entries submitted on the same day as the entry date.<br>
                                <em>Formula:</em> (Timely Entries / Total Entries) × 100 × 0.3<br>
                                <em>Max Points:</em> 30
                            </li>
                            <li>
                                <strong>Total Score:</strong><br>
                                Sum of weighted scores from above.<br>
                                <em>Formula:</em> Task Completion + Time Efficiency + Consistency<br>
                                <em>Max Total:</em> 100
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Task Status Distribution Chart
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
            type: 'pie',
            data: {
                labels: <?php echo json_encode($taskStatusLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($taskStatusData); ?>,
                    backgroundColor: ['#FF9800', '#4CC9F0', '#4CAF50', '#F44336'],
                    borderColor: ['#ffffff', '#ffffff', '#ffffff', '#ffffff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Task Status Distribution', font: { size: 14 } }
                }
            }
        });

        // Task Completion Over Time Chart
        <?php
        $stmt = $pdo->query("SELECT DATE(EntryDate) as date, COUNT(*) as count 
                            FROM WorkDiary 
                            WHERE TaskStatus = 'Completed' 
                            AND EntryDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
                            GROUP BY DATE(EntryDate)");
        $completionCounts = [];
        while ($row = $stmt->fetch()) {
            $completionCounts[date('Y-m-d', strtotime($row['date']))] = $row['count'];
        }
        $allDates = [];
        $completionData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $allDates[] = date('D', strtotime($date));
            $completionData[] = isset($completionCounts[$date]) ? $completionCounts[$date] : 0;
        }
        ?>
        const completionCtx = document.getElementById('completionChart').getContext('2d');
        const completionChart = new Chart(completionCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($allDates); ?>,
                datasets: [{
                    label: 'Completed Tasks',
                    data: <?php echo json_encode($completionData); ?>,
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.2)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Tasks Completed' } },
                    x: { title: { display: true, text: 'Day' } }
                },
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Task Completions (Last 7 Days)', font: { size: 14 } }
                }
            }
        });

        // Average Time Deviation by User Chart
        <?php
        $stmt = $pdo->query("SELECT 
            u.Username,
            ROUND(COALESCE(AVG(w.ActualTime - w.AllocatedTime), 0), 2) AS avg_deviation
            FROM Users u
            LEFT JOIN WorkDiary w ON w.UserID = u.UserID
            WHERE u.Role NOT IN ('SA Team', 'Externals', 'FSID', 'Faculties', 'Smart Factory')
            GROUP BY u.UserID, u.Username
            HAVING COUNT(w.ActualTime) > 0
            ORDER BY avg_deviation DESC
            LIMIT 10");
        $deviationLabels = [];
        $deviationData = [];
        while ($row = $stmt->fetch()) {
            $deviationLabels[] = $row['Username'];
            $deviationData[] = $row['avg_deviation'];
        }
        ?>
        const deviationCtx = document.getElementById('deviationChart').getContext('2d');
        const deviationChart = new Chart(deviationCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($deviationLabels); ?>,
                datasets: [{
                    label: 'Avg Time Deviation (Hours)',
                    data: <?php echo json_encode($deviationData); ?>,
                    backgroundColor: '#4361EE',
                    borderColor: '#4361EE',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { title: { display: true, text: 'Hours' } },
                    x: { title: { display: true, text: 'User' } }
                },
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Avg Time Deviation by User', font: { size: 14 } }
                }
            }
        });

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

        // Progress Bar Animation for Leaderboard
        document.addEventListener('DOMContentLoaded', () => {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => bar.style.width = width, 100);
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
