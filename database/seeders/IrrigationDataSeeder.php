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
        for ($i = 1; $i <= 30; $i++) {

        $eto = rand(40, 60) / 10; // 4.0 - 6.0
        $etc = $eto * 1.1;
        $curah = rand(0, 20);
        $kebutuhan = ($etc * 10) - $curah;

        IrrigationData::create([
            'tanggal' => Carbon::now()->subDays(30 - $i),
            'eto' => $eto,
            'etc' => $etc,
            'curah_hujan' => $curah,
            'kebutuhan_air' => $kebutuhan
        ]);
        }
    }
}
