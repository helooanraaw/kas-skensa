<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    // ✅ TAMBAHKAN INI (IZINKAN UPDATE SETTING)
    protected $fillable = [
        'jurusan',
        'name',
        'slug',
        'tagihan_nominal',      // <-- Ini penting
        'tagihan_tipe',         // <-- Ini penting
        'tagihan_target_per_bulan', // <-- Ini juga
    ];

    // (Jika ada fungsi relasi lain, biarkan saja)
}