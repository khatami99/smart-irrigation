@extends('layouts.app')
@section('title', 'Tambah Daerah Irigasi — Smart Irrigation')
@section('page-title', 'Master Data Daerah Irigasi')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--earth:#8b5e3c;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .card { background:var(--cream);border:1px solid var(--border);border-radius:14px;padding:1.75rem; }
    .form-group { margin-bottom:1.25rem; }
    .form-label { display:block;font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--textlt);margin-bottom:.5rem; }
    .form-control { width:100%;padding:.65rem .9rem;border:1.5px solid var(--border);border-radius:8px;font-size:.9rem;font-family:'Karla',sans-serif;color:var(--text);background:var(--cream);transition:border .2s; }
    .form-control:focus { outline:none;border-color:var(--water2); }
    .form-select { width:100%;padding:.65rem .9rem;border:1.5px solid var(--border);border-radius:8px;font-size:.9rem;font-family:'Karla',sans-serif;color:var(--text);background:var(--cream);transition:border .2s; }
    .form-select:focus { outline:none;border-color:var(--water2); }
    .form-error { font-size:.78rem;color:#a03828;margin-top:.35rem; }
    .form-hint { font-size:.75rem;color:var(--textlt);margin-top:.35rem; }
</style>

<div style="margin-bottom:1.5rem;">
    <a href="{{ route('daerah_irigasi.index') }}" style="font-size:.82rem;color:var(--textlt);text-decoration:none;">← Kembali ke Daerah Irigasi</a>
</div>

<div style="max-width:720px;">
    <div style="margin-bottom:1.5rem;">
        <h2 style="font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--soil);">Tambah Daerah Irigasi</h2>
        <p style="font-size:.85rem;color:var(--textlt);margin-top:.25rem;">Isi data daerah irigasi baru.</p>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('daerah_irigasi.store') }}">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 2fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Kode <span style="color:#a03828;">*</span></label>
                    <input type="text" name="kode" class="form-control" value="{{ old('kode') }}" placeholder="DI-001">
                    @error('kode')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Daerah Irigasi <span style="color:#a03828;">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" placeholder="Daerah Irigasi Barito">
                    @error('nama')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Luas Total (ha)</label>
                    <input type="number" name="luas_total" class="form-control" value="{{ old('luas_total') }}" placeholder="0.00" step="0.01" min="0">
                    @error('luas_total')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Sumber Air</label>
                    <input type="text" name="sumber_air" class="form-control" value="{{ old('sumber_air') }}" placeholder="Sungai Barito">
                    @error('sumber_air')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Penanggung Jawab</label>
                <input type="text" name="penanggung_jawab" class="form-control" value="{{ old('penanggung_jawab') }}" placeholder="Nama penanggung jawab">
                @error('penanggung_jawab')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            {{-- Koordinat --}}
            <div style="background:var(--cream2);border:1px solid var(--border);border-radius:10px;padding:1.25rem;margin-bottom:1.25rem;">
                <p style="font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--textlt);margin-bottom:1rem;">📍 Koordinat (Opsional)</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Latitude</label>
                        <input type="number" name="latitude" id="latitude" class="form-control" value="{{ old('latitude') }}" placeholder="-2.5489" step="any">
                        @error('latitude')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Longitude</label>
                        <input type="number" name="longitude" id="longitude" class="form-control" value="{{ old('longitude') }}" placeholder="115.7624" step="any">
                        @error('longitude')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <p class="form-hint" style="margin-top:.75rem;">Klik peta untuk mengisi koordinat secara otomatis.</p>
                <div id="map-picker" style="height:220px;border-radius:8px;margin-top:.75rem;border:1px solid var(--border);"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Status <span style="color:#a03828;">*</span></label>
                <select name="status" class="form-select">
                    <option value="aktif" {{ old('status','aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
                </select>
                @error('status')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
                @error('keterangan')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div style="display:flex;gap:.75rem;margin-top:1.5rem;">
                <button type="submit"
                    style="background:var(--soil);color:var(--straw);padding:.65rem 1.5rem;border-radius:8px;font-size:.875rem;font-weight:600;border:none;cursor:pointer;font-family:'Karla',sans-serif;transition:background .2s;"
                    onmouseover="this.style.background='var(--soil2)'" onmouseout="this.style.background='var(--soil)'">
                    Simpan
                </button>
                <a href="{{ route('daerah_irigasi.index') }}"
                    style="background:rgba(139,94,60,.08);border:1.5px solid var(--border);color:var(--textlt);padding:.65rem 1.25rem;border-radius:8px;font-size:.875rem;font-weight:600;text-decoration:none;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map-picker').setView([-2.5489, 115.7624], 9);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    let marker = null;

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    // Kalau sudah ada nilai old, taruh marker
    if (latInput.value && lngInput.value) {
        marker = L.marker([latInput.value, lngInput.value]).addTo(map);
        map.setView([latInput.value, lngInput.value], 12);
    }

    map.on('click', function(e) {
        latInput.value = e.latlng.lat.toFixed(7);
        lngInput.value = e.latlng.lng.toFixed(7);
        if (marker) marker.remove();
        marker = L.marker(e.latlng).addTo(map);
    });
</script>
@endpush
