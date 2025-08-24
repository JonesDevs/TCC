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
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

if (!$email || !$senha) {
    die("Por favor, preencha e-mail e senha!");
}

// Buscar usuário pelo e-mail
$sql = "SELECT * FROM usuario WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Conferir senha
    if (password_verify($senha, $user['senha'])) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['nomeUsuario'] = $user['nomeUsuario'];

        // Verificar se já escolheu linguagens
        $sql2 = "SELECT * FROM escolha WHERE id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $user['id']);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2->num_rows > 0) {
            // Já escolheu → dashboard
            header("Location: ../html/dashboard.html");
        } else {
            // Novo usuário → formulário
            header("Location: ../html/form.html");
        }
        exit();
    } else {
        echo "Senha incorreta!";
    }
} else {
    echo "Usuário não encontrado!";
}

$stmt->close();
$conn->close();
?>
