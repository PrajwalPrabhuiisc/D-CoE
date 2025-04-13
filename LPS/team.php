<?php
session_start();
require 'db_connect.php';

// Redirect if not SA_Team
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'SA_Team') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch team
$stmt = $conn->prepare("SELECT assigned_team FROM tasks WHERE created_by = ? LIMIT 1");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$team_result = $stmt->get_result()->fetch_assoc();
$team = $team_result['assigned_team'] ?? 'Unassigned';
$stmt->close();
if ($team === 'Unassigned') {
    error_log("No team found for user_id: $user_id");
}

// Fetch tasks (next 4 weeks)
$stmt = $conn->prepare("SELECT t.*, m.name AS milestone_name 
                        FROM tasks t 
                        JOIN milestones m ON t.milestone_id = m.milestone_id 
                        WHERE t.assigned_team = ? AND t.start_date <= DATE_ADD(CURDATE(), INTERVAL 4 WEEK)");
$stmt->bind_param('s', $team);
$stmt->execute();
$tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
if (empty($tasks)) {
    error_log("No tasks for team: $team, date <= " . date('Y-m-d', strtotime('+4 weeks')));
}

// Fetch weekly plans (current week)
$week_start = date('Y-m-d', strtotime('monday this week'));
error_log("Week start: $week_start");
$stmt = $conn->prepare("SELECT w.*, t.task_name 
                        FROM weekly_plans w 
                        JOIN tasks t ON w.task_id = t.task_id 
                        WHERE t.assigned_team = ? AND w.week_start_date = ?");
$stmt->bind_param('ss', $team, $week_start);
$stmt->execute();
$weekly = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
if (empty($weekly)) {
    error_log("No weekly plans for team: $team, week: $week_start");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SA Team Dashboard - LPS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        .sidebar {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.1);
            padding: 20px;
            max-height: 400px;
            position: sticky;
            top: 15px;
        }
        .team-info {
            background: linear-gradient(135deg, #6366F1, #14B8A6);
            color: white;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        .team-info span {
            font-weight: 700;
        }
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
        .btn-action {
            background: linear-gradient(90deg, #6366F1, #14B8A6);
            border: none;
            border-radius: 50px;
            padding: 0.4rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
            transition: transform 0.3s ease;
        }
        .btn-action:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(20, 184, 166, 0.3);
        }
        .revision-preview {
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
        .empty-state {
            text-align: center;
            padding: 20px;
            color: #64748B;
            font-size: 0.9rem;
        }
        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }
        .pop {
            animation: pop 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pop {
            0% { transform: scale(0.8); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        @media (max-width: 992px) {
            .sidebar {
                position: static;
                max-height: 300px;
                margin-bottom: 15px;
            }
            .team-info {
                font-size: 1rem;
            }
        }
        @media (max-width: 576px) {
            body {
                padding: 10px;
            }
            .card-header {
                font-size: 1rem;
            }
            .table, .revision-preview {
                font-size: 0.75rem;
                max-width: 80px;
            }
            .btn-action {
                font-size: 0.7rem;
                padding: 0.3rem 0.8rem;
            }
            .team-info {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container fade-in">
        <div class="row g-3">
            <!-- Sidebar: Team Info -->
            <div class="col-lg-3 col-md-4">
                <div class="sidebar pop">
                    <div class="team-info">
                        Team: <span><?php echo htmlspecialchars($team); ?></span>
                    </div>
                    <h5 class="fw-bold mb-3 text-center text-primary">Filters</h5>
                    <div class="text-center text-muted small">
                        (Filters coming soon)
                    </div>
                </div>
            </div>

            <!-- Main Content: Tasks & Weekly Plans -->
            <div class="col-lg-9 col-md-8 main-content">
                <!-- Header -->
                <div class="card mb-3 pop">
                    <div class="card-header text-center">SA Team Dashboard</div>
                    <div class="card-body p-3">
                        <p class="text-muted text-center mb-0">Collaborate on your lookahead and weekly plans</p>
                    </div>
                </div>

                <!-- Lookahead Plan -->
                <div class="card">
                    <div class="card-header" data-bs-toggle="collapse" data-bs-target="#lookaheadCollapse" aria-expanded="true">
                        Lookahead Plan (Next 4 Weeks)
                    </div>
                    <div class="collapse show" id="lookaheadCollapse">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Milestone</th>
                                            <th>Dates</th>
                                            <th>Feedback</th>
                                            <th>Revisions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($tasks): ?>
                                            <?php foreach ($tasks as $t): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($t['task_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($t['milestone_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($t['start_date'] . ' - ' . $t['end_date']); ?></td>
                                                    <td>
                                                        <a href="submit_feedback.php?task_id=<?php echo $t['task_id']; ?>" class="btn-action">Submit</a>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $revision_query = $conn->query("SELECT * FROM revisions WHERE task_id = {$t['task_id']} ORDER BY revision_id DESC LIMIT 1");
                                                        if ($revision_query === false) {
                                                            error_log("Revision query error for task_id {$t['task_id']}: " . $conn->error);
                                                            echo '<span class="text-muted">Error</span>';
                                                        } else {
                                                            $revisions = $revision_query->fetch_assoc();
                                                            if ($revisions) {
                                                                $preview = htmlspecialchars($revisions['reason']);
                                                                echo '<span class="revision-preview" data-bs-toggle="tooltip" title="' . $preview . '">' . 
                                                                     substr($preview, 0, 15) . (strlen($preview) > 15 ? '...' : '') . '</span>';
                                                            } else {
                                                                echo '<span class="text-muted">None</span>';
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="empty-state">No tasks scheduled for the next 4 weeks.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Weekly Plan -->
                <div class="card">
                    <div class="card-header" data-bs-toggle="collapse" data-bs-target="#weeklyCollapse" aria-expanded="true">
                        Weekly Plan (<?php echo htmlspecialchars($week_start); ?>)
                    </div>
                    <div class="collapse show" id="weeklyCollapse">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Week</th>
                                            <th>Commit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($weekly): ?>
                                            <?php foreach ($weekly as $w): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($w['task_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($w['week_start_date']); ?></td>
                                                    <td>
                                                        <a href="commit_plan.php?weekly_plan_id=<?php echo $w['weekly_plan_id']; ?>" class="btn-action">Commit</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="empty-state">No plans for this week.</td>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    </script>
</body>
</html>