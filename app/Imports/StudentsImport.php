<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Penting: Untuk membaca Baris 1 (nomor_absen, name, nisn)

class StudentsImport implements ToModel, WithHeadingRow
{
    protected $class_id;

    // 1. Terima ID Kelas dari Controller
    public function __construct(int $class_id)
    {
        $this->class_id = $class_id;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    // 2. Fungsi ini akan dijalankan untuk SETIAP BARIS di Excel
    public function model(array $row)
    {
        // $row['nomor_absen'] akan membaca kolom 'nomor_absen' di Excel

        return new Student([
            'school_class_id' => $this->class_id, // Masukkan ID Kelas
            'nomor_absen'     => $row['nomor_absen'],
            'name'            => $row['name'],
            'nisn'            => $row['nisn'],
        ]);
    }
}