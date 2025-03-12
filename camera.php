<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <title>OCR | Camera</title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container" style="margin-top: 5em;">
        <div class="row">

            <div class="col-md-12">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation"><a href="index.php">Upload Image</a></li>
                    <li role="presentation" class="active"><a href="camera.php">Use Camera</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active">
                        <h2 class="text-left">Use Camera</h2>

                        <div class="col-md-4">
                            <video class="mb-2" width="320" height="240" id="video" autoplay></video>
                            <button id="capture" class="btn btn-md btn-primary">Capture</button>
                        </div>

                        <div class="col-md-4">
                            <canvas id="canvas" style="display: none;"></canvas>
                            <img width="320" id="snapshot" />
                            <button id="upload" style="margin-top:.5rem;" class="btn btn-md btn-primary">Upload</button>
                        </div>

                        <div class="col-md-4">
                            <div id="outputText"></div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const snapshot = document.getElementById('snapshot');
        const captureBtn = document.getElementById('capture');
        const uploadBtn = document.getElementById('upload');
        let imageData = ''; // Store image data for upload

        // Access Webcam
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(error => console.error("Webcam access error:", error));

        // Capture Snapshot
        captureBtn.addEventListener('click', function() {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            imageData = canvas.toDataURL('image/png'); // Convert to Base64
            snapshot.src = imageData;

        });

        // Upload Snapshot
        uploadBtn.addEventListener('click', function() {
            if (!imageData) {
                alert("No image captured!");
                return;
            }

            fetch('save_snapshot.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        image: imageData
                    }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('outputText').innerHTML = '<p>' + data.extractedText + '</p>';
                    } else {
                        alert("Upload failed: " + data.error);
                    }
                });
        });
    </script>
</body>

</html>