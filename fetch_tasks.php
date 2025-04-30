<?php
include 'config.php';

header('Content-Type: application/json');

try {
    $user_id = $_POST['user_id'] ?? null;
    if (!$user_id) {
        error_log("fetch_tasks.php: No user_id provided");
        echo json_encode(['error' => 'User ID is required']);
        exit;
    }

    // Verify user exists
    $stmt = $pdo->prepare("SELECT UserID, Username FROM Users WHERE UserID = ? AND Role IN ('Team Member')");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        error_log("fetch_tasks.php: Invalid user_id=$user_id");
        echo json_encode(['error' => 'Invalid user']);
        exit;
    }

    // Fetch tasks for the user
    $stmt = $pdo->prepare("
        SELECT TaskID, TaskName 
        FROM ProjectTasks 
        WHERE OwnerID = ? AND Status IN ('Pending', 'Active')
        ORDER BY TaskName
    ");
    $stmt->execute([$user_id]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log("fetch_tasks.php: Fetched " . count($tasks) . " tasks for user_id=$user_id (" . $user['Username'] . ")");
    echo json_encode(['tasks' => $tasks]);
} catch (PDOException $e) {
    error_log("fetch_tasks.php: Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
}
?>
