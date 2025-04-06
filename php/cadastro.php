<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "CdoGo";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
  die("Erro na conexão: " . $conn->connect_error);
}

$usuario = $_POST['usuario'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (usuario, email, senha) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $usuario, $email, $senha);

if ($stmt->execute()) {
  echo "Cadastro realizado com sucesso!";
} else {
  echo "Erro ao cadastrar: " . $stmt->error;
}

$conn->close();
?>
