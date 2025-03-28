<?php
include 'config.php';
header('Content-Type: application/json');

$taskID = $_POST['task_id'];
$taskName = $_POST['task_name'];
$projectID = $_POST['project_id'];
$ownerID = $_POST['owner_id'];
$status = $_POST['status'];
$priority = $_POST['priority'];

try {
    $stmt = $pdo->prepare("UPDATE ProjectTasks SET TaskName = ?, ProjectID = ?, OwnerID = ?, Status = ?, Priority = ? 
                          WHERE TaskID = ?");
    $stmt->execute([$taskName, $projectID, $ownerID, $status, $priority, $taskID]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>