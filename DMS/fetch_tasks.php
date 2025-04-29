<?php
include 'config.php';

$user_id = $_POST['user_id'] ?? null;

if ($user_id) {
    try {
        // Modified query to exclude completed tasks
        $stmt = $pdo->prepare("
            SELECT TaskID, TaskName 
            FROM projecttasks 
            WHERE OwnerID = ? AND Status != 'Completed'
        ");
        $stmt->execute([$user_id]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['tasks' => $tasks]);
    } catch (PDOException $e) {
        error_log("Error fetching tasks: " . $e->getMessage());
        echo json_encode(['tasks' => []]);
    }
} else {
    echo json_encode(['tasks' => []]);
}
?>
