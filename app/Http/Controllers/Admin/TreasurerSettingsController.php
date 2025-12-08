<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolClass;

/**
 * Controller ini buat ngatur target tagihan kas kelas.
 * Jadi bendahara bisa tentuin sendiri, misal kas kelasnya Rp 2.000 per minggu,
 * atau Rp 5.000 per bulan. Data ini nanti dipake buat ngitung tunggakan otomatis.
 */
class TreasurerSettingsController extends Controller
{
    /**
     * Tampilkan Form Pengaturan Kelas Saya
     */
    /**
     * Tampilkan halaman formulir pengaturan kas.
     */
    public function index()
    {
        // Ambil data kelas milik Bendahara yang sedang login
        $class_id = auth()->user()->school_class_id;
        $class = SchoolClass::findOrFail($class_id);

        return view('admin.settings_class', [
            'class' => $class
        ]);
    }

    /**
     * Simpan Perubahan Pengaturan
     */
    /**
     * Simpan hasil edit pengaturan ke database.
     */
    public function update(Request $request)
    {
        $request->merge(['tagihan_nominal' => str_replace('.', '', $request->tagihan_nominal)]);
        $request->validate([
            'tagihan_nominal' => 'required|integer|min:0',
            'tagihan_tipe' => 'required|in:harian,mingguan,bulanan',
        ]);

        // Update data kelas milik Bendahara ini
        $class_id = auth()->user()->school_class_id;
        $class = SchoolClass::findOrFail($class_id);

        $class->update([
            'tagihan_nominal' => $request->tagihan_nominal,
            'tagihan_tipe' => $request->tagihan_tipe,
        ]);

        return redirect()->route('admin.transactions.index')
                         ->with('success', 'Pengaturan kas berhasil diperbarui!');
    }
}