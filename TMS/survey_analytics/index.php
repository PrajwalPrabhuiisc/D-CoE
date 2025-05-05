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
    <title>TMS Interactions - Analysis Dashboard</title>
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
        
        .analytics-card { background: white; border-radius: var(--border-radius); box-shadow: var(--box-shadow); padding: 1.5rem; margin-top: 2rem; }
        .analytics-section { margin-bottom: 2rem; }
        .analytics-title { color: var(--primary); font-weight: 600; margin-bottom: 1rem; }
        .chart-container { position: relative; height: 300px; width: 100%; margin-top: 1rem; }
        
        @media (max-width: 768px) { .header-section { padding: 1.5rem 0; } .card-header { padding: 0.8rem 1rem; } .legend { flex-direction: column; align-items: flex-start; } .section-title { font-size: 1.2rem; } .chart-container { height: 200px; } }
        .heat-1 { background-color: rgba(255, 107, 107, 0.85) !important; color: white; }
        .heat-2 { background-color: rgba(255, 159, 67, 0.85) !important; color: white; }
        .heat-3 { background-color: rgba(254, 202, 87, 0.85) !important; color: #333; }
        .heat-4 { background-color: rgba(72, 219, 156, 0.85) !important; color: white; }
        .heat-5 { background-color: rgba(29, 209, 161, 0.85) !important; color: white; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.5s ease forwards; }
        .loading-spinner { display: none; justify-content: center; padding: 2rem; }
        .spinner-border { width: 3rem; height: 3rem; color: var(--primary); }
    </style>
</head>
<body>
    <div class="header-section py-4">
        <div class="container text-center">
            <h1 class="display-4 fw-bold"><i class="fas fa-project-diagram me-2"></i>TMS Interaction Analysis</h1>
            <p class="lead">Visualize and analyze interactions across different categories</p>
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
            <div class="col-lg-6 fade-in" style="animation-delay: 0.2s;">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Planned Response</h5>
                        <span class="badge bg-light text-dark" id="plannedCount">0 responses</span>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted mb-3" id="plannedKeyword">Select a question above</h6>
                        <div class="table-container" id="plannedTable"></div>
                        <button id="downloadPlannedTable" class="btn btn-success download-btn"><i class="fas fa-download me-2"></i>Download as PNG</button>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 fade-in" style="animation-delay: 0.4s;">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Actual Response</h5>
                        <span class="badge bg-light text-dark" id="actualCount">0 responses</span>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted mb-3" id="actualKeyword">Select a question above</h6>
                        <div class="table-container" id="actualTable"></div>
                        <button id="downloadActualTable" class="btn btn-success download-btn"><i class="fas fa-download me-2"></i>Download as PNG</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 fade-in" style="animation-delay: 0.6s;">
                <div class="legend">
                    <div class="legend-item"><div class="legend-color heat-1"></div>1 - Lowest</div>
                    <div class="legend-item"><div class="legend-color heat-2"></div>2</div>
                    <div class="legend-item"><div class="legend-color heat-3"></div>3</div>
                    <div class="legend-item"><div class="legend-color heat-4"></div>4</div>
                    <div class="legend-item"><div class="legend-color heat-5"></div>5 - Highest</div>
                </div>
            </div>
        </div>

        <!-- Analytics Section with Charts -->
        <div class="row">
            <div class="col-12 fade-in" style="animation-delay: 0.8s;">
                <div class="analytics-card">
                    <h4 class="analytics-title"><i class="fas fa-chart-bar me-2"></i>Analytics Summary</h4>
                    <div id="analyticsContent">
                        <div class="analytics-section">
                            <h5>1. Expectation-Reality Gap</h5>
                            <div class="chart-container"><canvas id="gapChart"></canvas></div>
                        </div>
                        <div class="analytics-section">
                            <h5>2. Specialization Mapping</h5>
                            <div class="chart-container"><canvas id="specializationChart"></canvas></div>
                        </div>
                        <div class="analytics-section">
                            <h5>3. Consensus Analysis</h5>
                            <div class="chart-container"><canvas id="consensusChart"></canvas></div>
                        </div>
                        <div class="analytics-section">
                            <h5>5. Knowledge Utilization Efficiency</h5>
                            <div class="chart-container"><canvas id="utilizationChart"></canvas></div>
                        </div>
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
            <p><i class="fas fa-chart-bar me-2"></i>TMS Interaction Analysis Dashboard © 2025</p>
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
    let gapChart, specializationChart, consensusChart, utilizationChart;

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
        
        document.getElementById('plannedTable').innerHTML = '';
        document.getElementById('actualTable').innerHTML = '';
        document.getElementById('plannedKeyword').textContent = 'Select a question above';
        document.getElementById('actualKeyword').textContent = 'Select a question above';
        document.getElementById('plannedCount').textContent = '0 responses';
        document.getElementById('actualCount').textContent = '0 responses';
        updateAnalytics();
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
            
            if (plannedQid) {
                generateTable('plannedTable', plannedQid, 'Planned: ', questions[selectedCategory][plannedQid], selectedCategory);
            }
            if (actualQid) {
                generateTable('actualTable', actualQid, 'Actual: ', questions[selectedCategory][actualQid], selectedCategory);
            }
            
            document.getElementById('loadingSpinner').style.display = 'none';
            updateAnalytics();
        }, 300);
    }

    function generateTable(containerId, questionId, prefix, questionText, category) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';
        
        const table = document.createElement('table');
        table.classList.add('table', 'table-bordered', 'table-hover');
        const thead = document.createElement('thead');
        const tbody = document.createElement('tbody');

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

        let responseCount = 0;
        Object.entries(respondents).forEach(([user_id, respondent]) => {
            const row = document.createElement('tr');
            const tdRespondent = document.createElement('td');
            tdRespondent.textContent = respondent;
            tdRespondent.style.fontWeight = '500';
            row.appendChild(tdRespondent);

            subjectEntries.forEach(([subject_id, _]) => {
                const option_id = allResponses[questionId]?.[user_id]?.[subject_id] || null;
                const td = document.createElement('td');
                if (option_id) {
                    const option_order = questionOptions[option_id] || 1;
                    td.textContent = option_order;
                    td.classList.add(`heat-${option_order}`);
                    responseCount++;
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

        const keywordElement = document.getElementById(`${containerId.replace('Table', 'Keyword')}`);
        keywordElement.innerHTML = `
            ${prefix + (questionText || 'Unknown Question')}
            <button class="btn btn-sm btn-info ms-2" data-bs-toggle="modal" 
                    data-bs-target="#${containerId === 'plannedTable' ? 'planned' : 'actual'}OptionsModal"
                    onclick="showOptions('${questionId}', '${containerId === 'plannedTable' ? 'planned' : 'actual'}')">
                <i class="fas fa-info-circle"></i> Options
            </button>
        `;
        document.getElementById(`${containerId.replace('Table', 'Count')}`).textContent = `${responseCount} responses`;

        const optionsContainer = document.createElement('div');
        optionsContainer.classList.add('options-container');
        const optionsList = document.createElement('ul');
        optionsList.classList.add('options-list');
        
        if (questionOptionsText[questionId]) {
            Object.entries(questionOptionsText[questionId]).forEach(([order, text]) => {
                const li = document.createElement('li');
                li.classList.add('option-item');
                li.innerHTML = `<span class="option-number heat-${order}">${order}</span>${text}`;
                optionsList.appendChild(li);
            });
        }
        optionsContainer.appendChild(optionsList);
        container.appendChild(optionsContainer);
    }

    function showOptions(questionId, type) {
        const contentElement = document.getElementById(`${type}OptionsContent`);
        contentElement.innerHTML = '';
        
        if (questionOptionsText[questionId]) {
            const ul = document.createElement('ul');
            Object.entries(questionOptionsText[questionId]).forEach(([order, text]) => {
                const li = document.createElement('li');
                li.classList.add('option-item');
                li.innerHTML = `<span class="option-number heat-${order}">${order}</span>${text}`;
                ul.appendChild(li);
            });
            contentElement.appendChild(ul);
        } else {
            contentElement.textContent = 'No options available for this question.';
        }
    }

    function updateAnalytics() {
        const plannedQid = document.getElementById('plannedQuestion').value;
        const actualQid = document.getElementById('actualQuestion').value;
        console.log('Planned QID:', plannedQid, 'Actual QID:', actualQid, 'Selected Category:', selectedCategory);

        // Destroy existing charts to prevent overlap
        if (gapChart) gapChart.destroy();
        if (specializationChart) specializationChart.destroy();
        if (consensusChart) consensusChart.destroy();
        if (utilizationChart) utilizationChart.destroy();

        // Clear canvases if no data
        if (!plannedQid && !actualQid) {
            ['gapChart', 'specializationChart', 'consensusChart', 'utilizationChart'].forEach(id => {
                const ctx = document.getElementById(id).getContext('2d');
                ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
            });
            return;
        }

        // 1. Expectation-Reality Gap Analysis (Bar Chart)
        if (plannedQid && actualQid && questionPairs[plannedQid] === parseInt(actualQid)) {
            console.log('Processing Gap Analysis for:', plannedQid, actualQid);
            const subjectGaps = {};
            Object.entries(subjects[selectedCategory]).forEach(([subject_id, subject]) => {
                let totalGap = 0, responseCount = 0;
                Object.entries(respondents).forEach(([user_id, _]) => {
                    const plannedOption = allResponses[plannedQid]?.[user_id]?.[subject_id];
                    const actualOption = allResponses[actualQid]?.[user_id]?.[subject_id];
                    console.log('User:', user_id, 'Subject:', subject, 'Planned:', plannedOption, 'Actual:', actualOption);
                    if (plannedOption && actualOption) {
                        const plannedScore = questionOptions[plannedOption] || 0;
                        const actualScore = questionOptions[actualOption] || 0;
                        const gap = actualScore - plannedScore;
                        totalGap += gap;
                        responseCount++;
                    }
                });
                subjectGaps[subject] = responseCount > 0 ? (totalGap / responseCount).toFixed(2) : 0;
            });
            console.log('Subject Gaps:', subjectGaps);

            gapChart = new Chart(document.getElementById('gapChart'), {
                type: 'bar',
                data: {
                    labels: Object.keys(subjectGaps),
                    datasets: [{
                        label: 'Gap (Actual - Planned)',
                        data: Object.values(subjectGaps),
                        backgroundColor: Object.values(subjectGaps).map(gap => parseFloat(gap) >= 0 ? 'rgba(72, 219, 156, 0.7)' : 'rgba(255, 107, 107, 0.7)'),
                        borderColor: Object.values(subjectGaps).map(gap => parseFloat(gap) >= 0 ? 'rgba(72, 219, 156, 1)' : 'rgba(255, 107, 107, 1)'),
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: { y: { beginAtZero: true, title: { display: true, text: 'Gap Score' } } },
                    plugins: { legend: { display: false }, title: { display: true, text: 'Expectation-Reality Gap by Subject' } }
                }
            });
        } else {
            console.log('No valid pair for gap analysis:', plannedQid, actualQid);
        }

        // 2. Specialization Mapping (Bar Chart)
        if (plannedQid) {
            console.log('Processing Specialization for:', plannedQid);
            const specialization = {};
            Object.entries(subjects[selectedCategory]).forEach(([subject_id, subject]) => {
                let highScoreCount = 0, totalResponses = 0;
                Object.entries(respondents).forEach(([user_id, _]) => {
                    const option_id = allResponses[plannedQid]?.[user_id]?.[subject_id];
                    if (option_id) {
                        totalResponses++;
                        if (questionOptions[option_id] >= 4) highScoreCount++;
                    }
                });
                specialization[subject] = totalResponses > 0 ? (highScoreCount / totalResponses * 100).toFixed(1) : 0;
            });
            console.log('Specialization Data:', specialization);

            specializationChart = new Chart(document.getElementById('specializationChart'), {
                type: 'bar',
                data: {
                    labels: Object.keys(specialization),
                    datasets: [{
                        label: '% High Scores (≥4)',
                        data: Object.values(specialization),
                        backgroundColor: 'rgba(67, 97, 238, 0.7)',
                        borderColor: 'rgba(67, 97, 238, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: { y: { beginAtZero: true, max: 100, title: { display: true, text: '% of Respondents' } } },
                    plugins: { legend: { display: false }, title: { display: true, text: 'Specialization (Planned High Scores)' } }
                }
            });
        }

        // 3. Consensus Analysis (Bar Chart)
        if (actualQid) {
            console.log('Processing Consensus for:', actualQid);
            const variances = {};
            Object.entries(subjects[selectedCategory]).forEach(([subject_id, subject]) => {
                const scores = [];
                Object.entries(respondents).forEach(([user_id, _]) => {
                    const option_id = allResponses[actualQid]?.[user_id]?.[subject_id];
                    if (option_id) scores.push(questionOptions[option_id] || 0);
                });
                if (scores.length > 1) {
                    const mean = scores.reduce((a, b) => a + b, 0) / scores.length;
                    const variance = scores.reduce((a, b) => a + Math.pow(b - mean, 2), 0) / scores.length;
                    variances[subject] = variance.toFixed(2);
                } else {
                    variances[subject] = '0.00';
                }
            });
            console.log('Variance Data:', variances);

            consensusChart = new Chart(document.getElementById('consensusChart'), {
                type: 'bar',
                data: {
                    labels: Object.keys(variances),
                    datasets: [{
                        label: 'Variance',
                        data: Object.values(variances).map(v => parseFloat(v) || 0),
                        backgroundColor: Object.values(variances).map(v => parseFloat(v) > 1.5 ? 'rgba(255, 107, 107, 0.7)' : 'rgba(29, 209, 161, 0.7)'),
                        borderColor: Object.values(variances).map(v => parseFloat(v) > 1.5 ? 'rgba(255, 107, 107, 1)' : 'rgba(29, 209, 161, 1)'),
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: { y: { beginAtZero: true, title: { display: true, text: 'Variance' } } },
                    plugins: { legend: { display: false }, title: { display: true, text: 'Consensus (Actual Score Variance)' } }
                }
            });
        }

        // 5. Knowledge Utilization Efficiency (Doughnut Chart)
        if (plannedQid && actualQid && questionPairs[plannedQid] === parseInt(actualQid)) {
            console.log('Processing Utilization for:', plannedQid, actualQid);
            let plannedHigh = 0, actualHigh = 0, totalResponses = 0;
            Object.entries(respondents).forEach(([user_id, _]) => {
                Object.entries(subjects[selectedCategory]).forEach(([subject_id, _]) => {
                    const plannedOption = allResponses[plannedQid]?.[user_id]?.[subject_id];
                    const actualOption = allResponses[actualQid]?.[user_id]?.[subject_id];
                    if (plannedOption && actualOption) {
                        totalResponses++;
                        if (questionOptions[plannedOption] >= 4) plannedHigh++;
                        if (questionOptions[actualOption] >= 4) actualHigh++;
                    }
                });
            });
            console.log('Utilization Counts:', { plannedHigh, actualHigh, totalResponses });

            const plannedPercent = totalResponses > 0 ? (plannedHigh / totalResponses * 100).toFixed(1) : 0;
            const actualPercent = totalResponses > 0 ? (actualHigh / totalResponses * 100).toFixed(1) : 0;
            const utilization = plannedPercent > 0 ? (actualPercent / plannedPercent * 100).toFixed(1) : 0;
            const unusedPotential = plannedPercent > actualPercent ? (plannedPercent - actualPercent).toFixed(1) : 0;

            utilizationChart = new Chart(document.getElementById('utilizationChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Planned High Scores', 'Actual High Scores', 'Unused Potential'],
                    datasets: [{
                        data: [parseFloat(plannedPercent), parseFloat(actualPercent), parseFloat(unusedPotential)],
                        backgroundColor: ['rgba(67, 97, 238, 0.7)', 'rgba(29, 209, 161, 0.7)', 'rgba(255, 107, 107, 0.7)'],
                        borderColor: ['rgba(67, 97, 238, 1)', 'rgba(29, 209, 161, 1)', 'rgba(255, 107, 107, 1)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: { 
                        legend: { position: 'bottom' }, 
                        title: { display: true, text: `Utilization Efficiency: ${utilization}%` } 
                    }
                }
            });
        }
    }

    document.getElementById('downloadPlannedTable').addEventListener('click', () => downloadTable('plannedTable', 'planned'));
    document.getElementById('downloadActualTable').addEventListener('click', () => downloadTable('actualTable', 'actual'));

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

        // Create a temporary container to hold both the question and the table
        const tempContainer = document.createElement('div');
        tempContainer.style.position = 'absolute';
        tempContainer.style.left = '-9999px'; // Move it off-screen
        tempContainer.style.backgroundColor = '#ffffff';
        tempContainer.style.padding = '20px';
        tempContainer.style.border = '1px solid #dee2e6';
        tempContainer.style.borderRadius = '12px';
        tempContainer.style.boxShadow = '0 8px 30px rgba(0, 0, 0, 0.12)';
        tempContainer.style.width = `${container.scrollWidth + 40}px`; // Add padding

        // Clone the question text (keywordElement)
        const questionClone = keywordElement.cloneNode(true);
        questionClone.style.marginBottom = '15px';
        questionClone.style.fontSize = '1.1rem';
        questionClone.style.color = '#212529';
        // Remove the button from the cloned question to avoid capturing it
        const buttonInClone = questionClone.querySelector('button');
        if (buttonInClone) buttonInClone.remove();

        // Clone the table
        const tableClone = container.querySelector('table').cloneNode(true);
        tableClone.style.width = '100%';

        // Append both to the temporary container
        tempContainer.appendChild(questionClone);
        tempContainer.appendChild(tableClone);

        // Append the temporary container to the body
        document.body.appendChild(tempContainer);

        // Temporarily adjust the original container's styles
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
