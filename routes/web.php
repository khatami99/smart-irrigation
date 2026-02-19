<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IrrigationController;

Route::get('/irrigation', [IrrigationController::class, 'index']);

Route::get('/', function () {
    return view('welcome');
});
