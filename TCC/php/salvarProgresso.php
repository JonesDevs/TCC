<?php
session_start();
require_once "conexao.php";

header('Content-Type: application/json; charset=utf-8');

// Verifica se usuário logado
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

$idUsuario = $_SESSION['id'];

// Lê JSON do corpo
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido']);
    exit;
}

$ultimaBaia = isset($data['ultimaBaia']) ? intval($data['ultimaBaia']) : 0;
$progresso = isset($data['progresso']) ? intval($data['progresso']) : 0;

// Atualiza no banco
$stmt = $conn->prepare("UPDATE usuario SET ultima_baia = ?, progresso = ? WHERE id = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no prepare: ' . $conn->error]);
    exit;
}
$stmt->bind_param("iii", $ultimaBaia, $progresso, $idUsuario);

if ($stmt->execute()) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao gravar: ' . $stmt->error]);
}
$stmt->close();
$conn->close();
