<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AbsensiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Simple API endpoint
Route::get('/hello', function () {
    return response()->json([
        'message' => 'Hello World',
        'status' => 'success',
        'timestamp' => now()->toDateTimeString(),
    ]);
})->name('api.hello');

// Authenticated API routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('api.user');
});

Route::post('/absensi/scan', [AbsensiController::class, 'scan'])
    ->name('api.absensi.scan');
