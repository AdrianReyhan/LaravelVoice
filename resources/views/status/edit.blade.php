@extends('layouts.app')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Edit Status</h1>

     

        <!-- Form Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-column">
                <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Status</h6>
                <small class="text-muted">Silakan ubah data status absen di bawah ini.</small>
            </div>

            <div class="card-body">
                <form action="{{ route('statuses.update', $statuses->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label for="nama_status">Nama Status</label>
                        <input type="text" name="nama_status" id="nama_status"
                            class="form-control @error('nama_status') is-invalid @enderror"
                            value="{{ old('nama_status', $statuses->nama_status) }}"
                            placeholder="Contoh: Hadir, Izin, Sakit" required autofocus>

                        @error('nama_status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        <a href="{{ route('statuses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
