@extends('layouts.app')
@section('title', isset($rtt) ? 'Edit RTT' : 'Tambah RTT')
@section('page-title', isset($rtt) ? 'Edit RTT' : 'Tambah RTT')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--leaf:#5a7a47;--clay:#c4895a;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
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

    /* Fase preview */
    .fase-preview { display:flex;border-radius:8px;overflow:hidden;height:20px;margin-top:.75rem; }
    .fase-seg { display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;color:#fff;overflow:hidden; }
</style>

<div style="max-width:680px;margin:0 auto;">
    <a href="{{ route('rtt.index') }}" style="display:inline-flex;align-items:center;gap:.4rem;font-size:.82rem;color:var(--textlt);text-decoration:none;margin-bottom:1.25rem;"
       onmouseover="this.style.color='var(--water)'" onmouseout="this.style.color='var(--textlt)'">
        ← Kembali ke RTT
    </a>

    <div class="form-card">
        <div style="margin-bottom:1.75rem;">
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">{{ isset($rtt) ? 'Edit Data' : 'Form Input' }}</p>
            <h2 style="font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:var(--soil);">{{ isset($rtt) ? 'Edit RTT — '.$rtt->petak->kode_petak : 'Tambah Rencana Tata Tanam' }}</h2>
        </div>

        @if($errors->any())
            <div style="background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.2);color:#a03828;border-radius:8px;padding:.8rem 1rem;font-size:.85rem;margin-bottom:1.25rem;">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ isset($rtt) ? route('rtt.update', $rtt) : route('rtt.store') }}">
            @csrf
            @if(isset($rtt)) @method('PUT') @endif

            {{-- Identitas --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">📍 Identitas</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="form-label">Petak <span>*</span></label>
                        <select name="petak_id" required class="form-input" {{ isset($rtt) ? 'disabled' : '' }}>
                            <option value="">— Pilih Petak —</option>
                            @foreach($petaks as $petak)
                                @if(!isset($rtt) && in_array($petak->id, $petakSudahAda ?? []))
                                    <option value="{{ $petak->id }}" disabled style="color:var(--textlt);">
                                        {{ $petak->kode_petak }} — {{ $petak->nama_petak }} (sudah ada RTT)
                                    </option>
                                @else
                                    <option value="{{ $petak->id }}"
                                        {{ old('petak_id', isset($rtt) ? $rtt->petak_id : '') == $petak->id ? 'selected' : '' }}>
                                        {{ $petak->kode_petak }} — {{ $petak->nama_petak }} ({{ $petak->luas_area }} ha)
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @if(isset($rtt))
                            <input type="hidden" name="petak_id" value="{{ $rtt->petak_id }}">
                        @endif
                    </div>
                    <div>
                        <label class="form-label">Musim Tanam <span>*</span></label>
                        <select name="musim_tanam_id" required class="form-input" {{ isset($rtt) ? 'disabled' : '' }}>
                            @foreach($musimTanams as $mt)
                                <option value="{{ $mt->id }}"
                                    {{ old('musim_tanam_id', isset($rtt) ? $rtt->musim_tanam_id : ($mtAktif?->id)) == $mt->id ? 'selected' : '' }}>
                                    {{ $mt->nama_mt }} @if($mt->status=='berjalan')(Aktif)@endif
                                </option>
                            @endforeach
                        </select>
                        @if(isset($rtt))
                            <input type="hidden" name="musim_tanam_id" value="{{ $rtt->musim_tanam_id }}">
                        @endif
                    </div>
                </div>
            </div>

            {{-- Jadwal Rencana --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">📅 Jadwal Rencana Tanam</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="form-label">Mulai Tanam <span>*</span></label>
                        <input type="date" name="rencana_mulai_tanam" id="tgl-mulai"
                            value="{{ old('rencana_mulai_tanam', isset($rtt) ? $rtt->rencana_mulai_tanam->format('Y-m-d') : '') }}"
                            required class="form-input" oninput="updatePreview()">
                    </div>
                    <div>
                        <label class="form-label">Selesai Tanam <span>*</span></label>
                        <input type="date" name="rencana_selesai_tanam" id="tgl-selesai"
                            value="{{ old('rencana_selesai_tanam', isset($rtt) ? $rtt->rencana_selesai_tanam->format('Y-m-d') : '') }}"
                            required class="form-input" oninput="updatePreview()">
                    </div>
                </div>
                <p id="durasi-info" style="font-size:.75rem;color:var(--textlt);margin-top:.4rem;min-height:1rem;"></p>

                {{-- Preview fase otomatis --}}
                <div id="fase-preview-wrap" style="margin-top:.75rem;display:none;">
                    <p style="font-size:.72rem;font-weight:600;color:var(--textlt);margin-bottom:.4rem;">Preview jadwal fase (otomatis):</p>
                    <div class="fase-preview" id="fase-preview"></div>
                    <div id="fase-legend" style="display:flex;flex-wrap:wrap;gap:.4rem;margin-top:.4rem;"></div>
                </div>
            </div>

            {{-- Jadwal Realisasi (untuk edit) --}}
            @if(isset($rtt))
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">✅ Realisasi Tanam</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="form-label">Mulai Realisasi <span>(opsional)</span></label>
                        <input type="date" name="realisasi_mulai_tanam"
                            value="{{ old('realisasi_mulai_tanam', $rtt->realisasi_mulai_tanam?->format('Y-m-d')) }}"
                            class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Selesai Realisasi <span>(opsional)</span></label>
                        <input type="date" name="realisasi_selesai_tanam"
                            value="{{ old('realisasi_selesai_tanam', $rtt->realisasi_selesai_tanam?->format('Y-m-d')) }}"
                            class="form-input">
                    </div>
                </div>
            </div>
            @endif

            {{-- Target Luas --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">🌾 Luas Tanam</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="form-label">Target Luas <span>(ha) *</span></label>
                        <input type="number" name="target_luas" step="0.01" min="0.01" required
                            value="{{ old('target_luas', isset($rtt) ? $rtt->target_luas : '') }}"
                            placeholder="cth: 25.50" class="form-input">
                    </div>
                    @if(isset($rtt))
                    <div>
                        <label class="form-label">Realisasi Luas <span>(ha, opsional)</span></label>
                        <input type="number" name="realisasi_luas" step="0.01" min="0"
                            value="{{ old('realisasi_luas', $rtt->realisasi_luas) }}"
                            placeholder="cth: 24.00" class="form-input">
                    </div>
                    @endif
                </div>
            </div>

            {{-- Rotasi Air --}}
            <div style="margin-bottom:1.5rem;">
                <p class="section-label">🔄 Rotasi Pemberian Air</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="form-label">Urutan Rotasi <span>*</span></label>
                        <input type="number" name="urutan_rotasi" min="1" required
                            value="{{ old('urutan_rotasi', isset($rtt) ? $rtt->urutan_rotasi : 1) }}"
                            placeholder="cth: 1" class="form-input">
                        <p style="font-size:.72rem;color:var(--textlt);margin-top:.3rem;">Urutan giliran mendapat air irigasi</p>
                    </div>
                    <div>
                        <label class="form-label">Durasi Rotasi <span>(hari) *</span></label>
                        <input type="number" name="durasi_rotasi_hari" min="1" required
                            value="{{ old('durasi_rotasi_hari', isset($rtt) ? $rtt->durasi_rotasi_hari : 10) }}"
                            placeholder="cth: 10" class="form-input">
                        <p style="font-size:.72rem;color:var(--textlt);margin-top:.3rem;">Lama giliran mendapat air (hari)</p>
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
                            @foreach(['rencana','berjalan','selesai','batal'] as $s)
                                <option value="{{ $s }}" {{ old('status', isset($rtt) ? $rtt->status : 'rencana') == $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Keterangan <span>(opsional)</span></label>
                        <input type="text" name="keterangan"
                            value="{{ old('keterangan', isset($rtt) ? $rtt->keterangan : '') }}"
                            placeholder="Catatan tambahan..." class="form-input">
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:.75rem;">
                <button type="submit" class="btn-primary">{{ isset($rtt) ? 'Simpan Perubahan' : 'Simpan RTT' }}</button>
                <a href="{{ route('rtt.index') }}" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const faseColors  = { pengolahan_tanah:'#8b5e3c',tanam:'#c4895a',vegetatif:'#4a7c6f',generatif:'#6aab9a',pemasakan:'#5a7a47',panen:'#3d2b1f' };
    const faseLabels  = { pengolahan_tanah:'Pengolahan',tanam:'Tanam',vegetatif:'Vegetatif',generatif:'Generatif',pemasakan:'Pemasakan',panen:'Panen' };
    const faseProp    = { pengolahan_tanah:.08,tanam:.08,vegetatif:.33,generatif:.25,pemasakan:.17,panen:.09 };

    function updatePreview() {
        const mulai   = document.getElementById('tgl-mulai').value;
        const selesai = document.getElementById('tgl-selesai').value;
        const info    = document.getElementById('durasi-info');
        const wrap    = document.getElementById('fase-preview-wrap');

        if (!mulai || !selesai) { info.textContent = ''; wrap.style.display = 'none'; return; }
        const diff = Math.round((new Date(selesai) - new Date(mulai)) / 86400000);
        if (diff <= 0) {
            info.innerHTML = '<span style="color:#a03828;">⚠️ Tanggal selesai harus setelah mulai.</span>';
            wrap.style.display = 'none';
            return;
        }
        info.innerHTML = `<span style="color:var(--leaf);font-weight:600;">✓ Durasi: ${diff} hari</span>`;

        // Render fase preview
        const preview = document.getElementById('fase-preview');
        const legend  = document.getElementById('fase-legend');
        let prevHtml  = '';
        let legHtml   = '';
        Object.entries(faseProp).forEach(([fase, pct]) => {
            const hari = Math.round(diff * pct);
            prevHtml += `<div class="fase-seg" style="flex:${pct};background:${faseColors[fase]};" title="${faseLabels[fase]}: ~${hari} hari">
                ${pct > 0.1 ? faseLabels[fase] : ''}
            </div>`;
            legHtml += `<span style="font-size:.7rem;color:var(--textlt);display:flex;align-items:center;gap:.25rem;">
                <span style="width:8px;height:8px;border-radius:2px;background:${faseColors[fase]};display:inline-block;"></span>
                ${faseLabels[fase]} ~${hari}h
            </span>`;
        });
        preview.innerHTML = prevHtml;
        legend.innerHTML  = legHtml;
        wrap.style.display = '';
    }

    // Init jika ada nilai awal
    updatePreview();
</script>
@endpush
