<?php

namespace App\Http\Controllers;

use App\Models\Face;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaceController extends Controller
{
    public function index()
    {
        return view('face.index');
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function uploadImage(Request $request)
    {
        // Validasi file yang diupload (maksimal 3 file dan hanya gambar)
        $request->validate([
            'images' => 'required|array|min:1|max:3',  // Maksimal 3 gambar
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Validasi format dan ukuran
        ]);

        // Proses setiap file yang diupload
        foreach ($request->file('images') as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Simpan file di folder public/images
            $file->move(public_path('images'), $filename);

            // Menyimpan data ke dalam tabel images di database
            $image = new Face();
            $image->user_id = Auth::id(); // Menyimpan user_id yang sedang login
            $image->filename = $filename;
            $image->save();
        }

        // Mengembalikan respons untuk menampilkan hasil upload
        return redirect()->route('face.index')->with('success', 'Images uploaded successfully');
    }
}
