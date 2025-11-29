<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController; // Pastikan ini ada
use App\Http\Controllers\Admin\DashboardController; // Pastikan ini ada
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\TreasurerSettingsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ❌ HAPUS ATAU KOMENTARI YANG INI
// Route::get('/', function () {
//     return view('welcome');
// });

// ✅ PASTIKAN ADA YANG INI (YANG BARU KITA BUAT)
// ... (use statements) ...

// --- RUTE PUBLIK (BARU) ---
// Halaman utama (Landing Page)
Route::get('/', [PublicController::class, 'landing'])->name('home'); 

// Halaman untuk pilih kelas (24 tombol)
Route::get('/kas', [PublicController::class, 'index'])->name('kas.index'); 

// Halaman detail kas per kelas
Route::get('/kas/{slug}', [PublicController::class, 'showClass'])->name('kas.show'); 
// API: student transactions (used by landing page modal)
Route::get('/student/{id}/transactions', [PublicController::class, 'studentTransactions'])->name('student.transactions');
// -------------------------



// Sisanya (Auth::routes, /dashboard) biarkan di bawah...
Auth::routes(['register' => false]);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- MANAJEMEN SISWA ---
    Route::get('/dashboard/students', [StudentController::class, 'index'])->name('admin.students.index');
    Route::post('/dashboard/students', [StudentController::class, 'store'])->name('admin.students.store');

    // ✅ TAMBAHKAN 3 RUTE INI:
    // 1. Rute untuk menampilkan halaman form edit
    Route::get('/dashboard/students/{student}/edit', [StudentController::class, 'edit'])->name('admin.students.edit');
    // 2. Rute untuk menyimpan data (Update) dari form edit
    Route::put('/dashboard/students/{student}', [StudentController::class, 'update'])->name('admin.students.update');
    // 3. Rute untuk menghapus data
    Route::delete('/dashboard/students/{student}', [StudentController::class, 'destroy'])->name('admin.students.destroy');
    // -------------------------

    // Rute untuk menampilkan halaman input kas/transaksi
    Route::get('/dashboard/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');
    // Rute untuk menyimpan PEMASUKAN kas
    Route::post('/dashboard/transactions/in', [TransactionController::class, 'storePemasukan'])->name('admin.transactions.store.in');
    // Rute untuk menyimpan PENGELUARAN kas
    Route::post('/dashboard/transactions/out', [TransactionController::class, 'storePengeluaran'])->name('admin.transactions.store.out');
    Route::get('/dashboard/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('admin.transactions.edit');
    Route::put('/dashboard/transactions/{transaction}', [TransactionController::class, 'update'])->name('admin.transactions.update');
    Route::delete('/dashboard/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('admin.transactions.destroy');
    // --- ✅ TAMBAHKAN RUTE INI ---
    Route::get('/dashboard/export/excel', [TransactionController::class, 'exportExcel'])->name('admin.export.excel');

    Route::post('/dashboard/students/import', [StudentController::class, 'importExcel'])->name('admin.students.import');

    Route::post('/dashboard/profile/update', [DashboardController::class, 'updateProfile'])->name('admin.profile.update');

    Route::delete('/dashboard/profile/avatar', [DashboardController::class, 'deleteAvatar'])->name('admin.profile.delete_avatar');

    Route::get('/dashboard/settings', [TreasurerSettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('/dashboard/settings', [TreasurerSettingsController::class, 'update'])->name('admin.settings.update');

});