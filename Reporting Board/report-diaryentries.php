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
        
        .kanban-board {
            display: flex;
            overflow-x: auto;
            gap: 15px;
            max-height: 600px;
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
        
        .kanban-card .task-details {
            font-size: 0.7rem;
            color: #555;
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
        
        @media (max-width: 768px) {
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
        <h1 class="page-title">All Diary Entries</h1>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <i class="fas fa-book"></i> Diary Entries
                        </div>
                        <!-- <div class="filter-container">
                            <form id="filterForm" method="GET" class="d-inline">
                                <button type="button" class="btn btn-sm btn-outline-secondary filter-btn active" data-status="all">All</button>
                                <button type="button" class="btn btn-sm btn-outline-primary filter-btn" data-status="in progress">In Progress</button>
                                <button type="button" class="btn btn-sm btn-outline-success filter-btn" data-status="completed">Completed</button>
                                <button type="button" class="btn btn-sm btn-outline-warning filter-btn" data-status="not started">Not Started</button>
                                <button type="button" class="btn btn-sm btn-outline-danger filter-btn" data-status="blocked">Blocked</button>
                                <input type="hidden" name="status" id="statusFilter" value="">
                                <input type="hidden" name="page" id="pageFilter" value="1">
                            </form>
                        </div> -->
                    </div>
                    <div class="card-body p-0">
                        <div class="kanban-board">
                            <?php
                            $entriesPerPage = 20;
                            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                            $offset = ($page - 1) * $entriesPerPage;
                            
                            $statusFilter = isset($_GET['status']) ? strtolower($_GET['status']) : 'all';
                            
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
                            if ($statusFilter != 'all') {
                                $stmt->bindParam(':status', $statusFilter, PDO::PARAM_STR);
                            }
                            $stmt->bindParam(':limit', $entriesPerPage, PDO::PARAM_INT);
                            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                            $stmt->execute();
                            
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
                            
                            $rowCount = 0;
                            foreach ($tasksByStatus as $status => $tasks) {
                                if ($statusFilter != 'all' && strtolower($status) != $statusFilter) {
                                    continue; // Skip columns not matching the filter
                                }
                                $rowCount += count($tasks);
                                echo "<div class='kanban-column'>";
                                echo "<h6>" . htmlspecialchars($status) . "</h6>";
                                foreach ($tasks as $task) {
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
                                    echo "<div class='task-details'>Allocated: " . ($task['AllocatedTime'] ?? 'N/A') . "h</div>";
                                    echo "<div class='task-details'>Actual: " . ($task['ActualTime'] ?? 'N/A') . "h</div>";
                                    echo "<div class='task-details'>Reason: " . ($task['DeviationReason'] ?? 'None') . "</div>";
                                    echo "<div class='task-details'>Commitments: " . ($task['Commitments'] ?? 'None') . "</div>";
                                    echo "<span class='status-badge $statusClass'>" . htmlspecialchars($status) . "</span>";
                                    echo "</div>";
                                }
                                echo "</div>";
                            }
                            
                            if ($rowCount == 0) {
                                echo "<div class='no-results'>No entries found for the selected status.</div>";
                            }
                            ?>
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
                    pageFilterInput.value = 1;
                    filterForm.submit();
                });
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
        });

        // Auto-toggle to next page (report-saobservations.php) after 2 minutes (120,000 milliseconds)
        setTimeout(function() {
            window.location.href = 'report-saobservations.php';
        }, 120000);
    </script>
</body>
</html>
