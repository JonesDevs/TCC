<?php
session_start();

// Confere se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../php/login.php");
    exit();
}

require_once "conexao.php";

if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

$idUsuario = $_SESSION['id'];

// Pega as linguagens escolhidas
$linguagens = $_POST['languages'] ?? [];

if (count($linguagens) === 0) {
    header("Location: ../php/form.php");
    exit();
}

// Limpa escolhas antigas
$stmtDel = $conn->prepare("DELETE FROM escolha WHERE id = ?");
$stmtDel->bind_param("i", $idUsuario);
$stmtDel->execute();
$stmtDel->close();

// Mapear nomes das linguagens para IDs da tabela 'linguagem'
$mapa = [
    "HTML" => 1,
    "CSS" => 2,
    "JavaScript" => 3,
    "PHP" => 4,
    "C++" => 5,
    "Outros" => 6
];

// Insere escolhas novas
$stmt = $conn->prepare("INSERT INTO escolha (id, idLinguagem) VALUES (?, ?)");
foreach ($linguagens as $linguagem) {
    $idLinguagem = $mapa[$linguagem] ?? 0;
    if ($idLinguagem > 0) {
        $stmt->bind_param("ii", $idUsuario, $idLinguagem);
        $stmt->execute();
    }
}

$stmt->close();
$conn->close();

// Redireciona para o painel principal (tipo Duolingo)
header("Location: ../php/principal.php");
exit();
?>

