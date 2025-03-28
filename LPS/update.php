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
    if ($_POST['action'] === 'update_status') {
        $activity_id = (int)$_POST['activity_id'];
        $new_status = $_POST['status'];

        // Get the current status
        $stmt = $pdo->prepare("SELECT status FROM activities WHERE activity_id = ?");
        $stmt->execute([$activity_id]);
        $current_status = $stmt->fetchColumn();

        // Update the activity status and actual_end if completed
        $actual_end = ($new_status === 'Completed') ? date('Y-m-d') : null;
        $stmt = $pdo->prepare("UPDATE activities SET status = ?, actual_end = ? WHERE activity_id = ?");
        $stmt->execute([$new_status, $actual_end, $activity_id]);

        // Log the status change in the revisions table
        $stmt = $pdo->prepare("INSERT INTO revisions (activity_id, old_status, new_status, changed_by, changed_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$activity_id, $current_status, $new_status, $_SESSION['user_id']]);

        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);

    } elseif ($_POST['action'] === 'toggle_constraint') {
        $activity_id = (int)$_POST['activity_id'];
        $constraint_status = (int)$_POST['constraint_status'];
        $comment = $_POST['comment'];

        // Update the constraint flag
        $stmt = $pdo->prepare("UPDATE activities SET constraint_flag = ?, last_updated_by = ? WHERE activity_id = ?");
        $stmt->execute([$constraint_status, $_SESSION['user_id'], $activity_id]);

        // Insert feedback
        $stmt = $pdo->prepare("INSERT INTO feedback (activity_id, comment, submitted_by, submission_date) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$activity_id, $comment, $_SESSION['user_id']]);

        // Log revision (for constraint change)
        $old_constraint = $constraint_status ? 0 : 1; // Opposite of new status
        $stmt = $pdo->prepare("INSERT INTO revisions (activity_id, old_status, new_status, changed_by, changed_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$activity_id, "Constraint: $old_constraint", "Constraint: $constraint_status", $_SESSION['user_id']]);

        echo json_encode(['success' => true, 'message' => 'Constraint updated successfully']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    error_log('Error in update.php: ' . $e->getMessage());
}
exit;
?>