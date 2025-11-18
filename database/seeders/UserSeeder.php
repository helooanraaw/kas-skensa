<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Password Default untuk semua akun
        $password = Hash::make('password123');

        // Ambil semua kelas yang ada di database
        $classes = SchoolClass::all();

        foreach ($classes as $class) {
            // Bikin format email: xirpl1@skensa.com (huruf kecil semua, tanpa spasi)
            // Hapus spasi dari nama kelas dan ubah ke huruf kecil
            $emailPrefix = strtolower(str_replace(' ', '', $class->name)); 
            $email = $emailPrefix . '@skensa.com';

            // Cek apakah user sudah ada (biar XI RPL 1 kamu tidak tertimpa/dobel)
            $exist = User::where('school_class_id', $class->id)->first();

            if (!$exist) {
                User::create([
                    'name' => 'Bendahara ' . $class->name,
                    'email' => $email,
                    'password' => $password,
                    'school_class_id' => $class->id,
                ]);
            }
        }
    }
}