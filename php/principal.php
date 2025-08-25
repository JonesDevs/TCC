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
  <link rel="stylesheet" href="../css/principal.css" />
  <style>
    .barra-progresso { margin: 20px 0; }
    .barra { width: 100%; background: #f0e5ff; border-radius: 20px; height: 25px; overflow: hidden; }
    .preenchimento { height: 100%; background: #8000ff; width: <?= $progressoPercent ?>%; transition: width 1s ease; }
    #lago { display: block; margin: 30px auto; background: #cceeff; border-radius: 10px; }
    .header-principal { display: flex; justify-content: space-between; align-items: center; padding: 10px; background: #e0d4ff; }
    .avatar-header { width: 50px; height: 50px; border-radius: 50%; }
    .usuario-header { display: flex; align-items: center; gap: 10px; }
  </style>
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
  <!-- Menu lateral -->
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

  <!-- Conteúdo principal -->
  <section class="conteudo-principal">
    <div class="painel-central">
      <h2>Painel Principal</h2>

      <!-- Barra de progresso -->
      <div class="barra-progresso">
        <label>Progresso:</label>
        <div class="barra">
          <div class="preenchimento" id="preenchimento"></div>
        </div>
      </div>

      <!-- Canvas do peixe -->
      <canvas id="lago" width="800" height="300"></canvas>
    </div>
  </section>
</main>

<footer>
  <p>Integrantes do TCC: João Pedro, Matheus Nogueira, Marcus Evaristo Rocha, Matheus Nunes</p>
</footer>

<!-- Script do peixe -->
<script>
const canvas = document.getElementById("lago");
const ctx = canvas.getContext("2d");

let peixe = {
  largura: 80,
  altura: 80,
  amplitude: 25,
  frequencia: 0.02
};

let tempo = 0;

// Posição X baseada no progresso
let progressoUsuario = <?= $progressoPercent ?>;
let peixeX = (canvas.width * progressoUsuario) / 100;

// Carrega imagem do peixe
const imgPeixe = new Image();
imgPeixe.src = "../img/Logo1.png";

imgPeixe.onload = function () {
  atualizar();
};

function desenharLago() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.fillStyle = "#cceeff";
  ctx.fillRect(0, 0, canvas.width, canvas.height);
}

function desenharPeixe(x, y, angulo) {
  ctx.save();
  ctx.translate(x, y);
  ctx.rotate(angulo);
  ctx.drawImage(imgPeixe, -peixe.largura/2, -peixe.altura/2, peixe.largura, peixe.altura);
  ctx.restore();
}

function atualizar() {
  tempo += peixe.frequencia;

  let y = canvas.height / 2 + Math.sin(tempo * 2 * Math.PI) * peixe.amplitude;
  let inclinacao = Math.cos(tempo * 2 * Math.PI) * 0.3;

  desenharLago();
  desenharPeixe(peixeX, y, inclinacao);

  requestAnimationFrame(atualizar);
}
</script>

</body>
</html>
