<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>D-CoE Leaderboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #7c3aed;
            --primary-light: #a78bfa;
            --secondary: #14b8a6;
            --secondary-light: #5eead4;
            --accent: #f472b6;
            --dark: #1e293b;
            --light: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.95);
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            --border-radius: 20px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f5ff 0%, #e0e7ff 100%);
            color: var(--dark);
            min-height: 100vh;
            padding: 2rem 1rem;
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%' height='100%' filter='url(%23noiseFilter)' opacity='0.05'/%3E%3C/svg%3E");
            opacity: 0.2;
            z-index: -1;
        }

        .container {
            max-width: 1300px;
            margin: 0 auto;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            font-size: 2.8rem;
            font-weight: 800;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            position: relative;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 2px;
        }

        .subtitle {
            color: #64748b;
            font-size: 1.1rem;
            font-weight: 400;
        }

        .leaderboard-wrapper {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .leaderboard-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 1.5rem 2rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .header-title {
            font-size: 1.6rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header-title i {
            background: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .time-period {
            background: rgba(255, 255, 255, 0.15);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .info-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .scroll-container {
            padding: 1rem;
            max-height: 600px;
            overflow-y: auto;
        }

        .leaderboard-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .leaderboard-table th {
            background: rgba(248, 250, 252, 0.8);
            font-weight: 600;
            color: #475569;
            padding: 1rem 1.5rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .leaderboard-table tbody tr {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .leaderboard-table tbody tr:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .leaderboard-table td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
        }

        .rank {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .rank-1 { background: linear-gradient(135deg, #facc15, #eab308); color: #713f12; }
        .rank-2 { background: linear-gradient(135deg, #d1d5db, #9ca3af); color: #374151; }
        .rank-3 { background: linear-gradient(135deg, #f97316, #ea580c); color: white; }
        .rank-other { background: #e2e8f0; color: #64748b; }

        .username {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 600;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--secondary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .metric-container {
            width: 140px;
        }

        .metric-value {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }

        .progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            transition: width 0.8s ease-in-out;
        }

        .progress-task { background: linear-gradient(90deg, var(--secondary), var(--secondary-light)); }
        .progress-time { background: linear-gradient(90deg, var(--primary), var(--primary-light)); }
        .progress-consistency { background: linear-gradient(90deg, var(--accent), #f9a8d4); }

        .total-score {
            font-weight: 700;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .score-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .no-data {
            padding: 4rem;
            text-align: center;
            color: #64748b;
            font-size: 1.2rem;
        }

        .no-data i {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
        }

        .modal-title {
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-body {
            padding: 1.5rem;
            color: #1e293b;
        }

        .calculation-list {
            list-style: none;
            padding: 0;
        }

        .calculation-list li {
            margin-bottom: 1rem;
            padding: 1rem;
            background: rgba(241, 245, 249, 0.8);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .calculation-list li:hover {
            background: rgba(226, 232, 240, 0.9);
            transform: translateX(5px);
        }

        .calculation-list strong {
            color: var(--primary);
        }

        /* Pagination Styles */
        .pagination-container {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        .pagination {
            gap: 5px;
        }

        .page-item .page-link {
            border-radius: 8px;
            color: var(--primary);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(124, 58, 237, 0.2);
            transition: all 0.3s ease;
        }

        .page-item.active .page-link {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .page-item.disabled .page-link {
            color: #64748b;
            background: rgba(255, 255, 255, 0.5);
            border-color: rgba(124, 58, 237, 0.1);
        }

        .page-item .page-link:hover {
            background: var(--primary-light);
            color: white;
            border-color: var(--primary-light);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .page-title { font-size: 2.2rem; }
            .leaderboard-header { padding: 1rem 1.5rem; }
            .header-title { font-size: 1.4rem; }
            .metric-container { width: 120px; }
        }

        @media (max-width: 768px) {
            .leaderboard-table { display: block; }
            .leaderboard-table thead { display: none; }
            .leaderboard-table tbody tr {
                display: block;
                margin-bottom: 1rem;
                padding: 1rem;
            }
            .leaderboard-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 1rem;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }
            .leaderboard-table td:last-child { border-bottom: none; }
            .leaderboard-table td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #475569;
                margin-right: 1rem;
            }
            .metric-container { width: 100%; }
        }

        @media (max-width: 576px) {
            .page-title { font-size: 1.8rem; }
            .subtitle { font-size: 1rem; }
            .rank, .avatar, .score-circle { width: 35px; height: 35px; }
            .total-score { font-size: 1rem; }
            .info-btn { width: 28px; height: 28px; }
        }

        /* Animations */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .leaderboard-table tbody tr {
            animation: slideIn 0.5s ease forwards;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Performance Leaderboard</h1>
            <p class="subtitle">Celebrating Excellence in Productivity & Teamwork in D-CoE</p>
        </div>

        <div class="leaderboard-wrapper">
            <div class="leaderboard-header">
                <h2 class="header-title">
                    <i class="fas fa-trophy"></i>
                    Top Performers
                </h2>
                <div class="d-flex align-items-center gap-3">
                    <div class="time-period">
                        <i class="fas fa-calendar-alt"></i>
                        Last 30 Days
                    </div>
                    <button class="info-btn" data-bs-toggle="modal" data-bs-target="#calcInfoModal">
                        <i class="fas fa-info"></i>
                    </button>
                </div>
            </div>

            <div class="scroll-container">
                <table class="leaderboard-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Team Member</th>
                            <th>Task Completion</th>
                            <th>Time Efficiency</th>
                            <th>Consistency</th>
                            <th>Entry Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $itemsPerPage = 20;
                            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                            $offset = ($page - 1) * $itemsPerPage;
                            $startDate = date('Y-m-d', strtotime('-30 days'));

                            $sql = "
                                SELECT 
                                    u.Username,
                                    COUNT(w.EntryID) AS EntryCount,
                                    @rank := @rank + 1 as rank
                                FROM workdiary w
                                JOIN users u ON w.UserID = u.UserID
                                CROSS JOIN (SELECT @rank := :offset) as init
                                WHERE w.EntryDate >= :startDate AND u.Role = 'Team Member'
                                GROUP BY u.UserID, u.Username
                                ORDER BY EntryCount DESC
                                LIMIT :limit OFFSET :offset
                            ";

                            // Count total users for pagination
                            $countSql = "
                                SELECT COUNT(DISTINCT w.UserID)
                                FROM workdiary w
                                JOIN users u ON w.UserID = u.UserID
                                WHERE w.EntryDate >= :startDate AND u.Role = 'Team Member'
                            ";
                            $countStmt = $pdo->prepare($countSql);
                            $countStmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
                            $countStmt->execute();
                            $totalUsers = $countStmt->fetchColumn();
                            $totalPages = ceil($totalUsers / $itemsPerPage);

                            $stmt = $pdo->prepare($sql);
                            $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
                            $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
                            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                            $stmt->execute();

                            if ($stmt->rowCount() == 0) {
                                echo '<tr><td colspan="6" class="no-data"><i class="fas fa-chart-line"></i>No performance data available</td></tr>';
                            } else {
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $rankClass = $row['rank'] == 1 ? 'rank-1' : ($row['rank'] == 2 ? 'rank-2' : ($row['rank'] == 3 ? 'rank-3' : 'rank-other'));
                                    $initial = strtoupper(substr($row['Username'], 0, 1));
                                    $entryCount = number_format($row['EntryCount'], 0);

                                    echo "<tr>";
                                    echo "<td data-label='Rank'><div class='rank $rankClass'>" . htmlspecialchars($row['rank']) . "</div></td>";
                                    echo "<td data-label='Team Member'><div class='username'><div class='avatar'>$initial</div>" . htmlspecialchars($row['Username']) . "</div></td>";
                                    
                                    // Placeholder for Task Completion
                                    echo "<td data-label='Task Completion'>";
                                    echo "<div class='metric-container'>";
                                    echo "<div class='metric-value'>N/A</div>";
                                    echo "<div class='progress-bar'><div class='progress-fill progress-task' style='width: 0%'></div></div>";
                                    echo "</div></td>";

                                    // Placeholder for Time Efficiency
                                    echo "<td data-label='Time Efficiency'>";
                                    echo "<div class='metric-container'>";
                                    echo "<div class='metric-value'>N/A</div>";
                                    echo "<div class='progress-bar'><div class='progress-fill progress-time' style='width: 0%'></div></div>";
                                    echo "</div></td>";

                                    // Placeholder for Consistency
                                    echo "<td data-label='Consistency'>";
                                    echo "<div class='metric-container'>";
                                    echo "<div class='metric-value'>N/A</div>";
                                    echo "<div class='progress-bar'><div class='progress-fill progress-consistency' style='width: 0%'></div></div>";
                                    echo "</div></td>";

                                    // Display Entry Count
                                    echo "<td data-label='Entry Count'>";
                                    echo "<div class='total-score'>";
                                    echo "<div class='score-circle'>$entryCount</div>";
                                    echo "</div></td>";

                                    echo "</tr>";
                                }
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='6' class='no-data'><i class='fas fa-exclamation-triangle'></i>Database Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php if ($totalPages > 1): ?>
            <div class="pagination-container">
                <nav>
                    <ul class="pagination">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Calculation Info Modal -->
    <div class="modal fade" id="calcInfoModal" tabindex="-1" aria-labelledby="calcInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="calcInfoModalLabel">
                        <i class="fas fa-calculator"></i> How Rankings Are Determined
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>The leaderboard ranks Team Members based on the total number of work diary entries submitted over the last 30 days.</p>
                    <ul class="calculation-list">
                        <li>
                            <strong>Entry Count:</strong><br>
                            The total number of entries submitted by the team member in the Work Diary.<br>
                            <em>Formula:</em> COUNT(EntryID)<br>
                            <em>Ranking:</em> Higher entry counts result in higher rankings.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => bar.style.width = width, 100);
            });
        });

        // Auto-toggle to next page (report-tasks.php) after 2 minutes (120,000 milliseconds)
        setTimeout(function() {
            window.location.href = 'report-tasks.php';
        }, 120000);
    </script>
</body>
</html>
