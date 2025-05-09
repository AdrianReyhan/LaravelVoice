<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\FaceEnrollment;
use App\Models\Voice;
use App\Models\VoiceEnrollment;
use Illuminate\Support\Facades\Log;

class VerifController extends Controller
{
    public function index()
    {
        return view('verifAll.index');
    }

    public function verifyFace(Request $request)
    {
        $imageData = $request->input('image');

        if (!$imageData) {
            return response()->json([
                'status' => 'error',
                'message' => 'No image provided'
            ]);
        }

        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageBinary = base64_decode($imageData);

        $fileName = 'uploads/' . uniqid() . '.png';
        Storage::disk('public')->put($fileName, $imageBinary);

        $user_id = Auth::user()->id;

        $faceEnrollment = FaceEnrollment::where('user_id', $user_id)->first();

        if (!$faceEnrollment) {
            return response()->json([
                'status' => 'error',
                'message' => 'No face enrollment found for this user'
            ]);
        }

        try {
            $client = new Client();
            $response = $client->post('http://127.0.0.1:5000/recognize_face', [
                'multipart' => [
                    [
                        'name' => 'image',
                        'contents' => fopen(storage_path('app/public/' . $fileName), 'r'),
                        'filename' => basename($fileName),
                    ],
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if ($response->getStatusCode() === 400 && isset($data['message'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => $data['message']
                ]);
            }

            if (isset($data['identity']) && $data['identity'] == $user_id) {
                $user = User::find($user_id);

                return response()->json([
                    'status' => 'success',
                    'identity' => $user ? $user->name : 'Unknown',
                    'similarity' => $data['similarity'] ?? 0,
                    'message' => 'Wajah berhasil dikenali!'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wajah dikenali tetapi tidak sesuai dengan user yang login'
                ]);
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $data = json_decode($response->getBody(), true);

            return response()->json([
                'status' => 'error',
                'message' => $data['message'] ?? 'Kesalahan dari API saat mendeteksi spoofing.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada wajah yang terdeteksi atau kesalahan dari API.'
            ]);
        }
    }

    public function verifyVoice(Request $request)
    {
        // Ambil data audio
        $audio = $request->file('audio');

        if (!$audio) {
            return response()->json([
                'status' => 'error',
                'message' => 'No audio provided'
            ]);
        }

        // Ambil user_id dari autentikasi pengguna yang sedang login
        $userId = Auth::user()->id;

        // Pastikan voice enrollment untuk user sudah ada (bisa ditambahkan jika perlu)
        $voiceEnrollment = VoiceEnrollment::where('user_id', $userId)->first();

        if (!$voiceEnrollment) {
            return response()->json([
                'status' => 'error',
                'message' => 'No voice enrollment found for this user'
            ]);
        }

        try {
            // Kirim audio dan user_id ke API Flask untuk verifikasi
            $client = new Client();
            $response = $client->post('http://127.0.0.1:5000/recognize_voice', [
                'multipart' => [
                    [
                        'name'     => 'audio_file',
                        'contents' => fopen($audio->getPathname(), 'r'),
                        'filename' => $audio->getClientOriginalName(),
                    ],
                    [
                        'name'     => 'user_id',  // Menambahkan user_id ke dalam form data
                        'contents' => $userId,
                    ]
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            // Jika identitas yang dikembalikan sesuai dengan user yang login
            if (isset($data['identity']) && $data['identity'] == $userId) {
                $user = User::find($userId);

                return response()->json([
                    'status' => 'success',
                    'prediction' => $user ? $user->name : 'Unknown',
                    'score' => $data['similarity'], // Misalnya ini adalah skor kesamaan suara
                    'message' => 'Suara berhasil dikenali!'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Suara tidak dikenali atau tidak sesuai dengan user yang login'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memverifikasi suara: ' . $e->getMessage()
            ]);
        }
    }
}
