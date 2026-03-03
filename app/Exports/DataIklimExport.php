<?php
// app/Exports/DataIklimExport.php

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

class DataIklimExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(private int $tahun, private ?int $bulan = null) {}

    public function collection()
    {
        return IrrigationData::whereYear('tanggal', $this->tahun)
            ->when($this->bulan, fn($q) => $q->whereMonth('tanggal', $this->bulan))
            ->orderBy('tanggal')->get();
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Suhu Max (°C)', 'Suhu Min (°C)', 'Kelembaban (%)',
                'Angin (m/s)', 'Radiasi (MJ/m²)', 'Curah Hujan (mm)', 'Kc',
                'ETo (mm)', 'ETc (mm)', 'Kebutuhan Air (mm)'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [$no, $row->tanggal, $row->suhu_max, $row->suhu_min, $row->kelembaban,
                $row->kecepatan_angin, $row->radiasi_matahari, $row->curah_hujan,
                $row->kc, $row->eto, $row->etc, $row->kebutuhan_air];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3d2b1f']],
                  'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
        ];
    }

    public function title(): string { return 'Data Iklim'; }
}
