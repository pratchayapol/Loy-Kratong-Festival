<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KrathongController;

Route::get('/', [KrathongController::class, 'show'])->name('krathong.show');
// บันทึกกระทง (Ajax)
Route::post('/krathongs', [KrathongController::class, 'store'])->name('krathongs.store');

// กันกรณีมีใครเผลอกดไปที่ /krathongs แบบ GET
Route::get('/krathongs', fn() => redirect('/'))->name('krathongs.redirect');