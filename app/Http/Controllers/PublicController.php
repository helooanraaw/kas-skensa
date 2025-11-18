<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Transaction;
use Carbon\Carbon; // <-- PENTING UNTUK HITUNG TANGGAL
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Menampilkan Halaman Landing Page (Info).
     */
    public function landing()
    {
        // Kita kirim data kelas ke halaman depan
        $classes = \App\Models\SchoolClass::orderBy('name', 'asc')->get();
        
        return view('public.landing', [
            'classes' => $classes
        ]);
    }

    /**
     * Menampilkan Halaman Daftar Kelas (24 tombol).
     */
    public function index()
    {
        $classes = SchoolClass::orderBy('name', 'asc')->get();

        // Arahkan ke view baru yang sudah di-rename
        return view('public.kas_index', [
            'classes' => $classes
        ]);
    }

    /**
     * Menampilkan halaman detail kas (Tabel Merah/Hijau).
     */
    public function showClass(Request $request, $slug)
    {
        // 1. Ambil Data Kelas
        $class = SchoolClass::where('slug', $slug)->firstOrFail();
        $class_id = $class->id;

        // 2. Hitung Saldo Kas (All Time)
        $totalMasuk = Transaction::where('school_class_id', $class_id)->where('type', 'masuk')->sum('amount');
        $totalKeluar = Transaction::where('school_class_id', $class_id)->where('type', 'keluar')->sum('amount');
        $saldoAkhir = $totalMasuk - $totalKeluar;

        // 3. Hitung Tagihan Wajib
        $startDate = Carbon::parse('2025-07-21');
        $today = Carbon::now();
        $startOfMonth = $today->copy()->startOfMonth(); // Awal bulan ini

        $periodsPassed = 0;
        if ($class->tagihan_tipe == 'mingguan') {
            $periodsPassed = $startDate->diffInWeeks($today);
        } elseif ($class->tagihan_tipe == 'bulanan') {
            $periodsPassed = $startDate->diffInMonths($today);
        } else {
            $periodsPassed = $startDate->diffInDays($today);
        }
        $totalWajibBayar = $periodsPassed * $class->tagihan_nominal;

        // 4. Ambil Siswa & Hitung Ranking + Data Grafik Donat
        $students = Student::where('school_class_id', $class_id)->get();
        
        $siswaLunas = 0;
        $siswaNunggak = 0;

        foreach ($students as $student) {
            $student->total_paid = $student->transactions()->where('type', 'masuk')->sum('amount');
            $student->tunggakan = $totalWajibBayar - $student->total_paid;

            // Hitung untuk grafik donat
            if ($student->tunggakan > 0) {
                $siswaNunggak++;
            } else {
                $siswaLunas++;
            }
        }

        // Ranking Logic
        $rankedStudents = $students->sortByDesc('total_paid')->values();
        foreach ($students as $student) {
            $student->rank = $rankedStudents->search(function($item) use ($student) {
                return $item->id == $student->id;
            }) + 1;
        }

        // Filter Tampilan Table
        $sort = $request->input('sort', 'absen');
        if ($sort == 'tertinggi') $students = $students->sortByDesc('total_paid');
        elseif ($sort == 'terendah') $students = $students->sortBy('total_paid');
        else $students = $students->sortBy('nomor_absen');

        // 5. Ambil Pengeluaran (List)
        $pengeluaran = Transaction::where('school_class_id', $class_id)
                                ->where('type', 'keluar')
                                ->orderBy('date', 'desc')
                                ->get();

        // === DATA BARU UNTUK GRAFIK ===
        
        // A. Data Akumulasi Bulan Ini (Grafik Batang)
        $totalMasukBulanIni = Transaction::where('school_class_id', $class_id)
                                        ->where('type', 'masuk')
                                        ->whereBetween('date', [$startOfMonth, $today])
                                        ->sum('amount');
        $totalKeluarBulanIni = Transaction::where('school_class_id', $class_id)
                                        ->where('type', 'keluar')
                                        ->whereBetween('date', [$startOfMonth, $today])
                                        ->sum('amount');

        // B. Data Progres 30 Hari (Grafik Area)
        $dates = [];
        $pemasukanPerHari = [];
        $pengeluaranPerHari = [];

        for ($i = 29; $i >= 0; $i--) {
            $tgl = Carbon::now()->subDays($i);
            $dates[] = $tgl->format('d M'); 
            $dbDate = $tgl->format('Y-m-d');

            $pemasukanPerHari[] = Transaction::where('school_class_id', $class_id)
                                    ->where('type', 'masuk')->where('date', $dbDate)->sum('amount');
            $pengeluaranPerHari[] = Transaction::where('school_class_id', $class_id)
                                    ->where('type', 'keluar')->where('date', $dbDate)->sum('amount');
        }

        return view('public.show_class', [
            'class' => $class,
            'students' => $students,
            'saldoAkhir' => $saldoAkhir,
            'pengeluaran' => $pengeluaran,
            'totalWajibBayar' => $totalWajibBayar,
            'currentSort' => $sort,
            // Kirim Data Grafik
            'siswaLunas' => $siswaLunas,
            'siswaNunggak' => $siswaNunggak,
            'totalMasukBulanIni' => $totalMasukBulanIni,
            'totalKeluarBulanIni' => $totalKeluarBulanIni,
            'dates' => $dates,
            'pemasukanPerHari' => $pemasukanPerHari,
            'pengeluaranPerHari' => $pengeluaranPerHari
        ]);
    }
}