<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GiftController; // Jangan lupa import

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

// Halaman utama (form pembuatan hadiah)
Route::get('/', [GiftController::class, 'create'])->name('gift.create');

// Menyimpan data hadiah dari form
Route::post('/gifts', [GiftController::class, 'store'])->name('gift.store');

// Menampilkan hadiah berdasarkan slug unik
Route::get('/g/{slug}', [GiftController::class, 'show'])->name('gift.show');