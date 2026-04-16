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
use App\Http\Controllers\BlangkoDipController;
use App\Http\Controllers\BlangkoDirController;

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

    Route::prefix('blangko-dip')->name('blangko-dip.')->middleware('permission:view blangko-op')->group(function () {
        // O-01
        Route::get('/o01', [BlangkoDipController::class, 'o01Index'])->name('o01.index');
        Route::get('/o01/create', [BlangkoDipController::class, 'o01Create'])->name('o01.create')->middleware('permission:create blangko-op');
        Route::post('/o01', [BlangkoDipController::class, 'o01Store'])->name('o01.store')->middleware('permission:create blangko-op');
        Route::get('/o01/{o01}', [BlangkoDipController::class, 'o01Show'])->name('o01.show');
        Route::get('/o01/{o01}/edit', [BlangkoDipController::class, 'o01Edit'])->name('o01.edit')->middleware('permission:edit blangko-op');
        Route::put('/o01/{o01}', [BlangkoDipController::class, 'o01Update'])->name('o01.update')->middleware('permission:edit blangko-op');
        Route::delete('/o01/{o01}', [BlangkoDipController::class, 'o01Destroy'])->name('o01.destroy')->middleware('permission:delete blangko-op');

        // O-05
        Route::get('/o05', [BlangkoDipController::class, 'o05'])->name('o05');
        Route::get('/o05/pdf', [BlangkoDipController::class, 'o05Pdf'])->name('o05.pdf');
    });

    Route::get('/kebutuhan-air', [KebutuhanAirController::class, 'index'])
        ->name('kebutuhan-air.index')
        ->middleware('permission:view blangko-op');
    Route::get('/blangko-dip/o05', [BlangkoDipController::class, 'o05'])->name('blangko-dip.o05')->middleware('permission:view blangko-op');
    Route::get('/blangko-dip/o05/pdf', [BlangkoDipController::class, 'o05Pdf'])->name('blangko-dip.o05.pdf')->middleware('permission:view blangko-op');

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

    Route::prefix('blangko-dir')->name('blangko-dir.')->middleware('permission:view blangko-op')->group(function () {
        Route::get('/o09', [BlangkoDirController::class, 'o09Index'])->name('o09.index');
        Route::get('/o09/create', [BlangkoDirController::class, 'o09Create'])->name('o09.create')->middleware('permission:create blangko-op');
        Route::post('/o09', [BlangkoDirController::class, 'o09Store'])->name('o09.store')->middleware('permission:create blangko-op');
        Route::get('/o09/pdf', [BlangkoDirController::class, 'o09Pdf'])->name('o09.pdf');
    });

    Route::get('/profil', [AuthController::class, 'profilEdit'])->name('profil.edit');
    Route::put('/profil', [AuthController::class, 'profilUpdate'])->name('profil.update');
});
