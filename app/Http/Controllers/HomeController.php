<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Controller ini otomatis dibuat sama Laravel buat halaman dashboard.
 * Jadi kalau user berhasil login, biasanya bakal diarahkan ke sini.
 */
class HomeController extends Controller
{
    /**
     * Fungsi ini jalan duluan tiap kali controller dipanggil.
     * Gunanya buat mastiin cuma user yang udah login (auth) yang boleh akses halaman ini.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilin halaman dashboard utama buat user yang udah login.
     */
    public function index()
    {
        return view('home');
    }
}
