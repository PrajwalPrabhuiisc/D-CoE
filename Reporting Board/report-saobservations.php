<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Diary Leaderboard</title>
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
        
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }
        
        .table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table thead th {
            background-color: var(--light-color);
            font-weight: 600;
            color: var(--dark-color);
            border-top: none;
            padding: 12px 15px;
            position: sticky;
            top: 0;
            z-index: 1;
            background-clip: padding-box;
        }
        
        .table tbody tr {
            transition: background-color 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
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
        
        .pagination-container {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <h1 class="page-title">Work Diary Leaderboard</h1>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-trophy"></i> Top Contributors
                    </div>
                    <div class="card-body p-0">
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
                                    $itemsPerPage = 20;
                                    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                                    $offset = ($page - 1) * $itemsPerPage;

                                    // Calculate the date 30 days ago from current date
                                    $startDate = date('Y-m-d', strtotime('-30 days'));

                                    // Query to get leaderboard data with rank
                                    $sql = "
                                        SELECT 
                                            u.Username,
                                            COUNT(w.EntryID) as total_entries,
                                            ROUND(
                                                (SUM(CASE 
                                                    WHEN DATE(w.CreatedAt) = w.EntryDate 
                                                    THEN 1 ELSE 0 
                                                END) / COUNT(w.EntryID)) * 100, 
                                                2
                                            ) as timeliness_percentage,
                                            @rank := @rank + 1 as rank
                                        FROM workdiary w
                                        JOIN users u ON w.UserID = u.UserID
                                        CROSS JOIN (SELECT @rank := :offset) as init
                                        WHERE w.EntryDate >= :startDate
                                        GROUP BY u.UserID, u.Username
                                        ORDER BY total_entries DESC, timeliness_percentage DESC
                                        LIMIT :limit OFFSET :offset
                                    ";

                                    // Count total users for pagination
                                    $countSql = "
                                        SELECT COUNT(DISTINCT w.UserID)
                                        FROM workdiary w
                                        WHERE w.EntryDate >= :startDate
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
                                        echo "<tr><td colspan='4' class='no-results'>No work diary entries found for the last 30 days.</td></tr>";
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-toggle to next page (report-tasks.php) after 2 minutes (120,000 milliseconds)
        setTimeout(function() {
            window.location.href = 'report-tasks.php';
        }, 120000);
    </script>
</body>
</html>
