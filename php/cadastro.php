<?php
require_once "conexao.php";

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = $_POST['usuario'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $codigoProfessor = $_POST['codigo_professor'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos!";
    } else {

        $codigoCorreto = "CODGO-PROF-2025";

        // DEFINIR TIPO
        if (!empty($codigoProfessor) && $codigoProfessor === $codigoCorreto) {
            $tipo_usuario = "professor";
        } else {
            $tipo_usuario = "aluno";
        }

        if (!isset($erro)) {

            // ---- 1) Inserir usuário primeiro ----
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                "INSERT INTO usuario (nomeUsuario, email, senha, tipo_usuario)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $nome, $email, $senhaHash, $tipo_usuario);

            if ($stmt->execute()) {

                // pegar id gerado
                $idProfessor = $stmt->insert_id;

                // ---- 2) SE for professor, salvar linguagens ----
                if ($tipo_usuario === "professor" && !empty($_POST['linguagens'])) {

                    $stmtLing = $conn->prepare("
                        INSERT INTO professor_linguagens (idProfessor, idLinguagem)
                        VALUES (?, ?)
                    ");

                    foreach ($_POST['linguagens'] as $idLing) {
                        $stmtLing->bind_param("ii", $idProfessor, $idLing);
                        $stmtLing->execute();
                    }
                }

                header("Location: login.php");
                exit;
            } else {
                $erro = "Erro ao cadastrar usuário.";
            }
        }
    }
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
    }
    .toggle-password {
      position: absolute;
      right: 0.5em;
      background: none;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .toggle-password img {
      width: 1.5em;
      height: 1.5em;
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

    <?php if (isset($erro)): ?>
      <div class="erro"><?= $erro ?></div>
    <?php endif; ?>

    <form action="" method="post">

      <label for="usuario">Usuário:</label>
      <input type="text" id="usuario" name="usuario" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>

      <label for="codigo_professor">Código de Professor (opcional):</label>
      <input type="text" name="codigo_professor" id="codigo_professor">

      <div id="box-linguagens" style="display:none; margin-top:15px;">
        <label><strong>Quais linguagens você ensina?</strong></label><br>

        <label><input type="checkbox" name="linguagens[]" value="1"> HTML</label><br>
        <label><input type="checkbox" name="linguagens[]" value="2"> CSS</label><br>
        <label><input type="checkbox" name="linguagens[]" value="3"> JavaScript</label><br>
        <label><input type="checkbox" name="linguagens[]" value="4"> PHP</label><br>
        <label><input type="checkbox" name="linguagens[]" value="5"> C++</label><br>
        <label><input type="checkbox" name="linguagens[]" value="6"> Outros</label><br>
      </div>

<script>
// Mostrar linguagens automaticamente quando o código correto for digitado
document.getElementById("codigo_professor").addEventListener("input", function () {
    const correto = "CODGO-PROF-2025";
    const box = document.getElementById("box-linguagens");

    if (this.value === correto) {
        box.style.display = "block";
    } else {
        box.style.display = "none";
    }
});
</script>

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
                   }">
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
