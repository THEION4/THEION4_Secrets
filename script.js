const canvas = document.getElementById('particles');
const ctx = canvas.getContext('2d');
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let particles = [];
for (let i = 0; i < 100; i++) {
  particles.push({
    x: Math.random() * canvas.width,
    y: Math.random() * canvas.height,
    baseRadius: Math.random() * 1.5 + 1,
    dx: (Math.random() - 0.5) * 0.5,
    dy: (Math.random() - 0.5) * 0.5
  });
}

function animateParticles() {
  analyser.getByteFrequencyData(dataArray);
  const bassEnergy = dataArray[5];

  ctx.clearRect(0, 0, canvas.width, canvas.height);
  particles.forEach(p => {
    const dynamicRadius = p.baseRadius + bassEnergy / 64;
    ctx.beginPath();
    ctx.arc(p.x, p.y, dynamicRadius, 0, Math.PI * 2);
    ctx.fillStyle = `rgba(0, 255, 224, ${0.4 + bassEnergy / 512})`;
    ctx.fill();

    p.x += p.dx * (2 + bassEnergy / 64);
    p.y += p.dy * (2 + bassEnergy / 64);

    if (p.x < 0 || p.x > canvas.width) p.dx *= -1;
    if (p.y < 0 || p.y > canvas.height) p.dy *= -1;
  });
  requestAnimationFrame(animateParticles);
}

window.addEventListener('resize', () => {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
  visualizerCanvas.width = window.innerWidth;
  visualizerCanvas.height = window.innerHeight;
});

const audio = document.getElementById('audioPlayer');
const visualizerCanvas = document.getElementById('visualizer');
const vCtx = visualizerCanvas.getContext('2d');
visualizerCanvas.width = window.innerWidth;
visualizerCanvas.height = window.innerHeight;

const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
const analyser = audioCtx.createAnalyser();
analyser.fftSize = 2048;
const bufferLength = analyser.frequencyBinCount;
const dataArray = new Uint8Array(bufferLength);

const source = audioCtx.createMediaElementSource(audio);
source.connect(analyser);
analyser.connect(audioCtx.destination);

const unlockMessage = document.getElementById('unlockMessage');

function drawVisualizer() {
  requestAnimationFrame(drawVisualizer);
  analyser.getByteFrequencyData(dataArray);

  const bass = dataArray[5];
  unlockMessage.classList.toggle('boosted', bass > 100);

  vCtx.clearRect(0, 0, visualizerCanvas.width, visualizerCanvas.height);
  let barWidth = (visualizerCanvas.width / bufferLength) * 2.5;
  let x = 0;
  for (let i = 0; i < bufferLength; i++) {
    let barHeight = dataArray[i];
    vCtx.fillStyle = 'rgb(0,255,' + (barHeight + 100) + ')';
    vCtx.fillRect(x, visualizerCanvas.height - barHeight, barWidth, barHeight);
    x += barWidth + 1;
  }
}

audio.addEventListener('play', () => {
  if (audioCtx.state === 'suspended') {
    audioCtx.resume();
  }
  drawVisualizer();
  animateParticles();
});
