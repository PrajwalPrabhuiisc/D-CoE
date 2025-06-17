<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Diary Entries</title>
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
        
        .filter-container {
            margin-bottom: 20px;
        }
        
        .filter-btn {
            margin-right: 8px;
            margin-bottom: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 20px;
            transition: all 0.3s ease;
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
        
        .pagination-container {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="report-board.php">
                <i class="fas fa-book-open me-2"></i>Diary System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="report-board.php">
                            <i class="fas fa-chart-bar me-1"></i> Report Board
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container dashboard-container">
        <h1 class="page-title">All Diary Entries</h1>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <i class="fas fa-book"></i> Diary Entries
                        </div>
                        <div>
                            <form id="filterForm" method="GET" class="d-inline">
                                <button type="button" class="btn btn-sm btn-outline-secondary filter-btn active" data-status="all">All</button>
                                <button type="button" class="btn btn-sm btn-outline-primary filter-btn" data-status="in progress">In Progress</button>
                                <button type="button" class="btn btn-sm btn-outline-success filter-btn" data-status="completed">Completed</button>
                                <button type="button" class="btn btn-sm btn-outline-warning filter-btn" data-status="not started">Not Started</button>
                                <button type="button" class="btn btn-sm btn-outline-danger filter-btn" data-status="blocked">Blocked</button>
                                <input type="hidden" name="status" id="statusFilter" value="">
                                <input type="hidden" name="page" id="pageFilter" value="1">
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Task Description</th>
                                        <th>Status</th>
                                        <th>User</th>
                                        <th>Allocated Time (Hours)</th>
                                        <th>Actual Time (Hours)</th>
                                        <th>Deviation Reason</th>
                                        <th>Commitments</th>
                                        <th>Entry Date</th>
                                    </tr>
                                </thead>
                                <tbody id="diaryTable">
                                    <?php
                                    // Pagination settings
                                    $entriesPerPage = 20;
                                    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                                    $offset = ($page - 1) * $entriesPerPage;
                                    
                                    // Get filter status from GET parameter
                                    $statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
                                    
                                    // Count total entries for pagination
                                    $countSql = "SELECT COUNT(*) as total 
                                                FROM WorkDiary w 
                                                JOIN Users u ON w.UserID = u.UserID";
                                    if ($statusFilter != 'all') {
                                        $countSql .= " WHERE LOWER(w.TaskStatus) = :status";
                                    }
                                    
                                    $countStmt = $pdo->prepare($countSql);
                                    if ($statusFilter != 'all') {
                                        $countStmt->bindParam(':status', $statusFilter, PDO::PARAM_STR);
                                    }
                                    $countStmt->execute();
                                    $totalEntries = $countStmt->fetchColumn();
                                    $totalPages = ceil($totalEntries / $entriesPerPage);
                                    
                                    // Prepare the main query
                                    $sql = "SELECT w.TaskDescription, w.TaskStatus, u.Username, 
                                            w.AllocatedTime, w.ActualTime, w.DeviationReason, 
                                            w.Commitments, w.EntryDate 
                                            FROM WorkDiary w 
                                            JOIN Users u ON w.UserID = u.UserID ";
                                    
                                    if ($statusFilter != 'all') {
                                        $sql .= "WHERE LOWER(w.TaskStatus) = :status ";
                                    }
                                    
                                    $sql .= "ORDER BY w.EntryDate DESC LIMIT :limit OFFSET :offset";
                                    
                                    $stmt = $pdo->prepare($sql);
                                    
                                    // Bind parameters
                                    if ($statusFilter != 'all') {
                                        $stmt->bindParam(':status', $statusFilter, PDO::PARAM_STR);
                                    }
                                    $stmt->bindParam(':limit', $entriesPerPage, PDO::PARAM_INT);
                                    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                                    
                                    $stmt->execute();
                                    $rowCount = 0;
                                    
                                    while ($row = $stmt->fetch()) {
                                        $rowCount++;
                                        $statusClass = "";
                                        switch (strtolower($row['TaskStatus'])) {
                                            case 'in progress':
                                                $statusClass = "status-active";
                                                break;
                                            case 'completed':
                                                $statusClass = "status-completed";
                                                break;
                                            case 'not started':
                                            case 'blocked':
                                                $statusClass = "status-pending";
                                                break;
                                        }
                                        echo "<tr>";
                                        echo "<td>{$row['TaskDescription']}</td>";
                                        echo "<td><span class='status-badge {$statusClass}'>{$row['TaskStatus']}</span></td>";
                                        echo "<td><i class='fas fa-user me-1'></i>{$row['Username']}</td>";
                                        echo "<td>" . ($row['AllocatedTime'] ?? 'N/A') . "</td>";
                                        echo "<td>" . ($row['ActualTime'] ?? 'N/A') . "</td>";
                                        echo "<td>" . ($row['DeviationReason'] ?? 'None') . "</td>";
                                        echo "<td>" . ($row['Commitments'] ?? 'None') . "</td>";
                                        echo "<td>{$row['EntryDate']}</td>";
                                        echo "</tr>";
                                    }
                                    
                                    if ($rowCount == 0) {
                                        echo "<tr><td colspan='8' class='no-results'>No entries found for the selected status.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if ($totalPages > 1): ?>
                        <div class="pagination-container">
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?status=<?= htmlspecialchars($statusFilter) ?>&page=<?= $page - 1 ?>" aria-label="Previous">
                                            <span aria-hidden="true">«</span>
                                        </a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?status=<?= htmlspecialchars($statusFilter) ?>&page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?status=<?= htmlspecialchars($statusFilter) ?>&page=<?= $page + 1 ?>" aria-label="Next">
                                            <span aria-hidden="true">»</span>
                                        </a>
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
            const pageFilterInput = document.getElementById('pageFilter');
            
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
                    pageFilterInput.value = 1; // Reset to page 1 when changing status
                    filterForm.submit();
                });
            });
        });
    </script>
</body>
</html>
