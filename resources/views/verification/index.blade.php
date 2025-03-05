@extends('layouts.app')

@section('content')
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page Heading -->
            <h1 class="h3 mb-2 text-gray-800">{{ __('Verification Page') }}</h1>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">
                                {{ __('Verification ') }}
                            </p>

                            <!-- Tambahkan tombol untuk verifikasi suara -->
                            <button id="recordBtn" class="btn btn-primary">Mulai Rekam</button>
                            <button id="stopBtn" class="btn btn-danger" disabled>Stop</button>

                            <p id="status" class="mt-2"></p>

                            <!-- Hasil verifikasi -->
                            <div id="result" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->

    <script>
     let mediaRecorder;
let audioChunks = [];

document.getElementById("recordBtn").addEventListener("click", async () => {
    try {
        let stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        mediaRecorder.start();

        document.getElementById("recordBtn").disabled = true;
        document.getElementById("stopBtn").disabled = false;
        document.getElementById("status").innerText = "Merekam...";

        mediaRecorder.ondataavailable = event => {
            audioChunks.push(event.data);
        };
    } catch (error) {
        console.error("Gagal mengakses mikrofon:", error);
        alert("Gagal mengakses mikrofon. Pastikan izin mikrofon diberikan.");
    }
});

document.getElementById("stopBtn").addEventListener("click", async () => {
    mediaRecorder.stop();
    document.getElementById("status").innerText = "Merekam selesai.";

    mediaRecorder.onstop = async () => {
        let audioBlob = new Blob(audioChunks, { type: "audio/wav" });
        let formData = new FormData();
        formData.append("voice", audioBlob);

        document.getElementById("status").innerText = "Mengirim untuk verifikasi...";

        try {
            let response = await fetch("http://127.0.0.1:8000/api/verify-voice", {
                method: "POST",
                headers: {
                    "Accept": "application/json"
                },
                body: formData
            });

            let textResponse = await response.text(); // Cek isi response sebelum parsing
            console.log("Respon dari Laravel:", textResponse);

            try {
                let result = JSON.parse(textResponse);
                document.getElementById("result").innerHTML = `
                    <strong>Hasil Verifikasi:</strong> ${result.message} <br>
                    <strong>User ID:</strong> ${result.user_id || "Tidak ditemukan"} <br>
                    <strong>Score:</strong> ${result.score}
                `;
            } catch (jsonError) {
                console.error("Gagal parsing JSON:", jsonError);
                document.getElementById("result").innerHTML = "Terjadi kesalahan dalam respon server.";
            }
        } catch (error) {
            console.error("Gagal mengirim ke server:", error);
            document.getElementById("result").innerHTML = "Terjadi kesalahan saat menghubungi server.";
        }

        // Reset tombol
        document.getElementById("recordBtn").disabled = false;
        document.getElementById("stopBtn").disabled = true;
    };
});

    </script>
@endsection
