@extends('layouts.app')

@section('title', 'Import CSV — Data Iklim')
@section('page-title', 'Import Data Iklim')

@section('content')

<div style="max-width:640px;margin:0 auto;">

    <a href="{{ route('irrigation.index') }}" style="display:inline-flex;align-items:center;gap:.4rem;font-size:.82rem;color:var(--textlt);text-decoration:none;margin-bottom:1.25rem;">
        ← Kembali ke Data Iklim
    </a>

    {{-- Info Format --}}
    <div style="background:rgba(74,124,111,.06);border:1px solid rgba(74,124,111,.2);border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;">
        <p style="font-size:.75rem;font-weight:700;color:var(--water);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.75rem;">📋 Format CSV yang Didukung</p>
        <p style="font-size:.82rem;color:var(--textlt);margin-bottom:.5rem;">CSV hasil download dari <strong>dataonline.bmkg.go.id</strong> langsung bisa diupload. Kolom yang dikenali:</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.35rem;font-size:.78rem;">
            <div style="color:var(--text);"><span style="color:var(--water);font-weight:600;">Tn / suhu_min</span> — Suhu minimum</div>
            <div style="color:var(--text);"><span style="color:var(--water);font-weight:600;">Tx / suhu_max</span> — Suhu maksimum</div>
            <div style="color:var(--text);"><span style="color:var(--water);font-weight:600;">RH_avg / kelembaban</span> — Kelembaban</div>
            <div style="color:var(--text);"><span style="color:var(--water);font-weight:600;">ff_avg / kecepatan_angin</span> — Kec. angin</div>
            <div style="color:var(--text);"><span style="color:var(--water);font-weight:600;">ss / radiasi_matahari</span> — Radiasi/lama sinar</div>
            <div style="color:var(--text);"><span style="color:var(--water);font-weight:600;">RR / curah_hujan</span> — Curah hujan</div>
        </div>
        <p style="font-size:.75rem;color:var(--textlt);margin-top:.75rem;">Data duplikat (tanggal sudah ada) akan dilewati otomatis. Nilai missing (8888/9999) juga dilewati.</p>
    </div>

    {{-- Form --}}
    <div style="background:var(--cream);border:1px solid var(--border);border-radius:14px;padding:2rem;">

        <h2 style="font-family:'Fraunces',serif;font-size:1.4rem;font-weight:700;color:var(--soil);margin-bottom:1.5rem;">Upload File CSV</h2>

        @if($errors->any())
            <div style="background:rgba(185,74,60,.08);border:1px solid rgba(185,74,60,.2);color:#a03828;border-radius:8px;padding:.8rem 1rem;font-size:.85rem;margin-bottom:1.25rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('irrigation.import.post') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-size:.8rem;font-weight:600;color:var(--text);margin-bottom:.45rem;">File CSV</label>

                {{-- Drop zone --}}
                <div id="drop-zone" onclick="document.getElementById('file_csv').click()"
                    style="border:2px dashed rgba(74,124,111,.35);border-radius:10px;padding:2.5rem;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;"
                    ondragover="event.preventDefault();this.style.background='rgba(74,124,111,.06)'"
                    ondragleave="this.style.background='none'"
                    ondrop="handleDrop(event)">
                    <p style="font-size:2rem;margin-bottom:.5rem;">📂</p>
                    <p style="font-size:.875rem;font-weight:600;color:var(--water);">Klik atau drag & drop file CSV</p>
                    <p style="font-size:.75rem;color:var(--textlt);margin-top:.25rem;">Format: .csv · Maksimal 2MB</p>
                    <p id="file-name" style="font-size:.8rem;color:var(--soil);font-weight:600;margin-top:.75rem;display:none;"></p>
                </div>
                <input type="file" id="file_csv" name="file_csv" accept=".csv,.txt" style="display:none;" onchange="showFileName(this)">
            </div>

            <div style="display:flex;gap:.75rem;">
                <button type="submit" style="padding:.75rem 1.75rem;background:var(--water);color:white;border:none;border-radius:8px;font-family:'Karla',sans-serif;font-size:.875rem;font-weight:600;cursor:pointer;">
                    Import Data
                </button>
                <a href="{{ route('irrigation.index') }}" style="padding:.75rem 1.5rem;background:rgba(139,94,60,.08);color:var(--textlt);border:1px solid var(--border);border-radius:8px;font-family:'Karla',sans-serif;font-size:.875rem;font-weight:600;text-decoration:none;">
                    Batal
</a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function showFileName(input) {
        if (input.files.length > 0) {
            const el = document.getElementById('file-name');
            el.textContent = '✓ ' + input.files[0].name;
            el.style.display = 'block';
        }
    }
    function handleDrop(e) {
        e.preventDefault();
        e.currentTarget.style.background = 'none';
        const file = e.dataTransfer.files[0];
        if (file) {
            const input = document.getElementById('file_csv');
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            showFileName(input);
        }
    }
</script>
@endpush
