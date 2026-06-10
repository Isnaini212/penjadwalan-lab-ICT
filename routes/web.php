<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\AsistenController;
use App\Http\Controllers\TvController;
use App\Http\Controllers\MhsController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\SetujuController;
use App\Http\Controllers\AkunController;

Route::get('/', [JadwalController::class, 'welcome']);
Route::get('/minggu', [JadwalController::class, 'minggu']);
Route::get('/minggu/cetak', [JadwalController::class, 'cetakMinggu'])->name('cetak');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:spv'])->group(function () {
    
    // DASHBOARD
    Route::get('/spv/dashboard', [JadwalController::class, 'dashboard'])->name('spv.dashboard');

    // JADWAL
    Route::get('/spv/jadwal', [JadwalController::class, 'manajemenJadwal'])->name('spv.jadwal');
    Route::post('/spv/jadwal/simpen', [JadwalController::class, 'store'])->name('spv.store');
    // Route::get('/spv/jadwal/edit/{id_jadwal}', [JadwalController::class, 'editJadwal'])->name('spv.edit');
    Route::put('/jadwal/update/{id_jadwal}', [JadwalController::class, 'update'])->name('spv.update');
    Route::delete('/spv/jadwal/hapus/{id_jadwal}', [JadwalController::class, 'destroy'])->name('spv.delete');
    Route::delete('/spv/jadwal/bersih', [JadwalController::class, 'bersihin'])->name('bersih');
    Route::post('/spv/import-excel', [JadwalController::class, 'importExcel'])->name('schedule.import'); 

    // LAB
    Route::get('/spv/lab', [LabController::class, 'manajemenLab'])->name('spv.lab');
    Route::post('/spv/lab/simpen', [LabController::class, 'buatLab'])->name('spv.buatLab');
    Route::put('/spv/{id_lab}', [LabController::class, 'update'])->name('spv.lab.update');
    Route::delete('/spv/lab/hapus/{id_lab}', [LabController::class, 'destroy'])->name('spv.lab.delete');

    // ASISTEN
    Route::get('/spv/asisten', [AsistenController::class, 'jadwalAsisten'])->name('spv.asisten');
    Route::post('/spv/asisten', [AsistenController::class, 'storeAsisten'])->name('asisten.store');
    Route::patch('/spv/asisten/{id}', [AsistenController::class, 'updateAsisten'])->name('asisten.update');
    Route::delete('/spv/asisten/{id}', [AsistenController::class, 'destroyAsisten'])->name('asisten.destroy');
    Route::delete('/spv/asisten-clear', [AsistenController::class, 'clearAsistenSchedule'])->name('asisten.clear');
    Route::post('/spv/import-asisten', [AsistenController::class, 'importAsistenExcel'])->name('spv.importAsisten');

    // MANAJEMEN ASISTEN & MATRIX
    Route::get('/spv/jasis', [AsistenController::class, 'manajemenasisten'])->name('spv.jasis'); 
    Route::post('/spv/matrix-schedule/update', [AsistenController::class, 'updateMatrixRA'])->name('schedule.matrix.update');

    // APPROVE BOOKING
    Route::prefix('spv/booking')->group(function () {
        Route::get('/', [SetujuController::class, 'index'])->name('spv.booking.index');
        Route::patch('/update-lab/{type}/{id}', [SetujuController::class, 'updateLab'])->name('spv.booking.update_lab');
        Route::post('/approve/{type}/{id}', [SetujuController::class, 'approve'])->name('spv.booking.approve');
        Route::post('/reject/{type}/{id}', [SetujuController::class, 'reject'])->name('spv.booking.reject');
    });

    // TV SPV MANAGEMENT
    Route::prefix('spv/tv')->group(function () {
        Route::get('/', [TvController::class, 'manageTv'])->name('spv.tv.index');
        Route::post('/text', [TvController::class, 'updateTvText'])->name('spv.tv.text');
        Route::post('/slide', [TvController::class, 'uploadTvSlide'])->name('spv.tv.slide.upload');
        Route::delete('/slide/{id}', [TvController::class, 'deleteTvSlide'])->name('spv.tv.slide.delete');
    });

    
    
    //Akun///
    Route::get('/spv/akun', [AkunController::class, 'buat'])->name('akun');
    
    Route::post('/spv/akun', [AkunController::class, 'simpen'])->name('akun');
    
});
    Route::get('/tv', [TvController::class, 'tvSon'])->name('tv.display');



Route::middleware(['auth', 'role:ormawa'])->group(function () {
Route::prefix('ormawa')->group(function () {
    Route::get('/booking', [MhsController::class, 'index'])->name('ormawa.booking.index');
    Route::post('/booking/store', [MhsController::class, 'store'])->name('ormawa.booking.store');
});});

Route::middleware(['auth', 'role:dosen'])->group(function () {
Route::prefix('dosen')->group(function () {
    Route::get('/booking', [DosenController::class, 'index'])->name('dosen.booking.index');
    Route::post('/booking/store', [DosenController::class, 'store'])->name('dosen.booking.store');
    Route::post('/booking/check-labs', [DosenController::class, 'checkAvailableLabs'])->name('dosen.booking.check_labs');
}); });

Route::middleware(['auth', 'role:asisten'])->group(function () {
    Route::get('/asisten/jadwal', [AsistenController::class, 'inputMatrix'])->name('asisten.jadwal');
    Route::post('/asisten/simpan-jadwal', [AsistenController::class, 'storsis'])->name('asisten.jadwal.store');
    Route::delete('/asisten/jadwal/{id}', [AsistenController::class, 'hapusJadwal'])->name('asisten.jadwal.delete');
    Route::get('/asisten/cetak-matriks', [\App\Http\Controllers\AsistenController::class, 'cetakMatriks'])->name('asisten.cetak_matriks');
});

require __DIR__.'/auth.php';
