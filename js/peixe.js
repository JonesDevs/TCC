const canvas = document.getElementById("lago");
const ctx = canvas.getContext("2d");

let peixe = {
  x: 0,
  yBase: 150,
  largura: 80,
  altura: 80,
  velocidade: 2,
  amplitude: 25,
  frequencia: 0.02
};

let tempo = 0;

// Carrega a imagem do peixe
const imgPeixe = new Image();
imgPeixe.src = "../img/Logo1.png"; // ajuste o caminho se necessário

imgPeixe.onload = function () {
  atualizar();
};

function desenharLago() {
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
  peixe.x += peixe.velocidade;
  tempo += peixe.frequencia;

  let y = peixe.yBase + Math.sin(tempo * 2 * Math.PI) * peixe.amplitude;
  let inclinacao = Math.cos(tempo * 2 * Math.PI) * 0.3;

  if (peixe.x > canvas.width + peixe.largura) {
    peixe.x = -peixe.largura;
  }

  desenharLago();
  desenharPeixe(peixe.x, y, inclinacao);
  requestAnimationFrame(atualizar);
}
