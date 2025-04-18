<?php

namespace App\Http\Controllers;

use App\Models\Voice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class VoiceEnrollmentController extends Controller
{
    public function index()
    {
        return view('voiceEnrollment.index');
    }

    public function registerVoice(Request $request)
    {
        try {
            $request->validate([
                'voice' => 'required|mimes:wav,mp3,webm|max:10240', // max 10MB
            ]);

            $userId = Auth::id();
            $file = $request->file('voice');
            $extension = $file->getClientOriginalExtension();

            // Buat nama file dan simpan ke storage/public/voices
            $fileName = 'voices/voice_' . uniqid() . '.' . $extension;
            $stored = Storage::disk('public')->put($fileName, file_get_contents($file));

            if (!$stored) {
                Log::error("Gagal menyimpan file suara ke storage.");
                return redirect()->route('voiceEnroll.index')->with('error', 'Gagal menyimpan suara.');
            }

            Log::info("Voice successfully saved: " . $fileName);
            $filePath = storage_path('app/public/' . $fileName);

            // Kirim file ke Flask API
            $client = new Client();
            $response = $client->post('http://127.0.0.1:5000/enrol_voice', [
                'multipart' => [
                    [
                        'name'     => 'voice',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => basename($filePath),
                    ],
                    [
                        'name'     => 'user_id',
                        'contents' => $userId,
                    ],
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['status']) && $data['status'] === 'success') {
                $existing = Voice::where('user_id', $userId)->first();

                if ($existing) {
                    // Hapus file lama dari storage
                    if (Storage::disk('public')->exists($existing->voice_path)) {
                        Storage::disk('public')->delete($existing->voice_path);
                    }

                    // Update voice_path baru
                    $existing->update(['voice_path' => $fileName]);
                } else {
                    // Insert baru
                    Voice::create([
                        'user_id' => $userId,
                        'voice_path' => $fileName,
                    ]);
                }

                return redirect()->route('voiceEnroll.index')->with('success', 'Suara berhasil didaftarkan!');
            }

            return redirect()->route('voiceEnroll.index')->with('error', 'Pendaftaran suara gagal dari API.');
        } catch (\Exception $e) {
            Log::error("Voice enrollment error: " . $e->getMessage());

            return redirect()->route('voiceEnroll.index')->with('error', 'Terjadi kesalahan saat mendaftar suara.');
        }
    }
}
