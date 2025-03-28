<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['response'])) {
    $_SESSION['responses'] = array_merge($_SESSION['responses'], $_POST['response']);
}

$user_id = $_SESSION['user_id'] ?? null;
$responses = $_SESSION['responses'] ?? [];

if (!$user_id) {
    header("Location: index.php");
    exit();
}

foreach ($responses as $target_id => $questions) {
    foreach ($questions as $question_id => $option_id) {
        $stmt = $pdo->prepare("INSERT INTO responses (user_id, question_id, option_id) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $question_id, $option_id]);
    }
}

session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey MVP - Submitted</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6B48FF;
            --secondary: #00DDEB;
            --dark: #1A1A2E;
            --light: #E6E6FA;
            --white: #FFFFFF;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            --success: #10B981; /* Retained for the checkmark icon */
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
            animation: slideInLeft 0.5s ease-out;
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
            text-align: center;
        }

        .submit-icon {
            width: 64px;
            height: 64px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .submit-icon svg {
            width: 32px;
            height: 32px;
            color: var(--white);
        }

        .submit-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.75rem;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }

        .submit-title::after {
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

        .submit-message {
            font-size: 1.1rem;
            color: #6B7280; /* Matches gray-500 from previous pages */
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .back-link {
            display: inline-block;
            padding: 1rem 2rem;
            background: var(--primary);
            color: var(--white);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            font-size: 1.1rem;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            background: #5A3DE5;
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(107, 72, 255, 0.2);
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
            }

            .survey-main {
                padding: 2rem;
            }

            .submit-title {
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

            .submit-icon {
                width: 48px;
                height: 48px;
            }

            .submit-icon svg {
                width: 24px;
                height: 24px;
            }

            .submit-title {
                font-size: 1.5rem;
            }

            .submit-message {
                font-size: 1rem;
            }

            .back-link {
                padding: 0.75rem 1.5rem;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="survey-wrapper">
        <div class="survey-sidebar">
            <h1 class="sidebar-title">Survey Portal</h1>
            <p class="sidebar-text">Thank you for completing the survey! Your responses have been recorded.</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Rate People
                </div>
                <div class="sidebar-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Rate Software
                </div>
                <div class="sidebar-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Rate Hardware
                </div>
                <!-- <div class="sidebar-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Rate Analog
                </div> -->
                <div class="sidebar-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Review Results
                </div>
            </div>
        </div>

        <div class="survey-main">
            <div class="submit-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="submit-title">Survey Submitted</h1>
            <p class="submit-message">Thank you for your responses! Your input helps us understand your workflow better.</p>
            <a href="index.php" class="back-link">Back to Start</a>
        </div>
    </div>
</body>
</html>