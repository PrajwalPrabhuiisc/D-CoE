<?php
include 'config.php';
header('Content-Type: application/json');

$dependencyID = $_POST['dependency_id'];
$taskID = $_POST['task_id'];
$taskOwnerID = $_POST['task_owner_id'];
$predecessorID = $_POST['predecessor_id'];
$predecessorOwnerID = $_POST['predecessor_owner_id'];
$projectID = $_POST['project_id'];
$status = $_POST['status'];

try {
    // Start transaction to update both tables atomically
    $pdo->beginTransaction();

    // Update TaskDependencies
    $stmt = $pdo->prepare("UPDATE TaskDependencies SET TaskID = ?, PredecessorID = ? WHERE DependencyID = ?");
    $stmt->execute([$taskID, $predecessorID, $dependencyID]);

    // Update ProjectTasks for the dependent task (TaskID)
    $stmt = $pdo->prepare("UPDATE ProjectTasks SET OwnerID = ?, ProjectID = ?, Status = ? WHERE TaskID = ?");
    $stmt->execute([$taskOwnerID, $projectID, $status, $taskID]);

    // Update ProjectTasks for the predecessor task (PredecessorID)
    $stmt = $pdo->prepare("UPDATE ProjectTasks SET OwnerID = ? WHERE TaskID = ?");
    $stmt->execute([$predecessorOwnerID, $predecessorID]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>