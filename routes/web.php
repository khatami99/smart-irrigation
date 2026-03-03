<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IrrigationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PetakController;
use App\Http\Controllers\MusimTanamController;
use App\Http\Controllers\BlangkoOpController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\RttController;
use App\Http\Controllers\LaporanController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Route::get('/irrigation', [IrrigationController::class, 'index'])->name('irrigation.index');
    Route::get('/dashboard', [IrrigationController::class, 'index'])->name('dashboard');
    Route::get('/irrigation', [IrrigationController::class, 'dataIklim'])->name('irrigation.index');
    Route::get('/irrigation/create', [IrrigationController::class, 'create'])->name('irrigation.create');
    Route::post('/irrigation', [IrrigationController::class, 'store'])->name('irrigation.store');
    Route::get('/irrigation/{irrigationData}/edit', [IrrigationController::class, 'edit'])->name('irrigation.edit');
    Route::put('/irrigation/{irrigationData}', [IrrigationController::class, 'update'])->name('irrigation.update');
    Route::delete('/irrigation/{irrigationData}', [IrrigationController::class, 'destroy'])->name('irrigation.destroy');
    Route::resource('petak', PetakController::class)->except(['show']);
    Route::resource('musim-tanam', MusimTanamController::class)->except(['show']);
    Route::resource('blangko-op', BlangkoOpController::class)->except(['show']);
    Route::get('/grafik', [GrafikController::class, 'index'])->name('grafik.index');
    Route::get('/grafik/data', [GrafikController::class, 'data'])->name('grafik.data');
    Route::resource('rtt', RttController::class)->except(['show']);

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf/data-iklim',  [LaporanController::class, 'pdfDataIklim'])->name('laporan.pdf.data-iklim');
    Route::get('/laporan/pdf/blangko-op',  [LaporanController::class, 'pdfBlangkoOp'])->name('laporan.pdf.blangko-op');
    Route::get('/laporan/pdf/rtt',         [LaporanController::class, 'pdfRtt'])->name('laporan.pdf.rtt');
    Route::get('/laporan/pdf/rekap',       [LaporanController::class, 'pdfRekap'])->name('laporan.pdf.rekap');
    Route::get('/laporan/excel/data-iklim',[LaporanController::class, 'excelDataIklim'])->name('laporan.excel.data-iklim');
    Route::get('/laporan/excel/blangko-op',[LaporanController::class, 'excelBlangkoOp'])->name('laporan.excel.blangko-op');
    Route::get('/laporan/excel/rtt',       [LaporanController::class, 'excelRtt'])->name('laporan.excel.rtt');
    Route::get('/laporan/excel/rekap',     [LaporanController::class, 'excelRekap'])->name('laporan.excel.rekap');
});
