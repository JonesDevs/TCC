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

// Busca linguagens escolhidas pelo usuário
$stmtEscolhas = $conn->prepare("
    SELECT l.nomeLinguagem 
    FROM escolha e
    INNER JOIN linguagem l ON e.idLinguagem = l.idLinguagem
    WHERE e.id = ?
");
$stmtEscolhas->bind_param("i", $idUsuario);
$stmtEscolhas->execute();
$resultEscolhas = $stmtEscolhas->get_result();

$linguagens = [];
while ($row = $resultEscolhas->fetch_assoc()) {
    $linguagens[] = $row['nomeLinguagem'];
}
$stmtEscolhas->close();

$totalLinguagens = 6;
$linguagensEscolhidas = count($linguagens);
$progressoPercent = ($linguagensEscolhidas / $totalLinguagens) * 100;

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cod&Go - Painel</title>
  <link rel="stylesheet" href="../css/principal.css" />
</head>
<body>

<header class="header-principal">
  <h1 class="logo-titulo">Cod&Go</h1>
  <div class="usuario-header">
    <span class="nome-usuario">Olá, <?= htmlspecialchars($usuario) ?>!</span>
    <img src="../img/avatar.jpg" alt="Avatar do usuário" class="avatar-header" id="avatar-btn">
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

<script>
  const progressoUsuario = <?= $progressoPercent ?>;
</script>
<script src="../js/peixe.js"></script>

<script>
  const avatarBtn = document.getElementById("avatar-btn");
  const overlay = document.getElementById("usuario-overlay");
  const fecharMenu = document.getElementById("fechar-menu");

  avatarBtn.addEventListener("click", () => {
    overlay.classList.add("show");
  });

  fecharMenu.addEventListener("click", () => {
    overlay.classList.remove("show");
  });

  overlay.addEventListener("click", (e) => {
    if (e.target === overlay) overlay.classList.remove("show");
  });
</script>

</body>
</html>
