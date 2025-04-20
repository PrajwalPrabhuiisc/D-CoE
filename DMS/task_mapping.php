<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Mapping - Diary System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Cytoscape.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cytoscape/3.23.0/cytoscape.min.js"></script>
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
        body { font-family: 'Poppins', sans-serif; background-color: #f5f7fa; color: #333; }
        .navbar { background-color: white !important; box-shadow: 0 2px 15px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; color: var(--primary-color) !important; font-size: 1.5rem; }
        .nav-link { font-weight: 500; color: #555 !important; margin: 0 5px; transition: all 0.3s ease; }
        .nav-link:hover { color: var(--primary-color) !important; transform: translateY(-2px); }
        .nav-link.active { color: var(--primary-color) !important; border-bottom: 3px solid var(--primary-color); }
        .page-container { padding: 30px 0; }
        .page-title { font-weight: 700; color: var(--dark-color); margin-bottom: 30px; border-left: 5px solid var(--primary-color); padding-left: 15px; }
        .card { border-radius: 10px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: transform 0.3s ease; margin-bottom: 25px; overflow: hidden; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .card-header { background-color: white; border-bottom: 1px solid rgba(0,0,0,0.05); font-weight: 600; font-size: 1.1rem; padding: 15px 20px; display: flex; align-items: center; }
        .card-header i { margin-right: 10px; color: var(--primary-color); }
        .card-body { padding: 20px; }
        .table-container { max-height: 500px; overflow-y: auto; }
        .table { width: 100%; margin-bottom: 0; }
        .table thead th { font-weight: 600; color: #444; border-bottom: 2px solid rgba(0,0,0,0.05); padding: 12px 15px; position: sticky; top: 0; background-color: #fff; z-index: 1; }
        .table tbody tr { transition: all 0.2s ease; }
        .table tbody tr:hover { background-color: rgba(67, 97, 238, 0.05); }
        .table tbody td { padding: 12px 15px; vertical-align: middle; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .status-badge { padding: 5px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 500; text-transform: uppercase; display: inline-block; }
        .status-active { background-color: rgba(76, 201, 240, 0.1); color: var(--accent-color); }
        .status-completed { background-color: rgba(76, 175, 80, 0.1); color: var(--success-color); }
        .status-pending { background-color: rgba(255, 152, 0, 0.1); color: var(--warning-color); }
        .priority-high { background-color: rgba(244, 67, 54, 0.1); color: var(--danger-color); }
        .priority-medium { background-color: rgba(255, 152, 0, 0.1); color: var(--warning-color); }
        .priority-low { background-color: rgba(76, 201, 240, 0.1); color: var(--accent-color); }
        .quick-actions { display: flex; gap: 15px; margin-bottom: 30px; }
        .action-card { background-color: white; border-radius: 10px; padding: 15px; flex: 1; box-shadow: 0 5px 15px rgba(0,0,0,0.05); display: flex; align-items: center; cursor: pointer; transition: all 0.3s ease; }
        .action-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .action-icon { background-color: rgba(67, 97, 238, 0.1); width: 50px; height: 50px; border-radius: 15px; display: flex; justify-content: center; align-items: center; margin-right: 15px; }
        .action-icon i { color: var(--primary-color); font-size: 1.2rem; }
        .action-text { font-weight: 500; }
        #cy { width: 100%; height: 600px; background-color: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .filter-panel { background-color: white; border-radius: 10px; padding: 15px; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .filter-panel .collapse { transition: all 0.3s ease; }
        .filter-section { margin-bottom: 15px; }
        .filter-section label { font-weight: 500; margin-bottom: 5px; display: block; }
        .filter-section select.form-select, .filter-section input { 
            width: 100%; 
            max-width: 300px; /* Ensure a reasonable maximum width */
            padding: 0.5rem 1rem; /* Improve padding for better readability */
            font-size: 1rem; /* Ensure text is legible */
        }
        .filter-section .form-check { margin-bottom: 5px; }
        .dropdown-menu {
            min-width: 200px; /* Ensure dropdown menu has enough width */
            max-height: 300px; /* Prevent overflow with many options */
            overflow-y: auto; /* Add scrollbar if needed */
        }
        .dropdown-item {
            white-space: normal; /* Allow text to wrap if too long */
            padding: 0.5rem 1rem; /* Consistent padding */
            font-size: 0.9rem; /* Adjust font size for readability */
        }
        .pagination-container { margin-top: 20px; display: flex; justify-content: center; }
        @media (max-width: 768px) { 
            .quick-actions { flex-direction: column; }
            .filter-panel { padding: 10px; }
            .filter-section select.form-select, .filter-section input {
                max-width: 100%; /* Full width on mobile */
            }
            .dropdown-menu {
                min-width: 100%; /* Full width on mobile */
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
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="task_mapping.php"><i class="fas fa-map me-1"></i> Task Mapping</a></li>
                    <li class="nav-item"><a class="nav-link" href="analytics.php"><i class="fas fa-chart-line me-1"></i> Analytics</a></li>
                    <li class="nav-item"><a class="nav-link" href="kanban.php"><i class="fas fa-columns me-1"></i> Kanban</a></li>
                    <li class="nav-item"><a class="nav-link" href="insights.php"><i class="fas fa-lightbulb me-1"></i> Insights</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container page-container">
        <h1 class="page-title">Task Mapping</h1>
        
        <div class="quick-actions">
            <div class="action-card" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <div class="action-icon"><i class="fas fa-plus"></i></div>
                <div class="action-text">Add Task</div>
            </div>
            <div class="action-card" onclick="window.location.href='export_tasks.php'">
                <div class="action-icon"><i class="fas fa-file-export"></i></div>
                <div class="action-text">Export Tasks</div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header"><i class="fas fa-tasks"></i> Tasks</div>
                    <div class="card-body">
                        <!-- Filter Panel -->
                        <div class="filter-panel">
                            <button class="btn btn-primary w-100 mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                                <i class="fas fa-filter"></i> Show Filters
                            </button>
                            <div class="collapse" id="filterCollapse">
                                <form id="filterForm">
                                    <div class="filter-section">
                                        <label>Search Tasks</label>
                                        <input type="text" id="searchTask" class="form-control" placeholder="Enter task name...">
                                    </div>
                                    <div class="filter-section">
                                        <label>Project</label>
                                        <select id="filterProject" class="form-select">
                                            <option value="">All Projects</option>
                                            <?php
                                            $stmt = $pdo->query("SELECT ProjectID, ProjectName FROM Projects");
                                            while ($row = $stmt->fetch()) {
                                                echo "<option value='{$row['ProjectID']}'>{$row['ProjectName']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="filter-section">
                                        <label>Owner</label>
                                        <select id="filterOwner" class="form-select">
                                            <option value="">All Owners</option>
                                            <?php
                                            $stmt = $pdo->query("SELECT UserID, Username FROM Users WHERE Role != 'SA Team'");
                                            while ($row = $stmt->fetch()) {
                                                echo "<option value='{$row['UserID']}'>{$row['Username']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="filter-section">
                                        <label>Status</label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="statusPending" value="Pending">
                                            <label class="form-check-label" for="statusPending">Pending</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="statusActive" value="Active">
                                            <label class="form-check-label" for="statusActive">Active</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="statusCompleted" value="Completed">
                                            <label class="form-check-label" for="statusCompleted">Completed</label>
                                        </div>
                                    </div>
                                    <div class="filter-section">
                                        <label>Priority</label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="priorityLow" value="Low">
                                            <label class="form-check-label" for="priorityLow">Low</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="priorityMedium" value="Medium">
                                            <label class="form-check-label" for="priorityMedium">Medium</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="priorityHigh" value="High">
                                            <label class="form-check-label" for="priorityHigh">High</label>
                                        </div>
                                    </div>
                                    <div class="filter-section">
                                        <label>Task</label>
                                        <select id="filterTask" class="form-select">
                                            <option value="">All Tasks</option>
                                            <?php
                                            $stmt = $pdo->query("SELECT TaskID, TaskName FROM ProjectTasks");
                                            while ($row = $stmt->fetch()) {
                                                echo "<option value='{$row['TaskID']}'>{$row['TaskName']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="filter-section">
                                        <label>People Dependencies</label>
                                        <select id="filterPeopleDependency" class="form-select">
                                            <option value="">All People Dependencies</option>
                                            <?php
                                            $stmt = $pdo->query("SELECT DISTINCT PeopleDependencies FROM ProjectTasks WHERE PeopleDependencies IS NOT NULL AND PeopleDependencies != ''");
                                            while ($row = $stmt->fetch()) {
                                                $people = array_map('trim', explode(',', $row['PeopleDependencies']));
                                                foreach ($people as $person) {
                                                    $person = preg_replace('/\d+/', '', trim($person));
                                                    echo "<option value='$person'>$person</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 mt-3">Apply Filters</button>
                                    <button type="button" class="btn btn-secondary w-100 mt-2" id="clearFilters">Clear Filters</button>
                                </form>
                            </div>
                        </div>
                        <div class="table-container">
                            <table class="table" id="tasksTable">
                                <thead>
                                    <tr><th>Task Name</th><th>Project</th><th>Owner</th><th>Status</th><th>Priority</th><th>People Dependencies</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $itemsPerPage = 10;
                                    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                                    $offset = ($page - 1) * $itemsPerPage;

                                    $query = "SELECT pt.TaskID, pt.TaskName, p.ProjectName, u.Username, pt.Status, pt.Priority, pt.PeopleDependencies 
                                              FROM ProjectTasks pt 
                                              JOIN Projects p ON pt.ProjectID = p.ProjectID 
                                              JOIN Users u ON pt.OwnerID = u.UserID 
                                              WHERE 1=1";
                                    $params = [];

                                    $paramCount = 0;
                                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                                        $paramCount++;
                                        $query .= " AND pt.TaskName LIKE ?";
                                        $params[] = '%' . $_GET['search'] . '%';
                                    }
                                    if (isset($_GET['project']) && !empty($_GET['project'])) {
                                        $paramCount++;
                                        $query .= " AND pt.ProjectID = ?";
                                        $params[] = $_GET['project'];
                                    }
                                    if (isset($_GET['owner']) && !empty($_GET['owner'])) {
                                        $paramCount++;
                                        $query .= " AND pt.OwnerID = ?";
                                        $params[] = $_GET['owner'];
                                    }
                                    if (isset($_GET['status']) && !empty($_GET['status'])) {
                                        $statuses = explode(',', $_GET['status']);
                                        if (!empty($statuses)) {
                                            $placeholders = implode(',', array_fill(0, count($statuses), '?'));
                                            $query .= " AND pt.Status IN ($placeholders)";
                                            $params = array_merge($params, $statuses);
                                            $paramCount += count($statuses);
                                        }
                                    }
                                    if (isset($_GET['priority']) && !empty($_GET['priority'])) {
                                        $priorities = explode(',', $_GET['priority']);
                                        if (!empty($priorities)) {
                                            $placeholders = implode(',', array_fill(0, count($priorities), '?'));
                                            $query .= " AND pt.Priority IN ($placeholders)";
                                            $params = array_merge($params, $priorities);
                                            $paramCount += count($priorities);
                                        }
                                    }
                                    if (isset($_GET['task']) && !empty($_GET['task'])) {
                                        $paramCount++;
                                        $query .= " AND pt.TaskID = ?";
                                        $params[] = $_GET['task'];
                                    }
                                    if (isset($_GET['people_dependency']) && !empty($_GET['people_dependency'])) {
                                        $paramCount++;
                                        $query .= " AND pt.PeopleDependencies LIKE ?";
                                        $params[] = '%' . $_GET['people_dependency'] . '%';
                                    }

                                    $countQuery = str_replace("SELECT pt.TaskID, pt.TaskName, p.ProjectName, u.Username, pt.Status, pt.Priority, pt.PeopleDependencies", "SELECT COUNT(*) as total", $query);
                                    $stmt = $pdo->prepare($countQuery);
                                    $stmt->execute($params);
                                    $totalTasks = $stmt->fetchColumn();
                                    $totalPages = ceil($totalTasks / $itemsPerPage);

                                    $query .= " LIMIT ? OFFSET ?";
                                    $params[] = $itemsPerPage;
                                    $params[] = $offset;

                                    $stmt = $pdo->prepare($query);
                                    for ($i = 0; $i < count($params) - 2; $i++) {
                                        $stmt->bindValue($i + 1, $params[$i], PDO::PARAM_STR);
                                    }
                                    $stmt->bindValue(count($params) - 1, $params[count($params) - 2], PDO::PARAM_INT); // LIMIT
                                    $stmt->bindValue(count($params), $params[count($params) - 1], PDO::PARAM_INT); // OFFSET

                                    $stmt->execute();

                                    while ($row = $stmt->fetch()) {
                                        $statusClass = 'status-' . strtolower($row['Status']);
                                        $priorityClass = 'priority-' . strtolower($row['Priority']);
                                        $peopleDeps = $row['PeopleDependencies'] ? explode(',', $row['PeopleDependencies']) : [];
                                        echo "<tr>
                                                <td>{$row['TaskName']}</td>
                                                <td>{$row['ProjectName']}</td>
                                                <td>{$row['Username']}</td>
                                                <td><span class='status-badge {$statusClass}'>{$row['Status']}</span></td>
                                                <td><span class='status-badge {$priorityClass}'>{$row['Priority']}</span></td>
                                                <td>" . ($row['PeopleDependencies'] ? implode(', ', $peopleDeps) : '-') . "</td>
                                                <td>
                                                    <button class='btn btn-sm btn-outline-primary edit-task' data-id='{$row['TaskID']}' data-bs-toggle='modal' data-bs-target='#editTaskModal'><i class='fas fa-edit'></i></button>
                                                    <button class='btn btn-sm btn-outline-danger delete-task ms-1' data-id='{$row['TaskID']}'><i class='fas fa-trash'></i></button>
                                                </td>
                                              </tr>";
                                    }
                                    if ($stmt->rowCount() === 0) {
                                        echo "<tr><td colspan='7' class='text-center'>No tasks found matching the selected filters.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="pagination-container">
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&project=<?php echo urlencode($_GET['project'] ?? ''); ?>&owner=<?php echo urlencode($_GET['owner'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>&task=<?php echo urlencode($_GET['task'] ?? ''); ?>&people_dependency=<?php echo urlencode($_GET['people_dependency'] ?? ''); ?>">Previous</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&project=<?php echo urlencode($_GET['project'] ?? ''); ?>&owner=<?php echo urlencode($_GET['owner'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>&task=<?php echo urlencode($_GET['task'] ?? ''); ?>&people_dependency=<?php echo urlencode($_GET['people_dependency'] ?? ''); ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&project=<?php echo urlencode($_GET['project'] ?? ''); ?>&owner=<?php echo urlencode($_GET['owner'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>&task=<?php echo urlencode($_GET['task'] ?? ''); ?>&people_dependency=<?php echo urlencode($_GET['people_dependency'] ?? ''); ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header"><i class="fas fa-project-diagram"></i> People Dependencies Network</div>
                    <div class="card-body">
                        <div id="cy"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Task Modal -->
        <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="addTaskForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Task Name</label>
                                <input type="text" name="task_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Project</label>
                                <select name="project_id" class="form-select" required>
                                    <?php
                                    $stmt = $pdo->query("SELECT ProjectID, ProjectName FROM Projects");
                                    while ($row = $stmt->fetch()) {
                                        echo "<option value='{$row['ProjectID']}'>{$row['ProjectName']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Owner</label>
                                <select name="owner_id" class="form-select" required>
                                    <?php
                                    $stmt = $pdo->query("SELECT UserID, Username FROM Users WHERE Role != 'SA Team'");
                                    while ($row = $stmt->fetch()) {
                                        echo "<option value='{$row['UserID']}'>{$row['Username']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Active">Active</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select name="priority" class="form-select" required>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">People Dependencies (comma-separated)</label>
                                <input type="text" name="people_dependencies" class="form-control" placeholder="e.g., Fabrication Vendor, Mechanical Engineer">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Task Modal -->
        <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editTaskForm">
                        <div class="modal-body">
                            <input type="hidden" name="task_id" id="edit_task_id">
                            <div class="mb-3">
                                <label class="form-label">Task Name</label>
                                <input type="text" name="task_name" id="edit_task_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Project</label>
                                <select name="project_id" id="edit_project_id" class="form-select" required>
                                    <?php
                                    $stmt = $pdo->query("SELECT ProjectID, ProjectName FROM Projects");
                                    while ($row = $stmt->fetch()) {
                                        echo "<option value='{$row['ProjectID']}'>{$row['ProjectName']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Owner</label>
                                <select name="owner_id" id="edit_owner_id" class="form-select" required>
                                    <?php
                                    $stmt = $pdo->query("SELECT UserID, Username FROM Users WHERE Role != 'SA Team'");
                                    while ($row = $stmt->fetch()) {
                                        echo "<option value='{$row['UserID']}'>{$row['Username']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="edit_status" class="form-select" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Active">Active</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select name="priority" id="edit_priority" class="form-select" required>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">People Dependencies (comma-separated)</label>
                                <input type="text" name="people_dependencies" id="edit_people_dependencies" class="form-control" placeholder="e.g., Fabrication Vendor, Mechanical Engineer">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Helper function to handle fetch errors
            function handleFetch(url, options) {
                return fetch(url, options)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        alert('An error occurred. Please try again.');
                    });
            }

            // Add Task
            document.getElementById('addTaskForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                handleFetch('add_task.php', { method: 'POST', body: formData })
                    .then(data => { if (data && data.success) window.location.reload(); });
            });

            // Edit Task
            document.querySelectorAll('.edit-task').forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.getAttribute('data-id');
                    handleFetch(`get_task.php?id=${taskId}`).then(data => {
                        if (data) {
                            document.getElementById('edit_task_id').value = data.TaskID;
                            document.getElementById('edit_task_name').value = data.TaskName;
                            document.getElementById('edit_project_id').value = data.ProjectID;
                            document.getElementById('edit_owner_id').value = data.OwnerID;
                            document.getElementById('edit_status').value = data.Status;
                            document.getElementById('edit_priority').value = data.Priority;
                            document.getElementById('edit_people_dependencies').value = data.PeopleDependencies || '';
                        }
                    });
                });
            });
            document.getElementById('editTaskForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                handleFetch('edit_task.php', { method: 'POST', body: formData })
                    .then(data => { if (data && data.success) window.location.reload(); });
            });

            // Delete Task
            document.querySelectorAll('.delete-task').forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('Are you sure you want to delete this task?')) {
                        const taskId = this.getAttribute('data-id');
                        handleFetch('delete_task.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `id=${taskId}`
                        }).then(data => { if (data && data.success) window.location.reload(); });
                    }
                });
            });

            // People Dependencies Network Visualization
            function updateNetwork(taskId) {
                handleFetch(taskId ? `get_people_dependencies.php?task_id=${taskId}` : 'get_people_dependencies.php')
                    .then(data => {
                        if (data && data.tasks) {
                            const cy = cytoscape({
                                container: document.getElementById('cy'),
                                elements: [
                                    ...data.tasks.map(task => ({
                                        data: { id: 'task_' + task.TaskID, label: task.TaskName, type: 'task' }
                                    })),
                                    ...data.people.map(person => ({
                                        data: { id: 'person_' + person, label: person, type: 'person' }
                                    })),
                                    ...data.dependencies.flatMap(dep => 
                                        dep.people.split(',').map(person => ({
                                            data: { source: 'task_' + dep.TaskID, target: 'person_' + person.trim() }
                                        }))
                                    )
                                ],
                                style: [
                                    {
                                        selector: 'node[type="task"]',
                                        style: {
                                            'background-color': '#4361ee',
                                            'label': 'data(label)',
                                            'text-valign': 'center',
                                            'color': '#fff',
                                            'text-outline-width': 2,
                                            'text-outline-color': '#333',
                                            'width': 80,
                                            'height': 80
                                        }
                                    },
                                    {
                                        selector: 'node[type="person"]',
                                        style: {
                                            'background-color': '#4cc9f0',
                                            'label': 'data(label)',
                                            'text-valign': 'center',
                                            'color': '#fff',
                                            'text-outline-width': 2,
                                            'text-outline-color': '#333',
                                            'width': 60,
                                            'height': 60
                                        }
                                    },
                                    {
                                        selector: 'edge',
                                        style: {
                                            'width': 2,
                                            'line-color': '#4361ee',
                                            'target-arrow-color': '#4361ee',
                                            'target-arrow-shape': 'triangle',
                                            'curve-style': 'bezier'
                                        }
                                    }
                                ],
                                layout: {
                                    name: 'cose',
                                    fit: true,
                                    padding: 30,
                                    animate: true,
                                    nodeRepulsion: 400000,
                                    idealEdgeLength: 100,
                                    gravity: 0.5
                                }
                            });
                            cy.on('layoutstop', function() {
                                cy.fit();
                                cy.center();
                            });
                        } else {
                            alert('Failed to load people dependencies network data.');
                        }
                    });
            }

            // Filter Handling
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const search = document.getElementById('searchTask').value;
                const project = document.getElementById('filterProject').value;
                const owner = document.getElementById('filterOwner').value;
                const task = document.getElementById('filterTask').value;
                const peopleDependency = document.getElementById('filterPeopleDependency').value;
                const statuses = Array.from(document.querySelectorAll('#filterCollapse input[id^="status"]:checked')).map(cb => cb.value).join(',');
                const priorities = Array.from(document.querySelectorAll('#filterCollapse input[id^="priority"]:checked')).map(cb => cb.value).join(',');
                const url = new URL(window.location.href);
                url.searchParams.set('search', search);
                url.searchParams.set('project', project);
                url.searchParams.set('owner', owner);
                url.searchParams.set('status', statuses);
                url.searchParams.set('priority', priorities);
                url.searchParams.set('task', task);
                url.searchParams.set('people_dependency', peopleDependency);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            });

            document.getElementById('clearFilters').addEventListener('click', function() {
                document.getElementById('searchTask').value = '';
                document.getElementById('filterProject').value = '';
                document.getElementById('filterOwner').value = '';
                document.getElementById('filterTask').value = '';
                document.getElementById('filterPeopleDependency').value = '';
                document.querySelectorAll('#filterCollapse input[type="checkbox"]').forEach(cb => cb.checked = false);
                const url = new URL(window.location.href);
                url.searchParams.delete('search');
                url.searchParams.delete('project');
                url.searchParams.delete('owner');
                url.searchParams.delete('status');
                url.searchParams.delete('priority');
                url.searchParams.delete('task');
                url.searchParams.delete('people_dependency');
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            });

            // Task and People Dependency filter handling for network
            document.getElementById('filterTask').addEventListener('change', function() {
                const taskId = this.value;
                updateNetwork(taskId);
            });
            document.getElementById('filterPeopleDependency').addEventListener('change', function() {
                updateNetwork('');
            });

            // Initial network load
            document.addEventListener('DOMContentLoaded', function() {
                updateNetwork('');
                // Pre-select filters based on URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                document.getElementById('searchTask').value = urlParams.get('search') || '';
                document.getElementById('filterProject').value = urlParams.get('project') || '';
                document.getElementById('filterOwner').value = urlParams.get('owner') || '';
                document.getElementById('filterTask').value = urlParams.get('task') || '';
                document.getElementById('filterPeopleDependency').value = urlParams.get('people_dependency') || '';
                const statuses = urlParams.get('status') ? urlParams.get('status').split(',') : [];
                statuses.forEach(status => {
                    const cb = document.getElementById(`status${status}`);
                    if (cb) cb.checked = true;
                });
                const priorities = urlParams.get('priority') ? urlParams.get('priority').split(',') : [];
                priorities.forEach(priority => {
                    const cb = document.getElementById(`priority${priority}`);
                    if (cb) cb.checked = true;
                });
            });
        </script>
    </body>
</html>
