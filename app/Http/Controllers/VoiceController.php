<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voice;
use Illuminate\Support\Facades\Auth;

class VoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Menampilkan halaman upload suara
    public function index()
    {
        return view('voice.index');
    }

    // Menyimpan suara ke database
    public function store(Request $request)
    {
        if (!$request->hasFile('voice')) {
            return response()->json(['message' => 'File suara tidak ditemukan'], 400);
        }


        $path = $request->file('voice')->store('voices', 'public');

        Voice::updateOrCreate(
            ['user_id' => Auth::id()],
            ['voice_path' => $path]
        );

        return response()->json(['message' => 'Suara berhasil disimpan.', 'path' => $path], 200);
    }

    // Mengambil suara user
    public function getUserVoice()
    {
        $voice = Voice::where('user_id', Auth::id())->first();
        return response()->json(['voice' => $voice]);
    }
}
