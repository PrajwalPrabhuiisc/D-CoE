<?php
include 'config.php';
header('Content-Type: application/json');

$taskId = $_POST['task_id'];
$status = $_POST['status'];

$stmt = $pdo->prepare("UPDATE ProjectTasks SET Status = ? WHERE TaskID = ?");
$stmt->execute([$status, $taskId]);

$counts = [];
foreach (['Pending', 'Active', 'Completed'] as $s) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM ProjectTasks WHERE Status = ?");
    $stmt->execute([$s]);
    $counts[$s] = $stmt->fetch()['count'];
}

echo json_encode($counts);
?>