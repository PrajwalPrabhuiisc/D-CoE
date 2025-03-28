<?php
session_start();
require 'db_connect.php';
require 'survey_navigation.php';

// Process incoming POST data from index.php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['response'])) {
    // Merge new responses with existing ones in session (if any)
    $_SESSION['responses'] = isset($_SESSION['responses']) 
        ? array_merge($_SESSION['responses'], $_POST['response']) 
        : $_POST['response'];

    // Determine the next page dynamically
    $nextPage = getNextSurveyPage($_SESSION['selected_people'], $_SESSION['selected_tools'], 'people');
    header("Location: $nextPage");
    exit();
}

$user_id = $_SESSION['user_id'] ?? null;
$selected_people = $_SESSION['selected_people'] ?? [];

if (!$user_id) {
    header("Location: index.php");
    exit();
}

// Fetch user details
$stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch selected people details
$selectedPeopleDetails = [];
foreach ($selected_people as $personId) {
    $stmt = $pdo->prepare("SELECT user_id, first_name, last_name FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $personId]);
    if ($person = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $selectedPeopleDetails[] = $person;
    }
}

// If no people are selected, skip to the next page immediately
if (empty($selectedPeopleDetails)) {
    $nextPage = getNextSurveyPage($selected_people, $_SESSION['selected_tools'], 'people');
    header("Location: $nextPage");
    exit();
}

// Fetch questions for 'people' category
$questionsStmt = $pdo->query("SELECT question_id, question_text FROM questions WHERE category = 'human'");
$peopleQuestions = $questionsStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey MVP - People Questions</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6B48FF;
            --secondary: #00DDEB;
            --dark: #1A1A2E;
            --light: #E6E6FA;
            --white: #FFFFFF;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
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
            max-width: 1200px;
            width: 100%;
            position: relative;
            gap: 2rem;
            overflow-x: hidden;
        }

        .survey-sidebar {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem 2rem;
            color: var(--white);
            position: sticky;
            top: 2rem;
            height: fit-content;
            box-shadow: var(--shadow);
            min-width: 250px;
            display: block;
        }

        .sidebar-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--secondary);
            text-transform: uppercase;
        }

        .sidebar-text {
            font-size: 1.1rem;
            line-height: 1.6;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .sidebar-steps {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .sidebar-step {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1rem;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .sidebar-step:hover {
            opacity: 1;
            transform: translateX(5px);
        }

        .survey-main {
            flex: 2;
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
            background: #E6E6FA; /* Opaque background to prevent overlap */
            min-width: 200px;
        }

        .survey-table th:first-child {
            z-index: 15;
            background: rgba(107, 72, 255, 1); /* Opaque background for top-left cell */
        }

        .survey-table th:not(:first-child),
        .survey-table td:not(:first-child) {
            min-width: 120px;
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
            min-width: 200px;
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

        @keyframes slideInLeft {
            from { transform: translateX(-100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeInUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (max-width: 900px) {
            .survey-wrapper {
                flex-direction: column;
            }

            .survey-sidebar {
                position: relative;
                top: 0;
                padding: 2rem;
                min-width: 100%;
            }

            .survey-main {
                padding: 2rem;
            }

            .section-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }

            .sidebar-title {
                font-size: 1.5rem;
            }

            .sidebar-text {
                font-size: 1rem;
            }

            .survey-table th,
            .survey-table td {
                padding: 1rem;
                display: block;
                width: 100%;
            }

            .question-cell {
                width: 100%;
            }

            .table-container {
                max-height: 400px;
            }

            .survey-table th:first-child,
            .survey-table td:first-child {
                position: static;
            }
        }
    </style>
</head>
<body>
    <div class="survey-wrapper">
        <div class="survey-sidebar">
            <h1 class="sidebar-title">Survey Portal</h1>
            <p class="sidebar-text">Evaluate the people you interact with. Your feedback shapes insights.</p>
            <div class="sidebar-steps">
                <div class="sidebar-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Identify Yourself
                </div>
                <div class="sidebar-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Select Connections
                </div>
                <div class="sidebar-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Rate People
                </div>
                <div class="sidebar-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Rate Software
                </div>
                <div class="sidebar-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Rate Hardware
                </div>
                <!-- <div class="sidebar-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Rate Analog
                </div> -->
                <div class="sidebar-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Review Results
                </div>
            </div>
        </div>

        <div class="survey-main">
            <div class="section-header">
                <h2 class="section-title">People Survey for <?php echo htmlspecialchars("{$user['first_name']} {$user['last_name']}"); ?></h2>
            </div>

            <?php if (!empty($selectedPeopleDetails)): ?>
                <form action="" method="POST" id="surveyForm">
                    <div class="table-container">
                        <table class="survey-table">
                            <thead>
                                <tr>
                                    <th>Questions</th>
                                    <?php foreach ($selectedPeopleDetails as $person): ?>
                                        <th><?php echo htmlspecialchars("{$person['first_name']} {$person['last_name']}"); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($peopleQuestions)): ?>
                                    <tr>
                                        <td colspan="<?php echo count($selectedPeopleDetails) + 1; ?>" style="text-align: center; color: #6B7280; padding: 2rem;">
                                            No questions available for the 'people' category.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($peopleQuestions as $question): ?>
                                        <?php
                                        $optionsStmt = $pdo->prepare("SELECT option_id, option_text FROM question_options WHERE question_id = :question_id");
                                        $optionsStmt->execute(['question_id' => $question['question_id']]);
                                        $optionsList = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);
                                        ?>
                                        <tr>
                                            <td class="question-cell"><?php echo htmlspecialchars($question['question_text']); ?></td>
                                            <?php if (empty($optionsList)): ?>
                                                <td colspan="<?php echo count($selectedPeopleDetails); ?>" style="text-align: center; color: #6B7280;">
                                                    No options available for this question.
                                                </td>
                                            <?php else: ?>
                                                <?php foreach ($selectedPeopleDetails as $person): ?>
                                                    <td class="radio-cell">
                                                        <?php foreach ($optionsList as $option): ?>
                                                            <label>
                                                                <input type="radio" name="response[<?php echo $person['user_id']; ?>][<?php echo $question['question_id']; ?>]" 
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
                    No people selected. Proceeding to the next step...
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>