<?php
include 'config.php';

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="Diary_System_Report_' . date('Y-m-d_H-i-s') . '.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Define CSV headers
$headers = [
    'Section', 'Username', 'TaskDescription/Details/TaskName', 'Status/Category', 
    'AllocatedTime', 'ActualTime', 'DeviationReason', 'PersonalInsights', 'Commitments', 
    'EntryDate/ObservationDate', 'Priority', 'StartDate', 'EndDate'
];
fputcsv($output, $headers);

// Fetch WorkDiary data
$stmt = $pdo->query("SELECT u.Username, w.TaskDescription, w.TaskStatus AS Status, 
                    w.AllocatedTime, w.ActualTime, w.DeviationReason, w.PersonalInsights, 
                    w.Commitments, w.EntryDate, NULL AS Priority, NULL AS StartDate, NULL AS EndDate 
                    FROM WorkDiary w 
                    JOIN Users u ON w.UserID = u.UserID 
                    ORDER BY w.EntryDate DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        'Diary Entry', $row['Username'], $row['TaskDescription'], $row['Status'], 
        $row['AllocatedTime'], $row['ActualTime'], $row['DeviationReason'], 
        $row['PersonalInsights'], $row['Commitments'], $row['EntryDate'], 
        '', '', ''
    ]);
}

// Fetch SATeamObservations data
$stmt = $pdo->query("SELECT u.Username, s.Details, s.Category AS Category, 
                    NULL AS AllocatedTime, NULL AS ActualTime, NULL AS DeviationReason, 
                    NULL AS PersonalInsights, NULL AS Commitments, s.ObservationDate, 
                    NULL AS Priority, NULL AS StartDate, NULL AS EndDate 
                    FROM SATeamObservations s 
                    JOIN Users u ON s.UserID = u.UserID 
                    ORDER BY s.ObservationDate DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        'SA Observation', $row['Username'], $row['Details'], $row['Category'], 
        '', '', '', '', '', $row['ObservationDate'], 
        '', '', ''
    ]);
}

// Fetch ProjectTasks data
$stmt = $pdo->query("SELECT u.Username, p.TaskName, p.Status, 
                    NULL AS AllocatedTime, NULL AS ActualTime, NULL AS DeviationReason, 
                    NULL AS PersonalInsights, NULL AS Commitments, NULL AS EntryDate, 
                    p.Priority, p.StartDate, p.EndDate 
                    FROM ProjectTasks p 
                    JOIN Users u ON p.OwnerID = u.UserID 
                    ORDER BY p.StartDate DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        'Project Task', $row['Username'], $row['TaskName'], $row['Status'], 
        '', '', '', '', '', '', 
        $row['Priority'], $row['StartDate'], $row['EndDate']
    ]);
}

// Close the output stream
fclose($output);
exit;
?>