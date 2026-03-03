@extends('laporan.pdf.layout')

@section('doc-title', 'Laporan Rencana Tata Tanam (RTT)')
@section('doc-meta')
    Musim Tanam: {{ $mt?->nama_mt ?? 'Semua' }}
    @if($mt) · {{ $mt->tanggal_mulai->format('d M Y') }} — {{ $mt->tanggal_selesai->format('d M Y') }} @endif
    · Total: {{ $data->count() }} petak
@endsection

@section('content')
{{-- Summary --}}
<div class="stat-row">
    <div class="stat-box">
        <div class="stat-box-label">Total Petak</div>
        <div class="stat-box-val">{{ $data->count() }}</div>
    </div>
    <div class="stat-box">
        <div class="stat-box-label">Target Luas</div>
        <div class="stat-box-val">{{ number_format($data->sum('target_luas'),1) }} <span class="stat-box-unit">ha</span></div>
    </div>
    <div class="stat-box">
        <div class="stat-box-label">Realisasi Luas</div>
        <div class="stat-box-val">{{ number_format($data->whereNotNull('realisasi_luas')->sum('realisasi_luas'),1) }} <span class="stat-box-unit">ha</span></div>
    </div>
    <div class="stat-box">
        <div class="stat-box-label">Status Selesai</div>
        <div class="stat-box-val">{{ $data->where('status','selesai')->count() }} <span class="stat-box-unit">petak</span></div>
    </div>
</div>

@php
    $start     = \Carbon\Carbon::parse($data->min('rencana_mulai_tanam'));
    $end       = \Carbon\Carbon::parse($data->max('rencana_selesai_tanam'));
    $total     = $start->diffInDays($end) + 1;
    $bulanNama = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $faseColors = [
        'pengolahan_tanah' => '#8b5e3c',
        'tanam'            => '#c4895a',
        'vegetatif'        => '#4a7c6f',
        'generatif'        => '#6aab9a',
        'pemasakan'        => '#5a7a47',
        'panen'            => '#3d2b1f',
    ];
    $faseLabels = [
        'pengolahan_tanah' => 'Pengolahan',
        'tanam'            => 'Tanam',
        'vegetatif'        => 'Vegetatif',
        'generatif'        => 'Generatif',
        'pemasakan'        => 'Pemasakan',
        'panen'            => 'Panen',
    ];
    $labelWidth = 70; // px lebar kolom label
@endphp

<div style="margin-bottom:14px;">
    <div style="font-size:8px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;color:#7a6355;margin-bottom:5px;">TIMELINE TANAM</div>

    {{-- Outer table: kolom kiri = label, kolom kanan = gantt area --}}
    <table style="width:100%;border-collapse:collapse;table-layout:fixed;">
        {{-- Header --}}
        <tr>
            <td style="width:{{ $labelWidth }}px;"></td>
            <td style="padding:0;">
                {{-- Header bulan sebagai inner table --}}
                <table style="width:100%;border-collapse:collapse;table-layout:fixed;">
                    <tr>
                        @php $cur = $start->copy()->startOfMonth(); @endphp
                        @while($cur->lte($end))
                        @php
                            $effS = $cur->lt($start) ? $start->copy() : $cur->copy();
                            $effE = $cur->copy()->endOfMonth()->gt($end) ? $end->copy() : $cur->copy()->endOfMonth();
                            $pct  = round($effS->diffInDays($effE) + 1) / $total * 100;
                        @endphp
                        <td style="width:{{ $pct }}%;font-size:6.5px;font-weight:bold;color:#7a6355;text-align:center;border-left:1px solid #e8d5a3;border-bottom:2px solid #c4895a;padding:2px 1px;overflow:hidden;">
                            {{ $bulanNama[$cur->month-1] }} {{ $cur->year }}
                        </td>
                        @php $cur->addMonth() @endphp
                        @endwhile
                    </tr>
                </table>
            </td>
        </tr>

        {{-- Rows per petak --}}
        @foreach($data as $rtt)
        @php
            $mulai    = \Carbon\Carbon::parse($rtt->rencana_mulai_tanam);
            $selesai  = \Carbon\Carbon::parse($rtt->rencana_selesai_tanam);
            $barLeft  = round($start->diffInDays($mulai) / $total * 100, 2);
            $barWidth = round(($mulai->diffInDays($selesai) + 1) / $total * 100, 2);
        @endphp

        {{-- Row 1: label + bar rencana --}}
        <tr>
            <td style="width:{{ $labelWidth }}px;font-size:7px;font-weight:bold;color:#3d2b1f;padding:4px 4px 0 0;vertical-align:top;border-bottom:none;">
                {{ $rtt->petak->kode_petak }}<br>
                <span style="font-weight:normal;color:#7a6355;font-size:6px;">{{ $rtt->petak->nama_petak }}</span>
            </td>
            <td style="padding:4px 0 0 0;border-bottom:none;">
                {{-- Bar rencana --}}
                <table style="width:100%;border-collapse:collapse;table-layout:fixed;height:10px;">
                    <tr>
                        @if($barLeft > 0)
                        <td style="width:{{ $barLeft }}%;height:10px;"></td>
                        @endif
                        <td style="width:{{ $barWidth }}%;height:10px;background:rgba(74,124,111,.25);border:1px solid rgba(74,124,111,.6);border-radius:2px;"></td>
                        @if(($barLeft + $barWidth) < 100)
                        <td style="width:{{ 100 - $barLeft - $barWidth }}%;height:10px;"></td>
                        @endif
                    </tr>
                </table>
            </td>
        </tr>

        {{-- Row 2: fase bars --}}
        <tr>
            <td style="width:{{ $labelWidth }}px;border-bottom:1px solid #f0e8dc;padding:0;"></td>
            <td style="padding:1px 0 3px 0;border-bottom:1px solid #f0e8dc;">
                @if($rtt->jadwal_fase)
                <table style="width:100%;border-collapse:collapse;table-layout:fixed;height:8px;">
                    <tr>
                        @php $cursor = 0; @endphp
                        @foreach($rtt->jadwal_fase as $fase)
                        @php
                            $fm  = \Carbon\Carbon::parse($fase['mulai']);
                            $fs  = \Carbon\Carbon::parse($fase['selesai']);
                            $fl  = round($start->diffInDays($fm) / $total * 100, 2);
                            $fw  = round(($fm->diffInDays($fs) + 1) / $total * 100, 2);
                            $fc  = $faseColors[$fase['fase']] ?? '#8b5e3c';
                            $gap = $fl - $cursor;
                        @endphp
                        @if($gap > 0)
                        <td style="width:{{ $gap }}%;height:8px;"></td>
                        @endif
                        <td style="width:{{ $fw }}%;height:8px;background:{{ $fc }};border-radius:1px;"></td>
                        @php $cursor = $fl + $fw; @endphp
                        @endforeach
                        @if($cursor < 100)
                        <td style="width:{{ 100 - $cursor }}%;height:8px;"></td>
                        @endif
                    </tr>
                </table>
                @endif
            </td>
        </tr>
        @endforeach
    </table>

    {{-- Legend --}}
    <table style="margin-top:6px;margin-left:{{ $labelWidth }}px;">
        <tr>
            @foreach($faseLabels as $key => $label)
            <td style="padding-right:10px;font-size:6.5px;color:#7a6355;white-space:nowrap;">
                <span style="display:inline-block;width:8px;height:8px;border-radius:2px;background:{{ $faseColors[$key] }};margin-right:3px;vertical-align:middle;"></span>{{ $label }}
            </td>
            @endforeach
        </tr>
    </table>
</div>

{{-- Tabel Data --}}
<table>
    <thead>
        <tr>
            <th>Rot.</th><th>Petak</th><th>Rencana Mulai</th><th>Rencana Selesai</th>
            <th>Realisasi Mulai</th><th>Realisasi Selesai</th>
            <th>Target (ha)</th><th>Realisasi (ha)</th><th>Efisiensi</th>
            <th>Durasi Air</th><th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            <td style="text-align:center;font-weight:bold;">{{ $row->urutan_rotasi }}</td>
            <td><strong>{{ $row->petak->kode_petak }}</strong><br><small>{{ $row->petak->nama_petak }}</small></td>
            <td style="text-align:center;">{{ $row->rencana_mulai_tanam->format('d/m/Y') }}</td>
            <td style="text-align:center;">{{ $row->rencana_selesai_tanam->format('d/m/Y') }}</td>
            <td style="text-align:center;">{{ $row->realisasi_mulai_tanam?->format('d/m/Y') ?? '—' }}</td>
            <td style="text-align:center;">{{ $row->realisasi_selesai_tanam?->format('d/m/Y') ?? '—' }}</td>
            <td style="text-align:center;">{{ $row->target_luas }}</td>
            <td style="text-align:center;">{{ $row->realisasi_luas ?? '—' }}</td>
            <td style="text-align:center;">
                @if($row->efisiensi_luas)
                <span class="badge {{ $row->efisiensi_luas >= 80 ? 'badge-leaf' : 'badge-red' }}">{{ $row->efisiensi_luas }}%</span>
                @else —
                @endif
            </td>
            <td style="text-align:center;">{{ $row->durasi_pemberian_air }} hr</td>
            <td style="text-align:center;">
                <span class="badge {{ $row->status === 'selesai' ? 'badge-leaf' : ($row->status === 'berjalan' ? 'badge-water' : ($row->status === 'terlambat' ? 'badge-red' : 'badge-clay')) }}">
                    {{ ucfirst($row->status) }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
