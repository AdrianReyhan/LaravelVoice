<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VerificationController extends Controller
{
    public function index(){
        return view('verification.index');
    }

    public function verifyVoice(Request $request)
    {
        // Validasi file suara
        if (!$request->hasFile('voice')) {
            return response()->json(['message' => 'File suara tidak ditemukan'], 400);
        }

        $file = $request->file('voice');

        // Kirim file suara ke Flask API untuk verifikasi
        $response = Http::attach('voice', file_get_contents($file), 'audio.wav')
            ->post('http://127.0.0.1:5000/verify-voice'); // Sesuaikan dengan URL Flask Anda

        // Kembalikan hasil dari Flask ke frontend
        if ($response->successful()) {
            return response()->json([
                'message' => $response->json()['message'],
                'score' => $response->json()['score']
            ], 200);
        }

        return response()->json([
            'message' => $response->json()['message'] ?? 'Terjadi kesalahan',
            'error' => $response->json()['error'] ?? 'Unknown Error'
        ], $response->status());
    }
}
