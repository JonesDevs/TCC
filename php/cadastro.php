<?php
session_start();

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

// Pegar dados do formulário
$nomeUsuario = $_POST['usuario'];
$email = $_POST['email'];
$senha = $_POST['senha'];

// Hash da senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Inserir no banco
$sql = "INSERT INTO usuario (nomeUsuario, email, senha) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nomeUsuario, $email, $senhaHash);

if ($stmt->execute()) {
    // Guardar ID do usuário na sessão
    $_SESSION['id'] = $stmt->insert_id;
    $_SESSION['nomeUsuario'] = $nomeUsuario;

    // Vai direto pro form escolher linguagens
    header("Location: ../html/form.html");
    exit();
} else {
    echo "Erro ao cadastrar: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
