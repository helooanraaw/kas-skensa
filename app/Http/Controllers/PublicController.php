<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller ini ngurusin semua halaman yang bisa dilihat sama orang umum,
 * kayak landing page, daftar kelas, dan detail kas per kelas.
 */
class PublicController extends Controller
{
    /**
     * Menampilkan halaman depan utama (Landing Page).
     * Di sini kita ambil daftar kelas dan juga Top 5 siswa yang paling rajin bayar kas buat dipajang.
     */
    public function landing()
    {
        $classes = \App\Models\SchoolClass::orderBy('name', 'asc')->get();

        $topPayers = DB::table('transactions')
            ->join('students', 'transactions.student_id', '=', 'students.id')
            ->join('s chool_classes', 'students.school_class_id', '=', 'school_classes.id')
            ->where('transactions.type', 'masuk')
            ->select('students.id as student_id', 'students.name as student_name', 'students.nisn as nisn', 'school_classes.name as class_name', DB::raw('SUM(transactions.amount) as total_paid'))
            ->groupBy('students.id', 'students.name', 'students.nisn', 'school_classes.name')
            ->orderByDesc('total_paid')
            ->limit(5)
            ->get();

        return view('public.landing', [
            'classes' => $classes,
            'topPayers' => $topPayers,
        ]);
    }

    /**
     * Ini buat mengambil data riwayat pembayaran kas dari satu siswa tertentu.
     * Datanya dikirim dalam bentuk JSON, biasanya dipake buat popup (modal) di landing page.
     */
    public function studentTransactions($studentId)
    {
        $student = Student::with(['transactions' => function($q) {
            $q->orderBy('date', 'desc');
        }])->findOrFail($studentId);

        $data = [
            'id' => $student->id,
            'name' => $student->name,
            'nisn' => $student->nisn,
            'class_name' => optional($student->schoolClass)->name,
            'transactions' => $student->transactions->map(function($t){
                return [
                    'id' => $t->id,
                    'type' => $t->type,
                    'amount' => $t->amount,
                    'date' => $t->date,
                    'description' => $t->description,
                    'proof_image' => $t->proof_image,
                ];
            }),
        ];

        return response()->json($data);
    }

    /**
     * Menampilkan halaman yang isinya daftar semua tombol kelas.
     * Jadi pengunjung bisa milih mau lihat detail kas kelas yang mana.
     */
    public function index()
    {
        $classes = SchoolClass::orderBy('name', 'asc')->get();

        return view('public.kas_index', [
            'classes' => $classes
        ]);
    }

    /**
     * Menampilkan halaman detail kas dari satu kelas yang dipilih.
     * Di fungsi ini lumayan banyak hitungannya:
     * - Hitung saldo akhir kelas.
     * - Cek siapa siswa yang lunas dan siapa yang masih nunggak.
     * - Siapin data buat grafik pemasukan dan pengeluaran.
     */
    public function showClass(Request $request, $slug)
    {
        $class = SchoolClass::where('slug', $slug)->firstOrFail();
        $class_id = $class->id;

        $sums = Transaction::where('school_class_id', $class_id)
                    ->selectRaw("SUM(CASE WHEN type = 'masuk' THEN amount ELSE 0 END) as total_masuk")
                    ->selectRaw("SUM(CASE WHEN type = 'keluar' THEN amount ELSE 0 END) as total_keluar")
                    ->first();
        
        $saldoAkhir = ($sums->total_masuk ?? 0) - ($sums->total_keluar ?? 0);

        $startDate = Carbon::parse('2025-07-21');
        $today = Carbon::now();
        $startOfMonth = $today->copy()->startOfMonth();

        $periodsPassed = 0;
        if ($class->tagihan_tipe == 'mingguan') {
            $periodsPassed = $startDate->diffInWeeks($today);
        } elseif ($class->tagihan_tipe == 'bulanan') {
            $periodsPassed = $startDate->diffInMonths($today);
        } else {
            $periodsPassed = $startDate->diffInDays($today);
        }
        $totalWajibBayar = $periodsPassed * $class->tagihan_nominal;

        $students = Student::where('school_class_id', $class_id)
            ->with(['transactions' => function($query) {
                $query->where('type', 'masuk')->orderBy('date', 'desc');
            }])
            ->get();
        
        $siswaLunas = 0;
        $siswaNunggak = 0;

        foreach ($students as $student) {
            $student->total_paid = $student->transactions->sum('amount');
            $student->tunggakan = $totalWajibBayar - $student->total_paid;

            if ($student->tunggakan > 0) $siswaNunggak++;
            else $siswaLunas++;
        }

        $rankedStudents = $students->sortByDesc('total_paid')->values();
        foreach ($students as $student) {
            $student->rank = $rankedStudents->search(function($item) use ($student) {
                return $item->id == $student->id;
            }) + 1;
        }

        $sort = $request->input('sort', 'absen');
        if ($sort == 'tertinggi') $students = $students->sortByDesc('total_paid');
        elseif ($sort == 'terendah') $students = $students->sortBy('total_paid');
        else $students = $students->sortBy('nomor_absen');

        $pengeluaran = Transaction::where('school_class_id', $class_id)
                                ->where('type', 'keluar')
                                ->orderBy('date', 'desc')
                                ->limit(50) 
                                ->get();

        $monthlyStats = Transaction::where('school_class_id', $class_id)
                            ->whereBetween('date', [$startOfMonth, $today])
                            ->selectRaw("SUM(CASE WHEN type = 'masuk' THEN amount ELSE 0 END) as masuk")
                            ->selectRaw("SUM(CASE WHEN type = 'keluar' THEN amount ELSE 0 END) as keluar")
                            ->first();

        $totalMasukBulanIni = $monthlyStats->masuk ?? 0;
        $totalKeluarBulanIni = $monthlyStats->keluar ?? 0;

        $dates = [];
        $pemasukanPerHari = [];
        $pengeluaranPerHari = [];

        for ($i = 29; $i >= 0; $i--) {
            $tgl = Carbon::now()->subDays($i);
            $dates[] = $tgl->format('d M'); 
            $dbDate = $tgl->format('Y-m-d');

            $daily = Transaction::where('school_class_id', $class_id)
                        ->where('date', $dbDate)
                        ->selectRaw("SUM(CASE WHEN type = 'masuk' THEN amount ELSE 0 END) as masuk")
                        ->selectRaw("SUM(CASE WHEN type = 'keluar' THEN amount ELSE 0 END) as keluar")
                        ->first();

            $pemasukanPerHari[] = $daily->masuk ?? 0;
            $pengeluaranPerHari[] = $daily->keluar ?? 0;
        }

        return view('public.show_class', [
            'class' => $class,
            'students' => $students,
            'saldoAkhir' => $saldoAkhir,
            'pengeluaran' => $pengeluaran,
            'totalWajibBayar' => $totalWajibBayar,
            'currentSort' => $sort,
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