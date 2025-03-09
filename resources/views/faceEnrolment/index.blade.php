@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <h1 class="h3 mb-2 text-gray-800">Take Photos for Face Recognition</h1>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @elseif(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Tampilkan video live -->
            <div class="form-group">
                <div class="video-container text-center">
                    <video id="videoElement" width="640" height="480" autoplay></video>
                </div>
                <div class="text-center">
                    <button id="captureBtn" class="btn btn-success mt-3">Capture Face</button>
                </div>

                <!-- Hidden form to send captured image to backend -->
                <div class="video-container text-center">

                    <form id="captureForm" action="{{ route('registerFace') }}" method="POST" enctype="multipart/form-data"
                    style="display: none;">
                    @csrf
                    <input type="hidden" name="image" id="capturedImage">
                    <button type="submit" id="submitBtn" class="btn btn-success mt-3">Submit Face</button>
                </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Akses video dari kamera
        const video = document.getElementById('videoElement');
        const captureBtn = document.getElementById('captureBtn');
        const capturedImageInput = document.getElementById('capturedImage');
        const captureForm = document.getElementById('captureForm');
        const submitBtn = document.getElementById('submitBtn');

        // Gambar yang akan dikirim ke backend
        let capturedImage = null;

        // Akses kamera pengguna
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then((stream) => {
                video.srcObject = stream;
            })
            .catch((err) => {
                alert("Error accessing camera: " + err);
            });

        // Tangkap gambar dari video
        captureBtn.addEventListener('click', () => {
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Konversi gambar ke base64
            capturedImage = canvas.toDataURL('image/png');

            // Set image data ke form
            capturedImageInput.value = capturedImage;

            // Tampilkan form submit
            captureForm.style.display = 'block';
            captureBtn.style.display = 'none';
        });
    </script>
    <style>
         .video-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            transform: scaleX(-1);
        }
    </style>
@endsection
