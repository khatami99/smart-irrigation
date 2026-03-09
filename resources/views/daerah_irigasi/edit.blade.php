@extends('layouts.app')
@section('title', 'Edit Daerah Irigasi — Smart Irrigation')
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
        <h2 style="font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--soil);">Edit Daerah Irigasi</h2>
        <p style="font-size:.85rem;color:var(--textlt);margin-top:.25rem;">Update data daerah irigasi <strong>{{ $daerahIrigasi->nama }}</strong>.</p>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('daerah_irigasi.update', $daerahIrigasi) }}">
            @csrf @method('PUT')

            @include('daerah_irigasi._form')  {{-- ← semua field ada di sini --}}

            <div style="display:flex;gap:.75rem;margin-top:1.5rem;">
                <button type="submit"
                    style="background:var(--soil);color:var(--straw);padding:.65rem 1.5rem;border-radius:8px;font-size:.875rem;font-weight:600;border:none;cursor:pointer;font-family:'Karla',sans-serif;transition:background .2s;"
                    onmouseover="this.style.background='var(--soil2)'" onmouseout="this.style.background='var(--soil)'">
                    Update
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
    const lat = {{ $daerahIrigasi->latitude ?? -2.5489 }};
    const lng = {{ $daerahIrigasi->longitude ?? 115.7624 }};
    const map = L.map('map-picker').setView([lat, lng], lat !== -2.5489 ? 12 : 9);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    let marker = null;

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    if ({{ $daerahIrigasi->latitude ? 'true' : 'false' }}) {
        marker = L.marker([lat, lng]).addTo(map);
    }

    map.on('click', function(e) {
        latInput.value = e.latlng.lat.toFixed(7);
        lngInput.value = e.latlng.lng.toFixed(7);
        if (marker) marker.remove();
        marker = L.marker(e.latlng).addTo(map);
    });
</script>
@endpush
