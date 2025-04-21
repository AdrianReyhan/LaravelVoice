<?php

use App\Http\Controllers\FaceController;
use App\Http\Controllers\FaceEnrollmentController;
use App\Http\Controllers\VerifController;
use App\Http\Controllers\VerifFaceController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\VerifVoiceController;
use App\Http\Controllers\VoiceController;
use App\Http\Controllers\VoiceEnrollmentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::view('about', 'about')->name('about');
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::post('/register-voice', [VoiceController::class, 'store'])->name('voice.store');
    // Route::get('voice', [VoiceController::class, 'index'])->name('voice.index');

    // wajah
    Route::get('face', [FaceController::class, 'index'])->name('face.index');
    Route::get('/face-enrol', [FaceEnrollmentController::class, 'index'])->name('faceEnrol.index');
    Route::post('/face-enrol', [FaceEnrollmentController::class, 'registerFace'])->name('registerFace');
    Route::get('/verifwajah', [VerifFaceController::class, 'index'])->name('verif.index');
    Route::post('/verifwajah/upload', [VerifFaceController::class, 'upload'])->name('wajah.upload');
    Route::post('/verify-face', [VerifFaceController::class, 'verifyFace']);

    #suara
    Route::get('/voice-enrol', [VoiceEnrollmentController::class, 'index'])->name('voiceEnroll.index');
    Route::post('/voice-enrol', [VoiceEnrollmentController::class, 'registerVoice'])->name('registerVoice');
    Route::get('/verifsuara', [VerifVoiceController::class , 'index'])->name('verif.index');
    Route::post('/verify-voice', [VoiceEnrollmentController::class, 'registerVoice'])->name('verifyVoice');


    Route::get('/verifikasi', [VerifController::class, 'index'])->name('verifikasi.index');
    Route::post('/verifikasi-wajah', [VerifController::class, 'verifyFace'])->name('verifikasi.face');
    Route::post('/verifikasi-suara', [VerifController::class, 'verifyVoice'])->name('verifikasi.voice');
    // Route::post('/submit-absen', [PresensiController::class, 'submitAbsen']);


    Route::post('/upload-image', [FaceController::class, 'uploadImage'])->name('uploadImage');
    Route::get('verification', [VerificationController::class, 'index'])->name('verification.index');
    Route::post('/verify-voice', [VerificationController::class, 'verifyVoice'])->name('verify.voice');
    Route::post('/verify', [VerificationController::class, 'verify'])->name('verify');
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
