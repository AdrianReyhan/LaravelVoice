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
        if (!$request->hasFile('voice')) {
            return response()->json(['message' => 'File suara tidak ditemukan'], 400);
        }

        $file = $request->file('voice');

        // Kirim ke Flask untuk verifikasi
        $response = Http::attach('voice', file_get_contents($file), 'audio.wav')
            ->post('http://127.0.0.1:5000/verify-voice');

        return response()->json($response->json(), $response->status());
    }
}
