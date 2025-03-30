<?php
session_start();
require 'db_connect.php';
require 'survey_navigation.php'; // Include the helper function

// Fetch all users for the "Who Are You" dropdown
$stmt = $pdo->query("SELECT user_id, first_name, last_name FROM users ORDER BY first_name, last_name");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all tools for modals
$all_tools = [
    'software' => $pdo->query("SELECT tool_id, tool_name FROM tools WHERE tool_type = 'software' ORDER BY tool_name")->fetchAll(PDO::FETCH_ASSOC),
    'hardware' => $pdo->query("SELECT tool_id, tool_name FROM tools WHERE tool_type = 'hardware' ORDER BY tool_name")->fetchAll(PDO::FETCH_ASSOC),
    'analog' => $pdo->query("SELECT tool_id, tool_name FROM tools WHERE tool_type = 'analog' ORDER BY tool_name")->fetchAll(PDO::FETCH_ASSOC),
];

// Count items for each tool type (for conditional rendering)
$tool_counts = [
    'software' => count($all_tools['software']),
    'hardware' => count($all_tools['hardware']),
    'analog' => count($all_tools['analog']),
    'people' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn()
];

// Handle form submission to store selections in session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['user_id'] = $_POST['user_id'] ?? null;
    
    // Handle people selections
    $_SESSION['selected_people'] = ($tool_counts['people'] > 0 && !empty($_POST['people']) && !empty($_POST['people'][0]))
        ? explode(',', $_POST['people'][0])
        : [];
    
    // Handle tool selections
    $_SESSION['selected_tools'] = array_merge(
        ($tool_counts['software'] > 0 && !empty($_POST['software']) && !empty($_POST['software'][0])) ? explode(',', $_POST['software'][0]) : [],
        ($tool_counts['hardware'] > 0 && !empty($_POST['hardware']) && !empty($_POST['hardware'][0])) ? explode(',', $_POST['hardware'][0]) : [],
        ($tool_counts['analog'] > 0 && !empty($_POST['analog']) && !empty($_POST['analog'][0])) ? explode(',', $_POST['analog'][0]) : []
    );

    // Debug: Log session data to verify
    error_log("Session Data: " . print_r($_SESSION, true));

    // Determine the next page dynamically
    $nextPage = getNextSurveyPage($_SESSION['selected_people'], $_SESSION['selected_tools'], 'index');
    error_log("Next Page: $nextPage");
    header("Location: $nextPage");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey MVP - Selections</title>
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

        .select-dropdown {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--light);
            border-radius: 12px;
            font-size: 1rem;
            background: var(--white);
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .select-dropdown:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(107, 72, 255, 0.2);
        }

        .selection-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: 1px solid var(--light);
        }

        .selection-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(107, 72, 255, 0.1);
            border-color: var(--primary);
        }

        .card-title {
            font-weight: 500;
            color: var(--dark);
            font-size: 1.25rem;
        }

        .card-subtitle {
            color: #6B7280;
            font-size: 0.9rem;
        }

        .card-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .action-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .select-btn {
            background: var(--primary);
            color: var(--white);
        }

        .select-btn:hover {
            background: #5A3DE5;
        }

        .preview-btn {
            background: var(--light);
            color: var(--dark);
        }

        .preview-btn:hover {
            background: #DCDCFF;
        }

        .count-badge {
            background: var(--secondary);
            color: var(--dark);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
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

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content {
            background: var(--white);
            border-radius: 1.5rem;
            max-width: 650px;
            width: 90%;
            max-height: 85vh;
            padding: 2rem;
            position: relative;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            transform: translateY(-20px);
            animation: slideIn 0.3s ease-out forwards;
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 2rem;
            height: 2rem;
            background: var(--light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            background: #DCDCFF;
            transform: rotate(90deg);
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--light);
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(107, 72, 255, 0.1);
        }

        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            max-height: 50vh;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid var(--light);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .checkbox-item:hover {
            background: #F1F5F9;
            border-color: var(--primary);
            box-shadow: 0 2px 5px rgba(107, 72, 255, 0.1);
        }

        .checkbox-item input[type="checkbox"] {
            accent-color: var(--primary);
            margin-right: 0.75rem;
            width: 1.25rem;
            height: 1.25rem;
        }

        .modal-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            justify-content: center;
        }

        .action-btn-modal {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .clear-btn {
            background: #FF4D4D;
            color: #FFFFFF;
        }

        .clear-btn:hover {
            background: #E63939;
        }

        .reset-btn {
            background: #FFB347;
            color: #FFFFFF;
        }

        .reset-btn:hover {
            background: #FF9900;
        }

        @keyframes slideInLeft {
            from { transform: translateX(-100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeInUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
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

            .selection-card {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .card-actions {
                justify-content: center;
            }

            .checkbox-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="survey-wrapper">
        <div class="survey-sidebar">
            <h1 class="sidebar-title">Survey Portal</h1>
            <p class="sidebar-text">Dive into the future of workflow analysis. Your insights shape tomorrow.</p>
            <div class="sidebar-steps">
                <div class="sidebar-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Identify Yourself
                </div>
                <div class="sidebar-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Select Connections
                </div>
                <div class="sidebar-step">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Begin Analysis
                </div>
            </div>
        </div>

        <div class="survey-main">
            <div class="section-header">
                <h2 class="section-title">Launch Survey</h2>
            </div>

            <form action="" method="POST" id="surveyForm">
                <div class="selection-card">
                    <div>
                        <label class="card-title">Who Are You?</label>
                        <select name="user_id" id="user_id" class="select-dropdown" required>
                            <option value="">-- Select Your Profile --</option>
                            <?php
                            foreach ($users as $row) {
                                $selected = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']) ? 'selected' : '';
                                echo "<option value='{$row['user_id']}' $selected>" . htmlspecialchars("{$row['first_name']} {$row['last_name']}") . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <?php 
                $sections = [
                    'people' => 'People You Interact With',
                    'software' => 'Software Tools',
                    'hardware' => 'Hardware Tools',
                    'analog' => 'Analog Tools'
                ];
                foreach ($sections as $type => $title):
                    if ($tool_counts[$type] > 0): // Only show if there are items
                ?>
                <div class="selection-card">
                    <div>
                        <h3 class="card-title"><?= $title ?></h3>
                        <p class="card-subtitle">Select your relevant <?= strtolower($title) ?></p>
                    </div>
                    <div class="card-actions">
                        <button type="button" class="action-btn select-btn" data-modal="<?= $type ?>Modal">Select</button>
                        <button type="button" class="action-btn preview-btn" data-modal="<?= $type ?>PreviewModal">Preview</button>
                        <span id="<?= $type ?>Count" class="count-badge">0</span>
                    </div>
                </div>
                <?php endif; endforeach; ?>

                <!-- Hidden inputs to store selections -->
                <?php if ($tool_counts['people'] > 0): ?>
                <input type="hidden" name="people[]" id="hiddenPeople">
                <?php endif; ?>
                <?php if ($tool_counts['software'] > 0): ?>
                <input type="hidden" name="software[]" id="hiddenSoftware">
                <?php endif; ?>
                <?php if ($tool_counts['hardware'] > 0): ?>
                <input type="hidden" name="hardware[]" id="hiddenHardware">
                <?php endif; ?>
                <?php if ($tool_counts['analog'] > 0): ?>
                <input type="hidden" name="analog[]" id="hiddenAnalog">
                <?php endif; ?>

                <button type="submit" class="submit-button">Next</button>
            </form>
        </div>

        <!-- Modals with Reset and Clear Buttons -->
        <?php if ($tool_counts['people'] > 0): ?>
        <div id="peopleModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" data-modal="peopleModal">✕</div>
                <h2 class="section-title">Select People You Interact With</h2>
                <input type="text" id="peopleSearch" class="search-input" placeholder="Search people..." onkeyup="searchItems('people')">
                <div class="checkbox-grid" id="peopleList"></div>
                <div class="modal-actions">
                    <button type="button" class="action-btn-modal reset-btn" onclick="resetToDefault('people')">Reset to Default</button>
                    <button type="button" class="action-btn-modal clear-btn" onclick="clearSelections('people')">Clear Selections</button>
                </div>
            </div>
        </div>

        <div id="peoplePreviewModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" data-modal="peoplePreviewModal">✕</div>
                <h2 class="section-title">Selected People</h2>
                <div id="peoplePreviewList" class="checkbox-grid"></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($tool_counts['software'] > 0): ?>
        <div id="softwareModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" data-modal="softwareModal">✕</div>
                <h2 class="section-title">Select Software Tools</h2>
                <input type="text" id="softwareSearch" class="search-input" placeholder="Search software..." onkeyup="searchItems('software')">
                <div class="checkbox-grid" id="softwareList"></div>
                <div class="modal-actions">
                    <button type="button" class="action-btn-modal reset-btn" onclick="resetToDefault('software')">Reset to Default</button>
                    <button type="button" class="action-btn-modal clear-btn" onclick="clearSelections('software')">Clear Selections</button>
                </div>
            </div>
        </div>

        <div id="softwarePreviewModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" data-modal="softwarePreviewModal">✕</div>
                <h2 class="section-title">Selected Software</h2>
                <div id="softwarePreviewList" class="checkbox-grid"></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($tool_counts['hardware'] > 0): ?>
        <div id="hardwareModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" data-modal="hardwareModal">✕</div>
                <h2 class="section-title">Select Hardware Tools</h2>
                <input type="text" id="hardwareSearch" class="search-input" placeholder="Search hardware..." onkeyup="searchItems('hardware')">
                <div class="checkbox-grid" id="hardwareList"></div>
                <div class="modal-actions">
                    <button type="button" class="action-btn-modal reset-btn" onclick="resetToDefault('hardware')">Reset to Default</button>
                    <button type="button" class="action-btn-modal clear-btn" onclick="clearSelections('hardware')">Clear Selections</button>
                </div>
            </div>
        </div>

        <div id="hardwarePreviewModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" data-modal="hardwarePreviewModal">✕</div>
                <h2 class="section-title">Selected Hardware</h2>
                <div id="hardwarePreviewList" class="checkbox-grid"></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($tool_counts['analog'] > 0): ?>
        <div id="analogModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" data-modal="analogModal">✕</div>
                <h2 class="section-title">Select Analog Tools</h2>
                <input type="text" id="analogSearch" class="search-input" placeholder="Search analog tools..." onkeyup="searchItems('analog')">
                <div class="checkbox-grid" id="analogList"></div>
                <div class="modal-actions">
                    <button type="button" class="action-btn-modal reset-btn" onclick="resetToDefault('analog')">Reset to Default</button>
                    <button type="button" class="action-btn-modal clear-btn" onclick="clearSelections('analog')">Clear Selections</button>
                </div>
            </div>
        </div>

        <div id="analogPreviewModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" data-modal="analogPreviewModal">✕</div>
                <h2 class="section-title">Selected Analog Tools</h2>
                <div id="analogPreviewList" class="checkbox-grid"></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
<script>
// Pass PHP data to JavaScript
const allUsers = <?php echo json_encode($users); ?>;
const allTools = <?php echo json_encode($all_tools); ?>;
const toolCounts = <?php echo json_encode($tool_counts); ?>;
const userPeople = <?php
    $stmt = $pdo->query("SELECT up.user_id, up.person_id, CONCAT(u.first_name, ' ', u.last_name) AS person_name 
                         FROM user_people up 
                         JOIN users u ON up.person_id = u.user_id");
    $userPeople = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $userPeople[$row['user_id']][] = $row['person_id'];
    }
    echo json_encode($userPeople);
?>;
const userTools = <?php
    $stmt = $pdo->query("SELECT ut.user_id, t.tool_id, t.tool_name, t.tool_type 
                         FROM user_tools ut 
                         JOIN tools t ON ut.tool_id = t.tool_id ORDER BY t.tool_name");
    $userTools = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $userTools[$row['user_id']][] = $row['tool_id'];
    }
    echo json_encode($userTools);
?>;
const sessionSelectedPeople = <?php echo json_encode($_SESSION['selected_people'] ?? []); ?>;
const sessionSelectedTools = <?php echo json_encode($_SESSION['selected_tools'] ?? []); ?>;

// In-memory storage for current selections, initialized with session data or empty
let currentSelectedPeople = [...sessionSelectedPeople];
let currentSelectedTools = [...sessionSelectedTools];

function updateLists() {
    const userId = document.getElementById('user_id').value;
    if (!userId) {
        // Clear all lists if no user is selected
        if (toolCounts.people > 0) updatePeopleList('');
        if (toolCounts.software > 0) updateToolList('software', '');
        if (toolCounts.hardware > 0) updateToolList('hardware', '');
        if (toolCounts.analog > 0) updateToolList('analog', '');
        return;
    }
    // Update lists with current selections, not resetting unless explicitly requested
    if (toolCounts.people > 0) updatePeopleList(userId);
    if (toolCounts.software > 0) updateToolList('software', userId);
    if (toolCounts.hardware > 0) updateToolList('hardware', userId);
    if (toolCounts.analog > 0) updateToolList('analog', userId);
}

function updatePeopleList(userId) {
    const peopleList = document.getElementById('peopleList');
    if (!peopleList) {
        console.error("peopleList element not found");
        return;
    }
    peopleList.innerHTML = '';

    if (!userId) {
        peopleList.innerHTML = '<p class="text-gray-500 col-span-full text-center">Select a user first.</p>';
        updateSelected('people', '');
        return;
    }

    const defaultPeople = userPeople[userId] || [];
    allUsers.forEach(person => {
        if (person.user_id.toString() !== userId.toString()) { // Exclude the selected user
            // Always prioritize current selections over defaults
            const isSelected = currentSelectedPeople.includes(person.user_id.toString());
            const checked = isSelected ? 'checked' : '';
            const label = document.createElement('label');
            label.className = 'checkbox-item';
            label.innerHTML = `
                <input type="checkbox" name="people[]" value="${person.user_id}" ${checked} onchange="updateCurrentSelections('people', '${person.user_id}', this.checked)">
                ${person.first_name} ${person.last_name}
            `;
            peopleList.appendChild(label);
        }
    });

    searchItems('people');
    updateSelected('people', '');
}

function updateToolList(type, userId) {
    const toolList = document.getElementById(`${type}List`);
    if (!toolList) {
        console.error(`${type}List element not found`);
        return;
    }
    toolList.innerHTML = '';

    if (!userId) {
        toolList.innerHTML = `<p class="text-gray-500 col-span-full text-center">Select a user first.</p>`;
        updateSelected(type, '');
        return;
    }

    allTools[type].forEach(tool => {
        // Always prioritize current selections over defaults
        const isSelected = currentSelectedTools.includes(tool.tool_id.toString());
        const checked = isSelected ? 'checked' : '';
        const label = document.createElement('label');
        label.className = 'checkbox-item';
        label.innerHTML = `
            <input type="checkbox" name="${type}[]" value="${tool.tool_id}" ${checked} onchange="updateCurrentSelections('${type}', '${tool.tool_id}', this.checked)">
            ${tool.tool_name}
        `;
        toolList.appendChild(label);
    });

    searchItems(type);
    updateSelected(type, '');
}

function updateCurrentSelections(type, id, isChecked) {
    if (type === 'people') {
        if (isChecked) {
            if (!currentSelectedPeople.includes(id)) currentSelectedPeople.push(id);
        } else {
            currentSelectedPeople = currentSelectedPeople.filter(item => item !== id);
        }
    } else {
        if (isChecked) {
            if (!currentSelectedTools.includes(id)) currentSelectedTools.push(id);
        } else {
            currentSelectedTools = currentSelectedTools.filter(item => item !== id);
        }
    }
    updateSelected(type, '');
}

function resetToDefault(type) {
    const userId = document.getElementById('user_id').value;
    if (!userId) return;

    if (type === 'people') {
        currentSelectedPeople = (userPeople[userId] || []).map(String);
        updatePeopleList(userId);
    } else {
        const defaultTools = (userTools[userId] || []).filter(id => 
            allTools[type].some(tool => tool.tool_id === id)
        ).map(String);
        currentSelectedTools = currentSelectedTools.filter(id => 
            !allTools[type].some(tool => tool.tool_id.toString() === id)
        ).concat(defaultTools.filter(id => !currentSelectedTools.includes(id)));
        updateToolList(type, userId);
    }
}

function clearSelections(type) {
    if (type === 'people') {
        currentSelectedPeople = [];
        updatePeopleList(document.getElementById('user_id').value);
    } else {
        currentSelectedTools = currentSelectedTools.filter(id => 
            !allTools[type].some(tool => tool.tool_id.toString() === id)
        );
        updateToolList(type, document.getElementById('user_id').value);
    }
}

function openModal(modalId) {
    const userId = document.getElementById('user_id').value;
    if (!userId) {
        alert('Please select a user in "Who Are You?" before proceeding.');
        return;
    }
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        updateLists(); // Refresh lists with current selections
    } else {
        console.error(`Modal with ID '${modalId}' not found`);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

function searchItems(type) {
    const searchInput = document.getElementById(`${type}Search`);
    if (!searchInput) return;
    const query = searchInput.value.toLowerCase();
    const items = document.querySelectorAll(`#${type}List .checkbox-item`);
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(query) ? 'flex' : 'none';
    });
}

function updateSelected(type, itemText) {
    const hiddenInput = document.getElementById(`hidden${type.charAt(0).toUpperCase() + type.slice(1)}`);
    if (!hiddenInput) return;

    const checkboxes = document.querySelectorAll(`#${type}List input[type="checkbox"]:checked`);
    const selected = Array.from(checkboxes).map(cb => ({
        id: cb.value,
        text: cb.parentElement.textContent.trim()
    }));

    const countContainer = document.getElementById(`${type}Count`);
    countContainer.textContent = `${selected.length}`;

    const previewList = document.getElementById(`${type}PreviewList`);
    previewList.innerHTML = selected.length > 0
        ? selected.map(item => `<div class="checkbox-item">${item.text}</div>`).join('')
        : '<p class="text-gray-500 col-span-full text-center">No items selected</p>';

    hiddenInput.value = selected.map(item => item.id).join(',');
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    const userIdSelect = document.getElementById('user_id');
    userIdSelect.addEventListener('change', () => {
        // Reset selections only if user changes, then update lists
        const newUserId = userIdSelect.value;
        if (newUserId && newUserId !== '<?php echo $_SESSION['user_id'] ?? ''; ?>') {
            currentSelectedPeople = (userPeople[newUserId] || []).map(String);
            currentSelectedTools = (userTools[newUserId] || []).map(String);
        }
        updateLists();
    });

    const buttons = document.querySelectorAll('.action-btn');
    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const modalId = button.getAttribute('data-modal');
            if (modalId) openModal(modalId);
        });
    });

    const closeButtons = document.querySelectorAll('.modal-close');
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const modalId = button.getAttribute('data-modal');
            if (modalId) closeModal(modalId);
        });
    });

    window.addEventListener('click', (event) => {
        if (event.target.classList.contains('modal')) {
            document.querySelectorAll('.modal').forEach(modal => modal.style.display = 'none');
        }
    });

    const userId = userIdSelect.value;
    if (userId) updateLists();
});
</script>
    
</body>
</html>
