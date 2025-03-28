<?php
session_start();
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Phase Scheduling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Phase Scheduling</h1>
        <a href="index.php" class="btn btn-secondary mb-3">Back</a>
        <table class="table table-striped">
            <thead>
                <tr><th>ID</th><th>Name</th><th>Start Date</th><th>End Date</th><th>Handoffs</th></tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT p.*, GROUP_CONCAT(h.description) as handoffs FROM phases p LEFT JOIN handoffs h ON p.phase_id = h.phase_id GROUP BY p.phase_id");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo $row['phase_id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['start_date']; ?></td>
                        <td><?php echo $row['end_date']; ?></td>
                        <td><?php echo $row['handoffs'] ?: 'None'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>