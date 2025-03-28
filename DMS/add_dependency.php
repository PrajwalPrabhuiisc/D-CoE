<?php
include 'config.php';
header('Content-Type: application/json');

$taskID = $_POST['task_id'];
$predecessorID = $_POST['predecessor_id'];

try {
    $stmt = $pdo->prepare("INSERT INTO TaskDependencies (TaskID, PredecessorID) VALUES (?, ?)");
    $stmt->execute([$taskID, $predecessorID]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>