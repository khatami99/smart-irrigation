<?php

namespace App\Http\Controllers;

use App\Models\IrrigationData;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil ringkasan data untuk ditampilkan di dashboard
        $totalData = IrrigationData::count();
        $latestData = IrrigationData::orderBy('tanggal', 'desc')->first();

        return view('dashboard', compact('totalData', 'latestData'));
    }
}
