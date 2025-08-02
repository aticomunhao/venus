<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Teste de Câmera</title>
    <style>
        video,
        canvas {
            display: block;
            margin: 10px auto;
            border: 2px solid #000;
        }
    </style>
</head>

<body>
    <h2 style="text-align:center;">Teste de Câmera</h2>
    <video id="video" autoplay playsinline style="display: none;"></video>

    <canvas id="canvasPreview"></canvas>

    <button onclick="tirarFoto()">Tirar Foto</button>

    <canvas id="canvas"></canvas> <!-- Imagem final -->

    {{-- Importa o JS corretamente --}}
    <script>
        const video = document.getElementById('video'); // oculto
        const canvas = document.getElementById('canvas'); // foto final
        const preview = document.getElementById('canvasPreview'); // visualização ao vivo
        const context = canvas.getContext('2d');
        const previewCtx = preview.getContext('2d');

        // Define resoluções
        video.width = 640;
        video.height = 480;
        canvas.width = 240;
        canvas.height = 320;
        preview.width = 240;
        preview.height = 320;

        // Inicia a câmera
        navigator.mediaDevices.getUserMedia({
                video: {
                    width: 640,
                    height: 480
                }
            })
            .then(stream => {
                video.srcObject = stream;
                video.play();
                requestAnimationFrame(atualizarPreview);
            })
            .catch(err => {
                console.error("Erro ao acessar a câmera:", err);
            });

        // Atualiza o canvasPreview em tempo real com corte 3x4
        function atualizarPreview() {
            // Define área de crop central (360x480 = 3:4)
            const cropWidth = 360;
            const cropHeight = 480;
            const cropX = (640 - cropWidth) / 2;
            const cropY = 0;

            // Desenha o corte no preview
            previewCtx.clearRect(0, 0, preview.width, preview.height);
            previewCtx.drawImage(
                video,
                cropX, cropY, cropWidth, cropHeight,
                0, 0, preview.width, preview.height
            );

            requestAnimationFrame(atualizarPreview); // loop contínuo
        }

        // Captura a foto cortada
        function tirarFoto() {
            const cropWidth = 360;
            const cropHeight = 480;
            const cropX = (640 - cropWidth) / 2;
            const cropY = 0;

            context.clearRect(0, 0, canvas.width, canvas.height);
            context.drawImage(
                video,
                cropX, cropY, cropWidth, cropHeight,
                0, 0, canvas.width, canvas.height
            );
        }
    </script>
</body>

</html>
