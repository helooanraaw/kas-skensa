<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public function student()
    {
        // Relasi ke Model Student
        return $this->belongsTo(Student::class);
    }

    // ✅ TAMBAHKAN INI
    protected $fillable = [
        'school_class_id',
        'student_id',
        'user_id',
        'type',
        'amount',
        'date',
        'description',
        'proof_image',
    ];
}