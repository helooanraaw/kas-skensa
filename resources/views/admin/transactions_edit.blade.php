@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Transaksi (ID: {{ $transaction->id }})</div>
                <div class="card-body">

                    <form action="{{ route('admin.transactions.update', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="date" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ $transaction->date }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="description" name="description" value="{{ $transaction->description }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah (Rp)</label>
                            <input type="number" class="form-control" id="amount" name="amount" value="{{ $transaction->amount }}" required>
                        </div>

                        @if($transaction->type == 'masuk')
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Siswa</label>
                                <select class="form-select" id="student_id" name="student_id" required>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ $transaction->student_id == $student->id ? 'selected' : '' }}>
                                            {{ $student->nomor_absen }}. {{ $student->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if($transaction->type == 'keluar')
                            <div class="mb-3">
                                <label for="proof_image" class="form-label">Ganti Foto Nota (Opsional)</label>
                                <input type="file" class="form-control" id="proof_image" name="proof_image">
                                @if($transaction->proof_image)
                                    <small>Bukti saat ini: <a href="{{ asset('storage/' . $transaction->proof_image) }}" target="_blank">Lihat Bukti</a></small>
                                @endif
                            </div>
                        @endif

                        <button type="submit" class="btn btn-success">Update Transaksi</button>
                        <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">Batal</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection