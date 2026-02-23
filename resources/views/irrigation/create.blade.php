@extends('layouts.app')

@section('title', 'Tambah Data Irigasi')
@section('page-title', 'Tambah Data Irigasi')

@section('content')

<div class="max-w-3xl mx-auto">

    {{-- Back button --}}
    <a href="{{ route('irrigation') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 mb-6 transition">
        ← Kembali ke Dashboard
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-800">Input Data Iklim Harian</h2>
            <p class="text-sm text-slate-400 mt-1">ETo, ETc, dan Kebutuhan Air akan dihitung otomatis oleh sistem.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 mb-6 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('irrigation.store') }}" id="form-tambah">
            @csrf

            {{-- Tanggal --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Pengamatan</label>
                <input type="date" name="tanggal" value="{{ old('tanggal') }}" required
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Data Iklim --}}
            <div class="mb-4">
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4">📊 Data Iklim</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Suhu Maksimum <span class="text-slate-400 font-normal">(°C)</span>
                        </label>
                        <input type="number" name="suhu_max" value="{{ old('suhu_max') }}"
                            step="0.1" min="0" max="60" required placeholder="cth: 34.5"
                            class="input-field w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Suhu Minimum <span class="text-slate-400 font-normal">(°C)</span>
                        </label>
                        <input type="number" name="suhu_min" value="{{ old('suhu_min') }}"
                            step="0.1" min="0" max="60" required placeholder="cth: 24.0"
                            class="input-field w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Kelembaban Udara <span class="text-slate-400 font-normal">(%)</span>
                        </label>
                        <input type="number" name="kelembaban" value="{{ old('kelembaban') }}"
                            step="0.1" min="0" max="100" required placeholder="cth: 80"
                            class="input-field w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Kecepatan Angin <span class="text-slate-400 font-normal">(m/s)</span>
                        </label>
                        <input type="number" name="kecepatan_angin" value="{{ old('kecepatan_angin') }}"
                            step="0.1" min="0" required placeholder="cth: 1.5"
                            class="input-field w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Radiasi Matahari <span class="text-slate-400 font-normal">(MJ/m²/hari)</span>
                        </label>
                        <input type="number" name="radiasi_matahari" value="{{ old('radiasi_matahari') }}"
                            step="0.1" min="0" required placeholder="cth: 18.5"
                            class="input-field w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Curah Hujan <span class="text-slate-400 font-normal">(mm)</span>
                        </label>
                        <input type="number" name="curah_hujan" value="{{ old('curah_hujan', 0) }}"
                            step="0.1" min="0" required placeholder="cth: 5.0"
                            class="input-field w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                </div>
            </div>

            {{-- Kc --}}
            <div class="mb-8">
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4">🌾 Koefisien Tanaman</p>
                <div class="max-w-sm">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Nilai Kc <span class="text-slate-400 font-normal">(koefisien tanaman)</span>
                    </label>
                    <input type="number" name="kc" value="{{ old('kc', 1.0) }}"
                        step="0.01" min="0" max="2" required placeholder="cth: 1.05"
                        class="input-field w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-slate-400 mt-1">Padi: fase awal 1.05 · pertengahan 1.2 · akhir 0.75</p>
                </div>
            </div>

            {{-- Preview Hasil Kalkulasi --}}
            <div id="preview-kalkulasi" class="hidden bg-blue-50 border border-blue-100 rounded-xl p-5 mb-8">
                <p class="text-xs font-black uppercase tracking-widest text-blue-400 mb-3">⚡ Preview Hasil Kalkulasi</p>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-xs text-slate-400 mb-1">ETo</p>
                        <p class="text-2xl font-extrabold text-blue-600" id="prev-eto">-</p>
                        <p class="text-xs text-slate-400">mm/hari</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">ETc</p>
                        <p class="text-2xl font-extrabold text-blue-600" id="prev-etc">-</p>
                        <p class="text-xs text-slate-400">mm/hari</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Kebutuhan Air</p>
                        <p class="text-2xl font-extrabold text-emerald-600" id="prev-kebutuhan">-</p>
                        <p class="text-xs text-slate-400">mm/hari</p>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition text-sm">
                    Simpan Data
                </button>
                <a href="{{ route('irrigation') }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold px-8 py-3 rounded-xl transition text-sm">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Kalkulasi real-time preview
    function hitungETo(tmax, tmin, rh, ws, rs) {
        const tmean = (tmax + tmin) / 2;
        const delta = (4098 * (0.6108 * Math.exp((17.27 * tmean) / (tmean + 237.3)))) / Math.pow(tmean + 237.3, 2);
        const gamma = 0.0665;
        const esTmax = 0.6108 * Math.exp((17.27 * tmax) / (tmax + 237.3));
        const esTmin = 0.6108 * Math.exp((17.27 * tmin) / (tmin + 237.3));
        const es = (esTmax + esTmin) / 2;
        const ea = (rh / 100) * es;
        const Rn = 0.77 * rs;
        const ETo = (0.408 * delta * Rn + gamma * (900 / (tmean + 273)) * ws * (es - ea))
                    / (delta + gamma * (1 + 0.34 * ws));
        return Math.max(0, Math.round(ETo * 100) / 100);
    }

    function updatePreview() {
        const tmax = parseFloat(document.querySelector('[name=suhu_max]').value);
        const tmin = parseFloat(document.querySelector('[name=suhu_min]').value);
        const rh   = parseFloat(document.querySelector('[name=kelembaban]').value);
        const ws   = parseFloat(document.querySelector('[name=kecepatan_angin]').value);
        const rs   = parseFloat(document.querySelector('[name=radiasi_matahari]').value);
        const kc   = parseFloat(document.querySelector('[name=kc]').value);
        const ch   = parseFloat(document.querySelector('[name=curah_hujan]').value) || 0;

        if ([tmax, tmin, rh, ws, rs, kc].some(isNaN)) return;

        const eto = hitungETo(tmax, tmin, rh, ws, rs);
        const etc = Math.round(eto * kc * 100) / 100;
        const kebutuhan = Math.max(0, Math.round((etc - ch * 0.8) * 100) / 100);

        document.getElementById('prev-eto').textContent = eto;
        document.getElementById('prev-etc').textContent = etc;
        document.getElementById('prev-kebutuhan').textContent = kebutuhan;
        document.getElementById('preview-kalkulasi').classList.remove('hidden');
    }

    document.querySelectorAll('.input-field').forEach(input => {
        input.addEventListener('input', updatePreview);
    });
</script>
@endpush
