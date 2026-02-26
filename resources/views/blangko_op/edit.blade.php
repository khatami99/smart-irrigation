@extends('layouts.app')
@section('title', 'Input Blangko OP — Smart Irrigation')
@section('page-title', 'Input Blangko OP')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;--clay:#c4895a;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .form-card { background:var(--cream);border:1px solid var(--border);border-radius:14px;padding:2rem; }
    .form-label { display:block;font-size:.8rem;font-weight:600;color:var(--text);margin-bottom:.45rem; }
    .form-label span { font-weight:300;color:var(--textlt); }
    .form-input { width:100%;background:var(--cream);border:1.5px solid rgba(139,94,60,.18);color:var(--text);border-radius:8px;padding:.75rem 1rem;font-size:.875rem;font-family:'Karla',sans-serif;transition:border-color .2s,box-shadow .2s;outline:none; }
    .form-input:focus { border-color:var(--water);box-shadow:0 0 0 3px rgba(74,124,111,.1); }
    .form-input::placeholder { color:rgba(122,99,85,.35); }
    .section-label { font-size:.65rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--textlt);margin-bottom:1rem;padding-bottom:.5rem;border-bottom:1px solid rgba(139,94,60,.1); }
    .btn-primary { background:var(--soil);color:var(--straw);padding:.8rem 2rem;border-radius:8px;border:none;font-family:'Karla',sans-serif;font-size:.9rem;font-weight:600;cursor:pointer;transition:background .2s; }
    .btn-primary:hover { background:var(--soil2); }
    .btn-cancel { background:rgba(139,94,60,.08);color:var(--textlt);padding:.8rem 2rem;border-radius:8px;border:1px solid var(--border);font-family:'Karla',sans-serif;font-size:.9rem;font-weight:600;text-decoration:none; }
    .btn-cancel:hover { background:rgba(139,94,60,.14); }
    .rencana-realisasi { display:grid;grid-template-columns:1fr 1fr;gap:.75rem; }
    .sub-label { font-size:.72rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--textlt);margin-bottom:.35rem; }
    .efisiensi-preview { font-size:.75rem;margin-top:.35rem;min-height:1rem; }
    .kondisi-group { display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem; }
    .kondisi-option input[type=radio] { display:none; }
    .kondisi-option label {
        display:block;text-align:center;padding:.6rem .5rem;border-radius:8px;
        border:1.5px solid var(--border);font-size:.78rem;font-weight:600;
        cursor:pointer;transition:all .2s;color:var(--textlt);
    }
    .kondisi-option input[type=radio]:checked + label { border-width:2px; }
    .kondisi-option.baik input:checked         + label { background:rgba(90,122,71,.1);border-color:var(--leaf);color:var(--leaf); }
    .kondisi-option.rusak_ringan input:checked + label { background:rgba(196,137,90,.1);border-color:var(--clay);color:var(--clay); }
    .kondisi-option.rusak_berat input:checked  + label { background:rgba(185,74,60,.08);border-color:#a03828;color:#a03828; }
</style>

<div style="max-width:700px;margin:0 auto;">
    <a href="{{ route('blangko-op.index') }}" style="display:inline-flex;align-items:center;gap:.4rem;font-size:.82rem;color:var(--textlt);text-decoration:none;margin-bottom:1.25rem;"
       onmouseover="this.style.color='var(--water)'" onmouseout="this.style.color='var(--textlt)'">
        ← Kembali ke Daftar Blangko OP
    </a>

    <div class="form-card">
        <div style="margin-bottom:1.75rem;">
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Form Input</p>
            <h2 style="font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--soil);letter-spacing:-.02em;">Blangko OP Per Dekade</h2>
            <p style="font-size:.85rem;color:var(--textlt);font-weight:300;margin-top:.25rem;">Catatan pengamatan lapangan per 10 harian.</p>
        </div>

        @if($errors->any())
            <div style="background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.2);color:#a03828;border-radius:8px;padding:.8rem 1rem;font-size:.85rem;margin-bottom:1.25rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('blangko-op.store') }}">
            @csrf

            {{-- Identitas --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">📍 Identitas Catatan</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                    <div>
                        <label class="form-label">Petak <span>*</span></label>
                        <select name="petak_id" required class="form-input">
                            <option value="">— Pilih Petak —</option>
                            @foreach($petaks as $petak)
                                <option value="{{ $petak->id }}" {{ old('petak_id') == $petak->id ? 'selected' : '' }}>
                                    {{ $petak->kode_petak }} — {{ $petak->nama_petak }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Musim Tanam <span>*</span></label>
                        <select name="musim_tanam_id" required class="form-input">
                            <option value="">— Pilih MT —</option>
                            @foreach($musimTanams as $mt)
                                <option value="{{ $mt->id }}"
                                    {{ old('musim_tanam_id', $mtAktif?->id) == $mt->id ? 'selected' : '' }}>
                                    {{ $mt->nama_mt }}
                                    @if($mt->status === 'berjalan') (Aktif) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
                    <div>
                        <label class="form-label">Tahun <span>*</span></label>
                        <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}"
                            min="2000" max="2100" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Bulan <span>*</span></label>
                        <select name="bulan" required class="form-input">
                            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                                <option value="{{ $i+1 }}" {{ old('bulan', date('n')) == $i+1 ? 'selected' : '' }}>{{ $bln }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Dekade <span>*</span></label>
                        <select name="dekade" required class="form-input">
                            <option value="I"   {{ old('dekade') == 'I'   ? 'selected' : '' }}>I &nbsp;— Tgl 1–10</option>
                            <option value="II"  {{ old('dekade') == 'II'  ? 'selected' : '' }}>II — Tgl 11–20</option>
                            <option value="III" {{ old('dekade') == 'III' ? 'selected' : '' }}>III — Tgl 21–akhir</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Debit Air --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">💧 Debit Air (liter/detik)</p>
                <div class="rencana-realisasi">
                    <div>
                        <p class="sub-label">Rencana</p>
                        <input type="number" name="debit_rencana" value="{{ old('debit_rencana') }}"
                            step="0.01" min="0" placeholder="cth: 120.5" class="form-input" id="debit-r"
                            oninput="hitungEfisiensi('debit')">
                    </div>
                    <div>
                        <p class="sub-label">Realisasi</p>
                        <input type="number" name="debit_realisasi" value="{{ old('debit_realisasi') }}"
                            step="0.01" min="0" placeholder="cth: 105.0" class="form-input" id="debit-a"
                            oninput="hitungEfisiensi('debit')">
                    </div>
                </div>
                <p class="efisiensi-preview" id="efisiensi-debit"></p>
            </div>

            {{-- Luas Areal --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">🌾 Luas Areal (ha)</p>
                <div class="rencana-realisasi">
                    <div>
                        <p class="sub-label">Rencana</p>
                        <input type="number" name="luas_rencana" value="{{ old('luas_rencana') }}"
                            step="0.01" min="0" placeholder="cth: 45.0" class="form-input" id="luas-r"
                            oninput="hitungEfisiensi('luas')">
                    </div>
                    <div>
                        <p class="sub-label">Realisasi</p>
                        <input type="number" name="luas_realisasi" value="{{ old('luas_realisasi') }}"
                            step="0.01" min="0" placeholder="cth: 42.5" class="form-input" id="luas-a"
                            oninput="hitungEfisiensi('luas')">
                    </div>
                </div>
                <p class="efisiensi-preview" id="efisiensi-luas"></p>
            </div>

            {{-- TMA & Curah Hujan --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">📏 Pengukuran Lapangan</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="form-label">Tinggi Muka Air <span>(cm)</span></label>
                        <input type="number" name="tinggi_muka_air" value="{{ old('tinggi_muka_air') }}"
                            step="0.1" min="0" placeholder="cth: 45.5" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Curah Hujan Dekade <span>(mm)</span></label>
                        <input type="number" name="curah_hujan" value="{{ old('curah_hujan', 0) }}"
                            step="0.1" min="0" placeholder="cth: 35.0" class="form-input">
                    </div>
                </div>
            </div>

            {{-- Fase Pertumbuhan --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">🌱 Fase Pertumbuhan Tanaman</p>
                <select name="fase_pertumbuhan" class="form-input">
                    <option value="">— Pilih Fase —</option>
                    <option value="pengolahan_tanah" {{ old('fase_pertumbuhan') == 'pengolahan_tanah' ? 'selected' : '' }}>Pengolahan Tanah</option>
                    <option value="tanam"            {{ old('fase_pertumbuhan') == 'tanam'            ? 'selected' : '' }}>Tanam</option>
                    <option value="vegetatif"        {{ old('fase_pertumbuhan') == 'vegetatif'        ? 'selected' : '' }}>Vegetatif (Pertumbuhan)</option>
                    <option value="generatif"        {{ old('fase_pertumbuhan') == 'generatif'        ? 'selected' : '' }}>Generatif (Berbunga)</option>
                    <option value="panen"            {{ old('fase_pertumbuhan') == 'panen'            ? 'selected' : '' }}>Panen</option>
                    <option value="bero"             {{ old('fase_pertumbuhan') == 'bero'             ? 'selected' : '' }}>Bero (Lahan Kosong)</option>
                </select>
            </div>

            {{-- Kondisi Saluran --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">🔧 Kondisi Saluran & Bangunan</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                    <div>
                        <p style="font-size:.78rem;font-weight:600;color:var(--text);margin-bottom:.6rem;">Saluran Irigasi</p>
                        <div class="kondisi-group">
                            <div class="kondisi-option baik">
                                <input type="radio" name="kondisi_saluran" id="sal-baik" value="baik" {{ old('kondisi_saluran') == 'baik' ? 'checked' : '' }}>
                                <label for="sal-baik">✓ Baik</label>
                            </div>
                            <div class="kondisi-option rusak_ringan">
                                <input type="radio" name="kondisi_saluran" id="sal-rr" value="rusak_ringan" {{ old('kondisi_saluran') == 'rusak_ringan' ? 'checked' : '' }}>
                                <label for="sal-rr">⚠ Ringan</label>
                            </div>
                            <div class="kondisi-option rusak_berat">
                                <input type="radio" name="kondisi_saluran" id="sal-rb" value="rusak_berat" {{ old('kondisi_saluran') == 'rusak_berat' ? 'checked' : '' }}>
                                <label for="sal-rb">✕ Berat</label>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p style="font-size:.78rem;font-weight:600;color:var(--text);margin-bottom:.6rem;">Bangunan Air</p>
                        <div class="kondisi-group">
                            <div class="kondisi-option baik">
                                <input type="radio" name="kondisi_bangunan" id="bang-baik" value="baik" {{ old('kondisi_bangunan') == 'baik' ? 'checked' : '' }}>
                                <label for="bang-baik">✓ Baik</label>
                            </div>
                            <div class="kondisi-option rusak_ringan">
                                <input type="radio" name="kondisi_bangunan" id="bang-rr" value="rusak_ringan" {{ old('kondisi_bangunan') == 'rusak_ringan' ? 'checked' : '' }}>
                                <label for="bang-rr">⚠ Ringan</label>
                            </div>
                            <div class="kondisi-option rusak_berat">
                                <input type="radio" name="kondisi_bangunan" id="bang-rb" value="rusak_berat" {{ old('kondisi_bangunan') == 'rusak_berat' ? 'checked' : '' }}>
                                <label for="bang-rb">✕ Berat</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="margin-top:1rem;">
                    <label class="form-label">Catatan Kondisi <span>(opsional)</span></label>
                    <textarea name="catatan_kondisi" rows="2" placeholder="Deskripsikan kerusakan atau catatan kondisi lapangan..."
                        class="form-input" style="resize:vertical;">{{ old('catatan_kondisi') }}</textarea>
                </div>
            </div>

            {{-- Keterangan --}}
            <div style="margin-bottom:1.75rem;">
                <label class="form-label">Keterangan Umum <span>(opsional)</span></label>
                <textarea name="keterangan" rows="2" placeholder="Catatan tambahan lainnya..."
                    class="form-input" style="resize:vertical;">{{ old('keterangan') }}</textarea>
            </div>

            <div style="display:flex;gap:.75rem;">
                <button type="submit" class="btn-primary">Simpan Blangko OP</button>
                <a href="{{ route('blangko-op.index') }}" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function hitungEfisiensi(jenis) {
        const r   = parseFloat(document.getElementById(jenis+'-r').value);
        const a   = parseFloat(document.getElementById(jenis+'-a').value);
        const el  = document.getElementById('efisiensi-'+jenis);
        if (isNaN(r) || isNaN(a) || r === 0) { el.textContent = ''; return; }
        const pct = Math.round((a / r) * 100 * 10) / 10;
        const color = pct >= 80 ? 'var(--leaf)' : (pct >= 60 ? 'var(--clay)' : '#a03828');
        el.innerHTML = `<span style="color:${color};font-weight:700;">Efisiensi: ${pct}%</span>
            <span style="color:var(--textlt);"> — ${pct >= 80 ? 'Baik' : (pct >= 60 ? 'Perlu perhatian' : 'Di bawah target')}</span>`;
    }
</script>
@endpush
