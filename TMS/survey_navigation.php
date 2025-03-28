<?php
function getNextSurveyPage($selectedPeople, $selectedTools, $currentPage) {
    // Categorize selected tools by type
    $softwareTools = [];
    $hardwareTools = [];
    $analogTools = [];
    
    if (!empty($selectedTools)) {
        global $pdo; // Assuming $pdo is available from db_connect.php
        foreach ($selectedTools as $toolId) {
            $stmt = $pdo->prepare("SELECT tool_type FROM tools WHERE tool_id = :tool_id");
            $stmt->execute(['tool_id' => $toolId]);
            $toolType = $stmt->fetchColumn();
            if ($toolType === 'software') {
                $softwareTools[] = $toolId;
            } elseif ($toolType === 'hardware') {
                $hardwareTools[] = $toolId;
            } elseif ($toolType === 'analog') {
                $analogTools[] = $toolId;
            }
        }
    }

    // Define the survey page order
    $pages = [
        'index' => ['next' => 'people', 'condition' => !empty($selectedPeople)],
        'people' => ['next' => 'software', 'condition' => !empty($softwareTools)],
        'software' => ['next' => 'hardware', 'condition' => !empty($hardwareTools)],
        'hardware' => ['next' => 'analog', 'condition' => !empty($analogTools)],
        'analog' => ['next' => 'results', 'condition' => true] // Always go to results after analog
    ];

    $current = $currentPage;
    while (isset($pages[$current])) {
        $next = $pages[$current]['next'];
        $condition = $pages[$current]['condition'];
        
        // If the condition for the next page is met, return that page
        if ($condition) {
            return $next . '_survey.php';
        }
        
        // Otherwise, skip to the next page in the sequence
        $current = $next;
    }

    // If no conditions are met, go to results
    return 'submit.php';
}