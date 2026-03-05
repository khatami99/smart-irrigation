<?php
namespace App\Http\Controllers;

use App\Models\MapFeature;
use App\Models\MapLayer;
use App\Models\Petak;
use App\Models\Saluran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\DaerahIrigasi;

class PetaController extends Controller
{
    // ── Halaman utama peta ──────────────────────────────────────
    public function index()
    {
        $layers = MapLayer::with('features.layer', 'features.petak', 'features.saluran')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        $petaks    = Petak::orderBy('kode_petak')->get();
        $allLayers = MapLayer::orderBy('urutan')->get();

        $geoJsonAll = [
            'type'     => 'FeatureCollection',
            'features' => $layers->flatMap(fn($l) => $l->features->map(fn($f) => $f->toGeoJsonFeature()))->values(),
        ];

        return view('peta.index', compact('layers', 'petaks', 'allLayers', 'geoJsonAll'));
    }

    // ── API: ambil semua features sebagai GeoJSON ───────────────
    public function getGeoJson()
    {
        $layers = MapLayer::with('features.petak', 'features.saluran', 'features.layer')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        $features = $layers->flatMap(fn($l) => $l->features->map(fn($f) => $f->toGeoJsonFeature()))->values();

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    // ── Layer CRUD ──────────────────────────────────────────────
    public function storeLayer(Request $request)
    {
        $data = $request->validate([
            'nama'       => 'required|string|max:100',
            'tipe'       => 'required|in:polygon,polyline',
            'kategori'   => 'required|in:daerah_irigasi,petak,saluran',
            'warna'      => 'required|string|size:7',
            'opacity'    => 'nullable|numeric|min:0|max:1',
            'keterangan' => 'nullable|string',
        ]);

        $layer = MapLayer::create($data);
        return response()->json(['success' => true, 'layer' => $layer]);
    }

    public function updateLayer(Request $request, MapLayer $layer)
    {
        $data = $request->validate([
            'nama'       => 'required|string|max:100',
            'kategori'   => 'required|in:daerah_irigasi,petak,saluran',
            'warna'      => 'required|string|size:7',
            'opacity'    => 'nullable|numeric|min:0|max:1',
            'keterangan' => 'nullable|string',
            'is_active'  => 'nullable|boolean',
        ]);

        $layer->update($data);
        return response()->json(['success' => true, 'layer' => $layer]);
    }

    public function destroyLayer(MapLayer $layer)
    {
        $layer->delete();
        return response()->json(['success' => true]);
    }

    // ── Feature CRUD ────────────────────────────────────────────
    public function storeFeature(Request $request)
    {
        $layer = MapLayer::findOrFail($request->map_layer_id);

        Log::info('storeFeature debug', [
            'layer_kategori' => $layer->kategori,
            'kode_petak'     => $request->kode_petak,
            'filled'         => $request->filled('kode_petak'),
        ]);

        $data = $request->validate([
            'map_layer_id'        => 'required|exists:map_layers,id',
            'nama'                => 'required|string|max:150',
            'deskripsi'           => 'nullable|string',
            'luas_manual'         => 'nullable|numeric|min:0',
            'geojson'             => 'required|array',
            'warna'               => 'nullable|string|size:7',
            'kode_petak'          => 'nullable|string|max:20',
            'pintu_air'           => 'nullable|string|max:100',
            'penanggung_jawab'    => 'nullable|string|max:100',
            'status_petak'        => 'nullable|in:aktif,nonaktif',
            'keterangan_petak'    => 'nullable|string',
            'tipe_saluran'        => 'nullable|in:primer,sekunder,tersier',
            'panjang_km'          => 'nullable|numeric|min:0',
            'kondisi_saluran'     => 'nullable|in:baik,sedang,rusak',
            'pj_saluran'          => 'nullable|string|max:100',
            'keterangan_saluran'  => 'nullable|string',
        ]);

        $feature = MapFeature::create([
            'map_layer_id' => $data['map_layer_id'],
            'nama'         => $data['nama'],
            'deskripsi'    => $data['deskripsi'] ?? null,
            'luas_manual'  => $data['luas_manual'] ?? null,
            'geojson'      => $data['geojson'],
            'warna'        => $data['warna'] ?? null,
        ]);

        // ── Auto-sync Daerah Irigasi ──────────────────────────────
        if ($layer->kategori === 'daerah_irigasi') {
            DaerahIrigasi::create([
                'map_feature_id'   => $feature->id,
                'kode'             => $data['kode_di'] ?? 'DI-' . $feature->id,
                'nama'             => $data['nama'],
                'luas_total'       => $data['luas_manual'] ?? null,
                'penanggung_jawab' => $data['pj_di'] ?? null,
                'status'           => 'aktif',
            ]);
        }

        // ── Auto-sync Petak ───────────────────────────────────
        if ($layer->kategori === 'petak' && $request->filled('kode_petak')) {
            Log::info('Masuk kondisi petak sync');
            $petak = Petak::where('kode_petak', $request->kode_petak)->first();
            Log::info('Hasil cek petak', ['existing' => $petak?->id, 'feature_id' => $feature->id]);

            if ($petak) {
                $petak->update([
                    'nama_petak'       => $data['nama'],
                    'luas_area'        => $data['luas_manual'] ?? $petak->luas_area,
                    'pintu_air'        => $data['pintu_air'] ?? $petak->pintu_air,
                    'penanggung_jawab' => $data['penanggung_jawab'] ?? $petak->penanggung_jawab,
                    'status'           => $data['status_petak'] ?? $petak->status,
                    'keterangan'       => $data['keterangan_petak'] ?? $petak->keterangan,
                    'map_feature_id'   => $feature->id,
                ]);
            } else {
                Petak::create([
                    'kode_petak'       => $data['kode_petak'],
                    'nama_petak'       => $data['nama'],
                    'luas_area'        => $data['luas_manual'] ?? 0,
                    'lokasi_wilayah'   => '-',
                    'pintu_air'        => $data['pintu_air'] ?? null,
                    'penanggung_jawab' => $data['penanggung_jawab'] ?? null,
                    'status'           => $data['status_petak'] ?? 'aktif',
                    'keterangan'       => $data['keterangan_petak'] ?? null,
                    'map_feature_id'   => $feature->id,
                ]);
            }
        }

        // ── Auto-sync Saluran ─────────────────────────────────
        if ($layer->kategori === 'saluran' && $request->filled('tipe_saluran')) {
            Saluran::create([
                'map_feature_id'   => $feature->id,
                'nama'             => $data['nama'],
                'tipe'             => $data['tipe_saluran'] ?? 'sekunder',
                'panjang_km'       => $data['panjang_km'] ?? null,
                'kondisi'          => $data['kondisi_saluran'] ?? 'baik',
                'penanggung_jawab' => $data['pj_saluran'] ?? null,
                'keterangan'       => $data['keterangan_saluran'] ?? null,
            ]);
        }

        $feature = MapFeature::with('layer', 'petak', 'saluran')->find($feature->id);

        return response()->json([
            'success' => true,
            'feature' => $feature->toGeoJsonFeature(),
        ]);
    }

    public function updateFeature(Request $request, MapFeature $feature)
    {
        $layer = $feature->layer;

        $data = $request->validate([
            'nama'         => 'required|string|max:150',
            'deskripsi'    => 'nullable|string',
            'luas_manual'  => 'nullable|numeric|min:0',
            'geojson'      => 'nullable|array',
            'warna'        => 'nullable|string|size:7',

            // Field petak
            'kode_petak'       => 'nullable|string|max:20',
            'pintu_air'        => 'nullable|string|max:100',
            'penanggung_jawab' => 'nullable|string|max:100',
            'status_petak'     => 'nullable|in:aktif,nonaktif',
            'keterangan_petak' => 'nullable|string',

            // Field saluran
            'tipe_saluran'       => 'nullable|in:primer,sekunder,tersier',
            'panjang_km'         => 'nullable|numeric|min:0',
            'kondisi_saluran'    => 'nullable|in:baik,sedang,rusak',
            'pj_saluran'         => 'nullable|string|max:100',
            'keterangan_saluran' => 'nullable|string',
        ]);

        // Update feature
        $feature->update([
            'nama'        => $data['nama'],
            'deskripsi'   => $data['deskripsi'] ?? null,
            'luas_manual' => $data['luas_manual'] ?? null,
            'geojson'     => $data['geojson'] ?? $feature->geojson,
            'warna'       => $data['warna'] ?? null,
        ]);

        // ── Auto-sync update daerah irigasi ──────────────────────────────────
        if ($layer->kategori === 'daerah_irigasi') {
            $di = DaerahIrigasi::where('map_feature_id', $feature->id)->first();
            if ($di) {
                $di->update([
                    'nama'             => $data['nama'],
                    'luas_total'       => $data['luas_manual'] ?? $di->luas_total,
                    'penanggung_jawab' => $data['pj_di'] ?? $di->penanggung_jawab,
                ]);
            }
        }

        // ── Auto-sync update petak ──────────────────────────────────
        if ($layer->kategori === 'petak') {
            $petak = Petak::where('map_feature_id', $feature->id)->first();
            if ($petak) {
                $petak->update([
                    'nama_petak'       => $data['nama'],
                    'luas_area'        => $data['luas_manual'] ?? $petak->luas_area,
                    'pintu_air'        => $data['pintu_air'] ?? $petak->pintu_air,
                    'penanggung_jawab' => $data['penanggung_jawab'] ?? $petak->penanggung_jawab,
                    'status'           => $data['status_petak'] ?? $petak->status,
                    'keterangan'       => $data['keterangan_petak'] ?? $petak->keterangan,
                ]);
            }
        }

        // ── Auto-sync update saluran ──────────────────────────────────
        if ($layer->kategori === 'saluran') {
            $saluran = Saluran::where('map_feature_id', $feature->id)->first();
            if ($saluran) {
                $saluran->update([
                    'nama'             => $data['nama'],
                    'tipe'             => $data['tipe_saluran'] ?? $saluran->tipe,
                    'panjang_km'       => $data['panjang_km'] ?? $saluran->panjang_km,
                    'kondisi'          => $data['kondisi_saluran'] ?? $saluran->kondisi,
                    'penanggung_jawab' => $data['pj_saluran'] ?? $saluran->penanggung_jawab,
                    'keterangan'       => $data['keterangan_saluran'] ?? $saluran->keterangan,
                ]);
            }
        }

        $feature->load('layer', 'petak');

        return response()->json([
            'success' => true,
            'feature' => $feature->toGeoJsonFeature(),
        ]);
    }

    public function destroyFeature(MapFeature $feature)
    {
        // Soft delete daerah irigasi/petak/saluran terkait
        DaerahIrigasi::where('map_feature_id', $feature->id)->delete();
        Petak::where('map_feature_id', $feature->id)->update(['map_feature_id' => null]);
        Saluran::where('map_feature_id', $feature->id)->delete();

        $feature->delete();
        return response()->json(['success' => true]);
    }

    // ── Import GeoJSON dari file ────────────────────────────────
    public function importGeoJson(Request $request)
    {
        $request->validate([
            'map_layer_id' => 'required|exists:map_layers,id',
            'file'         => 'required|file|mimes:json,geojson|max:5120',
        ]);

        $layer   = MapLayer::findOrFail($request->map_layer_id);
        $content = json_decode(file_get_contents($request->file->getRealPath()), true);

        if (!isset($content['features'])) {
            return response()->json(['success' => false, 'message' => 'Format GeoJSON tidak valid'], 422);
        }

        $count = 0;
        foreach ($content['features'] as $f) {
            $props = $f['properties'] ?? [];
            MapFeature::create([
                'map_layer_id' => $layer->id,
                'nama'         => $props['nama'] ?? $props['name'] ?? 'Feature ' . (++$count),
                'deskripsi'    => $props['deskripsi'] ?? $props['description'] ?? null,
                'luas_manual'  => $props['luas'] ?? $props['area'] ?? null,
                'geojson'      => $f['geometry'],
            ]);
            $count++;
        }

        return response()->json(['success' => true, 'imported' => $count]);
    }
}
