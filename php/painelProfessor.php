<?php
session_start();
require_once "conexao.php";

// impedir acesso de alunos
if (!isset($_SESSION['id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'professor') {
    header("Location: login.php");
    exit;
}

$idProfessor = (int) $_SESSION['id'];

// 1) buscar linguagens que esse professor ensina
$sql = "SELECT l.idLinguagem, l.nomeLinguagem
        FROM professor_linguagens pl
        JOIN linguagem l ON l.idLinguagem = pl.idLinguagem
        WHERE pl.idProfessor = ?";

$linguagens = [];
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $idProfessor);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
        $linguagens = $res->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
} else {
    // debug mínimo — não vaza dados sensíveis, só aviso
    error_log("Erro prepare linguagens: " . $conn->error);
}

// 2) para cada linguagem, buscar lições (sem módulos — lições ligadas diretamente à linguagem)
$licoesPorLinguagem = [];
$sqlL = "SELECT idLicao, titulo, SUBSTRING(conteudo,1,200) AS resumo, ordem, dataCriacao
         FROM licao
         WHERE idLinguagem = ?
         ORDER BY COALESCE(ordem, idLicao) ASC";

foreach ($linguagens as $ling) {
    $arr = [];
    if ($stmtL = $conn->prepare($sqlL)) {
        $idLing = (int)$ling['idLinguagem'];
        $stmtL->bind_param("i", $idLing);
        $stmtL->execute();
        $resL = $stmtL->get_result();
        if ($resL) {
            $arr = $resL->fetch_all(MYSQLI_ASSOC);
        }
        $stmtL->close();
    } else {
        error_log("Erro prepare lições: " . $conn->error);
    }
    $licoesPorLinguagem[$ling['idLinguagem']] = $arr;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Painel do Professor</title>
  <style>
    html,body{height:100%}
    body{
      margin:0;font-family:'Poppins',sans-serif;
      background:linear-gradient(135deg,#3f2a6e,#8000ff);
      color:#fff;display:flex;flex-direction:column;
    }
    header{background:rgba(0,0,0,0.4);padding:20px;text-align:center;font-size:24px;font-weight:600;backdrop-filter:blur(6px);}
    .container{max-width:1100px;margin:40px auto;padding:20px;background:rgba(255,255,255,0.1);border-radius:15px;backdrop-filter:blur(6px);box-shadow:0 0 15px rgba(0,0,0,0.3)}
    .section{margin:20px 0;padding:18px;background:rgba(255,255,255,0.12);border-radius:12px}
    .btn{background:#8000ff;border:none;padding:10px 14px;border-radius:10px;color:#fff;font-weight:600;cursor:pointer;margin-right:8px}
    .btn:hover{background:#3f2a6e}
    .li-box{background:rgba(0,0,0,0.15);padding:10px;border-radius:8px;margin:10px 0}
    .meta{opacity:0.8;font-size:13px}
    footer{margin-top:auto;background:#3f2a6e;padding:16px;text-align:center}
  </style>
</head>
<body>
  <header>Painel do Professor</header>
  <div class="container">
    <h2>Minhas Linguagens</h2>

    <?php if (empty($linguagens)) : ?>
      <div class="section">
        <p>⚠ Você ainda não está vinculado a nenhuma linguagem. Para criar lições, associe-se a uma linguagem no seu perfil ou peça para o administrador vincular você.</p>
      </div>
    <?php else : ?>

      <?php foreach ($linguagens as $ling) : ?>
        <div class="section">
          <h3><?= htmlspecialchars($ling['nomeLinguagem'], ENT_QUOTES, 'UTF-8') ?></h3>

          <button class="btn" onclick="location.href='criarLicao.php?idLinguagem=<?= (int)$ling['idLinguagem'] ?>'">
            ➕ Criar Lição
          </button>

          <button class="btn" onclick="location.href='listarLicoes.php?idLinguagem=<?= (int)$ling['idLinguagem'] ?>'">
            📄 Gerenciar Lições
          </button>

          <h4 style="margin-top:14px">Lições existentes</h4>

          <?php
            $lista = $licoesPorLinguagem[$ling['idLinguagem']] ?? [];
            if (empty($lista)) :
          ?>
            <p style="opacity:0.85">Nenhuma lição criada ainda para essa linguagem.</p>
          <?php else: ?>
            <?php foreach ($lista as $li) : ?>
              <div class="li-box">
                <strong><?= htmlspecialchars($li['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                <div class="meta">ID: <?= (int)$li['idLicao'] ?> · Criação: <?= htmlspecialchars($li['dataCriacao'] ?? '') ?> · ordem: <?= htmlspecialchars($li['ordem'] ?? '') ?></div>
                <p style="margin-top:8px;opacity:0.95"><?= nl2br(htmlspecialchars($li['resumo'])) ?><?= (strlen($li['resumo'])>=200 ? '...' : '') ?></p>

                <div style="margin-top:8px">
                  <button class="btn" onclick="location.href='editarLicao.php?idLicao=<?= (int)$li['idLicao'] ?>'">✏️ Editar</button>
                  <button class="btn" onclick="if(confirm('Excluir lição?')) location.href='excluirLicao.php?idLicao=<?= (int)$li['idLicao'] ?>'">🗑 Excluir</button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div>
      <?php endforeach; ?>

    <?php endif; ?>

    <div class="section">
      <button class="btn" onclick="location.href='correcoes.php'">Ver Respostas dos Alunos</button>
    </div>

  </div>

  <footer>
    <p>Integrantes do TCC: João Pedro, Matheus Nogueira, Marcus Evaristo Rocha, Matheus Nunes</p>
  </footer>
</body>
</html>
