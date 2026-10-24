<?php
header('Content-Type: application/json');
require_once '../config.php';

$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, TRUE);

if (isset($data['id']) && isset($data['status'])) {
    try {
        $stmt = $pdo->prepare("UPDATE projects SET status_board = ? WHERE id = ?");
        $stmt->execute([$data['status'], $data['id']]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Dados faltando na requisição.']);
}
?>
