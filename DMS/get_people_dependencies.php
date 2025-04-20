<?php
include 'config.php';

// Get task_id from query parameter, default to null for all tasks
$taskId = isset($_GET['task_id']) ? (int)$_GET['task_id'] : null;

$query = "
    SELECT TaskID, TaskName, OwnerID, PeopleDependencies 
    FROM ProjectTasks 
    WHERE PeopleDependencies IS NOT NULL AND PeopleDependencies != ''
";
if ($taskId) {
    $query .= " AND TaskID = :task_id";
}

$stmt = $pdo->prepare($query);
if ($taskId) {
    $stmt->bindParam(':task_id', $taskId, PDO::PARAM_INT);
}
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize arrays for Cytoscape elements
$elements = [
    'tasks' => [],
    'people' => [],
    'dependencies' => []
];

// Process each task
foreach ($tasks as $task) {
    $taskId = $task['TaskID'];
    $elements['tasks'][] = [
        'TaskID' => $taskId,
        'TaskName' => $task['TaskName']
    ];

    // Split people dependencies (comma-separated)
    $people = array_map('trim', explode(',', $task['PeopleDependencies']));
    foreach ($people as $person) {
        // Clean person name (remove UserID or extra spaces if present)
        $person = preg_replace('/\d+/', '', trim($person)); // Remove numbers (e.g., UserID)
        if (!in_array($person, $elements['people'])) {
            $elements['people'][] = $person;
        }
        $elements['dependencies'][] = [
            'TaskID' => $taskId,
            'people' => $person
        ];
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($elements);
?>
