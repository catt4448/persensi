<?php

use Illuminate\Support\Facades\Route;

/* Redirect awal */
Route::get('/', function () {
    return redirect('/login');
});

/* Smart dashboard */
Route::middleware(['auth'])->get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect('/admin/dashboard');
    }
    return redirect('/user/dashboard');
});

/* Admin */
Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    });
});

/* User */
Route::middleware(['auth','role:user'])->group(function () {
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    });
});

require __DIR__.'/auth.php';
