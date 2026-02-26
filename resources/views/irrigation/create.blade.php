@extends('layouts.app')

@section('title', 'Tambah Data — Smart Irrigation')
@section('page-title', 'Tambah Data Harian')

@section('content')

<style>
    :root {
        --soil:#3d2b1f;--soil2:#5c3d2e;--straw:#e8d5a3;--cream:#faf6ef;
        --water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;
        --text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14);
    }
    .form-card { background:var(--cream);border:1px solid var(--border);border-radius:14px;padding:2rem; }
    .form-label { display:block;font-size:.8rem;font-weight:600;color:var(--text);margin-bottom:.45rem; }
    .form-label span { font-weight:300;color:var(--textlt); }
    .form-input {
        width:100%;background:var(--cream);border:1.5px solid rgba(139,94,60,.18);
        color:var(--text);border-radius:8px;padding:.75rem 1rem;
        font-size:.875rem;font-family:'Karla',sans-serif;
        transition:border-color .2s,box-shadow .2s;outline:none;
    }
    .form-input:focus { border-color:var(--water);box-shadow:0 0 0 3px rgba(74,124,111,.1); }
    .form-input::placeholder { color:rgba(122,99,85,.35); }
    .section-label {
        font-size:.65rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;
        color:var(--textlt);margin-bottom:1rem;padding-bottom:.5rem;
        border-bottom:1px solid rgba(139,94,60,.1);
    }
    .btn-primary {
        background:var(--soil);color:var(--straw);padding:.8rem 2rem;border-radius:8px;
        border:none;font-family:'Karla',sans-serif;font-size:.9rem;font-weight:600;
        cursor:pointer;transition:background .2s,transform .15s;
    }
    .btn-primary:hover { background:var(--soil2);transform:translateY(-1px); }
    .btn-cancel {
        background:rgba(139,94,60,.08);color:var(--textlt);padding:.8rem 2rem;border-radius:8px;
        border:1px solid var(--border);font-family:'Karla',sans-serif;font-size:.9rem;font-weight:600;
        text-decoration:none;transition:background .2s;
    }
    .btn-cancel:hover { background:rgba(139,94,60,.14); }
    .preview-box {
        background:rgba(74,124,111,.06);border:1px solid rgba(74,124,111,.15);
        border-radius:12px;padding:1.5rem;margin-bottom:1.5rem;
    }
</style>

<div style="max-width:680px;margin:0 auto;">

    <a href="{{ route('irrigation') }}" style="display:inline-flex;align-items:center;gap:.4rem;font-size:.82rem;color:var(--textlt);text-decoration:none;margin-bottom:1.25rem;transition:color .2s;"
       onmouseover="this.style.color='var(--water)'" onmouseout="this.style.color='var(--textlt)'">
        ← Kembali ke Dashboard
    </a>

    <div class="form-card">

        <div style="margin-bottom:1.75rem;">
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Form Input</p>
            <h2 style="font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--soil);letter-spacing:-.02em;">Data Iklim Harian</h2>
            <p style="font-size:.85rem;color:var(--textlt);font-weight:300;margin-top:.25rem;">ETo, ETc, dan Kebutuhan Air dihitung otomatis dari data yang diinput.</p>
        </div>

        @if($errors->any())
            <div style="background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.2);color:#a03828;border-radius:8px;padding:.8rem 1rem;font-size:.85rem;margin-bottom:1.25rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('irrigation.store') }}">
            @csrf

            {{-- Tanggal --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">📅 Informasi Dasar</p>
                <label class="form-label">Tanggal Pengamatan</label>
                <input type="date" name="tanggal" value="{{ old('tanggal') }}" required class="form-input">
            </div>

            {{-- Data Iklim --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">🌤 Data Iklim Lapangan</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">

                    <div>
                        <label class="form-label">Suhu Maksimum <span>(°C)</span></label>
                        <input type="number" name="suhu_max" value="{{ old('suhu_max') }}"
                            step="0.1" min="0" max="60" required placeholder="cth: 34.5"
                            class="form-input input-field">
                    </div>
                    <div>
                        <label class="form-label">Suhu Minimum <span>(°C)</span></label>
                        <input type="number" name="suhu_min" value="{{ old('suhu_min') }}"
                            step="0.1" min="0" max="60" required placeholder="cth: 24.0"
                            class="form-input input-field">
                    </div>
                    <div>
                        <label class="form-label">Kelembaban Udara <span>(%)</span></label>
                        <input type="number" name="kelembaban" value="{{ old('kelembaban') }}"
                            step="0.1" min="0" max="100" required placeholder="cth: 80"
                            class="form-input input-field">
                    </div>
                    <div>
                        <label class="form-label">Kecepatan Angin <span>(m/s)</span></label>
                        <input type="number" name="kecepatan_angin" value="{{ old('kecepatan_angin') }}"
                            step="0.1" min="0" required placeholder="cth: 1.5"
                            class="form-input input-field">
                    </div>
                    <div>
                        <label class="form-label">Radiasi Matahari <span>(MJ/m²/hari)</span></label>
                        <input type="number" name="radiasi_matahari" value="{{ old('radiasi_matahari') }}"
                            step="0.1" min="0" required placeholder="cth: 18.5"
                            class="form-input input-field">
                    </div>
                    <div>
                        <label class="form-label">Curah Hujan <span>(mm)</span></label>
                        <input type="number" name="curah_hujan" value="{{ old('curah_hujan', 0) }}"
                            step="0.1" min="0" required placeholder="cth: 5.0"
                            class="form-input input-field">
                    </div>

                </div>
            </div>

            {{-- Kc --}}
            <div style="margin-bottom:1.75rem;">
                <p class="section-label">🌾 Koefisien Tanaman</p>
                <div style="max-width:280px;">
                    <label class="form-label">Nilai Kc</label>
                    <input type="number" name="kc" value="{{ old('kc', 1.0) }}"
                        step="0.01" min="0" max="2" required placeholder="cth: 1.05"
                        class="form-input input-field">
                    <p style="font-size:.75rem;color:var(--textlt);margin-top:.4rem;">Padi: awal 1.05 · tengah 1.2 · akhir 0.75</p>
                </div>
            </div>

            {{-- Preview --}}
            <div id="preview-kalkulasi" class="preview-box" style="display:none;">
                <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--water);margin-bottom:1rem;">⚡ Preview Hasil Kalkulasi</p>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;text-align:center;">
                    <div>
                        <p style="font-size:.72rem;color:var(--textlt);margin-bottom:.3rem;">ETo</p>
                        <p style="font-family:'Fraunces',serif;font-size:1.8rem;font-weight:700;color:var(--water);line-height:1;" id="prev-eto">—</p>
                        <p style="font-size:.7rem;color:var(--textlt);margin-top:.2rem;">mm/hari</p>
                    </div>
                    <div>
                        <p style="font-size:.72rem;color:var(--textlt);margin-bottom:.3rem;">ETc</p>
                        <p style="font-family:'Fraunces',serif;font-size:1.8rem;font-weight:700;color:var(--water);line-height:1;" id="prev-etc">—</p>
                        <p style="font-size:.7rem;color:var(--textlt);margin-top:.2rem;">mm/hari</p>
                    </div>
                    <div>
                        <p style="font-size:.72rem;color:var(--textlt);margin-bottom:.3rem;">Kebutuhan Air</p>
                        <p style="font-family:'Fraunces',serif;font-size:1.8rem;font-weight:700;color:var(--leaf);line-height:1;" id="prev-kebutuhan">—</p>
                        <p style="font-size:.7rem;color:var(--textlt);margin-top:.2rem;">mm/hari</p>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:.75rem;align-items:center;">
                <button type="submit" class="btn-primary">Simpan Data</button>
                <a href="{{ route('irrigation') }}" class="btn-cancel">Batal</a>
            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function hitungETo(tmax, tmin, rh, ws, rs) {
        const tmean = (tmax + tmin) / 2;
        const delta = (4098 * (0.6108 * Math.exp((17.27 * tmean) / (tmean + 237.3)))) / Math.pow(tmean + 237.3, 2);
        const gamma = 0.0665;
        const es = (0.6108 * Math.exp((17.27 * tmax) / (tmax + 237.3)) + 0.6108 * Math.exp((17.27 * tmin) / (tmin + 237.3))) / 2;
        const ea = (rh / 100) * es;
        const Rn = 0.77 * rs;
        const ETo = (0.408 * delta * Rn + gamma * (900 / (tmean + 273)) * ws * (es - ea)) / (delta + gamma * (1 + 0.34 * ws));
        return Math.max(0, Math.round(ETo * 100) / 100);
    }

    function updatePreview() {
        const tmax = parseFloat(document.querySelector('[name=suhu_max]').value);
        const tmin = parseFloat(document.querySelector('[name=suhu_min]').value);
        const rh   = parseFloat(document.querySelector('[name=kelembaban]').value);
        const ws   = parseFloat(document.querySelector('[name=kecepatan_angin]').value);
        const rs   = parseFloat(document.querySelector('[name=radiasi_matahari]').value);
        const kc   = parseFloat(document.querySelector('[name=kc]').value);
        const ch   = parseFloat(document.querySelector('[name=curah_hujan]').value) || 0;
        if ([tmax, tmin, rh, ws, rs, kc].some(isNaN)) return;
        const eto = hitungETo(tmax, tmin, rh, ws, rs);
        const etc = Math.round(eto * kc * 100) / 100;
        const kebutuhan = Math.max(0, Math.round((etc - ch * 0.8) * 100) / 100);
        document.getElementById('prev-eto').textContent = eto;
        document.getElementById('prev-etc').textContent = etc;
        document.getElementById('prev-kebutuhan').textContent = kebutuhan;
        document.getElementById('preview-kalkulasi').style.display = 'block';
    }

    document.querySelectorAll('.input-field').forEach(i => i.addEventListener('input', updatePreview));
</script>
@endpush
