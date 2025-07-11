<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Tasks</title>
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
            justify-content: space-between;
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
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .table tbody tr:nth-child(even) {
            background-color: #ffffff;
        }
        
        .table tbody tr:nth-child(odd) {
            background-color: #f9fbfd;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            display: inline-block;
        }
        
        .status-in-progress {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--accent-color);
        }
        
        .no-results {
            text-align: center;
            padding: 30px;
            color: #777;
            font-style: italic;
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
        <h1 class="page-title">Active Tasks</h1>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <i class="fas fa-tasks"></i> Active Tasks
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Task Description</th>
                                        <th>Status</th>
                                        <th>Owner</th>
                                        <th>Project</th>
                                        <th>Entry Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $itemsPerPage = 20;
                                    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                                    $offset = ($page - 1) * $itemsPerPage;

                                    $sql = "SELECT w.TaskDescription, w.TaskStatus, u.Username, p.ProjectName, w.EntryDate 
                                            FROM WorkDiary w 
                                            JOIN Users u ON w.UserID = u.UserID 
                                            LEFT JOIN ProjectTasks pt ON w.TaskID = pt.TaskID 
                                            LEFT JOIN Projects p ON pt.ProjectID = p.ProjectID 
                                            WHERE w.TaskStatus = 'In Progress'
                                            ORDER BY w.EntryDate DESC";

                                    $countSql = "SELECT COUNT(*) FROM WorkDiary w WHERE w.TaskStatus = 'In Progress'";
                                    $countStmt = $pdo->prepare($countSql);
                                    $countStmt->execute();
                                    $totalTasks = $countStmt->fetchColumn();
                                    $totalPages = ceil($totalTasks / $itemsPerPage);

                                    $sql .= " LIMIT :limit OFFSET :offset";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
                                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                                    $stmt->execute();

                                    if ($stmt->rowCount() > 0) {
                                        while ($row = $stmt->fetch()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['TaskDescription']) . "</td>";
                                            echo "<td><span class='status-badge status-in-progress'>" . htmlspecialchars($row['TaskStatus']) . "</span></td>";
                                            echo "<td><i class='fas fa-user me-1'></i>" . htmlspecialchars($row['Username']) . "</td>";
                                            echo "<td>" . (isset($row['ProjectName']) ? htmlspecialchars($row['ProjectName']) : 'N/A') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['EntryDate']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='no-results'>No in-progress tasks found.</td></tr>";
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
        // Auto-toggle to next page (report-board.php) after 2 minutes (120,000 milliseconds)
        setTimeout(function() {
            window.location.href = 'report-board.php';
        }, 120000);
    </script>
</body>
</html>
