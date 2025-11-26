<?php
session_start();
require_once "conexao.php";

// verificar login antes de usar $_SESSION['id']
if (!isset($_SESSION['id'])) {
    header("Location: ../php/login.php");
    exit;
}

$idUsuario = (int) $_SESSION['id'];

// pegar linguagem atual do aluno (checa prepare)
$stmtL = $conn->prepare("SELECT idLinguagem, nomeUsuario, progresso, ultima_baia FROM usuario WHERE id = ?");
if (!$stmtL) {
    // mensagem de debug amigável
    die("Erro no prepare (buscar usuário): " . $conn->error);
}
$stmtL->bind_param("i", $idUsuario);
$stmtL->execute();
$resUser = $stmtL->get_result();
if (!$resUser) {
    die("Erro ao obter usuário: " . $conn->error);
}
$userRow = $resUser->fetch_assoc();
$stmtL->close();

$nomeUsuario = $userRow['nomeUsuario'] ?? 'Usuário';
$progressoPercent = intval($userRow['progresso'] ?? 0);
$ultimaBaia = intval($userRow['ultima_baia'] ?? 0);
$idLinguagem = $userRow['idLinguagem'] ? (int)$userRow['idLinguagem'] : null;

// se o aluno ainda não escolheu linguagem, mostra mensagem simples
if (!$idLinguagem) {
    $licoes = [];
} else {
    // pegar lições desta linguagem. selecionamos etapa para mapear intro/l1/l2
    $sql = "SELECT idLicao, idLinguagem, etapa, titulo, conteudo, imagem, video, ordem
            FROM licao
            WHERE idLinguagem = ?
            ORDER BY FIELD(etapa,'intro','l1','l2'), ordem ASC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erro no prepare (buscar lições): " . $conn->error);
    }
    $stmt->bind_param("i", $idLinguagem);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();

    // normalizar: queremos exatamente 3 posições: intro, l1, l2
    $map = ['intro' => null, 'l1' => null, 'l2' => null];
    foreach ($rows as $r) {
        $et = $r['etapa'] ?? '';
        if (in_array($et, ['intro','l1','l2'])) {
            $map[$et] = $r;
        }
    }
    // montar array em ordem: intro, l1, l2 (padrão null quando não existe)
    $licoes = [$map['intro'], $map['l1'], $map['l2']];
}

// fechar conexão se quiser (opcional)
// $conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cod&Go - Área do Aluno</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    /* (mantive seu CSS — não alterei o visual) */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { background: #1a0a3d; color: #eee; min-height: 100vh; display: flex; flex-direction: column; transition: background 0.6s ease; }
    header.header-principal { background: #3f2a6e; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 6px rgba(0,0,0,0.5); }
    .logo-titulo { font-weight: 700; font-size: 1.8rem; color: #c39eff; }
    .usuario-header { display: flex; align-items: center; gap: 1rem; font-weight: 600; }
    .avatar-header { width: 40px; height: 40px; border-radius: 50%; border: 2px solid #c39eff; cursor: pointer; }
    main.painel-container { flex: 1; display: flex; background: #2a1e57; overflow: hidden; transition: opacity 0.5s ease; }
    aside.menu-lateral { width: 220px; background: #3f2a6e; padding: 1.5rem 1rem; box-shadow: inset -2px 0 5px rgba(0,0,0,0.4); display: flex; flex-direction: column; align-items: center; gap: 2rem; }
    .logo-menu { width: 120px; filter: drop-shadow(0 0 5px #c39eff); }
    nav ul { list-style: none; width: 100%; text-align: center; } nav ul li { margin: 1rem 0; } nav ul li a { color: #d1b3ff; text-decoration: none; font-weight: 600; font-size: 1.1rem; display: block; padding: 0.5rem 0; border-radius: 8px; transition: background-color 0.3s; } nav ul li a:hover { background: #c39eff; color: #2a1e57; }

    section.conteudo-principal { flex: 1; padding: 2rem; display: flex; flex-direction: column; align-items: center; overflow: auto; }
    .painel-central { background: #3f2a6e; border-radius: 15px; padding: 1.5rem; width: 100%; max-width: 850px; box-shadow: 0 0 20px #c39eff88; position: relative; }

    .barra-progresso.gamificada { margin-bottom: 1.5rem; }
    .barra { background: #6b4da3; border-radius: 20px; height: 20px; overflow: hidden; position: relative; }
    .preenchimento { background: #c39eff; height: 100%; width: 0%; transition: width 0.7s ease-in-out; border-radius: 20px 0 0 20px; }
    .percentual { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #fff; font-weight: 700; font-size: 0.9rem; text-shadow: 0 0 5px #551a8b; }

    .popup { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #5a3f95cc; border-radius: 15px; padding: 2rem; color: #eee; box-shadow: 0 0 20px #c39effbb; width: 320px; text-align: center; display: none; z-index: 10; }
    .popup h3 { margin-bottom: 1rem; font-weight: 700; font-size: 1.4rem; text-shadow: 0 0 5px #fff; }
    .popup button { background: #c39eff; border: none; padding: 0.7rem 1.2rem; border-radius: 10px; font-weight: 600; color: #2a1e57; cursor: pointer; transition: background-color 0.3s; box-shadow: 0 0 10px #c39effaa; margin: 0.3rem; }
    .popup button:hover { background: #d8baff; }

    .tela-introducao { display: none; flex-direction: column; align-items: center; text-align: center; padding: 3rem; animation: fadeIn 0.6s ease forwards; }
    .tela-introducao h2 { font-size: 2rem; color: #d1b3ff; text-shadow: 0 0 10px #a77eff; margin-bottom: 1.2rem; }
    .tela-introducao p { max-width: 650px; line-height: 1.7; font-size: 1.05rem; color: #f1e6ff; margin-bottom: 2rem; }
    .btn-prosseguir { background: #c39eff; border: none; padding: 0.9rem 1.5rem; border-radius: 10px; font-weight: 600; color: #2a1e57; cursor: pointer; font-size: 1rem; transition: background 0.3s; box-shadow: 0 0 15px #c39effaa; }
    .btn-prosseguir:hover { background: #d8baff; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
  </style>
</head>
<body>
  <header class="header-principal">
    <h1 class="logo-titulo">Cod&Go</h1>
    <div class="usuario-header">
      <span class="nome-usuario">Olá, <?= htmlspecialchars($nomeUsuario) ?>!</span>
      <img src="https://i.pravatar.cc/40" alt="Avatar do usuário" class="avatar-header" id="avatar-btn" />
      <button class="btn-editar" id="btn-editar">Editar Perfil</button>
    </div>
  </header>

  <main class="painel-container" id="painelPrincipal">
    <aside class="menu-lateral">
      <img src="../img/Logo1.png" alt="Logo" class="logo-menu" />
      <nav>
        <ul>
          <li><a href="#">Início</a></li>
          <li><a href="form.php">Linguagens</a></li>
        </ul>
      </nav>
    </aside>

    <section class="conteudo-principal">
      <div class="painel-central" id="painelPrincipalConteudo">
        <h2>Painel Principal</h2>
        <div class="barra-progresso gamificada">
          <label>Progresso:</label>
          <div class="barra" id="barra">
            <div class="preenchimento" id="preenchimento"></div>
            <span class="percentual" id="percentual">0%</span>
          </div>
        </div>

        <!-- canvas do lago / peixe -->
        <canvas id="lago" width="800" height="300" style="display:block; margin: 1.5rem auto; border-radius:8px;"></canvas>

        <div class="popup" id="popup-licao">
          <h3 id="titulo-licao">Começar Introdução?</h3>
          <button id="btn-iniciar">Ir para a lição</button>
          <button id="btn-fechar">Fechar</button>
        </div>
      </div>

      <!-- Nova Tela Introdução -->
      <div class="painel-central tela-introducao" id="telaIntroducao">
        <h2>Introdução ao Curso</h2>
        <p>Bem-vindo à introdução do seu curso! Aqui você vai entender o que será ensinado, como funcionam as lições e o que esperar das próximas etapas.</p>
        <button class="btn-prosseguir">Avançar</button>
      </div>
<!-- PAINEL DA LIÇÃO REAL (DEVE FICAR DENTRO DA SECTION!) -->
<section class="conteudo-principal">
  <!-- PAINEL DA LIÇÃO REAL (AQUI AGORA ESTÁ NO LUGAR CERTO!) -->
  <div class="painel-central tela-licao" id="telaLicao" style="display:none; flex-direction:column; margin: 2rem auto; max-width:850px;">
      <h2 id="licaoTitulo"></h2>
      <p id="licaoConteudo" style="margin-top:1rem; line-height:1.6;"></p>
      <div id="licaoMidias"></div>
      <button class="btn-prosseguir" id="btnConcluirLicao" style="margin-top:2rem;">
         Concluir Lição
      </button>
  </div>
</section>

</main>
  <!-- PAINEL DA LIÇÃO REAL -->
<div class="painel-central tela-licao" id="telaLicao" style="display:none; flex-direction:column; margin: 2rem auto; max-width:850px;">
    <h2 id="licaoTitulo"></h2>
    <p id="licaoConteudo" style="margin-top:1rem; line-height:1.6;"></p>

    <div id="licaoMidias"></div>

    <button class="btn-prosseguir" id="btnConcluirLicao" style="margin-top:2rem;">
        Concluir Lição
    </button>
</div>

  <!-- Popup de Edição -->
  <div class="popup" id="popup-editar">
    <h3>Editar Perfil</h3>
    <input type="text" id="nome-usuario" placeholder="Nome do Usuário" />
    <input type="email" id="email-usuario" placeholder="E-mail" />
    <button id="btn-salvar">Salvar Alterações</button>
    <button id="btn-fechar-popup">Fechar</button>
  </div>

  <footer style="text-align:center; padding:1rem; background:#3f2a6e; color:#c39eff;">
    <p>Integrantes do TCC: João Pedro, Matheus Nogueira, Marcus Evaristo Rocha, Matheus Nunes</p>
  </footer>
<script>
/* dados vindos do PHP */
const licoes = <?= json_encode($licoes, JSON_UNESCAPED_UNICODE) ?>;
const NOME_USUARIO = <?= json_encode($nomeUsuario, JSON_UNESCAPED_UNICODE) ?>;
let progressoUsuario = <?= json_encode($progressoPercent) ?>;
let ultimaBaiaSalva = <?= json_encode($ultimaBaia) ?>;

/* DOM */
const nomeUsuarioSpan = document.querySelector('.nome-usuario');
const preenchimento = document.getElementById('preenchimento');
const percentual = document.getElementById('percentual');
const canvas = document.getElementById('lago');
const ctx = canvas.getContext('2d');
const width = canvas.width, height = canvas.height;
const painelPrincipal = document.getElementById('painelPrincipalConteudo');
const telaIntroducao = document.getElementById('telaIntroducao');
const btnAvancar = document.querySelector('.btn-prosseguir');

nomeUsuarioSpan.textContent = `Olá, ${NOME_USUARIO}!`;

function atualizarBarra(p){
    preenchimento.style.width = p + '%';
    percentual.textContent = p + '%';
}
atualizarBarra(progressoUsuario);

/* Baias: garantimos 3 baias (intro, l1, l2) */
const peixe = { x: 50, y: height/2, radius: 20, speed: 2, color: '#d3aaff' };

const baias = [
  { x:220, y:height/2+10, radius:40, nome:"Introdução", bloqueado:false, idLicao: licoes[0]?.idLicao ?? null },
  { x:450, y:height/2-30, radius:40, nome:"Lição 1",     bloqueado:true,  idLicao: licoes[1]?.idLicao ?? null },
  { x:680, y:height/2+20, radius:40, nome:"Lição 2",     bloqueado:true,  idLicao: licoes[2]?.idLicao ?? null }
];

let targetBaiaIndex = Math.max(0, Math.min(ultimaBaiaSalva, baias.length - 1));
for (let i = 0; i < baias.length; i++) baias[i].bloqueado = (i > ultimaBaiaSalva);
peixe.x = baias[targetBaiaIndex].x; peixe.y = baias[targetBaiaIndex].y;

/* desenho */
function desenharLago(){
  let grad = ctx.createLinearGradient(0,0,0,height);
  grad.addColorStop(0,'#4b2a6e'); grad.addColorStop(1,'#1f0e3a');
  ctx.fillStyle = grad; ctx.fillRect(0,0,width,height);
  for(let i=0;i<30;i++){ ctx.beginPath(); ctx.arc((i*27)%width+10,(i*47)%height+10,5,0,Math.PI*2); ctx.fillStyle='rgba(159,126,243,0.3)'; ctx.fill(); }
}
function desenharBaia(baia){
  ctx.beginPath();
  const grad = ctx.createRadialGradient(baia.x,baia.y,10,baia.x,baia.y,baia.radius);
  grad.addColorStop(0, baia.bloqueado?'#333':'#a983ffaa');
  grad.addColorStop(1, baia.bloqueado?'#111':'#5a3f95cc');
  ctx.fillStyle=grad; ctx.shadowColor=baia.bloqueado?'#000':'#c39effcc'; ctx.shadowBlur=10;
  ctx.arc(baia.x,baia.y,baia.radius,0,Math.PI*2); ctx.fill();
  ctx.font='16px Poppins'; ctx.fillStyle=baia.bloqueado?'#777':'#d1b3ff'; ctx.textAlign='center';
  ctx.fillText(baia.nome,baia.x,baia.y+baia.radius+20);
}
function desenharPeixe(){
  ctx.save(); ctx.translate(peixe.x,peixe.y); ctx.fillStyle=peixe.color;
  ctx.beginPath(); ctx.ellipse(0,0,peixe.radius*1.2,peixe.radius,0,0,Math.PI*2); ctx.fill();
  ctx.beginPath(); ctx.moveTo(-peixe.radius*1.2,0); ctx.lineTo(-peixe.radius*1.8,-peixe.radius*0.7); ctx.lineTo(-peixe.radius*1.8,peixe.radius*0.7); ctx.closePath(); ctx.fill();
  ctx.fillStyle='#fff'; ctx.beginPath(); ctx.arc(peixe.radius*0.5,-peixe.radius*0.2,peixe.radius*0.3,0,Math.PI*2); ctx.fill();
  ctx.fillStyle='#222'; ctx.beginPath(); ctx.arc(peixe.radius*0.55,-peixe.radius*0.2,peixe.radius*0.15,0,Math.PI*2); ctx.fill();
  ctx.restore();
}

/* loop anim */
let animating = false;
function animar(){
  ctx.clearRect(0,0,width,height);
  desenharLago(); baias.forEach(desenharBaia);
  if (animating) {
    let alvo = baias[targetBaiaIndex];
    let dx = alvo.x - peixe.x, dy = alvo.y - peixe.y;
    let dist = Math.sqrt(dx*dx+dy*dy);
    if (dist > peixe.speed) {
      peixe.x += (dx/dist)*peixe.speed;
      peixe.y += (dy/dist)*peixe.speed + Math.sin(peixe.x*0.1)*0.5;
      let partial = Math.round(((targetBaiaIndex + 1) / baias.length) * 100);
      atualizarBarra(partial);
    } else {
      peixe.x = alvo.x; peixe.y = alvo.y; animating = false;
      progressoUsuario = Math.round(((targetBaiaIndex + 1) / baias.length) * 100);
      atualizarBarra(progressoUsuario);
    }
  }
  desenharPeixe();
  requestAnimationFrame(animar);
}
animar();

/* clique nas baias -> abrir lição */
canvas.addEventListener('click', (e) => {
  const rect = canvas.getBoundingClientRect();
  const mx = e.clientX - rect.left, my = e.clientY - rect.top;
  baias.forEach((baia, i) => {
    const dx = mx - baia.x, dy = my - baia.y;
    if (Math.sqrt(dx*dx+dy*dy) < baia.radius && !baia.bloqueado) {
      const licaoID = baia.idLicao;
      const licao = licoes.find(l => l && (l.idLicao == licaoID));
      if (!licao) {
        alert("Nenhuma lição cadastrada para esta baía!");
        return;
      }
      const telaLicao = document.getElementById("telaLicao");
      const licaoTitulo = document.getElementById("licaoTitulo");
      const licaoConteudo = document.getElementById("licaoConteudo");
      const licaoMidias = document.getElementById("licaoMidias");

      painelPrincipal.style.display = 'none';
      telaIntroducao.style.display = 'none';
      telaLicao.style.display = 'flex';

      licaoTitulo.innerHTML = licao.titulo;
      licaoConteudo.innerHTML = licao.conteudo;
      licaoMidias.innerHTML = "";

      if (licao.imagem) licaoMidias.innerHTML += `<img src="${licao.imagem}" style="max-width:100%; margin-top:1rem;">`;
      if (licao.video) licaoMidias.innerHTML += `<video controls style="width:100%; margin-top:1rem;"><source src="${licao.video}"></video>`;

      document.getElementById("btnConcluirLicao").onclick = () => {
        telaLicao.style.display = 'none';
        painelPrincipal.style.display = 'flex';
        if (targetBaiaIndex < baias.length - 1) {
            baias[targetBaiaIndex + 1].bloqueado = false;
            targetBaiaIndex++;
            progressoUsuario = Math.round(((targetBaiaIndex + 1) / baias.length) * 100);
            atualizarBarra(progressoUsuario);
            // salva progresso no servidor
            fetch("salvarProgresso.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ ultimaBaia: targetBaiaIndex, progresso: progressoUsuario })
            });
            animating = true;
        }
      };
    }
  });
});

/* botão Avançar (tela introdução) */
btnAvancar.addEventListener('click', () => {
  telaIntroducao.style.display = 'none';
  painelPrincipal.style.display = 'flex';
  if (targetBaiaIndex < baias.length - 1) {
    baias[targetBaiaIndex + 1].bloqueado = false;
    targetBaiaIndex++;
    progressoUsuario = Math.round(((targetBaiaIndex + 1) / baias.length) * 100);
    atualizarBarra(progressoUsuario);
    fetch('salvarProgresso.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ultimaBaia: targetBaiaIndex, progresso: progressoUsuario })
    });
    animating = true;
  }
});

/* popup editar perfil (mantido) */
const btnEditar = document.getElementById('btn-editar');
const popupEditar = document.getElementById('popup-editar');
const btnFecharPopup = document.getElementById('btn-fechar-popup');
const btnSalvar = document.getElementById('btn-salvar');

btnEditar.addEventListener('click', () => popupEditar.style.display = 'block');
btnFecharPopup.addEventListener('click', () => popupEditar.style.display = 'none');
btnSalvar.addEventListener('click', () => {
  const nome = document.getElementById('nome-usuario').value;
  const email = document.getElementById('email-usuario').value;
  console.log('Nome:', nome, 'E-mail:', email);
  popupEditar.style.display = 'none';
});
</script>

</body>
</html>
