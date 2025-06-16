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
        if (empty($taskDescriptions)) {
            throw new Exception("At least one task must be provided.");
        }

        // Begin transaction
        $pdo->beginTransaction();

        // Process each task entry
        foreach ($taskDescriptions as $index => $taskDescription) {
            // Validate required fields for each task
            if (
                empty($taskDescription) ||
                empty($taskStatuses[$index]) ||
                empty($allocatedTimes[$index])
            ) {
                throw new Exception("All required fields must be filled for task " . ($index + 1) . ".");
            }

            // Insert into WorkDiary table
            $stmt = $pdo->prepare("
                INSERT INTO WorkDiary (
                    UserID, TaskDescription, TaskStatus, AllocatedTime, ActualTime, DeviationReason, 
                    PersonalInsights, Commitments, GeneralObservations, ImprovementSuggestions
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userID,
                $taskDescription,
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

        // Clear session storage only on successful submission
        echo '<script>sessionStorage.removeItem("diary_form_data");</script>';
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
    <title>Submit Work Diary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a6cf7;
            --primary-dark: #3d55f0;
            --secondary-color: #f8f9fa;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-gray: #e9ecef;
            --border-radius: 12px;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.12);
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
            line-height: 1.6;
        }

        .main-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .header-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            text-align: center;
        }

        .header-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-section p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        .form-wrapper {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .progress-indicator {
            background: var(--light-gray);
            height: 4px;
            position: relative;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--success-color));
            width: 0%;
            transition: var(--transition);
        }

        .form-content {
            padding: 2rem;
        }

        .user-selection-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid var(--light-gray);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
            transition: var(--transition);
        }

        .user-selection-card:hover {
            border-color: var(--primary-color);
            box-shadow: var(--shadow-sm);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .section-title i {
            font-size: 1.1rem;
        }

        .task-card {
            background: white;
            border: 2px solid var(--light-gray);
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: var(--transition);
            position: relative;
        }

        .task-card:hover {
            border-color: var(--primary-color);
            box-shadow: var(--shadow-sm);
        }

        .task-card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .task-number {
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .remove-task-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }

        .remove-task-btn:hover {
            background: rgba(220, 53, 69, 0.8);
            transform: translateY(-50%) scale(1.1);
        }

        .task-card-body {
            padding: 1.5rem;
        }

        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--light-gray);
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .subsection-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .required {
            color: var(--danger-color);
        }

        .form-control, .form-select {
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(74, 108, 247, 0.25);
        }

        .input-group {
            position: relative;
        }

        .input-group-text {
            background: var(--light-gray);
            border: 2px solid var(--light-gray);
            border-left: none;
            color: #6c757d;
            font-weight: 500;
        }

        .private-section {
            background: linear-gradient(135deg, #fff8f8 0%, #ffeaea 100%);
            border: 2px solid #f8d7da;
            border-left: 4px solid var(--danger-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-top: 1.5rem;
            position: relative;
        }

        .private-section::before {
            content: '\f023';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: -12px;
            left: 20px;
            background: var(--danger-color);
            color: white;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .private-title {
            color: var(--danger-color);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .add-task-section {
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px dashed var(--light-gray);
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            transition: var(--transition);
        }

        .add-task-section:hover {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, #e7f3ff 0%, #cce7ff 100%);
        }

        .submit-section {
            background: linear-gradient(135deg, var(--success-color) 0%, #20c997 100%);
            padding: 2rem;
            text-align: center;
            border-radius: var(--border-radius);
            margin-top: 2rem;
        }

        .submit-section .btn {
            background: white;
            color: var(--success-color);
            border: none;
            font-size: 1.1rem;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 700;
            transition: var(--transition);
        }

        .submit-section .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .is-invalid {
            border-color: var(--danger-color) !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }

        .invalid-feedback {
            display: none;
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .is-invalid ~ .invalid-feedback {
            display: block;
        }

        .alert {
            border: none;
            border-radius: var(--border-radius);
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
            color: #721c24;
        }

        .form-text {
            font-size: 0.85rem;
            color: #6c757d;
            font-style: italic;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .task-counter {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            box-shadow: var(--shadow-md);
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .header-section {
                padding: 1.5rem;
            }
            
            .header-section h1 {
                font-size: 2rem;
            }
            
            .form-content {
                padding: 1.5rem;
            }
            
            .task-counter {
                position: static;
                display: inline-block;
                margin-bottom: 1rem;
            }
        }

        .floating-save-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--success-color);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            box-shadow: var(--shadow-md);
            z-index: 1001;
            opacity: 0;
            transform: translateY(20px);
            transition: var(--transition);
        }

        .floating-save-indicator.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="main-container">
        <div class="header-section">
            <h1><i class="fas fa-clipboard-list"></i> Work Diary Submission</h1>
            <p>Record your daily tasks with comprehensive tracking and optional private notes for the SA Team</p>
        </div>

        <div class="task-counter">
            <i class="fas fa-tasks"></i> Tasks: <span id="task-count">1</span>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="form-wrapper">
            <div class="progress-indicator">
                <div class="progress-bar" id="form-progress"></div>
            </div>
            
            <div class="form-content">
                <form method="POST" id="diaryForm" novalidate>
                    <!-- User Selection Section -->
                    <div class="user-selection-card">
                        <h3 class="section-title">
                            <i class="fas fa-user"></i>
                            User Information
                        </h3>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user-tag"></i>
                                Diary Submitted By <span class="required">*</span>
                            </label>
                            <select name="diary_user" id="diary_user" class="form-select" required>
                                <option value="">Select User</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['UserID']; ?>">
                                        <?php echo htmlspecialchars($user['Username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a user to continue.</div>
                        </div>
                    </div>

                    <!-- Task Sections Container -->
                    <div id="task-sections">
                        <!-- Initial task section will be inserted here -->
                    </div>

                    <!-- Add Task Section -->
                    <div class="add-task-section">
                        <button type="button" class="btn btn-primary add-task-btn">
                            <i class="fas fa-plus"></i>
                            Add Another Task
                        </button>
                        <p class="text-muted mt-2 mb-0">Click to add additional tasks to your diary entry</p>
                    </div>

                    <!-- Submit Section -->
                    <div class="submit-section">
                        <button type="submit" class="btn">
                            <i class="fas fa-paper-plane"></i>
                            Submit Work Diary
                        </button>
                        <p class="text-white mt-3 mb-0 opacity-75">
                            <i class="fas fa-info-circle"></i>
                            Your submission will be saved and processed immediately
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="floating-save-indicator" id="save-indicator">
        <i class="fas fa-save"></i> Form saved automatically
    </div>

    <script>
    $(document).ready(function() {
        // Form persistence key
        const STORAGE_KEY = 'diary_form_data';
        let taskCounter = 1;

        // Task section template
        const taskSectionTemplate = `
            <div class="task-card fade-in">
                <div class="task-card-header">
                    <div class="task-number">
                        <i class="fas fa-clipboard-check"></i>
                        Task <span class="task-num"></span>
                    </div>
                    <button type="button" class="remove-task-btn" title="Remove Task">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="task-card-body">
                    <div class="form-section">
                        <div class="subsection-title">
                            <i class="fas fa-list"></i>
                            Task Details
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-align-left"></i>
                                    Task Description <span class="required">*</span>
                                </label>
                                <input type="text" name="task_description[]" class="form-control task-description" placeholder="Enter task description" required>
                                <div class="invalid-feedback">Task description is required.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-flag"></i>
                                    Task Status <span class="required">*</span>
                                </label>
                                <select name="task_status[]" class="form-select" required>
                                    <option value="Not Started">Not Started</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Blocked">Blocked</option>
                                </select>
                                <div class="invalid-feedback">Please select a task status.</div>
                            </div>
                        </div>
                        
                        <div class="private-section">
                            <div class="private-title">
                                <i class="fas fa-lock"></i>
                                Private Details (SA Team Only)
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Private Task Description</label>
                                    <input type="text" name="private_task_description[]" class="form-control" placeholder="Enter private task details">
                                    <div class="form-text">Visible only to SA Team members</div>
                                </div>
                                <div class="col-md-6 mb-3">
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
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="subsection-title">
                            <i class="fas fa-clock"></i>
                            Time Tracking
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar-check"></i>
                                    Allocated Time (hours) <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="allocated_time[]" class="form-control" placeholder="Enter allocated time" step="0.5" min="0" required>
                                    <span class="input-group-text">hours</span>
                                </div>
                                <div class="invalid-feedback">Allocated time is required.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-stopwatch"></i>
                                    Actual Time (hours)
                                </label>
                                <div class="input-group">
                                    <input type="number" name="actual_time[]" class="form-control" placeholder="Enter actual time" step="0.5" min="0">
                                    <span class="input-group-text">hours</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-question-circle"></i>
                                Deviation Reason
                            </label>
                            <textarea name="deviation_reason[]" class="form-control" rows="2" placeholder="Explain any time deviation..."></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="subsection-title">
                            <i class="fas fa-comments"></i>
                            Feedback & Insights
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-lightbulb"></i>
                                    Public Insights
                                </label>
                                <textarea name="personal_insights[]" class="form-control" rows="3" placeholder="Share insights visible to all team members..."></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-handshake"></i>
                                    Commitments
                                </label>
                                <textarea name="commitments[]" class="form-control" rows="3" placeholder="What are you committing to next?"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-eye"></i>
                                    General Observations
                                </label>
                                <textarea name="general_observations[]" class="form-control" rows="3" placeholder="Any general observations about the work or process?"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-chart-line"></i>
                                    Improvement Suggestions
                                </label>
                                <textarea name="improvement_suggestions[]" class="form-control" rows="3" placeholder="Suggestions for process or workflow improvements?"></textarea>
                            </div>
                        </div>
                        
                        <div class="private-section">
                            <div class="private-title">
                                <i class="fas fa-user-secret"></i>
                                Private Insights (SA Team Only)
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Private Insights</label>
                                <textarea name="private_insights[]" class="form-control" rows="3" placeholder="Confidential notes and insights for SA Team review only..."></textarea>
                                <div class="form-text">These insights are completely confidential and visible only to SA Team members</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Initialize form
        function initializeForm() {
            // Check if there is saved data
            const savedData = sessionStorage.getItem(STORAGE_KEY);
            if (!savedData) {
                // No saved data, add one initial task section
                addTaskSection();
            } else {
                // Load saved data
                loadFormData();
            }
            
            // Update task counter and progress
            updateTaskCounter();
            updateProgress();
        }

        // Add new task section
        function addTaskSection() {
            const newSection = $(taskSectionTemplate);
            newSection.find('.task-num').text(taskCounter);
            $('#task-sections').append(newSection);
            
            taskCounter++;
            updateTaskCounter();
            updateProgress();
            saveFormData();
        }

        // Update task counter display
        function updateTaskCounter() {
            const count = $('.task-card').length;
            $('#task-count').text(count);
            
            // Update task numbers
            $('.task-card').each(function(index) {
                $(this).find('.task-num').text(index + 1);
            });
        }

        // Update progress bar
        function updateProgress() {
            const totalFields = $('input[required], select[required]').length;
            const filledFields = $('input[required], select[required]').filter(function() {
                return $(this).val() !== '';
            }).length;
            
            const progress = totalFields > 0 ? (filledFields / totalFields) * 100 : 0;
            $('#form-progress').css('width', progress + '%');
        }

        // Save form data to sessionStorage
        function saveFormData() {
            const formData = {};
            
            // Save user selection
            formData.diary_user = $('#diary_user').val();
            
            // Save all form fields
            $('#diaryForm').find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                const value = $(this).val();
                if (name) {
                    if (!formData[name]) {
                        formData[name] = [];
                    }
                    if (name.includes('[]')) {
                        formData[name].push(value);
                    } else {
                        formData[name] = value;
                    }
                }
            });
            
            try {
                sessionStorage.setItem(STORAGE_KEY, JSON.stringify(formData));
                showSaveIndicator();
            } catch (e) {
                console.warn('Could not save form data:', e);
            }
        }

        // Load form data from sessionStorage
        function loadFormData() {
            try {
                const savedData = sessionStorage.getItem(STORAGE_KEY);
                if (!savedData) return null;
                
                const formData = JSON.parse(savedData);
                
                // Restore user selection
                if (formData.diary_user) {
                    $('#diary_user').val(formData.diary_user).trigger('change');
                }
                
                // Restore task sections
                if (formData['task_description[]'] && Array.isArray(formData['task_description[]'])) {
                    const neededTasks = formData['task_description[]'].length;
                    // Clear existing sections to avoid duplication
                    $('#task-sections').empty();
                    taskCounter = 1;
                    // Add task sections based on saved data
                    for (let i = 0; i < neededTasks; i++) {
                        addTaskSection();
                    }
                    
                    // Populate fields
                    Object.keys(formData).forEach(fieldName => {
                        if (fieldName.includes('[]') && Array.isArray(formData[fieldName])) {
                            $(`[name="${fieldName}"]`).each(function(index) {
                                if (formData[fieldName][index] !== undefined) {
                                    $(this).val(formData[fieldName][index]);
                                }
                            });
                        }
                    });
                } else {
                    // No task data, ensure at least one task section
                    if ($('.task-card').length === 0) {
                        addTaskSection();
                    }
                }
                
                updateProgress();
                return formData;
            } catch (e) {
                console.warn('Could not load saved form data:', e);
                // Fallback to one task section if loading fails
                if ($('.task-card').length === 0) {
                    addTaskSection();
                }
                return null;
            }
        }

        // Show save indicator
        function showSaveIndicator() {
            const indicator = $('#save-indicator');
            indicator.addClass('show');
            setTimeout(() => {
                indicator.removeClass('show');
            }, 2000);
        }

        // Event Handlers
        
        // Add new task
        $('.add-task-btn').click(function() {
            addTaskSection();
        });

        // Remove task section
        $(document).on('click', '.remove-task-btn', function() {
            if ($('.task-card').length > 1) {
                $(this).closest('.task-card').fadeOut(300, function() {
                    $(this).remove();
                    updateTaskCounter();
                    updateProgress();
                    saveFormData();
                });
            } else {
                alert('At least one task is required.');
            }
        });

        // User selection change
        $('#diary_user').change(function() {
            saveFormData();
            updateProgress();
        });

        // Save form data on any input change
        $(document).on('input change', '#diaryForm input, #diaryForm select, #diaryForm textarea', function() {
            updateProgress();
            saveFormData();
        });

        // Form validation and submission
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
            $('.task-card').each(function() {
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
                alert('Please fill in all required fields before submitting.');
                
                // Scroll to first invalid field
                const firstInvalid = $('.is-invalid').first();
                if (firstInvalid.length) {
                    $('html, body').animate({
                        scrollTop: firstInvalid.offset().top - 100
                    }, 500);
                }
            } else {
                // Mark form as submitting
                $(this).data('submitting', true);
            }
        });

        // Initialize the form
        initializeForm();
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
