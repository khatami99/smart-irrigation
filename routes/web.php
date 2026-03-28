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
use App\Http\Controllers\PetaController;
use App\Http\Controllers\DaerahIrigasiController;
use App\Http\Controllers\SaluranController;
use App\Http\Controllers\BlangkoO01Controller;
use App\Http\Controllers\KebutuhanAirController;

Route::get('/', function () {
    $latest = \App\Models\IrrigationData::orderBy('tanggal', 'desc')->first();

    $latestKebutuhan = $latest ? round($latest->kebutuhan_air, 2) : null;
    $latestEto       = $latest ? round($latest->eto, 2) : null;

    $stats  = \App\Models\IrrigationData::selectRaw('AVG(kebutuhan_air) as avg, STDDEV(kebutuhan_air) as stddev')->first();
    $avg    = (float) ($stats->avg ?? 5);
    $stddev = (float) ($stats->stddev ?? 1.5);

    if ($latestKebutuhan > $avg + ($stddev * 0.5)) {
        $statusHari = 'tinggi';
    } elseif ($latestKebutuhan >= $avg - ($stddev * 0.5)) {
        $statusHari = 'normal';
    } else {
        $statusHari = 'rendah';
    }

    return view('welcome', compact('latestKebutuhan', 'latestEto', 'statusHari'));
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Route::get('/irrigation', [IrrigationController::class, 'index'])->name('irrigation.index');
    Route::get('/dashboard', [IrrigationController::class, 'index'])->name('dashboard');
    Route::get('/irrigation', [IrrigationController::class, 'dataIklim'])->name('irrigation.index');
    Route::get('/irrigation/import', [IrrigationController::class, 'importForm'])->name('irrigation.import');
    Route::post('/irrigation/import', [IrrigationController::class, 'importCsv'])->name('irrigation.import.post');
    Route::get('/irrigation/create', [IrrigationController::class, 'create'])->name('irrigation.create');
    Route::post('/irrigation', [IrrigationController::class, 'store'])->name('irrigation.store');
    Route::get('/irrigation/{irrigationData}/edit', [IrrigationController::class, 'edit'])->name('irrigation.edit');
    Route::put('/irrigation/{irrigationData}', [IrrigationController::class, 'update'])->name('irrigation.update');
    Route::delete('/irrigation/{irrigationData}', [IrrigationController::class, 'destroy'])->name('irrigation.destroy');
    Route::resource('petak', PetakController::class)->except(['show']);
    Route::resource('daerah_irigasi', DaerahIrigasiController::class)->except(['show']);
    Route::resource('saluran', SaluranController::class)->except(['show']);
    Route::resource('musim-tanam', MusimTanamController::class)->except(['show']);
    Route::resource('blangko-op', BlangkoOpController::class)->except(['show']);
    Route::get('/api/daerah-irigasi/{daerahIrigasi}/petaks', [DaerahIrigasiController::class, 'getPetaksByDI'])
        ->name('api.daerah-irigasi.petaks');
    Route::get('/grafik', [GrafikController::class, 'index'])->name('grafik.index');
    Route::get('/grafik/data', [GrafikController::class, 'data'])->name('grafik.data');
    Route::get('/rtt/daerah-irigasi/{daerahIrigasi}', [RttController::class, 'showByDI'])->name('rtt.by-di');
    Route::resource('rtt', RttController::class)->except(['show']);
    Route::resource('blangko-o01', BlangkoO01Controller::class);
    Route::get('/kebutuhan-air', [KebutuhanAirController::class, 'index'])
        ->name('kebutuhan-air.index')
        ->middleware('permission:view blangko-op');

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

    Route::middleware('permission:view peta')->group(function () {
        Route::get('/peta', [PetaController::class, 'index'])->name('peta.index');
        Route::get('/peta/geojson', [PetaController::class, 'getGeoJson'])->name('peta.geojson');
    });

    Route::middleware('permission:create peta')->group(function () {
        Route::post('/peta/layer', [PetaController::class, 'storeLayer'])->name('peta.layer.store');
        Route::post('/peta/feature', [PetaController::class, 'storeFeature'])->name('peta.feature.store');
        Route::post('/peta/import', [PetaController::class, 'importGeoJson'])->name('peta.import');
    });

    Route::middleware('permission:edit peta')->group(function () {
        Route::put('/peta/layer/{layer}', [PetaController::class, 'updateLayer'])->name('peta.layer.update');
        Route::put('/peta/feature/{feature}', [PetaController::class, 'updateFeature'])->name('peta.feature.update');
    });

    Route::middleware('permission:delete peta')->group(function () {
        Route::delete('/peta/layer/{layer}', [PetaController::class, 'destroyLayer'])->name('peta.layer.destroy');
        Route::delete('/peta/feature/{feature}', [PetaController::class, 'destroyFeature'])->name('peta.feature.destroy');
    });
});
