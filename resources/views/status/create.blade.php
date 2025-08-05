@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Status</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Create Status Absen</h6>
                <p class="text-sm text-gray-600">Tambah formulir berikut untuk menambah status absen.</p>
            </div>
            <div class="card-body">
                <!-- Form for Editing Status -->
                <form action="{{ route('statuses.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nama_status">Status Absen</label>
                        <input type="text" id="nama_status" name="nama_status"
                            value="{{ old('nama_status') }}" required class="form-control" />

                        @error('nama_status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            Simpan
                        </button>
                        <a href="{{ route('statuses.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
