<?php
session_start();

// Conexão com o banco
$servername = "localhost";
$username = "root";
$password = "";
$database = "CodGo";

$conn = new mysqli($servername, $username, $password, $database);

// Verifica conexão
if ($conn->connect_error) {
  die("Erro de conexão: " . $conn->connect_error);
}

// Recebe os dados
$usuario = $_POST['usuario'];
$senha = $_POST['senha'];

// Verifica se o usuário existe
$sql = "SELECT * FROM usuarios WHERE usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $user = $result->fetch_assoc();
  
  // Verifica a senha
  if (password_verify($senha, $user['senha'])) {
    $_SESSION['usuario'] = $user['usuario'];
    header("Location: ../html/dashboard.html"); // Redireciona para a página principal
    exit();
  } else {
    echo "Senha incorreta!";
  }
} else {
  echo "Usuário não encontrado!";
}

$conn->close();
?>
