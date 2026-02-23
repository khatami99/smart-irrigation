<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IrrigationData;
use Carbon\Carbon;

class IrrigationDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = Carbon::now()->subDays(300);

        for ($i = 0; $i < 20; $i++) {
            $eto = rand(40, 60) / 10;
            $etc = number_format($eto * 1.1, 2);
            $curah = rand(0, 20);

            // Rumus kebutuhan air sederhana
            $kebutuhan = number_format(($etc * 10) - $curah, 2);
            if ($kebutuhan < 0) $kebutuhan = 0; // Biar nggak ada angka minus kalau hujan deres

            IrrigationData::create([
                // KUNCINYA DI SINI: Tiap looping, tanggalnya nambah 15 hari
                'tanggal' => $startDate->copy()->addDays($i * 15)->format('Y-m-d'),
                'eto' => $eto,
                'etc' => $etc,
                'curah_hujan' => $curah,
                'kebutuhan_air' => $kebutuhan
            ]);
        }
    }
}
