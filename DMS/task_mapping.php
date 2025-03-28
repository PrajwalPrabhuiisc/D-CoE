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
        .table { width: 100%; margin-bottom: 0; }
        .table thead th { font-weight: 600; color: #444; border-bottom: 2px solid rgba(0,0,0,0.05); padding: 12px 15px; }
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
        @media (max-width: 768px) { .quick-actions { flex-direction: column; } }
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
                    <!-- <li class="nav-item"><a class="nav-link" href="diary_submit.php"><i class="fas fa-edit me-1"></i> Submit Diary</a></li>
                    <li class="nav-item"><a class="nav-link" href="sa_observations.php"><i class="fas fa-eye me-1"></i> SA Observations</a></li> -->
                    <li class="nav-item"><a class="nav-link active" href="task_mapping.php"><i class="fas fa-map me-1"></i> Task Mapping</a></li>
                    <li class="nav-item"><a class="nav-link" href="analytics.php"><i class="fas fa-chart-line me-1"></i> Analytics</a></li>
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

    <div class="container page-container">
        <h1 class="page-title">Task Mapping</h1>
        
        <div class="quick-actions">
            <div class="action-card" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <div class="action-icon"><i class="fas fa-plus"></i></div>
                <div class="action-text">Add Task</div>
            </div>
            <div class="action-card" data-bs-toggle="modal" data-bs-target="#addDependencyModal">
                <div class="action-icon"><i class="fas fa-link"></i></div>
                <div class="action-text">Create Dependency</div>
            </div>
            <div class="action-card" data-bs-toggle="modal" data-bs-target="#taskNetworkModal">
                <div class="action-icon"><i class="fas fa-project-diagram"></i></div>
                <div class="action-text">View Task Network</div>
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
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr><th>Task Name</th><th>Project</th><th>Owner</th><th>Status</th><th>Priority</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT pt.TaskID, pt.TaskName, p.ProjectName, u.Username, pt.Status, pt.Priority 
                                                        FROM ProjectTasks pt 
                                                        JOIN Projects p ON pt.ProjectID = p.ProjectID 
                                                        JOIN Users u ON pt.OwnerID = u.UserID");
                                    while ($row = $stmt->fetch()) {
                                        $statusClass = 'status-' . strtolower($row['Status']);
                                        $priorityClass = 'priority-' . strtolower($row['Priority']);
                                        echo "<tr>
                                                <td>{$row['TaskName']}</td>
                                                <td>{$row['ProjectName']}</td>
                                                <td>{$row['Username']}</td>
                                                <td><span class='status-badge {$statusClass}'>{$row['Status']}</span></td>
                                                <td><span class='status-badge {$priorityClass}'>{$row['Priority']}</span></td>
                                                <td>
                                                    <button class='btn btn-sm btn-outline-primary edit-task' data-id='{$row['TaskID']}' data-bs-toggle='modal' data-bs-target='#editTaskModal'><i class='fas fa-edit'></i></button>
                                                    <button class='btn btn-sm btn-outline-danger delete-task ms-1' data-id='{$row['TaskID']}'><i class='fas fa-trash'></i></button>
                                                </td>
                                              </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
    <div class="card">
        <div class="card-header"><i class="fas fa-project-diagram"></i> Dependencies</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Task Owner</th>
                            <th>Depends On</th>
                            <th>Predecessor Owner</th>
                            <th>Project</th>
                            <th>Status</th>
                            <!-- <th>Actions</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("
                            SELECT 
                                td.DependencyID, 
                                t1.TaskName AS Task, 
                                u1.Username AS TaskOwner, 
                                t2.TaskName AS Predecessor, 
                                u2.Username AS PredecessorOwner, 
                                p.ProjectName, 
                                t1.Status
                            FROM TaskDependencies td 
                            JOIN ProjectTasks t1 ON td.TaskID = t1.TaskID 
                            JOIN ProjectTasks t2 ON td.PredecessorID = t2.TaskID 
                            JOIN Users u1 ON t1.OwnerID = u1.UserID 
                            JOIN Users u2 ON t2.OwnerID = u2.UserID 
                            JOIN Projects p ON t1.ProjectID = p.ProjectID
                        ");
                        while ($row = $stmt->fetch()) {
                            $statusClass = 'status-' . strtolower($row['Status']);
                            echo "<tr>
                                    <td>{$row['Task']}</td>
                                    <td>{$row['TaskOwner']}</td>
                                    <td>{$row['Predecessor']}</td>
                                    <td>{$row['PredecessorOwner']}</td>
                                    <td>{$row['ProjectName']}</td>
                                    <td><span class='status-badge {$statusClass}'>{$row['Status']}</span></td>
                                  </tr>";
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Dependency Modal -->
    <div class="modal fade" id="addDependencyModal" tabindex="-1" aria-labelledby="addDependencyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDependencyModalLabel">Create Dependency</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addDependencyForm">
                    <div class="modal-body">
                        <div class formulaire="mb-3">
                            <label class="form-label">Task</label>
                            <select name="task_id" class="form-select" required>
                                <?php
                                $stmt = $pdo->query("SELECT TaskID, TaskName FROM ProjectTasks");
                                while ($row = $stmt->fetch()) {
                                    echo "<option value='{$row['TaskID']}'>{$row['TaskName']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Depends On</label>
                            <select name="predecessor_id" class="form-select" required>
                                <?php
                                $stmt = $pdo->query("SELECT TaskID, TaskName FROM ProjectTasks");
                                while ($row = $stmt->fetch()) {
                                    echo "<option value='{$row['TaskID']}'>{$row['TaskName']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Dependency</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Edit Dependency Modal -->
<div class="modal fade" id="editDependencyModal" tabindex="-1" aria-labelledby="editDependencyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDependencyModalLabel">Edit Dependency</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editDependencyForm">
                <div class="modal-body">
                    <input type="hidden" name="dependency_id" id="edit_dependency_id">
                    <div class="mb-3">
                        <label class="form-label">Task</label>
                        <select name="task_id" id="edit_task_id_dep" class="form-select" required>
                            <?php
                            $stmt = $pdo->query("SELECT TaskID, TaskName FROM ProjectTasks");
                            while ($row = $stmt->fetch()) {
                                echo "<option value='{$row['TaskID']}'>{$row['TaskName']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Task Owner</label>
                        <select name="task_owner_id" id="edit_task_owner_id" class="form-select" required>
                            <?php
                            $stmt = $pdo->query("SELECT UserID, Username FROM Users WHERE Role != 'SA Team'");
                            while ($row = $stmt->fetch()) {
                                echo "<option value='{$row['UserID']}'>{$row['Username']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Depends On</label>
                        <select name="predecessor_id" id="edit_predecessor_id" class="form-select" required>
                            <?php
                            $stmt = $pdo->query("SELECT TaskID, TaskName FROM ProjectTasks");
                            while ($row = $stmt->fetch()) {
                                echo "<option value='{$row['TaskID']}'>{$row['TaskName']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Predecessor Owner</label>
                        <select name="predecessor_owner_id" id="edit_predecessor_owner_id" class="form-select" required>
                            <?php
                            $stmt = $pdo->query("SELECT UserID, Username FROM Users WHERE Role != 'SA Team'");
                            while ($row = $stmt->fetch()) {
                                echo "<option value='{$row['UserID']}'>{$row['Username']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Project</label>
                        <select name="project_id" id="edit_project_id_dep" class="form-select" required>
                            <?php
                            $stmt = $pdo->query("SELECT ProjectID, ProjectName FROM Projects");
                            while ($row = $stmt->fetch()) {
                                echo "<option value='{$row['ProjectID']}'>{$row['ProjectName']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="edit_status_dep" class="form-select" required>
                            <option value="Pending">Pending</option>
                            <option value="Active">Active</option>
                            <option value="Completed">Completed</option>
                        </select>
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

<!-- Task Network Modal -->
<div class="modal fade" id="taskNetworkModal" tabindex="-1" aria-labelledby="taskNetworkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 90vw;"> <!-- Wider modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskNetworkModalLabel">Task Network</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="cy"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
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

    // Add Dependency
    document.getElementById('addDependencyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        handleFetch('add_dependency.php', { method: 'POST', body: formData })
            .then(data => { if (data && data.success) window.location.reload(); });
    });



    // Delete Dependency
    document.querySelectorAll('.delete-dependency').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this dependency?')) {
                const dependencyId = this.getAttribute('data-id');
                handleFetch('delete_dependency.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${dependencyId}`
                }).then(data => { if (data && data.success) window.location.reload(); });
            }
        });
    });

    // Task Network Visualization
    document.querySelector('.action-card[data-bs-target="#taskNetworkModal"]').addEventListener('click', function() {
        handleFetch('get_task_network.php').then(data => {
            if (data && data.tasks) {
                const cy = cytoscape({
                    container: document.getElementById('cy'),
                    elements: [
                        ...data.tasks.map(task => ({
                            data: { id: task.TaskID, label: task.TaskName, status: task.Status }
                        })),
                        ...data.dependencies.map(dep => ({
                            data: { source: dep.PredecessorID, target: dep.TaskID }
                        }))
                    ],
                    style: [
                        {
                            selector: 'node',
                            style: {
                                'background-color': ele => {
                                    switch (ele.data('status')) {
                                        case 'Pending': return '#ff9800';
                                        case 'Active': return '#4cc9f0';
                                        case 'Completed': return '#4CAF50';
                                        default: return '#4361ee';
                                    }
                                },
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
                alert('Failed to load task network data.');
            }
        });
    });
</script>
</body>
</html>