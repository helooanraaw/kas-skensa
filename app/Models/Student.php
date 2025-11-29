<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    
    protected $fillable = [
        'school_class_id',
        'nomor_absen',
        'name',
        'nisn',
    ];

    public function transactions()
    {
        // Siswa punya banyak Transaksi
        return $this->hasMany(Transaction::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }
}