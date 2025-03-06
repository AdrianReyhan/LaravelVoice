@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <h1 class="h3 mb-2 text-gray-800">Take Photos for Face Recognition</h1>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @elseif(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Form untuk mengupload gambar -->
            <form action="{{ route('uploadImage') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="images" class="form-label">Select 3 Images for Upload</label>
                    <!-- Menambahkan atribut multiple untuk memilih lebih dari satu gambar -->
                    <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple required>
                    <small class="form-text text-muted">You can select up to 3 images</small>
                </div>
                <button type="submit" class="btn btn-success mt-3">Upload Images</button>
            </form>

        </div>
    </div>
@endsection
