<?php
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KrathongController;

Route::get('/', [KrathongController::class,'show'])->name('krathong.show');
Route::post('/krathongs', [KrathongController::class,'store'])->name('krathongs.store');

// กันเผลอ GET
Route::match(['get','head'], '/krathongs', fn() => abort(405));

Route::get('/kuma/heartbeat/{slug}', function ($slug) {
    $r = Http::timeout(10)->get("https://kuma.pcnone.com/api/status-page/heartbeat/{$slug}");
    abort_unless($r->ok(), $r->status(), $r->body());
    return response($r->body(), 200)
        ->header('Content-Type', 'application/json');
});