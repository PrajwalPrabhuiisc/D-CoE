<?php
include 'config.php';
header('Content-Type: application/json');

$taskID = $_GET['id'];
$stmt = $pdo->prepare("SELECT TaskID, TaskName, ProjectID, OwnerID, Status, Priority 
                      FROM ProjectTasks WHERE TaskID = ?");
$stmt->execute([$taskID]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($task);
?>
