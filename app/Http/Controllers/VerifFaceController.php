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

    // Menangani pengunggahan gambar dan melakukan request ke API Django
    public function upload(Request $request)
    {
        // Validasi file gambar yang diunggah
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Ambil file gambar yang diunggah
        $image = $request->file('image');

        // Menyimpan gambar ke dalam storage dan mendapatkan path-nya
        $imagePath = $image->store('images', 'public');

        // Mengirimkan gambar ke API Django untuk pengenalan wajah
        $response = $this->sendImageToDjangoAPI($image);

        // Log response as an array
        Log::info('Django API Response:', ['response' => $response]);

        // Menyimpan hasil prediksi dan path gambar di sesi untuk ditampilkan di Blade
        if ($response && isset($response['prediction'])) {
            // Menyimpan hasil prediksi dan path gambar di sesi untuk ditampilkan di Blade
            return redirect()->back()->with([
                'result' => $response['prediction'],
                'image_path' => $imagePath
            ]);
        } else {
            // Handle the case where 'prediction' is not found
            return redirect()->back()->with('error', 'Prediction not found or invalid response from API.');
        }
    }

    private function sendImageToDjangoAPI($image)
    {
        // Mengambil CSRF token dari Laravel session
        $csrfToken = csrf_token();

        // Mengirim request POST ke API Django menggunakan multipart/form-data
        $response = Http::withHeaders([
            'X-CSRF-TOKEN' => $csrfToken,  // Menambahkan CSRF token di header
            'Content-Type' => 'application/json',
        ])->attach(
            'image',    
            file_get_contents($image),
            $image->getClientOriginalName()
        )->post('http://127.0.0.1:8000/api/prediksi-wajah/');

        // Mengembalikan hasil dari API Django
        return $response->successful() ? $response->json() : null;
    }

    public function verifyFace(Request $request)
    {
        // Pastikan ada file gambar dalam request
        if ($request->hasFile('image')) {
            // Ambil file gambar
            $image = $request->file('image');
            
            // Mengirim gambar ke API Flask
            $client = new Client();
            $response = $client->post('http://127.0.0.1:5000/recognize_face', [
                'multipart' => [
                    [
                        'name' => 'image',
                        'contents' => fopen($image->getRealPath(), 'r'),
                        'filename' => $image->getClientOriginalName(),
                    ],
                ]
            ]);

            // Mendapatkan hasil dari Flask
            $data = json_decode($response->getBody(), true);

            // Cek hasil identifikasi
            if (isset($data['identity'])) {
                return response()->json(['status' => 'success', 'identity' => $data['identity']]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'No face detected']);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'No image provided']);
    }
}
