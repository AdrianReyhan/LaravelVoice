<?php

use App\Http\Controllers\Api\FaceRecognitionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/get-images/{user_id}', [FaceRecognitionController::class, 'getImages']);  // Tambahkan route baru untuk mengambil gambar berdasarkan user_id
