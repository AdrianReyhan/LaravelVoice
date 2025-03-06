<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class VoiceVerificationController extends Controller
{
    public function verifyVoice(Request $request)
    {
        // Validasi file yang di-upload
        $request->validate([
            'voice' => 'required|file|mimes:wav,mp3|max:10240', // Hanya file WAV atau MP3 hingga 10MB
        ]);

        // Ambil file yang di-upload
        $file = $request->file('voice');
        $filePath = $file->getRealPath();  // Path sementara file yang di-upload

        // Tentukan path file referensi untuk verifikasi suara
        $referenceFilePath = storage_path('app/voices/temp.wav'); // Pastikan file referensi ada

        // Jalankan script Python untuk verifikasi suara
        try {
            // Path ke Python dalam virtual environment
            $pythonPath = 'E:\voiceLaravel\venv\Scripts\python';

            // Path ke skrip Python yang akan dijalankan
            $scriptPath = base_path('scripts/speaker.py');

            // Proses eksekusi skrip Python dengan file yang di-upload dan file referensi
            $process = new Process([
                $pythonPath,    // Menjalankan Python dari virtual environment
                $scriptPath,    // Path ke skrip Python
                $filePath,      // Path ke file audio yang di-upload
                $referenceFilePath  // Path ke file referensi
            ]);

            $process->run();  // Menjalankan proses

            // Jika terjadi error saat menjalankan proses
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Ambil output dari Python script
            $output = $process->getOutput();
            $outputArray = explode(',', $output);

            // Ambil skor dan prediksi dari output
            $score = $outputArray[0] ?? 'N/A';
            $prediction = $outputArray[1] ?? 'N/A';

            return response()->json([
                'score' => $score,
                'prediction' => $prediction
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the request: ' . $e->getMessage()], 500);
        }
    }
}
