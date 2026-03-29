{{-- resources/views/blangko_o01/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Input O-01 — Smart Irrigation')
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
    .preview-box { background:rgba(74,124,111,.06);border:1.5px solid rgba(74,124,111,.2);border-radius:10px;padding:1.25rem;margin-top:1.25rem; }
</style>

<div style="margin-bottom:1.5rem;">
    <a href="{{ route('blangko-o01.index') }}" style="font-size:.82rem;color:var(--textlt);text-decoration:none;">← Kembali ke O-01</a>
</div>

<div style="max-width:760px;">
    <div style="margin-bottom:1.5rem;">
        <h2 style="font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--soil);">Input Blangko O-01</h2>
        <p style="font-size:.85rem;color:var(--textlt);margin-top:.25rem;">Usulan luas tanam per Daerah Irigasi per Musim Tanam</p>
    </div>

    @if($errors->any())
    <div style="background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.2);color:#a03828;border-radius:8px;padding:.8rem 1rem;font-size:.85rem;margin-bottom:1.25rem;">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('blangko-o01.store') }}">
            @csrf

            {{-- DI & MT --}}
            <div class="section-box">
                <p class="section-title">📍 Identitas</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Daerah Irigasi <span style="color:#a03828;">*</span></label>
                        <select name="daerah_irigasi_id" id="di-select" class="form-control" required onchange="updateDIInfo()">
                            <option value="">— Pilih DI —</option>
                            @foreach($daerahIrigasis as $di)
                                <option value="{{ $di->id }}"
                                    data-jenis="{{ $di->jenis }}"
                                    data-luas="{{ $di->luas_total }}"
                                    data-faktor="{{ $di->faktor_tersier ?? 0.83 }}"
                                    data-ska-padi-olah="{{ $di->ska_padi_pengolahan ?? 1.25 }}"
                                    data-ska-padi-tumbuh="{{ $di->ska_padi_pertumbuhan ?? 0.725 }}"
                                    data-ska-palawija="{{ $di->ska_palawija_banyak ?? 0.30 }}"
                                    data-pct="{{ $di->pct_kehilangan_air ?? 35 }}"
                                    {{ old('daerah_irigasi_id', $selectedDiId) == $di->id ? 'selected' : '' }}>
                                    {{ $di->kode }} — {{ $di->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('daerah_irigasi_id')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Musim Tanam <span style="color:#a03828;">*</span></label>
                        <select name="musim_tanam_id" class="form-control" required>
                            <option value="">— Pilih MT —</option>
                            @foreach($musimTanams as $mt)
                                <option value="{{ $mt->id }}" {{ old('musim_tanam_id', $selectedMtId) == $mt->id ? 'selected' : '' }}>
                                    {{ $mt->nama_mt }} ({{ $mt->jenis_mt }})
                                    @if($mt->status === 'berjalan') ← Aktif @endif
                                </option>
                            @endforeach
                        </select>
                        @error('musim_tanam_id')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                {{-- Info DI --}}
                <div id="di-info" style="display:none;margin-top:.75rem;padding:.6rem .9rem;background:var(--cream);border:1px solid var(--border);border-radius:8px;font-size:.82rem;color:var(--textlt);">
                    Jenis: <strong id="di-info-jenis">—</strong> &nbsp;|&nbsp; Luas Total: <strong id="di-info-luas">—</strong> ha
                </div>
            </div>

            {{-- Luas Usulan --}}
            <div class="section-box">
                <p class="section-title">🌾 Usulan Luas Tanam (ha)</p>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Padi</label>
                        <input type="number" name="luas_padi_usulan" id="luas-padi" class="form-control"
                            value="{{ old('luas_padi_usulan', 0) }}" step="0.01" min="0" oninput="hitungPreview()">
                        @error('luas_padi_usulan')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Palawija</label>
                        <input type="number" name="luas_palawija_usulan" id="luas-palawija" class="form-control"
                            value="{{ old('luas_palawija_usulan', 0) }}" step="0.01" min="0" oninput="hitungPreview()">
                        @error('luas_palawija_usulan')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Tebu</label>
                        <input type="number" name="luas_tebu_usulan" id="luas-tebu" class="form-control"
                            value="{{ old('luas_tebu_usulan', 0) }}" step="0.01" min="0" oninput="hitungPreview()">
                        @error('luas_tebu_usulan')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <p class="form-hint" style="margin-top:.75rem;">Isi 0 jika komoditas tersebut tidak ditanam.</p>
                <div id="luas-warning" style="display:none;margin-top:.75rem;padding:.6rem .9rem;background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.25);border-radius:8px;font-size:.82rem;color:#a03828;font-weight:600;"></div>
            </div>

            {{-- Preview Kebutuhan Air --}}
            <div class="preview-box" id="preview-box" style="display:none;">
                <p style="font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--water);margin-bottom:.75rem;">💧 Estimasi Kebutuhan Air (O-05)</p>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;text-align:center;">
                    <div>
                        <p style="font-size:.72rem;color:var(--textlt);margin-bottom:.25rem;">Fase Pengolahan</p>
                        <p style="font-family:'Fraunces',serif;font-size:1.4rem;font-weight:700;color:var(--soil);" id="prev-olah">—</p>
                        <p style="font-size:.7rem;color:var(--textlt);">l/det</p>
                    </div>
                    <div>
                        <p style="font-size:.72rem;color:var(--textlt);margin-bottom:.25rem;">Fase Pertumbuhan</p>
                        <p style="font-family:'Fraunces',serif;font-size:1.4rem;font-weight:700;color:var(--water);" id="prev-tumbuh">—</p>
                        <p style="font-size:.7rem;color:var(--textlt);">l/det</p>
                    </div>
                    <div>
                        <p style="font-size:.72rem;color:var(--textlt);margin-bottom:.25rem;">Total Luas</p>
                        <p style="font-family:'Fraunces',serif;font-size:1.4rem;font-weight:700;color:var(--earth);" id="prev-total-luas">—</p>
                        <p style="font-size:.7rem;color:var(--textlt);">ha</p>
                    </div>
                </div>
                <p style="font-size:.72rem;color:var(--textlt);margin-top:.75rem;text-align:center;">
                    Nilai SKA sesuai Permen PU No. 32/PRT/M/2007 · <span id="prev-faktor">—</span>
                </p>
            </div>

            <div class="form-group" style="margin-top:1.25rem;">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
            </div>

            <div style="display:flex;gap:.75rem;margin-top:1.5rem;">
                <button type="submit"
                    style="background:var(--soil);color:var(--straw);padding:.65rem 1.5rem;border-radius:8px;font-size:.875rem;font-weight:600;border:none;cursor:pointer;font-family:'Karla',sans-serif;"
                    onmouseover="this.style.background='var(--soil2)'" onmouseout="this.style.background='var(--soil)'">
                    Simpan O-01
                </button>
                <a href="{{ route('blangko-o01.index') }}"
                    style="background:rgba(139,94,60,.08);border:1.5px solid var(--border);color:var(--textlt);padding:.65rem 1.25rem;border-radius:8px;font-size:.875rem;font-weight:600;text-decoration:none;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let diData = {};

function updateDIInfo() {
    const sel = document.getElementById('di-select');
    const opt = sel.options[sel.selectedIndex];
    if (!opt.value) {
        document.getElementById('di-info').style.display = 'none';
        document.getElementById('preview-box').style.display = 'none';
        diData = {};
        return;
    }
    diData = {
        jenis:        opt.dataset.jenis,
        luas:         parseFloat(opt.dataset.luas) || 0,
        faktor:       parseFloat(opt.dataset.faktor) || 0.83,
        skaOlah:      parseFloat(opt.dataset.skaOlahPadi || opt.dataset.skaPadiOlah) || 1.25,
        skaTumbuh:    parseFloat(opt.dataset.skaPadiTumbuh) || 0.725,
        skaPalawija:  parseFloat(opt.dataset.skaPalawija) || 0.30,
        pct:          parseFloat(opt.dataset.pct) || 35,
    };
    const infoEl = document.getElementById('di-info');
    infoEl.style.display = 'block';
    document.getElementById('di-info-jenis').textContent = diData.jenis === 'permukaan' ? 'DIP (Permukaan)' : 'DIR (Rawa)';
    document.getElementById('di-info-luas').textContent  = diData.luas.toLocaleString('id-ID');
    hitungPreview();
}

function hitungPreview() {
    if (!diData.jenis) return;

    const padi     = parseFloat(document.getElementById('luas-padi').value)    || 0;
    const palawija = parseFloat(document.getElementById('luas-palawija').value) || 0;
    const tebu     = parseFloat(document.getElementById('luas-tebu').value)     || 0;
    const total    = padi + palawija + tebu;

    const warningEl = document.getElementById('luas-warning');
    if (diData.luas > 0 && total > diData.luas) {
        warningEl.style.display = 'block';
        warningEl.textContent   = `⚠️ Total luas (${total.toFixed(2)} ha) melebihi luas DI (${diData.luas.toFixed(2)} ha)!`;
    } else {
        warningEl.style.display = 'none';
    }

    if (total <= 0) {
        document.getElementById('preview-box').style.display = 'none';
        return;
    }

    document.getElementById('preview-box').style.display = 'block';
    document.getElementById('prev-total-luas').textContent = total.toLocaleString('id-ID', {minimumFractionDigits:2});

    let olah, tumbuh, faktorLabel;

    if (diData.jenis === 'rawa') {
        const val = (total * diData.pct / 100).toFixed(2);
        olah  = val;
        tumbuh = val;
        faktorLabel = `Kehilangan air ${diData.pct}% (DIR)`;
    } else {
        const f = diData.faktor;
        olah   = (padi * diData.skaOlah * f + palawija * diData.skaPalawija * f + tebu * diData.skaTumbuh * f).toFixed(2);
        tumbuh = (padi * diData.skaTumbuh * f + palawija * diData.skaPalawija * f + tebu * diData.skaTumbuh * f).toFixed(2);
        faktorLabel = `Faktor Tersier ${f} (DIP)`;
    }

    document.getElementById('prev-olah').textContent    = parseFloat(olah).toLocaleString('id-ID', {minimumFractionDigits:2});
    document.getElementById('prev-tumbuh').textContent  = parseFloat(tumbuh).toLocaleString('id-ID', {minimumFractionDigits:2});
    document.getElementById('prev-faktor').textContent  = faktorLabel;
}

// Init jika sudah ada value dari old()
document.addEventListener('DOMContentLoaded', function() {
    updateDIInfo();
});
</script>
@endpush
