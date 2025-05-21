<?php
session_start();
require 'db_connect.php';
require 'survey_navigation.php';

// Ensure session data is valid
$user_id = (int)($_SESSION['user_id'] ?? 0);
$selected_tools = $_SESSION['selected_tools'] ?? [];
if (!$user_id) {
    header("Location: index.php");
    exit();
}

// Fetch user details
$stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    header("Location: index.php");
    exit();
}

// Fetch selected software tools
$selectedSoftwareDetails = [];
$validToolIds = $pdo->query("SELECT tool_id FROM tools WHERE tool_type = 'software'")->fetchAll(PDO::FETCH_COLUMN);
foreach ($selected_tools as $toolId) {
    $toolId = (int)$toolId;
    if ($toolId > 0 && in_array($toolId, $validToolIds)) {
        $stmt = $pdo->prepare("SELECT tool_id, tool_name, tool_type FROM tools WHERE tool_id = :tool_id AND tool_type = 'software'");
        $stmt->execute(['tool_id' => $toolId]);
        if ($tool = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $selectedSoftwareDetails[] = $tool;
        }
    }
}

// If no valid software tools are selected, skip to the next page
if (empty($selectedSoftwareDetails)) {
    $nextPage = getNextSurveyPage($_SESSION['selected_people'], $_SESSION['selected_tools'], 'software');
    header("Location: $nextPage");
    exit();
}

// Fetch questions for 'software' category, including example
$questionsStmt = $pdo->query("SELECT question_id, question_text, example FROM questions WHERE category = 'software'");
$softwareQuestions = $questionsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch question options
$optionsStmt = $pdo->query("SELECT option_id, question_id, option_text FROM question_options");
$options = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);
$questionOptions = [];
foreach ($options as $option) {
    $questionOptions[$option['question_id']][] = $option;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['response'])) {
    // Log incoming POST data for debugging
    error_log("POST response data: " . print_r($_POST['response'], true));

    // Clear previous responses for this user and software tools to prevent duplicates
    $placeholders = implode(',', array_fill(0, count($selectedSoftwareDetails), '?'));
    $toolIds = array_column($selectedSoftwareDetails, 'tool_id');
    $stmt = $pdo->prepare("DELETE FROM responses WHERE user_id = ? AND subject_id IN ($placeholders) AND subject_type = 'software'");
    $stmt->execute(array_merge([$user_id], $toolIds));
    error_log("Cleared previous responses for user $user_id and software tools: " . implode(',', $toolIds));

    // Insert new responses
    foreach ($_POST['response'] as $tool_id => $questions) {
        $tool_id = (int)$tool_id;
        if ($tool_id > 0 && in_array($tool_id, $toolIds)) {
            foreach ($questions as $question_id => $option_id) {
                $question_id = (int)$question_id;
                $option_id = (int)$option_id;
                if ($option_id > 0) { // Ensure a valid option is selected
                    $stmt = $pdo->prepare("
                        INSERT INTO responses (user_id, question_id, option_id, subject_id, subject_type)
                        VALUES (:user_id, :question_id, :option_id, :subject_id, 'software')
                        ON DUPLICATE KEY UPDATE option_id = :option_id
                    ");
                    $stmt->execute([
                        'user_id' => $user_id,
                        'question_id' => $question_id,
                        'option_id' => $option_id,
                        'subject_id' => $tool_id
                    ]);
                    error_log("Inserted response: user_id=$user_id, question_id=$question_id, option_id=$option_id, subject_id=$tool_id");
                }
            }
        } else {
            error_log("Skipped invalid tool_id: $tool_id");
        }
    }

    // Clear session responses to prevent re-submission
    unset($_SESSION['responses']);

    // Determine the next page dynamically
    $nextPage = getNextSurveyPage($_SESSION['selected_people'], $_SESSION['selected_tools'], 'software');
    header("Location: $nextPage");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey MVP - Software Questions</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6B48FF;
            --secondary: #00DDEB;
            --dark: #1A1A2E;
            --light: #E6E6FA;
            --white: #FFFFFF;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            --gray: #6B7280;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, var(--dark) 0%, #16213E 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow-x: hidden;
        }

        .survey-wrapper {
            display: flex;
            flex-direction: column;
            max-width: 1400px;
            width: 100%;
            position: relative;
            gap: 2rem;
            overflow-x: hidden;
        }

        .survey-header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            color: var(--white);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 2rem;
            width: 100%;
        }

        .header-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--secondary);
            text-transform: uppercase;
            flex: 0 0 auto;
        }

        .header-text {
            font-size: 1.1rem;
            line-height: 1.6;
            opacity: 0.9;
            flex: 1;
        }

        .header-steps {
            display: flex;
            gap: 1.5rem;
            flex: 1;
        }

        .header-step {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .header-step:hover {
            opacity: 1;
        }

        .survey-main {
            background: var(--white);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: var(--shadow);
            animation: fadeInUp 0.5s ease-out;
            width: 100%;
            overflow: hidden;
        }

        .section-header {
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .section-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.75rem;
            font-weight: 500;
            color: var(--dark);
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--primary);
            border-radius: 2px;
        }

        .table-container {
            max-height: 500px;
            width: 100%;
            overflow-y: auto;
            overflow-x: auto;
            position: relative;
            border-radius: 12px;
            box-shadow: var(--shadow);
            display: block;
        }

        .survey-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--white);
            table-layout: auto;
        }

        .survey-table thead {
            position: sticky;
            top: 0;
            z-index: 10;
            background: var(--white);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .survey-table th {
            background: rgba(107, 72, 255, 0.1);
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            padding: 1.25rem;
            text-align: left;
            color: var(--dark);
            border-bottom: 2px solid var(--light);
            font-size: 1rem;
            white-space: nowrap;
        }

        .survey-table th:first-child,
        .survey-table td:first-child {
            position: sticky;
            left: 0;
            z-index: 5;
            background: #E6E6FA;
            min-width: 300px; /* Increased to accommodate button and example */
        }

        .survey-table th:first-child {
            z-index: 15;
            background: rgba(107, 72, 255, 1);
        }

        .survey-table th:not(:first-child),
        .survey-table td:not(:first-child) {
            min-width: 150px;
            white-space: nowrap;
        }

        .survey-table td {
            padding: 1.25rem;
            border-bottom: 1px solid var(--light);
            vertical-align: top;
        }

        .question-cell {
            font-weight: 600;
            color: var(--dark);
            font-size: 1rem;
            min-width: 300px; /* Increased to accommodate button and example */
        }

        .example-text {
            font-size: 0.9rem;
            color: var(--gray);
            font-style: italic;
            margin-top: 0.5rem;
            display: none;
            line-height: 1.2;
        }

        .example-text.visible {
            display: block;
        }

        .toggle-example-btn {
            margin-top: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .toggle-example-btn:hover {
            background: #5A3DE5;
        }

        .radio-cell {
            text-align: left;
        }

        .radio-cell label {
            display: flex;
            align-items: center;
            margin: 0.75rem 0;
            cursor: pointer;
            font-size: 0.95rem;
            color: var(--dark);
            transition: color 0.3s ease;
        }

        .radio-cell label:hover {
            color: var(--primary);
        }

        .radio-cell input[type="radio"] {
            margin-right: 0.75rem;
            accent-color: var(--primary);
            width: 1.25rem;
            height: 1.25rem;
        }

        .submit-button {
            display: block;
            width: 100%;
            max-width: 300px;
            margin: 2rem auto 0;
            padding: 1rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-weight: 500;
            font-size: 1.1rem;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-button:hover {
            background: #5A3DE5;
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(107, 72, 255, 0.2);
        }

        .table-container::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 5px;
        }

        .table-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        @keyframes fadeInUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (max-width: 900px) {
            .survey-wrapper {
                max-width: 100%;
            }

            .survey-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 1.5rem;
            }

            .header-title {
                font-size: 1.75rem;
            }

            .header-text {
                font-size: 1rem;
            }

            .header-steps {
                flex-direction: column;
                gap: 1rem;
            }

            .survey-main {
                padding: 2rem;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .survey-table th:first-child,
            .survey-table td:first-child {
                min-width: 250px;
            }

            .question-cell {
                min-width: 250px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }

            .header-title {
                font-size: 1.5rem;
            }

            .header-text {
                font-size: 0.9rem;
            }

            .header-step {
                font-size: 0.9rem;
            }

            .survey-table th,
            .survey-table td {
                padding: 1rem;
                display: block;
                width: 100%;
            }

            .question-cell {
                width: 100%;
                min-width: auto;
            }

            .example-text {
                font-size: 0.85rem;
            }

            .toggle-example-btn {
                font-size: 0.8rem;
                padding: 0.4rem 0.8rem;
            }

            .table-container {
                max-height: 400px;
            }

            .survey-table th:first-child,
            .survey-table td:first-child {
                position: static;
                min-width: auto;
            }

            .survey-table th:not(:first-child),
            .survey-table td:not(:first-child) {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="survey-wrapper">
        <div class="survey-header">
            <h1 class="header-title">Survey Portal</h1>
            <p class="header-text">Evaluate your software tools. Your feedback shapes insights.</p>
            <div class="header-steps">
                <div class="header-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Identify Yourself
                </div>
                <div class="header-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Select Connections
                </div>
                <div class="header-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Rate People
                </div>
                <div class="header-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Rate Software
                </div>
                <div class="header-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Rate Hardware
                </div>
                <div class="header-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Review Results
                </div>
            </div>
        </div>
        <div class="survey-main">
            <div class="section-header">
                <h2 class="section-title">Software Survey for <?php echo htmlspecialchars("{$user['first_name']} {$user['last_name']}"); ?></h2>
            </div>
            <?php if (!empty($selectedSoftwareDetails)): ?>
                <form action="" method="POST" id="surveyForm">
                    <div class="table-container">
                        <table class="survey-table">
                            <thead>
                                <tr>
                                    <th>Questions</th>
                                    <?php foreach ($selectedSoftwareDetails as $tool): ?>
                                        <th><?php echo htmlspecialchars($tool['tool_name']); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($softwareQuestions)): ?>
                                    <tr>
                                        <td colspan="<?php echo count($selectedSoftwareDetails) + 1; ?>" style="text-align: center; color: #6B7280; padding: 2rem;">
                                            No questions available for the 'software' category.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($softwareQuestions as $question): ?>
                                        <?php
                                        $optionsStmt = $pdo->prepare("SELECT option_id, option_text FROM question_options WHERE question_id = :question_id");
                                        $optionsStmt->execute(['question_id' => $question['question_id']]);
                                        $optionsList = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);
                                        $exampleText = !empty($question['example']) ? htmlspecialchars($question['example']) : '';
                                        ?>
                                        <tr>
                                            <td class="question-cell">
                                                <?php echo htmlspecialchars($question['question_text']); ?>
                                                <?php if ($exampleText): ?>
                                                    <button type="button" class="toggle-example-btn" 
                                                            onclick="toggleExample(<?php echo $question['question_id']; ?>)"
                                                            aria-expanded="false"
                                                            aria-label="Toggle example for <?php echo htmlspecialchars($question['question_text']); ?>">
                                                        Show Example
                                                    </button>
                                                    <div class="example-text" id="example-<?php echo $question['question_id']; ?>">
                                                        <strong>Example:</strong> <?php echo $exampleText; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <?php if (empty($optionsList)): ?>
                                                <td colspan="<?php echo count($selectedSoftwareDetails); ?>" style="text-align: center; color: #6B7280;">
                                                    No options available for this question.
                                                </td>
                                            <?php else: ?>
                                                <?php foreach ($selectedSoftwareDetails as $tool): ?>
                                                    <td class="radio-cell">
                                                        <?php foreach ($optionsList as $option): ?>
                                                            <label>
                                                                <input type="radio" name="response[<?php echo $tool['tool_id']; ?>][<?php echo $question['question_id']; ?>]" 
                                                                       value="<?php echo htmlspecialchars($option['option_id']); ?>" required>
                                                                <?php echo htmlspecialchars($option['option_text']); ?>
                                                            </label>
                                                        <?php endforeach; ?>
                                                    </td>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="submit-button">Next</button>
                </form>
            <?php else: ?>
                <p style="text-align: center; color: #6B7280; padding: 2rem;">
                    No software tools selected. Proceeding to the next step...
                </p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleExample(questionId) {
            const exampleDiv = document.getElementById(`example-${questionId}`);
            const button = exampleDiv.previousElementSibling;
            const isVisible = exampleDiv.classList.contains('visible');

            if (isVisible) {
                exampleDiv.classList.remove('visible');
                button.textContent = 'Show Example';
                button.setAttribute('aria-expanded', 'false');
            } else {
                exampleDiv.classList.add('visible');
                button.textContent = 'Hide Example';
                button.setAttribute('aria-expanded', 'true');
            }
        }
    </script>
</body>
</html>
