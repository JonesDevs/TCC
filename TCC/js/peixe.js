
  <script>
    const preenchimento = document.getElementById('preenchimento');
    const percentual = document.getElementById('percentual');
    const popup = document.getElementById('popup-licao');
    const tituloLicao = document.getElementById('titulo-licao');
    const btnIniciar = document.getElementById('btn-iniciar');
    const btnFechar = document.getElementById('btn-fechar');
    const canvas = document.getElementById('lago');
    const ctx = canvas.getContext('2d');
    const width = canvas.width, height = canvas.height;
    const painelPrincipal = document.getElementById('painelPrincipalConteudo');
    const telaIntroducao = document.getElementById('telaIntroducao');

    let progressoUsuario = 0;
    function atualizarBarra(p){ preenchimento.style.width = p+'%'; percentual.textContent = p+'%'; }

    const peixe = { x: 50, y: height/2, radius: 20, speed: 2, color: '#d3aaff' };
    const baias = [
      { x:220, y:height/2+10, radius:40, nome:"Introdução", bloqueado:false },
      { x:450, y:height/2-30, radius:40, nome:"Lição 1", bloqueado:true },
      { x:680, y:height/2+20, radius:40, nome:"Lição 2", bloqueado:true }
    ];

    let animating = true, targetBaiaIndex = 0;

    function desenharLago(){
      let grad = ctx.createLinearGradient(0,0,0,height);
      grad.addColorStop(0,'#4b2a6e');
      grad.addColorStop(1,'#1f0e3a');
      ctx.fillStyle = grad;
      ctx.fillRect(0,0,width,height);

      for(let i=0;i<30;i++){
        ctx.beginPath();
        ctx.arc((i*27)%width+10,(i*47)%height+10,5,0,Math.PI*2);
        ctx.fillStyle='rgba(159,126,243,0.3)';
        ctx.fill();
      }
    }

    function desenharBaia(baia){
      ctx.beginPath();
      const grad = ctx.createRadialGradient(baia.x,baia.y,10,baia.x,baia.y,baia.radius);
      grad.addColorStop(0, baia.bloqueado?'#333':'#a983ffaa');
      grad.addColorStop(1, baia.bloqueado?'#111':'#5a3f95cc');
      ctx.fillStyle=grad;
      ctx.shadowColor=baia.bloqueado?'#000':'#c39effcc';
      ctx.shadowBlur=10;
      ctx.arc(baia.x,baia.y,baia.radius,0,Math.PI*2);
      ctx.fill();

      ctx.font='16px Poppins';
      ctx.fillStyle=baia.bloqueado?'#777':'#d1b3ff';
      ctx.textAlign='center';
      ctx.fillText(baia.nome,baia.x,baia.y+baia.radius+20);
    }

    function desenharPeixe(){
      ctx.save();
      ctx.translate(peixe.x,peixe.y);
      ctx.fillStyle=peixe.color;
      ctx.beginPath();
      ctx.ellipse(0,0,peixe.radius*1.2,peixe.radius,0,0,Math.PI*2);
      ctx.fill();

      ctx.beginPath();
      ctx.moveTo(-peixe.radius*1.2,0);
      ctx.lineTo(-peixe.radius*1.8,-peixe.radius*0.7);
      ctx.lineTo(-peixe.radius*1.8,peixe.radius*0.7);
      ctx.closePath();
      ctx.fill();

      ctx.fillStyle='#fff';
      ctx.beginPath();
      ctx.arc(peixe.radius*0.5,-peixe.radius*0.2,peixe.radius*0.3,0,Math.PI*2);
      ctx.fill();

      ctx.fillStyle='#222';
      ctx.beginPath();
      ctx.arc(peixe.radius*0.55,-peixe.radius*0.2,peixe.radius*0.15,0,Math.PI*2);
      ctx.fill();

      ctx.restore();
    }

    function animar(){
      if(!animating) return;
      ctx.clearRect(0,0,width,height);
      desenharLago();
      baias.forEach(desenharBaia);

      let alvo = baias[targetBaiaIndex];
      let dx = alvo.x - peixe.x, dy = alvo.y - peixe.y, dist = Math.sqrt(dx*dx+dy*dy);
      if(dist>peixe.speed){
        peixe.x += (dx/dist)*peixe.speed;
        peixe.y += (dy/dist)*peixe.speed + Math.sin(peixe.x*0.1)*0.5;
      } else {
        peixe.x = alvo.x; peixe.y = alvo.y;
        animating = false;
      }
      desenharPeixe();
      requestAnimationFrame(animar);
    }

    animar();

    canvas.addEventListener('click',(e)=>{
      const rect = canvas.getBoundingClientRect();
      const mx = e.clientX - rect.left;
      const my = e.clientY - rect.top;
      baias.forEach((baia,i)=>{
        let dx = mx - baia.x, dy = my - baia.y;
        if(Math.sqrt(dx*dx+dy*dy) < baia.radius && !baia.bloqueado && i===targetBaiaIndex){
          tituloLicao.textContent = `Começar ${baia.nome}?`;
          popup.style.display='block';
        }
      });
    });

    btnIniciar.addEventListener('click',()=>{
      popup.style.display='none';
      if(targetBaiaIndex === 0){
        painelPrincipal.style.display='none';
        telaIntroducao.style.display='flex';
        return;
      }
      progressoUsuario = Math.min(100, ((targetBaiaIndex+1)/baias.length)*100);
      atualizarBarra(progressoUsuario);
      if(targetBaiaIndex < baias.length-1){
        baias[targetBaiaIndex+1].bloqueado = false;
        targetBaiaIndex++;
        animating = true;
        animar();
      } else {
        alert('Todas as lições concluídas!');
      }
    ;

    btnFechar.addEventListener('click',()=>{ popup.style.display='none'; });

    atualizarBarra(progressoUsuario);
  </script>