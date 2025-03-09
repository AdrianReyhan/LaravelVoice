

<?php $__env->startSection('content'); ?>
    <div class="content">
        <div class="container-fluid">
            <h1 class="h3 mb-2 text-gray-800"><?php echo e(__('Verifikasi Wajah')); ?></h1>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">
                                <?php echo e(__('Arahkan wajah ke kamera untuk verifikasi.')); ?>

                            </p>

                            <!-- Menampilkan Live Video -->
                            <div class="video-container text-center">
                                <video id="video" autoplay></video>
                                <canvas id="canvas" style="display: none;"></canvas>
                                <br>
                                <button class="btn btn-primary mt-3" onclick="captureImage()">Verify Face</button>
                            </div>

                            <!-- Menampilkan hasil verifikasi -->
                            <div id="result" class="mt-3"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mengakses kamera pengguna
        const video = document.getElementById('video');
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                console.error("Error accessing camera: ", err);
            });

        function captureImage() {
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');

            // Set ukuran canvas sesuai dengan video
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            // Gambar frame dari video ke canvas
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Konversi gambar ke base64
            const imageData = canvas.toDataURL('image/png');

            // Kirim data ke Laravel dengan AJAX
            fetch("<?php echo e(url('/verify-face')); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
                },
                body: JSON.stringify({ image: imageData })
            })
            .then(response => response.json())
            .then(data => {
                let resultDiv = document.getElementById("result");
                if (data.status === "success") {
                    resultDiv.innerHTML = `<div class="alert alert-success">
                        <strong>Identitas:</strong> ${data.identity}
                    </div>`;
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => console.error("Error:", error));
        }
    </script>

    <style>
        .video-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        video {
            width: 100%;
            max-width: 500px;
            border-radius: 10px;
            border: 2px solid #007bff;
            transform: scaleX(-1);
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\voiceLaravel\resources\views/verifwajah/index.blade.php ENDPATH**/ ?>