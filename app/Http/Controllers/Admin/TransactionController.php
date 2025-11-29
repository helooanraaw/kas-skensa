<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student; // <-- 1. Import
use App\Models\Transaction; // <-- 2. Import
use Maatwebsite\Excel\Facades\Excel; // <-- 1. TAMBAHKAN INI
use App\Exports\TransactionsExport;

class TransactionController extends Controller
{

    public function storePemasukan(Request $request)
    {
        $request->merge(['amount' => str_replace('.', '', $request->amount)]);
        // 1. Validasi
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|integer|min:100', // Minimal bayar 100
            'date' => 'required|date',
            'description' => 'required|string',
        ]);

        // 2. Simpan ke database
        Transaction::create([
            'school_class_id' => auth()->user()->school_class_id, // ID Kelas
            'student_id' => $request->student_id, // Siswa yg bayar
            'user_id' => auth()->id(), // Bendahara yg input
            'type' => 'masuk', // Tipe: Masuk
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        // 3. Kembali ke halaman sebelumnya
        return redirect()->route('admin.transactions.index')
                         ->with('success', 'Pemasukan berhasil dicatat.');
    }

    public function storePengeluaran(Request $request)
    {
        $request->merge(['amount' => str_replace('.', '', $request->amount)]);
        // 1. Validasi
        $request->validate([
            'description' => 'required|string',
            'amount' => 'required|integer|min:100',
            'date' => 'required|date',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Opsional, maks 2MB
        ]);

        // (Logika upload foto nota/bukti)
        $imagePath = null;
        if ($request->hasFile('proof_image')) {
            // Simpan foto di folder 'public/bukti'
            $imagePath = $request->file('proof_image')->store('bukti', 'public');
        }

        // 2. Simpan ke database
        Transaction::create([
            'school_class_id' => auth()->user()->school_class_id,
            'student_id' => null, // Pengeluaran tidak ada ID siswa
            'user_id' => auth()->id(),
            'type' => 'keluar', // Tipe: Keluar
            'amount' => $request->amount, // Uang keluar dicatat positif
            'date' => $request->date,
            'description' => $request->description,
            'proof_image' => $imagePath, // Simpan path fotonya
        ]);

        // 3. Kembali
        return redirect()->route('admin.transactions.index')
                         ->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function edit(Transaction $transaction)
    {
        // Cek Keamanan: Pastikan Bendahara hanya bisa edit transaksi kelasnya sendiri
        if (auth()->user()->school_class_id != $transaction->school_class_id) {
            abort(403); // Dilarang akses
        }

        // Ambil data siswa HANYA untuk dropdown
        $students = Student::where('school_class_id', auth()->user()->school_class_id)
                        ->orderBy('nomor_absen', 'asc')
                        ->get();

        return view('admin.transactions_edit', [
            'transaction' => $transaction,
            'students' => $students
        ]);
    }

    /**
     * Update (Simpan) data transaksi yang diedit.
     */
    public function update(Request $request, Transaction $transaction)
    {
        // Cek Keamanan
        if (auth()->user()->school_class_id != $transaction->school_class_id) {
            abort(403);
        }

        // Validasi data (disesuaikan)
        $request->validate([
            'description' => 'required|string',
            'amount' => 'required|integer|min:100',
            'date' => 'required|date',
            // ✅ UPDATE DI SINI: Tambah mimes video dan max size jadi 20MB (20480 KB)
            'proof_image' => 'nullable|file|mimes:jpeg,png,jpg,mp4,mov,avi|max:20480', 
        ]);

        // --- Logika Update ---
        $transaction->date = $request->date;
        $transaction->description = $request->description;
        $transaction->amount = $request->amount;

        // Jika dia pemasukan, update student_id nya
        if ($transaction->type == 'masuk') {
            $transaction->student_id = $request->student_id;
        }

        // Jika dia pengeluaran dan ada Ganti Foto Nota
        if ($transaction->type == 'keluar' && $request->hasFile('proof_image')) {
            // (Opsional: Hapus foto lama)
            // Storage::disk('public')->delete($transaction->proof_image);

            // Simpan foto baru
            $transaction->proof_image = $request->file('proof_image')->store('bukti', 'public');
        }

        $transaction->save(); // Simpan perubahan

        return redirect()->route('admin.transactions.index')
                        ->with('success', 'Transaksi berhasil di-update.');
    }

    /**
     * Hapus data transaksi.
     */
    public function destroy(Transaction $transaction)
    {
        // Cek Keamanan
        if (auth()->user()->school_class_id != $transaction->school_class_id) {
            abort(403);
        }

        // (Opsional: Hapus file fotonya jika ada)
        // if ($transaction->proof_image) {
        //     Storage::disk('public')->delete($transaction->proof_image);
        // }

        $transaction->delete(); // Hapus dari database

        return redirect()->route('admin.transactions.index')
                        ->with('success', 'Transaksi berhasil dihapus.');
    }

    // FUNGSI UNTUK MENAMPILKAN HALAMAN INPUT (index)
    public function index()
    {
        // Ambil ID Kelas Bendahara
        $class_id = auth()->user()->school_class_id;
        $class = \App\Models\SchoolClass::find($class_id);

        // --- (Data Siswa, ini sudah ada) ---
        $students = Student::where('school_class_id', $class_id)
                        ->orderBy('nomor_absen', 'asc')
                        ->get();

        // --- ✅ KODE BARU DIMULAI DARI SINI ---

        // 1. Ambil 5 transaksi terakhir (urut dari yg terbaru)
       $latestTransactions = Transaction::where('school_class_id', $class_id)
                                         ->with('student') // ✅ TAMBAHKAN INI
                                         ->orderBy('date', 'desc')
                                         ->orderBy('created_at', 'desc')
                                         ->get();

        // 2. Hitung Total Saldo
        $totalMasuk = Transaction::where('school_class_id', $class_id)
                                ->where('type', 'masuk')
                                ->sum('amount');

        $totalKeluar = Transaction::where('school_class_id', $class_id)
                                ->where('type', 'keluar')
                                ->sum('amount');

        $saldoAkhir = $totalMasuk - $totalKeluar;

        // --- ✅ KODE BARU SELESAI ---

        // Kirim semua data ke view
        return view('admin.transactions', [
            'students' => $students,
            'latestTransactions' => $latestTransactions, // <-- Data baru
            'saldoAkhir' => $saldoAkhir,           // <-- Data baru
            'class' => $class,
        ]);
    }

    public function exportExcel(Request $request) // Tambah Request
    {
        $class_id = auth()->user()->school_class_id;

        // Ambil filter dari URL (tombol download)
        $bulan = $request->input('bulan'); 
        $tahun = $request->input('tahun');

        $className = auth()->user()->schoolClass->slug ?? 'laporan';
        // Nama file jadi lebih spesifik (misal: laporan_oktober_2025.xlsx)
        $fileName = 'laporan_' . $className . '_' . ($bulan ? $bulan.'-'.$tahun : 'semua') . '.xlsx';

        // Kirim filter ke Class Export
        return Excel::download(new TransactionsExport($class_id, $bulan, $tahun), $fileName);
    }

}