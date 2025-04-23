<?php

namespace App\Http\Controllers;

use App\Models\VoiceEnrollment;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VoiceEnrollmentController extends Controller
{
    public function index()
    {
        return view('voiceEnrollment.index');
    }

    public function registerVoice(Request $request)
    {
        try {
            // Validate the incoming request to ensure an audio file is provided
            $request->validate([
                'voice' => 'required|mimes:wav,mp3,webm|max:10240', // 10 MB limit
            ]);

            // Get user ID and the uploaded file
            $userId = Auth::id();
            $file = $request->file('voice');

            $directory = storage_path('app/public/voices');

            $fileName = 'voice_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $directory . '/' . $fileName; 

            $file->move($directory, $fileName);

            // Send the audio file to Flask API for processing
            $client = new Client();
            $response = $client->post('http://127.0.0.1:5000/enrol_voice', [
                'multipart' => [
                    [
                        'name'     => 'audio_file',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => $fileName,
                    ],
                    [
                        'name'     => 'user_id',
                        'contents' => $userId,
                    ],
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['status']) && $data['status'] === 'success') {
                // Get the embedding from the Flask response
                $embedding = $data['embedding'];

                // Check if the user already has an existing voice record
                $existingVoice = VoiceEnrollment::where('user_id', $userId)->first();
                if ($existingVoice) {
                    // Update the existing voice embedding
                    $existingVoice->update(['embedding' => $embedding]);
                } else {
                    // Create a new record for the user's voice embedding
                    VoiceEnrollment::create([
                        'user_id' => $userId,
                        'embedding' => $embedding,
                    ]);
                }

                return redirect()->route('voiceEnroll.index')->with('success', 'Voice enrolled successfully!');
            }

            return redirect()->route('voiceEnroll.index')->with('error', 'Voice enrollment failed.');
        } catch (\Exception $e) {
            Log::error("Voice enrollment error: " . $e->getMessage());
            return redirect()->route('voiceEnroll.index')->with('error', 'An error occurred while registering the voice.');
        }
    }
}
