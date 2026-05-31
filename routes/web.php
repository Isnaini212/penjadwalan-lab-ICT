<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JadwalController;

Route::get('/', [JadwalController::class, 'welcome']);

Route::get('/spv/dashboard', function () {
    return view('spv.dashboard');
})->name('spv.dashboard');

Route::get('/login', function (){
    return view('auth.login');
    })->name('login');


Route::get('/spv/jadwal', [App\Http\Controllers\JadwalController::class, 'manajemenJadwal'])->name('spv.jadwal');
Route::post('/spv/jadwal/simpen', [App\Http\Controllers\JadwalController::class, 'store'])->name('spv.store');
Route::get('/spv/jadwal/edit/{id_jadwal}', [App\Http\Controllers\JadwalController::class, 'editJadwal'])->name('spv.edit');
Route::put('/jadwal/update/{id_jadwal}', [JadwalController::class, 'update'])->name('spv.update');
Route::delete('/spv/jadwal/hapus/{id_jadwal}', [App\Http\Controllers\JadwalController::class, 'destroy'])->name('spv.delete');
