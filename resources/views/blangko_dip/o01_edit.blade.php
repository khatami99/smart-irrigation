{{-- resources/views/blangko_o01/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit O-01 — Smart Irrigation')
@section('page-title', 'Blangko O-01 Usulan Luas Tanam')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--earth:#8b5e3c;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .card { background:var(--cream);border:1px solid var(--border);border-radius:14px;padding:1.75rem; }
    .form-group { margin-bottom:1.25rem; }
    .form-label { display:block;font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--textlt);margin-bottom:.5rem; }
    .form-control { width:100%;padding:.65rem .9rem;border:1.5px solid var(--border);border-radius:8px;font-size:.9rem;font-family:'Karla',sans-serif;color:var(--text);background:var(--cream);transition:border .2s; }
    .form-control:focus { outline:none;border-color:var(--water2); }
    .form-error { font-size:.78rem;color:#a03828;margin-top:.35rem; }
    .form-hint { font-size:.75rem;color:var(--textlt);margin-top:.35rem; }
    .section-box { background:var(--cream2);border:1px solid var(--border);border-radius:10px;padding:1.25rem;margin-bottom:1.25rem; }
    .section-title { font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--textlt);margin-bottom:1rem; }
</style>

<div style="margin-bottom:1.5rem;">
    <a href="{{ route('blangko-dip.o01.index') }}" style="font-size:.82rem;color:var(--textlt);text-decoration:none;">← Kembali ke O-01</a>
</div>

<div style="max-width:760px;">
    <div style="margin-bottom:1.5rem;">
        <h2 style="font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--soil);">Edit Blangko O-01</h2>
        <p style="font-size:.85rem;color:var(--textlt);margin-top:.25rem;">
            <strong>{{ $blangkoO01->daerahIrigasi->nama }}</strong> — {{ $blangkoO01->musimTanam->nama_mt }}
        </p>
    </div>

    @if($errors->any())
    <div style="background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.2);color:#a03828;border-radius:8px;padding:.8rem 1rem;font-size:.85rem;margin-bottom:1.25rem;">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('blangko-dip.o01.update', $blangkoO01) }}">
            @csrf @method('PUT')

            {{-- Info readonly --}}
            <div class="section-box">
                <p class="section-title">📍 Identitas</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <p class="form-label">Daerah Irigasi</p>
                        <p style="font-weight:600;color:var(--soil);">{{ $blangkoO01->daerahIrigasi->kode }} — {{ $blangkoO01->daerahIrigasi->nama }}</p>
                    </div>
                    <div>
                        <p class="form-label">Musim Tanam</p>
                        <p style="font-weight:600;color:var(--soil);">{{ $blangkoO01->musimTanam->nama_mt }}</p>
                    </div>
                </div>
            </div>

            {{-- Luas Usulan --}}
            <div class="section-box">
                <p class="section-title">🌾 Usulan Luas Tanam (ha)</p>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Padi</label>
                        <input type="number" name="luas_padi_usulan" class="form-control"
                            value="{{ old('luas_padi_usulan', $blangkoO01->luas_padi_usulan) }}" step="0.01" min="0">
                        @error('luas_padi_usulan')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Palawija</label>
                        <input type="number" name="luas_palawija_usulan" class="form-control"
                            value="{{ old('luas_palawija_usulan', $blangkoO01->luas_palawija_usulan) }}" step="0.01" min="0">
                        @error('luas_palawija_usulan')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Tebu</label>
                        <input type="number" name="luas_tebu_usulan" class="form-control"
                            value="{{ old('luas_tebu_usulan', $blangkoO01->luas_tebu_usulan) }}" step="0.01" min="0">
                        @error('luas_tebu_usulan')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Luas Disetujui (admin only) --}}
            @if(auth()->user()->hasRole('admin') || auth()->user()->can('delete blangko-op'))
            <div class="section-box" style="border-color:rgba(74,124,111,.3);">
                <p class="section-title" style="color:var(--water);">✅ Keputusan Luas Tanam (ha) — Admin/Dinas</p>
                <p class="form-hint" style="margin-bottom:.75rem;margin-top:-.5rem;">Kosongkan untuk mengikuti nilai usulan.</p>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Padi Disetujui</label>
                        <input type="number" name="luas_padi_disetujui" class="form-control"
                            value="{{ old('luas_padi_disetujui', $blangkoO01->luas_padi_disetujui) }}" step="0.01" min="0">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Palawija Disetujui</label>
                        <input type="number" name="luas_palawija_disetujui" class="form-control"
                            value="{{ old('luas_palawija_disetujui', $blangkoO01->luas_palawija_disetujui) }}" step="0.01" min="0">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Tebu Disetujui</label>
                        <input type="number" name="luas_tebu_disetujui" class="form-control"
                            value="{{ old('luas_tebu_disetujui', $blangkoO01->luas_tebu_disetujui) }}" step="0.01" min="0">
                    </div>
                </div>
                <div class="form-group" style="margin-top:1rem;margin-bottom:0;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="usulan"    {{ old('status', $blangkoO01->status) === 'usulan'    ? 'selected' : '' }}>Usulan</option>
                        <option value="disetujui" {{ old('status', $blangkoO01->status) === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="revisi"    {{ old('status', $blangkoO01->status) === 'revisi'    ? 'selected' : '' }}>Perlu Revisi</option>
                    </select>
                </div>
            </div>
            @endif

            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $blangkoO01->keterangan) }}</textarea>
            </div>

            <div style="display:flex;gap:.75rem;margin-top:1.5rem;">
                <button type="submit"
                    style="background:var(--soil);color:var(--straw);padding:.65rem 1.5rem;border-radius:8px;font-size:.875rem;font-weight:600;border:none;cursor:pointer;font-family:'Karla',sans-serif;"
                    onmouseover="this.style.background='var(--soil2)'" onmouseout="this.style.background='var(--soil)'">
                    Update O-01
                </button>
                <a href="{{ route('blangko-dip.o01.index') }}"
                    style="background:rgba(139,94,60,.08);border:1.5px solid var(--border);color:var(--textlt);padding:.65rem 1.25rem;border-radius:8px;font-size:.875rem;font-weight:600;text-decoration:none;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
