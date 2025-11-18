<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION['id']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: login.php");
    exit;
}

$idLicao = (int)($_GET['idLicao'] ?? 0);
if ($idLicao <= 0) {
    die("ID inválido");
}

// Buscar licao existente
$stmt = $conn->prepare("SELECT * FROM licao WHERE idLicao = ?");
$stmt->bind_param("i", $idLicao);
$stmt->execute();
$licao = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$licao) die("Lição não encontrada");

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $titulo = $_POST['titulo'];
    $conteudo = $_POST['conteudo'];
    $ordem = (int)$_POST['ordem'];
    $link = $_POST['link'];
    $quiz_json = $_POST['quiz_json'];

    // uploads
    $novos = [
        'imagem' => $licao['imagem'],
        'video'  => $licao['video'],
        'pdf'    => $licao['pdf']
    ];

    $uploadDir = __DIR__ . "/uploads/";

    foreach(['imagem','video','pdf'] as $c){
        if(!empty($_FILES[$c]['name'])){
            $nome = time()."_".basename($_FILES[$c]['name']);
            $dest = $uploadDir.$nome;
            if(move_uploaded_file($_FILES[$c]['tmp_name'], $dest)){
                $novos[$c] = "uploads/".$nome;
            }
        }
    }

    $stmt = $conn->prepare("
        UPDATE licao
        SET titulo=?, conteudo=?, imagem=?, video=?, pdf=?, link=?, quiz_json=?, ordem=?
        WHERE idLicao=?
    ");
    $stmt->bind_param(
        "sssssssii",
        $titulo,
        $conteudo,
        $novos['imagem'],
        $novos['video'],
        $novos['pdf'],
        $link,
        $quiz_json,
        $ordem,
        $idLicao
    );

    if ($stmt->execute()) {
        header("Location: painelProfessor.php");
        exit;
    } else {
        $erro = "Erro ao salvar: ".$stmt->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Editar Lição</title>
<style>
body{font-family:Poppins;background:linear-gradient(135deg,#3f2a6e,#8000ff);color:white;padding:30px}
.container{max-width:900px;margin:auto;background:rgba(255,255,255,0.06);padding:20px;border-radius:12px}
input,textarea{width:100%;padding:10px;border-radius:8px;border:none;margin-top:6px}
</style>
</head>
<body>
<div class="container">
<h2>Editar Lição</h2>

<?php if($erro): ?>
<p style="color:#ffb3b3;"><?= $erro ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">

<label>Título</label>
<input type="text" name="titulo" value="<?= htmlspecialchars($licao['titulo']) ?>">

<label>Conteúdo</label>
<textarea name="conteudo" rows="8"><?= htmlspecialchars($licao['conteudo']) ?></textarea>

<label>Ordem</label>
<input type="number" name="ordem" value="<?= $licao['ordem'] ?>">

<label>Imagem (atual: <?= $licao['imagem'] ?> )</label>
<input type="file" name="imagem">

<label>Vídeo (atual: <?= $licao['video'] ?> )</label>
<input type="file" name="video">

<label>PDF (atual: <?= $licao['pdf'] ?> )</label>
<input type="file" name="pdf">

<label>Link</label>
<input type="text" name="link" value="<?= $licao['link'] ?>">

<label>Quiz JSON</label>
<textarea name="quiz_json" rows="4"><?= htmlspecialchars($licao['quiz_json']) ?></textarea>

<button class="btn">Salvar</button>

</form>
</div>
</body>
</html>
