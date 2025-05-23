<?php
// Database configuration
$host = 'localhost';
$dbname = 'new_survey';
$username = 'root';
$password = '';

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch questions and categorize them
    $categories = ['human', 'software', 'hardware', 'analog'];
    $questions = ['planned' => [], 'actual' => [], 'human' => [], 'software' => [], 'hardware' => [], 'analog' => []];
    $stmt = $conn->prepare("SELECT question_id, question_text, variable_code, category FROM questions");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        if ($row['category'] === 'human' && $row['variable_code']) {
            $code = strtoupper($row['variable_code']);
            if (in_array($code, ['A', 'C', 'E', 'G', 'I', 'K', 'M', 'O'])) {
                $questions['planned'][$row['question_id']] = $row['question_text'];
            } elseif (in_array($code, ['B', 'D', 'F', 'H', 'J', 'L', 'N'])) {
                $questions['actual'][$row['question_id']] = $row['question_text'];
            }
        }
        $questions[$row['category']][$row['question_id']] = $row['question_text'];
    }
    $stmt->close();

    // Fetch respondents (users)
    $respondents = [];
    $stmt = $conn->prepare("SELECT user_id, first_name, last_name FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $respondents[$row['user_id']] = $row['first_name'] . ' ' . $row['last_name'];
    }
    $stmt->close();

    // Fetch subjects (people, tools) based on category
    $subjects = [];
    foreach ($categories as $category) {
        if ($category === 'human') {
            $stmt = $conn->prepare("SELECT user_id, first_name, last_name FROM users");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $subjects[$category][$row['user_id']] = $row['first_name'] . ' ' . $row['last_name'];
            }
        } else {
            $stmt = $conn->prepare("SELECT tool_id, tool_name FROM tools WHERE tool_type = ?");
            $stmt->bind_param('s', $category);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $subjects[$category][$row['tool_id']] = $row['tool_name'];
            }
        }
        $stmt->close();
    }

    // Fetch all responses
    $allResponses = [];
    $stmt = $conn->prepare("SELECT question_id, user_id, subject_id, subject_type, option_id FROM responses");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $allResponses[$row['question_id']][$row['user_id']][$row['subject_id']] = $row['option_id'];
    }
    $stmt->close();

    // Fetch question options with text
    $questionOptions = [];
    $questionOptionsText = [];
    $stmt = $conn->prepare("SELECT option_id, question_id, option_text, option_order FROM question_options");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $questionOptions[$row['option_id']] = $row['option_order'];
        $questionOptionsText[$row['question_id']][$row['option_order']] = $row['option_text'];
    }
    $stmt->close();

    // Generate question pairs and identify unpaired questions
    $questionPairs = [];
    $unpairedQuestions = [];
    $stmt = $conn->prepare("SELECT question_id, variable_code, category FROM questions WHERE variable_code IS NOT NULL");
    $stmt->execute();
    $result = $stmt->get_result();
    $questionsByCategory = ['human' => [], 'software' => [], 'hardware' => []];
    while ($row = $result->fetch_assoc()) {
        $questionsByCategory[$row['category']][$row['variable_code']] = $row['question_id'];
    }
    $stmt->close();

    foreach (['human', 'software', 'hardware'] as $category) {
        $codes = array_keys($questionsByCategory[$category]);
        for ($i = 0; $i < count($codes) - 1; $i += 2) {
            $plannedCode = $codes[$i];
            $actualCode = $codes[$i + 1];
            $questionPairs[$questionsByCategory[$category][$plannedCode]] = $questionsByCategory[$category][$actualCode];
        }
        if (count($codes) % 2 !== 0) {
            $lastCode = end($codes);
            $unpairedQuestions[$questionsByCategory[$category][$lastCode]] = $category;
        }
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TMS Interactions - Merged Analysis Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-matrix"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4cc9f0;
            --warning: #f72585;
            --info: #4895ef;
            --border-radius: 12px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            --gradient: linear-gradient(120deg, #4361ee, #4cc9f0);
        }
        
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; color: var(--dark); margin: 0; padding: 0; min-height: 100vh; }
        .header-section { background: var(--gradient); color: white; padding: 2rem 0; margin-bottom: 2rem; border-radius: 0 0 var(--border-radius) var(--border-radius); box-shadow: var(--box-shadow); }
        .dashboard-container { max-width: 1400px; margin: 0 auto; padding: 0 20px; }
        .card { border: none; border-radius: var(--border-radius); box-shadow: var(--box-shadow); margin-bottom: 2rem; overflow: hidden; transition: transform 0.3s ease; }
        .card:hover { transform: translateY(-5px); }
        .card-header { background: var(--gradient); color: white; font-weight: 600; border: none; padding: 1rem 1.5rem; }
        .control-card { background-color: white; padding: 1.5rem; }
        .form-control, .form-select { padding: 0.8rem; border-radius: var(--border-radius); border: 1px solid #dee2e6; margin-bottom: 1rem; font-size: 1rem; }
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25); border-color: var(--primary); }
        .btn { padding: 0.8rem 1.5rem; border-radius: var(--border-radius); font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; transition: all 0.3s ease; }
        .btn-primary { background: var(--primary); border: none; }
        .btn-primary:hover { background: var(--secondary); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); }
        .btn-success { background: var(--success); border: none; }
        .btn-success:hover { background: #3ba5d1; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); }
        .table-container { overflow: auto; border-radius: var(--border-radius); background: white; box-shadow: var(--box-shadow); margin: 1.5rem 0; }
        .table { margin-bottom: 0; width: 100%; }
        .table thead th { background: var(--primary); color: white; border: none; padding: 1rem; position: sticky; top: 0; }
        .table tbody td { padding: 0.8rem; vertical-align: middle; border-color: #edf2f7; }
        .table tbody tr:hover { background-color: rgba(72, 149, 239, 0.05); }
        .table tbody td[style*="background-color"] { transition: all 0.3s ease; }
        .clickable-cell { cursor: pointer; }
        .clickable-cell:hover { opacity: 0.9; }
        .section-title { display: flex; align-items: center; font-size: 1.5rem; margin-bottom: 1rem; color: var(--primary); font-weight: 600; }
        .section-title i { margin-right: 0.5rem; }
        .legend { background: white; border-radius: var(--border-radius); padding: 1rem; margin-top: 1rem; display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem; box-shadow: var(--box-shadow); }
        .legend-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; background-color: rgba(255, 255, 255, 0.8); padding: 0.5rem 1rem; border-radius: 50px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); }
        .legend-color { width: 25px; height: 25px; border-radius: 50%; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .download-btn { width: auto; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; }
        .key-info { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; background-color: #e9ecef; border-radius: 50px; font-size: 0.9rem; margin-left: 1rem; }
        .nav-pills { margin-bottom: 2rem; justify-content: center; }
        .nav-pills .nav-link { border-radius: var(--border-radius); padding: 0.8rem 1.5rem; color: var(--dark); font-weight: 600; margin: 0 0.5rem; transition: all 0.3s ease; }
        .nav-pills .nav-link.active { background-color: var(--primary); color: white; box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3); }
        .nav-pills .nav-link:hover:not(.active) { background-color: rgba(67, 97, 238, 0.1); transform: translateY(-2px); }
        .options-container { background: white; border-radius: var(--border-radius); padding: 1rem; margin-top: 1rem; box-shadow: var(--box-shadow); }
        .options-list { list-style: none; padding: 0; margin: 0; }
        .option-item { display: flex; align-items: center; padding: 0.5rem; border-bottom: 1px solid #edf2f7; }
        .option-item:last-child { border-bottom: none; }
        .option-number { width: 25px; height: 25px; border-radius: 50%; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; margin-right: 0.75rem; font-weight: 600; }
        .modal-body ul { list-style: none; padding: 0; }
        .modal-body .option-item { margin-bottom: 0.5rem; }
        .unpaired-highlight { background-color: #fff3cd; color: #856404; padding: 0.2rem 0.5rem; border-radius: 4px; }
        .match { background-color: rgba(29, 209, 161, 0.85) !important; color: white; }
        .deviation { background-color: rgba(255, 107, 107, 0.85) !important; color: white; }
        .loading-spinner { display: none; justify-content: center; padding: 2rem; }
        .spinner-border { width: 3rem; height: 3rem; color: var(--primary); }
        .score-details-table td { padding: 0.5rem; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.5s ease forwards; }
        @media (max-width: 768px) { 
            .header-section { padding: 1.5rem 0; } 
            .card-header { padding: 0.8rem 1rem; } 
            .legend { flex-direction: column; align-items: flex-start; } 
            .section-title { font-size: 1.2rem; } 
            .table-container { font-size: 0.9rem; }
            .table thead th { padding: 0.5rem; }
            .table tbody td { padding: 0.5rem; }
        }
    </style>
</head>
<body>
    <div class="header-section py-4">
        <div class="container text-center">
            <h1 class="display-4 fw-bold"><i class="fas fa-project-diagram me-2"></i>TMS Merged Interaction Analysis</h1>
            <p class="lead">Visualize planned and actual interactions with deviation highlighting</p>
        </div>
    </div>
    
    <div class="dashboard-container">
        <ul class="nav nav-pills mb-4" id="categoryTabs">
            <li class="nav-item"><a class="nav-link active" href="#" data-category="human"><i class="fas fa-user me-2"></i>Human</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-category="software"><i class="fas fa-code me-2"></i>Software</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-category="hardware"><i class="fas fa-microchip me-2"></i>Hardware</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-category="analog"><i class="fas fa-tools me-2"></i>Analog</a></li>
        </ul>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card control-card fade-in">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <label for="plannedQuestion" class="form-label"><i class="fas fa-clipboard-list me-2"></i>Planned Question:</label>
                                <select id="plannedQuestion" class="form-select" onchange="updateTable()"></select>
                            </div>
                            <div class="col-md-5">
                                <label for="actualQuestion" class="form-label"><i class="fas fa-clipboard-check me-2"></i>Actual Question:</label>
                                <select id="actualQuestion" class="form-select" onchange="updateTable()"></select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-primary w-100" onclick="updateTable()"><i class="fas fa-sync-alt me-2"></i>Generate</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12 fade-in" style="animation-delay: 0.2s;">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-table me-2"></i>Merged Planned & Actual Responses</h5>
                        <span class="badge bg-light text-dark" id="mergedCount">0 responses</span>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted mb-3" id="mergedKeyword">Select questions above</h6>
                        <div class="table-container" id="mergedTable"></div>
                        <button id="downloadMergedTable" class="btn btn-success download-btn"><i class="fas fa-download me-2"></i>Download as PNG</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 fade-in" style="animation-delay: 0.4s;">
                <div class="legend">
                    <div class="legend-item"><div class="legend-color match"></div>Match</div>
                    <div class="legend-item"><div class="legend-color deviation"></div>Deviation</div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="scoreDetailsModal" tabindex="-1" aria-labelledby="scoreDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="scoreDetailsModalLabel">Score Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="scoreDetailsContent"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="plannedOptionsModal" tabindex="-1" aria-labelledby="plannedOptionsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="plannedOptionsModalLabel">Planned Question Options</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="plannedOptionsContent"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="actualOptionsModal" tabindex="-1" aria-labelledby="actualOptionsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="actualOptionsModalLabel">Actual Question Options</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="actualOptionsContent"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <footer class="text-center py-4 text-muted">
            <p><i class="fas fa-chart-bar me-2"></i>TMS Merged Interaction Analysis Dashboard © 2025</p>
        </footer>
    </div>

    <script>
    const questions = <?php echo json_encode($questions); ?>;
    const respondents = <?php echo json_encode($respondents); ?>;
    const subjects = <?php echo json_encode($subjects); ?>;
    const allResponses = <?php echo json_encode($allResponses); ?>;
    const questionOptions = <?php echo json_encode($questionOptions); ?>;
    const questionOptionsText = <?php echo json_encode($questionOptionsText); ?>;
    const questionPairs = <?php echo json_encode($questionPairs); ?>;
    const unpairedQuestions = <?php echo json_encode($unpairedQuestions); ?>;

    let selectedCategory = 'human';

    document.querySelectorAll('#categoryTabs .nav-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('#categoryTabs .nav-link').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            selectedCategory = this.getAttribute('data-category');
            updateQuestions();
        });
    });

    function updateQuestions() {
        const plannedSelect = document.getElementById('plannedQuestion');
        const actualSelect = document.getElementById('actualQuestion');
        plannedSelect.innerHTML = '';
        actualSelect.innerHTML = '';

        plannedSelect.innerHTML = '<option value="">-- Select Planned Question --</option>';
        actualSelect.innerHTML = '<option value="">-- Select Actual Question --</option>';

        const pairedActualIds = Object.values(questionPairs);
        const allQuestions = questions[selectedCategory];

        Object.entries(allQuestions).forEach(([qid, qtext]) => {
            if (questionPairs[qid]) {
                plannedSelect.innerHTML += `<option value="${qid}">${qtext}</option>`;
            }
        });

        Object.entries(allQuestions).forEach(([qid, qtext]) => {
            if (!questionPairs[qid] && !pairedActualIds.includes(parseInt(qid))) {
                actualSelect.innerHTML += `<option value="${qid}">${qtext} <span class="unpaired-highlight">(Unpaired)</span></option>`;
            } else if (pairedActualIds.includes(parseInt(qid))) {
                actualSelect.innerHTML += `<option value="${qid}">${qtext}</option>`;
            }
        });

        plannedSelect.removeEventListener('change', autoSelectActual);
        plannedSelect.addEventListener('change', autoSelectActual);
        
        document.getElementById('mergedTable').innerHTML = '';
        document.getElementById('mergedKeyword').textContent = 'Select questions above';
        document.getElementById('mergedCount').textContent = '0 responses';
    }

    function autoSelectActual() {
        const plannedQid = this.value;
        if (plannedQid && questionPairs[plannedQid]) {
            document.getElementById('actualQuestion').value = questionPairs[plannedQid];
            updateTable();
        }
    }

    function updateTable() {
        document.getElementById('loadingSpinner').style.display = 'flex';
        
        setTimeout(() => {
            const plannedQid = document.getElementById('plannedQuestion').value;
            const actualQid = document.getElementById('actualQuestion').value;
            
            if (plannedQid && actualQid) {
                generateMergedTable('mergedTable', plannedQid, actualQid, selectedCategory);
            }
            
            document.getElementById('loadingSpinner').style.display = 'none';
        }, 300);
    }

    function generateMergedTable(containerId, plannedQid, actualQid, category) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';
        
        const table = document.createElement('table');
        table.classList.add('table', 'table-bordered', 'table-hover');
        const thead = document.createElement('thead');
        const tbody = document.createElement('tbody');

        // Create header row
        const headerRow = document.createElement('tr');
        const thEmpty = document.createElement('th');
        thEmpty.textContent = 'Respondent';
        thEmpty.style.minWidth = '120px';
        headerRow.appendChild(thEmpty);
        
        const subjectEntries = Object.entries(subjects[category]);
        subjectEntries.forEach(([subject_id, subject]) => {
            const th = document.createElement('th');
            th.textContent = subject;
            th.style.minWidth = '100px';
            headerRow.appendChild(th);
        });
        thead.appendChild(headerRow);

        // Create data rows
        let responseCount = 0;
        Object.entries(respondents).forEach(([user_id, respondent]) => {
            const row = document.createElement('tr');
            const tdRespondent = document.createElement('td');
            tdRespondent.textContent = respondent;
            tdRespondent.style.fontWeight = '500';
            row.appendChild(tdRespondent);

            subjectEntries.forEach(([subject_id, subject]) => {
                const plannedOption = allResponses[plannedQid]?.[user_id]?.[subject_id] || null;
                const actualOption = allResponses[actualQid]?.[user_id]?.[subject_id] || null;
                const td = document.createElement('td');
                td.classList.add('clickable-cell');
                
                if (plannedOption || actualOption) {
                    const plannedOrder = plannedOption ? questionOptions[plannedOption] || 1 : null;
                    const actualOrder = actualOption ? questionOptions[actualOption] || 1 : null;
                    
                    if (plannedOption && actualOption) {
                        td.classList.add(plannedOrder === actualOrder ? 'match' : 'deviation');
                        responseCount++;
                    } else {
                        td.classList.add('deviation'); // Treat missing responses as deviations
                        if (plannedOption || actualOption) responseCount++;
                    }
                    
                    td.setAttribute('data-bs-toggle', 'modal');
                    td.setAttribute('data-bs-target', '#scoreDetailsModal');
                    td.addEventListener('click', () => showScoreDetails(plannedQid, actualQid, user_id, subject_id, respondent, subject));
                } else {
                    td.textContent = '-';
                    td.classList.add('text-muted');
                }
                row.appendChild(td);
            });
            tbody.appendChild(row);
        });

        table.appendChild(thead);
        table.appendChild(tbody);
        container.appendChild(table);

        const plannedText = questions[category][plannedQid] || 'Unknown Planned Question';
        const actualText = questions[category][actualQid] || 'Unknown Actual Question';
        const keywordElement = document.getElementById('mergedKeyword');
        keywordElement.innerHTML = `
            Planned: ${plannedText} | Actual: ${actualText}
            <button class="btn btn-sm btn-info ms-2" data-bs-toggle="modal" 
                    data-bs-target="#plannedOptionsModal"
                    onclick="showOptions('${plannedQid}', 'planned')">
                <i class="fas fa-info-circle"></i> Planned Options
            </button>
            <button class="btn btn-sm btn-info ms-2" data-bs-toggle="modal" 
                    data-bs-target="#actualOptionsModal"
                    onclick="showOptions('${actualQid}', 'actual')">
                <i class="fas fa-info-circle"></i> Actual Options
            </button>
        `;
        document.getElementById('mergedCount').textContent = `${responseCount} responses`;

        const optionsContainer = document.createElement('div');
        optionsContainer.classList.add('options-container');
        const optionsList = document.createElement('ul');
        optionsList.classList.add('options-list');
        
        if (questionOptionsText[plannedQid] || questionOptionsText[actualQid]) {
            const li = document.createElement('li');
            li.classList.add('option-item');
            li.textContent = 'Options apply to both Planned and Actual questions:';
            optionsList.appendChild(li);
            const uniqueOptions = {...(questionOptionsText[plannedQid] || {}), ...(questionOptionsText[actualQid] || {})};
            Object.entries(uniqueOptions).forEach(([order, text]) => {
                const li = document.createElement('li');
                li.classList.add('option-item');
                li.innerHTML = `<span class="option-number">${order}</span>${text}`;
                optionsList.appendChild(li);
            });
        }
        optionsContainer.appendChild(optionsList);
        container.appendChild(optionsContainer);
    }

    function showScoreDetails(plannedQid, actualQid, user_id, subject_id, respondent, subject) {
        const contentElement = document.getElementById('scoreDetailsContent');
        contentElement.innerHTML = '';

        const plannedOption = allResponses[plannedQid]?.[user_id]?.[subject_id] || null;
        const actualOption = allResponses[actualQid]?.[user_id]?.[subject_id] || null;
        
        const table = document.createElement('table');
        table.classList.add('table', 'score-details-table');
        const tbody = document.createElement('tbody');

        // Planned row
        const plannedRow = document.createElement('tr');
        plannedRow.innerHTML = `
            <td><strong>Planned</strong></td>
            <td>${plannedOption ? questionOptions[plannedOption] || '-' : '-'}</td>
            <td>${plannedOption ? questionOptionsText[plannedQid]?.[questionOptions[plannedOption]] || 'No text available' : 'No response'}</td>
        `;
        tbody.appendChild(plannedRow);

        // Actual row
        const actualRow = document.createElement('tr');
        actualRow.innerHTML = `
            <td><strong>Actual</strong></td>
            <td>${actualOption ? questionOptions[actualOption] || '-' : '-'}</td>
            <td>${actualOption ? questionOptionsText[actualQid]?.[questionOptions[actualOption]] || 'No text available' : 'No response'}</td>
        `;
        tbody.appendChild(actualRow);

        table.appendChild(tbody);
        
        contentElement.innerHTML = `
            <p><strong>Respondent:</strong> ${respondent}</p>
            <p><strong>Subject:</strong> ${subject}</p>
        `;
        contentElement.appendChild(table);
    }

    function showOptions(questionId, type) {
        const contentElement = document.getElementById(`${type}OptionsContent`);
        contentElement.innerHTML = '';
        
        if (questionOptionsText[questionId]) {
            const ul = document.createElement('ul');
            Object.entries(questionOptionsText[questionId]).forEach(([order, text]) => {
                const li = document.createElement('li');
                li.classList.add('option-item');
                li.innerHTML = `<span class="option-number">${order}</span>${text}`;
                ul.appendChild(li);
            });
            contentElement.appendChild(ul);
        } else {
            contentElement.textContent = 'No options available for this question.';
        }
    }

    document.getElementById('downloadMergedTable').addEventListener('click', () => downloadTable('mergedTable', 'merged'));

    function downloadTable(containerId, prefix) {
        const button = document.getElementById(`download${containerId.charAt(0).toUpperCase() + containerId.slice(1)}`);
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Preparing...';
        button.disabled = true;

        setTimeout(() => {
            const container = document.getElementById(containerId);
            const keywordElement = document.getElementById(`${containerId.replace('Table', 'Keyword')}`);
            const originalOverflow = container.style.overflow;
            const originalHeight = container.style.height;
            const originalMaxHeight = container.style.maxHeight;

            // Create a temporary container
            const tempContainer = document.createElement('div');
            tempContainer.style.position = 'absolute';
            tempContainer.style.left = '-9999px';
            tempContainer.style.backgroundColor = '#ffffff';
            tempContainer.style.padding = '20px';
            tempContainer.style.border = '1px solid #dee2e6';
            tempContainer.style.borderRadius = '12px';
            tempContainer.style.boxShadow = '0 8px 30px rgba(0, 0, 0, 0.12)';
            tempContainer.style.width = `${container.scrollWidth + 40}px`;

            // Clone the question text
            const questionClone = keywordElement.cloneNode(true);
            questionClone.style.marginBottom = '15px';
            questionClone.style.fontSize = '1.1rem';
            questionClone.style.color = '#212529';
            const buttonsInClone = questionClone.querySelectorAll('button');
            buttonsInClone.forEach(btn => btn.remove());

            // Clone the table and modify cells to show scores and options
            const tableClone = container.querySelector('table').cloneNode(true);
            const plannedQid = document.getElementById('plannedQuestion').value;
            const actualQid = document.getElementById('actualQuestion').value;
            const tbodyRows = tableClone.querySelectorAll('tbody tr');

            tbodyRows.forEach((row, rowIndex) => {
                const user_id = Object.keys(respondents)[rowIndex];
                const cells = row.querySelectorAll('td:not(:first-child)');
                cells.forEach((cell, cellIndex) => {
                    const subject_id = Object.keys(subjects[selectedCategory])[cellIndex];
                    const plannedOption = allResponses[plannedQid]?.[user_id]?.[subject_id] || null;
                    const actualOption = allResponses[actualQid]?.[user_id]?.[subject_id] || null;
                    
                    if (plannedOption || actualOption) {
                        const plannedOrder = plannedOption ? questionOptions[plannedOption] || '-' : '-';
                        const actualOrder = actualOption ? questionOptions[actualOption] || '-' : '-';
                        const plannedText = plannedOption ? questionOptionsText[plannedQid]?.[questionOptions[plannedOption]] || 'No text' : 'No response';
                        const actualText = actualOption ? questionOptionsText[actualQid]?.[questionOptions[actualOption]] || 'No text' : 'No response';
                        
                        cell.innerHTML = `
                            <div>P: ${plannedOrder} A: ${actualOrder}</div>
                            <div style="font-size: 0.8rem; color: #555;">P: ${plannedText}</div>
                            <div style="font-size: 0.8rem; color: #555;">A: ${actualText}</div>
                        `;
                        cell.style.padding = '0.5rem';
                        cell.style.lineHeight = '1.2';
                    } else {
                        cell.textContent = '-';
                        cell.classList.add('text-muted');
                    }
                });
            });
            tableClone.style.width = '100%';

            tempContainer.appendChild(questionClone);
            tempContainer.appendChild(tableClone);
            document.body.appendChild(tempContainer);

            // Temporarily adjust styles
            container.style.overflow = 'visible';
            container.style.height = 'auto';
            container.style.maxHeight = 'none';

            html2canvas(tempContainer, {
                backgroundColor: '#ffffff',
                scale: 2,
                scrollY: -window.scrollY,
                width: tempContainer.offsetWidth,
                height: tempContainer.offsetHeight,
                logging: false
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = `TMS_${prefix}_analysis_${Date.now()}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();

                // Clean up
                button.innerHTML = originalText;
                button.disabled = false;
                container.style.overflow = originalOverflow;
                container.style.height = originalHeight;
                container.style.maxHeight = originalMaxHeight;
                document.body.removeChild(tempContainer);
            }).catch(error => {
                console.error("Error generating image:", error);
                button.innerHTML = originalText;
                button.disabled = false;
                container.style.overflow = originalOverflow;
                container.style.height = originalHeight;
                container.style.maxHeight = originalMaxHeight;
                document.body.removeChild(tempContainer);
                alert("Error capturing table. Please try again.");
            });
        }, 100);
    }

    function handleScrollAnimation() {
        const elements = document.querySelectorAll('.fade-in');
        elements.forEach(element => {
            const position = element.getBoundingClientRect();
            if (position.top < window.innerHeight) {
                element.style.opacity = 1;
                element.style.transform = 'translateY(0)';
            }
        });
    }

    window.addEventListener('scroll', handleScrollAnimation);
    window.addEventListener('load', () => {
        handleScrollAnimation();
        updateQuestions();
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
