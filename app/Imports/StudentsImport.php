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
        // Support either 'nisn' or 'nis' header in the Excel file (some templates use 'nis')
        $nomor_absen = isset($row['nomor_absen']) ? trim((string) $row['nomor_absen']) : null;
        $name = isset($row['name']) ? trim((string) $row['name']) : null;

        // prefer 'nisn' if present, otherwise accept 'nis'
        $nisValue = null;
        if (isset($row['nisn']) && $row['nisn'] !== null && $row['nisn'] !== '') {
            $nisValue = trim((string) $row['nisn']);
        } elseif (isset($row['nis']) && $row['nis'] !== null && $row['nis'] !== '') {
            $nisValue = trim((string) $row['nis']);
        }

        return new Student([
            'school_class_id' => $this->class_id,
            'nomor_absen'     => $nomor_absen,
            'name'            => $name,
            'nisn'            => $nisValue,
        ]);
    }
}