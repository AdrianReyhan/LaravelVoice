from speechbrain.pretrained import SpeakerRecognition
import sys

# Fungsi untuk verifikasi suara
def verify_speaker(wav_file, ref_file):
    speaker_rec = SpeakerRecognition.from_hparams(
        source="speechbrain/spkrec-xvector-voxceleb",
        savedir="tmpdir"
    )
    
    # Verifikasi suara
    score, prediction = speaker_rec.verify_files(wav_file, ref_file)
    return score, prediction

if __name__ == '__main__':
    # Ambil argumen dari command line (file suara dan referensi)
    wav_file = sys.argv[1]  # File suara
    ref_file = sys.argv[2]  # File referensi
    
    score, prediction = verify_speaker(wav_file, ref_file)
    
    # Print hasil verifikasi
    print(f"Score: {score}, Prediction: {prediction}")
