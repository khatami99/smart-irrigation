@extends('layouts.app')
@section('title', 'Edit Petak — Smart Irrigation')
@section('page-title', 'Edit Petak Irigasi')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--straw:#e8d5a3;--cream:#faf6ef;--water:#4a7c6f;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .form-card { background:var(--cream);border:1px solid var(--border);border-radius:14px;padding:2rem; }
    .form-label { display:block;font-size:.8rem;font-weight:600;color:var(--text);margin-bottom:.45rem; }
    .form-label span { font-weight:300;color:var(--textlt); }
    .form-input { width:100%;background:var(--cream);border:1.5px solid rgba(139,94,60,.18);color:var(--text);border-radius:8px;padding:.75rem 1rem;font-size:.875rem;font-family:'Karla',sans-serif;transition:border-color .2s,box-shadow .2s;outline:none; }
    .form-input:focus { border-color:var(--water);box-shadow:0 0 0 3px rgba(74,124,111,.1); }
    .form-input::placeholder { color:rgba(122,99,85,.35); }
    .section-label { font-size:.65rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--textlt);margin-bottom:1rem;padding-bottom:.5rem;border-bottom:1px solid rgba(139,94,60,.1); }
    .btn-primary { background:var(--soil);color:var(--straw);padding:.8rem 2rem;border-radius:8px;border:none;font-family:'Karla',sans-serif;font-size:.9rem;font-weight:600;cursor:pointer;transition:background .2s; }
    .btn-primary:hover { background:var(--soil2); }
    .btn-cancel { background:rgba(139,94,60,.08);color:var(--textlt);padding:.8rem 2rem;border-radius:8px;border:1px solid var(--border);font-family:'Karla',sans-serif;font-size:.9rem;font-weight:600;text-decoration:none;transition:background .2s; }
    .btn-cancel:hover { background:rgba(139,94,60,.14); }
</style>

<div style="max-width:640px;margin:0 auto;">
    <a href="{{ route('petak.index') }}" style="display:inline-flex;align-items:center;gap:.4rem;font-size:.82rem;color:var(--textlt);text-decoration:none;margin-bottom:1.25rem;"
       onmouseover="this.style.color='var(--water)'" onmouseout="this.style.color='var(--textlt)'">
        ← Kembali ke Daftar Petak
    </a>

    <div class="form-card">
        <div style="margin-bottom:1.75rem;">
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Edit Data</p>
            <h2 style="font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--soil);letter-spacing:-.02em;">Edit Petak Irigasi</h2>
            <p style="font-size:.85rem;color:var(--textlt);font-weight:300;margin-top:.25rem;">
                Kode: <strong style="color:var(--soil);">{{ $petak->kode_petak }}</strong>
            </p>
        </div>

        @if($errors->any())
            <div style="background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.2);color:#a03828;border-radius:8px;padding:.8rem 1rem;font-size:.85rem;margin-bottom:1.25rem;">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('petak.update', $petak) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:1.5rem;">
                <p class="section-label">📍 Identitas Petak</p>
                <div style="display:grid;grid-template-columns:1fr 2fr;gap:1rem;">
                    <div>
                        <label class="form-label">Kode Petak <span>*</span></label>
                        <input type="text" name="kode_petak"
                            value="{{ old('kode_petak', $petak->kode_petak) }}"
                            required placeholder="cth: P-01" class="form-input" style="text-transform:uppercase;">
                        <p style="font-size:.72rem;color:var(--textlt);margin-top:.3rem;">Harus unik</p>
                    </div>
                    <div>
                        <label class="form-label">Nama Petak <span>*</span></label>
                        <input type="text" name="nama_petak"
                            value="{{ old('nama_petak', $petak->nama_petak) }}"
                            required placeholder="cth: Petak Sawah Blok A" class="form-input">
                    </div>
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <p class="section-label">🏞️ Daerah Irigasi</p>
                <div>
                    <label class="form-label">Daerah Irigasi <span>(opsional)</span></label>
                    <select name="daerah_irigasi_id" class="form-input">
                        <option value="">— Belum ditentukan —</option>
                        @foreach($daerahIrigasis as $di)
                        <option value="{{ $di->id }}" {{ old('daerah_irigasi_id', $petak->daerah_irigasi_id) == $di->id ? 'selected' : '' }}>
                            {{ $di->kode }} — {{ $di->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <p class="section-label">📐 Detail Area</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="form-label">Luas Area <span>(hektar) *</span></label>
                        <input type="number" name="luas_area"
                            value="{{ old('luas_area', $petak->luas_area) }}"
                            step="0.01" min="0.01" required placeholder="cth: 12.50" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Lokasi / Wilayah <span>*</span></label>
                        <input type="text" name="lokasi_wilayah"
                            value="{{ old('lokasi_wilayah', $petak->lokasi_wilayah) }}"
                            required placeholder="cth: Desa Makmur, Kec. Sungai Besar" class="form-input">
                    </div>
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <p class="section-label">🚰 Infrastruktur & Pengelola</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="form-label">Pintu Air <span>(opsional)</span></label>
                        <input type="text" name="pintu_air"
                            value="{{ old('pintu_air', $petak->pintu_air) }}"
                            placeholder="cth: PA-01, Pintu Sekunder A" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Penanggung Jawab Juru <span>(opsional)</span></label>
                        <input type="text" name="penanggung_jawab"
                            value="{{ old('penanggung_jawab', $petak->penanggung_jawab) }}"
                            placeholder="cth: Bapak Ahmad" class="form-input">
                    </div>
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <p class="section-label">🌐 Koordinat Lokasi <span style="font-weight:400;text-transform:none;letter-spacing:0;font-size:.7rem;">(opsional — klik peta atau isi manual)</span></p>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:.75rem;">
                    <div>
                        <label class="form-label">Latitude</label>
                        <input type="number" name="latitude" id="input-lat"
                            value="{{ old('latitude', $petak->latitude) }}"
                            step="0.0000001" placeholder="cth: -2.3456789"
                            class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Longitude</label>
                        <input type="number" name="longitude" id="input-lng"
                            value="{{ old('longitude', $petak->longitude) }}"
                            step="0.0000001" placeholder="cth: 114.1234567"
                            class="form-input">
                    </div>
                </div>

                <div style="border:1.5px solid var(--border);border-radius:10px;overflow:hidden;">
                    <div style="padding:.6rem 1rem;background:rgba(139,94,60,.04);border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:.75rem;color:var(--textlt);">💡 Klik pada peta untuk mengubah titik lokasi petak</span>
                        <button type="button" onclick="resetMarker()"
                            style="font-size:.72rem;color:var(--textlt);background:none;border:none;cursor:pointer;padding:0;">
                            ✖ Reset
                        </button>
                    </div>
                    <div id="map-picker" style="height:260px;"></div>
                </div>
            </div>

            <div style="margin-bottom:1.75rem;">
                <p class="section-label">⚙️ Status & Keterangan</p>
                <div style="display:grid;grid-template-columns:1fr 2fr;gap:1rem;">
                    <div>
                        <label class="form-label">Status <span>*</span></label>
                        <select name="status" required class="form-input">
                            <option value="aktif"    {{ old('status', $petak->status) == 'aktif'    ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', $petak->status) == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Keterangan <span>(opsional)</span></label>
                        <input type="text" name="keterangan"
                            value="{{ old('keterangan', $petak->keterangan) }}"
                            placeholder="Catatan tambahan..." class="form-input">
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:.75rem;">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
                <a href="{{ route('petak.index') }}" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const initLat = {{ old('latitude', $petak->latitude) ?: -2.2 }};
const initLng = {{ old('longitude', $petak->longitude) ?: 114.0 }};
const hasCoord = {{ ($petak->latitude && $petak->longitude) ? 'true' : 'false' }};

const pickerMap = L.map('map-picker').setView([initLat, initLng], hasCoord ? 14 : 10);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap', maxZoom: 19
}).addTo(pickerMap);

const markerIcon = L.divIcon({
    html: `<div style="width:18px;height:18px;background:var(--water,#4a7c6f);border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,.3);"></div>`,
    iconSize: [18, 18], iconAnchor: [9, 9], className: ''
});

let marker = hasCoord ? L.marker([initLat, initLng], { icon: markerIcon }).addTo(pickerMap) : null;

pickerMap.on('click', function(e) {
    const { lat, lng } = e.latlng;
    document.getElementById('input-lat').value = lat.toFixed(7);
    document.getElementById('input-lng').value = lng.toFixed(7);

    if (marker) marker.remove();
    marker = L.marker([lat, lng], { icon: markerIcon })
        .addTo(pickerMap)
        .bindPopup(`<small>${lat.toFixed(5)}, ${lng.toFixed(5)}</small>`)
        .openPopup();
});

// Sync input manual → update marker
['input-lat', 'input-lng'].forEach(id => {
    document.getElementById(id).addEventListener('change', function() {
        const lat = parseFloat(document.getElementById('input-lat').value);
        const lng = parseFloat(document.getElementById('input-lng').value);
        if (!isNaN(lat) && !isNaN(lng)) {
            if (marker) marker.remove();
            marker = L.marker([lat, lng], { icon: markerIcon }).addTo(pickerMap);
            pickerMap.setView([lat, lng], 14);
        }
    });
});

function resetMarker() {
    if (marker) { marker.remove(); marker = null; }
    document.getElementById('input-lat').value = '';
    document.getElementById('input-lng').value = '';
}
</script>
@endpush

@endsection
