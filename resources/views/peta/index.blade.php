@extends('layouts.app')

@section('title', 'Peta Irigasi')
@section('page-title', 'Peta Daerah Irigasi')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
<style>
/* ── Root & Layout ───────────────────────────────────────── */
#peta-wrapper {
    display: flex;
    gap: 1.25rem;
    height: calc(100vh - 200px);
    min-height: 520px;
}

#map {
    flex: 1;
    border-radius: 16px;
    border: 1.5px solid var(--border);
    box-shadow: 0 4px 24px rgba(61,43,31,.08);
    z-index: 1;
}

#sidebar {
    width: 280px;
    display: flex;
    flex-direction: column;
    gap: .875rem;
    overflow-y: auto;
    padding-right: 2px;
}

#sidebar::-webkit-scrollbar { width: 4px; }
#sidebar::-webkit-scrollbar-track { background: transparent; }
#sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

/* ── Panel Card ─────────────────────────────────────────── */
.panel-card {
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 1rem 1.1rem;
    box-shadow: 0 2px 12px rgba(61,43,31,.05);
}

.panel-title {
    font-family: 'Fraunces', serif;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: var(--textlt);
    margin-bottom: .75rem;
    padding-bottom: .6rem;
    border-bottom: 1.5px solid var(--border);
    display: flex;
    align-items: center;
    gap: .4rem;
}

/* ── Layer Item ─────────────────────────────────────────── */
.layer-item {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .5rem .6rem;
    border-radius: 8px;
    cursor: pointer;
    transition: background .15s;
    font-size: .82rem;
    user-select: none;
}
.layer-item:hover { background: var(--cream2); }
.layer-item.hidden-layer { opacity: .4; }

.layer-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
    box-shadow: 0 0 0 2px rgba(255,255,255,.8), 0 0 0 3px currentColor;
}

.layer-name { flex: 1; color: var(--soil); font-weight: 500; }

.layer-badge {
    font-size: .62rem;
    padding: 2px 7px;
    border-radius: 20px;
    background: var(--cream2);
    color: var(--textlt);
    border: 1px solid var(--border);
    font-weight: 600;
    letter-spacing: .03em;
}

.layer-count {
    font-size: .7rem;
    font-weight: 700;
    color: var(--water);
    background: rgba(74,124,111,.1);
    border-radius: 20px;
    padding: 1px 7px;
    min-width: 22px;
    text-align: center;
}

.layer-edit-btn {
    background: none; border: none; cursor: pointer;
    font-size: .75rem; padding: 2px 4px; border-radius: 5px;
    color: var(--textlt); transition: background .15s, color .15s;
    line-height: 1;
}
.layer-edit-btn:hover { background: var(--cream2); color: var(--soil); }

/* ── Buttons ────────────────────────────────────────────── */
.btn-peta {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .45rem .9rem; border-radius: 8px; border: none;
    font-family: 'Karla', sans-serif; font-size: .8rem; font-weight: 600;
    cursor: pointer; transition: all .18s; text-decoration: none;
    white-space: nowrap;
}
.btn-peta:active { transform: scale(.97); }

.btn-primary { background: var(--water); color: #fff; }
.btn-primary:hover { background: var(--water2); color: #fff; }

.btn-secondary { background: var(--cream2); color: var(--soil); border: 1.5px solid var(--border); }
.btn-secondary:hover { background: var(--straw); }

.btn-success { background: var(--leaf); color: #fff; }
.btn-success:hover { background: #4e6b3d; color: #fff; }

.btn-danger { background: rgba(185,74,60,.1); color: #b94a3c; border: 1.5px solid rgba(185,74,60,.2); }
.btn-danger:hover { background: rgba(185,74,60,.18); }

.btn-sm { padding: .3rem .65rem; font-size: .75rem; }
.btn-block { width: 100%; justify-content: center; }

/* ── Draw Panel ─────────────────────────────────────────── */
.draw-hint {
    font-size: .75rem;
    color: var(--water);
    background: rgba(74,124,111,.07);
    border: 1.5px dashed rgba(74,124,111,.3);
    border-radius: 8px;
    padding: .6rem .75rem;
    margin-top: .6rem;
    display: flex; align-items: flex-start; gap: .4rem;
    line-height: 1.4;
}

.form-select-peta, .form-input-peta {
    width: 100%;
    padding: .45rem .7rem;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-family: 'Karla', sans-serif;
    font-size: .82rem;
    color: var(--soil);
    background: #fff;
    transition: border-color .15s;
    outline: none;
}
.form-select-peta:focus, .form-input-peta:focus {
    border-color: var(--water);
    box-shadow: 0 0 0 3px rgba(74,124,111,.1);
}

/* ── Legenda ────────────────────────────────────────────── */
.legend-item {
    display: flex; align-items: center; gap: .6rem;
    font-size: .8rem; color: var(--soil); padding: .25rem 0;
}
.legend-polygon {
    width: 18px; height: 14px; border-radius: 4px;
    border: 2px solid currentColor; flex-shrink: 0;
}
.legend-polyline {
    width: 18px; height: 3px; border-radius: 2px; flex-shrink: 0;
}

/* ── Popup ──────────────────────────────────────────────── */
.leaflet-popup-content-wrapper {
    border-radius: 14px !important;
    border: 1.5px solid var(--border, #ddd) !important;
    box-shadow: 0 8px 32px rgba(61,43,31,.15) !important;
    padding: 0 !important;
    overflow: hidden;
}
.leaflet-popup-content { margin: 0 !important; width: auto !important; }
.leaflet-popup-tip-container { margin-top: -1px; }

.popup-wrap { padding: 1rem 1.1rem; min-width: 210px; max-width: 260px; }
.popup-header {
    display: flex; align-items: flex-start; gap: .5rem;
    margin-bottom: .75rem; padding-bottom: .6rem;
    border-bottom: 1.5px solid #f0ebe4;
}
.popup-icon {
    width: 30px; height: 30px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
}
.popup-title {
    font-family: 'Fraunces', serif;
    font-size: .95rem; font-weight: 700;
    color: var(--soil, #3d2b1f); line-height: 1.2;
    margin: 0;
}
.popup-layer-tag {
    font-size: .65rem; color: var(--textlt, #7a6355);
    background: var(--cream2, #f5ede0); border-radius: 4px;
    padding: 1px 6px; margin-top: 2px; display: inline-block;
}
.popup-row {
    display: flex; justify-content: space-between; align-items: center;
    font-size: .78rem; margin-bottom: .3rem; gap: .5rem;
}
.popup-row-label { color: var(--textlt, #7a6355); flex-shrink: 0; }
.popup-row-val { font-weight: 600; color: var(--soil, #3d2b1f); text-align: right; }
.popup-desc {
    font-size: .77rem; color: #888;
    margin-top: .5rem; padding-top: .5rem;
    border-top: 1px solid #f0ebe4; line-height: 1.5;
}
.popup-actions {
    margin-top: .75rem; display: flex; gap: .4rem;
}

/* ── Header Actions ─────────────────────────────────────── */
.peta-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    margin-bottom: 1.25rem; flex-wrap: wrap; gap: .75rem;
}
.peta-header-title h4 {
    font-family: 'Fraunces', serif;
    font-size: 1.25rem; font-weight: 700;
    color: var(--soil); margin: 0 0 .2rem;
}
.peta-header-title p { font-size: .8rem; color: var(--textlt); margin: 0; }

/* ── Modal ──────────────────────────────────────────────── */
.modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(61,43,31,.35); backdrop-filter: blur(3px);
    z-index: 9999; align-items: center; justify-content: center;
}
.modal-box {
    background: #fff; border-radius: 16px;
    padding: 1.5rem; width: 400px; max-width: 95vw;
    box-shadow: 0 20px 60px rgba(61,43,31,.2);
    animation: modalIn .2s ease;
}
@keyframes modalIn {
    from { opacity:0; transform: scale(.95) translateY(8px); }
    to   { opacity:1; transform: scale(1)  translateY(0); }
}
.modal-title {
    font-family: 'Fraunces', serif;
    font-size: 1.1rem; font-weight: 700;
    color: var(--soil); margin: 0 0 1.1rem;
    padding-bottom: .75rem; border-bottom: 1.5px solid var(--border);
}
.modal-field { margin-bottom: .9rem; }
.modal-label {
    display: block; font-size: .78rem; font-weight: 600;
    color: var(--textlt); margin-bottom: .35rem; letter-spacing: .02em;
}
.modal-input, .modal-select, .modal-textarea {
    width: 100%; padding: .5rem .75rem;
    border: 1.5px solid var(--border); border-radius: 9px;
    font-family: 'Karla', sans-serif; font-size: .85rem;
    color: var(--soil); background: #fff; outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.modal-input:focus, .modal-select:focus, .modal-textarea:focus {
    border-color: var(--water);
    box-shadow: 0 0 0 3px rgba(74,124,111,.12);
}
.modal-textarea { resize: vertical; min-height: 70px; }
.modal-footer {
    display: flex; gap: .5rem; justify-content: flex-end;
    margin-top: 1.1rem; padding-top: .9rem;
    border-top: 1.5px solid var(--border);
}

/* ── Leaflet draw overrides ─────────────────────────────── */
.leaflet-draw-toolbar a { border-radius: 6px !important; }
.leaflet-touch .leaflet-bar { border-radius: 10px !important; box-shadow: 0 2px 12px rgba(61,43,31,.12) !important; }

@media (max-width: 768px) {
    #peta-wrapper { flex-direction: column; height: auto; }
    #map { height: 60vw; min-height: 320px; }
    #sidebar { width: 100%; }
}
</style>
@endpush

@section('content')
<div style="padding: 0 .25rem;">

    {{-- Header --}}
    <div class="peta-header">
        <div class="peta-header-title">
            <h4>🗺️ Peta Daerah Irigasi</h4>
            <p>Klik area pada peta untuk melihat detail daerah irigasi</p>
        </div>
        @can('create peta')
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <button class="btn-peta btn-secondary" onclick="openImportModal()">
                📁 Import GeoJSON
            </button>
            <button class="btn-peta btn-primary" onclick="openLayerModal()">
                <span style="font-size:1rem;line-height:1">+</span> Tambah Layer
            </button>
        </div>
        @endcan
    </div>

    {{-- Map + Sidebar --}}
    <div id="peta-wrapper">

        {{-- Map --}}
        <div id="map"></div>

        {{-- Sidebar --}}
        <div id="sidebar">

            {{-- Layer List --}}
            <div class="panel-card">
                <div class="panel-title">
                    <span>🗂️</span> Layer Peta
                </div>
            <div id="layer-list">
                @forelse($layers as $layer)
                <div class="layer-item" id="layer-item-{{ $layer->id }}" data-layer-id="{{ $layer->id }}"
                    onclick="toggleLayer({{ $layer->id }})">
                    <span class="layer-dot" style="background:{{ $layer->warna }};color:{{ $layer->warna }}"></span>
                    <span class="layer-name">{{ $layer->nama }}</span>
                    <span class="layer-badge">{{ $layer->tipe }}</span>
                    <span class="layer-count">{{ $layer->features->count() }}</span>
                    @can('edit peta')
                    <button class="layer-edit-btn" title="Edit layer"
                        onclick="event.stopPropagation(); openEditLayerModal({{ $layer->id }}, '{{ addslashes($layer->nama) }}', '{{ $layer->warna }}', '{{ $layer->tipe }}', '{{ addslashes($layer->keterangan ?? '') }}')">
                        ✏️
                    </button>
                    @endcan
                    @can('delete peta')
                    <button class="layer-edit-btn" title="Hapus layer" style="color:#b94a3c;"
                        onclick="event.stopPropagation(); deleteLayer({{ $layer->id }}, '{{ addslashes($layer->nama) }}')">
                        🗑
                    </button>
                    @endcan
                </div>
                @empty
                <div style="text-align:center;padding:1.5rem .5rem;">
                    <div style="font-size:1.8rem;margin-bottom:.4rem;">🗂️</div>
                    <p style="font-size:.8rem;color:var(--textlt);margin:0;">Belum ada layer.<br>Klik <b>+ Tambah Layer</b> dulu.</p>
                </div>
                @endforelse
            </div>
            </div>

            {{-- Draw Tools --}}
            @can('create peta')
            <div class="panel-card">
                <div class="panel-title">
                    <span>✏️</span> Gambar di Peta
                </div>
                <div style="display:flex;flex-direction:column;gap:.5rem;">
                    <select id="draw-layer-select" class="form-select-peta">
                        <option value="">— Pilih Layer —</option>
                        @foreach($allLayers as $layer)
                        <option value="{{ $layer->id }}" data-tipe="{{ $layer->tipe }}" data-warna="{{ $layer->warna }}">
                            {{ $layer->nama }}
                        </option>
                        @endforeach
                    </select>
                    <button class="btn-peta btn-success btn-block" id="btn-start-draw" onclick="startDraw()">
                        🖊️ Mulai Gambar
                    </button>
                    <button class="btn-peta btn-danger btn-block" id="btn-cancel-draw" onclick="cancelDraw()" style="display:none;">
                        ✖ Batalkan Gambar
                    </button>
                </div>
                <div class="draw-hint" id="draw-hint" style="display:none;">
                    <span>💡</span>
                    <span>Klik peta untuk menambah titik. <b>Double-klik</b> untuk selesai menggambar.</span>
                </div>
            </div>
            @endcan

            {{-- Legenda --}}
            <div class="panel-card">
                <div class="panel-title">
                    <span>📋</span> Legenda
                </div>
                @forelse($layers as $layer)
                <div class="legend-item">
                    @if($layer->tipe === 'polygon')
                    <span class="legend-polygon" style="background:{{ $layer->warna }}33;border-color:{{ $layer->warna }}"></span>
                    @else
                    <span class="legend-polyline" style="background:{{ $layer->warna }}"></span>
                    @endif
                    <span>{{ $layer->nama }}</span>
                </div>
                @empty
                <p style="font-size:.8rem;color:var(--textlt);margin:0;">—</p>
                @endforelse
            </div>

        </div>{{-- /sidebar --}}
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MODAL: Tambah / Edit Layer
══════════════════════════════════════════════ --}}
<div id="layerModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-title" id="layerModalTitle">➕ Tambah Layer</div>
        <input type="hidden" id="edit-layer-id">

        <div class="modal-field">
            <label class="modal-label">Nama Layer</label>
            <input type="text" id="layer-nama" class="modal-input" placeholder="cth: Daerah Irigasi Primer">
        </div>
        <div class="modal-field">
            <label class="modal-label">Tipe</label>
            <select id="layer-tipe" class="modal-select">
                <option value="polygon">🔷 Polygon — batas daerah/wilayah</option>
                <option value="polyline">➖ Polyline — jalur saluran</option>
            </select>
        </div>
        <div class="modal-field">
            <label class="modal-label">Warna</label>
            <div style="display:flex;align-items:center;gap:.75rem;">
                <input type="color" id="layer-warna" class="modal-input" value="#4a7c6f"
                    style="width:48px;height:38px;padding:2px;cursor:pointer;">
                <div id="warna-preview" style="flex:1;height:38px;border-radius:8px;border:1.5px solid var(--border);transition:background .2s;background:#4a7c6f22;"></div>
            </div>
        </div>
        <div class="modal-field">
            <label class="modal-label">Keterangan <span style="font-weight:400;color:#bbb">(opsional)</span></label>
            <textarea id="layer-keterangan" class="modal-textarea" placeholder="Deskripsi singkat layer ini..."></textarea>
        </div>

        <div class="modal-footer">
            <button class="btn-peta btn-secondary" onclick="closeLayerModal()">Batal</button>
            <button class="btn-peta btn-primary" onclick="saveLayer()">💾 Simpan Layer</button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MODAL: Simpan Feature
══════════════════════════════════════════════ --}}
<div id="featureModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-title" id="featureModalTitle">📍 Simpan Area</div>
        <input type="hidden" id="edit-feature-id">
        <input type="hidden" id="feature-geojson">
        <input type="hidden" id="feature-layer-id">

        <div class="modal-field">
            <label class="modal-label">Nama Area / Daerah Irigasi</label>
            <input type="text" id="feature-nama" class="modal-input" placeholder="cth: DI Rawa Adul">
        </div>
        <div class="modal-field">
            <label class="modal-label">Petak Sawah <span style="font-weight:400;color:#bbb">(opsional)</span></label>
            <select id="feature-petak" class="modal-select">
                <option value="">— Tidak terkait petak —</option>
                @foreach($petaks as $p)
                <option value="{{ $p->id }}">{{ $p->kode_petak }} — {{ $p->nama_petak }}</option>
                @endforeach
            </select>
        </div>
        <div class="modal-field">
            <label class="modal-label">Luas <span style="font-weight:400;color:#bbb">(ha, opsional)</span></label>
            <input type="number" id="feature-luas" class="modal-input" step="0.01" min="0" placeholder="0.00">
        </div>
        <div class="modal-field">
            <label class="modal-label">Deskripsi <span style="font-weight:400;color:#bbb">(opsional)</span></label>
            <textarea id="feature-deskripsi" class="modal-textarea" placeholder="Keterangan tambahan..."></textarea>
        </div>

        <div class="modal-footer">
            <button class="btn-peta btn-secondary" onclick="closeFeatureModal()">Batal</button>
            <button class="btn-peta btn-primary" onclick="saveFeature()">💾 Simpan</button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MODAL: Import GeoJSON
══════════════════════════════════════════════ --}}
<div id="importModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-title">📁 Import GeoJSON</div>

        <div class="modal-field">
            <label class="modal-label">Masukkan ke Layer</label>
            <select id="import-layer-select" class="modal-select">
                @foreach($allLayers as $layer)
                <option value="{{ $layer->id }}">{{ $layer->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="modal-field">
            <label class="modal-label">File GeoJSON</label>
            <input type="file" id="import-file" class="modal-input" accept=".json,.geojson"
                style="padding:.4rem .6rem;cursor:pointer;">
        </div>
        <div style="background:rgba(74,124,111,.07);border:1.5px dashed rgba(74,124,111,.3);border-radius:8px;padding:.6rem .75rem;font-size:.76rem;color:var(--water);">
            💡 Format yang didukung: <b>.json</b> dan <b>.geojson</b>. Maksimal 5MB.
        </div>

        <div class="modal-footer">
            <button class="btn-peta btn-secondary" onclick="closeImportModal()">Batal</button>
            <button class="btn-peta btn-primary" onclick="importGeoJson()">📥 Import</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script>
// ══════════════════════════════════════════════════════════════
// INIT MAP
// ══════════════════════════════════════════════════════════════
const map = L.map('map', {
    center: [-2.2, 114.0],
    zoom: 10,
    zoomControl: true,
});

const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors', maxZoom: 19,
});
const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: '© Esri World Imagery', maxZoom: 19,
});
osmLayer.addTo(map);
L.control.layers({ '🗺️ Peta' : osmLayer, '🛰️ Satelit': satelliteLayer }, {}, { position: 'topright' }).addTo(map);

// ══════════════════════════════════════════════════════════════
// LOAD FEATURES
// ══════════════════════════════════════════════════════════════
const geoJsonData   = @json($geoJsonAll);
const leafletLayers = {};
const featureGroup  = L.featureGroup().addTo(map);

function styleForFeature(feature) {
    const warna = feature.properties.warna || '#4a7c6f';
    if (feature.properties.layer_tipe === 'polygon') {
        return { color: warna, weight: 2.5, fillColor: warna, fillOpacity: 0.25, dashArray: null };
    }
    return { color: warna, weight: 3.5, opacity: .9 };
}

function buildPopup(props) {
    const canEdit = @json(auth()->user()?->can('edit peta'));
    const canDel  = @json(auth()->user()?->can('delete peta'));
    const warna   = props.warna || '#4a7c6f';
    const icon    = props.layer_tipe === 'polygon' ? '🏞️' : '〰️';

    let html = `<div class="popup-wrap">
        <div class="popup-header">
            <div class="popup-icon" style="background:${warna}22;color:${warna}">${icon}</div>
            <div>
                <div class="popup-title">${props.nama}</div>
                <span class="popup-layer-tag">${props.layer_nama ?? '—'}</span>
            </div>
        </div>`;

    if (props.petak_kode) {
        html += `<div class="popup-row">
            <span class="popup-row-label">Petak</span>
            <span class="popup-row-val">${props.petak_kode} — ${props.petak_nama}</span>
        </div>`;
    }
    if (props.luas_manual) {
        html += `<div class="popup-row">
            <span class="popup-row-label">Luas</span>
            <span class="popup-row-val">${parseFloat(props.luas_manual).toLocaleString('id-ID')} ha</span>
        </div>`;
    }
    if (props.deskripsi) {
        html += `<div class="popup-desc">${props.deskripsi}</div>`;
    }

    if (canEdit || canDel) {
        html += `<div class="popup-actions">`;
        if (canEdit) html += `
            <button class="btn-peta btn-secondary btn-sm" onclick="openEditFeature(${props.id})">✏️ Info</button>
            <button class="btn-peta btn-secondary btn-sm" onclick="editGeometry(${props.id})">📐 Geometri</button>`;
        if (canDel) html += `
            <button class="btn-peta btn-danger btn-sm" onclick="deleteFeature(${props.id})">🗑 Hapus</button>`;
        html += `</div>`;
    }

    html += `</div>`;
    return html;
}

function renderGeoJson(data) {
    L.geoJSON(data, {
        style: styleForFeature,
        onEachFeature(feature, layer) {
            layer.bindPopup(buildPopup(feature.properties), { maxWidth: 280 });
            layer.on('mouseover', function() {
                if (feature.properties.layer_tipe === 'polygon') {
                    this.setStyle({ fillOpacity: 0.45, weight: 3 });
                }
            });
            layer.on('mouseout', function() {
                this.setStyle(styleForFeature(feature));
            });
            const lid = feature.properties.layer_id;
            if (!leafletLayers[lid]) leafletLayers[lid] = [];
            leafletLayers[lid].push(layer);
            featureGroup.addLayer(layer);
        },
    }).addTo(map);
}

renderGeoJson(geoJsonData);
if (geoJsonData.features.length > 0) {
    try { map.fitBounds(featureGroup.getBounds().pad(0.15)); } catch(e) {}
}

// ══════════════════════════════════════════════════════════════
// TOGGLE LAYER
// ══════════════════════════════════════════════════════════════
const layerVisible = {};
function toggleLayer(layerId) {
    if (!leafletLayers[layerId]) return;
    layerVisible[layerId] = layerVisible[layerId] !== false ? false : true;
    leafletLayers[layerId].forEach(l => {
        layerVisible[layerId] === false ? map.removeLayer(l) : map.addLayer(l);
    });
    const item = document.getElementById(`layer-item-${layerId}`);
    if (item) item.classList.toggle('hidden-layer', layerVisible[layerId] === false);
}

// ══════════════════════════════════════════════════════════════
// DRAW TOOLS
// ══════════════════════════════════════════════════════════════
let drawControl = null;
let drawnItems  = new L.FeatureGroup().addTo(map);
let activeDrawLayerId = null;

function startDraw() {
    const sel = document.getElementById('draw-layer-select');
    if (!sel.value) {
        showToast('⚠️ Pilih layer dulu sebelum menggambar!', 'warn');
        return;
    }
    activeDrawLayerId = sel.value;
    const tipe  = sel.options[sel.selectedIndex].dataset.tipe;
    const warna = sel.options[sel.selectedIndex].dataset.warna || '#4a7c6f';

    if (drawControl) map.removeControl(drawControl);
    drawControl = new L.Control.Draw({
        draw: {
            polygon:      tipe === 'polygon'  ? { shapeOptions: { color: warna, fillColor: warna, fillOpacity: .25 } } : false,
            polyline:     tipe === 'polyline' ? { shapeOptions: { color: warna, weight: 3.5 } } : false,
            rectangle: false, circle: false, marker: false, circlemarker: false,
        },
        edit: { featureGroup: drawnItems },
    });
    map.addControl(drawControl);
    document.getElementById('draw-hint').style.display      = 'flex';
    document.getElementById('btn-cancel-draw').style.display = 'flex';
    document.getElementById('btn-start-draw').style.display  = 'none';

    setTimeout(() => {
        const btn = document.querySelector(
            tipe === 'polygon' ? '.leaflet-draw-draw-polygon' : '.leaflet-draw-draw-polyline'
        );
        if (btn) btn.click();
    }, 100);
}

function cancelDraw() {
    if (drawControl) { map.removeControl(drawControl); drawControl = null; }
    drawnItems.clearLayers();
    document.getElementById('draw-hint').style.display       = 'none';
    document.getElementById('btn-cancel-draw').style.display  = 'none';
    document.getElementById('btn-start-draw').style.display   = 'flex';
}

map.on(L.Draw.Event.CREATED, function(e) {
    drawnItems.clearLayers();
    drawnItems.addLayer(e.layer);
    document.getElementById('feature-layer-id').value = activeDrawLayerId;
    document.getElementById('feature-geojson').value  = JSON.stringify(e.layer.toGeoJSON().geometry);
    document.getElementById('featureModalTitle').textContent = '📍 Simpan Area';
    document.getElementById('edit-feature-id').value  = '';
    document.getElementById('feature-nama').value     = '';
    document.getElementById('feature-petak').value    = '';
    document.getElementById('feature-luas').value     = '';
    document.getElementById('feature-deskripsi').value = '';
    openModal('featureModal');
});

// ══════════════════════════════════════════════════════════════
// MODAL HELPERS
// ══════════════════════════════════════════════════════════════
function openModal(id)  { document.getElementById(id).style.display = 'flex'; }
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

function openLayerModal() {
    document.getElementById('layerModalTitle').textContent = '➕ Tambah Layer';
    document.getElementById('edit-layer-id').value   = '';
    document.getElementById('layer-nama').value      = '';
    document.getElementById('layer-warna').value     = '#4a7c6f';
    document.getElementById('layer-keterangan').value = '';
    document.getElementById('warna-preview').style.background = '#4a7c6f22';
    openModal('layerModal');
}

function openEditLayerModal(id, nama, warna, tipe, ket) {
    document.getElementById('layerModalTitle').textContent = '✏️ Edit Layer';
    document.getElementById('edit-layer-id').value    = id;
    document.getElementById('layer-nama').value       = nama;
    document.getElementById('layer-warna').value      = warna;
    document.getElementById('layer-tipe').value       = tipe;
    document.getElementById('layer-keterangan').value = ket;
    document.getElementById('warna-preview').style.background = warna + '33';
    openModal('layerModal');
}

function closeLayerModal()  { closeModal('layerModal'); }
function closeFeatureModal(){ closeModal('featureModal'); cancelDraw(); }
function openImportModal()  { openModal('importModal'); }
function closeImportModal() { closeModal('importModal'); }

// Live preview warna
document.getElementById('layer-warna').addEventListener('input', function() {
    document.getElementById('warna-preview').style.background = this.value + '33';
});

// Close modal klik overlay
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});

// ══════════════════════════════════════════════════════════════
// LAYER CRUD
// ══════════════════════════════════════════════════════════════
function saveLayer() {
    const id     = document.getElementById('edit-layer-id').value;
    const isEdit = !!id;
    const url    = isEdit ? `/peta/layer/${id}` : '/peta/layer';
    const method = isEdit ? 'PUT' : 'POST';

    const payload = {
        nama:       document.getElementById('layer-nama').value.trim(),
        tipe:       document.getElementById('layer-tipe').value,
        warna:      document.getElementById('layer-warna').value,
        keterangan: document.getElementById('layer-keterangan').value,
    };

    if (!payload.nama) { showToast('⚠️ Nama layer wajib diisi!', 'warn'); return; }

    fetchJson(url, method, payload).then(r => {
        if (r.success) {
            closeLayerModal();
            showToast('✅ Layer berhasil disimpan!');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('❌ Gagal menyimpan layer', 'error');
        }
    });
}

function deleteLayer(layerId, namaLayer) {
    if (!confirm(`Hapus layer "${namaLayer}" beserta semua area di dalamnya?\n\nTindakan ini tidak bisa dibatalkan.`)) return;
    fetchJson(`/peta/layer/${layerId}`, 'DELETE', {}).then(r => {
        if (r.success) {
            showToast('🗑 Layer berhasil dihapus');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('❌ Gagal menghapus layer', 'error');
        }
    });
}

// ══════════════════════════════════════════════════════════════
// FEATURE CRUD
// ══════════════════════════════════════════════════════════════
function saveFeature() {
    const id     = document.getElementById('edit-feature-id').value;
    const isEdit = !!id;
    const url    = isEdit ? `/peta/feature/${id}` : '/peta/feature';
    const method = isEdit ? 'PUT' : 'POST';
    const nama   = document.getElementById('feature-nama').value.trim();

    if (!nama) { showToast('⚠️ Nama area wajib diisi!', 'warn'); return; }

    const payload = {
        map_layer_id: document.getElementById('feature-layer-id').value,
        nama,
        petak_id:    document.getElementById('feature-petak').value  || null,
        luas_manual: document.getElementById('feature-luas').value   || null,
        deskripsi:   document.getElementById('feature-deskripsi').value,
        geojson:     JSON.parse(document.getElementById('feature-geojson').value),
    };

    fetchJson(url, method, payload).then(r => {
        if (r.success) {
            closeFeatureModal();
            showToast('✅ Area berhasil disimpan!');
            reloadFeatures();
        } else {
            showToast('❌ Gagal menyimpan: ' + (r.message || ''), 'error');
        }
    });
}

// Edit info (nama, petak, luas, deskripsi) tanpa ubah geometri
function openEditFeature(featureId) {
    fetch('/peta/geojson').then(r => r.json()).then(data => {
        const feat = data.features.find(f => f.id == featureId);
        if (!feat) return;
        const p = feat.properties;

        document.getElementById('featureModalTitle').textContent = '✏️ Edit Info Area';
        document.getElementById('edit-feature-id').value    = featureId;
        document.getElementById('feature-layer-id').value   = p.layer_id;
        document.getElementById('feature-geojson').value    = JSON.stringify(feat.geometry);
        document.getElementById('feature-nama').value       = p.nama;
        document.getElementById('feature-luas').value       = p.luas_manual || '';
        document.getElementById('feature-deskripsi').value  = p.deskripsi || '';

        // Set petak jika ada
        const petakSel = document.getElementById('feature-petak');
        petakSel.value = '';
        for (let opt of petakSel.options) {
            if (opt.text.startsWith(p.petak_kode + ' —')) { opt.selected = true; break; }
        }

        openModal('featureModal');
    });
}

// Edit geometri (bentuk/posisi di peta)
function editGeometry(featureId) {
    // Tutup popup dulu
    map.closePopup();

    fetch('/peta/geojson').then(r => r.json()).then(data => {
        const feat = data.features.find(f => f.id == featureId);
        if (!feat) return;

        const tipe  = feat.properties.layer_tipe;
        const warna = feat.properties.warna || '#4a7c6f';

        // Load geometry ke drawnItems agar bisa diedit
        drawnItems.clearLayers();
        const geomLayer = L.geoJSON(feat, {
            style: { color: warna, weight: 3, fillColor: warna, fillOpacity: .25 }
        }).getLayers()[0];
        drawnItems.addLayer(geomLayer);

        // Aktifkan draw control mode EDIT saja
        if (drawControl) map.removeControl(drawControl);
        drawControl = new L.Control.Draw({
            draw: {
                polygon: false, polyline: false, rectangle: false,
                circle: false, marker: false, circlemarker: false,
            },
            edit: { featureGroup: drawnItems, edit: true, remove: false },
        });
        map.addControl(drawControl);

        // Simpan data feature yang diedit
        pendingEditFeatureId    = featureId;
        pendingEditFeatureProps = feat.properties;

        // Zoom ke feature
        try { map.fitBounds(drawnItems.getBounds().pad(0.2)); } catch(e) {}

        showToast('📐 Mode edit geometri aktif. Geser titik lalu klik <b>Save</b> di toolbar peta.', 'warn');

        // Tampilkan panel info edit geometri
        document.getElementById('draw-hint').style.display       = 'flex';
        document.getElementById('draw-hint').innerHTML           = '💡 <span>Geser titik untuk edit bentuk. Klik <b>Save</b> di toolbar atas peta bila selesai.</span>';
        document.getElementById('btn-cancel-draw').style.display  = 'flex';
        document.getElementById('btn-start-draw').style.display   = 'none';
    });
}

// Variabel untuk menyimpan state edit geometri
let pendingEditFeatureId    = null;
let pendingEditFeatureProps = null;

// Tangkap event EDITED dari Leaflet Draw
map.on(L.Draw.Event.EDITED, function(e) {
    if (!pendingEditFeatureId) return;

    e.layers.eachLayer(function(l) {
        document.getElementById('feature-geojson').value = JSON.stringify(l.toGeoJSON().geometry);
    });

    // Isi form dengan data lama
    const p = pendingEditFeatureProps;
    document.getElementById('featureModalTitle').textContent = '📐 Simpan Perubahan Geometri';
    document.getElementById('edit-feature-id').value    = pendingEditFeatureId;
    document.getElementById('feature-layer-id').value   = p.layer_id;
    document.getElementById('feature-nama').value       = p.nama;
    document.getElementById('feature-luas').value       = p.luas_manual || '';
    document.getElementById('feature-deskripsi').value  = p.deskripsi || '';

    const petakSel = document.getElementById('feature-petak');
    petakSel.value = '';
    if (p.petak_kode) {
        for (let opt of petakSel.options) {
            if (opt.text.startsWith(p.petak_kode + ' —')) { opt.selected = true; break; }
        }
    }

    // Cleanup
    pendingEditFeatureId    = null;
    pendingEditFeatureProps = null;
    cancelDraw();

    openModal('featureModal');
});

function deleteFeature(featureId) {
    if (!confirm('Hapus area ini dari peta?\n\nTindakan ini tidak bisa dibatalkan.')) return;
    fetchJson(`/peta/feature/${featureId}`, 'DELETE', {}).then(r => {
        if (r.success) { showToast('🗑 Area dihapus'); reloadFeatures(); }
        else showToast('❌ Gagal menghapus', 'error');
    });
}

function reloadFeatures() {
    featureGroup.clearLayers();
    Object.keys(leafletLayers).forEach(k => { leafletLayers[k] = []; });
    fetch('/peta/geojson').then(r => r.json()).then(data => {
        renderGeoJson(data);
    });
}

// ══════════════════════════════════════════════════════════════
// IMPORT GEOJSON
// ══════════════════════════════════════════════════════════════
function importGeoJson() {
    const layerId = document.getElementById('import-layer-select').value;
    const file    = document.getElementById('import-file').files[0];
    if (!layerId || !file) { showToast('⚠️ Pilih layer dan file!', 'warn'); return; }

    const formData = new FormData();
    formData.append('map_layer_id', layerId);
    formData.append('file', file);
    formData.append('_token', csrfToken());

    fetch('/peta/import', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(r => {
            if (r.success) {
                closeImportModal();
                showToast(`✅ Berhasil import ${r.imported} feature!`);
                reloadFeatures();
            } else {
                showToast('❌ Gagal import: ' + (r.message || ''), 'error');
            }
        });
}

// ══════════════════════════════════════════════════════════════
// TOAST NOTIFICATION
// ══════════════════════════════════════════════════════════════
function showToast(msg, type = 'success') {
    const existing = document.getElementById('peta-toast');
    if (existing) existing.remove();

    const colors = {
        success: { bg: 'rgba(90,122,71,.95)',  border: 'rgba(90,122,71,.3)'  },
        warn:    { bg: 'rgba(196,137,90,.95)', border: 'rgba(196,137,90,.3)' },
        error:   { bg: 'rgba(185,74,60,.95)',  border: 'rgba(185,74,60,.3)'  },
    };
    const c = colors[type] || colors.success;

    const toast = document.createElement('div');
    toast.id = 'peta-toast';
    toast.style.cssText = `
        position:fixed; bottom:1.5rem; right:1.5rem; z-index:99999;
        background:${c.bg}; color:#fff; border:1px solid ${c.border};
        padding:.7rem 1.1rem; border-radius:10px; font-size:.83rem;
        font-family:'Karla',sans-serif; font-weight:500;
        box-shadow:0 4px 20px rgba(0,0,0,.2);
        animation: toastIn .25s ease;
        display:flex; align-items:center; gap:.5rem; max-width: 320px;
    `;
    toast.innerHTML = msg;
    document.body.appendChild(toast);

    const style = document.createElement('style');
    style.textContent = `@keyframes toastIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }`;
    document.head.appendChild(style);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all .3s';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// ══════════════════════════════════════════════════════════════
// HELPERS
// ══════════════════════════════════════════════════════════════
function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}
function fetchJson(url, method, data) {
    const opts = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json'
        },
    };
    if (method !== 'GET') opts.body = JSON.stringify(data);
    return fetch(url, opts).then(r => r.json());
}
</script>
@endpush
