<?php
session_start();

// Confere se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../html/login.html");
    exit();
}

// Dados do banco
$host = "localhost";
$user = "root";
$password = "";
$database = "codgotemp";

// Conectar
$conn = new mysqli($host, $user, $password, $database);

// Erro de conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$idUsuario = $_SESSION['id'];

// Pega as linguagens escolhidas (checkbox)
if (isset($_POST['languages'])) {
    $linguagens = $_POST['languages']; // array das escolhas
} else {
    $linguagens = [];
}

// Limpa escolhas antigas (se já existirem)
$sqlDelete = "DELETE FROM escolha WHERE id = ?";
$stmtDel = $conn->prepare($sqlDelete);
$stmtDel->bind_param("i", $idUsuario);
$stmtDel->execute();
$stmtDel->close();

// Insere escolhas novas
$sqlInsert = "INSERT INTO escolha (id, linguagem) VALUES (?, ?)";
$stmt = $conn->prepare($sqlInsert);

foreach ($linguagens as $linguagem) {
    $stmt->bind_param("is", $idUsuario, $linguagem);
    $stmt->execute();
}

$stmt->close();
$conn->close();

// Vai pro dashboard depois de salvar
header("Location: ../html/dashboard.html");
exit();
?>
