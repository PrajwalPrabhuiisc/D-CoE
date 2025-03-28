<?php
session_start();
require 'db_connect.php';
require 'survey_navigation.php'; // Include the helper function

// Fetch all users for the "Who Are You" dropdown and for JavaScript filtering
$stmt = $pdo->query("SELECT user_id, first_name, last_name FROM users ORDER BY first_name, last_name");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count items for each tool type
$tool_counts = [
    'software' => $pdo->query("SELECT COUNT(*) FROM tools WHERE tool_type = 'software'")->fetchColumn(),
    'hardware' => $pdo->query("SELECT COUNT(*) FROM tools WHERE tool_type = 'hardware'")->fetchColumn(),
    'analog' => $pdo->query("SELECT COUNT(*) FROM tools WHERE tool_type = 'analog'")->fetchColumn(),
    'people' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn()
];

// Handle form submission to store selections in session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['user_id'] = $_POST['user_id'] ?? null;
    
    // Handle people (only if people section is available)
    $_SESSION['selected_people'] = ($tool_counts['people'] > 0 && !empty($_POST['people']) && !empty($_POST['people'][0]))
        ? explode(',', $_POST['people'][0])
        : [];
    
    // Combine all tool selections into a single selected_tools array
    $_SESSION['selected_tools'] = array_merge(
        ($tool_counts['software'] > 0 && !empty($_POST['software']) && !empty($_POST['software'][0])) ? explode(',', $_POST['software'][0]) : [],
        ($tool_counts['hardware'] > 0 && !empty($_POST['hardware']) && !empty($_POST['hardware'][0])) ? explode(',', $_POST['hardware'][0]) : [],
        ($tool_counts['analog'] > 0 && !empty($_POST['analog']) && !empty($_POST['analog'][0])) ? explode(',', $_POST['analog'][0]) : []
    );
    
    // Determine the next page dynamically
    $nextPage = getNextSurveyPage($_SESSION['selected_people'], $_SESSION['selected_tools'], 'index');
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
            color: #6B7280; /* Matches --gray-500 */
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
                        <button type="button" class="action-btn select-btn" onclick="openModal('<?= $type ?>Modal')">Select</button>
                        <button type="button" class="action-btn preview-btn" onclick="openModal('<?= $type ?>PreviewModal')">Preview</button>
                        <span id="<?= $type ?>Count" class="count-badge">0</span>
                    </div>
                </div>
                <?php endif; endforeach; ?>

                <!-- Hidden inputs to store selections (only for available sections) -->
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

        <!-- Modals (only shown if items exist) -->
        <?php if ($tool_counts['people'] > 0): ?>
        <div id="peopleModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" onclick="closeModal('peopleModal')">✕</div>
                <h2 class="section-title">Select People You Interact With</h2>
                <input type="text" id="peopleSearch" class="search-input" placeholder="Search people..." onkeyup="searchItems('people')">
                <div class="checkbox-grid" id="peopleList">
                    <!-- Populated dynamically via JavaScript -->
                </div>
            </div>
        </div>

        <div id="peoplePreviewModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" onclick="closeModal('peoplePreviewModal')">✕</div>
                <h2 class="section-title">Selected People</h2>
                <div id="peoplePreviewList" class="checkbox-grid"></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($tool_counts['software'] > 0): ?>
        <div id="softwareModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" onclick="closeModal('softwareModal')">✕</div>
                <h2 class="section-title">Select Software Tools</h2>
                <input type="text" id="softwareSearch" class="search-input" placeholder="Search software..." onkeyup="searchItems('software')">
                <div class="checkbox-grid" id="softwareList">
                    <?php
                    $stmt = $pdo->query("SELECT tool_id, tool_name FROM tools WHERE tool_type = 'software'");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $checked = in_array($row['tool_id'], $_SESSION['selected_tools'] ?? []) ? 'checked' : '';
                        echo "<label class='checkbox-item'>";
                        echo "<input type='checkbox' name='software[]' value='{$row['tool_id']}' $checked onchange='updateSelected(\"software\", \"{$row['tool_name']}\")'>";
                        echo htmlspecialchars($row['tool_name']);
                        echo "</label>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div id="softwarePreviewModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" onclick="closeModal('softwarePreviewModal')">✕</div>
                <h2 class="section-title">Selected Software</h2>
                <div id="softwarePreviewList" class="checkbox-grid"></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($tool_counts['hardware'] > 0): ?>
        <div id="hardwareModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" onclick="closeModal('hardwareModal')">✕</div>
                <h2 class="section-title">Select Hardware Tools</h2>
                <input type="text" id="hardwareSearch" class="search-input" placeholder="Search hardware..." onkeyup="searchItems('hardware')">
                <div class="checkbox-grid" id="hardwareList">
                    <?php
                    $stmt = $pdo->query("SELECT tool_id, tool_name FROM tools WHERE tool_type = 'hardware'");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $checked = in_array($row['tool_id'], $_SESSION['selected_tools'] ?? []) ? 'checked' : '';
                        echo "<label class='checkbox-item'>";
                        echo "<input type='checkbox' name='hardware[]' value='{$row['tool_id']}' $checked onchange='updateSelected(\"hardware\", \"{$row['tool_name']}\")'>";
                        echo htmlspecialchars($row['tool_name']);
                        echo "</label>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div id="hardwarePreviewModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" onclick="closeModal('hardwarePreviewModal')">✕</div>
                <h2 class="section-title">Selected Hardware</h2>
                <div id="hardwarePreviewList" class="checkbox-grid"></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($tool_counts['analog'] > 0): ?>
        <div id="analogModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" onclick="closeModal('analogModal')">✕</div>
                <h2 class="section-title">Select Analog Tools</h2>
                <input type="text" id="analogSearch" class="search-input" placeholder="Search analog tools..." onkeyup="searchItems('analog')">
                <div class="checkbox-grid" id="analogList">
                    <?php
                    $stmt = $pdo->query("SELECT tool_id, tool_name FROM tools WHERE tool_type = 'analog'");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $checked = in_array($row['tool_id'], $_SESSION['selected_tools'] ?? []) ? 'checked' : '';
                        echo "<label class='checkbox-item'>";
                        echo "<input type='checkbox' name='analog[]' value='{$row['tool_id']}' $checked onchange='updateSelected(\"analog\", \"{$row['tool_name']}\")'>";
                        echo htmlspecialchars($row['tool_name']);
                        echo "</label>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div id="analogPreviewModal" class="modal">
            <div class="modal-content">
                <div class="modal-close" onclick="closeModal('analogPreviewModal')">✕</div>
                <h2 class="section-title">Selected Analog Tools</h2>
                <div id="analogPreviewList" class="checkbox-grid"></div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Store all users in a JavaScript array for dynamic filtering
        const allUsers = <?php echo json_encode($users); ?>;
        const selectedPeople = <?php echo json_encode($_SESSION['selected_people'] ?? []); ?>;

        // Function to update the people list in the modal based on the selected user_id
        function updatePeopleList() {
            const userIdSelect = document.getElementById('user_id');
            const peopleList = document.getElementById('peopleList');
            const selectedUserId = userIdSelect.value;

            // Clear the current list
            peopleList.innerHTML = '';

            // Filter out the selected user and populate the list
            const filteredUsers = allUsers.filter(user => user.user_id != selectedUserId);
            filteredUsers.forEach(user => {
                const checked = selectedPeople.includes(user.user_id.toString()) ? 'checked' : '';
                const label = document.createElement('label');
                label.className = 'checkbox-item';
                label.innerHTML = `
                    <input type="checkbox" name="people[]" value="${user.user_id}" ${checked} onchange='updateSelected("people", "${user.first_name} ${user.last_name}")'>
                    ${user.first_name} ${user.last_name}
                `;
                peopleList.appendChild(label);
            });

            // Reapply search filter if a search term exists
            searchItems('people');
        }

        function openModal(modalId) {
            if (modalId === 'peopleModal') {
                const userIdSelect = document.getElementById('user_id');
                if (!userIdSelect.value) {
                    alert('Please select a user in "Who Are You?" before selecting people.');
                    return;
                }
                updatePeopleList(); // Update the people list when opening the modal
            }
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function searchItems(type) {
            const searchInput = document.getElementById(`${type}Search`).value.toLowerCase();
            const items = document.querySelectorAll(`#${type}List .checkbox-item`);
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchInput) ? 'flex' : 'none';
            });
        }

        const selectedItems = {
            people: <?php echo json_encode($_SESSION['selected_people'] ?? []); ?>,
            software: <?php echo json_encode(array_filter($_SESSION['selected_tools'] ?? [], function($id) use ($pdo) {
                $stmt = $pdo->query("SELECT tool_type FROM tools WHERE tool_id = $id");
                return $stmt->fetchColumn() === 'software';
            })); ?>,
            hardware: <?php echo json_encode(array_filter($_SESSION['selected_tools'] ?? [], function($id) use ($pdo) {
                $stmt = $pdo->query("SELECT tool_type FROM tools WHERE tool_id = $id");
                return $stmt->fetchColumn() === 'hardware';
            })); ?>,
            analog: <?php echo json_encode(array_filter($_SESSION['selected_tools'] ?? [], function($id) use ($pdo) {
                $stmt = $pdo->query("SELECT tool_type FROM tools WHERE tool_id = $id");
                return $stmt->fetchColumn() === 'analog';
            })); ?>
        };

        window.addEventListener('load', () => {
            <?php foreach ($sections as $type => $title): ?>
                <?php if ($tool_counts[$type] > 0): ?>
                    updateSelected('<?= $type ?>', '');
                <?php endif; ?>
            <?php endforeach; ?>
        });

        function updateSelected(type, itemText) {
            const checkboxes = document.querySelectorAll(`#${type}List input[type="checkbox"]:checked`);
            const selected = Array.from(checkboxes).map(cb => ({
                id: cb.value,
                text: cb.parentElement.textContent.trim()
            }));
            
            // Update the count
            const countContainer = document.getElementById(`${type}Count`);
            countContainer.textContent = `${selected.length}`;
            
            // Update the preview
            const previewList = document.getElementById(`${type}PreviewList`);
            previewList.innerHTML = selected.length > 0
                ? selected.map(item => `<div class="checkbox-item">${item.text}</div>`).join('')
                : '<p class="text-gray-500 col-span-full text-center">No items selected</p>';

            // Update the hidden input with selected IDs
            const hiddenInput = document.getElementById(`hidden${type.charAt(0).toUpperCase() + type.slice(1)}`);
            hiddenInput.value = selected.map(item => item.id).join(','); // Store as comma-separated string

            // Update selectedPeople for people type to keep track of selections
            if (type === 'people') {
                selectedItems.people = selected.map(item => item.id);
            }
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => modal.style.display = 'none');
            }
        };

        // Update the people list whenever the user_id selection changes
        document.getElementById('user_id').addEventListener('change', updatePeopleList);

        document.getElementById('surveyForm').onsubmit = function(e) {
            return true; // Allow form submission
        };
    </script>
</body>
</html>