<?php
include 'config.php';
header('Content-Type: application/json');

try {
    // Fetch tasks
    $stmt = $pdo->query("SELECT TaskID, TaskName, Status FROM ProjectTasks");
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch dependencies
    $stmt = $pdo->query("SELECT TaskID, PredecessorID FROM TaskDependencies");
    $dependencies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'tasks' => $tasks,
        'dependencies' => $dependencies
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>