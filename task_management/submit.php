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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $newTasks = $_POST['new_tasks'] ?? [];
    $taskOwners = $_POST['task_owners'] ?? [];
    $taskDependencies = $_POST['task_dependencies'] ?? [];
    $newDependencies = $_POST['new_dependencies'] ?? [];
    $interactions = $_POST['interactions'] ?? [];
    $projects = $_POST['projects'] ?? [];
    $softwareTools = $_POST['software_tools'];
    $hardwareTools = $_POST['hardware_tools'];

    $pdo->beginTransaction();
    $success = true;
    $errorMessage = "";

    try {
        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (Username, Email) VALUES (?, ?)");
        $stmt->execute([$username, $email]);
        $userId = $pdo->lastInsertId();

        // Insert new tasks and their dependencies
        $taskIds = [];
        foreach ($newTasks as $index => $taskName) {
            $ownerId = $taskOwners[$index];
            $stmt = $pdo->prepare("INSERT INTO tasks (TaskName, OwnerID) VALUES (?, ?)");
            $stmt->execute([$taskName, $ownerId]);
            $taskIds[$index] = $pdo->lastInsertId();

            // Insert existing dependencies
            if (!empty($taskDependencies[$index])) {
                foreach ($taskDependencies[$index] as $predecessorId) {
                    if ($taskIds[$index] != $predecessorId) {
                        $stmt = $pdo->prepare("INSERT INTO task_dependencies (TaskID, PredecessorTaskID) VALUES (?, ?)");
                        $stmt->execute([$taskIds[$index], $predecessorId]);
                    }
                }
            }

            // Insert new dependencies (as new tasks with a default owner)
            if (!empty($newDependencies[$index])) {
                $stmt = $pdo->prepare("INSERT INTO tasks (TaskName, OwnerID) VALUES (?, ?)");
                $stmt->execute([$newDependencies[$index], $ownerId]); // Default to same owner
                $newDepId = $pdo->lastInsertId();
                $stmt = $pdo->prepare("INSERT INTO task_dependencies (TaskID, PredecessorTaskID) VALUES (?, ?)");
                $stmt->execute([$taskIds[$index], $newDepId]);
            }
        }

        // Insert user interactions
        foreach ($interactions as $interactedUserId) {
            if ($userId != $interactedUserId) {
                $stmt = $pdo->prepare("INSERT INTO user_interactions (UserID, InteractedUserID) VALUES (?, ?)");
                $stmt->execute([$userId, $interactedUserId]);
            }
        }

        // Insert user projects
        foreach ($projects as $projectId) {
            $stmt = $pdo->prepare("INSERT INTO user_projects (UserID, ProjectID) VALUES (?, ?)");
            $stmt->execute([$userId, $projectId]);
        }

        // Insert software tools
        if (!empty($softwareTools)) {
            $tools = explode("\n", trim($softwareTools));
            foreach ($tools as $tool) {
                if (!empty($tool)) {
                    $stmt = $pdo->prepare("INSERT INTO user_tools (UserID, ToolType, ToolName) VALUES (?, 'Software', ?)");
                    $stmt->execute([$userId, trim($tool)]);
                }
            }
        }

        // Insert hardware tools
        if (!empty($hardwareTools)) {
            $tools = explode("\n", trim($hardwareTools));
            foreach ($tools as $tool) {
                if (!empty($tool)) {
                    $stmt = $pdo->prepare("INSERT INTO user_tools (UserID, ToolType, ToolName) VALUES (?, 'Hardware', ?)");
                    $stmt->execute([$userId, trim($tool)]);
                }
            }
        }

        $pdo->commit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $success = false;
        $errorMessage = $e->getMessage();
    }
} else {
    $success = false;
    $errorMessage = "Invalid request method.";
}

// Return JSON if it's an AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    if ($success) {
        echo json_encode(['status' => 'success', 'userId' => $userId]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $errorMessage]);
    }
    exit;
}

// HTML output for non-AJAX requests
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? 'Submission Successful' : 'Submission Error'; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --primary-light: #e0f2ff;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --text-color: #2c3e50;
            --light-gray: #f5f8fa;
            --border-radius: 12px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--light-gray);
            color: var(--text-color);
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .header .icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 36px;
        }
        
        .content {
            padding: 30px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .user-info .avatar {
            width: 60px;
            height: 60px;
            background-color: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 24px;
            color: var(--primary-color);
        }
        
        .user-info .details h2 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .user-info .details p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section h3 {
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .section h3 i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .card {
            background-color: #fff;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .task-list {
            list-style: none;
        }
        
        .task-item {
            display: flex;
            flex-direction: column;
            padding: 15px;
            border-radius: 8px;
            background-color: #f8f9fa;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        .task-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .task-item .task-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .task-item .task-details {
            font-size: 14px;
            color: #7f8c8d;
        }
        
        .badge-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            background-color: var(--primary-light);
            color: var(--primary-color);
        }
        
        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .tool-card {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }
        
        .tool-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary-color);
        }
        
        .tool-name {
            font-weight: 500;
            font-size: 14px;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
        }
        
        .footer button {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .footer button:hover {
            background-color: #2980b9;
        }
        
        .error-container {
            text-align: center;
            padding: 50px;
        }
        
        .error-icon {
            font-size: 64px;
            color: var(--danger-color);
            margin-bottom: 20px;
        }
        
        .error-message {
            margin-top: 20px;
            padding: 15px;
            background-color: #fae5e5;
            border-radius: 8px;
            color: var(--danger-color);
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
            }
            
            .header {
                padding: 20px;
            }
            
            .content {
                padding: 20px;
            }
            
            .tools-grid {
                grid-template-columns: 1fr;
            }
            
            .user-info {
                flex-direction: column;
                text-align: center;
            }
            
            .user-info .avatar {
                margin: 0 auto 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($success): ?>
            <div class="header">
                <i class="fas fa-check-circle icon"></i>
                <h1>Submission Successful!</h1>
                <p>Your task information has been submitted successfully.</p>
            </div>
            
            <div class="content">
                <div class="user-info">
                    <div class="avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="details">
                        <h2><?php echo htmlspecialchars($username); ?></h2>
                        <p><?php echo htmlspecialchars($email); ?></p>
                    </div>
                </div>
                
                <div class="section">
                    <h3><i class="fas fa-tasks"></i> Tasks</h3>
                    <ul class="task-list">
                        <?php foreach ($newTasks as $index => $task): ?>
                        <li class="task-item">
                            <div class="task-name"><?php echo htmlspecialchars($task); ?></div>
                            <div class="task-details">Owner ID: <?php echo htmlspecialchars($taskOwners[$index]); ?></div>
                            
                            <?php if (!empty($taskDependencies[$index]) || !empty($newDependencies[$index])): ?>
                            <div class="badge-list">
                                <?php if (!empty($taskDependencies[$index])): ?>
                                    <?php foreach ($taskDependencies[$index] as $depId): ?>
                                    <span class="badge">Depends on: <?php echo htmlspecialchars($depId); ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <?php if (!empty($newDependencies[$index])): ?>
                                <span class="badge">New Dependency: <?php echo htmlspecialchars($newDependencies[$index]); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <?php if (!empty($projects)): ?>
                <div class="section">
                    <h3><i class="fas fa-project-diagram"></i> Projects</h3>
                    <div class="card">
                        <div class="badge-list">
                            <?php foreach ($projects as $project): ?>
                            <span class="badge"><?php echo htmlspecialchars($project); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($interactions)): ?>
                <div class="section">
                    <h3><i class="fas fa-users"></i> Interactions</h3>
                    <div class="card">
                        <div class="badge-list">
                            <?php foreach ($interactions as $interaction): ?>
                            <span class="badge">User #<?php echo htmlspecialchars($interaction); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($softwareTools) || !empty($hardwareTools)): ?>
                <div class="section">
                    <h3><i class="fas fa-tools"></i> Tools</h3>
                    
                    <?php if (!empty($softwareTools)): ?>
                    <h4 style="margin: 15px 0 10px; font-size: 16px;"><i class="fas fa-code"></i> Software Tools</h4>
                    <div class="tools-grid">
                        <?php 
                        $tools = explode("\n", trim($softwareTools));
                        foreach ($tools as $tool): 
                            if (!empty(trim($tool))):
                        ?>
                        <div class="tool-card">
                            <div class="tool-icon">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                            <div class="tool-name"><?php echo htmlspecialchars(trim($tool)); ?></div>
                        </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($hardwareTools)): ?>
                    <h4 style="margin: 25px 0 10px; font-size: 16px;"><i class="fas fa-microchip"></i> Hardware Tools</h4>
                    <div class="tools-grid">
                        <?php 
                        $tools = explode("\n", trim($hardwareTools));
                        foreach ($tools as $tool): 
                            if (!empty(trim($tool))):
                        ?>
                        <div class="tool-card">
                            <div class="tool-icon">
                                <i class="fas fa-memory"></i>
                            </div>
                            <div class="tool-name"><?php echo htmlspecialchars(trim($tool)); ?></div>
                        </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="footer">
                <button onclick="window.location.href='index.php'">Back to Form</button>
            </div>
            
        <?php else: ?>
            <div class="header" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                <i class="fas fa-exclamation-circle icon"></i>
                <h1>Submission Error</h1>
                <p>There was a problem with your submission.</p>
            </div>
            
            <div class="error-container">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2>Something went wrong</h2>
                <p>We couldn't process your submission due to an error.</p>
                
                <?php if (!empty($errorMessage)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
                <?php endif; ?>
                
                <button onclick="window.history.back()" style="margin-top: 20px; padding: 10px 20px; background-color: var(--danger-color); color: white; border: none; border-radius: var(--border-radius); cursor: pointer;">
                    Go Back and Try Again
                </button>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Enable animation effects when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.container');
            container.style.opacity = '0';
            container.style.transform = 'translateY(20px)';
            container.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            
            setTimeout(function() {
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);
            
            // Add animation to task items
            const taskItems = document.querySelectorAll('.task-item');
            taskItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
                item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                
                setTimeout(function() {
                    item.style.opacity = '1';
                    item.style.transform = 'translateX(0)';
                }, 300 + (index * 100));
            });
            
            // Add animation to tool cards
            const toolCards = document.querySelectorAll('.tool-card');
            toolCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                
                setTimeout(function() {
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                }, 500 + (index * 50));
            });
        });
    </script>
</body>
</html>
