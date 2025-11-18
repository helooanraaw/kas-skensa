<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Hash;    

class DashboardController extends Controller
{

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        // 1. Validasi
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed', // Confirmed artinya butuh field password_confirmation
        ]);

        // 2. Update Data Dasar
        $user->name = $request->name;
        $user->email = $request->email;

        // 3. Update Password (Jika diisi)
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // 4. Update Foto (Dari Hasil Crop Base64)
        if ($request->avatar_cropped) {
            // Hapus foto lama jika ada (dan bukan default)
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) { // <-- Storage kini dikenali
            Storage::disk('public')->delete($user->avatar);
            }

            // Decode Base64 Image dari Cropper.js
            $image_parts = explode(";base64,", $request->avatar_cropped);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_base64 = base64_decode($image_parts[1]);
            
            // Simpan File
            $filename = 'avatars/' . uniqid() . '.png';
            Storage::disk('public')->put($filename, $image_base64);

            // Simpan ke Database
            $user->avatar = $filename;
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function deleteAvatar()
    {
        $user = auth()->user();
        
        if ($user->avatar) {
            // Hapus file fisik
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Hapus data di database
            $user->avatar = null;
            $user->save();
        }

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }

    // TAMBAHKAN FUNGSI INI
    public function index(Request $request) // Tambahkan Request
    {
        // 1. AMBIL INPUT FILTER (Default: Bulan & Tahun Sekarang)
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // 2. PENGATURAN DASAR
        $class_id = auth()->user()->school_class_id;
        $class = \App\Models\SchoolClass::find($class_id);
        
        // 3. HITUNG SALDO TOTAL (Ini tetap ALL TIME, tidak kena filter)
        $totalMasuk = \App\Models\Transaction::where('school_class_id', $class_id)->where('type', 'masuk')->sum('amount');
        $totalKeluar = \App\Models\Transaction::where('school_class_id', $class_id)->where('type', 'keluar')->sum('amount');
        $saldoAkhir = $totalMasuk - $totalKeluar;

        // 4. DATA PERIODE INI (Kena Filter Bulan/Tahun)
        $totalMasukPeriode = \App\Models\Transaction::where('school_class_id', $class_id)
                                        ->where('type', 'masuk')
                                        ->whereMonth('date', $bulan) // Filter Bulan
                                        ->whereYear('date', $tahun)  // Filter Tahun
                                        ->sum('amount');

        $totalKeluarPeriode = \App\Models\Transaction::where('school_class_id', $class_id)
                                        ->where('type', 'keluar')
                                        ->whereMonth('date', $bulan)
                                        ->whereYear('date', $tahun)
                                        ->sum('amount');

        // 5. LOGIKA GRAFIK DONAT (STATUS SISWA - ALL TIME)
        $startDate = \Carbon\Carbon::parse('2025-07-21'); // Asumsi Tgl Mulai Tahun Ajaran
        $today = \Carbon\Carbon::now();
        $periodsPassed = $class->tagihan_tipe == 'mingguan' ? $startDate->diffInWeeks($today) : $startDate->diffInMonths($today);
        $totalWajibBayar = $periodsPassed * $class->tagihan_nominal;
        
        $students = \App\Models\Student::where('school_class_id', $class_id)->get();
        $siswaLunas = 0; $siswaNunggak = 0;
        foreach ($students as $student) {
            $totalSudahBayar = $student->transactions()->where('type', 'masuk')->sum('amount');
            if (($totalWajibBayar - $totalSudahBayar) > 0) $siswaNunggak++; else $siswaLunas++;
        }

        // 6. LOGIKA GRAFIK AREA (Progres Harian di Bulan Terpilih)
        $dates = [];
        $pemasukanPerHari = [];
        $pengeluaranPerHari = [];
        $daysInMonth = \Carbon\Carbon::createFromDate($tahun, $bulan)->daysInMonth;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = \Carbon\Carbon::createFromDate($tahun, $bulan, $i)->format('Y-m-d');
            $dates[] = $i; // Label tanggal (1, 2, 3...)

            $pemasukanPerHari[] = \App\Models\Transaction::where('school_class_id', $class_id)
                                    ->where('type', 'masuk')->whereDate('date', $date)->sum('amount');
            $pengeluaranPerHari[] = \App\Models\Transaction::where('school_class_id', $class_id)
                                    ->where('type', 'keluar')->whereDate('date', $date)->sum('amount');
        }

        // 7. KIRIM DATA KE VIEW
        return view('admin.dashboard', [
            'saldoAkhir' => $saldoAkhir,
            'totalMasukPeriode' => $totalMasukPeriode, // Ganti nama variabel
            'totalKeluarPeriode' => $totalKeluarPeriode, // Ganti nama variabel
            'siswaLunas' => $siswaLunas,
            'siswaNunggak' => $siswaNunggak,
            'dates' => $dates,
            'pemasukanPerHari' => $pemasukanPerHari,
            'pengeluaranPerHari' => $pengeluaranPerHari,
            'selectedBulan' => $bulan, // Kirim filter yg dipilih
            'selectedTahun' => $tahun  // Kirim filter yg dipilih
        ]);
    }
}