<?php
session_start();
include 'db_connect.php';

// Initialize session week if not set
if (!isset($_SESSION['week'])) {
    $_SESSION['week'] = 12; // Default week
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        if ($_POST['action'] === 'update_week') {
            $week = (int)$_POST['week'];
            if ($week >= 9 && $week <= 22) {
                $_SESSION['week'] = $week;
                echo json_encode(['success' => true, 'week' => $week]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid week']);
            }
        } elseif ($_POST['action'] === 'get_stats') {
            $week = (int)$_POST['week'];
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as total, 
                    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed, 
                    SUM(CASE WHEN constraint_flag = 1 THEN 1 ELSE 0 END) as `delayed` 
                FROM activities 
                WHERE week_number = ?
            ");
            $stmt->execute([$week]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($stats === false) {
                throw new Exception("No data returned for week $week");
            }
            $total = (int)$stats['total'];
            $completed = (int)$stats['completed'];
            $delayed = (int)$stats['delayed'];
            $ppc = $total > 0 ? round(($completed / $total) * 100) : 0;
            echo json_encode([
                'success' => true,
                'active_tasks' => $total,
                'completion_rate' => $ppc,
                'delayed_tasks' => $delayed
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LPS Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3a0ca3;
            --accent: #4cc9f0;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
        }
        
        body {
            background-color: #f5f7fa;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: var(--dark);
        }

        .dashboard-container {
            max-width: 1140px;
            padding: 2rem 1rem;
            margin: 0 auto;
        }

        .card {
            border: none;
            border-radius: 16px;
            background: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 1.5rem;
            border-bottom: none;
            border-radius: 16px 16px 0 0 !important;
            position: relative;
            overflow: hidden;
        }

        .card-header::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
        }

        .menu-item {
            border: none;
            border-radius: 12px;
            margin-bottom: 0.75rem;
            background: var(--light);
            transition: all 0.3s ease;
            padding: 1rem 1.25rem;
            color: var(--dark);
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .menu-item:hover, .menu-item:focus {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            background: linear-gradient(45deg, var(--primary), var(--primary-light));
            color: white;
            text-decoration: none;
        }

        .menu-item:hover .menu-icon, .menu-item:focus .menu-icon {
            color: white;
        }

        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .menu-icon {
            margin-right: 12px;
            font-size: 1.2rem;
            color: var(--primary);
        }
        
        .badge-notification {
            position: absolute;
            top: -5px;
            right: -5px;
            padding: 0.25rem 0.4rem;
            border-radius: 50%;
            background: var(--accent);
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        .week-selector {
            background: white;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 1.5rem;
        }
        
        .week-btn {
            border: none;
            background: #f1f3f7;
            color: var(--primary);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .week-btn:hover {
            background: var(--primary-light);
            color: white;
        }
        
        .week-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .week-display {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--dark);
        }
        
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
        }
        
        .stat-icon.tasks {
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }
        
        .stat-icon.completion {
            background: rgba(46, 204, 113, 0.1);
            color: var(--success);
        }
        
        .stat-icon.delay {
            background: rgba(243, 156, 18, 0.1);
            color: var(--warning);
        }
        
        .stat-info {
            flex: 1;
        }
        
        .stat-title {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0;
            color: var(--dark);
        }
        
        .dashboard-menu {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }
        
        @media (max-width: 768px) {
            .quick-stats {
                grid-template-columns: 1fr;
            }
            
            .dashboard-menu {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="card mb-4">
            <div class="card-header">
                <div class="dashboard-header">
                    <div class="logo">
                        <i class="bi bi-kanban"></i> 
                        <span>LPS Dashboard</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <h2 class="page-title">Last Planner System Dashboard</h2>
                
                <div class="week-selector">
                    <div class="d-flex align-items-center">
                        <span class="me-2 text-muted">Current Period:</span>
                        <button class="week-btn me-2" id="prevWeek">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <div class="week-display px-3" id="weekDisplay">Week <?php echo $_SESSION['week']; ?></div>
                        <button class="week-btn ms-2" id="nextWeek">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
                
                <div class="quick-stats">
                    <div class="stat-card">
                        <div class="stat-icon tasks">
                            <i class="bi bi-list-check"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-title">Active Tasks</div>
                            <div class="stat-value" id="activeTasks">0</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon completion">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-title">Completion Rate</div>
                            <div class="stat-value" id="completionRate">0%</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon delay">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-title">Delayed Tasks</div>
                            <div class="stat-value" id="delayedTasks">0</div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-menu">
                    <a href="master.php" class="menu-item">
                        <i class="bi bi-calendar3 menu-icon"></i>
                        <span>Master Schedule</span>
                    </a>
                    <a href="phase.php" class="menu-item">
                        <i class="bi bi-diagram-3 menu-icon"></i>
                        <span>Phase Scheduling</span>
                    </a>
                    <a href="lookahead.php" class="menu-item">
                        <i class="bi bi-eye menu-icon"></i>
                        <span>Lookahead Planning</span>
                    </a>
                    <a href="weekly.php" class="menu-item">
                        <i class="bi bi-calendar-week menu-icon"></i>
                        <span>Weekly Work Planning</span>
                    </a>
                    <a href="feedback.php" class="menu-item">
                        <i class="bi bi-chat-left-text menu-icon"></i>
                        <span>Feedback</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        $(document).ready(function() {
            let currentWeek = <?php echo $_SESSION['week']; ?>;
            const weekDisplay = $('#weekDisplay');
            const prevWeekBtn = $('#prevWeek');
            const nextWeekBtn = $('#nextWeek');
            
            // Initial load
            updateWeekDisplay();
            updateDashboardData(currentWeek);

            // Week navigation
            prevWeekBtn.on('click', function(e) {
                e.preventDefault();
                if (currentWeek > 9) {
                    currentWeek--;
                    updateWeekViaAjax(currentWeek);
                }
            });
            
            nextWeekBtn.on('click', function(e) {
                e.preventDefault();
                if (currentWeek < 22) {
                    currentWeek++;
                    updateWeekViaAjax(currentWeek);
                }
            });
            
            function updateWeekDisplay() {
                weekDisplay.text(`Week ${currentWeek}`);
                prevWeekBtn.prop('disabled', currentWeek <= 9);
                nextWeekBtn.prop('disabled', currentWeek >= 22);
            }
            
            function updateWeekViaAjax(week) {
                $.ajax({
                    url: 'index.php',
                    method: 'POST',
                    data: { action: 'update_week', week: week },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            currentWeek = response.week;
                            updateWeekDisplay();
                            updateDashboardData(currentWeek);
                        } else {
                            console.error('Week update failed:', response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error updating week:', status, error, xhr.responseText);
                    }
                });
            }
            
            function updateDashboardData(week) {
                $.ajax({
                    url: 'index.php',
                    method: 'POST',
                    data: { action: 'get_stats', week: week },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            $('#activeTasks').text(data.active_tasks || 0);
                            $('#completionRate').text((data.completion_rate || 0) + '%');
                            $('#delayedTasks').text(data.delayed_tasks || 0);
                        } else {
                            console.error('Stats fetch failed:', data.error);
                            $('#activeTasks').text('N/A');
                            $('#completionRate').text('N/A');
                            $('#delayedTasks').text('N/A');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error fetching stats:', status, error, xhr.responseText);
                        $('#activeTasks').text('N/A');
                        $('#completionRate').text('N/A');
                        $('#delayedTasks').text('N/A');
                    }
                });
            }
        });
    </script>
</body>
</html>