<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/spv/jadwal', [App\Http\Controllers\JadwalController::class, 'index'])->name('spv.jadwal');
Route::post('/spv/jadwal/simpen', [App\Http\Controllers\JadwalController::class, 'store'])->name('spv.store');
Route::get('/spv/jadwal/edit/{id_jadwal}', [App\Http\Controllers\JadwalController::class, 'editJadwal'])->name('spv.edit');
Route::put('/spv/jadwal/update/{id_jadwal}', [App\Http\Controllers\JadwalController::class, 'updateJadwal'])->name('spv.update');
route::delete('/spv/jadwal/hapus/{id_jadwal}', [App\Http\Controllers\JadwalController::class, 'hapusJadwal'])->name('spv.delete');
Route::get('/spv/dashboard', [App\Http\Controllers\JadwalController::class, 'dbdepan'])->name('spv.dashboard');
