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

    $stmt = $pdo->prepare("
        INSERT INTO WorkDiary (UserID, TaskDescription, TaskStatus, AllocatedTime, ActualTime, DeviationReason, PersonalInsights, Commitments, GeneralObservations, ImprovementSuggestions) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$userID, $taskDescription, $taskStatus, $allocatedTime, $actualTime, $deviationReason, $personalInsights, $commitments, $generalObservations, $improvementSuggestions]);
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
            --primary-color: #4a6cf7;
            --secondary-color: #3d55f0;
            --accent-color: #6ec1e4;
            --light-color: #f9fafb;
            --dark-color: #1f2937;
            --success-color: #22c55e;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --shadow-light: 0 4px 12px rgba(0,0,0,0.05);
            --shadow-hover: 0 6px 18px rgba(0,0,0,0.1);
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
            padding-bottom: 3rem;
            overflow-x: hidden;
        }
        .container { padding: 30px 15px; max-width: 800px; }
        .form-container {
            background-color: white;
            border-radius: 16px;
            box-shadow: var(--shadow-light);
            padding: 2rem;
            transition: all 0.3s ease;
        }
        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            display: block;
        }
        .form-control, .form-select {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            background-color: #f9fafb;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(74, 108, 247, 0.2);
            background-color: white;
        }
        textarea.form-control { min-height: 120px; resize: vertical; }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #e5e7eb;
            position: relative;
        }
        .form-section:last-child { border-bottom: none; }
        .progress-bar-container {
            position: sticky;
            top: 0;
            background: white;
            padding: 1rem 0;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .progress {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-bar {
            background-color: var(--primary-color);
            transition: width 0.3s ease;
        }
        .header-banner {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2.5rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(74, 108, 247, 0.2);
        }
        .header-content { display: flex; align-items: center; gap: 1.5rem; }
        .header-icon {
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            transition: transform 0.3s ease;
        }
        .header-icon:hover { transform: rotate(15deg); }
        .header-text h1 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }
        .header-text p { opacity: 0.9; margin-bottom: 0; font-size: 1.1rem; }
        .section-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.2rem;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .input-group-text {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px 0 0 8px;
        }
        .private-section {
            border-left: 4px solid var(--danger-color);
            padding-left: 1rem;
            margin-top: 1rem;
            background-color: #fef2f2;
            border-radius: 8px;
            padding: 1rem;
            transition: max-height 0.3s ease;
            overflow: hidden;
        }
        .private-toggle {
            font-weight: 500;
            color: var(--danger-color);
            cursor: pointer;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .private-toggle i { transition: transform 0.3s ease; }
        .private-toggle.collapsed i { transform: rotate(-90deg); }
        .privacy-note {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
        }
        .invalid-feedback {
            display: none;
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .form-control:invalid ~ .invalid-feedback,
        .form-select:invalid ~ .invalid-feedback { display: block; }
        .required { color: var(--danger-color); }
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
                    <p>Record your tasks with optional private notes for SA Team.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="progress-bar-container">
        <div class="container">
            <div class="progress">
                <div class="progress-bar" id="progressBar" style="width: 0%;"></div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="form-container">
            <form method="POST" id="diaryForm" novalidate>
                <div class="form-section" data-section="1">
                    <h3 class="section-title"><i class="fas fa-tasks"></i> Task Information</h3>
                    <div class="mb-4">
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
                    <div class="mb-4">
                        <label class="form-label">Select Task <span class="required">*</span></label>
                        <select name="task_id" id="task_id" class="form-select" required>
                            <option value="">Select a user first</option>
                        </select>
                        <div class="invalid-feedback">Please select a task.</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Public Task Description <span class="required">*</span></label>
                        <input type="text" name="task_description" id="task_description" class="form-control" placeholder="Enter public task description" required>
                        <div class="invalid-feedback">Please enter a task description.</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Public Task Status <span class="required">*</span></label>
                        <select name="task_status" class="form-select" required>
                            <option value="Not Started">Not Started</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Blocked">Blocked</option>
                        </select>
                        <div class="invalid-feedback">Please select a task status.</div>
                    </div>
                    <div class="private-section" id="privateSection">
                        <div class="private-toggle collapsed" data-toggle="collapse" data-target="#privateFields">
                            <i class="fas fa-lock"></i> Private Details (SA Team Only)
                        </div>
                        <div id="privateFields" class="collapse">
                            <div class="mb-4">
                                <label class="form-label">Private Task Description</label>
                                <input type="text" name="private_task_description" class="form-control" placeholder="Enter private task details">
                                <div class="privacy-note">Visible only to SA Team</div>
                            </div>
                            <div class="mb-4">
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
                    </div>
                </div>
                
                <div class="form-section" data-section="2">
                    <h3 class="section-title"><i class="fas fa-clock"></i> Time Tracking</h3>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Allocated Time (hours) <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="number" name="allocated_time" class="form-control" placeholder="Enter allocated time" step="0.5" min="0" required>
                                <span class="input-group-text">hours</span>
                            </div>
                            <div class="invalid-feedback">Please enter allocated time.</div>
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
                
                <div class="form-section" data-section="3">
                    <h3 class="section-title"><i class="fas fa-lightbulb"></i> Feedback</h3>
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
                    <div class="private-section" id="privateFeedbackSection">
                        <div class="private-toggle collapsed" data-toggle="collapse" data-target="#privateFeedbackFields">
                            <i class="fas fa-lock"></i> Private Insights (SA Team Only)
                        </div>
                        <div id="privateFeedbackFields" class="collapse">
                            <div class="mb-4">
                                <label class="form-label">Private Insights</label>
                                <textarea name="private_insights" class="form-control" placeholder="Confidential notes (SA Team only)..."></textarea>
                                <div class="privacy-note">Visible only to SA Team</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Submit Diary
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        const sections = $('.form-section');
        const progressBar = $('#progressBar');
        let currentSection = 1;

        function updateProgress() {
            const progress = ((currentSection - 1) / (sections.length - 1)) * 100;
            progressBar.css('width', `${progress}%`);
        }

        sections.each(function(index) {
            $(this).data('index', index + 1);
        });

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
                        $('#task_id').html('<option value="">Select a user first</option>').addClass('is-invalid');
                    }
                });
            } else {
                $('#task_id').html('<option value="">Select a user first</option>').removeClass('is-invalid');
            }
        });

        $('#task_id').change(function() {
            const selectedTask = $(this).find('option:selected').text();
            $('#task_description').val(selectedTask !== 'Select a task' && selectedTask !== 'No tasks found' ? selectedTask : '')
                               .toggleClass('is-invalid', !selectedTask || selectedTask === 'Select a task' || selectedTask === 'No tasks found');
        });

        $('.private-toggle').click(function() {
            const target = $($(this).data('target'));
            const icon = $(this).find('i');
            target.collapse('toggle');
            $(this).toggleClass('collapsed');
            icon.toggleClass('fa-rotate-90');
        });

        $(window).scroll(function() {
            sections.each(function() {
                const sectionTop = $(this).offset().top;
                const sectionBottom = sectionTop + $(this).outerHeight();
                const windowTop = $(window).scrollTop();
                const windowBottom = windowTop + $(window).height();
                if (windowTop >= sectionTop && windowBottom <= sectionBottom) {
                    currentSection = $(this).data('index');
                    updateProgress();
                }
            });
        });

        updateProgress();
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
