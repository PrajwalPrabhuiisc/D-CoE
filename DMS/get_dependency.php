<?php
include 'config.php';
header('Content-Type: application/json');

$dependencyID = $_GET['id'];
$stmt = $pdo->prepare("
    SELECT 
        td.DependencyID, 
        td.TaskID, 
        t1.OwnerID AS TaskOwnerID, 
        td.PredecessorID, 
        t2.OwnerID AS PredecessorOwnerID, 
        t1.ProjectID, 
        t1.Status
    FROM TaskDependencies td 
    JOIN ProjectTasks t1 ON td.TaskID = t1.TaskID 
    JOIN ProjectTasks t2 ON td.PredecessorID = t2.TaskID 
    WHERE td.DependencyID = ?
");
$stmt->execute([$dependencyID]);
$dependency = $stmt->fetch(PDO::FETCH_ASSOC);

if ($dependency) {
    echo json_encode($dependency);
} else {
    echo json_encode(['error' => 'Dependency not found']);
}
?>