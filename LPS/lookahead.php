<?php
session_start();
include 'db_connect.php';
$week = $_SESSION['week'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lookahead Planning</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Keep your existing CSS and add: */
        .timeline-container {
            position: relative;
            background: white;
            padding: 1rem;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow-x: auto;
        }
        .timeline-week {
            display: inline-block;
            width: 200px;
            vertical-align: top;
            border-right: 1px solid var(--soft-gray);
            padding: 0.5rem;
        }
        .activity-card {
            background: #fff;
            border: 1px solid var(--soft-gray);
            border-radius: 6px;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            position: relative;
        }
        .milestone-line {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--warning);
            z-index: 1;
        }
        .handoff-arrow {
            color: var(--secondary);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="bi bi-calendar-week me-2"></i>
                Lookahead Planning
                <span class="text-primary">(Weeks <?php echo $week; ?>-<?php echo $week + 2; ?>)</span>
            </h1>
            <div>
                <span class="badge bg-success me-2">PPC: <span id="ppc-value">0%</span></span>
                <a href="index.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <div class="timeline-container" id="lookahead-timeline">
            <!-- Timeline will be populated by JS -->
        </div>

        <!-- Constraint Resolution Modal -->
        <div class="modal fade" id="constraintModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Resolve Constraint</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="constraintForm">
                            <input type="hidden" name="activity_id" id="constraintActivityId">
                            <div class="mb-3">
                                <label class="form-label">Feedback</label>
                                <textarea class="form-control" name="comment" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="constraint_status">
                                    <option value="0">Free</option>
                                    <option value="1">Blocked</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveConstraint">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast -->
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            const toastEl = $('#toastMessage');
            const toast = new bootstrap.Toast(toastEl);
            const constraintModal = new bootstrap.Modal($('#constraintModal'));

            function showToast(message, isError = false) {
                toastEl.find('.toast-body').text(message);
                toastEl.find('.toast-header').removeClass('bg-success bg-danger')
                    .addClass(isError ? 'bg-danger' : 'bg-success');
                toast.show();
            }

            function calculatePPC(data) {
                const completed = data.filter(item => item.status === 'Completed' && item.actual_end).length;
                const total = data.length;
                const ppc = total > 0 ? Math.round((completed / total) * 100) : 0;
                $('#ppc-value').text(`${ppc}%`);
            }

            function refreshTimeline() {
                $.ajax({
                    url: 'ajax_handler.php',
                    method: 'POST',
                    data: { action: 'get_lookahead', week: <?php echo $week; ?> },
                    success: function(data) {
                        let html = '';
                        const weeks = [<?php echo $week; ?>, <?php echo $week + 1; ?>, <?php echo $week + 2; ?>];
                        weeks.forEach(week => {
                            html += `<div class="timeline-week"><h6>Week ${week}</h6>`;
                            data.filter(item => item.week_number == week).forEach(item => {
                                html += `
                                    <div class="activity-card">
                                        <small>#${item.activity_id}</small>
                                        <div>${item.description}</div>
                                        <span class="constraint-badge ${item.constraint_flag ? 'constraint-blocked' : 'constraint-free'}">
                                            ${item.constraint_flag ? 'Blocked' : 'Free'}
                                        </span>
                                        <button class="btn btn-sm toggle-btn toggle-constraint" data-id="${item.activity_id}">
                                            <i class="bi bi-toggle-${item.constraint_flag ? 'off' : 'on'}"></i>
                                        </button>
                                        <button class="btn btn-sm toggle-btn revision-history" data-id="${item.activity_id}">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                    </div>
                                    ${item.handoff ? `<div class="handoff-arrow"><i class="bi bi-arrow-right"></i> ${item.handoff}</div>` : ''}
                                `;
                            });
                            html += `</div>`;
                        });
                        $('#lookahead-timeline').html(html);
                        calculatePPC(data);
                    },
                    error: function() {
                        showToast('Failed to fetch lookahead data', true);
                    }
                });
            }

            // Toggle constraint
            $(document).on('click', '.toggle-constraint', function() {
                const activityId = $(this).data('id');
                $('#constraintActivityId').val(activityId);
                constraintModal.show();
            });

            $('#saveConstraint').click(function() {
                $.ajax({
                    url: 'update.php',
                    method: 'POST',
                    data: $('#constraintForm').serialize() + '&action=toggle_constraint',
                    success: function(response) {
                        refreshTimeline();
                        constraintModal.hide();
                        showToast('Constraint updated successfully');
                    },
                    error: function() {
                        showToast('Failed to update constraint', true);
                    }
                });
            });

            // Revision history
            $(document).on('click', '.revision-history', function() {
                const activityId = $(this).data('id');
                $.ajax({
                    url: 'ajax_handler.php',
                    method: 'POST',
                    data: { action: 'get_revisions', activity_id: activityId },
                    success: function(data) {
                        alert('Revision History:\n' + data.map(r => `${r.changed_at}: ${r.old_status} -> ${r.new_status}`).join('\n'));
                    }
                });
            });

            // Initial load and refresh
            refreshTimeline();
            setInterval(refreshTimeline, 5000);
        });
    </script>
</body>
</html>