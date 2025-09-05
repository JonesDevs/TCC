<?php
session_start();

// Confere se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../html/login.html");
    exit();
}

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

$idUsuario = $_SESSION['id'];

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pega as linguagens escolhidas (checkbox)
    if (isset($_POST['languages'])) {
        $linguagens = $_POST['languages']; // array das escolhas
    } else {
        $linguagens = [];
    }

    // Limpa escolhas antigas (se já existirem)
    $sqlDelete = "DELETE FROM escolha WHERE id = ?";
    $stmtDel = $conn->prepare($sqlDelete);
    $stmtDel->bind_param("i", $idUsuario);
    $stmtDel->execute();
    $stmtDel->close();

    // Mapeamento de linguagens para os IDs na tabela 'linguagem'
    $mapa = [
        "HTML" => 1,
        "CSS" => 2,
        "JavaScript" => 3,
        "PHP" => 4,
        "C++" => 5,
        "Outros" => 6
    ];

    // Insere escolhas novas
    $sqlInsert = "INSERT INTO escolha (id, idLinguagem) VALUES (?, ?)";
    $stmt = $conn->prepare($sqlInsert);

    foreach ($linguagens as $linguagem) {
        $idLinguagem = $mapa[$linguagem] ?? 0;
        if ($idLinguagem > 0) {
            $stmt->bind_param("ii", $idUsuario, $idLinguagem);
            $stmt->execute();
        }
    }

    $stmt->close();
    $conn->close();

    // Redireciona para o painel principal (dashboard ou principal.php)
    header("Location: principal.php");
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
            <input type="checkbox" name="languages[]" value="HTML" />
            <img src="../img/html.png" alt="HTML" class="language-img" />
            <span class="label-text">HTML</span>
          </label>

          <label class="option">
            <input type="checkbox" name="languages[]" value="CSS" />
            <img src="../img/css.png" alt="CSS" class="language-img" />
            <span class="label-text">CSS</span>
          </label>

          <label class="option">
            <input type="checkbox" name="languages[]" value="JavaScript" />
            <img src="../img/js.jpg" alt="JavaScript" class="language-img" />
            <span class="label-text">JavaScript</span>
          </label>

          <label class="option">
            <input type="checkbox" name="languages[]" value="PHP" />
            <img src="../img/php.png" alt="PHP" class="language-img" />
            <span class="label-text">PHP</span>
          </label>

          <label class="option">
            <input type="checkbox" name="languages[]" value="C++" />
            <img src="../img/C++.png" alt="C++" class="language-img" />
            <span class="label-text">C++</span>
          </label>

          <label class="option">
            <input type="checkbox" name="languages[]" value="Outros" />
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
    const options = document.querySelectorAll('.option input[type="checkbox"]');

    options.forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        const label = checkbox.parentElement;
        if (checkbox.checked) {
          label.classList.add('selected');
        } else {
          label.classList.remove('selected');
        }
      });
    });

    // Validação antes de enviar o formulário
    document.getElementById("source-form").addEventListener("submit", function (e) {
      const selectedOptions = document.querySelectorAll('input[name="languages[]"]:checked');

      if (selectedOptions.length === 0) {
        e.preventDefault(); // impede o envio se não escolher nada
        alert("Por favor, selecione pelo menos uma linguagem.");
      }
    });
  </script>

</body>
</html>
