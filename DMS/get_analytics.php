<?php
include 'config.php';
header('Content-Type: application/json');

// Get filter parameters
$timeFilter = isset($_GET['time']) ? $_GET['time'] : 'all';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$userFilter = isset($_GET['user']) ? $_GET['user'] : 'all';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$pageSize = isset($_GET['pageSize']) ? (int)$_GET['pageSize'] : 30;

// Build WHERE clauses for filters
$whereClauses = [];
$timeClause = '';
if ($timeFilter === '7days') {
    $timeClause = "DATE(EntryDate) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($timeFilter === '30days') {
    $timeClause = "DATE(EntryDate) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
}

$statusClause = $statusFilter !== 'all' ? "TaskStatus = :status" : '';
$userClause = $userFilter !== 'all' ? "w.UserID = :userID" : '';

if ($timeClause) $whereClauses[] = $timeClause;
if ($statusClause) $whereClauses[] = $statusClause;
if ($userClause) $whereClauses[] = $userClause;

$whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Define color mapping for TaskStatus
$statusColors = [
    'Not Started' => ['background' => 'rgba(255, 152, 0, 0.7)', 'border' => 'rgba(255, 152, 0, 1)'],
    'In Progress' => ['background' => 'rgba(76, 201, 240, 0.7)', 'border' => 'rgba(76, 201, 240, 1)'],
    'Completed' => ['background' => 'rgba(76, 175, 80, 0.7)', 'border' => 'rgba(76, 175, 80, 1)'],
    'Blocked' => ['background' => 'rgba(244, 67, 54, 0.7)', 'border' => 'rgba(244, 67, 54, 1)']
];

// Task Status Distribution (WorkDiary)
$query = "SELECT TaskStatus, COUNT(*) as count FROM WorkDiary w $whereSql GROUP BY TaskStatus ORDER BY FIELD(TaskStatus, 'Not Started', 'In Progress', 'Completed', 'Blocked')";
$stmt = $pdo->prepare($query);
if ($statusFilter !== 'all') $stmt->bindValue(':status', $statusFilter);
if ($userFilter !== 'all') $stmt->bindValue(':userID', $userFilter, PDO::PARAM_INT);
$stmt->execute();
$taskStatusData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$taskStatusLabels = array_column($taskStatusData, 'TaskStatus');
$taskStatusCounts = array_column($taskStatusData, 'count');
$taskStatusBackgroundColors = array_map(function($label) use ($statusColors) {
    return $statusColors[$label]['background'] ?? 'rgba(0, 0, 0, 0.7)';
}, $taskStatusLabels);
$taskStatusBorderColors = array_map(function($label) use ($statusColors) {
    return $statusColors[$label]['border'] ?? 'rgba(0, 0, 0, 1)';
}, $taskStatusLabels);

// Task Time Deviation (WorkDiary) - Exclude SA team members
$timeDeviationWhereClauses = $whereClauses;
$timeDeviationWhereClauses[] = "UPPER(TRIM(u.Role)) != 'SA Team'";
$timeDeviationWhereSql = !empty($timeDeviationWhereClauses) ? 'WHERE ' . implode(' AND ', $timeDeviationWhereClauses) : '';
$query = "SELECT u.Username, SUM(w.AllocatedTime) as allocated, SUM(w.ActualTime) as actual 
          FROM WorkDiary w 
          JOIN Users u ON w.UserID = u.UserID 
          $timeDeviationWhereSql 
          GROUP BY w.UserID, u.Username";
$stmt = $pdo->prepare($query);
if ($statusFilter !== 'all') $stmt->bindValue(':status', $statusFilter);
if ($userFilter !== 'all') $stmt->bindValue(':userID', $userFilter, PDO::PARAM_INT);
$stmt->execute();
$timeDeviationData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$timeDeviationLabels = array_column($timeDeviationData, 'Username');
$timeDeviationAllocated = array_map(function($row) { return $row['allocated'] ?? 0; }, $timeDeviationData);
$timeDeviationActual = array_map(function($row) { return $row['actual'] ?? 0; }, $timeDeviationData);

// Blocked Tasks Trend (WorkDiary)
$blockedTasksLabels = [];
$blockedTasksData = [];
$blockedTasksHasMore = false;
$offset = ($page - 1) * $pageSize;
$limit = $pageSize + 1;
if ($timeFilter === 'all') {
    $query = "SELECT DATE(EntryDate) as date, COUNT(*) as count FROM WorkDiary WHERE TaskStatus = 'Blocked' GROUP BY DATE(EntryDate) ORDER BY DATE(EntryDate) DESC LIMIT $limit OFFSET $offset";
    $stmt = $pdo->query($query);
} else {
    $days = $timeFilter === '7days' ? 7 : 30;
    $query = "SELECT DATE(EntryDate) as date, COUNT(*) as count FROM WorkDiary WHERE TaskStatus = 'Blocked' AND DATE(EntryDate) >= DATE_SUB(CURDATE(), INTERVAL $days DAY) GROUP BY DATE(EntryDate) ORDER BY DATE(EntryDate) DESC LIMIT $limit OFFSET $offset";
    $stmt = $pdo->query($query);
}
$blockedTasksResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($blockedTasksResult) > $pageSize) {
    $blockedTasksHasMore = true;
    array_pop($blockedTasksResult);
}
$blockedTasksByDate = array_column($blockedTasksResult, 'count', 'date');
$blockedTasksDates = array_keys($blockedTasksByDate);
foreach ($blockedTasksDates as $date) {
    $blockedTasksLabels[] = date('Y-m-d', strtotime($date));
    $blockedTasksData[] = $blockedTasksByDate[$date];
}

// Daily Diary Submission Trend (WorkDiary)
$submissionRateLabels = [];
$submissionRateData = [];
$submissionRateHasMore = false;
if ($timeFilter === 'all') {
    $query = "SELECT DATE(EntryDate) as date, COUNT(*) as count FROM WorkDiary GROUP BY DATE(EntryDate) ORDER BY DATE(EntryDate) DESC LIMIT $limit OFFSET $offset";
    $stmt = $pdo->query($query);
} else {
    $days = $timeFilter === '7days' ? 7 : 30;
    $query = "SELECT DATE(EntryDate) as date, COUNT(*) as count FROM WorkDiary WHERE DATE(EntryDate) >= DATE_SUB(CURDATE(), INTERVAL $days DAY) GROUP BY DATE(EntryDate) ORDER BY DATE(EntryDate) DESC LIMIT $limit OFFSET $offset";
    $stmt = $pdo->query($query);
}
$submissionRateResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($submissionRateResult) > $pageSize) {
    $submissionRateHasMore = true;
    array_pop($submissionRateResult);
}
$submissionsByDate = array_column($submissionRateResult, 'count', 'date');
$submissionDates = array_keys($submissionsByDate);
foreach ($submissionDates as $date) {
    $submissionRateLabels[] = date('Y-m-d', strtotime($date));
    $submissionRateData[] = $submissionsByDate[$date];
}

// Task Completion Rate Over Time (WorkDiary + ProjectTasks)
$completionRateLabels = [];
$completionRateData = [];
$completionRateHasMore = false;
$combinedCompletion = [];
if ($timeFilter === 'all') {
    $query = "SELECT DATE(EntryDate) as date, COUNT(*) as count FROM WorkDiary WHERE TaskStatus = 'Completed' GROUP BY DATE(EntryDate)";
    $stmt = $pdo->query($query);
    $diaryCompletion = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $query = "SELECT DATE(EndDate) as date, COUNT(*) as count FROM ProjectTasks WHERE Status = 'Completed' AND EndDate IS NOT NULL GROUP BY DATE(EndDate)";
    $stmt = $pdo->query($query);
    $taskCompletion = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $days = $timeFilter === '7days' ? 7 : 30;
    $query = "SELECT DATE(EntryDate) as date, COUNT(*) as count FROM WorkDiary WHERE TaskStatus = 'Completed' AND DATE(EntryDate) >= DATE_SUB(CURDATE(), INTERVAL $days DAY) GROUP BY DATE(EntryDate)";
    $stmt = $pdo->query($query);
    $diaryCompletion = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $query = "SELECT DATE(EndDate) as date, COUNT(*) as count FROM ProjectTasks WHERE Status = 'Completed' AND EndDate IS NOT NULL AND DATE(EndDate) >= DATE_SUB(CURDATE(), INTERVAL $days DAY) GROUP BY DATE(EndDate)";
    $stmt = $pdo->query($query);
    $taskCompletion = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
foreach ($diaryCompletion as $row) {
    $date = $row['date'];
    $combinedCompletion[$date] = ($combinedCompletion[$date] ?? 0) + $row['count'];
}
foreach ($taskCompletion as $row) {
    $date = $row['date'];
    $combinedCompletion[$date] = ($combinedCompletion[$date] ?? 0) + $row['count'];
}
$completionDates = array_keys($combinedCompletion);
usort($completionDates, function($a, $b) { return strtotime($b) - strtotime($a); });
$completionDates = array_slice($completionDates, $offset, $limit);
if (count($completionDates) > $pageSize) {
    $completionRateHasMore = true;
    $completionDates = array_slice($completionDates, 0, $pageSize);
}
foreach ($completionDates as $date) {
    $completionRateLabels[] = date('Y-m-d', strtotime($date));
    $completionRateData[] = $combinedCompletion[$date];
}

// Average Task Duration by Status (WorkDiary)
$query = "SELECT TaskStatus, AVG(ActualTime) as avg_duration FROM WorkDiary WHERE ActualTime IS NOT NULL $whereSql GROUP BY TaskStatus ORDER BY FIELD(TaskStatus, 'Not Started', 'In Progress', 'Completed', 'Blocked')";
$stmt = $pdo->prepare($query);
if ($statusFilter !== 'all') $stmt->bindValue(':status', $statusFilter);
if ($userFilter !== 'all') $stmt->bindValue(':userID', $userFilter, PDO::PARAM_INT);
$stmt->execute();
$avgTaskDurationData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$avgTaskDurationLabels = array_column($avgTaskDurationData, 'TaskStatus');
$avgTaskDurationData = array_map(function($row) { return round($row['avg_duration'], 2); }, $avgTaskDurationData);

// Project Completion (ProjectTasks)
$projectWhereClauses = [];
$projectTimeClause = '';
if ($timeFilter === '7days') {
    $projectTimeClause = "StartDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($timeFilter === '30days') {
    $projectTimeClause = "StartDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
}
$projectUserClause = $userFilter !== 'all' ? "p.OwnerID = :userID" : ''; // Fixed typo (removed SLS)
if ($projectTimeClause) $projectWhereClauses[] = $projectTimeClause;
if ($projectUserClause) $projectWhereClauses[] = $projectUserClause;
$projectWhereSql = !empty($projectWhereClauses) ? 'WHERE ' . implode(' AND ', $projectWhereClauses) : '';

$query = "SELECT ProjectID, 
          (SUM(CASE WHEN Status = 'Completed' THEN 1 ELSE 0 END) / COUNT(*) * 100) as completion 
          FROM ProjectTasks p $projectWhereSql GROUP BY ProjectID";
$stmt = $pdo->prepare($query);
if ($userFilter !== 'all') $stmt->bindValue(':userID', $userFilter, PDO::PARAM_INT);
$stmt->execute();
$projectCompletionData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$projectCompletionLabels = array_map(function($row) { return "Project {$row['ProjectID']}"; }, $projectCompletionData);
$projectCompletionData = array_column($projectCompletionData, 'completion');

// User Workload (ProjectTasks)
$query = "SELECT u.Username, 
          SUM(CASE WHEN p.Status = 'Pending' THEN 1 ELSE 0 END) as pending, 
          SUM(CASE WHEN p.Status = 'Active' THEN 1 ELSE 0 END) as active, 
          SUM(CASE WHEN p.Status = 'Completed' THEN 1 ELSE 0 END) as completed 
          FROM ProjectTasks p JOIN Users u ON p.OwnerID = u.UserID $projectWhereSql 
          GROUP BY p.OwnerID, u.Username";
$stmt = $pdo->prepare($query);
if ($userFilter !== 'all') $stmt->bindValue(':userID', $userFilter, PDO::PARAM_INT);
$stmt->execute();
$userWorkloadData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$userWorkloadLabels = array_column($userWorkloadData, 'Username');
$userWorkloadPending = array_column($userWorkloadData, 'pending');
$userWorkloadActive = array_column($userWorkloadData, 'active');
$userWorkloadCompleted = array_column($userWorkloadData, 'completed');

// SA Observations by Category (SATeamObservations)
$saWhereClauses = [];
$saTimeClause = '';
if ($timeFilter === '7days') {
    $saTimeClause = "ObservationDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($timeFilter === '30days') {
    $saTimeClause = "ObservationDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
}
$saUserClause = $userFilter !== 'all' ? "s.UserID = :userID" : '';
$saCategoryClause = $categoryFilter !== 'all' ? "s.Category = :category" : '';
if ($saTimeClause) $saWhereClauses[] = $saTimeClause;
if ($saUserClause) $saWhereClauses[] = $saUserClause;
if ($saCategoryClause) $saWhereClauses[] = $saCategoryClause;
$saWhereSql = !empty($saWhereClauses) ? 'WHERE ' . implode(' AND ', $saWhereClauses) : '';

$query = "SELECT Category, COUNT(*) as count FROM SATeamObservations s $saWhereSql GROUP BY Category";
$stmt = $pdo->prepare($query);
if ($userFilter !== 'all') $stmt->bindValue(':userID', $userFilter, PDO::PARAM_INT);
if ($categoryFilter !== 'all') $stmt->bindValue(':category', $categoryFilter);
$stmt->execute();
$saObservationsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$saObservationsLabels = array_column($saObservationsData, 'Category');
$saObservationsData = array_column($saObservationsData, 'count');

// User Activity Heatmap (WorkDiary, SATeamObservations, ProjectTasks)
$userActivityDiary = array_fill(0, 7, 0);
$userActivityObservations = array_fill(0, 7, 0);
$userActivityTasks = array_fill(0, 7, 0);
$daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

$query = "SELECT DAYOFWEEK(EntryDate) as day, COUNT(*) as count FROM WorkDiary w $whereSql GROUP BY DAYOFWEEK(EntryDate)";
$stmt = $pdo->prepare($query);
if ($statusFilter !== 'all') $stmt->bindValue(':status', $statusFilter);
if ($userFilter !== 'all') $stmt->bindValue(':userID', $userFilter, PDO::PARAM_INT);
$stmt->execute();
$diaryActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($diaryActivity as $row) {
    $dayIndex = ($row['day'] + 5) % 7;
    $userActivityDiary[$dayIndex] = $row['count'];
}

$query = "SELECT DAYOFWEEK(ObservationDate) as day, COUNT(*) as count FROM SATeamObservations s $saWhereSql GROUP BY DAYOFWEEK(ObservationDate)";
$stmt = $pdo->prepare($query);
if ($userFilter !== 'all') $stmt->bindValue(':userID', $userFilter, PDO::PARAM_INT);
if ($categoryFilter !== 'all') $stmt->bindValue(':category', $categoryFilter);
$stmt->execute();
$obsActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($obsActivity as $row) {
    $dayIndex = ($row['day'] + 5) % 7;
    $userActivityObservations[$dayIndex] = $row['count'];
}

$query = "SELECT DAYOFWEEK(StartDate) as day, COUNT(*) as count FROM ProjectTasks p $projectWhereSql GROUP BY DAYOFWEEK(StartDate)";
$stmt = $pdo->prepare($query);
if ($userFilter !== 'all') $stmt->bindValue(':userID', $userFilter, PDO::PARAM_INT);
$stmt->execute();
$taskActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($taskActivity as $row) {
    $dayIndex = ($row['day'] + 5) % 7;
    $userActivityTasks[$dayIndex] = $row['count'];
}

// Return JSON response
echo json_encode([
    'taskStatus' => [
        'labels' => $taskStatusLabels,
        'data' => $taskStatusCounts,
        'backgroundColors' => $taskStatusBackgroundColors,
        'borderColors' => $taskStatusBorderColors
    ],
    'timeDeviation' => ['labels' => $timeDeviationLabels, 'allocated' => $timeDeviationAllocated, 'actual' => $timeDeviationActual],
    'blockedTasks' => ['labels' => $blockedTasksLabels, 'data' => $blockedTasksData, 'hasMore' => $blockedTasksHasMore],
    'submissionRate' => ['labels' => $submissionRateLabels, 'data' => $submissionRateData, 'hasMore' => $submissionRateHasMore],
    'completionRate' => ['labels' => $completionRateLabels, 'data' => $completionRateData, 'hasMore' => $completionRateHasMore],
    'avgTaskDuration' => ['labels' => $avgTaskDurationLabels, 'data' => $avgTaskDurationData],
    'projectCompletion' => ['labels' => $projectCompletionLabels, 'data' => $projectCompletionData],
    'userWorkload' => ['labels' => $userWorkloadLabels, 'pending' => $userWorkloadPending, 'active' => $userWorkloadActive, 'completed' => $userWorkloadCompleted],
    'saObservations' => ['labels' => $saObservationsLabels, 'data' => $saObservationsData],
    'userActivity' => ['labels' => $daysOfWeek, 'diary' => $userActivityDiary, 'observations' => $userActivityObservations, 'tasks' => $userActivityTasks]
]);
exit;
?>