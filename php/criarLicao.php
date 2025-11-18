<?php
session_start();
require_once "conexao.php";

// Apenas professor
if (!isset($_SESSION['id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'professor') {
    header("Location: login.php");
    exit;
}

$idProfessor = (int)$_SESSION['id'];

// --- Buscar linguagens que o professor ensina ---
$stmt = $conn->prepare("
    SELECT l.idLinguagem, l.nomeLinguagem
    FROM professor_linguagens pl
    JOIN linguagem l ON l.idLinguagem = pl.idLinguagem
    WHERE pl.idProfessor = ?
");
if (!$stmt) {
    die("Erro prepare linguagens: " . $conn->error);
}
$stmt->bind_param("i", $idProfessor);
$stmt->execute();
$linguagens = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recebe campos
    $idLinguagem = (int)($_POST['idLinguagem'] ?? 0);
    $etapa       = $_POST['etapa'] ?? '';
    $titulo      = trim($_POST['titulo'] ?? '');
    $conteudo    = $_POST['conteudo'] ?? '';
    $ordem       = (int)($_POST['ordem'] ?? 1);
    // recebemos mas não vamos inserir pdf/link/quiz no DB atual (sua tabela não tem)
    $link        = $_POST['link'] ?? null;
    $quiz_json   = $_POST['quiz_json'] ?? null;

    // validações mínimas
    if ($idLinguagem <= 0) {
        $erro = "Escolha a linguagem.";
    } elseif ($etapa === '' ) {
        $erro = "Escolha a etapa (Introdução / Lição 1 / Lição 2).";
    } elseif ($titulo === '') {
        $erro = "Preencha o título.";
    }

    // --- Upload de arquivos ---
    $uploads = ['imagem' => null, 'video' => null, 'pdf' => null];
    $uploadDir = __DIR__ . "/uploads/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    foreach (['imagem','video','pdf'] as $campo) {
        if (!empty($_FILES[$campo]['name']) && isset($_FILES[$campo]['tmp_name']) && is_uploaded_file($_FILES[$campo]['tmp_name'])) {
            $nome = time() . "_" . preg_replace('/[^a-zA-Z0-9\._-]/', '_', basename($_FILES[$campo]['name']));
            $dest = $uploadDir . $nome;

            if (move_uploaded_file($_FILES[$campo]['tmp_name'], $dest)) {
                $uploads[$campo] = "uploads/" . $nome;
            } else {
                // não falha o processo por upload; apenas não salva o arquivo
                // $erro = "Falha ao enviar arquivo: {$campo}";
            }
        }
    }

    // se não houve erro, insere
    if ($erro === '') {

        // --- Inserir no banco ---
        $sql = "INSERT INTO licao
        (idLinguagem, etapa, titulo, conteudo, dataCriacao, imagem, video, ordem)
        VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)";

        $stmtIns = $conn->prepare($sql);
        if (!$stmtIns) {
            $erro = "Erro SQL: " . $conn->error;
        } else {
            // bind: i s s s s s i i => mas usamos "isssssii"
            // parâmetros: idLinguagem (int), etapa (string), titulo (string), conteudo (string),
            //            imagem (string|null), video (string|null), ordem (int)
            $imgParam = $uploads['imagem'] ?? null;
            $vidParam = $uploads['video'] ?? null;

          $stmtIns->bind_param(
    "isssssi",
    $idLinguagem,
    $etapa,
    $titulo,
    $conteudo,
    $imgParam,
    $vidParam,
    $ordem
);


            if ($stmtIns->execute()) {
                $stmtIns->close();
                header("Location: painelProfessor.php");
                exit;
            } else {
                $erro = "Erro ao salvar: " . $stmtIns->error;
                $stmtIns->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8" />
<title>Criar Lição</title>
<style>
body{font-family:Poppins;background:linear-gradient(135deg,#3f2a6e,#8000ff);color:#fff;padding:30px}
.container{max-width:900px;margin:0 auto;background:rgba(255,255,255,0.06);padding:20px;border-radius:12px}
input,select,textarea{width:100%;padding:10px;border-radius:8px;border:none;margin-top:6px}
.btn{background:#8000ff;border:none;padding:10px 16px;border-radius:8px;color:#fff;margin-top:12px;cursor:pointer}
.alert { color:#ff8080; margin-bottom:12px; }
</style>
</head>
<body>
<div class="container">
<h2>Criar Lição</h2>

<?php if($erro): ?>
<p class="alert"><?= htmlspecialchars($erro) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">

<label>Linguagem</label>
<select name="idLinguagem" required>
    <option value="">-- Escolha --</option>
    <?php foreach($linguagens as $l): ?>
        <option value="<?= (int)$l['idLinguagem'] ?>" <?= (isset($idLinguagem) && $idLinguagem==(int)$l['idLinguagem']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($l['nomeLinguagem']) ?>
        </option>
    <?php endforeach; ?>
</select>

<label>Etapa</label>
<select name="etapa" required>
    <option value="">-- Escolha --</option>
    <option value="intro" <?= (isset($etapa) && $etapa==='intro') ? 'selected' : '' ?>>Introdução</option>
    <option value="l1" <?= (isset($etapa) && $etapa==='l1') ? 'selected' : '' ?>>Lição 1</option>
    <option value="l2" <?= (isset($etapa) && $etapa==='l2') ? 'selected' : '' ?>>Lição 2</option>
</select>

<label>Título</label>
<input type="text" name="titulo" required value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">

<label>Conteúdo</label>
<textarea name="conteudo" rows="8"><?= htmlspecialchars($_POST['conteudo'] ?? '') ?></textarea>

<label>Ordem</label>
<input type="number" name="ordem" value="<?= htmlspecialchars($_POST['ordem'] ?? 1) ?>">

<label>Imagem</label>
<input type="file" name="imagem" accept="image/*">

<label>Vídeo</label>
<input type="file" name="video" accept="video/*">

<!-- o PDF será enviado para pasta se o professor anexar, porém não existe coluna no DB atualmente -->
<label>PDF (anexado, não salvo no DB atual)</label>
<input type="file" name="pdf" accept="application/pdf">

<label>Link externo (opcional)</label>
<input type="text" name="link" value="<?= htmlspecialchars($_POST['link'] ?? '') ?>">

<label>Quiz JSON (opcional)</label>
<textarea name="quiz_json" rows="4"><?= htmlspecialchars($_POST['quiz_json'] ?? '') ?></textarea>

<button class="btn">Salvar</button>
</form>
</div>
</body>
</html>
