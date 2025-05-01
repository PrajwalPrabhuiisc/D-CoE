<?php
include 'config.php';

// Fetch users with role 'Team Member' for the dropdown
try {
    $stmt = $pdo->prepare("SELECT UserID, Username FROM Users WHERE Role IN ('Team Member')");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $users = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $userID = $_POST['diary_user'] ?? null;
        $taskIDs = $_POST['task_id'] ?? [];
        $taskDescriptions = $_POST['task_description'] ?? [];
        $taskStatuses = $_POST['task_status'] ?? [];
        $allocatedTimes = $_POST['allocated_time'] ?? [];
        $actualTimes = $_POST['actual_time'] ?? [];
        $deviationReasons = $_POST['deviation_reason'] ?? [];
        $personalInsights = $_POST['personal_insights'] ?? [];
        $commitments = $_POST['commitments'] ?? [];
        $generalObservations = $_POST['general_observations'] ?? [];
        $improvementSuggestions = $_POST['improvement_suggestions'] ?? [];
        $privateTaskDescriptions = $_POST['private_task_description'] ?? [];
        $privateTaskStatuses = $_POST['private_task_status'] ?? [];
        $privateInsights = $_POST['private_insights'] ?? [];

        // Validate user ID
        if (!$userID) {
            throw new Exception("User selection is required.");
        }

        // Ensure at least one task is provided
        if (empty($taskIDs)) {
            throw new Exception("At least one task must be provided.");
        }

        // Begin transaction
        $pdo->beginTransaction();

        // Process each task entry
        foreach ($taskIDs as $index => $taskID) {
            // Validate required fields for each task
            if (
                empty($taskDescriptions[$index]) ||
                empty($taskStatuses[$index]) ||
                empty($allocatedTimes[$index])
            ) {
                throw new Exception("All required fields must be filled for task " . ($index + 1) . ".");
            }

            // If taskID is 'others', treat as custom task
            if ($taskID !== 'others') {
                // Verify TaskID exists for non-custom tasks
                $stmt = $pdo->prepare("SELECT TaskID FROM ProjectTasks WHERE TaskID = ?");
                $stmt->execute([$taskID]);
                if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                    throw new Exception("Invalid Task ID for task " . ($index + 1) . ".");
                }
            }

            // Insert into WorkDiary table
            $stmt = $pdo->prepare("
                INSERT INTO WorkDiary (
                    UserID, TaskID, TaskDescription, TaskStatus, AllocatedTime, ActualTime, DeviationReason, 
                    PersonalInsights, Commitments, GeneralObservations, ImprovementSuggestions
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userID,
                $taskID === 'others' ? null : $taskID,
                $taskDescriptions[$index],
                $taskStatuses[$index],
                $allocatedTimes[$index],
                $actualTimes[$index] ?? null,
                $deviationReasons[$index] ?? null,
                $personalInsights[$index] ?? null,
                $commitments[$index] ?? null,
                $generalObservations[$index] ?? null,
                $improvementSuggestions[$index] ?? null
            ]);
            $entryID = $pdo->lastInsertId();

            // Insert into PrivateDiaryEntries if private fields are provided
            if (
                !empty($privateTaskDescriptions[$index]) ||
                !empty($privateTaskStatuses[$index]) ||
                !empty($privateInsights[$index])
            ) {
                $stmt = $pdo->prepare("
                    INSERT INTO PrivateDiaryEntries (
                        WorkDiaryEntryID, PrivateTaskDescription, PrivateTaskStatus, PrivateInsights
                    ) VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $entryID,
                    $privateTaskDescriptions[$index] ?? null,
                    $privateTaskStatuses[$index] ?? null,
                    $privateInsights[$index] ?? null
                ]);
            }
        }

        // Update or insert into DiarySubmissions
        $stmt = $pdo->prepare("
            INSERT INTO DiarySubmissions (UserID, EntryDate, Submitted, SubmissionTime) 
            VALUES (?, CURDATE(), TRUE, NOW()) 
            ON DUPLICATE KEY UPDATE Submitted = TRUE, SubmissionTime = NOW()
        ");
        $stmt->execute([$userID]);

        // Commit transaction
        $pdo->commit();

        header("Location: submission_confirmation.php");
        exit;
    } catch (Exception $e) {
        // Roll back transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Submission error: " . $e->getMessage());
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Diary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding-bottom: 2rem;
        }
        .container {
            max-width: 800px;
            padding: 20px 15px;
        }
        .form-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-top: 20px;
        }
        .header-banner {
            background-color: #4a6cf7;
            color: white;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
        }
        .section-title {
            font-weight: 600;
            color: #4a6cf7;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        .form-section {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        .task-section {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            position: relative;
        }
        .task-section .remove-task {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #dc3545;
            cursor: pointer;
            font-size: 1.2rem;
        }
        .task-section .remove-task:hover {
            color: #b02a37;
        }
        .private-section {
            border-left: 3px solid #dc3545;
            padding-left: 1rem;
            margin-top: 1rem;
            background-color: #fff8f8;
            border-radius: 4px;
            padding: 1rem;
        }
        .required {
            color: #dc3545;
        }
        .btn-primary {
            background-color: #4a6cf7;
            border-color: #4a6cf7;
        }
        .btn-primary:hover {
            background-color: #3d55f0;
            border-color: #3d55f0;
        }
        .form-control[readonly]:not(.custom-task) {
            background-color: #e9ecef;
        }
        .add-task-btn {
            margin-bottom: 1.5rem;
        }
        .is-invalid {
            border-color: #dc3545;
        }
        .invalid-feedback {
            display: none;
        }
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="header-banner">
            <h1>Submit Work Diary</h1>
            <p>Record your tasks with optional private notes for SA Team. Add multiple tasks as needed.</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" id="diaryForm" novalidate>
                <div class="form-section">
                    <h3 class="section-title">User Information</h3>
                    <div class="mb-3">
                        <label class="form-label">Diary Submitted By <span class="required">*</span></label>
                        <select name="diary_user" id="diary_user" class="form-select" required>
                            <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['UserID']; ?>">
                                    <?php echo htmlspecialchars($user['Username']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select a user.</div>
                    </div>
                </div>

                <div id="task-sections">
                    <!-- Initial task section -->
                    <div class="task-section">
                        <span class="remove-task" title="Remove Task">&times;</span>
                        <h3 class="section-title">Task Information</h3>
                        <div class="mb-3">
                            <label class="form-label">Select Task <span class="required">*</span></label>
                            <select name="task_id[]" class="form-select task-id" required>
                                <option value="">Select a user first</option>
                            </select>
                            <div class="invalid-feedback">Please select a task.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Task Description <span class="required">*</span></label>
                            <input type="text" name="task_description[]" class="form-control task-description" readonly required>
                            <div class="invalid-feedback">Task description is required.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Task Status <span class="required">*</span></label>
                            <select name="task_status[]" class="form-select" required>
                                <option value="Not Started">Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Blocked">Blocked</option>
                            </select>
                            <div class="invalid-feedback">Please select a task status.</div>
                        </div>
                        <div class="private-section">
                            <h4>Private Details (SA Team Only)</h4>
                            <div class="mb-3">
                                <label class="form-label">Private Task Description</label>
                                <input type="text" name="private_task_description[]" class="form-control" placeholder="Enter private task details">
                                <div class="form-text">Visible only to SA Team</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Private Task Status</label>
                                <select name="private_task_status[]" class="form-select">
                                    <option value="">None</option>
                                    <option value="Not Started">Not Started</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Blocked">Blocked</option>
                                </select>
                            </div>
                        </div>
                        <h3 class="section-title">Time Tracking</h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Allocated Time (hours) <span class="required">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="allocated_time[]" class="form-control" placeholder="Enter allocated time" step="0.5" min="0" required>
                                    <span class="input-group-text">hours</span>
                                </div>
                                <div class="invalid-feedback">Allocated time is required.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Actual Time (hours)</label>
                                <div class="input-group">
                                    <input type="number" name="actual_time[]" class="form-control" placeholder="Enter actual time" step="0.5" min="0">
                                    <span class="input-group-text">hours</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deviation Reason</label>
                            <textarea name="deviation_reason[]" class="form-control" placeholder="Explain any time deviation..."></textarea>
                        </div>
                        <h3 class="section-title">Feedback</h3>
                        <div class="mb-3">
                            <label class="form-label">Public Insights</label>
                            <textarea name="personal_insights[]" class="form-control" placeholder="Share insights visible to all..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Commitments</label>
                            <textarea name="commitments[]" class="form-control" placeholder="What are you committing to next?"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">General Observations</label>
                            <textarea name="general_observations[]" class="form-control" placeholder="Any general observations?"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Improvement Suggestions</label>
                            <textarea name="improvement_suggestions[]" class="form-control" placeholder="Suggestions for improvement?"></textarea>
                        </div>
                        <div class="private-section">
                            <h4>Private Insights (SA Team Only)</h4>
                            <div class="mb-3">
                                <label class="form-label">Private Insights</label>
                                <textarea name="private_insights[]" class="form-control" placeholder="Confidential notes (SA Team only)..."></textarea>
                                <div class="form-text">Visible only to SA Team</div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-primary add-task-btn">Add Another Task</button>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Submit Diary</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Template for new task section
        const taskSectionTemplate = `
            <div class="task-section">
                <span class="remove-task" title="Remove Task">&times;</span>
                <h3 class="section-title">Task Information</h3>
                <div class="mb-3">
                    <label class="form-label">Select Task <span class="required">*</span></label>
                    <select name="task_id[]" class="form-select task-id" required>
                        <option value="">Select a user first</option>
                    </select>
                    <div class="invalid-feedback">Please select a task.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Task Description <span class="required">*</span></label>
                    <input type="text" name="task_description[]" class="form-control task-description" readonly required>
                    <div class="invalid-feedback">Task description is required.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Task Status <span class="required">*</span></label>
                    <select name="task_status[]" class="form-select" required>
                        <option value="Not Started">Not Started</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Blocked">Blocked</option>
                    </select>
                    <div class="invalid-feedback">Please select a task status.</div>
                </div>
                <div class="private-section">
                    <h4>Private Details (SA Team Only)</h4>
                    <div class="mb-3">
                        <label class="form-label">Private Task Description</label>
                        <input type="text" name="private_task_description[]" class="form-control" placeholder="Enter private task details">
                        <div class="form-text">Visible only to SA Team</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Private Task Status</label>
                        <select name="private_task_status[]" class="form-select">
                            <option value="">None</option>
                            <option value="Not Started">Not Started</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Blocked">Blocked</option>
                        </select>
                    </div>
                </div>
                <h3 class="section-title">Time Tracking</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Allocated Time (hours) <span class="required">*</span></label>
                        <div class="input-group">
                            <input type="number" name="allocated_time[]" class="form-control" placeholder="Enter allocated time" step="0.5" min="0" required>
                            <span class="input-group-text">hours</span>
                        </div>
                        <div class="invalid-feedback">Allocated time is required.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Actual Time (hours)</label>
                        <div class="input-group">
                            <input type="number" name="actual_time[]" class="form-control" placeholder="Enter actual time" step="0.5" min="0">
                            <span class="input-group-text">hours</span>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deviation Reason</label>
                    <textarea name="deviation_reason[]" class="form-control" placeholder="Explain any time deviation..."></textarea>
                </div>
                <h3 class="section-title">Feedback</h3>
                <div class="mb-3">
                    <label class="form-label">Public Insights</label>
                    <textarea name="personal_insights[]" class="form-control" placeholder="Share insights visible to all..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Commitments</label>
                    <textarea name="commitments[]" class="form-control" placeholder="What are you committing to next?"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">General Observations</label>
                    <textarea name="general_observations[]" class="form-control" placeholder="Any general observations?"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Improvement Suggestions</label>
                    <textarea name="improvement_suggestions[]" class="form-control" placeholder="Suggestions for improvement?"></textarea>
                </div>
                <div class="private-section">
                    <h4>Private Insights (SA Team Only)</h4>
                    <div class="mb-3">
                        <label class="form-label">Private Insights</label>
                        <textarea name="private_insights[]" class="form-control" placeholder="Confidential notes (SA Team only)..."></textarea>
                        <div class="form-text">Visible only to SA Team</div>
                    </div>
                </div>
            </div>
        `;

        // Add new task section
        $('.add-task-btn').click(function() {
            $('#task-sections').append(taskSectionTemplate);
            // Re-fetch tasks for the new section if a user is selected
            const userId = $('#diary_user').val();
            if (userId) {
                fetchTasks(userId, $('#task-sections .task-section:last .task-id'));
            }
        });

        // Remove task section
        $(document).on('click', '.remove-task', function() {
            if ($('.task-section').length > 1) {
                $(this).closest('.task-section').remove();
            } else {
                alert('At least one task is required.');
            }
        });

        // Form validation
        $('#diaryForm').on('submit', function(event) {
            let isValid = true;

            // Validate user selection
            if (!$('#diary_user').val()) {
                $('#diary_user').addClass('is-invalid');
                isValid = false;
            } else {
                $('#diary_user').removeClass('is-invalid');
            }

            // Validate each task section
            $('.task-section').each(function() {
                const section = $(this);
                section.find('select[required], input[required]').each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
            });

            if (!isValid) {
                event.preventDefault();
                alert('Please fill in all required fields.');
            }
        });

        // Fetch tasks for a specific dropdown
        function fetchTasks(userId, taskSelect) {
            $.ajax({
                url: 'fetch_tasks.php',
                type: 'POST',
                data: { user_id: userId },
                dataType: 'json',
                success: function(response) {
                    taskSelect.empty().append('<option value="">Select a task</option>');
                    if (response.tasks && response.tasks.length > 0) {
                        $.each(response.tasks, function(index, task) {
                            taskSelect.append($('<option>').val(task.TaskID).text(task.TaskName));
                        });
                    } else {
                        taskSelect.append('<option value="">No open tasks available</option>');
                    }
                    // Add Others option
                    taskSelect.append($('<option>').val('others').text('Others'));
                    taskSelect.removeClass('is-invalid');
                    taskSelect.closest('.task-section').find('.task-description').val('').removeClass('is-invalid').prop('readonly', true).removeClass('custom-task');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error, xhr.responseText);
                    alert('Error fetching tasks. Please try again.');
                    taskSelect.html('<option value="">Error loading tasks</option>').addClass('is-invalid');
                    taskSelect.closest('.task-section').find('.task-description').val('').addClass('is-invalid').prop('readonly', true).removeClass('custom-task');
                }
            });
        }

        // AJAX for task loading when user changes
        $('#diary_user').change(function() {
            const userId = $(this).val();
            if (userId) {
                $('.task-section').each(function() {
                    const taskSelect = $(this).find('.task-id');
                    fetchTasks(userId, taskSelect);
                });
            } else {
                $('.task-section').each(function() {
                    const taskSelect = $(this).find('.task-id');
                    taskSelect.html('<option value="">Select a user first</option>').removeClass('is-invalid');
                    $(this).find('.task-description').val('').removeClass('is-invalid').prop('readonly', true).removeClass('custom-task');
                });
            }
        });

        // Handle task selection
        $(document).on('change', '.task-id', function() {
            const selectedValue = $(this).val();
            const selectedText = $(this).find('option:selected').text();
            const taskDescriptionInput = $(this).closest('.task-section').find('.task-description');
            
            if (selectedValue === 'others') {
                // Allow custom task description
                taskDescriptionInput.val('').prop('readonly', false).addClass('custom-task').focus();
            } else if (
                selectedText !== 'Select a task' &&
                selectedText !== 'No open tasks available' &&
                selectedText !== 'Error loading tasks' &&
                selectedText !== 'Select a user first'
            ) {
                // Auto-fill with predefined task name
                taskDescriptionInput.val(selectedText).prop('readonly', true).removeClass('custom-task').removeClass('is-invalid');
            } else {
                // Clear and make readonly for invalid selections
                taskDescriptionInput.val('').prop('readonly', true).removeClass('custom-task').addClass('is-invalid');
            }
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
