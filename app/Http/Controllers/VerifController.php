<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\FaceEnrollment;
use App\Models\Voice;

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

            if (isset($data['identity']) && $data['identity'] == $user_id) {
                $user = User::find($user_id);

                return response()->json([
                    'status' => 'success',
                    'identity' => $user ? $user->name : 'Unknown',
                    'message' => 'Face recognized successfully!'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wajah dikenali tetapi tidak sesuai dengan user yang login'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No face detected or error from API.'
            ]);
        }
    }

    /**
     * Verifikasi suara dari input file audio
     */
    public function verifyVoice(Request $request)
    {
        if (!$request->hasFile('audio')) {
            return response()->json([
                'status' => 'error',
                'message' => 'No audio file provided'
            ]);
        }

        $audioFile = $request->file('audio');

        $fileName = 'uploads/audio_' . uniqid() . '.wav';
        Storage::disk('public')->put($fileName, file_get_contents($audioFile));

        $user_id = Auth::user()->id;

        $voiceEnrollment = Voice::where('user_id', $user_id)->first();

        if (!$voiceEnrollment) {
            return response()->json([
                'status' => 'error',
                'message' => 'No voice enrollment found for this user'
            ]);
        }

        try {
            $client = new Client();
            $response = $client->post('http://127.0.0.1:5000/recognize_voice', [
                'multipart' => [
                    [
                        'name' => 'audio',
                        'contents' => fopen(storage_path('app/public/' . $fileName), 'r'),
                        'filename' => basename($fileName),
                    ],
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['identity']) && $data['identity'] == $user_id) {
                $user = User::find($user_id);

                return response()->json([
                    'status' => 'success',
                    'identity' => $user ? $user->name : 'Unknown',
                    'message' => 'Voice recognized successfully!'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Suara dikenali tetapi tidak sesuai dengan user yang login'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Voice recognition failed or no voice detected.'
            ]);
        }
    }
}
