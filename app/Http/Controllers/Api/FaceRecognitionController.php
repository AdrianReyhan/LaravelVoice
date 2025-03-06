<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Face;
use Illuminate\Http\Request;

class FaceRecognitionController extends Controller
{
    /**
     * Mengambil gambar berdasarkan user_id
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getImages($user_id)
    {
        // Ambil data gambar berdasarkan user_id
        $images = Face::where('user_id', $user_id)->get();

        // Cek jika tidak ada gambar ditemukan
        if ($images->isEmpty()) {
            return response()->json(["status" => "error", "message" => "Tidak ada gambar ditemukan"], 404);
        }

        // Kirimkan gambar-gambar yang ditemukan dalam format JSON
        return response()->json($images);
    }
}