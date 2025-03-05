<?php

use App\Http\Controllers\VoiceVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/verify-voice', [VoiceVerificationController::class, 'verifyVoice']);
