<?php

namespace App\Http\Controllers;

use App\Models\IrrigationData;
use App\Services\IrrigationDataService;
use Illuminate\Http\Request;

class IrrigationController extends Controller
{
    public function index()
    {
        $pythonPath = base_path('predict.py');
        $output = shell_exec("python $pythonPath");

        preg_match('/Prediksi Kebutuhan Air Besok: ([\d.]+)/', $output, $matches);
        $forecast = $matches[1] ?? '0.0';

        if ($forecast > 50) {
            $recommendation = ['status' => 'Kritis', 'color' => 'text-red-500', 'msg' => 'Tanah diprediksi sangat kering. Segera siapkan volume air ekstra!'];
        } elseif ($forecast >= 20) {
            $recommendation = ['status' => 'Normal', 'color' => 'text-blue-400', 'msg' => 'Kebutuhan air stabil. Lakukan penyiraman sesuai jadwal rutin.'];
        } else {
            $recommendation = ['status' => 'Hemat', 'color' => 'text-emerald-400', 'msg' => 'Kebutuhan air rendah. Anda bisa menghemat penggunaan pompa.'];
        }

        $allData   = IrrigationData::orderBy('tanggal', 'asc')
                        ->where('tanggal', '>=', now()->subDays(30)->format('Y-m-d'))
                        ->get();
        $labels    = $allData->pluck('tanggal');
        $kebutuhan = $allData->pluck('kebutuhan_air');
        $tableData = IrrigationData::orderBy('tanggal', 'desc')->paginate(10);

        return view('irrigation.index', compact('labels', 'kebutuhan', 'forecast', 'recommendation', 'tableData'));
    }

    public function dataIklim()
    {
        $tableData = IrrigationData::orderBy('tanggal', 'desc')->paginate(15);
        return view('irrigation.data', compact('tableData'));
    }

    public function create()
    {
        return view('irrigation.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required|date|unique:irrigation_data,tanggal',
            'suhu_max'         => 'required|numeric|min:0|max:60',
            'suhu_min'         => 'required|numeric|min:0|max:60',
            'kelembaban'       => 'required|numeric|min:0|max:100',
            'kecepatan_angin'  => 'required|numeric|min:0',
            'radiasi_matahari' => 'required|numeric|min:0',
            'kc'               => 'required|numeric|min:0|max:2',
            'curah_hujan'      => 'required|numeric|min:0',
        ], [
            'tanggal.unique' => 'Data untuk tanggal ini sudah ada.',
        ]);

        $eto          = IrrigationDataService::hitungETo($request->suhu_max, $request->suhu_min, $request->kelembaban, $request->kecepatan_angin, $request->radiasi_matahari);
        $etc          = IrrigationDataService::hitungETc($eto, $request->kc);
        $kebutuhanAir = IrrigationDataService::hitungKebutuhanAir($etc, $request->curah_hujan);

        IrrigationData::create([
            'tanggal'          => $request->tanggal,
            'suhu_max'         => $request->suhu_max,
            'suhu_min'         => $request->suhu_min,
            'kelembaban'       => $request->kelembaban,
            'kecepatan_angin'  => $request->kecepatan_angin,
            'radiasi_matahari' => $request->radiasi_matahari,
            'kc'               => $request->kc,
            'curah_hujan'      => $request->curah_hujan,
            'eto'              => $eto,
            'etc'              => $etc,
            'kebutuhan_air'    => $kebutuhanAir,
        ]);

        return redirect()->route('irrigation.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function edit(IrrigationData $irrigationData)
    {
        return view('irrigation.edit', compact('irrigationData'));
    }

    public function update(Request $request, IrrigationData $irrigationData)
    {
        $request->validate([
            'tanggal'          => 'required|date|unique:irrigation_data,tanggal,' . $irrigationData->id,
            'suhu_max'         => 'required|numeric|min:0|max:60',
            'suhu_min'         => 'required|numeric|min:0|max:60',
            'kelembaban'       => 'required|numeric|min:0|max:100',
            'kecepatan_angin'  => 'required|numeric|min:0',
            'radiasi_matahari' => 'required|numeric|min:0',
            'kc'               => 'required|numeric|min:0|max:2',
            'curah_hujan'      => 'required|numeric|min:0',
        ], [
            'tanggal.unique' => 'Data untuk tanggal ini sudah ada.',
        ]);

        $eto          = IrrigationDataService::hitungETo($request->suhu_max, $request->suhu_min, $request->kelembaban, $request->kecepatan_angin, $request->radiasi_matahari);
        $etc          = IrrigationDataService::hitungETc($eto, $request->kc);
        $kebutuhanAir = IrrigationDataService::hitungKebutuhanAir($etc, $request->curah_hujan);

        $irrigationData->update([
            'tanggal'          => $request->tanggal,
            'suhu_max'         => $request->suhu_max,
            'suhu_min'         => $request->suhu_min,
            'kelembaban'       => $request->kelembaban,
            'kecepatan_angin'  => $request->kecepatan_angin,
            'radiasi_matahari' => $request->radiasi_matahari,
            'kc'               => $request->kc,
            'curah_hujan'      => $request->curah_hujan,
            'eto'              => $eto,
            'etc'              => $etc,
            'kebutuhan_air'    => $kebutuhanAir,
        ]);

        return redirect()->route('irrigation.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(IrrigationData $irrigationData)
    {
        $irrigationData->delete();
        return redirect()->route('irrigation.index')->with('success', 'Data berhasil dihapus!');
    }
}
