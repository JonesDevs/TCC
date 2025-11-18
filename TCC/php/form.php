<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION['id'])) {
    header("Location: ../html/login.php");
    exit();
}

$idUsuario = $_SESSION['id'];

// Se formulário enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['linguagem'])) {
        die("Nenhuma linguagem foi selecionada.");
    }

    $idLinguagem = intval($_POST['linguagem']);

    // 1) Apaga escolha anterior
    $sqlDelete = "DELETE FROM escolha WHERE id = ?";
    $stmtDel = $conn->prepare($sqlDelete);

    if (!$stmtDel) {
        die("Erro no prepare DELETE: " . $conn->error);
    }

    $stmtDel->bind_param("i", $idUsuario);
    $stmtDel->execute();
    $stmtDel->close();

    // 2) Insere nova escolha
    $sqlInsert = "INSERT INTO escolha (id, idLinguagem) VALUES (?, ?)";
    $stmt = $conn->prepare($sqlInsert);

    if (!$stmt) {
        die("Erro no prepare INSERT: " . $conn->error);
    }

    $stmt->bind_param("ii", $idUsuario, $idLinguagem);
    $stmt->execute();
    $stmt->close();

    // 3) Atualiza usuário
    $sqlUpdate = "UPDATE usuario SET idLinguagem = ? WHERE id = ?";
    $stmtUp = $conn->prepare($sqlUpdate);

    if (!$stmtUp) {
        die("Erro no prepare UPDATE: " . $conn->error);
    }

    $stmtUp->bind_param("ii", $idLinguagem, $idUsuario);
    $stmtUp->execute();
    $stmtUp->close();

    // 4) Redirecionar
    header("Location: ../php/areaAluno.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Quais linguagens quer aprender?</title>
  <link rel="stylesheet" href="../css/form.css" />
</head>
<body>

  <header>
    <h1>Cod&Go</h1>
  </header>

  <main class="container">
    <section class="form-section">
      <img src="../img/Logo1.png" alt="Mascote" class="mascot"/>

      <h2>Quais linguagens quer aprender?</h2>

      <form id="source-form" action="" method="POST">
        <div class="options-grid">
          <label class="option">
    <input type="radio" name="linguagem" value="1" />
    <img src="../img/html.png" alt="HTML" class="language-img" />
    <span class="label-text">HTML</span>
</label>

<label class="option">
    <input type="radio" name="linguagem" value="2" />
    <img src="../img/css.png" alt="CSS" class="language-img" />
    <span class="label-text">CSS</span>
</label>

<label class="option">
    <input type="radio" name="linguagem" value="3" />
    <img src="../img/js.jpg" alt="JavaScript" class="language-img" />
    <span class="label-text">JavaScript</span>
</label>

<label class="option">
    <input type="radio" name="linguagem" value="4" />
    <img src="../img/php.png" alt="PHP" class="language-img" />
    <span class="label-text">PHP</span>
</label>

<label class="option">
    <input type="radio" name="linguagem" value="5" />
    <img src="../img/C++.png" alt="C++" class="language-img" />
    <span class="label-text">C++</span>
</label>

<label class="option">
    <input type="radio" name="linguagem" value="6" />
    <img src="../img/outros.webp" alt="Outros" class="language-img" />
    <span class="label-text">Outros</span>
</label>

        </div>

        <button type="submit" class="submit-btn">CONTINUAR</button>
      </form>
    </section>
  </main>

  <footer>
    <p>Integrantes do TCC: João Pedro, Matheus Nogueira e Matheus Nunes</p>
  </footer>

  <!-- Script para marcar visualmente os selecionados -->
<script>
    // Deixa o CARD clicável
    document.querySelectorAll(".option").forEach(option => {
        option.addEventListener("click", () => {
            const radio = option.querySelector("input[type='radio']");
            radio.checked = true;

            // remove seleção dos outros
            document.querySelectorAll(".option").forEach(op => op.classList.remove("selected"));

            // adiciona a classe de seleção
            option.classList.add("selected");
        });
    });

    // Validação antes de enviar
    document.getElementById("source-form").addEventListener("submit", function (e) {
        const selected = document.querySelector("input[name='linguagem']:checked");
        if (!selected) {
            e.preventDefault();
            alert("Por favor, selecione uma linguagem.");
        }
    });
</script>


</body>
</html>
