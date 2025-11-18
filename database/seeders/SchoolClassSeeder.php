<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolClass; // Pastikan Model Class di-import
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema; // <-- PENTING: TAMBAHKAN INI

class SchoolClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // BUNGKUS PERINTAH TRUNCATE DENGAN INI:
        Schema::disableForeignKeyConstraints();
        SchoolClass::truncate();
        Schema::enableForeignKeyConstraints();
        // SELESAI DIBUNGKUS

        // Ini adalah list yang kamu berikan
        $jurusanList = [
            'DPIB' => 3, 'TKP' => 2, 'TSM' => 2, 'TITL' => 2, 'TKR' => 2,
            'TPM' => 2, 'RPL' => 2, 'TKJ' => 3, 'DKV' => 2, 'TPTUP' => 1,
            'TAV' => 2, 'PRF' => 1,
        ];

        // Atur default tagihan (Nanti bisa diedit Bendahara)
        $defaultTagihan = 5000;
        $defaultTipe = 'mingguan';

        // Looping untuk membuat data kelas
        foreach ($jurusanList as $jurusanKode => $jumlahKelas) {
            for ($i = 1; $i <= $jumlahKelas; $i++) {

                $namaKelas = "XI $jurusanKode $i"; 
                $slug = Str::slug($namaKelas); 

                SchoolClass::create([
                    'jurusan' => $jurusanKode,
                    'name' => $namaKelas,
                    'slug' => $slug,
                    'tagihan_nominal' => $defaultTagihan,
                    'tagihan_tipe' => $defaultTipe,
                ]);
            }
        }
    }
}