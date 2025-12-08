<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student; // <-- 1. IMPORT MODEL SISWA
use Maatwebsite\Excel\Facades\Excel; // <-- 1. TAMBAHKAN INI
use App\Imports\StudentsImport;
use Illuminate\Database\QueryException;

/**
 * Controller ini khusus buat ngurusin data Siswa.
 * Mulai dari nampilin daftar siswa, ranking bayaran, tambah, edit, hapus,
 * sampai fitur canggih import data dari Excel.
 */
class StudentController extends Controller
{
    /**
     * Menampilkan daftar semua siswa di kelas admin yang login.
     * Di sini ada fitur ranking otomatis based on siapa yang paling banyak bayar kas.
     */
    public function index(Request $request)
    {
        $class_id = auth()->user()->school_class_id;

        // 1. Ambil semua siswa beserta Total Bayar mereka (Hanya Pemasukan)
        // withSum adalah fitur Laravel untuk menjumlahkan relasi otomatis
        $students = Student::where('school_class_id', $class_id)
                           ->withSum(['transactions as total_paid' => function($query) {
                               $query->where('type', 'masuk');
                           }], 'amount')
                           ->get();

        // 2. Hitung Ranking (Siapa Juara 1, 2, 3...)
        // Kita urutkan dulu berdasarkan bayaran tertinggi untuk menentukan posisi
        $rankedIds = $students->sortByDesc('total_paid')->pluck('id')->values();

        // Kita "tempelkan" data ranking ke setiap siswa
        foreach ($students as $student) {
            // Search index + 1 karena index mulai dari 0
            $student->rank = $rankedIds->search($student->id) + 1;
        }

        // 3. Filter Urutan Tampilan (Sesuai Request User)
        $sort = $request->input('sort', 'absen'); // Default urut Absen

        if ($sort == 'tertinggi') {
            $students = $students->sortByDesc('total_paid');
        } elseif ($sort == 'terendah') {
            $students = $students->sortBy('total_paid');
        } else {
            // Default: Urut No Absen
            $students = $students->sortBy('nomor_absen');
        }

        return view('admin.students', [
            'students' => $students,
            'currentSort' => $sort
        ]);
    }

    /**
     * Simpan data siswa baru yang diinput manual lewat form.
     */
    public function store(Request $request)
    {
        // 1. Validasi data yang masuk
        $request->validate([
            'nomor_absen' => 'required|integer',
            'name' => 'required|string',
            'nisn' => 'required|string|unique:students,nisn', // NISN harus unik
        ]);

        // 2. Ambil ID Kelas dari Bendahara yang sedang login
        $class_id = auth()->user()->school_class_id;

        // 3. Simpan data ke database
        Student::create([
            'school_class_id' => $class_id, // Masukkan ID kelas bendahara
            'nomor_absen' => $request->nomor_absen,
            'name' => $request->name,
            'nisn' => $request->nisn,
        ]);

        // 4. Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('admin.students.index')
                         ->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit untuk siswa tertentu.
     */
    public function edit(Student $student)
    {
        // $student adalah data siswa yg diklik (otomatis diambil Laravel)
        return view('admin.students_edit', [
            'student' => $student
        ]);
    }

    /**
     * Simpan (Update) data siswa yang diedit.
     */
    public function update(Request $request, Student $student)
    {
        // 1. Validasi data
        $request->validate([
            'nomor_absen' => 'required|integer',
            'name' => 'required|string',
            'nisn' => 'required|string|unique:students,nisn,' . $student->id, // NISN unik, kecuali untuk dirinya sendiri
        ]);

        // 2. Update data di database
        $student->update([
            'nomor_absen' => $request->nomor_absen,
            'name' => $request->name,
            'nisn' => $request->nisn,
        ]);

        // 3. Kembali ke halaman tabel
        return redirect()->route('admin.students.index')
                        ->with('success', 'Data siswa berhasil di-update.');
    }

    /**
     * Hapus data siswa selamanya.
     * HATI-HATI: Siswa yang sudah pernah bayar kas TIDAK BOLEH dihapus sembarangan
     * karena bakal ngerusak laporan keuangan. Jadi sistem akan nolak kalau ada datanya.
     */
    public function destroy(Student $student)
    {
        // Kita akan "mencoba" (try) menghapus
        try {

            // 1. Coba hapus data dari database
            $student->delete();

            // 2. Jika BERHASIL, kembali dengan pesan sukses
            return redirect()->route('admin.students.index')
                            ->with('success', 'Siswa berhasil dihapus.');

        } catch (QueryException $e) {
            // 3. Jika GAGAL (karena error database)

            // Kita cek apakah errornya adalah "Foreign Key" (kode 1451 atau 23000)
            if ($e->errorInfo[1] == 1451) {
                // 4. Jika BENAR, kembali dengan pesan PERINGATAN
                return redirect()->route('admin.students.index')
                                ->with('error', 'GAGAL HAPUS! Siswa ini sudah memiliki riwayat transaksi kas.');
            }

            // 5. Jika errornya bukan karena Foreign Key, tampilkan error aslinya
            return redirect()->route('admin.students.index')
                            ->with('error', 'Terjadi kesalahan database: ' . $e->getMessage());
        }
    }

    /**
     * Fitur Import Excel ajaib.
     * Upload satu file Excel, ribuan data siswa langsung masuk database.
     */
    public function importExcel(Request $request)
    {
        // 1. Validasi file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            // 2. Ambil ID Kelas Bendahara
            $class_id = auth()->user()->school_class_id;

            // 3. Panggil Class Import, kirim ID Kelas
            Excel::import(new StudentsImport($class_id), $request->file('file'));

            // 4. Kembali dengan pesan sukses
            return redirect()->route('admin.students.index')
                            ->with('success', 'Data siswa berhasil di-import massal!');

        } catch (\Exception $e) {
            // Jika ada error (misal format salah), kembali dengan error
            return redirect()->route('admin.students.index')
                            ->with('error', 'Gagal import! Pastikan format file benar. Error: ' . $e->getMessage());
        }
    }
}