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
@endsection
