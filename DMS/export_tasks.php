<?php
include 'config.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="tasks_export.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Task Name', 'Project', 'Owner', 'Status', 'Priority']);

$stmt = $pdo->query("SELECT pt.TaskName, p.ProjectName, u.Username, pt.Status, pt.Priority 
                    FROM ProjectTasks pt 
                    JOIN Projects p ON pt.ProjectID = p.ProjectID 
                    JOIN Users u ON pt.OwnerID = u.UserID");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [$row['TaskName'], $row['ProjectName'], $row['Username'], $row['Status'], $row['Priority']]);
}

fclose($output);
exit;
?>