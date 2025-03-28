<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            --gray-color: #6c757d;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            padding-bottom: 40px;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 1rem;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.5rem;
        }
        
        .nav-link {
            font-weight: 500;
            color: #555;
            margin: 0 10px;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .nav-link.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
        }
        
        .page-title {
            font-weight: 700;
            color: var(--dark-color);
            margin: 30px 0;
            padding-left: 15px;
            border-left: 5px solid var(--primary-color);
            display: flex;
            align-items: center;
        }
        
        .column-header {
            font-weight: 600;
            font-size: 1.1rem;
            padding: 12px 20px;
            margin-bottom: 15px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 20px;
            z-index: 10;
        }
        
        .column-header i {
            margin-right: 10px;
        }
        
        .kanban-column {
            min-height: 500px;
            background-color: #f9fafc;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
            margin-bottom: 20px;
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
        
        .task-card {
            background-color: white;
            padding: 16px;
            margin-bottom: 12px;
            cursor: move;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
            position: relative;
        }
        
        .task-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .task-card.dragging {
            opacity: 0.5;
            transform: scale(0.98);
        }
        
        .kanban-column.drag-over {
            background-color: rgba(76, 201, 240, 0.15);
            box-shadow: 0 0 0 2px var(--accent-color);
            transform: scale(1.02);
        }
        
        .task-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            font-size: 0.85rem;
            color: var(--gray-color);
        }
        
        .task-title {
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
            font-size: 1rem;
            line-height: 1.4;
        }
        
        .task-project {
            display: inline-block;
            font-size: 0.75rem;
            padding: 3px 10px;
            border-radius: 12px;
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .task-owner {
            display: flex;
            align-items: center;
        }
        
        .task-owner i {
            margin-right: 6px;
            opacity: 0.7;
            font-size: 0.9rem;
        }
        
        .priority-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 2px 8px;
            font-size: 0.7rem;
            border-radius: 10px;
            font-weight: 500;
        }
        
        .priority-high { background-color: rgba(244, 67, 54, 0.1); color: var(--danger-color); }
        .priority-medium { background-color: rgba(255, 152, 0, 0.1); color: var(--warning-color); }
        .priority-low { background-color: rgba(76, 175, 80, 0.1); color: var(--success-color); }

        .column-pending {
            background-color: rgba(255, 152, 0, 0.05);
        }
        
        .column-pending .column-header {
            color: var(--warning-color);
            border-bottom: 2px solid var(--warning-color);
        }
        
        .column-pending .task-card {
            border-left-color: var(--warning-color);
        }
        
        .column-active {
            background-color: rgba(76, 201, 240, 0.05);
        }
        
        .column-active .column-header {
            color: var(--accent-color);
            border-bottom: 2px solid var(--accent-color);
        }
        
        .column-active .task-card {
            border-left-color: var(--accent-color);
        }
        
        .column-completed {
            background-color: rgba(76, 175, 80, 0.05);
        }
        
        .column-completed .column-header {
            color: var(--success-color);
            border-bottom: 2px solid var(--success-color);
        }
        
        .column-completed .task-card {
            border-left-color: var(--success-color);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book-open me-2"></i>Work Progress Status
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
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="diary_submit.php">
                            <i class="fas fa-edit me-1"></i> Submit Diary
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sa_observations.php">
                            <i class="fas fa-eye me-1"></i> SA Observations
                        </a>
                    </li> -->
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
                        <a class="nav-link active" href="kanban.php">
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

    <div class="container mt-4">
        <h1 class="page-title">
            <i class="fas fa-columns me-2"></i>Kanban Board
            <!-- <button class="btn btn-sm btn-outline-primary ms-auto" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="fas fa-plus me-1"></i> Add Task
            </button> -->
        </h1>
        
        <div class="row">
            <!-- Pending Column -->
            <div class="col-md-4">
                <div class="column-header column-pending">
                    <span><i class="fas fa-clock me-2"></i>Pending</span>
                    <span class="badge rounded-pill bg-light text-dark">
                        <?php
                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM ProjectTasks WHERE Status = 'Pending'");
                        $stmt->execute();
                        echo $stmt->fetch()['count'];
                        ?>
                    </span>
                </div>
                <div class="kanban-column column-pending" data-status="Pending">
                    <?php
                    $stmt = $pdo->prepare("SELECT pt.TaskID, pt.TaskName, pt.Priority, p.ProjectName, u.Username 
                                           FROM ProjectTasks pt 
                                           JOIN Projects p ON pt.ProjectID = p.ProjectID 
                                           JOIN Users u ON pt.OwnerID = u.UserID 
                                           WHERE pt.Status = 'Pending'");
                    $stmt->execute();
                    while ($row = $stmt->fetch()) {
                        echo "<div class='task-card' draggable='true' data-task-id='{$row['TaskID']}'>
                                <div class='task-project'>{$row['ProjectName']}</div>
                                <div class='task-title'>{$row['TaskName']}</div>
                                <span class='priority-badge priority-{$row['Priority']}'>{$row['Priority']}</span>
                                <div class='task-meta'>
                                    <span class='task-owner'><i class='fas fa-user'></i>{$row['Username']}</span>
                                </div>
                              </div>";
                    }
                    ?>
                </div>
            </div>
            
            <!-- Active Column -->
            <div class="col-md-4">
                <div class="column-header column-active">
                    <span><i class="fas fa-spinner me-2"></i>Active</span>
                    <span class="badge rounded-pill bg-light text-dark">
                        <?php
                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM ProjectTasks WHERE Status = 'Active'");
                        $stmt->execute();
                        echo $stmt->fetch()['count'];
                        ?>
                    </span>
                </div>
                <div class="kanban-column column-active" data-status="Active">
                    <?php
                    $stmt = $pdo->prepare("SELECT pt.TaskID, pt.TaskName, pt.Priority, p.ProjectName, u.Username 
                                           FROM ProjectTasks pt 
                                           JOIN Projects p ON pt.ProjectID = p.ProjectID 
                                           JOIN Users u ON pt.OwnerID = u.UserID 
                                           WHERE pt.Status = 'Active'");
                    $stmt->execute();
                    while ($row = $stmt->fetch()) {
                        echo "<div class='task-card' draggable='true' data-task-id='{$row['TaskID']}'>
                                <div class='task-project'>{$row['ProjectName']}</div>
                                <div class='task-title'>{$row['TaskName']}</div>
                                <span class='priority-badge priority-{$row['Priority']}'>{$row['Priority']}</span>
                                <div class='task-meta'>
                                    <span class='task-owner'><i class='fas fa-user'></i>{$row['Username']}</span>
                                </div>
                              </div>";
                    }
                    ?>
                </div>
            </div>
            
            <!-- Completed Column -->
            <div class="col-md-4">
                <div class="column-header column-completed">
                    <span><i class="fas fa-check-circle me-2"></i>Completed</span>
                    <span class="badge rounded-pill bg-light text-dark">
                        <?php
                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM ProjectTasks WHERE Status = 'Completed'");
                        $stmt->execute();
                        echo $stmt->fetch()['count'];
                        ?>
                    </span>
                </div>
                <div class="kanban-column column-completed" data-status="Completed">
                    <?php
                    $stmt = $pdo->prepare("SELECT pt.TaskID, pt.TaskName, pt.Priority, p.ProjectName, u.Username 
                                           FROM ProjectTasks pt 
                                           JOIN Projects p ON pt.ProjectID = p.ProjectID 
                                           JOIN Users u ON pt.OwnerID = u.UserID 
                                           WHERE pt.Status = 'Completed'");
                    $stmt->execute();
                    while ($row = $stmt->fetch()) {
                        echo "<div class='task-card' draggable='true' data-task-id='{$row['TaskID']}'>
                                <div class='task-project'>{$row['ProjectName']}</div>
                                <div class='task-title'>{$row['TaskName']}</div>
                                <span class='priority-badge priority-{$row['Priority']}'>{$row['Priority']}</span>
                                <div class='task-meta'>
                                    <span class='task-owner'><i class='fas fa-user'></i>{$row['Username']}</span>
                                    <span><i class='fas fa-check text-success'></i></span>
                                </div>
                              </div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addTaskForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Task Name</label>
                            <input type="text" class="form-control" name="task_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Project</label>
                            <select class="form-select" name="project_id" required>
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
                            <select class="form-select" name="owner_id" required>
                                <?php
                                $stmt = $pdo->query("SELECT UserID, Username FROM Users WHERE Role != 'SA Team'");
                                while ($row = $stmt->fetch()) {
                                    echo "<option value='{$row['UserID']}'>{$row['Username']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Priority</label>
                            <select class="form-select" name="priority" required>
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.task-card').on('dragstart', function(e) {
                e.originalEvent.dataTransfer.setData('text/plain', $(this).data('task-id'));
                $(this).addClass('dragging');
            }).on('dragend', function() {
                $(this).removeClass('dragging');
            });

            $('.kanban-column').on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('drag-over');
            }).on('dragleave', function() {
                $(this).removeClass('drag-over');
            }).on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('drag-over');
                const taskId = e.originalEvent.dataTransfer.getData('text/plain');
                const newStatus = $(this).data('status');
                const $taskCard = $(`.task-card[data-task-id="${taskId}"]`);
                
                $(this).append($taskCard);

                $.ajax({
                    url: 'update_task_status.php',
                    method: 'POST',
                    data: { task_id: taskId, status: newStatus },
                    success: function(response) {
                        const counts = JSON.parse(response);
                        $('.column-pending .badge').text(counts.Pending);
                        $('.column-active .badge').text(counts.Active);
                        $('.column-completed .badge').text(counts.Completed);
                    },
                    error: function(xhr) {
                        alert('Error updating task: ' + xhr.responseText);
                        location.reload();
                    }
                });
            });

            $('#addTaskForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'add_task.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#addTaskModal').modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error adding task: ' + xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>