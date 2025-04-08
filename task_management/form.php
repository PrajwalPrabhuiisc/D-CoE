<?php
// Database connection
$host = '127.0.0.1';
$dbname = 'task_management_db';
$username = 'root'; // Replace with your DB username
$password = '';     // Replace with your DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch existing users for dropdowns
$usersStmt = $pdo->query("SELECT UserID, Username FROM users");
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing projects for dropdowns
$projectsStmt = $pdo->query("SELECT ProjectID, ProjectName FROM projects");
$projects = $projectsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing tasks for dependencies
$tasksStmt = $pdo->query("SELECT t.TaskID, t.TaskName, t.OwnerID, u.Username AS OwnerName 
                         FROM tasks t 
                         LEFT JOIN users u ON t.OwnerID = u.UserID");
$tasks = $tasksStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing software tools
$softwareStmt = $pdo->query("SELECT ToolID, ToolName FROM tools WHERE ToolType = 'Software'");
$softwareTools = $softwareStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing hardware tools
$hardwareStmt = $pdo->query("SELECT ToolID, ToolName FROM tools WHERE ToolType = 'Hardware'");
$hardwareTools = $hardwareStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4ecca3;
            --primary-hover: #3ba583;
            --secondary-color: #86e3c0;
            --accent-color: #ffa41b;
            --light-bg: #f7fbfc;
            --card-bg: #ffffff;
            --text-color: #435058;
            --text-light: #8c9ba5;
        }
        
        body {
            font-family: 'Quicksand', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-color);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #4ecca3 0%, #55d8c1 100%);
            color: white;
            border: none;
            padding: 20px;
        }
        
        .section-card {
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            margin-bottom: 30px;
            border: 1px solid #f0f0f0;
        }
        
        .section-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }
        
        .section-header {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            color: var(--text-color);
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .section-header i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .task-entry {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            border-left: 4px solid var(--primary-color);
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e8eef2;
            margin-bottom: 10px;
            background-color: #fcfcfc;
        }
        
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px rgba(78, 204, 163, 0.15);
            border-color: var(--primary-color);
            background-color: #fff;
        }
        
        .input-group-text {
            border-radius: 10px 0 0 10px;
            background-color: #f8fafb;
            border: 1px solid #e8eef2;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4ecca3 0%, #55d8c1 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 204, 163, 0.3);
            background: linear-gradient(135deg, #3dbb92 0%, #46c9b2 100%);
        }
        
        .btn-outline-secondary {
            border-radius: 10px;
            padding: 12px 25px;
            color: #8c9ba5;
            border-color: #e8eef2;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f8fafb;
            color: var(--text-color);
            transform: translateY(-2px);
            border-color: #d1d9e0;
        }
        
        .project-section {
            background-color: #fff9f0;
            border-left: 4px solid var(--accent-color);
        }
        
        .tools-section {
            background-color: #f0f9f6;
            border-left: 4px solid #93e4c1;
        }
        
        .collaboration-section {
            background-color: #eef9fc;
            border-left: 4px solid #83d9e8;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-color);
        }
        
        .form-text {
            color: var(--text-light);
        }
        
        .progress {
            height: 6px;
            border-radius: 3px;
            margin-bottom: 30px;
            overflow: visible;
            background-color: rgba(255, 255, 255, 0.3);
        }
        
        .progress-bar {
            background: linear-gradient(135deg, #ffffff 0%, #e5f8f3 100%);
            position: relative;
            border-radius: 3px;
            animation: progress 2s ease-in-out forwards;
        }
        
        @keyframes progress {
            0% { width: 0%; }
            100% { width: 100%; }
        }
        
        select[multiple] {
            min-height: 120px;
        }
        
        .badge {
            padding: 8px 15px;
            border-radius: 30px;
            margin-right: 5px;
            margin-bottom: 5px;
            font-weight: 600;
            background-color: var(--secondary-color);
            color: var(--text-color);
            position: relative;
            padding-right: 30px;
            display: inline-flex;
            align-items: center;
        }
        
        .badge .btn-close {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            opacity: 0.7;
        }
        
        .badge:hover .btn-close {
            opacity: 1;
        }
        
        .nav-pills .nav-link {
            border-radius: 10px;
            margin-bottom: 10px;
            color: var(--text-color);
            padding: 12px 20px;
            font-weight: 500;
        }
        
        .nav-pills .nav-link.active {
            background-color: var(--primary-color);
        }
        
        .footer {
            background-color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 15px 15px;
            color: var(--text-light);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .alert-warning {
            background-color: #fff8e6;
            color: #e6a100;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .form-floating label {
            padding-left: 20px;
        }
        
        .form-floating > .form-control {
            padding: 25px 15px 10px 15px;
        }

        .selection-container {
            margin-top: 10px;
            min-height: 40px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="card">
            <div class="card-header text-center">
                <h1 class="display-5 fw-bold">
                    <i class="fas fa-tasks me-2"></i> Task Management System
                </h1>
                <p class="lead">Manage your tasks, projects, and teamwork efficiently</p>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            
            <div class="card-body p-lg-5 p-4">
                <form action="submit.php" method="POST">
                    <!-- Navigation Tabs -->
                    <ul class="nav nav-pills mb-4" id="formTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="user-tab" data-bs-toggle="pill" data-bs-target="#user-info" type="button" role="tab" aria-controls="user-info" aria-selected="true">
                                <i class="fas fa-user me-2"></i> User Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tasks-tab" data-bs-toggle="pill" data-bs-target="#tasks-section" type="button" role="tab" aria-controls="tasks-section" aria-selected="false">
                                <i class="fas fa-clipboard-list me-2"></i> Tasks
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="projects-tab" data-bs-toggle="pill" data-bs-target="#projects-section" type="button" role="tab" aria-controls="projects-section" aria-selected="false">
                                <i class="fas fa-project-diagram me-2"></i> Projects
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="collab-tab" data-bs-toggle="pill" data-bs-target="#collab-section" type="button" role="tab" aria-controls="collab-section" aria-selected="false">
                                <i class="fas fa-users me-2"></i> Team
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tools-tab" data-bs-toggle="pill" data-bs-target="#tools-section" type="button" role="tab" aria-controls="tools-section" aria-selected="false">
                                <i class="fas fa-tools me-2"></i> Tools
                            </button>
                        </li>
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content" id="formTabContent">
                        <!-- User Info Tab -->
                        <div class="tab-pane fade show active" id="user-info" role="tabpanel" aria-labelledby="user-tab">
                            <div class="section-card">
                                <div class="section-header">
                                    <i class="fas fa-user-circle fa-lg"></i>
                                    <h3 class="fs-5 mb-0">Your Information</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="username" class="form-label">Username</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <div></div>
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('tasks-tab').click()">
                                    Next <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Tasks Tab -->
                        <div class="tab-pane fade" id="tasks-section" role="tabpanel" aria-labelledby="tasks-tab">
                            <div class="section-card">
                                <div class="section-header">
                                    <i class="fas fa-clipboard-list fa-lg"></i>
                                    <h3 class="fs-5 mb-0">Your Tasks & Dependencies</h3>
                                </div>
                                <div class="card-body">
                                    <div id="task-entries">
                                        <div class="task-entry">
                                            <h4 class="fs-5 mb-3">Task 1</h4>
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label for="new_task_1" class="form-label">Task Name</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-clipboard-list"></i></span>
                                                        <input type="text" class="form-control" id="new_task_1" name="new_tasks[]" required placeholder="e.g., Develop Feature X">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="task_owner_1" class="form-label">Task Owner</label>
                                                    <select class="form-select" id="task_owner_1" name="task_owners[]" required>
                                                        <option value="">-- Select Owner --</option>
                                                        <?php foreach ($users as $user): ?>
                                                            <option value="<?php echo $user['UserID']; ?>"><?php echo htmlspecialchars($user['Username']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="card bg-light mt-3">
                                                <div class="card-body">
                                                    <label for="task_dependency_1" class="form-label">Task Dependencies</label>
                                                    <select class="form-select mb-3" id="task_dependency_1" name="task_dependencies[0][]" multiple>
                                                        <option value="">-- Select Existing Predecessor Tasks --</option>
                                                        <?php foreach ($tasks as $task): ?>
                                                            <option value="<?php echo $task['TaskID']; ?>">
                                                                <?php echo htmlspecialchars($task['TaskName']) . " (Owner: " . ($task['OwnerName'] ?? 'None') . ")"; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="input-group mt-2">
                                                        <span class="input-group-text"><i class="fas fa-plus"></i></span>
                                                        <input type="text" class="form-control" name="new_dependencies[0]" placeholder="Add new dependency if not listed">
                                                    </div>
                                                    <div class="form-text mt-2">
                                                        <i class="fas fa-info-circle me-1"></i> Select existing dependencies or type a new one if it doesn't exist.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2 col-lg-6 mx-auto mt-4">
                                        <button type="button" class="btn btn-outline-secondary" id="add-task-btn" onclick="addTaskEntry()">
                                            <i class="fas fa-plus me-2"></i> Add Another Task
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('user-tab').click()">
                                    <i class="fas fa-arrow-left me-2"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('projects-tab').click()">
                                    Next <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Projects Tab -->
                        <div class="tab-pane fade" id="projects-section" role="tabpanel" aria-labelledby="projects-tab">
                            <div class="section-card project-section">
                                <div class="section-header">
                                    <i class="fas fa-project-diagram fa-lg"></i>
                                    <h3 class="fs-5 mb-0">Your Projects</h3>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i> 
                                        Please select all projects you're currently involved with
                                    </div>
                                    <label for="projects" class="form-label">Projects You're Working On</label>
                                    <select class="form-select" id="projects" name="projects[]" multiple required>
                                        <option value="">-- Select Projects --</option>
                                        <?php foreach ($projects as $project): ?>
                                            <option value="<?php echo $project['ProjectID']; ?>">
                                                <?php echo htmlspecialchars($project['ProjectName']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text mt-2">
                                        <i class="fas fa-info-circle me-1"></i> Hold Ctrl (Windows) or Cmd (Mac) to select multiple projects.
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('tasks-tab').click()">
                                    <i class="fas fa-arrow-left me-2"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('collab-tab').click()">
                                    Next <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Collaborations Tab -->
                        <div class="tab-pane fade" id="collab-section" role="tabpanel" aria-labelledby="collab-tab">
                            <div class="section-card collaboration-section">
                                <div class="section-header">
                                    <i class="fas fa-users fa-lg"></i>
                                    <h3 class="fs-5 mb-0">Team Collaborations</h3>
                                </div>
                                <div class="card-body">
                                    <label for="interactions" class="form-label">Team Member Interactions</label>
                                    <select class="form-select mb-3" id="interactions" name="interactions[]" multiple>
                                        <option value="">-- Select Team Members --</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?php echo $user['UserID']; ?>">
                                                <?php echo htmlspecialchars($user['Username']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="selection-container" id="interactions-badges"></div>
                                    <div class="form-text mt-2">
                                        <i class="fas fa-info-circle me-1"></i> Selected team members will appear as badges above. Click X to remove.
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('projects-tab').click()">
                                    <i class="fas fa-arrow-left me-2"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('tools-tab').click()">
                                    Next <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Tools Tab -->
                        <div class="tab-pane fade" id="tools-section" role="tabpanel" aria-labelledby="tools-tab">
                            <div class="section-card tools-section">
                                <div class="section-header">
                                    <i class="fas fa-tools fa-lg"></i>
                                    <h3 class="fs-5 mb-0">Tools & Resources</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label for="software_tools" class="form-label">Software Tools</label>
                                            <select class="form-select mb-3" id="software_tools" name="software_tools[]" multiple>
                                                <option value="">-- Select Existing Software Tools --</option>
                                                <?php foreach ($softwareTools as $tool): ?>
                                                    <option value="<?php echo $tool['ToolID']; ?>">
                                                        <?php echo htmlspecialchars($tool['ToolName']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="selection-container" id="software-badges"></div>
                                            <div class="form-floating mt-3">
                                                <textarea class="form-control" name="new_software_tools" id="new_software_tools" style="height: 100px" placeholder="Add new software tools"></textarea>
                                                <label for="new_software_tools">Add new software tools (one per line)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="hardware_tools" class="form-label">Hardware Tools</label>
                                            <select class="form-select mb-3" id="hardware_tools" name="hardware_tools[]" multiple>
                                                <option value="">-- Select Existing Hardware Tools --</option>
                                                <?php foreach ($hardwareTools as $tool): ?>
                                                    <option value="<?php echo $tool['ToolID']; ?>">
                                                        <?php echo htmlspecialchars($tool['ToolName']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="selection-container" id="hardware-badges"></div>
                                            <div class="form-floating mt-3">
                                                <textarea class="form-control" name="new_hardware_tools" id="new_hardware_tools" style="height: 100px" placeholder="Add new hardware tools"></textarea>
                                                <label for="new_hardware_tools">Add new hardware tools (one per line)</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-text mt-2">
                                        <i class="fas fa-info-circle me-1"></i> Selected and new tools will appear as badges above. Click X to remove.
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('collab-tab').click()">
                                    <i class="fas fa-arrow-left me-2"></i> Previous
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i> Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="footer">
                <p class="mb-0">© 2025 Task Management System. All rights reserved.</p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addTaskEntry() {
            const container = document.getElementById('task-entries');
            const taskCount = container.children.length + 1;
            const newEntry = document.createElement('div');
            newEntry.className = 'task-entry';
            newEntry.innerHTML = `
                <h4 class="fs-5 mb-3">Task ${taskCount}</h4>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="new_task_${taskCount}" class="form-label">Task Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-clipboard-list"></i></span>
                            <input type="text" class="form-control" id="new_task_${taskCount}" name="new_tasks[]" required placeholder="e.g., Develop Feature X">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="task_owner_${taskCount}" class="form-label">Task Owner</label>
                        <select class="form-select" id="task_owner_${taskCount}" name="task_owners[]" required>
                            <option value="">-- Select Owner --</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['UserID']; ?>"><?php echo htmlspecialchars($user['Username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="card bg-light mt-3">
                    <div class="card-body">
                        <label for="task_dependency_${taskCount}" class="form-label">Task Dependencies</label>
                        <select class="form-select mb-3" id="task_dependency_${taskCount}" name="task_dependencies[${taskCount-1}][]" multiple>
                            <option value="">-- Select Existing Predecessor Tasks --</option>
                            <?php foreach ($tasks as $task): ?>
                                <option value="<?php echo $task['TaskID']; ?>">
                                    <?php echo htmlspecialchars($task['TaskName']) . " (Owner: " . ($task['OwnerName'] ?? 'None') . ")"; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-group mt-2">
                            <span class="input-group-text"><i class="fas fa-plus"></i></span>
                            <input type="text" class="form-control" name="new_dependencies[${taskCount-1}]" placeholder="Add new dependency if not listed">
                        </div>
                        <div class="form-text mt-2">
                            <i class="fas fa-info-circle me-1"></i> Select existing dependencies or type a new one if it doesn't exist.
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(newEntry);
        }

        function updateBadges(selectElement, textareaElement, containerId) {
            const container = document.getElementById(containerId);
            container.innerHTML = ''; // Clear existing badges

            // Add badges for selected options from the select element
            const selectedOptions = Array.from(selectElement.selectedOptions);
            selectedOptions.forEach(option => {
                const badge = document.createElement('span');
                badge.className = 'badge';
                badge.dataset.type = 'select';
                badge.dataset.value = option.value;
                badge.innerHTML = `
                    ${option.text}
                    <button type="button" class="btn-close" aria-label="Close"></button>
                `;
                badge.querySelector('.btn-close').addEventListener('click', () => {
                    option.selected = false;
                    updateBadges(selectElement, textareaElement, containerId);
                });
                container.appendChild(badge);
            });

            // Add badges for new tools from the textarea
            const newToolsText = textareaElement.value.trim();
            if (newToolsText) {
                const newTools = newToolsText.split('\n').filter(tool => tool.trim() !== '');
                newTools.forEach(tool => {
                    const badge = document.createElement('span');
                    badge.className = 'badge';
                    badge.dataset.type = 'textarea';
                    badge.dataset.value = tool;
                    badge.innerHTML = `
                        ${tool}
                        <button type="button" class="btn-close" aria-label="Close"></button>
                    `;
                    badge.querySelector('.btn-close').addEventListener('click', () => {
                        const updatedTools = newTools.filter(t => t !== tool).join('\n');
                        textareaElement.value = updatedTools;
                        updateBadges(selectElement, textareaElement, containerId);
                    });
                    container.appendChild(badge);
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const interactionsSelect = document.getElementById('interactions');
            const softwareSelect = document.getElementById('software_tools');
            const hardwareSelect = document.getElementById('hardware_tools');
            const softwareTextarea = document.getElementById('new_software_tools');
            const hardwareTextarea = document.getElementById('new_hardware_tools');

            // Update badges for interactions
            interactionsSelect.addEventListener('change', () => updateBadges(interactionsSelect, { value: '' }, 'interactions-badges'));

            // Update badges for software tools
            softwareSelect.addEventListener('change', () => updateBadges(softwareSelect, softwareTextarea, 'software-badges'));
            softwareTextarea.addEventListener('input', () => updateBadges(softwareSelect, softwareTextarea, 'software-badges'));

            // Update badges for hardware tools
            hardwareSelect.addEventListener('change', () => updateBadges(hardwareSelect, hardwareTextarea, 'hardware-badges'));
            hardwareTextarea.addEventListener('input', () => updateBadges(hardwareSelect, hardwareTextarea, 'hardware-badges'));

            // Initial update
            updateBadges(interactionsSelect, { value: '' }, 'interactions-badges');
            updateBadges(softwareSelect, softwareTextarea, 'software-badges');
            updateBadges(hardwareSelect, hardwareTextarea, 'hardware-badges');
        });
    </script>
</body>
</html>
