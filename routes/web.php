<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\SesiController;
use App\Http\Controllers\Admin\KehadiranController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\DeviceController;
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
        $totalUsers = \App\Models\User::where('role', 'user')->count();
        $totalMahasiswa = \App\Models\Mahasiswa::count();
        $totalSesiAktif = \App\Models\Sesi::where('status', 'aktif')->count();
        
        // Kehadiran hari ini
        $kehadiranHariIni = \App\Models\Kehadiran::whereDate('created_at', today())
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();
        
        // Total sesi
        $totalSesi = \App\Models\Sesi::count();
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalMahasiswa',
            'totalSesiAktif',
            'kehadiranHariIni',
            'totalSesi'
        ));
    })->name('dashboard');
    Route::get('/pengaturan', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/pengaturan', [ProfileController::class, 'update'])->name('profile.update');
});
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::resource('users', UserController::class);
});
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('devices', DeviceController::class);
});
Route::middleware(['auth','role:admin'])->prefix('admin')->group(function () {
    Route::get('/mahasiswa/export/pdf', [MahasiswaController::class, 'exportPdf'])->name('mahasiswa.exportPdf');
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
    Route::get('/kehadiran/{sesi}/export/pdf', [KehadiranController::class, 'exportPdf'])
        ->name('kehadiran.exportPdf');
    Route::post('/kehadiran', [KehadiranController::class, 'store'])
        ->name('kehadiran.store');
    Route::put('/kehadiran/{sesi}/mahasiswa/{mahasiswa}', [KehadiranController::class, 'update'])
        ->name('kehadiran.update');
    Route::put('/kehadiran/{sesi}/bulk', [KehadiranController::class, 'bulkUpdate'])
        ->name('kehadiran.bulkUpdate');
});


// User Routes
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', function () {
        return view('user.dashboard');
    })->name('dashboard');
});

require __DIR__.'/auth.php';
