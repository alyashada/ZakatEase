<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <style>
        #video-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            display: none;
        }
        #video-feed {
            width: 100%;
            height: auto;
        }
        #floating-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 24px;
            cursor: pointer;
            z-index: 9999;
        }
    </style>
</head>
<body>
    <button id="floating-icon">📷</button>

    <div id="video-container">
        <video id="video-feed"></video>
        <button id="close-btn">Close</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@2.0.1/dist/jsQR.js"></script>
    <script>
        const floatingIcon = document.getElementById('floating-icon');
        const videoContainer = document.getElementById('video-container');
        const video = document.getElementById('video-feed');
        const closeButton = document.getElementById('close-btn');

        floatingIcon.addEventListener('click', () => {
            videoContainer.style.display = 'block';
            startCamera();
        });

        closeButton.addEventListener('click', () => {
            videoContainer.style.display = 'none';
            stopCamera();
        });

        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(stream => {
                    video.srcObject = stream;
                    video.play();
                    scanQRCode();
                })
                .catch(err => {
                    console.error('Error accessing camera:', err);
                });
        }

        function stopCamera() {
            const stream = video.srcObject;
            const tracks = stream.getTracks();

            tracks.forEach(track => {
                track.stop();
            });

            video.srcObject = null;
        }

        function scanQRCode() {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, canvas.width, canvas.height);
            
            if (code) {
                alert('QR Code Detected: ' + code.data);
            } else {
                requestAnimationFrame(scanQRCode);
            }
        }
    </script>
</body>
</html>
