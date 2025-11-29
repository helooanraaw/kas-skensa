<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\TreasurerSettingsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicController::class, 'landing'])->name('home'); 

Route::get('/kas', [PublicController::class, 'index'])->name('kas.index');

Route::get('/kas/{slug}', [PublicController::class, 'showClass'])->name('kas.show');
Route::get('/student/{id}/transactions', [PublicController::class, 'studentTransactions'])->name('student.transactions');


Auth::routes(['register' => false]);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- MANAJEMEN SISWA ---
    Route::get('/dashboard/students', [StudentController::class, 'index'])->name('admin.students.index');
    Route::post('/dashboard/students', [StudentController::class, 'store'])->name('admin.students.store');

    Route::get('/dashboard/students/{student}/edit', [StudentController::class, 'edit'])->name('admin.students.edit');
    Route::put('/dashboard/students/{student}', [StudentController::class, 'update'])->name('admin.students.update');
    Route::delete('/dashboard/students/{student}', [StudentController::class, 'destroy'])->name('admin.students.destroy');

    Route::get('/dashboard/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');
    Route::post('/dashboard/transactions/in', [TransactionController::class, 'storePemasukan'])->name('admin.transactions.store.in');
    Route::post('/dashboard/transactions/out', [TransactionController::class, 'storePengeluaran'])->name('admin.transactions.store.out');
    Route::get('/dashboard/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('admin.transactions.edit');
    Route::put('/dashboard/transactions/{transaction}', [TransactionController::class, 'update'])->name('admin.transactions.update');
    Route::delete('/dashboard/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('admin.transactions.destroy');
    Route::get('/dashboard/export/excel', [TransactionController::class, 'exportExcel'])->name('admin.export.excel');

    Route::post('/dashboard/students/import', [StudentController::class, 'importExcel'])->name('admin.students.import');

    Route::post('/dashboard/profile/update', [DashboardController::class, 'updateProfile'])->name('admin.profile.update');

    Route::delete('/dashboard/profile/avatar', [DashboardController::class, 'deleteAvatar'])->name('admin.profile.delete_avatar');

    Route::get('/dashboard/settings', [TreasurerSettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('/dashboard/settings', [TreasurerSettingsController::class, 'update'])->name('admin.settings.update');

});