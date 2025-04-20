<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Tasks</title>
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
        
        .filter-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            margin-right: 8px;
            margin-bottom: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 20px;
            transition: all 0.3s ease;
            border: 1px solid #ddd;
        }
        
        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .filter-btn.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .no-results {
            text-align: center;
            padding: 30px;
            color: #777;
            font-style: italic;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
            .filter-container {
                flex-direction: column;
            }
            .filter-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book-open me-2"></i>Diary System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="task_mapping.php">
                            <i class="fas fa-map me-1"></i> Task Mapping
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">
                            <i class="fas fa-chart-line me-1"></i> Analytics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kanban.php">
                            <i class="fas fa-columns me-1"></i> Kanban
                        </a>
                    </li>
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
        <h1 class="page-title">All Tasks</h1>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <i class="fas fa-tasks"></i> Tasks
                        </div>
                        <div class="filter-container">
                            <form id="filterForm" method="GET" class="d-inline">
                                <button type="button" class="btn btn-sm btn-outline-secondary filter-btn active" data-status="all">All</button>
                                <button type="button" class="btn btn-sm btn-outline-primary filter-btn" data-status="active">Active</button>
                                <button type="button" class="btn btn-sm btn-outline-success filter-btn" data-status="completed">Completed</button>
                                <button type="button" class="btn btn-sm btn-outline-warning filter-btn" data-status="pending">Pending</button>
                                <input type="hidden" name="status" id="statusFilter" value="">
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Task Name</th>
                                        <th>Status</th>
                                        <th>Owner</th>
                                        <th>Project</th>
                                        <th>Start Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $itemsPerPage = 20;
                                    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                                    $offset = ($page - 1) * $itemsPerPage;

                                    $statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

                                    $sql = "SELECT p.TaskName, p.Status, u.Username, pr.ProjectName, p.StartDate 
                                            FROM ProjectTasks p 
                                            JOIN Users u ON p.OwnerID = u.UserID 
                                            JOIN Projects pr ON p.ProjectID = pr.ProjectID 
                                            WHERE 1=1";

                                    $params = [];
                                    if ($statusFilter != 'all') {
                                        $sql .= " AND LOWER(p.Status) = :status";
                                        $params[':status'] = strtolower($statusFilter);
                                    }

                                    $sql .= " ORDER BY p.StartDate DESC";
                                    
                                    $countSql = "SELECT COUNT(*) FROM ProjectTasks p WHERE 1=1";
                                    if ($statusFilter != 'all') {
                                        $countSql .= " AND LOWER(p.Status) = :status";
                                    }
                                    $countStmt = $pdo->prepare($countSql);
                                    $countStmt->execute($params);
                                    $totalTasks = $countStmt->fetchColumn();
                                    $totalPages = ceil($totalTasks / $itemsPerPage);

                                    $sql .= " LIMIT :limit OFFSET :offset";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
                                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                                    foreach ($params as $key => $value) {
                                        $stmt->bindValue($key, $value, PDO::PARAM_STR);
                                    }
                                    $stmt->execute();
                                    $rowCount = 0;

                                    if ($stmt->rowCount() > 0) {
                                        while ($row = $stmt->fetch()) {
                                            $rowCount++;
                                            $statusClass = "";
                                            switch (strtolower($row['Status'])) {
                                                case 'active':
                                                    $statusClass = "status-active";
                                                    break;
                                                case 'completed':
                                                    $statusClass = "status-completed";
                                                    break;
                                                default:
                                                    $statusClass = "status-pending";
                                            }
                                            echo "<tr>";
                                            echo "<td>{$row['TaskName']}</td>";
                                            echo "<td><span class='status-badge {$statusClass}'>{$row['Status']}</span></td>";
                                            echo "<td><i class='fas fa-user me-1'></i>{$row['Username']}</td>";
                                            echo "<td>{$row['ProjectName']}</td>";
                                            echo "<td>{$row['StartDate']}</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='no-results'>No tasks found for the selected status.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($totalPages > 1): ?>
                        <div class="pagination-container d-flex justify-content-center mt-3">
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo urlencode($statusFilter); ?>">Previous</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo urlencode($statusFilter); ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo urlencode($statusFilter); ?>">Next</a>
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
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const filterForm = document.getElementById('filterForm');
            const statusFilterInput = document.getElementById('statusFilter');

            const urlParams = new URLSearchParams(window.location.search);
            const currentStatus = urlParams.get('status') || 'all';

            filterButtons.forEach(button => {
                if (button.dataset.status === currentStatus) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            });

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    statusFilterInput.value = this.dataset.status;
                    filterForm.submit();
                });
            });
        });
    </script>
</body>
</html>
