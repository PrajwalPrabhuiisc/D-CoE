<?php
include 'config.php';

header('Content-Type: application/json');

if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    
    $stmt = $pdo->prepare("
        SELECT TaskID, TaskName 
        FROM ProjectTasks 
        WHERE OwnerID = ? 
        AND Status != 'Completed'
        ORDER BY TaskName
    ");
    $stmt->execute([$userId]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['tasks' => $tasks]);
} else {
    echo json_encode(['error' => 'No user ID provided']);
}
?>