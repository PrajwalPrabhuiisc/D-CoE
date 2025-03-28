<?php
include 'config.php';
header('Content-Type: application/json');

$timeFilter = $_GET['time'] ?? 'all';
$statusFilter = $_GET['status'] ?? 'all';
$userFilter = $_GET['user'] ?? 'all';

$where = [];
$params = [];

if ($timeFilter === '7days') {
    $where[] = "w.EntryDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($timeFilter === '30days') {
    $where[] = "w.EntryDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
}

if ($statusFilter !== 'all') {
    $where[] = "w.TaskStatus = :status";
    $params[':status'] = $statusFilter;
}

if ($userFilter !== 'all') {
    $where[] = "w.UserID = :user";
    $params[':user'] = $userFilter;
}

// Fetch strictly from WorkDiary, joining Users for Username
$query = "
    SELECT w.TaskDescription, w.TaskStatus, w.EntryDate, u.Username 
    FROM WorkDiary w 
    JOIN Users u ON w.UserID = u.UserID
" . (count($where) ? " WHERE " . implode(" AND ", $where) : "") . " 
    ORDER BY w.EntryDate DESC
";

$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->execute();

$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($entries as &$entry) {
    $entry['EntryDate'] = date('Y-m-d', strtotime($entry['EntryDate']));
}

echo json_encode($entries);
?>