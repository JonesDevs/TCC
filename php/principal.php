<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.html");
    exit();
}

$idUsuario = $_SESSION['id'];

// Conexão com banco
$conn = new mysqli("localhost", "root", "", "codgotemp");
if ($conn->connect_error) die("Erro de conexão: " . $conn->connect_error);

// Pega o nome do usuário  
$stmtUser = $conn->prepare("SELECT nomeUsuario FROM usuario WHERE id = ?");
$stmtUser->bind_param("i", $idUsuario);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$rowUser = $resultUser->fetch_assoc();
$usuario = $rowUser['nomeUsuario'];
$stmtUser->close();

// Quantas linguagens o usuário já escolheu
$stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM escolha WHERE id = ?");
$stmtCount->bind_param("i", $idUsuario);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$rowCount = $resultCount->fetch_assoc();
$linguagensEscolhidas = $rowCount['total'];
$stmtCount->close();

// Total de linguagens disponíveis (6)
$totalLinguagens = 6;
$progressoPercent = ($linguagensEscolhidas / $totalLinguagens) * 100;

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cod&Go - Painel</title>
  <link rel="stylesheet" href="principal.css" />
</head>
<body>

<header class="header-principal">
  <h1 class="logo-titulo">Cod&Go</h1>
  <div class="usuario-header">
    <span class="nome-usuario">Olá, <?= htmlspecialchars($usuario) ?>!</span>
    <img src="../img/avatar.png" alt="Avatar do usuário" class="avatar-header">
  </div>
</header>

<main class="painel-container">
  <aside class="menu-lateral">
    <img src="../img/Logo1.png" alt="Logo" class="logo-menu">
    <nav>
      <ul>
        <li><a href="#">Módulos</a></li>
        <li><a href="#">Linguagens</a></li>
        <li><a href="#">Classificação</a></li>
        <li><a href="#">Desafios</a></li>
      </ul>
    </nav>
  </aside>

  <section class="conteudo-principal">
    <div class="painel-central">
      <h2>Painel Principal</h2>

      <div class="barra-progresso">
        <label>Progresso:</label>
        <div class="barra">
          <div class="preenchimento" id="preenchimento"></div>
        </div>
      </div>

      <canvas id="lago" width="800" height="300"></canvas>
    </div>
  </section>
</main>

<footer>
  <p>Integrantes do TCC: João Pedro, Matheus Nogueira, Marcus Evaristo Rocha, Matheus Nunes</p>
</footer>

<!-- Passando a variável PHP para JS -->
<script>
  const progressoUsuario = <?= $progressoPercent ?>;
</script>
<script src="script-peixe.js"></script>

</body>
</html>
