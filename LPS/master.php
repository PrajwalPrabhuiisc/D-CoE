<?php
session_start();
include 'db_connect.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    try {
        if ($_POST['action'] === 'update_milestone') {
            $milestone_id = (int)$_POST['milestone_id'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $responsible_user = (int)$_POST['responsible_user'];

            if (strtotime($start_date) >= strtotime($end_date)) {
                throw new Exception("Start date must be before end date.");
            }

            $stmt = $pdo->prepare("UPDATE milestones SET start_date = ?, end_date = ?, responsible_user = ? WHERE milestone_id = ?");
            $stmt->execute([$start_date, $end_date, $responsible_user, $milestone_id]);

            echo json_encode(['success' => true, 'message' => 'Milestone updated successfully']);
        } elseif ($_POST['action'] === 'add_milestone') {
            $name = trim($_POST['name']);
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $responsible_user = (int)$_POST['responsible_user'];

            if (empty($name)) {
                throw new Exception("Milestone name is required.");
            }
            if (strtotime($start_date) >= strtotime($end_date)) {
                throw new Exception("Start date must be before end date.");
            }

            $stmt = $pdo->prepare("INSERT INTO milestones (name, start_date, end_date, responsible_user) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $start_date, $end_date, $responsible_user]);
            $new_id = $pdo->lastInsertId();
            echo json_encode([
                'success' => true,
                'message'=>'Milestone added successfully',
                'milestone' => [
                    'milestone_id' => $new_id,
                    'name' => $name,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'responsible_user' => $responsible_user
                ]
            ]);
        } elseif ($_POST['action'] === 'delete_milestone') {
            $milestone_id = (int)$_POST['milestone_id'];
            $stmt = $pdo->prepare("DELETE FROM milestones WHERE milestone_id = ?");
            $stmt->execute([$milestone_id]);
            echo json_encode(['success' => true, 'message' => 'Milestone deleted successfully']);
        } elseif ($_POST['action'] === 'add_feedback') {
            $milestone_id = (int)$_POST['milestone_id'];
            $comment = trim($_POST['comment']);
            $submitted_by = (int)$_SESSION['user_id'];

            if (empty($comment)) {
                throw new Exception("Comment cannot be empty.");
            }

            $stmt = $pdo->prepare("INSERT INTO feedback (milestone_id, comment, submitted_by, submission_date) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$milestone_id, $comment, $submitted_by]);
            echo json_encode(['success' => true, 'message' => 'Feedback added successfully']);
        } elseif ($_POST['action'] === 'get_risks') {
            $milestone_id = (int)$_POST['milestone_id'];
            $depsStmt = $pdo->prepare("
                SELECT h.description
                FROM handoffs h
                JOIN phases p ON h.phase_id = p.phase_id
                WHERE p.milestone_id = ?
            ");
            $depsStmt->execute([$milestone_id]);
            $dependencies = $depsStmt->fetchAll(PDO::FETCH_ASSOC);

            $constStmt = $pdo->prepare("
                SELECT a.description, a.constraint_flag
                FROM activities a
                JOIN phases p ON a.phase_id = p.phase_id
                WHERE p.milestone_id = ? AND a.constraint_flag = 1
            ");
            $constStmt->execute([$milestone_id]);
            $constraints = $constStmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'dependencies' => $dependencies,
                'constraints' => $constraints
            ]);
        } elseif ($_POST['action'] === 'get_feedback') {
            $milestone_id = (int)$_POST['milestone_id'];
            $stmt = $pdo->prepare("
                SELECT f.feedback_id, f.comment, f.submitted_by, f.submission_date, u.name AS user_name
                FROM feedback f
                JOIN users u ON f.submitted_by = u.user_id
                WHERE f.milestone_id = ?
                ORDER BY f.submission_date DESC
            ");
            $stmt->execute([$milestone_id]);
            $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $rows = '';
            if (empty($feedback)) {
                $rows = "<li class='list-group-item text-muted'>No feedback available</li>";
            } else {
                foreach ($feedback as $row) {
                    $rows .= "
                        <li class='list-group-item'>
                            <div class='d-flex justify-content-between align-items-center'>
                                <div>
                                    <strong>" . htmlspecialchars($row['user_name']) . "</strong> - 
                                    <span class='text-muted'>" . (new DateTime($row['submission_date']))->format('Y-m-d H:i:s') . "</span>
                                    <p class='mb-0'>" . htmlspecialchars($row['comment']) . "</p>
                                </div>
                                <button class='btn btn-link btn-sm delete-feedback-btn' data-id='{$row['feedback_id']}' data-milestone-id='{$milestone_id}'>
                                    <i class='bi bi-trash text-danger'></i>
                                </button>
                            </div>
                        </li>";
                }
            }
            echo $rows;
        } elseif ($_POST['action'] === 'delete_feedback') {
            $feedback_id = (int)$_POST['feedback_id'];
            $stmt = $pdo->prepare("DELETE FROM feedback WHERE feedback_id = ?");
            $stmt->execute([$feedback_id]);
            echo json_encode(['success' => true, 'message' => 'Feedback deleted successfully']);
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

$usersStmt = $pdo->query("SELECT user_id, name FROM users");
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch milestones for the Gantt chart
$milestonesStmt = $pdo->query("
    SELECT m.*, u.name as responsible_user_name
    FROM milestones m
    LEFT JOIN users u ON m.responsible_user = u.user_id
    ORDER BY m.start_date ASC
");
$milestones = $milestonesStmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for the Gantt chart
$ganttData = [];
$today = new DateTime();
$minDate = null;
$maxDate = null;

foreach ($milestones as $row) {
    $start = new DateTime($row['start_date']);
    $end = new DateTime($row['end_date']);
    $totalDays = $start->diff($end)->days;
    $daysPassed = $start->diff($today)->days;
    $progress = $totalDays > 0 ? min(100, max(0, ($daysPassed / $totalDays) * 100)) : 0;
    if ($today < $start) $progress = 0;
    if ($today > $end) $progress = 100;

    // Determine min and max dates for the timeline
    if (!$minDate || $start < $minDate) $minDate = $start;
    if (!$maxDate || $end > $maxDate) $maxDate = $end;

    $depsStmt = $pdo->prepare("
        SELECT h.description
        FROM handoffs h
        JOIN phases p ON h.phase_id = p.phase_id
        WHERE p.milestone_id = ?
    ");
    $depsStmt->execute([$row['milestone_id']]);
    $dependencies = $depsStmt->fetchAll(PDO::FETCH_ASSOC);

    $constStmt = $pdo->prepare("
        SELECT a.description, a.constraint_flag
        FROM activities a
        JOIN phases p ON a.phase_id = p.phase_id
        WHERE p.milestone_id = ? AND a.constraint_flag = 1
    ");
    $constStmt->execute([$row['milestone_id']]);
    $constraints = $constStmt->fetchAll(PDO::FETCH_ASSOC);

    $totalRisks = count($dependencies) + count($constraints);

    $ganttData[] = [
        'milestone_id' => $row['milestone_id'],
        'name' => $row['name'],
        'start_date' => $row['start_date'],
        'end_date' => $row['end_date'],
        'responsible_user' => $row['responsible_user'],
        'responsible_user_name' => $row['responsible_user_name'],
        'progress' => $progress,
        'total_risks' => $totalRisks
    ];
}

// Add buffer to the date range
if ($minDate && $maxDate) {
    $minDate->modify('-1 month');
    $maxDate->modify('+1 month');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3a0ca3;
            --accent: #4cc9f0;
            --success: #2ecc71;
            --warning: #f1c40f;
            --danger: #e74c3c;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --soft-gray: #e9ecef;
            --pastel-warning: #fef9e7;
            --pastel-danger: #fef1f1;
            --text-muted: #adb5bd;
        }
        
        body {
            background-color: #f5f7fa;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: var(--dark);
        }

        .dashboard-container {
            max-width: 1400px;
            padding: 3rem 1.5rem;
            margin: 0 auto;
        }

        .card {
            border: none;
            border-radius: 16px;
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 1.5rem;
            border-bottom: none;
            border-radius: 16px 16px 0 0 !important;
            position: relative;
            overflow: hidden;
        }

        .card-header::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
        }

        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: var(--dark);
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-light);
        }

        .btn-outline-secondary {
            border-color: var(--soft-gray);
            color: var(--gray);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background: var(--soft-gray);
            color: var(--dark);
        }

        .btn-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .btn-link:hover {
            color: var(--primary-light);
            text-decoration: underline;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid var(--soft-gray);
            font-size: 0.95rem;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.1);
        }

        .add-milestone-form {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);
            margin-bottom: 2rem;
        }

        .add-milestone-form h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .badge-risk {
            background: var(--pastel-danger);
            color: #c0392b;
            font-weight: 500;
            padding: 0.4rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
        }

        .modal-content {
            border-radius: 12px;
            border: none;
            animation: fadeIn 0.3s ease;
        }

        .modal-header {
            background: var(--primary);
            color: white;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .list-group-item {
            border: none;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }

        .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
            animation: fadeIn 0.2s ease;
        }

        .dropdown-item {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dropdown-item:hover {
            background: var(--soft-gray);
        }

        .action-menu-btn {
            border: none;
            background: none;
            color: var(--gray);
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .action-menu-btn:hover {
            color: var(--primary);
        }

        /* Gantt Chart Styles */
        .gantt-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);
            padding: 1rem;
            margin-bottom: 2rem;
        }

        .gantt-chart {
            width: 100%;
            height: 400px;
        }

        .milestone-details {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .milestone-details h5 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 2rem 1rem;
            }

            .add-milestone-form {
                padding: 1.5rem;
            }

            .form-control, .form-select {
                font-size: 0.9rem;
            }

            .gantt-chart {
                height: 600px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="card mb-4">
            <div class="card-header">
                <div class="dashboard-header">
                    <div class="logo">
                        <i class="bi bi-kanban"></i> 
                        <span>LPS Dashboard</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <h2 class="page-title">Master Schedule (Gantt Chart)</h2>
                <a href="index.php" class="btn btn-outline-secondary mb-4">Back to Dashboard</a>

                <!-- Add Milestone Form -->
                <div class="add-milestone-form">
                    <h3>Add New Milestone</h3>
                    <form id="addMilestoneForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="milestoneName" class="form-label">Milestone Name</label>
                                <input type="text" class="form-control" id="milestoneName" name="name" required>
                            </div>
                            <div class="col-md-2">
                                <label for="startDate" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="startDate" name="start_date" required>
                            </div>
                            <div class="col-md-2">
                                <label for="endDate" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="endDate" name="end_date" required>
                            </div>
                            <div class="col-md-3">
                                <label for="responsibleUser" class="form-label">Responsible User</label>
                                <select class="form-select" id="responsibleUser" name="responsible_user" required>
                                    <option value="">Select User</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Add Milestone</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Gantt Chart -->
                <div class="gantt-container">
                    <canvas id="ganttChart"></canvas>
                </div>

                <!-- Milestone Details -->
                <div id="milestoneDetails">
                    <?php foreach ($ganttData as $milestone): ?>
                        <div class="milestone-details" data-id="<?php echo $milestone['milestone_id']; ?>">
                            <h5><?php echo htmlspecialchars($milestone['name']); ?></h5>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control start-date" value="<?php echo $milestone['start_date']; ?>" data-id="<?php echo $milestone['milestone_id']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control end-date" value="<?php echo $milestone['end_date']; ?>" data-id="<?php echo $milestone['milestone_id']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Responsible User</label>
                                    <select class="form-select responsible-user" data-id="<?php echo $milestone['milestone_id']; ?>">
                                        <option value="">Select User</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?php echo $user['user_id']; ?>" <?php echo $milestone['responsible_user'] == $user['user_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($user['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end gap-2">
                                    <button class="btn btn-primary save-btn" data-id="<?php echo $milestone['milestone_id']; ?>">Save</button>
                                    <div class="dropdown">
                                        <button class="action-menu-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><button class="dropdown-item delete-btn" data-id="<?php echo $milestone['milestone_id']; ?>"><i class="bi bi-trash"></i> Delete</button></li>
                                            <li><button class="dropdown-item feedback-btn" data-id="<?php echo $milestone['milestone_id']; ?>" data-bs-toggle="modal" data-bs-target="#feedbackModal"><i class="bi bi-chat-left-text"></i> Feedback</button></li>
                                            <?php if ($milestone['total_risks'] > 0): ?>
                                                <li><button class="dropdown-item view-risks-btn" data-id="<?php echo $milestone['milestone_id']; ?>" data-bs-toggle="modal" data-bs-target="#risksModal"><i class="bi bi-exclamation-triangle"></i> View Risks (<?php echo $milestone['total_risks']; ?>)</button></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Risks Modal -->
        <div class="modal fade" id="risksModal" tabindex="-1" aria-labelledby="risksModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="risksModalLabel">Risks</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6 class="mb-2">Dependencies</h6>
                        <ul class="list-group mb-3" id="depsList"></ul>
                        <h6 class="mb-2">Constraints</h6>
                        <ul class="list-group" id="constList"></ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback Modal -->
        <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="feedbackModalLabel">Feedback for Milestone</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="feedbackForm" class="mb-4">
                            <input type="hidden" id="feedbackMilestoneId" name="milestone_id">
                            <div class="mb-3">
                                <label for="feedbackComment" class="form-label">Add New Feedback</label>
                                <textarea class="form-control" id="feedbackComment" name="comment" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Feedback</button>
                        </form>
                        <h6 class="mb-2">Existing Feedback</h6>
                        <ul class="list-group" id="feedbackList"></ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Container -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="toastMessage" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto">Notification</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-gantt@1.2.1/dist/chartjs-chart-gantt.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        $(document).ready(function() {
            const toastEl = $('#toastMessage');
            const toast = new bootstrap.Toast(toastEl);

            // Function to show toast messages
            function showToast(message, isError = false) {
                toastEl.find('.toast-body').text(message);
                toastEl.find('.toast-header').removeClass('bg-success bg-danger').addClass(isError ? 'bg-danger' : 'bg-success');
                toast.show();
            }

            // Gantt Chart Data
            const ganttData = <?php echo json_encode($ganttData); ?>;
            const minDate = new Date('<?php echo $minDate ? $minDate->format('Y-m-d') : date('Y-m-d'); ?>');
            const maxDate = new Date('<?php echo $maxDate ? $maxDate->format('Y-m-d') : date('Y-m-d'); ?>');

            // Initialize Gantt Chart
            const ctx = document.getElementById('ganttChart').getContext('2d');
            const ganttChart = new Chart(ctx, {
                type: 'gantt',
                data: {
                    datasets: [{
                        label: 'Milestones',
                        data: ganttData.map(item => ({
                            x: [new Date(item.start_date), new Date(item.end_date)],
                            y: item.name,
                            milestone_id: item.milestone_id,
                            progress: item.progress
                        })),
                        backgroundColor: '#4361ee',
                        borderColor: '#4361ee',
                        borderWidth: 1,
                        barPercentage: 0.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'month',
                                displayFormats: {
                                    month: 'MMM YYYY'
                                }
                            },
                            min: minDate,
                            max: maxDate,
                            title: {
                                display: true,
                                text: 'Timeline'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Milestones'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const item = context.raw;
                                    return `Start: ${new Date(item.x[0]).toLocaleDateString()} - End: ${new Date(item.x[1]).toLocaleDateString()} (Progress: ${item.progress}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Function to refresh the Gantt chart and details
            function refreshGanttChart() {
                $.ajax({
                    url: 'master.php',
                    method: 'POST',
                    data: { action: 'get_milestones' },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            ganttChart.data.datasets[0].data = response.milestones.map(item => ({
                                x: [new Date(item.start_date), new Date(item.end_date)],
                                y: item.name,
                                milestone_id: item.milestone_id,
                                progress: item.progress
                            }));
                            ganttChart.update();

                            // Update milestone details
                            let detailsHtml = '';
                            response.milestones.forEach(item => {
                                detailsHtml += `
                                    <div class="milestone-details" data-id="${item.milestone_id}">
                                        <h5>${item.name}</h5>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Start Date</label>
                                                <input type="date" class="form-control start-date" value="${item.start_date}" data-id="${item.milestone_id}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">End Date</label>
                                                <input type="date" class="form-control end-date" value="${item.end_date}" data-id="${item.milestone_id}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Responsible User</label>
                                                <select class="form-select responsible-user" data-id="${item.milestone_id}">
                                                    <option value="">Select User</option>
                                                    <?php foreach ($users as $user): ?>
                                                        <option value="<?php echo $user['user_id']; ?>" ${item.responsible_user == <?php echo $user['user_id']; ?> ? 'selected' : ''}>
                                                            <?php echo htmlspecialchars($user['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 d-flex align-items-end gap-2">
                                                <button class="btn btn-primary save-btn" data-id="${item.milestone_id}">Save</button>
                                                <div class="dropdown">
                                                    <button class="action-menu-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><button class="dropdown-item delete-btn" data-id="${item.milestone_id}"><i class="bi bi-trash"></i> Delete</button></li>
                                                        <li><button class="dropdown-item feedback-btn" data-id="${item.milestone_id}" data-bs-toggle="modal" data-bs-target="#feedbackModal"><i class="bi bi-chat-left-text"></i> Feedback</button></li>
                                                        ${item.total_risks > 0 ? `<li><button class="dropdown-item view-risks-btn" data-id="${item.milestone_id}" data-bs-toggle="modal" data-bs-target="#risksModal"><i class="bi bi-exclamation-triangle"></i> View Risks (${item.total_risks})</button></li>` : ''}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                            });
                            $('#milestoneDetails').html(detailsHtml);
                        } else {
                            showToast(response.message, true);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error fetching milestones:', status, error, xhr.responseText);
                        showToast('Failed to fetch milestones', true);
                    }
                });
            }

            // Add new milestone via AJAX
            $('#addMilestoneForm').on('submit', function(e) {
                e.preventDefault();
                const formData = {
                    action: 'add_milestone',
                    name: $('#milestoneName').val(),
                    start_date: $('#startDate').val(),
                    end_date: $('#endDate').val(),
                    responsible_user: $('#responsibleUser').val()
                };

                $.ajax({
                    url: 'master.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#addMilestoneForm')[0].reset();
                            refreshGanttChart();
                            showToast(response.message);
                        } else {
                            showToast(response.message, true);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error adding milestone:', status, error, xhr.responseText);
                        showToast('Failed to add milestone', true);
                    }
                });
            });

            // Save milestone updates via AJAX
            $(document).on('click', '.save-btn', function() {
                const milestoneId = $(this).data('id');
                const details = $(`.milestone-details[data-id="${milestoneId}"]`);
                const startDate = details.find('.start-date').val();
                const endDate = details.find('.end-date').val();
                const responsibleUser = details.find('.responsible-user').val();

                $.ajax({
                    url: 'master.php',
                    method: 'POST',
                    data: {
                        action: 'update_milestone',
                        milestone_id: milestoneId,
                        start_date: startDate,
                        end_date: endDate,
                        responsible_user: responsibleUser
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            refreshGanttChart();
                            showToast(response.message);
                        } else {
                            showToast(response.message, true);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error updating milestone:', status, error, xhr.responseText);
                        showToast('Failed to update milestone', true);
                    }
                });
            });

            // Delete milestone via AJAX
            $(document).on('click', '.delete-btn', function() {
                const milestoneId = $(this).data('id');
                if (confirm('Are you sure you want to delete this milestone?')) {
                    $.ajax({
                        url: 'master.php',
                        method: 'POST',
                        data: { action: 'delete_milestone', milestone_id: milestoneId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                refreshGanttChart();
                                showToast(response.message);
                            } else {
                                showToast(response.message, true);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error deleting milestone:', status, error, xhr.responseText);
                            showToast('Failed to delete milestone', true);
                        }
                    });
                }
            });

            // View risks
            $(document).on('click', '.view-risks-btn', function() {
                const milestoneId = $(this).data('id');
                $.ajax({
                    url: 'master.php',
                    method: 'POST',
                    data: { action: 'get_risks', milestone_id: milestoneId },
                    dataType: 'json',
                    success: function(response) {
                        const depsList = $('#depsList');
                        const constList = $('#constList');
                        depsList.empty();
                        constList.empty();

                        if (response.dependencies && response.dependencies.length > 0) {
                            response.dependencies.forEach(dep => {
                                depsList.append(`<li class="list-group-item">${dep.description}</li>`);
                            });
                        } else {
                            depsList.append('<li class="list-group-item text-muted">No dependencies found.</li>');
                        }

                        if (response.constraints && response.constraints.length > 0) {
                            response.constraints.forEach(con => {
                                constList.append(`<li class="list-group-item">${con.description}</li>`);
                            });
                        } else {
                            constList.append('<li class="list-group-item text-muted">No constraints found.</li>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error fetching risks:', status, error, xhr.responseText);
                        showToast('Failed to fetch risks', true);
                    }
                });
            });

            // Add and view feedback
            $(document).on('click', '.feedback-btn', function() {
                const milestoneId = $(this).data('id');
                $('#feedbackMilestoneId').val(milestoneId);
                fetchFeedback(milestoneId);
            });

            $('#feedbackForm').on('submit', function(e) {
                e.preventDefault();
                const formData = {
                    action: 'add_feedback',
                    milestone_id: $('#feedbackMilestoneId').val(),
                    comment: $('#feedbackComment').val()
                };

                $.ajax({
                    url: 'master.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#feedbackComment').val('');
                            fetchFeedback(formData.milestone_id);
                            showToast(response.message);
                        } else {
                            showToast(response.message, true);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error adding feedback:', status, error, xhr.responseText);
                        showToast('Failed to add feedback', true);
                    }
                });
            });

            // Fetch feedback for the modal
            function fetchFeedback(milestoneId) {
                $.ajax({
                    url: 'master.php',
                    method: 'POST',
                    data: { action: 'get_feedback', milestone_id: milestoneId },
                    success: function(data) {
                        $('#feedbackList').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error fetching feedback:', status, error, xhr.responseText);
                        showToast('Failed to fetch feedback', true);
                    }
                });
            }

            // Delete feedback
            $(document).on('click', '.delete-feedback-btn', function() {
                const feedbackId = $(this).data('id');
                const milestoneId = $(this).data('milestone-id');
                if (confirm('Are you sure you want to delete this feedback?')) {
                    $.ajax({
                        url: 'master.php',
                        method: 'POST',
                        data: { action: 'delete_feedback', feedback_id: feedbackId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                fetchFeedback(milestoneId);
                                showToast(response.message);
                            } else {
                                showToast(response.message, true);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error deleting feedback:', status, error, xhr.responseText);
                            showToast('Failed to delete feedback', true);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html> 