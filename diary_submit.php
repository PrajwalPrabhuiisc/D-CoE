<?php
include 'config.php';

// Error handling to catch issues
try {
    $stmt = $pdo->prepare("SELECT UserID, Username FROM Users WHERE Role IN ('Team Member')");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $users = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $userID = $_POST['diary_user'] ?? null;
        $taskID = $_POST['task_id'] ?? null;
        $taskDescription = $_POST['task_description'] ?? null;
        $taskStatus = $_POST['task_status'] ?? null;
        $allocatedTime = $_POST['allocated_time'] ?? null;
        $actualTime = !empty($_POST['actual_time']) ? $_POST['actual_time'] : null;
        $deviationReason = !empty($_POST['deviation_reason']) ? $_POST['deviation_reason'] : null;
        $personalInsights = !empty($_POST['personal_insights']) ? $_POST['personal_insights'] : null;
        $commitments = !empty($_POST['commitments']) ? $_POST['commitments'] : null;
        $generalObservations = !empty($_POST['general_observations']) ? $_POST['general_observations'] : null;
        $improvementSuggestions = !empty($_POST['improvement_suggestions']) ? $_POST['improvement_suggestions'] : null;
        $privateTaskDescription = !empty($_POST['private_task_description']) ? $_POST['private_task_description'] : null;
        $privateTaskStatus = !empty($_POST['private_task_status']) ? $_POST['private_task_status'] : null;
        $privateInsights = !empty($_POST['private_insights']) ? $_POST['private_insights'] : null;

        $stmt = $pdo->prepare("
            INSERT INTO WorkDiary (UserID, TaskDescription, TaskStatus, AllocatedTime, ActualTime, DeviationReason, 
                                  PersonalInsights, Commitments, GeneralObservations, ImprovementSuggestions) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$userID, $taskDescription, $taskStatus, $allocatedTime, $actualTime, $deviationReason, 
                       $personalInsights, $commitments, $generalObservations, $improvementSuggestions]);
        $entryID = $pdo->lastInsertId();

        if ($privateTaskDescription || $privateTaskStatus || $privateInsights) {
            $stmt = $pdo->prepare("
                INSERT INTO PrivateDiaryEntries (WorkDiaryEntryID, PrivateTaskDescription, PrivateTaskStatus, PrivateInsights) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$entryID, $privateTaskDescription, $privateTaskStatus, $privateInsights]);
        }

        $stmt = $pdo->prepare("
            INSERT INTO DiarySubmissions (UserID, EntryDate, Submitted, SubmissionTime) 
            VALUES (?, CURDATE(), TRUE, NOW()) 
            ON DUPLICATE KEY UPDATE Submitted = TRUE, SubmissionTime = NOW()
        ");
        $stmt->execute([$userID]);

        header("Location: submission_confirmation.php");
        exit;
    } catch (PDOException $e) {
        error_log("Submission error: " . $e->getMessage());
        $error = "There was an error submitting your diary. Please try again.";
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
        .form-section:last-child {
            border-bottom: none;
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
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="header-banner">
            <h1>Submit Work Diary</h1>
            <p>Record your tasks with optional private notes for SA Team.</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" id="diaryForm" novalidate>
                <div class="form-section">
                    <h3 class="section-title">Task Information</h3>
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
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Task <span class="required">*</span></label>
                        <select name="task_id" id="task_id" class="form-select" required>
                            <option value="">Select a user first</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Public Task Description <span class="required">*</span></label>
                        <input type="text" name="task_description" id="task_description" class="form-control" placeholder="Enter public task description" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Public Task Status <span class="required">*</span></label>
                        <select name="task_status" class="form-select" required>
                            <option value="Not Started">Not Started</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Blocked">Blocked</option>
                        </select>
                    </div>
                    <div class="private-section">
                        <h4>Private Details (SA Team Only)</h4>
                        <div class="mb-3">
                            <label class="form-label">Private Task Description</label>
                            <input type="text" name="private_task_description" class="form-control" placeholder="Enter private task details">
                            <div class="form-text">Visible only to SA Team</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Private Task Status</label>
                            <select name="private_task_status" class="form-select">
                                <option value="">None</option>
                                <option value="Not Started">Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Blocked">Blocked</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3 class="section-title">Time Tracking</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Allocated Time (hours) <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="number" name="allocated_time" class="form-control" placeholder="Enter allocated time" step="0.5" min="0" required>
                                <span class="input-group-text">hours</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Actual Time (hours)</label>
                            <div class="input-group">
                                <input type="number" name="actual_time" class="form-control" placeholder="Enter actual time" step="0.5" min="0">
                                <span class="input-group-text">hours</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deviation Reason</label>
                        <textarea name="deviation_reason" class="form-control" placeholder="Explain any time deviation..."></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3 class="section-title">Feedback</h3>
                    <div class="mb-3">
                        <label class="form-label">Public Insights</label>
                        <textarea name="personal_insights" class="form-control" placeholder="Share insights visible to all..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Commitments</label>
                        <textarea name="commitments" class="form-control" placeholder="What are you committing to next?"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">General Observations</label>
                        <textarea name="general_observations" class="form-control" placeholder="Any general observations?"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Improvement Suggestions</label>
                        <textarea name="improvement_suggestions" class="form-control" placeholder="Suggestions for improvement?"></textarea>
                    </div>
                    <div class="private-section">
                        <h4>Private Insights (SA Team Only)</h4>
                        <div class="mb-3">
                            <label class="form-label">Private Insights</label>
                            <textarea name="private_insights" class="form-control" placeholder="Confidential notes (SA Team only)..."></textarea>
                            <div class="form-text">Visible only to SA Team</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Submit Diary</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Form validation
        $('#diaryForm').on('submit', function(event) {
            let isValid = true;
            $(this).find('select[required], input[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            if (!isValid) {
                event.preventDefault();
                alert('Please fill in all required fields.');
            }
        });

        // AJAX for task loading
        $('#diary_user').change(function() {
            const userId = $(this).val();
            if (userId) {
                $.ajax({
                    url: 'fetch_tasks.php',
                    type: 'POST',
                    data: {user_id: userId},
                    dataType: 'json',
                    success: function(response) {
                        const taskSelect = $('#task_id');
                        taskSelect.empty().append('<option value="">Select a task</option>');
                        
                        if (response.tasks && response.tasks.length > 0) {
                            $.each(response.tasks, function(index, task) {
                                taskSelect.append($('<option>').val(task.TaskID).text(task.TaskName));
                            });
                        } else {
                            taskSelect.append('<option value="">No tasks found</option>');
                        }
                        
                        taskSelect.removeClass('is-invalid');
                    },
                    error: function() {
                        alert('Error fetching tasks');
                        $('#task_id').html('<option value="">Error loading tasks</option>');
                    }
                });
            } else {
                $('#task_id').html('<option value="">Select a user first</option>');
            }
        });

        // Auto-fill task description based on selection
        $('#task_id').change(function() {
            const selectedTask = $(this).find('option:selected').text();
            if (selectedTask !== 'Select a task' && selectedTask !== 'No tasks found' && selectedTask !== 'Error loading tasks') {
                $('#task_description').val(selectedTask);
            } else {
                $('#task_description').val('');
            }
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
