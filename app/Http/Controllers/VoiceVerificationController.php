<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VoiceVerificationController extends Controller
{
    public function verifyVoice(Request $request)
    {
        // Validasi input suara
        $request->validate([
            'voice' => 'required|file|mimes:wav,mp3',
        ]);

        // Kirim file suara ke Flask untuk verifikasi
        $response = Http::attach(
            'voice',
            file_get_contents($request->file('voice')->getRealPath()),
            $request->file('voice')->getClientOriginalName()
        )->post('http://127.0.0.1:5000/verify-voice');

        // Ambil response dari Flask
        $result = $response->json();

        // Kembalikan hasil ke frontend
        return response()->json($result, $response->status());
    }
}
