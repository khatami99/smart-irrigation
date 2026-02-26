<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IrrigationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PetakController;
use App\Http\Controllers\MusimTanamController;
use App\Http\Controllers\BlangkoOpController;
use App\Http\Controllers\GrafikController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/irrigation', [IrrigationController::class, 'index'])->name('irrigation');
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
});
