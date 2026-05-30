<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\LabController;

Route::get('/', [JadwalController::class, 'welcome']);

Route::get('/login', function (){
    return view('auth.login');
    })->name('login');


Route::get('/spv/jadwal', [App\Http\Controllers\JadwalController::class, 'manajemenJadwal'])->name('spv.jadwal');
Route::post('/spv/jadwal/simpen', [App\Http\Controllers\JadwalController::class, 'store'])->name('spv.store');
Route::get('/spv/jadwal/edit/{id_jadwal}', [App\Http\Controllers\JadwalController::class, 'editJadwal'])->name('spv.edit');
Route::put('/jadwal/update/{id_jadwal}', [JadwalController::class, 'update'])->name('spv.update');
Route::delete('/spv/jadwal/hapus/{id_jadwal}', [App\Http\Controllers\JadwalController::class, 'destroy'])->name('spv.delete');
Route::get('/spv/dashboard', [App\Http\Controllers\JadwalController::class, 'dbdepan'])->name('spv.dashboard');

//LAB//
Route::get('/spv/lab', [App\Http\Controllers\LabController::class, 'manajemenLab'])->name('spv.lab');
Route::post('/spv/lab/simpen', [App\Http\Controllers\LabController::class, 'buatLab'])->name('spv.buatLab');
Route::put('/spv/{id_lab}', [App\Http\Controllers\LabController::class, 'update'])->name('spv.lab.update');
Route::delete('/spv/lab/hapus/{id_lab}', [App\Http\Controllers\LabController::class, 'destroy'])->name('spv.lab.delete');