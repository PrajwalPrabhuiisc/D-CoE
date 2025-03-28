<?php
include 'config.php';

$stmt = $pdo->prepare("SELECT UserID, Username FROM Users WHERE Role != 'SA Team'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_POST['diary_user'];
    $taskID = $_POST['task_id'];
    $taskDescription = $_POST['task_description'];
    $taskStatus = $_POST['task_status'];
    $allocatedTime = $_POST['allocated_time'];
    $actualTime = $_POST['actual_time'] ?: null;
    $deviationReason = $_POST['deviation_reason'] ?: null;
    $personalInsights = $_POST['personal_insights'] ?: null;
    $commitments = $_POST['commitments'] ?: null;
    $generalObservations = $_POST['general_observations'] ?: null;
    $improvementSuggestions = $_POST['improvement_suggestions'] ?: null;
    $privateTaskDescription = $_POST['private_task_description'] ?: null;
    $privateTaskStatus = $_POST['private_task_status'] ?: null;
    $privateInsights = $_POST['private_insights'] ?: null;
    $isPrivate = isset($_POST['is_private']) ? 1 : 0;

    // Insert into WorkDiary
    $stmt = $pdo->prepare("
        INSERT INTO WorkDiary (UserID, TaskDescription, TaskStatus, AllocatedTime, ActualTime, DeviationReason, PersonalInsights, Commitments, GeneralObservations, ImprovementSuggestions, IsPrivate) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$userID, $taskDescription, $taskStatus, $allocatedTime, $actualTime, $deviationReason, $personalInsights, $commitments, $generalObservations, $improvementSuggestions, $isPrivate]);
    $entryID = $pdo->lastInsertId();

    // If any private fields are filled, insert into PrivateDiaryEntries
    if ($privateTaskDescription || $privateTaskStatus || $privateInsights) {
        $stmt = $pdo->prepare("
            INSERT INTO PrivateDiaryEntries (WorkDiaryEntryID, PrivateTaskDescription, PrivateTaskStatus, PrivateInsights) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$entryID, $privateTaskDescription, $privateTaskStatus, $privateInsights]);
    }

    // Update DiarySubmissions
    $stmt = $pdo->prepare("
        INSERT INTO DiarySubmissions (UserID, EntryDate, Submitted, SubmissionTime) 
        VALUES (?, CURDATE(), TRUE, NOW()) 
        ON DUPLICATE KEY UPDATE Submitted = TRUE, SubmissionTime = NOW()
    ");
    $stmt->execute([$userID]);

    header("Location: submission_confirmation.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Diary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4CAF50;
            --warning-color: #ff9800;
            --danger-color: #f44336;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            padding-bottom: 3rem;
        }
        .container { padding: 30px 15px; }
        .form-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.04);
            padding: 30px;
            margin-bottom: 25px;
            transition: transform 0.3s ease;
        }
        .form-container:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .form-label {
            font-weight: 500;
            color: #444;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            background-color: #f8fafc;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
            background-color: #fff;
        }
        textarea.form-control { min-height: 100px; }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.2);
        }
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .form-section:last-child { border-bottom: none; }
        .header-banner {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.2);
        }
        .header-content {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }
        .header-icon {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        .header-text h1 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }
        .header-text p {
            opacity: 0.9;
            margin-bottom: 0;
            font-size: 1rem;
        }
        .section-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.2rem;
            font-size: 1.2rem;
        }
        .input-group-text {
            background-color: #f8fafc;
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 10px 0 0 10px;
        }
        .privacy-toggle {
            margin-top: 0.5rem;
            padding: 0.5rem;
            background-color: #f8fafc;
            border-radius: 8px;
        }
        .private-section {
            border-left: 4px solid var(--danger-color);
            padding-left: 1rem;
            margin-top: 1rem;
        }
        .privacy-note {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="header-banner">
        <div class="container">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="header-text">
                    <h1>Submit Work Diary</h1>
                    <p>Record your tasks - mark sensitive info as private if needed.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="form-container">
            <form method="POST" id="diaryForm">
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-tasks me-2"></i>Task Information</h3>
                    <div class="mb-4">
                        <label class="form-label">Diary Submitted By</label>
                        <select name="diary_user" id="diary_user" class="form-select" required>
                            <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['UserID']; ?>">
                                    <?php echo htmlspecialchars($user['Username']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Select Task</label>
                        <select name="task_id" id="task_id" class="form-select" required>
                            <option value="">Select a user first</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Public Task Description</label>
                        <input type="text" name="task_description" id="task_description" class="form-control" placeholder="Enter public task description" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Public Task Status</label>
                        <select name="task_status" class="form-select" required>
                            <option value="Not Started">Not Started</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Blocked">Blocked</option>
                        </select>
                    </div>
                    <div class="mb-4 private-section">
                        <label class="form-label">Private Task Description</label>
                        <input type="text" name="private_task_description" class="form-control" placeholder="Enter private task details (SA Team only)">
                        <div class="privacy-note">Visible only to SA Team</div>
                    </div>
                    <div class="mb-4 private-section">
                        <label class="form-label">Private Task Status</label>
                        <select name="private_task_status" class="form-select">
                            <option value="">None</option>
                            <option value="Not Started">Not Started</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Blocked">Blocked</option>
                        </select>
                        <div class="privacy-note">Visible only to SA Team</div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-clock me-2"></i>Time Tracking</h3>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Allocated Time (hours)</label>
                            <div class="input-group">
                                <input type="number" name="allocated_time" class="form-control" placeholder="Enter allocated time" step="0.5" min="0" required>
                                <span class="input-group-text">hours</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Actual Time (hours)</label>
                            <div class="input-group">
                                <input type="number" name="actual_time" class="form-control" placeholder="Enter actual time" step="0.5" min="0">
                                <span class="input-group-text">hours</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Deviation Reason</label>
                        <textarea name="deviation_reason" class="form-control" placeholder="Explain any time deviation..."></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-lightbulb me-2"></i>Feedback</h3>
                    <div class="mb-4">
                        <label class="form-label">Public Insights</label>
                        <textarea name="personal_insights" class="form-control" placeholder="Share insights visible to all..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Commitments</label>
                        <textarea name="commitments" class="form-control" placeholder="What are you committing to next?"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">General Observations</label>
                        <textarea name="general_observations" class="form-control" placeholder="Any general observations?"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Improvement Suggestions</label>
                        <textarea name="improvement_suggestions" class="form-control" placeholder="Suggestions for improvement?"></textarea>
                    </div>
                    <div class="mb-4 private-section">
                        <label class="form-label">Private Insights</label>
                        <textarea name="private_insights" class="form-control" placeholder="Confidential notes (SA Team only)..."></textarea>
                        <div class="privacy-note">Visible only to SA Team</div>
                    </div>
                    <div class="privacy-toggle">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_private" id="is_private">
                            <label class="form-check-label" for="is_private">
                                Make entire entry private (SA Team only)
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Submit Diary Entry
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#diary_user').change(function() {
            var userId = $(this).val();
            if (userId) {
                $.ajax({
                    url: 'fetch_tasks.php',
                    type: 'POST',
                    data: {user_id: userId},
                    dataType: 'json',
                    success: function(response) {
                        var taskSelect = $('#task_id');
                        taskSelect.empty();
                        taskSelect.append('<option value="">Select a task</option>');
                        if (response.tasks && response.tasks.length > 0) {
                            $.each(response.tasks, function(index, task) {
                                taskSelect.append(
                                    $('<option>').val(task.TaskID).text(task.TaskName)
                                );
                            });
                        } else {
                            taskSelect.append(
                                '<option value="">No tasks found</option>'
                            );
                        }
                    },
                    error: function() {
                        alert('Error fetching tasks');
                    }
                });
            } else {
                $('#task_id').html('<option value="">Select a user first</option>');
            }
        });

        $('#task_id').change(function() {
            var selectedTask = $(this).find('option:selected').text();
            if (selectedTask && selectedTask !== 'Select a task' && selectedTask !== 'No tasks found') {
                $('#task_description').val(selectedTask);
            }
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>