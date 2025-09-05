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
imgPeixe.src = "img/Logo1.png";

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
    ctx.drawImage(imgPeixe, -peixe.largura / 2, -peixe.altura / 2, peixe.largura, peixe.altura);
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
