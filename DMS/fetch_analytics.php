<?php
include 'config.php';
header('Content-Type: application/json');

$days = isset($_GET['days']) ? $_GET['days'] : '7';
$whereClause = '';
if ($days !== 'all') {
    $days = (int)$days;
    $whereClause = " WHERE EntryDate >= DATE_SUB(CURDATE(), INTERVAL $days DAY)";
}

// Task Status Distribution (WorkDiary)
$stmt = $pdo->query("SELECT TaskStatus, COUNT(*) as count FROM WorkDiary $whereClause GROUP BY TaskStatus");
$taskStatus = ['labels' => [], 'data' => []];
while ($row = $stmt->fetch()) {
    $taskStatus['labels'][] = $row['TaskStatus'];
    $taskStatus['data'][] = $row['count'];
}

// Project Task Distribution
$stmt = $pdo->query("SELECT p.ProjectName, COUNT(pt.TaskID) as count 
                    FROM Projects p 
                    LEFT JOIN ProjectTasks pt ON p.ProjectID = pt.ProjectID 
                    GROUP BY p.ProjectID, p.ProjectName");
$projectDistribution = ['labels' => [], 'data' => []];
while ($row = $stmt->fetch()) {
    $projectDistribution['labels'][] = $row['ProjectName'];
    $projectDistribution['data'][] = $row['count'];
}

// Weekly Activity
$weeklyActivity = ['labels' => [], 'entries' => [], 'observations' => []];
$limit = ($days === 'all') ? 30 : min($days, 7); // Cap at 30 days for "all" to keep chart readable
for ($i = $limit - 1; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $weeklyActivity['labels'][] = date('D', strtotime($date));
}
$stmt = $pdo->query("SELECT DATE(EntryDate) as date, COUNT(*) as count 
                    FROM WorkDiary $whereClause 
                    GROUP BY DATE(EntryDate)");
$entryCounts = [];
while ($row = $stmt->fetch()) {
    $entryCounts[$row['date']] = $row['count'];
}
$stmt = $pdo->query("SELECT DATE(ObservationDate) as date, COUNT(*) as count 
                    FROM SATeamObservations $whereClause 
                    GROUP BY DATE(ObservationDate)");
$obsCounts = [];
while ($row = $stmt->fetch()) {
    $obsCounts[$row['date']] = $row['count'];
}
foreach ($weeklyActivity['labels'] as $i => $label) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $weeklyActivity['entries'][] = isset($entryCounts[$date]) ? $entryCounts[$date] : 0;
    $weeklyActivity['observations'][] = isset($obsCounts[$date]) ? $obsCounts[$date] : 0;
}

// User Performance (Tasks Completed from WorkDiary)
$stmt = $pdo->query("SELECT u.Username, COUNT(w.EntryID) as count 
                    FROM Users u 
                    LEFT JOIN WorkDiary w ON u.UserID = w.UserID AND w.TaskStatus = 'Completed' $whereClause 
                    GROUP BY u.UserID, u.Username");
$userPerformance = ['labels' => [], 'data' => []];
while ($row = $stmt->fetch()) {
    $userPerformance['labels'][] = $row['Username'];
    $userPerformance['data'][] = $row['count'];
}

// Time Deviation Trend
$stmt = $pdo->query("SELECT DATE(EntryDate) as date, AVG(ABS(ActualTime - AllocatedTime)) as deviation 
                    FROM WorkDiary 
                    WHERE ActualTime IS NOT NULL $whereClause 
                    GROUP BY DATE(EntryDate)");
$timeDeviation = ['labels' => [], 'data' => []];
while ($row = $stmt->fetch()) {
    $timeDeviation['labels'][] = date('D', strtotime($row['date']));
    $timeDeviation['data'][] = round($row['deviation'], 2);
}

echo json_encode([
    'taskStatus' => $taskStatus,
    'projectDistribution' => $projectDistribution,
    'weeklyActivity' => $weeklyActivity,
    'userPerformance' => $userPerformance,
    'timeDeviation' => $timeDeviation
]);
?>