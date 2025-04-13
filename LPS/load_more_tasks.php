<?php
session_start();

// Suppress direct output; errors go to log
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Set JSON header
header('Content-Type: application/json');

require 'db_connect.php';

// Ensure Planner role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Planner') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized']));
}

// Get offset (default to 0 if invalid)
$offset = isset($_POST['offset']) && is_numeric($_POST['offset']) ? (int)$_POST['offset'] : 0;
$limit = 5;

// Fetch tasks
try {
    $query = "SELECT t.*, m.name AS milestone_name 
              FROM tasks t 
              JOIN milestones m ON t.milestone_id = m.milestone_id 
              LIMIT ?, ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        exit(json_encode(['error' => 'Database error']));
    }
    $stmt->bind_param('ii', $offset, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Build HTML for rows
    $output = [];
    foreach ($tasks as $t) {
        // Fetch feedback
        $feedback_result = $conn->query("SELECT * FROM feedback WHERE task_id = {$t['task_id']} LIMIT 1");
        if ($feedback_result === false) {
            error_log("Feedback query failed for task_id {$t['task_id']}: " . $conn->error);
            $feedback_html = '<span class="text-muted">Error</span>';
        } else {
            $feedback = $feedback_result->fetch_assoc();
            $feedback_html = $feedback 
                ? '<span class="feedback-preview" data-bs-toggle="tooltip" title="' . htmlspecialchars($feedback['comments']) . '">' . 
                  htmlspecialchars($feedback['feasibility'] . ': ' . substr($feedback['comments'], 0, 15) . (strlen($feedback['comments']) > 15 ? '...' : '')) . '</span>'
                : '<span class="text-muted">None</span>';
        }

        // Fetch revision
        $revision_result = $conn->query("SELECT * FROM revisions WHERE task_id = {$t['task_id']} ORDER BY revision_id DESC LIMIT 1");
        if ($revision_result === false) {
            error_log("Revision query failed for task_id {$t['task_id']}: " . $conn->error);
            $revision_html = '<span class="text-muted">Error</span>';
        } else {
            $revision = $revision_result->fetch_assoc();
            $revision_html = $revision 
                ? '<span class="revision-preview" data-bs-toggle="tooltip" title="Reason: ' . htmlspecialchars($revision['reason']) . '">' . 
                  htmlspecialchars('To ' . $revision['new_data']) . '</span>'
                : '<span class="text-muted">None</span>';
        }

        $output[] = [
            'html' => '
                <tr class="fade-in-row">
                    <td>' . htmlspecialchars($t['task_name']) . '</td>
                    <td>' . htmlspecialchars($t['milestone_name']) . '</td>
                    <td>' . htmlspecialchars($t['start_date'] . ' - ' . $t['end_date']) . '</td>
                    <td>' . htmlspecialchars($t['assigned_team']) . '</td>
                    <td>' . $feedback_html . '</td>
                    <td>' . $revision_html . '</td>
                    <td><a href="revise_task.php?task_id=' . $t['task_id'] . '" class="btn btn-warning btn-sm">Revise</a></td>
                </tr>'
        ];
    }

    // Check if more tasks exist
    $total_tasks = $conn->query("SELECT COUNT(*) as count FROM tasks")->fetch_assoc()['count'];
    $has_more = ($offset + $limit) < $total_tasks;

    echo json_encode([
        'tasks' => $output,
        'has_more' => $has_more
    ]);
} catch (Exception $e) {
    error_log("Exception in load_more_tasks.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
?>