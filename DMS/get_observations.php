<?php
include 'config.php';
header('Content-Type: application/json');

$timeFilter = $_GET['time'] ?? 'all';
$categoryFilter = $_GET['category'] ?? 'all';
$userFilter = $_GET['user'] ?? 'all';

$where = [];
$params = [];

if ($timeFilter === '7days') {
    $where[] = "s.ObservationDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($timeFilter === '30days') {
    $where[] = "s.ObservationDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
}

if ($categoryFilter !== 'all') {
    $where[] = "s.Category = :category";
    $params[':category'] = $categoryFilter;
}

if ($userFilter !== 'all') {
    $where[] = "s.UserID = :user";
    $params[':user'] = $userFilter;
}

$query = "
    SELECT s.Details, s.Category, s.ObservationDate, u.Username 
    FROM SATeamObservations s 
    JOIN Users u ON s.UserID = u.UserID
" . (count($where) ? " WHERE " . implode(" AND ", $where) : "") . " 
    ORDER BY s.ObservationDate DESC
";

$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->execute();

$observations = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($observations as &$obs) {
    $obs['ObservationDate'] = date('Y-m-d', strtotime($obs['ObservationDate']));
}

echo json_encode($observations);
?>