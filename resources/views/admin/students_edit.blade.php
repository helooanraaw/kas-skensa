@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6"> <div class="card">
                <div class="card-header">Edit Data Siswa: {{ $student->name }}</div>
                <div class="card-body">

                    <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
                        @csrf
                        @method('PUT') <div class="mb-3">
                            <label for="nomor_absen" class="form-label">No. Absen</label>
                            <input type="number" class="form-control" id="nomor_absen" name="nomor_absen" value="{{ $student->nomor_absen }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Siswa</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $student->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="nisn" class="form-label">NISN</label>
                            <input type="text" class="form-control" id="nisn" name="nisn" value="{{ $student->nisn }}" required>
                        </div>

                        <button type="submit" class="btn btn-success">Update Data</button>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Batal</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection