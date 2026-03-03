@extends('layouts.app')
@section('title', 'Laporan — Smart Irrigation')
@section('page-title', 'Laporan & Export')

@section('content')
<style>
    :root { --soil:#3d2b1f;--soil2:#5c3d2e;--earth:#8b5e3c;--clay:#c4895a;--straw:#e8d5a3;--cream:#faf6ef;--cream2:#f5ede0;--water:#4a7c6f;--water2:#6aab9a;--leaf:#5a7a47;--text:#4a3728;--textlt:#7a6355;--border:rgba(139,94,60,.14); }
    .card { background:var(--cream);border:1px solid var(--border);border-radius:14px;overflow:hidden; }
    .laporan-card { background:var(--cream);border:1px solid var(--border);border-radius:14px;padding:1.5rem; }
    .laporan-card:hover { border-color:rgba(139,94,60,.3);box-shadow:0 4px 20px rgba(61,43,31,.06); }
    .form-input { width:100%;background:var(--cream);border:1.5px solid rgba(139,94,60,.18);color:var(--text);border-radius:8px;padding:.6rem .9rem;font-size:.85rem;font-family:'Karla',sans-serif;outline:none; }
    .form-input:focus { border-color:var(--water); }
    .btn-pdf { background:#a03828;color:#fff;padding:.6rem 1.2rem;border-radius:7px;font-size:.82rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;border:none;cursor:pointer;font-family:'Karla',sans-serif;transition:background .2s; }
    .btn-pdf:hover { background:#8a2f20; }
    .btn-excel { background:#5a7a47;color:#fff;padding:.6rem 1.2rem;border-radius:7px;font-size:.82rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;border:none;cursor:pointer;font-family:'Karla',sans-serif;transition:background .2s; }
    .btn-excel:hover { background:#4a6639; }
    .section-icon { width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0; }
</style>

{{-- Top --}}
<div style="margin-bottom:1.75rem;">
    <p style="font-size:.68rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.3rem;">Fase 5</p>
    <h2 style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--soil);letter-spacing:-.02em;">Laporan & Export</h2>
    <p style="font-size:.82rem;color:var(--textlt);margin-top:.2rem;">Export data dalam format PDF atau Excel</p>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">

    {{-- ① Data Iklim --}}
    <div class="laporan-card">
        <div style="display:flex;align-items:flex-start;gap:.75rem;margin-bottom:1.25rem;">
            <div class="section-icon" style="background:rgba(74,124,111,.1);">🌤️</div>
            <div>
                <h3 style="font-family:'Fraunces',serif;font-size:1.05rem;font-weight:700;color:var(--soil);">Data Iklim Harian</h3>
                <p style="font-size:.78rem;color:var(--textlt);margin-top:.15rem;">Suhu, kelembaban, ETo, ETc, kebutuhan air</p>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1rem;">
            <div>
                <label style="font-size:.72rem;font-weight:600;color:var(--textlt);display:block;margin-bottom:.35rem;">Tahun</label>
                <select id="iklim-tahun" class="form-input">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $t == date('Y') ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:.72rem;font-weight:600;color:var(--textlt);display:block;margin-bottom:.35rem;">Bulan <span style="font-weight:300;">(opsional)</span></label>
                <select id="iklim-bulan" class="form-input">
                    <option value="">Semua Bulan</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $b)
                        <option value="{{ $i+1 }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="display:flex;gap:.6rem;">
            <button onclick="exportLaporan('pdf-data-iklim','iklim')" class="btn-pdf">📄 PDF</button>
            <button onclick="exportLaporan('excel-data-iklim','iklim')" class="btn-excel">📊 Excel</button>
        </div>
    </div>

    {{-- ② Blangko OP --}}
    <div class="laporan-card">
        <div style="display:flex;align-items:flex-start;gap:.75rem;margin-bottom:1.25rem;">
            <div class="section-icon" style="background:rgba(196,137,90,.1);">📋</div>
            <div>
                <h3 style="font-family:'Fraunces',serif;font-size:1.05rem;font-weight:700;color:var(--soil);">Blangko OP Per Dekade</h3>
                <p style="font-size:.78rem;color:var(--textlt);margin-top:.15rem;">Debit, luas areal, kondisi saluran per petak</p>
            </div>
        </div>
        <div style="margin-bottom:1rem;">
            <label style="font-size:.72rem;font-weight:600;color:var(--textlt);display:block;margin-bottom:.35rem;">Musim Tanam</label>
            <select id="blangko-mt" class="form-input">
                @foreach($musimTanams as $mt)
                    <option value="{{ $mt->id }}" {{ $mt->id == $mtAktif?->id ? 'selected' : '' }}>
                        {{ $mt->nama_mt }} @if($mt->status=='berjalan')(Aktif)@endif
                    </option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;gap:.6rem;">
            <button onclick="exportLaporan('pdf-blangko-op','blangko')" class="btn-pdf">📄 PDF</button>
            <button onclick="exportLaporan('excel-blangko-op','blangko')" class="btn-excel">📊 Excel</button>
        </div>
    </div>

    {{-- ③ RTT --}}
    <div class="laporan-card">
        <div style="display:flex;align-items:flex-start;gap:.75rem;margin-bottom:1.25rem;">
            <div class="section-icon" style="background:rgba(90,122,71,.1);">🗓️</div>
            <div>
                <h3 style="font-family:'Fraunces',serif;font-size:1.05rem;font-weight:700;color:var(--soil);">Rencana Tata Tanam</h3>
                <p style="font-size:.78rem;color:var(--textlt);margin-top:.15rem;">Jadwal tanam, rotasi air, target vs realisasi</p>
            </div>
        </div>
        <div style="margin-bottom:1rem;">
            <label style="font-size:.72rem;font-weight:600;color:var(--textlt);display:block;margin-bottom:.35rem;">Musim Tanam</label>
            <select id="rtt-mt" class="form-input">
                @foreach($musimTanams as $mt)
                    <option value="{{ $mt->id }}" {{ $mt->id == $mtAktif?->id ? 'selected' : '' }}>
                        {{ $mt->nama_mt }} @if($mt->status=='berjalan')(Aktif)@endif
                    </option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;gap:.6rem;">
            <button onclick="exportLaporan('pdf-rtt','rtt')" class="btn-pdf">📄 PDF</button>
            <button onclick="exportLaporan('excel-rtt','rtt')" class="btn-excel">📊 Excel</button>
        </div>
    </div>

    {{-- ④ Rekap Kebutuhan Air --}}
    <div class="laporan-card">
        <div style="display:flex;align-items:flex-start;gap:.75rem;margin-bottom:1.25rem;">
            <div class="section-icon" style="background:rgba(139,94,60,.1);">💧</div>
            <div>
                <h3 style="font-family:'Fraunces',serif;font-size:1.05rem;font-weight:700;color:var(--soil);">Rekapitulasi Kebutuhan Air</h3>
                <p style="font-size:.78rem;color:var(--textlt);margin-top:.15rem;">Ringkasan bulanan ETo, ETc, kebutuhan air</p>
            </div>
        </div>
        <div style="margin-bottom:1rem;">
            <label style="font-size:.72rem;font-weight:600;color:var(--textlt);display:block;margin-bottom:.35rem;">Tahun</label>
            <select id="rekap-tahun" class="form-input">
                @foreach($tahunList as $t)
                    <option value="{{ $t }}" {{ $t == date('Y') ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;gap:.6rem;">
            <button onclick="exportLaporan('pdf-rekap','rekap')" class="btn-pdf">📄 PDF</button>
            <button onclick="exportLaporan('excel-rekap','rekap')" class="btn-excel">📊 Excel</button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function exportLaporan(type, prefix) {
    const routes = {
        'pdf-data-iklim':   '{{ route("laporan.pdf.data-iklim") }}',
        'excel-data-iklim': '{{ route("laporan.excel.data-iklim") }}',
        'pdf-blangko-op':   '{{ route("laporan.pdf.blangko-op") }}',
        'excel-blangko-op': '{{ route("laporan.excel.blangko-op") }}',
        'pdf-rtt':          '{{ route("laporan.pdf.rtt") }}',
        'excel-rtt':        '{{ route("laporan.excel.rtt") }}',
        'pdf-rekap':        '{{ route("laporan.pdf.rekap") }}',
        'excel-rekap':      '{{ route("laporan.excel.rekap") }}',
    };

    const params = new URLSearchParams();

    if (prefix === 'iklim') {
        params.set('tahun', document.getElementById('iklim-tahun').value);
        const bulan = document.getElementById('iklim-bulan').value;
        if (bulan) params.set('bulan', bulan);
    } else if (prefix === 'blangko') {
        params.set('musim_tanam_id', document.getElementById('blangko-mt').value);
    } else if (prefix === 'rtt') {
        params.set('musim_tanam_id', document.getElementById('rtt-mt').value);
    } else if (prefix === 'rekap') {
        params.set('tahun', document.getElementById('rekap-tahun').value);
    }

    window.open(routes[type] + '?' + params.toString(), '_blank');
}
</script>
@endpush
