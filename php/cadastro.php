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
    $nomeUsuario = $_POST['usuario'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Validar os dados (opcional)
    if (empty($nomeUsuario) || empty($email) || empty($senha)) {
        $erro = "Todos os campos são obrigatórios!";
    } else {
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

            // Redireciona para o formulário de escolha de linguagens (ou dashboard)
            header("Location: form.php");
            exit();
        } else {
            $erro = "Erro ao cadastrar: " . $conn->error;
        }

        $stmt->close();
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cadastro - Cod&Go</title>
  <link rel="stylesheet" href="../css/cadastro.css" />
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
      padding: -1;
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
      <h2>Crie sua conta</h2>

      <!-- Exibir mensagem de erro, se houver -->
      <?php if (isset($erro)): ?>
        <div class="erro"><?= $erro ?></div>
      <?php endif; ?>

      <form action="" method="post">
        
        <label for="usuario">Usuário:</label>
        <input type="text" id="usuario" name="usuario" required>
      
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
      
        <label for="senha">Senha:</label>
        <div class="password-wrapper">
          <input type="password" id="senha" name="senha" required>
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
      
        <button type="submit" class="btn-primary">Cadastrar</button>
      </form>
      
      <div class="register-link">
        Já tem uma conta? <a href="login.php">Fazer login</a>
      </div>
    </div>
  </main>
  
  <footer>
    <p>Desenvolvido por João Pedro, Matheus Nogueira, Marcus Evaristo Rocha e Matheus Nunes</p>
  </footer>

</body>
</html>
