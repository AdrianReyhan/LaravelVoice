<?php $__env->startSection('content'); ?>
    <div class="content">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800"><?php echo e(__('Verifikasi Wajah & Suara')); ?></h1>

            <!-- STEP PROGRESS -->
            <div class="steps mb-4 d-flex justify-content-between">
                <div class="step text-center inactive" id="step-face">
                    <div class="step-circle">1</div>
                    <small>Verifikasi Wajah</small>
                </div>
                <div class="step text-center inactive" id="step-voice">
                    <div class="step-circle">2</div>
                    <small>Verifikasi Suara</small>
                </div>
                <div class="step text-center inactive" id="step-absen">
                    <div class="step-circle">3</div>
                    <small>Absen</small>
                </div>
            </div>

            <!-- VERIFIKASI SECTION -->
            <div class="row">
                <div class="col-lg-12">

                    <!-- Wajah -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <p class="card-text">Arahkan wajah ke kamera untuk verifikasi.</p>
                            <div class="video-container text-center">
                                <video id="video" autoplay></video>
                                <canvas id="canvas" style="display: none;"></canvas>
                                <br>
                                <button class="btn btn-primary mt-3" onclick="captureImage()">Verifikasi Wajah</button>
                            </div>
                        </div>
                    </div>

                    <!-- Suara -->
                    <div class="card mb-4" id="voiceCard" style="display: none;">
                        <div class="card-body">
                            <p class="card-text">Klik tombol di bawah untuk memulai rekaman suara.</p>
                            <button id="recordBtn" class="btn btn-primary">Mulai Rekam</button>
                            <button id="stopBtn" class="btn btn-danger" disabled>Stop</button>
                            <p id="status" class="mt-2"></p>
                        </div>
                    </div>

                    <!-- Hasil -->
                    <div class="card" id="resultCard" style="display: none;">
                        <div class="card-body">
                            <h5>Hasil Verifikasi</h5>
                            <div id="faceResult" class="mt-3"></div>
                            <div id="voiceResult" class="mt-3"></div>

                            <div id="finalStep" class="mt-4" style="display:none;">>
                                <button class="btn btn-success" onclick="submitAbsen()">Simpan Presensi</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        let faceVerified = false;
        let voiceVerified = false;

        // Kamera
        const video = document.getElementById('video');
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => console.error("Camera Error:", err));

        function captureImage() {
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = canvas.toDataURL('image/png');

            // Munculkan card hasil sebelum mulai proses
            const resultCard = document.getElementById('resultCard');
            const faceResultDiv = document.getElementById("faceResult");
            resultCard.style.display = 'block';
            faceResultDiv.innerHTML = "";

            fetch("<?php echo e(url('/verifikasi-wajah')); ?>", {
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
                    if (data.status === "success") {
                        faceResultDiv.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                    <strong>Berhasil!</strong> Wajah dikenali sebagai <strong>${data.identity}</strong> <br>
                    <strong>Similarity:</strong> <strong>${data.similarity}%</strong>.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
                        document.getElementById('step-face').classList.replace('inactive', 'active');
                        document.getElementById('voiceCard').style.display = 'block';
                        faceVerified = true;
                    } else {
                        faceResultDiv.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                    <strong>Gagal!</strong> ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
                        faceVerified = false;
                    }
                })
                .catch(error => {
                    faceResultDiv.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                <strong>Error!</strong> Terjadi kesalahan saat mengirim data.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
                    console.error("Face Error:", error);
                });
        }


        // Suara
        const recordBtn = document.getElementById('recordBtn');
        const stopBtn = document.getElementById('stopBtn');
        const statusText = document.getElementById('status');
        let mediaRecorder;
        let audioChunks = [];

        recordBtn.addEventListener('click', () => {
            navigator.mediaDevices.getUserMedia({
                    audio: true
                })
                .then(stream => {
                    mediaRecorder = new MediaRecorder(stream);
                    mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
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
                .catch(err => {
                    console.error("Mic Error:", err);
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

            fetch('<?php echo e(route('verify.voice')); ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>"
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const voiceResultDiv = document.getElementById("voiceResult");
                    if (data.score && data.prediction) {
                        voiceResultDiv.innerHTML = `
                        <div class="alert alert-success">
                            <p><strong>Score:</strong> ${data.score}</p>
                            <p><strong>Prediction:</strong> ${data.prediction}</p>
                        </div>`;
                        document.getElementById('step-voice').classList.replace('inactive', 'active');
                        document.getElementById('step-absen').classList.replace('inactive', 'active');
                        document.getElementById('resultCard').style.display = 'block';
                        voiceVerified = true;
                    } else {
                        voiceResultDiv.innerHTML = `<div class="alert alert-danger">Verifikasi suara gagal.</div>`;
                        voiceVerified = false;
                    }
                })
                .catch(err => {
                    console.error("Voice Error:", err);
                    document.getElementById("voiceResult").innerHTML =
                        `<div class="alert alert-danger">Terjadi kesalahan saat verifikasi suara.</div>`;
                    voiceVerified = false;
                });
        }

        function submitAbsen() {
            if (!faceVerified || !voiceVerified) {
                alert("Silakan selesaikan semua verifikasi terlebih dahulu.");
                return;
            }

            fetch('<?php echo e(url('/submit-absen')); ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>",
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Presensi berhasil!");
                        window.location.href = "<?php echo e(url('/home')); ?>";
                    } else {
                        alert("Gagal mencatat presensi.");
                    }
                })
                .catch(err => console.error("Submit Error:", err));
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

        .steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 30px;
        }

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
            left: 50%;
            right: -50%;
            height: 3px;
            background: #dee2e6;
            z-index: -1;
            transition: background-color 0.3s ease;
        }

        .step.active::after {
            background-color: #007bff;
        }

        .step:last-child::after {
            display: none;
        }

        .step-circle {
            width: 30px;
            height: 30px;
            line-height: 30px;
            border-radius: 50%;
            color: #fff;
            margin: 0 auto 5px;
            text-align: center;
            z-index: 1;
            position: relative;
            background: #dee2e6;
        }

        .step.active .step-circle {
            background: #007bff;
        }

        .step.inactive .step-circle {
            background: #dee2e6;
        }

        .step.active small {
            color: #007bff;
        }

        .step.inactive small {
            color: #6c757d;
        }

        .step small {
            display: block;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\voiceLaravel\resources\views/verifAll/index.blade.php ENDPATH**/ ?>