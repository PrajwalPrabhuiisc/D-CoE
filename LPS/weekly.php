<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['week'])) {
    header("Location: index.php");
    exit;
}
$week = (int)$_SESSION['week'];

// Fetch milestones for the filter dropdown
$milestonesStmt = $pdo->query("SELECT milestone_id, name FROM milestones ORDER BY start_date");
$milestones = $milestonesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Work Planning</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #3a86ff;
            --primary-hover: #1a76ff;
            --secondary: #4361ee;
            --success: #2ecc71;
            --warning: #f1c40f;
            --danger: #e74c3c;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --soft-gray: #e9ecef;
            --text-muted: #adb5bd;
            --bg-light: #f2f5ff;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }
        
        body {
            background-color: var(--bg-light);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: var(--dark);
        }

        .dashboard-container {
            max-width: 1400px;
            padding: 2rem 1.5rem;
            margin: 0 auto;
        }

        .card {
            border: none;
            border-radius: 12px;
            background: white;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-header {
            background: var(--primary);
            color: white;
            padding: 1.25rem 1.5rem;
            border-bottom: none;
            border-radius: 12px 12px 0 0 !important;
            position: relative;
        }

        .logo {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
            width: 100%;
        }

        .table th {
            background: #fafbff;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--soft-gray);
            color: var(--gray);
        }

        .table td {
            vertical-align: middle;
            padding: 1.25rem;
            font-size: 0.95rem;
            color: var(--dark);
            border-bottom: 1px solid var(--soft-gray);
        }

        .table tr:hover {
            background: #fafbff;
            transition: background 0.2s ease;
        }

        .btn {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-outline-secondary {
            border-color: var(--soft-gray);
            color: var(--gray);
        }

        .btn-outline-secondary:hover {
            background: var(--soft-gray);
            color: var(--dark);
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid var(--soft-gray);
            font-size: 0.95rem;
            padding: 0.625rem 0.75rem;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(58, 134, 255, 0.15);
        }

        .filter-section {
            display: flex;
            align-items: flex-end;
            gap: 1rem;
            margin-bottom: 1.5rem;
            background: white;
            padding: 1.25rem;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
        }

        .ppc-display {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--success);
            margin-bottom: 1.5rem;
            background: white;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            display: inline-block;
        }

        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background: var(--primary);
            color: white;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            padding: 1.25rem 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .toast {
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .toast-header {
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            padding: 0.75rem 1rem;
        }

        .toast-body {
            padding: 1rem;
        }

        .status-select {
            padding: 0.5rem;
            border-radius: 6px;
            border: 1px solid var(--soft-gray);
            background-color: white;
            min-width: 140px;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            padding: 0.4rem 0.75rem;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1.5rem 1rem;
            }

            .table th, .table td {
                padding: 1rem 0.75rem;
                font-size: 0.9rem;
            }

            .filter-section {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="card mb-4">
            <div class="card-header">
                <div class="logo">
                    <i class="bi bi-kanban"></i> 
                    <span>LPS Dashboard</span>
                </div>
            </div>
            <div class="card-body p-4">
                <h2 class="page-title">Weekly Work Planning (Week <?php echo $week; ?>)</h2>
                <a href="index.php" class="btn btn-outline-secondary mb-4">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>

                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="w-100">
                        <label for="filter_milestone" class="form-label">Filter by Milestone</label>
                        <select id="filter_milestone" class="form-select">
                            <option value="">All Milestones</option>
                            <?php foreach ($milestones as $milestone): ?>
                                <option value="<?php echo $milestone['milestone_id']; ?>">
                                    <?php echo htmlspecialchars($milestone['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- PPC Display -->
                <div class="ppc-display">
                    <i class="bi bi-graph-up-arrow me-2"></i>
                    PPC: <span id="ppc">0</span>%
                </div>

                <!-- Weekly Tasks Table -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><i class="bi bi-hash me-1"></i>ID</th>
                                <th><i class="bi bi-list-task me-1"></i>Description</th>
                                <th><i class="bi bi-check-circle me-1"></i>Status</th>
                                <th><i class="bi bi-arrow-repeat me-1"></i>Update</th>
                                <th><i class="bi bi-chat-left-text me-1"></i>Feedback</th>
                                <th><i class="bi bi-clock-history me-1"></i>Revisions</th>
                            </tr>
                        </thead>
                        <tbody id="weekly-table-body"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Feedback Modal -->
        <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="feedbackModalLabel">
                            <i class="bi bi-chat-text me-2"></i>Feedback for Activity
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addFeedbackForm" class="mb-4">
                            <input type="hidden" id="feedbackActivityId" name="activity_id">
                            <div class="mb-3">
                                <label for="feedbackComment" class="form-label">Add New Feedback</label>
                                <textarea class="form-control" id="feedbackComment" name="comment" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Submit Feedback
                            </button>
                        </form>
                        <h6 class="mb-3 fw-bold">Existing Feedback</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Activity</th>
                                        <th>Comment</th>
                                        <th>User</th>
                                        <th>Submission Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="feedbackList"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revisions Modal -->
        <div class="modal fade" id="revisionsModal" tabindex="-1" aria-labelledby="revisionsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="revisionsModalLabel">
                            <i class="bi bi-clock-history me-2"></i>Revision History
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Old Status</th>
                                        <th>New Status</th>
                                        <th>Changed By</th>
                                        <th>Change Date</th>
                                    </tr>
                                </thead>
                                <tbody id="revisionsList"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Container -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="toastMessage" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="bi bi-bell me-2"></i>
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

            // Function to refresh the weekly tasks table
            function refreshTable() {
                const milestoneId = $('#filter_milestone').val();
                $.ajax({
                    url: 'ajax_handler.php',
                    method: 'POST',
                    data: { 
                        action: 'get_weekly',
                        week: <?php echo $week; ?>,
                        milestone_id: milestoneId
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#weekly-table-body').html(data.tasks);
                        $('#ppc').text(data.ppc);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error fetching weekly tasks:', status, error, xhr.responseText);
                        showToast('Failed to fetch weekly tasks', true);
                    }
                });
            }

            // Initial load and periodic refresh
            refreshTable();
            setInterval(refreshTable, 5000);

            // Refresh table when milestone filter changes
            $('#filter_milestone').on('change', function() {
                refreshTable();
            });

            // Update status
            $(document).on('change', '.status-select', function() {
                const activityId = $(this).data('id');
                const newStatus = $(this).val();
                $.ajax({
                    url: 'update.php',
                    method: 'POST',
                    data: { action: 'update_status', activity_id: activityId, status: newStatus },
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
                        console.error('AJAX error updating status:', status, error, xhr.responseText);
                        showToast('Failed to update status', true);
                    }
                });
            });

            // Add feedback
            $(document).on('click', '.add-feedback-btn', function() {
                const activityId = $(this).data('id');
                $('#feedbackActivityId').val(activityId);
                fetchFeedback(activityId);
            });

            $('#addFeedbackForm').on('submit', function(e) {
                e.preventDefault();
                const formData = {
                    action: 'add_feedback',
                    activity_id: $('#feedbackActivityId').val(),
                    comment: $('#feedbackComment').val(),
                    submitted_by: <?php echo $_SESSION['user_id']; ?>
                };

                $.ajax({
                    url: 'feedback.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#feedbackComment').val('');
                            fetchFeedback(formData.activity_id);
                            showToast(response.message);
                        } else {
                            showToast(response.message, true);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error adding feedback:', status, error, xhr.responseText);
                        showToast('Failed to add feedback', true);
                    }
                });
            });

            // Fetch feedback for the modal
            function fetchFeedback(activityId) {
                $.ajax({
                    url: 'ajax_handler.php',
                    method: 'POST',
                    data: { action: 'get_feedback', activity_id: activityId },
                    success: function(data) {
                        $('#feedbackList').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error fetching feedback:', status, error, xhr.responseText);
                        showToast('Failed to fetch feedback', true);
                    }
                });
            }

            // Delete feedback
            $(document).on('click', '.delete-feedback-btn', function() {
                const feedbackId = $(this).data('id');
                const activityId = $('#feedbackActivityId').val();
                if (confirm('Are you sure you want to delete this feedback?')) {
                    $.ajax({
                        url: 'ajax_handler.php',
                        method: 'POST',
                        data: { action: 'delete_feedback', feedback_id: feedbackId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                fetchFeedback(activityId);
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

            // View revisions
            $(document).on('click', '.view-revisions-btn', function() {
                const activityId = $(this).data('id');
                $.ajax({
                    url: 'ajax_handler.php',
                    method: 'POST',
                    data: { action: 'get_revisions', activity_id: activityId },
                    success: function(data) {
                        $('#revisionsList').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error fetching revisions:', status, error, xhr.responseText);
                        showToast('Failed to fetch revisions', true);
                    }
                });
            });
        });
    </script>
</body>
</html>