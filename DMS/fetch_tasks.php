<?php
include 'config.php';

// Set proper headers for JSON response
header('Content-Type: application/json');

// Get user ID from POST request
$userId = $_POST['user_id'] ?? null;

// Response array
$response = [
    'success' => false,
    'tasks' => []
];

if (!$userId) {
    $response['message'] = 'User ID is required';
    echo json_encode($response);
    exit;
}

try {
    // Prepare and execute query to get tasks assigned to the user
    $stmt = $pdo->prepare("
        SELECT t.TaskID, t.TaskName, t.Description, t.Priority, p.ProjectName 
        FROM projecttasks t
        LEFT JOIN projects p ON t.ProjectID = p.ProjectID
        WHERE t.OwnerID = ?
        ORDER BY t.Priority DESC, t.TaskName ASC
    ");
    
    $stmt->execute([$userId]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add tasks to response
    $response['success'] = true;
    $response['tasks'] = $tasks;
    
} catch (PDOException $e) {
    error_log("Database error in fetch_tasks.php: " . $e->getMessage());
    $response['message'] = 'Error fetching tasks';
}

echo json_encode($response);
exit;
?>
