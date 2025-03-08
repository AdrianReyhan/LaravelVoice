

<?php $__env->startSection('content'); ?>
    <div class="container">
        <h1 class="h3 mb-2 text-gray-800"><?php echo e(__('Rekam Suara')); ?></h1>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <p class="card-text">
                            <button id="recordButton">Mulai Rekam</button>
                            <button id="stopButton" disabled>Stop</button>
                        </p>

                        <p id="status">Tekan "Mulai Rekam" untuk merekam suara</p>
                        <audio id="audioPlayback" controls></audio>

                        <button id="uploadButton" disabled>Upload Suara</button>
                        <p id="message"></p>

                        <script>
                            let mediaRecorder = null;
                            let audioChunks = [];
                            let audioBlob = null;

                            document.getElementById("recordButton").addEventListener("click", async function() {
                                try {
                                    const stream = await navigator.mediaDevices.getUserMedia({
                                        audio: true
                                    });
                                    mediaRecorder = new MediaRecorder(stream, {
                                        mimeType: 'audio/webm'
                                    });

                                    audioChunks = []; // Reset buffer rekaman
                                    document.getElementById("status").innerText = "Merekam...";

                                    mediaRecorder.ondataavailable = event => {
                                        audioChunks.push(event.data);
                                    };

                                    mediaRecorder.onstop = async () => {
                                        const webmBlob = new Blob(audioChunks, {
                                            type: 'audio/webm'
                                        });
                                        audioBlob = await convertWebMToWav(webmBlob); // Konversi ke WAV
                                        const audioUrl = URL.createObjectURL(audioBlob);
                                        document.getElementById("audioPlayback").src = audioUrl;
                                        document.getElementById("uploadButton").disabled = false;
                                        document.getElementById("status").innerText =
                                            "Rekaman selesai. Klik 'Upload Suara' untuk mengunggah.";
                                    };

                                    mediaRecorder.start();
                                    document.getElementById("recordButton").disabled = true;
                                    document.getElementById("stopButton").disabled = false;

                                } catch (error) {
                                    console.error("Gagal mengakses mikrofon:", error);
                                    document.getElementById("status").innerText = "Gagal mengakses mikrofon. Periksa izin browser.";
                                }
                            });

                            document.getElementById("stopButton").addEventListener("click", function() {
                                if (mediaRecorder && mediaRecorder.state !== "inactive") {
                                    mediaRecorder.stop();
                                    document.getElementById("recordButton").disabled = false;
                                    document.getElementById("stopButton").disabled = true;
                                }
                            });

                            document.getElementById("uploadButton").addEventListener("click", function() {
                                if (!audioBlob) {
                                    alert("Belum ada rekaman yang tersedia untuk diunggah!");
                                    return;
                                }

                                const formData = new FormData();
                                formData.append("voice", audioBlob, "recorded_audio.wav");

                                fetch("<?php echo e(route('voice.store')); ?>", {
                                        method: "POST",
                                        body: formData,
                                        headers: {
                                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                                "content"),
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        document.getElementById("message").innerText = data.message || "Upload berhasil!";
                                    })
                                    .catch(error => {
                                        console.error("Terjadi kesalahan:", error);
                                        document.getElementById("message").innerText = "Gagal mengunggah suara.";
                                    });
                            });

                            async function convertWebMToWav(webmBlob) {
                                const audioContext = new AudioContext();
                                const arrayBuffer = await webmBlob.arrayBuffer();
                                const audioBuffer = await audioContext.decodeAudioData(arrayBuffer);

                                const offlineContext = new OfflineAudioContext(
                                    audioBuffer.numberOfChannels,
                                    audioBuffer.length,
                                    audioBuffer.sampleRate
                                );

                                const source = offlineContext.createBufferSource();
                                source.buffer = audioBuffer;
                                source.connect(offlineContext.destination);
                                source.start();

                                const renderedBuffer = await offlineContext.startRendering();
                                return audioBufferToWav(renderedBuffer);
                            }

                            function audioBufferToWav(buffer) {
                                const numOfChannels = buffer.numberOfChannels;
                                const sampleRate = buffer.sampleRate;
                                const format = 1; // PCM
                                const bitDepth = 16;

                                let interleaved;
                                let length = buffer.length * numOfChannels * 2 + 44;
                                let bufferArray = new ArrayBuffer(length);
                                let view = new DataView(bufferArray);

                                let writeString = function(view, offset, string) {
                                    for (let i = 0; i < string.length; i++) {
                                        view.setUint8(offset + i, string.charCodeAt(i));
                                    }
                                };

                                writeString(view, 0, 'RIFF');
                                view.setUint32(4, 36 + buffer.length * numOfChannels * 2, true);
                                writeString(view, 8, 'WAVE');
                                writeString(view, 12, 'fmt ');
                                view.setUint32(16, 16, true);
                                view.setUint16(20, format, true);
                                view.setUint16(22, numOfChannels, true);
                                view.setUint32(24, sampleRate, true);
                                view.setUint32(28, sampleRate * numOfChannels * 2, true);
                                view.setUint16(32, numOfChannels * 2, true);
                                view.setUint16(34, bitDepth, true);
                                writeString(view, 36, 'data');
                                view.setUint32(40, buffer.length * numOfChannels * 2, true);

                                let offset = 44;
                                for (let i = 0; i < buffer.length; i++) {
                                    for (let channel = 0; channel < numOfChannels; channel++) {
                                        let sample = buffer.getChannelData(channel)[i] * 32768;
                                        view.setInt16(offset, sample, true);
                                        offset += 2;
                                    }
                                }

                                return new Blob([view], {
                                    type: 'audio/wav'
                                });
                            }
                        </script>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\voiceLaravel\resources\views/voice/index.blade.php ENDPATH**/ ?>