{{-- resources/views/daerah_irigasi/_form.blade.php --}}
{{-- Di-include dari create.blade.php dan edit.blade.php --}}

{{-- ═══════════════════════════════════
     INFORMASI DASAR
═══════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:1fr 2fr;gap:1rem;">
    <div class="form-group">
        <label class="form-label">Kode <span style="color:#a03828;">*</span></label>
        <input type="text" name="kode" class="form-control"
            value="{{ old('kode', $daerahIrigasi->kode ?? '') }}"
            placeholder="DI-001">
        @error('kode')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">Nama Daerah Irigasi <span style="color:#a03828;">*</span></label>
        <input type="text" name="nama" class="form-control"
            value="{{ old('nama', $daerahIrigasi->nama ?? '') }}"
            placeholder="Daerah Irigasi Barito">
        @error('nama')<p class="form-error">{{ $message }}</p>@enderror
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
    <div class="form-group">
        <label class="form-label">Jenis DI <span style="color:#a03828;">*</span></label>
        <select name="jenis" id="jenisDI" class="form-select">
            <option value="permukaan" {{ old('jenis', $daerahIrigasi->jenis ?? 'permukaan') === 'permukaan' ? 'selected' : '' }}>
                DIP — Irigasi Permukaan
            </option>
            <option value="rawa" {{ old('jenis', $daerahIrigasi->jenis ?? '') === 'rawa' ? 'selected' : '' }}>
                DIR — Irigasi Rawa
            </option>
        </select>
        @error('jenis')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">Status <span style="color:#a03828;">*</span></label>
        <select name="status" class="form-select">
            <option value="aktif" {{ old('status', $daerahIrigasi->status ?? 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ old('status', $daerahIrigasi->status ?? '') === 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
        </select>
        @error('status')<p class="form-error">{{ $message }}</p>@enderror
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
    <div class="form-group">
        <label class="form-label">Luas Total (ha)</label>
        <input type="number" name="luas_total" class="form-control"
            value="{{ old('luas_total', $daerahIrigasi->luas_total ?? '') }}"
            placeholder="0.00" step="0.01" min="0">
        @error('luas_total')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">Sumber Air</label>
        <input type="text" name="sumber_air" class="form-control"
            value="{{ old('sumber_air', $daerahIrigasi->sumber_air ?? '') }}"
            placeholder="Sungai Barito">
        @error('sumber_air')<p class="form-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="form-group">
    <label class="form-label">Penanggung Jawab</label>
    <input type="text" name="penanggung_jawab" class="form-control"
        value="{{ old('penanggung_jawab', $daerahIrigasi->penanggung_jawab ?? '') }}"
        placeholder="Nama penanggung jawab">
    @error('penanggung_jawab')<p class="form-error">{{ $message }}</p>@enderror
</div>

{{-- Koordinat --}}
<div style="background:var(--cream2);border:1px solid var(--border);border-radius:10px;padding:1.25rem;margin-bottom:1.25rem;">
    <p style="font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--textlt);margin-bottom:1rem;">📍 Koordinat (Opsional)</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Latitude</label>
            <input type="number" name="latitude" id="latitude" class="form-control"
                value="{{ old('latitude', $daerahIrigasi->latitude ?? '') }}"
                placeholder="-2.5489" step="any">
            @error('latitude')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Longitude</label>
            <input type="number" name="longitude" id="longitude" class="form-control"
                value="{{ old('longitude', $daerahIrigasi->longitude ?? '') }}"
                placeholder="115.7624" step="any">
            @error('longitude')<p class="form-error">{{ $message }}</p>@enderror
        </div>
    </div>
    <p class="form-hint" style="margin-top:.75rem;">Klik peta untuk mengisi koordinat secara otomatis.</p>
    <div id="map-picker" style="height:220px;border-radius:8px;margin-top:.75rem;border:1px solid var(--border);"></div>
</div>

<div class="form-group">
    <label class="form-label">Keterangan</label>
    <textarea name="keterangan" class="form-control" rows="2"
        placeholder="Keterangan tambahan...">{{ old('keterangan', $daerahIrigasi->keterangan ?? '') }}</textarea>
</div>

{{-- ═══════════════════════════════════
     PARAMETER KEBUTUHAN AIR
═══════════════════════════════════ --}}
<div style="background:var(--cream2);border:1px solid var(--border);border-radius:10px;padding:1.25rem;margin-bottom:1.25rem;">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
        <p style="font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--textlt);margin:0;">
            💧 Parameter Kebutuhan Air
        </p>
        <span style="font-size:.72rem;color:var(--textlt);background:var(--cream);border:1px solid var(--border);border-radius:6px;padding:.2rem .6rem;">
            Default: Permen PU No. 32/PRT/M/2007
        </span>
    </div>

    <p class="form-hint" style="margin-bottom:1rem;">
        Satuan Kebutuhan Air (SKA) dalam <strong>l/det/ha</strong>. Dapat disesuaikan dengan kondisi lokal DI ini.
    </p>

    {{-- SKA Padi & Palawija --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">SKA Padi — Pengolahan Tanah</label>
            <div style="display:flex;gap:.5rem;align-items:center;">
                <input type="number" step="0.001" name="ska_padi_pengolahan" class="form-control"
                    value="{{ old('ska_padi_pengolahan', $daerahIrigasi->ska_padi_pengolahan ?? 1.250) }}">
                <span style="font-size:.75rem;color:var(--textlt);white-space:nowrap;">l/det/ha</span>
            </div>
            <p class="form-hint">Default: 1.250</p>
            @error('ska_padi_pengolahan')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">SKA Padi — Pertumbuhan</label>
            <div style="display:flex;gap:.5rem;align-items:center;">
                <input type="number" step="0.001" name="ska_padi_pertumbuhan" class="form-control"
                    value="{{ old('ska_padi_pertumbuhan', $daerahIrigasi->ska_padi_pertumbuhan ?? 0.725) }}">
                <span style="font-size:.75rem;color:var(--textlt);white-space:nowrap;">l/det/ha</span>
            </div>
            <p class="form-hint">Default: 0.725</p>
            @error('ska_padi_pertumbuhan')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">SKA Palawija — Banyak Air</label>
            <div style="display:flex;gap:.5rem;align-items:center;">
                <input type="number" step="0.001" name="ska_palawija_banyak" class="form-control"
                    value="{{ old('ska_palawija_banyak', $daerahIrigasi->ska_palawija_banyak ?? 0.300) }}">
                <span style="font-size:.75rem;color:var(--textlt);white-space:nowrap;">l/det/ha</span>
            </div>
            <p class="form-hint">Default: 0.300</p>
            @error('ska_palawija_banyak')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">SKA Palawija — Sedikit Air</label>
            <div style="display:flex;gap:.5rem;align-items:center;">
                <input type="number" step="0.001" name="ska_palawija_sedikit" class="form-control"
                    value="{{ old('ska_palawija_sedikit', $daerahIrigasi->ska_palawija_sedikit ?? 0.200) }}">
                <span style="font-size:.75rem;color:var(--textlt);white-space:nowrap;">l/det/ha</span>
            </div>
            <p class="form-hint">Default: 0.200</p>
            @error('ska_palawija_sedikit')<p class="form-error">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Parameter DIP (tampil jika jenis = permukaan) --}}
    <div id="paramDIP">
        <div style="border-top:1px solid var(--border);margin:1rem 0;"></div>
        <p style="font-size:.75rem;font-weight:600;color:var(--textlt);margin-bottom:.75rem;">
            🌊 Parameter DIP — Irigasi Permukaan
        </p>
        <div style="max-width:280px;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Faktor Tersier</label>
                <div style="display:flex;gap:.5rem;align-items:center;">
                    <input type="number" step="0.001" name="faktor_tersier" class="form-control"
                        value="{{ old('faktor_tersier', $daerahIrigasi->faktor_tersier ?? 0.830) }}">
                    <span style="font-size:.75rem;color:var(--textlt);white-space:nowrap;">koef.</span>
                </div>
                <p class="form-hint">Default: 0.830 — efisiensi saluran tersier</p>
                @error('faktor_tersier')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- Parameter DIR (tampil jika jenis = rawa) --}}
    <div id="paramDIR" style="display:none;">
        <div style="border-top:1px solid var(--border);margin:1rem 0;"></div>
        <p style="font-size:.75rem;font-weight:600;color:var(--textlt);margin-bottom:.75rem;">
            🌿 Parameter DIR — Irigasi Rawa
        </p>
        <div style="max-width:280px;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Kehilangan Air</label>
                <div style="display:flex;gap:.5rem;align-items:center;">
                    <input type="number" step="0.01" name="pct_kehilangan_air" class="form-control"
                        value="{{ old('pct_kehilangan_air', $daerahIrigasi->pct_kehilangan_air ?? 35.00) }}">
                    <span style="font-size:.75rem;color:var(--textlt);white-space:nowrap;">%</span>
                </div>
                <p class="form-hint">Default: 35% — kehilangan air di jaringan rawa</p>
                @error('pct_kehilangan_air')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

</div>

{{-- JS: toggle DIP/DIR section berdasarkan pilihan jenis --}}
<script>
(function () {
    const select   = document.getElementById('jenisDI');
    const paramDIP = document.getElementById('paramDIP');
    const paramDIR = document.getElementById('paramDIR');

    function toggle() {
        const isRawa = select.value === 'rawa';
        paramDIP.style.display = isRawa ? 'none'  : 'block';
        paramDIR.style.display = isRawa ? 'block' : 'none';
    }

    select.addEventListener('change', toggle);
    toggle(); // jalankan saat halaman pertama load
})();
</script>
