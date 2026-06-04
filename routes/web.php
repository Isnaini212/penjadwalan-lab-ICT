<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\AsistenController;
use App\Http\Controllers\TvController;


Route::get('/', [JadwalController::class, 'welcome']);

Route::get('/login', function (){
    return view('auth.login');
    })->name('login');

//jadwal//
Route::get('/spv/jadwal', [App\Http\Controllers\JadwalController::class, 'manajemenJadwal'])->name('spv.jadwal');
Route::post('/spv/jadwal/simpen', [App\Http\Controllers\JadwalController::class, 'store'])->name('spv.store');
Route::get('/spv/jadwal/edit/{id_jadwal}', [App\Http\Controllers\JadwalController::class, 'editJadwal'])->name('spv.edit');
Route::put('/jadwal/update/{id_jadwal}', [JadwalController::class, 'update'])->name('spv.update');
Route::delete('/spv/jadwal/hapus/{id_jadwal}', [App\Http\Controllers\JadwalController::class, 'destroy'])->name('spv.delete');
Route::delete('/spv/jadwal/bersih', [JadwalController::class, 'bersihin'])->name('bersih');
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
Route::get('/tv', [TvController::class, 'tvSon'])->name('tv.display');

// Kelompok rute kontrol manajemen TV khusus untuk SPV
Route::prefix('spv/tv')->group(function () {
    // Halaman dashboard remote control TV
    Route::get('/', [TvController::class, 'manageTv'])->name('spv.tv.index');
    
    // Proses pembaruan teks agenda berjalan
    Route::post('/text', [TvController::class, 'updateTvText'])->name('spv.tv.text');
    
    // Proses unggah berkas gambar slide baru
    Route::post('/slide', [TvController::class, 'uploadTvSlide'])->name('spv.tv.slide.upload');
    
    // Proses hapus berkas gambar slide berdasarkan ID
    Route::delete('/slide/{id}', [TvController::class, 'deleteTvSlide'])->name('spv.tv.slide.delete');
});

Route::get('/asisten/input', [AsistenController::class, 'inputMatrix'])->name('schedule.matrix');
Route::post('/asisten/update', [AsistenController::class, 'storsis'])->name('simput');
