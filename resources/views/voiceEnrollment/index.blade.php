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

                <!-- Form upload tersembunyi -->
                <form id="voiceForm" action="{{ route('registerVoice') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                    @csrf
                    <input type="file" name="voice" id="voiceInput" accept="audio/wav">
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
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });

                audioChunks = [];
                status.innerText = "Merekam suara...";
                message.innerText = "";
                audioPlayback.style.display = "none";

                mediaRecorder.ondataavailable = event => audioChunks.push(event.data);

                mediaRecorder.onstop = async () => {
                    const webmBlob = new Blob(audioChunks, { type: 'audio/webm' });
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

            const file = new File([audioBlob], "recorded_audio.wav", { type: "audio/wav" });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById("voiceInput").files = dataTransfer.files;

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
                for (let ch = 0; ch < numOfChannels; ch++) {
                    let sample = buffer.getChannelData(ch)[i] * 32767;
                    view.setInt16(offset, sample, true);
                    offset += 2;
                }
            }

            return new Blob([view], { type: 'audio/wav' });
        }
    </script>
@endsection
