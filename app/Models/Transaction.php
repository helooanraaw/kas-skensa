<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

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