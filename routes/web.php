<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\SesiController;
use App\Http\Controllers\Admin\KehadiranController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect awal
Route::get('/', function () {
    return redirect('/login');
});

// Smart dashboard - redirect berdasarkan role
Route::middleware(['auth'])->get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('user.dashboard');
})->name('dashboard');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::resource('users', UserController::class);
});
Route::middleware(['auth','role:admin'])->prefix('admin')->group(function () {
    Route::resource('mahasiswa', MahasiswaController::class);
    Route::post('/mahasiswa/import', [MahasiswaController::class, 'import'])->name('mahasiswa.import');
});

// Sesi Routes
Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('sesi', SesiController::class);
    Route::post('sesi/{id}/selesai', [SesiController::class,'selesai'])
        ->name('sesi.selesai');
});

// Kehadiran Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/kehadiran/{sesi}', [KehadiranController::class, 'index'])
        ->name('kehadiran.index');
    Route::post('/kehadiran', [KehadiranController::class, 'store'])
        ->name('kehadiran.store');
    Route::put('/kehadiran/{kehadiran}', [KehadiranController::class, 'update'])
        ->name('kehadiran.update');
});


// User Routes
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', function () {
        return view('user.dashboard');
    })->name('dashboard');
});

require __DIR__.'/auth.php';
