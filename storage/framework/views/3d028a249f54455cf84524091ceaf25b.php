

<?php $__env->startSection('content'); ?>
    <div class="content">
        <div class="container-fluid">
            <h1 class="h3 mb-2 text-gray-800"><?php echo e(__('Verification Page')); ?></h1>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text"><?php echo e(__('Verification')); ?></p>

                            <!-- Tombol rekam -->
                            <button id="recordBtn" class="btn btn-primary">Mulai Rekam</button>
                            <button id="stopBtn" class="btn btn-danger" disabled>Stop</button>

                            <p id="status" class="mt-2"></p>

                            <!-- Hasil verifikasi -->
                            <div id="result" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Elemen DOM
        const recordBtn = document.getElementById('recordBtn');
        const stopBtn = document.getElementById('stopBtn');
        const statusText = document.getElementById('status');
        const resultDiv = document.getElementById('result');

        let mediaRecorder;
        let audioChunks = [];
        let audioBlob;
        let audioUrl;
        let audio;

        // Fungsi untuk memulai rekaman
        recordBtn.addEventListener('click', () => {
            // Minta izin untuk menggunakan mikrofon
            navigator.mediaDevices.getUserMedia({
                    audio: true
                })
                .then((stream) => {
                    // Inisialisasi MediaRecorder
                    mediaRecorder = new MediaRecorder(stream);

                    mediaRecorder.ondataavailable = (event) => {
                        audioChunks.push(event.data); // Menyimpan potongan rekaman audio
                    };

                    mediaRecorder.onstop = () => {
                        // Setelah rekaman dihentikan, gabungkan potongan-potongan audio
                        audioBlob = new Blob(audioChunks, {
                            type: 'audio/wav'
                        });
                        audioUrl = URL.createObjectURL(audioBlob);
                        audio = new Audio(audioUrl);

                        // Tampilkan status rekaman selesai
                        statusText.textContent = 'Rekaman selesai. Siap untuk verifikasi!';

                        // Aktifkan tombol stop
                        stopBtn.disabled = true;

                        // Kirim file ke backend untuk verifikasi
                        sendToBackend(audioBlob);
                    };

                    // Mulai merekam
                    mediaRecorder.start();
                    statusText.textContent = 'Merekam...';

                    // Nonaktifkan tombol rekam dan aktifkan tombol stop
                    recordBtn.disabled = true;
                    stopBtn.disabled = false;
                })
                .catch((error) => {
                    console.error('Error accessing media devices.', error);
                    statusText.textContent = 'Gagal mengakses mikrofon!';
                });
        });

        // Fungsi untuk menghentikan rekaman
        stopBtn.addEventListener('click', () => {
            mediaRecorder.stop(); // Hentikan rekaman
            statusText.textContent = 'Rekaman dihentikan. Mengirim ke server...';
        });

        // Fungsi untuk mengirim rekaman ke server
        function sendToBackend(blob) {
            const formData = new FormData();
            formData.append('voice', blob, 'recorded_audio.wav'); // Nama file yang dikirimkan ke backend

            // Mengambil CSRF token dari meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('<?php echo e(route('verify.voice')); ?>', { // Pastikan Anda mengubah URL sesuai dengan route Laravel Anda
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken // Menambahkan CSRF token ke header
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    // Menampilkan hasil verifikasi dari backend
                    if (data.score && data.prediction) {
                        resultDiv.innerHTML = `
                <p>Score: ${data.score}</p>
                <p>Prediction: ${data.prediction}</p>
            `;
                    } else {
                        resultDiv.innerHTML = `<p>Verifikasi gagal.</p>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultDiv.innerHTML = `<p>Terjadi kesalahan saat mengirim data.</p>`;
                });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\voiceLaravel\resources\views/verification/index.blade.php ENDPATH**/ ?>