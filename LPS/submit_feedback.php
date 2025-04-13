<?php
session_start();
require 'db_connect.php';

// Redirect if not SA_Team
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'SA_Team') {
    header('Location: login.php');
    exit;
}

// Validate task_id
$task_id = filter_input(INPUT_GET, 'task_id', FILTER_VALIDATE_INT) ?: 0;
$user_id = $_SESSION['user_id'] ?? 0;
$errors = [];
$success_message = '';
$feasibility = $_POST['feasibility'] ?? '';
$comments = $_POST['comments'] ?? '';

// Fetch task details
$stmt = $conn->prepare("SELECT t.task_name, t.start_date, t.end_date, m.name AS milestone_name 
                        FROM tasks t 
                        JOIN milestones m ON t.milestone_id = m.milestone_id 
                        WHERE t.task_id = ?");
$stmt->bind_param('i', $task_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$task) {
    $_SESSION['error_message'] = 'Invalid task ID.';
    header('Location: team.php');
    exit;
}
$task_name = htmlspecialchars($task['task_name']);

// Check for existing feedback
$stmt = $conn->prepare("SELECT feedback_id FROM feedback WHERE task_id = ? AND user_id = ?");
$stmt->bind_param('ii', $task_id, $user_id);
$stmt->execute();
$existing_feedback = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($existing_feedback) {
    $_SESSION['error_message'] = 'You have already submitted feedback for this task.';
    header('Location: team.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $feasibility = trim($_POST['feasibility'] ?? '');
    $comments = trim($_POST['comments'] ?? '');

    // Validate inputs
    if (empty($feasibility) || !in_array($feasibility, ['Feasible', 'Not Feasible'])) {
        $errors[] = 'Please select a valid feasibility status.';
    }
    if (strlen($comments) > 500) {
        $errors[] = 'Comments must be 500 characters or less.';
    }

    if (empty($errors)) {
        // Insert feedback
        $stmt = $conn->prepare("INSERT INTO feedback (task_id, user_id, feasibility, comments) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiss', $task_id, $user_id, $feasibility, $comments);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Feedback submitted successfully!';
            header('Location: team.php');
            exit;
        } else {
            $errors[] = 'Failed to submit feedback. Please try again.';
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
    <title>Submit Feedback - LPS</title>
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
            overflow-x: hidden;
        }
        .feedback-card {
            max-width: 500px;
            width: 100%;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.1);
            background: #ffffff;
            animation: fadeIn 0.8s ease-out;
        }
        .card-header {
            background: linear-gradient(90deg, #6366F1, #14B8A6);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 1.25rem;
            font-weight: 600;
            font-size: 1.2rem;
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
        }
        .form-control:focus, .form-select:focus {
            border-color: #14B8A6;
            box-shadow: 0 0 5px rgba(20, 184, 166, 0.3);
        }
        .form-select option:disabled {
            color: #6b7280;
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
        .btn-outline-secondary {
            border-color: #14B8A6;
            color: #14B8A6;
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        .btn-outline-secondary:hover {
            background: #14B8A6;
            color: white;
            transform: scale(1.05);
        }
        .alert {
            border-radius: 10px;
            font-size: 0.875rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .task-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 10px;
            font-size: 0.875rem;
            color: #64748B;
            margin-bottom: 15px;
        }
        .char-counter {
            font-size: 0.75rem;
            color: #64748B;
            text-align: right;
            margin-top: 5px;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 576px) {
            body {
                padding: 10px;
            }
            .feedback-card {
                max-width: 100%;
            }
            .card-header {
                font-size: 1rem;
            }
            .form-control, .form-select, .btn-primary, .btn-outline-secondary {
                font-size: 0.75rem;
            }
            .task-info, .char-counter {
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="feedback-card">
        <div class="card-header">
            Feedback for <?php echo $task_name; ?>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <div class="task-info">
                Milestone: <?php echo htmlspecialchars($task['milestone_name']); ?><br>
                Dates: <?php echo htmlspecialchars($task['start_date'] . ' - ' . $task['end_date']); ?>
            </div>
            <form method="POST">
                <div class="mb-3">
                    <label for="feasibility" class="form-label fw-medium">Feasibility</label>
                    <select class="form-select" id="feasibility" name="feasibility" required>
                        <option value="" disabled <?php echo empty($feasibility) ? 'selected' : ''; ?>>Select status</option>
                        <option value="Feasible" <?php echo $feasibility === 'Feasible' ? 'selected' : ''; ?>>Feasible</option>
                        <option value="Not Feasible" <?php echo $feasibility === 'Not Feasible' ? 'selected' : ''; ?>>Not Feasible</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="comments" class="form-label fw-medium">Comments</label>
                    <textarea class="form-control" id="comments" name="comments" rows="4" maxlength="500" placeholder="Add details or concerns (max 500 characters)" oninput="updateCharCount()"><?php echo htmlspecialchars($comments); ?></textarea>
                    <div class="char-counter" id="charCounter"><?php echo strlen($comments); ?>/500</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="team.php" class="btn btn-outline-secondary">Back to Dashboard</a>
                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateCharCount() {
            const textarea = document.getElementById('comments');
            const counter = document.getElementById('charCounter');
            counter.textContent = `${textarea.value.length}/500`;
        }
        // Initialize counter on load
        updateCharCount();
    </script>
</body>
</html>