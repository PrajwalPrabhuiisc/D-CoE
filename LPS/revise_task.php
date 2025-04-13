<?php
session_start();
require 'db_connect.php';
if ($_SESSION['role'] != 'Planner') {
    header('Location: login.php');
    exit;
}
$task_id = $_GET['task_id'] ?? 0;
$task = $conn->query("SELECT * FROM tasks WHERE task_id = $task_id")->fetch_assoc();
if (!$task) {
    $_SESSION['error_message'] = 'Invalid task ID.';
    header('Location: index.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_start = $_POST['start_date'] ?? '';
    $new_end = $_POST['end_date'] ?? '';
    $reason = $_POST['reason'] ?? '';
    if (empty($new_start) || empty($new_end) || empty($reason)) {
        $_SESSION['error_message'] = 'All fields are required.';
    } else {
        $old_data = $task['start_date'];
        $conn->query("INSERT INTO revisions (task_id, old_data, new_data, reason) VALUES ($task_id, '$old_data', '$new_start', '" . $conn->real_escape_string($reason) . "')");
        $conn->query("UPDATE tasks SET start_date = '$new_start', end_date = '$new_end' WHERE task_id = $task_id");
        $_SESSION['success_message'] = 'Task revised successfully!';
    }
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revise Task - LPS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            max-width: 500px;
            width: 100%;
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            background: #ffffff;
        }
        .card-header {
            background: linear-gradient(90deg, #F59E0B, #D97706);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1.25rem;
            font-weight: 600;
        }
        .form-control:focus {
            border-color: #F59E0B;
            box-shadow: 0 0 5px rgba(245, 158, 11, 0.3);
        }
        .btn-primary {
            background: #F59E0B;
            border: none;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background: #D97706;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">Revise Task: <?php echo htmlspecialchars($task['task_name']); ?></div>
        <div class="card-body p-4">
            <form method="POST">
                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($task['start_date']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($task['end_date']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="reason" class="form-label">Reason for Revision</label>
                    <input type="text" class="form-control" id="reason" name="reason" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Task</button>
                <a href="index.php" class="btn btn-outline-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>