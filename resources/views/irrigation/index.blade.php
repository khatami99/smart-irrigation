@extends('layouts.app')

@section('title', 'Smart Irrigation | AI Dashboard')
@section('page-title', 'Monitoring Panel')

@section('content')

    <div class="mb-8 flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 uppercase tracking-tight">Ringkasan Sistem Irigasi</h2>
            <p class="text-slate-500 text-sm mt-1 italic font-medium">Data diolah secara otomatis menggunakan algoritma Linear Regression.</p>
        </div>
        <a href="{{ route('irrigation.create') }}"
        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition">
            + Tambah Data
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Rerata Kebutuhan</p>
                    <h3 class="text-3xl font-extrabold text-slate-800">{{ number_format($kebutuhan->avg(), 1) }} <span class="text-lg font-medium text-slate-400 tracking-tighter">L</span></h3>
                </div>
                <div class="p-2 bg-blue-50 rounded-lg text-blue-500 font-bold">AVG</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Status Sistem</p>
                    <h3 class="text-3xl font-extrabold text-emerald-500 tracking-tight">Optimal</h3>
                </div>
                <div class="p-2 bg-emerald-50 rounded-lg text-emerald-500 text-xl">✓</div>
            </div>
        </div>

        <div class="bg-slate-900 p-6 rounded-2xl shadow-xl glow border border-blue-500/30 transform hover:scale-105 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] text-blue-400 font-black uppercase tracking-[0.2em] mb-1">AI Prediction (24h)</p>
                    <h3 class="text-4xl font-extrabold text-white tracking-tight">
                        {{ $forecast }} <span class="text-sm font-medium text-blue-300">Liter</span>
                    </h3>
                    <p class="text-[10px] text-slate-400 mt-2 italic font-semibold">Estimasi kebutuhan air esok hari</p>
                </div>
                <div class="p-3 bg-blue-600/20 rounded-xl text-blue-400 animate-pulse text-2xl">🤖</div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 mb-10">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Tren Kebutuhan Air</h2>
                <p class="text-xs text-slate-400">Visualisasi data historis per 15 hari</p>
            </div>
            <span class="flex items-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span> Kebutuhan Air (L)
            </span>
        </div>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="chartKebutuhan"></canvas>
        </div>
    </div>

    {{-- Tabel --}}
    <div id="table-wrapper" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        @include('irrigation.partials.table', ['tableData' => $tableData])
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart
    const ctx = document.getElementById('chartKebutuhan');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Kebutuhan Air',
                data: @json($kebutuhan),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.05)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                borderWidth: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }
            }
        }
    });

    // AJAX Pagination
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#table-wrapper a');
        if (!link) return;

        e.preventDefault();
        const url = link.href;

        startLoading();

        // Skeleton loading
        document.getElementById('table-wrapper').innerHTML = `
            <div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <div class="h-5 w-48 bg-slate-200 rounded-lg animate-pulse"></div>
                <div class="h-5 w-24 bg-slate-200 rounded-full animate-pulse"></div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100">
                            ${['w-28','w-16','w-16','w-20','w-20'].map(w => `
                            <th class="px-8 py-4">
                                <div class="h-3 ${w} bg-slate-200 rounded animate-pulse"></div>
                            </th>`).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        ${Array(10).fill('').map(() => `
                        <tr class="border-b border-slate-50">
                            <td class="px-8 py-4"><div class="h-4 w-28 bg-slate-100 rounded animate-pulse"></div></td>
                            <td class="px-8 py-4"><div class="h-4 w-10 bg-slate-100 rounded animate-pulse mx-auto"></div></td>
                            <td class="px-8 py-4"><div class="h-4 w-10 bg-slate-100 rounded animate-pulse mx-auto"></div></td>
                            <td class="px-8 py-4"><div class="h-4 w-12 bg-slate-100 rounded animate-pulse mx-auto"></div></td>
                            <td class="px-8 py-4 flex justify-end"><div class="h-4 w-16 bg-slate-100 rounded animate-pulse"></div></td>
                        </tr>`).join('')}
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-4 bg-slate-50 border-t border-slate-100">
                <div class="h-8 w-48 bg-slate-200 rounded-lg animate-pulse"></div>
            </div>
        `;

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.querySelector('#table-wrapper');
            if (newTable) {
                document.getElementById('table-wrapper').innerHTML = newTable.innerHTML;
                window.history.pushState({}, '', url);
            }
            finishLoading();
        });
    });
</script>
@endpush
