<?php

namespace App\Http\Controllers;

use App\Models\IrrigationData;

class IrrigationController extends Controller
{
    public function index()
    {
        $data = IrrigationData::orderBy('tanggal', 'asc')->get();

        $labels = $data->pluck('tanggal');
        $kebutuhan = $data->pluck('kebutuhan_air');

        return view('irrigation.index', compact('data', 'labels', 'kebutuhan'));
    }
}
