<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KrathongController;

Route::get('/', [KrathongController::class, 'show'])->name('krathong.show');
Route::post('/krathongs', [KrathongController::class,'store'])->name('krathongs.store');
