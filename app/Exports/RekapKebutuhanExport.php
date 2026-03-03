<?php
// app/Exports/RekapKebutuhanExport.php

namespace App\Exports;

use App\Models\IrrigationData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class RekapKebutuhanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(private int $tahun) {}

    public function collection()
    {
        $data = IrrigationData::whereYear('tanggal', $this->tahun)
            ->orderBy('tanggal')->get();

        return $data->groupBy(fn($d) => Carbon::parse($d->tanggal)->format('Y-m'))
            ->map(fn($group, $key) => (object)[
                'bulan'         => Carbon::parse($key.'-01')->locale('id')->isoFormat('MMMM YYYY'),
                'avg_eto'       => round($group->avg('eto'), 2),
                'avg_etc'       => round($group->avg('etc'), 2),
                'avg_kebutuhan' => round($group->avg('kebutuhan_air'), 2),
                'max_kebutuhan' => round($group->max('kebutuhan_air'), 2),
                'min_kebutuhan' => round($group->min('kebutuhan_air'), 2),
                'total_hujan'   => round($group->sum('curah_hujan'), 1),
                'jumlah_data'   => $group->count(),
            ])->values();
    }

    public function headings(): array
    {
        return ['Bulan', 'Rata-rata ETo (mm)', 'Rata-rata ETc (mm)',
                'Rata-rata Kebutuhan Air (mm)', 'Max Kebutuhan (mm)',
                'Min Kebutuhan (mm)', 'Total Curah Hujan (mm)', 'Jumlah Data'];
    }

    public function map($row): array
    {
        return [$row->bulan, $row->avg_eto, $row->avg_etc, $row->avg_kebutuhan,
                $row->max_kebutuhan, $row->min_kebutuhan, $row->total_hujan, $row->jumlah_data];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '8b5e3c']],
                  'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
        ];
    }

    public function title(): string { return 'Rekap Kebutuhan Air'; }
}
