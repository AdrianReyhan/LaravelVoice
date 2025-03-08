<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VerifFaceController extends Controller
{
    // Menampilkan halaman upload
    public function index()
    {
        return view('verifwajah.index');
    }

    public function verifyFace(Request $request)
    {
        // Pastikan ada gambar dalam request (base64)
        $imageData = $request->input('image');

        if (!$imageData) {
            return response()->json([
                'status' => 'error',
                'message' => 'No image provided'
            ]);
        }

        // Menghapus prefix base64
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageBinary = base64_decode($imageData);

        // Simpan sementara gambar di storage Laravel
        $fileName = 'uploads/' . uniqid() . '.png';
        Storage::disk('public')->put($fileName, $imageBinary);

        // Kirim gambar ke API Flask
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

            // Mendapatkan hasil dari Flask
            $data = json_decode($response->getBody(), true);

            if (isset($data['identity'])) {
                return response()->json([
                    'status' => 'success',
                    'identity' => $data['identity']
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No face detected'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error connecting to Face Recognition API.'
            ]);
        }
    }
}
