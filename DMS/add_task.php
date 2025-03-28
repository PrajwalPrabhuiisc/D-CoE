<?php
include 'config.php';
header('Content-Type: application/json');

$taskName = $_POST['task_name'];
$projectId = $_POST['project_id'];
$ownerId = $_POST['owner_id'];
$priority = $_POST['priority'];

$stmt = $pdo->prepare("INSERT INTO ProjectTasks (TaskName, ProjectID, OwnerID, Status, Priority) 
                       VALUES (?, ?, ?, 'Pending', ?)");
$stmt->execute([$taskName, $projectId, $ownerId, $priority]);

echo json_encode(['success' => true]);
?>