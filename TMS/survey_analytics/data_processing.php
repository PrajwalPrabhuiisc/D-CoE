<?php
require_once 'db_connect.php';

function getQuestionsByCategory($pdo, $category) {
    $stmt = $pdo->prepare("SELECT question_id, question_text, variable_code FROM questions WHERE category = ?");
    $stmt->execute([$category]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUsers($pdo) {
    $stmt = $pdo->query("SELECT user_id, CONCAT(first_name, ' ', last_name) AS name FROM users");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTools($pdo, $category) {
    $stmt = $pdo->prepare("SELECT tool_id, tool_name FROM tools WHERE tool_type = ?");
    $stmt->execute([$category === 'hardware' ? 'hardware' : 'software']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getResponses($pdo, $question_id) {
    $stmt = $pdo->prepare("
        SELECT r.user_id, r.option_id, qo.option_text 
        FROM responses r 
        JOIN question_options qo ON r.option_id = qo.option_id 
        WHERE r.question_id = ?
    ");
    $stmt->execute([$question_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getResponseFrequencies($pdo, $question_id) {
    $stmt = $pdo->prepare("
        SELECT qo.option_text, COUNT(*) as count 
        FROM responses r 
        JOIN question_options qo ON r.option_id = qo.option_id 
        WHERE r.question_id = ? 
        GROUP BY qo.option_text
    ");
    $stmt->execute([$question_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMatrixData($pdo, $question_id, $category, $users) {
    $matrix = [];
    $evaluated_entities = []; // Will hold users or tools based on category

    // Determine evaluated entities based on category
    if ($category === 'human') {
        $stmt = $pdo->prepare("SELECT DISTINCT person_id FROM user_people");
        $stmt->execute();
        $evaluated_entities = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // Fix: Use 0 instead of 'person_id'
    } elseif (in_array($category, ['software', 'hardware'])) {
        $stmt = $pdo->prepare("SELECT DISTINCT tool_id FROM user_tools");
        $stmt->execute();
        $evaluated_entities = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // Fix: Use 0 instead of 'tool_id'
    }

    // Initialize the matrix with 0s
    // Rows: Respondents (user_id), Columns: Evaluated entities (person_id or tool_id)
    foreach ($users as $respondent) {
        foreach ($evaluated_entities as $evaluated_id) {
            $matrix[$respondent['user_id']][$evaluated_id] = 0;
        }
    }

    // Fetch responses for the given question
    if ($category === 'human') {
        $stmt = $pdo->prepare("
            SELECT r.user_id, up.person_id, r.option_id, qo.option_order, r.timestamp
            FROM responses r
            JOIN user_people up ON r.user_id = up.user_id
            JOIN question_options qo ON r.option_id = qo.option_id
            WHERE r.question_id = :question_id
            ORDER BY r.timestamp DESC
        ");
    } elseif (in_array($category, ['software', 'hardware'])) {
        $stmt = $pdo->prepare("
            SELECT r.user_id, ut.tool_id AS person_id, r.option_id, qo.option_order, r.timestamp
            FROM responses r
            JOIN user_tools ut ON r.user_id = ut.user_id
            JOIN question_options qo ON r.option_id = qo.option_id
            WHERE r.question_id = :question_id
            ORDER BY r.timestamp DESC
        ");
    }
    $stmt->execute(['question_id' => $question_id]);
    $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Populate the matrix with the latest response value
    $processed = []; // Track processed combinations to avoid duplicates
    foreach ($responses as $response) {
        $key = $response['user_id'] . '-' . $response['person_id'];
        if (!isset($processed[$key])) {
            $respondent_id = $response['user_id'];    // The person providing feedback (row)
            $evaluated_id = $response['person_id'];   // The person or tool being evaluated (column)
            $value = $response['option_order'];       // The numerical value of the response (1-5)

            // Only populate if the respondent and evaluated entity are different (no self-evaluation)
            if ($respondent_id != $evaluated_id && in_array($evaluated_id, $evaluated_entities)) {
                $matrix[$respondent_id][$evaluated_id] = $value;
            }
            $processed[$key] = true;
        }
    }

    return $matrix;
}
?>
