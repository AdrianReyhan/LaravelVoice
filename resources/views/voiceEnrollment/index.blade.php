    @extends('layouts.app')

    @section('content')
        <div class="container">
            <h1 class="h3 mb-4 text-gray-800">{{ __('Pendaftaran Suara') }}</h1>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="mb-3">Tekan tombol "Mulai Rekam" untuk merekam suara Anda.</p>
                    <p class="mb-3">Contoh: Saya merekam suara ini untuk mendaftar ke sistem pengenalan suara.</p>

                    <div class="mb-3">
                        <button id="recordButton" class="btn btn-primary me-2">Mulai Rekam</button>
                        <button id="stopButton" class="btn btn-warning me-2" disabled>Stop</button>
                        <button id="uploadButton" class="btn btn-success" disabled>Upload Suara</button>
                    </div>

                    <p id="status" class="text-muted">Menunggu tindakan pengguna...</p>
                    <audio id="audioPlayback" controls class="my-3 w-100" style="display:none;"></audio>
                    <div id="message" class="mt-3 text-success fw-semibold"></div>

                    <!-- Form upload tersembunyi untuk embedding -->
                    <form id="voiceForm" action="{{ route('registerVoice') }}" method="POST" enctype="multipart/form-data"
                        style="display: none;">
                        @csrf
                        <input type="hidden" name="embedding" id="embeddingInput">
                        <input type="hidden" id="userId" value="{{ Auth::id() }}">

                        <!-- Add the file input for the audio file -->
                        <input type="file" name="voice" id="voice" accept="audio/*" required />
                    </form>
                </div>
            </div>
        </div>

        <script>
            let mediaRecorder = null;
            let audioChunks = [];
            let audioBlob = null;

            const recordButton = document.getElementById("recordButton");
            const stopButton = document.getElementById("stopButton");
            const uploadButton = document.getElementById("uploadButton");
            const audioPlayback = document.getElementById("audioPlayback");
            const status = document.getElementById("status");
            const message = document.getElementById("message");

            recordButton.addEventListener("click", async () => {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        audio: true
                    });
                    mediaRecorder = new MediaRecorder(stream, {
                        mimeType: 'audio/webm'
                    });

                    audioChunks = [];
                    status.innerText = "Merekam suara...";
                    message.innerText = "";
                    audioPlayback.style.display = "none";

                    mediaRecorder.ondataavailable = event => audioChunks.push(event.data);

                    mediaRecorder.onstop = async () => {
                        const webmBlob = new Blob(audioChunks, {
                            type: 'audio/webm'
                        });
                        audioBlob = await convertWebMToWav(webmBlob);

                        const audioUrl = URL.createObjectURL(audioBlob);
                        audioPlayback.src = audioUrl;
                        audioPlayback.style.display = "block";

                        status.innerText = "Rekaman selesai. Klik 'Upload Suara' untuk mengirim.";
                        uploadButton.disabled = false;
                    };

                    mediaRecorder.start();
                    recordButton.disabled = true;
                    stopButton.disabled = false;

                } catch (err) {
                    console.error("Microphone error:", err);
                    status.innerText = "Tidak dapat mengakses mikrofon. Periksa izin browser.";
                }
            });

            stopButton.addEventListener("click", () => {
                if (mediaRecorder && mediaRecorder.state !== "inactive") {
                    mediaRecorder.stop();
                    recordButton.disabled = false;
                    stopButton.disabled = true;
                }
            });

            uploadButton.addEventListener("click", () => {
                if (!audioBlob) {
                    alert("Belum ada rekaman untuk diunggah!");
                    return;
                }

                // Convert audio Blob to wav file
                const file = new File([audioBlob], "recorded_audio.wav", {
                    type: "audio/wav"
                });

                // Set the value of the hidden file input
                document.getElementById('voice').files = new DataTransfer().files; // Clear existing files

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file); // Add the audio file
                document.getElementById('voice').files = dataTransfer.files;

                // Optionally set the embedding value (if needed)
                document.getElementById("embeddingInput").value =
                "your_embedding_value"; // Set the embedding if necessary

                // Submit the form
                document.getElementById("voiceForm").submit();
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
                const bitDepth = 16;
                const format = 1;

                const bufferLength = buffer.length * numOfChannels * 2 + 44;
                const arrayBuffer = new ArrayBuffer(bufferLength);
                const view = new DataView(arrayBuffer);

                let writeString = (view, offset, str) => {
                    for (let i = 0; i < str.length; i++) {
                        view.setUint8(offset + i, str.charCodeAt(i));
                    }
                };

                writeString(view, 0, 'RIFF');
                view.setUint32(4, 36 + buffer.length * numOfChannels * 2, true);
                writeString(view, 8, 'WAVE');
                writeString(view, 12, 'fmt '); // Add fmt chunk
                view.setUint32(16, 16, true); // Subchunk1Size (16 for PCM)
                view.setUint16(20, format, true); // AudioFormat (1 for PCM)
                view.setUint16(22, numOfChannels, true); // NumChannels
                view.setUint32(24, sampleRate, true); // SampleRate
                view.setUint32(28, sampleRate * numOfChannels * 2, true); // ByteRate
                view.setUint16(32, numOfChannels * 2, true); // BlockAlign
                view.setUint16(34, bitDepth, true); // BitsPerSample
                writeString(view, 36, 'data');
                view.setUint32(40, buffer.length * numOfChannels * 2, true); // Subchunk2Size

                let offset = 44;
                for (let i = 0; i < buffer.length; i++) {
                    for (let ch = 0; ch < numOfChannels; ch++) {
                        let sample = buffer.getChannelData(ch)[i] * 32767;
                        view.setInt16(offset, sample, true);
                        offset += 2;
                    }
                }

                return new Blob([view], {
                    type: 'audio/wav'
                });
            }

            async function uploadAudioAndGetEmbedding(audioBlob) {
                // Convert audio blob to wav file
                const file = new File([audioBlob], "recorded_audio.wav", {
                    type: "audio/wav"
                });
                const userId = document.getElementById("userId").value;
                // FormData to send audio file to backend
                const formData = new FormData();
                formData.append("audio_file", file);
                formData.append("user_id", userId);
                const response = await fetch("http://127.0.0.1:5000/enrol_voice", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });


                const result = await response.json();

                if (result.status === 'success') {
                    document.getElementById("embeddingInput").value = result.embedding;
                    document.getElementById("voiceForm").submit();
                    message.innerText = "Voice successfully enrolled!";
                } else {
                    message.innerText = "Failed to enroll voice. Please try again.";
                }
            }
        </script>
    @endsection
