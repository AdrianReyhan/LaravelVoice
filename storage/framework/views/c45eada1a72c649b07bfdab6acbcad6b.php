

<?php $__env->startSection('content'); ?>
    <div class="content">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800"><?php echo e(__('Verifikasi Wajah & Suara')); ?></h1>

            <div class="steps mb-4 d-flex justify-content-between">
                <div class="step text-center active" id="step-face">
                    <div class="step-circle">1</div>
                    <small>Verifikasi Wajah</small>
                </div>
                <div class="step text-center" id="step-voice">
                    <div class="step-circle">2</div>
                    <small>Verifikasi Suara</small>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <!-- Kartu Verifikasi Wajah -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <p class="card-text"><?php echo e(__('Arahkan wajah ke kamera untuk verifikasi.')); ?></p>

                            <div class="video-container text-center">
                                <video id="video" autoplay></video>
                                <canvas id="canvas" style="display: none;"></canvas>
                                <br>
                                <button class="btn btn-primary mt-3" onclick="captureImage()">Verifikasi Wajah</button>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Verifikasi Suara -->
                    <div class="card mb-4" id="voiceCard" style="display: none;">
                        <div class="card-body">
                            <p class="card-text"><?php echo e(__('Klik tombol di bawah untuk memulai rekaman suara.')); ?></p>

                            <button id="recordBtn" class="btn btn-primary">Mulai Rekam</button>
                            <button id="stopBtn" class="btn btn-danger" disabled>Stop</button>
                            <p id="status" class="mt-2"></p>
                        </div>
                    </div>

                    <!-- Hasil Verifikasi -->
                    <div class="card">
                        <div class="card-body">
                            <h5>Hasil Verifikasi</h5>
                            <div id="faceResult" class="mt-3"></div>
                            <div id="voiceResult" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ---------------- Kamera (Face) ---------------- //
        const video = document.getElementById('video');
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                console.error("Error accessing camera: ", err);
            });

        function captureImage() {
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = canvas.toDataURL('image/png');

            fetch("<?php echo e(url('/verify-face')); ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
                    },
                    body: JSON.stringify({
                        image: imageData
                    })
                })
                .then(response => response.json())
                .then(data => {
                    let faceResultDiv = document.getElementById("faceResult");
                    if (data.status === "success") {
                        faceResultDiv.innerHTML = `<div class="alert alert-success">
                            <strong>Identitas:</strong> ${data.identity}
                        </div>`;
                        moveToVoiceStep(); // Pindah ke step suara
                    } else {
                        faceResultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                })
                .catch(error => console.error("Error:", error));
        }

        function moveToVoiceStep() {
            document.getElementById('voiceCard').style.display = 'block';
            document.getElementById('step-voice').classList.add('active');
        }

        // ---------------- Mikrofon (Voice) ---------------- //
        const recordBtn = document.getElementById('recordBtn');
        const stopBtn = document.getElementById('stopBtn');
        const statusText = document.getElementById('status');
        const resultDiv = document.getElementById('result');
        let mediaRecorder;
        let audioChunks = [];

        recordBtn.addEventListener('click', () => {
            navigator.mediaDevices.getUserMedia({
                    audio: true
                })
                .then(stream => {
                    mediaRecorder = new MediaRecorder(stream);
                    mediaRecorder.ondataavailable = (event) => {
                        audioChunks.push(event.data);
                    };
                    mediaRecorder.onstop = () => {
                        const audioBlob = new Blob(audioChunks, {
                            type: 'audio/wav'
                        });
                        sendToBackend(audioBlob);
                        audioChunks = [];
                    };
                    mediaRecorder.start();
                    statusText.textContent = 'Merekam...';
                    recordBtn.disabled = true;
                    stopBtn.disabled = false;
                })
                .catch(error => {
                    console.error('Error accessing microphone:', error);
                    statusText.textContent = 'Gagal mengakses mikrofon!';
                });
        });

        stopBtn.addEventListener('click', () => {
            mediaRecorder.stop();
            statusText.textContent = 'Rekaman dihentikan.';
            stopBtn.disabled = true;
            recordBtn.disabled = false;
        });

        function sendToBackend(blob) {
            const formData = new FormData();
            formData.append('voice', blob, 'recorded_audio.wav');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('<?php echo e(route('verify.voice')); ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    const voiceResultDiv = document.getElementById("voiceResult");
                    if (data.score && data.prediction) {
                        voiceResultDiv.innerHTML = `
                            <div class="alert alert-success">
                                <p><strong>Score:</strong> ${data.score}</p>
                                <p><strong>Prediction:</strong> ${data.prediction}</p>
                            </div>
                        `;
                    } else {
                        voiceResultDiv.innerHTML = `<div class="alert alert-danger">Verifikasi suara gagal.</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    voiceResultDiv.innerHTML = `<p class="text-danger">Verifikasi suara gagal.</p>`;
                });
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

        /* Progress Step Styles */
        .steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 30px;
        }
/* 
        .steps::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            height: 3px;
            background: #dee2e6;
            z-index: 0;
        } */

        .step {
            position: relative;
            text-align: center;
            z-index: 1;
            flex: 1;
        }

        .step::after {
            content: '';
            position: absolute;
            top: 15px;
            right: -50%;
            width: 100%;
            height: 3px;
            background: #dee2e6;
            z-index: 0;
        }

        .step:last-child::after {
            display: none;
        }

        .step-circle {
            width: 30px;
            height: 30px;
            line-height: 30px;
            border-radius: 50%;
            background: #dee2e6;
            color: #fff;
            margin: 0 auto 5px;
            text-align: center;
            z-index: 1;
            position: relative;

        }

        .step.active .step-circle {
            background: #007bff;
        }

        .step small {
            display: block;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\voiceLaravel\resources\views/verifwajah/index.blade.php ENDPATH**/ ?>