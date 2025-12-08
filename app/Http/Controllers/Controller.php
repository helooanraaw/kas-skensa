<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Ini adalah controller induk (Main Controller).
 * Semua controller lain di aplikasi ini 'anak' dari controller ini,
 * jadi mereka bisa pake fitur-fitur dasar bawaan Laravel tanpa harus bikin ulang.
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
