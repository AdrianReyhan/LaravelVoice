@extends('layouts.app')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Status</h1>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status Table</h6>
            </div>
            <div class="ml-3 mt-4">
                <a href="{{ route('statuses.create') }}" class="btn btn-primary">Tambah Status</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($statuses as $status)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $status->nama_status }}</td>
                                    <td class="d-flex ">
                                        <a href="{{ route('statuses.show', $status->id) }}"
                                            class="btn btn-info btn-sm mr-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('statuses.edit', $status->id) }}"
                                            class="btn btn-primary btn-sm mr-2">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('statuses.destroy', $status->id) }}" method="POST"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>
                                                </button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
@endsection
