<?php
session_start();
require 'db_connect.php';

// Redirect if not Planner
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Planner') {
    header('Location: login.php');
    exit;
}

// Initialize messages
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Fetch milestones and tasks (initial load)
$milestones = $conn->query("SELECT * FROM milestones")->fetch_all(MYSQLI_ASSOC);
$tasks_query = "SELECT t.*, m.name AS milestone_name FROM tasks t JOIN milestones m ON t.milestone_id = m.milestone_id LIMIT 5";
$tasks = $conn->query($tasks_query)->fetch_all(MYSQLI_ASSOC);

// Fetch weekly plans (current week only)
$weekly_query = "SELECT w.*, t.task_name 
                 FROM weekly_plans w 
                 JOIN tasks t ON w.task_id = t.task_id 
                 WHERE w.week_start_date = DATE_FORMAT(CURDATE(), '%Y-%m-%d')";
$weekly = $conn->query($weekly_query)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planner Dashboard - LPS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #e0e7ff, #d1e3ff);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            padding: 15px;
            overflow-x: hidden;
        }
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        /* Sidebar: Scrollable Milestones */
        .sidebar {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.1);
            padding: 20px;
            max-height: 400px;
            overflow: hidden;
            position: sticky;
            top: 15px;
        }
        .milestone-scroll {
            max-height: 320px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #14B8A6 transparent;
        }
        .milestone-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .milestone-scroll::-webkit-scrollbar-thumb {
            background: #14B8A6;
            border-radius: 10px;
        }
        .milestone-item {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .milestone-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(20, 184, 166, 0.2);
        }
        .milestone-item strong {
            color: #0F172A;
        }
        .milestone-item small {
            color: #64748B;
        }
        /* Main Content */
        .main-content .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            background: #ffffff;
            margin-bottom: 15px;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(90deg, #6366F1, #14B8A6);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 1rem;
            font-weight: 600;
            font-size: 1.1rem;
            position: relative;
            cursor: pointer;
            clip-path: polygon(0 0, 100% 0, 100% 80%, 95% 100%, 0 100%);
        }
        .card-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 20px;
            width: 30px;
            height: 10px;
            background: #14B8A6;
            clip-path: polygon(0 0, 100% 0, 50% 100%);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        .table {
            margin-bottom: 0;
            font-size: 0.875rem;
        }
        .table th {
            background: linear-gradient(135deg, #f1f5f9, #e9ecef);
            color: #4F46E5;
            font-weight: 500;
            padding: 8px;
        }
        .table td {
            padding: 8px;
            vertical-align: middle;
            transition: background 0.2s ease;
        }
        .table tr:hover td {
            background: rgba(20, 184, 166, 0.05);
        }
        .form-control, .form-select {
            border-radius: 10px;
            font-size: 0.875rem;
            border: 1px solid #e2e8f0;
        }
        .form-control:focus, .form-select:focus {
            border-color: #14B8A6;
            box-shadow: 0 0 5px rgba(20, 184, 166, 0.3);
        }
        .btn-primary {
            background: linear-gradient(90deg, #6366F1, #14B8A6);
            border: none;
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(20, 184, 166, 0.3);
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.4s ease, height 0.4s ease;
        }
        .btn-primary:hover::before {
            width: 200px;
            height: 200px;
        }
        .btn-warning {
            background: #F59E0B;
            border: none;
            border-radius: 8px;
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
        }
        .btn-warning:hover {
            background: #D97706;
            transform: scale(1.05);
        }
        .btn-load-more {
            background: linear-gradient(90deg, #14B8A6, #20c997);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: transform 0.3s ease;
        }
        .btn-load-more:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(20, 184, 166, 0.3);
        }
        .btn-load-more:disabled {
            background: #64748B;
            cursor: not-allowed;
        }
        .alert {
            border-radius: 10px;
            font-size: 0.875rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .badge {
            font-size: 0.7rem;
            padding: 0.4em 0.8em;
            border-radius: 20px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .badge-pending {
            background: linear-gradient(135deg, #F59E0B, #f97316);
        }
        .feedback-preview, .revision-preview {
            font-size: 0.75rem;
            color: #64748B;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 120px;
            cursor: pointer;
            display: inline-block;
            padding: 4px 8px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        .modal-content {
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
        }
        .modal-header {
            background: linear-gradient(90deg, #6366F1, #14B8A6);
            color: white;
            border-radius: 20px 20px 0 0;
            clip-path: polygon(0 0, 100% 0, 100% 80%, 95% 100%, 0 100%);
        }
        .fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #6366F1, #14B8A6);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 20px rgba(20, 184, 166, 0.4);
            transition: transform 0.3s ease;
            z-index: 1000;
        }
        .fab:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(20, 184, 166, 0.5);
        }
        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }
        .fade-in-row {
            animation: fadeInRow 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInRow {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .pop {
            animation: pop 0.5s ease-out;
        }
        @keyframes pop {
            0% { transform: scale(0.8); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid #14B8A6;
            border-top: 3px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @media (max-width: 992px) {
            .sidebar {
                position: static;
                max-height: 300px;
                margin-bottom: 15px;
            }
            .milestone-scroll {
                max-height: 220px;
            }
            .fab {
                width: 50px;
                height: 50px;
                bottom: 15px;
                right: 15px;
            }
        }
        @media (max-width: 576px) {
            body {
                padding: 10px;
            }
            .card-header {
                font-size: 1rem;
            }
            .table, .feedback-preview, .revision-preview {
                font-size: 0.75rem;
                max-width: 80px;
            }
            .btn-primary, .btn-warning, .btn-load-more {
                font-size: 0.75rem;
                padding: 0.4rem 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container fade-in">
        <div class="row g-3">
            <!-- Sidebar: Scrollable Milestones -->
            <div class="col-lg-3 col-md-4">
                <div class="sidebar pop">
                    <h5 class="fw-bold mb-3 text-center text-primary">Milestones</h5>
                    <div class="milestone-scroll">
                        <?php foreach ($milestones as $m): ?>
                            <div class="milestone-item">
                                <strong><?php echo htmlspecialchars($m['name']); ?></strong><br>
                                <small>Due: <?php echo htmlspecialchars($m['due_date']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Main Content: Tasks & Weekly Plans -->
            <div class="col-lg-9 col-md-8 main-content">
                <!-- Header -->
                <div class="card mb-3 pop">
                    <div class="card-header text-center">Planner Dashboard</div>
                    <div class="card-body p-3">
                        <?php if ($success_message): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($success_message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <p class="text-muted text-center mb-0">Orchestrate your project with precision</p>
                    </div>
                </div>

                <!-- Tasks -->
                <div class="card">
                    <div class="card-header" data-bs-toggle="collapse" data-bs-target="#tasksCollapse" aria-expanded="true">
                        Tasks (Phase & Lookahead)
                    </div>
                    <div class="collapse show" id="tasksCollapse">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tasksTable">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Milestone</th>
                                            <th>Dates</th>
                                            <th>Team</th>
                                            <th>Feedback</th>
                                            <th>Revisions</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tasksBody">
                                        <?php foreach ($tasks as $t): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($t['task_name']); ?></td>
                                                <td><?php echo htmlspecialchars($t['milestone_name']); ?></td>
                                                <td><?php echo htmlspecialchars($t['start_date'] . ' - ' . $t['end_date']); ?></td>
                                                <td><?php echo htmlspecialchars($t['assigned_team']); ?></td>
                                                <td>
                                                    <?php
                                                    $feedback = $conn->query("SELECT * FROM feedback WHERE task_id = {$t['task_id']} LIMIT 1")->fetch_assoc();
                                                    if ($feedback) {
                                                        $preview = htmlspecialchars($feedback['feasibility'] . ': ' . substr($feedback['comments'], 0, 15) . (strlen($feedback['comments']) > 15 ? '...' : ''));
                                                        echo '<span class="feedback-preview" data-bs-toggle="tooltip" title="' . htmlspecialchars($feedback['comments']) . '">' . $preview . '</span>';
                                                    } else {
                                                        echo '<span class="text-muted">None</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $revision = $conn->query("SELECT * FROM revisions WHERE task_id = {$t['task_id']} ORDER BY revision_id DESC LIMIT 1")->fetch_assoc();
                                                    if ($revision) {
                                                        $preview = htmlspecialchars('To ' . $revision['new_data']);
                                                        echo '<span class="revision-preview" data-bs-toggle="tooltip" title="Reason: ' . htmlspecialchars($revision['reason']) . '">' . $preview . '</span>';
                                                    } else {
                                                        echo '<span class="text-muted">None</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="revise_task.php?task_id=<?php echo $t['task_id']; ?>" class="btn btn-warning btn-sm">Revise</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer text-center" id="loadMoreContainer">
                                <button class="btn btn-load-more" id="loadMoreBtn">Load more tasks...</button>
                                <div class="loading-spinner" id="loadingSpinner"></div>
                                <p class="text-muted small d-none" id="noMoreTasks">No more tasks to load.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Weekly Plans -->
                <div class="card">
                    <div class="card-header" data-bs-toggle="collapse" data-bs-target="#weeklyCollapse" aria-expanded="true">
                        Weekly Plans
                    </div>
                    <div class="collapse show" id="weeklyCollapse">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Week</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($weekly): ?>
                                            <?php foreach ($weekly as $w): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($w['task_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($w['week_start_date']); ?></td>
                                                    <td>
                                                        <span class="badge badge-<?php echo strtolower($w['commit_status']) === 'committed' ? 'committed' : 'pending'; ?>">
                                                            <?php echo htmlspecialchars($w['commit_status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No plans this week.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Action Button -->
        <button class="fab" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4"/>
            </svg>
        </button>

        <!-- Add Task Modal -->
        <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="add_task.php">
                            <div class="mb-3">
                                <label for="task_name" class="form-label fw-medium">Task Name</label>
                                <input type="text" class="form-control" id="task_name" name="task_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="milestone_id" class="form-label fw-medium">Milestone ID</label>
                                <input type="number" class="form-control" id="milestone_id" name="milestone_id" required>
                            </div>
                            <div class="mb-3">
                                <label for="start_date" class="form-label fw-medium">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="end_date" class="form-label fw-medium">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="assigned_team" class="form-label fw-medium">Team</label>
                                <input type="text" class="form-control" id="assigned_team" name="assigned_team" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Task</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        // Load more tasks
        let offset = 5;
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const noMoreTasks = document.getElementById('noMoreTasks');
        const tasksBody = document.getElementById('tasksBody');

        loadMoreBtn.addEventListener('click', function () {
            loadMoreBtn.disabled = true;
            loadingSpinner.style.display = 'block';

            fetch('load_more_tasks.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'offset=' + offset
            })
            .then(response => response.json())
            .then(data => {
                loadingSpinner.style.display = 'none';
                if (data.error) {
                    alert(data.error);
                    loadMoreBtn.disabled = false;
                    return;
                }

                data.tasks.forEach(task => {
                    tasksBody.insertAdjacentHTML('beforeend', task.html);
                    // Reinitialize tooltips for new rows
                    const newTooltips = tasksBody.querySelectorAll('[data-bs-toggle="tooltip"]:not(.tooltip-initialized)');
                    newTooltips.forEach(el => {
                        el.classList.add('tooltip-initialized');
                        new bootstrap.Tooltip(el);
                    });
                });

                offset += 5;
                if (!data.has_more) {
                    loadMoreBtn.classList.add('d-none');
                    noMoreTasks.classList.remove('d-none');
                } else {
                    loadMoreBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadingSpinner.style.display = 'none';
                loadMoreBtn.disabled = false;
                alert('Failed to load tasks.');
            });
        });
    </script>
</body>
</html>