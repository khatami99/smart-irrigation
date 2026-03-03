<?php
namespace App\Http\Controllers;

use App\Models\MapLayer;
use App\Models\MapFeature;
use App\Models\Petak;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    // ── Halaman utama peta ──────────────────────────────────────
    public function index()
    {
        $layers = MapLayer::with('features.petak')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        $petaks  = Petak::orderBy('kode_petak')->get();
        $allLayers = MapLayer::orderBy('urutan')->get(); // untuk panel manage

        // Semua fitur sebagai GeoJSON untuk Leaflet
        $geoJsonAll = [
            'type'     => 'FeatureCollection',
            'features' => $layers->flatMap(fn($l) => $l->features->map(fn($f) => $f->toGeoJsonFeature()))->values(),
        ];

        return view('peta.index', compact('layers', 'petaks', 'allLayers', 'geoJsonAll'));
    }

    // ── API: ambil semua features sebagai GeoJSON ───────────────
    public function getGeoJson()
    {
        $layers = MapLayer::with('features.petak')
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
        $data = $request->validate([
            'map_layer_id' => 'required|exists:map_layers,id',
            'petak_id'     => 'nullable|exists:petaks,id',
            'nama'         => 'required|string|max:150',
            'deskripsi'    => 'nullable|string',
            'luas_manual'  => 'nullable|numeric|min:0',
            'geojson'      => 'required|array',
            'warna'        => 'nullable|string|size:7',
        ]);

        $feature = MapFeature::create($data);
        // $feature->load('layer', 'petak');
        $feature = MapFeature::with('layer', 'petak')->find($feature->id);

        return response()->json([
            'success' => true,
            'feature' => $feature->toGeoJsonFeature(),
        ]);
    }

    public function updateFeature(Request $request, MapFeature $feature)
    {
        $data = $request->validate([
            'petak_id'    => 'nullable|exists:petaks,id',
            'nama'        => 'required|string|max:150',
            'deskripsi'   => 'nullable|string',
            'luas_manual' => 'nullable|numeric|min:0',
            'geojson'     => 'nullable|array',
            'warna'       => 'nullable|string|size:7',
        ]);

        $feature->update($data);
        $feature->load('layer', 'petak');

        return response()->json([
            'success' => true,
            'feature' => $feature->toGeoJsonFeature(),
        ]);
    }

    public function destroyFeature(MapFeature $feature)
    {
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
