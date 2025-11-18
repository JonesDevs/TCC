<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION['id']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: login.php");
    exit;
}

$idLicao = (int)($_GET['idLicao'] ?? 0);
if ($idLicao <= 0) die("ID inválido");

$stmt = $conn->prepare("DELETE FROM licao WHERE idLicao = ?");
$stmt->bind_param("i", $idLicao);
$stmt->execute();

header("Location: painelProfessor.php");
exit;
