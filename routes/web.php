<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\AsistenController;


Route::get('/', [JadwalController::class, 'welcome']);

Route::get('/login', function (){
    return view('auth.login');
    })->name('login');

//login//
Route::get('/spv/jadwal', [App\Http\Controllers\JadwalController::class, 'manajemenJadwal'])->name('spv.jadwal');
Route::post('/spv/jadwal/simpen', [App\Http\Controllers\JadwalController::class, 'store'])->name('spv.store');
Route::get('/spv/jadwal/edit/{id_jadwal}', [App\Http\Controllers\JadwalController::class, 'editJadwal'])->name('spv.edit');
Route::put('/jadwal/update/{id_jadwal}', [JadwalController::class, 'update'])->name('spv.update');
Route::delete('/spv/jadwal/hapus/{id_jadwal}', [App\Http\Controllers\JadwalController::class, 'destroy'])->name('spv.delete');

///import jdwl//
Route::post('/spv/', [JadwalController::class, 'importExcel'])->name('schedule.import');



//LAB//
Route::get('/spv/lab', [App\Http\Controllers\LabController::class, 'manajemenLab'])->name('spv.lab');
Route::post('/spv/lab/simpen', [App\Http\Controllers\LabController::class, 'buatLab'])->name('spv.buatLab');
Route::put('/spv/{id_lab}', [App\Http\Controllers\LabController::class, 'update'])->name('spv.lab.update');
Route::delete('/spv/lab/hapus/{id_lab}', [App\Http\Controllers\LabController::class, 'destroy'])->name('spv.lab.delete');

//ASISTEN//
Route::get('/spv/asisten', [AsistenController::class, 'jadwalAsisten'])->name('spv.asisten');
Route::delete('/spv/asisten/clear', [AsistenController::class, 'clearAsistenSchedule'])->name('asisten.clear');
Route::patch('/spv/asisten/{id}', [AsistenController::class, 'updateAsisten'])->name('asisten.update');
Route::delete('/spv/asisten/{id}', [AsistenController::class, 'destroyAsisten'])->name('asisten.destroy');
Route::post('/spv/asisten', [AsistenController::class, 'storeAsisten'])->name('asisten.store');
Route::post('/spv/import-asisten', [AsistenController::class, 'importAsistenExcel'])->name('spv.importAsisten');

//ngasih jadwal asisten//
Route::get('/spv/jasis', [AsistenController::class, 'manajemenasisten'])->name('spv.asisten');
Route::delete('/spv/asisten/clear', [AsistenController::class, 'clearAsistenSchedule'])->name('asisten.clear');
Route::post('/spv/import-asisten', [AsistenController::class, 'importAsistenExcel'])->name('spv.importAsisten');
Route::post('/spv/matrix-schedule/update', [AsistenController::class, 'updateMatrixRA'])->name('schedule.matrix.update');

//dashboard//
Route::get('/spv/dashboard', [App\Http\Controllers\JadwalController::class, 'dashboard'])->name('spv.dashboard');

//tv//
Route::get('/tv', [App\Http\Controllers\TvController::class, 'tvSon'])->name('tv');
