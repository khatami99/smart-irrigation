@extends('layouts.app')
@section('title', 'Edit Musim Tanam — Smart Irrigation')
@section('page-title', 'Edit Musim Tanam')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--leaf:#5a7a47;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
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
    .info-box { background:rgba(74,124,111,.06);border:1px solid rgba(74,124,111,.15);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;font-size:.82rem;color:var(--textlt);line-height:1.6; }
</style>

<div style="max-width:640px;margin:0 auto;">
    <a href="{{ route('musim-tanam.index') }}" style="display:inline-flex;align-items:center;gap:.4rem;font-size:.82rem;color:var(--textlt);text-decoration:none;margin-bottom:1.25rem;"
       onmouseover="this.style.color='var(--water)'" onmouseout="this.style.color='var(--textlt)'">
        ← Kembali ke Daftar MT
    </a>

    <div class="form-card">
        <div style="margin-bottom:1.75rem;">
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Edit Data</p>
            <h2 style="font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--soil);letter-spacing:-.02em;">Edit Musim Tanam</h2>
            <p style="font-size:.85rem;color:var(--textlt);font-weight:300;margin-top:.25rem;">
                Mengedit: <strong style="color:var(--soil);">{{ $musimTanam->nama_mt }}</strong>
            </p>
        </div>

        <div class="info-box">
            ℹ️ Jika status diubah ke <strong>Berjalan</strong>, musim tanam lain yang sedang berjalan akan otomatis diubah ke <strong>Selesai</strong>.
        </div>

        @if($errors->any())
            <div style="background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.2);color:#a03828;border-radius:8px;padding:.8rem 1rem;font-size:.85rem;margin-bottom:1.25rem;">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('musim-tanam.update', $musimTanam) }}">
            @csrf
            @method('PUT')

            {{-- Identitas MT --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">📋 Identitas Musim Tanam</p>
                <div style="display:grid;grid-template-columns:2fr 1fr;gap:1rem;">
                    <div>
                        <label class="form-label">Nama MT <span>*</span></label>
                        <input type="text" name="nama_mt"
                            value="{{ old('nama_mt', $musimTanam->nama_mt) }}"
                            required placeholder="cth: MT1 2025/2026" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Jenis MT <span>*</span></label>
                        <select name="jenis_mt" required class="form-input">
                            @foreach(['MT1','MT2','MT3','MK'] as $jenis)
                                <option value="{{ $jenis }}"
                                    {{ old('jenis_mt', $musimTanam->jenis_mt) == $jenis ? 'selected' : '' }}>
                                    {{ $jenis }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Jadwal --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">📅 Jadwal</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="form-label">Tanggal Mulai <span>*</span></label>
                        <input type="date" name="tanggal_mulai"
                            value="{{ old('tanggal_mulai', $musimTanam->tanggal_mulai->format('Y-m-d')) }}"
                            required class="form-input" id="tgl-mulai">
                    </div>
                    <div>
                        <label class="form-label">Tanggal Selesai <span>*</span></label>
                        <input type="date" name="tanggal_selesai"
                            value="{{ old('tanggal_selesai', $musimTanam->tanggal_selesai->format('Y-m-d')) }}"
                            required class="form-input" id="tgl-selesai">
                    </div>
                </div>
                <p id="durasi-info" style="font-size:.75rem;color:var(--leaf);margin-top:.4rem;min-height:1rem;">
                    ✓ Durasi saat ini: {{ $musimTanam->durasi_hari }} hari
                </p>
            </div>

            {{-- Target --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">🌾 Target Produksi</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="form-label">Target Luas Tanam <span>(ha) *</span></label>
                        <input type="number" name="target_luas_tanam"
                            value="{{ old('target_luas_tanam', $musimTanam->target_luas_tanam) }}"
                            step="0.01" min="0" required placeholder="cth: 150.00" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Jenis Tanaman <span>*</span></label>
                        <input type="text" name="jenis_tanaman"
                            value="{{ old('jenis_tanaman', $musimTanam->jenis_tanaman) }}"
                            required placeholder="cth: Padi, Palawija" class="form-input">
                    </div>
                </div>
            </div>

            {{-- Status & Keterangan --}}
            <div style="margin-bottom:1.75rem;">
                <p class="section-label">⚙️ Status & Keterangan</p>
                <div style="display:grid;grid-template-columns:1fr 2fr;gap:1rem;">
                    <div>
                        <label class="form-label">Status <span>*</span></label>
                        <select name="status" required class="form-input">
                            <option value="rencana"  {{ old('status', $musimTanam->status) == 'rencana'  ? 'selected' : '' }}>Rencana</option>
                            <option value="berjalan" {{ old('status', $musimTanam->status) == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                            <option value="selesai"  {{ old('status', $musimTanam->status) == 'selesai'  ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Keterangan <span>(opsional)</span></label>
                        <input type="text" name="keterangan"
                            value="{{ old('keterangan', $musimTanam->keterangan) }}"
                            placeholder="Catatan tambahan..." class="form-input">
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:.75rem;">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
                <a href="{{ route('musim-tanam.index') }}" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function hitungDurasi() {
        const mulai   = document.getElementById('tgl-mulai').value;
        const selesai = document.getElementById('tgl-selesai').value;
        const info    = document.getElementById('durasi-info');
        if (!mulai || !selesai) { info.textContent = ''; return; }
        const diff = Math.round((new Date(selesai) - new Date(mulai)) / (1000*60*60*24));
        if (diff <= 0) {
            info.textContent = '⚠️ Tanggal selesai harus setelah tanggal mulai.';
            info.style.color = '#a03828';
        } else {
            info.textContent = `✓ Durasi: ${diff} hari`;
            info.style.color = 'var(--leaf)';
        }
    }
    document.getElementById('tgl-mulai').addEventListener('change', hitungDurasi);
    document.getElementById('tgl-selesai').addEventListener('change', hitungDurasi);
</script>
@endpush
