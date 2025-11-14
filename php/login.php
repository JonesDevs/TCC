<?php
session_start();

// Dados do banco
$host = "sql100.infinityfree.com";
$user = "if0_39760133";
$password = "HuSONDu9CUsFc";
$database = "if0_39760133_codgotemp";

// Conectar
$conn = new mysqli($host, $user, $password, $database);

// Erro de conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pegar dados do formulário
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!$email || !$senha) {
        $erro = "Por favor, preencha e-mail e senha!";
    } else {
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
                    exit();
                } else {
                    // Novo usuário → formulário
                    header("Location: form.php");
                    exit();
                }
            } else {
                $erro = "Senha incorreta!";
            }
        } else {
            $erro = "Usuário não encontrado!";
        }

        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Cod&Go</title>
  <link rel="stylesheet" href="../css/login.css">
  <style>
    .password-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .password-wrapper input {
      flex: 1;
      padding-right: 2.5em;
      height: 2.5em;
      box-sizing: border-box;
    }

    .toggle-password {
      transform: translateY(-15%);
      position: absolute;
      right: 0.5em;
      background: none;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
    }

    .toggle-password img {
      width: 1.5em;
      height: 1.5em;
      object-fit: contain;
    }

    .erro {
      color: red;
      margin-top: 10px;
    }
  </style>
</head>
<body>

  <header>
    <h1>Cod&Go</h1>
  </header>

  <main>
    <div class="login-container">
      <h2>Entrar na sua conta</h2>

      <!-- Exibir mensagem de erro, se houver -->
      <?php if (isset($erro)): ?>
        <div class="erro"><?= $erro ?></div>
      <?php endif; ?>

      <form action="" method="POST">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="Seu e-mail" required>

        <label for="senha">Senha</label>
        <div class="password-wrapper">
          <input type="password" id="senha" name="senha" placeholder="Sua senha" required>
          <button type="button" class="toggle-password"
            onclick="const input = this.previousElementSibling;
              const img = this.querySelector('img');
              if(input.type === 'password'){
                input.type = 'text';
                img.src='../img/closeEYE.png';
              } else {
                input.type = 'password';
                img.src='../img/openEYE.png';
              }
            ">
            <img src="../img/openEYE.png" alt="Mostrar senha">
          </button>
        </div>

        <button type="submit" class="btn-primary">Entrar</button>
      </form>

      <p class="register-link">Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
    </div>
  </main>

  <footer>
    <p>Integrantes do TCC: João Pedro, Matheus Nogueira, Marcus Evaristo Rocha, Matheus Nunes</p>
  </footer>

</body>
</html>
