<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_POST['action'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Action not specified']);
    exit;
}

try {
    if ($_POST['action'] === 'get_lookahead') {
        $week = (int)$_POST['week'];

        // Define the 3-week lookahead range
        $start_week = $week;
        $end_week = $week + 2;

        // Fetch activities within the 3-week lookahead, including handoffs
        $stmt = $pdo->prepare("
            SELECT a.activity_id, a.description, a.week_number, a.status, a.constraint_flag, 
                   a.actual_end, h.description AS handoff
            FROM activities a
            LEFT JOIN handoffs h ON a.phase_id = h.phase_id
            WHERE a.week_number BETWEEN ? AND ?
            ORDER BY a.week_number, a.activity_id
        ");
        $stmt->execute([$start_week, $end_week]);
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate PPC (Percent Plan Complete)
        $total_tasks = count($activities);
        $completed_tasks = array_filter($activities, fn($a) => $a['status'] === 'Completed' && $a['actual_end']);
        $ppc = $total_tasks > 0 ? round((count($completed_tasks) / $total_tasks) * 100) : 0;

        // Return JSON data for the timeline
        echo json_encode([
            'success' => true,
            'activities' => $activities,
            'ppc' => $ppc
        ]);

    } elseif ($_POST['action'] === 'get_feedback') {
        $whereClause = '';
        $params = [];

        if (!empty($_POST['activity_id'])) {
            $whereClause .= ' WHERE f.activity_id = ?';
            $params[] = (int)$_POST['activity_id'];
        }

        if (!empty($_POST['submitted_by'])) {
            $whereClause .= (empty($whereClause) ? ' WHERE' : ' AND') . ' f.submitted_by = ?';
            $params[] = (int)$_POST['submitted_by'];
        }

        $stmt = $pdo->prepare("
            SELECT f.feedback_id, f.activity_id, f.comment, f.submitted_by, f.submission_date, 
                   a.description AS activity_name, u.name AS user_name
            FROM feedback f
            JOIN activities a ON f.activity_id = a.activity_id
            JOIN users u ON f.submitted_by = u.user_id
            $whereClause
            ORDER BY f.submission_date DESC
        ");
        $stmt->execute($params);

        $rows = '';
        $rowCount = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rowCount++;
            $rows .= "
                <tr data-id='{$row['feedback_id']}'>
                    <td>{$row['feedback_id']}</td>
                    <td>" . htmlspecialchars($row['activity_name']) . "</td>
                    <td>" . htmlspecialchars($row['comment']) . "</td>
                    <td>" . htmlspecialchars($row['user_name']) . "</td>
                    <td>" . (new DateTime($row['submission_date']))->format('Y-m-d H:i:s') . "</td>
                    <td>
                        <button class='btn btn-link btn-sm delete-feedback-btn' data-id='{$row['feedback_id']}'>
                            <i class='bi bi-trash text-danger'></i>
                        </button>
                    </td>
                </tr>";
        }
        if ($rowCount === 0) {
            $rows = "<tr><td colspan='6' class='text-center text-muted'>No feedback available</td></tr>";
        }
        echo $rows;

    } elseif ($_POST['action'] === 'delete_feedback') {
        $feedback_id = (int)$_POST['feedback_id'];
        $stmt = $pdo->prepare("DELETE FROM feedback WHERE feedback_id = ?");
        $stmt->execute([$feedback_id]);
        echo json_encode(['success' => true, 'message' => 'Feedback deleted successfully']);

    } elseif ($_POST['action'] === 'get_revisions') {
        $activity_id = (int)$_POST['activity_id'];
        $stmt = $pdo->prepare("
            SELECT r.revision_id, r.old_status, r.new_status, r.changed_by, r.changed_at, u.name AS changed_by_name
            FROM revisions r
            JOIN users u ON r.changed_by = u.user_id
            WHERE r.activity_id = ?
            ORDER BY r.changed_at DESC
        ");
        $stmt->execute([$activity_id]);
        $revisions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $rows = '';
        if (empty($revisions)) {
            $rows = "<tr><td colspan='5' class='text-center text-muted'>No revisions available</td></tr>";
        } else {
            foreach ($revisions as $revision) {
                $rows .= "
                    <tr>
                        <td>{$revision['revision_id']}</td>
                        <td>" . htmlspecialchars($revision['old_status']) . "</td>
                        <td>" . htmlspecialchars($revision['new_status']) . "</td>
                        <td>" . htmlspecialchars($revision['changed_by_name']) . "</td>
                        <td>" . (new DateTime($revision['changed_at']))->format('Y-m-d H:i:s') . "</td>
                    </tr>";
            }
        }
        echo $rows;

    } elseif ($_POST['action'] === 'get_weekly') {
        // Keep your original get_weekly logic for compatibility
        $week = (int)$_POST['week'];
        $milestone_id = isset($_POST['milestone_id']) ? (int)$_POST['milestone_id'] : null;

        $year = date('Y');
        $start_of_week = new DateTime();
        $start_of_week->setISODate($year, $week);
        $start_date = $start_of_week->format('Y-m-d');
        $end_of_week = clone $start_of_week;
        $end_of_week->modify('+6 days');
        $end_date = $end_of_week->format('Y-m-d');

        $whereClause = "WHERE m.start_date <= ? AND m.end_date >= ?";
        $params = [$end_date, $start_date];

        if ($milestone_id) {
            $whereClause .= " AND m.milestone_id = ?";
            $params[] = $milestone_id;
        }

        $stmt = $pdo->prepare("
            SELECT a.activity_id, a.description, a.status
            FROM activities a
            JOIN phases p ON a.phase_id = p.phase_id
            JOIN milestones m ON p.milestone_id = m.milestone_id
            $whereClause
            ORDER BY a.activity_id
        ");
        $stmt->execute($params);
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_tasks = count($activities);
        $completed_tasks = array_filter($activities, fn($a) => $a['status'] === 'Completed');
        $ppc = $total_tasks > 0 ? round((count($completed_tasks) / $total_tasks) * 100) : 0;

        $tasks = '';
        if ($total_tasks === 0) {
            $tasks = "<tr><td colspan='6' class='text-center text-muted'>No tasks available for this week</td></tr>";
        } else {
            foreach ($activities as $activity) {
                $tasks .= "
                    <tr data-id='{$activity['activity_id']}'>
                        <td>{$activity['activity_id']}</td>
                        <td>" . htmlspecialchars($activity['description']) . "</td>
                        <td>" . htmlspecialchars($activity['status'] ?: 'Not Started') . "</td>
                        <td>
                            <select class='form-select status-select' data-id='{$activity['activity_id']}'>
                                <option value='Not Started' " . ($activity['status'] === 'Not Started' ? 'selected' : '') . ">Not Started</option>
                                <option value='In Progress' " . ($activity['status'] === 'In Progress' ? 'selected' : '') . ">In Progress</option>
                                <option value='Completed' " . ($activity['status'] === 'Completed' ? 'selected' : '') . ">Completed</option>
                            </select>
                        </td>
                        <td>
                            <button class='btn btn-link btn-sm add-feedback-btn' data-id='{$activity['activity_id']}' data-bs-toggle='modal' data-bs-target='#feedbackModal'>
                                <i class='bi bi-chat-left-text text-primary'></i>
                            </button>
                        </td>
                        <td>
                            <button class='btn btn-link btn-sm view-revisions-btn' data-id='{$activity['activity_id']}' data-bs-toggle='modal' data-bs-target='#revisionsModal'>
                                <i class='bi bi-clock-history text-secondary'></i>
                            </button>
                        </td>
                    </tr>";
            }
        }

        echo json_encode([
            'tasks' => $tasks,
            'ppc' => $ppc
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    error_log('Error in ajax_handler.php: ' . $e->getMessage());
}
exit;
?>