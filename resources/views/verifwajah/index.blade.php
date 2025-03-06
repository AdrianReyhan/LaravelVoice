@extends('layouts.app')

@section('content')
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page Heading -->
            <h1 class="h3 mb-2 text-gray-800">{{ __('Verifikasi Wajah') }}</h1>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">
                                {{ __('Silakan unggah gambar untuk verifikasi wajah.') }}
                            </p>

                            <!-- Form untuk mengunggah gambar -->
                            <h3>Upload Gambar untuk Pengenalan Wajah</h3>
                            <form action="{{ url('/verify-face') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <input type="file" name="image" accept="image/*" required>
                                    <button type="submit" class="btn btn-primary">Verify Face</button>
                                </div>
                            </form>

                            <!-- Menampilkan hasil verifikasi -->
                            @if (session('status'))
                                <div class="alert alert-{{ session('status') == 'success' ? 'success' : 'danger' }}">
                                    <p>{{ session('message') ?? (session('status') == 'success' ? 'Verifikasi berhasil!' : 'Verifikasi gagal!') }}</p>
                                    @if (session('identity'))
                                        <p><strong>Identitas:</strong> {{ session('identity') }}</p>
                                    @endif
                                </div>
                            @endif

                            <!-- Menampilkan gambar yang diupload -->
                            @if (session('image_path'))
                                <h3>Gambar yang Diupload:</h3>
                                <img src="{{ asset('storage/' . session('image_path')) }}" alt="Uploaded Image" class="img-fluid">
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection
