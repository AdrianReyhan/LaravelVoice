<?php

namespace App\Http\Controllers;

use App\Models\FaceEnrollment;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FaceEnrollmentController extends Controller
{
    public function index()
    {
        return view('faceEnrolment.index');
    }

    public function registerFace(Request $request)
    {
        // Validasi input gambar (base64)
        $request->validate([
            'image' => 'required',
        ]);

        // Ambil data gambar base64
        $imageData = $request->input('image');

        // Menghapus prefix base64
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);

        // Decode gambar base64
        $imageBinary = base64_decode($imageData);

        // Simpan gambar sementara
        $fileName = 'uploads/' . uniqid() . '.png';
        $filePath = storage_path('app/public/' . $fileName);

        // Log untuk mengecek file path dan apakah gambar berhasil disimpan
        Log::info("Saving image to: " . $filePath);

        // Simpan gambar ke disk public
        if (Storage::disk('public')->put($fileName, $imageBinary)) {
            Log::info("Image successfully saved: " . $fileName);
        } else {
            Log::error("Failed to save image");
        }

        // Ambil ID pengguna yang sedang login
        $userId = Auth::user()->id;

        // Kirim gambar dan user_id ke API Flask untuk pendaftaran wajah
        try {
            $client = new Client();
            $response = $client->post('http://127.0.0.1:5000/enrol_face', [
                'multipart' => [
                    [
                        'name' => 'image',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => basename($fileName),
                    ],
                    [
                        'name' => 'user_id',
                        'contents' => $userId,
                    ],
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] == 'success') {
                // Simpan data pendaftaran wajah ke dalam database
                FaceEnrollment::create([
                    'user_id' => $userId,
                    'image_path' => $fileName,
                ]);

                return redirect()->route('faceEnrol.index')->with('success', 'Face registered successfully!');
            }

            return redirect()->route('faceEnrol.index')->with('error', 'Error registering face');
        } catch (\Exception $e) {
            Log::error("Error connecting to Face Recognition API: " . $e->getMessage());
            return redirect()->route('faceEnrol.index')->with('error', 'Error connecting to Face Recognition API');
        }
    }
}
