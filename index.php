<?php
$audioFile = "THEION4 SECRET'S.mp3";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>THEION4 SECRET'S</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      background: #0a0a0a;
      color: #fff;
      font-family: 'Orbitron', monospace;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      position: relative;
    }
    h1 {
      font-size: 2.5rem;
      letter-spacing: 2px;
      margin-bottom: 1rem;
      text-align: center;
      z-index: 2;
    }
    .message {
      font-size: 1rem;
      color: #00ffe0;
      letter-spacing: 4px;
      animation: pulse 2s infinite;
      z-index: 2;
      transition: transform 0.1s ease, text-shadow 0.1s ease;
    }
    .boosted {
      transform: scale(1.3);
      text-shadow: 0 0 12px #00ffe0;
    }
    @keyframes pulse {
      0%, 100% { opacity: 0.2; }
      50% { opacity: 1; }
    }
    .player {
      margin-top: 2rem;
      z-index: 2;
    }
    audio {
      outline: none;
      width: 300px;
      filter: drop-shadow(0 0 8px #00ffe0);
    }
    canvas {
      position: absolute;
      top: 0;
      left: 0;
    }
    #visualizer {
      opacity: 0.6;
      z-index: 0;
      width: 100%;
      height: 100%;
    }
    #particles {
      z-index: 0;
    }
  </style>
</head>
<body oncontextmenu="return false;">
  <h1>THEION4 SECRET'S</h1>
  <div class="message" id="unlockMessage">UNLOCK THE FREQUENCY</div>
  <div class="player">
    <audio id="audioPlayer" controls controlsList="nodownload">
      <source src="<?php echo $audioFile; ?>" type="audio/mpeg">
      Your browser does not support the audio element.
    </audio>
  </div>

  <canvas id="visualizer"></canvas>
  <canvas id="particles"></canvas>

  <script>
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
  </script>
</body>
</html>
