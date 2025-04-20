<?php
include 'config.php';
header('Content-Type: application/json');

$taskName = $_POST['task_name'];
$projectId = $_POST['project_id'];
$ownerId = $_POST['owner_id'];
$priority = $_POST['priority'];
$status = $_POST['status'];
$peopleDependencies = !empty($_POST['people_dependencies']) ? trim($_POST['people_dependencies']) : null;

$stmt = $pdo->prepare("INSERT INTO ProjectTasks (TaskName, ProjectID, OwnerID, Status, Priority, PeopleDependencies) 
                       VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$taskName, $projectId, $ownerId, $status, $priority, $peopleDependencies]);

echo json_encode(['success' => true]);
?>
