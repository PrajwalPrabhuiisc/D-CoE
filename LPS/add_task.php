<?php
session_start();
require 'db_connect.php';

// Redirect if not Planner
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Planner') {
    header('Location: login.php');
    exit;
}

$success_message = '';
$error_message = '';
$task_name = '';
$milestone_id = '';
$start_date = '';
$end_date = '';
$assigned_team = '';

// Fetch milestones for dropdown
$milestones = $conn->query("SELECT milestone_id, name FROM milestones")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_name = $_POST['task_name'] ?? '';
    $milestone_id = $_POST['milestone_id'] ?? 0;
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $assigned_team = $_POST['assigned_team'] ?? '';
    $created_by = $_SESSION['user_id'];

    if (empty($task_name) || $milestone_id <= 0 || empty($start_date) || empty($end_date) || empty($assigned_team)) {
        $error_message = 'All fields are required.';
    } elseif (strtotime($end_date) < strtotime($start_date)) {
        $error_message = 'End date cannot be before start date.';
    } else {
        $query = "INSERT INTO tasks (milestone_id, task_name, start_date, end_date, assigned_team, created_by) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('issssi', $milestone_id, $task_name, $start_date, $end_date, $assigned_team, $created_by);
        if ($stmt->execute()) {
            $task_id = $conn->insert_id;
            if (strtotime($start_date) <= strtotime('+1 week')) {
                $week_start = date('Y-m-d', strtotime('monday this week', strtotime($start_date)));
                $conn->query("INSERT INTO weekly_plans (task_id, week_start_date) VALUES ($task_id, '$week_start')");
            }
            $success_message = 'Task added successfully! Redirecting...';
            header('Refresh: 2; URL=index.php');
        } else {
            $error_message = 'Failed to add task. Please try again.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task - LPS</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .task-card {
            max-width: 550px;
            width: 100%;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.1);
            background: #ffffff;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .task-card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background: linear-gradient(90deg, #6366F1, #14B8A6);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.25rem;
            text-align: center;
            position: relative;
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
        .form-control, .form-select {
            border-radius: 10px;
            font-size: 0.875rem;
            border: 1px solid #e2e8f0;
            transition: border-color 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #14B8A6;
            box-shadow: 0 0 5px rgba(20, 184, 166, 0.3);
        }
        .btn-primary {
            background: linear-gradient(90deg, #6366F1, #14B8A6);
            border: none;
            border-radius: 50px;
            padding: 0.75rem 2rem;
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
        .btn-outline-secondary {
            border-color: #64748B;
            color: #64748B;
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-size: 0.875rem;
            font-weight: 600;
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #ffffff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .btn-outline-secondary:hover {
            transform: scale(1.05);
            background: #64748B;
            color: white;
        }
        .alert {
            border-radius: 10px;
            font-size: 0.875rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
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
        @media (max-width: 576px) {
            .task-card {
                margin: 10px;
            }
            .card-header {
                font-size: 1.1rem;
                padding: 1rem;
            }
            .btn-primary, .btn-outline-secondary {
                padding: 0.5rem 1.5rem;
                font-size: 0.75rem;
            }
            .btn-outline-secondary {
                bottom: 15px;
                right: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="task-card fade-in pop">
        <div class="card-header">
            Add New Task
        </div>
        <div class="card-body p-4">
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
            <form method="POST" id="taskForm">
                <div class="mb-3">
                    <label for="task_name" class="form-label fw-medium">Task Name</label>
                    <input type="text" class="form-control" id="task_name" name="task_name" value="<?php echo htmlspecialchars($task_name); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="milestone_id" class="form-label fw-medium">Milestone</label>
                    <select class="form-select" id="milestone_id" name="milestone_id" required>
                        <option value="" disabled <?php echo empty($milestone_id) ? 'selected' : ''; ?>>Select milestone</option>
                        <?php foreach ($milestones as $m): ?>
                            <option value="<?php echo $m['milestone_id']; ?>" <?php echo $milestone_id == $m['milestone_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($m['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="start_date" class="form-label fw-medium">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="end_date" class="form-label fw-medium">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="assigned_team" class="form-label fw-medium">Assigned Team</label>
                    <input type="text" class="form-control" id="assigned_team" name="assigned_team" value="<?php echo htmlspecialchars($assigned_team); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Add Task</button>
            </form>
            <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>

    <!-- Bootstrap 5 JS and Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side validation
        document.getElementById('taskForm').addEventListener('submit', function (e) {
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);
            if (endDate < startDate) {
                e.preventDefault();
                alert('End date cannot be before start date.');
                document.getElementById('end_date').focus();
            }
        });
    </script>
</body>
</html>