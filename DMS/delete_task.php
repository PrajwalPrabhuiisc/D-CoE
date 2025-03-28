<?php
include 'config.php';
header('Content-Type: application/json');

$taskID = $_POST['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM ProjectTasks WHERE TaskID = ?");
    $stmt->execute([$taskID]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>