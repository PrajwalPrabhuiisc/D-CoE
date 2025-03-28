<?php
include 'config.php';
header('Content-Type: application/json');

$dependencyID = $_POST['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM TaskDependencies WHERE DependencyID = ?");
    $stmt->execute([$dependencyID]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>