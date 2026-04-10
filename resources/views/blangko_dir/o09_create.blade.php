@extends('layouts.app')
@section('title', 'Input O-09 DIR')
@section('page-title', 'Input O-09 DIR')

@section('content')
<style>
    .form-control { width:100%;padding:.6rem .9rem;border:1.5px solid var(--border);border-radius:8px;font-size:.875rem;font-family:'Karla',sans-serif;color:var(--text);background:var(--cream);outline:none; }
    .form-control:focus { border-color:var(--water2); }
    .section-box { background:var(--cream2);border:1px solid var(--border);border-radius:10px;padding:1.25rem;margin-bottom:1.25rem; }
    .section-title { font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--textlt);margin-bottom:1rem; }
</style>

<div style="margin-bottom:1.5rem;">
    <a href="{{ route('blangko-dir.o09.index') }}" style="font-size:.82rem;color:var(--textlt);text-decoration:none;">← Kembali ke O-09</a>
</div>

<div style="max-width:760px;">
    <h2 style="font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--soil);margin-bottom:1.5rem;">Input Data O-09 DIR</h2>

    @if($errors->any())
    <div style="background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.2);color:#a03828;border-radius:8px;padding:.8rem 1rem;font-size:.85rem;margin-bottom:1.25rem;">
        {{ $errors->first() }}
    </div>
    @endif

    <div style="background:var(--cream);border:1px solid var(--border);border-radius:14px;padding:1.75rem;">
        <form method="POST" action="{{ route('blangko-dir.o09.store') }}">
            @csrf

            {{-- Identitas --}}
            <div class="section-box">
                <p class="section-title">📍 Identitas</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                    <div>
                        <label style="font-size:.75rem;font-weight:700;color:var(--textlt);display:block;margin-bottom:.4rem;">Daerah Irigasi</label>
                        <select name="daerah_irigasi_id" id="di-select" class="form-control" required onchange="loadPetaks(this.value)">
                            <option value="">— Pilih DI Rawa —</option>
                            @foreach($daerahIrigasis as $di)
                                <option value="{{ $di->id }}" {{ $diId == $di->id ? 'selected' : '' }}>
                                    {{ $di->kode }} — {{ $di->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:.75rem;font-weight:700;color:var(--textlt);display:block;margin-bottom:.4rem;">Musim Tanam</label>
                        <select name="musim_tanam_id" class="form-control" required>
                            <option value="">— Pilih MT —</option>
                            @foreach($musimTanams as $mt)
                                <option value="{{ $mt->id }}" {{ $mtId == $mt->id ? 'selected' : '' }}>
                                    {{ $mt->nama_mt }} ({{ $mt->jenis_mt }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
                    <div>
                        <label style="font-size:.75rem;font-weight:700;color:var(--textlt);display:block;margin-bottom:.4rem;">Petak Tersier</label>
                        <select name="petak_id" id="petak-select" class="form-control" required>
                            <option value="">— Pilih Petak —</option>
                            @foreach($petaks as $petak)
                                <option value="{{ $petak->id }}">{{ $petak->kode_petak }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:.75rem;font-weight:700;color:var(--textlt);display:block;margin-bottom:.4rem;">Bulan</label>
                        <select name="bulan" class="form-control" required>
                            <option value="">— Pilih Bulan —</option>
                            @foreach($bulan as $b)
                                <option value="{{ $b['bulan'] }}" {{ old('bulan') == $b['bulan'] ? 'selected' : '' }}>
                                    {{ $b['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:.75rem;font-weight:700;color:var(--textlt);display:block;margin-bottom:.4rem;">Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="{{ old('tahun', now()->year) }}" required>
                    </div>
                </div>
            </div>

            {{-- Data Tanaman --}}
            <div class="section-box">
                <p class="section-title">🌾 Rencana & Realisasi Luas Tanam (ha)</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

                    <div>
                        <p style="font-size:.78rem;font-weight:700;color:var(--water);margin-bottom:.75rem;">💧 Padi</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                            <div>
                                <label style="font-size:.72rem;color:var(--textlt);display:block;margin-bottom:.3rem;">Rencana (ha)</label>
                                <input type="number" name="rencana_padi" class="form-control" value="{{ old('rencana_padi', 0) }}" step="0.01" min="0">
                            </div>
                            <div>
                                <label style="font-size:.72rem;color:var(--textlt);display:block;margin-bottom:.3rem;">Realisasi (ha)</label>
                                <input type="number" name="realisasi_padi" class="form-control" value="{{ old('realisasi_padi', 0) }}" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <div>
                        <p style="font-size:.78rem;font-weight:700;color:var(--leaf);margin-bottom:.75rem;">🌿 Palawija</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                            <div>
                                <label style="font-size:.72rem;color:var(--textlt);display:block;margin-bottom:.3rem;">Rencana (ha)</label>
                                <input type="number" name="rencana_palawija" class="form-control" value="{{ old('rencana_palawija', 0) }}" step="0.01" min="0">
                            </div>
                            <div>
                                <label style="font-size:.72rem;color:var(--textlt);display:block;margin-bottom:.3rem;">Realisasi (ha)</label>
                                <input type="number" name="realisasi_palawija" class="form-control" value="{{ old('realisasi_palawija', 0) }}" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <div>
                        <p style="font-size:.78rem;font-weight:700;color:var(--earth);margin-bottom:.75rem;">🌳 Tanaman Keras</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                            <div>
                                <label style="font-size:.72rem;color:var(--textlt);display:block;margin-bottom:.3rem;">Rencana (ha)</label>
                                <input type="number" name="rencana_tanaman_keras" class="form-control" value="{{ old('rencana_tanaman_keras', 0) }}" step="0.01" min="0">
                            </div>
                            <div>
                                <label style="font-size:.72rem;color:var(--textlt);display:block;margin-bottom:.3rem;">Realisasi (ha)</label>
                                <input type="number" name="realisasi_tanaman_keras" class="form-control" value="{{ old('realisasi_tanaman_keras', 0) }}" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <div>
                        <p style="font-size:.78rem;font-weight:700;color:var(--textlt);margin-bottom:.75rem;">⬜ Bera</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                            <div>
                                <label style="font-size:.72rem;color:var(--textlt);display:block;margin-bottom:.3rem;">Rencana (ha)</label>
                                <input type="number" name="rencana_bera" class="form-control" value="{{ old('rencana_bera', 0) }}" step="0.01" min="0">
                            </div>
                            <div>
                                <label style="font-size:.72rem;color:var(--textlt);display:block;margin-bottom:.3rem;">Realisasi (ha)</label>
                                <input type="number" name="realisasi_bera" class="form-control" value="{{ old('realisasi_bera', 0) }}" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div style="margin-bottom:1.25rem;">
                <label style="font-size:.75rem;font-weight:700;color:var(--textlt);display:block;margin-bottom:.4rem;">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
            </div>

            <div style="display:flex;gap:.75rem;">
                <button type="submit" style="background:var(--soil);color:var(--straw);padding:.65rem 1.5rem;border-radius:8px;font-size:.875rem;font-weight:600;border:none;cursor:pointer;font-family:'Karla',sans-serif;">
                    Simpan O-09
                </button>
                <a href="{{ route('blangko-dir.o09.index') }}" style="background:rgba(139,94,60,.08);border:1.5px solid var(--border);color:var(--textlt);padding:.65rem 1.25rem;border-radius:8px;font-size:.875rem;font-weight:600;text-decoration:none;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loadPetaks(diId) {
    if (!diId) return;
    fetch(`/api/daerah-irigasi/${diId}/petaks`)
        .then(r => r.json())
        .then(petaks => {
            const sel = document.getElementById('petak-select');
            sel.innerHTML = '<option value="">— Pilih Petak —</option>';
            petaks.forEach(p => {
                sel.innerHTML += `<option value="${p.id}">${p.kode_petak}</option>`;
            });
        });
}
</script>
@endpush
