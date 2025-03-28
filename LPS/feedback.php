<?php
session_start();
include 'db_connect.php';

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activity_id'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO feedback (activity_id, comment, submitted_by, submission_date) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$_POST['activity_id'], $_POST['comment'], $_SESSION['user_id']]);
        // Redirect to avoid form resubmission on refresh
        header("Location: feedback.php?success=Feedback submitted successfully");
        exit;
    } catch (Exception $e) {
        $error = "Failed to submit feedback: " . $e->getMessage();
    }
}

// Fetch activities and users for dropdowns
$activitiesStmt = $pdo->query("SELECT activity_id, description FROM activities ORDER BY description");
$activities = $activitiesStmt->fetchAll(PDO::FETCH_ASSOC);

$usersStmt = $pdo->query("SELECT user_id, name FROM users ORDER BY name");
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #5472d3;
            --primary-light: #738ede;
            --secondary: #2c3e99;
            --accent: #4cc9f0;
            --success: #4CAF50;
            --warning: #FFC107;
            --danger: #F44336;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --soft-gray: #edf2f7;
            --text-muted: #64748b;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.03);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --transition: all 0.2s ease;
        }
        
        body {
            background-color: #f5f7fa;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            line-height: 1.6;
        }

        .dashboard-container {
            max-width: 1400px;
            padding: 2rem 1.5rem;
            margin: 0 auto;
        }

        .card {
            border: none;
            border-radius: var(--radius-lg);
            background: white;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 1.5rem;
            border-bottom: none;
            position: relative;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 100%;
            background: linear-gradient(to right, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.15));
            transform: skewX(-30deg) translateX(30%);
        }

        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo i {
            font-size: 1.2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .feedback-form {
            background: white;
            border-radius: var(--radius-md);
            padding: 1.75rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.75rem;
            border: 1px solid var(--soft-gray);
        }

        .feedback-form h3 {
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            color: var(--dark);
            position: relative;
            padding-bottom: 0.75rem;
        }

        .feedback-form h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--primary);
            border-radius: 10px;
        }

        .table-container {
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            border: 1px solid var(--soft-gray);
        }

        .table {
            margin-bottom: 0;
            width: 100%;
        }

        .table th {
            background: var(--soft-gray);
            color: var(--dark);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            padding: 0.85rem 1.25rem;
            letter-spacing: 0.5px;
        }

        .table th i {
            margin-right: 0.5rem;
            color: var(--primary);
        }

        .table td {
            vertical-align: middle;
            padding: 1.25rem;
            font-size: 0.95rem;
            color: var(--dark);
            border-bottom: 1px solid var(--soft-gray);
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover {
            background: #f9fafb;
            transition: var(--transition);
        }

        .btn {
            border-radius: var(--radius-sm);
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--primary);
            border: none;
        }

        .btn-primary:hover, .btn-primary:focus {
            background: var(--primary-light);
            box-shadow: 0 4px 10px rgba(84, 114, 211, 0.3);
        }

        .btn-outline-secondary {
            border-color: var(--soft-gray);
            color: var(--gray);
        }

        .btn-outline-secondary:hover {
            background: var(--soft-gray);
            color: var(--dark);
        }

        .btn-danger {
            background: var(--danger);
            border: none;
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
        }

        .btn-danger:hover {
            background: #d32f2f;
            box-shadow: 0 2px 8px rgba(244, 67, 54, 0.3);
        }

        .form-control, .form-select {
            border-radius: var(--radius-sm);
            border: 1px solid var(--soft-gray);
            font-size: 0.95rem;
            padding: 0.65rem 0.75rem;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(84, 114, 211, 0.15);
        }

        .form-label {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .filter-section {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .filter-section > div {
            flex: 1;
            max-width: 300px;
        }
        
        .toast {
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            border: none;
        }
        
        .toast-header {
            padding: 0.75rem 1rem;
            border-bottom: none;
        }
        
        .toast-body {
            padding: 1rem;
            font-weight: 500;
        }
        
        .bg-success {
            background-color: var(--success) !important;
            color: white;
        }
        
        .bg-danger {
            background-color: var(--danger) !important;
            color: white;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1.5rem 1rem;
            }

            .feedback-form {
                padding: 1.25rem;
            }

            .table th, .table td {
                padding: 0.85rem;
                font-size: 0.9rem;
            }

            .filter-section {
                flex-direction: column;
            }
            
            .filter-section > div {
                max-width: none;
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
                <h2 class="page-title">Feedback</h2>
                <a href="index.php" class="btn btn-outline-secondary mb-4">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>

                <!-- Feedback Form -->
                <div class="feedback-form">
                    <h3>Submit Feedback</h3>
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="activity_id" class="form-label">Activity</label>
                                <select name="activity_id" id="activity_id" class="form-select" required>
                                    <option value="">Select Activity</option>
                                    <?php foreach ($activities as $activity): ?>
                                        <option value="<?php echo $activity['activity_id']; ?>">
                                            <?php echo htmlspecialchars($activity['description']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="comment" class="form-label">Comment</label>
                                <textarea name="comment" id="comment" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-send me-1"></i> Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Filter Section -->
                <div class="filter-section">
                    <div>
                        <label for="filter_activity" class="form-label">
                            <i class="bi bi-funnel me-1"></i> Filter by Activity
                        </label>
                        <select id="filter_activity" class="form-select">
                            <option value="">All Activities</option>
                            <?php foreach ($activities as $activity): ?>
                                <option value="<?php echo $activity['activity_id']; ?>">
                                    <?php echo htmlspecialchars($activity['description']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="filter_user" class="form-label">
                            <i class="bi bi-person me-1"></i> Filter by User
                        </label>
                        <select id="filter_user" class="form-select">
                            <option value="">All Users</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['user_id']; ?>">
                                    <?php echo htmlspecialchars($user['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Feedback Table -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><i class="bi bi-hash"></i> ID</th>
                                <th><i class="bi bi-list-task"></i> Activity</th>
                                <th><i class="bi bi-chat-left-text"></i> Comment</th>
                                <th><i class="bi bi-person"></i> User</th>
                                <th><i class="bi bi-calendar"></i> Submission Date</th>
                                <th><i class="bi bi-gear"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody id="feedback-table-body"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Toast Container -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="toastMessage" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto">Notification</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        $(document).ready(function() {
            const toastEl = $('#toastMessage');
            const toast = new bootstrap.Toast(toastEl);

            // Function to show toast messages
            function showToast(message, isError = false) {
                toastEl.find('.toast-body').text(message);
                toastEl.find('.toast-header').removeClass('bg-success bg-danger').addClass(isError ? 'bg-danger' : 'bg-success');
                toast.show();
            }

            // Show success message if present
            <?php if (isset($_GET['success'])): ?>
                showToast("<?php echo htmlspecialchars($_GET['success']); ?>");
            <?php endif; ?>
            <?php if (isset($error)): ?>
                showToast("<?php echo htmlspecialchars($error); ?>", true);
            <?php endif; ?>

            // Function to refresh the feedback table
            function refreshTable() {
                const activityId = $('#filter_activity').val();
                const userId = $('#filter_user').val();
                $.ajax({
                    url: 'ajax_handler.php',
                    method: 'POST',
                    data: { 
                        action: 'get_feedback',
                        activity_id: activityId,
                        submitted_by: userId
                    },
                    success: function(data) {
                        $('#feedback-table-body').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error fetching feedback:', status, error, xhr.responseText);
                        try {
                            const response = JSON.parse(xhr.responseText);
                            showToast('Failed to fetch feedback: ' + response.message, true);
                        } catch (e) {
                            showToast('Failed to fetch feedback: Unknown error', true);
                        }
                    }
                });
            }

            // Initial load and periodic refresh
            refreshTable();
            setInterval(refreshTable, 5000);

            // Refresh table when filters change
            $('#filter_activity, #filter_user').on('change', function() {
                refreshTable();
            });

            // Delete feedback
            $(document).on('click', '.delete-feedback-btn', function() {
                const feedbackId = $(this).data('id');
                if (confirm('Are you sure you want to delete this feedback?')) {
                    $.ajax({
                        url: 'ajax_handler.php',
                        method: 'POST',
                        data: { action: 'delete_feedback', feedback_id: feedbackId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                refreshTable();
                                showToast(response.message);
                            } else {
                                showToast(response.message, true);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error deleting feedback:', status, error, xhr.responseText);
                            showToast('Failed to delete feedback', true);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>